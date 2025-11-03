<?php

namespace App\Services;

use App\Models\SeedlingRequest;
use App\Models\CategoryItem;
use App\Models\SeedlingRequestItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DSSDataService
{
    /**
     * Collect comprehensive data for DSS analysis
     */
    public function collectMonthlyData(string $month = null, string $year = null): array
    {
        // Increase timeout for data collection
        set_time_limit(300); // 5 minutes

        $month = $month ?? now()->format('m');
        $year = $year ?? now()->format('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Create cache key based on the date range
        $cacheKey = "dss_data_{$year}_{$month}";

        // Cache for 30 minutes (1800 seconds)
        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return [
                'period' => [
                    'month' => $startDate->format('F Y'),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'requests_data' => $this->getSeedlingRequestsData($startDate, $endDate),
                'supply_data' => $this->getSupplyData(),
                'demand_analysis' => $this->getDemandAnalysis($startDate, $endDate),
                'barangay_analysis' => $this->getBarangayAnalysis($startDate, $endDate),
                'fulfillment_analysis' => $this->getFulfillmentAnalysis($startDate, $endDate),
                'shortage_analysis' => $this->getShortageAnalysis($startDate, $endDate),
            ];
        });
    }

    /**
     * Clear cached DSS data for a specific month/year
     */
    public function clearCache(string $month = null, string $year = null): void
    {
        $month = $month ?? now()->format('m');
        $year = $year ?? now()->format('Y');

        $cacheKey = "dss_data_{$year}_{$month}";
        Cache::forget($cacheKey);
    }

    /**
     * Clear all DSS related cache
     */
    public function clearAllCache(): void
    {
        // Clear current and previous months
        $currentDate = now();
        for ($i = 0; $i < 3; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $this->clearCache($date->format('m'), $date->format('Y'));
        }
    }

    /**
     * Get seedling requests data for the period
     */
    private function getSeedlingRequestsData(Carbon $startDate, Carbon $endDate): array
    {
        // Use a single query with aggregations instead of loading all data into memory
        $requestStats = SeedlingRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_requests,
                SUM(CASE WHEN status IN ("approved", "partially_approved") THEN 1 ELSE 0 END) as approved_requests,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_requests,
                SUM(CASE WHEN status = "under_review" THEN 1 ELSE 0 END) as pending_requests,
                SUM(total_quantity) as total_items_requested,
                AVG(CASE WHEN updated_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, updated_at) ELSE NULL END) as avg_processing_hours
            ')
            ->first();

        return [
            'total_requests' => $requestStats->total_requests ?? 0,
            'approved_requests' => $requestStats->approved_requests ?? 0,
            'rejected_requests' => $requestStats->rejected_requests ?? 0,
            'pending_requests' => $requestStats->pending_requests ?? 0,
            'total_items_requested' => $requestStats->total_items_requested ?? 0,
            'average_processing_time' => round($requestStats->avg_processing_hours ?? 0, 2) . ' hours',
            'approval_rate' => $requestStats->total_requests > 0 ?
                round(($requestStats->approved_requests / $requestStats->total_requests) * 100, 2) : 0,
        ];
    }

    /**
     * Get current supply/stock data
     */
    private function getSupplyData(): array
    {
        // Use aggregations instead of loading all items
        $supplyStats = CategoryItem::selectRaw('
            COUNT(*) as total_items,
            SUM(current_supply) as available_stock,
            SUM(CASE WHEN current_supply <= 10 THEN 1 ELSE 0 END) as low_stock_items,
            SUM(CASE WHEN current_supply = 0 THEN 1 ELSE 0 END) as out_of_stock_items,
            SUM(CASE WHEN reorder_point IS NOT NULL AND current_supply <= reorder_point THEN 1 ELSE 0 END) as needs_reorder,
            SUM(CASE WHEN minimum_supply IS NOT NULL AND current_supply <= minimum_supply THEN 1 ELSE 0 END) as critical_items
        ')->first();

        // Get category distribution with aggregations
        $categoryDistribution = CategoryItem::with('category')
            ->select('category_id')
            ->selectRaw('
                COUNT(*) as count,
                SUM(current_supply) as total_stock,
                AVG(current_supply) as avg_stock,
                SUM(CASE WHEN current_supply = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN current_supply <= 10 THEN 1 ELSE 0 END) as low_stock
            ')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $item->category->name ?? 'Unknown',
                    'count' => (int) $item->count,
                    'total_stock' => (int) $item->total_stock,
                    'avg_stock' => round($item->avg_stock, 2),
                    'out_of_stock' => (int) $item->out_of_stock,
                    'low_stock' => (int) $item->low_stock,
                    'stock_status' => $this->getCategoryStockStatus($item->total_stock, $item->count, $item->out_of_stock, $item->low_stock),
                ];
            });

        // Get detailed stock distribution with more information
        $stockDistribution = CategoryItem::with('category')
            ->select('id', 'name', 'current_supply', 'unit', 'category_id', 'minimum_supply', 'maximum_supply', 'reorder_point', 'last_supplied_at')
            ->orderByDesc('current_supply')
            ->limit(50)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'current_supply' => $item->current_supply,
                    'unit' => $item->unit,
                    'category' => $item->category->name ?? 'Unknown',
                    'minimum_supply' => $item->minimum_supply,
                    'maximum_supply' => $item->maximum_supply,
                    'reorder_point' => $item->reorder_point,
                    'last_supplied_at' => $item->last_supplied_at ? $item->last_supplied_at->format('M j, Y') : 'Never',
                    'status' => $this->getDetailedStockStatus($item),
                    'status_color' => $this->getStockStatusColor($item),
                    'needs_attention' => $this->itemNeedsAttention($item),
                    'supply_percentage' => $this->calculateSupplyPercentage($item),
                ];
            });

        // Get items that need immediate attention
        $attentionItems = CategoryItem::with('category')
            ->where(function($query) {
                $query->where('current_supply', '<=', 0)
                      ->orWhereColumn('current_supply', '<=', 'reorder_point')
                      ->orWhereColumn('current_supply', '<=', 'minimum_supply');
            })
            ->orderBy('current_supply')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->name,
                    'category' => $item->category->name ?? 'Unknown',
                    'current_supply' => $item->current_supply,
                    'unit' => $item->unit,
                    'reorder_point' => $item->reorder_point,
                    'minimum_supply' => $item->minimum_supply,
                    'status' => $this->getDetailedStockStatus($item),
                    'urgency' => $this->getUrgencyLevel($item),
                    'recommended_action' => $this->getRecommendedAction($item),
                ];
            });

        return [
            'total_items' => $supplyStats->total_items ?? 0,
            'available_stock' => $supplyStats->available_stock ?? 0,
            'low_stock_items' => $supplyStats->low_stock_items ?? 0,
            'out_of_stock_items' => $supplyStats->out_of_stock_items ?? 0,
            'needs_reorder' => $supplyStats->needs_reorder ?? 0,
            'critical_items' => $supplyStats->critical_items ?? 0,
            'items_by_category' => $categoryDistribution,
            'stock_distribution' => $stockDistribution,
            'attention_items' => $attentionItems,
            'supply_health_score' => $this->calculateSupplyHealthScore($supplyStats),
            'supply_summary' => $this->generateSupplySummary($supplyStats, $categoryDistribution, $attentionItems),
        ];
    }

    /**
     * Analyze demand patterns
     */
    private function getDemandAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        // Use a single optimized query with joins to get all demand data
        $demandQuery = DB::table('seedling_requests as sr')
            ->join('seedling_request_items as sri', 'sr.id', '=', 'sri.seedling_request_id')
            ->leftJoin('category_items as ci', 'sri.category_item_id', '=', 'ci.id')
            ->whereBetween('sr.created_at', [$startDate, $endDate])
            ->select(
                'sri.item_name',
                'ci.name as category_name',
                DB::raw('SUM(sri.requested_quantity) as total_requested'),
                DB::raw('COUNT(sri.id) as request_count')
            )
            ->groupBy('sri.item_name', 'ci.name')
            ->orderByDesc('total_requested')
            ->get();

        $demandData = $demandQuery->map(function ($item) {
            return [
                'name' => $item->item_name,
                'category' => $item->category_name ?? 'unknown',
                'total_requested' => (int) $item->total_requested,
                'request_count' => (int) $item->request_count,
            ];
        })->toArray();

        // Get category analysis
        $categoryAnalysis = collect($demandData)
            ->groupBy('category')
            ->map(function ($items) {
                return [
                    'total_requests' => $items->sum('request_count'),
                    'total_quantity' => $items->sum('total_requested'),
                    'unique_items' => $items->count(),
                ];
            });

        return [
            'top_requested_items' => array_slice($demandData, 0, 10),
            'total_unique_items' => count($demandData),
            'demand_by_category' => $categoryAnalysis,
        ];
    }

    /**
     * Analyze requests by barangay
     */
    private function getBarangayAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $requests = SeedlingRequest::whereBetween('created_at', [$startDate, $endDate])
            ->select('barangay', DB::raw('count(*) as request_count'), DB::raw('sum(total_quantity) as total_quantity'))
            ->groupBy('barangay')
            ->orderByDesc('request_count')
            ->get();

        return [
            'total_barangays' => $requests->count(),
            'barangay_details' => $requests->map(function($item) {
                return [
                    'name' => $item->barangay,
                    'requests' => $item->request_count,
                    'total_quantity' => $item->total_quantity,
                    'priority_level' => $this->calculateBarangayPriority($item->request_count, $item->total_quantity),
                ];
            })->toArray(),
            'top_requesting_barangays' => $requests->take(5)->toArray(),
        ];
    }

    /**
     * Analyze fulfillment rates and shortages
     */
    private function getFulfillmentAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $approvedRequests = SeedlingRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'partially_approved'])
            ->get();

        $partiallyApproved = $approvedRequests->where('status', 'partially_approved')->count();
        $fullyApproved = $approvedRequests->where('status', 'approved')->count();

        return [
            'fulfillment_rate' => $approvedRequests->count() > 0 ?
                round(($fullyApproved / $approvedRequests->count()) * 100, 2) : 0,
            'partial_fulfillment_rate' => $approvedRequests->count() > 0 ?
                round(($partiallyApproved / $approvedRequests->count()) * 100, 2) : 0,
            'total_fulfilled_requests' => $fullyApproved,
            'total_partially_fulfilled' => $partiallyApproved,
            'fulfillment_challenges' => $this->identifyFulfillmentChallenges($approvedRequests),
        ];
    }

    /**
     * Identify potential shortages and supply issues
     */
    private function getShortageAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $demandData = $this->getDemandAnalysis($startDate, $endDate);
        $supplyData = $this->getSupplyData();

        $shortages = [];
        $surpluses = [];

        foreach ($demandData['top_requested_items'] as $demandItem) {
            // Try to match with available stock
            $matchingStock = collect($supplyData['stock_distribution'])
                ->firstWhere('name', 'like', '%' . $demandItem['name'] . '%');

            if ($matchingStock) {
                $shortageAmount = $demandItem['total_requested'] - $matchingStock['current_supply'];

                if ($shortageAmount > 0) {
                    $shortages[] = [
                        'item' => $demandItem['name'],
                        'demanded' => $demandItem['total_requested'],
                        'available' => $matchingStock['current_supply'],
                        'shortage' => $shortageAmount,
                        'severity' => $this->calculateShortageSeverity($shortageAmount, $demandItem['total_requested']),
                    ];
                } else {
                    $surpluses[] = [
                        'item' => $demandItem['name'],
                        'demanded' => $demandItem['total_requested'],
                        'available' => $matchingStock['current_supply'],
                        'surplus' => abs($shortageAmount),
                    ];
                }
            } else {
                // Item not found in stock
                $shortages[] = [
                    'item' => $demandItem['name'],
                    'demanded' => $demandItem['total_requested'],
                    'available' => 0,
                    'shortage' => $demandItem['total_requested'],
                    'severity' => 'CRITICAL',
                ];
            }
        }

        return [
            'shortages' => $shortages,
            'surpluses' => $surpluses,
            'critical_shortages' => collect($shortages)->where('severity', 'CRITICAL')->count(),
            'total_shortage_value' => collect($shortages)->sum('shortage'),
        ];
    }

    /**
     * Helper methods
     */
    private function calculateAverageProcessingTime($requests): float
    {
        $processedRequests = $requests->whereNotIn('status', ['under_review']);

        if ($processedRequests->isEmpty()) {
            return 0;
        }

        $totalDays = $processedRequests->sum(function($request) {
            return $request->created_at->diffInDays($request->updated_at);
        });

        return round($totalDays / $processedRequests->count(), 2);
    }

    private function getStockStatus(int $supply): string
    {
        if ($supply == 0) return 'OUT_OF_STOCK';
        if ($supply <= 10) return 'LOW_STOCK';
        if ($supply <= 50) return 'MEDIUM_STOCK';
        return 'GOOD_STOCK';
    }

    private function getDetailedStockStatus($item): string
    {
        if ($item->current_supply <= 0) {
            return 'Out of Stock';
        }
        if ($item->minimum_supply && $item->current_supply <= $item->minimum_supply) {
            return 'Critical Level';
        }
        if ($item->reorder_point && $item->current_supply <= $item->reorder_point) {
            return 'Needs Reorder';
        }
        if ($item->current_supply <= 10) {
            return 'Low Stock';
        }
        if ($item->maximum_supply && $item->current_supply >= $item->maximum_supply * 0.8) {
            return 'Well Stocked';
        }
        return 'Adequate';
    }

    private function getStockStatusColor($item): string
    {
        if ($item->current_supply <= 0) {
            return 'danger';
        }
        if ($item->minimum_supply && $item->current_supply <= $item->minimum_supply) {
            return 'danger';
        }
        if ($item->reorder_point && $item->current_supply <= $item->reorder_point) {
            return 'warning';
        }
        if ($item->current_supply <= 10) {
            return 'warning';
        }
        return 'success';
    }

    private function itemNeedsAttention($item): bool
    {
        return $item->current_supply <= 0 ||
               ($item->minimum_supply && $item->current_supply <= $item->minimum_supply) ||
               ($item->reorder_point && $item->current_supply <= $item->reorder_point);
    }

    private function calculateSupplyPercentage($item): int
    {
        if (!$item->maximum_supply || $item->maximum_supply <= 0) {
            return $item->current_supply > 50 ? 100 : ($item->current_supply * 2);
        }
        return min(100, round(($item->current_supply / $item->maximum_supply) * 100));
    }

    private function getCategoryStockStatus($totalStock, $count, $outOfStock, $lowStock): string
    {
        $outOfStockPercent = $count > 0 ? ($outOfStock / $count) * 100 : 0;
        $lowStockPercent = $count > 0 ? ($lowStock / $count) * 100 : 0;

        if ($outOfStockPercent >= 50) {
            return 'Critical';
        }
        if ($outOfStockPercent >= 25 || $lowStockPercent >= 50) {
            return 'Poor';
        }
        if ($lowStockPercent >= 25) {
            return 'Fair';
        }
        return 'Good';
    }

    private function getUrgencyLevel($item): string
    {
        if ($item->current_supply <= 0) {
            return 'CRITICAL';
        }
        if ($item->minimum_supply && $item->current_supply <= $item->minimum_supply) {
            return 'HIGH';
        }
        if ($item->reorder_point && $item->current_supply <= $item->reorder_point) {
            return 'MEDIUM';
        }
        return 'LOW';
    }

    private function getRecommendedAction($item): string
    {
        if ($item->current_supply <= 0) {
            return 'Immediate procurement required - Item completely out of stock';
        }
        if ($item->minimum_supply && $item->current_supply <= $item->minimum_supply) {
            return 'Emergency restock needed - Below minimum safety level';
        }
        if ($item->reorder_point && $item->current_supply <= $item->reorder_point) {
            return 'Initiate regular reorder process';
        }
        return 'Monitor stock levels';
    }

    private function calculateSupplyHealthScore($supplyStats): int
    {
        $totalItems = $supplyStats->total_items ?? 1;

        // Calculate health score based on stock status
        $outOfStockPenalty = ($supplyStats->out_of_stock_items ?? 0) * 10;
        $lowStockPenalty = ($supplyStats->low_stock_items ?? 0) * 5;
        $criticalPenalty = ($supplyStats->critical_items ?? 0) * 8;

        $maxPenalty = $totalItems * 10; // Maximum possible penalty
        $actualPenalty = $outOfStockPenalty + $lowStockPenalty + $criticalPenalty;

        $score = max(0, 100 - (($actualPenalty / $maxPenalty) * 100));

        return round($score);
    }

    private function generateSupplySummary($supplyStats, $categoryDistribution, $attentionItems): array
    {
        $totalItems = $supplyStats->total_items ?? 0;
        $totalStock = $supplyStats->available_stock ?? 0;
        $outOfStock = $supplyStats->out_of_stock_items ?? 0;
        $lowStock = $supplyStats->low_stock_items ?? 0;
        $needsReorder = $supplyStats->needs_reorder ?? 0;
        $critical = $supplyStats->critical_items ?? 0;

        // Calculate percentages
        $outOfStockPercent = $totalItems > 0 ? round(($outOfStock / $totalItems) * 100, 1) : 0;
        $lowStockPercent = $totalItems > 0 ? round(($lowStock / $totalItems) * 100, 1) : 0;
        $criticalPercent = $totalItems > 0 ? round(($critical / $totalItems) * 100, 1) : 0;

        // Determine overall status
        $overallStatus = 'Good';
        if ($outOfStockPercent >= 25 || $criticalPercent >= 20) {
            $overallStatus = 'Critical';
        } elseif ($outOfStockPercent >= 10 || $lowStockPercent >= 30) {
            $overallStatus = 'Poor';
        } elseif ($lowStockPercent >= 15) {
            $overallStatus = 'Fair';
        }

        // Get top performing categories
        $topCategories = $categoryDistribution
            ->where('total_stock', '>', 0)
            ->sortByDesc('total_stock')
            ->take(3)
            ->pluck('category_name')
            ->toArray();

        // Get categories needing attention
        $concernCategories = $categoryDistribution
            ->where('stock_status', '!=', 'Good')
            ->pluck('category_name')
            ->toArray();

        return [
            'overall_status' => $overallStatus,
            'total_units' => $totalStock,
            'item_types' => $totalItems,
            'out_of_stock_percent' => $outOfStockPercent,
            'low_stock_percent' => $lowStockPercent,
            'critical_percent' => $criticalPercent,
            'needs_reorder' => $needsReorder,
            'top_stocked_categories' => $topCategories,
            'concern_categories' => $concernCategories,
            'immediate_attention_count' => $attentionItems->count(),
            'summary_text' => $this->generateSummaryText($overallStatus, $totalStock, $totalItems, $outOfStockPercent, $lowStockPercent),
        ];
    }

    private function generateSummaryText($status, $totalStock, $totalItems, $outOfStockPercent, $lowStockPercent): string
    {
        $stockDensity = $totalItems > 0 ? round($totalStock / $totalItems, 1) : 0;

        $summary = "Supply inventory contains {$totalStock} total units across {$totalItems} different item types ";
        $summary .= "(average {$stockDensity} units per item type). ";

        if ($status === 'Critical') {
            $summary .= "CRITICAL STATUS: {$outOfStockPercent}% of items are out of stock. Immediate action required.";
        } elseif ($status === 'Poor') {
            $summary .= "Supply challenges identified with {$outOfStockPercent}% out of stock and {$lowStockPercent}% low stock items.";
        } elseif ($status === 'Fair') {
            $summary .= "Supply levels are manageable but {$lowStockPercent}% of items need restocking attention.";
        } else {
            $summary .= "Supply levels are generally adequate with good stock distribution.";
        }

        return $summary;
    }

    private function calculateBarangayPriority(int $requests, int $totalQuantity): string
    {
        $score = ($requests * 0.6) + ($totalQuantity * 0.4);

        if ($score >= 50) return 'HIGH';
        if ($score >= 20) return 'MEDIUM';
        return 'LOW';
    }

    private function identifyFulfillmentChallenges($requests): array
    {
        $challenges = [];

        $partiallyFulfilled = $requests->where('status', 'partially_approved');

        if ($partiallyFulfilled->count() > ($requests->count() * 0.3)) {
            $challenges[] = 'High rate of partial fulfillment indicates supply constraints';
        }

        return $challenges;
    }

    private function calculateShortageSeverity(int $shortage, int $demand): string
    {
        $percentage = ($shortage / $demand) * 100;

        if ($percentage >= 80) return 'CRITICAL';
        if ($percentage >= 50) return 'HIGH';
        if ($percentage >= 25) return 'MEDIUM';
        return 'LOW';
    }
}
