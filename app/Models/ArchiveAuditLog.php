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

    public function archive(): BelongsTo
    {
        return $this->belongsTo(Archive::class, 'archive_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}