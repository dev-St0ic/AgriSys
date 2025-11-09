<?php

namespace App\Http\Controllers;

use App\Models\UserRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\RegistrationApprovedNotification;
use App\Notifications\RegistrationRejectedNotification;
use Laravel\Socialite\Facades\Socialite;

class UserRegistrationController extends Controller
{
    /**
     * Display a listing of user registrations (Admin only)
     */
    public function index(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
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

        if ($request->filled('verification_status')) {
            if ($request->verification_status === 'verified') {
                $query->emailVerified();
            } elseif ($request->verification_status === 'unverified') {
                $query->emailUnverified();
            }
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(15);

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
     * Simple Registration - Username, Email, Password only
     */
    public function register(Request $request)
    {
        \Log::info('Registration attempt:', $request->all());

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/|unique:user_registration,username',
            'email' => 'required|string|email|max:255|unique:user_registration,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'terms_accepted' => 'required|boolean',
            'g-recaptcha-response' => 'required|string',
        ], [
            'username.required' => 'Username is required',
            'username.unique' => 'This username is already taken',
            'username.min' => 'Username must be at least 3 characters',
            'username.max' => 'Username cannot exceed 50 characters',
            'username.regex' => 'Username can only contain letters, numbers, and underscores',
            'email.required' => 'Email address is required',
            'email.unique' => 'This email is already registered',
            'email.email' => 'Please enter a valid email address',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'terms_accepted.required' => 'You must accept the Terms of Service',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Please check the form for errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify reCAPTCHA v2
        try {
            $recaptchaSecret = config('recaptcha.secret_key');
            $recaptchaToken = $request->input('g-recaptcha-response');

            if (!$recaptchaSecret) {
                Log::error('RECAPTCHA_SECRET_KEY not set in .env');
                throw new \Exception('reCAPTCHA configuration missing');
            }

            if (!$recaptchaToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete the reCAPTCHA challenge'
                ], 422);
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaToken,
            ]);

            $recaptchaData = $response->json();

            if (!isset($recaptchaData['success']) || !$recaptchaData['success']) {
                Log::warning('reCAPTCHA verification failed', [
                    'response' => $recaptchaData,
                    'email' => $request->email
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'reCAPTCHA verification failed. Please try again.',
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'reCAPTCHA verification error. Please try again.',
            ], 500);
        }

        try {
            $registrationData = [
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'status' => 'unverified',
                'terms_accepted' => (bool)$request->terms_accepted,
                'privacy_accepted' => true,
            ];

            $registration = UserRegistration::create($registrationData);

            \Log::info('New user registration created', [
                'id' => $registration->id,
                'username' => $registration->username,
                'email' => $registration->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully! Your account has been added to our database.',
                'data' => [
                    'user_id' => $registration->id,
                    'username' => $registration->username,
                    'email' => $registration->email,
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
            'username.required' => 'Username or email is required',
            'password.required' => 'Password is required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide both username/email and password.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $loginField = $request->username;
            $password = $request->password;

            $userRegistration = UserRegistration::where('email', $loginField)
                ->orWhere('username', $loginField)
                ->first();

            if ($userRegistration && Hash::check($password, $userRegistration->password)) {
                // Store user data in session
                $request->session()->put('user', [
                    'id' => $userRegistration->id,
                    'username' => $userRegistration->username,
                    'email' => $userRegistration->email,
                    'name' => $userRegistration->full_name ?? $userRegistration->username,
                    'user_type' => $userRegistration->user_type,
                    'status' => $userRegistration->status
                ]);

                $request->session()->put('user_id', $userRegistration->id);
                $request->session()->put('user_email', $userRegistration->email);
                $request->session()->put('user_username', $userRegistration->username);
                $request->session()->put('user_status', $userRegistration->status);

                $userRegistration->update(['last_login_at' => now()]);

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
                        'email' => $userRegistration->email,
                        'status' => $userRegistration->status,
                        'user_type' => $userRegistration->user_type,
                    ]
                ]);
            }

            // Check admin users
            $user = User::where('email', $loginField)->first();

            if ($user && Hash::check($password, $user->password)) {
                Auth::login($user);

                $request->session()->put('user', [
                    'id' => $user->id,
                    'username' => $user->email,
                    'email' => $user->email,
                    'name' => $user->name,
                    'user_type' => 'admin',
                    'status' => 'approved'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Admin login successful!',
                    'redirect' => '/admin/users',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => 'admin'
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials. Please check your username/email and password.'
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
        $request->session()->flush();

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
            'role' => 'required|in:farmer,fisherfolk,general,agri-entrepreneur,cooperative-member,government-employee',
            'contactNumber' => [
                'required',
                'string',
                'max:20',
                'regex:/^(\+639|09)\d{9}$/'
            ],
            'dateOfBirth' => 'required|date|before:today|after:' . now()->subYears(100)->toDateString(),
            'barangay' => 'required|string|max:100',
            'completeAddress' => 'required|string',
            'emergencyContactName' => 'required|string|max:100',
            'emergencyContactPhone' => [
                'required',
                'string',
                'max:20',
                'regex:/^(\+639|09)\d{9}$/'
            ],
            'idFront' => 'required|file|image|max:5120',
            'idBack' => 'required|file|image|max:5120',
            'locationProof' => 'required|file|image|max:5120',
        ]);

        if ($validator->fails()) {
            \Log::error('Verification validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Please check all required fields.',
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
                'name_extension' => trim($request->extensionName),
                'user_type' => $request->role,
                'contact_number' => trim($request->contactNumber),
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

            $request->session()->put('user', [
                'id' => $userRegistration->id,
                'username' => $userRegistration->username,
                'email' => $userRegistration->email,
                'name' => $userRegistration->full_name,
                'user_type' => $userRegistration->user_type,
                'status' => 'pending'
            ]);

            \Log::info('Verification submitted successfully', [
                'user_id' => $userId,
                'user_type' => $request->role,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification submitted successfully! Your account will be reviewed within 2-3 business days.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Verification submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Verification submission failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Get registration details for admin view
     */
    public function getRegistration($id)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $registration = UserRegistration::with('approvedBy')->find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $registration->id,
                'username' => $registration->username,
                'email' => $registration->email,
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
                'date_of_birth' => $registration->date_of_birth ? $registration->date_of_birth->format('M d, Y') : null,
                'age' => $registration->age,
                'gender' => $registration->gender,
                'emergency_contact_name' => $registration->emergency_contact_name,
                'emergency_contact_phone' => $registration->emergency_contact_phone,
                'location_document_path' => $registration->location_document_path,
                'id_front_path' => $registration->id_front_path,
                'id_back_path' => $registration->id_back_path,
                'email_verified' => $registration->hasVerifiedEmail(),
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
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $registration = UserRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
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
                    'message' => 'Invalid document type. Allowed types: location, id_front, id_back'
                ], 400);
        }

        if (!$documentPath) {
            return response()->json([
                'success' => false,
                'message' => "No {$documentName} found for this registration"
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
                    'message' => 'Document file not found on server'
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
                'message' => 'Error accessing document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Serve document directly
     */
    public function serveDocument($id, $type)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $registration = UserRegistration::find($id);

        if (!$registration) {
            abort(404, 'Registration not found');
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
                abort(400, 'Invalid document type');
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
                'message' => 'Registration not found'
            ], 404);
        }

        $registration->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
            'rejected_at' => null
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
                'message' => 'Registration not found'
            ], 404);
        }

        $registration->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->reason ?? 'No reason provided',
            'approved_at' => null
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
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/|unique:user_registration,username',
            'email' => 'required|string|email|max:255|unique:user_registration,email',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'name_extension' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'nullable|in:male,female',
            'contact_number' => ['required', 'string', 'max:20', 'regex:/^(\+639|09)\d{9}$/'],
            'barangay' => 'required|string|max:100',
            'complete_address' => 'required|string',
            'user_type' => 'required|in:farmer,fisherfolk,general,agri-entrepreneur,cooperative-member',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_phone' => ['required', 'string', 'max:20', 'regex:/^(\+639|09)\d{9}$/'],
            'status' => 'required|in:unverified,pending,approved',
            'email_verified' => 'boolean',
            'id_front' => 'required|file|image|mimes:jpeg,png,jpg|max:5120',
            'id_back' => 'required|file|image|mimes:jpeg,png,jpg|max:5120',
            'location_proof' => 'required|file|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'username.regex' => 'Username can only contain letters, numbers, and underscores',
            'username.unique' => 'This username is already taken',
            'contact_number.regex' => 'Please enter a valid Philippine mobile number',
            'emergency_contact_phone.regex' => 'Please enter a valid Philippine mobile number',
            'id_front.image' => 'ID front must be an image file',
            'id_front.max' => 'ID front image must be less than 5MB',
            'id_back.image' => 'ID back must be an image file',
            'id_back.max' => 'ID back image must be less than 5MB',
            'location_proof.image' => 'Location proof must be an image file',
            'location_proof.max' => 'Location proof image must be less than 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check all required fields.',
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
                    'admin_user' => auth()->user()->email
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed. Please try again with smaller images.'
                ], 500);
            }

            // Create user
            $userData = [
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'name_extension' => $request->name_extension,
                'date_of_birth' => $request->date_of_birth,
                'age' => $age,
                'gender' => $request->gender,
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

            if ($request->email_verified) {
                $userData['email_verified_at'] = now();
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
                'admin_user' => auth()->user()->email,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'user' => [
                    'id' => $registration->id,
                    'username' => $registration->username,
                    'email' => $registration->email,
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
     * Update registration status
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $registration = UserRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
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
            $updateData = [
                'status' => $request->status,
                'approved_by' => auth()->id(),
            ];

            if ($request->status === 'approved') {
                $updateData['approved_at'] = now();
                $updateData['rejected_at'] = null;
                $updateData['rejection_reason'] = null;
            } elseif ($request->status === 'rejected') {
                $updateData['rejected_at'] = now();
                $updateData['approved_at'] = null;
                $updateData['rejection_reason'] = $request->remarks;
            } else {
                if ($request->remarks) {
                    $updateData['rejection_reason'] = $request->remarks;
                }
            }

            $registration->update($updateData);

            \Log::info('Registration status updated', [
                'registration_id' => $id,
                'old_status' => $registration->getOriginal('status'),
                'new_status' => $request->status,
                'admin_user' => auth()->user()->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration status updated successfully',
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
     * Delete registration
     */
    public function destroy($id)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        $registration = UserRegistration::find($id);

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Registration not found'
            ], 404);
        }

        $registration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registration deleted successfully'
        ]);
    }

    /**
     * Get statistics for admin dashboard
     */
    public function getStatistics()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
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
            'email_verified' => UserRegistration::whereNotNull('email_verified_at')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get user profile data
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

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->full_name ?? $user->username,
                    'username' => $user->username,
                    'email' => $user->email,
                    'status' => $user->status,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'middle_name' => $user->middle_name,
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
            'email' => $user->email,
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
        if (!auth()->check() || !auth()->user()->isAdmin()) {
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
        if (!auth()->check() || !auth()->user()->isAdmin()) {
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
     * Export registrations to CSV
     */
    public function export(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Access denied');
        }

        $query = UserRegistration::query();

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

            fputcsv($file, [
                'ID', 'Username', 'Email', 'First Name', 'Last Name',
                'User Type', 'Status', 'Contact Number', 'Barangay',
                'Created At', 'Approved At', 'Rejected At', 'Last Login'
            ]);

            foreach ($registrations as $registration) {
                fputcsv($file, [
                    $registration->id,
                    $registration->username,
                    $registration->email,
                    $registration->first_name,
                    $registration->last_name,
                    $registration->user_type,
                    $registration->status,
                    $registration->contact_number,
                    $registration->barangay,
                    $registration->created_at ? $registration->created_at->format('Y-m-d H:i:s') : '',
                    $registration->approved_at ? $registration->approved_at->format('Y-m-d H:i:s') : '',
                    $registration->rejected_at ? $registration->rejected_at->format('Y-m-d H:i:s') : '',
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
                'message' => 'Please check all required fields.',
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
                'message' => 'Password change failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Update user profile
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
                'max:20',
                'regex:/^(\+639|09)\d{9}$/'
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

            $registration->update(array_filter($updateData, function($value) {
                return $value !== null;
            }));

            $updatedUser = session('user');
            $updatedUser['username'] = $registration->username;
            $updatedUser['name'] = $registration->full_name ?? $registration->username;
            session(['user' => $updatedUser]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $registration->id,
                    'username' => $registration->username,
                    'email' => $registration->email,
                    'contact_number' => $registration->contact_number,
                    'complete_address' => $registration->complete_address,
                    'barangay' => $registration->barangay,
                    'name' => $registration->full_name ?? $registration->username,
                    'status' => $registration->status,
                    'username_changed_at' => $registration->username_changed_at,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Profile update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Redirect to Facebook OAuth page
     */
    public function redirectToFacebook()
    {
        try {
            return Socialite::driver('facebook')
                ->scopes(['email', 'public_profile'])
                ->redirect();
        } catch (\Exception $e) {
            \Log::error('Facebook redirect error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Facebook login is temporarily unavailable. Please try again later.');
        }
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function handleFacebookCallback(Request $request)
    {
        try {
            if ($request->has('_') && empty($request->get('_'))) {
                return redirect()->to($request->path());
            }

            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            \Log::info('Facebook user data:', [
                'id' => $facebookUser->id,
                'email' => $facebookUser->email,
                'name' => $facebookUser->name
            ]);

            if (empty($facebookUser->email)) {
                \Log::warning('Facebook login attempted without email permission');
                return redirect('/')->with('error', 'Email permission is required. Please try again and allow email access.');
            }

            $userRegistration = UserRegistration::where('email', $facebookUser->email)
                ->orWhere('facebook_id', $facebookUser->id)
                ->first();

            if (!$userRegistration) {
                $username = $this->generateUniqueUsername($facebookUser->name);

                $nameParts = explode(' ', trim($facebookUser->name));
                $firstName = $nameParts[0] ?? $facebookUser->name;
                $lastName = count($nameParts) > 1 ? end($nameParts) : '';

                $userRegistration = UserRegistration::create([
                    'username' => $username,
                    'email' => $facebookUser->email,
                    'password' => Hash::make(Str::random(32)),
                    'facebook_id' => $facebookUser->id,
                    'profile_image_url' => $facebookUser->avatar,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'status' => UserRegistration::STATUS_UNVERIFIED,
                    'terms_accepted' => true,
                    'privacy_accepted' => true,
                    'email_verified_at' => now(),
                ]);

                \Log::info('New user created via Facebook', [
                    'user_id' => $userRegistration->id,
                    'facebook_id' => $facebookUser->id,
                    'username' => $username
                ]);

            } else {
                if (!$userRegistration->facebook_id) {
                    $userRegistration->update([
                        'facebook_id' => $facebookUser->id,
                        'profile_image_url' => $facebookUser->avatar,
                        'email_verified_at' => $userRegistration->email_verified_at ?? now(),
                    ]);
                }

                $userRegistration->update(['last_login_at' => now()]);

                \Log::info('Existing user logged in via Facebook', [
                    'user_id' => $userRegistration->id,
                    'facebook_id' => $facebookUser->id
                ]);
            }

            $request->session()->regenerate();
            
            $request->session()->put('user', [
                'id' => $userRegistration->id,
                'username' => $userRegistration->username,
                'email' => $userRegistration->email,
                'name' => $userRegistration->full_name ?? $userRegistration->username,
                'user_type' => $userRegistration->user_type,
                'status' => $userRegistration->status,
                'profile_image' => $userRegistration->profile_image_url,
            ]);

            $request->session()->put('user_id', $userRegistration->id);
            $request->session()->put('user_email', $userRegistration->email);
            $request->session()->put('user_username', $userRegistration->username);

            $firstName = explode(' ', $userRegistration->first_name ?? $userRegistration->username)[0];
            $message = 'Welcome! Please complete your profile verification to access all services.';

            if ($userRegistration->status === UserRegistration::STATUS_APPROVED) {
                $message = 'Welcome back, ' . $firstName . '!';
            } elseif ($userRegistration->status === UserRegistration::STATUS_PENDING) {
                $message = 'Welcome! Your verification is being reviewed.';
            }

            return redirect('/')->with('success', $message);

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            \Log::error('Facebook OAuth state error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Login session expired. Please try again.');

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            \Log::error('Facebook API error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Unable to connect to Facebook. Please try again.');

        } catch (\Exception $e) {
            \Log::error('Facebook callback error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Facebook login failed. Please try again or use email login.');
        }
    }

    /**
     * Generate a unique username from Facebook name
     */
    private function generateUniqueUsername($name)
    {
        $baseUsername = strtolower(preg_replace('/[^a-z0-9_]/', '', str_replace(' ', '', $name)));

        if (strlen($baseUsername) < 3) {
            $baseUsername = 'user' . $baseUsername;
        }

        $baseUsername = substr($baseUsername, 0, 17);

        $username = $baseUsername;
        $counter = 1;

        while (UserRegistration::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}