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

    /**
     * Get the color for an action (for UI display)
     */
    public function getActionColorAttribute($action = null)
    {
        $action = $action ?? $this->action ?? 'updated';
        return match($action) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'published' => 'success',
            'unpublished' => 'warning',
            'archived' => 'secondary',
            'restored' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get the icon for an action (for UI display)
     */
    public function getActionIconAttribute($action = null)
    {
        $action = $action ?? $this->action ?? 'updated';
        return match($action) {
            'created' => '➕',
            'updated' => '✏️',
            'deleted' => '🗑️',
            'published' => '✅',
            'unpublished' => '⏸️',
            'archived' => '📦',
            'restored' => '↩️',
            default => '📝'
        };
    }

    /**
     * Get readable action label
     */
    public function getReadableActionAttribute($action = null)
    {
        $action = $action ?? $this->action ?? 'updated';
        return match($action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'published' => 'Published',
            'unpublished' => 'Unpublished',
            'archived' => 'Archived',
            'restored' => 'Restored',
            default => ucfirst($action)
        };
    }

    // ========================================
    // HELPER METHODS
    // ========================================

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

    public function archive($userId, $reason = null)
    {
        $this->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => $userId,
            'archive_reason' => $reason,
            'is_active' => false
        ]);
    }

    public function unarchive($userId)
    {
        $this->update([
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
            'is_active' => true
        ]);
    }

    public function isDisplayable()
    {
        return $this->is_active && !$this->is_archived && !$this->trashed();
    }

    public static function getPublicEvents($category = null)
    {
        $query = self::public();

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        return $query->get();
    }

    public static function getFeaturedEvents($limit = 3)
    {
        return self::featured()
            ->limit($limit)
            ->get();
    }

    public static function getArchivedEvents()
    {
        return self::archived()
            ->orderBy('archived_at', 'desc')
            ->get();
    }

    // ========================================
    // JSON SERIALIZATION OVERRIDE
    // ========================================

    public function toArray()
    {
        $array = parent::toArray();

        $array['image_url'] = $this->image_url;
        $array['formatted_date'] = $this->formatted_date;
        $array['status_badge'] = $this->status_badge;

        return $array;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}