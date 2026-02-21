<?php

namespace App\Services;

use App\Models\SeedlingRequest;
use App\Models\CategoryItem;
use App\Models\SeedlingRequestItem;
use App\Models\TrainingApplication;
use App\Models\RsbsaApplication;
use App\Models\FishrApplication;
use App\Models\BoatrApplication;
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
                SUM(CASE WHEN status IN ("rejected", "denied") THEN 1 ELSE 0 END) as rejected_requests,
                SUM(CASE WHEN status IN ("pending", "under_review") THEN 1 ELSE 0 END) as pending_requests,
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
            ->first(function($stock) use ($demandItem) {
                return str_contains(
                    strtolower($stock['name']),
                    strtolower($demandItem['name'])
                ) || str_contains(
                    strtolower($demandItem['name']),
                    strtolower($stock['name'])
                );
            });

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
        $totalItems = $supplyStats->total_items ?: 1;  // use ?: to treat 0 same as null

        // Calculate health score based on stock status
        $outOfStockPenalty = ($supplyStats->out_of_stock_items ?? 0) * 10;
        $lowStockPenalty = ($supplyStats->low_stock_items ?? 0) * 5;
        $criticalPenalty = ($supplyStats->critical_items ?? 0) * 8;

        $maxPenalty = $totalItems * 10; // Maximum possible penalty
        $actualPenalty = $outOfStockPenalty + $lowStockPenalty + $criticalPenalty;

        if ($maxPenalty === 0) {
            return 100; // No items, return perfect score
        }

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

    /**
     * ================================================================
     * TRAINING-SPECIFIC DATA COLLECTION
     * ================================================================
     */

    /**
     * Collect Training data for DSS analysis
     */
    public function collectTrainingData(string $month = null, string $year = null): array
    {
        set_time_limit(300);

        $month = $month ?? now()->format('m');
        $year = $year ?? now()->format('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $cacheKey = "dss_training_data_{$year}_{$month}";

        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return [
                'period' => [
                    'month' => $startDate->format('F Y'),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'training_stats' => $this->getTrainingStats($startDate, $endDate),
                'training_by_type' => $this->getTrainingByType($startDate, $endDate),
                'training_by_barangay' => $this->getTrainingByBarangay($startDate, $endDate),
                'training_approval_analysis' => $this->getTrainingApprovalAnalysis($startDate, $endDate),
                'training_trends' => $this->getTrainingTrends($startDate, $endDate),
            ];
        });
    }

    private function getTrainingStats(Carbon $startDate, Carbon $endDate): array
    {
        $stats = TrainingApplication::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = "under_review" THEN 1 ELSE 0 END) as pending,
                AVG(CASE WHEN status_updated_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, status_updated_at) ELSE NULL END) as avg_processing_hours
            ')
            ->first();

        return [
            'total_applications' => $stats->total_applications ?? 0,
            'approved' => $stats->approved ?? 0,
            'rejected' => $stats->rejected ?? 0,
            'pending' => $stats->pending ?? 0,
            'approval_rate' => $stats->total_applications > 0 ?
                round(($stats->approved / $stats->total_applications) * 100, 2) : 0,
            'rejection_rate' => $stats->total_applications > 0 ?
                round(($stats->rejected / $stats->total_applications) * 100, 2) : 0,
            'avg_processing_time' => round($stats->avg_processing_hours ?? 0, 2) . ' hours',
        ];
    }

    private function getTrainingByType(Carbon $startDate, Carbon $endDate): array
    {
        $byType = TrainingApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('training_type')
            ->selectRaw('
                COUNT(*) as count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count
            ')
            ->groupBy('training_type')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'type' => $item->training_type,
                    'display_name' => $this->getTrainingTypeDisplay($item->training_type),
                    'total' => $item->count,
                    'approved' => $item->approved_count,
                    'approval_rate' => $item->count > 0 ? round(($item->approved_count / $item->count) * 100, 2) : 0,
                ];
            })
            ->toArray();

        return [
            'distribution' => $byType,
            'most_popular' => collect($byType)->sortByDesc('total')->take(3)->values()->toArray(),
            'total_types_requested' => count($byType),
        ];
    }

    private function getTrainingByBarangay(Carbon $startDate, Carbon $endDate): array
    {
        $byBarangay = TrainingApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('barangay')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'barangay' => $item->barangay,
                    'applications' => $item->count,
                ];
            })
            ->toArray();

        return [
            'distribution' => $byBarangay,
            'top_barangays' => array_slice($byBarangay, 0, 5),
            'total_barangays_covered' => count($byBarangay),
        ];
    }

    private function getTrainingApprovalAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $applications = TrainingApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('status_updated_at')
            ->get();

        return [
            'average_decision_time' => $applications->isNotEmpty() ?
                round($applications->avg(function($app) {
                    return $app->created_at->diffInHours($app->status_updated_at);
                }), 2) . ' hours' : '0 hours',
            'fastest_approval' => $applications->where('status', 'approved')->isNotEmpty() ?
                round($applications->where('status', 'approved')->min(function($app) {
                    return $app->created_at->diffInHours($app->status_updated_at);
                }), 2) . ' hours' : 'N/A',
            'slowest_approval' => $applications->where('status', 'approved')->isNotEmpty() ?
                round($applications->where('status', 'approved')->max(function($app) {
                    return $app->created_at->diffInHours($app->status_updated_at);
                }), 2) . ' hours' : 'N/A',
        ];
    }

    private function getTrainingTrends(Carbon $startDate, Carbon $endDate): array
    {
        // Compare with previous month
        $prevStartDate = $startDate->copy()->subMonth();
        $prevEndDate = $endDate->copy()->subMonth();

        $currentCount = TrainingApplication::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousCount = TrainingApplication::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();

        $change = $previousCount > 0 ? round((($currentCount - $previousCount) / $previousCount) * 100, 2) : 0;

        return [
            'current_month_total' => $currentCount,
            'previous_month_total' => $previousCount,
            'percentage_change' => $change,
            'trend' => $change > 0 ? 'increasing' : ($change < 0 ? 'decreasing' : 'stable'),
        ];
    }

    private function getTrainingTypeDisplay(string $type): string
    {
        return match($type) {
            'tilapia_hito' => 'Tilapia and Hito Training',
            'hydroponics' => 'Hydroponics Training',
            'aquaponics' => 'Aquaponics Training',
            'mushrooms' => 'Mushrooms Production Training',
            'livestock_poultry' => 'Livestock and Poultry Training',
            'high_value_crops' => 'High Value Crops Training',
            'sampaguita_propagation' => 'Sampaguita Propagation Training',
            default => ucfirst(str_replace('_', ' ', $type))
        };
    }

    /**
     * ================================================================
     * RSBSA-SPECIFIC DATA COLLECTION
     * ================================================================
     */

    /**
     * Collect RSBSA data for DSS analysis
     */
    public function collectRsbsaData(string $month = null, string $year = null): array
    {
        set_time_limit(300);

        $month = $month ?? now()->format('m');
        $year = $year ?? now()->format('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $cacheKey = "dss_rsbsa_data_{$year}_{$month}";

        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return [
                'period' => [
                    'month' => $startDate->format('F Y'),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'rsbsa_stats' => $this->getRsbsaStats($startDate, $endDate),
                'rsbsa_by_commodity' => $this->getRsbsaByCommodity($startDate, $endDate),
                'rsbsa_by_livelihood' => $this->getRsbsaByLivelihood($startDate, $endDate),
                'rsbsa_by_barangay' => $this->getRsbsaByBarangay($startDate, $endDate),
                'rsbsa_demographics' => $this->getRsbsaDemographics($startDate, $endDate),
                'rsbsa_land_analysis' => $this->getRsbsaLandAnalysis($startDate, $endDate),
                'rsbsa_farmer_details' => $this->getRsbsaFarmerDetails($startDate, $endDate),
                'rsbsa_farmer_crops' => $this->getRsbsaFarmerCrops($startDate, $endDate),
                'rsbsa_farmworker_type' => $this->getRsbsaFarmworkerType($startDate, $endDate),
                'rsbsa_fisherfolk_activity' => $this->getRsbsaFisherfolkActivity($startDate, $endDate),
                'rsbsa_agriyouth_analysis' => $this->getRsbsaAgriyouthAnalysis($startDate, $endDate),
                'rsbsa_approval_analysis' => $this->getRsbsaApprovalAnalysis($startDate, $endDate),
                'rsbsa_trends' => $this->getRsbsaTrends($startDate, $endDate),
            ];
        });
    }

    private function getRsbsaStats(Carbon $startDate, Carbon $endDate): array
    {
        $stats = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status IN ("pending", "under_review") THEN 1 ELSE 0 END) as pending,
                SUM(farmer_land_area) as total_land_area,
                AVG(farmer_land_area) as avg_land_area
            ')
            ->first();

        return [
            'total_applications' => $stats->total_applications ?? 0,
            'approved' => $stats->approved ?? 0,
            'rejected' => $stats->rejected ?? 0,
            'pending' => $stats->pending ?? 0,
            'approval_rate' => $stats->total_applications > 0 ?
                round(($stats->approved / $stats->total_applications) * 100, 2) : 0,
            'total_land_area' => round($stats->total_land_area ?? 0, 2) . ' hectares',
            'avg_land_area' => round($stats->avg_land_area ?? 0, 2) . ' hectares',
        ];
    }

    private function getRsbsaByCommodity(Carbon $startDate, Carbon $endDate): array
    {
        $byCommodity = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('commodity')
            ->selectRaw('
                COUNT(*) as count,
                SUM(farmer_land_area) as total_land,
                AVG(farmer_land_area) as avg_land
            ')
            ->groupBy('commodity')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'commodity' => $item->commodity,
                    'total_farmers' => $item->count,
                    'total_land_area' => round($item->total_land ?? 0, 2),
                    'avg_land_area' => round($item->avg_land ?? 0, 2),
                ];
            })
            ->toArray();

        return [
            'distribution' => $byCommodity,
            'top_commodities' => array_slice($byCommodity, 0, 5),
            'total_commodity_types' => count($byCommodity),
        ];
    }

    private function getRsbsaByLivelihood(Carbon $startDate, Carbon $endDate): array
    {
        $byLivelihood = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('main_livelihood')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('main_livelihood')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'livelihood' => $item->main_livelihood,
                    'count' => $item->count,
                ];
            })
            ->toArray();

        return [
            'distribution' => $byLivelihood,
            'primary_livelihood' => $byLivelihood[0] ?? null,
        ];
    }

    private function getRsbsaByBarangay(Carbon $startDate, Carbon $endDate): array
    {
        $byBarangay = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('barangay')
            ->selectRaw('
                COUNT(*) as count,
                SUM(farmer_land_area) as total_land
            ')
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'barangay' => $item->barangay,
                    'farmers' => $item->count,
                    'total_land_area' => round($item->total_land ?? 0, 2),
                ];
            })
            ->toArray();

        return [
            'distribution' => $byBarangay,
            'top_barangays' => array_slice($byBarangay, 0, 5),
            'total_barangays_covered' => count($byBarangay),
        ];
    }

    private function getRsbsaDemographics(Carbon $startDate, Carbon $endDate): array
    {
        $demographics = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                SUM(CASE WHEN sex = "male" THEN 1 ELSE 0 END) as male_count,
                SUM(CASE WHEN sex = "female" THEN 1 ELSE 0 END) as female_count,
                COUNT(*) as total
            ')
            ->first();

        return [
            'male_count' => $demographics->male_count ?? 0,
            'female_count' => $demographics->female_count ?? 0,
            'total' => $demographics->total ?? 0,
            'male_percentage' => $demographics->total > 0 ?
                round(($demographics->male_count / $demographics->total) * 100, 2) : 0,
            'female_percentage' => $demographics->total > 0 ?
                round(($demographics->female_count / $demographics->total) * 100, 2) : 0,
        ];
    }

    private function getRsbsaLandAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $landData = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('farmer_land_area')
            ->select('farmer_land_area')
            ->get();

        $small = $landData->where('farmer_land_area', '<', 1)->count();
        $medium = $landData->where('farmer_land_area', '>=', 1)->where('farmer_land_area', '<', 3)->count();
        $large = $landData->where('farmer_land_area', '>=', 3)->count();

        return [
            'small_farms' => $small . ' (< 1 ha)',
            'medium_farms' => $medium . ' (1-3 ha)',
            'large_farms' => $large . ' (≥ 3 ha)',
            'total_farms' => $landData->count(),
            'min_land_area' => $landData->isNotEmpty() ? round($landData->min('farmer_land_area'), 2) . ' ha' : '0 ha',
            'max_land_area' => $landData->isNotEmpty() ? round($landData->max('farmer_land_area'), 2) . ' ha' : '0 ha',
        ];
    }

    private function getRsbsaApprovalAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $applications = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('reviewed_at')
            ->get();

        return [
            'average_review_time' => $applications->isNotEmpty() ?
                round($applications->avg(function($app) {
                    return $app->created_at->diffInHours($app->reviewed_at);
                }), 2) . ' hours' : '0 hours',
            'total_reviewed' => $applications->count(),
            'fastest_review' => $applications->isNotEmpty() ?
                round($applications->min(function($app) {
                    return $app->created_at->diffInHours($app->reviewed_at);
                }), 2) . ' hours' : 'N/A',
        ];
    }

    private function getRsbsaFarmerDetails(Carbon $startDate, Carbon $endDate): array
    {
        $byTypeOfFarm = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Farmer')
            ->whereNotNull('farmer_type_of_farm')
            ->where('farmer_type_of_farm', '!=', '')
            ->select('farmer_type_of_farm')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('farmer_type_of_farm')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'type' => $item->farmer_type_of_farm,
                'count' => $item->count,
            ])
            ->toArray();

        $byLandOwnership = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Farmer')
            ->whereNotNull('farmer_land_ownership')
            ->where('farmer_land_ownership', '!=', '')
            ->select('farmer_land_ownership')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('farmer_land_ownership')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'ownership' => $item->farmer_land_ownership,
                'count' => $item->count,
            ])
            ->toArray();

        $bySpecialStatus = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Farmer')
            ->whereNotNull('farmer_special_status')
            ->where('farmer_special_status', '!=', '')
            ->select('farmer_special_status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('farmer_special_status')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'status' => $item->farmer_special_status,
                'count' => $item->count,
            ])
            ->toArray();

        $totalFarmers = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Farmer')
            ->count();

        return [
            'total_farmers' => $totalFarmers,
            'by_type_of_farm' => $byTypeOfFarm,
            'by_land_ownership' => $byLandOwnership,
            'by_special_status' => $bySpecialStatus,
        ];
    }

    private function getRsbsaFarmerCrops(Carbon $startDate, Carbon $endDate): array
    {
        $byCrop = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Farmer')
            ->whereNotNull('farmer_crops')
            ->where('farmer_crops', '!=', '')
            ->select('farmer_crops')
            ->selectRaw('COUNT(*) as count, SUM(farmer_land_area) as total_land')
            ->groupBy('farmer_crops')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'crop' => $item->farmer_crops,
                'count' => $item->count,
                'total_land_area' => round($item->total_land ?? 0, 2),
            ])
            ->toArray();

        $otherCrops = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('farmer_crops', 'Other Crops')
            ->whereNotNull('farmer_other_crops')
            ->where('farmer_other_crops', '!=', '')
            ->select('farmer_other_crops')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('farmer_other_crops')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'crop' => $item->farmer_other_crops,
                'count' => $item->count,
            ])
            ->toArray();

        return [
            'distribution' => $byCrop,
            'top_crops' => array_slice($byCrop, 0, 5),
            'total_crop_types' => count($byCrop),
            'other_crops_specified' => $otherCrops,
        ];
    }

    private function getRsbsaFarmworkerType(Carbon $startDate, Carbon $endDate): array
    {
        $byType = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Farmworker/Laborer')
            ->whereNotNull('farmworker_type')
            ->where('farmworker_type', '!=', '')
            ->select('farmworker_type')
            ->selectRaw('COUNT(*) as count, SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count')
            ->groupBy('farmworker_type')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'type' => $item->farmworker_type,
                'count' => $item->count,
                'approved' => $item->approved_count,
                'approval_rate' => $item->count > 0 ? round(($item->approved_count / $item->count) * 100, 2) : 0,
            ])
            ->toArray();

        $otherTypes = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('farmworker_type', 'Others')
            ->whereNotNull('farmworker_other_type')
            ->where('farmworker_other_type', '!=', '')
            ->select('farmworker_other_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('farmworker_other_type')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'type' => $item->farmworker_other_type,
                'count' => $item->count,
            ])
            ->toArray();

        $totalFarmworkers = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Farmworker/Laborer')
            ->count();

        return [
            'total_farmworkers' => $totalFarmworkers,
            'distribution' => $byType,
            'other_types_specified' => $otherTypes,
        ];
    }

    private function getRsbsaFisherfolkActivity(Carbon $startDate, Carbon $endDate): array
    {
        $byActivity = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Fisherfolk')
            ->whereNotNull('fisherfolk_activity')
            ->where('fisherfolk_activity', '!=', '')
            ->select('fisherfolk_activity')
            ->selectRaw('COUNT(*) as count, SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count')
            ->groupBy('fisherfolk_activity')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'activity' => $item->fisherfolk_activity,
                'count' => $item->count,
                'approved' => $item->approved_count,
                'approval_rate' => $item->count > 0 ? round(($item->approved_count / $item->count) * 100, 2) : 0,
            ])
            ->toArray();

        $otherActivities = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('fisherfolk_activity', 'Others')
            ->whereNotNull('fisherfolk_other_activity')
            ->where('fisherfolk_other_activity', '!=', '')
            ->select('fisherfolk_other_activity')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('fisherfolk_other_activity')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'activity' => $item->fisherfolk_other_activity,
                'count' => $item->count,
            ])
            ->toArray();

        $totalFisherfolk = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Fisherfolk')
            ->count();

        return [
            'total_fisherfolk' => $totalFisherfolk,
            'distribution' => $byActivity,
            'most_common' => collect($byActivity)->sortByDesc('count')->take(3)->values()->toArray(),
            'total_activity_types' => count($byActivity),
            'other_activities_specified' => $otherActivities,
        ];
    }

    private function getRsbsaAgriyouthAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $total = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Agri-youth')
            ->count();

        $demographics = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Agri-youth')
            ->selectRaw('
                SUM(CASE WHEN sex = "Male" THEN 1 ELSE 0 END) as male_count,
                SUM(CASE WHEN sex = "Female" THEN 1 ELSE 0 END) as female_count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                COUNT(*) as total
            ')
            ->first();

        $byFarmingHousehold = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Agri-youth')
            ->whereNotNull('agriyouth_farming_household')
            ->where('agriyouth_farming_household', '!=', '')
            ->select('agriyouth_farming_household')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('agriyouth_farming_household')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'value' => $item->agriyouth_farming_household,
                'count' => $item->count,
            ])
            ->toArray();

        $byTraining = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Agri-youth')
            ->whereNotNull('agriyouth_training')
            ->where('agriyouth_training', '!=', '')
            ->select('agriyouth_training')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('agriyouth_training')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'training' => $item->agriyouth_training,
                'count' => $item->count,
            ])
            ->toArray();

        $byParticipation = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->where('main_livelihood', 'Agri-youth')
            ->whereNotNull('agriyouth_participation')
            ->where('agriyouth_participation', '!=', '')
            ->select('agriyouth_participation')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('agriyouth_participation')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'participation' => $item->agriyouth_participation,
                'count' => $item->count,
            ])
            ->toArray();

        return [
            'total_agriyouth' => $total,
            'male_count' => $demographics->male_count ?? 0,
            'female_count' => $demographics->female_count ?? 0,
            'approved' => $demographics->approved ?? 0,
            'approval_rate' => ($demographics->total ?? 0) > 0 ?
                round(($demographics->approved / $demographics->total) * 100, 2) : 0,
            'by_farming_household' => $byFarmingHousehold,
            'by_training' => $byTraining,
            'by_participation' => $byParticipation,
        ];
    }

    private function getRsbsaTrends(Carbon $startDate, Carbon $endDate): array
    {
        $prevStartDate = $startDate->copy()->subMonth();
        $prevEndDate = $endDate->copy()->subMonth();

        $currentCount = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousCount = RsbsaApplication::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();

        $change = $previousCount > 0 ? round((($currentCount - $previousCount) / $previousCount) * 100, 2) : 0;

        $byLivelihoodCurrent = RsbsaApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('main_livelihood')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('main_livelihood')
            ->orderByDesc('count')
            ->get()
            ->map(fn($item) => [
                'livelihood' => $item->main_livelihood,
                'count' => $item->count,
            ])
            ->toArray();

        return [
            'current_month_total' => $currentCount,
            'previous_month_total' => $previousCount,
            'change_percentage' => $change,
            'trend' => $change > 0 ? 'increasing' : ($change < 0 ? 'decreasing' : 'stable'),
            'livelihood_breakdown_current_month' => $byLivelihoodCurrent,
        ];
    }

    /**
     * ================================================================
     * FISHR DATA COLLECTION
     * ================================================================
     */
    public function collectFishrData(string $month = null, string $year = null): array
    {
        set_time_limit(300);

        $month = $month ?? now()->format('m');
        $year = $year ?? now()->format('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $cacheKey = "dss_fishr_data_{$year}_{$month}";

        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return [
                'period' => [
                    'month' => $startDate->format('F Y'),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'fishr_stats' => $this->getFishrStats($startDate, $endDate),
                'fishr_by_livelihood' => $this->getFishrByLivelihood($startDate, $endDate),
                'fishr_secondary_livelihood' => $this->getFishrBySecondaryLivelihood($startDate, $endDate),
                'fishr_by_barangay' => $this->getFishrByBarangay($startDate, $endDate),
                'fishr_demographics' => $this->getFishrDemographics($startDate, $endDate),
                'fishr_approval_analysis' => $this->getFishrApprovalAnalysis($startDate, $endDate),
                'fishr_trends' => $this->getFishrTrends($startDate, $endDate),
            ];
        });
    }

    private function getFishrStats(Carbon $startDate, Carbon $endDate): array
    {
        $stats = FishrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status IN ("pending", "under_review") THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN fishr_number IS NOT NULL THEN 1 ELSE 0 END) as with_fishr_number
            ')
            ->first();

        return [
            'total_applications' => $stats->total_applications ?? 0,
            'approved' => $stats->approved ?? 0,
            'rejected' => $stats->rejected ?? 0,
            'pending' => $stats->pending ?? 0,
            'with_fishr_number' => $stats->with_fishr_number ?? 0,
            'approval_rate' => $stats->total_applications > 0 ?
                round(($stats->approved / $stats->total_applications) * 100, 2) : 0,
            'rejection_rate' => $stats->total_applications > 0 ?
                round(($stats->rejected / $stats->total_applications) * 100, 2) : 0,
        ];
    }

    private function getFishrByLivelihood(Carbon $startDate, Carbon $endDate): array
    {
        $byLivelihood = FishrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('main_livelihood')
            ->selectRaw('
                COUNT(*) as count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count
            ')
            ->groupBy('main_livelihood')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'livelihood' => $item->main_livelihood ?? 'Not Specified',
                    'total' => $item->count,
                    'approved' => $item->approved_count,
                    'approval_rate' => $item->count > 0 ? round(($item->approved_count / $item->count) * 100, 2) : 0,
                ];
            })
            ->toArray();

        return [
            'distribution' => $byLivelihood,
            'most_common' => collect($byLivelihood)->sortByDesc('total')->take(3)->values()->toArray(),
            'total_livelihood_types' => count($byLivelihood),
        ];
    }

    private function getFishrBySecondaryLivelihood(Carbon $startDate, Carbon $endDate): array
    {
        $bySecondary = FishrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('secondary_livelihood')
            ->where('secondary_livelihood', '!=', '')
            ->select('secondary_livelihood')
            ->selectRaw('
                COUNT(*) as count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count
            ')
            ->groupBy('secondary_livelihood')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'livelihood' => $item->secondary_livelihood ?? 'Not Specified',
                    'total' => $item->count,
                    'approved' => $item->approved_count,
                    'approval_rate' => $item->count > 0 ? round(($item->approved_count / $item->count) * 100, 2) : 0,
                ];
            })
            ->toArray();

        $totalWithSecondary = FishrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('secondary_livelihood')
            ->where('secondary_livelihood', '!=', '')
            ->count();

        return [
            'distribution' => $bySecondary,
            'total_with_secondary' => $totalWithSecondary,
            'total_types' => count($bySecondary),
        ];
    }

    private function getFishrByBarangay(Carbon $startDate, Carbon $endDate): array
    {
        $byBarangay = FishrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('barangay')
            ->selectRaw('
                COUNT(*) as count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved
            ')
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'barangay' => $item->barangay ?? 'Unknown',
                    'applications' => $item->count,
                    'approved' => $item->approved,
                ];
            })
            ->toArray();

        return [
            'distribution' => $byBarangay,
            'top_barangays' => array_slice($byBarangay, 0, 5),
            'total_barangays_covered' => count($byBarangay),
        ];
    }

    private function getFishrDemographics(Carbon $startDate, Carbon $endDate): array
    {
        $demographics = FishrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                SUM(CASE WHEN sex = "male" THEN 1 ELSE 0 END) as male_count,
                SUM(CASE WHEN sex = "female" THEN 1 ELSE 0 END) as female_count,
                COUNT(*) as total
            ')
            ->first();

        return [
            'male_count' => $demographics->male_count ?? 0,
            'female_count' => $demographics->female_count ?? 0,
            'total' => $demographics->total ?? 0,
            'male_percentage' => $demographics->total > 0 ?
                round(($demographics->male_count / $demographics->total) * 100, 2) : 0,
            'female_percentage' => $demographics->total > 0 ?
                round(($demographics->female_count / $demographics->total) * 100, 2) : 0,
        ];
    }

    private function getFishrApprovalAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $applications = FishrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('status_updated_at')
            ->get();

        return [
            'average_processing_time' => $applications->isNotEmpty() ?
                round($applications->avg(function($app) {
                    return $app->created_at->diffInHours($app->status_updated_at);
                }), 2) . ' hours' : '0 hours',
            'total_processed' => $applications->count(),
            'fastest_processing' => $applications->isNotEmpty() ?
                round($applications->min(function($app) {
                    return $app->created_at->diffInHours($app->status_updated_at);
                }), 2) . ' hours' : 'N/A',
            'slowest_processing' => $applications->isNotEmpty() ?
                round($applications->max(function($app) {
                    return $app->created_at->diffInHours($app->status_updated_at);
                }), 2) . ' hours' : 'N/A',
        ];
    }

    private function getFishrTrends(Carbon $startDate, Carbon $endDate): array
    {
        $prevStartDate = $startDate->copy()->subMonth();
        $prevEndDate = $endDate->copy()->subMonth();

        $currentCount = FishrApplication::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousCount = FishrApplication::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();

        $change = $previousCount > 0 ? round((($currentCount - $previousCount) / $previousCount) * 100, 2) : 0;

        return [
            'current_month_total' => $currentCount,
            'previous_month_total' => $previousCount,
            'change_percentage' => $change,
            'trend' => $change > 0 ? 'increasing' : ($change < 0 ? 'decreasing' : 'stable'),
        ];
    }

    /**
     * ================================================================
     * BOATR DATA COLLECTION
     * ================================================================
     */
    public function collectBoatrData(string $month = null, string $year = null): array
    {
        set_time_limit(300);

        $month = $month ?? now()->format('m');
        $year = $year ?? now()->format('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $cacheKey = "dss_boatr_data_{$year}_{$month}";

        return Cache::remember($cacheKey, 1800, function () use ($startDate, $endDate) {
            return [
                'period' => [
                    'month' => $startDate->format('F Y'),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'boatr_stats' => $this->getBoatrStats($startDate, $endDate),
                'boatr_by_boat_type' => $this->getBoatrByBoatType($startDate, $endDate),
                'boatr_by_barangay' => $this->getBoatrByBarangay($startDate, $endDate),
                'boatr_engine_analysis' => $this->getBoatrEngineAnalysis($startDate, $endDate),
                'boatr_fishing_gear' => $this->getBoatrFishingGearAnalysis($startDate, $endDate),
                'boatr_inspection_analysis' => $this->getBoatrInspectionAnalysis($startDate, $endDate),
                'boatr_approval_analysis' => $this->getBoatrApprovalAnalysis($startDate, $endDate),
                'boatr_trends' => $this->getBoatrTrends($startDate, $endDate),
                'boatr_motorized_breakdown' => $this->getBoatrMotorizedBreakdown($startDate, $endDate),
                'boatr_multiple_registrations' => $this->getBoatrMultipleRegistrations($startDate, $endDate),
            ];
        });
    }

    private function getBoatrStats(Carbon $startDate, Carbon $endDate): array
    {
        $stats = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status IN ("pending", "under_review") THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN inspection_completed = 1 THEN 1 ELSE 0 END) as inspections_completed,
                SUM(CASE WHEN documents_verified = 1 THEN 1 ELSE 0 END) as documents_verified,
                AVG(boat_length) as avg_boat_length,
                AVG(engine_horsepower) as avg_horsepower
            ')
            ->first();

        return [
            'total_applications' => $stats->total_applications ?? 0,
            'approved' => $stats->approved ?? 0,
            'rejected' => $stats->rejected ?? 0,
            'pending' => $stats->pending ?? 0,
            'inspections_completed' => $stats->inspections_completed ?? 0,
            'documents_verified' => $stats->documents_verified ?? 0,
            'approval_rate' => $stats->total_applications > 0 ?
                round(($stats->approved / $stats->total_applications) * 100, 2) : 0,
            'inspection_rate' => $stats->total_applications > 0 ?
                round(($stats->inspections_completed / $stats->total_applications) * 100, 2) : 0,
            'document_verification_rate' => $stats->total_applications > 0 ?
                round(($stats->documents_verified / $stats->total_applications) * 100, 2) : 0,
            'avg_boat_length' => round($stats->avg_boat_length ?? 0, 2) . ' meters',
            'avg_horsepower' => round($stats->avg_horsepower ?? 0, 2) . ' HP',
        ];
    }

    private function getBoatrByBoatType(Carbon $startDate, Carbon $endDate): array
    {
        $byBoatType = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('boat_type')
            ->selectRaw('
                COUNT(*) as count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count,
                AVG(boat_length) as avg_length,
                AVG(engine_horsepower) as avg_hp
            ')
            ->groupBy('boat_type')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'boat_type' => $item->boat_type ?? 'Not Specified',
                    'total' => $item->count,
                    'approved' => $item->approved_count,
                    'approval_rate' => $item->count > 0 ? round(($item->approved_count / $item->count) * 100, 2) : 0,
                    'avg_length' => round($item->avg_length ?? 0, 2),
                    'avg_horsepower' => round($item->avg_hp ?? 0, 2),
                ];
            })
            ->toArray();

        $byClassification = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('boat_classification')
            ->where('boat_classification', '!=', '')
            ->select('boat_classification')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('boat_classification')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'classification' => $item->boat_classification,
                    'total' => $item->count,
                ];
            })
            ->toArray();

        return [
            'distribution' => $byBoatType,
            'most_common' => collect($byBoatType)->sortByDesc('total')->take(3)->values()->toArray(),
            'total_boat_types' => count($byBoatType),
            'by_classification' => $byClassification,
        ];
    }

    private function getBoatrByBarangay(Carbon $startDate, Carbon $endDate): array
    {
        $byBarangay = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('barangay')
            ->selectRaw('
                COUNT(*) as count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved
            ')
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'barangay' => $item->barangay ?? 'Unknown',
                    'applications' => $item->count,
                    'approved' => $item->approved,
                ];
            })
            ->toArray();

        return [
            'distribution' => $byBarangay,
            'top_barangays' => array_slice($byBarangay, 0, 5),
            'total_barangays_covered' => count($byBarangay),
        ];
    }

    private function getBoatrEngineAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $engineTypes = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->select('engine_type')
            ->selectRaw('COUNT(*) as count, AVG(engine_horsepower) as avg_hp')
            ->groupBy('engine_type')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'type' => $item->engine_type ?? 'Not Specified',
                    'count' => $item->count,
                    'avg_horsepower' => round($item->avg_hp ?? 0, 2),
                ];
            })
            ->toArray();

        return [
            'engine_types' => $engineTypes,
            'most_common_engine' => $engineTypes[0] ?? null,
        ];
    }

    private function getBoatrFishingGearAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $byGear = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('primary_fishing_gear')
            ->where('primary_fishing_gear', '!=', '')
            ->select('primary_fishing_gear')
            ->selectRaw('
                COUNT(*) as count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count
            ')
            ->groupBy('primary_fishing_gear')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'gear' => $item->primary_fishing_gear,
                    'total' => $item->count,
                    'approved' => $item->approved_count,
                    'approval_rate' => $item->count > 0 ? round(($item->approved_count / $item->count) * 100, 2) : 0,
                ];
            })
            ->toArray();

        return [
            'distribution' => $byGear,
            'most_common' => collect($byGear)->sortByDesc('total')->take(3)->values()->toArray(),
            'total_gear_types' => count($byGear),
        ];
    }

    private function getBoatrInspectionAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $inspectionStats = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                SUM(CASE WHEN inspection_completed = 1 THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN inspection_completed = 0 OR inspection_completed IS NULL THEN 1 ELSE 0 END) as pending,
                COUNT(*) as total
            ')
            ->first();

        return [
            'inspections_completed' => $inspectionStats->completed ?? 0,
            'inspections_pending' => $inspectionStats->pending ?? 0,
            'total_applications' => $inspectionStats->total ?? 0,
            'completion_rate' => $inspectionStats->total > 0 ?
                round(($inspectionStats->completed / $inspectionStats->total) * 100, 2) : 0,
        ];
    }

    private function getBoatrApprovalAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $applications = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('reviewed_at')
            ->get();

        return [
            'average_processing_time' => $applications->isNotEmpty() ?
                round($applications->avg(function($app) {
                    return $app->created_at->diffInHours($app->reviewed_at);
                }), 2) . ' hours' : '0 hours',
            'total_processed' => $applications->count(),
            'fastest_processing' => $applications->isNotEmpty() ?
                round($applications->min(function($app) {
                    return $app->created_at->diffInHours($app->reviewed_at);
                }), 2) . ' hours' : 'N/A',
            'slowest_processing' => $applications->isNotEmpty() ?
                round($applications->max(function($app) {
                    return $app->created_at->diffInHours($app->reviewed_at);
                }), 2) . ' hours' : 'N/A',
        ];
    }

    private function getBoatrTrends(Carbon $startDate, Carbon $endDate): array
    {
        $prevStartDate = $startDate->copy()->subMonth();
        $prevEndDate = $endDate->copy()->subMonth();

        $currentCount = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousCount = BoatrApplication::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();

        $change = $previousCount > 0 ? round((($currentCount - $previousCount) / $previousCount) * 100, 2) : 0;

        return [
            'current_month_total' => $currentCount,
            'previous_month_total' => $previousCount,
            'change_percentage' => $change,
            'trend' => $change > 0 ? 'increasing' : ($change < 0 ? 'decreasing' : 'stable'),
        ];
    }

    private function getBoatrMotorizedBreakdown(Carbon $startDate, Carbon $endDate): array
    {
        $distribution = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('boat_classification')
            ->where('boat_classification', '!=', '')
            ->select('boat_classification')
            ->selectRaw('
                COUNT(*) as count,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN inspection_completed = 1 THEN 1 ELSE 0 END) as inspections_completed,
                AVG(engine_horsepower) as avg_horsepower
            ')
            ->groupBy('boat_classification')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'classification' => $item->boat_classification,
                    'total' => $item->count,
                    'approved' => $item->approved_count,
                    'approval_rate' => $item->count > 0 ? round(($item->approved_count / $item->count) * 100, 2) : 0,
                    'inspections_completed' => $item->inspections_completed,
                    'avg_horsepower' => round($item->avg_horsepower ?? 0, 2),
                ];
            })
            ->toArray();

        $total = array_sum(array_column($distribution, 'total'));
        $distribution = array_map(function ($item) use ($total) {
            $item['percentage'] = $total > 0 ? round(($item['total'] / $total) * 100, 1) : 0;
            return $item;
        }, $distribution);

        $motorized = collect($distribution)->where('classification', 'Motorized')->first();
        $nonMotorized = collect($distribution)->where('classification', 'Non-motorized')->first();

        return [
            'distribution' => $distribution,
            'total' => $total,
            'motorized_count' => $motorized['total'] ?? 0,
            'non_motorized_count' => $nonMotorized['total'] ?? 0,
            'motorized_percentage' => $motorized['percentage'] ?? 0,
            'non_motorized_percentage' => $nonMotorized['percentage'] ?? 0,
        ];
    }

    private function getBoatrMultipleRegistrations(Carbon $startDate, Carbon $endDate): array
    {
        $multiple = BoatrApplication::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('fishr_number')
            ->where('fishr_number', '!=', '')
            ->select('fishr_number', DB::raw('COUNT(*) as boat_count'))
            ->groupBy('fishr_number')
            ->having('boat_count', '>', 1)
            ->orderByDesc('boat_count')
            ->get()
            ->map(function ($item) {
                return [
                    'fishr_number' => $item->fishr_number,
                    'boat_count'   => $item->boat_count,
                ];
            })
            ->toArray();

        return [
            'fishers_with_multiple' => count($multiple),
            'top_details'           => array_slice($multiple, 0, 10),
            'max_boats'             => count($multiple) > 0 ? max(array_column($multiple, 'boat_count')) : 0,
        ];
    }
}
