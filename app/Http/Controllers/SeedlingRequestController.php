<?php

namespace App\Http\Controllers;

use App\Models\SeedlingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $rejectedCount = SeedlingRequest::where('status', 'rejected')->count();

        // Get unique barangays for filter dropdown
        $barangays = SeedlingRequest::distinct()->pluck('barangay')->filter()->sort();
        
        return view('admin.seedling_requests.index', compact(
            'requests',
            'totalRequests',
            'underReviewCount', 
            'approvedCount',
            'rejectedCount',
            'barangays'
        ));
    }

    /**
     * Show a specific seedling request
     */
    public function show(SeedlingRequest $seedlingRequest)
    {
        return view('admin.seedling_requests.show', compact('seedlingRequest'));
    }

    /**
     * Update the status of a seedling request
     */
    public function updateStatus(Request $request, SeedlingRequest $seedlingRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,under_review',
            'remarks' => 'nullable|string|max:500',
            'approved_quantity' => 'nullable|integer|min:1'
        ]);

        $oldStatus = $seedlingRequest->status;
        $newStatus = $request->status;

        // Check inventory availability and deduct when approving
        if ($newStatus === 'approved') {
            $inventoryCheck = $seedlingRequest->checkInventoryAvailability();
            
            if (!$inventoryCheck['can_fulfill']) {
                $unavailableItems = $inventoryCheck['unavailable_items'];
                $errorMessage = 'Cannot approve request due to insufficient inventory: ';
                $shortages = [];
                foreach ($unavailableItems as $item) {
                    $shortages[] = $item['name'] . ' (need ' . $item['needed'] . ', available ' . $item['available'] . ')';
                }
                $errorMessage .= implode(', ', $shortages);
                
                return redirect()->back()
                    ->withErrors(['inventory' => $errorMessage])
                    ->withInput();
            }

            // Deduct from inventory in real-time when approved
            if (!$seedlingRequest->deductFromInventory()) {
                return redirect()->back()
                    ->withErrors(['inventory' => 'Failed to deduct items from inventory. Please try again.'])
                    ->withInput();
            }
        }

        // If changing from approved to rejected/under_review, restore inventory
        if ($oldStatus === 'approved' && $newStatus !== 'approved') {
            $seedlingRequest->restoreToInventory();
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

        $message = match($newStatus) {
            'approved' => 'Request approved successfully and inventory has been updated in real-time.',
            'rejected' => 'Request rejected successfully.',
            'under_review' => 'Request moved back to under review.',
            default => 'Request status updated successfully.'
        };

        return redirect()->back()->with('success', $message);
    }
}