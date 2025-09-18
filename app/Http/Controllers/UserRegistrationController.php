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
     * This is for the admin panel to manage all user registrations
     */
    public function index(Request $request)
    {
        // Ensure only admins can access this
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
            'pending' => UserRegistration::pending()->count(),
            'approved' => UserRegistration::approved()->count(),
            'rejected' => UserRegistration::rejected()->count(),
            'verified' => UserRegistration::emailVerified()->count(),
            'unverified' => UserRegistration::emailUnverified()->count(),
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
     * PUBLIC REGISTRATION - This is for users registering from the frontend
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Personal Information
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:user_registration,email',
            'date_of_birth' => 'nullable|date|before:today|after:1900-01-01',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            
            // Professional Information
            'user_type' => 'required|in:farmer,fisherfolk,general',
            'occupation' => 'nullable|string|max:100',
            'organization' => 'nullable|string|max:150',
            
            // Emergency Contact
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            
            // Account Security
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            
            // Terms and Consent
            'terms_accepted' => 'required|accepted',
            'privacy_accepted' => 'required|accepted',
            'marketing_consent' => 'boolean',
        ], [
            'firstname.required' => 'First name is required',
            'lastname.required' => 'Last name is required',
            'email.required' => 'Email address is required',
            'email.unique' => 'This email is already registered',
            'user_type.required' => 'Please select your user type',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'terms_accepted.accepted' => 'You must accept the Terms of Service',
            'privacy_accepted.accepted' => 'You must accept the Privacy Policy',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate verification token
            $verificationToken = Str::random(64);

            // Get additional tracking data
            $registrationData = $request->only([
                'firstname', 'lastname', 'email', 'date_of_birth', 'gender',
                'phone', 'address', 'user_type', 'occupation', 'organization',
                'emergency_contact_name', 'emergency_contact_phone',
                'terms_accepted', 'privacy_accepted', 'marketing_consent'
            ]);

            // Add system fields
            $registrationData['password'] = $request->password; // Will be hashed by model
            $registrationData['verification_token'] = $verificationToken;
            $registrationData['status'] = 'pending';
            $registrationData['registration_ip'] = $request->ip();
            $registrationData['user_agent'] = $request->userAgent();
            $registrationData['referral_source'] = $this->getReferralSource($request);

            // Create user registration
            $registration = UserRegistration::create($registrationData);

            // Send verification email
            try {
                $registration->notify(new EmailVerificationNotification($verificationToken));
            } catch (\Exception $e) {
                \Log::error('Failed to send verification email: ' . $e->getMessage());
                // Don't fail the registration if email fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Please check your email to verify your account before it can be reviewed.',
                'data' => [
                    'registration_id' => $registration->id,
                    'email' => $registration->email,
                    'status' => $registration->status,
                    'verification_required' => true
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Registration failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail($token)
    {
        $registration = UserRegistration::where('verification_token', $token)->first();

        if (!$registration) {
            return redirect('/')->with('error', 'Invalid verification token.');
        }

        if ($registration->hasVerifiedEmail()) {
            return redirect('/')->with('info', 'Email already verified. Your registration is pending admin approval.');
        }

        $registration->markEmailAsVerified();

        return redirect('/')->with('success', 'Email verified successfully! Your registration is now pending admin approval. You will be notified once approved.');
    }

    /**
     * Show specific registration details (Admin only)
     */
    public function show(UserRegistration $registration)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Access denied.');
        }
        
        return view('admin.registrations.show', compact('registration'));
    }

    /**
     * Get registration details as JSON (Admin only)
     */
    public function getRegistration($id)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $registration = UserRegistration::with('approvedBy')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $registration->id,
                'first_name' => $registration->first_name,
                'last_name' => $registration->last_name,
                'full_name' => $registration->full_name,
                'email' => $registration->email,
                'phone' => $registration->formatted_phone,
                'date_of_birth' => $registration->date_of_birth?->format('F j, Y'),
                'age' => $registration->age,
                'gender' => ucfirst($registration->gender ?? 'Not specified'),
                'address' => $registration->address ?? 'Not provided',
                'user_type' => $registration->user_type_display,
                'occupation' => $registration->occupation ?? 'Not specified',
                'organization' => $registration->organization ?? 'Not specified',
                'emergency_contact_name' => $registration->emergency_contact_name ?? 'Not provided',
                'emergency_contact_phone' => $registration->emergency_contact_phone ?? 'Not provided',
                'status' => $registration->status,
                'status_color' => $registration->status_color,
                'email_verified' => $registration->hasVerifiedEmail(),
                'email_verified_at' => $registration->email_verified_at?->format('M j, Y \a\t g:i A'),
                'terms_accepted' => $registration->terms_accepted,
                'privacy_accepted' => $registration->privacy_accepted,
                'marketing_consent' => $registration->marketing_consent,
                'registration_ip' => $registration->registration_ip,
                'user_agent' => $registration->user_agent,
                'referral_source' => $registration->referral_source,
                'created_at' => $registration->created_at->format('F j, Y \a\t g:i A'),
                'approved_at' => $registration->approved_at?->format('F j, Y \a\t g:i A'),
                'approved_by' => $registration->approvedBy?->name,
                'rejection_reason' => $registration->rejection_reason,
            ]
        ]);
    }

    /**
     * Approve a registration (Admin only)
     */
    public function approve(UserRegistration $registration)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$registration->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending registrations can be approved.'
            ], 400);
        }

        if (!$registration->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'User must verify their email before registration can be approved.'
            ], 400);
        }

        try {
            $registration->approve(auth()->id());

            // Send approval notification
            try {
                $registration->notify(new RegistrationApprovedNotification());
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration approved successfully! User has been notified via email.',
                'data' => [
                    'status' => $registration->status,
                    'approved_at' => $registration->approved_at->format('M j, Y \a\t g:i A'),
                    'approved_by' => auth()->user()->name
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to approve registration: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve registration. Please try again.'
            ], 500);
        }
    }

    /**
     * Reject a registration (Admin only)
     */
    public function reject(Request $request, UserRegistration $registration)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$registration->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending registrations can be rejected.'
            ], 400);
        }

        try {
            $registration->reject($request->reason, auth()->id());

            // Send rejection notification if requested
            if ($request->get('send_notification', true)) {
                try {
                    $registration->notify(new RegistrationRejectedNotification($request->reason));
                } catch (\Exception $e) {
                    \Log::error('Failed to send rejection email: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration rejected successfully.',
                'data' => [
                    'status' => $registration->status,
                    'rejection_reason' => $registration->rejection_reason,
                    'rejected_by' => auth()->user()->name
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to reject registration: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject registration. Please try again.'
            ], 500);
        }
    }

    /**
     * Update registration information (Admin only)
     */
    public function update(Request $request, UserRegistration $registration)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:user_registration,email,' . $registration->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:500',
            'user_type' => 'sometimes|required|in:farmer,fisherfolk,general',
            'occupation' => 'sometimes|nullable|string|max:100',
            'organization' => 'sometimes|nullable|string|max:150',
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
                'first_name', 'last_name', 'email', 'phone', 'address',
                'user_type', 'occupation', 'organization'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Registration updated successfully.',
                'data' => $registration->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update registration: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update registration. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete a registration (Admin only)
     */
    public function destroy(UserRegistration $registration)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $registration->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registration deleted successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to delete registration: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete registration. Please try again.'
            ], 500);
        }
    }

    /**
     * Bulk approve registrations (Admin only)
     */
    public function bulkApprove(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'registration_ids' => 'required|array|min:1',
            'registration_ids.*' => 'exists:user_registration,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration IDs provided'
            ], 422);
        }

        try {
            $registrations = UserRegistration::whereIn('id', $request->registration_ids)
                ->where('status', 'pending')
                ->whereNotNull('email_verified_at')
                ->get();

            $approved = 0;
            $failed = 0;

            foreach ($registrations as $registration) {
                try {
                    $registration->approve(auth()->id());
                    $registration->notify(new RegistrationApprovedNotification());
                    $approved++;
                } catch (\Exception $e) {
                    \Log::error('Failed to approve registration ID ' . $registration->id . ': ' . $e->getMessage());
                    $failed++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk approval completed. {$approved} approved, {$failed} failed.",
                'data' => [
                    'approved' => $approved,
                    'failed' => $failed
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk approval failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk approval failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Export registrations data (Admin only)
     */
    public function export(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        $query = UserRegistration::with('approvedBy');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $registrations = $query->orderBy('created_at', 'desc')->get();

        $filename = 'user_registrations_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function() use ($registrations) {
            $handle = fopen('php://output', 'w');

            // CSV headers
            fputcsv($handle, [
                'ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Date of Birth', 'Gender',
                'Address', 'User Type', 'Occupation', 'Organization', 'Emergency Contact Name',
                'Emergency Contact Phone', 'Status', 'Email Verified', 'Terms Accepted',
                'Privacy Accepted', 'Marketing Consent', 'Registration Date', 'Approved Date',
                'Approved By', 'Rejection Reason', 'Registration IP', 'Referral Source'
            ]);

            // Data rows
            foreach ($registrations as $reg) {
                fputcsv($handle, [
                    $reg->id,
                    $reg->first_name,
                    $reg->last_name,
                    $reg->email,
                    $reg->phone,
                    $reg->date_of_birth?->format('Y-m-d'),
                    $reg->gender,
                    $reg->address,
                    $reg->user_type,
                    $reg->occupation,
                    $reg->organization,
                    $reg->emergency_contact_name,
                    $reg->emergency_contact_phone,
                    $reg->status,
                    $reg->hasVerifiedEmail() ? 'Yes' : 'No',
                    $reg->terms_accepted ? 'Yes' : 'No',
                    $reg->privacy_accepted ? 'Yes' : 'No',
                    $reg->marketing_consent ? 'Yes' : 'No',
                    $reg->created_at->format('Y-m-d H:i:s'),
                    $reg->approved_at?->format('Y-m-d H:i:s'),
                    $reg->approvedBy?->name,
                    $reg->rejection_reason,
                    $reg->registration_ip,
                    $reg->referral_source
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get registration statistics (Admin only)
     */
    public function statistics()
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'total' => UserRegistration::count(),
            'pending' => UserRegistration::pending()->count(),
            'approved' => UserRegistration::approved()->count(),
            'rejected' => UserRegistration::rejected()->count(),
            'verified' => UserRegistration::emailVerified()->count(),
            'unverified' => UserRegistration::emailUnverified()->count(),
            'recent' => UserRegistration::where('created_at', '>=', now()->subDays(7))->count(),
            'this_month' => UserRegistration::whereMonth('created_at', now()->month)->count(),
            'by_type' => [
                'farmer' => UserRegistration::where('user_type', 'farmer')->count(),
                'fisherfolk' => UserRegistration::where('user_type', 'fisherfolk')->count(),
                'general' => UserRegistration::where('user_type', 'general')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
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
     * Bulk reject registrations (Admin only)
     */
    public function bulkReject(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'registration_ids' => 'required|array|min:1',
            'registration_ids.*' => 'exists:user_registration,id',
            'reason' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration IDs provided'
            ], 422);
        }

        try {
            $registrations = UserRegistration::whereIn('id', $request->registration_ids)
                ->where('status', 'pending')
                ->get();

            $rejected = 0;
            $failed = 0;

            foreach ($registrations as $registration) {
                try {
                    $registration->reject($request->reason, auth()->id());
                    $registration->notify(new RegistrationRejectedNotification($request->reason));
                    $rejected++;
                } catch (\Exception $e) {
                    \Log::error('Failed to reject registration ID ' . $registration->id . ': ' . $e->getMessage());
                    $failed++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk rejection completed. {$rejected} rejected, {$failed} failed.",
                'data' => [
                    'rejected' => $rejected,
                    'failed' => $failed
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk rejection failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk rejection failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Bulk delete registrations (Admin only)
     */
    public function bulkDelete(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'registration_ids' => 'required|array|min:1',
            'registration_ids.*' => 'exists:user_registration,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid registration IDs provided'
            ], 422);
        }

        try {
            $deleted = UserRegistration::whereIn('id', $request->registration_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deleted} registrations.",
                'data' => [
                    'deleted' => $deleted
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk deletion failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Check registration status by email (Public - for users to check their own status)
     */
    public function checkStatus($email)
    {
        $registration = UserRegistration::where('email', $email)->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'No registration found for this email address.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'email' => $registration->email,
                'status' => $registration->status,
                'email_verified' => $registration->hasVerifiedEmail(),
                'created_at' => $registration->created_at->format('M j, Y'),
                'message' => $this->getStatusMessage($registration)
            ]
        ]);
    }

    /**
     * Get status message for user
     */
    private function getStatusMessage(UserRegistration $registration)
    {
        if (!$registration->hasVerifiedEmail()) {
            return 'Please check your email and click the verification link to continue with your registration.';
        }

        switch ($registration->status) {
            case 'pending':
                return 'Your email has been verified and your registration is currently under review by our admin team.';
            case 'approved':
                return 'Congratulations! Your registration has been approved. You can now access our services.';
            case 'rejected':
                $reason = $registration->rejection_reason 
                    ? ' Reason: ' . $registration->rejection_reason 
                    : '';
                return 'Unfortunately, your registration has been rejected.' . $reason . ' Please contact us if you have questions.';
            default:
                return 'Registration status unknown. Please contact support.';
        }
    }
}