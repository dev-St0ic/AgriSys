<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSession
{
    /**
     * Handle an incoming request.
     * Ensures the user is logged in (checks session exists)
     * Does NOT check verification status (use VerifiedUser for that)
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
                    'message' => 'Please log in to access this resource.',
                    'session_expired' => true,
                    'redirect' => '/?session_expired=true'
                ], 401);
            }

            return redirect('/?session_expired=true')
                ->with('error', 'Please log in to access this page.');
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

            return redirect('/?session_expired=true')
                ->with('error', 'Your session is invalid. Please log in again.');
        }

        // Update last activity timestamp (resets inactivity counter)
        $request->session()->put('last_activity', time());

        return $next($request);
    }
}