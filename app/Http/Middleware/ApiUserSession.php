<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiUserSession
{
    /**
     * Handle an incoming request.
     * Ensures the user is logged in for API routes
     * Always returns JSON responses
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user session exists
        if (!$request->session()->has('user')) {
            return response()->json([
                'success' => false,
                'error' => 'Authentication required',
                'message' => 'Please log in to access this resource.',
                'session_expired' => true,
                'redirect' => '/?session_expired=true'
            ], 401);
        }

        // Get user data from session
        $user = $request->session()->get('user');

        // Validate user array structure
        if (!is_array($user) || !isset($user['id']) || !isset($user['username'])) {
            // Session is corrupted, clear it
            $request->session()->flush();
            $request->session()->regenerate();

            return response()->json([
                'success' => false,
                'error' => 'Invalid session',
                'message' => 'Your session is invalid. Please log in again.',
                'session_expired' => true,
                'redirect' => '/?session_expired=true'
            ], 401);
        }

        // Update last activity timestamp (resets inactivity counter)
        $request->session()->put('last_activity', time());

        return $next($request);
    }
}