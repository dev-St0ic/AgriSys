<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSessionExpiration
{
    
    /**
     * Session timeout in seconds
     * 1800 seconds = 30 minutes
     */
    const SESSION_TIMEOUT = 1800; // 30 minutes in seconds

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip session check for certain routes (public routes)
        $skipRoutes = [
            '/',
            '/login',
            '/auth/login',
            '/auth/register',
            '/auth/logout',
            '/auth/check-username',
            '/auth/check-contact',
            '/csrf-token',
            '/api/events',
            '/api/slideshow/active',
            '/api/system-status',
            '/api/registration/stats',
            '/auth/forgot-password/send-otp',
            '/auth/forgot-password/verify-otp',
            '/auth/forgot-password/reset',
            '/auth/forgot-password/resend-otp',
            '/storage/',
            '/privacy-policy',
            '/terms-of-service',
            '/debug/',
        ];

        // Check if current route should skip session check
        foreach ($skipRoutes as $route) {
            if ($request->is($route) || str_starts_with($request->getPathInfo(), $route)) {
                return $next($request);
            }
        }

        // Check if user is logged in via session
        if ($request->session()->has('user')) {
            $lastActivity = $request->session()->get('last_activity');
            $now = time();

            // Initialize last activity if not set
            if (!$lastActivity) {
                $request->session()->put('last_activity', $now);
                return $next($request);
            }

            // Check if session has expired (SESSION_TIMEOUT seconds of inactivity)
            $timeoutSeconds = self::SESSION_TIMEOUT; // Already in seconds
            $timeSinceActivity = $now - $lastActivity;

            if ($timeSinceActivity > $timeoutSeconds) {
                // Session has expired - clear it completely
                $user = $request->session()->get('user');
                \Log::warning('Session expired due to inactivity', [
                    'user_id' => $user['id'] ?? null,
                    'username' => $user['username'] ?? null,
                    'inactive_for_seconds' => $timeSinceActivity,
                    'timeout_seconds' => $timeoutSeconds,
                    'ip_address' => $request->ip()
                ]);

                // Clear the entire session
                $request->session()->flush();
                $request->session()->regenerate();

                // If it's an AJAX request, return JSON
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your session has expired. Please log in again.',
                        'session_expired' => true,
                        'redirect' => '/'
                    ], 401);
                }

                // For regular requests, redirect to home WITHOUT the warning message
                // The JavaScript session manager will handle the notification
                return redirect('/')->with('session_expired_flag', true);
            }

            // Update last activity timestamp
            $request->session()->put('last_activity', $now);
        }

        return $next($request);
    }
}