<?php

namespace App\Http\Controllers;

use App\Models\BoatrApplication;
use App\Models\BoatrAnnex;
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

            $query = BoatrApplication::with(['reviewer', 'inspector', 'annexes']);

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

            if ($request->filled('barangay')) {
                $query->where('barangay', $request->barangay);
            }

            // Add date filtering
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Add search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->search($search);
            }

            // Sort and paginate
            $registrations = $query->orderBy('created_at', 'desc')
                                  ->paginate(10)
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

            // Get available barangays for filter dropdown
            $barangays = BoatrApplication::whereNotNull('barangay')
                ->where('barangay', '!=', '')
                ->distinct()
                ->orderBy('barangay')
                ->pluck('barangay');

            return view('admin.boatr.index', compact(
                'registrations',
                'totalRegistrations',
                'pendingCount',
                'underReviewCount',
                'inspectionRequiredCount',
                'inspectionScheduledCount',
                'documentsPendingCount',
                'approvedCount',
                'rejectedCount',
                'barangays'
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
     * Update the status of the specified BoatR registration - FULLY FIXED
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Log incoming request
            Log::info('BoatR updateStatus called', [
                'request_method' => $request->method(),
                'registration_id' => $id,
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            // Validate input
            $validated = $request->validate([
                'status' => 'required|in:pending,under_review,inspection_scheduled,inspection_required,documents_pending,approved,rejected',
                'remarks' => 'nullable|string|max:2000',
            ], [
                'status.required' => 'Status is required',
                'status.in' => 'Invalid status selected',
            ]);

            Log::info('Validation passed', ['validated_data' => $validated]);

            // Find registration
            $registration = BoatrApplication::findOrFail($id);
            $oldStatus = $registration->status;

            Log::info('Registration found', [
                'registration_id' => $registration->id,
                'current_status' => $oldStatus,
                'new_status' => $validated['status']
            ]);

            // Update directly (NO calling undefined methods)
            $registration->status = $validated['status'];
            $registration->remarks = $validated['remarks'] ?? $registration->remarks;
            $registration->reviewed_at = now();
            $registration->reviewed_by = auth()->id();
            $registration->save();

            Log::info('Status updated successfully', [
                'registration_id' => $registration->id,
                'old_status' => $oldStatus,
                'new_status' => $registration->status
            ]);

            // Refresh from database to ensure we have fresh data
            $registration->refresh();

            // Send approval email if approved
            if ($validated['status'] === 'approved' && $registration->email) {
                try {
                    Mail::to($registration->email)->send(new ApplicationApproved($registration, 'boatr'));
                    Log::info('Approval email sent', ['email' => $registration->email]);
                } catch (\Exception $mailError) {
                    Log::error('Failed to send approval email', [
                        'email' => $registration->email,
                        'error' => $mailError->getMessage()
                    ]);
                    // Don't fail the request if email fails
                }
            }

            // Build response with fresh data from database
            $response = [
                'success' => true,
                'message' => "Application {$registration->application_number} status updated to " . $this->formatStatus($registration->status),
                'registration' => [
                    'id' => $registration->id,
                    'status' => $registration->status,
                    'formatted_status' => $this->formatStatus($registration->status),
                    'status_color' => $this->getStatusColor($registration->status),
                    'inspection_completed' => (bool) $registration->inspection_completed,
                ]
            ];

            Log::info('Returning success response', ['response_data' => $response]);

            return response()->json($response, 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in updateStatus', [
                'registration_id' => $id,
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors()['status'] ?? []),
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Registration not found', ['registration_id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Unexpected error in updateStatus', [
                'registration_id' => $id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

   /**
     * Helper method to format status
     */
    private function formatStatus($status)
    {
        $statusMap = [
            'pending' => 'Pending',
            'under_review' => 'Under Review',
            'inspection_required' => 'Inspection Required',
            'inspection_scheduled' => 'Inspection Scheduled',
            'documents_pending' => 'Documents Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        return $statusMap[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }
 /**
     * Helper method to get status color
     */
    private function getStatusColor($status)
    {
        $colorMap = [
            'pending' => 'secondary',
            'under_review' => 'info',
            'inspection_required' => 'warning',
            'inspection_scheduled' => 'warning',
            'documents_pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];

        return $colorMap[$status] ?? 'secondary';
    }
    /**
     * Complete inspection - FULLY FIXED
     */
    public function completeInspection(Request $request, $id)
    {
        try {
            Log::info('completeInspection called', [
                'registration_id' => $id,
                'user_id' => auth()->id()
            ]);

            $validated = $request->validate([
                'supporting_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'inspection_notes' => 'nullable|string|max:2000',
                'approve_application' => 'sometimes|boolean'
            ]);

            Log::info('Validation passed for inspection');

            $registration = BoatrApplication::findOrFail($id);

            Log::info('Registration found', ['registration_id' => $registration->id]);

            // Handle inspection document upload
            if ($request->hasFile('supporting_document')) {
                $file = $request->file('supporting_document');

                Log::info('File received', [
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_mime' => $file->getMimeType()
                ]);

                if ($file->isValid()) {
                    $fileName = 'inspection_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('boatr_documents/inspection', $fileName, 'public');

                    Log::info('File stored', ['file_path' => $filePath]);

                    // Store inspection document in JSON array
                    $inspectionDocs = $registration->inspection_documents ?? [];
                    $inspectionDocs[] = [
                        'path' => $filePath,
                        'original_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                        'notes' => $validated['inspection_notes'] ?? null,
                        'uploaded_by' => auth()->id(),
                        'uploaded_at' => now()->toDateTimeString()
                    ];

                    $registration->inspection_documents = $inspectionDocs;
                    $registration->inspection_completed = true;
                    $registration->inspection_date = now();
                    $registration->inspector_id = auth()->id();
                    $registration->inspection_notes = $validated['inspection_notes'] ?? null;

                    Log::info('Inspection document added to registration');
                } else {
                    throw new \Exception('File upload validation failed');
                }
            } else {
                throw new \Exception('No file provided');
            }

            // Update status based on approval decision
            $newStatus = ($validated['approve_application'] ?? false) ? 'approved' : 'documents_pending';
            $registration->status = $newStatus;
            $registration->reviewed_at = now();
            $registration->reviewed_by = auth()->id();
            $registration->save();

            Log::info('Inspection completed and status updated', [
                'registration_id' => $registration->id,
                'new_status' => $newStatus,
                'inspection_completed' => true
            ]);

            // Send email if approved
            if ($newStatus === 'approved' && $registration->email) {
                try {
                    Mail::to($registration->email)->send(new ApplicationApproved($registration, 'boatr'));
                } catch (\Exception $mailError) {
                    Log::error('Failed to send approval email', [
                        'email' => $registration->email,
                        'error' => $mailError->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Inspection completed successfully' . ($newStatus === 'approved' ? ' and application approved.' : '.'),
                'registration' => [
                    'id' => $registration->id,
                    'status' => $registration->status,
                    'formatted_status' => $this->formatStatus($registration->status),
                    'status_color' => $this->getStatusColor($registration->status),
                    'inspection_completed' => $registration->inspection_completed,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in completeInspection', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Registration not found in completeInspection', ['registration_id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error completing inspection', [
                'registration_id' => $id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show individual application details - FIXED
     */
    public function show($id)
    {
        try {
            $registration = BoatrApplication::with(['reviewer', 'inspector', 'annexes.uploader'])->findOrFail($id);

            // Get user document with URL (single document) - FIXED
            $userDocument = $registration->getUserDocumentWithUrl();

            // Get inspection documents with URLs (multiple documents)
            $inspectionDocuments = $registration->getInspectionDocumentsWithUrls();

            // Get annexes data
            $annexes = $registration->annexes->map(function ($annex) {
                return [
                    'id' => $annex->id,
                    'title' => $annex->title,
                    'description' => $annex->description,
                    'file_name' => $annex->file_name,
                    'file_extension' => $annex->file_extension,
                    'file_size' => $annex->file_size,
                    'formatted_file_size' => $annex->formatted_file_size,
                    'is_image' => $annex->is_image,
                    'is_pdf' => $annex->is_pdf,
                    'uploaded_by' => $annex->uploader->name,
                    'created_at' => $annex->created_at->format('M d, Y h:i A'),
                ];
            });

            // FIXED: Return proper JSON structure with correct user_documents format
            return response()->json([
                'success' => true,
                'id' => $registration->id,
                'application_number' => $registration->application_number,
                'full_name' => $registration->full_name,
                'first_name' => $registration->first_name,
                'middle_name' => $registration->middle_name,
                'last_name' => $registration->last_name,
                'barangay' => $registration->barangay,
                'contact_number' => $registration->contact_number,
                'email' => $registration->email,
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

                // Annexes
                'annexes' => $annexes,
                'has_annexes' => $annexes->isNotEmpty(),

                // Document counts (including annexes)
                'total_documents_count' => $registration->total_documents_count + $annexes->count(),

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
     * Delete registration - FIXED & ENHANCED
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

            // Delete all associated annexes - FIXED: Get the collection first
            $annexes = $registration->annexes; // This returns a collection
            
            if ($annexes->isNotEmpty()) {
                foreach ($annexes as $annex) {
                    if ($annex->file_path && Storage::disk('public')->exists($annex->file_path)) {
                        Storage::disk('public')->delete($annex->file_path);
                    }
                    $annex->delete();
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

            if ($request->filled('barangay')) {
                $query->where('barangay', $request->barangay);
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
                    'Barangay',
                    'Contact Number',
                    'Email',
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
                        $registration->barangay ?? 'N/A',
                        $registration->contact_number ?? 'N/A',
                        $registration->email ?? 'N/A',
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

    /**
     * Get annexes for a specific registration
     */
    public function getAnnexes($id)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);
            $annexes = $registration->annexes()->with('uploader')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'annexes' => $annexes->map(function ($annex) {
                    return [
                        'id' => $annex->id,
                        'title' => $annex->title,
                        'description' => $annex->description,
                        'file_name' => $annex->file_name,
                        'file_extension' => $annex->file_extension,
                        'file_size' => $annex->file_size,
                        'formatted_file_size' => $annex->formatted_file_size,
                        'is_image' => $annex->is_image,
                        'is_pdf' => $annex->is_pdf,
                        'uploaded_by' => $annex->uploader->name,
                        'created_at' => $annex->created_at,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting annexes', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading annexes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a new annex for a registration
     */
    public function uploadAnnex(Request $request, $id)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);

            // Validation
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240', // 10MB max
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            $file = $request->file('file');

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'boatr/annexes/' . $registration->id . '/' . $filename;

            // Store file
            Storage::disk('public')->put($filePath, file_get_contents($file));

            // Create annex record
            $annex = $registration->annexes()->create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_extension' => $file->getClientOriginalExtension(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);

            Log::info('Annex uploaded successfully', [
                'registration_id' => $id,
                'annex_id' => $annex->id,
                'title' => $annex->title,
                'file_name' => $annex->file_name,
                'uploaded_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Annex uploaded successfully',
                'annex' => [
                    'id' => $annex->id,
                    'title' => $annex->title,
                    'description' => $annex->description,
                    'file_name' => $annex->file_name,
                    'formatted_file_size' => $annex->formatted_file_size,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error uploading annex', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error uploading annex: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview an annex
     */
    public function previewAnnex($id, $annexId)
    {
        try {
            Log::info('Preview annex request', [
                'registration_id' => $id,
                'annex_id' => $annexId,
                'user_id' => auth()->id()
            ]);

            $registration = BoatrApplication::findOrFail($id);
            $annex = $registration->annexes()->findOrFail($annexId);

            Log::info('Annex found', [
                'annex_title' => $annex->title,
                'file_path' => $annex->file_path,
                'file_exists' => Storage::disk('public')->exists($annex->file_path)
            ]);

            return response()->json([
                'success' => true,
                'title' => $annex->title,
                'description' => $annex->description,
                'file_name' => $annex->file_name,
                'file_extension' => $annex->file_extension,
                'file_url' => $annex->file_url,
                'is_image' => $annex->is_image,
                'is_pdf' => $annex->is_pdf,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Annex not found', [
                'id' => $id,
                'annex_id' => $annexId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Annex not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error previewing annex', [
                'id' => $id,
                'annex_id' => $annexId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading annex preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download an annex
     */
    public function downloadAnnex($id, $annexId)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);
            $annex = $registration->annexes()->findOrFail($annexId);

            if (!Storage::disk('public')->exists($annex->file_path)) {
                abort(404, 'File not found');
            }

            Log::info('Annex downloaded', [
                'registration_id' => $id,
                'annex_id' => $annexId,
                'title' => $annex->title,
                'downloaded_by' => auth()->id()
            ]);

            return Storage::disk('public')->download($annex->file_path, $annex->file_name);

        } catch (\Exception $e) {
            Log::error('Error downloading annex', [
                'id' => $id,
                'annex_id' => $annexId,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Error downloading file');
        }
    }

    /**
     * Delete an annex
     */
    public function deleteAnnex($id, $annexId)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);
            $annex = $registration->annexes()->findOrFail($annexId);

            // Delete the file and database record
            $deleted = $annex->deleteWithFile();

            if ($deleted) {
                Log::info('Annex deleted successfully', [
                    'registration_id' => $id,
                    'annex_id' => $annexId,
                    'title' => $annex->title,
                    'deleted_by' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Annex deleted successfully'
                ]);
            } else {
                throw new \Exception('Failed to delete annex');
            }

        } catch (\Exception $e) {
            Log::error('Error deleting annex', [
                'id' => $id,
                'annex_id' => $annexId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting annex: ' . $e->getMessage()
            ], 500);
        }
    }
}
