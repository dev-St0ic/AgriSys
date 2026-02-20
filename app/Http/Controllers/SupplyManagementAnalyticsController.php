<?php

namespace App\Http\Controllers;

use App\Models\CategoryItem;
use App\Models\RequestCategory;
use App\Models\ItemSupplyLog;
use App\Models\SeedlingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SupplyManagementAnalyticsController extends Controller
{
    /**
     * Display Supply Management analytics dashboard
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

            // 1. Overview Statistics
            $overview = $this->getOverviewStatistics();

            // 2. Supply Level Analysis
            $supplyLevelAnalysis = $this->getSupplyLevelAnalysis();

            // 3. Supply Trends (Monthly)
            $supplyTrends = $this->getSupplyTrends($startDate, $endDate);

            // 4. Transaction Analysis
            $transactionAnalysis = $this->getTransactionAnalysis($startDate, $endDate);

            // 5. Category Performance
            $categoryPerformance = $this->getCategoryPerformance();

            // 6. Top Items Analysis
            $topItemsAnalysis = $this->getTopItemsAnalysis($startDate, $endDate);

            // 7. Supply Alerts
            $supplyAlerts = $this->getSupplyAlerts();

            // 8. Fulfillment Analysis
            $fulfillmentAnalysis = $this->getFulfillmentAnalysis($startDate, $endDate);

            // 8.1. Recently Fulfilled Items
            $recentlyFulfilledItems = $this->getRecentlyFulfilledItems($startDate, $endDate);

            // 9. Supply Efficiency Metrics
            $efficiencyMetrics = $this->getEfficiencyMetrics($startDate, $endDate);

            // 10. Turnover Analysis
            $turnoverAnalysis = $this->getTurnoverAnalysis($startDate, $endDate);

            // 11. Loss and Waste Analysis
            $lossAnalysis = $this->getLossAnalysis($startDate, $endDate);

            // 12. Restock Recommendations
            $restockRecommendations = $this->getRestockRecommendations();

            return view('admin.analytics.supply-management', compact(
                'overview',
                'supplyLevelAnalysis',
                'supplyTrends',
                'transactionAnalysis',
                'categoryPerformance',
                'topItemsAnalysis',
                'supplyAlerts',
                'fulfillmentAnalysis',
                'recentlyFulfilledItems',
                'efficiencyMetrics',
                'turnoverAnalysis',
                'lossAnalysis',
                'restockRecommendations',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('Supply Management Analytics Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading supply analytics data. Please try again.');
        }
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStatistics()
    {
        try {
            $totalItems = CategoryItem::count();
            $activeItems = CategoryItem::where('is_active', true)->count();
            $totalSupply = CategoryItem::sum('current_supply');
            $totalCategories = RequestCategory::count();
            $activeCategories = RequestCategory::where('is_active', true)->count();

            // Supply status counts
            $lowSupplyItems = CategoryItem::where('supply_alert_enabled', true)
                ->whereColumn('current_supply', '<=', 'reorder_point')
                ->where('current_supply', '>', 0)
                ->count();

            $outOfStockItems = CategoryItem::where('current_supply', 0)->count();

            $healthyStockItems = CategoryItem::where('current_supply', '>', 0)
                ->where(function($q) {
                    $q->where('supply_alert_enabled', false)
                      ->orWhereColumn('current_supply', '>', 'reorder_point');
                })
                ->count();

            // Average supply per item
            $avgSupplyPerItem = $activeItems > 0 ? round($totalSupply / $activeItems, 2) : 0;

            // Total value estimate (if you have price field)
            $itemsWithMinSupply = CategoryItem::where('minimum_supply', '>', 0)->count();
            $itemsWithMaxSupply = CategoryItem::where('maximum_supply', '>', 0)->count();

            // Supply health score (0-100)
            $healthScore = $this->calculateSupplyHealthScore(
                $totalItems,
                $healthyStockItems,
                $lowSupplyItems,
                $outOfStockItems
            );

            return [
                'total_items' => $totalItems,
                'active_items' => $activeItems,
                'inactive_items' => $totalItems - $activeItems,
                'total_supply' => $totalSupply,
                'avg_supply_per_item' => $avgSupplyPerItem,
                'total_categories' => $totalCategories,
                'active_categories' => $activeCategories,
                'low_supply_items' => $lowSupplyItems,
                'out_of_stock_items' => $outOfStockItems,
                'healthy_stock_items' => $healthyStockItems,
                'items_with_min_supply' => $itemsWithMinSupply,
                'items_with_max_supply' => $itemsWithMaxSupply,
                'supply_health_score' => $healthScore
            ];
        } catch (\Exception $e) {
            Log::error('Supply Overview Statistics Error: ' . $e->getMessage());
            return $this->getDefaultOverview();
        }
    }

    /**
     * Calculate supply health score
     */
    private function calculateSupplyHealthScore($total, $healthy, $low, $outOfStock)
    {
        if ($total == 0) return 0;

        // Weighted scoring
        $healthyScore = ($healthy / $total) * 100 * 1.0;
        $lowPenalty = ($low / $total) * 100 * 0.5;
        $outOfStockPenalty = ($outOfStock / $total) * 100 * 1.0;

        $score = $healthyScore - $lowPenalty - $outOfStockPenalty;

        return round(max(0, min(100, $score)), 2);
    }

    /**
     * Get supply level analysis
     */
    private function getSupplyLevelAnalysis()
    {
        try {
            $items = CategoryItem::with('category')
                ->select([
                    'id',
                    'category_id',
                    'name',
                    'unit',
                    'current_supply',
                    'minimum_supply',
                    'maximum_supply',
                    'reorder_point',
                    'supply_alert_enabled',
                    'is_active'
                ])
                ->get();

            $supplyLevels = [
                'critical' => [],
                'low' => [],
                'adequate' => [],
                'optimal' => [],
                'overstocked' => []
            ];

            foreach ($items as $item) {
                $status = $this->determineSupplyStatus($item);
                $supplyLevels[$status][] = $item;
            }

            return [
                'levels' => $supplyLevels,
                'counts' => [
                    'critical' => count($supplyLevels['critical']),
                    'low' => count($supplyLevels['low']),
                    'adequate' => count($supplyLevels['adequate']),
                    'optimal' => count($supplyLevels['optimal']),
                    'overstocked' => count($supplyLevels['overstocked'])
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Supply Level Analysis Error: ' . $e->getMessage());
            return [
                'levels' => ['critical' => [], 'low' => [], 'adequate' => [], 'optimal' => [], 'overstocked' => []],
                'counts' => ['critical' => 0, 'low' => 0, 'adequate' => 0, 'optimal' => 0, 'overstocked' => 0]
            ];
        }
    }

    /**
     * Determine supply status for an item
     */
    private function determineSupplyStatus($item)
    {
        if ($item->current_supply == 0) {
            return 'critical';
        }

        if ($item->reorder_point > 0 && $item->current_supply <= $item->reorder_point) {
            return 'low';
        }

        if ($item->maximum_supply > 0) {
            $percentOfMax = ($item->current_supply / $item->maximum_supply) * 100;

            if ($percentOfMax > 100) {
                return 'overstocked';
            } elseif ($percentOfMax >= 70) {
                return 'optimal';
            } elseif ($percentOfMax >= 40) {
                return 'adequate';
            } else {
                return 'low';
            }
        }

        return 'adequate';
    }

    /**
     * Get supply trends over time - with no missing months
     */
    private function getSupplyTrends($startDate, $endDate)
    {
        try {
            // Get real data from DB
            $rawTrends = ItemSupplyLog::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('SUM(CASE WHEN transaction_type = "received" THEN quantity ELSE 0 END) as supplies_added'),
                    DB::raw('SUM(CASE WHEN transaction_type = "distributed" THEN quantity ELSE 0 END) as supplies_deducted'),
                    DB::raw('SUM(CASE WHEN transaction_type = "loss" THEN quantity ELSE 0 END) as supplies_lost'),
                    DB::raw('SUM(CASE WHEN transaction_type = "adjustment" THEN ABS(quantity) ELSE 0 END) as adjustments'),
                    DB::raw('COUNT(DISTINCT category_item_id) as items_affected')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month'); // key by "YYYY-MM" for easy lookup

            // Build a complete list of every month in the range
            $start  = Carbon::parse($startDate)->startOfMonth();
            $end    = Carbon::parse($endDate)->startOfMonth();
            $filled = collect();

            while ($start->lte($end)) {
                $key = $start->format('Y-m');

                if ($rawTrends->has($key)) {
                    $filled->push($rawTrends->get($key));
                } else {
                    // Push a zero-row for months with no activity
                    $filled->push((object) [
                        'month'               => $key,
                        'total_transactions'  => 0,
                        'supplies_added'      => 0,
                        'supplies_deducted'   => 0,
                        'supplies_lost'       => 0,
                        'adjustments'         => 0,
                        'items_affected'      => 0,
                    ]);
                }

                $start->addMonth();
            }

            return $filled;

        } catch (\Exception $e) {
            Log::error('Supply Trends Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get transaction analysis
     */
    private function getTransactionAnalysis($startDate, $endDate)
    {
        try {
            $transactions = ItemSupplyLog::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    'transaction_type',
                    DB::raw('COUNT(*) as transaction_count'),
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('AVG(quantity) as avg_quantity'),
                    DB::raw('COUNT(DISTINCT category_item_id) as unique_items')
                )
                ->groupBy('transaction_type')
                ->get();

            $total = $transactions->sum('transaction_count');

            $percentages = [];
            foreach ($transactions as $transaction) {
                $percentages[$transaction->transaction_type] = $total > 0
                    ? round(($transaction->transaction_count / $total) * 100, 2)
                    : 0;
            }

            return [
                'transactions' => $transactions,
                'percentages' => $percentages,
                'total_transactions' => $total
            ];
        } catch (\Exception $e) {
            Log::error('Transaction Analysis Error: ' . $e->getMessage());
            return [
                'transactions' => collect([]),
                'percentages' => [],
                'total_transactions' => 0
            ];
        }
    }

    /**
     * Get category performance
     */
    private function getCategoryPerformance()
    {
        try {
            $categories = RequestCategory::with(['items' => function($query) {
                $query->select('id', 'category_id', 'current_supply', 'minimum_supply', 'maximum_supply', 'is_active');
            }])
            ->withCount(['items as total_items', 'items as active_items' => function($q) {
                $q->where('is_active', true);
            }])
            ->get()
            ->map(function($category) {
                $totalSupply = $category->items->sum('current_supply');
                $lowSupplyCount = $category->items->filter(function($item) {
                    return $item->current_supply <= $item->reorder_point && $item->current_supply > 0;
                })->count();
                $outOfStockCount = $category->items->where('current_supply', 0)->count();

                return [
                    'id' => $category->id,
                    'name' => $category->display_name,
                    'total_items' => $category->total_items,
                    'active_items' => $category->active_items,
                    'total_supply' => $totalSupply,
                    'low_supply_count' => $lowSupplyCount,
                    'out_of_stock_count' => $outOfStockCount,
                    'is_active' => $category->is_active
                ];
            });

            return $categories;
        } catch (\Exception $e) {
            Log::error('Category Performance Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get top items analysis
     */
    private function getTopItemsAnalysis($startDate, $endDate)
    {
        try {
            // Most requested items
            $mostRequested = DB::table('seedling_request_items as sri')
                ->join('category_items as ci', 'sri.category_item_id', '=', 'ci.id')
                ->join('seedling_requests as sr', 'sri.seedling_request_id', '=', 'sr.id')
                ->whereBetween('sr.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    'ci.id',
                    'ci.name',
                    'ci.unit',
                    DB::raw('COUNT(DISTINCT sri.seedling_request_id) as request_count'),
                    DB::raw('SUM(sri.requested_quantity) as total_requested'),
                    DB::raw('SUM(sri.approved_quantity) as total_approved'),
                    DB::raw('AVG(sri.requested_quantity) as avg_requested')
                )
                ->groupBy('ci.id', 'ci.name', 'ci.unit')
                ->orderBy('request_count', 'desc')
                ->limit(10)
                ->get();

            // Most supplied items
            $mostSupplied = ItemSupplyLog::whereBetween('item_supply_logs.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('transaction_type', 'received')
                ->join('category_items', 'item_supply_logs.category_item_id', '=', 'category_items.id')
                ->select(
                    'category_items.id',
                    'category_items.name',
                    'category_items.unit',
                    DB::raw('COUNT(*) as supply_transactions'),
                    DB::raw('SUM(item_supply_logs.quantity) as total_supplied'),
                    DB::raw('AVG(item_supply_logs.quantity) as avg_supply')
                )
                ->groupBy('category_items.id', 'category_items.name', 'category_items.unit')
                ->orderBy('total_supplied', 'desc')
                ->limit(10)
                ->get();

            // Items with highest loss
            $highestLoss = ItemSupplyLog::whereBetween('item_supply_logs.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('transaction_type', 'loss')
                ->join('category_items', 'item_supply_logs.category_item_id', '=', 'category_items.id')
                ->select(
                    'category_items.id',
                    'category_items.name',
                    'category_items.unit',
                    DB::raw('COUNT(*) as loss_incidents'),
                    DB::raw('SUM(item_supply_logs.quantity) as total_loss'),
                    DB::raw('AVG(item_supply_logs.quantity) as avg_loss')
                )
                ->groupBy('category_items.id', 'category_items.name', 'category_items.unit')
                ->orderBy('total_loss', 'desc')
                ->limit(10)
                ->get();

            return [
                'most_requested' => $mostRequested,
                'most_supplied' => $mostSupplied,
                'highest_loss' => $highestLoss
            ];
        } catch (\Exception $e) {
            Log::error('Top Items Analysis Error: ' . $e->getMessage());
            return [
                'most_requested' => collect([]),
                'most_supplied' => collect([]),
                'highest_loss' => collect([])
            ];
        }
    }

    /**
     * Get supply alerts
     */
    private function getSupplyAlerts()
    {
        try {
            $criticalItems = CategoryItem::where('current_supply', 0)
                ->where('is_active', true)
                ->with('category')
                ->get();

            $lowSupplyItems = CategoryItem::where('supply_alert_enabled', true)
                ->whereColumn('current_supply', '<=', 'reorder_point')
                ->where('current_supply', '>', 0)
                ->with('category')
                ->orderBy('current_supply', 'asc')
                ->get();

            $overstockedItems = CategoryItem::where('maximum_supply', '>', 0)
                ->whereColumn('current_supply', '>', 'maximum_supply')
                ->with('category')
                ->get();

            return [
                'critical' => $criticalItems,
                'low_supply' => $lowSupplyItems,
                'overstocked' => $overstockedItems,
                'total_alerts' => $criticalItems->count() + $lowSupplyItems->count() + $overstockedItems->count()
            ];
        } catch (\Exception $e) {
            Log::error('Supply Alerts Error: ' . $e->getMessage());
            return [
                'critical' => collect([]),
                'low_supply' => collect([]),
                'overstocked' => collect([]),
                'total_alerts' => 0
            ];
        }
    }

    /**
     * Get fulfillment analysis
     */
    private function getFulfillmentAnalysis($startDate, $endDate)
    {
        try {
            $fulfillment = DB::table('seedling_request_items as sri')
                ->join('seedling_requests as sr', 'sri.seedling_request_id', '=', 'sr.id')
                ->whereBetween('sr.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereIn('sr.status', ['approved', 'partially_approved'])
                ->select(
                    DB::raw('COUNT(*) as total_items'),
                    DB::raw('SUM(sri.requested_quantity) as total_requested'),
                    DB::raw('SUM(sri.approved_quantity) as total_approved'),
                    DB::raw('SUM(CASE WHEN sri.approved_quantity >= sri.requested_quantity THEN 1 ELSE 0 END) as fully_fulfilled'),
                    DB::raw('SUM(CASE WHEN sri.approved_quantity < sri.requested_quantity AND sri.approved_quantity > 0 THEN 1 ELSE 0 END) as partially_fulfilled'),
                    DB::raw('SUM(CASE WHEN sri.approved_quantity = 0 OR sri.approved_quantity IS NULL THEN 1 ELSE 0 END) as not_fulfilled')
                )
                ->first();

            $fulfillmentRate = $fulfillment->total_requested > 0
                ? round(($fulfillment->total_approved / $fulfillment->total_requested) * 100, 2)
                : 0;

            $fullFulfillmentRate = $fulfillment->total_items > 0
                ? round(($fulfillment->fully_fulfilled / $fulfillment->total_items) * 100, 2)
                : 0;

            return [
                'total_items' => $fulfillment->total_items,
                'total_requested' => $fulfillment->total_requested,
                'total_approved' => $fulfillment->total_approved,
                'fully_fulfilled' => $fulfillment->fully_fulfilled,
                'partially_fulfilled' => $fulfillment->partially_fulfilled,
                'not_fulfilled' => $fulfillment->not_fulfilled,
                'fulfillment_rate' => $fulfillmentRate,
                'full_fulfillment_rate' => $fullFulfillmentRate
            ];
        } catch (\Exception $e) {
            Log::error('Fulfillment Analysis Error: ' . $e->getMessage());
            return [
                'total_items' => 0,
                'total_requested' => 0,
                'total_approved' => 0,
                'fully_fulfilled' => 0,
                'partially_fulfilled' => 0,
                'not_fulfilled' => 0,
                'fulfillment_rate' => 0,
                'full_fulfillment_rate' => 0
            ];
        }
    }

    /**
     * Get efficiency metrics
     */
    private function getEfficiencyMetrics($startDate, $endDate)
    {
        try {
            $supplyAdded = ItemSupplyLog::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('transaction_type', 'received')
                ->sum('quantity');

            $supplyDeducted = ItemSupplyLog::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('transaction_type', 'distributed')
                ->sum('quantity');

            $supplyLost = ItemSupplyLog::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('transaction_type', 'loss')
                ->sum('quantity');

            $netSupplyChange = $supplyAdded - $supplyDeducted - $supplyLost;

            $lossRate = $supplyAdded > 0 ? round(($supplyLost / $supplyAdded) * 100, 2) : 0;
            $utilizationRate = $supplyAdded > 0 ? round(($supplyDeducted / $supplyAdded) * 100, 2) : 0;

            return [
                'supply_added' => $supplyAdded,
                'supply_deducted' => $supplyDeducted,
                'supply_lost' => $supplyLost,
                'net_supply_change' => $netSupplyChange,
                'loss_rate' => $lossRate,
                'utilization_rate' => $utilizationRate
            ];
        } catch (\Exception $e) {
            Log::error('Efficiency Metrics Error: ' . $e->getMessage());
            return [
                'supply_added' => 0,
                'supply_deducted' => 0,
                'supply_lost' => 0,
                'net_supply_change' => 0,
                'loss_rate' => 0,
                'utilization_rate' => 0
            ];
        }
    }

    /**
     * Get turnover analysis
     */
    private function getTurnoverAnalysis($startDate, $endDate)
    {
        try {
            $items = CategoryItem::where('is_active', true)
                ->where('current_supply', '>', 0)
                ->get()
                ->map(function($item) use ($startDate, $endDate) {
                    $supplied = ItemSupplyLog::where('category_item_id', $item->id)
                        ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                        ->where('transaction_type', 'received')
                        ->sum('quantity');

                    $deducted = ItemSupplyLog::where('category_item_id', $item->id)
                        ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                        ->where('transaction_type', 'distributed')
                        ->sum('quantity');

                    $avgSupply = ($item->current_supply + $supplied) / 2;
                    $turnoverRate = $avgSupply > 0 ? round($deducted / $avgSupply, 2) : 0;

                    return [
                        'item_id' => $item->id,
                        'item_name' => $item->name,
                        'category' => $item->category->display_name ?? 'N/A',
                        'current_supply' => $item->current_supply,
                        'supplied' => $supplied,
                        'deducted' => $deducted,
                        'turnover_rate' => $turnoverRate
                    ];
                })
                ->sortByDesc('turnover_rate')
                ->take(15);

            return $items;
        } catch (\Exception $e) {
            Log::error('Turnover Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get loss and waste analysis
     */
    private function getLossAnalysis($startDate, $endDate)
    {
        try {
            $lossLogs = ItemSupplyLog::whereBetween('item_supply_logs.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where('transaction_type', 'loss')
                ->with('categoryItem.category')
                ->get();

            $totalLoss = $lossLogs->sum('quantity');
            $lossCount = $lossLogs->count();

            $lossByItem = $lossLogs->groupBy('category_item_id')
                ->map(function($logs, $itemId) {
                    $firstLog = $logs->first();
                    return [
                        'item_id' => $itemId,
                        'item_name' => $firstLog->categoryItem->name ?? 'Unknown',
                        'category' => $firstLog->categoryItem->category->display_name ?? 'N/A',
                        'unit' => $firstLog->categoryItem->unit ?? '',
                        'total_loss' => $logs->sum('quantity'),
                        'loss_incidents' => $logs->count(),
                        'avg_loss' => round($logs->avg('quantity'), 2)
                    ];
                })
                ->sortByDesc('total_loss')
                ->values();

            return [
                'total_loss' => $totalLoss,
                'loss_incidents' => $lossCount,
                'avg_loss_per_incident' => $lossCount > 0 ? round($totalLoss / $lossCount, 2) : 0,
                'loss_by_item' => $lossByItem
            ];
        } catch (\Exception $e) {
            Log::error('Loss Analysis Error: ' . $e->getMessage());
            return [
                'total_loss' => 0,
                'loss_incidents' => 0,
                'avg_loss_per_incident' => 0,
                'loss_by_item' => collect([])
            ];
        }
    }

    /**
     * Get restock recommendations
     */
    private function getRestockRecommendations()
    {
        try {
            $items = CategoryItem::where('is_active', true)
                ->with('category')
                ->get()
                ->map(function($item) {
                    $status = $this->determineSupplyStatus($item);
                    $priority = $this->calculateRestockPriority($item, $status);
                    $recommendedQuantity = $this->calculateRecommendedRestock($item);

                    return [
                        'item' => $item,
                        'status' => $status,
                        'priority' => $priority,
                        'recommended_quantity' => $recommendedQuantity,
                        'urgency' => $this->getUrgencyLevel($priority)
                    ];
                })
                ->filter(function($rec) {
                    return in_array($rec['status'], ['critical', 'low']);
                })
                ->sortByDesc('priority')
                ->values();

            return $items;
        } catch (\Exception $e) {
            Log::error('Restock Recommendations Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Calculate restock priority
     */
    private function calculateRestockPriority($item, $status)
    {
        $priority = 0;

        // Status priority
        if ($status === 'critical') $priority += 100;
        elseif ($status === 'low') $priority += 70;

        // Active item priority
        if ($item->is_active) $priority += 20;

        // Alert enabled priority
        if ($item->supply_alert_enabled) $priority += 10;

        return $priority;
    }

    /**
     * Calculate recommended restock quantity
     */
    private function calculateRecommendedRestock($item)
    {
        if ($item->maximum_supply > 0) {
            return $item->maximum_supply - $item->current_supply;
        }

        if ($item->reorder_point > 0) {
            return max(0, ($item->reorder_point * 2) - $item->current_supply);
        }

        return 0;
    }

    /**
     * Get urgency level
     */
    private function getUrgencyLevel($priority)
    {
        if ($priority >= 100) return 'critical';
        if ($priority >= 80) return 'high';
        if ($priority >= 50) return 'medium';
        return 'low';
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
            'total_supply' => 0,
            'avg_supply_per_item' => 0,
            'total_categories' => 0,
            'active_categories' => 0,
            'low_supply_items' => 0,
            'out_of_stock_items' => 0,
            'healthy_stock_items' => 0,
            'items_with_min_supply' => 0,
            'items_with_max_supply' => 0,
            'supply_health_score' => 0
        ];
    }

/**
     * Export analytics data as CSV
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
            $endDate   = $request->get('end_date', now()->format('Y-m-d'));

            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate   = Carbon::parse($endDate)->format('Y-m-d');

            $filename = 'supply-management-analytics-' . $startDate . '-to-' . $endDate . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            $overview            = $this->getOverviewStatistics();
            $fulfillment         = $this->getFulfillmentAnalysis($startDate, $endDate);
            $efficiency          = $this->getEfficiencyMetrics($startDate, $endDate);
            $supplyTrends        = $this->getSupplyTrends($startDate, $endDate);
            $topItems            = $this->getTopItemsAnalysis($startDate, $endDate);
            $restockRecs         = $this->getRestockRecommendations();
            $lossAnalysis        = $this->getLossAnalysis($startDate, $endDate);

            $callback = function () use (
                $startDate, $endDate, $overview, $fulfillment,
                $efficiency, $supplyTrends, $topItems, $restockRecs, $lossAnalysis
            ) {
                $file = fopen('php://output', 'w');

                // ── Export Info ───────────────────────────────────────
                fputcsv($file, ['SUPPLY MANAGEMENT ANALYTICS EXPORT']);
                fputcsv($file, ['Generated At', now()->format('Y-m-d H:i:s')]);
                fputcsv($file, ['Date Range', $startDate . ' to ' . $endDate]);
                fputcsv($file, ['Generated By', auth()->user()->name ?? 'System']);
                fputcsv($file, []);

                // ── Overview ──────────────────────────────────────────
                fputcsv($file, ['OVERVIEW']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Total Items',          $overview['total_items']]);
                fputcsv($file, ['Active Items',         $overview['active_items']]);
                fputcsv($file, ['Inactive Items',       $overview['inactive_items']]);
                fputcsv($file, ['Total Supply',         $overview['total_supply']]);
                fputcsv($file, ['Avg Supply Per Item',  $overview['avg_supply_per_item']]);
                fputcsv($file, ['Total Categories',     $overview['total_categories']]);
                fputcsv($file, ['Low Supply Items',     $overview['low_supply_items']]);
                fputcsv($file, ['Out of Stock Items',   $overview['out_of_stock_items']]);
                fputcsv($file, ['Healthy Stock Items',  $overview['healthy_stock_items']]);
                fputcsv($file, ['Supply Health Score',  $overview['supply_health_score'] . '%']);
                fputcsv($file, []);

                // ── Fulfillment Analysis ──────────────────────────────
                fputcsv($file, ['FULFILLMENT ANALYSIS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Total Items Requested',    $fulfillment['total_requested']]);
                fputcsv($file, ['Total Items Approved',     $fulfillment['total_approved']]);
                fputcsv($file, ['Fully Fulfilled',          $fulfillment['fully_fulfilled']]);
                fputcsv($file, ['Partially Fulfilled',      $fulfillment['partially_fulfilled']]);
                fputcsv($file, ['Not Fulfilled',            $fulfillment['not_fulfilled']]);
                fputcsv($file, ['Fulfillment Rate',         $fulfillment['fulfillment_rate'] . '%']);
                fputcsv($file, ['Full Fulfillment Rate',    $fulfillment['full_fulfillment_rate'] . '%']);
                fputcsv($file, []);

                // ── Efficiency Metrics ────────────────────────────────
                fputcsv($file, ['EFFICIENCY METRICS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Supply Added',        $efficiency['supply_added']]);
                fputcsv($file, ['Supply Distributed',  $efficiency['supply_deducted']]);
                fputcsv($file, ['Supply Lost',         $efficiency['supply_lost']]);
                fputcsv($file, ['Net Supply Change',   $efficiency['net_supply_change']]);
                fputcsv($file, ['Loss Rate',           $efficiency['loss_rate'] . '%']);
                fputcsv($file, ['Utilization Rate',    $efficiency['utilization_rate'] . '%']);
                fputcsv($file, []);

                // ── Monthly Supply Trends ─────────────────────────────
                fputcsv($file, ['MONTHLY SUPPLY TRENDS']);
                fputcsv($file, ['Month', 'Total Transactions', 'Supplies Added', 'Supplies Distributed', 'Supplies Lost', 'Items Affected']);
                foreach ($supplyTrends as $trend) {
                    fputcsv($file, [
                        $trend->month,
                        $trend->total_transactions,
                        $trend->supplies_added,
                        $trend->supplies_deducted,
                        $trend->supplies_lost,
                        $trend->items_affected,
                    ]);
                }
                fputcsv($file, []);

                // ── Top Requested Items ───────────────────────────────
                fputcsv($file, ['TOP REQUESTED ITEMS']);
                fputcsv($file, ['Item Name', 'Unit', 'Request Count', 'Total Requested', 'Total Approved', 'Avg Requested']);
                foreach ($topItems['most_requested'] as $item) {
                    fputcsv($file, [
                        $item->name,
                        $item->unit,
                        $item->request_count,
                        $item->total_requested,
                        $item->total_approved,
                        round($item->avg_requested, 2),
                    ]);
                }
                fputcsv($file, []);

                // ── Top Supplied Items ────────────────────────────────
                fputcsv($file, ['TOP SUPPLIED ITEMS']);
                fputcsv($file, ['Item Name', 'Unit', 'Supply Transactions', 'Total Supplied', 'Avg Supply']);
                foreach ($topItems['most_supplied'] as $item) {
                    fputcsv($file, [
                        $item->name,
                        $item->unit,
                        $item->supply_transactions,
                        $item->total_supplied,
                        round($item->avg_supply, 2),
                    ]);
                }
                fputcsv($file, []);

                // ── Loss Analysis ─────────────────────────────────────
                fputcsv($file, ['LOSS ANALYSIS']);
                fputcsv($file, ['Total Loss', $lossAnalysis['total_loss']]);
                fputcsv($file, ['Loss Incidents', $lossAnalysis['loss_incidents']]);
                fputcsv($file, ['Avg Loss Per Incident', $lossAnalysis['avg_loss_per_incident']]);
                fputcsv($file, []);
                fputcsv($file, ['Item Name', 'Category', 'Unit', 'Total Loss', 'Loss Incidents', 'Avg Loss']);
                foreach ($lossAnalysis['loss_by_item'] as $loss) {
                    fputcsv($file, [
                        $loss['item_name'],
                        $loss['category'],
                        $loss['unit'],
                        $loss['total_loss'],
                        $loss['loss_incidents'],
                        $loss['avg_loss'],
                    ]);
                }
                fputcsv($file, []);

                // ── Restock Recommendations ───────────────────────────
                fputcsv($file, ['RESTOCK RECOMMENDATIONS']);
                fputcsv($file, ['Item Name', 'Category', 'Current Supply', 'Status', 'Urgency', 'Recommended Restock Qty']);
                foreach ($restockRecs as $rec) {
                    fputcsv($file, [
                        $rec['item']->name,
                        $rec['item']->category->display_name ?? 'N/A',
                        $rec['item']->current_supply,
                        $rec['status'],
                        $rec['urgency'],
                        $rec['recommended_quantity'],
                    ]);
                }

                fclose($file);
            };

            // Log activity
            $this->logActivity('exported', 'CategoryItem', null, [
                'format'     => 'CSV',
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ], 'Exported Supply Management analytics data from ' . $startDate . ' to ' . $endDate);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Supply Management Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }

    /**
     * Get recently fulfilled items
     */
    private function getRecentlyFulfilledItems($startDate, $endDate)
    {
        try {
            $recentlyFulfilled = DB::table('seedling_request_items as sri')
                ->join('seedling_requests as sr', 'sri.seedling_request_id', '=', 'sr.id')
                ->join('request_categories as rc', 'sri.category_id', '=', 'rc.id')
                ->whereBetween('sr.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereIn('sr.status', ['approved', 'partially_approved'])
                ->where('sri.approved_quantity', '>', 0)
                ->select(
                    'sri.item_name',
                    'rc.display_name as category_name',
                    'sri.approved_quantity',
                    'sri.requested_quantity',
                    'sr.first_name',
                    'sr.last_name',
                    'sr.barangay',
                    'sr.reviewed_at',
                    DB::raw('ROUND((sri.approved_quantity / sri.requested_quantity) * 100, 1) as fulfillment_percentage')
                )
                ->orderBy('sr.reviewed_at', 'desc')
                ->limit(8)
                ->get()
                ->map(function($item) {
                    return [
                        'item_name' => $item->item_name,
                        'category_name' => $item->category_name,
                        'approved_quantity' => $item->approved_quantity,
                        'requested_quantity' => $item->requested_quantity,
                        'applicant_name' => trim($item->first_name . ' ' . $item->last_name),
                        'barangay' => $item->barangay,
                        'reviewed_at' => $item->reviewed_at,
                        'fulfillment_percentage' => $item->fulfillment_percentage,
                        'is_fully_fulfilled' => $item->approved_quantity >= $item->requested_quantity
                    ];
                });

            return $recentlyFulfilled;
        } catch (\Exception $e) {
            Log::error('Recently Fulfilled Items Error: ' . $e->getMessage());
            return collect([]);
        }
    }
}
