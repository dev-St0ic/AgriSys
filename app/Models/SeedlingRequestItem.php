<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SeedlingRequestItem extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'seedling_request_id',
        'user_id',
        'category_id',
        'category_item_id',
        'category_name',    
        'category_icon', 
        'item_name',
        'item_unit',
        'requested_quantity',
        'approved_quantity',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
        'approved_quantity' => 'integer'
    ];

    /**
     * Get the seedling request
     */
    public function seedlingRequest()
    {
        return $this->belongsTo(SeedlingRequest::class);
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(RequestCategory::class, 'category_id');
    }

    // Safe category name (snapshot fallback)
    public function getCategoryNameAttribute()
    {
        if (!empty($this->attributes['category_name'])) {
            return $this->attributes['category_name'];
        }
        return $this->category?->display_name ?? 'Deleted Category';
    }

    // Safe category icon
    public function getCategoryIconAttribute()
    {
        if (!empty($this->attributes['category_icon'])) {
            return $this->attributes['category_icon'];
        }
        return $this->category?->icon ?? 'fa-leaf';
    }

    /**
     * Get the category item
     */
    public function categoryItem()
    {
        return $this->belongsTo(CategoryItem::class, 'category_item_id');
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'approved' => 'success',
            'rejected' => 'danger',
            'pending' => 'warning'
        ];

        $color = $colors[$this->status] ?? 'secondary';
        $label = ucfirst($this->status);

        return "<span class='badge bg-{$color}'>{$label}</span>";
    }

      // Always get item name (snapshot or live)
    public function getItemNameAttribute()
    {
        // Return stored snapshot if exists
        if (!empty($this->attributes['item_name'])) {
            return $this->attributes['item_name'];
        }
        
        // Fallback to live data if item still exists
        if ($this->categoryItem) {
            return $this->categoryItem->name;
        }
        
        return 'Unknown Item';
    }

    //  Always get item unit
    public function getItemUnitAttribute()
    {
        // Return stored snapshot if exists
        if (!empty($this->attributes['item_unit'])) {
            return $this->attributes['item_unit'];
        }
        
        // Fallback to live data
        if ($this->categoryItem) {
            return $this->categoryItem->unit;
        }
        
        return 'pcs';
    }

    /**
     * Scope for specific status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for approved items
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected items
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Log activity options
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([]) // Disable automatic logging - use manual controller logging instead
            ->dontSubmitEmptyLogs();
    }
}
