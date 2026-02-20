<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Archive extends Model
{
    protected $fillable = [
        'archive_reference_number',
        'model_type',
        'model_id',
        'item_name',
        'record_type',
        'data',
        'data_checksum',
        'data_version',
        'retention_category',
        'retention_years',
        'retention_expires_at',
        'disposal_authority',
        'retention_justification',
        'archive_reason',
        'archive_source',
        'classification',
        'tags',
        'archived_from_recycle_bin_id',
        'archived_by',
        'archived_by_ip',
        'archived_at',
        'disposal_status',
        'disposal_approved_by',
        'disposal_approved_at',
        'disposal_notes',
        'disposed_by',
        'disposed_at',
        'disposed_by_ip',
        'is_locked',
        'locked_by',
        'locked_at',
        'lock_reason',
        'is_restricted',
        'allowed_roles',
    ];

    protected $casts = [
        'data'             => 'array',
        'tags'             => 'array',
        'allowed_roles'    => 'array',
        'is_locked'        => 'boolean',
        'is_restricted'    => 'boolean',
        'archived_at'      => 'datetime',
        'disposal_approved_at' => 'datetime',
        'disposed_at'      => 'datetime',
        'locked_at'        => 'datetime',
        'retention_expires_at' => 'date',
    ];

    // Retention Categories (ISO 15489)
    const RETENTION_PERMANENT   = 'permanent';   // Never delete (critical gov records)
    const RETENTION_LONG_TERM   = 'long_term';   // 25-50 years
    const RETENTION_STANDARD    = 'standard';    // 10 years
    const RETENTION_SHORT_TERM  = 'short_term';  // 3-5 years

    // Classification Levels
    const CLASS_PUBLIC       = 'public';
    const CLASS_INTERNAL     = 'internal';
    const CLASS_CONFIDENTIAL = 'confidential';
    const CLASS_RESTRICTED   = 'restricted';

    // Disposal Status
    const DISPOSAL_RETAINED          = 'retained';
    const DISPOSAL_APPROVED          = 'approved_for_disposal';
    const DISPOSAL_DISPOSED          = 'disposed';

    // --- Relationships ---

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function disposalApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disposal_approved_by');
    }

    public function disposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(ArchiveAuditLog::class, 'archive_id')->orderBy('performed_at', 'desc');
    }

    // --- Scopes ---

    public function scopeRetained($query)
    {
        return $query->where('disposal_status', self::DISPOSAL_RETAINED);
    }

    public function scopeEligibleForDisposal($query)
    {
        return $query->where('disposal_status', self::DISPOSAL_RETAINED)
                     ->where('retention_category', '!=', self::RETENTION_PERMANENT)
                     ->whereNotNull('retention_expires_at')
                     ->where('retention_expires_at', '<=', now()->toDateString());
    }

    public function scopeApprovedForDisposal($query)
    {
        return $query->where('disposal_status', self::DISPOSAL_APPROVED);
    }

    public function scopeDisposed($query)
    {
        return $query->where('disposal_status', self::DISPOSAL_DISPOSED);
    }

    public function scopeByType($query, string $recordType)
    {
        return $query->where('record_type', $recordType);
    }

    // --- Accessors ---

    public function getRetentionCategoryLabelAttribute(): string
    {
        return match($this->retention_category) {
            self::RETENTION_PERMANENT  => 'Permanent',
            self::RETENTION_LONG_TERM  => 'Long Term',
            self::RETENTION_STANDARD   => 'Standard',
            self::RETENTION_SHORT_TERM => 'Short Term',
            default                    => ucfirst($this->retention_category),
        };
    }

    public function getClassificationBadgeColorAttribute(): string
    {
        return match($this->classification) {
            self::CLASS_PUBLIC       => 'success',
            self::CLASS_INTERNAL     => 'primary',
            self::CLASS_CONFIDENTIAL => 'warning',
            self::CLASS_RESTRICTED   => 'danger',
            default                  => 'secondary',
        };
    }

    public function getDisposalStatusBadgeColorAttribute(): string
    {
        return match($this->disposal_status) {
            self::DISPOSAL_RETAINED => 'success',
            self::DISPOSAL_APPROVED => 'warning',
            self::DISPOSAL_DISPOSED => 'danger',
            default                 => 'secondary',
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->retention_category === self::RETENTION_PERMANENT) {
            return false;
        }
        return $this->retention_expires_at && $this->retention_expires_at->isPast();
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->retention_expires_at || $this->retention_category === self::RETENTION_PERMANENT) {
            return null;
        }
        return now()->diffInDays($this->retention_expires_at, false);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->record_type) {
            'fishr'             => 'FishR Registration',
            'fishr_annex'       => 'FishR Annex',
            'boatr'             => 'BoatR Registration',
            'boatr_annex'       => 'BoatR Annex',
            'rsbsa'             => 'RSBSA Registration',
            'seedlings'         => 'Supply Request',
            'training'          => 'Training Request',
            'user_registration' => 'User Registration',
            'category_item'     => 'Supply Item',
            'admin_user'        => 'Admin User',
            'request_category'  => 'Supply Category',
            default             => ucfirst(str_replace('_', ' ', $this->record_type)),
        };
    }

    // --- Helpers ---

    public function verifyIntegrity(): bool
    {
        $currentHash = hash('sha256', json_encode($this->data));
        return $currentHash === $this->data_checksum;
    }

    public function isEligibleForDisposal(): bool
    {
        return $this->retention_category !== self::RETENTION_PERMANENT
            && $this->retention_expires_at !== null
            && $this->retention_expires_at->isPast()
            && $this->disposal_status === self::DISPOSAL_RETAINED;
    }

    // --- Static Generators ---

    public static function generateReferenceNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('archived_at', $year)->count() + 1;
        return sprintf('ARCH-%s-%05d', $year, $count);
    }
}