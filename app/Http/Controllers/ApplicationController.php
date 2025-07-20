<?php

namespace App\Http\Controllers;

use App\Models\FishrApplication;
use App\Models\SeedlingRequest;
use App\Models\BoatrApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    /**
     * Submit FishR (Fisherfolk Registration) request
     */
    public function submitFishR(Request $request)
    {
        try {
            // Enhanced validation with better error messages
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'sex' => 'required|in:Male,Female,Preferred not to say',
                'barangay' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:20',
                'main_livelihood' => 'required|in:capture,aquaculture,vending,processing,others',
                'other_livelihood' => 'nullable|string|max:255|required_if:main_livelihood,others',
                'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'sex.required' => 'Please select your sex',
                'barangay.required' => 'Please select your barangay',
                'mobile_number.required' => 'Mobile number is required',
                'main_livelihood.required' => 'Please select your main livelihood',
                'other_livelihood.required_if' => 'Please specify your livelihood when selecting "Others"',
                'supporting_documents.mimes' => 'Supporting documents must be PDF, JPG, JPEG, or PNG',
                'supporting_documents.max' => 'Supporting documents must not exceed 10MB'
            ]);

            // Handle file upload with better error handling
            $documentPath = null;
            if ($request->hasFile('supporting_documents')) {
                $file = $request->file('supporting_documents');
                if ($file->isValid()) {
                    $documentPath = $file->store('fishr_documents', 'public');
                    Log::info('FishR document uploaded', ['path' => $documentPath]);
                } else {
                    throw new \Exception('Invalid file upload');
                }
            }

            // Determine livelihood description
            $livelihoodDescription = $this->getLivelihoodDescription(
                $validated['main_livelihood'], 
                $validated['other_livelihood'] ?? null
            );

            // Generate unique registration number
            $registrationNumber = $this->generateUniqueRegistrationNumber();

            // Create the FishR registration - Only include fields that exist in your migration
            $fishRRegistration = FishrApplication::create([
                'registration_number' => $registrationNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'sex' => $validated['sex'],
                'barangay' => $validated['barangay'],
                'contact_number' => $validated['mobile_number'],
                'main_livelihood' => $validated['main_livelihood'],
                'livelihood_description' => $livelihoodDescription,
                'other_livelihood' => $validated['other_livelihood'] ?? null,
                'document_path' => $documentPath,
                'status' => 'under_review'
                // Note: remarks, status_updated_at, and updated_by will be set when admin updates the status
            ]);

            Log::info('FishR registration created successfully', [
                'id' => $fishRRegistration->id,
                'registration_number' => $fishRRegistration->registration_number,
                'name' => $fishRRegistration->full_name,
                'livelihood' => $livelihoodDescription
            ]);

            $successMessage = 'Your FishR registration has been submitted successfully! Registration Number: ' . 
                            $fishRRegistration->registration_number . 
                            '. You will receive an SMS notification once your registration is processed.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'registration_number' => $fishRRegistration->registration_number,
                    'data' => [
                        'id' => $fishRRegistration->id,
                        'name' => $fishRRegistration->full_name,
                        'status' => $fishRRegistration->status
                    ]
                ]);
            }

            return redirect()->route('landing.page')->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('FishR registration validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['supporting_documents'])
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please check your input and try again.');

        } catch (\Exception $e) {
            Log::error('FishR registration error: ' . $e->getMessage(), [
                'request_data' => $request->except(['supporting_documents']),
                'file_info' => $request->hasFile('supporting_documents') ? [
                    'original_name' => $request->file('supporting_documents')->getClientOriginalName(),
                    'size' => $request->file('supporting_documents')->getSize(),
                    'mime' => $request->file('supporting_documents')->getMimeType()
                ] : null,
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'There was an error submitting your registration. Please try again.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Submit a new seedling request
     */
    public function submitSeedlings(Request $request)
    {
        try {
            // Enhanced validation with better error messages
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'mobile' => 'required|string|max:20',
                'barangay' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'selected_seedlings' => 'required|string',
                'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'mobile.required' => 'Mobile number is required',
                'barangay.required' => 'Please select your barangay',
                'address.required' => 'Address is required',
                'selected_seedlings.required' => 'Please select at least one seedling type',
                'supporting_documents.mimes' => 'Supporting documents must be PDF, JPG, JPEG, or PNG',
                'supporting_documents.max' => 'Supporting documents must not exceed 10MB'
            ]);

            // Parse selected seedlings with validation
            $selectedSeedlings = json_decode($validated['selected_seedlings'], true);
            if (!$selectedSeedlings || !is_array($selectedSeedlings)) {
                throw new \Exception('Invalid seedlings selection data');
            }

            // Handle file upload with better error handling
            $documentPath = null;
            if ($request->hasFile('supporting_documents')) {
                $file = $request->file('supporting_documents');
                if ($file->isValid()) {
                    $documentPath = $file->store('seedling_documents', 'public');
                    Log::info('Seedling document uploaded', ['path' => $documentPath]);
                } else {
                    throw new \Exception('Invalid file upload');
                }
            }

            // Generate unique request number
            $requestNumber = $this->generateUniqueRequestNumber();

            // Create the seedling request
            $seedlingRequest = SeedlingRequest::create([
                'request_number' => $requestNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'contact_number' => $validated['mobile'],
                'address' => $validated['address'],
                'barangay' => $validated['barangay'],
                'seedling_type' => $this->formatSeedlingTypes($selectedSeedlings),
                'vegetables' => $selectedSeedlings['vegetables'] ?? [],
                'fruits' => $selectedSeedlings['fruits'] ?? [],
                'fertilizers' => $selectedSeedlings['fertilizers'] ?? [],
                'requested_quantity' => $selectedSeedlings['totalQuantity'] ?? 0,
                'total_quantity' => $selectedSeedlings['totalQuantity'] ?? 0,
                'document_path' => $documentPath,
                'status' => 'under_review'
            ]);

            Log::info('Seedling request created successfully', [
                'id' => $seedlingRequest->id,
                'request_number' => $seedlingRequest->request_number,
                'name' => $seedlingRequest->full_name,
                'total_quantity' => $seedlingRequest->total_quantity
            ]);

            $successMessage = 'Your seedling request has been submitted successfully! Request Number: ' . 
                            $seedlingRequest->request_number . 
                            '. You will receive an SMS notification once your request is processed.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'request_number' => $seedlingRequest->request_number,
                    'data' => [
                        'id' => $seedlingRequest->id,
                        'name' => $seedlingRequest->full_name,
                        'status' => $seedlingRequest->status
                    ]
                ]);
            }

            return redirect()->route('landing.page')->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Seedling request validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['supporting_documents'])
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please check your input and try again.');

        } catch (\Exception $e) {
            Log::error('Seedling request error: ' . $e->getMessage(), [
                'request_data' => $request->except(['supporting_documents']),
                'file_info' => $request->hasFile('supporting_documents') ? [
                    'original_name' => $request->file('supporting_documents')->getClientOriginalName(),
                    'size' => $request->file('supporting_documents')->getSize(),
                    'mime' => $request->file('supporting_documents')->getMimeType()
                ] : null,
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'There was an error submitting your request. Please try again.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Submit RSBSA request (placeholder for future implementation)
     */
    public function submitRsbsa(Request $request)
    {
        try {
            // TODO: Implement RSBSA submission logic
            Log::info('RSBSA request submitted (placeholder)', [
                'request_data' => $request->except(['supporting_documents'])
            ]);

            $successMessage = 'RSBSA request submitted successfully! You will receive further instructions via SMS.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }

            return redirect()->route('landing.page')->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::error('RSBSA request error: ' . $e->getMessage(), [
                'request_data' => $request->except(['supporting_documents'])
            ]);

            $errorMessage = 'There was an error submitting your RSBSA request. Please try again.';

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
     * Submit Boat Registration request
     */
    public function submitBoatR(Request $request)
    {
        try {
            // Enhanced validation with better error messages
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'fishr_number' => 'required|string|max:255',
                'vessel_name' => 'required|string|max:255',
                'boat_type' => 'required|in:Spoon,Plumb,Banca,Rake Stem - Rake Stern,Rake Stem - Transom/Spoon/Plumb Stern,Skiff (Typical Design)',
                'boat_length' => 'required|numeric|min:1|max:200',
                'boat_width' => 'required|numeric|min:1|max:50',
                'boat_depth' => 'required|numeric|min:1|max:30',
                'engine_type' => 'required|string|max:255',
                'engine_horsepower' => 'required|integer|min:1|max:500',
                'primary_fishing_gear' => 'required|in:Hook and Line,Bottom Set Gill Net,Fish Trap,Fish Coral'
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'fishr_number.required' => 'FishR registration number is required',
                'vessel_name.required' => 'Vessel name is required',
                'boat_type.required' => 'Please select a boat type',
                'boat_type.in' => 'Invalid boat type selected',
                'boat_length.required' => 'Boat length is required',
                'boat_length.numeric' => 'Boat length must be a number',
                'boat_width.required' => 'Boat width is required',
                'boat_width.numeric' => 'Boat width must be a number',
                'boat_depth.required' => 'Boat depth is required',
                'boat_depth.numeric' => 'Boat depth must be a number',
                'engine_type.required' => 'Engine type is required',
                'engine_horsepower.required' => 'Engine horsepower is required',
                'engine_horsepower.integer' => 'Engine horsepower must be a whole number',
                'primary_fishing_gear.required' => 'Please select primary fishing gear',
                'primary_fishing_gear.in' => 'Invalid fishing gear selected'
            ]);

            // Generate unique application number
            $applicationNumber = $this->generateUniqueApplicationNumber();

            // Create the BoatR registration
            $boatRRegistration = BoatrApplication::create([
                'application_number' => $applicationNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'fishr_number' => $validated['fishr_number'],
                'vessel_name' => $validated['vessel_name'],
                'boat_type' => $validated['boat_type'],
                'boat_length' => $validated['boat_length'],
                'boat_width' => $validated['boat_width'],
                'boat_depth' => $validated['boat_depth'],
                'engine_type' => $validated['engine_type'],
                'engine_horsepower' => $validated['engine_horsepower'],
                'primary_fishing_gear' => $validated['primary_fishing_gear'],
                'status' => 'pending',
                'inspection_completed' => false
            ]);

            Log::info('BoatR registration created successfully', [
                'id' => $boatRRegistration->id,
                'application_number' => $boatRRegistration->application_number,
                'name' => $boatRRegistration->full_name,
                'vessel_name' => $boatRRegistration->vessel_name,
                'fishr_number' => $validated['fishr_number']
            ]);

            $successMessage = 'Your BoatR registration has been submitted successfully! Application Number: ' . 
                            $boatRRegistration->application_number . 
                            '. An on-site inspection will be scheduled. You will receive an SMS notification with further instructions.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'application_number' => $boatRRegistration->application_number,
                    'data' => [
                        'id' => $boatRRegistration->id,
                        'name' => $boatRRegistration->full_name,
                        'vessel_name' => $boatRRegistration->vessel_name,
                        'status' => $boatRRegistration->status
                    ]
                ]);
            }

            return redirect()->route('landing.page')->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('BoatR registration validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please check your input and try again.');

        } catch (\Exception $e) {
            Log::error('BoatR registration error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'There was an error submitting your boat registration. Please try again.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    // ========================================
    // ADDITIONAL HELPER METHODS FOR BOATR
    // ========================================

    /**
     * Generate unique application number for BoatR applications
     */
    private function generateUniqueApplicationNumber(): string
    {
        do {
            $applicationNumber = 'BOATR-' . strtoupper(Str::random(8));
        } while (BoatrApplication::where('application_number', $applicationNumber)->exists());

        return $applicationNumber;
    }

    // ========================================
    // PRIVATE HELPER METHODS
    // ========================================

    /**
     * Generate unique registration number for FishR applications
     */
    private function generateUniqueRegistrationNumber(): string
    {
        do {
            $registrationNumber = 'FISHR-' . strtoupper(Str::random(8));
        } while (FishrApplication::where('registration_number', $registrationNumber)->exists());

        return $registrationNumber;
    }

    /**
     * Generate unique request number for Seedling requests
     */
    private function generateUniqueRequestNumber(): string
    {
        do {
            $requestNumber = 'SEED-' . strtoupper(Str::random(8));
        } while (SeedlingRequest::where('request_number', $requestNumber)->exists());

        return $requestNumber;
    }

    /**
     * Get livelihood description based on the selected option
     */
    private function getLivelihoodDescription($mainLivelihood, $otherLivelihood = null): string
    {
        $descriptions = [
            'capture' => 'Capture Fishing',
            'aquaculture' => 'Aquaculture',
            'vending' => 'Fish Vending',
            'processing' => 'Fish Processing',
            'others' => $otherLivelihood ?? 'Others'
        ];

        return $descriptions[$mainLivelihood] ?? 'Unknown';
    }

    /**
     * Format seedling types for display
     */
    private function formatSeedlingTypes($selectedSeedlings): string
    {
        $types = [];

        if (!empty($selectedSeedlings['vegetables'])) {
            $vegNames = collect($selectedSeedlings['vegetables'])->pluck('name')->toArray();
            $types[] = 'Vegetables: ' . implode(', ', $vegNames);
        }

        if (!empty($selectedSeedlings['fruits'])) {
            $fruitNames = collect($selectedSeedlings['fruits'])->pluck('name')->toArray();
            $types[] = 'Fruits: ' . implode(', ', $fruitNames);
        }

        if (!empty($selectedSeedlings['fertilizers'])) {
            $fertNames = collect($selectedSeedlings['fertilizers'])->pluck('name')->toArray();
            $types[] = 'Fertilizers: ' . implode(', ', $fertNames);
        }

        return implode(' | ', $types);
    }
}