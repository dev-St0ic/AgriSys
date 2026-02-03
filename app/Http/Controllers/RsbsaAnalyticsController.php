<?php

namespace App\Http\Controllers;

use App\Models\RsbsaApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RsbsaAnalyticsController extends Controller
{
    /**
     * Display RSBSA analytics dashboard
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
            $baseQuery = RsbsaApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            // 1. Overview Statistics
            $overview = $this->getOverviewStatistics(clone $baseQuery);

            // 2. Application Status Analysis
            $statusAnalysis = $this->getStatusAnalysis(clone $baseQuery);

            // 3. Monthly Trends
            $monthlyTrends = $this->getMonthlyTrends($startDate, $endDate);

            // 4. Barangay Analysis
            $barangayAnalysis = $this->getBarangayAnalysis(clone $baseQuery);

            // 5. Livelihood Analysis
            $livelihoodAnalysis = $this->getLivelihoodAnalysis(clone $baseQuery);

            // 6. Gender Distribution Analysis
            $genderAnalysis = $this->getGenderAnalysis(clone $baseQuery);

            // 7. Application Processing Time Analysis
            $processingTimeAnalysis = $this->getProcessingTimeAnalysis(clone $baseQuery);

            // 8. Registration Patterns
            $registrationPatterns = $this->getRegistrationPatterns(clone $baseQuery);

            // 9. Document Submission Analysis
            $documentAnalysis = $this->getDocumentAnalysis(clone $baseQuery);

            // 10. Monthly Performance Metrics
            $performanceMetrics = $this->getPerformanceMetrics(clone $baseQuery);

            // 11. Commodity Analysis (specific to RSBSA)
            $commodityAnalysis = $this->getCommodityAnalysis(clone $baseQuery);

            // 12. Land Area Analysis (specific to RSBSA)
            $landAreaAnalysis = $this->getLandAreaAnalysis(clone $baseQuery);

            return view('admin.analytics.rsbsa', compact(
                'overview',
                'statusAnalysis',
                'monthlyTrends',
                'barangayAnalysis',
                'livelihoodAnalysis',
                'genderAnalysis',
                'processingTimeAnalysis',
                'registrationPatterns',
                'documentAnalysis',
                'performanceMetrics',
                'commodityAnalysis',
                'landAreaAnalysis',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('RSBSA Analytics Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading RSBSA analytics data. Please try again.');
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
            $pending = (clone $baseQuery)->whereIn('status', ['pending', 'under_review'])->count();

            // Count unique applicants safely
            $uniqueApplicants = (clone $baseQuery)
                ->select(DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
                ->distinct()
                ->whereNotNull('first_name')
                ->count();

            // Count active barangays
            $activeBarangays = (clone $baseQuery)->whereNotNull('barangay')->distinct()->count('barangay');

            // Count applications with documents
            $withDocuments = (clone $baseQuery)->whereNotNull('supporting_document_path')->where('supporting_document_path', '!=', '')->count();

            // Count unique commodities
            $uniqueCommodities = (clone $baseQuery)->whereNotNull('commodity')->distinct()->count('commodity');

            // Total land area
            $totalLandArea = (clone $baseQuery)->whereNotNull('farmer_land_area')->sum('farmer_land_area');

            // Average land area
            $avgLandArea = (clone $baseQuery)->whereNotNull('farmer_land_area')->avg('farmer_land_area');

            return [
                'total_applications' => $total,
                'approved_applications' => $approved,
                'rejected_applications' => $rejected,
                'pending_applications' => $pending,
                'approval_rate' => $this->calculateApprovalRate($total, $approved),
                'unique_applicants' => $uniqueApplicants,
                'active_barangays' => $activeBarangays,
                'with_documents' => $withDocuments,
                'document_submission_rate' => $total > 0 ? round(($withDocuments / $total) * 100, 2) : 0,
                'unique_commodities' => $uniqueCommodities,
                'total_land_area' => round($totalLandArea, 2),
                'avg_land_area' => round($avgLandArea, 2)
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Overview Statistics Error: ' . $e->getMessage());
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
            $defaultStatuses = ['approved' => 0, 'rejected' => 0, 'pending' => 0, 'under_review' => 0];
            $statusCounts = array_merge($defaultStatuses, $statusCounts);

            // Combine pending and under_review for display
            $displayStatusCounts = [
                'approved' => $statusCounts['approved'],
                'rejected' => $statusCounts['rejected'],
                'pending' => $statusCounts['pending'] + $statusCounts['under_review']
            ];

            // Add percentage calculations
            $total = array_sum($displayStatusCounts);
            $statusPercentages = [];
            foreach ($displayStatusCounts as $status => $count) {
                $statusPercentages[$status] = $total > 0 ? round(($count / $total) * 100, 2) : 0;
            }

            return [
                'counts' => $displayStatusCounts,
                'percentages' => $statusPercentages,
                'total' => $total
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Status Analysis Error: ' . $e->getMessage());
            return [
                'counts' => ['approved' => 0, 'rejected' => 0, 'pending' => 0],
                'percentages' => ['approved' => 0, 'rejected' => 0, 'pending' => 0],
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
            $trends = RsbsaApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status IN ("pending", "under_review") THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(CASE WHEN supporting_document_path IS NOT NULL AND supporting_document_path != "" THEN 1 ELSE 0 END) as with_documents'),
                    DB::raw('COUNT(DISTINCT barangay) as unique_barangays'),
                    DB::raw('SUM(COALESCE(farmer_land_area, 0)) as total_land_area'),
                    DB::raw('COUNT(DISTINCT commodity) as unique_commodities')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return $trends;
        } catch (\Exception $e) {
            Log::error('RSBSA Monthly Trends Error: ' . $e->getMessage());
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
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status IN ("pending", "under_review") THEN 1 ELSE 0 END) as pending'),
                    DB::raw('COUNT(DISTINCT CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, ""))) as unique_applicants'),
                    DB::raw('SUM(CASE WHEN supporting_document_path IS NOT NULL AND supporting_document_path != "" THEN 1 ELSE 0 END) as with_documents'),
                    DB::raw('SUM(COALESCE(farmer_land_area, 0)) as total_land_area'),
                    DB::raw('COUNT(DISTINCT commodity) as commodities_grown')
                )
                ->whereNotNull('barangay')
                ->where('barangay', '!=', '')
                ->groupBy('barangay')
                ->orderBy('total_applications', 'desc')
                ->get();

            return $barangayStats;
        } catch (\Exception $e) {
            Log::error('RSBSA Barangay Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get livelihood analysis
     */
    private function getLivelihoodAnalysis($baseQuery)
    {
        try {
            $livelihoodStats = (clone $baseQuery)->select(
                    'main_livelihood',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('COUNT(DISTINCT barangay) as barangays_served'),
                    DB::raw('SUM(COALESCE(farmer_land_area, 0)) as total_land_area'),
                    DB::raw('AVG(COALESCE(farmer_land_area, 0)) as avg_land_area'),
                    DB::raw('COUNT(DISTINCT commodity) as commodities_grown'),
                    DB::raw('AVG(CASE WHEN approved_at IS NOT NULL AND created_at IS NOT NULL
                             THEN DATEDIFF(approved_at, created_at) END) as avg_processing_days')
                )
                ->whereNotNull('main_livelihood')
                ->where('main_livelihood', '!=', '')
                ->groupBy('main_livelihood')
                ->orderBy('total_applications', 'desc')
                ->get();

            return $livelihoodStats;
        } catch (\Exception $e) {
            Log::error('RSBSA Livelihood Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get gender distribution analysis
     */
    private function getGenderAnalysis($baseQuery)
    {
        try {
            $genderStats = (clone $baseQuery)->select(
                    'sex',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('COUNT(DISTINCT barangay) as barangays_represented'),
                    DB::raw('SUM(COALESCE(farmer_land_area, 0)) as total_land_area'),
                    DB::raw('AVG(COALESCE(farmer_land_area, 0)) as avg_land_area')
                )
                ->whereNotNull('sex')
                ->where('sex', '!=', '')
                ->groupBy('sex')
                ->get();

            $total = $genderStats->sum('total_applications');

            $genderPercentages = [];
            foreach ($genderStats as $stat) {
                $genderPercentages[$stat->sex] = $total > 0 ? round(($stat->total_applications / $total) * 100, 2) : 0;
            }

            return [
                'stats' => $genderStats,
                'percentages' => $genderPercentages,
                'total' => $total
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Gender Analysis Error: ' . $e->getMessage());
            return [
                'stats' => collect([]),
                'percentages' => [],
                'total' => 0
            ];
        }
    }

    /**
     * Get processing time analysis
     */
    private function getProcessingTimeAnalysis($baseQuery)
    {
        try {
            $processedApplications = (clone $baseQuery)->whereNotNull('approved_at')->orWhereNotNull('rejected_at')->get();

            $processingTimes = [];
            foreach ($processedApplications as $application) {
                $processedAt = $application->approved_at ?? $application->rejected_at;
                if ($processedAt && $application->created_at) {
                    $processingTime = Carbon::parse($application->created_at)
                        ->diffInDays(Carbon::parse($processedAt));
                    $processingTimes[] = $processingTime;
                }
            }

            if (empty($processingTimes)) {
                return [
                    'avg_processing_days' => 0,
                    'min_processing_days' => 0,
                    'max_processing_days' => 0,
                    'processed_count' => 0,
                    'median_processing_days' => 0
                ];
            }

            sort($processingTimes);
            $count = count($processingTimes);
            $median = $count % 2 === 0
                ? ($processingTimes[$count/2 - 1] + $processingTimes[$count/2]) / 2
                : $processingTimes[floor($count/2)];

            return [
                'avg_processing_days' => round(array_sum($processingTimes) / count($processingTimes), 2),
                'min_processing_days' => min($processingTimes),
                'max_processing_days' => max($processingTimes),
                'processed_count' => count($processingTimes),
                'median_processing_days' => round($median, 2)
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Processing Time Error: ' . $e->getMessage());
            return [
                'avg_processing_days' => 0,
                'min_processing_days' => 0,
                'max_processing_days' => 0,
                'processed_count' => 0,
                'median_processing_days' => 0
            ];
        }
    }

    /**
     * Get registration patterns (day of week, time patterns)
     */
    private function getRegistrationPatterns($baseQuery)
    {
        try {
            $dayOfWeekStats = (clone $baseQuery)->select(
                    DB::raw('DAYNAME(created_at) as day_name'),
                    DB::raw('DAYOFWEEK(created_at) as day_number'),
                    DB::raw('COUNT(*) as applications_count')
                )
                ->groupBy('day_name', 'day_number')
                ->orderBy('day_number')
                ->get();

            $hourlyStats = (clone $baseQuery)->select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as applications_count')
                )
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            return [
                'day_of_week' => $dayOfWeekStats,
                'hourly' => $hourlyStats
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Registration Patterns Error: ' . $e->getMessage());
            return [
                'day_of_week' => collect([]),
                'hourly' => collect([])
            ];
        }
    }

    /**
     * Get document submission analysis
     */
    private function getDocumentAnalysis($baseQuery)
    {
        try {
            $withDocs = (clone $baseQuery)->whereNotNull('supporting_document_path')->where('supporting_document_path', '!=', '')->count();
            $withoutDocs = (clone $baseQuery)->where(function($q) {
                $q->whereNull('supporting_document_path')->orWhere('supporting_document_path', '');
            })->count();

            $total = $withDocs + $withoutDocs;

            // Approval rates by document submission
            $approvalWithDocs = (clone $baseQuery)
                ->whereNotNull('supporting_document_path')
                ->where('supporting_document_path', '!=', '')
                ->where('status', 'approved')
                ->count();

            $approvalWithoutDocs = (clone $baseQuery)
                ->where(function($q) {
                    $q->whereNull('supporting_document_path')->orWhere('supporting_document_path', '');
                })
                ->where('status', 'approved')
                ->count();

            return [
                'with_documents' => $withDocs,
                'without_documents' => $withoutDocs,
                'total' => $total,
                'submission_rate' => $total > 0 ? round(($withDocs / $total) * 100, 2) : 0,
                'approval_rate_with_docs' => $withDocs > 0 ? round(($approvalWithDocs / $withDocs) * 100, 2) : 0,
                'approval_rate_without_docs' => $withoutDocs > 0 ? round(($approvalWithoutDocs / $withoutDocs) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Document Analysis Error: ' . $e->getMessage());
            return [
                'with_documents' => 0,
                'without_documents' => 0,
                'total' => 0,
                'submission_rate' => 0,
                'approval_rate_with_docs' => 0,
                'approval_rate_without_docs' => 0
            ];
        }
    }

    /**
     * Get commodity analysis (specific to RSBSA)
     */
    private function getCommodityAnalysis($baseQuery)
    {
        try {
            $commodityStats = (clone $baseQuery)->select(
                    'commodity',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('COUNT(DISTINCT barangay) as unique_barangays'),
                    DB::raw('SUM(COALESCE(farmer_land_area, 0)) as total_land_area'),
                    DB::raw('AVG(COALESCE(farmer_land_area, 0)) as avg_land_area'),
                    DB::raw('COUNT(DISTINCT CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, ""))) as unique_farmers')
                )
                ->whereNotNull('commodity')
                ->where('commodity', '!=', '')
                ->groupBy('commodity')
                ->orderBy('total_applications', 'desc')
                ->get();

            return $commodityStats;
        } catch (\Exception $e) {
            Log::error('RSBSA Commodity Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get land area analysis (specific to RSBSA)
     */
    private function getLandAreaAnalysis($baseQuery)
    {
        try {
            // Land area distribution ranges
            $landAreaRanges = (clone $baseQuery)
                ->select(
                    DB::raw('CASE
                        WHEN farmer_land_area <= 1 THEN "0-1 hectares"
                        WHEN farmer_land_area <= 3 THEN "1-3 hectares"
                        WHEN farmer_land_area <= 5 THEN "3-5 hectares"
                        WHEN farmer_land_area <= 10 THEN "5-10 hectares"
                        ELSE "10+ hectares"
                        END as land_range'),
                    DB::raw('COUNT(*) as farmer_count'),
                    DB::raw('SUM(farmer_land_area) as total_area'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved')
                )
                ->whereNotNull('farmer_land_area')
                ->where('farmer_land_area', '>', 0)
                ->groupBy('land_range')
                ->orderBy(DB::raw('MIN(farmer_land_area)'))
                ->get();

            // Top farmers by land area
            $topFarmers = (clone $baseQuery)
                ->select(
                    DB::raw('CONCAT(first_name, " ", COALESCE(middle_name, ""), " ", last_name) as full_name'),
                    'barangay',
                    'commodity',
                    'farmer_land_area as land_area',
                    'status'
                )
                ->whereNotNull('farmer_land_area')
                ->where('farmer_land_area', '>', 0)
                ->orderBy('farmer_land_area', 'desc')
                ->limit(10)
                ->get();

            return [
                'land_ranges' => $landAreaRanges,
                'top_farmers' => $topFarmers
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Land Area Analysis Error: ' . $e->getMessage());
            return [
                'land_ranges' => collect([]),
                'top_farmers' => collect([])
            ];
        }
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics($baseQuery)
    {
        try {
            $metrics = [];

            // Overall completion rate
            $total = (clone $baseQuery)->count();
            $completed = (clone $baseQuery)->whereIn('status', ['approved', 'rejected'])->count();
            $metrics['completion_rate'] = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

            // Average applications per day
            $dateRange = (clone $baseQuery)->selectRaw('DATEDIFF(MAX(created_at), MIN(created_at)) + 1 as days')->first();
            $totalDays = $dateRange ? $dateRange->days : 1;
            $metrics['avg_applications_per_day'] = round($total / max(1, $totalDays), 2);

            // Quality score (based on document submission and approval rates)
            $overview = $this->getOverviewStatistics(clone $baseQuery);
            $docAnalysis = $this->getDocumentAnalysis(clone $baseQuery);
            $metrics['quality_score'] = round(
                ($overview['approval_rate'] * 0.6) +
                ($docAnalysis['submission_rate'] * 0.4), 2
            );

            // Total agricultural impact (land area covered)
            $metrics['total_land_impact'] = $overview['total_land_area'];

            return $metrics;
        } catch (\Exception $e) {
            Log::error('RSBSA Performance Metrics Error: ' . $e->getMessage());
            return [
                'completion_rate' => 0,
                'avg_applications_per_day' => 0,
                'quality_score' => 0,
                'total_land_impact' => 0
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
            'total_applications' => 0,
            'approved_applications' => 0,
            'rejected_applications' => 0,
            'pending_applications' => 0,
            'approval_rate' => 0,
            'unique_applicants' => 0,
            'active_barangays' => 0,
            'with_documents' => 0,
            'document_submission_rate' => 0,
            'unique_commodities' => 0,
            'total_land_area' => 0,
            'avg_land_area' => 0
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

            $baseQuery = RsbsaApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

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
                'livelihood_analysis' => $this->getLivelihoodAnalysis(clone $baseQuery)->toArray(),
                'gender_analysis' => $this->getGenderAnalysis(clone $baseQuery),
                'processing_time' => $this->getProcessingTimeAnalysis(clone $baseQuery),
                'registration_patterns' => $this->getRegistrationPatterns(clone $baseQuery),
                'document_analysis' => $this->getDocumentAnalysis(clone $baseQuery),
                'performance_metrics' => $this->getPerformanceMetrics(clone $baseQuery),
                'commodity_analysis' => $this->getCommodityAnalysis(clone $baseQuery)->toArray(),
                'land_area_analysis' => $this->getLandAreaAnalysis(clone $baseQuery)
            ];

            $filename = 'rsbsa-analytics-' . $startDate . '-to-' . $endDate . '.json';

            // Log activity
            $this->logActivity('exported', 'RsbsaApplication', null, [
                'format' => 'JSON',
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ], 'Exported RSBSA analytics data from ' . $startDate . ' to ' . $endDate);

            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('RSBSA Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting RSBSA data: ' . $e->getMessage());
        }
    }
}
