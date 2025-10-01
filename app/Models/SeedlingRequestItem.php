<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeedlingRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'seedling_request_id',
        'category_id',
        'category_item_id',
        'item_name',
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
}