<?php

namespace App\Http\Controllers;

use App\Models\UserRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\RegistrationApprovedNotification;
use App\Notifications\RegistrationRejectedNotification;

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

        // Apply filters
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

        // Get statistics
        $stats = [
            'total' => UserRegistration::count(),
            'unverified' => UserRegistration::where('status', 'unverified')->count(),
            'pending' => UserRegistration::where('status', 'pending')->count(),
            'approved' => UserRegistration::where('status', 'approved')->count(),
            'rejected' => UserRegistration::where('status', 'rejected')->count(),
            'banned' => UserRegistration::where('status', 'banned')->count(),
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
        
        // Check if username exists in user_registration table
        $exists = UserRegistration::where('username', $username)->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Username already taken' : 'Username available'
        ]);
    }

    /**
     * SIMPLIFIED REGISTRATION - Username, Email, Password only
     */
    public function register(Request $request)
    {
        // Debug: Log incoming data
        \Log::info('Registration attempt:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/|unique:user_registration,username',
            'email' => 'required|string|email|max:255|unique:user_registration,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'terms_accepted' => 'required|boolean',
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
            // Debug: Log validation errors
            \Log::error('Validation failed:', $validator->errors()->toArray());
            
            return response()->json([
                'success' => false,
                'message' => 'Please check the form for errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user registration
            $registrationData = [
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password, // Will be hashed by model mutator
                'status' => 'unverified',
                'terms_accepted' => (bool)$request->terms_accepted,
                'privacy_accepted' => true,
                'registration_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referral_source' => $this->getReferralSource($request),
            ];

            $registration = UserRegistration::create($registrationData);

            \Log::info('New user registration created', [
                'id' => $registration->id,
                'username' => $registration->username,
                'email' => $registration->email,
                'status' => $registration->status,
                'ip' => $request->ip()
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
            \Log::error('Registration failed: ' . $e->getMessage(), [
                'email' => $request->email,
                'username' => $request->username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * UPDATED LOGIN - Only ban "banned" users, allow "rejected" users to retry verification
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

            // Try to find user in user_registration table (by username or email)
            $userRegistration = UserRegistration::where('email', $loginField)
                ->orWhere('username', $loginField)
                ->first();

            if ($userRegistration && Hash::check($password, $userRegistration->password)) {
                
                // UPDATED: Only block permanently banned users, allow all others including "rejected"
                if ($userRegistration->status === 'banned' || $userRegistration->status === 'permanently_banned') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been permanently banned. Please contact support for assistance.'
                    ], 403);
                }

                // Store user data in 'user' session key (this is what your middleware expects)
                $request->session()->put('user', [
                    'id' => $userRegistration->id,
                    'username' => $userRegistration->username,
                    'email' => $userRegistration->email,
                    'name' => $userRegistration->full_name ?? $userRegistration->username,
                    'user_type' => $userRegistration->user_type,
                    'status' => $userRegistration->status
                ]);
                
                // Also store individual keys for backward compatibility if needed
                $request->session()->put('user_id', $userRegistration->id);
                $request->session()->put('user_email', $userRegistration->email);
                $request->session()->put('user_username', $userRegistration->username);
                $request->session()->put('user_name', $userRegistration->full_name ?? $userRegistration->username);
                $request->session()->put('user_type', $userRegistration->user_type);
                $request->session()->put('user_status', $userRegistration->status);

                // Update last login
                $userRegistration->update(['last_login_at' => now()]);

                // UPDATED: Determine message based on user status with proper rejected handling
                $redirectUrl = '/dashboard'; // Default user dashboard
                
                switch ($userRegistration->status) {
                    case 'unverified':
                        $message = 'Welcome! You can start using our services. Complete profile verification for full access.';
                        break;
                    case 'pending':
                    case 'pending_verification':
                        $message = 'Welcome! Your verification is being reviewed. You\'ll be notified once approved.';
                        break;
                    case 'approved':
                    case 'verified':
                        $message = 'Welcome back! Login successful.';
                        break;
                    case 'rejected':
                        $message = 'Welcome! Your previous verification was rejected. You can submit updated documents for review.';
                        break;
                    default:
                        $message = 'Login successful! Welcome.';
                        break;
                }

                \Log::info('User login successful', [
                    'user_id' => $userRegistration->id,
                    'username' => $userRegistration->username,
                    'status' => $userRegistration->status,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => $redirectUrl,
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

            // If not found in registrations, try admin users table
            $user = User::where('email', $loginField)->first();
            
            if ($user && Hash::check($password, $user->password)) {
                Auth::login($user);
                
                // For admin users, also store in session format expected by middleware
                $request->session()->put('user', [
                    'id' => $user->id,
                    'username' => $user->email, // Admin users might not have username
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
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // Clear all session data
        $request->session()->flush();
        
        // If it's an admin user, logout from Laravel auth
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
     * UPDATED: Submit profile verification - FIXED TO MATCH FRONTEND FORM
     */
    public function submitVerification(Request $request)
    {
        // Log incoming request for debugging
        \Log::info('Verification submission attempt', [
            'user_id' => session('user.id'),
            'form_fields' => array_keys($request->all()),
            'files' => array_keys($request->allFiles())
        ]);

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'middleName' => 'nullable|string|max:100',
            'extensionName' => 'nullable|string|max:20',
            'role' => 'required|in:farmer,fisherfolk,general,agri-entrepreneur,cooperative-member,government-employee',
            'contactNumber' => 'required|string|max:20',
            'dateOfBirth' => 'required|date|before:today|after:' . now()->subYears(100)->toDateString(),
            'barangay' => 'required|string|max:100',
            'completeAddress' => 'required|string',
            'idFront' => 'required|file|image|max:5120', // 5MB max
            'idBack' => 'required|file|image|max:5120',
            'locationProof' => 'required|file|image|max:5120',
        ], [
            'firstName.required' => 'First name is required',
            'lastName.required' => 'Last name is required',
            'role.required' => 'Role is required',
            'role.in' => 'Please select a valid role',
            'contactNumber.required' => 'Contact number is required',
            'dateOfBirth.required' => 'Date of birth is required',
            'dateOfBirth.before' => 'Date of birth must be before today',
            'dateOfBirth.after' => 'Please enter a valid date of birth',
            'barangay.required' => 'Barangay is required',
            'completeAddress.required' => 'Complete address is required',
            'idFront.required' => 'ID front image is required',
            'idFront.image' => 'ID front must be an image file',
            'idFront.max' => 'ID front image must be less than 5MB',
            'idBack.required' => 'ID back image is required',
            'idBack.image' => 'ID back must be an image file',
            'idBack.max' => 'ID back image must be less than 5MB',
            'locationProof.required' => 'Location proof image is required',
            'locationProof.image' => 'Location proof must be an image file',
            'locationProof.max' => 'Location proof image must be less than 5MB',
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

        // MANUAL: Validate Philippine mobile format to avoid preg_match delimiter issues in Validator rules
        $contactNumber = $request->input('contactNumber', '');
        if (!preg_match('/^(\+639|09)\d{9}$/', $contactNumber)) {
            \Log::warning('Invalid contact number format in verification submission', [
                'user_id' => session('user.id'),
                'contact' => $contactNumber
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX).',
                'errors' => ['contactNumber' => ['Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX).']]
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

            // Handle file uploads with proper error handling
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

            // Calculate age from date of birth
            $dateOfBirth = new \DateTime($request->dateOfBirth);
            $age = $dateOfBirth->diff(new \DateTime())->y;

            // Validate minimum age
            if ($age < 18) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be at least 18 years old to register.'
                ], 422);
            }

            // Update user registration with verification data - ALIGNED WITH DATABASE SCHEMA
            $updateData = [
                'first_name' => trim($request->firstName),
                'middle_name' => trim($request->middleName),
                'last_name' => trim($request->lastName),
                'name_extension' => trim($request->extensionName),
                'user_type' => $request->role, // Maps to user_type in DB
                'contact_number' => trim($request->contactNumber), // Maps to contact_number in DB
                'date_of_birth' => $request->dateOfBirth, // Maps to date_of_birth in DB
                'age' => $age, // Calculated from date_of_birth
                'barangay' => $request->barangay,
                'complete_address' => trim($request->completeAddress),
                'status' => 'pending', // Change status to pending review
                // Clear any previous rejection data when resubmitting
                'rejection_reason' => null,
                'rejected_at' => null,
            ];

            // Add file paths if uploads were successful
            if ($idFrontPath) {
                $updateData['id_front_path'] = $idFrontPath;
            }
            if ($idBackPath) {
                $updateData['id_back_path'] = $idBackPath;
            }
            if ($locationProofPath) {
                $updateData['location_document_path'] = $locationProofPath; // Maps to location_document_path in DB
            }

            $userRegistration->update($updateData);

            // Update session data with new status
            $request->session()->put('user', [
                'id' => $userRegistration->id,
                'username' => $userRegistration->username,
                'email' => $userRegistration->email,
                'name' => $userRegistration->full_name,
                'user_type' => $userRegistration->user_type,
                'status' => 'pending' // Updated status
            ]);

            \Log::info('Verification submitted successfully', [
                'user_id' => $userId,
                'user_type' => $request->role,
                'previous_status' => $userRegistration->getOriginal('status'),
                'new_status' => 'pending',
                'files_uploaded' => [
                    'id_front' => !empty($idFrontPath),
                    'id_back' => !empty($idBackPath),
                    'location_proof' => !empty($locationProofPath)
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification submitted successfully! Your account will be reviewed within 2-3 business days.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Verification submission failed: ' . $e->getMessage(), [
                'user_id' => $userId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Verification submission failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Get registration details for admin view - UPDATED FOR DATABASE ALIGNMENT
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
                'occupation' => $registration->occupation,
                'organization' => $registration->organization,
                'emergency_contact_name' => $registration->emergency_contact_name,
                'emergency_contact_phone' => $registration->emergency_contact_phone,
                
                // Document paths
                'location_document_path' => $registration->location_document_path,
                'id_front_path' => $registration->id_front_path,
                'id_back_path' => $registration->id_back_path,
                
                'email_verified' => $registration->hasVerifiedEmail(),
                'terms_accepted' => $registration->terms_accepted,
                'privacy_accepted' => $registration->privacy_accepted,
                'created_at' => $registration->created_at->format('M d, Y g:i A'),
                'last_login_at' => $registration->last_login_at ? $registration->last_login_at->format('M d, Y g:i A') : null,
                'registration_ip' => $registration->registration_ip,
                'referral_source' => $registration->referral_source,
                'rejection_reason' => $registration->rejection_reason,
                'ban_reason' => $registration->ban_reason ?? null,
                'approved_at' => $registration->approved_at ? $registration->approved_at->format('M d, Y g:i A') : null,
                'rejected_at' => $registration->rejected_at ? $registration->rejected_at->format('M d, Y g:i A') : null,
                'banned_at' => $registration->banned_at ? $registration->banned_at->format('M d, Y g:i A') : null,
                'approved_by' => $registration->approvedBy ? $registration->approvedBy->name : null,
            ]
        ]);
    }

    /**
     * FIXED: View uploaded document with proper file handling and security checks
     */
    public function viewDocument($id, $type)
    {
        // Check admin authentication
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
            // Check if file exists in storage
            if (!\Storage::disk('public')->exists($documentPath)) {
                \Log::error("Document file not found in storage", [
                    'registration_id' => $id,
                    'document_type' => $type,
                    'document_path' => $documentPath,
                    'storage_path' => storage_path('app/public/' . $documentPath)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Document file not found on server'
                ], 404);
            }

            // Get file info
            $filePath = storage_path('app/public/' . $documentPath);
            $fileSize = filesize($filePath);
            $mimeType = mime_content_type($filePath);
            $fileName = basename($documentPath);
            
            // Generate the public URL for the document
            $documentUrl = asset('storage/' . $documentPath);
            
            // Check if it's an image
            $isImage = str_starts_with($mimeType, 'image/');
            
            // Log successful document access
            \Log::info("Document accessed successfully", [
                'registration_id' => $id,
                'document_type' => $type,
                'document_path' => $documentPath,
                'document_url' => $documentUrl,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'admin_user' => auth()->user()->email
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
                'document_path' => $documentPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error accessing document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ALTERNATIVE: Direct file serving (if public URL doesn't work)
     */
    public function serveDocument($id, $type)
    {
        // Check admin authentication
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
            'ban_reason' => null,
            'rejected_at' => null,
            'banned_at' => null
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
            'ban_reason' => null,
            'approved_at' => null,
            'banned_at' => null
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Registration rejected successfully'
        ]);
    }

    /**
     * UPDATED: Ban user (permanent login block)
     */
    public function banUser(Request $request, $id)
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
        
        $registration->update([
            'status' => 'banned',
            'banned_at' => now(),
            'approved_by' => auth()->id(),
            'ban_reason' => $request->reason ?? 'Banned by administrator',
            'approved_at' => null,
            'rejected_at' => null,
            'rejection_reason' => null
        ]);
        
        \Log::warning('User account banned', [
            'registration_id' => $id,
            'username' => $registration->username,
            'email' => $registration->email,
            'banned_by' => auth()->user()->email,
            'reason' => $request->reason ?? 'No reason provided'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'User account banned successfully'
        ]);
    }

    /**
     * UPDATED: Unban user (restore access)
     */
    public function unbanUser(Request $request, $id)
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
        
        $registration->update([
            'status' => 'unverified', // Reset to unverified so they can resubmit verification
            'banned_at' => null,
            'ban_reason' => null,
        ]);
        
        \Log::info('User account unbanned', [
            'registration_id' => $id,
            'username' => $registration->username,
            'email' => $registration->email,
            'unbanned_by' => auth()->user()->email
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'User account unbanned successfully'
        ]);
    }

    /**
     * Update registration status - UPDATED TO AUTO-REFRESH with new status options
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
            'status' => 'required|in:unverified,pending,approved,rejected,banned',
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
                $updateData['banned_at'] = null;
                $updateData['rejection_reason'] = null;
                $updateData['ban_reason'] = null;
            } elseif ($request->status === 'rejected') {
                $updateData['rejected_at'] = now();
                $updateData['approved_at'] = null;
                $updateData['banned_at'] = null;
                $updateData['rejection_reason'] = $request->remarks;
                $updateData['ban_reason'] = null;
            } elseif ($request->status === 'banned') {
                $updateData['banned_at'] = now();
                $updateData['approved_at'] = null;
                $updateData['rejected_at'] = null;
                $updateData['ban_reason'] = $request->remarks;
                $updateData['rejection_reason'] = null;
            } else {
                // For unverified/pending status
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
                'remarks' => $request->remarks
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration status updated successfully',
                'auto_refresh' => true // Signal frontend to refresh
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update registration status', [
                'registration_id' => $id,
                'error' => $e->getMessage(),
                'admin_user' => auth()->user()->email
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
     * Get statistics for admin dashboard - UPDATED to include banned users
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
            'banned' => UserRegistration::where('status', 'banned')->count(),
            'recent' => UserRegistration::where('created_at', '>=', now()->subDays(7))->count(),
            'email_verified' => UserRegistration::whereNotNull('email_verified_at')->count(),
            'with_documents' => UserRegistration::whereNotNull('location_document_path')
                ->orWhereNotNull('id_front_path')
                ->orWhereNotNull('id_back_path')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get user profile for API
     */
    public function getUserProfile(Request $request)
    {
        $userId = session('user.id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to access profile'
            ], 401);
        }

        $registration = UserRegistration::find($userId);
        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $registration->id,
                'username' => $registration->username,
                'email' => $registration->email,
                'full_name' => $registration->full_name,
                'status' => $registration->status,
                'user_type' => $registration->user_type,
                'created_at' => $registration->created_at->format('M d, Y'),
                'last_login_at' => $registration->last_login_at ? $registration->last_login_at->format('M d, Y') : null,
            ]
        ]);
    }

    /**
     * Get user applications for API
     */
    public function getUserApplications(Request $request)
    {
        $userId = session('user.id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to view applications'
            ], 401);
        }

        // This would typically query application tables
        // For now, return mock data
        $applications = [
            [
                'id' => 1,
                'type' => 'RSBSA Registration',
                'status' => 'approved',
                'date' => '2025-01-15',
                'description' => 'Registry System for Basic Sectors in Agriculture enrollment'
            ],
            [
                'id' => 2,
                'type' => 'Seedlings Request',
                'status' => 'pending',
                'date' => '2025-01-18',
                'description' => 'Request for vegetable seedlings'
            ]
        ];

        return response()->json([
            'success' => true,
            'applications' => $applications
        ]);
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

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'contact_number' => 'sometimes|string|max:20',
            'complete_address' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $registration->update($request->only([
                'first_name',
                'last_name', 
                'contact_number',
                'complete_address'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
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
     * Get public statistics (no auth required)
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
                    'ban_reason' => null,
                    'rejected_at' => null,
                    'banned_at' => null
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
                    'ban_reason' => null,
                    'approved_at' => null,
                    'banned_at' => null
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
     * ADDED: Bulk ban registrations
     */
    public function bulkBan(Request $request)
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
        if (!auth()->check() || !auth()->user()->isAdmin()) {
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
                'ID', 'Username', 'Email', 'First Name', 'Last Name', 
                'User Type', 'Status', 'Contact Number', 'Barangay', 
                'Created At', 'Approved At', 'Rejected At', 'Banned At', 'Last Login'
            ]);

            // CSV Data
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
                    $registration->banned_at ? $registration->banned_at->format('Y-m-d H:i:s') : '',
                    $registration->last_login_at ? $registration->last_login_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper method to determine referral source
     */
    private function getReferralSource($request)
    {
        $referer = $request->headers->get('referer');
        if (!$referer) {
            return 'direct';
        }

        $host = parse_url($referer, PHP_URL_HOST);
        if (!$host) {
            return 'unknown';
        }

        // Common social media and search engines
        $sources = [
            'facebook.com' => 'facebook',
            'google.com' => 'google',
            'twitter.com' => 'twitter',
            'instagram.com' => 'instagram',
            'youtube.com' => 'youtube',
            'linkedin.com' => 'linkedin',
        ];

        foreach ($sources as $domain => $source) {
            if (str_contains($host, $domain)) {
                return $source;
            }
        }

        return $host;
    }
}