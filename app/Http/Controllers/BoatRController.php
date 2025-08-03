<?php

namespace App\Http\Controllers;

use App\Models\BoatrApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationApproved;

class BoatRController extends Controller
{
    /**
     * Display a listing of BoatR registrations - ENHANCED for auto-refresh
     */
    public function index(Request $request)
    {
        try {
            Log::info('BoatR index method called', [
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            $query = BoatrApplication::with(['reviewer', 'inspector']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('boat_type')) {
                $query->where('boat_type', $request->boat_type);
            }

            if ($request->filled('primary_fishing_gear')) {
                $query->where('primary_fishing_gear', $request->primary_fishing_gear);
            }

            // Add search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->search($search);
            }

            // Sort and paginate
            $registrations = $query->orderBy('created_at', 'desc')
                                  ->paginate(15)
                                  ->appends($request->query());

            // Calculate statistics
            $totalRegistrations = BoatrApplication::count();
            $pendingCount = BoatrApplication::where('status', 'pending')->count();
            $underReviewCount = BoatrApplication::where('status', 'under_review')->count();
            $inspectionRequiredCount = BoatrApplication::where('status', 'inspection_required')->count();
            $inspectionScheduledCount = BoatrApplication::where('status', 'inspection_scheduled')->count();
            $documentsPendingCount = BoatrApplication::where('status', 'documents_pending')->count();
            $approvedCount = BoatrApplication::where('status', 'approved')->count();
            $rejectedCount = BoatrApplication::where('status', 'rejected')->count();

            Log::info('BoatR data loaded successfully', [
                'total_registrations' => $totalRegistrations,
                'paginated_count' => $registrations->count(),
                'current_page' => $registrations->currentPage(),
            ]);

            // Handle AJAX requests for auto-refresh (same pattern as FishR)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('admin.boatr.partials.table', compact('registrations'))->render(),
                    'statistics' => [
                        'total' => $totalRegistrations,
                        'pending' => $pendingCount,
                        'inspection_required' => $inspectionRequiredCount,
                        'approved' => $approvedCount
                    ]
                ]);
            }

            return view('admin.boatr.index', compact(
                'registrations',
                'totalRegistrations',
                'pendingCount',
                'underReviewCount',
                'inspectionRequiredCount',
                'inspectionScheduledCount',
                'documentsPendingCount',
                'approvedCount',
                'rejectedCount'
            ));

        } catch (\Exception $e) {
            Log::error('Error in BoatR index:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading data: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error loading data: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the specified BoatR registration - ENHANCED with refresh data
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,under_review,inspection_scheduled,inspection_required,documents_pending,approved,rejected',
                'remarks' => 'nullable|string|max:2000',
            ], [
                'status.required' => 'Status is required',
                'status.in' => 'Invalid status selected',
            ]);

            $registration = BoatrApplication::findOrFail($id);
            $oldStatus = $registration->status;

            // Update the registration using the model method
            $registration->updateStatus(
                $validated['status'],
                $validated['remarks'],
                auth()->id()
            );

            // Send email notification if approved and email is available
            if ($validated['status'] === 'approved' && $registration->email) {
                try {
                    Mail::to($registration->email)->send(new ApplicationApproved($registration, 'boatr'));
                } catch (\Exception $e) {
                    // Log the error but don't fail the status update
                    Log::error('Failed to send approval email for BoatR registration ' . $registration->id . ': ' . $e->getMessage());
                }
            }

            Log::info('BoatR registration status updated', [
                'registration_id' => $registration->id,
                'application_number' => $registration->application_number,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'updated_by' => auth()->user()->name ?? 'System',
                'remarks' => $validated['remarks'],
                'email_sent' => ($validated['status'] === 'approved' && $registration->email) ? 'yes' : 'no'
            ]);

            $message = "Application {$registration->application_number} status updated to " . 
                      $registration->formatted_status .
                      ($validated['status'] === 'approved' && $registration->email ? '. Email notification sent to applicant.' : '');

            // ENHANCED: Return updated statistics for auto-refresh
            $statistics = [
                'total' => BoatrApplication::count(),
                'pending' => BoatrApplication::where('status', 'pending')->count(),
                'inspection_required' => BoatrApplication::where('status', 'inspection_required')->count(),
                'approved' => BoatrApplication::where('status', 'approved')->count()
            ];

            return response()->json([
                'success' => true,
                'message' => $message,
                'registration' => [
                    'id' => $registration->id,
                    'status' => $registration->status,
                    'formatted_status' => $registration->formatted_status,
                    'status_color' => $registration->status_color,
                    'inspection_completed' => $registration->inspection_completed,
                    'total_documents' => $registration->total_documents_count
                ],
                'statistics' => $statistics
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error updating BoatR registration status', [
                'registration_id' => $id,
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating registration status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete inspection - ENHANCED with refresh data
     */
    public function completeInspection(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'supporting_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'inspection_notes' => 'nullable|string|max:2000',
                'approve_application' => 'sometimes|boolean'
            ]);

            $registration = BoatrApplication::findOrFail($id);

            // Handle inspection document upload
            if ($request->hasFile('supporting_document')) {
                $file = $request->file('supporting_document');
                if ($file->isValid()) {
                    $fileName = 'inspection_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('boatr_documents/inspection', $fileName, 'public');
                    
                    // Add inspection document
                    $registration->addInspectionDocument($filePath, $file->getClientOriginalName());
                }
            }

            // Complete inspection
            $registration->completeInspection(
                $validated['inspection_notes'] ?? null,
                auth()->id()
            );

            // Update status based on approval decision
            $newStatus = ($validated['approve_application'] ?? false) ? 'approved' : 'documents_pending';
            $registration->updateStatus(
                $newStatus,
                $validated['inspection_notes'] ?? 'Inspection completed.',
                auth()->id()
            );

            // ENHANCED: Return updated statistics for auto-refresh
            $statistics = [
                'total' => BoatrApplication::count(),
                'pending' => BoatrApplication::where('status', 'pending')->count(),
                'inspection_required' => BoatrApplication::where('status', 'inspection_required')->count(),
                'approved' => BoatrApplication::where('status', 'approved')->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Inspection completed successfully' . ($newStatus === 'approved' ? ' and application approved.' : '.'),
                'registration' => [
                    'id' => $registration->id,
                    'status' => $registration->status,
                    'formatted_status' => $registration->formatted_status,
                    'status_color' => $registration->status_color,
                    'inspection_completed' => $registration->inspection_completed,
                    'total_documents' => $registration->total_documents_count
                ],
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error completing inspection', [
                'registration_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error completing inspection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show individual application details - FIXED
     */
    public function show($id)
    {
        try {
            $registration = BoatrApplication::with(['reviewer', 'inspector'])->findOrFail($id);

            // Get user document with URL (single document) - FIXED
            $userDocument = $registration->getUserDocumentWithUrl();
            
            // Get inspection documents with URLs (multiple documents)
            $inspectionDocuments = $registration->getInspectionDocumentsWithUrls();

            // FIXED: Return proper JSON structure with correct user_documents format
            return response()->json([
                'success' => true,
                'id' => $registration->id,
                'application_number' => $registration->application_number,
                'full_name' => $registration->full_name,
                'first_name' => $registration->first_name,
                'middle_name' => $registration->middle_name,
                'last_name' => $registration->last_name,
                'fishr_number' => $registration->fishr_number,
                'vessel_name' => $registration->vessel_name,
                'boat_type' => $registration->boat_type,
                'boat_length' => $registration->boat_length,
                'boat_width' => $registration->boat_width,
                'boat_depth' => $registration->boat_depth,
                'boat_dimensions' => $registration->boat_dimensions,
                'engine_type' => $registration->engine_type,
                'engine_horsepower' => $registration->engine_horsepower,
                'primary_fishing_gear' => $registration->primary_fishing_gear,
                'status' => $registration->status,
                'status_color' => $registration->status_color,
                'formatted_status' => $registration->formatted_status,
                'remarks' => $registration->remarks,
                
                // FIXED: Single user document - return as array for consistency with frontend
                'user_documents' => $userDocument ? [$userDocument] : [],
                'has_user_document' => $registration->hasUserDocument(),
                
                // Multiple inspection documents
                'inspection_documents' => $inspectionDocuments,
                'has_inspection_documents' => $registration->hasInspectionDocuments(),
                
                // Document counts
                'total_documents_count' => $registration->total_documents_count,
                
                // Inspection information
                'inspection_completed' => $registration->inspection_completed,
                'inspection_date' => $registration->inspection_date?->format('M d, Y h:i A'),
                'inspection_notes' => $registration->inspection_notes,
                'inspector_name' => $registration->inspector?->name,
                'documents_verified' => $registration->documents_verified,
                'documents_verified_at' => $registration->documents_verified_at?->format('M d, Y h:i A'),
                
                // Workflow information
                'is_ready_for_approval' => $registration->isReadyForApproval(),
                'requires_inspection' => $registration->requiresInspection(),
                
                // Timestamps
                'created_at' => $registration->created_at->format('M d, Y h:i A'),
                'updated_at' => $registration->updated_at->format('M d, Y h:i A'),
                'reviewed_at' => $registration->reviewed_at ? 
                    $registration->reviewed_at->format('M d, Y h:i A') : null,
                'reviewed_by_name' => $registration->reviewer?->name ?? null,
                
                // Status history
                'status_history' => $registration->status_history ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching BoatR registration', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * View/preview document - FIXED AND COMPLETE
     */
    public function viewDocument(Request $request, $id)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);
            $documentType = $request->get('type', 'user'); // 'user' or 'inspection'
            $documentIndex = (int) $request->get('index', 0);

            Log::info('Viewing document', [
                'registration_id' => $id,
                'type' => $documentType,
                'index' => $documentIndex
            ]);

            if ($documentType === 'user') {
                // Single user document
                if (!$registration->hasUserDocument()) {
                    return response()->json(['error' => 'No user document found'], 404);
                }

                $userDoc = $registration->getUserDocumentWithUrl();
                if (!$userDoc || !$userDoc['exists']) {
                    return response()->json(['error' => 'Document file not found'], 404);
                }

                return response()->json([
                    'success' => true,
                    'url' => $userDoc['url'],
                    'type' => $userDoc['type'],
                    'name' => $userDoc['original_name'],
                    'size' => $userDoc['size'],
                    'uploaded_at' => $userDoc['uploaded_at']
                ]);

            } else {
                // Multiple inspection documents
                $inspectionDocs = $registration->getInspectionDocumentsWithUrls();
                
                if (!$inspectionDocs || !isset($inspectionDocs[$documentIndex])) {
                    return response()->json(['error' => 'Inspection document not found'], 404);
                }

                $document = $inspectionDocs[$documentIndex];
                if (!$document['exists']) {
                    return response()->json(['error' => 'Document file not found'], 404);
                }

                return response()->json([
                    'success' => true,
                    'url' => $document['url'],
                    'type' => $document['extension'],
                    'name' => $document['original_name'],
                    'size' => $document['size'] ?? null,
                    'uploaded_at' => $document['uploaded_at'] ?? null,
                    'uploader' => $document['uploader'] ?? null,
                    'notes' => $document['notes'] ?? null
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error viewing document', [
                'registration_id' => $id,
                'type' => $request->get('type'),
                'index' => $request->get('index'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Error loading document: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Document preview for modal - NEW METHOD
     */
    public function documentPreview(Request $request, $id)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);
            $documentType = $request->input('type', 'user');
            $documentIndex = (int) $request->input('index', 0);

            Log::info('Document preview request', [
                'registration_id' => $id,
                'type' => $documentType,
                'index' => $documentIndex
            ]);

            if ($documentType === 'user') {
                if (!$registration->hasUserDocument()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No user document found'
                    ], 404);
                }

                $userDoc = $registration->getUserDocumentWithUrl();
                if (!$userDoc || !$userDoc['exists']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Document file not found'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'document_url' => $userDoc['url'],
                    'document_name' => $userDoc['original_name'],
                    'document_type' => $userDoc['type']
                ]);

            } else {
                $inspectionDocs = $registration->getInspectionDocumentsWithUrls();
                
                if (!$inspectionDocs || !isset($inspectionDocs[$documentIndex])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Inspection document not found'
                    ], 404);
                }

                $document = $inspectionDocs[$documentIndex];
                if (!$document['exists']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Document file not found'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'document_url' => $document['url'],
                    'document_name' => $document['original_name'],
                    'document_type' => $document['extension']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in document preview', [
                'registration_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading document preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download document - NEW METHOD
     */
    public function downloadDocument(Request $request, $id)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);
            $documentType = $request->get('type', 'user');
            $documentIndex = (int) $request->get('index', 0);

            Log::info('Document download request', [
                'registration_id' => $id,
                'type' => $documentType,
                'index' => $documentIndex
            ]);

            if ($documentType === 'user') {
                if (!$registration->hasUserDocument() || !$registration->user_document_path) {
                    abort(404, 'User document not found');
                }

                $filePath = $registration->user_document_path;
                $fileName = $registration->user_document_name ?? 'user_document.pdf';

                if (!Storage::disk('public')->exists($filePath)) {
                    abort(404, 'Document file not found');
                }

                return Storage::disk('public')->download($filePath, $fileName);

            } else {
                $inspectionDocs = $registration->inspection_documents ?? [];
                
                if (!isset($inspectionDocs[$documentIndex])) {
                    abort(404, 'Inspection document not found');
                }

                $document = $inspectionDocs[$documentIndex];
                $filePath = $document['path'];
                $fileName = $document['original_name'] ?? 'inspection_document.pdf';

                if (!Storage::disk('public')->exists($filePath)) {
                    abort(404, 'Document file not found');
                }

                return Storage::disk('public')->download($filePath, $fileName);
            }

        } catch (\Exception $e) {
            Log::error('Error downloading document', [
                'registration_id' => $id,
                'type' => $request->get('type'),
                'index' => $request->get('index'),
                'error' => $e->getMessage()
            ]);

            abort(500, 'Error downloading document: ' . $e->getMessage());
        }
    }

    /**
     * Delete registration - FIXED
     */
    public function destroy(Request $request, $id)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);
            $applicationNumber = $registration->application_number;

            // Delete user document if exists
            if ($registration->hasUserDocument() && $registration->user_document_path) {
                if (Storage::disk('public')->exists($registration->user_document_path)) {
                    Storage::disk('public')->delete($registration->user_document_path);
                }
            }

            // Delete inspection documents if they exist
            if ($registration->inspection_documents) {
                foreach ($registration->inspection_documents as $document) {
                    if (isset($document['path']) && Storage::disk('public')->exists($document['path'])) {
                        Storage::disk('public')->delete($document['path']);
                    }
                }
            }

            // Delete the registration
            $registration->delete();

            Log::info('BoatR registration deleted', [
                'registration_id' => $id,
                'application_number' => $applicationNumber,
                'deleted_by' => auth()->user()->name ?? 'System'
            ]);

            $message = "Application {$applicationNumber} has been deleted successfully";

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('admin.boatr.requests')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error deleting BoatR registration', [
                'registration_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'Error deleting registration: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Export registrations to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = BoatrApplication::with(['reviewer', 'inspector']);

            // Apply same filters as index
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('boat_type')) {
                $query->where('boat_type', $request->boat_type);
            }

            if ($request->filled('primary_fishing_gear')) {
                $query->where('primary_fishing_gear', $request->primary_fishing_gear);
            }

            $registrations = $query->orderBy('created_at', 'desc')->get();

            $filename = 'boatr_registrations_' . now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($registrations) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'Application Number',
                    'Full Name',
                    'FishR Number',
                    'Vessel Name',
                    'Boat Type',
                    'Dimensions (L×W×D)',
                    'Engine Type',
                    'Engine HP',
                    'Primary Fishing Gear',
                    'Status',
                    'Has User Document',
                    'Inspection Documents Count',
                    'Inspection Completed',
                    'Documents Verified',
                    'Date Applied',
                    'Date Reviewed',
                    'Reviewed By',
                    'Inspector'
                ]);

                // CSV data
                foreach ($registrations as $registration) {
                    fputcsv($file, [
                        $registration->application_number,
                        $registration->full_name,
                        $registration->fishr_number,
                        $registration->vessel_name,
                        $registration->boat_type,
                        $registration->boat_dimensions,
                        $registration->engine_type,
                        $registration->engine_horsepower,
                        $registration->primary_fishing_gear,
                        $registration->formatted_status,
                        $registration->hasUserDocument() ? 'Yes' : 'No',
                        count($registration->inspection_documents ?? []),
                        $registration->inspection_completed ? 'Yes' : 'No',
                        $registration->documents_verified ? 'Yes' : 'No',
                        $registration->created_at->format('M d, Y h:i A'),
                        $registration->reviewed_at ? 
                            $registration->reviewed_at->format('M d, Y h:i A') : 'N/A',
                        $registration->reviewer?->name ?? 'N/A',
                        $registration->inspector?->name ?? 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting BoatR registrations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }
}