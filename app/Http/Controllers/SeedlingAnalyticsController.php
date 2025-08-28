<?php

namespace App\Http\Controllers;

use App\Models\SeedlingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SeedlingAnalyticsController extends Controller
{
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

            // 7. Request Processing Time Analysis
            $processingTimeAnalysis = $this->getProcessingTimeAnalysis(clone $baseQuery);

            // 8. Seasonal Analysis
            $seasonalAnalysis = $this->getSeasonalAnalysis(clone $baseQuery);

            // 9. Inventory Impact Analysis
            $inventoryImpact = $this->getInventoryImpactAnalysis(clone $baseQuery);

            return view('admin.analytics.seedlings', compact(
                'overview',
                'statusAnalysis',
                'monthlyTrends',
                'barangayAnalysis',
                'categoryAnalysis',
                'topItems',
                'processingTimeAnalysis',
                'seasonalAnalysis',
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
     * Get category analysis (vegetables, fruits, fertilizers)
     */
    private function getCategoryAnalysis($baseQuery)
    {
        try {
            $requests = (clone $baseQuery)->get();

            $categoryStats = [
                'vegetables' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'fruits' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'fertilizers' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []]
            ];

            foreach ($requests as $request) {
                // Handle JSON decoding safely
                $vegetables = $this->safeJsonDecode($request->vegetables);
                $fruits = $this->safeJsonDecode($request->fruits);
                $fertilizers = $this->safeJsonDecode($request->fertilizers);

                // Vegetables analysis
                if (!empty($vegetables) && is_array($vegetables)) {
                    $categoryStats['vegetables']['requests']++;
                    foreach ($vegetables as $item) {
                        if (isset($item['name']) && isset($item['quantity'])) {
                            $quantity = (int) $item['quantity'];
                            $categoryStats['vegetables']['total_items'] += $quantity;
                            $name = trim($item['name']);
                            if (!empty($name)) {
                                $categoryStats['vegetables']['unique_items'][$name] =
                                    ($categoryStats['vegetables']['unique_items'][$name] ?? 0) + $quantity;
                            }
                        }
                    }
                }

                // Fruits analysis
                if (!empty($fruits) && is_array($fruits)) {
                    $categoryStats['fruits']['requests']++;
                    foreach ($fruits as $item) {
                        if (isset($item['name']) && isset($item['quantity'])) {
                            $quantity = (int) $item['quantity'];
                            $categoryStats['fruits']['total_items'] += $quantity;
                            $name = trim($item['name']);
                            if (!empty($name)) {
                                $categoryStats['fruits']['unique_items'][$name] =
                                    ($categoryStats['fruits']['unique_items'][$name] ?? 0) + $quantity;
                            }
                        }
                    }
                }

                // Fertilizers analysis
                if (!empty($fertilizers) && is_array($fertilizers)) {
                    $categoryStats['fertilizers']['requests']++;
                    foreach ($fertilizers as $item) {
                        if (isset($item['name']) && isset($item['quantity'])) {
                            $quantity = (int) $item['quantity'];
                            $categoryStats['fertilizers']['total_items'] += $quantity;
                            $name = trim($item['name']);
                            if (!empty($name)) {
                                $categoryStats['fertilizers']['unique_items'][$name] =
                                    ($categoryStats['fertilizers']['unique_items'][$name] ?? 0) + $quantity;
                            }
                        }
                    }
                }
            }

            return $categoryStats;
        } catch (\Exception $e) {
            Log::error('Category Analysis Error: ' . $e->getMessage());
            return [
                'vegetables' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
                'fruits' => ['requests' => 0, 'total_items' => 0, 'unique_items' => []],
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

            foreach ($requests as $request) {
                // Process vegetables
                $vegetables = $this->safeJsonDecode($request->vegetables);
                if (!empty($vegetables) && is_array($vegetables)) {
                    foreach ($vegetables as $item) {
                        if (isset($item['name']) && isset($item['quantity'])) {
                            $name = trim($item['name']);
                            if (!empty($name)) {
                                $key = 'vegetables_' . $name;
                                $quantity = (int) $item['quantity'];
                                $allItems[$key] = [
                                    'name' => $name,
                                    'category' => 'vegetables',
                                    'total_quantity' => ($allItems[$key]['total_quantity'] ?? 0) + $quantity,
                                    'request_count' => ($allItems[$key]['request_count'] ?? 0) + 1
                                ];
                            }
                        }
                    }
                }

                // Process fruits
                $fruits = $this->safeJsonDecode($request->fruits);
                if (!empty($fruits) && is_array($fruits)) {
                    foreach ($fruits as $item) {
                        if (isset($item['name']) && isset($item['quantity'])) {
                            $name = trim($item['name']);
                            if (!empty($name)) {
                                $key = 'fruits_' . $name;
                                $quantity = (int) $item['quantity'];
                                $allItems[$key] = [
                                    'name' => $name,
                                    'category' => 'fruits',
                                    'total_quantity' => ($allItems[$key]['total_quantity'] ?? 0) + $quantity,
                                    'request_count' => ($allItems[$key]['request_count'] ?? 0) + 1
                                ];
                            }
                        }
                    }
                }

                // Process fertilizers
                $fertilizers = $this->safeJsonDecode($request->fertilizers);
                if (!empty($fertilizers) && is_array($fertilizers)) {
                    foreach ($fertilizers as $item) {
                        if (isset($item['name']) && isset($item['quantity'])) {
                            $name = trim($item['name']);
                            if (!empty($name)) {
                                $key = 'fertilizers_' . $name;
                                $quantity = (int) $item['quantity'];
                                $allItems[$key] = [
                                    'name' => $name,
                                    'category' => 'fertilizers',
                                    'total_quantity' => ($allItems[$key]['total_quantity'] ?? 0) + $quantity,
                                    'request_count' => ($allItems[$key]['request_count'] ?? 0) + 1
                                ];
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
     * Export analytics data
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            // Validate dates
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            $baseQuery = SeedlingRequest::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            $data = [
                'export_info' => [
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ],
                    'generated_by' => auth()->user()->name ?? 'System'
                ],
                'overview' => $this->getOverviewStatistics(clone $baseQuery),
                'status_analysis' => $this->getStatusAnalysis(clone $baseQuery),
                'monthly_trends' => $this->getMonthlyTrends($startDate, $endDate)->toArray(),
                'barangay_analysis' => $this->getBarangayAnalysis(clone $baseQuery)->toArray(),
                'category_analysis' => $this->getCategoryAnalysis(clone $baseQuery),
                'top_items' => $this->getTopRequestedItems(clone $baseQuery)->toArray(),
                'processing_time' => $this->getProcessingTimeAnalysis(clone $baseQuery),
                'seasonal_analysis' => $this->getSeasonalAnalysis(clone $baseQuery),
                'inventory_impact' => $this->getInventoryImpactAnalysis(clone $baseQuery)
            ];

            $filename = 'seedling-analytics-' . $startDate . '-to-' . $endDate . '.json';

            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }
}
