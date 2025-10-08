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
                    'message' => 'Please log in to access this service.'
                ], 401);
            }

            return redirect('/')->with('error', 'Please log in to access this service.');
        }

        // Get user data from session
        $user = $request->session()->get('user');

        // Check if user has approved/verified status
        if (!isset($user['status']) || $user['status'] !== 'approved') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Account verification required',
                    'message' => 'Your account must be verified to access this service. Please complete your profile verification.',
                    'status' => $user['status'] ?? 'unknown'
                ], 403);
            }

            return redirect('/')->with('error', 'Your account must be verified to access this service. Please complete your profile verification.');
        }

        return $next($request);
    }
}
