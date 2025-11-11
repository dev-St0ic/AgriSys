<?php

namespace App\Http\Controllers;

use App\Models\RsbsaApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationApproved;

class RsbsaController extends Controller
{
    /**
     * Display a listing of RSBSA applications
     */
    public function index(Request $request)
    {
        try {
            Log::info('RSBSA index method called', [
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            $query = RsbsaApplication::query();

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('barangay')) {
                $query->where('barangay', $request->barangay);
            }

            if ($request->filled('main_livelihood')) {
                $query->where('main_livelihood', $request->main_livelihood);
            }
            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('application_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
                });
            }

            // Date range filtering
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sort and paginate
            $applications = $query->orderBy('created_at', 'desc')
                                  ->paginate(15)
                                  ->appends($request->query());

            // Calculate statistics
            $totalApplications = RsbsaApplication::count();
            $pendingCount = RsbsaApplication::where('status', 'pending')->count();
            $underReviewCount = RsbsaApplication::where('status', 'under_review')->count();
            $approvedCount = RsbsaApplication::where('status', 'approved')->count();
            $rejectedCount = RsbsaApplication::where('status', 'rejected')->count();

            Log::info('RSBSA data loaded successfully', [
                'total_applications' => $totalApplications,
                'paginated_count' => $applications->count(),
                'current_page' => $applications->currentPage(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $applications,
                    'view' => view('admin.rsbsa.partials.table', compact('applications'))->render()
                ]);
            }

            return view('admin.rsbsa.index', compact(
                'applications',
                'totalApplications',
                'pendingCount',
                'underReviewCount',
                'approvedCount',
                'rejectedCount'
            ));

        } catch (\Exception $e) {
            Log::error('Error fetching RSBSA applications', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading applications: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error loading applications: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified RSBSA application
     */
    public function show($id)
    {
        try {
            $application = RsbsaApplication::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'application_number' => $application->application_number,
                    // Build full name with all name parts
                'full_name' => trim(
                    $application->first_name . ' ' .
                    ($application->middle_name ? $application->middle_name . ' ' : '') .
                    $application->last_name . ' ' .
                    ($application->name_extension ? $application->name_extension : '')
                ),
                'first_name' => $application->first_name,
                'middle_name' => $application->middle_name,
                'last_name' => $application->last_name,
                'name_extension' => $application->name_extension,
                    'sex' => $application->sex,
                    // 'mobile_number' => $application->contact_number,  // Map contact_number to mobile_number
                    'contact_number' => $application->contact_number,  // Keep for backward compatibility
                    'barangay' => $application->barangay,
                    'main_livelihood' => $application->main_livelihood,
                    'land_area' => $application->land_area,
                    'farm_location' => $application->farm_location,
                    'commodity' => $application->commodity,
                    'status' => $application->status,
                    'status_color' => $application->status_color,
                    'formatted_status' => $application->formatted_status,
                    'remarks' => $application->remarks,
                    'created_at' => $application->created_at->format('M d, Y h:i A'),
                    'updated_at' => $application->updated_at->format('M d, Y h:i A'),
                    'reviewed_at' => $application->reviewed_at ?
                        $application->reviewed_at->format('M d, Y h:i A') : null,
                    'reviewer_name' => optional($application->reviewer)->name,
                    'number_assigned_at' => $application->number_assigned_at ?
                        $application->number_assigned_at->format('M d, Y h:i A') : null,
                    'assigned_by_name' => optional($application->assignedBy)->name
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading RSBSA application', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading application details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of the specified RSBSA application
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'status' => 'required|in:pending,under_review,approved,rejected',
                'remarks' => 'nullable|string|max:1000',
            ]);

            // Find the application
            $application = RsbsaApplication::findOrFail($id);

            // Update the application
            $updateData = [
                'status' => $validated['status'],
                'remarks' => $validated['remarks'],
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id()
            ];

            // Set specific timestamp based on status
            if ($validated['status'] === 'approved') {
                $updateData['approved_at'] = now();
                $updateData['number_assigned_at'] = now();
                $updateData['assigned_by'] = auth()->id();
            } elseif ($validated['status'] === 'rejected') {
                $updateData['rejected_at'] = now();
            }

            $application->update($updateData);

            // Send email notification if approved and email is available
            if ($validated['status'] === 'approved' && $application->email) {
                try {
                    Mail::to($application->email)->send(new ApplicationApproved($application, 'rsbsa'));
                } catch (\Exception $e) {
                    // Log the error but don't fail the status update
                    Log::error('Failed to send approval email for RSBSA application ' . $application->id . ': ' . $e->getMessage());
                }
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully' .
                           ($validated['status'] === 'approved' && $application->email ? '. Email notification sent to applicant.' : ''),
                'data' => [
                    'status' => $application->status,
                    'formatted_status' => $application->formatted_status,
                    'status_color' => $application->status_color,
                    'updated_at' => $application->updated_at->format('M d, Y h:i A')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating RSBSA application status', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete the specified RSBSA application
    public function destroy(Request $request, $id)
    {
        try {
            $application = RsbsaApplication::findOrFail($id);
            $applicationNumber = $application->application_number;

            // Delete associated document if exists
            if ($application->supporting_document_path && Storage::disk('public')->exists($application->supporting_document_path)) {
                Storage::disk('public')->delete($application->supporting_document_path);
            }

            // Delete the application
            $application->delete();

            Log::info('RSBSA application deleted', [
                'application_id' => $id,
                'application_number' => $applicationNumber,
                'deleted_by' => auth()->user()->name ?? 'System'
            ]);

            $message = "Application {$applicationNumber} has been deleted successfully";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('admin.rsbsa.applications')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error deleting RSBSA application', [
                'application_id' => $id,
                'error' => $e->getMessage()
            ]);

            $errorMessage = 'Error deleting application: ' . $e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }
    /**
     * Download supporting document
     */
    public function downloadDocument($id)
    {
        try {
            $application = RsbsaApplication::findOrFail($id);

            if (!$application->supporting_document_path || !Storage::disk('public')->exists($application->supporting_document_path)) {
                return redirect()->back()->with('error', 'Document not found');
            }

            $fileName = "RSBSA_{$application->application_number}_document." .
                       pathinfo($application->supporting_document_path, PATHINFO_EXTENSION);

            return Storage::disk('public')->download($application->supporting_document_path, $fileName);

        } catch (\Exception $e) {
            Log::error('Error downloading RSBSA document', [
                'application_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error downloading document: ' . $e->getMessage());
        }
    }

    /**
     * Export applications to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = RsbsaApplication::query();

            // Apply same filters as index
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('barangay')) {
                $query->where('barangay', $request->barangay);
            }

            if ($request->filled('main_livelihood')) {
                $query->where('main_livelihood', $request->main_livelihood);
            }

            $applications = $query->orderBy('created_at', 'desc')->get();

            $filename = 'rsbsa_applications_' . now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($applications) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'Application Number',
                    'Full Name',
                    'Sex',
                    'Mobile Number',
                    'Barangay',
                    'Main Livelihood',
                    'Land Area (ha)',
                    'Farm Location',
                    'Commodity',
                    'Status',
                    'Date Applied',
                    'Date Reviewed'
                ]);

                // CSV data
                foreach ($applications as $application) {
                    fputcsv($file, [
                        $application->application_number,
                        $application->full_name,
                        $application->sex,
                        $application->mobile_number,
                        $application->barangay,
                        $application->main_livelihood,
                        $application->land_area,
                        $application->farm_location,
                        $application->commodity,
                        $application->formatted_status,
                        $application->created_at->format('M d, Y h:i A'),
                        $application->reviewed_at ?
                            $application->reviewed_at->format('M d, Y h:i A') : 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting RSBSA applications', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }


}
