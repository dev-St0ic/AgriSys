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
            'unverified' => UserRegistration::unverified()->count(),
            'pending' => UserRegistration::pending()->count(),
            'approved' => UserRegistration::approved()->count(),
            'rejected' => UserRegistration::rejected()->count(),
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
     * Login for users (simplified for username-based auth)
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
                // Check if user status allows login
                if ($userRegistration->status === 'rejected') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been rejected. Please contact support for assistance.'
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

                // Determine redirect and message based on user status
                $redirectUrl = '/dashboard'; // Default user dashboard
                
                if ($userRegistration->status === 'unverified') {
                    $message = 'Welcome! You can start using our services.';
                } elseif ($userRegistration->status === 'pending') {
                    $message = 'Welcome! Your account is pending verification.';
                } elseif ($userRegistration->status === 'approved') {
                    $message = 'Welcome back! Login successful.';
                } else {
                    $message = 'Login successful! Welcome.';
                }

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
            'rejection_reason' => null
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
            'rejection_reason' => $request->reason ?? 'No reason provided'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Registration rejected successfully'
        ]);
    }

    /**
     * Delete registration
     */
    public function destroy($id)
    {
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

        /**
     * Submit profile verification
     */
    public function submitVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'middleName' => 'nullable|string|max:100',
            'extensionName' => 'nullable|string|max:20',
            'role' => 'required|in:farmer,fisherfolk,general,agri-entrepreneur,cooperative-member,government-employee',
            'contactNumber' => 'required|string|max:20',
            'barangay' => 'required|string|max:100',
            'completeAddress' => 'required|string',
            'idFront' => 'required|file|image|max:5120', // 5MB max
            'idBack' => 'required|file|image|max:5120',
            'locationProof' => 'required|file|image|max:5120',
        ]);

        if ($validator->fails()) {
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

            if ($request->hasFile('idFront')) {
                $idFrontPath = $request->file('idFront')->store('verification/id_front', 'public');
            }

            if ($request->hasFile('idBack')) {
                $idBackPath = $request->file('idBack')->store('verification/id_back', 'public');
            }

            if ($request->hasFile('locationProof')) {
                $locationProofPath = $request->file('locationProof')->store('verification/location_proof', 'public');
            }

            // Update user registration with verification data
            $userRegistration->update([
                'first_name' => $request->firstName,
                'middle_name' => $request->middleName,
                'last_name' => $request->lastName,
                'name_extension' => $request->extensionName,
                'user_type' => $request->role,
                'phone' => $request->contactNumber,
                'barangay' => $request->barangay,
                'complete_address' => $request->completeAddress,
                'id_front_path' => $idFrontPath,
                'id_back_path' => $idBackPath,
                'place_document_path' => $locationProofPath,
                'status' => 'pending', // Change status to pending review
            ]);

            // Update session data
            $request->session()->put('user', [
                'id' => $userRegistration->id,
                'username' => $userRegistration->username,
                'email' => $userRegistration->email,
                'name' => $userRegistration->full_name,
                'user_type' => $userRegistration->user_type,
                'status' => 'pending'
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
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

/**
 * Get registration details for admin view (FIXED)
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
            'phone' => $registration->phone,
            'contact_number' => $registration->phone,         
            'complete_address' => $registration->complete_address,
            'barangay' => $registration->barangay,
            'user_type' => $registration->user_type,
            'status' => $registration->status,
            'date_of_birth' => $registration->date_of_birth ? $registration->date_of_birth->format('M d, Y') : null,
            'gender' => $registration->gender,
            'occupation' => $registration->occupation,
            'organization' => $registration->organization,
            'emergency_contact_name' => $registration->emergency_contact_name,
            'emergency_contact_phone' => $registration->emergency_contact_phone,
            
            // Document paths - FIXED to handle both field names
            'place_document_path' => $registration->place_document_path,
            'location_document_path' => $registration->place_document_path, // Alias for consistency
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
            'approved_at' => $registration->approved_at ? $registration->approved_at->format('M d, Y g:i A') : null,
            'rejected_at' => $registration->rejected_at ? $registration->rejected_at->format('M d, Y g:i A') : null,
            'approved_by' => $registration->approvedBy ? $registration->approvedBy->name : null,
        ]
    ]);
}

/**
 * View uploaded document (FIXED)
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
    
    switch ($type) {
        case 'location':
            $documentPath = $registration->place_document_path;
            break;
        case 'id_front':
            $documentPath = $registration->id_front_path;
            break;
        case 'id_back':
            $documentPath = $registration->id_back_path;
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
            'message' => 'Document not found for this registration'
        ], 404);
    }

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
            'message' => 'Document file not found on server. Path: ' . $documentPath
        ], 404);
    }

    try {
        // Generate the public URL for the document
        $documentUrl = \Storage::disk('public')->url($documentPath);
        
        // Log successful document access
        \Log::info("Document accessed successfully", [
            'registration_id' => $id,
            'document_type' => $type,
            'document_path' => $documentPath,
            'document_url' => $documentUrl,
            'admin_user' => auth()->user()->email
        ]);
        
        return response()->json([
            'success' => true,
            'document_url' => $documentUrl,
            'document_path' => $documentPath,
            'document_type' => $type,
            'file_exists' => true
        ]);
        
    } catch (\Exception $e) {
        \Log::error("Error generating document URL", [
            'registration_id' => $id,
            'document_type' => $type,
            'document_path' => $documentPath,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error accessing document: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Update registration status (FIXED - Add this method if missing)
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
            $updateData['rejection_reason'] = $request->remarks;
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
            'message' => 'Registration status updated successfully'
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
 * Get statistics for admin dashboard (Add this if missing)
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
        'with_documents' => UserRegistration::whereNotNull('place_document_path')
            ->orWhereNotNull('id_front_path')
            ->orWhereNotNull('id_back_path')
            ->count(),
    ];

    return response()->json([
        'success' => true,
        'data' => $stats
    ]);
}

}