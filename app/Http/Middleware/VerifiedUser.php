<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedUser
{
    /**
     * Handle an incoming request.
     * Ensures the user is logged in and has an approved/verified status
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user session exists
        if (!$request->session()->has('user')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Authentication required',
                    'message' => 'Please log in to access this service.',
                    'session_expired' => true,
                    'redirect' => '/?session_expired=true'
                ], 401);
            }

            return redirect('/?session_expired=true')->with('error', 'Please log in to access this service.');
        }

        // Get user data from session
        $user = $request->session()->get('user');

        // Validate user array structure
        if (!is_array($user) || !isset($user['id']) || !isset($user['username'])) {
            // Session is corrupted, clear it
            $request->session()->flush();
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid session',
                    'message' => 'Your session is invalid. Please log in again.',
                    'session_expired' => true,
                    'redirect' => '/?session_expired=true'
                ], 401);
            }

            return redirect('/?session_expired=true')->with('error', 'Your session is invalid. Please log in again.');
        }

        // Check if user has approved/verified status
        $userStatus = strtolower($user['status'] ?? 'unverified');
        
        // Allow approved and verified statuses
        $allowedStatuses = ['approved', 'verified'];
        
        if (!in_array($userStatus, $allowedStatuses)) {
            // Log the failed verification attempt
            \Log::warning('Unverified user attempted to access restricted service', [
                'user_id' => $user['id'] ?? null,
                'username' => $user['username'] ?? null,
                'status' => $user['status'] ?? null,
                'requested_url' => $request->getPathInfo(),
                'ip_address' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Account verification required',
                    'message' => 'Your account must be verified to access this service. Please complete your profile verification.',
                    'status' => $user['status'] ?? 'unverified',
                    'required_status' => 'approved',
                    'redirect' => '/?verification_required=true'
                ], 403);
            }

            return redirect('/?verification_required=true')
                ->with('error', 'Your account must be verified to access this service. Please complete your profile verification.');
        }

        // Update last activity timestamp (resets inactivity counter)
        $request->session()->put('last_activity', time());

        return $next($request);
    }
}