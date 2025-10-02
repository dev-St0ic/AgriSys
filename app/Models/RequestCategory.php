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
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    /**
     * Get all items in this category
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
        return $this->hasMany(CategoryItem::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('display_order');
    }

    /**
     * Get request items
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
        return $query->orderBy('display_order')->orderBy('display_name');
    }

    /**
     * Get inventory category name for mapping
     */
    public function getInventoryCategoryName(): string
    {
        $mapping = [
            'seeds' => 'Seeds',
            'seedlings' => 'Seedlings',
            'fruits' => 'Fruit Trees',
            'ornamentals' => 'Ornamental Plants',
            'fingerlings' => 'Fingerlings',
            'fertilizers' => 'Fertilizers',
            'tools' => 'Tools',
            'equipment' => 'Equipment'
        ];

        return $mapping[$this->name] ?? $this->display_name;
    }
}