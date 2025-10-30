<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'image_path',
        'date',
        'location',
        'details',
        'is_active',
        'display_order',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'details' => 'array',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function logs()
    {
        return $this->hasMany(EventLog::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at', 'desc');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ?? 'Date TBA';
    }

    // Helper Methods
    public function getDetail($key, $default = null)
    {
        return $this->details[$key] ?? $default;
    }

    public function setDetail($key, $value)
    {
        $details = $this->details ?? [];
        $details[$key] = $value;
        $this->details = $details;
    }

    public function logAction($action, $performedBy, $changes = null, $notes = null)
    {
        return EventLog::create([
            'event_id' => $this->id,
            'action' => $action,
            'performed_by' => $performedBy,
            'changes' => $changes,
            'notes' => $notes
        ]);
    }
}