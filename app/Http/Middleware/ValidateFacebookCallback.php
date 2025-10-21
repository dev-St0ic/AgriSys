<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateFacebookCallback
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verify that the request is coming from Facebook OAuth
        if ($request->has('error')) {
            \Log::warning('Facebook OAuth error', [
                'error' => $request->get('error'),
                'error_description' => $request->get('error_description'),
                'ip' => $request->ip()
            ]);
            
            return redirect('/')
                ->with('error', 'Facebook login was cancelled or denied.');
        }
        
        // Verify state parameter to prevent CSRF attacks
        if (!$request->has('code') && !$request->has('state')) {
            \Log::warning('Invalid Facebook callback - missing code or state', [
                'ip' => $request->ip()
            ]);
            
            return redirect('/')
                ->with('error', 'Invalid Facebook login attempt.');
        }
        
        return $next($request);
    }
}