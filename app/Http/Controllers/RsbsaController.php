<?php

namespace App\Http\Controllers;

use App\Models\RsbsaApplication;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                                  ->paginate(10)
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
                    'supporting_document_path' => $application->supporting_document_path,
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
     * OPTIMIZED: Update the status of the specified RSBSA application
     * Update personal information of an RSBSA application (for edit modal)
     */
   public function update(Request $request, $id)
{
    try {
        // DEBUG: Log incoming request data
        Log::info('RSBSA Update Request Received', [
            'application_id' => $id,
            'request_data' => $request->except(['supporting_document']),
            'has_file' => $request->hasFile('supporting_document'),
            'file_size' => $request->hasFile('supporting_document') ? $request->file('supporting_document')->getSize() : null,
        ]);

        // Validate incoming data - NOW INCLUDING LIVELIHOOD FIELDS AND FILE UPLOAD
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'name_extension' => 'nullable|string|max:10',
            'contact_number' => ['required', 'string', 'regex:/^(\+639|09)\d{9}$/'],
            'barangay' => 'required|string|max:100',
            'farm_location' => 'nullable|string|max:500',
            // NOW EDITABLE: Livelihood information
            'main_livelihood' => 'required|in:Farmer,Farmworker/Laborer,Fisherfolk,Agri-youth',
            'land_area' => 'nullable|numeric|min:0|max:99999.99',
            'commodity' => 'nullable|string|max:1000',
            'supporting_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        Log::info('RSBSA Validation Passed', [
            'application_id' => $id,
            'validated_data' => array_diff_key($validated, ['supporting_document' => '']),
        ]);

        // Find the application
        $application = RsbsaApplication::findOrFail($id);

        // Store original values for audit
        $originalData = $application->only([
            'first_name', 'middle_name', 'last_name', 'name_extension',
            'contact_number', 'barangay', 'farm_location',
            'main_livelihood', 'land_area', 'commodity', 'supporting_document_path'
        ]);

        // Handle file upload if provided
        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            
            // Delete old document if exists
            if ($application->supporting_document_path && Storage::disk('public')->exists($application->supporting_document_path)) {
                Storage::disk('public')->delete($application->supporting_document_path);
            }
            
            // Store new document
            $path = $file->store('rsbsa-applications', 'public');
            $validated['supporting_document_path'] = $path;
        }

        // Update the application
        $application->update($validated);

        // Log the changes
        $changes = [];
        foreach ($validated as $key => $value) {
            if (($originalData[$key] ?? null) !== $value) {
                $changes[$key] = [
                    'old' => $originalData[$key] ?? null,
                    'new' => $value
                ];
            }
        }

        $this->logActivity('updated', 'RsbsaApplication', $application->id, [
            'changes' => $changes,
            'application_number' => $application->application_number
        ]);

        Log::info('RSBSA application updated by admin', [
            'application_id' => $id,
            'application_number' => $application->application_number,
            'updated_by' => auth()->user()->name,
            'changes' => $changes
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully',
                'data' => [
                    'id' => $application->id,
                    'full_name' => $application->full_name,
                    'updated_at' => $application->updated_at->format('M d, Y g:i A')
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Application updated successfully');

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('RSBSA Validation Failed', [
            'application_id' => $id,
            'validation_errors' => $e->errors(),
            'request_data' => $request->except(['supporting_document']),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();

    } catch (\Exception $e) {
        Log::error('Error updating RSBSA application', [
            'application_id' => $id,
            'error' => $e->getMessage(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating application: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', 'Error updating application: ' . $e->getMessage());
    }
}

    /**
 * Update the status of an RSBSA application (separate from personal info edits)
 */
public function updateStatus(Request $request, $id)
{
    try {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $application = RsbsaApplication::findOrFail($id);

        // Store original status for comparison
        $originalStatus = $application->status;

        // Update status and remarks
        $application->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'] ?? $application->remarks,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Set approval/rejection timestamps
        if ($validated['status'] === 'approved') {
            $application->update([
                'approved_at' => now(),
                'number_assigned_at' => now(),
                'assigned_by' => auth()->id(),
            ]);
        } elseif ($validated['status'] === 'rejected') {
            $application->update([
                'rejected_at' => now(),
            ]);
        }

        // Fire SMS notification event if status changed to approved or rejected
        $statusChanged = ($originalStatus !== $validated['status']);
        if ($statusChanged && in_array($validated['status'], ['approved', 'rejected'])) {
            \Log::info('RSBSA Status changed - firing SMS notification', [
                'application_id' => $application->id,
                'previous_status' => $originalStatus,
                'new_status' => $validated['status']
            ]);

            $application->fireApplicationStatusChanged(
                $application->getApplicationTypeName(),
                $originalStatus,
                $validated['status'],
                $validated['remarks'] ?? null
            );
        }

        $this->logActivity('updated_status', 'RsbsaApplication', $application->id, [
            'old_status' => $originalStatus,
            'new_status' => $validated['status'],
            'remarks' => $validated['remarks']
        ]);

        Log::info('RSBSA application status updated', [
            'application_id' => $id,
            'application_number' => $application->application_number,
            'old_status' => $originalStatus,
            'new_status' => $validated['status'],
            'updated_by' => auth()->user()->name,
        ]);

        // Send admin notification about status change
        if ($statusChanged) {
            NotificationService::rsbsaApplicationStatusChanged($application, $originalStatus);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => [
                    'id' => $application->id,
                    'status' => $application->status,
                    'formatted_status' => $application->formatted_status,
                    'status_color' => $application->status_color,
                    'updated_at' => $application->updated_at->format('M d, Y g:i A')
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully');

    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        return redirect()->back()->withErrors($e->errors())->withInput();

    } catch (\Exception $e) {
        Log::error('Error updating RSBSA application status', [
            'application_id' => $id,
            'error' => $e->getMessage(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', 'Error updating status: ' . $e->getMessage());
    }
}

// /**
//  * Helper method to get changed fields for audit logging
//  */
// private function getChangedFields($original, $updated)
// {
//     $changes = [];
//     foreach ($updated as $key => $value) {
//         if (($original[$key] ?? null) !== $value) {
//             $changes[$key] = [
//                 'old' => $original[$key] ?? null,
//                 'new' => $value
//             ];
//         }
//     }
//     return $changes;
// }


    /**
     * Store a new RSBSA application ADD REGISTRATION
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming data
            $validated = $request->validate([
                // Basic info
                'first_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'last_name' => 'required|string|max:100',
                'name_extension' => 'nullable|string|max:10',
                'sex' => 'required|in:Male,Female,Preferred not to say',
                'contact_number' => ['required', 'string', 'regex:/^(\+639|09)\d{9}$/'],
                'barangay' => 'required|string|max:100',

                // Livelihood info
                'main_livelihood' => 'required|in:Farmer,Farmworker/Laborer,Fisherfolk,Agri-youth',
                'land_area' => 'nullable|numeric|min:0|max:99999.99',
                'farm_location' => 'nullable|string|max:500',
                'commodity' => 'nullable|string|max:1000',

                // Status
                'status' => 'nullable|in:pending,under_review,approved,rejected',

                // Document
                'supporting_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max

            ]);


            // Generate unique application number
            $validated['application_number'] = $this->generateApplicationNumber();

            // Handle document upload
            if ($request->hasFile('supporting_document')) {
                $file = $request->file('supporting_document');
                $filename = 'rsbsa_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('rsbsa_documents', $filename, 'public');
                $validated['supporting_document_path'] = $path;
            }

            // Set default status if not provided
            $validated['status'] = $validated['status'] ?? 'pending';

            // Set reviewed fields if status is not pending
            if ($validated['status'] !== 'pending') {
                $validated['reviewed_at'] = now();
                $validated['reviewed_by'] = auth()->id();

                if ($validated['status'] === 'approved') {
                    $validated['approved_at'] = now();
                    $validated['number_assigned_at'] = now();
                    $validated['assigned_by'] = auth()->id();
                } elseif ($validated['status'] === 'rejected') {
                    $validated['rejected_at'] = now();
                }
            }

            // Create the application
            $application = RsbsaApplication::create($validated);

            $this->logActivity('created', 'RsbsaApplication', $application->id, [
                'application_number' => $application->application_number,
                'main_livelihood' => $application->main_livelihood,
                'barangay' => $application->barangay
            ]);

            Log::info('RSBSA application created by admin', [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'created_by' => auth()->user()->name,
                'linked_to_user' => isset($validated['user_id']) && $validated['user_id'] ? 'Yes (ID: ' . $validated['user_id'] . ')' : 'No (Standalone registration)'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'RSBSA registration created successfully',
                'data' => [
                    'id' => $application->id,
                    'application_number' => $application->application_number,
                    'status' => $application->status,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating RSBSA registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the registration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique RSBSA application number
     */
    private function generateApplicationNumber()
    {
        do {
            $number = 'RSBSA-' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (RsbsaApplication::where('application_number', $number)->exists());

        return $number;
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

            $this->logActivity('deleted', 'RsbsaApplication', $id, [
                'application_number' => $applicationNumber
            ]);

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

            $this->logActivity('exported', 'RsbsaApplication', null, [
                'records_count' => $applications->count(),
                'filters' => $request->all()
            ]);

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
                        $application->contact_number,
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
