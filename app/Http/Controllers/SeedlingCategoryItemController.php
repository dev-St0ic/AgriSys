<?php

namespace App\Http\Controllers;

use App\Models\RequestCategory;
use App\Models\CategoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeedlingCategoryItemController extends Controller
{
    // ==========================================
    // CATEGORY CRUD OPERATIONS
    // ==========================================
    
    public function indexCategories()
    {
        $categories = RequestCategory::with(['items' => function($query) {
            $query->orderBy('name', 'asc');
        }])->orderBy('display_order')->get();
        
        return view('admin.seedlings.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:request_categories',
            'display_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
        ]);

        // Set category as inactive by default when created
        $validated['is_active'] = false;

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
        // Check if category has at least one active item before allowing activation
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'unit' => 'required|string|max:20',
            'min_quantity' => 'nullable|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Set display_order to 0 by default (will be sorted alphabetically in queries)
        $validated['display_order'] = 0;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('category-items', 'public');
        }

        $item = CategoryItem::create($validated);

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
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('category-items', 'public');
        }

        $oldCategoryId = $item->category_id;
        $item->update($validated);

        // Check if old category should be deactivated (no active items left)
        $oldCategory = RequestCategory::find($oldCategoryId);
        if ($oldCategory && $oldCategory->is_active) {
            $hasActiveItems = $oldCategory->items()->where('is_active', true)->exists();
            if (!$hasActiveItems) {
                $oldCategory->update(['is_active' => false]);
            }
        }

        // Check if new category should be activated (has active items now)
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

        // Delete image
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        // Check if category should be deactivated (no active items left)
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

        // If activating item, activate category if needed
        if ($newStatus && !$category->is_active) {
            $category->update(['is_active' => true]);
            $message = 'Item activated and category has been activated';
        }
        // If deactivating item, check if category should be deactivated
        elseif (!$newStatus && $category->is_active) {
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
        return response()->json($item->load('category'));
    }
}