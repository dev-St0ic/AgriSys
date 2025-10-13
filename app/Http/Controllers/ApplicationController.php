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
     * Normalize Philippine mobile number to +639 format
     * Converts 09XXXXXXXXX to +639XXXXXXXXX
     */
    private function normalizeMobileNumber($mobileNumber)
    {
        if (!$mobileNumber) {
            return null;
        }

        // Remove any spaces or dashes
        $mobileNumber = preg_replace('/[\s\-]/', '', $mobileNumber);

        // If starts with 09, convert to +639
        if (preg_match('/^09\d{9}$/', $mobileNumber)) {
            return '+63' . substr($mobileNumber, 1);
        }

        // If already in +639 format, return as is
        if (preg_match('/^\+639\d{9}$/', $mobileNumber)) {
            return $mobileNumber;
        }

        // If starts with 639, add + prefix
        if (preg_match('/^639\d{9}$/', $mobileNumber)) {
            return '+' . $mobileNumber;
        }

        // Return original if doesn't match any pattern
        return $mobileNumber;
    }

    /**
     * Submit FishR (Fisherfolk Registration) request
     */
    public function submitFishR(Request $request)
    {
        try {
        // ✅ ADD THIS AUTHENTICATION CHECK
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

            // Enhanced validation with better error messages
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'name_extension' => ['nullable', 'string', 'max:10', 'regex:/^[a-zA-Z.\s]+$/'],
                'sex' => 'required|in:Male,Female,Preferred not to say',
                'barangay' => 'required|string|max:255',
                'contact_number' => ['required', 'string', 'regex:/^(\+639|09)\d{9}$/'],
                'email' => 'required|email|max:255',
                'main_livelihood' => 'required|in:capture,aquaculture,vending,processing,others',
                'other_livelihood' => 'nullable|string|max:255|required_if:main_livelihood,others',
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
                'contact_number.regex' => 'Contact number must be in the format +639XXXXXXXXX or 09XXXXXXXXX',
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

            // Normalize contact number to +639 format
            $normalizedContactNumber = $this->normalizeMobileNumber($validated['contact_number']);

            // Create the FishR registration
            $fishRRegistration = FishrApplication::create([
                'user_id' => $userId,
                'registration_number' => $registrationNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'name_extension' => $validated['name_extension'] ?? null,
                'sex' => $validated['sex'],
                'barangay' => $validated['barangay'],
                'contact_number' => $normalizedContactNumber,
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
            'mobile' => ['required', 'string', 'regex:/^(\+639|09)\d{9}$/'],
            'email' => 'required|email|max:255',
            'barangay' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'selected_seedlings' => 'required|string',
            'supporting_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ], [
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
            'middle_name.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
            'extension_name.regex' => 'Name extension can only contain letters, periods, and spaces',
            'mobile.required' => 'Mobile number is required',
            'mobile.regex' => 'Mobile number must be in the format +639XXXXXXXXX or 09XXXXXXXXX',
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

        // Normalize mobile number to +639 format
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
            'email' => $validated['email'],
            'address' => $validated['address'],
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

/**
 * Submit RSBSA request - FIXED SESSION ACCESS
 */
public function submitRsbsa(Request $request)
{

    try {
    // ✅ ADD THIS AUTHENTICATION CHECK
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
        'username' => $userExists->username,
        'request_method' => $request->method(),
        'has_csrf' => $request->has('_token'),
        'content_type' => $request->header('Content-Type')
    ]);

        // ... rest of your validation and submission code stays the same ...

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'name_extension' => ['nullable', 'string', 'max:10', 'regex:/^[a-zA-Z.\s]+$/'],
            'sex' => 'required|in:Male,Female,Preferred not to say',
            'barangay' => 'required|string|max:255',
            'mobile' => ['required', 'string', 'regex:/^(\+639|09)\d{9}$/'],
            'email' => 'required|email|max:255',
            'main_livelihood' => 'required|in:Farmer,Farmworker/Laborer,Fisherfolk,Agri-youth',
            'land_area' => 'nullable|numeric|min:0|max:1000',
            'farm_location' => 'nullable|string|max:500',
            'commodity' => 'nullable|string|max:1000',
            'supporting_docs' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
            'middle_name.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
            'name_extension.regex' => 'Name extension can only contain letters, periods, and spaces',
            'mobile.required' => 'Mobile number is required',
            'mobile.regex' => 'Mobile number must be in the format +639XXXXXXXXX or 09XXXXXXXXX',
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

        // Normalize mobile number to +639 format
        $normalizedMobile = $this->normalizeMobileNumber($validated['mobile']);

        // Create application
        $applicationData = [
            'user_id' => $userId,
            'application_number' => $applicationNumber,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?: null,
            'last_name' => $validated['last_name'],
            'name_extension' => $validated['name_extension'] ?? null,
            'sex' => $validated['sex'],
            'contact_number' => $normalizedMobile,
            'email' => $validated['email'],
            'barangay' => $validated['barangay'],
            'main_livelihood' => $validated['main_livelihood'],
            'land_area' => $validated['land_area'],
            'farm_location' => $validated['farm_location'],
            'commodity' => $validated['commodity'],
            'supporting_document_path' => $documentPath,
            'status' => 'pending'
        ];

        $rsbsaApplication = \App\Models\RsbsaApplication::create($applicationData);

        Log::info('RSBSA application created', [
            'id' => $rsbsaApplication->id,
            'application_number' => $rsbsaApplication->application_number,
        ]);

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
        // ... validation error handling ...
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $e->errors()
            ], 422);
        }

        return redirect()->back()->withErrors($e->validator)->withInput();

    } catch (\Exception $e) {
        Log::error('RSBSA error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
                'debug_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }

        return redirect()->back()->with('error', 'An error occurred.')->withInput();
    }
}
    /**
     * Submit Boat Registration request - COMPLETE WORKING VERSION
     */
    public function submitBoatR(Request $request)
    {
        try {
        // ✅ ADD THIS AUTHENTICATION CHECK
        $userId = session('user.id');

        if (!$userId) {
            Log::warning('BoatR submission attempted without authentication');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to submit a BoatR registration.',
                    'require_auth' => true
                ], 401);
            }

            return redirect()->route('landing.page')
                ->with('error', 'You must be logged in to submit a BoatR registration.');
        }

        // Verify user exists
        $userExists = \App\Models\UserRegistration::find($userId);
        if (!$userExists) {
            Log::error('User ID does not exist', ['user_id' => $userId]);

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

        Log::info('BoatR submission started', [
            'user_id' => $userId,
            'username' => $userExists->username,
            'request_method' => $request->method(),
            'has_csrf' => $request->has('_token'),
            'content_type' => $request->header('Content-Type')
        ]);

            // Enhanced validation
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'name_extension' => ['nullable', 'string', 'max:10', 'regex:/^[a-zA-Z.\s]+$/'],
                'contact_number' => ['required', 'string', 'regex:/^(\+639|09)\d{9}$/'],
                'email' => 'required|email|max:255',
                'barangay' => 'required|string|max:255',
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
                'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
                'middle_name.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes',
                'last_name.required' => 'Last name is required',
                'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
                'name_extension.regex' => 'Name extension can only contain letters, periods, and spaces',
                'contact_number.required' => 'Contact number is required',
                'contact_number.regex' => 'Contact number must be in the format +639XXXXXXXXX or 09XXXXXXXXX',
                'email.required' => 'Email address is required',
                'email.email' => 'Please enter a valid email address',
                'barangay.required' => 'Barangay is required',
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

            // Normalize contact number to +639 format
            $normalizedContactNumber = $this->normalizeMobileNumber($validated['contact_number']);

            // Create the BoatR registration
            $boatRRegistration = BoatrApplication::create([
                'user_id' => $userId,
                'application_number' => $applicationNumber,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'name_extension' => $validated['name_extension'] ?? null,
                'contact_number' => $normalizedContactNumber,
                'email' => $validated['email'],
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
                'contact_number' => ['required', 'string', 'regex:/^(\+639|09)\d{9}$/'],
                'email' => 'required|email|max:255',
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
                'contact_number.regex' => 'Contact number must be in the format +639XXXXXXXXX or 09XXXXXXXXX',
                'email.required' => 'Email address is required',
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
                'email' => $validated['email'],
                'barangay' => $validated['barangay'],
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
