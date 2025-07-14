<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventory.
     */
    public function index(Request $request)
    {
        $query = Inventory::with(['creator', 'updater']);

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->lowStock();
                    break;
                case 'out':
                    $query->where('current_stock', '<=', 0);
                    break;
                case 'available':
                    $query->where('current_stock', '>', 0);
                    break;
            }
        }

        // Search by item name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('item_name', 'like', '%' . $request->search . '%')
                  ->orWhere('variety', 'like', '%' . $request->search . '%');
            });
        }

        $inventories = $query->orderBy('item_name')->paginate(15);
        
        // Get statistics
        $stats = [
            'total_items' => Inventory::active()->count(),
            'low_stock_items' => Inventory::active()->lowStock()->count(),
            'out_of_stock_items' => Inventory::active()->where('current_stock', '<=', 0)->count(),
            'total_stock' => Inventory::active()->sum('current_stock'),
        ];

        return view('admin.inventory.index', compact('inventories', 'stats'));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        return view('admin.inventory.create');
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:vegetables,fruits,fertilizers',
            'variety' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'last_restocked' => 'nullable|date',
        ]);

        $inventory = Inventory::create([
            ...$request->all(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item created successfully.');
    }

    /**
     * Display the specified inventory item.
     */
    public function show(Inventory $inventory)
    {
        $inventory->load(['creator', 'updater']);
        return view('admin.inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(Inventory $inventory)
    {
        return view('admin.inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:vegetables,fruits,fertilizers',
            'variety' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'last_restocked' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $inventory->update([
            ...$request->all(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified inventory item.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    /**
     * Adjust stock for an inventory item
     */
    public function adjustStock(Request $request, Inventory $inventory)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $oldStock = $inventory->current_stock;

        switch ($request->adjustment_type) {
            case 'add':
                $newStock = $oldStock + $request->quantity;
                break;
            case 'subtract':
                $newStock = max(0, $oldStock - $request->quantity);
                break;
            case 'set':
                $newStock = $request->quantity;
                break;
        }

        $inventory->update([
            'current_stock' => $newStock,
            'updated_by' => Auth::id(),
            'last_restocked' => $request->adjustment_type === 'add' ? now() : $inventory->last_restocked,
        ]);

        return redirect()->back()
            ->with('success', "Stock adjusted from {$oldStock} to {$newStock} {$inventory->unit}.");
    }
}
