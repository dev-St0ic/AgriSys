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
        $underReviewCount = $statsQuery->where('status', 'under_review')->orWhere('status', 'pending')->count();
        $approvedCount = (clone $statsQuery)->where('status', 'approved')->count();
        $partiallyApprovedCount = (clone $statsQuery)->where('status', 'partially_approved')->count();
        $rejectedCount = (clone $statsQuery)->where('status', 'rejected')->count();

        // Get unique barangays for filter dropdown
        $barangays = SeedlingRequest::distinct()->pluck('barangay')->filter()->sort();

        // Get active categories for filter dropdown
        $categories = RequestCategory::active()->ordered()->get();

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
        $categories = RequestCategory::with('activeItems')->active()->ordered()->get();
        $barangays = SeedlingRequest::distinct()->pluck('barangay')->filter()->sort();
        
        return view('admin.seedlings.create', compact('categories', 'barangays'));
    }

    /**
     * Store a newly created seedling request
     */
    public function store(Request $request)
    {
        $request->validate([
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
            'items' => 'required|array|min:1',
            'items.*.category_item_id' => 'required|exists:category_items,id',
            'items.*.quantity' => 'required|integer|min:1',
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
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'extension_name' => $request->extension_name,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'address' => $request->address,
                'barangay' => $request->barangay,
                'planting_location' => $request->planting_location,
                'purpose' => $request->purpose,
                'preferred_delivery_date' => $request->preferred_delivery_date,
                'document_path' => $documentPath,
                'status' => 'pending',
            ]);

            // Create request items
            $totalQuantity = 0;
            foreach ($request->items as $itemData) {
                $categoryItem = CategoryItem::findOrFail($itemData['category_item_id']);
                
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
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show a specific seedling request
     */
    public function show(SeedlingRequest $seedlingRequest)
    {
        $seedlingRequest->load(['items.category', 'items.categoryItem', 'reviewer']);
        return view('admin.seedlings.show', compact('seedlingRequest'));
    }

    /**
     * Show the form for editing a seedling request
     */
    public function edit(SeedlingRequest $seedlingRequest)
    {
        // Only allow editing pending requests
        if (!in_array($seedlingRequest->status, ['pending', 'under_review'])) {
            return redirect()->back()->with('error', 'Cannot edit approved, rejected, or cancelled requests.');
        }

        $categories = RequestCategory::with('activeItems')->active()->ordered()->get();
        $barangays = SeedlingRequest::distinct()->pluck('barangay')->filter()->sort();
        $seedlingRequest->load(['items.category', 'items.categoryItem']);
        
        return view('admin.seedlings.edit', compact('seedlingRequest', 'categories', 'barangays'));
    }

    /**
     * Update a seedling request
     */
    public function update(Request $request, SeedlingRequest $seedlingRequest)
    {
        // Only allow editing pending requests
        if (!in_array($seedlingRequest->status, ['pending', 'under_review'])) {
            return redirect()->back()->with('error', 'Cannot edit approved, rejected, or cancelled requests.');
        }

        $request->validate([
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
            'items' => 'required|array|min:1',
            'items.*.category_item_id' => 'required|exists:category_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            \DB::beginTransaction();

            // Handle document upload
            if ($request->hasFile('document')) {
                // Delete old document if exists
                if ($seedlingRequest->document_path) {
                    \Storage::disk('public')->delete($seedlingRequest->document_path);
                }
                $documentPath = $request->file('document')->store('seedling-requests', 'public');
                $seedlingRequest->document_path = $documentPath;
            }

            // Update main request
            $seedlingRequest->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'extension_name' => $request->extension_name,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'address' => $request->address,
                'barangay' => $request->barangay,
                'planting_location' => $request->planting_location,
                'purpose' => $request->purpose,
                'preferred_delivery_date' => $request->preferred_delivery_date,
            ]);

            // Delete old items
            $seedlingRequest->items()->delete();

            // Create new items
            $totalQuantity = 0;
            foreach ($request->items as $itemData) {
                $categoryItem = CategoryItem::findOrFail($itemData['category_item_id']);
                
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
            // Only allow deletion of pending requests
            if (!in_array($seedlingRequest->status, ['pending', 'under_review', 'rejected'])) {
                return redirect()->back()->with('error', 'Cannot delete approved requests. Please reject it first.');
            }

            // If approved items exist, restore inventory
            if ($seedlingRequest->status === 'approved' || $seedlingRequest->status === 'partially_approved') {
                $seedlingRequest->restoreToInventory();
            }

            $seedlingRequest->delete();

            return redirect()->route('admin.seedlings.requests')
                ->with('success', 'Seedling request deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete request: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of a seedling request
     */
    public function updateStatus(Request $request, SeedlingRequest $seedlingRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,under_review',
            'remarks' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->status;

        try {
            \DB::beginTransaction();

            if ($newStatus === 'approved') {
                // Check inventory availability
                $inventoryCheck = $seedlingRequest->checkInventoryAvailability();
                
                if (!$inventoryCheck['can_fulfill']) {
                    throw new \Exception('Insufficient inventory to approve all items');
                }

                // Approve all items
                foreach ($seedlingRequest->items as $item) {
                    $item->update([
                        'status' => 'approved',
                        'approved_quantity' => $item->requested_quantity
                    ]);
                }

                // Deduct from inventory
                if (!$seedlingRequest->deductFromInventory()) {
                    throw new \Exception('Failed to deduct items from inventory');
                }

            } elseif ($newStatus === 'rejected') {
                // Reject all items
                foreach ($seedlingRequest->items as $item) {
                    $item->update(['status' => 'rejected']);
                }
            } else {
                // Reset to under review
                foreach ($seedlingRequest->items as $item) {
                    if ($item->status === 'approved') {
                        // Restore inventory for previously approved items
                        $seedlingRequest->restoreToInventory();
                    }
                    $item->update(['status' => 'pending']);
                }
            }

            $seedlingRequest->update([
                'status' => $newStatus,
                'remarks' => $request->remarks,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'approved_at' => $newStatus === 'approved' ? now() : null,
                'rejected_at' => $newStatus === 'rejected' ? now() : null,
            ]);

            \DB::commit();

            // Send email notification if approved and email is available
            if ($newStatus === 'approved' && $seedlingRequest->email) {
                try {
                    Mail::to($seedlingRequest->email)->send(new ApplicationApproved($seedlingRequest, 'seedling'));
                } catch (\Exception $e) {
                    \Log::error('Failed to send approval email: ' . $e->getMessage());
                }
            }

            $message = match($newStatus) {
                'approved' => 'Request approved successfully and inventory has been updated.',
                'rejected' => 'Request rejected successfully.',
                'under_review' => 'Request moved back to under review.',
                default => 'Request status updated successfully.'
            };

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update individual items in a request
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
                            throw new \Exception("Insufficient stock for {$item->item_name}");
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
                            $inventory->increment('current_stock', $item->requested_quantity);
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
                            $inventory->increment('current_stock', $item->requested_quantity);
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
                'approved_at' => $overallStatus === 'approved' ? now() : null,
                'rejected_at' => $overallStatus === 'rejected' ? now() : null,
                'approved_quantity' => $seedlingRequest->approvedItems()->sum('approved_quantity')
            ]);

            \DB::commit();

            return redirect()->back()->with('success', 'Items updated successfully and inventory adjusted.');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Get inventory status for AJAX requests
     */
    public function getInventoryStatus(SeedlingRequest $seedlingRequest)
    {
        $inventoryStatus = $seedlingRequest->checkInventoryAvailability();

        return response()->json([
            'success' => true,
            'data' => $inventoryStatus
        ]);
    }

    /**
     * Get category statistics for dashboard/reports
     */
    public function getCategoryStats(Request $request)
    {
        $query = SeedlingRequest::with('items');

        // Apply date filters if provided
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->get();
        $categories = RequestCategory::active()->get();

        $stats = [];
        foreach ($categories as $category) {
            $categoryItems = SeedlingRequestItem::where('category_id', $category->id)
                ->whereIn('seedling_request_id', $requests->pluck('id'))
                ->get();

            $stats[$category->name] = [
                'display_name' => $category->display_name,
                'total_requests' => $categoryItems->unique('seedling_request_id')->count(),
                'total_items' => $categoryItems->count(),
                'approved' => $categoryItems->where('status', 'approved')->count(),
                'rejected' => $categoryItems->where('status', 'rejected')->count(),
                'pending' => $categoryItems->where('status', 'pending')->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}