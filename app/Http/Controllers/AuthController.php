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
                    return redirect()->intended('/admin/dashboard');
                } else {
                    return redirect()->intended('/admin/dashboard');
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

        // Get inventory statistics
        $lowStockItems = \App\Models\Inventory::lowStock()->count();
        $outOfStockItems = \App\Models\Inventory::outOfStock()->count();
        $totalInventoryItems = \App\Models\Inventory::active()->count();

        // Analytics data
        $analyticsData = $this->getAnalyticsData();

        return view('admin.dashboard', compact(
            'user', 
            'totalAdmins', 
            'totalSuperAdmins', 
            'totalUsers',
            'lowStockItems',
            'outOfStockItems',
            'totalInventoryItems',
            'analyticsData'
        ));
    }

    /**
     * Get analytics data for dashboard
     */
    private function getAnalyticsData()
    {
        // Application counts
        $rsbsaCount = \App\Models\RsbsaApplication::count();
        $seedlingCount = \App\Models\SeedlingRequest::count();
        $fishrCount = \App\Models\FishrApplication::count();
        $boatrCount = \App\Models\BoatrApplication::count();
        
        $totalApplications = $rsbsaCount + $seedlingCount + $fishrCount + $boatrCount;

        // Monthly trends (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'rsbsa' => \App\Models\RsbsaApplication::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'seedling' => \App\Models\SeedlingRequest::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'fishr' => \App\Models\FishrApplication::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'boatr' => \App\Models\BoatrApplication::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        }

        // Status distribution
        $pendingApplications = \App\Models\RsbsaApplication::where('status', 'pending')->count() +
                              \App\Models\SeedlingRequest::where('status', 'pending')->count() +
                              \App\Models\FishrApplication::where('status', 'pending')->count() +
                              \App\Models\BoatrApplication::where('status', 'pending')->count();

        $approvedApplications = \App\Models\RsbsaApplication::where('status', 'approved')->count() +
                               \App\Models\SeedlingRequest::where('status', 'approved')->count() +
                               \App\Models\FishrApplication::where('status', 'approved')->count() +
                               \App\Models\BoatrApplication::where('status', 'approved')->count();

        // Recent activity
        $recentApplications = collect();
        
        // Get recent RSBSA applications
        $recentRsbsa = \App\Models\RsbsaApplication::latest()->take(3)->get()->map(function($app) {
            return [
                'type' => 'RSBSA Application',
                'name' => $app->full_name,
                'created_at' => $app->created_at,
                'status' => $app->status,
                'barangay' => $app->barangay
            ];
        });

        // Get recent seedling requests
        $recentSeedling = \App\Models\SeedlingRequest::latest()->take(3)->get()->map(function($app) {
            return [
                'type' => 'Seedling Request',
                'name' => $app->first_name . ' ' . $app->last_name,
                'created_at' => $app->created_at,
                'status' => $app->status,
                'barangay' => $app->barangay
            ];
        });

        $recentApplications = $recentRsbsa->merge($recentSeedling)->sortByDesc('created_at')->take(5);

        return [
            'totals' => [
                'rsbsa' => $rsbsaCount,
                'seedling' => $seedlingCount,
                'fishr' => $fishrCount,
                'boatr' => $boatrCount,
                'total' => $totalApplications
            ],
            'monthly_trends' => $monthlyData,
            'status' => [
                'pending' => $pendingApplications,
                'approved' => $approvedApplications
            ],
            'recent_activity' => $recentApplications
        ];
    }
}
