<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'action',
        'performed_by',
        'changes',
        'notes'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public $timestamps = true;

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest('created_at')->limit($limit);
    }

    // ========================================
    // HELPER METHODS & ATTRIBUTES
    // ========================================

    /**
     * Get the color for this action (for UI display)
     */
    public function getActionColorAttribute()
    {
        return match($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'published' => 'success',
            'unpublished' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get the icon for this action (for UI display)
     */
    public function getActionIconAttribute()
    {
        return match($this->action) {
            'created' => '➕',
            'updated' => '✏️',
            'deleted' => '🗑️',
            'published' => '✅',
            'unpublished' => '⏸️',
            default => '📝'
        };
    }

    /**
     * Get readable action label
     */
    public function getReadableActionAttribute()
    {
        return match($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'published' => 'Published',
            'unpublished' => 'Unpublished',
            default => ucfirst($this->action)
        };
    }

    /**
     * Get performer information
     */
    public function getPerformerNameAttribute()
    {
        return $this->performer?->name ?? 'System';
    }

    /**
     * Format changes for display
     */
    public function getFormattedChangesAttribute()
    {
        if (!$this->changes) {
            return [];
        }

        $formatted = [];
        foreach ($this->changes as $field => $change) {
            $formatted[] = [
                'field' => $this->formatFieldName($field),
                'old' => $change['old'] ?? null,
                'new' => $change['new'] ?? null
            ];
        }

        return $formatted;
    }

    /**
     * Format field name for display
     */
    private function formatFieldName($field)
    {
        return ucwords(str_replace('_', ' ', $field));
    }

    /**
     * Check if this is a creation log
     */
    public function isCreation()
    {
        return $this->action === 'created';
    }

    /**
     * Check if this is a deletion log
     */
    public function isDeletion()
    {
        return $this->action === 'deleted';
    }

    /**
     * Check if this is a status change log
     */
    public function isStatusChange()
    {
        return in_array($this->action, ['published', 'unpublished']);
    }
}