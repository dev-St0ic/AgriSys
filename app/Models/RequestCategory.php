<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'icon',
        'description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get items belonging to this category
     */
    public function items()
    {
        return $this->hasMany(CategoryItem::class, 'category_id');
    }

    /**
     * Get active items only
     */
    public function activeItems()
    {
        return $this->items()->where('is_available', true)->orderBy('sort_order');
    }

    /**
     * Get request items for this category
     */
    public function requestItems()
    {
        return $this->hasMany(SeedlingRequestItem::class, 'category_id');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    /**
     * Get inventory category name mapping
     */
    public function getInventoryCategoryName(): string
    {
        // Map to inventory category names
        $mapping = [
            'seeds' => 'seed',
            'seedlings' => 'seedling',
            'fruits' => 'fruit',
            'ornamentals' => 'ornamental',
            'fingerlings' => 'fingerling',
            'fertilizers' => 'fertilizer'
        ];

        return $mapping[$this->name] ?? $this->name;
    }
}