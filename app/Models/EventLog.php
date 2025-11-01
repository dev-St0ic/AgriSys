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
        'created_at' => 'datetime'
    ];

    public $timestamps = true;

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Helper Methods
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
}