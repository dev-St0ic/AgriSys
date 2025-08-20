<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InventoryAnalyticsController extends Controller
{
    /**
     * Display inventory analytics dashboard
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
            $baseQuery = Inventory::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
            // 1. Overview Statistics
            $overview = $this->getOverviewStatistics(clone $baseQuery);
            
            // 2. Stock Level Analysis
            $stockAnalysis = $this->getStockAnalysis(clone $baseQuery);
            
            // 3. Monthly Trends
            $monthlyTrends = $this->getMonthlyTrends($startDate, $endDate);
            
            // 4. Category Analysis
            $categoryAnalysis = $this->getCategoryAnalysis(clone $baseQuery);
            
            // 5. Variety Analysis
            $varietyAnalysis = $this->getVarietyAnalysis(clone $baseQuery);
            
            // 6. Stock Movement Analysis
            $stockMovementAnalysis = $this->getStockMovementAnalysis(clone $baseQuery);
            
            // 7. User Activity Analysis
            $userActivityAnalysis = $this->getUserActivityAnalysis(clone $baseQuery);
            
            // 8. Restock Patterns
            $restockPatterns = $this->getRestockPatterns(clone $baseQuery);
            
            // 9. Unit Distribution Analysis
            $unitAnalysis = $this->getUnitAnalysis(clone $baseQuery);
            
            // 10. Performance Metrics
            $performanceMetrics = $this->getPerformanceMetrics(clone $baseQuery);
            
            // 11. Stock Efficiency Analysis
            $efficiencyAnalysis = $this->getStockEfficiencyAnalysis(clone $baseQuery);
            
            // 12. Item Activity Patterns
            $activityPatterns = $this->getActivityPatterns(clone $baseQuery);

            return view('admin.analytics.inventory', compact(
                'overview',
                'stockAnalysis',
                'monthlyTrends',
                'categoryAnalysis',
                'varietyAnalysis',
                'stockMovementAnalysis',
                'userActivityAnalysis',
                'restockPatterns',
                'unitAnalysis',
                'performanceMetrics',
                'efficiencyAnalysis',
                'activityPatterns',
                'startDate',
                'endDate'
            ));

        } catch (\Exception $e) {
            Log::error('Inventory Analytics Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading inventory analytics data. Please try again.');
        }
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStatistics($baseQuery)
    {
        try {
            $totalItems = $baseQuery->count();
            $activeItems = (clone $baseQuery)->where('is_active', true)->count();
            $inactiveItems = (clone $baseQuery)->where('is_active', false)->count();
            $lowStockItems = (clone $baseQuery)->whereRaw('current_stock <= minimum_stock')->count();
            $outOfStockItems = (clone $baseQuery)->where('current_stock', '<=', 0)->count();
            $overstockedItems = (clone $baseQuery)->whereRaw('current_stock >= maximum_stock')->count();
            
            // Count unique categories and varieties
            $uniqueCategories = (clone $baseQuery)->whereNotNull('category')->distinct()->count('category');
            $uniqueVarieties = (clone $baseQuery)->whereNotNull('variety')->distinct()->count('variety');
            
            // Calculate total stock value (if you have price data, otherwise stock quantity)
            $totalStockQuantity = (clone $baseQuery)->sum('current_stock');
            $totalMinStock = (clone $baseQuery)->sum('minimum_stock');
            $totalMaxStock = (clone $baseQuery)->sum('maximum_stock');
            
            // Items recently restocked (last 30 days)
            $recentlyRestocked = (clone $baseQuery)->where('last_restocked', '>=', now()->subDays(30))->count();
            
            // Calculate stock utilization rate
            $stockUtilizationRate = $totalMaxStock > 0 ? round(($totalStockQuantity / $totalMaxStock) * 100, 2) : 0;
            
            return [
                'total_items' => $totalItems,
                'active_items' => $activeItems,
                'inactive_items' => $inactiveItems,
                'low_stock_items' => $lowStockItems,
                'out_of_stock_items' => $outOfStockItems,
                'overstocked_items' => $overstockedItems,
                'activity_rate' => $totalItems > 0 ? round(($activeItems / $totalItems) * 100, 2) : 0,
                'stock_alert_rate' => $totalItems > 0 ? round((($lowStockItems + $outOfStockItems) / $totalItems) * 100, 2) : 0,
                'unique_categories' => $uniqueCategories,
                'unique_varieties' => $uniqueVarieties,
                'total_stock_quantity' => $totalStockQuantity,
                'total_min_stock' => $totalMinStock,
                'total_max_stock' => $totalMaxStock,
                'recently_restocked' => $recentlyRestocked,
                'stock_utilization_rate' => $stockUtilizationRate
            ];
        } catch (\Exception $e) {
            Log::error('Inventory Overview Statistics Error: ' . $e->getMessage());
            return $this->getDefaultOverview();
        }
    }

    /**
     * Get stock level analysis
     */
    private function getStockAnalysis($baseQuery)
    {
        try {
            $stockLevels = (clone $baseQuery)->select(
                DB::raw("
                    CASE 
                        WHEN current_stock = 0 THEN 'Out of Stock'
                        WHEN current_stock <= minimum_stock THEN 'Low Stock'
                        WHEN current_stock >= maximum_stock THEN 'Overstocked'
                        ELSE 'Normal'
                    END as stock_status"
                ),
                DB::raw('count(*) as count'),
                DB::raw('sum(current_stock) as total_quantity')
            )
            ->groupBy('stock_status')
            ->get();

            $total = $stockLevels->sum('count');
            $stockPercentages = [];
            
            foreach ($stockLevels as $level) {
                $stockPercentages[$level->stock_status] = $total > 0 ? round(($level->count / $total) * 100, 2) : 0;
            }

            // Ensure all statuses are present
            $defaultStatuses = ['Normal' => 0, 'Low Stock' => 0, 'Out of Stock' => 0, 'Overstocked' => 0];
            $stockCounts = [];
            $stockQuantities = [];
            
            foreach ($stockLevels as $level) {
                $stockCounts[$level->stock_status] = $level->count;
                $stockQuantities[$level->stock_status] = $level->total_quantity;
            }
            
            $stockCounts = array_merge($defaultStatuses, $stockCounts);
            
            return [
                'counts' => $stockCounts,
                'quantities' => $stockQuantities,
                'percentages' => array_merge($defaultStatuses, $stockPercentages),
                'total' => $total
            ];
        } catch (\Exception $e) {
            Log::error('Inventory Stock Analysis Error: ' . $e->getMessage());
            return [
                'counts' => ['Normal' => 0, 'Low Stock' => 0, 'Out of Stock' => 0, 'Overstocked' => 0],
                'quantities' => ['Normal' => 0, 'Low Stock' => 0, 'Out of Stock' => 0, 'Overstocked' => 0],
                'percentages' => ['Normal' => 0, 'Low Stock' => 0, 'Out of Stock' => 0, 'Overstocked' => 0],
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
            $trends = Inventory::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_items'),
                    DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_items'),
                    DB::raw('SUM(CASE WHEN current_stock <= minimum_stock THEN 1 ELSE 0 END) as low_stock_items'),
                    DB::raw('SUM(CASE WHEN current_stock <= 0 THEN 1 ELSE 0 END) as out_of_stock_items'),
                    DB::raw('SUM(current_stock) as total_stock_quantity'),
                    DB::raw('COUNT(DISTINCT category) as unique_categories'),
                    DB::raw('AVG(current_stock) as avg_stock_level')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return $trends;
        } catch (\Exception $e) {
            Log::error('Inventory Monthly Trends Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get category analysis
     */
    private function getCategoryAnalysis($baseQuery)
    {
        try {
            $categoryStats = (clone $baseQuery)->select(
                    'category',
                    DB::raw('COUNT(*) as total_items'),
                    DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_items'),
                    DB::raw('SUM(CASE WHEN current_stock <= minimum_stock THEN 1 ELSE 0 END) as low_stock_items'),
                    DB::raw('SUM(CASE WHEN current_stock <= 0 THEN 1 ELSE 0 END) as out_of_stock_items'),
                    DB::raw('SUM(current_stock) as total_stock_quantity'),
                    DB::raw('AVG(current_stock) as avg_stock_level'),
                    DB::raw('COUNT(DISTINCT variety) as unique_varieties'),
                    DB::raw('COUNT(DISTINCT unit) as different_units')
                )
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->groupBy('category')
                ->orderBy('total_items', 'desc')
                ->get();

            return $categoryStats;
        } catch (\Exception $e) {
            Log::error('Inventory Category Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get variety analysis
     */
    private function getVarietyAnalysis($baseQuery)
    {
        try {
            $varietyStats = (clone $baseQuery)->select(
                    'variety',
                    'category',
                    DB::raw('COUNT(*) as total_items'),
                    DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_items'),
                    DB::raw('SUM(current_stock) as total_stock_quantity'),
                    DB::raw('AVG(current_stock) as avg_stock_level'),
                    DB::raw('SUM(CASE WHEN current_stock <= minimum_stock THEN 1 ELSE 0 END) as items_needing_restock')
                )
                ->whereNotNull('variety')
                ->where('variety', '!=', '')
                ->groupBy('variety', 'category')
                ->orderBy('total_stock_quantity', 'desc')
                ->limit(20) // Top 20 varieties by stock
                ->get();

            return $varietyStats;
        } catch (\Exception $e) {
            Log::error('Inventory Variety Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get stock movement analysis
     */
    private function getStockMovementAnalysis($baseQuery)
    {
        try {
            // Items by stock turnover potential (based on min/max ratios)
            $stockMovement = (clone $baseQuery)->select(
                    'item_name',
                    'category',
                    'current_stock',
                    'minimum_stock',
                    'maximum_stock',
                    'last_restocked',
                    DB::raw('CASE 
                        WHEN maximum_stock > 0 THEN ROUND((current_stock / maximum_stock) * 100, 2)
                        ELSE 0
                    END as stock_fill_percentage'),
                    DB::raw('CASE 
                        WHEN minimum_stock > 0 THEN ROUND(current_stock / minimum_stock, 2)
                        ELSE 0
                    END as stock_coverage_ratio'),
                    DB::raw('DATEDIFF(NOW(), last_restocked) as days_since_restock')
                )
                ->whereNotNull('last_restocked')
                ->orderBy('days_since_restock', 'desc')
                ->limit(20)
                ->get();

            return $stockMovement;
        } catch (\Exception $e) {
            Log::error('Inventory Stock Movement Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get user activity analysis
     */
    private function getUserActivityAnalysis($baseQuery)
    {
        try {
            $creatorStats = (clone $baseQuery)->select(
                    'created_by',
                    DB::raw('COUNT(*) as items_created'),
                    DB::raw('COUNT(DISTINCT category) as categories_managed'),
                    DB::raw('SUM(current_stock) as total_stock_managed'),
                    DB::raw('AVG(current_stock) as avg_stock_per_item')
                )
                ->whereNotNull('created_by')
                ->with('creator:id,name,email')
                ->groupBy('created_by')
                ->orderBy('items_created', 'desc')
                ->get();

            $updaterStats = (clone $baseQuery)->select(
                    'updated_by',
                    DB::raw('COUNT(*) as items_updated'),
                    DB::raw('MAX(updated_at) as last_update_time')
                )
                ->whereNotNull('updated_by')
                ->with('updater:id,name,email')
                ->groupBy('updated_by')
                ->orderBy('items_updated', 'desc')
                ->get();

            return [
                'creators' => $creatorStats,
                'updaters' => $updaterStats
            ];
        } catch (\Exception $e) {
            Log::error('Inventory User Activity Error: ' . $e->getMessage());
            return [
                'creators' => collect([]),
                'updaters' => collect([])
            ];
        }
    }

    /**
     * Get restock patterns
     */
    private function getRestockPatterns($baseQuery)
    {
        try {
            // Items restocked by month
            $monthlyRestocks = (clone $baseQuery)->select(
                    DB::raw('DATE_FORMAT(last_restocked, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as items_restocked'),
                    DB::raw('COUNT(DISTINCT category) as categories_restocked')
                )
                ->whereNotNull('last_restocked')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();

            // Restock frequency by category
            $categoryRestocks = (clone $baseQuery)->select(
                    'category',
                    DB::raw('COUNT(*) as total_items'),
                    DB::raw('SUM(CASE WHEN last_restocked >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as restocked_last_30_days'),
                    DB::raw('AVG(DATEDIFF(NOW(), last_restocked)) as avg_days_since_restock')
                )
                ->whereNotNull('category')
                ->whereNotNull('last_restocked')
                ->groupBy('category')
                ->get();

            return [
                'monthly' => $monthlyRestocks,
                'by_category' => $categoryRestocks
            ];
        } catch (\Exception $e) {
            Log::error('Inventory Restock Patterns Error: ' . $e->getMessage());
            return [
                'monthly' => collect([]),
                'by_category' => collect([])
            ];
        }
    }

    /**
     * Get unit distribution analysis
     */
    private function getUnitAnalysis($baseQuery)
    {
        try {
            $unitStats = (clone $baseQuery)->select(
                    'unit',
                    DB::raw('COUNT(*) as total_items'),
                    DB::raw('SUM(current_stock) as total_quantity'),
                    DB::raw('AVG(current_stock) as avg_quantity_per_item'),
                    DB::raw('COUNT(DISTINCT category) as categories_using_unit')
                )
                ->whereNotNull('unit')
                ->where('unit', '!=', '')
                ->groupBy('unit')
                ->orderBy('total_items', 'desc')
                ->get();

            return $unitStats;
        } catch (\Exception $e) {
            Log::error('Inventory Unit Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics($baseQuery)
    {
        try {
            $metrics = [];

            // Stock efficiency metrics
            $total = (clone $baseQuery)->count();
            $activeItems = (clone $baseQuery)->where('is_active', true)->count();
            $metrics['inventory_utilization_rate'] = $total > 0 ? round(($activeItems / $total) * 100, 2) : 0;

            // Stock health score
            $lowStock = (clone $baseQuery)->whereRaw('current_stock <= minimum_stock')->count();
            $outOfStock = (clone $baseQuery)->where('current_stock', '<=', 0)->count();
            $metrics['stock_health_score'] = $total > 0 ? round((($total - $lowStock - $outOfStock) / $total) * 100, 2) : 0;

            // Category diversity
            $uniqueCategories = (clone $baseQuery)->distinct()->count('category');
            $metrics['category_diversity'] = $uniqueCategories;

            // Average stock levels
            $avgCurrentStock = (clone $baseQuery)->avg('current_stock') ?? 0;
            $avgMinStock = (clone $baseQuery)->avg('minimum_stock') ?? 0;
            $avgMaxStock = (clone $baseQuery)->avg('maximum_stock') ?? 0;
            
            $metrics['avg_current_stock'] = round($avgCurrentStock, 2);
            $metrics['avg_min_stock'] = round($avgMinStock, 2);
            $metrics['avg_max_stock'] = round($avgMaxStock, 2);

            // Restock frequency
            $recentRestocks = (clone $baseQuery)->where('last_restocked', '>=', now()->subDays(30))->count();
            $metrics['recent_restock_rate'] = $total > 0 ? round(($recentRestocks / $total) * 100, 2) : 0;

            return $metrics;
        } catch (\Exception $e) {
            Log::error('Inventory Performance Metrics Error: ' . $e->getMessage());
            return [
                'inventory_utilization_rate' => 0,
                'stock_health_score' => 0,
                'category_diversity' => 0,
                'avg_current_stock' => 0,
                'avg_min_stock' => 0,
                'avg_max_stock' => 0,
                'recent_restock_rate' => 0
            ];
        }
    }

    /**
     * Get stock efficiency analysis
     */
    private function getStockEfficiencyAnalysis($baseQuery)
    {
        try {
            $efficiency = [];

            // Items with optimal stock levels (between min and max)
            $optimalStock = (clone $baseQuery)->whereRaw('current_stock >= minimum_stock AND current_stock <= maximum_stock')->count();
            $total = (clone $baseQuery)->count();
            $efficiency['optimal_stock_percentage'] = $total > 0 ? round(($optimalStock / $total) * 100, 2) : 0;

            // Stock buffer analysis
            $stockBuffer = (clone $baseQuery)->select(
                DB::raw('AVG(CASE WHEN maximum_stock > minimum_stock 
                         THEN (current_stock - minimum_stock) / (maximum_stock - minimum_stock) * 100 
                         ELSE 0 END) as avg_buffer_utilization')
            )->first();
            
            $efficiency['avg_buffer_utilization'] = round($stockBuffer->avg_buffer_utilization ?? 0, 2);

            // Categories with best stock management
            $bestCategories = (clone $baseQuery)->select(
                    'category',
                    DB::raw('COUNT(*) as total_items'),
                    DB::raw('SUM(CASE WHEN current_stock >= minimum_stock AND current_stock <= maximum_stock THEN 1 ELSE 0 END) as optimal_items'),
                    DB::raw('ROUND(SUM(CASE WHEN current_stock >= minimum_stock AND current_stock <= maximum_stock THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as efficiency_score')
                )
                ->whereNotNull('category')
                ->groupBy('category')
                ->orderBy('efficiency_score', 'desc')
                ->limit(5)
                ->get();

            $efficiency['best_managed_categories'] = $bestCategories;

            return $efficiency;
        } catch (\Exception $e) {
            Log::error('Inventory Efficiency Analysis Error: ' . $e->getMessage());
            return [
                'optimal_stock_percentage' => 0,
                'avg_buffer_utilization' => 0,
                'best_managed_categories' => collect([])
            ];
        }
    }

    /**
     * Get item activity patterns
     */
    private function getActivityPatterns($baseQuery)
    {
        try {
            // Items created by day of week
            $dayOfWeekStats = (clone $baseQuery)->select(
                    DB::raw('DAYNAME(created_at) as day_name'),
                    DB::raw('DAYOFWEEK(created_at) as day_number'),
                    DB::raw('COUNT(*) as items_created')
                )
                ->groupBy('day_name', 'day_number')
                ->orderBy('day_number')
                ->get();

            // Items created by hour
            $hourlyStats = (clone $baseQuery)->select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as items_created')
                )
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            // Most recently created items
            $recentItems = (clone $baseQuery)->select('item_name', 'category', 'current_stock', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return [
                'day_of_week' => $dayOfWeekStats,
                'hourly' => $hourlyStats,
                'recent_items' => $recentItems
            ];
        } catch (\Exception $e) {
            Log::error('Inventory Activity Patterns Error: ' . $e->getMessage());
            return [
                'day_of_week' => collect([]),
                'hourly' => collect([]),
                'recent_items' => collect([])
            ];
        }
    }

    /**
     * Get default overview when errors occur
     */
    private function getDefaultOverview()
    {
        return [
            'total_items' => 0,
            'active_items' => 0,
            'inactive_items' => 0,
            'low_stock_items' => 0,
            'out_of_stock_items' => 0,
            'overstocked_items' => 0,
            'activity_rate' => 0,
            'stock_alert_rate' => 0,
            'unique_categories' => 0,
            'unique_varieties' => 0,
            'total_stock_quantity' => 0,
            'total_min_stock' => 0,
            'total_max_stock' => 0,
            'recently_restocked' => 0,
            'stock_utilization_rate' => 0
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
            
            $baseQuery = Inventory::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
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
                'stock_analysis' => $this->getStockAnalysis(clone $baseQuery),
                'monthly_trends' => $this->getMonthlyTrends($startDate, $endDate)->toArray(),
                'category_analysis' => $this->getCategoryAnalysis(clone $baseQuery)->toArray(),
                'variety_analysis' => $this->getVarietyAnalysis(clone $baseQuery)->toArray(),
                'stock_movement' => $this->getStockMovementAnalysis(clone $baseQuery)->toArray(),
                'user_activity' => $this->getUserActivityAnalysis(clone $baseQuery),
                'restock_patterns' => $this->getRestockPatterns(clone $baseQuery),
                'unit_analysis' => $this->getUnitAnalysis(clone $baseQuery)->toArray(),
                'performance_metrics' => $this->getPerformanceMetrics(clone $baseQuery),
                'efficiency_analysis' => $this->getStockEfficiencyAnalysis(clone $baseQuery),
                'activity_patterns' => $this->getActivityPatterns(clone $baseQuery)
            ];
            
            $filename = 'inventory-analytics-' . $startDate . '-to-' . $endDate . '.json';
            
            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Inventory Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting inventory data: ' . $e->getMessage());
        }
    }

    /**
     * Get legacy date range (for backward compatibility)
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
        }

        return [
            'start' => $start,
            'end' => $end
        ];
    }
}