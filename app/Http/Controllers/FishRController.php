<?php

namespace App\Http\Controllers;

use App\Models\FishrApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
                'user_id' => auth()->id(),
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
                                  ->paginate(15)
                                  ->appends($request->query());

            // Calculate statistics
            $totalRegistrations = FishrApplication::count();
            $underReviewCount = FishrApplication::where('status', 'under_review')->count();
            $approvedCount = FishrApplication::where('status', 'approved')->count();
            $rejectedCount = FishrApplication::where('status', 'rejected')->count();

            Log::info('FishR data loaded successfully', [
                'total_registrations' => $totalRegistrations,
                'paginated_count' => $registrations->count(),
                'current_page' => $registrations->currentPage(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $registrations,
                    'view' => view('admin.fishr_requests.partials.table', compact('registrations'))->render()
                ]);
            }

            // FIXED: Changed view path to match your actual file location
            return view('admin.fishr_requests.index', compact(
                'registrations',
                'totalRegistrations',
                'underReviewCount',
                'approvedCount',
                'rejectedCount'
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

            // If it's an AJAX request, return JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'id' => $registration->id,
                    'registration_number' => $registration->registration_number,
                    'full_name' => $registration->full_name,
                    'first_name' => $registration->first_name,
                    'middle_name' => $registration->middle_name,
                    'last_name' => $registration->last_name,
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
                    'document_path' => $registration->document_path,
                    'created_at' => $registration->created_at->format('M d, Y h:i A'),
                    'updated_at' => $registration->updated_at->format('M d, Y h:i A'),
                    'status_updated_at' => $registration->status_updated_at ? 
                        $registration->status_updated_at->format('M d, Y h:i A') : null,
                    'updated_by_name' => $registration->updatedBy?->name ?? null,
                ]);
            }

            // FIXED: Changed view path for show page
            return view('admin.fishr_requests.show', compact('registration'));

        } catch (\Exception $e) {
            Log::error('Error fetching FishR registration', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration not found'
                ], 404);
            }

            return redirect()->route('admin.fishr.requests')
                           ->with('error', 'Registration not found');
        }
    }

    /**
     * Update the status of the specified FishR registration
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:under_review,approved,rejected',
                'remarks' => 'nullable|string|max:1000',
            ], [
                'status.required' => 'Status is required',
                'status.in' => 'Invalid status selected',
            ]);

            $registration = FishrApplication::findOrFail($id);
            $oldStatus = $registration->status;

            // Update the registration
            $registration->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'],
                'status_updated_at' => now(),
                'updated_by' => auth()->id()
            ]);

            Log::info('FishR registration status updated', [
                'registration_id' => $registration->id,
                'registration_number' => $registration->registration_number,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'updated_by' => auth()->user()->name ?? 'System',
                'remarks' => $validated['remarks']
            ]);

            $message = "Registration {$registration->registration_number} status updated to " . 
                      ucfirst(str_replace('_', ' ', $validated['status']));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'status' => $registration->status,
                        'formatted_status' => $registration->formatted_status,
                        'status_color' => $registration->status_color,
                        'status_updated_at' => $registration->status_updated_at->format('M d, Y h:i A')
                    ]
                ]);
            }

            return redirect()->route('admin.fishr.show', $registration->id)
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
            Log::error('Error updating FishR registration status', [
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
     * Remove the specified FishR registration from storage
     */
    public function destroy(Request $request, $id)
    {
        try {
            $registration = FishrApplication::findOrFail($id);
            $registrationNumber = $registration->registration_number;

            // Delete associated document if exists
            if ($registration->document_path && Storage::disk('public')->exists($registration->document_path)) {
                Storage::disk('public')->delete($registration->document_path);
            }

            // Delete the registration
            $registration->delete();

            Log::info('FishR registration deleted', [
                'registration_id' => $id,
                'registration_number' => $registrationNumber,
                'deleted_by' => auth()->user()->name ?? 'System'
            ]);

            $message = "Registration {$registrationNumber} has been deleted successfully";

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('admin.fishr.requests')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error deleting FishR registration', [
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
}