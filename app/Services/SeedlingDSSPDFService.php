<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SeedlingDSSPDFService
{
    /**
     * Generate comprehensive DSS report as PDF
     */
    public function generateDSSReport(array $analyticsData, array $insights, string $startDate = null, string $endDate = null): \Barryvdh\DomPDF\PDF
    {
        $reportData = $this->prepareReportData($analyticsData, $insights, $startDate, $endDate);

        $pdf = Pdf::loadView('admin.analytics.seedlings.dss-report', $reportData);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        return $pdf;
    }

    /**
     * Prepare comprehensive report data with proper formatting
     */
    private function prepareReportData(array $analyticsData, array $insights, string $startDate = null, string $endDate = null): array
    {
        // Check if this is the new prescriptive format
        $isPrescriptive = isset($insights['critical_prescriptions']) || isset($insights['ai_confidence']) && $insights['ai_confidence'] === 'prescriptive_analysis';

        return [
            'title' => 'Seedling Distribution Analytics - ' . ($isPrescriptive ? 'Prescriptive DSS Report' : 'DSS Report'),
            'subtitle' => $isPrescriptive ? 'Prescriptive Decision Support System Analysis' : 'Decision Support System Analysis & Recommendations',
            'period' => $this->formatReportPeriod($startDate, $endDate),
            'generated_at' => Carbon::now()->format('F j, Y \a\t g:i A'),
            'overview' => $this->sanitizeOverviewData($analyticsData['overview'] ?? []),
            'insights' => $isPrescriptive ? $this->transformPrescriptiveInsights($insights) : $this->sanitizeInsightsData($insights),
            'analytics' => $this->processAnalyticsForReport($analyticsData),
            'is_prescriptive' => $isPrescriptive,
            'prescriptive_data' => $isPrescriptive ? $this->preparePrescriptiveData($insights) : null,

            // Add these direct variables for template compatibility:
            'topItems' => collect($this->sanitizeItemData($analyticsData['topItems'] ?? [])),
            'leastRequestedItems' => collect($this->sanitizeItemData($analyticsData['leastRequestedItems'] ?? [])),
            'barangayAnalysis' => $this->sanitizeBarangayData($analyticsData['barangayAnalysis'] ?? []),
            'processingTimeAnalysis' => $analyticsData['processingTimeAnalysis'] ?? [],

            'charts_data' => $this->prepareChartsData($analyticsData),
            'performance_summary' => $this->generatePerformanceSummary($analyticsData),
            'ai_confidence' => $insights['ai_confidence'] ?? 'medium'
        ];
    }
    /**
     * Transform prescriptive insights to be compatible with existing template
     */
    private function transformPrescriptiveInsights(array $insights): array
    {
        // Map prescriptive sections to template-expected sections
        return [
            'executive_summary' => $insights['executive_summary'] ?? [],
            'performance_insights' => $this->combinePrescriptiveSections($insights, [
                'critical_prescriptions',
                'process_prescriptions'
            ]),
            'strategic_recommendations' => $insights['resource_prescriptions'] ?? [],
            'operational_prescriptions' => $insights['geographic_prescriptions'] ?? [],
            'risk_assessment' => $insights['capacity_prescriptions'] ?? [],
            'generated_at' => $insights['generated_at'] ?? Carbon::now()->toISOString(),
            'ai_confidence' => $insights['ai_confidence'] ?? 'prescriptive'
        ];
    }

    /**
     * Prepare prescriptive-specific data for enhanced PDF sections
     */
    private function preparePrescriptiveData(array $insights): array
    {
        return [
            'data_quality_score' => $insights['data_quality_score'] ?? 0,
            'confidence_level' => $insights['ai_confidence'] ?? 'unknown',
            'critical_actions' => array_slice($insights['critical_prescriptions'] ?? [], 0, 5),
            'resource_optimizations' => array_slice($insights['resource_prescriptions'] ?? [], 0, 5),
            'geographic_interventions' => array_slice($insights['geographic_prescriptions'] ?? [], 0, 5),
            'process_improvements' => array_slice($insights['process_prescriptions'] ?? [], 0, 5),
            'capacity_adjustments' => array_slice($insights['capacity_prescriptions'] ?? [], 0, 3),
            'monitoring_protocols' => array_slice($insights['monitoring_prescriptions'] ?? [], 0, 3),

            // AI-enhanced sections if available
            'ai_roadmap' => $insights['ai_implementation_roadmap'] ?? [],
            'ai_resources' => $insights['ai_resource_requirements'] ?? [],
            'ai_metrics' => $insights['ai_success_metrics'] ?? [],
            'ai_risks' => $insights['ai_risk_mitigation'] ?? [],
            'ai_stakeholders' => $insights['ai_stakeholder_actions'] ?? []
        ];
    }

    /**
     * Combine multiple prescriptive sections into one array
     */
    private function combinePrescriptiveSections(array $insights, array $sectionKeys): array
    {
        $combined = [];
        foreach ($sectionKeys as $key) {
            if (!empty($insights[$key]) && is_array($insights[$key])) {
                $combined = array_merge($combined, $insights[$key]);
            }
        }
        return $combined;
    }

    /**
     * Sanitize overview data to ensure proper types
     */
    private function sanitizeOverviewData(array $overview): array
    {
        return [
            'total_requests' => (int) ($overview['total_requests'] ?? 0),
            'approved_requests' => (int) ($overview['approved_requests'] ?? 0),
            'rejected_requests' => (int) ($overview['rejected_requests'] ?? 0),
            'pending_requests' => (int) ($overview['pending_requests'] ?? 0),
            'approval_rate' => (float) ($overview['approval_rate'] ?? 0),
            'total_quantity_requested' => (int) ($overview['total_quantity_requested'] ?? 0),
            'avg_request_size' => (float) ($overview['avg_request_size'] ?? 0),
            'unique_applicants' => (int) ($overview['unique_applicants'] ?? 0),
            'active_barangays' => (int) ($overview['active_barangays'] ?? 0),
        ];
    }

    /**
     * Sanitize insights data to ensure arrays contain strings
     */
    private function sanitizeInsightsData(array $insights): array
    {
        $sanitized = [];

        $sections = [
            'executive_summary',
            'performance_insights',
            'strategic_recommendations',
            'operational_prescriptions',
            'risk_assessment'
        ];

        foreach ($sections as $section) {
            $sanitized[$section] = [];

            if (isset($insights[$section]) && is_array($insights[$section])) {
                foreach ($insights[$section] as $item) {
                    if (is_string($item)) {
                        $sanitized[$section][] = $item;
                    } elseif (is_array($item) && isset($item['text'])) {
                        $sanitized[$section][] = $item['text'];
                    } else {
                        $sanitized[$section][] = 'Data formatting error - please check source data';
                    }
                }
            }
        }

        $sanitized['generated_at'] = $insights['generated_at'] ?? Carbon::now()->toISOString();
        $sanitized['ai_confidence'] = $insights['ai_confidence'] ?? 'medium';

        return $sanitized;
    }

    /**
     * Process analytics data for report display with proper type checking
     */
    private function processAnalyticsForReport(array $data): array
    {
        // Default categories configuration
        $defaultCategories = [
            'seeds' => ['requests' => 0, 'total_items' => 0],
            'seedlings' => ['requests' => 0, 'total_items' => 0],
            'fruits' => ['requests' => 0, 'total_items' => 0],
            'ornamentals' => ['requests' => 0, 'total_items' => 0],
            'fingerlings' => ['requests' => 0, 'total_items' => 0],
            'fertilizers' => ['requests' => 0, 'total_items' => 0]
        ];

        // Merge provided category analysis with defaults
        $categoryAnalysis = array_merge(
            $defaultCategories,
            is_array($data['categoryAnalysis'] ?? []) ? $data['categoryAnalysis'] : []
        );

        return [
            'top_barangays' => $this->sanitizeBarangayData($data['barangayAnalysis'] ?? []),
            'bottom_barangays' => $this->sanitizeBarangayData(
                is_array($data['barangayAnalysis'] ?? [])
                    ? array_slice($data['barangayAnalysis'], -5)
                    : ($data['barangayAnalysis'] ?? collect())->slice(-5)->toArray()
            ),
            'top_items' => $this->sanitizeItemData($data['topItems'] ?? []),
            'least_items' => $this->sanitizeItemData($data['leastRequestedItems'] ?? []),
            'monthly_trends' => is_array($data['monthlyTrends'] ?? []) ? $data['monthlyTrends'] : [],
            'category_analysis' => $categoryAnalysis,
            'status_analysis' => is_array($data['statusAnalysis'] ?? []) ? $data['statusAnalysis'] : [],
            'processing_time' => is_array($data['processingTimeAnalysis'] ?? []) ? $data['processingTimeAnalysis'] : []
        ];
    }

    /**
     * Sanitize barangay data to ensure consistent structure
     */
    private function sanitizeBarangayData($barangayData): Collection
    {
        if (is_object($barangayData) && method_exists($barangayData, 'take')) {
            return $barangayData->take(10)->map(function ($item) {
                return (object) [
                    'barangay' => $item->barangay ?? 'Unknown',
                    'total_requests' => (int) ($item->total_requests ?? 0),
                    'approved' => (int) ($item->approved ?? 0),
                    'rejected' => (int) ($item->rejected ?? 0),
                    'pending' => (int) ($item->pending ?? 0),
                    'total_quantity' => (int) ($item->total_quantity ?? 0),
                    'approval_rate' => (float) ($item->approval_rate ?? 0),
                ];
            });
        }

        if (is_array($barangayData)) {
            return collect($barangayData)->take(10)->map(function ($item) {
                $item = is_object($item) ? $item : (object) $item;
                return (object) [
                    'barangay' => $item->barangay ?? 'Unknown',
                    'total_requests' => (int) ($item->total_requests ?? 0),
                    'approved' => (int) ($item->approved ?? 0),
                    'rejected' => (int) ($item->rejected ?? 0),
                    'pending' => (int) ($item->pending ?? 0),
                    'total_quantity' => (int) ($item->total_quantity ?? 0),
                    'approval_rate' => (float) ($item->approval_rate ?? 0),
                ];
            });
        }

        return collect([]);
    }

    /**
     * Sanitize item data
     */
    private function sanitizeItemData($itemData): array
    {
        if (!is_array($itemData)) {
            return [];
        }

        $validCategories = ['seeds', 'seedlings', 'fruits', 'ornamentals', 'fingerlings', 'fertilizers'];

        return array_map(function ($item) use ($validCategories) {
            $category = is_string($item['category'] ?? '') ? strtolower($item['category']) : 'uncategorized';
            
            // Ensure category is valid
            if (!in_array($category, $validCategories)) {
                $category = 'uncategorized';
            }

            return [
                'name' => is_string($item['name'] ?? '') ? $item['name'] : 'Unknown Item',
                'category' => $category,
                'total_quantity' => (int) ($item['total_quantity'] ?? 0),
                'request_count' => (int) ($item['request_count'] ?? 0),
            ];
        }, array_slice($itemData, 0, 10));
    }

    /**
     * Generate performance summary metrics with proper type checking
     */
    private function generatePerformanceSummary(array $data): array
    {
        $overview = $data['overview'] ?? [];
        $barangayCount = is_countable($data['barangayAnalysis'] ?? [])
            ? count($data['barangayAnalysis'])
            : (is_object($data['barangayAnalysis'] ?? null) && method_exists($data['barangayAnalysis'], 'count')
                ? $data['barangayAnalysis']->count()
                : 0);

        $avgProcessingDays = (float) ($data['processingTimeAnalysis']['avg_processing_days'] ?? 0);
        $approvalRate = (float) ($overview['approval_rate'] ?? 0);

        return [
            'overall_grade' => $this->calculateOverallGrade($overview),
            'efficiency_score' => $this->calculateEfficiencyScore($data),
            'distribution_score' => $this->calculateDistributionScore($data),
            'satisfaction_indicator' => $this->calculateSatisfactionIndicator($overview),
            'performance_indicators' => [
                'approval_rate' => [
                    'value' => $approvalRate,
                    'status' => $this->getPerformanceStatus($approvalRate, [70, 85]),
                    'target' => 85
                ],
                'processing_time' => [
                    'value' => $avgProcessingDays,
                    'status' => $this->getProcessingTimeStatus($avgProcessingDays),
                    'target' => 3
                ],
                'coverage' => [
                    'value' => $barangayCount,
                    'status' => $barangayCount >= 10 ? 'excellent' : ($barangayCount >= 5 ? 'good' : 'needs_improvement'),
                    'target' => 15
                ]
            ]
        ];
    }

    /**
     * Calculate overall performance grade
     */
    private function calculateOverallGrade(array $overview): string
    {
        $approvalRate = (float) ($overview['approval_rate'] ?? 0);

        if ($approvalRate >= 90) return 'A+';
        if ($approvalRate >= 85) return 'A';
        if ($approvalRate >= 80) return 'B+';
        if ($approvalRate >= 75) return 'B';
        if ($approvalRate >= 70) return 'C+';
        if ($approvalRate >= 65) return 'C';
        return 'D';
    }

    /**
     * Calculate efficiency score
     */
    private function calculateEfficiencyScore(array $data): int
    {
        $processingTime = (float) ($data['processingTimeAnalysis']['avg_processing_days'] ?? 7);
        $approvalRate = (float) ($data['overview']['approval_rate'] ?? 0);

        $timeScore = max(0, 100 - ($processingTime * 10));
        $approvalScore = $approvalRate;

        return (int) (($timeScore + $approvalScore) / 2);
    }

    /**
     * Calculate distribution score
     */
    private function calculateDistributionScore(array $data): int
    {
        $barangayCount = is_countable($data['barangayAnalysis'] ?? [])
            ? count($data['barangayAnalysis'])
            : (is_object($data['barangayAnalysis'] ?? null) && method_exists($data['barangayAnalysis'], 'count')
                ? $data['barangayAnalysis']->count()
                : 0);

        $totalFarmers = (int) ($data['overview']['unique_applicants'] ?? 0);

        $coverageScore = min(100, ($barangayCount / 20) * 100);
        $reachScore = min(100, ($totalFarmers / 500) * 100);

        return (int) (($coverageScore + $reachScore) / 2);
    }

    /**
     * Calculate satisfaction indicator
     */
    private function calculateSatisfactionIndicator(array $overview): string
    {
        $approvalRate = (float) ($overview['approval_rate'] ?? 0);

        if ($approvalRate >= 85) return 'High';
        if ($approvalRate >= 70) return 'Medium';
        return 'Low';
    }

    /**
     * Get performance status based on thresholds
     */
    private function getPerformanceStatus(float $value, array $thresholds): string
    {
        if ($value >= $thresholds[1]) return 'excellent';
        if ($value >= $thresholds[0]) return 'good';
        return 'needs_improvement';
    }

    /**
     * Get processing time status
     */
    private function getProcessingTimeStatus(float $days): string
    {
        if ($days <= 2) return 'excellent';
        if ($days <= 5) return 'good';
        return 'needs_improvement';
    }

    /**
     * Prepare chart data for static display in PDF
     */
    private function prepareChartsData(array $data): array
    {
        $categoryAnalysis = is_array($data['categoryAnalysis'] ?? []) ? $data['categoryAnalysis'] : [];
        
        // Ensure all categories are represented
        $defaultCategories = [
            'seeds' => ['requests' => 0, 'total_items' => 0],
            'seedlings' => ['requests' => 0, 'total_items' => 0],
            'fruits' => ['requests' => 0, 'total_items' => 0],
            'ornamentals' => ['requests' => 0, 'total_items' => 0],
            'fingerlings' => ['requests' => 0, 'total_items' => 0],
            'fertilizers' => ['requests' => 0, 'total_items' => 0]
        ];

        $categoryDistribution = array_merge($defaultCategories, $categoryAnalysis);

        return [
            'approval_rate_chart' => [
                'approved' => (int) ($data['overview']['approved_requests'] ?? 0),
                'rejected' => (int) (($data['overview']['total_requests'] ?? 0) - ($data['overview']['approved_requests'] ?? 0)),
                'approval_percentage' => (float) ($data['overview']['approval_rate'] ?? 0)
            ],
            'category_distribution' => $categoryDistribution,
            'monthly_performance' => is_array($data['monthlyTrends'] ?? []) ? $data['monthlyTrends'] : []
        ];
    }

    /**
     * Format report period
     */
    private function formatReportPeriod(string $startDate = null, string $endDate = null): string
    {
        if (!$startDate || !$endDate) {
            return 'All Available Data';
        }

        $start = Carbon::parse($startDate)->format('M j, Y');
        $end = Carbon::parse($endDate)->format('M j, Y');

        return "{$start} - {$end}";
    }
}
