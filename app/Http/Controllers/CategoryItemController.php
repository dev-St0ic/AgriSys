<?php

namespace App\Http\Controllers;

use App\Models\RequestCategory;
use App\Models\CategoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryItemController extends Controller
{
    // ==========================================
    // CATEGORY CRUD OPERATIONS
    // ==========================================
    
    public function indexCategories()
    {
        $categories = RequestCategory::with('items')->orderBy('display_order')->get();
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

        $category = RequestCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
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
        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Category status updated successfully',
            'is_active' => $category->is_active
        ]);
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
            'display_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('category-items', 'public');
        }

        $item = CategoryItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully',
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
            'display_order' => 'nullable|integer|min:0',
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

        $item->update($validated);

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

        // Delete image
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
    }

    public function toggleItemStatus(CategoryItem $item)
    {
        $item->update(['is_active' => !$item->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Item status updated successfully',
            'is_active' => $item->is_active
        ]);
    }

    public function showCategory(RequestCategory $category)
    {
        return response()->json($category->load('items'));
    }

    public function showItem(CategoryItem $item)
    {
        return response()->json($item->load('category'));
    }
}