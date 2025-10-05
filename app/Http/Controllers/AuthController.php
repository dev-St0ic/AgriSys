<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TrainingApplication;

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

        // Inventory statistics disabled for new supply management
        // Set default values to prevent dashboard errors
        $lowStockItems = 0;
        $outOfStockItems = 0;
        $totalInventoryItems = 0;

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
        // RSBSA Applications Statistics
        $rsbsaApproved = \App\Models\RsbsaApplication::where('status', 'approved')->count();
        $rsbsaPending = \App\Models\RsbsaApplication::whereIn('status', ['pending', 'under_review'])->count();
        $rsbsaRejected = \App\Models\RsbsaApplication::where('status', 'rejected')->count();
        $rsbsaTotal = \App\Models\RsbsaApplication::count();

        // Seedling Requests Statistics
        $seedlingApproved = \App\Models\SeedlingRequest::where('status', 'approved')->count();
        $seedlingPending = \App\Models\SeedlingRequest::whereIn('status', ['under_review', 'partially_approved'])->count();
        $seedlingRejected = \App\Models\SeedlingRequest::where('status', 'rejected')->count();
        $seedlingTotal = \App\Models\SeedlingRequest::count();

        // FishR Applications Statistics
        $fishrApproved = \App\Models\FishrApplication::where('status', 'approved')->count();
        $fishrPending = \App\Models\FishrApplication::whereIn('status', ['pending', 'under_review', 'inspection_scheduled', 'inspection_required', 'documents_pending'])->count();
        $fishrRejected = \App\Models\FishrApplication::where('status', 'rejected')->count();
        $fishrTotal = \App\Models\FishrApplication::count();

        // BoatR Applications Statistics
        $boatrApproved = \App\Models\BoatrApplication::where('status', 'approved')->count();
        $boatrPending = \App\Models\BoatrApplication::whereIn('status', ['pending', 'under_review', 'inspection_scheduled', 'inspection_required', 'documents_pending'])->count();
        $boatrRejected = \App\Models\BoatrApplication::where('status', 'rejected')->count();
        $boatrTotal = \App\Models\BoatrApplication::count();

        // Training Applications Statistics
        $trainingApproved = \App\Models\TrainingApplication::where('status', 'approved')->count();
        $trainingPending = \App\Models\TrainingApplication::whereIn('status', ['pending', 'under_review'])->count();
        $trainingRejected = \App\Models\TrainingApplication::where('status', 'rejected')->count();
        $trainingTotal = \App\Models\TrainingApplication::count();

        // Calculate totals
        $totalApplications = $rsbsaTotal + $seedlingTotal + $fishrTotal + $boatrTotal + $trainingTotal;
        $totalApproved = $rsbsaApproved + $seedlingApproved + $fishrApproved + $boatrApproved + $trainingApproved;
        $totalPending = $rsbsaPending + $seedlingPending + $fishrPending + $boatrPending + $trainingPending;
        $totalRejected = $rsbsaRejected + $seedlingRejected + $fishrRejected + $boatrRejected + $trainingRejected;

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
                'training' => \App\Models\TrainingApplication::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        }

        // Recent activity
        $recentApplications = collect();

        // Get recent RSBSA applications
        $recentRsbsa = \App\Models\RsbsaApplication::latest()->take(2)->get()->map(function($app) {
            return [
                'type' => 'RSBSA Application',
                'name' => $app->full_name,
                'created_at' => $app->created_at,
                'status' => $app->status,
                'barangay' => $app->barangay
            ];
        });

        // Get recent seedling requests
        $recentSeedling = \App\Models\SeedlingRequest::latest()->take(2)->get()->map(function($app) {
            return [
                'type' => 'Seedling Request',
                'name' => $app->first_name . ' ' . $app->last_name,
                'created_at' => $app->created_at,
                'status' => $app->status,
                'barangay' => $app->barangay
            ];
        });

        // Get recent training applications
        $recentTraining = \App\Models\TrainingApplication::latest()->take(1)->get()->map(function($app) {
            return [
                'type' => 'Training Application',
                'name' => $app->first_name . ' ' . $app->last_name,
                'created_at' => $app->created_at,
                'status' => $app->status,
                'barangay' => $app->barangay ?? 'N/A'
            ];
        });

        $recentApplications = $recentRsbsa->merge($recentSeedling)->merge($recentTraining)->sortByDesc('created_at')->take(5);

        return [
            'services' => [
                'rsbsa' => [
                    'name' => 'RSBSA Applications',
                    'total' => $rsbsaTotal,
                    'approved' => $rsbsaApproved,
                    'pending' => $rsbsaPending,
                    'rejected' => $rsbsaRejected,
                    'icon' => 'fas fa-seedling',
                    'color' => 'primary'
                ],
                'seedling' => [
                    'name' => 'Seedling Requests',
                    'total' => $seedlingTotal,
                    'approved' => $seedlingApproved,
                    'pending' => $seedlingPending,
                    'rejected' => $seedlingRejected,
                    'icon' => 'fas fa-leaf',
                    'color' => 'success'
                ],
                'fishr' => [
                    'name' => 'FishR Registrations',
                    'total' => $fishrTotal,
                    'approved' => $fishrApproved,
                    'pending' => $fishrPending,
                    'rejected' => $fishrRejected,
                    'icon' => 'fas fa-fish',
                    'color' => 'info'
                ],
                'boatr' => [
                    'name' => 'BoatR Applications',
                    'total' => $boatrTotal,
                    'approved' => $boatrApproved,
                    'pending' => $boatrPending,
                    'rejected' => $boatrRejected,
                    'icon' => 'fas fa-ship',
                    'color' => 'warning'
                ],
                'training' => [
                    'name' => 'Training Applications',
                    'total' => $trainingTotal,
                    'approved' => $trainingApproved,
                    'pending' => $trainingPending,
                    'rejected' => $trainingRejected,
                    'icon' => 'fas fa-graduation-cap',
                    'color' => 'purple'
                ]
            ],
            'totals' => [
                'total' => $totalApplications,
                'approved' => $totalApproved,
                'pending' => $totalPending,
                'rejected' => $totalRejected
            ],
            'monthly_trends' => $monthlyData,
            'recent_activity' => $recentApplications
        ];
    }
}
