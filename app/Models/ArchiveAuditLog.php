<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchiveAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'archive_id',
        'action',
        'performed_by',
        'performed_by_ip',
        'performed_by_role',
        'notes',       
        'metadata',
        'performed_at',
    ];

    protected $casts = [
        'metadata'     => 'array',
        'performed_at' => 'datetime',
    ];

    const ACTION_ARCHIVED          = 'archived';
    const ACTION_VIEWED            = 'viewed';
    const ACTION_EXPORTED          = 'exported';
    const ACTION_DISPOSAL_APPROVED = 'disposal_approved';
    const ACTION_DISPOSAL_REVOKED  = 'disposal_revoked';
    const ACTION_DISPOSED          = 'disposed';
    const ACTION_LOCKED            = 'locked';
    const ACTION_INTEGRITY_CHECK   = 'integrity_check';

    //  ACCESSORS
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            self::ACTION_ARCHIVED          => 'Archived',
            self::ACTION_VIEWED            => 'Viewed',
            self::ACTION_EXPORTED          => 'Exported',
            self::ACTION_DISPOSAL_APPROVED => 'Disposal Approved',
            self::ACTION_DISPOSAL_REVOKED  => 'Disposal Revoked',
            self::ACTION_DISPOSED          => 'Disposed',
            self::ACTION_LOCKED            => 'Locked',
            self::ACTION_INTEGRITY_CHECK   => 'Integrity Check',
            default                        => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    public function getActionBadgeColorAttribute(): string
    {
        return match($this->action) {
            self::ACTION_ARCHIVED          => 'primary',
            self::ACTION_VIEWED            => 'info',
            self::ACTION_EXPORTED          => 'secondary',
            self::ACTION_DISPOSAL_APPROVED => 'warning',
            self::ACTION_DISPOSAL_REVOKED  => 'success',
            self::ACTION_DISPOSED          => 'danger',
            self::ACTION_LOCKED            => 'dark',
            self::ACTION_INTEGRITY_CHECK   => 'info',
            default                        => 'secondary',
        };
    }

    public function archive(): BelongsTo
    {
        return $this->belongsTo(Archive::class, 'archive_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}