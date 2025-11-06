<?php

namespace App\Http\Controllers;

use App\Models\RequestCategory;
use App\Models\CategoryItem;
use App\Models\ItemSupplyLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SeedlingCategoryItemController extends Controller
{
    // ==========================================
    // CATEGORY CRUD OPERATIONS
    // ==========================================
    
    public function indexCategories()
    {
        $categories = RequestCategory::with(['items' => function($query) {
            $query->orderBy('name', 'asc');
        }])->orderBy('display_order', 'asc')->get();
        
        // Get supply statistics
        $totalItems = CategoryItem::count();
        $lowSupplyItems = CategoryItem::lowSupply()->count();
        $outOfSupplyItems = CategoryItem::outOfSupply()->count();
        $totalSupply = CategoryItem::sum('current_supply');
        
        return view('admin.seedlings.categories.index', compact(
            'categories',
            'totalItems',
            'lowSupplyItems',
            'outOfSupplyItems',
            'totalSupply'
        ));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:request_categories',
            'display_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = false;
        $maxOrder = RequestCategory::max('display_order') ?? -1;
        $validated['display_order'] = $maxOrder + 1;

        $category = RequestCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully. Add items to activate it.',
            'category' => $category->load('items')
        ]);
    }

    public function updateCategory(Request $request, RequestCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:request_categories,name,' . $category->id,
            'display_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category->load('items')
        ]);
    }

    public function destroyCategory(RequestCategory $category)
    {
        if ($category->items()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing items. Please delete all items first.'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    public function toggleCategoryStatus(RequestCategory $category)
    {
        if (!$category->is_active) {
            $hasActiveItems = $category->items()->where('is_active', true)->exists();
            
            if (!$hasActiveItems) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot activate category without active items. Please add and activate at least one item first.'
                ], 422);
            }
        }

        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Category status updated successfully',
            'is_active' => $category->is_active
        ]);
    }

    public function showCategory(RequestCategory $category)
    {
        return response()->json($category->load(['items' => function($query) {
            $query->orderBy('name', 'asc');
        }]));
    }

    // ==========================================
    // ITEM CRUD OPERATIONS
    // ==========================================

    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:request_categories,id',
            'name' => 'required|string|max:255|unique:category_items,name,NULL,id,category_id,' . $request->category_id,  
            'description' => 'nullable|string|max:500',
            'unit' => 'required|string|max:20',
            'min_quantity' => 'nullable|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            // Supply fields
            'current_supply' => 'nullable|integer|min:0',
            'minimum_supply' => 'nullable|integer|min:0',
            'maximum_supply' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'supply_alert_enabled' => 'nullable|boolean',
        ]);

        $validated['display_order'] = 0;
        $validated['current_supply'] = $validated['current_supply'] ?? 0;
        $validated['minimum_supply'] = $validated['minimum_supply'] ?? 0;
        $validated['supply_alert_enabled'] = $validated['supply_alert_enabled'] ?? true;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('category-items', 'public');
        }

        $item = CategoryItem::create($validated);

        // Log initial supply if set
        if ($item->current_supply > 0) {
            ItemSupplyLog::create([
                'category_item_id' => $item->id,
                'transaction_type' => 'initial_supply',
                'quantity' => $item->current_supply,
                'old_supply' => 0,
                'new_supply' => $item->current_supply,
                'performed_by' => auth()->id(),
                'notes' => 'Initial supply setup',
            ]);
        }

        // Auto-activate category when first active item is added
        $category = RequestCategory::find($validated['category_id']);
        if (!$category->is_active && $item->is_active) {
            $category->update(['is_active' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully' . (!$category->is_active ? ' and category has been activated' : ''),
            'item' => $item->load('category')
        ]);
    }

    public function updateItem(Request $request, CategoryItem $item)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:request_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'unit' => 'required|string|max:20',
            'min_quantity' => 'nullable|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            // Supply fields (not directly updatable here - use supply management endpoints)
            'minimum_supply' => 'nullable|integer|min:0',
            'maximum_supply' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'supply_alert_enabled' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('category-items', 'public');
        }

        $oldCategoryId = $item->category_id;
        $item->update($validated);

        // Check if old category should be deactivated
        $oldCategory = RequestCategory::find($oldCategoryId);
        if ($oldCategory && $oldCategory->is_active) {
            $hasActiveItems = $oldCategory->items()->where('is_active', true)->exists();
            if (!$hasActiveItems) {
                $oldCategory->update(['is_active' => false]);
            }
        }

        // Check if new category should be activated
        $newCategory = RequestCategory::find($validated['category_id']);
        if ($newCategory && !$newCategory->is_active && $item->is_active) {
            $newCategory->update(['is_active' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'item' => $item->load('category')
        ]);
    }

    public function destroyItem(CategoryItem $item)
    {
        if ($item->requestItems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete item that has been used in requests'
            ], 422);
        }

        $categoryId = $item->category_id;

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        // Check if category should be deactivated
        $category = RequestCategory::find($categoryId);
        if ($category && $category->is_active) {
            $hasActiveItems = $category->items()->where('is_active', true)->exists();
            if (!$hasActiveItems) {
                $category->update(['is_active' => false]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
    }

    public function toggleItemStatus(CategoryItem $item)
    {
        $newStatus = !$item->is_active;
        $item->update(['is_active' => $newStatus]);

        $category = $item->category;

        if ($newStatus && !$category->is_active) {
            $category->update(['is_active' => true]);
            $message = 'Item activated and category has been activated';
        } elseif (!$newStatus && $category->is_active) {
            $hasActiveItems = $category->items()->where('is_active', true)->exists();
            if (!$hasActiveItems) {
                $category->update(['is_active' => false]);
                $message = 'Item deactivated and category has been deactivated (no active items remaining)';
            } else {
                $message = 'Item status updated successfully';
            }
        } else {
            $message = 'Item status updated successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_active' => $item->is_active
        ]);
    }

    public function showItem(CategoryItem $item)
    {
        return response()->json($item->load('category', 'supplyLogs'));
    }

    /**
     * Get real-time stock status for items
     * POST /api/seedlings/stock-status
     */
    public function getStockStatus(Request $request)
    {
        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'required|integer|exists:category_items,id'
        ]);

        $items = CategoryItem::whereIn('id', $validated['item_ids'])
            ->select('id', 'name', 'unit', 'current_supply', 'min_quantity', 'max_quantity', 'reorder_point')
            ->get();

        $itemsData = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'unit' => $item->unit,
                'current_supply' => $item->current_supply,
                'min_quantity' => $item->min_quantity,
                'max_quantity' => $item->max_quantity,
                'reorder_point' => $item->reorder_point,
                'stock_status' => $item->stock_status, // Uses the getStockStatusAttribute from model
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $itemsData
        ]);
    }

    // ==========================================
    // SUPPLY MANAGEMENT OPERATIONS
    // ==========================================

    /**
     * Add supply (receive new supplies)
     */
    public function addSupply(Request $request, CategoryItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'source' => 'nullable|string|max:255',
        ]);

        $success = $item->addSupply(
            $validated['quantity'],
            auth()->id(),
            $validated['notes'],
            $validated['source'] ?? null
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "Successfully added {$validated['quantity']} {$item->unit} to supply",
                'item' => $item->fresh()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to add supply. Check maximum supply limit.'
        ], 422);
    }

    /**
     * Adjust supply manually
     */
    public function adjustSupply(Request $request, CategoryItem $item)
    {
        $validated = $request->validate([
            'new_supply' => 'required|integer|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $success = $item->adjustSupply(
            $validated['new_supply'],
            auth()->id(),
            $validated['reason']
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Supply adjusted successfully',
                'item' => $item->fresh()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to adjust supply. Check constraints.'
        ], 422);
    }

    /**
     * Record supply loss (expired/damaged)
     */
    public function recordLoss(Request $request, CategoryItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ]);

        $success = $item->recordLoss(
            $validated['quantity'],
            auth()->id(),
            $validated['reason']
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "Supply loss of {$validated['quantity']} {$item->unit} recorded",
                'item' => $item->fresh()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to record loss. Insufficient supply.'
        ], 422);
    }

    /**
     * Get supply logs for an item
     */
    public function getSupplyLogs(CategoryItem $item)
    {
        $logs = $item->supplyLogs()
            ->with('performedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($logs);
    }

    /**
     * Get supply statistics
     */
    public function getSupplyStats()
    {
        $stats = [
            'total_items' => CategoryItem::count(),
            'active_items' => CategoryItem::active()->count(),
            'low_supply_items' => CategoryItem::lowSupply()->count(),
            'out_of_supply_items' => CategoryItem::outOfSupply()->count(),
            'total_supply_value' => CategoryItem::sum('current_supply'),
            'categories_with_low_supply' => RequestCategory::whereHas('items', function($q) {
                $q->whereColumn('current_supply', '<=', 'reorder_point');
            })->count(),
        ];

        return response()->json($stats);
    }
}