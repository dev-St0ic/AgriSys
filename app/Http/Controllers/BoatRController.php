<?php

namespace App\Http\Controllers;

use App\Models\BoatrApplication;
use App\Models\BoatrAnnex;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     * Store a newly created BoatR registration
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming data
            $validated = $request->validate([
                // Personal Information
                'first_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'last_name' => 'required|string|max:100',
                'name_extension' => 'nullable|string|in:Jr.,Sr.,II,III,IV,V',
                'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
                'barangay' => 'required|string|max:100',
                'fishr_number' => 'required|string|max:50',

                // Vessel Information
                'vessel_name' => 'required|string|max:100',
                'boat_type' => 'required|in:Spoon,Plumb,Banca,Rake Stem - Rake Stern,Rake Stem - Transom/Spoon/Plumb Stern,Skiff (Typical Design)',

                // Boat Dimensions
                'boat_length' => 'required|numeric|min:0.1|max:999.99',
                'boat_width' => 'required|numeric|min:0.1|max:999.99',
                'boat_depth' => 'required|numeric|min:0.1|max:999.99',
                
                // boat classification
                'boat_classification' => 'required|in:Motorized,Non-motorized',

                // Engine Information
                'engine_type' => 'nullable|string|max:100',
                'engine_horsepower' => 'nullable|integer|min:1|max:9999',

                // Fishing Information
                'primary_fishing_gear' => 'required|in:Hook and Line,Bottom Set Gill Net,Fish Trap,Fish Coral,Not Applicable',

                // Status
                'status' => 'nullable|in:pending,under_review,inspection_required,inspection_scheduled,documents_pending,approved,rejected',
                'remarks' => 'nullable|string|max:2000',

                // Document
                'user_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',

                // IMPORTANT:
                'fishr_application_id' => 'nullable|exists:fishr_applications,id'
            ]);

             if ($validated['boat_classification'] === 'Motorized') {
            if (empty($validated['engine_type'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'engine_type' => ['Engine Type is required for motorized boats']
                ]);
            }
            
            if (empty($validated['engine_horsepower'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'engine_horsepower' => ['Engine Horsepower is required for motorized boats']
                ]);
            }
        } else {
            // For non-motorized boats, clear engine fields
            $validated['engine_type'] = null;
            $validated['engine_horsepower'] = null;
        }

            if (!isset($validated['fishr_application_id'])) {
                $validated['fishr_application_id'] = null;
            }

            // Generate unique application number
            $validated['application_number'] = $this->generateApplicationNumber();

            // Build full_name
            $fullName = $validated['first_name'] . ' ' .
                    ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') .
                    $validated['last_name'] .
                    ($validated['name_extension'] ? ' ' . $validated['name_extension'] : '');

            $validated['full_name'] = trim($fullName);

            // Calculate boat_dimensions string (Length × Width × Depth)
            $validated['boat_dimensions'] = $validated['boat_length'] . '×' .
                                        $validated['boat_width'] . '×' .
                                        $validated['boat_depth'] . ' ft';

            // Handle document upload
            if ($request->hasFile('user_document')) {
                $file = $request->file('user_document');
                $filename = 'boatr_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('boatr_documents', $filename, 'public');

                $validated['user_document_path'] = $path;
                $validated['user_document_name'] = $file->getClientOriginalName();
                $validated['user_document_type'] = $file->getMimeType();
                $validated['user_document_size'] = $file->getSize();
                $validated['user_document_uploaded_at'] = now();
            }

            // Set default status if not provided
            $validated['status'] = $validated['status'] ?? 'pending';

            // Set reviewed fields if status is not pending
            if ($validated['status'] !== 'pending') {
                $validated['reviewed_at'] = now();
                $validated['reviewed_by'] = auth()->id();
            }

            // Create the registration
            $registration = BoatrApplication::create($validated);

            Log::info('BoatR application created by admin', [
                'application_id' => $registration->id,
                'application_number' => $registration->application_number,
                'vessel_name' => $registration->vessel_name,
                'created_by' => auth()->user()->name,
                'linked_to_fishr' => $validated['fishr_application_id'] ? 'Yes (ID: ' . $validated['fishr_application_id'] . ')' : 'No'
            ]);

            // Log activity
            $this->logActivity('created', 'BoatrApplication', $registration->id, [
                'application_number' => $registration->application_number,
                'vessel_name' => $registration->vessel_name,
                'boat_type' => $registration->boat_type,
                'linked_to_fishr' => $validated['fishr_application_id'] ? 'Yes' : 'No'
            ]);

            // ✅ Send admin notification
            NotificationService::boatrApplicationCreated($registration);

            return response()->json([
                'success' => true,
                'message' => 'BoatR application created successfully',
                'data' => [
                    'id' => $registration->id,
                    'application_number' => $registration->application_number,
                    'vessel_name' => $registration->vessel_name,
                    'status' => $registration->status,
                    'linked_to_fishr' => $validated['fishr_application_id'] ? true : false
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error creating BoatR application', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error creating BoatR application', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique BoatR application number
     */
    private function generateApplicationNumber()
    {
        do {
            $number = 'BOATR-' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (BoatrApplication::where('application_number', $number)->exists());

        return $number;
    }
/**
 * Update BoatR registration with file uploads - FIXED
 */
public function update(Request $request, $id)
{
    try {
        // Validate input
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'name_extension' => 'nullable|string|max:20',
            'contact_number' => 'required|string|max:20',
            'barangay' => 'required|string|max:100',
            'vessel_name' => 'required|string|max:100',
            'boat_type' => 'required|string|max:100',
            'boat_classification' => 'required|in:Motorized,Non-motorized',
            'boat_length' => 'required|numeric|min:0.1',
            'boat_width' => 'required|numeric|min:0.1',
            'boat_depth' => 'required|numeric|min:0.1',
            'engine_type' => 'nullable|string|max:100',
            'engine_horsepower' => 'nullable|integer|min:1',
            'primary_fishing_gear' => 'required|string|max:100',
            'inspection_notes' => 'nullable|string|max:2000',
            'supporting_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'inspection_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'replace_inspection_document' => 'nullable|boolean',
        ]);

        if ($validated['boat_classification'] === 'Motorized') {
    if (empty($validated['engine_type'])) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'engine_type' => ['Engine Type is required for motorized boats']
        ]);
    }
    
    if (empty($validated['engine_horsepower'])) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'engine_horsepower' => ['Engine Horsepower is required for motorized boats']
        ]);
    }
} else {
    // For non-motorized boats, clear engine fields
    $validated['engine_type'] = null;
    $validated['engine_horsepower'] = null;
}

        // ✅ FIXED: Use correct model class name
        $boatr = BoatrApplication::findOrFail($id);

        // Update basic fields
        $boatr->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'name_extension' => $validated['name_extension'] ?? null,
            'contact_number' => $validated['contact_number'],
            'barangay' => $validated['barangay'],
            'vessel_name' => $validated['vessel_name'],
            'boat_type' => $validated['boat_type'],
            'boat_classification' => $validated['boat_classification'],
            'boat_length' => $validated['boat_length'],
            'boat_width' => $validated['boat_width'],
            'boat_depth' => $validated['boat_depth'],
            'engine_type' => $validated['engine_type'],
            'engine_horsepower' => $validated['engine_horsepower'],
            'primary_fishing_gear' => $validated['primary_fishing_gear'],
            'inspection_notes' => $validated['inspection_notes'] ?? null,
        ]);

        // Handle supporting document (replace if new one uploaded)
        if ($request->hasFile('supporting_document')) {
            // Delete old document if exists
            if ($boatr->user_document_path && Storage::disk('public')->exists($boatr->user_document_path)) {
                Storage::disk('public')->delete($boatr->user_document_path);
            }

            // Store new document
            $file = $request->file('supporting_document');
            $filename = 'boatr_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('boatr_documents', $filename, 'public');

            $boatr->update([
                'user_document_path' => $path,
                'user_document_name' => $file->getClientOriginalName(),
                'user_document_type' => $file->getMimeType(),
                'user_document_size' => $file->getSize(),
                'user_document_uploaded_at' => now(),
            ]);

            Log::info("Supporting document updated for BoatR registration {$boatr->id}");
        }

        // Handle inspection document (replace if replace flag is set and new file uploaded)
        if ($request->hasFile('inspection_document') && $request->input('replace_inspection_document')) {
            // Delete existing inspection documents
            if ($boatr->inspection_documents && is_array($boatr->inspection_documents)) {
                foreach ($boatr->inspection_documents as $oldDoc) {
                    if (isset($oldDoc['path']) && Storage::disk('public')->exists($oldDoc['path'])) {
                        Storage::disk('public')->delete($oldDoc['path']);
                    }
                }
            }

            // Store new inspection document
            $file = $request->file('inspection_document');
            $fileName = 'inspection_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('boatr_documents/inspection', $fileName, 'public');

            $inspectionDocs = [[
                'path' => $filePath,
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now()->toDateTimeString()
            ]];

            $boatr->update([
                'inspection_documents' => $inspectionDocs
            ]);

            Log::info("Inspection document replaced for BoatR registration {$boatr->id}");
        }

        Log::info("BoatR registration {$boatr->id} updated by " . auth()->user()->name);

        return response()->json([
            'success' => true,
            'message' => 'BoatR registration updated successfully',
            'registration' => $boatr->load(['reviewer', 'inspector', 'annexes'])
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validation error updating BoatR application', [
            'errors' => $e->errors()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Error updating BoatR application', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating the application: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * Helper method to track changed fields
 */
private function getChangedFields($original, $updated)
{
    $changed = [];
    foreach ($original as $key => $value) {
        if (isset($updated[$key]) && $updated[$key] != $value) {
            $changed[$key] = [
                'from' => $value,
                'to' => $updated[$key]
            ];
        }
    }
    return $changed;
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

            // Update status and related fields in one operation
            $registration->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'],
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id()
            ]);

            // Fire event for SMS notification
            if ($oldStatus !== $validated['status']) {
                try {
                    event(new \App\Events\ApplicationStatusChanged(
                        $registration,
                        $registration->getApplicationTypeName(),
                        $oldStatus,
                        $validated['status'],
                        $validated['remarks'] ?? null,
                        $registration->getApplicantPhone(),
                        $registration->getApplicantName()
                    ));
                } catch (\Exception $smsException) {
                    Log::warning('SMS notification failed but status update succeeded', [
                        'registration_id' => $id,
                        'error' => $smsException->getMessage()
                    ]);
                }

                // ✅ Send admin notification
                NotificationService::boatrApplicationStatusChanged(
                    $registration,
                    $oldStatus
                );
            }

            // Queue activity logging (non-blocking)
            try {
                dispatch(function() use ($registration, $oldStatus, $validated) {
                    activity()
                        ->performedOn($registration)
                        ->causedBy(auth()->user())
                        ->withProperties([
                            'application_number' => $registration->application_number,
                            'old_status' => $oldStatus,
                            'new_status' => $registration->status,
                            'remarks' => $validated['remarks'] ?? null
                        ])
                        ->log('updated');
                })->afterResponse();
            } catch (\Exception $logException) {
                Log::warning('Activity logging failed', ['error' => $logException->getMessage()]);
            }

            // Build and return response immediately
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
                'request_data' => $request->all()
            ]);

            // Validate with better error messages
            $validated = $request->validate([
                'supporting_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'inspection_notes' => 'nullable|string|max:2000',
                'approve_application' => 'nullable'
            ], [
                'supporting_document.required' => 'Please upload a supporting document',
                'supporting_document.file' => 'The supporting document must be a valid file',
                'supporting_document.mimes' => 'Only PDF, JPG, JPEG, and PNG files are allowed',
                'supporting_document.max' => 'File size must not exceed 10MB'
            ]);

            Log::info('Validation passed for inspection');

            $registration = BoatrApplication::findOrFail($id);

            Log::info('Registration found', ['registration_id' => $registration->id]);

            // Handle inspection document upload
            if (!$request->hasFile('supporting_document')) {
                throw new \Exception('No file uploaded. Please select a supporting document.');
            }

            $file = $request->file('supporting_document');

            Log::info('File received', [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_mime' => $file->getMimeType(),
                'is_valid' => $file->isValid()
            ]);

            if (!$file->isValid()) {
                throw new \Exception('Uploaded file is not valid. Please try again.');
            }

            // Store the file
            $fileName = 'inspection_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('boatr_documents/inspection', $fileName, 'public');

            if (!$filePath) {
                throw new \Exception('Failed to store file. Please check server storage permissions.');
            }

            Log::info('File stored successfully', ['file_path' => $filePath]);

            // Store inspection document in JSON array
            $inspectionDocs = $registration->inspection_documents ?? [];
            $inspectionDocs[] = [
                'path' => $filePath,
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'notes' => $validated['inspection_notes'] ?? null,
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now()->toDateTimeString()
            ];

            // Update registration
            $registration->inspection_documents = $inspectionDocs;
            $registration->inspection_completed = true;
            $registration->inspection_date = now();
            $registration->inspected_by = auth()->id();  // ✅ FIXED: Use correct column name
            $registration->inspection_notes = $validated['inspection_notes'] ?? null;

            // Handle approval decision - convert checkbox value to boolean
            $approveApplication = $request->input('approve_application') === '1' ||
                                $request->input('approve_application') === 'on' ||
                                $request->input('approve_application') === true;

            Log::info('Approval decision', [
                'raw_value' => $request->input('approve_application'),
                'converted_to_boolean' => $approveApplication
            ]);

            // Update status based on approval decision
            $oldStatus = $registration->status;
            $newStatus = $approveApplication ? 'approved' : 'documents_pending';
            $registration->status = $newStatus;
            $registration->reviewed_at = now();
            $registration->reviewed_by = auth()->id();

            // Save to database
            $saved = $registration->save();

            if (!$saved) {
                throw new \Exception('Failed to save inspection data to database');
            }

            Log::info('Inspection completed and status updated', [
                'registration_id' => $registration->id,
                'new_status' => $newStatus,
                'inspection_completed' => true,
                'auto_approved' => $approveApplication
            ]);

            // ✅ Send admin notification if status changed
            if ($oldStatus !== $newStatus) {
                NotificationService::boatrApplicationStatusChanged(
                    $registration,
                    $oldStatus
                );
            }

            $message = 'Inspection completed successfully';
            if ($newStatus === 'approved') {
                $message .= ' and application approved.';
            } else {
                $message .= '. Status set to Documents Pending.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'registration' => [
                    'id' => $registration->id,
                    'status' => $registration->status,
                    'formatted_status' => $this->formatStatus($registration->status),
                    'status_color' => $this->getStatusColor($registration->status),
                    'inspection_completed' => $registration->inspection_completed,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in completeInspection', [
                'registration_id' => $id,
                'errors' => $e->errors(),
                'messages' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . collect($e->errors())->flatten()->first(),
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Registration not found in completeInspection', ['registration_id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Application not found'
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
                'message' => 'Inspection failed: ' . $e->getMessage()
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
                    'file_path' => $annex->file_path,
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
                'name_extension' => $registration->name_extension,
                'barangay' => $registration->barangay,
                'contact_number' => $registration->contact_number,
                'fishr_number' => $registration->fishr_number,
                'vessel_name' => $registration->vessel_name,
                'boat_type' => $registration->boat_type,
                'boat_length' => $registration->boat_length,
                'boat_width' => $registration->boat_width,
                'boat_depth' => $registration->boat_depth,
                'boat_dimensions' => $registration->boat_dimensions,
                'boat_classification' => $registration->boat_classification, 
                'engine_type' => $registration->engine_type,
                'engine_horsepower' => $registration->engine_horsepower,
                'boat_classification' => $registration->boat_classification,
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
 * Delete registration - FIXED & ENHANCED WITH RECYCLE BIN
 */
public function destroy(Request $request, $id)
{
    try {
        $registration = BoatrApplication::findOrFail($id);
        $applicationNumber = $registration->application_number;

        // Move to recycle bin instead of permanent deletion
        \App\Services\RecycleBinService::softDelete(
            $registration,
            'Deleted from BoatR registrations'
        );

        // Send admin notification
        NotificationService::boatrApplicationDeleted(
            $applicationNumber,
            $registration->full_name
        );

        $this->logActivity('deleted', 'BoatrApplication', $id, [
            'application_number' => $applicationNumber,
            'action' => 'moved_to_recycle_bin'
        ]);

        Log::info('BoatR registration moved to recycle bin', [
            'registration_id' => $id,
            'application_number' => $applicationNumber,
            'deleted_by' => auth()->user()->name ?? 'System'
        ]);

        $message = "Application {$applicationNumber} has been moved to recycle bin";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('admin.boatr.requests')->with('success', $message);

    } catch (\Exception $e) {
        Log::error('Error moving BoatR registration to recycle bin', [
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

            // Log activity
            $this->logActivity('exported', 'BoatrApplication', null, [
                'records_count' => count($registrations),
                'filters_applied' => [
                    'status' => $request->filled('status') ? $request->status : null,
                    'boat_type' => $request->filled('boat_type') ? $request->boat_type : null,
                    'barangay' => $request->filled('barangay') ? $request->barangay : null
                ]
            ], 'Exported ' . count($registrations) . ' BoatR registrations to CSV');

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
                        'file_path' => $annex->file_path,
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
     * Delete an annex - moves to recycle bin
     */
    public function deleteAnnex($id, $annexId)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);
            $annex = $registration->annexes()->findOrFail($annexId);

            // Move to recycle bin instead of permanent delete
            \App\Services\RecycleBinService::softDelete(
                $annex,
                'Deleted from BoatR annexes'
            );

            Log::info('BoatR annex moved to recycle bin', [
                'registration_id' => $id,
                'annex_id' => $annexId,
                'title' => $annex->title,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Annex moved to recycle bin'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting BoatR annex', [
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
   /**
 * Validate FishR number - Allow MULTIPLE boats per FishR
 * User can register multiple boats with the same FishR
 */
public function validateFishrNumber($fishrNumber)
{
    try {
        $fishrNumber = urldecode(trim($fishrNumber));
        $currentUser = auth()->user();

        if (empty($fishrNumber)) {
            return response()->json([
                'exists' => false,
                'valid' => false,
                'message' => 'Registration number is required'
            ], 200);
        }

        // Search for FishR
        $searchValue = $fishrNumber;
        if (!str_starts_with(strtoupper($searchValue), 'FISHR-')) {
            $searchValue = 'FISHR-' . $searchValue;
        }

        $fishrApp = \App\Models\FishrApplication::where('registration_number', $searchValue)
            ->orWhere('registration_number', strtoupper($searchValue))
            ->orWhere('registration_number', strtolower($searchValue))
            ->first();

        if (!$fishrApp) {
            return response()->json([
                'exists' => false,
                'valid' => false,
                'message' => 'FishR number not found'
            ], 200);
        }

        // CHECK 1: Is it approved?
        if ($fishrApp->status !== 'approved') {
            return response()->json([
                'exists' => true,
                'valid' => false,
                'approved' => false,
                'message' => 'FishR number is not yet approved'
            ], 200);
        }

        // ✅ CHECK 2: Verify username matches (WITHOUT revealing owner info)
        if ($currentUser) {
            $currentUsername = strtoupper(trim($currentUser->username ?? ''));
            
            // Get the user who owns this FishR
            $fishrUser = \App\Models\User::find($fishrApp->user_id);
            $fishrUsername = strtoupper(trim($fishrUser->username ?? ''));

            // If usernames don't match, deny BUT DON'T REVEAL WHO OWNS IT
            if ($currentUsername && $fishrUsername && $currentUsername !== $fishrUsername) {
                \Log::warning('Username mismatch on FishR validation', [
                    'fishr_number' => $fishrApp->registration_number,
                    'current_user' => $currentUsername,
                    'fishr_user' => $fishrUsername
                ]);

                // ✅ GENERIC MESSAGE - Don't leak who owns it
                return response()->json([
                    'exists' => true,
                    'valid' => false,
                    'approved' => true,
                    'message' => 'This FishR number cannot be used with your account'
                ], 200);
            }
        }

        // CHECK 3: Already used for another boat?
        $boatCount = BoatrApplication::where('fishr_number', $fishrApp->registration_number)->count();
        if ($boatCount > 0) {
            return response()->json([
                'exists' => true,
                'valid' => false,
                'already_used' => true,
                'message' => 'This FishR number has already been registered for a boat'
            ], 200);
        }

        // ✅ ALL CHECKS PASSED
        return response()->json([
            'exists' => true,
            'valid' => true,
            'approved' => true,
            'first_name' => $fishrApp->first_name ?? '',
            'middle_name' => $fishrApp->middle_name ?? '',
            'last_name' => $fishrApp->last_name ?? '',
            'name_extension' => $fishrApp->name_extension ?? '',
            'contact_number' => $fishrApp->contact_number ?? '',
            'barangay' => $fishrApp->barangay ?? '',
            'fishr_app_id' => $fishrApp->id,
            'registration_number' => $fishrApp->registration_number,
            'boats_count' => $boatCount
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Error validating FishR number', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'exists' => false,
            'valid' => false,
            'message' => 'Error validating registration'
        ], 200);
    }
}
}
