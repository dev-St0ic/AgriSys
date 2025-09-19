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
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_]+$/|unique:user_registration,username',
            'email' => 'required|string|email|max:255|unique:user_registration,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'terms_accepted' => 'required|accepted',
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
            'terms_accepted.accepted' => 'You must accept the Terms of Service',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the form for errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user registration with minimal required info
            // Password will be automatically hashed by the model mutator
            $registrationData = [
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password, // Will be hashed by model mutator
                'status' => 'unverified', // Start as unverified
                'terms_accepted' => true,
                'privacy_accepted' => true,
                'registration_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referral_source' => $this->getReferralSource($request),
            ];

            // Create user registration
            $registration = UserRegistration::create($registrationData);

            // Log the creation for admin tracking
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
            'username' => 'required|string', // Changed from 'login' to 'username'
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

                // Create session for the user
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
     * Get registration details for admin view
     */
    public function getRegistration($id)
    {
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
                'full_name' => $registration->full_name ?? $registration->username,
                'phone' => $registration->phone,
                'address' => $registration->complete_address,
                'barangay' => $registration->barangay,
                'user_type' => $registration->user_type,
                'status' => $registration->status,
                'date_of_birth' => $registration->date_of_birth,
                'gender' => $registration->gender,
                'occupation' => $registration->occupation,
                'organization' => $registration->organization,
                'emergency_contact_name' => $registration->emergency_contact_name,
                'emergency_contact_phone' => $registration->emergency_contact_phone,
                'email_verified' => $registration->hasVerifiedEmail(),
                'created_at' => $registration->created_at->format('M d, Y g:i A'),
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
}