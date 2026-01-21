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

    // Logout any existing user first
    Auth::logout();
    $request->session()->flush();
    $request->session()->regenerate();

    if (Auth::attempt($credentials)) {
        /** @var User $user */
        $user = Auth::user();
        $user->refresh();

        // Check if user has admin privileges
        if ($user->hasAdminPrivileges()) {
            // Regenerate session again to prevent session fixation attacks
            $request->session()->regenerate();

            // Log successful login
            $this->logActivity('login', 'User', $user->id, [
                'role' => $user->role,
                'success' => true
            ]);

            return redirect()->intended('/admin/dashboard')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        } else {
            // User exists but doesn't have admin privileges
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Log failed login attempt (no admin privileges)
            $this->logActivity('login_failed', 'User', null, [
                'email' => $credentials['email'],
                'reason' => 'No admin privileges'
            ]);

            return back()->withErrors([
                'email' => 'You do not have admin privileges.',
            ])->onlyInput('email');
        }
    }

    // Log failed login attempt (invalid credentials)
    $this->logActivity('login_failed', 'User', null, [
        'email' => $credentials['email'],
        'reason' => 'Invalid credentials'
    ]);

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
            $this->logActivity('logout', 'User', $user->id, [
                'role' => $user->role
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

}
