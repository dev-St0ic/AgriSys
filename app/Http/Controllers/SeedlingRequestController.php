<?php

namespace App\Http\Controllers;

use App\Models\SeedlingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeedlingRequestController extends Controller
{
    /**
     * Display all seedling requests with improved pagination
     */
    public function index(Request $request)
    {
        $query = SeedlingRequest::orderBy('created_at', 'desc');
        
        // Add search functionality if needed
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

        // Add status filter if needed
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15)->withQueryString();
        
        return view('admin.seedling_requests.index', compact('requests'));
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

        $seedlingRequest->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'approved_quantity' => $request->status === 'approved' ? $request->approved_quantity : null,
            'approved_at' => $request->status === 'approved' ? now() : null,
            'rejected_at' => $request->status === 'rejected' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Request status updated successfully.');
    }
}