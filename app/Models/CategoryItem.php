<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'unit',
        'price',
        'min_quantity',
        'max_quantity',
        'is_available',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'is_available' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the category this item belongs to
     */
    public function category()
    {
        return $this->belongsTo(RequestCategory::class, 'category_id');
    }

    /**
     * Get request items
     */
    public function requestItems()
    {
        return $this->hasMany(SeedlingRequestItem::class, 'category_item_id');
    }

    /**
     * Scope for available items
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get formatted name with unit
     */
    public function getFormattedNameAttribute(): string
    {
        return $this->name . ' (' . $this->unit . ')';
    }

    /**
     * Check inventory availability
     */
    public function checkInventoryAvailability(int $quantity): array
    {
        $inventory = \App\Models\Inventory::where('item_name', 'LIKE', "%{$this->name}%")
            ->where('category', $this->category->getInventoryCategoryName())
            ->where('is_active', true)
            ->first();

        if (!$inventory) {
            return [
                'available' => false,
                'current_stock' => 0,
                'message' => 'Item not found in inventory'
            ];
        }

        return [
            'available' => $inventory->current_stock >= $quantity,
            'current_stock' => $inventory->current_stock,
            'message' => $inventory->current_stock >= $quantity 
                ? 'Item available' 
                : 'Insufficient stock'
        ];
    }
}