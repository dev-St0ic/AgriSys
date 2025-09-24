<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('user')) {
            return redirect('/')->with('error', 'Please log in to access this page.');
        }
        
        return $next($request);
    }
}