<?php

namespace App\Http\Controllers;

use App\Models\UserRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Notifications\RegistrationApprovedNotification;
use App\Notifications\RegistrationRejectedNotification;
use Laravel\Socialite\Facades\Socialite;
use App\Services\NotificationService;


class UserRegistrationController extends Controller
{
    /**
     * Display a listing of user registrations (Admin only)
     */
    public function index(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $query = UserRegistration::with('approvedBy');

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(10);

        $stats = [
            'total' => UserRegistration::count(),
            'unverified' => UserRegistration::where('status', 'unverified')->count(),
            'pending' => UserRegistration::where('status', 'pending')->count(),
            'approved' => UserRegistration::where('status', 'approved')->count(),
            'rejected' => UserRegistration::where('status', 'rejected')->count(),
            'recent' => UserRegistration::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $registrations,
                'stats' => $stats
            ]);
        }

        return view('admin.users.index', compact('registrations', 'stats'));
    }

    /**
     * Check username availability
     */
    public function checkUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'available' => false,
                'message' => 'Invalid username format'
            ]);
        }

        $username = $request->username;
        $exists = UserRegistration::where('username', $username)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Username already taken' : 'Username available'
        ]);
    }

    /**
     * Check if contact number is already registered
     */
    public function checkContactNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_number' => 'required|string|min:11|max:11'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'available' => false,
                'message' => 'Invalid contact number format'
            ]);
        }

        $contactNumber = $request->contact_number;
        $exists = UserRegistration::where('contact_number', $contactNumber)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Contact number already registered' : 'Contact number available'
        ]);
    }

    /**
     * Simple Registration - Username, Contact number, Password only
     */
    public function register(Request $request)
    {
        \Log::info('Registration attempt:', $request->all());

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/|unique:user_registration,username',
            'contact_number' => 'required|string|min:11|max:11|regex:/^09[0-9]{9}$/|unique:user_registration,contact_number',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'terms_accepted' => 'required|boolean',
        ], [
            'username.required' => 'Username is required',
            'username.unique' => 'This username is already taken',
            'username.min' => 'Username must be at least 3 characters',
            'username.max' => 'Username cannot exceed 50 characters',
            'username.regex' => 'Username can only contain letters, numbers, and underscores',
            'contact_number.required' => 'Contact number is required',
            'contact_number.unique' => 'This contact number is already registered',
            'contact_number.regex' => 'Contact number must start with 09 followed by 9 digits (e.g., 09123456789)',
            'contact_number.min' => 'Contact number must be exactly 11 digits',
            'contact_number.max' => 'Contact number must be exactly 11 digits',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'terms_accepted.required' => 'You must accept the Terms of Service',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Please complete all required fields correctly',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $registrationData = [
                'username' => $request->username,
                'contact_number' => $request->contact_number,
                'password' => $request->password,
                'status' => 'unverified',
                'terms_accepted' => (bool)$request->terms_accepted,
                'privacy_accepted' => true,
            ];

            $registration = UserRegistration::create($registrationData);

            \Log::info('New user registration created', [
                'id' => $registration->id,
                'username' => $registration->username,
                'contact_number' => $registration->contact_number,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully! Your account has been added to our database.',
                'data' => [
                    'user_id' => $registration->id,
                    'username' => $registration->username,
                    'status' => $registration->status
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Registration failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Login - Allow all statuses except 'rejected' on retry
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username is required',
            'password.required' => 'Password is incorrect'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide both username and password.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $loginField = $request->username;
            $password = $request->password;

            $userRegistration = UserRegistration::where('username', $loginField)->first();

            if ($userRegistration && Hash::check($password, $userRegistration->password)) {
                $request->session()->put('user', [
                    'id' => $userRegistration->id,
                    'username' => $userRegistration->username,
                    'name' => $userRegistration->full_name ?? $userRegistration->username,
                    'user_type' => $userRegistration->user_type,
                    'status' => $userRegistration->status
                ]);

                $request->session()->put('user_id', $userRegistration->id);
                $request->session()->put('user_status', $userRegistration->status);

                $userRegistration->update(['last_login_at' => now()]);

                // Log successful login using direct Activity facade (session-based user)
                activity()
                    ->causedBy($userRegistration)
                    ->withProperties([
                        'username' => $userRegistration->username,
                        'user_status' => $userRegistration->status,
                        'login_method' => 'password',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ])
                    ->event('login')
                    ->log('login - UserRegistration');

                $statusMessages = [
                    'unverified' => 'Welcome! You can start using our services. Complete profile verification for full access.',
                    'pending' => 'Welcome! Your verification is being reviewed. You\'ll be notified once approved.',
                    'approved' => 'Welcome back! Login successful.',
                    'rejected' => 'Welcome! Your previous verification was rejected. You can submit updated documents for review.',
                ];

                $message = $statusMessages[$userRegistration->status] ?? 'Login successful! Welcome.';

                \Log::info('User login successful', [
                    'user_id' => $userRegistration->id,
                    'username' => $userRegistration->username,
                    'status' => $userRegistration->status,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => '/dashboard',
                    'user' => [
                        'id' => $userRegistration->id,
                        'username' => $userRegistration->username,
                        'name' => $userRegistration->full_name ?? $userRegistration->username,
                        'status' => $userRegistration->status,
                        'user_type' => $userRegistration->user_type,
                    ]
                ]);
            }

            // Log failed login attempt
            activity()
                ->withProperties([
                    'username' => $loginField,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'failed_reason' => 'invalid_credentials'
                ])
                ->event('login_failed')
                ->log('login_failed');

            \Log::warning('Failed login attempt', [
                'username' => $loginField,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Username or password is incorrect. Please try again.',
            ], 401);

        } catch (\Exception $e) {
            \Log::error('Login failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Login failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $userId = $request->session()->get('user.id');
        $username = $request->session()->get('user.username');

        // Log logout activity using direct Activity facade
        if ($userId) {
            $user = \App\Models\UserRegistration::find($userId);
            if ($user) {
                activity()
                    ->causedBy($user)
                    ->withProperties([
                        'username' => $username,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ])
                    ->event('logout')
                    ->log('logout - UserRegistration');
            }
        }

         // Log the logout
        \Log::info('User logout', [
            'user_id' => $request->session()->get('user.id') ?? null,
            'ip' => $request->ip()
        ]);
        // Destroy the session completely
        $request->session()->flush();
        $request->session()->regenerate();


        if (Auth::check()) {
            Auth::logout();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'redirect' => '/'
        ]);
    }

    /**
     * Submit profile verification
     */
    public function submitVerification(Request $request)
    {
        \Log::info('Verification submission attempt', [
            'user_id' => session('user.id'),
            'form_fields' => array_keys($request->all()),
        ]);

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'middleName' => 'nullable|string|max:100',
            'extensionName' => 'nullable|string|max:20',
            'sex' => 'required|in:Male,Female,Preferred not to say',
            'role' => 'required|in:farmer,fisherfolk,general,agri-entrepreneur,cooperative-member,government-employee',
            'dateOfBirth' => 'required|date|before:today|after:' . now()->subYears(100)->toDateString(),
            'barangay' => 'required|string|max:100',
            'completeAddress' => 'required|string',
            'emergencyContactName' => 'required|string|max:100',
            'emergencyContactPhone' => [
                'required',
                'string',
                'max:11',
                'regex:/^09\d{9}$/'
            ],
            'idFront' => 'required|file|image|max:10240',
            'idBack' => 'required|file|image|max:10240',
            'locationProof' => 'required|file|image|max:10240',
        ]);

        if ($validator->fails()) {
            \Log::error('Verification validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Please complete all required fields correctly',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user.id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to submit verification.'
                ], 401);
            }

            $userRegistration = UserRegistration::find($userId);
            if (!$userRegistration) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            // Handle file uploads
            $idFrontPath = null;
            $idBackPath = null;
            $locationProofPath = null;

            try {
                if ($request->hasFile('idFront') && $request->file('idFront')->isValid()) {
                    $idFrontPath = $request->file('idFront')->store('verification/id_front', 'public');
                }

                if ($request->hasFile('idBack') && $request->file('idBack')->isValid()) {
                    $idBackPath = $request->file('idBack')->store('verification/id_back', 'public');
                }

                if ($request->hasFile('locationProof') && $request->file('locationProof')->isValid()) {
                    $locationProofPath = $request->file('locationProof')->store('verification/location_proof', 'public');
                }
            } catch (\Exception $fileException) {
                \Log::error('File upload failed', [
                    'error' => $fileException->getMessage(),
                    'user_id' => $userId
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed. Please try again with smaller images.'
                ], 500);
            }

            // Calculate age
            $dateOfBirth = new \DateTime($request->dateOfBirth);
            $age = $dateOfBirth->diff(new \DateTime())->y;

            if ($age < 18) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be at least 18 years old to register.'
                ], 422);
            }

            // Update user registration
            $updateData = [
                'first_name' => trim($request->firstName),
                'middle_name' => trim($request->middleName),
                'last_name' => trim($request->lastName),
                'name_extension' => trim($request->extensionName) ?: null,
                'sex' => $request->sex,
                'user_type' => $request->role,
                'date_of_birth' => $request->dateOfBirth,
                'age' => $age,
                'barangay' => $request->barangay,
                'complete_address' => trim($request->completeAddress),
                'emergency_contact_name' => $request->emergencyContactName ? trim($request->emergencyContactName) : null,
                'emergency_contact_phone' => $request->emergencyContactPhone ? trim($request->emergencyContactPhone) : null,
                'status' => 'pending',
                'rejection_reason' => null,
                'rejected_at' => null,
            ];

            if ($idFrontPath) {
                $updateData['id_front_path'] = $idFrontPath;
            }
            if ($idBackPath) {
                $updateData['id_back_path'] = $idBackPath;
            }
            if ($locationProofPath) {
                $updateData['location_document_path'] = $locationProofPath;
            }

           $userRegistration->update($updateData);

            //  Refresh model so full_name accessor has the new first/last name data
            $userRegistration = UserRegistration::find($userId);

            $request->session()->put('user', [
                'id' => $userRegistration->id,
                'username' => $userRegistration->username,
                'name' => $userRegistration->full_name,
                'user_type' => $userRegistration->user_type,
                'status' => 'pending'
            ]);

            \Log::info('Verification submitted successfully', [
                'user_id' => $userId,
                'user_type' => $request->role,
            ]);

            // ✅ Notify AFTER refresh so full_name is correct
            try {
                NotificationService::userVerificationSubmitted($userRegistration);
            } catch (\Exception $e) {
                // Don't let notification failure break the submission
                \Log::error('Verification notification failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile submitted successfully! Your account will be reviewed within 1–3 business days.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Verification submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to submit profile. Please try again',
            ], 500);
        }
    }

    /**
     * Get registration details for admin view
     */
    public function getRegistration($id)
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $registration = UserRegistration::with('approvedBy')->find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'This account could not be found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $registration->id,
                'username' => $registration->username,
                'first_name' => $registration->first_name,
                'middle_name' => $registration->middle_name,
                'last_name' => $registration->last_name,
                'name_extension' => $registration->name_extension,
                'full_name' => $registration->full_name ?? $registration->username,
                'contact_number' => $registration->contact_number,
                'complete_address' => $registration->complete_address,
                'barangay' => $registration->barangay,
                'user_type' => $registration->user_type,
                'status' => $registration->status,
                // FIXED: Return date in YYYY-MM-DD format
                'date_of_birth' => $registration->date_of_birth ? $registration->date_of_birth->format('Y-m-d') : null,
                'age' => $registration->age,
                'sex' => $registration->sex,
                'emergency_contact_name' => $registration->emergency_contact_name,
                'emergency_contact_phone' => $registration->emergency_contact_phone,
                'location_document_path' => $registration->location_document_path,
                'id_front_path' => $registration->id_front_path,
                'id_back_path' => $registration->id_back_path,
                'terms_accepted' => $registration->terms_accepted,
                'privacy_accepted' => $registration->privacy_accepted,
                'created_at' => $registration->created_at->format('M d, Y g:i A'),
                'last_login_at' => $registration->last_login_at ? $registration->last_login_at->format('M d, Y g:i A') : null,
                'rejection_reason' => $registration->rejection_reason,
                'approved_at' => $registration->approved_at ? $registration->approved_at->format('M d, Y g:i A') : null,
                'rejected_at' => $registration->rejected_at ? $registration->rejected_at->format('M d, Y g:i A') : null,
                'approved_by' => $registration->approvedBy ? $registration->approvedBy->name : null,
            ]
        ]);
    }

    /**
     * View uploaded document
     */
    public function viewDocument($id, $type)
    {
        // Check admin authentication
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $registration = UserRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'This account could not be found'
            ], 404);
        }

        $documentPath = null;
        $documentName = '';

        switch ($type) {
            case 'location':
                $documentPath = $registration->location_document_path;
                $documentName = 'Location Proof Document';
                break;
            case 'id_front':
                $documentPath = $registration->id_front_path;
                $documentName = 'Government ID - Front';
                break;
            case 'id_back':
                $documentPath = $registration->id_back_path;
                $documentName = 'Government ID - Back';
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve document. Please try again or contact support.'
                ], 400);
        }

        if (!$documentPath) {
            return response()->json([
                'success' => false,
                'message' => "This document hasn't been uploaded yet"
            ], 404);
        }

        try {
            if (!\Storage::disk('public')->exists($documentPath)) {
                \Log::error("Document file not found in storage", [
                    'registration_id' => $id,
                    'document_type' => $type,
                    'document_path' => $documentPath,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, the document could not be found. Please try uploading again.'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $documentPath);
            $fileSize = filesize($filePath);
            $mimeType = mime_content_type($filePath);
            $fileName = basename($documentPath);
            $documentUrl = asset('storage/' . $documentPath);
            $isImage = str_starts_with($mimeType, 'image/');

            \Log::info("Document accessed successfully", [
                'registration_id' => $id,
                'document_type' => $type,
            ]);

            return response()->json([
                'success' => true,
                'document_url' => $documentUrl,
                'document_path' => $documentPath,
                'document_type' => $type,
                'file_info' => [
                    'name' => $fileName,
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                    'is_image' => $isImage
                ],
                'file_exists' => true
            ]);

        } catch (\Exception $e) {
            \Log::error("Error generating document URL", [
                'registration_id' => $id,
                'document_type' => $type,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to access the document. Please try again later.'
            ], 500);
        }
    }

    /**
     * Serve document directly
     */
    public function serveDocument($id, $type)
    {
        // Check admin authentication
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            abort(403, 'You do not have permission to access this document');
        }

        $registration = UserRegistration::find($id);

        if (!$registration) {
            abort(404, 'This account could not be found');
        }

        $documentPath = null;

        switch ($type) {
            case 'location':
                $documentPath = $registration->location_document_path;
                break;
            case 'id_front':
                $documentPath = $registration->id_front_path;
                break;
            case 'id_back':
                $documentPath = $registration->id_back_path;
                break;
            default:
                abort(400, 'Document type not recognized');
        }

        if (!$documentPath || !\Storage::disk('public')->exists($documentPath)) {
            abort(404, 'Document not found');
        }

        $filePath = \Storage::disk('public')->path($documentPath);
        $mimeType = mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($documentPath) . '"'
        ]);
    }

    /**
     * Approve registration
     */
    public function approve(Request $request, $id)
    {
        $registration = UserRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'This account could not be found'
            ], 404);
        }

        \Log::info('Account verification approval initiated', [
            'user_id' => $registration->id,
            'current_status' => $registration->status,
            'admin_id' => auth()->id()
        ]);

        // Use the model's approve method to trigger SMS notification
        $registration->approve(auth()->id());

        // Log activity
        $this->logActivity('approved', 'UserRegistration', $registration->id, [
            'username' => $registration->username,
            'contact_number' => $registration->contact_number
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration approved successfully'
        ]);
    }

    /**
     * Reject registration
     */
    public function reject(Request $request, $id)
    {
        $registration = UserRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'This account could not be found'
            ], 404);
        }

        // Use the model's reject method to trigger SMS notification
        $registration->reject($request->reason ?? 'No reason provided', auth()->id());

        // Log activity
        $this->logActivity('rejected', 'UserRegistration', $registration->id, [
            'username' => $registration->username,
            'reason' => $request->reason ?? 'No reason provided'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration rejected successfully'
        ]);
    }

    /**
     * Create new user with documents
     */
    public function createUser(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/|unique:user_registration,username',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'name_extension' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'sex' => 'required|in:Male,Female,Preferred not to say',
            'contact_number' => ['required', 'string', 'max:11', 'regex:/^09\d{9}$/'],
            'barangay' => 'required|string|max:100',
            'complete_address' => 'required|string',
            'user_type' => 'required|in:farmer,fisherfolk,general,agri-entrepreneur,cooperative-member,government-employee',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_phone' => ['required', 'string', 'max:11', 'regex:/^09\d{9}$/'],
            'status' => 'required|in:unverified,pending,approved',
            'id_front' => 'required|file|image|mimes:jpeg,png,jpg|max:10240',
            'id_back' => 'required|file|image|mimes:jpeg,png,jpg|max:10240',
            'location_proof' => 'required|file|image|mimes:jpeg,png,jpg|max:10240',
        ], [
            'username.regex' => 'Username can only contain letters, numbers, and underscores',
            'username.unique' => 'This username is already taken',
            'contact_number.regex' => 'Please enter a valid Philippine mobile number',
            'emergency_contact_phone.regex' => 'Please enter a valid Philippine mobile number',
            'id_front.image' => 'ID front must be an image file',
            'id_front.max' => 'ID front image must be less than 10MB',
            'id_back.image' => 'ID back must be an image file',
            'id_back.max' => 'ID back image must be less than 10MB',
            'location_proof.image' => 'Location proof must be an image file',
            'location_proof.max' => 'Location proof image must be less than 10MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete all required fields correctly',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dateOfBirth = new \DateTime($request->date_of_birth);
            $age = $dateOfBirth->diff(new \DateTime())->y;

            if ($age < 18) {
                return response()->json([
                    'success' => false,
                    'message' => 'User must be at least 18 years old.',
                    'errors' => [
                        'date_of_birth' => ['User must be at least 18 years old']
                    ]
                ], 422);
            }

            // Handle file uploads
            $idFrontPath = null;
            $idBackPath = null;
            $locationProofPath = null;

            try {
                if ($request->hasFile('id_front') && $request->file('id_front')->isValid()) {
                    $idFrontPath = $request->file('id_front')->store('verification/id_front', 'public');
                }

                if ($request->hasFile('id_back') && $request->file('id_back')->isValid()) {
                    $idBackPath = $request->file('id_back')->store('verification/id_back', 'public');
                }

                if ($request->hasFile('location_proof') && $request->file('location_proof')->isValid()) {
                    $locationProofPath = $request->file('location_proof')->store('verification/location_proof', 'public');
                }
            } catch (\Exception $fileException) {
                \Log::error('File upload failed during admin user creation', [
                    'error' => $fileException->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed. Please try again with smaller images.'
                ], 500);
            }

            // Create user
            $userData = [
                'username' => $request->username,
                'password' => $request->password,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'name_extension' => $request->name_extension,
                'date_of_birth' => $request->date_of_birth,
                'age' => $age,
                'sex' => $request->sex,
                'contact_number' => $request->contact_number,
                'barangay' => $request->barangay,
                'complete_address' => $request->complete_address,
                'user_type' => $request->user_type,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'status' => $request->status,
                'terms_accepted' => true,
                'privacy_accepted' => true,
            ];

            if ($idFrontPath) {
                $userData['id_front_path'] = $idFrontPath;
            }
            if ($idBackPath) {
                $userData['id_back_path'] = $idBackPath;
            }
            if ($locationProofPath) {
                $userData['location_document_path'] = $locationProofPath;
            }

            if ($request->status === 'approved') {
                $userData['approved_at'] = now();
                $userData['approved_by'] = auth()->id();
            }

            if ($request->status === 'pending') {
                $userData['approved_by'] = auth()->id();
            }

            $registration = UserRegistration::create($userData);

            \Log::info('User created by admin', [
                'created_user_id' => $registration->id,
                'username' => $registration->username,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'user' => [
                    'id' => $registration->id,
                    'username' => $registration->username,
                    'status' => $registration->status
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Admin user creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user. Please try again.',
            ], 500);
        }
    }
    /**
     * FIXED: Admin update registration (with all fields and file uploads)
     */
    public function update(Request $request, $id)
    {
        // Check admin authentication
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        // Find registration
        $registration = UserRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'This account could not be found'
            ], 404);
        }

        // Validation rules - COMPLETE with all fields
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'name_extension' => 'nullable|string|max:20',
            'sex' => 'sometimes|nullable|in:Male,Female,Preferred not to say',
            'date_of_birth' => 'sometimes|nullable|date|before:today|after:' . now()->subYears(100)->toDateString(),
            'age' => 'sometimes|nullable|integer|min:18|max:150',
            'contact_number' => [
                'sometimes',
                'required',
                'string',
                'max:11',
                'regex:/^09\d{9}$/',
                Rule::unique('user_registration', 'contact_number')->ignore($id)
            ],
            'barangay' => 'sometimes|required|string|max:100',
            'complete_address' => 'sometimes|string|max:500',
            'user_type' => 'sometimes|required|in:farmer,fisherfolk,general,agri-entrepreneur,cooperative-member,government-employee',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:11',
                'regex:/^09\d{9}$/'
            ],
            // FILE UPLOADS - NOW PROPERLY HANDLED
            'id_front' => 'nullable|file|image|mimes:jpeg,png,jpg|max:10240',
            'id_back' => 'nullable|file|image|mimes:jpeg,png,jpg|max:10240',
            'location_proof' => 'nullable|file|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        if ($validator->fails()) {
            \Log::warning('Admin edit validation failed for registration ' . $id, [
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Prepare update data
            $updateData = [];

            // Handle all text fields
            if ($request->has('first_name')) {
                $updateData['first_name'] = trim($request->first_name);
            }
            if ($request->has('middle_name')) {
                $updateData['middle_name'] = trim($request->middle_name) ?: null;
            }
            if ($request->has('last_name')) {
                $updateData['last_name'] = trim($request->last_name);
            }
            if ($request->has('name_extension')) {
                $updateData['name_extension'] = $request->name_extension ?: null;
            }
            if ($request->has('sex')) {
                $updateData['sex'] = $request->sex ?: null;
            }
            if ($request->has('date_of_birth')) {
                $updateData['date_of_birth'] = $request->date_of_birth;

                // Auto-calculate age if date_of_birth is provided
                if ($request->date_of_birth) {
                    $dateOfBirth = new \DateTime($request->date_of_birth);
                    $age = $dateOfBirth->diff(new \DateTime())->y;

                    if ($age < 18) {
                        return response()->json([
                            'success' => false,
                            'message' => 'User must be at least 18 years old',
                            'errors' => [
                                'date_of_birth' => ['User must be at least 18 years old']
                            ]
                        ], 422);
                    }

                    $updateData['age'] = $age;
                }
            }
            if ($request->has('contact_number')) {
                $updateData['contact_number'] = trim($request->contact_number);
            }
            if ($request->has('barangay')) {
                $updateData['barangay'] = $request->barangay;
            }
            if ($request->has('complete_address')) {
                $updateData['complete_address'] = trim($request->complete_address);
            }
            if ($request->has('user_type')) {
                $updateData['user_type'] = $request->user_type;
            }
            if ($request->has('emergency_contact_name')) {
                $updateData['emergency_contact_name'] = $request->emergency_contact_name ?
                    trim($request->emergency_contact_name) : null;
            }
            if ($request->has('emergency_contact_phone')) {
                $updateData['emergency_contact_phone'] = $request->emergency_contact_phone ?
                    trim($request->emergency_contact_phone) : null;
            }

            // Handle document uploads - THIS WAS MISSING!
            try {
                if ($request->hasFile('id_front') && $request->file('id_front')->isValid()) {
                    $idFrontPath = $request->file('id_front')->store('verification/id_front', 'public');
                    $updateData['id_front_path'] = $idFrontPath;
                    \Log::info('ID Front uploaded:', ['path' => $idFrontPath]);
                }

                if ($request->hasFile('id_back') && $request->file('id_back')->isValid()) {
                    $idBackPath = $request->file('id_back')->store('verification/id_back', 'public');
                    $updateData['id_back_path'] = $idBackPath;
                    \Log::info('ID Back uploaded:', ['path' => $idBackPath]);
                }

                if ($request->hasFile('location_proof') && $request->file('location_proof')->isValid()) {
                    $locationProofPath = $request->file('location_proof')->store('verification/location_proof', 'public');
                    $updateData['location_document_path'] = $locationProofPath;
                    \Log::info('Location proof uploaded:', ['path' => $locationProofPath]);
                }
            } catch (\Exception $fileException) {
                \Log::error('File upload failed during admin edit', [
                    'registration_id' => $id,
                    'error' => $fileException->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed. Please try again with smaller images.'
                ], 500);
            }

            if (empty($updateData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No changes provided. Please modify at least one field.'
                ], 422);
            }

            // Update the registration
            $registration->update($updateData);

            // Refresh registration data from database
            $registration = UserRegistration::find($id);

            // If this user is currently logged in, update their session
            if (session('user.id') == $id) {
                session()->put('user', [
                    'id' => $registration->id,
                    'username' => $registration->username,
                    'name' => $registration->full_name ?? $registration->username,
                    'user_type' => $registration->user_type,
                    'status' => $registration->status
                ]);
                session()->put('user_status', $registration->status);
            }

            \Log::info('Admin updated user registration', [
                'registration_id' => $id,
                'username' => $registration->username,
                'updated_fields' => array_keys($updateData),
                'admin_id' => auth()->id(),
                'files_uploaded' => [
                    'id_front' => isset($updateData['id_front_path']),
                    'id_back' => isset($updateData['id_back_path']),
                    'location_proof' => isset($updateData['location_document_path'])
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration updated successfully',
                'data' => [
                    'id' => $registration->id,
                    'username' => $registration->username,
                    'first_name' => $registration->first_name,
                    'last_name' => $registration->last_name,
                    'contact_number' => $registration->contact_number,
                    'user_type' => $registration->user_type,
                    'barangay' => $registration->barangay,
                    'complete_address' => $registration->complete_address,
                    'date_of_birth' => $registration->date_of_birth,
                    'age' => $registration->age,
                    'sex' => $registration->sex,
                    'status' => $registration->status
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Admin edit registration failed: ' . $e->getMessage(), [
                'registration_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update registration. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * FIXED: Update registration status with session sync
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $registration = UserRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'This account could not be found'
            ], 404);
        }

        $validator = \Validator::make($request->all(), [
            'status' => 'required|in:unverified,pending,approved,rejected',
            'remarks' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldStatus = $registration->status;
            $newStatus = $request->status;

            // Use model methods that trigger SMS notifications
            if ($newStatus === 'approved') {
                $registration->approve(auth()->id());
                $this->logActivity('approved', 'UserRegistration', $registration->id, [
                    'username' => $registration->username,
                    'contact_number' => $registration->contact_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);
            } elseif ($newStatus === 'rejected') {
                $registration->reject($request->remarks ?? 'No reason provided', auth()->id());
                $this->logActivity('rejected', 'UserRegistration', $registration->id, [
                    'username' => $registration->username,
                    'reason' => $request->remarks ?? 'No reason provided',
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);
            } else {
                $updateData = [
                    'status' => $newStatus,
                    'approved_by' => auth()->id(),
                ];

                if ($request->remarks) {
                    $updateData['rejection_reason'] = $request->remarks;
                }

                $registration->update($updateData);

                $this->logActivity('status_changed', 'UserRegistration', $registration->id, [
                    'username' => $registration->username,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'remarks' => $request->remarks,
                ]);
            }

            // FIXED: Refresh registration from database to get latest data
            $registration = UserRegistration::find($id);

            // FIXED: If user is currently logged in, update their session immediately
            if (session('user.id') == $id) {
                \Log::info('Updating session for logged-in user after status change', [
                    'user_id' => $id,
                    'old_status' => $oldStatus,
                    'new_status' => $registration->status
                ]);

                session()->put('user', [
                    'id' => $registration->id,
                    'username' => $registration->username,
                    'name' => $registration->full_name ?? $registration->username,
                    'user_type' => $registration->user_type,
                    'status' => $registration->status  // FRESH STATUS
                ]);
                session()->put('user_status', $registration->status);
            }

            \Log::info('Registration status updated', [
                'registration_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $registration->status,
                'sms_triggered' => in_array($newStatus, ['approved', 'rejected']),
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration status updated successfully',
                'new_status' => $registration->status,
                'auto_refresh' => true
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update registration status', [
                'registration_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update registration status'
            ], 500);
        }
    }

   /**
     * UPDATED: Move registration to recycle bin instead of permanent deletion
     *
     */
    public function destroy($id)
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $registration = UserRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'This account could not be found'
            ], 404);
        }

        try {
            $username = $registration->username;
            $registrationId = $registration->id;

            // Move to recycle bin instead of permanent deletion
            \App\Services\RecycleBinService::softDelete(
                $registration,
                'Deleted from User Registrations'
            );

            // Log activity
            $this->logActivity('deleted', 'UserRegistration', $registrationId, [
                'username' => $username,
                'action' => 'moved_to_recycle_bin'
            ]);

            Log::info('User registration moved to recycle bin', [
                'registration_id' => $registrationId,
                'username' => $username,
                'deleted_by' => auth()->user()->name ?? 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Registration for {$username} has been moved to recycle bin"
            ]);

        } catch (\Exception $e) {
            Log::error('Error moving user registration to recycle bin', [
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
     * Get statistics for admin dashboard
     */
    public function getStatistics()
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $stats = [
            'total' => UserRegistration::count(),
            'unverified' => UserRegistration::where('status', 'unverified')->count(),
            'pending' => UserRegistration::where('status', 'pending')->count(),
            'approved' => UserRegistration::where('status', 'approved')->count(),
            'rejected' => UserRegistration::where('status', 'rejected')->count(),
            'recent' => UserRegistration::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

 /**
 * FIXED: Get user profile with fresh data
 */
public function getUserProfile(Request $request)
{
    try {
        $userId = session('user.id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = UserRegistration::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // FIXED: Also update session with fresh data from DB
        $request->session()->put('user', [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->full_name ?? $user->username,
            'user_type' => $user->user_type,
            'status' => $user->status
        ]);
        $request->session()->put('user_status', $user->status);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name ?? $user->username,
                'username' => $user->username,
                'status' => $user->status,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'middle_name' => $user->middle_name,
                'name_extension' => $user->name_extension,
                'sex' => $user->sex,
                'contact_number' => $user->contact_number,
                'date_of_birth' => $user->date_of_birth,
                'age' => $user->age,
                'complete_address' => $user->complete_address,
                'barangay' => $user->barangay,
                'user_type' => $user->user_type,
                'remarks' => $user->rejection_reason,
                'verified_at' => $user->approved_at,
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Profile polling error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error retrieving user profile: ' . $e->getMessage()
        ], 500);
    }
}


    /**
     * Update session data
     */
    public function updateSession(Request $request)
    {
        $userId = session('user.id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'No active session'
            ], 401);
        }

        $user = UserRegistration::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $request->session()->put('user', [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->full_name ?? $user->username,
            'user_type' => $user->user_type,
            'status' => $user->status
        ]);

        $request->session()->put('user_status', $user->status);

        \Log::info('Session updated via polling', [
            'user_id' => $userId,
            'new_status' => $user->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session updated',
            'status' => $user->status
        ]);
    }

    /**
     * Get public statistics
     */
    public function getPublicStats()
    {
        $stats = [
            'total_users' => UserRegistration::count(),
            'approved_users' => UserRegistration::where('status', 'approved')->count(),
            'recent_registrations' => UserRegistration::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Bulk approve registrations
     */
    public function bulkApprove(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:user_registration,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration IDs provided',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $count = UserRegistration::whereIn('id', $request->ids)
                ->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'rejection_reason' => null,
                    'rejected_at' => null
                ]);

            // Log activity
            $this->logActivity('approved', 'UserRegistration', null, [
                'count' => $count,
                'ids' => $request->ids
            ], "Bulk approved {$count} user registrations");

            return response()->json([
                'success' => true,
                'message' => "Successfully approved {$count} registrations"
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk approve failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk approval failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Bulk reject registrations
     */
    public function bulkReject(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:user_registration,id',
            'reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $count = UserRegistration::whereIn('id', $request->ids)
                ->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                    'approved_by' => auth()->id(),
                    'rejection_reason' => $request->reason,
                    'approved_at' => null
                ]);

            // Log activity
            $this->logActivity('rejected', 'UserRegistration', null, [
                'count' => $count,
                'ids' => $request->ids,
                'reason' => $request->reason
            ], "Bulk rejected {$count} user registrations");

            return response()->json([
                'success' => true,
                'message' => "Successfully rejected {$count} registrations"
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk reject failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk rejection failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Change user password
     */

    public function bulkBan(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:user_registration,id',
            'reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $count = UserRegistration::whereIn('id', $request->ids)
                ->update([
                    'status' => 'banned',
                    'banned_at' => now(),
                    'approved_by' => auth()->id(),
                    'ban_reason' => $request->reason,
                    'rejection_reason' => null,
                    'approved_at' => null,
                    'rejected_at' => null
                ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully banned {$count} registrations"
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk ban failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Bulk ban failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Export registrations to CSV/Excel - UPDATED to include banned status
     */
    public function export(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            abort(403, 'Access denied');
        }

        $query = UserRegistration::query();

        // Apply same filters as index method
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $registrations = $query->orderBy('created_at', 'desc')->get();

        $filename = 'user_registrations_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($registrations) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'ID', 'Username', 'First Name', 'Last Name', 'Sex',
                'User Type', 'Status', 'Contact Number', 'Barangay',
                'Created At', 'Approved At', 'Rejected At', 'Banned At', 'Last Login'
            ]);

            // CSV Data
            foreach ($registrations as $registration) {
                fputcsv($file, [
                    $registration->id,
                    $registration->username,
                    $registration->first_name,
                    $registration->last_name,
                    $registration->sex,
                    $registration->user_type,
                    $registration->status,
                    $registration->contact_number,
                    $registration->barangay,
                    $registration->created_at ? $registration->created_at->format('Y-m-d H:i:s') : '',
                    $registration->approved_at ? $registration->approved_at->format('Y-m-d H:i:s') : '',
                    $registration->rejected_at ? $registration->rejected_at->format('Y-m-d H:i:s') : '',
                    $registration->banned_at ? $registration->banned_at->format('Y-m-d H:i:s') : '',
                    $registration->last_login_at ? $registration->last_login_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $userId = session('user.id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to change password'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete all required fields correctly',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userRegistration = UserRegistration::find($userId);

            if (!$userRegistration) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            if (!Hash::check($request->current_password, $userRegistration->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                    'errors' => [
                        'current_password' => ['Current password is incorrect']
                    ]
                ], 422);
            }

            $userRegistration->password = $request->new_password;
            $userRegistration->save();

            // FIXED: Properly flush and regenerate session
            $request->session()->flush();
            $request->session()->regenerate();

            \Log::info('Password changed successfully', [
                'user_id' => $userId,
                'username' => $userRegistration->username,
            ]);

            $request->session()->flush();
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully! Please log in with your new password.',
                'redirect' => '/'
            ]);

        } catch (\Exception $e) {
            \Log::error('Password change failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to change password. Please try again',
            ], 500);
        }
    }

    /**
     * FIXED: Update user profile with proper session sync
     */
    public function updateUserProfile(Request $request)
    {
        $userId = session('user.id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to update profile'
            ], 401);
        }

        $registration = UserRegistration::find($userId);
        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validationRules = [
            'contact_number' => [
                'sometimes',
                'string',
                'max:11',
                'regex:/^09\d{9}$/'
            ],
            'complete_address' => 'sometimes|string|max:500',
            'barangay' => 'sometimes|string|max:100',
        ];

        if ($request->has('username') && $request->username !== $registration->username) {
            $validationRules['username'] = [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:user_registration,username,' . $userId
            ];
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only([
                'contact_number',
                'complete_address',
                'barangay',
            ]);

            if ($request->has('username') && $request->username !== $registration->username) {
                if ($registration->username_changed_at !== null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Username can only be changed once. Your current username cannot be modified anymore.',
                        'errors' => [
                            'username' => ['Username can only be changed once per account']
                        ]
                    ], 422);
                }

                $updateData['username'] = $request->username;
                $updateData['username_changed_at'] = now();
            }

            // Filter out null values
            $filteredData = array_filter($updateData, function($value) {
                return $value !== null;
            });

            $registration->update($filteredData);

            // FIXED: Refresh the user data from database
            $registration = UserRegistration::find($userId);

            // FIXED: Update session with fresh data from database
            $request->session()->put('user', [
                'id' => $registration->id,
                'username' => $registration->username,
                'name' => $registration->full_name ?? $registration->username,
                'user_type' => $registration->user_type,
                'status' => $registration->status  // ENSURE status is updated
            ]);

            // FIXED: Also update user_status in session separately
            $request->session()->put('user_status', $registration->status);

            \Log::info('User profile updated', [
                'user_id' => $userId,
                'updated_fields' => array_keys($filteredData)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $registration->id,
                    'username' => $registration->username,
                    'contact_number' => $registration->contact_number,
                    'name_extension' => $registration->name_extension,
                    'complete_address' => $registration->complete_address,
                    'barangay' => $registration->barangay,
                    'name' => $registration->full_name ?? $registration->username,
                    'status' => $registration->status,  // RETURN current status
                    'username_changed_at' => $registration->username_changed_at,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Profile update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to update profile. Please try again'
            ], 500);
        }
    }

    /**
     * Force session refresh endpoint
     * Called by frontend after admin actions
     */
    public function refreshUserSession(Request $request)
    {
        $userId = session('user.id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'authenticated' => false,
                'message' => 'No active session'
            ], 401);
        }

        try {
            $user = UserRegistration::find($userId);

            if (!$user) {
                $request->session()->flush();
                return response()->json([
                    'success' => false,
                    'authenticated' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Update session with fresh data
            $request->session()->put('user', [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->full_name ?? $user->username,
                'user_type' => $user->user_type,
                'status' => $user->status
            ]);
            $request->session()->put('user_status', $user->status);

            \Log::info('User session refreshed', [
                'user_id' => $userId,
                'status' => $user->status
            ]);

            return response()->json([
                'success' => true,
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'status' => $user->status,
                    'name' => $user->full_name ?? $user->username,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Session refresh error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error refreshing session'
            ], 500);
        }
    }
}
