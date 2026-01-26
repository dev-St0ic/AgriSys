<?php

namespace App\Http\Controllers;

use App\Models\FishrApplication;
use App\Models\FishrAnnex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FishRController extends Controller
{
    /**
     * Display a listing of FishR registrations
     */
    public function index(Request $request)
    {
        try {
            Log::info('FishR index method called', [
                'request_data' => $request->all(),
            ]);

            $query = FishrApplication::query();

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('barangay')) {
                $query->where('barangay', $request->barangay);
            }

            if ($request->filled('livelihood')) {
                $query->where('main_livelihood', $request->livelihood);
            }

            // Date range filtering
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('registration_number', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('contact_number', 'like', "%{$search}%");
                });
            }

            // Sort and paginate
            $registrations = $query->orderBy('created_at', 'desc')
                                  ->paginate(10)
                                  ->appends($request->query());

            // Calculate statistics
            $totalRegistrations = FishrApplication::count();
            $underReviewCount = FishrApplication::where('status', 'under_review')->count();
            $approvedCount = FishrApplication::where('status', 'approved')->count();
            $pendingCount = FishrApplication::where('status', 'pending')->count();

            Log::info('FishR data loaded successfully', [
                'total_registrations' => $totalRegistrations,
                'paginated_count' => $registrations->count(),
                'current_page' => $registrations->currentPage(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $registrations,
                    'view' => view('admin.fishr.partials.table', compact('registrations'))->render()
                ]);
            }

            // FIXED: Changed view path to match your actual file location
            return view('admin.fishr.index', compact(
                'registrations',
                'totalRegistrations',
                'underReviewCount',
                'approvedCount',
                'pendingCount'
            ));

        } catch (\Exception $e) {
            Log::error('Error fetching FishR registrations', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading registrations: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error loading registrations: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified FishR registration
     */
    public function show($id)
    {
        try {
            $registration = FishrApplication::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $registration->id,
                    'registration_number' => $registration->registration_number,
                    'full_name' => $registration->full_name,
                    'first_name' => $registration->first_name,
                    'middle_name' => $registration->middle_name,
                    'last_name' => $registration->last_name,
                    'name_extension' => $registration->name_extension,
                    'sex' => $registration->sex,
                    'barangay' => $registration->barangay,
                    'contact_number' => $registration->contact_number,
                    'main_livelihood' => $registration->main_livelihood,
                    'livelihood_description' => $registration->livelihood_description,
                    'other_livelihood' => $registration->other_livelihood,
                    'status' => $registration->status,
                    'status_color' => $registration->status_color,
                    'formatted_status' => $registration->formatted_status,
                    'remarks' => $registration->remarks,
                    'created_at' => $registration->created_at->format('M d, Y h:i A'),
                    'updated_at' => $registration->updated_at->format('M d, Y h:i A'),
                    'status_updated_at' => $registration->status_updated_at ?
                        $registration->status_updated_at->format('M d, Y h:i A') : null,
                    'updated_by_name' => optional($registration->updatedBy)->name,
                    'document_path' => $registration->document_path
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading FishR registration', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading registration details: ' . $e->getMessage()
            ], 500);
        }
    }

 /**
 * Update the specified FishR registration (Personal info editing)
 */
public function update(Request $request, $id)
{
    try {
        // Validate the incoming data
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'name_extension' => 'nullable|string|max:10',
            'sex' => 'required|in:Male,Female,Preferred not to say',
            'contact_number' => ['required', 'string', 'regex:/^(\+639|09)\d{9}$/'],
            'barangay' => 'required|string|max:100',

            // NOW EDITABLE: Livelihood info
            'main_livelihood' => 'required|in:capture,aquaculture,vending,processing,others',
            'other_livelihood' => 'nullable|string|max:255',
            
            // Document - FIXED: accept image files and pdf
            'supporting_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        // Find the registration
        $registration = FishrApplication::findOrFail($id);

        // Handle document upload - FIXED
        if ($request->hasFile('supporting_document')) {
            // Delete old document if exists
            if ($registration->document_path && Storage::disk('public')->exists($registration->document_path)) {
                Storage::disk('public')->delete($registration->document_path);
            }

            // Upload new document
            $file = $request->file('supporting_document');
            $filename = 'fishr_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('fishr_documents', $filename, 'public');
            $validated['document_path'] = $path;
        }

        // Update the registration with all fields
        $registration->update($validated);

        $this->logActivity('updated', 'FishrApplication', $registration->id, [
            'fields_updated' => array_keys($validated),
            'registration_number' => $registration->registration_number
        ]);

        Log::info('FishR registration updated by admin', [
            'registration_id' => $registration->id,
            'registration_number' => $registration->registration_number,
            'updated_by' => auth()->user()->name,
            'fields_updated' => array_keys($validated),
            'document_updated' => $request->hasFile('supporting_document') ? 'yes' : 'no'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration information updated successfully',
            'data' => [
                'id' => $registration->id,
                'full_name' => $registration->full_name,
                'updated_at' => $registration->updated_at->format('M d, Y h:i A'),
                'document_path' => $registration->document_path
            ]
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Error updating FishR registration', [
            'id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error updating registration: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update the status of the specified FishR registration
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'status' => 'required|in:under_review,approved,rejected',
                'remarks' => 'nullable|string|max:1000',
            ]);

            // Find the registration
            $registration = FishrApplication::findOrFail($id);
            $previousStatus = $registration->status;

            // Update status and all related fields in one go
            $registration->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'],
                'status_updated_at' => now(),
                'updated_by' => auth()->id()
            ]);

            // Fire event for SMS notification (queued listener will handle it asynchronously)
            if ($previousStatus !== $validated['status']) {
                event(new \App\Events\ApplicationStatusChanged(
                    $registration,
                    $registration->getApplicationTypeName(),
                    $previousStatus,
                    $validated['status'],
                    $validated['remarks'],
                    $registration->getApplicantPhone(),
                    $registration->getApplicantName()
                ));

                // ✅ Send admin notification
                \App\Services\NotificationService::fishrApplicationStatusChanged(
                    $registration,
                    $previousStatus
                );
            }

            // Queue activity logging (non-blocking)
            dispatch(function() use ($registration, $previousStatus, $validated) {
                activity()
                    ->performedOn($registration)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old_status' => $previousStatus,
                        'new_status' => $validated['status'],
                        'remarks' => $validated['remarks']
                    ])
                    ->log('updated_status');
            })->afterResponse();

            // Return success response immediately
            return response()->json([
                'success' => true,
                'message' => 'Registration status updated successfully',
                'data' => [
                    'status' => $registration->status,
                    'formatted_status' => $registration->formatted_status,
                    'status_color' => $registration->status_color,
                    'updated_at' => $registration->updated_at->format('M d, Y h:i A')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating FishR registration status', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Store a newly created FishR registration
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
                'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
                'barangay' => 'required|string|max:100',

                // Livelihood info
                'main_livelihood' => 'required|in:capture,aquaculture,vending,processing,others',
                'other_livelihood' => 'nullable|string|max:255',

                // Status
                'status' => 'nullable|in:under_review,approved,rejected',
                'remarks' => 'nullable|string|max:1000',

                // Document
                'supporting_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',

            ]);

            // Generate unique registration number
            $validated['registration_number'] = $this->generateRegistrationNumber();

            // Calculate livelihood_description
            $livelihood_map = [
                'capture' => 'Capture Fishing',
                'aquaculture' => 'Aquaculture',
                'vending' => 'Fish Vending',
                'processing' => 'Fish Processing',
                'others' => $validated['other_livelihood'] ?? 'Others'
            ];
            $validated['livelihood_description'] = $livelihood_map[$validated['main_livelihood']] ?? 'Others';

            // Handle document upload
            if ($request->hasFile('supporting_document')) {
                $file = $request->file('supporting_document');
                $filename = 'fishr_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('fishr_documents', $filename, 'public');
                $validated['document_path'] = $path;
            }

            // Set default status if not provided
            $validated['status'] = $validated['status'] ?? 'under_review';

            // Set reviewed fields if status is not under_review
            if ($validated['status'] !== 'under_review') {
                $validated['status_updated_at'] = now();
                $validated['reviewed_by'] = auth()->id();
                $validated['reviewed_at'] = now();
            }

            // Create the registration
            $registration = FishrApplication::create($validated);

            // ✅ Send admin notification
            \App\Services\NotificationService::fishrApplicationCreated($registration);

            $this->logActivity('created', 'FishrApplication', $registration->id, [
                'registration_number' => $registration->registration_number,
                'livelihood' => $registration->livelihood_description,
                'barangay' => $registration->barangay
            ]);

            Log::info('FishR registration created by admin', [
                'registration_id' => $registration->id,
                'registration_number' => $registration->registration_number,
                'created_by' => auth()->user()->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FishR registration created successfully',
                'data' => [
                    'id' => $registration->id,
                    'registration_number' => $registration->registration_number,
                    'status' => $registration->status
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating FishR registration', [
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
     * Generate unique FishR registration number
     */
    private function generateRegistrationNumber()
    {
        do {
            $number = 'FISHR-' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (FishrApplication::where('registration_number', $number)->exists());

        return $number;
    }

        /**
         * Remove the specified FishR registration from storage
         */
        public function destroy($id)
        {
            try {
                $registration = FishrApplication::findOrFail($id);
                $registrationNumber = $registration->registration_number;

                // Delete associated document if exists
                if ($registration->document_path && Storage::disk('public')->exists($registration->document_path)) {
                    Storage::disk('public')->delete($registration->document_path);
                }

                // Delete all associated annexes - FIXED: Get the collection first
                $annexes = $registration->annexes; // This returns a collection, not a query

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

                // ✅ Send admin notification
                \App\Services\NotificationService::fishrApplicationDeleted(
                    $registrationNumber,
                    $registration->full_name
                );

                $this->logActivity('deleted', 'FishrApplication', $id, [
                    'registration_number' => $registrationNumber
                ]);

                Log::info('FishR registration deleted', [
                    'registration_id' => $id,
                    'registration_number' => $registrationNumber,
                    'deleted_by' => auth()->user()->name ?? 'System'
                ]);

                $message = "Registration {$registrationNumber} has been deleted successfully";

                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);

            } catch (\Exception $e) {
                Log::error('Error deleting FishR registration', [
                    'registration_id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting registration: ' . $e->getMessage()
                ], 500);
            }
        }

    /**
     * Download supporting document
     */
    public function downloadDocument($id)
    {
        try {
            $registration = FishrApplication::findOrFail($id);

            if (!$registration->document_path || !Storage::disk('public')->exists($registration->document_path)) {
                return redirect()->back()->with('error', 'Document not found');
            }

            $fileName = "FishR_{$registration->registration_number}_document." .
                       pathinfo($registration->document_path, PATHINFO_EXTENSION);

            return Storage::disk('public')->download($registration->document_path, $fileName);

        } catch (\Exception $e) {
            Log::error('Error downloading FishR document', [
                'registration_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error downloading document: ' . $e->getMessage());
        }
    }

    /**
     * Export registrations to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = FishrApplication::query();

            // Apply same filters as index
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('barangay')) {
                $query->where('barangay', $request->barangay);
            }

            if ($request->filled('livelihood')) {
                $query->where('main_livelihood', $request->livelihood);
            }

            $registrations = $query->orderBy('created_at', 'desc')->get();

            $this->logActivity('exported', 'FishrApplication', null, [
                'records_count' => $registrations->count(),
                'filters' => $request->all()
            ]);

            $filename = 'fishr_registrations_' . now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($registrations) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'Registration Number',
                    'Full Name',
                    'Sex',
                    'Barangay',
                    'Contact Number',
                    'Main Livelihood',
                    'Livelihood Description',
                    'Status',
                    'Date Applied',
                    'Status Updated'
                ]);

                // CSV data
                foreach ($registrations as $registration) {
                    fputcsv($file, [
                        $registration->registration_number,
                        $registration->full_name,
                        $registration->sex,
                        $registration->barangay,
                        $registration->contact_number,
                        $registration->main_livelihood,
                        $registration->livelihood_description,
                        $registration->formatted_status,
                        $registration->created_at->format('M d, Y h:i A'),
                        $registration->status_updated_at ?
                            $registration->status_updated_at->format('M d, Y h:i A') : 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting FishR registrations', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }

    /**
     * Generate and assign FishR number
     */
    public function generateNumber($id)
    {
        try {
            $registration = FishrApplication::findOrFail($id);

            if ($registration->registration_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration already has a number'
                ], 400);
            }

            if ($registration->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only generate numbers for approved registrations'
                ], 400);
            }

            // Generate unique number
            do {
                $number = 'FISHR-' . strtoupper(Str::random(8));
            } while (FishrApplication::where('registration_number', $number)->exists());

            // Assign number
            $registration->update([
                'registration_number' => $number,
                'number_assigned_at' => now(),
                'assigned_by' => auth()->id()
            ]);

            Log::info('FishR number generated', [
                'registration_id' => $id,
                'number' => $number,
                'user' => auth()->user()->name
            ]);

            return response()->json([
                'success' => true,
                'number' => $number,
                'message' => 'FishR number generated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating FishR number', [
                'registration_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign FishR number to a registration
     */
    public function assignFishRNumber(Request $request, $id)
    {
        try {
            $registration = FishrApplication::findOrFail($id);

            if ($registration->fishr_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'FishR number already assigned'
                ], 400);
            }

            if ($registration->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only assign FishR numbers to approved registrations'
                ], 400);
            }

            // Generate unique FishR number
            do {
                $number = 'FISHR-' . strtoupper(Str::random(8));
            } while (FishrApplication::where('fishr_number', $number)->exists());

            $registration->update([
                'fishr_number' => $number,
                'fishr_number_assigned_at' => now(),
                'fishr_number_assigned_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FishR number assigned successfully',
                'data' => [
                    'fishr_number' => $number,
                    'assigned_at' => $registration->fishr_number_assigned_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning FishR number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get annexes for a specific registration
     */
    public function getAnnexes($id)
    {
        try {
            $registration = FishrApplication::findOrFail($id);
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
            $registration = FishrApplication::findOrFail($id);

            // Validation
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240', // 10MB max
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            $file = $request->file('file');

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'fishr/annexes/' . $registration->id . '/' . $filename;

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

            Log::info('FishR annex uploaded successfully', [
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
            Log::error('Error uploading FishR annex', [
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
            Log::info('Preview FishR annex request', [
                'registration_id' => $id,
                'annex_id' => $annexId,
            ]);

            $registration = FishrApplication::findOrFail($id);
            $annex = $registration->annexes()->findOrFail($annexId);

            Log::info('FishR annex found', [
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
            Log::error('FishR annex not found', [
                'id' => $id,
                'annex_id' => $annexId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Annex not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error previewing FishR annex', [
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
            $registration = FishrApplication::findOrFail($id);
            $annex = $registration->annexes()->findOrFail($annexId);

            if (!Storage::disk('public')->exists($annex->file_path)) {
                abort(404, 'File not found');
            }

            Log::info('FishR annex downloaded', [
                'registration_id' => $id,
                'annex_id' => $annexId,
                'title' => $annex->title,
                'downloaded_by' => auth()->id()
            ]);

            return Storage::disk('public')->download($annex->file_path, $annex->file_name);

        } catch (\Exception $e) {
            Log::error('Error downloading FishR annex', [
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
            $registration = FishrApplication::findOrFail($id);
            $annex = $registration->annexes()->findOrFail($annexId);

            // Delete the file and database record
            $deleted = $annex->deleteWithFile();

            if ($deleted) {
                Log::info('FishR annex deleted successfully', [
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
            Log::error('Error deleting FishR annex', [
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
