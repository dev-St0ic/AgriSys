<?php

namespace App\Http\Controllers;

use App\Models\SeedlingRequest;
use App\Models\SeedlingRequestItem;
use App\Models\RequestCategory;
use App\Models\CategoryItem;
use App\Services\SeedlingAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SeedlingAnalyticsController extends Controller
{
    protected SeedlingAnalyticsService $analyticsService;

    public function __construct(SeedlingAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        try {
            // Get filter type and preset
            $filterType = $request->get('filter_type', 'preset');
            $datePreset = $request->get('date_preset', 'this_year'); // Changed from 'this_month' to 'this_year'

            // Calculate dates based on filter type
            if ($filterType === 'preset') {
                [$startDate, $endDate] = $this->calculatePresetDates($datePreset);
            } else {
                $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
                $endDate = $request->get('end_date', now()->format('Y-m-d'));

                // Validate dates
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
            }

            // Base query with date range
            $baseQuery = SeedlingRequest::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            // 1. Overview Statistics with comparisons
            $overview = $this->getOverviewStatistics(clone $baseQuery, $startDate, $endDate);

            // Check if we have sufficient data for meaningful analytics
            if ($overview['total_requests'] === 0) {
                session()->flash('warning', 'No seedling requests found for the selected date range. Try expanding the date range or selecting "All Time" to see available data.');
            } elseif ($overview['total_requests'] < 5) {
                session()->flash('info', 'Limited data available for the selected period (' . $overview['total_requests'] . ' requests). Consider expanding the date range for more comprehensive analytics.');
            }

            // 2. Request Status Analysis
            $statusAnalysis = $this->getStatusAnalysis(clone $baseQuery);

            // 3. Monthly Trends
            $monthlyTrends = $this->getMonthlyTrends($startDate, $endDate);

            // 4. Barangay Analysis with performance scoring
            $barangayAnalysis = $this->getBarangayAnalysis(clone $baseQuery);

            // 5. Seedling Category Analysis
            $categoryAnalysis = $this->getCategoryAnalysis(clone $baseQuery);

            // 6. Top Requested Items
            $topItems = $this->getTopRequestedItems(clone $baseQuery);

            // 7. Least Requested Items
            $leastRequestedItems = $this->getLeastRequestedItems(clone $baseQuery);

            // 8. Request Processing Time Analysis
            $processingTimeAnalysis = $this->getProcessingTimeAnalysis(clone $baseQuery);

            // 9. Seasonal Analysis
            $seasonalAnalysis = $this->getSeasonalAnalysis(clone $baseQuery);

            // 10. Monthly Barangay Analysis
            $monthlyBarangayAnalysis = $this->getMonthlyBarangayAnalysis($startDate, $endDate);

            // 11. Inventory Impact Analysis
            $inventoryImpact = $this->getInventoryImpactAnalysis(clone $baseQuery);

            // 12. Supply vs Demand Analysis
            $supplyDemandAnalysis = $this->getSupplyDemandAnalysis(clone $baseQuery);

            // 13. Barangay Performance Score
            $barangayPerformance = $this->getBarangayPerformanceScore($barangayAnalysis);

            // 14. Category Fulfillment Rate
            $categoryFulfillment = $this->getCategoryFulfillmentRate(clone $baseQuery);

            // 15. Request Velocity (Trend Analysis)
            $requestVelocity = $this->getRequestVelocity($startDate, $endDate);

            // 16. Geographic Distribution Heat Map Data
            $geoDistribution = $this->getGeographicDistribution(clone $baseQuery);

            // NEW: 17. Supply Alerts
            $supplyAlerts = $this->getSupplyAlerts();

            // NEW: 18. Rejection Analysis
            $rejectionAnalysis = $this->getRejectionAnalysis(clone $baseQuery);

            return view('admin.analytics.seedlings', compact(
                'overview',
                'statusAnalysis',
                'monthlyTrends',
                'barangayAnalysis',
                'categoryAnalysis',
                'topItems',
                'leastRequestedItems',
                'processingTimeAnalysis',
                'seasonalAnalysis',
                'monthlyBarangayAnalysis',
                'inventoryImpact',
                'supplyDemandAnalysis',
                'barangayPerformance',
                'categoryFulfillment',
                'requestVelocity',
                'geoDistribution',
                'supplyAlerts',
                'rejectionAnalysis',
                'startDate',
                'endDate',
                'filterType',
                'datePreset'
            ));
        } catch (\Exception $e) {
            Log::error('Analytics Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading analytics data. Please try again.');
        }
    }

    /**
     * Calculate date range based on preset selection
     */
    private function calculatePresetDates($preset)
    {
        $today = now();

        switch($preset) {
            case 'today':
                return [$today->format('Y-m-d'), $today->format('Y-m-d')];

            case 'yesterday':
                $yesterday = $today->copy()->subDay();
                return [$yesterday->format('Y-m-d'), $yesterday->format('Y-m-d')];

            case 'last_7_days':
                return [$today->copy()->subDays(7)->format('Y-m-d'), $today->format('Y-m-d')];

            case 'last_14_days':
                return [$today->copy()->subDays(14)->format('Y-m-d'), $today->format('Y-m-d')];

            case 'last_30_days':
                return [$today->copy()->subDays(30)->format('Y-m-d'), $today->format('Y-m-d')];

            case 'this_week':
                return [$today->copy()->startOfWeek()->format('Y-m-d'), $today->format('Y-m-d')];

            case 'last_week':
                $lastWeekEnd = $today->copy()->startOfWeek()->subDay();
                $lastWeekStart = $lastWeekEnd->copy()->startOfWeek();
                return [$lastWeekStart->format('Y-m-d'), $lastWeekEnd->format('Y-m-d')];

            case 'this_month':
                return [$today->copy()->startOfMonth()->format('Y-m-d'), $today->format('Y-m-d')];

            case 'last_month':
                $lastMonth = $today->copy()->subMonth();
                return [
                    $lastMonth->startOfMonth()->format('Y-m-d'),
                    $lastMonth->endOfMonth()->format('Y-m-d')
                ];

            case 'this_quarter':
                $quarter = ceil($today->month / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                return [
                    $today->copy()->setMonth($startMonth)->startOfMonth()->format('Y-m-d'),
                    $today->format('Y-m-d')
                ];

            case 'last_quarter':
                $currentQuarter = ceil($today->month / 3);
                $lastQuarter = $currentQuarter - 1;
                if ($lastQuarter < 1) {
                    $lastQuarter = 4;
                    $year = $today->year - 1;
                } else {
                    $year = $today->year;
                }
                $startMonth = ($lastQuarter - 1) * 3 + 1;
                $endMonth = $lastQuarter * 3;
                return [
                    now()->setYear($year)->setMonth($startMonth)->startOfMonth()->format('Y-m-d'),
                    now()->setYear($year)->setMonth($endMonth)->endOfMonth()->format('Y-m-d')
                ];

            case 'this_year':
                return [$today->copy()->startOfYear()->format('Y-m-d'), $today->format('Y-m-d')];

            case 'last_year':
                $lastYear = $today->copy()->subYear();
                return [
                    $lastYear->startOfYear()->format('Y-m-d'),
                    $lastYear->endOfYear()->format('Y-m-d')
                ];

            case 'all_time':
                return ['2020-01-01', $today->format('Y-m-d')];

            default:
                return [$today->copy()->startOfMonth()->format('Y-m-d'), $today->format('Y-m-d')];
        }
    }

    /**
     * Get overview statistics with period comparison
     */
    private function getOverviewStatistics($baseQuery, $startDate, $endDate)
    {
        try {
            $total = $baseQuery->count();
            $approved = (clone $baseQuery)->where('status', 'approved')->count();
            $rejected = (clone $baseQuery)->where('status', 'rejected')->count();
            $pending = (clone $baseQuery)->where('status', 'under_review')->count();

            // Get totals with null safety
            $totalQuantityRequested = (clone $baseQuery)->whereNotNull('total_quantity')->sum('total_quantity') ?: 0;
            $totalQuantityApproved = (clone $baseQuery)->where('status', 'approved')->whereNotNull('approved_quantity')->sum('approved_quantity') ?: 0;
            $avgRequestSize = $total > 0 ? round((clone $baseQuery)->whereNotNull('total_quantity')->avg('total_quantity') ?: 0, 2) : 0;

            // Count unique applicants safely
            $uniqueApplicants = (clone $baseQuery)
                ->select(DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
                ->distinct()
                ->whereNotNull('first_name')
                ->count();

            // Count active barangays
            $activeBarangays = (clone $baseQuery)->whereNotNull('barangay')->distinct()->count('barangay');

            // Calculate previous period for comparison
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $daysDiff = $start->diffInDays($end);

            $prevStart = $start->copy()->subDays($daysDiff)->format('Y-m-d');
            $prevEnd = $start->copy()->subDay()->format('Y-m-d');

            $prevTotal = SeedlingRequest::whereBetween('created_at', [$prevStart . ' 00:00:00', $prevEnd . ' 23:59:59'])->count();

            $changePercentage = $prevTotal > 0 ? round((($total - $prevTotal) / $prevTotal) * 100, 1) : 0;

            return [
                'total_requests' => $total,
                'approved_requests' => $approved,
                'rejected_requests' => $rejected,
                'pending_requests' => $pending,
                'total_quantity_requested' => $totalQuantityRequested,
                'total_quantity_approved' => $totalQuantityApproved,
                'approval_rate' => $this->calculateApprovalRate($total, $approved),
                'avg_request_size' => $avgRequestSize,
                'unique_applicants' => $uniqueApplicants,
                'active_barangays' => $activeBarangays,
                'change_percentage' => $changePercentage,
                'fulfillment_rate' => $totalQuantityRequested > 0 ? round(($totalQuantityApproved / $totalQuantityRequested) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Overview Statistics Error: ' . $e->getMessage());
            return $this->getDefaultOverview();
        }
    }

    /**
     * NEW: Get supply vs demand analysis
     */
    private function getSupplyDemandAnalysis($baseQuery)
    {
        try {
            $requestIds = (clone $baseQuery)->pluck('id');

            $analysis = RequestCategory::with('items')
                ->get()
                ->mapWithKeys(function ($category) use ($requestIds) {
                    $items = SeedlingRequestItem::whereIn('seedling_request_id', $requestIds)
                        ->where('category_id', $category->id)
                        ->get();

                    $itemDemands = $items->groupBy('item_name')
                        ->map(function ($group) {
                            return $group->sum('requested_quantity');
                        })
                        ->toArray();

                    $totalDemand = array_sum($itemDemands);
                    $topDemandItem = !empty($itemDemands)
                        ? array_keys($itemDemands, max($itemDemands))[0]
                        : 'N/A';

                    return [
                        $category->name => [
                            'total_demand' => $totalDemand,
                            'unique_items' => count($itemDemands),
                            'top_demand_item' => $topDemandItem,
                            'demand_distribution' => $itemDemands
                        ]
                    ];
                })
                ->toArray();

            return $analysis;
        } catch (\Exception $e) {
            Log::error('Supply Demand Analysis Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * NEW: Get barangay performance score
     */
    private function getBarangayPerformanceScore($barangayAnalysis)
    {
        try {
            $scored = [];

            foreach ($barangayAnalysis as $barangay) {
                $approvalRate = $barangay->total_requests > 0
                    ? ($barangay->approved / $barangay->total_requests) * 100
                    : 0;

                // Calculate performance score (0-100)
                $score = (
                    ($approvalRate * 0.4) + // 40% weight on approval rate
                    (min($barangay->total_requests / 50, 1) * 30) + // 30% on request volume (capped at 50)
                    (min($barangay->unique_applicants / 20, 1) * 20) + // 20% on unique applicants (capped at 20)
                    (min($barangay->total_quantity / 500, 1) * 10) // 10% on quantity (capped at 500)
                );

                $scored[] = [
                    'barangay' => $barangay->barangay,
                    'score' => round($score, 2),
                    'approval_rate' => round($approvalRate, 2),
                    'total_requests' => $barangay->total_requests,
                    'grade' => $this->getPerformanceGrade($score)
                ];
            }

            // Sort by score descending
            usort($scored, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            return collect($scored);
        } catch (\Exception $e) {
            Log::error('Barangay Performance Score Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Helper: Get performance grade
     */
    private function getPerformanceGrade($score)
    {
        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'B+';
        if ($score >= 75) return 'B';
        if ($score >= 70) return 'C+';
        if ($score >= 65) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    /**
     * NEW: Get category fulfillment rate
     */
    private function getCategoryFulfillmentRate($baseQuery)
    {
        try {
            $requestIds = (clone $baseQuery)
                ->whereIn('status', ['approved', 'partially_approved'])
                ->pluck('id');

            $fulfillment = RequestCategory::all()
                ->mapWithKeys(function ($category) use ($requestIds) {
                    $items = SeedlingRequestItem::whereIn('seedling_request_id', $requestIds)
                        ->where('category_id', $category->id)
                        ->get();

                    $totalRequested = $items->sum('requested_quantity');
                    $totalApproved = $items->where('status', 'approved')
                        ->sum('approved_quantity');

                    return [
                        $category->name => [
                            'requested' => $totalRequested,
                            'approved' => $totalApproved,
                            'rate' => $totalRequested > 0
                                ? round(($totalApproved / $totalRequested) * 100, 2)
                                : 0
                        ]
                    ];
                })
                ->toArray();

            return $fulfillment;
        } catch (\Exception $e) {
            Log::error('Category Fulfillment Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * NEW: Get request velocity (trend analysis)
     */
    private function getRequestVelocity($startDate, $endDate)
    {
        try {
            $weeklyData = SeedlingRequest::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('YEARWEEK(created_at, 1) as week'),
                    DB::raw('COUNT(*) as request_count'),
                    DB::raw('MIN(created_at) as week_start')
                )
                ->groupBy('week')
                ->orderBy('week')
                ->get();

            $velocity = [];
            $previousCount = null;

            foreach ($weeklyData as $index => $week) {
                $change = 0;
                if ($previousCount !== null) {
                    $change = $previousCount > 0 ? round((($week->request_count - $previousCount) / $previousCount) * 100, 1) : 0;
                }

                $velocity[] = [
                    'week' => Carbon::parse($week->week_start)->format('M d'),
                    'count' => $week->request_count,
                    'change' => $change,
                    'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable')
                ];

                $previousCount = $week->request_count;
            }

            return collect($velocity);
        } catch (\Exception $e) {
            Log::error('Request Velocity Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * NEW: Get geographic distribution data
     */
    private function getGeographicDistribution($baseQuery)
    {
        try {
            $distribution = (clone $baseQuery)
                ->select(
                    'barangay',
                    DB::raw('COUNT(*) as intensity'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('ROUND(AVG(CASE WHEN status = "approved" THEN 1 ELSE 0 END) * 100, 2) as approval_rate')
                )
                ->whereNotNull('barangay')
                ->where('barangay', '!=', '')
                ->groupBy('barangay')
                ->get();

            return $distribution;
        } catch (\Exception $e) {
            Log::error('Geographic Distribution Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get status analysis with trends
     */
    private function getStatusAnalysis($baseQuery)
    {
        try {
            $statusCounts = (clone $baseQuery)->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Ensure all statuses are present with default values
            $defaultStatuses = ['approved' => 0, 'rejected' => 0, 'under_review' => 0];
            $statusCounts = array_merge($defaultStatuses, $statusCounts);

            // Add percentage calculations
            $total = array_sum($statusCounts);
            $statusPercentages = [];
            foreach ($statusCounts as $status => $count) {
                $statusPercentages[$status] = $total > 0 ? round(($count / $total) * 100, 2) : 0;
            }

            return [
                'counts' => $statusCounts,
                'percentages' => $statusPercentages,
                'total' => $total
            ];
        } catch (\Exception $e) {
            Log::error('Status Analysis Error: ' . $e->getMessage());
            return [
                'counts' => ['approved' => 0, 'rejected' => 0, 'under_review' => 0],
                'percentages' => ['approved' => 0, 'rejected' => 0, 'under_review' => 0],
                'total' => 0
            ];
        }
    }

    /**
     * Get monthly trends
     */
    private function getMonthlyTrends($startDate, $endDate)
    {
        try {
            $trends = SeedlingRequest::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_requests'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status = "under_review" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(COALESCE(total_quantity, 0)) as total_quantity'),
                    DB::raw('ROUND(AVG(COALESCE(total_quantity, 0)), 2) as avg_quantity')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return $trends;
        } catch (\Exception $e) {
            Log::error('Monthly Trends Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get barangay analysis
     */
    private function getBarangayAnalysis($baseQuery)
    {
        try {
            $barangayStats = (clone $baseQuery)->select(
                    'barangay',
                    DB::raw('COUNT(*) as total_requests'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(COALESCE(total_quantity, 0)) as total_quantity'),
                    DB::raw('ROUND(AVG(COALESCE(total_quantity, 0)), 2) as avg_quantity'),
                    DB::raw('COUNT(DISTINCT CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, ""))) as unique_applicants')
                )
                ->whereNotNull('barangay')
                ->where('barangay', '!=', '')
                ->groupBy('barangay')
                ->orderBy('total_requests', 'desc')
                ->get();

            return $barangayStats;
        } catch (\Exception $e) {
            Log::error('Barangay Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get category analysis with item breakdown
     */
    private function getCategoryAnalysis($baseQuery)
    {
        try {
            $requestIds = (clone $baseQuery)->pluck('id');

            $categoryStats = RequestCategory::with('items')
                ->get()
                ->mapWithKeys(function ($category) use ($requestIds) {
                    $items = SeedlingRequestItem::whereIn('seedling_request_id', $requestIds)
                        ->where('category_id', $category->id)
                        ->get();

                    $uniqueItems = $items->groupBy('item_name')
                        ->map(function ($group) {
                            return $group->sum('requested_quantity');
                        })
                        ->toArray();

                    return [
                        $category->name => [
                            'requests' => $items->unique('seedling_request_id')->count(),
                            'total_items' => $items->sum('requested_quantity'),
                            'unique_items' => $uniqueItems
                        ]
                    ];
                })
                ->toArray();

            return $categoryStats;
        } catch (\Exception $e) {
            Log::error('Category Analysis Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get top requested items across all categories
     */
    private function getTopRequestedItems($baseQuery)
    {
        try {
            $requestIds = (clone $baseQuery)->pluck('id');

            $items = SeedlingRequestItem::with(['category', 'categoryItem'])
                ->whereIn('seedling_request_id', $requestIds)
                ->select(
                    'category_id',
                    'item_name',
                    DB::raw('SUM(requested_quantity) as total_quantity'),
                    DB::raw('COUNT(DISTINCT seedling_request_id) as request_count')
                )
                ->groupBy('category_id', 'item_name')
                ->orderBy('total_quantity', 'desc')
                ->take(15)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->item_name,
                        'category' => $item->category->name ?? 'Unknown',
                        'total_quantity' => $item->total_quantity,
                        'request_count' => $item->request_count
                    ];
                });

            return $items;
        } catch (\Exception $e) {
            Log::error('Top Items Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get least requested items across all categories
     */
    private function getLeastRequestedItems($baseQuery)
    {
        try {
            $requestIds = (clone $baseQuery)->pluck('id');

            $items = SeedlingRequestItem::with(['category', 'categoryItem'])
                ->whereIn('seedling_request_id', $requestIds)
                ->select(
                    'category_id',
                    'item_name',
                    DB::raw('SUM(requested_quantity) as total_quantity'),
                    DB::raw('COUNT(DISTINCT seedling_request_id) as request_count')
                )
                ->groupBy('category_id', 'item_name')
                ->orderBy('total_quantity', 'asc')
                ->take(15)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->item_name,
                        'category' => $item->category->name ?? 'Unknown',
                        'total_quantity' => $item->total_quantity,
                        'request_count' => $item->request_count
                    ];
                });

            return $items;
        } catch (\Exception $e) {
            Log::error('Least Requested Items Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get processing time analysis
     */
    private function getProcessingTimeAnalysis($baseQuery)
    {
        try {
            $processedRequests = (clone $baseQuery)->whereNotNull('reviewed_at')->get();

            $processingTimes = [];
            foreach ($processedRequests as $request) {
                if ($request->reviewed_at && $request->created_at) {
                    $processingTime = Carbon::parse($request->created_at)
                        ->diffInDays(Carbon::parse($request->reviewed_at));
                    $processingTimes[] = $processingTime;
                }
            }

            if (empty($processingTimes)) {
                return [
                    'avg_processing_days' => 0,
                    'min_processing_days' => 0,
                    'max_processing_days' => 0,
                    'processed_count' => 0
                ];
            }

            return [
                'avg_processing_days' => round(array_sum($processingTimes) / count($processingTimes), 2),
                'min_processing_days' => min($processingTimes),
                'max_processing_days' => max($processingTimes),
                'processed_count' => count($processingTimes)
            ];
        } catch (\Exception $e) {
            Log::error('Processing Time Error: ' . $e->getMessage());
            return [
                'avg_processing_days' => 0,
                'min_processing_days' => 0,
                'max_processing_days' => 0,
                'processed_count' => 0
            ];
        }
    }

    /**
     * Get seasonal analysis
     */
    private function getSeasonalAnalysis($baseQuery)
    {
        try {
            $seasonalData = (clone $baseQuery)->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as request_count'),
                    DB::raw('SUM(COALESCE(total_quantity, 0)) as total_quantity')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $seasons = [
                'Dry Season (Nov-Apr)' => ['months' => [11, 12, 1, 2, 3, 4], 'requests' => 0, 'quantity' => 0],
                'Wet Season (May-Oct)' => ['months' => [5, 6, 7, 8, 9, 10], 'requests' => 0, 'quantity' => 0]
            ];

            foreach ($seasonalData as $data) {
                if (in_array($data->month, $seasons['Dry Season (Nov-Apr)']['months'])) {
                    $seasons['Dry Season (Nov-Apr)']['requests'] += $data->request_count;
                    $seasons['Dry Season (Nov-Apr)']['quantity'] += $data->total_quantity;
                } else {
                    $seasons['Wet Season (May-Oct)']['requests'] += $data->request_count;
                    $seasons['Wet Season (May-Oct)']['quantity'] += $data->total_quantity;
                }
            }

            return $seasons;
        } catch (\Exception $e) {
            Log::error('Seasonal Analysis Error: ' . $e->getMessage());
            return [
                'Dry Season (Nov-Apr)' => ['months' => [11, 12, 1, 2, 3, 4], 'requests' => 0, 'quantity' => 0],
                'Wet Season (May-Oct)' => ['months' => [5, 6, 7, 8, 9, 10], 'requests' => 0, 'quantity' => 0]
            ];
        }
    }

    /**
     * Get supply alerts for low stock items
     */
    private function getSupplyAlerts()
    {
        try {
            $lowStockItems = CategoryItem::where('supply_alert_enabled', true)
                ->whereColumn('current_supply', '<=', 'reorder_point')
                ->with('category')
                ->orderBy('current_supply', 'asc')
                ->get()
                ->map(function($item) {
                    $percentage = $item->reorder_point > 0
                        ? round(($item->current_supply / $item->reorder_point) * 100, 1)
                        : 0;

                    return [
                        'name' => $item->name,
                        'category' => $item->category->display_name ?? 'Unknown',
                        'current' => $item->current_supply,
                        'reorder_point' => $item->reorder_point,
                        'minimum' => $item->minimum_supply,
                        'percentage' => $percentage,
                        'status' => $percentage <= 25 ? 'critical' : ($percentage <= 50 ? 'low' : 'warning')
                    ];
                });

            return $lowStockItems;
        } catch (\Exception $e) {
            Log::error('Supply Alerts Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get rejection analysis
     */
    private function getRejectionAnalysis($baseQuery)
    {
        try {
            $requestIds = (clone $baseQuery)->pluck('id');

            $rejectedItems = SeedlingRequestItem::whereIn('seedling_request_id', $requestIds)
                ->where('status', 'rejected')
                ->whereNotNull('rejection_reason')
                ->select('rejection_reason', DB::raw('count(*) as count'))
                ->groupBy('rejection_reason')
                ->orderBy('count', 'desc')
                ->get();

            return $rejectedItems;
        } catch (\Exception $e) {
            Log::error('Rejection Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get monthly barangay analysis
     */
    private function getMonthlyBarangayAnalysis($startDate, $endDate)
    {
        try {
            $monthlyData = SeedlingRequest::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    'barangay',
                    DB::raw('COUNT(*) as request_count'),
                    DB::raw('SUM(COALESCE(total_quantity, 0)) as total_quantity')
                )
                ->whereNotNull('barangay')
                ->where('barangay', '!=', '')
                ->groupBy('month', 'barangay')
                ->orderBy('month')
                ->orderBy('request_count', 'desc')
                ->get();

            $monthlyAnalysis = [];

            foreach ($monthlyData as $data) {
                $month = $data->month;

                if (!isset($monthlyAnalysis[$month])) {
                    $monthlyAnalysis[$month] = [
                        'month' => $month,
                        'barangays' => [],
                        'top_barangay' => null,
                        'least_barangay' => null,
                        'total_requests' => 0
                    ];
                }

                $monthlyAnalysis[$month]['barangays'][] = [
                    'barangay' => $data->barangay,
                    'requests' => $data->request_count,
                    'quantity' => $data->total_quantity
                ];

                $monthlyAnalysis[$month]['total_requests'] += $data->request_count;
            }

            foreach ($monthlyAnalysis as $month => &$analysis) {
                if (!empty($analysis['barangays'])) {
                    usort($analysis['barangays'], function($a, $b) {
                        return $b['requests'] - $a['requests'];
                    });

                    $analysis['top_barangay'] = $analysis['barangays'][0];
                    $analysis['least_barangay'] = end($analysis['barangays']);
                }
            }

            return collect($monthlyAnalysis)->values();
        } catch (\Exception $e) {
            Log::error('Monthly Barangay Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get inventory impact analysis
     */
    private function getInventoryImpactAnalysis($baseQuery)
    {
        try {
            $approvedRequests = (clone $baseQuery)->where('status', 'approved')->get();

            $inventoryImpact = [
                'total_items_distributed' => 0,
                'requests_fulfilled' => $approvedRequests->count(),
                'avg_fulfillment_quantity' => 0
            ];

            $totalDistributed = $approvedRequests->sum('approved_quantity') ?: 0;
            $inventoryImpact['total_items_distributed'] = $totalDistributed;
            $inventoryImpact['avg_fulfillment_quantity'] = $approvedRequests->count() > 0
                ? round($totalDistributed / $approvedRequests->count(), 2)
                : 0;

            return $inventoryImpact;
        } catch (\Exception $e) {
            Log::error('Inventory Impact Error: ' . $e->getMessage());
            return [
                'total_items_distributed' => 0,
                'requests_fulfilled' => 0,
                'avg_fulfillment_quantity' => 0
            ];
        }
    }

    /**
     * Calculate approval rate
     */
    private function calculateApprovalRate($total, $approved)
    {
        return $total > 0 ? round(($approved / $total) * 100, 2) : 0;
    }

    /**
     * Get default overview when errors occur
     */
    private function getDefaultOverview()
    {
        return [
            'total_requests' => 0,
            'approved_requests' => 0,
            'rejected_requests' => 0,
            'pending_requests' => 0,
            'total_quantity_requested' => 0,
            'total_quantity_approved' => 0,
            'approval_rate' => 0,
            'avg_request_size' => 0,
            'unique_applicants' => 0,
            'active_barangays' => 0,
            'change_percentage' => 0,
            'fulfillment_rate' => 0
        ];
    }
}
