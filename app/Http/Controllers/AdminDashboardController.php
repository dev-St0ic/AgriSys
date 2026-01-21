<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

        return view('admin.dashboard', compact(
            'user',
            'criticalAlerts',
            'keyMetrics',
            'recentActivity',
            'supplyAlerts',
            'applicationStatus'
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

        // Total active users
        $totalUsers = User::where('role', 'user')->count();

        return [
            'total_pending' => $totalPending,
            'out_of_stock_items' => $outOfStockItems,
            'total_users' => $totalUsers,
        ];
    }

    /**
     * Get recent activity with direct action links
     */
    private function getRecentActivity(): array
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
            ->values()
            ->toArray();

        return $activities;
    }

    /**
     * Get supply alerts for critical items
     */
    private function getSupplyAlerts(): array
    {
        $alerts = [];

        // Out of stock items
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

        // Low stock items (critically low)
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
            'total_issues' => $outOfStock->count() + $lowStock->count()
        ];
    }

    /**
     * Get application status overview (simple breakdown)
     */
    private function getApplicationStatusOverview(): array
    {
        $services = [
            'rsbsa' => [
                'name' => 'RSBSA',
                'icon' => 'fas fa-seedling',
                'pending' => RsbsaApplication::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => RsbsaApplication::where('status', 'approved')->count(),
                'rejected' => RsbsaApplication::where('status', 'rejected')->count(),
                'route' => 'admin.rsbsa.applications'
            ],
            'seedling' => [
                'name' => 'Seedling',
                'icon' => 'fas fa-leaf',
                'pending' => SeedlingRequest::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => SeedlingRequest::where('status', 'approved')->count(),
                'rejected' => SeedlingRequest::where('status', 'rejected')->count(),
                'route' => 'admin.seedlings.requests'
            ],
            'fishr' => [
                'name' => 'FishR',
                'icon' => 'fas fa-fish',
                'pending' => FishrApplication::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => FishrApplication::where('status', 'approved')->count(),
                'rejected' => FishrApplication::where('status', 'rejected')->count(),
                'route' => 'admin.fishr.requests'
            ],
            'boatr' => [
                'name' => 'BoatR',
                'icon' => 'fas fa-ship',
                'pending' => BoatrApplication::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => BoatrApplication::where('status', 'approved')->count(),
                'rejected' => BoatrApplication::where('status', 'rejected')->count(),
                'route' => 'admin.boatr.requests'
            ],
            'training' => [
                'name' => 'Training',
                'icon' => 'fas fa-graduation-cap',
                'pending' => TrainingApplication::whereIn('status', ['pending', 'under_review'])->count(),
                'approved' => TrainingApplication::where('status', 'approved')->count(),
                'rejected' => TrainingApplication::where('status', 'rejected')->count(),
                'route' => 'admin.training.requests'
            ],
        ];

        return $services;
    }
}