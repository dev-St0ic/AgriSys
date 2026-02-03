<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRegistration;
use App\Models\TrainingApplication;
use App\Models\RequestCategory;
use App\Models\CategoryItem;
use App\Models\SeedlingRequest;
use App\Models\RsbsaApplication;
use App\Models\FishrApplication;
use App\Models\BoatrApplication;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Show enhanced admin dashboard focused on critical items
     */
    public function dashboard()
    {
        $user = auth()->user();

        // CRITICAL ALERTS SECTION
        $criticalAlerts = $this->getCriticalAlerts();

        // KEY METRICS - Only essential numbers
        $keyMetrics = $this->getKeyMetrics();

        // RECENT ACTIVITY - Only actionable items
        $recentActivity = $this->getRecentActivity();

        // SUPPLY ALERTS
        $supplyAlerts = $this->getSupplyAlerts();

        // APPLICATION STATUS OVERVIEW
        $applicationStatus = $this->getApplicationStatusOverview();

        // GEOGRAPHIC DISTRIBUTION
        $geographicDistribution = $this->getGeographicDistribution();

        // USER REGISTRATION DATA with date range
        $startMonth = request('start_month', now()->subMonths(5)->format('Y-m'));
        $endMonth = request('end_month', now()->format('Y-m'));
        $userRegistrationData = $this->getUserRegistrationData($startMonth, $endMonth);

        return view('admin.dashboard', compact(
            'user',
            'criticalAlerts',
            'keyMetrics',
            'recentActivity',
            'supplyAlerts',
            'applicationStatus',
            'geographicDistribution',
            'userRegistrationData',
            'startMonth',
            'endMonth'
        ));
    }

    /**
     * Get critical alerts that need immediate attention
     */
    private function getCriticalAlerts(): array
    {
        $alerts = [];

        // PENDING APPROVALS
        $pendingRsbsa = RsbsaApplication::whereIn('status', ['pending', 'under_review'])->count();
        $pendingSeedling = SeedlingRequest::whereIn('status', ['pending', 'under_review'])->count();
        $pendingFishr = FishrApplication::whereIn('status', ['pending', 'under_review'])->count();
        $pendingBoatr = BoatrApplication::whereIn('status', ['pending', 'under_review'])->count();
        $pendingTraining = TrainingApplication::whereIn('status', ['pending', 'under_review'])->count();

        $totalPending = $pendingRsbsa + $pendingSeedling + $pendingFishr + $pendingBoatr + $pendingTraining;

        if ($totalPending > 0) {
            $alerts[] = [
                'type' => 'pending',
                'icon' => 'fas fa-hourglass-half',
                'color' => 'warning',
                'title' => 'Pending Approvals',
                'count' => $totalPending,
                'subtitle' => 'Awaiting review and decision',
                'actions' => [
                    ['label' => 'RSBSA (' . $pendingRsbsa . ')', 'route' => 'admin.rsbsa.applications'],
                    ['label' => 'Seedling (' . $pendingSeedling . ')', 'route' => 'admin.seedlings.requests'],
                    ['label' => 'FishR (' . $pendingFishr . ')', 'route' => 'admin.fishr.requests'],
                    ['label' => 'BoatR (' . $pendingBoatr . ')', 'route' => 'admin.boatr.requests'],
                    ['label' => 'Training (' . $pendingTraining . ')', 'route' => 'admin.training.requests'],
                ]
            ];
        }

        // RECENT REJECTIONS - Count from services that have rejected_at (RSBSA & Seedling only)
        $recentRejections =
            RsbsaApplication::where('rejected_at', '>=', Carbon::now()->subDays(7))->count() +
            SeedlingRequest::where('rejected_at', '>=', Carbon::now()->subDays(7))->count();

        if ($recentRejections > 0) {
            $alerts[] = [
                'type' => 'rejection',
                'icon' => 'fas fa-times-circle',
                'color' => 'danger',
                'title' => 'Recent Rejections',
                'count' => $recentRejections,
                'subtitle' => 'Last 7 days - may require follow-up',
                'actions' => []
            ];
        }

        // SUPPLY ISSUES
        $outOfStock = CategoryItem::outOfSupply()->count();
        $lowStock = CategoryItem::lowSupply()->count();
        $supplyIssues = $outOfStock + $lowStock;

        if ($supplyIssues > 0) {
            $alerts[] = [
                'type' => 'supply',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'danger',
                'title' => 'Supply Issues',
                'count' => $supplyIssues,
                'subtitle' => $outOfStock . ' out of stock, ' . $lowStock . ' low stock',
                'actions' => [
                    ['label' => 'Manage Supply', 'route' => 'admin.seedlings.supply-management.index']
                ]
            ];
        }

        return $alerts;
    }

    /**
     * Get key metrics - essential numbers only
     */
    private function getKeyMetrics(): array
    {
        // Total pending across all services
        $totalPending = RsbsaApplication::whereIn('status', ['pending', 'under_review'])->count()
            + SeedlingRequest::whereIn('status', ['pending', 'under_review'])->count()
            + FishrApplication::whereIn('status', ['pending', 'under_review'])->count()
            + BoatrApplication::whereIn('status', ['pending', 'under_review'])->count()
            + TrainingApplication::whereIn('status', ['pending', 'under_review'])->count();

        // Total out-of-stock items
        $outOfStockItems = CategoryItem::outOfSupply()->count();

        // Total registered users from user_registration table
        $totalUsers = UserRegistration::count();

        return [
            'total_pending' => $totalPending,
            'out_of_stock_items' => $outOfStockItems,
            'total_users' => $totalUsers,
        ];
    }

    /**
     * Get recent activity with direct action links
     */
    private function getRecentActivity()
    {
        $activities = [];

        // Recent pending RSBSA applications
        $recentRsbsa = RsbsaApplication::whereIn('status', ['pending', 'under_review'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($app) {
                return [
                    'type' => 'RSBSA',
                    'name' => $app->full_name,
                    'action' => 'Pending Review',
                    'created_at' => $app->created_at,
                    'action_url' => route('admin.rsbsa.show', $app->id),
                    'status_color' => 'warning'
                ];
            });

        // Recent pending Seedling requests
        $recentSeedling = SeedlingRequest::whereIn('status', ['pending', 'under_review'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($req) {
                return [
                    'type' => 'Seedling',
                    'name' => $req->full_name,
                    'action' => 'Pending Review',
                    'created_at' => $req->created_at,
                    'action_url' => route('admin.seedlings.edit', $req->id),
                    'status_color' => 'warning'
                ];
            });

        // Recent pending FishR
        $recentFishr = FishrApplication::whereIn('status', ['pending', 'under_review'])
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get()
            ->map(function($app) {
                return [
                    'type' => 'FishR',
                    'name' => $app->full_name,
                    'action' => 'Pending Review',
                    'created_at' => $app->created_at,
                    'action_url' => route('admin.fishr.show', $app->id),
                    'status_color' => 'warning'
                ];
            });

        // Merge and sort by date, take 8 most recent
        $activities = collect()
            ->merge($recentRsbsa)
            ->merge($recentSeedling)
            ->merge($recentFishr)
            ->sortByDesc('created_at')
            ->take(8)
            ->values();

        return $activities;
    }

    /**
     * Get supply alerts for critical items
     */
    private function getSupplyAlerts(): array
    {
        $alerts = [];

        // Get total counts first
        $totalOutOfStockCount = CategoryItem::outOfSupply()->count();
        $totalLowStockCount = CategoryItem::whereNotNull('reorder_point')
            ->whereColumn('current_supply', '<=', 'reorder_point')
            ->where('current_supply', '>', 0)
            ->count();
        $totalItemsCount = CategoryItem::count();

        // Out of stock items (top 5 for display)
        $outOfStock = CategoryItem::outOfSupply()
            ->with('category')
            ->orderBy('current_supply', 'asc')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'item' => $item->name,
                    'category' => $item->category->display_name,
                    'current' => $item->current_supply,
                    'status' => 'out_of_stock',
                    'action_url' => route('admin.seedlings.items.show', $item->id)
                ];
            });

        // Low stock items (critically low - top 5 for display)
        $lowStock = CategoryItem::whereNotNull('reorder_point')
            ->whereColumn('current_supply', '<=', 'reorder_point')
            ->where('current_supply', '>', 0)
            ->with('category')
            ->orderBy('current_supply', 'asc')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'item' => $item->name,
                    'category' => $item->category->display_name,
                    'current' => $item->current_supply,
                    'minimum' => $item->reorder_point,
                    'status' => 'low_stock',
                    'action_url' => route('admin.seedlings.items.show', $item->id)
                ];
            });

        return [
            'out_of_stock' => $outOfStock->toArray(),
            'low_stock' => $lowStock->toArray(),
            'total_issues' => $totalOutOfStockCount + $totalLowStockCount,
            'total_out_of_stock_count' => $totalOutOfStockCount,
            'total_low_stock_count' => $totalLowStockCount,
            'total_items_count' => $totalItemsCount
        ];
    }

    /**
     * Get application status overview (simple breakdown)
     */
    private function getApplicationStatusOverview(): array
    {
        $services = [
            'rsbsa' => [
                'name' => 'Rsbsa Registration',
                'icon' => 'fas fa-seedling',
                'pending' => RsbsaApplication::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => RsbsaApplication::where('status', 'approved')->count(),
                'rejected' => RsbsaApplication::where('status', 'rejected')->count(),
                'route' => 'admin.rsbsa.applications'
            ],
            'seedling' => [
                'name' => 'Supplies Request',
                'icon' => 'fas fa-leaf',
                'pending' => SeedlingRequest::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => SeedlingRequest::where('status', 'approved')->count(),
                'rejected' => SeedlingRequest::where('status', 'rejected')->count(),
                'route' => 'admin.seedlings.requests'
            ],
            'fishr' => [
                'name' => 'FishrR Registration',
                'icon' => 'fas fa-fish',
                'pending' => FishrApplication::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => FishrApplication::where('status', 'approved')->count(),
                'rejected' => FishrApplication::where('status', 'rejected')->count(),
                'route' => 'admin.fishr.requests'
            ],
            'boatr' => [
                'name' => 'BoatR Registration',
                'icon' => 'fas fa-ship',
                'pending' => BoatrApplication::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => BoatrApplication::where('status', 'approved')->count(),
                'rejected' => BoatrApplication::where('status', 'rejected')->count(),
                'route' => 'admin.boatr.requests'
            ],
            'training' => [
                'name' => 'Training Registration',
                'icon' => 'fas fa-graduation-cap',
                'pending' => TrainingApplication::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => TrainingApplication::where('status', 'approved')->count(),
                'rejected' => TrainingApplication::where('status', 'rejected')->count(),
                'route' => 'admin.training.requests'
            ],
        ];

        return $services;
    }

    /**
     * Get geographic distribution of users by barangay
     */
    private function getGeographicDistribution(): array
    {
        // Get user distribution by barangay from user_registration table
        $userDistribution = \DB::table('user_registration')
            ->select('barangay', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Get application distribution by barangay
        $applicationDistribution = collect();

        // RSBSA applications
        $rsbsaData = RsbsaApplication::select('barangay', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->groupBy('barangay')
            ->get();

        // Seedling requests
        $seedlingData = SeedlingRequest::select('barangay', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->groupBy('barangay')
            ->get();

        // FishR applications
        $fishrData = FishrApplication::select('barangay', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->groupBy('barangay')
            ->get();

        // BoatR applications
        $boatrData = BoatrApplication::select('barangay', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->groupBy('barangay')
            ->get();

        // Training applications
        $trainingData = TrainingApplication::select('barangay', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->groupBy('barangay')
            ->get();

        // Merge all application data by barangay
        $mergedData = collect();
        foreach ([$rsbsaData, $seedlingData, $fishrData, $boatrData, $trainingData] as $dataset) {
            foreach ($dataset as $item) {
                $existing = $mergedData->firstWhere('barangay', $item->barangay);
                if ($existing) {
                    $existing->count += $item->count;
                } else {
                    $mergedData->push((object)[
                        'barangay' => $item->barangay,
                        'count' => $item->count
                    ]);
                }
            }
        }

        $applicationDistribution = $mergedData->sortByDesc('count')->take(5);

        return [
            'users' => $userDistribution->toArray(),
            'applications' => $applicationDistribution->values()->toArray()
        ];
    }

    /**
     * Get user registration data based on date range (new registrations per month)
     * @param string $startMonth - Start month in Y-m format (e.g., '2025-09')
     * @param string $endMonth - End month in Y-m format (e.g., '2026-02')
     */
    private function getUserRegistrationData($startMonth, $endMonth): array
    {
        $monthlyData = [];

        // First check if we have any user registrations at all
        $totalRegistrations = UserRegistration::count();

        if ($totalRegistrations === 0) {
            // No registrations, return empty array (will trigger dummy data)
            return [];
        }

        // Parse the start and end dates
        try {
            $startDate = Carbon::createFromFormat('Y-m', $startMonth)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $endMonth)->endOfMonth();
        } catch (\Exception $e) {
            // If invalid dates, default to last 6 months
            $startDate = Carbon::now()->subMonths(5)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        // Ensure start is before end
        if ($startDate->greaterThan($endDate)) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        // Loop through each month in the range
        $currentMonth = $startDate->copy();
        while ($currentMonth->lessThanOrEqualTo($endDate)) {
            $monthStart = $currentMonth->copy()->startOfMonth();
            $monthEnd = $currentMonth->copy()->endOfMonth();

            // Count user registrations in this specific month
            $count = UserRegistration::whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $monthlyData[] = [
                'month' => $currentMonth->format('M Y'),
                'count' => $count
            ];

            $currentMonth->addMonth();
        }

        return $monthlyData;
    }
}
