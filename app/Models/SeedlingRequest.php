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
        
        // 6 Categories
        'seeds',
        'seedlings',
        'fruits',
        'ornamentals',
        'fingerlings',
        'fertilizers',
        
        'requested_quantity',
        'total_quantity',
        'preferred_delivery_date',
        'document_path',
        'status',
        
        // Category Status Fields
        'seeds_status',
        'seedlings_status',
        'fruits_status',
        'ornamentals_status',
        'fingerlings_status',
        'fertilizers_status',
        
        // Approved Items
        'seeds_approved_items',
        'seedlings_approved_items',
        'fruits_approved_items',
        'ornamentals_approved_items',
        'fingerlings_approved_items',
        'fertilizers_approved_items',
        
        // Rejected Items
        'seeds_rejected_items',
        'seedlings_rejected_items',
        'fruits_rejected_items',
        'ornamentals_rejected_items',
        'fingerlings_rejected_items',
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
        
        // 6 Categories - Cast to array instead of json for better handling
        'seeds' => 'array',
        'seedlings' => 'array',
        'fruits' => 'array',
        'ornamentals' => 'array',
        'fingerlings' => 'array',
        'fertilizers' => 'array',
        
        // Approved Items - Cast to array instead of json
        'seeds_approved_items' => 'array',
        'seedlings_approved_items' => 'array',
        'fruits_approved_items' => 'array',
        'ornamentals_approved_items' => 'array',
        'fingerlings_approved_items' => 'array',
        'fertilizers_approved_items' => 'array',
        
        // Rejected Items - Cast to array instead of json
        'seeds_rejected_items' => 'array',
        'seedlings_rejected_items' => 'array',
        'fruits_rejected_items' => 'array',
        'ornamentals_rejected_items' => 'array',
        'fingerlings_rejected_items' => 'array',
        'fertilizers_rejected_items' => 'array'
    ];

    // Define the categories array for easy iteration
    public static $categories = ['seeds', 'seedlings', 'fruits', 'ornamentals', 'fingerlings', 'fertilizers'];

    /**
     * Helper method to check if a category has items (safe version)
     */
    public function hasItemsInCategory($categoryField): bool
    {
        $items = $this->getCategoryItems($categoryField);
        return !empty($items) && count($items) > 0;
    }

    /**
     * Safely get category items as array
     */
    public function getCategoryItems($categoryField): array
    {
        $items = $this->{$categoryField};
        
        // If null or empty
        if (empty($items)) {
            return [];
        }
        
        // If already an array, return it
        if (is_array($items)) {
            return $items;
        }
        
        // If it's a string (shouldn't happen with proper casts, but safety first)
        if (is_string($items)) {
            // Handle empty JSON strings
            if ($items === '[]' || $items === 'null' || $items === '') {
                return [];
            }
            
            try {
                $decoded = json_decode($items, true);
                return is_array($decoded) ? $decoded : [];
            } catch (\Exception $e) {
                \Log::warning("Failed to decode JSON for {$categoryField}: " . $e->getMessage());
                return [];
            }
        }
        
        return [];
    }

    /**
     * Get category items count safely
     */
    public function getCategoryItemsCount($categoryField): int
    {
        return count($this->getCategoryItems($categoryField));
    }

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
            $this->seeds_status,
            $this->seedlings_status,
            $this->fruits_status,
            $this->ornamentals_status,
            $this->fingerlings_status,
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
     * Check inventory availability by category (enhanced with safe item access)
     */
    public function checkCategoryInventoryAvailability(string $category): array
    {
        if (!in_array($category, self::$categories)) {
            throw new \InvalidArgumentException("Invalid category: {$category}");
        }

        $availableItems = [];
        $unavailableItems = [];
        $canFulfill = true;
        $items = $this->getCategoryItems($category); // Use safe method

        if (!empty($items)) {
            foreach ($items as $item) {
                // Handle both array and string item formats
                $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
                $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
                
                if (empty($itemName)) continue;

                $inventory = Inventory::where('item_name', 'LIKE', "%{$itemName}%")
                    ->where('category', $this->mapCategoryForInventory($category))
                    ->where('is_active', true)
                    ->first();

                if (!$inventory) {
                    $unavailableItems[] = [
                        'name' => $itemName,
                        'needed' => $itemQuantity,
                        'available' => 0
                    ];
                    $canFulfill = false;
                } elseif ($inventory->current_stock < $itemQuantity) {
                    $unavailableItems[] = [
                        'name' => $itemName,
                        'needed' => $itemQuantity,
                        'available' => $inventory->current_stock
                    ];
                    $canFulfill = false;
                } else {
                    $availableItems[] = [
                        'name' => $itemName,
                        'needed' => $itemQuantity,
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
     * Map category names to inventory category names if needed
     */
    private function mapCategoryForInventory(string $category): string
    {
        return match($category) {
            'seeds' => 'seed',
            'seedlings' => 'seedling', 
            'fruits' => 'fruit',
            'ornamentals' => 'ornamental',
            'fingerlings' => 'fingerling',
            'fertilizers' => 'fertilizer',
            default => $category
        };
    }

    /**
     * Check inventory availability for all categories
     */
    public function checkInventoryAvailability(): array
    {
        $results = [];
        $overallCanFulfill = true;
        $allAvailable = [];
        $allUnavailable = [];

        foreach (self::$categories as $category) {
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
     * Deduct inventory for a specific category (enhanced with safe item access)
     */
    public function deductCategoryFromInventory(string $category): bool
    {
        if (!in_array($category, self::$categories)) {
            throw new \InvalidArgumentException("Invalid category: {$category}");
        }

        \DB::beginTransaction();

        try {
            $items = $this->getCategoryItems($category); // Use safe method

            if (!empty($items)) {
                foreach ($items as $item) {
                    $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
                    $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
                    
                    if (empty($itemName)) continue;

                    $inventory = Inventory::where('item_name', 'LIKE', "%{$itemName}%")
                        ->where('category', $this->mapCategoryForInventory($category))
                        ->where('is_active', true)
                        ->first();

                    if ($inventory && $inventory->current_stock >= $itemQuantity) {
                        $inventory->decrement('current_stock', $itemQuantity);
                        $inventory->update(['updated_by' => auth()->id()]);
                    } else {
                        throw new \Exception("Insufficient stock for {$itemName} in {$category}");
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
        if (!in_array($category, self::$categories)) {
            throw new \InvalidArgumentException("Invalid category: {$category}");
        }

        \DB::beginTransaction();

        try {
            $categoryStatus = $this->{$category . '_status'};

            // Only restore if the category was previously approved
            if ($categoryStatus !== 'approved') {
                return true; // Nothing to restore
            }

            $items = $this->getCategoryItems($category); // Use safe method

            if (!empty($items)) {
                foreach ($items as $item) {
                    $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
                    $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
                    
                    if (empty($itemName)) continue;

                    $inventory = Inventory::where('item_name', 'LIKE', "%{$itemName}%")
                        ->where('category', $this->mapCategoryForInventory($category))
                        ->where('is_active', true)
                        ->first();

                    if ($inventory) {
                        $inventory->increment('current_stock', $itemQuantity);
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
        if (!in_array($category, self::$categories)) {
            throw new \InvalidArgumentException("Invalid category: {$category}");
        }

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
     * Get formatted items for a category with approval status (enhanced with safe item access)
     */
    public function getFormattedCategoryItems(string $category): string
    {
        if (!in_array($category, self::$categories)) {
            return '';
        }

        $items = $this->getCategoryItems($category); // Use safe method
        $status = $this->{$category . '_status'} ?? 'under_review';

        if (empty($items)) {
            return '';
        }

        $formatted = collect($items)->map(function($item) {
            $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
            $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
            return $itemName . ' (' . $itemQuantity . ' pcs)';
        })->filter()->implode(', '); // filter() removes empty items

        $statusBadge = match($status) {
            'approved' => '<span class="badge bg-success ms-2">Approved</span>',
            'rejected' => '<span class="badge bg-danger ms-2">Rejected</span>',
            'partially_approved' => '<span class="badge bg-warning ms-2">Partial</span>',
            default => '<span class="badge bg-secondary ms-2">Pending</span>'
        };

        return $formatted . ' ' . $statusBadge;
    }

    // Dynamic attribute accessors for all 6 categories
    public function getFormattedSeedsWithStatusAttribute(): string
    {
        return $this->getFormattedCategoryItems('seeds');
    }

    public function getFormattedSeedlingsWithStatusAttribute(): string
    {
        return $this->getFormattedCategoryItems('seedlings');
    }

    public function getFormattedFruitsWithStatusAttribute(): string
    {
        return $this->getFormattedCategoryItems('fruits');
    }

    public function getFormattedOrnamentalsWithStatusAttribute(): string
    {
        return $this->getFormattedCategoryItems('ornamentals');
    }

    public function getFormattedFingerlingsWithStatusAttribute(): string
    {
        return $this->getFormattedCategoryItems('fingerlings');
    }

    public function getFormattedFertilizersWithStatusAttribute(): string
    {
        return $this->getFormattedCategoryItems('fertilizers');
    }

    // Basic formatted methods without status badges (enhanced with safe item access)
    public function getFormattedSeedsAttribute(): string
    {
        $items = $this->getCategoryItems('seeds');
        if (empty($items)) return '';
        return collect($items)->map(function($item) {
            $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
            $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
            return $itemName . ' (' . $itemQuantity . ' pcs)';
        })->filter()->implode(', ');
    }

    public function getFormattedSeedlingsAttribute(): string
    {
        $items = $this->getCategoryItems('seedlings');
        if (empty($items)) return '';
        return collect($items)->map(function($item) {
            $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
            $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
            return $itemName . ' (' . $itemQuantity . ' pcs)';
        })->filter()->implode(', ');
    }

    public function getFormattedFruitsAttribute(): string
    {
        $items = $this->getCategoryItems('fruits');
        if (empty($items)) return '';
        return collect($items)->map(function($item) {
            $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
            $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
            return $itemName . ' (' . $itemQuantity . ' pcs)';
        })->filter()->implode(', ');
    }

    public function getFormattedOrnamentalsAttribute(): string
    {
        $items = $this->getCategoryItems('ornamentals');
        if (empty($items)) return '';
        return collect($items)->map(function($item) {
            $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
            $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
            return $itemName . ' (' . $itemQuantity . ' pcs)';
        })->filter()->implode(', ');
    }

    public function getFormattedFingerlingsAttribute(): string
    {
        $items = $this->getCategoryItems('fingerlings');
        if (empty($items)) return '';
        return collect($items)->map(function($item) {
            $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
            $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
            return $itemName . ' (' . $itemQuantity . ' pcs)';
        })->filter()->implode(', ');
    }

    public function getFormattedFertilizersAttribute(): string
    {
        $items = $this->getCategoryItems('fertilizers');
        if (empty($items)) return '';
        return collect($items)->map(function($item) {
            $itemName = is_array($item) ? ($item['name'] ?? '') : (string)$item;
            $itemQuantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
            return $itemName . ' (' . $itemQuantity . ' pcs)';
        })->filter()->implode(', ');
    }

    // Document methods
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

    // Relationship with User
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}