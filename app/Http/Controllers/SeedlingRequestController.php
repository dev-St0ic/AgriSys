<?php

namespace App\Http\Controllers;

use App\Models\SeedlingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationApproved;

class SeedlingRequestController extends Controller
{
    /**
     * Display all seedling requests with filtering and statistics
     */
    public function index(Request $request)
    {
        $query = SeedlingRequest::orderBy('created_at', 'desc');

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

        // Apply other filters first
        if ($request->has('category') && !empty($request->category)) {
            $category = $request->category;
            $query->where(function($q) use ($category) {
                switch($category) {
                    case 'vegetables':
                        $q->whereNotNull('vegetables')->where('vegetables', '!=', '[]');
                        break;
                    case 'fruits':
                        $q->whereNotNull('fruits')->where('fruits', '!=', '[]');
                        break;
                    case 'fertilizers':
                        $q->whereNotNull('fertilizers')->where('fertilizers', '!=', '[]');
                        break;
                }
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

        // Get initial results
        $allResults = $query->get();

        // Apply status filter using the overall_status accessor
        if ($request->has('status') && !empty($request->status)) {
            $statusFilter = $request->status;
            $allResults = $allResults->filter(function($seedlingRequest) use ($statusFilter) {
                return $seedlingRequest->overall_status === $statusFilter;
            });
        }

        // Paginate manually
        $perPage = 15;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $allResults->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $requests = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $allResults->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Create a base query for statistics (apply same filters except status)
        $statsQuery = SeedlingRequest::orderBy('created_at', 'desc');

        // Apply the same filters to statistics (except status filter)
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
            $category = $request->category;
            $statsQuery->where(function($q) use ($category) {
                switch($category) {
                    case 'vegetables':
                        $q->whereNotNull('vegetables')->where('vegetables', '!=', '[]');
                        break;
                    case 'fruits':
                        $q->whereNotNull('fruits')->where('fruits', '!=', '[]');
                        break;
                    case 'fertilizers':
                        $q->whereNotNull('fertilizers')->where('fertilizers', '!=', '[]');
                        break;
                }
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

        // Get all filtered results for statistics calculation
        $statsResults = $statsQuery->get();

        // Calculate statistics using overall_status accessor
        $totalRequests = $statsResults->count();
        $underReviewCount = $statsResults->filter(fn($r) => $r->overall_status === 'under_review')->count();
        $approvedCount = $statsResults->filter(fn($r) => $r->overall_status === 'approved')->count();
        $partiallyApprovedCount = $statsResults->filter(fn($r) => $r->overall_status === 'partially_approved')->count();
        $rejectedCount = $statsResults->filter(fn($r) => $r->overall_status === 'rejected')->count();

        // Get unique barangays for filter dropdown
        $barangays = SeedlingRequest::distinct()->pluck('barangay')->filter()->sort();

        return view('admin.seedlings.index', compact(
            'requests',
            'totalRequests',
            'underReviewCount',
            'approvedCount',
            'partiallyApprovedCount',
            'rejectedCount',
            'barangays'
        ));
    }

    /**
     * Show a specific seedling request
     */
    public function show(SeedlingRequest $seedlingRequest)
    {
        return view('admin.seedlings.show', compact('seedlingRequest'));
    }

    /**
     * Update the status of a seedling request (legacy method for overall status)
     */
    public function updateStatus(Request $request, SeedlingRequest $seedlingRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,under_review',
            'remarks' => 'nullable|string|max:500',
            'approved_quantity' => 'nullable|integer|min:1'
        ]);

        $newStatus = $request->status;

        try {
            \DB::beginTransaction();

            if ($newStatus === 'approved') {
                // Approve all categories that have items
                $categories = ['vegetables', 'fruits', 'fertilizers'];
                foreach ($categories as $category) {
                    if (!empty($seedlingRequest->{$category})) {
                        $seedlingRequest->updateCategoryStatus($category, 'approved', $seedlingRequest->{$category});
                    }
                }
            } elseif ($newStatus === 'rejected') {
                // Reject all categories
                $categories = ['vegetables', 'fruits', 'fertilizers'];
                foreach ($categories as $category) {
                    if (!empty($seedlingRequest->{$category})) {
                        $seedlingRequest->updateCategoryStatus($category, 'rejected');
                    }
                }
            }

            $seedlingRequest->update([
                'status' => $newStatus,
                'remarks' => $request->remarks,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'approved_quantity' => $newStatus === 'approved' ? $request->approved_quantity : null,
                'approved_at' => $newStatus === 'approved' ? now() : null,
                'rejected_at' => $newStatus === 'rejected' ? now() : null,
            ]);

            \DB::commit();

            // Send email notification if approved and email is available
            if ($newStatus === 'approved' && $seedlingRequest->email) {
                try {
                    Mail::to($seedlingRequest->email)->send(new ApplicationApproved($seedlingRequest, 'seedling'));
                } catch (\Exception $e) {
                    \Log::error('Failed to send approval email for seedling request ' . $seedlingRequest->id . ': ' . $e->getMessage());
                }
            }

            $message = match($newStatus) {
                'approved' => 'Request approved successfully and inventory has been updated. ' .
                             ($seedlingRequest->email ? 'Email notification sent to applicant.' : ''),
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
     * Update category-specific status
     */
    public function updateCategoryStatus(Request $request, SeedlingRequest $seedlingRequest)
    {
        $request->validate([
            'category' => 'required|in:vegetables,fruits,fertilizers',
            'status' => 'required|in:approved,rejected,under_review',
            'remarks' => 'nullable|string|max:500',
        ]);

        $category = $request->category;
        $status = $request->status;

        try {
            \DB::beginTransaction();

            // Check if category has items
            if (empty($seedlingRequest->{$category})) {
                throw new \Exception("No {$category} found in this request");
            }

            $seedlingRequest->updateCategoryStatus($category, $status, $seedlingRequest->{$category});

            // Update remarks if provided
            if ($request->remarks) {
                $seedlingRequest->update(['remarks' => $request->remarks]);
            }

            \DB::commit();

            $categoryName = ucfirst($category);
            $message = match($status) {
                'approved' => "{$categoryName} approved successfully and inventory updated.",
                'rejected' => "{$categoryName} rejected successfully.",
                'under_review' => "{$categoryName} moved back to under review.",
                default => "{$categoryName} status updated successfully."
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
     * Bulk update categories with individual item status control
     */
    public function bulkUpdateCategories(Request $request, SeedlingRequest $seedlingRequest)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'required|in:vegetables,fruits,fertilizers',
            'item_statuses' => 'required|array',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            $categories = $request->categories;
            $itemStatuses = $request->item_statuses;

            foreach ($categories as $category) {
                if (!empty($seedlingRequest->{$category})) {
                    $this->updateCategoryWithIndividualItemStatuses(
                        $seedlingRequest,
                        $category,
                        $itemStatuses[$category] ?? []
                    );
                }
            }

            // Update remarks if provided
            if ($request->remarks) {
                $seedlingRequest->update(['remarks' => $request->remarks]);
            }

            \DB::commit();

            return redirect()->back()->with('success', 'Items updated successfully with individual approval status.');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update category with individual item statuses
     */
    private function updateCategoryWithIndividualItemStatuses($seedlingRequest, $category, $itemStatuses)
    {
        $requestedItems = $seedlingRequest->{$category} ?? [];
        $approvedItems = [];
        $rejectedItems = [];

        foreach ($requestedItems as $item) {
            $itemKey = is_array($item) ? $item['name'] : $item;
            $status = $itemStatuses[$itemKey] ?? 'pending';

            if ($status === 'approved') {
                $approvedItems[] = $item;
            } elseif ($status === 'rejected') {
                $rejectedItems[] = $item;
            }
        }

        // Determine overall category status
        $categoryStatus = $this->determineCategoryStatusFromStatuses($itemStatuses, $requestedItems);

        // Update the seedling request
        $seedlingRequest->update([
            "{$category}_status" => $categoryStatus,
            "{$category}_approved_items" => $approvedItems,
            "{$category}_rejected_items" => $rejectedItems,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Update inventory if items are approved
        if (!empty($approvedItems)) {
            $this->updateInventoryForApprovedItems($approvedItems, $category);
        }
    }

    /**
     * Determine category status based on individual item statuses
     */
    private function determineCategoryStatusFromStatuses($itemStatuses, $requestedItems)
    {
        $totalItems = count($requestedItems);
        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($requestedItems as $item) {
            $itemKey = is_array($item) ? $item['name'] : $item;
            $status = $itemStatuses[$itemKey] ?? 'pending';

            if ($status === 'approved') {
                $approvedCount++;
            } elseif ($status === 'rejected') {
                $rejectedCount++;
            }
        }

        if ($approvedCount === $totalItems) {
            return 'approved';
        } elseif ($rejectedCount === $totalItems) {
            return 'rejected';
        } elseif ($approvedCount > 0) {
            return 'partially_approved';
        } else {
            return 'under_review';
        }
    }

    /**
     * Update inventory for approved items
     */
    private function updateInventoryForApprovedItems($approvedItems, $category)
    {
        foreach ($approvedItems as $item) {
            $itemName = is_array($item) ? $item['name'] : $item;
            $quantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;

            $inventory = \App\Models\Inventory::where('item_name', 'LIKE', "%{$itemName}%")
                ->where('category', $category === 'fertilizers' ? 'fertilizer' : substr($category, 0, -1))
                ->first();

            if ($inventory && $inventory->current_stock >= $quantity) {
                $inventory->decrement('current_stock', $quantity);
            }
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
     * Get category inventory status for AJAX requests
     */
    public function getCategoryInventoryStatus(SeedlingRequest $seedlingRequest, $category)
    {
        if (!in_array($category, ['vegetables', 'fruits', 'fertilizers'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category'
            ], 400);
        }

        $inventoryStatus = $seedlingRequest->checkCategoryInventoryAvailability($category);

        return response()->json([
            'success' => true,
            'data' => $inventoryStatus
        ]);
    }
}
