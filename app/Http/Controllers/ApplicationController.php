<?php

namespace App\Http\Controllers;

use App\Models\FishrApplication;
use App\Models\SeedlingRequest;
use App\Models\BoatrApplication;
use App\Models\RsbsaApplication; 
use App\Models\TrainingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
                'email' => 'required|email|max:255',
                'main_livelihood' => 'required|in:capture,aquaculture,vending,processing,others',
                'other_livelihood' => 'nullable|string|max:255|required_if:main_livelihood,others',
                'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'sex.required' => 'Please select your sex',
                'barangay.required' => 'Please select your barangay',
                'mobile_number.required' => 'Mobile number is required',
                'email.required' => 'Email address is required',
                'email.email' => 'Please enter a valid email address',
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

            // Create the FishR registration
            $fishRRegistration = FishrApplication::create([
                'registration_number' => $registrationNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'sex' => $validated['sex'],
                'barangay' => $validated['barangay'],
                'contact_number' => $validated['mobile_number'],
                'email' => $validated['email'],
                'main_livelihood' => $validated['main_livelihood'],
                'livelihood_description' => $livelihoodDescription,
                'other_livelihood' => $validated['other_livelihood'] ?? null,
                'document_path' => $documentPath,
                'status' => 'under_review'
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
                'email' => 'required|email|max:255',
                'barangay' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'selected_seedlings' => 'required|string',
                'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'mobile.required' => 'Mobile number is required',
                'email.required' => 'Email address is required',
                'email.email' => 'Please enter a valid email address',
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
                'email' => $validated['email'],
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
 * Submit RSBSA request - STREAMLINED VERSION
 */
public function submitRsbsa(Request $request)
{
    try {
        Log::info('RSBSA submission started in ApplicationController', [
            'request_method' => $request->method(),
            'has_csrf' => $request->has('_token'),
            'content_type' => $request->header('Content-Type'),
            'form_data' => $request->except(['supporting_docs', '_token'])
        ]);

        // Streamlined validation matching the simplified form
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'required|in:Male,Female,Preferred not to say',
            'barangay' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'main_livelihood' => 'required|in:Farmer,Farmworker/Laborer,Fisherfolk,Agri-youth',
            'land_area' => 'nullable|numeric|min:0|max:1000',
            'farm_location' => 'nullable|string|max:500',
            'commodity' => 'nullable|string|max:1000',
            'supporting_docs' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ], [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'sex.required' => 'Please select your sex',
            'sex.in' => 'Invalid sex selection',
            'barangay.required' => 'Please select your barangay',
            'mobile.required' => 'Mobile number is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'main_livelihood.required' => 'Please select your main livelihood',
            'main_livelihood.in' => 'Invalid livelihood selected',
            'land_area.numeric' => 'Land area must be a number',
            'land_area.min' => 'Land area cannot be negative',
            'land_area.max' => 'Land area cannot exceed 1000 hectares',
            'supporting_docs.mimes' => 'Supporting documents must be PDF, JPG, JPEG, or PNG',
            'supporting_docs.max' => 'Supporting documents must not exceed 5MB'
        ]);

        Log::info('RSBSA validation passed', ['validated_data' => $validated]);

        // Handle file upload
        $documentPath = null;
        if ($request->hasFile('supporting_docs')) {
            $file = $request->file('supporting_docs');
            if ($file->isValid()) {
                try {
                    // Create directory if it doesn't exist
                    Storage::disk('public')->makeDirectory('rsbsa_documents');
                    
                    // Generate unique filename
                    $fileName = 'rsbsa_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $documentPath = $file->storeAs('rsbsa_documents', $fileName, 'public');
                    
                    Log::info('RSBSA document uploaded', [
                        'path' => $documentPath,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize()
                    ]);
                } catch (\Exception $e) {
                    Log::error('File upload error', ['error' => $e->getMessage()]);
                    throw new \Exception('File upload failed: ' . $e->getMessage());
                }
            } else {
                throw new \Exception('Invalid file upload');
            }
        }

        // Generate unique application number for RSBSA
        $applicationNumber = $this->generateUniqueRsbsaApplicationNumber();
        Log::info('Generated RSBSA application number: ' . $applicationNumber);

        // Prepare data for database insertion (streamlined)
        $applicationData = [
            'application_number' => $applicationNumber,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?: null,
            'last_name' => $validated['last_name'],
            'sex' => $validated['sex'],
            'mobile_number' => $validated['mobile'],
            'email' => $validated['email'],
            'barangay' => $validated['barangay'],
            'main_livelihood' => $validated['main_livelihood'],
            'land_area' => $validated['land_area'],
            'farm_location' => $validated['farm_location'], 
            'commodity' => $validated['commodity'], 
            'supporting_document_path' => $documentPath,
            'status' => 'pending'
        ];
        
        Log::info('Attempting to create RSBSA application', ['data' => $applicationData]);

        // Create the RSBSA application
        $rsbsaApplication = \App\Models\RsbsaApplication::create($applicationData);

        Log::info('RSBSA registration created successfully', [
            'id' => $rsbsaApplication->id,
            'application_number' => $rsbsaApplication->application_number,
            'name' => $rsbsaApplication->full_name,
            'livelihood' => $rsbsaApplication->main_livelihood
        ]);

        $successMessage = 'Your RSBSA application has been submitted successfully! Application Number: ' . 
                        $rsbsaApplication->application_number . 
                        '. You will receive an SMS notification once your application is processed.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'application_number' => $rsbsaApplication->application_number,
                'data' => [
                    'id' => $rsbsaApplication->id,
                    'name' => $rsbsaApplication->full_name,
                    'status' => $rsbsaApplication->status
                ]
            ]);
        }

        return redirect()->route('landing.page')->with('success', $successMessage);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('RSBSA application validation failed', [
            'errors' => $e->errors(),
            'request_data' => $request->except(['supporting_docs', '_token'])
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
        Log::error('RSBSA application error: ' . $e->getMessage(), [
            'request_data' => $request->except(['supporting_docs', '_token']),
            'file_info' => $request->hasFile('supporting_docs') ? [
                'original_name' => $request->file('supporting_docs')->getClientOriginalName(),
                'size' => $request->file('supporting_docs')->getSize(),
                'mime' => $request->file('supporting_docs')->getMimeType()
            ] : null,
            'trace' => $e->getTraceAsString()
        ]);

        $errorMessage = 'There was an error submitting your application. Please try again.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'debug_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }

        return redirect()->back()
            ->with('error', $errorMessage)
            ->withInput();
    }
}
    /**
     * Submit Boat Registration request - COMPLETE WORKING VERSION
     */
    public function submitBoatR(Request $request)
    {
        try {
            Log::info('BoatR submission started', [
                'request_method' => $request->method(),
                'has_csrf' => $request->has('_token'),
                'content_type' => $request->header('Content-Type')
            ]);

            // Enhanced validation
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'mobile' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'fishr_number' => 'required|string|max:255',
                'vessel_name' => 'required|string|max:255',
                'boat_type' => 'required|in:Spoon,Plumb,Banca,Rake Stem - Rake Stern,Rake Stem - Transom/Spoon/Plumb Stern,Skiff (Typical Design)',
                'boat_length' => 'required|numeric|min:1|max:200',
                'boat_width' => 'required|numeric|min:1|max:50',
                'boat_depth' => 'required|numeric|min:1|max:30',
                'engine_type' => 'required|string|max:255',
                'engine_horsepower' => 'required|integer|min:1|max:500',
                'primary_fishing_gear' => 'required|in:Hook and Line,Bottom Set Gill Net,Fish Trap,Fish Coral',
                'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'mobile.required' => 'Mobile number is required',
                'email.required' => 'Email address is required',
                'email.email' => 'Please enter a valid email address',
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
                'primary_fishing_gear.in' => 'Invalid fishing gear selected',
                'supporting_documents.mimes' => 'Document must be PDF, JPG, JPEG, or PNG',
                'supporting_documents.max' => 'Document must not exceed 10MB'
            ]);

            Log::info('BoatR validation passed');

            // Optional: Validate FishR number (skip for testing)
            // $fishRExists = FishrApplication::where('registration_number', $validated['fishr_number'])
            //     ->where('status', 'approved')
            //     ->exists();

            // if (!$fishRExists) {
            //     if ($request->ajax() || $request->wantsJson()) {
            //         return response()->json([
            //             'success' => false,
            //             'message' => 'Invalid FishR registration number.',
            //             'errors' => ['fishr_number' => ['Invalid or non-approved FishR number']]
            //         ], 422);
            //     }
            //     return redirect()->back()->withErrors(['fishr_number' => 'Invalid FishR number'])->withInput();
            // }

            // Generate unique application number
            $applicationNumber = $this->generateUniqueApplicationNumber();
            Log::info('Generated application number: ' . $applicationNumber);

            // Create the BoatR registration
            $boatRRegistration = BoatrApplication::create([
                'application_number' => $applicationNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'mobile' => $validated['mobile'],
                'email' => $validated['email'],
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
                'inspection_completed' => false,
            ]);

            Log::info('BoatR registration created with ID: ' . $boatRRegistration->id);

            // Handle single file upload if provided
            $documentUploaded = false;
            if ($request->hasFile('supporting_documents')) {
                $file = $request->file('supporting_documents');
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = $applicationNumber . '_user_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    // Ensure directory exists
                    Storage::disk('public')->makeDirectory('boatr_documents/user_uploads');
                    
                    $documentPath = $file->storeAs('boatr_documents/user_uploads', $fileName, 'public');
                    
                    // Update the registration with document info
                    $boatRRegistration->update([
                        'user_document_path' => $documentPath,
                        'user_document_name' => $originalName,
                        'user_document_type' => $file->getClientOriginalExtension(),
                        'user_document_size' => $file->getSize(),
                        'user_document_uploaded_at' => now()
                    ]);
                    
                    $documentUploaded = true;
                    Log::info('Document uploaded successfully', ['path' => $documentPath]);
                }
            }

            Log::info('BoatR registration completed successfully', [
                'id' => $boatRRegistration->id,
                'application_number' => $boatRRegistration->application_number,
                'has_document' => $documentUploaded
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
                        'status' => $boatRRegistration->status,
                        'has_document' => $documentUploaded
                    ]
                ]);
            }

            return redirect()->route('landing.page')->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('BoatR validation failed', ['errors' => $e->errors()]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->validator)->withInput();

        } catch (\Exception $e) {
            Log::error('BoatR registration error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'There was an error submitting your boat registration. Please try again.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Submit training application
     */
    public function submitTraining(Request $request)
    {
        try {
            Log::info('Training submission started', [
                'request_method' => $request->method(),
                'has_csrf' => $request->has('_token'),
                'content_type' => $request->header('Content-Type')
            ]);

            // Enhanced validation with better error messages
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'mobile_number' => 'required|string|size:11',
                'email' => 'required|email|max:255',
                'training_type' => 'required|string',
                'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
            ], [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'mobile_number.required' => 'Mobile number is required',
                'mobile_number.size' => 'Mobile number must be 11 digits',
                'email.required' => 'Email address is required',
                'email.email' => 'Please enter a valid email address',
                'training_type.required' => 'Please select a training program',
                'documents.*.mimes' => 'Documents must be PDF, JPG, JPEG, or PNG files',
                'documents.*.max' => 'Documents must not exceed 5MB'
            ]);

            // Generate unique application number
            $applicationNumber = 'TRAIN-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

            // Handle document uploads with better error handling
            $documentPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    if ($document->isValid()) {
                        $path = $document->store('training-documents', 'public');
                        $documentPaths[] = $path;
                        Log::info('Training document uploaded', ['path' => $path]);
                    }
                }
            }

            // Create training application with logging
            $training = TrainingApplication::create([
                'application_number' => $applicationNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'mobile_number' => $validated['mobile_number'],
                'email' => $validated['email'],
                'training_type' => $validated['training_type'],
                'document_paths' => $documentPaths,
                'status' => 'under_review'
            ]);

            Log::info('Training application created successfully', [
                'id' => $training->id,
                'application_number' => $training->application_number,
                'name' => $training->full_name
            ]);

            $successMessage = 'Your training application has been submitted successfully! ' . 
                             'Application Number: ' . $training->application_number . 
                             '. You will receive an SMS notification once your application is processed.';

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'application_number' => $training->application_number,
                'data' => [
                    'id' => $training->id,
                    'name' => $training->full_name,
                    'status' => $training->status
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Training application validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['documents'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Training application error: ' . $e->getMessage(), [
                'request_data' => $request->except(['documents']),
                'file_info' => $request->hasFile('documents') ? [
                    'count' => count($request->file('documents')),
                    'types' => collect($request->file('documents'))->map->getMimeType()
                ] : null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your application. Please try again.',
                'debug_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ========================================
    // PRIVATE HELPER METHODS
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

    /**
     * Generate unique application number for RSBSA applications
     */
    private function generateUniqueRsbsaApplicationNumber(): string
    {
        do {
            $applicationNumber = 'RSBSA-' . strtoupper(Str::random(8));
        } while (RsbsaApplication::where('application_number', $applicationNumber)->exists());

        return $applicationNumber;
    }

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