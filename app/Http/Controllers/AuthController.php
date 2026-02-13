<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TrainingApplication;
use App\Models\RequestCategory;
use App\Models\CategoryItem;
use App\Models\SeedlingRequest;
use App\Models\RsbsaApplication;
use App\Models\FishrApplication;
use App\Models\BoatrApplication;
use App\Models\TrainingRequest;
use Spatie\Activitylog\Models\Activity;


class AuthController extends Controller
{
   /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->refresh();

            if ($user->hasAdminPrivileges()) {

                $request->session()->regenerate();

                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice')
                        ->with('status', 'Please verify your email.');
                }

                // This is the correct place — $user is defined, event is 'login'
                activity()
                    ->causedBy($user)
                    ->event('login')
                    ->withProperties([
                        'role' => $user->role,
                        'name' => $user->name,
                        'email' => $user->email,
                        'success' => true,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ])
                    ->log('login - User (ID: ' . $user->id . ')');

                return redirect()->intended('/admin/dashboard')
                    ->with('success', 'Welcome back, ' . $user->name . '!');

            } else {

                // Log the failed attempt (non-admin tried to log in)
                activity()
                    ->event('login_failed')
                    ->withProperties([
                        'email' => $request->email,
                        'reason' => 'no_admin_privileges',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ])
                    ->log('login_failed - User (ID: ' . $user->id . ')');

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'You do not have admin privileges.',
                ])->onlyInput('email');
            }
        }

        // Log failed credentials attempt (user not found / wrong password)
        activity()
            ->event('login_failed')
            ->withProperties([
                'email' => $request->email,
                'reason' => 'invalid_credentials',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('login_failed - unknown user');

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
        
    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log logout before actually logging out
        if ($user) {
            activity()
                ->causedBy($user)
                ->event('logout')
                ->withProperties([
                    'role' => $user->role,
                    'name' => $user->name,
                    'email' => $user->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('logout - User (ID: ' . $user->id . ')');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

}
