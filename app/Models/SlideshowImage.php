<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SlideshowImage extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'image_path',
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * Get the full URL for the slideshow image
     * Uses url() helper for better compatibility with Cloudflare and CDNs
     */
    public function getImageUrlAttribute()
    {
        // Use url() instead of asset() for better CDN/Cloudflare compatibility
        // Add cache-busting parameter using updated_at timestamp
        $cacheBuster = $this->updated_at ? $this->updated_at->timestamp : time();
        return url('storage/' . $this->image_path) . '?v=' . $cacheBuster;
    }

    /**
     * Scope to get only active slideshow images
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get slideshow images ordered by their order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Delete the associated image file when the model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($slideshowImage) {
            if ($slideshowImage->image_path && Storage::disk('public')->exists($slideshowImage->image_path)) {
                Storage::disk('public')->delete($slideshowImage->image_path);
            }
        });
    }

    // Log activity options
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
