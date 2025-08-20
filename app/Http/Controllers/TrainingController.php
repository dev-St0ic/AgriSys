<?php

namespace App\Http\Controllers;

use App\Models\TrainingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    /**
     * Display a listing of training applications
     */
    public function index(Request $request)
    {
        $query = TrainingApplication::query()->with('updatedBy');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }

        if ($request->filled('training_type')) {
            $query->withTrainingType($request->training_type);
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $trainings = $query->paginate(10)->withQueryString();

        // Statistics
        $totalApplications = TrainingApplication::count();
        $underReviewCount = TrainingApplication::where('status', 'under_review')->count();
        $approvedCount = TrainingApplication::where('status', 'approved')->count();
        $rejectedCount = TrainingApplication::where('status', 'rejected')->count();

        return view('admin.training.index', compact(
            'trainings',
            'totalApplications',
            'underReviewCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Show the specified training application
     */
    public function show($id)
    {
        $training = TrainingApplication::with('updatedBy')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $training->id,
                'application_number' => $training->application_number,
                'first_name' => $training->first_name,
                'middle_name' => $training->middle_name,
                'last_name' => $training->last_name,
                'full_name' => $training->full_name,
                'mobile_number' => $training->mobile_number,
                'email' => $training->email,
                'training_type' => $training->training_type,
                'training_type_display' => $training->training_type_display,
                'status' => $training->status,
                'formatted_status' => $training->formatted_status,
                'status_color' => $training->status_color,
                'remarks' => $training->remarks,
                'document_paths' => $training->document_paths,
                'document_urls' => $training->document_urls,
                'created_at' => $training->created_at->format('M d, Y g:i A'),
                'updated_at' => $training->updated_at->format('M d, Y g:i A'),
                'status_updated_at' => $training->status_updated_at ? $training->status_updated_at->format('M d, Y g:i A') : null,
                'updated_by_name' => $training->updatedBy ? $training->updatedBy->name : null
            ]
        ]);
    }

    /**
     * Update the status of a training application
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:under_review,approved,rejected',
            'remarks' => 'nullable|string|max:1000'
        ]);

        $training = TrainingApplication::findOrFail($id);
        
        $training->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
            'status_updated_at' => now(),
            'updated_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Training application status updated successfully',
            'data' => $training->fresh(['updatedBy'])
        ]);
    }

    /**
     * Delete a training application
     */
    public function destroy($id)
    {
        $training = TrainingApplication::findOrFail($id);

        // Delete associated documents
        if ($training->hasDocuments()) {
            foreach ($training->document_paths as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $training->delete();

        return response()->json([
            'success' => true,
            'message' => 'Training application deleted successfully'
        ]);
    }
}