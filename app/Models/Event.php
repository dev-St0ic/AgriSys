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
        'is_featured',
        'is_archived',
        'archived_at',
        'archive_reason',
        'created_by',
        'updated_by',
        'archived_by',
        'published_at'
    ];

    // Append custom attributes to JSON responses
    protected $appends = [
        'image_url',
        'formatted_date',
        'status_badge'
    ];

    protected $casts = [
        'details' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_archived' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'archived_at' => 'datetime',
        'published_at' => 'datetime'
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

    public function archivist()
    {
        return $this->belongsTo(User::class, 'archived_by');
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
        return $query->where('is_active', true)->where('is_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('created_at', 'desc');
    }

    public function scopePublic($query)
    {
        return $query->where('is_active', true)
                     ->where('is_archived', false)
                     ->ordered();
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                     ->where('is_active', true)
                     ->where('is_archived', false);
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
        if (!$this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }

    /**
     * Get the formatted date attribute
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ?? 'Date TBA';
    }

    /**
     * Get status badge for event (active, archived, inactive)
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->is_archived) {
            return 'archived';
        }
        if ($this->is_active) {
            return 'active';
        }
        return 'inactive';
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
     * Archive this event
     */
    public function archive($userId, $reason = null)
    {
        $this->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => $userId,
            'archive_reason' => $reason,
            'is_active' => false // Automatically deactivate when archived
        ]);

        $this->logAction('archived', $userId, [
            'is_archived' => ['old' => false, 'new' => true],
            'is_active' => ['old' => true, 'new' => false]
        ], 'Event archived: ' . ($reason ?? 'No reason provided'));
    }

    /**
     * Restore/unarchive this event
     */
    public function unarchive($userId)
    {
        $this->update([
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'is_active' => true // Automatically activate when restored
        ]);

        $this->logAction('restored', $userId, [
            'is_archived' => ['old' => true, 'new' => false],
            'is_active' => ['old' => false, 'new' => true]
        ], 'Event restored from archive');
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

    /**
     * Check if event should be displayed on landing page
     */
    public function isDisplayable()
    {
        return $this->is_active && !$this->is_archived && !$this->trashed();
    }

    /**
     * Get all public (active and non-archived) events
     */
    public static function getPublicEvents($category = null)
    {
        $query = self::public();

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        return $query->get();
    }

    /**
     * Get featured events for landing page
     */
    public static function getFeaturedEvents($limit = 3)
    {
        return self::featured()
            ->limit($limit)
            ->get();
    }

    /**
     * Get all archived events (for archive management)
     */
    public static function getArchivedEvents()
    {
        return self::archived()
            ->orderBy('archived_at', 'desc')
            ->get();
    }

    // ========================================
    // JSON SERIALIZATION OVERRIDE
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
        $array['status_badge'] = $this->status_badge;

        return $array;
    }

    /**
     * Configure activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'category', 'date', 'is_active', 'is_archived'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}