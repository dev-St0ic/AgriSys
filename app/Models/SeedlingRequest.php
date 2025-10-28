<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class SeedlingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Foreign key to user_registration table
        'request_number',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'contact_number',
        'email',
        'address',
        'barangay',
        'planting_location',
        'purpose',
        'total_quantity',
        'approved_quantity',
        'preferred_delivery_date',
        'document_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remarks',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'preferred_delivery_date' => 'date',
        'total_quantity' => 'integer',
        'approved_quantity' => 'integer'
    ];

    /**
     * Relationship: Seedling request belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(UserRegistration::class, 'user_id');
    }

    /**
     * Get all request items
     */
    public function items()
    {
        return $this->hasMany(SeedlingRequestItem::class);
    }

    /**
     * Get items grouped by category
     */
    public function itemsByCategory()
    {
        return $this->items()->with(['category', 'categoryItem'])->get()->groupBy('category_id');
    }

    /**
     * Get approved items only
     */
    public function approvedItems()
    {
        return $this->items()->where('status', 'approved');
    }

    /**
     * Get rejected items only
     */
    public function rejectedItems()
    {
        return $this->items()->where('status', 'rejected');
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'partially_approved' => 'warning',
            'rejected' => 'danger',
            'under_review' => 'info',
            'cancelled' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Calculate and update overall status based on items
     */
    public function updateOverallStatus(): void
    {
        $items = $this->items;

        if ($items->isEmpty()) {
            $this->update(['status' => 'pending']);
            return;
        }

        $totalItems = $items->count();
        $approvedCount = $items->where('status', 'approved')->count();
        $rejectedCount = $items->where('status', 'rejected')->count();

        if ($approvedCount === $totalItems) {
            $status = 'approved';
            $this->update([
                'status' => $status,
                'approved_at' => now(),
                'approved_quantity' => $items->sum('approved_quantity')
            ]);
        } elseif ($rejectedCount === $totalItems) {
            $status = 'rejected';
            $this->update([
                'status' => $status,
                'rejected_at' => now()
            ]);
        } elseif ($approvedCount > 0) {
            $status = 'partially_approved';
            $this->update([
                'status' => $status,
                'approved_quantity' => $items->where('status', 'approved')->sum('approved_quantity')
            ]);
        } else {
            $this->update(['status' => 'under_review']);
        }
    }

    /**
     * Check inventory availability for all items
     */
    public function checkInventoryAvailability(): array
    {
        $results = [];
        $canFulfill = true;
        $availableItems = [];
        $unavailableItems = [];

        foreach ($this->items as $item) {
            if ($item->categoryItem) {
                $check = $item->categoryItem->checkInventoryAvailability($item->requested_quantity);

                $itemData = [
                    'name' => $item->item_name,
                    'category' => $item->category->display_name,
                    'needed' => $item->requested_quantity,
                    'available' => $check['current_stock']
                ];

                if ($check['available']) {
                    $availableItems[] = $itemData;
                } else {
                    $unavailableItems[] = $itemData;
                    $canFulfill = false;
                }

                $results[] = array_merge($itemData, ['can_fulfill' => $check['available']]);
            }
        }

        return [
            'can_fulfill' => $canFulfill,
            'available_items' => $availableItems,
            'unavailable_items' => $unavailableItems,
            'details' => $results
        ];
    }

    /**
     * Deduct items from inventory - DISABLED FOR NEW SUPPLY MANAGEMENT
     */
    // public function deductFromInventory(): bool
    // {
    //     \DB::beginTransaction();

    //     try {
    //         foreach ($this->approvedItems as $item) {
    //             if (!$item->categoryItem) continue;

    //             $inventory = \App\Models\Inventory::where('item_name', 'LIKE', "%{$item->item_name}%")
    //                 ->where('category', $item->category->getInventoryCategoryName())
    //                 ->where('is_active', true)
    //                 ->first();

    //             if ($inventory && $inventory->current_stock >= $item->approved_quantity) {
    //                 $inventory->decrement('current_stock', $item->approved_quantity);
    //                 $inventory->update(['updated_by' => auth()->id()]);
    //             } else {
    //                 throw new \Exception("Insufficient stock for {$item->item_name}");
    //             }
    //         }

    //         \DB::commit();
    //         return true;
    //     } catch (\Exception $e) {
    //         \DB::rollback();
    //         \Log::error("Inventory deduction failed: " . $e->getMessage());
    //         return false;
    //     }
    // }

    /**
     * Restore items to inventory - DISABLED FOR NEW SUPPLY MANAGEMENT
     */
    // public function restoreToInventory(): bool
    // {
    //     \DB::beginTransaction();

    //     try {
    //         foreach ($this->approvedItems as $item) {
    //             if (!$item->categoryItem) continue;

    //             $inventory = \App\Models\Inventory::where('item_name', 'LIKE', "%{$item->item_name}%")
    //                 ->where('category', $item->category->getInventoryCategoryName())
    //                 ->where('is_active', true)
    //                 ->first();

    //             if ($inventory) {
    //                 $inventory->increment('current_stock', $item->approved_quantity);
    //                 $inventory->update(['updated_by' => auth()->id()]);
    //             }
    //         }

    //         \DB::commit();
    //         return true;
    //     } catch (\Exception $e) {
    //         \DB::rollback();
    //         \Log::error("Inventory restoration failed: " . $e->getMessage());
    //         return false;
    //     }
    // }

    /**
     * Document methods
     */
    public function hasDocuments(): bool
    {
        return !empty($this->document_path) && Storage::disk('public')->exists($this->document_path);
    }

    public function getDocumentUrlAttribute(): ?string
    {
        if ($this->hasDocuments()) {
            return Storage::disk('public')->url($this->document_path);
        }
        return null;
    }

    /**
     * Reviewer relationship
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Generate unique request number
     */
    public static function generateRequestNumber(): string
    {
        do {
            $number = 'REQ-' . strtoupper(\Str::random(8));
        } while (self::where('request_number', $number)->exists());

        return $number;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = self::generateRequestNumber();
            }
        });
    }
}
