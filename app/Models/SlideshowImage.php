<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SlideshowImage extends Model
{
    use HasFactory;

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
     */
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
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
}
