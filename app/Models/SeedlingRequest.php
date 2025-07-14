<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Inventory;

class SeedlingRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'request_number',
        'first_name',
        'middle_name', 
        'last_name',
        'extension_name',
        'contact_number',
        'address',
        'barangay',
        'planting_location',
        'purpose',
        'seedling_type',
        'vegetables',
        'fruits',
        'fertilizers',
        'requested_quantity',
        'total_quantity',
        'preferred_delivery_date',
        'document_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remarks',
        'approved_quantity',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime', 
        'rejected_at' => 'datetime',
        'preferred_delivery_date' => 'date',
        'requested_quantity' => 'integer',
        'approved_quantity' => 'integer',
        'total_quantity' => 'integer',
        'vegetables' => 'array',
        'fruits' => 'array',
        'fertilizers' => 'array'
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger', 
            'under_review' => 'warning',
            default => 'secondary'
        };
    }

    public function getFormattedSeedlingsAttribute(): string
    {
        $parts = [];
        
        if (!empty($this->vegetables)) {
            $vegNames = collect($this->vegetables)->pluck('name')->toArray();
            $parts[] = 'Vegetables: ' . implode(', ', $vegNames);
        }
        
        if (!empty($this->fruits)) {
            $fruitNames = collect($this->fruits)->pluck('name')->toArray();
            $parts[] = 'Fruits: ' . implode(', ', $fruitNames);
        }
        
        if (!empty($this->fertilizers)) {
            $fertNames = collect($this->fertilizers)->pluck('name')->toArray();
            $parts[] = 'Fertilizers: ' . implode(', ', $fertNames);
        }
        
        return implode(' | ', $parts);
    }

    // Add individual formatters for each category
    public function getFormattedVegetablesAttribute(): string
    {
        if (empty($this->vegetables)) {
            return '';
        }
        
        return collect($this->vegetables)->map(function($item) {
            return $item['name'] . ' (' . $item['quantity'] . ' pcs)';
        })->implode(', ');
    }

    public function getFormattedFruitsAttribute(): string
    {
        if (empty($this->fruits)) {
            return '';
        }
        
        return collect($this->fruits)->map(function($item) {
            return $item['name'] . ' (' . $item['quantity'] . ' pcs)';
        })->implode(', ');
    }

    public function getFormattedFertilizersAttribute(): string
    {
        if (empty($this->fertilizers)) {
            return '';
        }
        
        return collect($this->fertilizers)->map(function($item) {
            return $item['name'] . ' (' . $item['quantity'] . ' pcs)';
        })->implode(', ');
    }

    // Check if request has supporting documents
    public function hasDocuments(): bool
    {
        return !empty($this->document_path) && Storage::disk('public')->exists($this->document_path);
    }

    // Get document URL
    public function getDocumentUrlAttribute(): ?string
    {
        if ($this->hasDocuments()) {
            return Storage::disk('public')->url($this->document_path);
        }
        
        return null;
    }

    /**
     * Check inventory availability for this request
     */
    public function checkInventoryAvailability(): array
    {
        $availableItems = [];
        $unavailableItems = [];
        $canFulfill = true;
        
        // Check vegetables
        if (!empty($this->vegetables)) {
            foreach ($this->vegetables as $vegetable) {
                $inventory = Inventory::where('item_name', $vegetable['name'])
                    ->where('category', 'vegetables')
                    ->where('is_active', true)
                    ->first();
                
                if (!$inventory) {
                    $unavailableItems[] = [
                        'name' => $vegetable['name'],
                        'needed' => $vegetable['quantity'],
                        'available' => 0
                    ];
                    $canFulfill = false;
                } elseif ($inventory->current_stock < $vegetable['quantity']) {
                    $unavailableItems[] = [
                        'name' => $vegetable['name'],
                        'needed' => $vegetable['quantity'],
                        'available' => $inventory->current_stock
                    ];
                    $canFulfill = false;
                } else {
                    $availableItems[] = [
                        'name' => $vegetable['name'],
                        'needed' => $vegetable['quantity'],
                        'available' => $inventory->current_stock
                    ];
                }
            }
        }
        
        // Check fruits
        if (!empty($this->fruits)) {
            foreach ($this->fruits as $fruit) {
                $inventory = Inventory::where('item_name', $fruit['name'])
                    ->where('category', 'fruits')
                    ->where('is_active', true)
                    ->first();
                
                if (!$inventory) {
                    $unavailableItems[] = [
                        'name' => $fruit['name'],
                        'needed' => $fruit['quantity'],
                        'available' => 0
                    ];
                    $canFulfill = false;
                } elseif ($inventory->current_stock < $fruit['quantity']) {
                    $unavailableItems[] = [
                        'name' => $fruit['name'],
                        'needed' => $fruit['quantity'],
                        'available' => $inventory->current_stock
                    ];
                    $canFulfill = false;
                } else {
                    $availableItems[] = [
                        'name' => $fruit['name'],
                        'needed' => $fruit['quantity'],
                        'available' => $inventory->current_stock
                    ];
                }
            }
        }
        
        // Check fertilizers
        if (!empty($this->fertilizers)) {
            foreach ($this->fertilizers as $fertilizer) {
                $inventory = Inventory::where('item_name', $fertilizer['name'])
                    ->where('category', 'fertilizers')
                    ->where('is_active', true)
                    ->first();
                
                if (!$inventory) {
                    $unavailableItems[] = [
                        'name' => $fertilizer['name'],
                        'needed' => $fertilizer['quantity'],
                        'available' => 0
                    ];
                    $canFulfill = false;
                } elseif ($inventory->current_stock < $fertilizer['quantity']) {
                    $unavailableItems[] = [
                        'name' => $fertilizer['name'],
                        'needed' => $fertilizer['quantity'],
                        'available' => $inventory->current_stock
                    ];
                    $canFulfill = false;
                } else {
                    $availableItems[] = [
                        'name' => $fertilizer['name'],
                        'needed' => $fertilizer['quantity'],
                        'available' => $inventory->current_stock
                    ];
                }
            }
        }
        
        return [
            'can_fulfill' => $canFulfill,
            'available_items' => $availableItems,
            'unavailable_items' => $unavailableItems
        ];
    }

    /**
     * Deduct inventory when request is approved
     */
    public function deductFromInventory(): bool
    {
        \DB::beginTransaction();
        
        try {
            // Deduct vegetables
            if (!empty($this->vegetables)) {
                foreach ($this->vegetables as $vegetable) {
                    $inventory = Inventory::where('item_name', $vegetable['name'])
                        ->where('category', 'vegetables')
                        ->where('is_active', true)
                        ->first();
                    
                    if ($inventory && $inventory->current_stock >= $vegetable['quantity']) {
                        $inventory->decrement('current_stock', $vegetable['quantity']);
                        $inventory->update(['updated_by' => auth()->id()]);
                    } else {
                        throw new \Exception("Insufficient stock for {$vegetable['name']}");
                    }
                }
            }
            
            // Deduct fruits
            if (!empty($this->fruits)) {
                foreach ($this->fruits as $fruit) {
                    $inventory = Inventory::where('item_name', $fruit['name'])
                        ->where('category', 'fruits')
                        ->where('is_active', true)
                        ->first();
                    
                    if ($inventory && $inventory->current_stock >= $fruit['quantity']) {
                        $inventory->decrement('current_stock', $fruit['quantity']);
                        $inventory->update(['updated_by' => auth()->id()]);
                    } else {
                        throw new \Exception("Insufficient stock for {$fruit['name']}");
                    }
                }
            }
            
            // Deduct fertilizers
            if (!empty($this->fertilizers)) {
                foreach ($this->fertilizers as $fertilizer) {
                    $inventory = Inventory::where('item_name', $fertilizer['name'])
                        ->where('category', 'fertilizers')
                        ->where('is_active', true)
                        ->first();
                    
                    if ($inventory && $inventory->current_stock >= $fertilizer['quantity']) {
                        $inventory->decrement('current_stock', $fertilizer['quantity']);
                        $inventory->update(['updated_by' => auth()->id()]);
                    } else {
                        throw new \Exception("Insufficient stock for {$fertilizer['name']}");
                    }
                }
            }
            
            \DB::commit();
            return true;
            
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Inventory deduction failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore inventory when request is rejected or cancelled
     */
    public function restoreToInventory(): bool
    {
        \DB::beginTransaction();
        
        try {
            // Only restore if the request was previously approved
            if ($this->status !== 'approved') {
                return true; // Nothing to restore
            }
            
            // Restore vegetables
            if (!empty($this->vegetables)) {
                foreach ($this->vegetables as $vegetable) {
                    $inventory = Inventory::where('item_name', $vegetable['name'])
                        ->where('category', 'vegetables')
                        ->where('is_active', true)
                        ->first();
                    
                    if ($inventory) {
                        $inventory->increment('current_stock', $vegetable['quantity']);
                        $inventory->update(['updated_by' => auth()->id()]);
                    }
                }
            }
            
            // Restore fruits
            if (!empty($this->fruits)) {
                foreach ($this->fruits as $fruit) {
                    $inventory = Inventory::where('item_name', $fruit['name'])
                        ->where('category', 'fruits')
                        ->where('is_active', true)
                        ->first();
                    
                    if ($inventory) {
                        $inventory->increment('current_stock', $fruit['quantity']);
                        $inventory->update(['updated_by' => auth()->id()]);
                    }
                }
            }
            
            // Restore fertilizers
            if (!empty($this->fertilizers)) {
                foreach ($this->fertilizers as $fertilizer) {
                    $inventory = Inventory::where('item_name', $fertilizer['name'])
                        ->where('category', 'fertilizers')
                        ->where('is_active', true)
                        ->first();
                    
                    if ($inventory) {
                        $inventory->increment('current_stock', $fertilizer['quantity']);
                        $inventory->update(['updated_by' => auth()->id()]);
                    }
                }
            }
            
            \DB::commit();
            return true;
            
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Inventory restoration failed: ' . $e->getMessage());
            return false;
        }
    }
}