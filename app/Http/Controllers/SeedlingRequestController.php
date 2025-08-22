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

        // Add status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Add category filter
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

        $requests = $query->paginate(15)->withQueryString();
        
        // Get statistics
        $totalRequests = SeedlingRequest::count();
        $underReviewCount = SeedlingRequest::where('status', 'under_review')->count();
        $approvedCount = SeedlingRequest::where('status', 'approved')->count();
        $partiallyApprovedCount = SeedlingRequest::where('status', 'partially_approved')->count();
        $rejectedCount = SeedlingRequest::where('status', 'rejected')->count();

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
     * Bulk update categories
     */
    public function bulkUpdateCategories(Request $request, SeedlingRequest $seedlingRequest)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'required|in:vegetables,fruits,fertilizers',
            'statuses' => 'required|array',
            'statuses.*' => 'required|in:approved,rejected,under_review',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            $categories = $request->categories;
            $statuses = $request->statuses;
            
            foreach ($categories as $index => $category) {
                if (!empty($seedlingRequest->{$category})) {
                    $status = $statuses[$index] ?? 'under_review';
                    $seedlingRequest->updateCategoryStatus($category, $status, $seedlingRequest->{$category});
                }
            }

            // Update remarks if provided
            if ($request->remarks) {
                $seedlingRequest->update(['remarks' => $request->remarks]);
            }

            \DB::commit();

            return redirect()->back()->with('success', 'Categories updated successfully.');

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