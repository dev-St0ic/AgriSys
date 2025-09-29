<?php

namespace App\Http\Controllers;

use App\Models\SeedlingRequest;
use App\Services\SeedlingAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\SeedlingDSSService;

class SeedlingAnalyticsController extends Controller
{    
    protected SeedlingAnalyticsService $analyticsService;

    public function __construct(SeedlingAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display seedling analytics dashboard
     */
    public function index(Request $request)
    {
        try {
            // Date range filter with better defaults
            $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            // Validate dates
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            // Base query with date range
            $baseQuery = SeedlingRequest::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            // 1. Overview Statistics
            $overview = $this->getOverviewStatistics(clone $baseQuery);

            // 2. Request Status Analysis
            $statusAnalysis = $this->getStatusAnalysis(clone $baseQuery);

            // 3. Monthly Trends
            $monthlyTrends = $this->getMonthlyTrends($startDate, $endDate);

            // 4. Barangay Analysis
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
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('Analytics Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading analytics data. Please try again.');
        }
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStatistics($baseQuery)
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
                'active_barangays' => $activeBarangays
            ];
        } catch (\Exception $e) {
            Log::error('Overview Statistics Error: ' . $e->getMessage());
            return $this->getDefaultOverview();
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
     * Get category analysis (seeds, seedlings, fruits, ornamentals, fingerlings, fertilizers)
     */
    private function getCategoryAnalysis($baseQuery)
    {
        try {
            $requests = (clone $baseQuery)->get();

            $categoryStats = [
                'seeds' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'seedlings' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'fruits' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'ornamentals' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'fingerlings' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'fertilizers' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []]
            ];

            foreach ($requests as $request) {
                // Process all 6 categories
                $categories = ['seeds', 'seedlings', 'fruits', 'ornamentals', 'fingerlings', 'fertilizers'];
                
                foreach ($categories as $category) {
                    $items = $this->safeJsonDecode($request->{$category});
                    
                    if (!empty($items) && is_array($items)) {
                        $categoryStats[$category]['requests']++;
                        foreach ($items as $item) {
                            if (isset($item['name']) && isset($item['quantity'])) {
                                $quantity = (int) $item['quantity'];
                                $categoryStats[$category]['total_items'] += $quantity;
                                $name = trim($item['name']);
                                if (!empty($name)) {
                                    $categoryStats[$category]['unique_items'][$name] =
                                        ($categoryStats[$category]['unique_items'][$name] ?? 0) + $quantity;
                                }
                            }
                        }
                    }
                }
            }

            return $categoryStats;
        } catch (\Exception $e) {
            Log::error('Category Analysis Error: ' . $e->getMessage());
            return [
                'seeds' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'seedlings' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'fruits' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'ornamentals' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'fingerlings' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'fertilizers' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []]
            ];
        }
    }

    /**
     * Get top requested items across all categories
     */
    private function getTopRequestedItems($baseQuery)
    {
        try {
            $requests = (clone $baseQuery)->get();
            $allItems = [];
            
            $categories = ['seeds', 'seedlings', 'fruits', 'ornamentals', 'fingerlings', 'fertilizers'];

            foreach ($requests as $request) {
                foreach ($categories as $category) {
                    $items = $this->safeJsonDecode($request->{$category});
                    
                    if (!empty($items) && is_array($items)) {
                        foreach ($items as $item) {
                            if (isset($item['name']) && isset($item['quantity'])) {
                                $name = trim($item['name']);
                                if (!empty($name)) {
                                    $key = $category . '_' . $name;
                                    $quantity = (int) $item['quantity'];
                                    $allItems[$key] = [
                                        'name' => $name,
                                        'category' => $category,
                                        'total_quantity' => ($allItems[$key]['total_quantity'] ?? 0) + $quantity,
                                        'request_count' => ($allItems[$key]['request_count'] ?? 0) + 1
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            // Sort by total quantity and return top 15
            return collect($allItems)
                ->sortByDesc('total_quantity')
                ->take(15)
                ->values();
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
            $requests = (clone $baseQuery)->get();
            $allItems = [];
            
            $categories = ['seeds', 'seedlings', 'fruits', 'ornamentals', 'fingerlings', 'fertilizers'];

            foreach ($requests as $request) {
                foreach ($categories as $category) {
                    $items = $this->safeJsonDecode($request->{$category});
                    
                    if (!empty($items) && is_array($items)) {
                        foreach ($items as $item) {
                            if (isset($item['name']) && isset($item['quantity'])) {
                                $name = trim($item['name']);
                                if (!empty($name)) {
                                    $key = $category . '_' . $name;
                                    $quantity = (int) $item['quantity'];
                                    $allItems[$key] = [
                                        'name' => $name,
                                        'category' => $category,
                                        'total_quantity' => ($allItems[$key]['total_quantity'] ?? 0) + $quantity,
                                        'request_count' => ($allItems[$key]['request_count'] ?? 0) + 1
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            // Sort by total quantity ascending and return least requested items
            return collect($allItems)
                ->sortBy('total_quantity')
                ->take(15)
                ->values();
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
     * Get monthly barangay analysis showing top and least active barangays per month
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

            // Determine top and least active barangays for each month
            foreach ($monthlyAnalysis as $month => &$analysis) {
                if (!empty($analysis['barangays'])) {
                    // Sort by request count
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
     * Safe JSON decode with fallback
     */
    private function safeJsonDecode($json)
    {
        if (empty($json)) {
            return [];
        }

        if (is_string($json)) {
            $decoded = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return is_array($json) ? $json : [];
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
            'active_barangays' => 0
        ];
    }

    /**
     * Generate Decision Support System (DSS) Report
     */
    public function generateDSSReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            $pdf = $this->analyticsService->generateDSSReport($startDate, $endDate);
            
            return $pdf->download('seedling_dss_report_' . date('Y-m-d_H-i-s') . '.pdf');
        } catch (\Exception $e) {
            Log::error('DSS Report Generation Error: ' . $e->getMessage());
            return back()->with('error', 'Error generating DSS report: ' . $e->getMessage());
        }
    }

    /**
     * Generate comprehensive DSS report
     */
    public function dssReport(Request $request)
    {
        try {
            // Validate request dates
            $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Validate date format and range
            if (!$this->validateDateRange($startDate, $endDate)) {
                return back()->with('error', 'Invalid date range specified.');
            }

            // Build base query with date filters
            $baseQuery = SeedlingRequest::query()
                ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                });

            // Gather all required data
            $overview = $this->getOverviewStatistics($baseQuery);
            $analytics = $this->getAnalyticsData($startDate, $endDate);
            $barangayAnalysis = $this->getBarangayAnalysis($baseQuery);
            $topItems = $this->getTopRequestedItems($baseQuery);
            $leastRequestedItems = $this->getLeastRequestedItems($baseQuery);

            // Generate AI insights
            $insights = $this->dssService->generateInsights(array_merge($overview, [
                'analytics' => $analytics,
                'barangayAnalysis' => $barangayAnalysis,
                'topItems' => $topItems,
                'leastRequestedItems' => $leastRequestedItems
            ]));

            // Prepare view data with safety checks
            return view('admin.analytics.seedlings.dss-report', [
                'overview' => $overview ?? [],
                'insights' => $insights ?? [],
                'analytics' => $analytics ?? [],
                'barangayAnalysis' => $barangayAnalysis ?? collect(),
                'topItems' => $topItems ?? collect(),
                'leastRequestedItems' => $leastRequestedItems ?? collect(),
                'period' => $this->formatPeriod($startDate, $endDate),
                'generated_at' => now()->format('F j, Y \a\t g:i A'),
                'ai_confidence' => $insights['ai_confidence'] ?? 'medium',
                'title' => 'Agricultural DSS Analysis Report - ' . config('app.name')
            ]);
        } catch (\Exception $e) {
            Log::error('DSS Report Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'dates' => ['start' => $startDate ?? null, 'end' => $endDate ?? null]
            ]);

            return back()->with('error', 'Failed to generate DSS report. Please try again or contact support.');
        }
    }

    /**
     * Validate date range for report generation
     */
    private function validateDateRange(?string $startDate, ?string $endDate): bool
    {
        if (!$startDate || !$endDate) {
            return true; // Allow null dates to use defaults
        }

        try {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            // Ensure dates are not in future and start is before end
            return $start <= $end 
                && $end <= now()
                && $start->diffInYears($end) <= 2; // Max 2 years range
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get analytics data for specified period
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    private function getAnalyticsData($startDate, $endDate): array
    {
        try {
            return $this->analyticsService->getAnalyticsData($startDate, $endDate);
        } catch (\Exception $e) {
            Log::error('Analytics Data Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'dates' => [
                    'start' => $startDate,
                    'end' => $endDate
                ]
            ]);

            // Return safe default structure
            return [
                'overview' => $this->getDefaultOverview(),
                'monthlyTrends' => [],
                'barangayAnalysis' => collect(),
                'categoryAnalysis' => [],
                'statusAnalysis' => [
                    'counts' => [],
                    'percentages' => [],
                    'total' => 0
                ],
                'processingTimeAnalysis' => [
                    'avg_processing_days' => 0,
                    'min_processing_days' => 0,
                    'max_processing_days' => 0,
                    'processed_count' => 0
                ]
            ];
        }
    }
}
