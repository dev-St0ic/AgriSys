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
        'email',
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
        'vegetables_status',
        'fruits_status',
        'fertilizers_status',
        'vegetables_approved_items',
        'fruits_approved_items',
        'fertilizers_approved_items',
        'vegetables_rejected_items',
        'fruits_rejected_items',
        'fertilizers_rejected_items',
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
        'fertilizers' => 'array',
        'vegetables_approved_items' => 'array',
        'fruits_approved_items' => 'array',
        'fertilizers_approved_items' => 'array',
        'vegetables_rejected_items' => 'array',
        'fruits_rejected_items' => 'array',
        'fertilizers_rejected_items' => 'array'
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'partially_approved' => 'warning',
            'rejected' => 'danger',
            'under_review' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get overall status based on individual category statuses
     */
    public function getOverallStatusAttribute(): string
    {
        $statuses = array_filter([
            $this->vegetables_status,
            $this->fruits_status,
            $this->fertilizers_status
        ]);

        if (empty($statuses)) return 'under_review';

        $approvedCount = count(array_filter($statuses, fn($s) => $s === 'approved'));
        $rejectedCount = count(array_filter($statuses, fn($s) => $s === 'rejected'));
        $totalCount = count($statuses);

        if ($approvedCount === $totalCount) {
            return 'approved';
        } elseif ($rejectedCount === $totalCount) {
            return 'rejected';
        } elseif ($approvedCount > 0) {
            return 'partially_approved';
        }

        return 'under_review';
    }

    /**
     * Check inventory availability by category
     */
    public function checkCategoryInventoryAvailability(string $category): array
    {
        $availableItems = [];
        $unavailableItems = [];
        $canFulfill = true;
        $items = $this->{$category} ?? [];

        if (!empty($items)) {
            foreach ($items as $item) {
                $inventory = Inventory::where('item_name', $item['name'])
                    ->where('category', $category)
                    ->where('is_active', true)
                    ->first();

                if (!$inventory) {
                    $unavailableItems[] = [
                        'name' => $item['name'],
                        'needed' => $item['quantity'],
                        'available' => 0
                    ];
                    $canFulfill = false;
                } elseif ($inventory->current_stock < $item['quantity']) {
                    $unavailableItems[] = [
                        'name' => $item['name'],
                        'needed' => $item['quantity'],
                        'available' => $inventory->current_stock
                    ];
                    $canFulfill = false;
                } else {
                    $availableItems[] = [
                        'name' => $item['name'],
                        'needed' => $item['quantity'],
                        'available' => $inventory->current_stock
                    ];
                }
            }
        }

        return [
            'can_fulfill' => $canFulfill,
            'available_items' => $availableItems,
            'unavailable_items' => $unavailableItems,
            'has_items' => !empty($items)
        ];
    }

    /**
     * Check inventory availability for all categories
     */
    public function checkInventoryAvailability(): array
    {
        $categories = ['vegetables', 'fruits', 'fertilizers'];
        $results = [];
        $overallCanFulfill = true;
        $allAvailable = [];
        $allUnavailable = [];

        foreach ($categories as $category) {
            $result = $this->checkCategoryInventoryAvailability($category);
            $results[$category] = $result;

            if ($result['has_items'] && !$result['can_fulfill']) {
                $overallCanFulfill = false;
            }

            $allAvailable = array_merge($allAvailable, $result['available_items']);
            $allUnavailable = array_merge($allUnavailable, $result['unavailable_items']);
        }

        return [
            'can_fulfill' => $overallCanFulfill,
            'available_items' => $allAvailable,
            'unavailable_items' => $allUnavailable,
            'by_category' => $results
        ];
    }

    /**
     * Deduct inventory for a specific category
     */
    public function deductCategoryFromInventory(string $category): bool
    {
        \DB::beginTransaction();

        try {
            $items = $this->{$category} ?? [];

            if (!empty($items)) {
                foreach ($items as $item) {
                    $inventory = Inventory::where('item_name', $item['name'])
                        ->where('category', $category)
                        ->where('is_active', true)
                        ->first();

                    if ($inventory && $inventory->current_stock >= $item['quantity']) {
                        $inventory->decrement('current_stock', $item['quantity']);
                        $inventory->update(['updated_by' => auth()->id()]);
                    } else {
                        throw new \Exception("Insufficient stock for {$item['name']} in {$category}");
                    }
                }
            }

            \DB::commit();
            return true;

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error("Inventory deduction failed for {$category}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore inventory for a specific category
     */
    public function restoreCategoryToInventory(string $category): bool
    {
        \DB::beginTransaction();

        try {
            $categoryStatus = $this->{$category . '_status'};

            // Only restore if the category was previously approved
            if ($categoryStatus !== 'approved') {
                return true; // Nothing to restore
            }

            $items = $this->{$category} ?? [];

            if (!empty($items)) {
                foreach ($items as $item) {
                    $inventory = Inventory::where('item_name', $item['name'])
                        ->where('category', $category)
                        ->where('is_active', true)
                        ->first();

                    if ($inventory) {
                        $inventory->increment('current_stock', $item['quantity']);
                        $inventory->update(['updated_by' => auth()->id()]);
                    }
                }
            }

            \DB::commit();
            return true;

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error("Inventory restoration failed for {$category}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update category status and handle inventory
     */
    public function updateCategoryStatus(string $category, string $status, array $approvedItems = []): bool
    {
        $oldStatus = $this->{$category . '_status'};

        // If approving, check inventory first
        if ($status === 'approved') {
            $inventoryCheck = $this->checkCategoryInventoryAvailability($category);

            if (!$inventoryCheck['can_fulfill']) {
                throw new \Exception("Insufficient inventory for {$category}");
            }

            // Deduct from inventory
            if (!$this->deductCategoryFromInventory($category)) {
                throw new \Exception("Failed to deduct {$category} from inventory");
            }
        }

        // If changing from approved to another status, restore inventory
        if ($oldStatus === 'approved' && $status !== 'approved') {
            $this->restoreCategoryToInventory($category);
        }

        // Update the category status
        $this->update([
            $category . '_status' => $status,
            $category . '_approved_items' => $status === 'approved' ? $approvedItems : null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Update overall status
        $this->update(['status' => $this->overall_status]);

        return true;
    }

    /**
     * Get formatted items for a category with approval status
     */
    public function getFormattedCategoryItems(string $category): string
    {
        $items = $this->{$category} ?? [];
        $status = $this->{$category . '_status'} ?? 'under_review';

        if (empty($items)) {
            return '';
        }

        $formatted = collect($items)->map(function($item) {
            return $item['name'] . ' (' . $item['quantity'] . ' pcs)';
        })->implode(', ');

        $statusBadge = match($status) {
            'approved' => '<span class="badge bg-success ms-2">Approved</span>',
            'rejected' => '<span class="badge bg-danger ms-2">Rejected</span>',
            'partially_approved' => '<span class="badge bg-warning ms-2">Partial</span>',
            default => '<span class="badge bg-secondary ms-2">Pending</span>'
        };

        return $formatted . ' ' . $statusBadge;
    }

    public function getFormattedVegetablesWithStatusAttribute(): string
    {
        return $this->getFormattedCategoryItems('vegetables');
    }

    public function getFormattedFruitsWithStatusAttribute(): string
    {
        return $this->getFormattedCategoryItems('fruits');
    }

    public function getFormattedFertilizersWithStatusAttribute(): string
    {
        return $this->getFormattedCategoryItems('fertilizers');
    }

    // Keep existing methods for backward compatibility
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

    // Keep other existing methods...
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
}
