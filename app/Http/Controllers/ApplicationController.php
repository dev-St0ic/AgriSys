<?php

namespace App\Http\Controllers;

use App\Models\FishrApplication;
use App\Models\SeedlingRequest;
use App\Models\SeedlingRequestItem;
use App\Models\CategoryItem;
use App\Models\BoatrApplication;
use App\Models\RsbsaApplication;
use App\Models\TrainingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Validate Philippine mobile number format
     * Only accepts 09XXXXXXXXX format
     */
    private function normalizeMobileNumber($mobileNumber)
    {
        if (!$mobileNumber) {
            return null;
        }

        // Remove any spaces or dashes
        $cleanNumber = preg_replace('/[\s\-]/', '', $mobileNumber);

        // Only accept 09XXXXXXXXX format
        if (preg_match('/^09\d{9}$/', $cleanNumber)) {
            return $cleanNumber;
        }

        // Return original if doesn't match pattern (will fail validation)
        return $mobileNumber;
    }

    /**
     * Submit FishR (Fisherfolk Registration) request
     */
    public function submitFishR(Request $request)
    {
        try {
            // ✅ AUTHENTICATION CHECK
            $userId = session('user.id');

            if (!$userId) {
                Log::warning('FishR submission attempted without authentication');

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You must be logged in to submit a FishR registration.',
                        'require_auth' => true
                    ], 401);
                }

                return redirect()->route('landing.page')
                    ->with('error', 'You must be logged in to submit a FishR registration.');
            }

            // Verify user exists
            $userExists = \App\Models\UserRegistration::find($userId);
            if (!$userExists) {
                Log::error('User ID from session does not exist in database', ['user_id' => $userId]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid user session. Please log in again.',
                        'require_auth' => true
                    ], 401);
                }

                return redirect()->route('landing.page')
                    ->with('error', 'Invalid user session. Please log in again.');
            }

            Log::info('FishR submission started', [
                'user_id' => $userId,
                'username' => $userExists->username
            ]);

            // ✅ FIXED: All validation rules in correct place
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'name_extension' => ['nullable', 'string', 'max:10', 'regex:/^[a-zA-Z.\s]+$/'],
                'sex' => 'required|in:Male,Female,Preferred not to say',
                'barangay' => 'required|string|max:255',
                'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
                'main_livelihood' => 'required|in:capture,aquaculture,vending,processing,others',
                'other_livelihood' => 'nullable|string|max:255|required_if:main_livelihood,others',
                'secondary_livelihood' => ['nullable', 'in:capture,aquaculture,vending,processing,others'],
                'other_secondary_livelihood' => ['nullable', 'string', 'max:255', 'required_if:secondary_livelihood,others'],
                'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ], [
                'first_name.required' => 'First name is required',
                'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
                'middle_name.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes',
                'last_name.required' => 'Last name is required',
                'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
                'name_extension.regex' => 'Name extension can only contain letters, periods, and spaces',
                'sex.required' => 'Please select your sex',
                'barangay.required' => 'Please select your barangay',
                'contact_number.required' => 'Contact number is required',
                'contact_number.regex' => 'Contact number must be in the format 09XXXXXXXXX',
                'main_livelihood.required' => 'Please select your main livelihood',
                'other_livelihood.required_if' => 'Please specify your livelihood when selecting "Others"',
                'secondary_livelihood.in' => 'Please select a valid secondary livelihood',
                'other_secondary_livelihood.required_if' => 'Please specify your secondary livelihood when selecting "Others"',
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

            // FIXED: Only get livelihood description if value exists
            $livelihoodDescription = $this->getLivelihoodDescription(
                $validated['main_livelihood'],
                $validated['other_livelihood'] ?? null
            );

            // FIXED: Only get secondary livelihood description if secondary_livelihood is selected
            $secondaryLivelihoodDescription = null;
            if (!empty($validated['secondary_livelihood'])) {
                $secondaryLivelihoodDescription = $this->getLivelihoodDescription(
                    $validated['secondary_livelihood'],
                    $validated['other_secondary_livelihood'] ?? null
                );
            }

            // Generate unique registration number
            $registrationNumber = $this->generateUniqueRegistrationNumber();

            // Normalize contact number to +639 format
            $normalizedContactNumber = $this->normalizeMobileNumber($validated['contact_number']);

            //  FIXED: Create with all fields properly set
            $fishRRegistration = FishrApplication::create([
                'user_id' => $userId,
                'registration_number' => $registrationNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'name_extension' => $validated['name_extension'] ?? null,
                'sex' => $validated['sex'],
                'barangay' => $validated['barangay'],
                'contact_number' => $normalizedContactNumber,
                'main_livelihood' => $validated['main_livelihood'],
                'livelihood_description' => $livelihoodDescription,
                'other_livelihood' => $validated['other_livelihood'] ?? null,
                'secondary_livelihood' => $validated['secondary_livelihood'] ?? null,
                'other_secondary_livelihood' => $validated['other_secondary_livelihood'] ?? null,
                'secondary_livelihood_description' => $secondaryLivelihoodDescription,
                'document_path' => $documentPath,
                'status' => 'pending'
            ]);

            Log::info('FishR registration created successfully', [
                'id' => $fishRRegistration->id,
                'registration_number' => $fishRRegistration->registration_number,
                'name' => $fishRRegistration->full_name,
                'main_livelihood' => $livelihoodDescription,
                'secondary_livelihood' => $secondaryLivelihoodDescription
            ]);

            // Log activity
            try {
                \Spatie\Activitylog\Facades\Activity::withProperties([
                    'registration_number' => $fishRRegistration->registration_number,
                    'full_name' => $fishRRegistration->full_name,
                    'main_livelihood' => $livelihoodDescription,
                    'secondary_livelihood' => $secondaryLivelihoodDescription,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])->log('submitted - FishrApplication (ID: ' . $fishRRegistration->id . ')');
            } catch (\Exception $e) {
                Log::error('Activity logging failed: ' . $e->getMessage());
            }

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

    // /**
    //  * Submit a new seedling request - Dynamic Categories Version
    //  */
    // public function submitSeedlings(Request $request)
    // {
    //     try {
    //         // Validation
    //         $validated = $request->validate([
    //             'first_name' => 'required|string|max:255',
    //             'middle_name' => 'nullable|string|max:255',
    //             'last_name' => 'required|string|max:255',
    //             'mobile' => 'required|string|max:20',
    //             'email' => 'required|email|max:255',
    //             'barangay' => 'required|string|max:255',
    //             'address' => 'required|string|max:500',
    //             'selected_seedlings' => 'required|string',
    //             'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
    //         ]);

    //         // Parse selected seedlings
    //         $selectedSeedlings = json_decode($validated['selected_seedlings'], true);
    //         if (!$selectedSeedlings || !is_array($selectedSeedlings)) {
    //             throw new \Exception('Invalid seedlings selection data');
    //         }

    //         // Handle file upload
    //         $documentPath = null;
    //         if ($request->hasFile('supporting_documents')) {
    //             $file = $request->file('supporting_documents');
    //             if ($file->isValid()) {
    //                 $documentPath = $file->store('seedling_documents', 'public');
    //                 \Log::info('Seedling document uploaded', ['path' => $documentPath]);
    //             }
    //         }

    //         // Generate unique request number
    //         $requestNumber = 'SEED-' . date('Ymd') . '-' . strtoupper(\Str::random(6));

    //         // Create the main seedling request
    //         $seedlingRequest = SeedlingRequest::create([
    //             'request_number' => $requestNumber,
    //             'first_name' => $validated['first_name'],
    //             'middle_name' => $validated['middle_name'],
    //             'last_name' => $validated['last_name'],
    //             'contact_number' => $validated['mobile'],
    //             'email' => $validated['email'],
    //             'address' => $validated['address'],
    //             'barangay' => $validated['barangay'],
    //             'total_quantity' => $selectedSeedlings['totalQuantity'] ?? 0,
    //             'document_path' => $documentPath,
    //             'status' => 'pending'
    //         ]);

    //         // Create individual request items from selections
    //         $selections = $selectedSeedlings['selections'] ?? [];

    //         foreach ($selections as $categoryName => $items) {
    //             foreach ($items as $item) {
    //                 // Find the category item in database
    //                 $categoryItem = CategoryItem::find($item['id']);

    //                 if ($categoryItem) {
    //                     SeedlingRequestItem::create([
    //                         'seedling_request_id' => $seedlingRequest->id,
    //                         'category_id' => $categoryItem->category_id,
    //                         'category_item_id' => $categoryItem->id,
    //                         'item_name' => $item['name'],
    //                         'requested_quantity' => $item['quantity'],
    //                         'status' => 'pending'
    //                     ]);
    //                 }
    //             }
    //         }

    //         \Log::info('Seedling request created successfully', [
    //             'id' => $seedlingRequest->id,
    //             'request_number' => $seedlingRequest->request_number,
    //             'name' => $seedlingRequest->full_name,
    //             'total_quantity' => $seedlingRequest->total_quantity
    //         ]);

    //         $successMessage = 'Your seedling request has been submitted successfully! Request Number: ' .
    //                         $seedlingRequest->request_number .
    //                         '. You will receive an SMS notification once your request is processed.';

    //         if ($request->ajax() || $request->wantsJson()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => $successMessage,
    //                 'request_number' => $seedlingRequest->request_number
    //             ]);
    //         }

    //         return redirect()->route('landing.page')->with('success', $successMessage);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         \Log::warning('Seedling request validation failed', [
    //             'errors' => $e->errors()
    //         ]);

    //         if ($request->ajax() || $request->wantsJson()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Please check your input and try again.',
    //                 'errors' => $e->errors()
    //             ], 422);
    //         }

    //         return redirect()->back()->withErrors($e->validator)->withInput();

    //     } catch (\Exception $e) {
    //         \Log::error('Seedling request error: ' . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         if ($request->ajax() || $request->wantsJson()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'There was an error submitting your request. Please try again.'
    //             ], 500);
    //         }

    //         return redirect()->back()->with('error', 'There was an error submitting your request.')->withInput();
    //     }
    // }

    /**
 * Submit a new seedling request - WITH USER AUTHENTICATION
 */
public function submitSeedlings(Request $request)
{
    try {
        // ✅ GET USER ID FROM SESSION FIRST
        $userId = session('user.id');

        // ✅ CHECK IF USER IS AUTHENTICATED
        if (!$userId) {
            Log::warning('Seedling submission attempted without authentication');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to submit a seedling request.',
                    'require_auth' => true
                ], 401);
            }

            return redirect()->route('landing.page')
                ->with('error', 'You must be logged in to submit a seedling request.');
        }

        // ✅ VERIFY USER EXISTS IN DATABASE
        $userExists = \App\Models\UserRegistration::find($userId);
        if (!$userExists) {
            Log::error('User ID from session does not exist in database', [
                'user_id' => $userId
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user session. Please log in again.',
                    'require_auth' => true
                ], 401);
            }

            return redirect()->route('landing.page')
                ->with('error', 'Invalid user session. Please log in again.');
        }

        Log::info('Seedling submission started', [
            'user_id' => $userId,
            'username' => $userExists->username,
            'request_data' => $request->except(['supporting_documents'])
        ]);

                // Validation
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'extension_name' => ['nullable', 'string', 'max:10', 'regex:/^[a-zA-Z.\s]+$/'],
            'mobile' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'barangay' => 'required|string|max:255',
            'selected_seedlings' => 'required|string',
            'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ], [
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
            'middle_name.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
            'extension_name.regex' => 'Name extension can only contain letters, periods, and spaces',
            'mobile.required' => 'Mobile number is required',
            'mobile.regex' => 'Mobile number must be in the format 09XXXXXXXXX',
        ]);

        // Parse selected seedlings
        $selectedSeedlings = json_decode($validated['selected_seedlings'], true);
        if (!$selectedSeedlings || !is_array($selectedSeedlings)) {
            throw new \Exception('Invalid seedlings selection data');
        }

        // Handle file upload
        $documentPath = null;
        if ($request->hasFile('supporting_documents')) {
            $file = $request->file('supporting_documents');
            if ($file->isValid()) {
                $documentPath = $file->store('seedling_documents', 'public');
                Log::info('Seedling document uploaded', ['path' => $documentPath]);
            }
        }

        // Generate unique request number
        $requestNumber = 'SEED-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Validate mobile number format
        $normalizedMobile = $this->normalizeMobileNumber($validated['mobile']);

        // ✅ CREATE THE SEEDLING REQUEST WITH USER_ID
        $seedlingRequest = SeedlingRequest::create([
            'user_id' => $userId, // ✅ CRITICAL: Associate with authenticated user
            'request_number' => $requestNumber,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'extension_name' => $validated['extension_name'] ?? null,
            'contact_number' => $normalizedMobile,
            'email' => null,
            'barangay' => $validated['barangay'],
            'total_quantity' => $selectedSeedlings['totalQuantity'] ?? 0,
            'document_path' => $documentPath,
            'status' => 'pending'
        ]);

        // Create individual request items from selections
        $selections = $selectedSeedlings['selections'] ?? [];

        foreach ($selections as $categoryName => $items) {
            foreach ($items as $item) {
                // Find the category item in database
                $categoryItem = CategoryItem::find($item['id']);

                if ($categoryItem) {
                    SeedlingRequestItem::create([
                        'seedling_request_id' => $seedlingRequest->id,
                        'user_id' => $userId,
                        'category_id' => $categoryItem->category_id,
                        'category_item_id' => $categoryItem->id,
                        'item_name' => $item['name'],
                        'requested_quantity' => $item['quantity'],
                        'status' => 'pending'
                    ]);
                }
            }
        }

        Log::info('Seedling request created successfully', [
            'id' => $seedlingRequest->id,
            'user_id' => $userId,
            'request_number' => $seedlingRequest->request_number,
            'name' => $seedlingRequest->full_name,
            'total_quantity' => $seedlingRequest->total_quantity
        ]);

        // Log activity
        try {
            \Spatie\Activitylog\Facades\Activity::withProperties([
                'request_number' => $seedlingRequest->request_number,
                'full_name' => $seedlingRequest->full_name,
                'total_quantity' => $seedlingRequest->total_quantity,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])->log('submitted - SeedlingRequest (ID: ' . $seedlingRequest->id . ')');
        } catch (\Exception $e) {
            Log::error('Activity logging failed: ' . $e->getMessage());
        }

        $successMessage = 'Your seedling request has been submitted successfully! Request Number: ' .
                        $seedlingRequest->request_number .
                        '. You will receive an SMS notification once your request is processed.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'request_number' => $seedlingRequest->request_number
            ]);
        }

        return redirect()->route('landing.page')->with('success', $successMessage);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Seedling request validation failed', [
            'errors' => $e->errors()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $e->errors()
            ], 422);
        }

        return redirect()->back()->withErrors($e->validator)->withInput();

    } catch (\Exception $e) {
        Log::error('Seedling request error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'There was an error submitting your request. Please try again.'
            ], 500);
        }

        return redirect()->back()->with('error', 'There was an error submitting your request.')->withInput();
    }
}
public function submitRsbsa(Request $request)
{
    try {
        // ✅ Authentication check
        $userId = session('user.id');

        if (!$userId) {
            Log::warning('RSBSA submission attempted without authentication');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to submit an RSBSA application.',
                    'require_auth' => true
                ], 401);
            }

            return redirect()->route('landing.page')
                ->with('error', 'You must be logged in to submit an RSBSA application.');
        }

        // Verify user exists
        $userExists = \App\Models\UserRegistration::find($userId);
        if (!$userExists) {
            Log::error('User ID from session does not exist in database', ['user_id' => $userId]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user session. Please log in again.',
                    'require_auth' => true
                ], 401);
            }

            return redirect()->route('landing.page')
                ->with('error', 'Invalid user session. Please log in again.');
        }

        Log::info('RSBSA submission started', [
            'user_id' => $userId,
            'username' => $userExists->username,
        ]);

        // ✅ COMPLETE VALIDATION WITH ALL FIELDS
        $validated = $request->validate([
            // Basic info
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]*$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'name_extension' => ['nullable', 'string', 'max:10', 'regex:/^[a-zA-Z.\s]*$/'],
            'sex' => 'required|in:Male,Female,Preferred not to say',
            
            // Contact & location
            'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'barangay' => 'required|string|max:255',
            'address' => 'required|string|max:500',  // ✅ NOW VALIDATED
            
            // Main livelihood
            'main_livelihood' => 'required|in:Farmer,Farmworker/Laborer,Fisherfolk,Agri-youth',
            
            // Farmer-specific
            'farmer_crops' => 'nullable|required_if:main_livelihood,Farmer|string|max:100',
            'farmer_other_crops' => 'nullable|string|max:100|regex:/^[a-zA-Z\s,\'-]*$/',
            'farmer_livestock' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s,()\'"-]*$/',
            'farmer_land_area' => 'nullable|numeric|min:0|max:1000',
            'farmer_type_of_farm' => 'nullable|required_if:main_livelihood,Farmer|in:Irrigated,Rainfed Upland,Rainfed Lowland',
            'farmer_land_ownership' => 'nullable|required_if:main_livelihood,Farmer|in:Owner,Tenant,Lessee',
            'farmer_special_status' => 'nullable|in:Ancestral Domain,Agrarian Reform Beneficiary,None',
            'farm_location' => 'nullable|required_if:main_livelihood,Farmer|string|max:500|regex:/^[a-zA-Z0-9\s,\'-]*$/',
            
            // Farmworker-specific
            'farmworker_type' => 'nullable|required_if:main_livelihood,Farmworker/Laborer|string|max:100',
            'farmworker_other_type' => 'nullable|string|max:100|regex:/^[a-zA-Z\s,\'-]*$/',
            
            // Fisherfolk-specific
            'fisherfolk_activity' => 'nullable|required_if:main_livelihood,Fisherfolk|string|max:100',
            'fisherfolk_other_activity' => 'nullable|string|max:100|regex:/^[a-zA-Z\s,\'-]*$/',
            
            // Agri-youth-specific
            'agriyouth_farming_household' => 'nullable|required_if:main_livelihood,Agri-youth|in:Yes,No',
            'agriyouth_training' => 'nullable|required_if:main_livelihood,Agri-youth|string|max:100',
            'agriyouth_participation' => 'nullable|required_if:main_livelihood,Agri-youth|in:Participated,Not Participated',
            
            // General
            'commodity' => 'nullable|string|max:1000',
            
            // Document
            'supporting_docs' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
            'middle_name.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
            'name_extension.regex' => 'Name extension can only contain letters, periods, and spaces',
            'contact_number.required' => 'Mobile number is required',
            'contact_number.regex' => 'Mobile number must be in the format 09XXXXXXXXX',
            'address.required' => 'Complete address is required',
        ]);

        // Handle file upload
        $documentPath = null;
        if ($request->hasFile('supporting_docs')) {
            $file = $request->file('supporting_docs');
            if ($file->isValid()) {
                Storage::disk('public')->makeDirectory('rsbsa_documents');
                $fileName = 'rsbsa_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $documentPath = $file->storeAs('rsbsa_documents', $fileName, 'public');
            }
        }

        // Generate application number
        $applicationNumber = $this->generateUniqueRsbsaApplicationNumber();

        // Normalize mobile number
        $normalizedMobile = $this->normalizeMobileNumber($validated['contact_number']);

        // ✅ CREATE APPLICATION WITH ALL CORRECT FIELD NAMES
        $applicationData = [
            'user_id' => $userId,
            'application_number' => $applicationNumber,
            
            // Basic info
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'name_extension' => $validated['name_extension'] ?? null,
            'sex' => $validated['sex'],
            
            // Contact & location
            'contact_number' => $normalizedMobile,
            'barangay' => $validated['barangay'],
            'address' => $validated['address'],  // ✅ NOW SAVED
            
            // Main livelihood
            'main_livelihood' => $validated['main_livelihood'],
            
            // Farmer-specific
            'farmer_crops' => $validated['farmer_crops'] ?? null,
            'farmer_other_crops' => $validated['farmer_other_crops'] ?? null,
            'farmer_livestock' => $validated['farmer_livestock'] ?? null,
            'farmer_land_area' => $validated['farmer_land_area'] ?? null,  // ✅ CORRECT FIELD NAME
            'farmer_type_of_farm' => $validated['farmer_type_of_farm'] ?? null,
            'farmer_land_ownership' => $validated['farmer_land_ownership'] ?? null,
            'farmer_special_status' => $validated['farmer_special_status'] ?? null,
            'farm_location' => $validated['farm_location'] ?? null,
            
            // Farmworker-specific
            'farmworker_type' => $validated['farmworker_type'] ?? null,
            'farmworker_other_type' => $validated['farmworker_other_type'] ?? null,
            
            // Fisherfolk-specific
            'fisherfolk_activity' => $validated['fisherfolk_activity'] ?? null,
            'fisherfolk_other_activity' => $validated['fisherfolk_other_activity'] ?? null,
            
            // Agri-youth-specific
            'agriyouth_farming_household' => $validated['agriyouth_farming_household'] ?? null,
            'agriyouth_training' => $validated['agriyouth_training'] ?? null,
            'agriyouth_participation' => $validated['agriyouth_participation'] ?? null,
            
            // General
            'commodity' => $validated['commodity'] ?? null,
            'supporting_document_path' => $documentPath,
            
            // Status
            'status' => 'pending'
        ];

        $rsbsaApplication = RsbsaApplication::create($applicationData);

        Log::info('RSBSA application created successfully', [
            'id' => $rsbsaApplication->id,
            'application_number' => $rsbsaApplication->application_number,
            'name' => $rsbsaApplication->full_name,
            'livelihood' => $rsbsaApplication->main_livelihood
        ]);

        // Log activity
        try {
            \Spatie\Activitylog\Facades\Activity::withProperties([
                'application_number' => $rsbsaApplication->application_number,
                'full_name' => $rsbsaApplication->full_name,
                'main_livelihood' => $rsbsaApplication->main_livelihood,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])->log('submitted - RsbsaApplication (ID: ' . $rsbsaApplication->id . ')');
        } catch (\Exception $e) {
            Log::error('Activity logging failed: ' . $e->getMessage());
        }

        $successMessage = 'Your RSBSA application has been submitted successfully! Application Number: ' .
                        $rsbsaApplication->application_number;

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
        Log::warning('RSBSA validation failed', [
            'errors' => $e->errors(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $e->errors()
            ], 422);
        }

        return redirect()->back()->withErrors($e->validator)->withInput();

    } catch (\Exception $e) {
        Log::error('RSBSA submission error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your application. Please try again.'
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'An error occurred. Please try again.')
            ->withInput();
    }
}
    /**
     * Submit Boat Registration request - COMPLETE WORKING VERSION
     */
public function submitBoatR(Request $request)
{   
    try {
        $userId = session('user.id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to submit a BoatR registration.',
                'require_auth' => true
            ], 401);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'name_extension' => ['nullable', 'string', 'max:10', 'in:Jr.,Sr.,II,III,IV,V'],
            'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'barangay' => 'required|string|max:255',
            'fishr_number' => 'required|string|max:255',
            'vessel_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-\']*$/'],
            'boat_type' => 'required|in:Spoon,Plumb,Banca,Rake Stem - Rake Stern,Rake Stem - Transom/Spoon/Plumb Stern,Skiff (Typical Design)',
            'boat_classification' => 'required|in:Motorized,Non-motorized',
            'boat_length' => 'required|numeric|min:1|max:200',
            'boat_width' => 'required|numeric|min:1|max:50',
            'boat_depth' => 'required|numeric|min:1|max:30',
            'engine_type' => 'required_if:boat_classification,Motorized|nullable|string|max:255',
            'engine_horsepower' => 'required_if:boat_classification,Motorized|nullable|integer|min:1|max:500',
            'primary_fishing_gear' => 'required|in:Hook and Line,Bottom Set Gill Net,Fish Trap,Fish Coral,Not Applicable',
            'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);

        $applicationNumber = $this->generateUniqueApplicationNumber();
        
        // Only set engine fields if motorized
        if ($validated['boat_classification'] === 'Non-motorized') {
            $validated['engine_type'] = null;
            $validated['engine_horsepower'] = null;
        }

  // ✅ VALIDATE FISHR - 1:1 RELATIONSHIP
try {
    $fishrRegistration = \App\Models\FishrApplication::where('registration_number', $validated['fishr_number'])
        ->where('status', 'approved')
        ->first();

    if (!$fishrRegistration) {
        return response()->json([
            'success' => false,
            'message' => 'FishR registration not found or not approved'
        ], 422);
    }

    // Check if this FishR is already used for BoatR (1:1 relationship)
    $boatrExists = BoatrApplication::where('fishr_number', $validated['fishr_number'])->first();
    if ($boatrExists) {
        Log::warning('FishR already used for BoatR', [
            'fishr_number' => $validated['fishr_number'],
            'existing_boatr_id' => $boatrExists->id
        ]);

        return response()->json([
            'success' => false,
            'message' => 'This FishR has already been registered for a boat registration. Each FishR can only be used once.'
        ], 422);
    }

    // Check name match
    if (
        strtoupper($validated['first_name']) !== strtoupper($fishrRegistration->first_name) ||
        strtoupper($validated['last_name']) !== strtoupper($fishrRegistration->last_name)
    ) {
        Log::warning('Name mismatch in BoatR submission', [
            'fishr_name' => $fishrRegistration->first_name . ' ' . $fishrRegistration->last_name,
            'boatr_name' => $validated['first_name'] . ' ' . $validated['last_name']
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Your names do not match your FishR registration'
        ], 422);
    }

} catch (\Exception $e) {
    Log::error('FishR validation error: ' . $e->getMessage());
    
    return response()->json([
        'success' => false,
        'message' => 'Error validating FishR registration'
    ], 500);
}

        $boatRRegistration = BoatrApplication::create([
            'user_id' => $userId,
            'application_number' => $applicationNumber,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'name_extension' => $validated['name_extension'],
            'contact_number' => $validated['contact_number'],
            'barangay' => $validated['barangay'],
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

        // Handle file upload
        if ($request->hasFile('supporting_documents')) {
            $file = $request->file('supporting_documents');
            if ($file->isValid()) {
                $fileName = $applicationNumber . '_user_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->makeDirectory('boatr_documents/user_uploads');
                $documentPath = $file->storeAs('boatr_documents/user_uploads', $fileName, 'public');

                $boatRRegistration->update([
                    'user_document_path' => $documentPath,
                    'user_document_name' => $file->getClientOriginalName(),
                    'user_document_type' => $file->getClientOriginalExtension(),
                    'user_document_size' => $file->getSize(),
                    'user_document_uploaded_at' => now()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Your BoatR registration has been submitted successfully! Application Number: ' . $boatRRegistration->application_number,
            'application_number' => $boatRRegistration->application_number,
            'data' => [
                'id' => $boatRRegistration->id,
                'name' => $boatRRegistration->full_name,
                'vessel_name' => $boatRRegistration->vessel_name,
                'status' => $boatRRegistration->status,
                'has_document' => !empty($boatRRegistration->user_document_path)
            ]
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('BoatR registration error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'There was an error submitting your boat registration. Please try again.'
        ], 500);
    }
}

    /**
     * Submit training application
     */
    public function submitTraining(Request $request)
    {
       try {
        // ✅ ADD THIS AUTHENTICATION CHECK
        $userId = session('user.id');

        if (!$userId) {
            Log::warning('Training submission attempted without authentication');

            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to submit a training application.',
                'require_auth' => true
            ], 401);
        }

        // Verify user exists
        $userExists = \App\Models\UserRegistration::find($userId);
        if (!$userExists) {
            Log::error('User ID does not exist', ['user_id' => $userId]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid user session. Please log in again.',
                'require_auth' => true
            ], 401);
        }

        Log::info('Training submission started', [
            'user_id' => $userId,
            'username' => $userExists->username,
            'request_method' => $request->method(),
            'has_csrf' => $request->has('_token'),
            'content_type' => $request->header('Content-Type')
        ]);
            // Enhanced validation with better error messages
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'name_extension' => ['nullable', 'string', 'max:10', 'regex:/^[a-zA-Z.\s]+$/'],
                'contact_number' => ['required', 'string', 'regex:/^09\d{9}$/'],
                'barangay' => 'required|string|max:255',
                'training_type' => 'required|string',
                'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
            ], [
                'first_name.required' => 'First name is required',
                'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
                'middle_name.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes',
                'last_name.required' => 'Last name is required',
                'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
                'name_extension.regex' => 'Name extension can only contain letters, periods, and spaces',
                'contact_number.required' => 'Contact number is required',
                'contact_number.regex' => 'Contact number must be in the format 09XXXXXXXXX',
                'email.email' => 'Please enter a valid email address',
                'barangay.required' => 'Barangay is required',
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

            // Normalize contact number to +639 format
            $normalizedContactNumber = $this->normalizeMobileNumber($validated['contact_number']);

            // Create training application with logging
            $training = TrainingApplication::create([
                'user_id' => $userId,
                'application_number' => $applicationNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'name_extension' => $validated['name_extension'] ?? null,
                'contact_number' => $normalizedContactNumber,
                'email' => null,
                'barangay' => $validated['barangay'],
                'training_type' => $validated['training_type'],
                'document_paths' => $documentPaths,
                'status' => 'pending'
            ]);

            Log::info('Training application created successfully', [
                'id' => $training->id,
                'application_number' => $training->application_number,
                'name' => $training->full_name
            ]);

            // Log activity
            try {
                \Spatie\Activitylog\Facades\Activity::withProperties([
                    'application_number' => $training->application_number,
                    'full_name' => $training->full_name,
                    'training_type' => $training->training_type,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])->log('submitted - TrainingApplication (ID: ' . $training->id . ')');
            } catch (\Exception $e) {
                Log::error('Activity logging failed: ' . $e->getMessage());
            }

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
     * Format seedling types for display - Updated for all 6 categories
     */
    private function formatSeedlingTypes($selectedSeedlings): string
    {
        $types = [];

        // Seeds
        if (!empty($selectedSeedlings['seeds'])) {
            $seedNames = collect($selectedSeedlings['seeds'])->pluck('name')->toArray();
            $types[] = 'Seeds: ' . implode(', ', $seedNames);
        }

        // Seedlings (new category)
        if (!empty($selectedSeedlings['seedlings'])) {
            $seedlingNames = collect($selectedSeedlings['seedlings'])->pluck('name')->toArray();
            $types[] = 'Seedlings: ' . implode(', ', $seedlingNames);
        }

        // Fruits
        if (!empty($selectedSeedlings['fruits'])) {
            $fruitNames = collect($selectedSeedlings['fruits'])->pluck('name')->toArray();
            $types[] = 'Fruits: ' . implode(', ', $fruitNames);
        }

        // Ornamentals
        if (!empty($selectedSeedlings['ornamentals'])) {
            $ornamentalNames = collect($selectedSeedlings['ornamentals'])->pluck('name')->toArray();
            $types[] = 'Ornamentals: ' . implode(', ', $ornamentalNames);
        }

        // Fingerlings
        if (!empty($selectedSeedlings['fingerlings'])) {
            $fingerlingNames = collect($selectedSeedlings['fingerlings'])->pluck('name')->toArray();
            $types[] = 'Fingerlings: ' . implode(', ', $fingerlingNames);
        }

        // Fertilizers
        if (!empty($selectedSeedlings['fertilizers'])) {
            $fertNames = collect($selectedSeedlings['fertilizers'])->pluck('name')->toArray();
            $types[] = 'Fertilizers: ' . implode(', ', $fertNames);
        }

        return implode(' | ', $types);
    }
}