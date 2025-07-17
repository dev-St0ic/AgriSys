<?php

namespace App\Http\Controllers;

use App\Models\BoatrApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BoatRController extends Controller
{
    /**
     * Display a listing of BoatR registrations
     */
    public function index(Request $request)
    {
        try {
            Log::info('BoatR index method called', [
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            $query = BoatrApplication::query();

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('barangay')) {
                $query->where('barangay', $request->barangay);
            }

            if ($request->filled('boat_type')) {
                $query->where('boat_type', $request->boat_type);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('application_number', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('vessel_name', 'like', "%{$search}%")
                      ->orWhere('fishr_number', 'like', "%{$search}%");
                });
            }

            // Sort and paginate
            $registrations = $query->orderBy('created_at', 'desc')
                                  ->paginate(15)
                                  ->appends($request->query());

            // Calculate statistics
            $totalRegistrations = BoatrApplication::count();
            $pendingCount = BoatrApplication::where('status', 'pending')->count();
            $inspectionRequiredCount = BoatrApplication::where('status', 'inspection_required')->count();
            $approvedCount = BoatrApplication::where('status', 'approved')->count();
            $rejectedCount = BoatrApplication::where('status', 'rejected')->count();

            Log::info('BoatR data loaded successfully', [
                'total_registrations' => $totalRegistrations,
                'paginated_count' => $registrations->count(),
                'current_page' => $registrations->currentPage(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $registrations,
                    'view' => view('admin.boatr_requests.partials.table', compact('registrations'))->render()
                ]);
            }

            return view('admin.boatr_requests.index', compact(
                'registrations',
                'totalRegistrations',
                'pendingCount',
                'inspectionRequiredCount',
                'approvedCount',
                'rejectedCount'
            ));

        } catch (\Exception $e) {
            Log::error('Error fetching BoatR registrations', [
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
     * Display the specified BoatR registration
     */
    public function show($id)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);

            // If it's an AJAX request, return JSON
            if (request()->ajax()) {
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
                    'supporting_document_path' => $registration->supporting_document_path,
                    'inspection_completed' => $registration->inspection_completed,
                    'inspection_date' => $registration->inspection_date?->format('M d, Y h:i A'),
                    'created_at' => $registration->created_at->format('M d, Y h:i A'),
                    'updated_at' => $registration->updated_at->format('M d, Y h:i A'),
                    'reviewed_at' => $registration->reviewed_at ? 
                        $registration->reviewed_at->format('M d, Y h:i A') : null,
                    'reviewed_by_name' => $registration->reviewer?->name ?? null,
                ]);
            }

            return view('admin.boatr_requests.show', compact('registration'));

        } catch (\Exception $e) {
            Log::error('Error fetching BoatR registration', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration not found'
                ], 404);
            }

            return redirect()->route('admin.boatr.requests')
                           ->with('error', 'Registration not found');
        }
    }

    /**
     * Update the status of the specified BoatR registration
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected,inspection_required',
                'remarks' => 'nullable|string|max:1000',
            ], [
                'status.required' => 'Status is required',
                'status.in' => 'Invalid status selected',
            ]);

            $registration = BoatrApplication::findOrFail($id);
            $oldStatus = $registration->status;

            // Update the registration
            $registration->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'],
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id()
            ]);

            Log::info('BoatR registration status updated', [
                'registration_id' => $registration->id,
                'application_number' => $registration->application_number,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'updated_by' => auth()->user()->name ?? 'System',
                'remarks' => $validated['remarks']
            ]);

            $message = "Application {$registration->application_number} status updated to " . 
                      ucfirst(str_replace('_', ' ', $validated['status']));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'status' => $registration->status,
                        'formatted_status' => $registration->formatted_status,
                        'status_color' => $registration->status_color,
                        'reviewed_at' => $registration->reviewed_at->format('M d, Y h:i A')
                    ]
                ]);
            }

            return redirect()->route('admin.boatr.show', $registration->id)
                           ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->validator)->withInput();

        } catch (\Exception $e) {
            Log::error('Error updating BoatR registration status', [
                'registration_id' => $id,
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            $errorMessage = 'Error updating registration status: ' . $e->getMessage();

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
     * Mark inspection as completed and allow document upload
     */
    public function completeInspection(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'supporting_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'inspection_notes' => 'nullable|string|max:1000',
            ], [
                'supporting_document.required' => 'Supporting document is required',
                'supporting_document.mimes' => 'Document must be PDF, JPG, JPEG, or PNG',
                'supporting_document.max' => 'Document must not exceed 10MB'
            ]);

            $registration = BoatrApplication::findOrFail($id);

            // Handle file upload
            $documentPath = null;
            if ($request->hasFile('supporting_document')) {
                $file = $request->file('supporting_document');
                if ($file->isValid()) {
                    $documentPath = $file->store('boatr_documents', 'public');
                } else {
                    throw new \Exception('Invalid file upload');
                }
            }

            // Update registration with inspection completion
            $registration->update([
                'inspection_completed' => true,
                'inspection_date' => now(),
                'supporting_document_path' => $documentPath,
                'status' => 'approved', // Auto-approve after inspection
                'remarks' => $validated['inspection_notes'] ?? 'Inspection completed successfully',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id()
            ]);

            Log::info('BoatR inspection completed', [
                'registration_id' => $registration->id,
                'application_number' => $registration->application_number,
                'inspected_by' => auth()->user()->name ?? 'System',
                'document_path' => $documentPath
            ]);

            $message = "Inspection completed for application {$registration->application_number}";

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('admin.boatr.show', $registration->id)
                           ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error completing BoatR inspection', [
                'registration_id' => $id,
                'error' => $e->getMessage()
            ]);

            $errorMessage = 'Error completing inspection: ' . $e->getMessage();

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
     * Remove the specified BoatR registration from storage
     */
    public function destroy(Request $request, $id)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);
            $applicationNumber = $registration->application_number;

            // Delete associated document if exists
            if ($registration->supporting_document_path && Storage::disk('public')->exists($registration->supporting_document_path)) {
                Storage::disk('public')->delete($registration->supporting_document_path);
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
                'error' => $e->getMessage()
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
     * Download supporting document
     */
    public function downloadDocument($id)
    {
        try {
            $registration = BoatrApplication::findOrFail($id);

            if (!$registration->supporting_document_path || !Storage::disk('public')->exists($registration->supporting_document_path)) {
                return redirect()->back()->with('error', 'Document not found');
            }

            $fileName = "BoatR_{$registration->application_number}_document." . 
                       pathinfo($registration->supporting_document_path, PATHINFO_EXTENSION);

            return Storage::disk('public')->download($registration->supporting_document_path, $fileName);

        } catch (\Exception $e) {
            Log::error('Error downloading BoatR document', [
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
            $query = BoatrApplication::query();

            // Apply same filters as index
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('boat_type')) {
                $query->where('boat_type', $request->boat_type);
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
                    'Inspection Completed',
                    'Date Applied',
                    'Date Reviewed'
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
                        $registration->inspection_completed ? 'Yes' : 'No',
                        $registration->created_at->format('M d, Y h:i A'),
                        $registration->reviewed_at ? 
                            $registration->reviewed_at->format('M d, Y h:i A') : 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting BoatR registrations', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }
}