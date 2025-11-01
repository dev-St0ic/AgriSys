<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Event extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

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

    // ⭐ ADDED: Append custom attributes to JSON responses
    protected $appends = [
        'image_url',
        'formatted_date'
    ];

    protected $casts = [
        'details' => 'array',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

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

    // ========================================
    // SCOPES
    // ========================================

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

    // ========================================
    // ACCESSORS (Custom Attributes)
    // ========================================

    /**
     * Get the full image URL for the event
     * Handles both local storage and external URLs
     */
    public function getImageUrlAttribute()
    {
        // Return null if no image path
        if (!$this->image_path) {
            return null;
        }

        // If it's already a full URL, return as-is
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        // Return the storage URL for local files
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get the formatted date attribute
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ?? 'Date TBA';
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get a specific detail by key
     */
    public function getDetail($key, $default = null)
    {
        return $this->details[$key] ?? $default;
    }

    /**
     * Set a specific detail by key
     */
    public function setDetail($key, $value)
    {
        $details = $this->details ?? [];
        $details[$key] = $value;
        $this->details = $details;
    }

    /**
     * Log an action for this event
     */
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

    // ========================================
    // JSON SERIALIZATION OVERRIDE (Optional)
    // ========================================

    /**
     * Override toArray to ensure custom attributes are included
     * This is useful for API responses
     */
    public function toArray()
    {
        $array = parent::toArray();

        // Explicitly include accessors
        $array['image_url'] = $this->image_url;
        $array['formatted_date'] = $this->formatted_date;

        return $array;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'category', 'date', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}