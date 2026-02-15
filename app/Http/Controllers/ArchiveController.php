<?php
// app/Http/Controllers/ArchiveController.php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\ArchiveAuditLog;
use App\Models\RecycleBin;
use App\Models\RetentionSchedule;
use App\Services\ArchiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArchiveController extends Controller
{
    /**
     * Gate: Only SuperAdmin can access the archive
     */
   public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\HasMiddleware,
        ];
    }
    private function requireSuperAdmin(): void
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. SuperAdmin privileges required.');
        }
    }

    // ─────────────────────────────────────────────
    // LIST
    // ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->requireSuperAdmin();
        try {
            $query = Archive::with('archivedBy');

            // Filter: disposal status
            if ($request->filled('status')) {
                $query->where('disposal_status', $request->status);
            } else {
                // Default: only retained records
                $query->where('disposal_status', '!=', Archive::DISPOSAL_DISPOSED);
            }

            // Filter: record type
            if ($request->filled('type')) {
                $query->where('record_type', $request->type);
            }

            // Filter: retention category
            if ($request->filled('retention')) {
                $query->where('retention_category', $request->retention);
            }

            // Filter: expired retention only
            if ($request->filled('expired') && $request->expired === '1') {
                $query->eligibleForDisposal();
            }

            // Filter: classification
            if ($request->filled('classification')) {
                $query->where('classification', $request->classification);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('item_name', 'like', "%{$search}%")
                      ->orWhere('archive_reference_number', 'like', "%{$search}%")
                      ->orWhere('archive_reason', 'like', "%{$search}%");
                });
            }

            $items   = $query->orderBy('archived_at', 'desc')->paginate(15)->appends($request->query());
            $stats   = $this->getDashboardStats();
            $schedules = RetentionSchedule::all()->keyBy('record_type');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data'    => $items,
                ]);
            }

            return view('admin.archive.index', compact('items', 'stats', 'schedules'));

        } catch (\Exception $e) {
            Log::error('Error loading archive', ['error' => $e->getMessage()]);
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error loading archive'], 500);
            }
            return redirect()->back()->with('error', 'Error loading archive');
        }
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────

   public function show($id)
    {   
        $this->requireSuperAdmin();
        
        try {
            $archive = Archive::with(['archivedBy', 'disposalApprovedBy', 'disposedBy', 'auditLogs.performedBy'])
                            ->findOrFail($id);

            // Log view - fix the method call
            ArchiveService::log($archive, ArchiveAuditLog::ACTION_VIEWED, 'Record viewed');

            // Verify integrity on view
            $integrity = ArchiveService::verifyIntegrity($archive);

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'                       => $archive->id,
                    'archive_reference_number' => $archive->archive_reference_number,
                    'type_label'               => $archive->type_label,
                    'item_name'                => $archive->item_name,
                    'archive_reason'           => $archive->archive_reason ?? '',
                    'classification'           => $archive->classification,
                    'classification_badge'     => $archive->classification_badge_color,
                    'retention_category'       => $archive->retention_category,
                    'retention_category_label' => $archive->retention_category_label,
                    'retention_years'          => $archive->retention_years,
                    'retention_expires_at'     => $archive->retention_expires_at?->format('M d, Y') ?? 'PERMANENT',
                    'disposal_authority'       => $archive->disposal_authority ?? '',
                    'disposal_status'          => $archive->disposal_status,
                    'disposal_status_badge'    => $archive->disposal_status_badge_color,
                    'is_expired'               => $archive->is_expired,
                    'is_locked'                => $archive->is_locked,
                    'archived_by'              => $archive->archivedBy?->name ?? 'Unknown',
                    'archived_at'              => $archive->archived_at->format('M d, Y h:i A'),
                    'data'                     => $archive->data ?? [],
                    'integrity'                => $integrity,
                    'audit_logs'               => $archive->auditLogs->map(fn($log) => [
                        'action'       => $log->action_label ?? $log->action,
                        'action_badge' => $log->action_badge_color ?? 'secondary',
                        'performed_by' => $log->performedBy?->name ?? 'System',
                        'performed_at' => $log->performed_at->format('M d, Y h:i A'),
                        'notes'        => $log->notes ?? '',
                    ]),
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Archive show error', [
                'id' => $id, 
                'error' => $e->getMessage(), 
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Error loading record: ' . $e->getMessage()
            ], 500);
        }
    }

    // ─────────────────────────────────────────────
    // ARCHIVE FROM RECYCLE BIN
    // ─────────────────────────────────────────────

    public function archiveFromRecycleBin(Request $request, $recycleBinId)
    {
        $this->requireSuperAdmin();
        try {
            $recycleBinItem = RecycleBin::findOrFail($recycleBinId);
            $reason         = $request->input('reason', 'Archived from recycle bin by SuperAdmin');

            $archive = ArchiveService::archiveFromRecycleBin($recycleBinItem, $reason);

            return response()->json([
                'success'   => true,
                'message'   => "Record archived successfully. Reference: {$archive->archive_reference_number}",
                'reference' => $archive->archive_reference_number,
            ]);
        } catch (\Exception $e) {
            Log::error('Error archiving from recycle bin', ['id' => $recycleBinId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error archiving: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // DISPOSAL WORKFLOW (Two-step, ISO 15489)
    // ─────────────────────────────────────────────

    public function approveDisposal(Request $request, $id)
    {
        $this->requireSuperAdmin();
        $request->validate(['notes' => 'required|string|min:10|max:1000']);

        try {
            $archive = Archive::findOrFail($id);
            ArchiveService::approveForDisposal($archive, $request->notes);

            return response()->json([
                'success' => true,
                'message' => "Record {$archive->archive_reference_number} approved for disposal.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function revokeDisposal(Request $request, $id)
    {
        $this->requireSuperAdmin();
        $request->validate(['reason' => 'required|string|min:5']);

        try {
            $archive = Archive::findOrFail($id);
            ArchiveService::revokeDisposalApproval($archive, $request->reason);

            return response()->json([
                'success' => true,
                'message' => "Disposal approval revoked for {$archive->archive_reference_number}.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function dispose(Request $request, $id)
    {
        $this->requireSuperAdmin();
        $request->validate([
            'reason'    => 'required|string|min:10',
            'confirm'   => 'required|in:CONFIRM_DISPOSE',
        ]);

        try {
            $archive = Archive::findOrFail($id);
            ArchiveService::dispose($archive, $request->reason);

            return response()->json([
                'success' => true,
                'message' => "Record {$archive->archive_reference_number} has been permanently disposed and compliance record retained.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ─────────────────────────────────────────────
    // RETENTION SCHEDULES
    // ─────────────────────────────────────────────

    public function retentionSchedules(Request $request)
    {
        $this->requireSuperAdmin();
        
        $query = RetentionSchedule::orderBy('retention_category')->orderBy('record_label');
        
        $schedules = $query->paginate(15)->appends($request->query());
        
        return view('admin.archive.retention-schedules', compact('schedules'));
    }

    public function updateRetentionSchedule(Request $request, $id)
    {
        $this->requireSuperAdmin();
        $request->validate([
            'retention_category' => 'required|in:permanent,long_term,standard,short_term',
            'retention_years'    => 'required_unless:retention_category,permanent|integer|min:1|max:100',
            'legal_basis'        => 'nullable|string|max:500',
            'description'        => 'nullable|string|max:1000',
        ]);

        try {
            $schedule = RetentionSchedule::findOrFail($id);
            $schedule->update($request->only(['retention_category', 'retention_years', 'legal_basis', 'description']));

            Log::info('Retention schedule updated', [
                'record_type' => $schedule->record_type,
                'updated_by'  => auth()->id(),
                'changes'     => $request->only(['retention_category', 'retention_years']),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Retention schedule for '{$schedule->record_label}' updated.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ─────────────────────────────────────────────
    // AUDIT LOG
    // ─────────────────────────────────────────────

    public function auditLog(Request $request)
    {
        $this->requireSuperAdmin();
        $query = ArchiveAuditLog::with(['archive', 'performedBy'])
                                ->orderBy('performed_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('archive_id')) {
            $query->where('archive_id', $request->archive_id);
        }

        $logs = $query->paginate(25)->appends($request->query());

        return view('admin.archive.audit-log', compact('logs'));
    }

    // ─────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────

    private function getDashboardStats(): array
    {
        $this->requireSuperAdmin();
        return [
            'total_retained'          => Archive::retained()->count(),
            'total_disposed'          => Archive::disposed()->count(),
            'eligible_for_disposal'   => Archive::eligibleForDisposal()->count(),
            'approved_for_disposal'   => Archive::approvedForDisposal()->count(),
            'permanent_records'       => Archive::where('retention_category', Archive::RETENTION_PERMANENT)->count(),
        ];
    }
}