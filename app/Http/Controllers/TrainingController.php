<?php

namespace App\Http\Controllers;

use App\Models\TrainingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationApproved;
use App\Services\NotificationService;

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

        // Add date filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
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

      
        $barangays = [
            'Bagong Silang',
            'Calendola',
            'Chrysanthemum',
            'Cuyab',
            'Estrella',
            'Fatima',
            'G.S.I.S.',
            'Landayan',
            'Langgam',
            'Laram',
            'Magsaysay',
            'Maharlika',
            'Narra',
            'Nueva',
            'Pacita 1',
            'Pacita 2',
            'Poblacion',
            'Riverside',
            'Rosario',
            'Sampaguita Village',
            'San Antonio',
            'San Lorenzo Ruiz',
            'San Roque',
            'San Vicente',
            'Santo Niño',
            'United Bayanihan',
            'United Better Living'
        ];

        return view('admin.training.index', compact(
            'trainings',
            'totalApplications',
            'underReviewCount',
            'approvedCount',
            'rejectedCount',
            'barangays' 
        ));
    }


    /**
     * Store a newly created training application
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
                'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
                'email' => 'nullable|email|max:254',
                'barangay' => 'required|string|max:255',

                // Training info
                'training_type' => 'required|in:tilapia_hito,hydroponics,aquaponics,mushrooms,livestock_poultry,high_value_crops,sampaguita_propagation',

                // Status
                'status' => 'nullable|in:under_review,approved,rejected',
                'remarks' => 'nullable|string|max:1000',

                // CHANGED: Single document instead of array
                'supporting_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',

                // Optional user link
                'user_id' => 'nullable|exists:user_registration,id'
            ], [
                'barangay.required' => 'Barangay is required',
                'contact_number.regex' => 'Please enter a valid Philippine mobile number (09XXXXXXXXX)',
                'supporting_document.max' => 'Document must not exceed 10MB',
                'supporting_document.mimes' => 'Only JPG, PNG, and PDF files are allowed'
            ]);

            // Set user_id to null if not provided
            if (!isset($validated['user_id'])) {
                $validated['user_id'] = null;
            }

            // Generate unique application number
            $validated['application_number'] = $this->generateApplicationNumber();

            // CHANGED: Handle single document upload
            $documentPath = null;
            if ($request->hasFile('supporting_document')) {
                $file = $request->file('supporting_document');
                $filename = 'training_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $documentPath = $file->storeAs('training_documents', $filename, 'public');
            }
            $validated['document_path'] = $documentPath;

            // Set default status if not provided
            $validated['status'] = $validated['status'] ?? 'under_review';

            // Set reviewed fields if status is not under_review
            if ($validated['status'] !== 'under_review') {
                $validated['status_updated_at'] = now();
                $validated['updated_by'] = auth()->id();
            }

            // Create the application
            $training = TrainingApplication::create($validated);

            $this->logActivity('created', 'TrainingApplication', $training->id, [
                'application_number' => $training->application_number,
                'training_type' => $training->training_type_display,
                'barangay' => $training->barangay
            ]);

            // Send admin notification
            NotificationService::trainingApplicationCreated($training);

            Log::info('Training registration created by admin', [
                'application_id' => $training->id,
                'application_number' => $training->application_number,
                'created_by' => auth()->user()->name,
                'linked_to_user' => $validated['user_id'] ? 'Yes (ID: ' . $validated['user_id'] . ')' : 'No (Standalone registration)'
            ]);

            // Send email notification if approved and email is provided
            if ($validated['status'] === 'approved' && !empty($validated['email'])) {
                try {
                    Mail::to($validated['email'])->send(new ApplicationApproved($training, 'training'));
                } catch (\Exception $e) {
                    Log::error('Failed to send training approval email: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Training application created successfully',
                'data' => [
                    'id' => $training->id,
                    'application_number' => $training->application_number,
                    'status' => $training->status,
                    'linked_to_user' => $validated['user_id'] ? true : false
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating training application', [
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
     * Generate unique training application number
     */
    private function generateApplicationNumber()
    {
        do {
            $number = 'TRAIN-' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (TrainingApplication::where('application_number', $number)->exists());

        return $number;
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
                'name_extension' => $training->name_extension,
                'full_name' => $training->full_name,
                'contact_number' => $training->contact_number,
                'email' => $training->email,
                'barangay' => $training->barangay,
                'training_type' => $training->training_type,
                'training_type_display' => $training->training_type_display,
                'status' => $training->status,
                'formatted_status' => $training->formatted_status,
                'status_color' => $training->status_color,
                'remarks' => $training->remarks,
                'document_path' => $training->document_path,
                'document_url' => $training->document_url,
                'created_at' => $training->created_at->format('M d, Y g:i A'),
                'updated_at' => $training->updated_at->format('M d, Y g:i A'),
                'status_updated_at' => $training->status_updated_at ? $training->status_updated_at->format('M d, Y g:i A') : null,
                'updated_by_name' => $training->updatedBy ? $training->updatedBy->name : null
            ]
        ]);
    }

    /**
     * Update training application (personal info only)
     */
   public function update(Request $request, $id)
    {
        try {
            $training = TrainingApplication::findOrFail($id);

            // Validate the incoming data
            $validated = $request->validate([
                'first_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'last_name' => 'required|string|max:100',
                'name_extension' => 'nullable|string|max:10',
                'contact_number' => ['required', 'string', 'regex:/^(\+639|09)\d{9}$/'],
                'email' => 'nullable|email|max:254',
                'barangay' => 'required|string|max:255',
                'training_type' => 'required|in:tilapia_hito,hydroponics,aquaponics,mushrooms,livestock_poultry,high_value_crops,sampaguita_propagation',
            ], [
                'contact_number.regex' => 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)',
                'training_type.in' => 'Invalid training type selected',
            ]);

            // Update the training application
            $training->update($validated);

            $this->logActivity('updated', 'TrainingApplication', $training->id, [
                'fields_updated' => array_keys($validated),
                'application_number' => $training->application_number
            ]);

            Log::info('Training application updated', [
                'application_id' => $id,
                'application_number' => $training->application_number,
                'updated_by' => auth()->user()->name ?? 'System',
                'fields_updated' => 'Personal info, location, and training type'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Training application updated successfully',
                'data' => $training
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating training application', [
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
     * Update the status of a training application
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:under_review,approved,rejected',
            'remarks' => 'nullable|string|max:1000'
        ]);

        $training = TrainingApplication::findOrFail($id);
        $previousStatus = $training->status;

        // Update using trait method to trigger SMS notification
        $training->updateStatusWithNotification($request->status, $request->remarks);

        // Update additional fields manually
        $training->update([
            'status_updated_at' => now(),
            'updated_by' => Auth::id()
        ]);

        $this->logActivity('updated_status', 'TrainingApplication', $training->id, [
            'new_status' => $request->status,
            'remarks' => $request->remarks,
            'application_number' => $training->application_number
        ]);

        // Send admin notification about status change
        NotificationService::trainingApplicationStatusChanged($training, $previousStatus);

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
        try {
            $training = TrainingApplication::findOrFail($id);
            $applicationNumber = $training->application_number;
            $fullName = $training->full_name;

            // CHANGED: Delete single document if exists
            if ($training->hasDocument()) {
                if (Storage::disk('public')->exists($training->document_path)) {
                    Storage::disk('public')->delete($training->document_path);
                }
            }

            // Delete the training application
            $training->delete();

            $this->logActivity('deleted', 'TrainingApplication', $id, [
                'application_number' => $applicationNumber
            ]);

            // Send admin notification about deletion
            NotificationService::trainingApplicationDeleted($applicationNumber, $fullName);

            Log::info('Training application deleted', [
                'application_id' => $id,
                'application_number' => $applicationNumber,
                'deleted_by' => auth()->user()->name ?? 'System'
            ]);

            $message = "Application {$applicationNumber} has been deleted successfully";

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting training application', [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export training applications to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = TrainingApplication::with('updatedBy');

            // Apply same filters as index
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            if ($request->filled('status')) {
                $query->withStatus($request->status);
            }

            if ($request->filled('training_type')) {
                $query->withTrainingType($request->training_type);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $applications = $query->orderBy('created_at', 'desc')->get();

            $this->logActivity('exported', 'TrainingApplication', null, [
                'records_count' => $applications->count(),
                'filters' => $request->all()
            ]);

            $filename = 'training_applications_' . now()->format('Y-m-d_H-i-s') . '.csv';

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
                    'Contact Number',
                    'Email',
                    'Training Type',
                    'Status',
                    'Has Document',
                    'Date Applied',
                    'Date Updated',
                    'Updated By',
                    'Remarks'
                ]);

                // CSV data
                foreach ($applications as $application) {
                    fputcsv($file, [
                        $application->application_number,
                        $application->full_name,
                        $application->contact_number ?? 'N/A',
                        $application->email ?? 'N/A',
                        $application->training_type_display,
                        $application->formatted_status,
                        $application->hasDocument() ? 'Yes' : 'No',
                        $application->created_at->format('M d, Y h:i A'),
                        $application->updated_at->format('M d, Y h:i A'),
                        $application->updatedBy?->name ?? 'N/A',
                        $application->remarks ?? 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }
}
