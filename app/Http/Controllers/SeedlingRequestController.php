<?php

namespace App\Http\Controllers;

use App\Models\SeedlingRequest;
use App\Models\SeedlingRequestItem;
use App\Models\RequestCategory;
use App\Models\CategoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationApproved;
use App\Services\NotificationService;

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
        $requests = $query->paginate(10)->withQueryString();

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
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',

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

            // Get user ID from session or auth
            $userId = auth()->id() ?? session('user.id');

            // Create the main request
            $seedlingRequest = SeedlingRequest::create([
                'user_id' => $userId,
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

            $this->logActivity('created', 'SeedlingRequest', $seedlingRequest->id, [
                'request_number' => $seedlingRequest->request_number,
                'total_quantity' => $totalQuantity,
                'items_count' => count($validated['items']),
                'barangay' => $seedlingRequest->barangay
            ]);

            NotificationService::seedlingRequestCreated($seedlingRequest);

            \DB::commit();

            // ✅ CHECK IF AJAX REQUEST FIRST
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Seedling request created successfully!'
                ], 201);
            }

            // Otherwise redirect
            return redirect()->route('admin.seedlings.requests')
                ->with('success', 'Seedling request created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Failed to create seedling request: ' . $e->getMessage());

            // Check if it's an AJAX request (our modal will be)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create request: ' . $e->getMessage()
                ], 400);
            }

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

    // /**
    //  * Update a seedling request
    //  */
    // public function update(Request $request, SeedlingRequest $seedlingRequest)
    // {
    //     // Only allow editing pending or under_review requests
    //     if (!in_array($seedlingRequest->status, ['pending', 'under_review'])) {
    //         return redirect()->route('admin.seedlings.requests')
    //             ->with('error', 'Cannot edit approved, rejected, or cancelled requests.');
    //     }

    //     $validated = $request->validate([
    //         // Personal Information
    //         'first_name' => 'required|string|max:255',
    //         'middle_name' => 'nullable|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'extension_name' => 'nullable|string|max:10',
    //         'contact_number' => 'required|string|max:20',
    //         'email' => 'nullable|email|max:255',
    //         'address' => 'required|string|max:500',
    //         'barangay' => 'required|string|max:255',
    //         'planting_location' => 'nullable|string|max:500',
    //         'purpose' => 'nullable|string|max:1000',
    //         'preferred_delivery_date' => 'nullable|date|after:today',
    //         'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',

    //         // Items - Dynamic array structure
    //         'items' => 'required|array|min:1',
    //         'items.*.category_item_id' => 'required|exists:category_items,id',
    //         'items.*.quantity' => 'required|integer|min:1',
    //     ], [
    //         'items.required' => 'Please add at least one item to the request.',
    //         'items.min' => 'Please add at least one item to the request.',
    //     ]);

    //     try {
    //         \DB::beginTransaction();

    //         // Handle document upload/replacement
    //         if ($request->hasFile('document')) {
    //             // Delete old document if exists
    //             if ($seedlingRequest->document_path) {
    //                 \Storage::disk('public')->delete($seedlingRequest->document_path);
    //             }
    //             $seedlingRequest->document_path = $request->file('document')->store('seedling-requests', 'public');
    //         }

    //         // Update main request information
    //         $seedlingRequest->update([
    //             'first_name' => $validated['first_name'],
    //             'middle_name' => $validated['middle_name'],
    //             'last_name' => $validated['last_name'],
    //             'extension_name' => $validated['extension_name'],
    //             'contact_number' => $validated['contact_number'],
    //             'email' => $validated['email'],
    //             'address' => $validated['address'],
    //             'barangay' => $validated['barangay'],
    //             'planting_location' => $validated['planting_location'],
    //             'purpose' => $validated['purpose'],
    //             'preferred_delivery_date' => $validated['preferred_delivery_date'],
    //         ]);

    //         // Delete all existing items
    //         $seedlingRequest->items()->delete();

    //         // Re-create items from the updated dynamic selections
    //         $totalQuantity = 0;
    //         foreach ($validated['items'] as $itemData) {
    //             $categoryItem = CategoryItem::with('category')->findOrFail($itemData['category_item_id']);

    //             SeedlingRequestItem::create([
    //                 'seedling_request_id' => $seedlingRequest->id,
    //                 'category_id' => $categoryItem->category_id,
    //                 'category_item_id' => $categoryItem->id,
    //                 'item_name' => $categoryItem->name,
    //                 'requested_quantity' => $itemData['quantity'],
    //                 'status' => 'pending',
    //             ]);

    //             $totalQuantity += $itemData['quantity'];
    //         }

    //         // Update total quantity
    //         $seedlingRequest->update(['total_quantity' => $totalQuantity]);

    //         \DB::commit();

    //         return redirect()->route('admin.seedlings.requests')
    //             ->with('success', 'Seedling request updated successfully!');

    //     } catch (\Exception $e) {
    //         \DB::rollback();
    //         \Log::error('Failed to update seedling request: ' . $e->getMessage());

    //         return redirect()->back()
    //             ->withErrors(['error' => 'Failed to update request: ' . $e->getMessage()])
    //             ->withInput();
    //     }
    // }

    /**
 * Update a seedling request - Personal information only
 * For edit modal functionality
 */
public function update(Request $request, SeedlingRequest $seedlingRequest)
{
    // Allow editing all statuses for personal info, but not items if already processed
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
        'preferred_delivery_date' => 'nullable|date|after_or_equal:today',
    ], [
        'contact_number.required' => 'Contact number is required.',
        'contact_number.regex' => 'Please enter a valid Philippine mobile number.',
        'first_name.required' => 'First name is required.',
        'last_name.required' => 'Last name is required.',
        'address.required' => 'Address is required.',
        'barangay.required' => 'Barangay is required.',
    ]);

    try {
        \DB::beginTransaction();

        // Validate contact number format
        $phoneRegex = '/^(\+639|09)\d{9}$/';
        if (!preg_match($phoneRegex, $validated['contact_number'])) {
            throw new \Exception('Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)');
        }

        // Log the changes for audit trail
        $changes = [];
        $changedFields = [
            'first_name', 'middle_name', 'last_name', 'extension_name',
            'contact_number', 'email', 'address', 'barangay',
            'planting_location', 'purpose', 'preferred_delivery_date'
        ];

        foreach ($changedFields as $field) {
            if ($seedlingRequest->$field != $validated[$field]) {
                $changes[$field] = [
                    'old' => $seedlingRequest->$field,
                    'new' => $validated[$field]
                ];
            }
        }

        // Update the request
        $seedlingRequest->update($validated);

        // Log activity if there are changes
        if (!empty($changes)) {
            $this->logActivity('updated', 'SeedlingRequest', $seedlingRequest->id, [
                'changes' => $changes,
                'request_number' => $seedlingRequest->request_number
            ]);

            activity()
                ->performedOn($seedlingRequest)
                ->withProperties(['changes' => $changes])
                ->log('Updated seedling request personal information');
        }

        \DB::commit();

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            $changeCount = count($changes);
            $message = $changeCount > 0
                ? "Seedling request updated successfully! ({$changeCount} field" . ($changeCount !== 1 ? 's' : '') . " changed)"
                : "Seedling request updated successfully!";

            return response()->json([
                'success' => true,
                'message' => $message,
                'changes_count' => $changeCount
            ]);
        }

        $message = count($changes) > 0
            ? 'Seedling request updated successfully!'
            : 'No changes were made.';

        return redirect()->route('admin.seedlings.requests')
            ->with('success', $message);

    } catch (\Exception $e) {
        \DB::rollback();
        \Log::error('Failed to update seedling request: ' . $e->getMessage(), [
            'request_id' => $seedlingRequest->id,
            'error_trace' => $e->getTraceAsString()
        ]);

        // Return JSON error response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        return redirect()->back()
            ->withErrors(['error' => $e->getMessage()])
            ->withInput();
    }
}


    /**
     * Remove a seedling request - ALLOWS ALL STATUSES WITH SUPPLY RETURN
     */
    public function destroy(SeedlingRequest $seedlingRequest)
    {
        try {
            // If there are approved items, return supplies to inventory
            $approvedItems = $seedlingRequest->items()->where('status', 'approved')->get();
            if ($approvedItems->count() > 0) {
                foreach ($approvedItems as $item) {
                    if ($item->categoryItem) {
                        $item->categoryItem->returnSupply(
                            $item->approved_quantity ?? $item->requested_quantity,
                            auth()->id(),
                            "Returned from deleted request #{$seedlingRequest->request_number}",
                            'SeedlingRequest',
                            $seedlingRequest->id
                        );
                    }
                }
            }

            NotificationService::seedlingRequestDeleted($seedlingRequest);

            // Delete the request
            $requestNumber = $seedlingRequest->request_number;
            $seedlingRequest->delete();

            $this->logActivity('deleted', 'SeedlingRequest', $seedlingRequest->id, [
                'request_number' => $requestNumber,
                'supplies_returned' => $approvedItems->count() > 0
            ]);

            // Log the deletion
            \Log::info('Seedling request deleted', [
                'request_id' => $seedlingRequest->id,
                'request_number' => $requestNumber,
                'deleted_by' => auth()->user()->name ?? 'System',
                'supplies_returned' => $approvedItems->count() > 0 ? 'Yes' : 'No'
            ]);

            // For AJAX requests, return JSON
            if (request()->expectsJson()) {
                $message = "Request {$requestNumber} deleted successfully!";
                if ($approvedItems->count() > 0) {
                    $message .= " {$approvedItems->count()} approved item(s) supplies returned to inventory.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            // For regular requests, redirect
            $message = "Seedling request {$requestNumber} deleted successfully!";
            if ($approvedItems->count() > 0) {
                $message .= " {$approvedItems->count()} approved item(s) supplies have been returned to inventory.";
            }

            return redirect()->route('admin.seedlings.requests')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Failed to delete seedling request', [
                'request_id' => $seedlingRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // For AJAX requests, return JSON error
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete request: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete request: ' . $e->getMessage());
        }
    }

    /**
     * Update individual items in a request (for approval/rejection) - WITH AUTOMATIC SUPPLY DEDUCTION
     */
    // public function updateItems(Request $request, SeedlingRequest $seedlingRequest)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'item_statuses' => 'required|array',
    //             'item_statuses.*' => 'required|in:pending,approved,rejected',
    //             'remarks' => 'nullable|string|max:500',
    //         ]);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         // Return JSON error response for AJAX requests
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Validation failed',
    //                 'errors' => $e->errors()
    //             ], 422);
    //         }
    //         throw $e;
    //     }

    //     try {
    //         \DB::beginTransaction();

    //         $itemStatuses = $validated['item_statuses'];
    //         $approvedCount = 0;
    //         $rejectedCount = 0;
    //         $totalCount = count($itemStatuses);

    //         // OPTIMIZATION: Load all items at once with relationships to avoid N+1 queries
    //         $items = SeedlingRequestItem::with('categoryItem')
    //             ->whereIn('id', array_keys($itemStatuses))
    //             ->get()
    //             ->keyBy('id');

    //         foreach ($itemStatuses as $itemId => $status) {
    //             $item = $items->get($itemId);

    //             if (!$item) {
    //                 throw new \Exception("Item not found: {$itemId}");
    //             }

    //             // Check if status is changing from approved to something else
    //             $wasApproved = $item->status === 'approved';

    //             // Skip if status hasn't changed
    //             if ($item->status === $status) {
    //                 if ($status === 'approved') $approvedCount++;
    //                 elseif ($status === 'rejected') $rejectedCount++;
    //                 continue;
    //             }

    //             if ($status === 'approved') {
    //                 // Check supply availability
    //                 if ($item->categoryItem) {
    //                     $check = $item->categoryItem->checkSupplyAvailability($item->requested_quantity);
    //                     if (!$check['available']) {
    //                         throw new \Exception("Insufficient supply for {$item->item_name}. Available: {$check['current_supply']}, Requested: {$item->requested_quantity}");
    //                     }
    //                 }

    //                 $item->status = 'approved';
    //                 $item->approved_quantity = $item->requested_quantity;
    //                 $item->rejection_reason = null;
    //                 $item->save();
    //                 $approvedCount++;

    //                 // AUTOMATIC SUPPLY DEDUCTION when newly approved
    //                 if (!$wasApproved && $item->categoryItem) {
    //                     $success = $item->categoryItem->distributeSupply(
    //                         $item->requested_quantity,
    //                         auth()->id(),
    //                         "Distributed for approved request #{$seedlingRequest->request_number} - {$seedlingRequest->full_name}",
    //                         'SeedlingRequest',
    //                         $seedlingRequest->id
    //                     );

    //                     if (!$success) {
    //                         throw new \Exception("Failed to distribute supply for {$item->item_name}");
    //                     }
    //                 }

    //             } elseif ($status === 'rejected') {
    //                 $item->status = 'rejected';
    //                 $item->approved_quantity = null;
    //                 $item->rejection_reason = $request->input("rejection_reasons.{$itemId}");
    //                 $item->save();
    //                 $rejectedCount++;

    //                 // AUTOMATIC SUPPLY RETURN if was previously approved
    //                 if ($wasApproved && $item->categoryItem) {
    //                     $item->categoryItem->returnSupply(
    //                         $item->approved_quantity ?? $item->requested_quantity,
    //                         auth()->id(),
    //                         "Returned from rejected item in request #{$seedlingRequest->request_number}",
    //                         'SeedlingRequest',
    //                         $seedlingRequest->id
    //                     );
    //                 }

    //             } else {
    //                 $item->status = 'pending';
    //                 $item->approved_quantity = null;
    //                 $item->rejection_reason = null;
    //                 $item->save();

    //                 // AUTOMATIC SUPPLY RETURN if was previously approved
    //                 if ($wasApproved && $item->categoryItem) {
    //                     $item->categoryItem->returnSupply(
    //                         $item->approved_quantity ?? $item->requested_quantity,
    //                         auth()->id(),
    //                         "Returned from pending item in request #{$seedlingRequest->request_number}",
    //                         'SeedlingRequest',
    //                         $seedlingRequest->id
    //                     );

    //                 }
    //             }
    //         }

    //         // Update overall request status
    //         $previousStatus = $seedlingRequest->status;
    //         $overallStatus = 'under_review';
    //         if ($approvedCount === $totalCount) {
    //             $overallStatus = 'approved';
    //         } elseif ($rejectedCount === $totalCount) {
    //             $overallStatus = 'rejected';
    //         } elseif ($approvedCount > 0) {
    //             $overallStatus = 'partially_approved';
    //         }

    //         $seedlingRequest->update([
    //             'status' => $overallStatus,
    //             'remarks' => $validated['remarks'] ?? $seedlingRequest->remarks,
    //             'reviewed_by' => auth()->id(),
    //             'reviewed_at' => now(),
    //             'approved_at' => ($overallStatus === 'approved' || $overallStatus === 'partially_approved') ? now() : null,
    //             'rejected_at' => $overallStatus === 'rejected' ? now() : null,
    //         ]);

    //         // In the updateItems() method, after status update:
    //         if ($previousStatus !== $overallStatus) {
    //             NotificationService::seedlingRequestStatusChanged($seedlingRequest, $previousStatus);
    //         }


    //         $this->logActivity('updated_items', 'SeedlingRequest', $seedlingRequest->id, [
    //             'request_number' => $seedlingRequest->request_number,
    //             'approved_count' => $approvedCount,
    //             'rejected_count' => $rejectedCount,
    //             'new_status' => $overallStatus,
    //             'old_status' => $previousStatus
    //         ]);

    //         \DB::commit();

    //         // OPTIMIZATION: Queue notifications to run after response (non-blocking)
    //         // Fire SMS notification event if status actually changed
    //         if ($previousStatus !== $overallStatus) {
    //             event(new \App\Events\ApplicationStatusChanged(
    //                 $seedlingRequest,
    //                 $seedlingRequest->getApplicationTypeName(),
    //                 $previousStatus,
    //                 $overallStatus,
    //                 $validated['remarks'],
    //                 $seedlingRequest->getApplicantPhone(),
    //                 $seedlingRequest->getApplicantName()
    //             ));
    //         }

    //         // Queue email notification if fully approved (non-blocking)
    //         if ($overallStatus === 'approved' && $seedlingRequest->email) {
    //             \Illuminate\Support\Facades\Mail::to($seedlingRequest->email)
    //                 ->queue(new \App\Mail\ApplicationApproved($seedlingRequest, 'seedling'));
    //         }

    //         // Return JSON response for AJAX requests
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Items updated successfully and supplies automatically adjusted.'
    //             ]);
    //         }

    //         return redirect()->back()->with('success', 'Items updated successfully and supplies automatically adjusted.');

    //     } catch (\Exception $e) {
    //         \DB::rollback();
    //         \Log::error('Failed to update items: ' . $e->getMessage());

    //         // Return JSON error response for AJAX requests
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => $e->getMessage()
    //             ], 400);
    //         }

    //         return redirect()->back()
    //             ->withErrors(['error' => $e->getMessage()])
    //             ->withInput();
    //     }
    // }
/**
     * Update individual items in a request (for approval/rejection) - WITH AUTOMATIC SUPPLY DEDUCTION
     */
    public function updateItems(Request $request, SeedlingRequest $seedlingRequest)
{
    try {
        $validated = $request->validate([
            'item_statuses' => 'required|array',
            'item_statuses.*' => 'required|in:pending,approved,rejected',
            'remarks' => 'nullable|string|max:500',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        throw $e;
    }

    try {
        \DB::beginTransaction();

        $itemStatuses = $validated['item_statuses'];

        // Load all items at once with relationships
        // Cast keys to integers for proper collection lookup
        $itemIds = array_map('intval', array_keys($itemStatuses));
        $items = SeedlingRequestItem::with('categoryItem')
            ->whereIn('id', $itemIds)
            ->get()
            ->keyBy('id');

        // ✅ CHECK IF ANY ITEMS ACTUALLY CHANGED STATUS
        $hasItemChanges = false;
        foreach ($itemStatuses as $itemId => $status) {
            $item = $items->get((int) $itemId);
            if ($item && $item->status !== $status) {
                $hasItemChanges = true;
                break;
            }
        }

        // If no items changed, skip the entire process
        if (!$hasItemChanges) {
            \DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No changes detected.'
                ]);
            }

            return redirect()->back()->with('success', 'No changes were made.');
        }

        $approvedCount = 0;
        $rejectedCount = 0;
        $totalCount = count($itemStatuses);

        foreach ($itemStatuses as $itemId => $status) {
            $item = $items->get((int) $itemId);

            if (!$item) {
                throw new \Exception("Item not found: {$itemId}");
            }

            $wasApproved = $item->status === 'approved';

            // Skip if status hasn't changed
            if ($item->status === $status) {
                if ($status === 'approved') $approvedCount++;
                elseif ($status === 'rejected') $rejectedCount++;
                continue;
            }

            if ($status === 'approved') {
                // Check supply availability
                if ($item->categoryItem) {
                    $check = $item->categoryItem->checkSupplyAvailability($item->requested_quantity);
                    if (!$check['available']) {
                        throw new \Exception("Insufficient supply for {$item->item_name}. Available: {$check['current_supply']}, Requested: {$item->requested_quantity}");
                    }
                }

                $item->status = 'approved';
                $item->approved_quantity = $item->requested_quantity;
                $item->rejection_reason = null;
                $item->save();
                $approvedCount++;

                // ✅ SUPPLY DEDUCTION + STOCK NOTIFICATIONS
                if (!$wasApproved && $item->categoryItem) {
                    $success = $item->categoryItem->distributeSupply(
                        $item->requested_quantity,
                        auth()->id(),
                        "Distributed for approved request #{$seedlingRequest->request_number} - {$seedlingRequest->full_name}",
                        'SeedlingRequest',
                        $seedlingRequest->id
                    );

                    if (!$success) {
                        throw new \Exception("Failed to distribute supply for {$item->item_name}");
                    }

                    // ✅ CHECK STOCK LEVELS AFTER DISTRIBUTION
                    $item->categoryItem->refresh();
                    if ($item->categoryItem->current_supply <= $item->categoryItem->minimum_stock_level && $item->categoryItem->current_supply > 0) {
                        NotificationService::seedlingStockLow($item->categoryItem);
                    }
                    if ($item->categoryItem->current_supply <= 0) {
                        NotificationService::seedlingStockOut($item->categoryItem);
                    }
                }

            } elseif ($status === 'rejected') {
                $item->status = 'rejected';
                $item->approved_quantity = null;
                $item->rejection_reason = $request->input("rejection_reasons.{$itemId}");
                $item->save();
                $rejectedCount++;

                // Supply return if was previously approved
                if ($wasApproved && $item->categoryItem) {
                    $item->categoryItem->returnSupply(
                        $item->approved_quantity ?? $item->requested_quantity,
                        auth()->id(),
                        "Returned from rejected item in request #{$seedlingRequest->request_number}",
                        'SeedlingRequest',
                        $seedlingRequest->id
                    );
                }

            } else { // pending
                $item->status = 'pending';
                $item->approved_quantity = null;
                $item->rejection_reason = null;
                $item->save();

                // Supply return if was previously approved
                if ($wasApproved && $item->categoryItem) {
                    $item->categoryItem->returnSupply(
                        $item->approved_quantity ?? $item->requested_quantity,
                        auth()->id(),
                        "Returned from pending item in request #{$seedlingRequest->request_number}",
                        'SeedlingRequest',
                        $seedlingRequest->id
                    );
                }
            }
        }

        // Update overall request status
        $previousStatus = $seedlingRequest->status;
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
            'remarks' => $validated['remarks'] ?? $seedlingRequest->remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'approved_at' => ($overallStatus === 'approved' || $overallStatus === 'partially_approved') ? now() : null,
            'rejected_at' => $overallStatus === 'rejected' ? now() : null,
        ]);

        // ✅ STATUS CHANGE NOTIFICATION
        if ($previousStatus !== $overallStatus) {
            NotificationService::seedlingRequestStatusChanged($seedlingRequest, $previousStatus);
        }

        $this->logActivity('updated_items', 'SeedlingRequest', $seedlingRequest->id, [
            'request_number' => $seedlingRequest->request_number,
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
            'new_status' => $overallStatus,
            'old_status' => $previousStatus
        ]);

        \DB::commit();

        // Fire SMS notification event
        if ($previousStatus !== $overallStatus) {
            event(new \App\Events\ApplicationStatusChanged(
                $seedlingRequest,
                $seedlingRequest->getApplicationTypeName(),
                $previousStatus,
                $overallStatus,
                $validated['remarks'],
                $seedlingRequest->getApplicantPhone(),
                $seedlingRequest->getApplicantName()
            ));
        }

        // Queue email notification
        if ($overallStatus === 'approved' && $seedlingRequest->email) {
            \Illuminate\Support\Facades\Mail::to($seedlingRequest->email)
                ->queue(new \App\Mail\ApplicationApproved($seedlingRequest, 'seedling'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Items updated successfully and supplies automatically adjusted.'
            ]);
        }

        return redirect()->back()->with('success', 'Items updated successfully and supplies automatically adjusted.');

    } catch (\Exception $e) {
        \DB::rollback();
        \Log::error('Failed to update items: ' . $e->getMessage());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        return redirect()->back()
            ->withErrors(['error' => $e->getMessage()])
            ->withInput();
    }
}

        /**
     * Export seedling requests to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = SeedlingRequest::with(['items.category', 'items.categoryItem'])
                ->orderBy('created_at', 'desc');

            // Apply same filters as index method
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

            if ($request->has('category') && !empty($request->category)) {
                $categoryId = $request->category;
                $query->whereHas('items', function($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            }

            if ($request->has('barangay') && !empty($request->barangay)) {
                $query->where('barangay', $request->barangay);
            }

            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            $requests = $query->get();

            $this->logActivity('exported', 'SeedlingRequest', null, [
                'records_count' => $requests->count(),
                'filters' => $request->all()
            ]);

            // Create CSV
            $fileName = 'seedling-requests-' . now()->format('Y-m-d-H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function() use ($requests) {
                $file = fopen('php://output', 'w');

                // Set UTF-8 BOM for Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Write headers
                $headers = [
                    'Request #',
                    'Date Applied',
                    'Full Name',
                    'Contact Number',
                    'Email',
                    'Barangay',
                    'Address',
                    'Planting Location',
                    'Purpose',
                    'Preferred Delivery Date',
                    'Total Quantity',
                    'Items Requested',
                    'Status',
                    'Remarks',
                    'Created At',
                    'Updated At'
                ];
                fputcsv($file, $headers);

                // Write data rows
                foreach ($requests as $request) {
                    // Format items
                    $itemsFormatted = [];
                    foreach ($request->items as $item) {
                        $itemsFormatted[] = sprintf(
                            '%s (%d %s) - %s',
                            $item->item_name,
                            $item->requested_quantity,
                            $item->categoryItem->unit ?? 'pcs',
                            ucfirst($item->status)
                        );
                    }
                    $itemsText = implode('; ', $itemsFormatted);

                    $row = [
                        $request->request_number,
                        $request->created_at->format('M d, Y g:i A'),
                        $request->full_name,
                        $request->contact_number,
                        $request->email ?? 'N/A',
                        $request->barangay,
                        $request->address,
                        $request->planting_location ?? 'N/A',
                        $request->purpose ?? 'N/A',
                        $request->preferred_delivery_date ?? 'N/A',
                        $request->total_quantity,
                        $itemsText,
                        ucfirst(str_replace('_', ' ', $request->status)),
                        $request->remarks ?? 'N/A',
                        $request->created_at->format('M d, Y g:i A'),
                        $request->updated_at->format('M d, Y g:i A')
                    ];

                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Failed to export seedling requests: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to export data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

}
