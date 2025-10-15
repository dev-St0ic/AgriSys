<?php

namespace App\Services;

use App\Models\SeedlingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SeedlingAnalyticsService
{
    protected SeedlingDSSService $dssService;
    protected SeedlingDSSPDFService $pdfService;

    public function __construct(SeedlingDSSService $dssService, SeedlingDSSPDFService $pdfService)
    {
        $this->dssService = $dssService;
        $this->pdfService = $pdfService;
    }

    /**
     * Get comprehensive analytics data with DSS insights
     */
    public function getComprehensiveAnalytics(string $startDate = null, string $endDate = null): array
    {
        $analyticsData = $this->getAnalyticsData($startDate, $endDate);
        $insights = $this->dssService->generateInsights($analyticsData);
        
        return [
            'analytics' => $analyticsData,
            'insights' => $insights,
            'dss_recommendations' => $this->generateDSSRecommendations($analyticsData, $insights),
            'performance_metrics' => $this->calculatePerformanceMetrics($analyticsData),
        ];
    }

    /**
     * Get analytics data for specified period
     */
    public function getAnalyticsData(string $startDate = null, string $endDate = null): array
    {
        $query = SeedlingRequest::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $requests = $query->get();

        return [
            'overview' => $this->getOverviewMetrics($requests),
            'monthlyTrends' => $this->getMonthlyTrends($requests),
            'barangayAnalysis' => $this->getBarangayAnalysis($requests),
            'topItems' => $this->getTopItems($requests),
            'leastRequestedItems' => $this->getLeastRequestedItems($requests),
            'statusAnalysis' => $this->getStatusAnalysis($requests),
            'categoryAnalysis' => $this->getCategoryAnalysis($requests),
            'processingTimeAnalysis' => $this->getProcessingTimeAnalysis($requests),
            'seasonalPatterns' => $this->getSeasonalPatterns($requests),
            'farmerDemographics' => $this->getFarmerDemographics($requests),
        ];
    }

    /**
     * Generate DSS-specific recommendations
     */
    private function generateDSSRecommendations(array $analyticsData, array $insights): array
    {
        $overview = $analyticsData['overview'];
        $recommendations = [];

        // Approval Rate Recommendations
        if ($overview['approval_rate'] < 70) {
            $recommendations['approval_rate'] = [
                'status' => 'critical',
                'message' => 'Approval rate critically low',
                'current' => $overview['approval_rate'] . '%',
                'target' => '85%',
                'action' => 'Review and streamline approval criteria'
            ];
        }

        // Processing Time Recommendations
        $avgProcessingDays = $analyticsData['processingTimeAnalysis']['avg_processing_days'] ?? 0;
        if ($avgProcessingDays > 5) {
            $recommendations['processing_time'] = [
                'status' => 'warning',
                'message' => 'Processing time exceeds target',
                'current' => round($avgProcessingDays, 1) . ' days',
                'target' => '<3 days',
                'action' => 'Optimize approval workflow'
            ];
        }

        // Barangay Coverage Recommendations
        $activeBarangays = $overview['active_barangays'];
        if ($activeBarangays < 10) {
            $recommendations['coverage'] = [
                'Expand outreach programs to underserved barangays',
                'Establish satellite distribution centers',
                'Partner with local agricultural coordinators'
            ];
        }

        return $recommendations;
    }
    /**
     * Calculate performance metrics
     */
    private function calculatePerformanceMetrics(array $data): array
    {
        $overview = $data['overview'];
        
        return [
            'overall_score' => $this->calculateOverallScore($data),
            'efficiency_rating' => $this->calculateEfficiencyRating($data),
            'distribution_effectiveness' => $this->calculateDistributionEffectiveness($data),
            'farmer_satisfaction_proxy' => $this->calculateSatisfactionProxy($data),
            'resource_utilization' => $this->calculateResourceUtilization($data),
        ];
    }

    /**
     * Get overview metrics
     */
    private function getOverviewMetrics(Collection $requests): array
    {
        $totalRequests = $requests->count();
        $approvedRequests = $requests->where('overall_status', 'approved')->count();
        $partiallyApprovedRequests = $requests->where('overall_status', 'partially_approved')->count();
        $rejectedRequests = $requests->where('overall_status', 'rejected')->count();
        
        $totalQuantity = $requests->sum('total_quantity');
        $uniqueApplicants = $requests->pluck('email')->filter()->unique()->count();
        $activeBarangays = $requests->pluck('barangay')->unique()->count();

        return [
            'total_requests' => $totalRequests,
            'approved_requests' => $approvedRequests + $partiallyApprovedRequests,
            'rejected_requests' => $rejectedRequests,
            'pending_requests' => $requests->where('overall_status', 'under_review')->count(),
            'approval_rate' => $totalRequests > 0 ? round((($approvedRequests + $partiallyApprovedRequests) / $totalRequests) * 100, 2) : 0,
            'total_quantity_requested' => $totalQuantity,
            'avg_request_size' => $totalRequests > 0 ? round($totalQuantity / $totalRequests, 2) : 0,
            'unique_applicants' => $uniqueApplicants,
            'active_barangays' => $activeBarangays,
        ];
    }

    /**
     * Get monthly trends
     */
    private function getMonthlyTrends(Collection $requests): array
    {
        return $requests->groupBy(function($request) {
            return $request->created_at->format('Y-m');
        })->map(function($monthRequests) {
            $total = $monthRequests->count();
            $approved = $monthRequests->whereIn('overall_status', ['approved', 'partially_approved'])->count();
            
            return [
                'total_requests' => $total,
                'approved_requests' => $approved,
                'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
                'total_quantity' => $monthRequests->sum('total_quantity'),
            ];
        })->toArray();
    }

    /**
     * Get barangay analysis
     */
    private function getBarangayAnalysis(Collection $requests): Collection
    {
        return $requests->groupBy('barangay')
            ->map(function($barangayRequests, $barangay) {
                $total = $barangayRequests->count();
                $approved = $barangayRequests->whereIn('overall_status', ['approved', 'partially_approved'])->count();
                $totalQuantity = $barangayRequests->sum('total_quantity');

                return (object) [
                    'barangay' => $barangay,
                    'total_requests' => $total,
                    'approved' => $approved,
                    'rejected' => $barangayRequests->where('overall_status', 'rejected')->count(),
                    'pending' => $barangayRequests->where('overall_status', 'under_review')->count(),
                    'total_quantity' => $totalQuantity,
                    'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
                    'avg_request_size' => $total > 0 ? round($totalQuantity / $total, 2) : 0,
                ];
            })
            ->sortByDesc('total_requests')
            ->values();
    }

    /**
     * Get top requested items
     */
    private function getTopItems(Collection $requests): array
    {
        $items = collect();
        
        // Update to use all 6 categories
        $categories = ['seeds', 'seedlings', 'fruits', 'ornamentals', 'fingerlings', 'fertilizers'];

        foreach ($requests as $request) {
            foreach ($categories as $category) {
                $categoryData = $request->$category;
                
                // Safe JSON decode
                if (is_string($categoryData)) {
                    $categoryData = json_decode($categoryData, true);
                }
                
                if ($categoryData && is_array($categoryData)) {
                    foreach ($categoryData as $item) {
                        $name = is_array($item) ? ($item['name'] ?? '') : $item;
                        $quantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
                        
                        if (empty($name)) continue;
                        
                        $existing = $items->firstWhere('name', $name);
                        if ($existing) {
                            $existing['total_quantity'] += $quantity;
                            $existing['request_count']++;
                        } else {
                            $items->push([
                                'name' => $name,
                                'total_quantity' => $quantity,
                                'request_count' => 1,
                                'category' => $category
                            ]);
                        }
                    }
                }
            }
        }

        return $items->sortByDesc('total_quantity')->take(15)->values()->toArray();
    }

    /**
     * Get least requested items
     */
    private function getLeastRequestedItems(Collection $requests): array
    {
        $items = collect();
        
        // Update to use all 6 categories
        $categories = ['seeds', 'seedlings', 'fruits', 'ornamentals', 'fingerlings', 'fertilizers'];

        foreach ($requests as $request) {
            foreach ($categories as $category) {
                $categoryData = $request->$category;
                
                // Safe JSON decode
                if (is_string($categoryData)) {
                    $categoryData = json_decode($categoryData, true);
                }
                
                if ($categoryData && is_array($categoryData)) {
                    foreach ($categoryData as $item) {
                        $name = is_array($item) ? ($item['name'] ?? '') : $item;
                        $quantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;
                        
                        if (empty($name)) continue;
                        
                        $existing = $items->firstWhere('name', $name);
                        if ($existing) {
                            $existing['total_quantity'] += $quantity;
                            $existing['request_count']++;
                        } else {
                            $items->push([
                                'name' => $name,
                                'total_quantity' => $quantity,
                                'request_count' => 1,
                                'category' => $category
                            ]);
                        }
                    }
                }
            }
        }

        return $items->sortBy('total_quantity')->take(10)->values()->toArray();
    }

    /**
     * Get status analysis
     */
    private function getStatusAnalysis(Collection $requests): array
    {
        return [
            'approved' => $requests->where('overall_status', 'approved')->count(),
            'partially_approved' => $requests->where('overall_status', 'partially_approved')->count(),
            'rejected' => $requests->where('overall_status', 'rejected')->count(),
            'under_review' => $requests->where('overall_status', 'under_review')->count(),
        ];
    }

    /**
     * Get category analysis
     */
    private function getCategoryAnalysis(Collection $requests): array
    {
        // Update to use all 6 categories
        $analysis = [
            'seeds' => ['requests' => 0, 'total_items' => 0],
            'seedlings' => ['requests' => 0, 'total_items' => 0],
            'fruits' => ['requests' => 0, 'total_items' => 0],
            'ornamentals' => ['requests' => 0, 'total_items' => 0],
            'fingerlings' => ['requests' => 0, 'total_items' => 0],
            'fertilizers' => ['requests' => 0, 'total_items' => 0],
        ];

        foreach ($requests as $request) {
            foreach (array_keys($analysis) as $category) {
                $categoryData = $request->$category;
                
                // Safe JSON decode
                if (is_string($categoryData)) {
                    $categoryData = json_decode($categoryData, true);
                }
                
                if ($categoryData && is_array($categoryData) && count($categoryData) > 0) {
                    $analysis[$category]['requests']++;
                    $analysis[$category]['total_items'] += count($categoryData);
                }
            }
        }

        return $analysis;
    }

    /**
     * Get processing time analysis
     */
    private function getProcessingTimeAnalysis(Collection $requests): array
    {
        $processedRequests = $requests->whereNotIn('overall_status', ['under_review']);
        
        $processingTimes = $processedRequests->map(function($request) {
            return $request->created_at->diffInDays($request->updated_at);
        });

        $sameDay = $processedRequests->filter(function($request) {
            return $request->created_at->isSameDay($request->updated_at);
        })->count();

        $oneTo3Days = $processedRequests->filter(function($request) {
            $days = $request->created_at->diffInDays($request->updated_at);
            return $days >= 1 && $days <= 3;
        })->count();

        $fourTo7Days = $processedRequests->filter(function($request) {
            $days = $request->created_at->diffInDays($request->updated_at);
            return $days >= 4 && $days <= 7;
        })->count();

        $over7Days = $processedRequests->filter(function($request) {
            $days = $request->created_at->diffInDays($request->updated_at);
            return $days > 7;
        })->count();

        return [
            'avg_processing_days' => $processingTimes->count() > 0 ? round($processingTimes->average(), 2) : 0,
            'same_day' => $sameDay,
            '1_3_days' => $oneTo3Days,
            '4_7_days' => $fourTo7Days,
            'over_7_days' => $over7Days,
        ];
    }

    /**
     * Get seasonal patterns
     */
    private function getSeasonalPatterns(Collection $requests): array
    {
        return $requests->groupBy(function($request) {
            return $request->created_at->quarter;
        })->map(function($quarterRequests, $quarter) {
            return [
                'quarter' => "Q{$quarter}",
                'total_requests' => $quarterRequests->count(),
                'approval_rate' => $quarterRequests->count() > 0 ? 
                    round(($quarterRequests->whereIn('overall_status', ['approved', 'partially_approved'])->count() / $quarterRequests->count()) * 100, 2) : 0,
                'total_quantity' => $quarterRequests->sum('total_quantity'),
            ];
        })->toArray();
    }

    /**
     * Get farmer demographics
     */
    private function getFarmerDemographics(Collection $requests): array
    {
        $uniqueEmails = $requests->pluck('email')->filter()->unique();
        
        return [
            'total_unique_farmers' => $uniqueEmails->count(),
            'repeat_applicants' => $requests->whereNotNull('email')
                ->groupBy('email')
                ->filter(function($group) { return $group->count() > 1; })
                ->count(),
            'barangay_distribution' => $requests->groupBy('barangay')
                ->map(function($group) { return $group->count(); })
                ->sortDesc()
                ->toArray(),
        ];
    }

    /**
     * Calculate overall performance score
     */
    private function calculateOverallScore(array $data): int
    {
        $overview = $data['overview'];
        $processing = $data['processingTimeAnalysis'];

        $approvalScore = min(100, ($overview['approval_rate'] / 90) * 40); // 40% weight
        $efficiencyScore = min(100, max(0, (7 - $processing['avg_processing_days']) / 7) * 30); // 30% weight
        $coverageScore = min(100, ($overview['active_barangays'] / 20) * 20); // 20% weight
        $volumeScore = min(100, ($overview['total_requests'] / 1000) * 10); // 10% weight

        return (int) ($approvalScore + $efficiencyScore + $coverageScore + $volumeScore);
    }

    /**
     * Calculate efficiency rating
     */
    private function calculateEfficiencyRating(array $data): string
    {
        $avgDays = $data['processingTimeAnalysis']['avg_processing_days'];
        
        if ($avgDays <= 2) return 'Excellent';
        if ($avgDays <= 5) return 'Good';
        if ($avgDays <= 10) return 'Fair';
        return 'Needs Improvement';
    }

    /**
     * Calculate distribution effectiveness
     */
    private function calculateDistributionEffectiveness(array $data): int
    {
        $overview = $data['overview'];
        $barangayCount = $overview['active_barangays'];
        $approvalRate = $overview['approval_rate'];
        
        return (int) (($barangayCount / 30) * 50 + ($approvalRate / 100) * 50);
    }

    /**
     * Calculate satisfaction proxy
     */
    private function calculateSatisfactionProxy(array $data): string
    {
        $approvalRate = $data['overview']['approval_rate'];
        $avgProcessing = $data['processingTimeAnalysis']['avg_processing_days'];
        
        $score = ($approvalRate * 0.7) + (max(0, (10 - $avgProcessing)) * 3); // Max processing penalty
        
        if ($score >= 80) return 'High';
        if ($score >= 60) return 'Medium';
        return 'Low';
    }

    /**
     * Calculate resource utilization
     */
    private function calculateResourceUtilization(array $data): int
    {
        $totalRequests = $data['overview']['total_requests'];
        $totalQuantity = $data['overview']['total_quantity_requested'];
        
        // Assume optimal utilization is 100 requests per month with 50 average quantity
        $requestUtilization = min(100, ($totalRequests / 100) * 100);
        $quantityUtilization = min(100, ($totalQuantity / 5000) * 100);
        
        return (int) (($requestUtilization + $quantityUtilization) / 2);
    }

    /**
     * Generate DSS PDF report
     */
    public function generateDSSReport(string $startDate = null, string $endDate = null): \Barryvdh\DomPDF\PDF
    {
        $data = $this->getComprehensiveAnalytics($startDate, $endDate);
        
        return $this->pdfService->generateDSSReport(
            $data['analytics'],
            $data['insights'],
            $startDate,
            $endDate
        );
    }
}