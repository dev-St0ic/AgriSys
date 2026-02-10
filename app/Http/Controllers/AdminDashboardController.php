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
use Spatie\Activitylog\Models\Activity;
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

        // PRIORITY TASKS - Most urgent pending applications
        $priorityTasks = $this->getPriorityTasks();

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
            'priorityTasks',
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
     * Get recent activity logs based on user role
     */
    private function getRecentActivity()
    {
        $user = auth()->user();

        // Base query for activity logs
        $query = Activity::with('causer')
            ->latest()
            ->where(function($q) {
                $q->whereNull('subject_type')
                  ->orWhere('subject_type', 'not like', '%ItemSupplyLog%');
            })
            // Exclude login/logout activities
            ->whereNotIn('event', ['login', 'logout', 'login_failed'])
            ->where(function($q) {
                $q->whereNotNull('subject_type')
                  ->orWhere(function($q2) {
                      $q2->whereNull('subject_type')
                         ->where('description', 'not like', '%login%')
                         ->where('description', 'not like', '%logout%');
                  });
            });

        // Role-based filtering: Admin sees only admin activities, Superadmin sees everything
        if ($user->isAdmin()) {
            $query->where('causer_type', 'App\\Models\\User')
                  ->whereExists(function($q) {
                      $q->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'activity_log.causer_id')
                        ->where('users.role', 'admin');
                  });
        }

        // Get recent 8 activities
        $activities = $query->take(8)->get()->map(function($activity) {
            // Determine action color based on event
            $statusColor = 'warning';
            $icon = 'fa-clock';

            if (in_array($activity->event, ['created', 'approved', 'completed'])) {
                $statusColor = 'success';
                $icon = 'fa-check-circle';
            } elseif (in_array($activity->event, ['deleted', 'rejected'])) {
                $statusColor = 'danger';
                $icon = 'fa-times-circle';
            } elseif (in_array($activity->event, ['updated', 'updated_status', 'updated_items'])) {
                $statusColor = 'warning';
                $icon = 'fa-edit';
            }

            // Get user info
            $userName = 'System';
            if ($activity->causer) {
                if ($activity->causer instanceof \App\Models\UserRegistration) {
                    $userName = $activity->causer->username ?? 'Unknown User';
                } else {
                    $userName = $activity->causer->name ?? 'Unknown User';
                }
            }

            // Extract module/type from description or subject
            $type = 'Activity';
            if ($activity->subject_type) {
                $type = class_basename($activity->subject_type);
            }

            // Format action text
            $action = ucfirst($activity->event ?? 'Activity');

            return [
                'type' => $type,
                'name' => $userName,
                'action' => $action,
                'created_at' => $activity->created_at,
                'status_color' => $statusColor,
                'icon' => $icon,
                'description' => $activity->description
            ];
        });

        return $activities;
    }

    /**
     * Get priority tasks - most urgent pending applications
     */
    private function getPriorityTasks()
    {
        $tasks = collect();

        // Get pending RSBSA (oldest first, most urgent)
        $rsbsa = RsbsaApplication::whereIn('status', ['pending', 'under_review'])
            ->orderBy('created_at', 'asc')
            ->take(3)
            ->get()
            ->map(function($app) {
                $daysOld = $app->created_at->diffInDays(now());
                $priority = $daysOld > 7 ? 'high' : ($daysOld > 3 ? 'medium' : 'low');

                return [
                    'id' => $app->id,
                    'type' => 'RSBSA',
                    'name' => $app->full_name,
                    'status' => ucfirst($app->status),
                    'days_pending' => $daysOld,
                    'created_at' => $app->created_at,
                    'priority' => $priority,
                    'route' => route('admin.rsbsa.applications') . '?highlight=' . $app->id,
                    'icon' => 'fa-seedling',
                    'color' => 'success'
                ];
            });

        // Get pending Seedling requests
        $seedling = SeedlingRequest::whereIn('status', ['pending', 'under_review'])
            ->orderBy('created_at', 'asc')
            ->take(2)
            ->get()
            ->map(function($req) {
                $daysOld = $req->created_at->diffInDays(now());
                $priority = $daysOld > 7 ? 'high' : ($daysOld > 3 ? 'medium' : 'low');

                return [
                    'id' => $req->id,
                    'type' => 'Supply Request',
                    'name' => $req->full_name,
                    'status' => ucfirst($req->status),
                    'days_pending' => $daysOld,
                    'created_at' => $req->created_at,
                    'priority' => $priority,
                    'route' => route('admin.seedlings.requests') . '?highlight=' . $req->id,
                    'icon' => 'fa-leaf',
                    'color' => 'warning'
                ];
            });

        // Get pending FishR
        $fishr = FishrApplication::whereIn('status', ['pending', 'under_review'])
            ->orderBy('created_at', 'asc')
            ->take(2)
            ->get()
            ->map(function($app) {
                $daysOld = $app->created_at->diffInDays(now());
                $priority = $daysOld > 7 ? 'high' : ($daysOld > 3 ? 'medium' : 'low');

                return [
                    'id' => $app->id,
                    'type' => 'FishR',
                    'name' => $app->full_name,
                    'status' => ucfirst($app->status),
                    'days_pending' => $daysOld,
                    'created_at' => $app->created_at,
                    'priority' => $priority,
                    'route' => route('admin.fishr.requests') . '?highlight=' . $app->id,
                    'icon' => 'fa-fish',
                    'color' => 'info'
                ];
            });

        // Get pending BoatR
        $boatr = BoatrApplication::whereIn('status', ['pending', 'under_review'])
            ->orderBy('created_at', 'asc')
            ->take(1)
            ->get()
            ->map(function($app) {
                $daysOld = $app->created_at->diffInDays(now());
                $priority = $daysOld > 7 ? 'high' : ($daysOld > 3 ? 'medium' : 'low');

                return [
                    'id' => $app->id,
                    'type' => 'BoatR',
                    'name' => $app->full_name,
                    'status' => ucfirst($app->status),
                    'days_pending' => $daysOld,
                    'created_at' => $app->created_at,
                    'priority' => $priority,
                    'route' => route('admin.boatr.requests') . '?highlight=' . $app->id,
                    'icon' => 'fa-ship',
                    'color' => 'danger'
                ];
            });

        // Get pending Training
        $training = TrainingApplication::whereIn('status', ['pending', 'under_review'])
            ->orderBy('created_at', 'asc')
            ->take(1)
            ->get()
            ->map(function($app) {
                $daysOld = $app->created_at->diffInDays(now());
                $priority = $daysOld > 7 ? 'high' : ($daysOld > 3 ? 'medium' : 'low');

                return [
                    'id' => $app->id,
                    'type' => 'Training',
                    'name' => $app->full_name,
                    'status' => ucfirst($app->status),
                    'days_pending' => $daysOld,
                    'created_at' => $app->created_at,
                    'priority' => $priority,
                    'route' => route('admin.training.requests') . '?highlight=' . $app->id,
                    'icon' => 'fa-chalkboard-teacher',
                    'color' => 'purple'
                ];
            });

        // Add critical supply items
        $criticalSupply = CategoryItem::outOfSupply()
            ->take(2)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Supply Alert',
                    'name' => $item->item_name,
                    'status' => 'Out of Stock',
                    'days_pending' => 0,
                    'created_at' => now(),
                    'priority' => 'high',
                    'route' => route('admin.seedlings.supply-management.index') . '?highlight=' . $item->id,
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'danger'
                ];
            });

        // Merge all tasks and sort by priority (high first) then by days pending (oldest first)
        return $tasks
            ->merge($rsbsa)
            ->merge($seedling)
            ->merge($fishr)
            ->merge($boatr)
            ->merge($training)
            ->merge($criticalSupply)
            ->sortByDesc(function($task) {
                // Sort order: high = 3, medium = 2, low = 1
                $priorityWeight = ['high' => 3, 'medium' => 2, 'low' => 1];
                return ($priorityWeight[$task['priority']] * 1000) + $task['days_pending'];
            })
            ->take(5)
            ->values();
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
                'name' => 'Training Request',
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
