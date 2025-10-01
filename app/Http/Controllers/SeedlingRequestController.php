<?php

namespace App\Http\Controllers;

use App\Models\SeedlingRequest;
use App\Models\SeedlingRequestItem;
use App\Models\RequestCategory;
use App\Models\CategoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationApproved;

class SeedlingRequestController extends Controller
{
    /**
     * Display all seedling requests with filtering and statistics
     */
    public function index(Request $request)
    {
        $query = SeedlingRequest::with(['items.category', 'items.categoryItem'])
            ->orderBy('created_at', 'desc');

        // Add search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('barangay', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($request->has('category') && !empty($request->category)) {
            $categoryId = $request->category;
            $query->whereHas('items', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Add barangay filter
        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('barangay', $request->barangay);
        }

        // Add date filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Paginate results
        $requests = $query->paginate(15)->withQueryString();

        // Get statistics
        $statsQuery = SeedlingRequest::query();
        
        // Apply same filters for stats (except status)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('barangay', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && !empty($request->category)) {
            $categoryId = $request->category;
            $statsQuery->whereHas('items', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $statsQuery->where('barangay', $request->barangay);
        }

        if ($request->has('date_from') && !empty($request->date_from)) {
            $statsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $statsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $totalRequests = $statsQuery->count();
        $underReviewCount = (clone $statsQuery)->where(function($q) {
            $q->where('status', 'under_review')->orWhere('status', 'pending');
        })->count();
        $approvedCount = (clone $statsQuery)->where('status', 'approved')->count();
        $partiallyApprovedCount = (clone $statsQuery)->where('status', 'partially_approved')->count();
        $rejectedCount = (clone $statsQuery)->where('status', 'rejected')->count();

        // Get unique barangays for filter dropdown
        $barangays = SeedlingRequest::distinct()->pluck('barangay')->filter()->sort();

        // Get active categories with items for the index page (for create modal)
        $categories = RequestCategory::with(['items' => function($query) {
            $query->where('is_active', true)->orderBy('display_order');
        }])->where('is_active', true)->orderBy('display_order')->get();

        return view('admin.seedlings.index', compact(
            'requests',
            'totalRequests',
            'underReviewCount',
            'approvedCount',
            'partiallyApprovedCount',
            'rejectedCount',
            'barangays',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new seedling request
     */
    public function create()
    {
        // Load categories with their active items for dynamic selection
        $categories = RequestCategory::with(['items' => function($query) {
            $query->where('is_active', true)->orderBy('display_order');
        }])->where('is_active', true)->orderBy('display_order')->get();
        
        $barangays = SeedlingRequest::distinct()->pluck('barangay')->filter()->sort();
        
        return view('admin.seedlings.create', compact('categories', 'barangays'));
    }

    /**
     * Store a newly created seedling request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Personal Information
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:10',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'barangay' => 'required|string|max:255',
            'planting_location' => 'nullable|string|max:500',
            'purpose' => 'nullable|string|max:1000',
            'preferred_delivery_date' => 'nullable|date|after:today',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            
            // Items - Dynamic array structure
            'items' => 'required|array|min:1',
            'items.*.category_item_id' => 'required|exists:category_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Please add at least one item to the request.',
            'items.min' => 'Please add at least one item to the request.',
        ]);

        try {
            \DB::beginTransaction();

            // Handle document upload
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('seedling-requests', 'public');
            }

            // Create the main request
            $seedlingRequest = SeedlingRequest::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'],
                'contact_number' => $validated['contact_number'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'barangay' => $validated['barangay'],
                'planting_location' => $validated['planting_location'],
                'purpose' => $validated['purpose'],
                'preferred_delivery_date' => $validated['preferred_delivery_date'],
                'document_path' => $documentPath,
                'status' => 'pending',
            ]);

            // Create request items from dynamic selections
            $totalQuantity = 0;
            foreach ($validated['items'] as $itemData) {
                $categoryItem = CategoryItem::with('category')->findOrFail($itemData['category_item_id']);
                
                SeedlingRequestItem::create([
                    'seedling_request_id' => $seedlingRequest->id,
                    'category_id' => $categoryItem->category_id,
                    'category_item_id' => $categoryItem->id,
                    'item_name' => $categoryItem->name,
                    'requested_quantity' => $itemData['quantity'],
                    'status' => 'pending',
                ]);

                $totalQuantity += $itemData['quantity'];
            }

            // Update total quantity
            $seedlingRequest->update(['total_quantity' => $totalQuantity]);

            \DB::commit();

            return redirect()->route('admin.seedlings.requests')
                ->with('success', 'Seedling request created successfully!');

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Failed to create seedling request: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show the form for editing a seedling request
     */
    public function edit(SeedlingRequest $seedlingRequest)
    {
        // Only allow editing pending or under_review requests
        if (!in_array($seedlingRequest->status, ['pending', 'under_review'])) {
            return redirect()->route('admin.seedlings.requests')
                ->with('error', 'Cannot edit approved, rejected, or cancelled requests.');
        }

        // Load categories with active items for dynamic item selection
        $categories = RequestCategory::with(['items' => function($query) {
            $query->where('is_active', true)->orderBy('display_order');
        }])->where('is_active', true)->orderBy('display_order')->get();
        
        $barangays = SeedlingRequest::distinct()->pluck('barangay')->filter()->sort();
        
        // Load existing items with their relationships
        $seedlingRequest->load(['items.category', 'items.categoryItem']);
        
        return view('admin.seedlings.edit', compact('seedlingRequest', 'categories', 'barangays'));
    }

    /**
     * Update a seedling request
     */
    public function update(Request $request, SeedlingRequest $seedlingRequest)
    {
        // Only allow editing pending or under_review requests
        if (!in_array($seedlingRequest->status, ['pending', 'under_review'])) {
            return redirect()->route('admin.seedlings.requests')
                ->with('error', 'Cannot edit approved, rejected, or cancelled requests.');
        }

        $validated = $request->validate([
            // Personal Information
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:10',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'barangay' => 'required|string|max:255',
            'planting_location' => 'nullable|string|max:500',
            'purpose' => 'nullable|string|max:1000',
            'preferred_delivery_date' => 'nullable|date|after:today',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            
            // Items - Dynamic array structure
            'items' => 'required|array|min:1',
            'items.*.category_item_id' => 'required|exists:category_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Please add at least one item to the request.',
            'items.min' => 'Please add at least one item to the request.',
        ]);

        try {
            \DB::beginTransaction();

            // Handle document upload/replacement
            if ($request->hasFile('document')) {
                // Delete old document if exists
                if ($seedlingRequest->document_path) {
                    \Storage::disk('public')->delete($seedlingRequest->document_path);
                }
                $seedlingRequest->document_path = $request->file('document')->store('seedling-requests', 'public');
            }

            // Update main request information
            $seedlingRequest->update([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'extension_name' => $validated['extension_name'],
                'contact_number' => $validated['contact_number'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'barangay' => $validated['barangay'],
                'planting_location' => $validated['planting_location'],
                'purpose' => $validated['purpose'],
                'preferred_delivery_date' => $validated['preferred_delivery_date'],
            ]);

            // Delete all existing items
            $seedlingRequest->items()->delete();

            // Re-create items from the updated dynamic selections
            $totalQuantity = 0;
            foreach ($validated['items'] as $itemData) {
                $categoryItem = CategoryItem::with('category')->findOrFail($itemData['category_item_id']);
                
                SeedlingRequestItem::create([
                    'seedling_request_id' => $seedlingRequest->id,
                    'category_id' => $categoryItem->category_id,
                    'category_item_id' => $categoryItem->id,
                    'item_name' => $categoryItem->name,
                    'requested_quantity' => $itemData['quantity'],
                    'status' => 'pending',
                ]);

                $totalQuantity += $itemData['quantity'];
            }

            // Update total quantity
            $seedlingRequest->update(['total_quantity' => $totalQuantity]);

            \DB::commit();

            return redirect()->route('admin.seedlings.requests')
                ->with('success', 'Seedling request updated successfully!');

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Failed to update seedling request: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove a seedling request (soft delete)
     */
    public function destroy(SeedlingRequest $seedlingRequest)
    {
        try {
            // Only allow deletion of pending, under_review, or rejected requests
            if (!in_array($seedlingRequest->status, ['pending', 'under_review', 'rejected'])) {
                return redirect()->back()
                    ->with('error', 'Cannot delete approved requests. Please reject it first.');
            }

            // If there are approved items, restore inventory
            $approvedItems = $seedlingRequest->items()->where('status', 'approved')->get();
            if ($approvedItems->count() > 0) {
                foreach ($approvedItems as $item) {
                    if ($item->categoryItem) {
                        $inventory = \App\Models\Inventory::where('item_name', 'LIKE', "%{$item->item_name}%")
                            ->where('category', $item->category->getInventoryCategoryName())
                            ->where('is_active', true)
                            ->first();

                        if ($inventory) {
                            $inventory->increment('current_stock', $item->approved_quantity ?? $item->requested_quantity);
                            $inventory->update(['updated_by' => auth()->id()]);
                        }
                    }
                }
            }

            $seedlingRequest->delete();

            return redirect()->route('admin.seedlings.requests')
                ->with('success', 'Seedling request deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to delete seedling request: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete request: ' . $e->getMessage());
        }
    }

    /**
     * Update individual items in a request (for approval/rejection)
     */
    public function updateItems(Request $request, SeedlingRequest $seedlingRequest)
    {
        $request->validate([
            'item_statuses' => 'required|array',
            'item_statuses.*' => 'required|in:pending,approved,rejected',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            $itemStatuses = $request->item_statuses;
            $approvedCount = 0;
            $rejectedCount = 0;
            $totalCount = count($itemStatuses);

            foreach ($itemStatuses as $itemId => $status) {
                $item = SeedlingRequestItem::findOrFail($itemId);
                
                // Check if status is changing from approved to something else
                $wasApproved = $item->status === 'approved';
                
                if ($status === 'approved') {
                    // Check inventory availability
                    if ($item->categoryItem) {
                        $check = $item->categoryItem->checkInventoryAvailability($item->requested_quantity);
                        if (!$check['available']) {
                            throw new \Exception("Insufficient stock for {$item->item_name}. Available: {$check['current_stock']}, Requested: {$item->requested_quantity}");
                        }
                    }
                    
                    $item->update([
                        'status' => 'approved',
                        'approved_quantity' => $item->requested_quantity,
                        'rejection_reason' => null
                    ]);
                    $approvedCount++;
                    
                    // Deduct from inventory if newly approved
                    if (!$wasApproved && $item->categoryItem) {
                        $inventory = \App\Models\Inventory::where('item_name', 'LIKE', "%{$item->item_name}%")
                            ->where('category', $item->category->getInventoryCategoryName())
                            ->where('is_active', true)
                            ->first();

                        if ($inventory && $inventory->current_stock >= $item->requested_quantity) {
                            $inventory->decrement('current_stock', $item->requested_quantity);
                            $inventory->update(['updated_by' => auth()->id()]);
                        }
                    }
                    
                } elseif ($status === 'rejected') {
                    $item->update([
                        'status' => 'rejected',
                        'approved_quantity' => null,
                        'rejection_reason' => $request->input("rejection_reasons.{$itemId}")
                    ]);
                    $rejectedCount++;
                    
                    // Restore inventory if was previously approved
                    if ($wasApproved && $item->categoryItem) {
                        $inventory = \App\Models\Inventory::where('item_name', 'LIKE', "%{$item->item_name}%")
                            ->where('category', $item->category->getInventoryCategoryName())
                            ->where('is_active', true)
                            ->first();

                        if ($inventory) {
                            $inventory->increment('current_stock', $item->approved_quantity ?? $item->requested_quantity);
                            $inventory->update(['updated_by' => auth()->id()]);
                        }
                    }
                    
                } else {
                    $item->update([
                        'status' => 'pending',
                        'approved_quantity' => null,
                        'rejection_reason' => null
                    ]);
                    
                    // Restore inventory if was previously approved
                    if ($wasApproved && $item->categoryItem) {
                        $inventory = \App\Models\Inventory::where('item_name', 'LIKE', "%{$item->item_name}%")
                            ->where('category', $item->category->getInventoryCategoryName())
                            ->where('is_active', true)
                            ->first();

                        if ($inventory) {
                            $inventory->increment('current_stock', $item->approved_quantity ?? $item->requested_quantity);
                            $inventory->update(['updated_by' => auth()->id()]);
                        }
                    }
                }
            }

            // Update overall request status
            $overallStatus = 'under_review';
            if ($approvedCount === $totalCount) {
                $overallStatus = 'approved';
            } elseif ($rejectedCount === $totalCount) {
                $overallStatus = 'rejected';
            } elseif ($approvedCount > 0) {
                $overallStatus = 'partially_approved';
            }

            $seedlingRequest->update([
                'status' => $overallStatus,
                'remarks' => $request->remarks,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'approved_at' => ($overallStatus === 'approved' || $overallStatus === 'partially_approved') ? now() : null,
                'rejected_at' => $overallStatus === 'rejected' ? now() : null,
            ]);

            \DB::commit();

            // Send email notification if fully approved and email is available
            if ($overallStatus === 'approved' && $seedlingRequest->email) {
                try {
                    Mail::to($seedlingRequest->email)->send(new ApplicationApproved($seedlingRequest, 'seedling'));
                } catch (\Exception $e) {
                    \Log::error('Failed to send approval email: ' . $e->getMessage());
                }
            }

            return redirect()->back()->with('success', 'Items updated successfully and inventory adjusted.');

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Failed to update items: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}