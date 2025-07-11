<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
            /** @var User $user */
            $user = Auth::user();

            // Check if user has admin privileges
            if ($user->hasAdminPrivileges()) {
                $request->session()->regenerate();

                if ($user->isSuperAdmin()) {
                    return redirect()->intended('/admin/dashboard')->with('success', 'Welcome, Super Admin!');
                } else {
                    return redirect()->intended('/admin/dashboard')->with('success', 'Welcome, Admin!');
                }
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have admin privileges.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalSuperAdmins = User::where('role', 'superadmin')->count();
        $totalUsers = User::where('role', 'user')->count();

        return view('admin.dashboard', compact('user', 'totalAdmins', 'totalSuperAdmins', 'totalUsers'));
    }
}
