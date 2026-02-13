<?php
// app/Services/ArchiveService.php

namespace App\Services;

use App\Models\Archive;
use App\Models\ArchiveAuditLog;
use App\Models\RecycleBin;
use App\Models\RetentionSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class ArchiveService
{
    /**
     * Archive a RecycleBin item (move from recycle bin to long-term archive)
     * ISO 15489: Captures record, metadata, provenance, and retention schedule.
     */
    public static function archiveFromRecycleBin(RecycleBin $recycleBinItem, ?string $reason = null): Archive
    {
        return DB::transaction(function () use ($recycleBinItem, $reason) {

            // Determine record type from model class
            $recordType = self::modelClassToRecordType($recycleBinItem->model_type);

            // Fetch ISO 15489 retention schedule
            $schedule = RetentionSchedule::forType($recordType);

            $retentionCategory = $schedule?->retention_category ?? Archive::RETENTION_STANDARD;
            $retentionYears    = $schedule?->retention_years ?? 10;
            $expiresAt         = $schedule?->calculateExpiryDate();

            // Serialize data and compute integrity hash
            $data     = $recycleBinItem->data ?? [];
            $checksum = hash('sha256', json_encode($data));

            // Create archive record
            $archive = Archive::create([
                'archive_reference_number'   => Archive::generateReferenceNumber(),
                'model_type'                 => $recycleBinItem->model_type,
                'model_id'                   => $recycleBinItem->model_id,
                'item_name'                  => $recycleBinItem->item_name,
                'record_type'                => $recordType,
                'data'                       => $data,
                'data_checksum'              => $checksum,
                'data_version'               => '1.0',

                // Retention (ISO 15489)
                'retention_category'         => $retentionCategory,
                'retention_years'            => $retentionYears,
                'retention_expires_at'       => $expiresAt,
                'disposal_authority'         => $schedule?->legal_basis,
                'retention_justification'    => $schedule?->description,

                // Metadata
                'archive_reason'             => $reason ?? $recycleBinItem->reason ?? 'Archived from recycle bin',
                'archive_source'             => 'recycle_bin',
                'classification'             => self::getClassificationForType($recordType),
                'archived_from_recycle_bin_id' => $recycleBinItem->id,

                // Provenance
                'archived_by'                => auth()->id(),
                'archived_by_ip'             => Request::ip(),
                'archived_at'                => now(),

                // Disposal
                'disposal_status'            => Archive::DISPOSAL_RETAINED,

                // Lock - archived records are locked by default
                'is_locked'                  => true,
                'locked_by'                  => auth()->id(),
                'locked_at'                  => now(),
                'lock_reason'                => 'Automatically locked on archive creation (ISO 15489 integrity)',
            ]);

            // Log the archival action
            self::log($archive, ArchiveAuditLog::ACTION_ARCHIVED);

            // Mark recycle bin item as archived (do NOT delete it yet - keep for audit trail)
            $recycleBinItem->update([
                'restored_at' => now(), // Using restored_at to mark as "processed"
                'restored_by' => auth()->id(),
            ]);

            Log::info("Archive created: {$archive->archive_reference_number}", [
                'record_type'       => $recordType,
                'retention_category'=> $retentionCategory,
                'expires_at'        => $expiresAt ?? 'PERMANENT',
            ]);

            return $archive;
        });
    }

    /**
     * Approve a record for disposal (SuperAdmin only)
     * ISO 15489: Two-step disposal - approval required before physical deletion
     */
    public static function approveForDisposal(Archive $archive, string $notes): bool
    {
        if ($archive->retention_category === Archive::RETENTION_PERMANENT) {
            throw new \Exception('Permanent records cannot be approved for disposal.');
        }

        if (!$archive->isEligibleForDisposal()) {
            throw new \Exception('Record retention period has not expired yet.');
        }

        DB::transaction(function () use ($archive, $notes) {
            $archive->update([
                'disposal_status'      => Archive::DISPOSAL_APPROVED,
                'disposal_approved_by' => auth()->id(),
                'disposal_approved_at' => now(),
                'disposal_notes'       => $notes,
            ]);

            self::log($archive, ArchiveAuditLog::ACTION_DISPOSAL_APPROVED,
                "Disposal approved. Notes: {$notes}");
        });

        return true;
    }

    /**
     * Revoke disposal approval
     */
    public static function revokeDisposalApproval(Archive $archive, string $reason): bool
    {
        DB::transaction(function () use ($archive, $reason) {
            $archive->update([
                'disposal_status'      => Archive::DISPOSAL_RETAINED,
                'disposal_approved_by' => null,
                'disposal_approved_at' => null,
                'disposal_notes'       => null,
            ]);

            self::log($archive, ArchiveAuditLog::ACTION_DISPOSAL_REVOKED,
                "Disposal approval revoked. Reason: {$reason}");
        });

        return true;
    }

    /**
     * Permanently dispose a record (SuperAdmin only, after approval)
     * ISO 15489: Disposal only after approval, with full audit trail
     */
    public static function dispose(Archive $archive, string $reason): bool
    {
        if ($archive->disposal_status !== Archive::DISPOSAL_APPROVED) {
            throw new \Exception('Record must be approved for disposal before it can be disposed.');
        }

        if ($archive->retention_category === Archive::RETENTION_PERMANENT) {
            throw new \Exception('Permanent records cannot be disposed.');
        }

        DB::transaction(function () use ($archive, $reason) {
            // Final audit log entry BEFORE disposal
            self::log($archive, ArchiveAuditLog::ACTION_DISPOSED,
                "Record permanently disposed. Reason: {$reason}. " .
                "Reference: {$archive->archive_reference_number}. " .
                "Approved by: " . ($archive->disposalApprovedBy?->name ?? 'Unknown')
            );

            // Mark as disposed (keep the archive row for compliance - just mark disposed)
            $archive->update([
                'disposal_status' => Archive::DISPOSAL_DISPOSED,
                'disposed_by'     => auth()->id(),
                'disposed_at'     => now(),
                'disposed_by_ip'  => Request::ip(),
                'disposal_notes'  => ($archive->disposal_notes ?? '') . "\nDisposed: {$reason}",
                'data'            => null, // Wipe actual data, keep metadata for audit
            ]);
        });

        return true;
    }

    /**
     * Log an action on an archive record (immutable audit trail)
     */
    public static function log(Archive $archive, string $action, array $metadata = []): void
    {
        try {
            ArchiveAuditLog::create([
                'archive_id'       => $archive->id,
                'action'           => $action,
                'performed_by'     => auth()->id(),
                'performed_by_ip'  => Request::ip(),
                'performed_by_role'=> auth()->user()?->role ?? 'unknown',
                'metadata'         => $metadata,
                'performed_at'     => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to write archive audit log', [
                'archive_id' => $archive->id,
                'action'     => $action,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Verify data integrity of an archive record
     */
    public static function verifyIntegrity(Archive $archive): array
    {
        $isValid     = $archive->verifyIntegrity();
        $currentHash = hash('sha256', json_encode($archive->data));

        self::log($archive, ArchiveAuditLog::ACTION_INTEGRITY_CHECK, 
            $isValid ? 'Integrity check passed.' : 'INTEGRITY CHECK FAILED - data may have been tampered.',
            ['expected_checksum' => $archive->data_checksum, 'current_checksum' => $currentHash]
        );

        return [
            'valid'            => $isValid,
            'expected_checksum'=> $archive->data_checksum,
            'current_checksum' => $currentHash,
        ];
    }

    // --- Private Helpers ---

    private static function modelClassToRecordType(string $modelClass): string
    {
        return match($modelClass) {
            'App\Models\FishrApplication'  => 'fishr',
            'App\Models\FishrAnnex'        => 'fishr_annex',
            'App\Models\BoatrApplication'  => 'boatr',
            'App\Models\BoatrAnnex'        => 'boatr_annex',
            'App\Models\RsbsaApplication'  => 'rsbsa',
            'App\Models\SeedlingRequest'   => 'seedlings',
            'App\Models\TrainingApplication'=> 'training',
            'App\Models\UserRegistration'  => 'user_registration',
            'App\Models\CategoryItem'      => 'category_item',
            'App\Models\RequestCategory'   => 'request_category',
            default                        => 'unknown',
        };
    }

    private static function getClassificationForType(string $recordType): string
    {
        // Government sensitivity classification
        return match($recordType) {
            'rsbsa', 'fishr', 'boatr' => Archive::CLASS_CONFIDENTIAL,  // Personal data
            'user_registration'        => Archive::CLASS_CONFIDENTIAL,  // Personal data (Data Privacy Act)
            'seedlings', 'training'    => Archive::CLASS_INTERNAL,
            default                    => Archive::CLASS_INTERNAL,
        };
    }
}