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

            // 13. Farmer Details (type of farm, land ownership, special status)
            $farmerDetails = $this->getFarmerDetails(clone $baseQuery);

            // 14. Farmer Crops breakdown
            $farmerCrops = $this->getFarmerCrops(clone $baseQuery);

            // 15. Farmworker Type breakdown
            $farmworkerType = $this->getFarmworkerType(clone $baseQuery);

            // 16. Fisherfolk Activity breakdown
            $fisherfolkActivity = $this->getFisherfolkActivity(clone $baseQuery);

            // 17. Agri-youth Analysis
            $agriyouthAnalysis = $this->getAgriyouthAnalysis(clone $baseQuery);

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
                'farmerDetails',
                'farmerCrops',
                'farmworkerType',
                'fisherfolkActivity',
                'agriyouthAnalysis',
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
     * Get farmer details (type of farm, land ownership, special status)
     */
    private function getFarmerDetails($baseQuery)
    {
        try {
            $farmerQuery = (clone $baseQuery)->where('main_livelihood', 'Farmer');

            $byTypeOfFarm = (clone $farmerQuery)
                ->select('farmer_type_of_farm', DB::raw('COUNT(*) as count'))
                ->whereNotNull('farmer_type_of_farm')->where('farmer_type_of_farm', '!=', '')
                ->groupBy('farmer_type_of_farm')->orderByDesc('count')->get();

            $byLandOwnership = (clone $farmerQuery)
                ->select('farmer_land_ownership', DB::raw('COUNT(*) as count'))
                ->whereNotNull('farmer_land_ownership')->where('farmer_land_ownership', '!=', '')
                ->groupBy('farmer_land_ownership')->orderByDesc('count')->get();

            $bySpecialStatus = (clone $farmerQuery)
                ->select('farmer_special_status', DB::raw('COUNT(*) as count'))
                ->whereNotNull('farmer_special_status')->where('farmer_special_status', '!=', '')
                ->groupBy('farmer_special_status')->orderByDesc('count')->get();

            return [
                'total_farmers' => (clone $farmerQuery)->count(),
                'by_type_of_farm' => $byTypeOfFarm,
                'by_land_ownership' => $byLandOwnership,
                'by_special_status' => $bySpecialStatus,
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Farmer Details Error: ' . $e->getMessage());
            return ['total_farmers' => 0, 'by_type_of_farm' => collect([]), 'by_land_ownership' => collect([]), 'by_special_status' => collect([])];
        }
    }

    /**
     * Get farmer crops breakdown
     */
    private function getFarmerCrops($baseQuery)
    {
        try {
            $byCrop = (clone $baseQuery)
                ->where('main_livelihood', 'Farmer')
                ->select('farmer_crops', DB::raw('COUNT(*) as count, SUM(COALESCE(farmer_land_area,0)) as total_land, SUM(CASE WHEN status="approved" THEN 1 ELSE 0 END) as approved'))
                ->whereNotNull('farmer_crops')->where('farmer_crops', '!=', '')
                ->groupBy('farmer_crops')->orderByDesc('count')->get();

            $otherCrops = (clone $baseQuery)
                ->where('farmer_crops', 'Other Crops')
                ->select('farmer_other_crops', DB::raw('COUNT(*) as count'))
                ->whereNotNull('farmer_other_crops')->where('farmer_other_crops', '!=', '')
                ->groupBy('farmer_other_crops')->orderByDesc('count')->get();

            return [
                'distribution' => $byCrop,
                'other_crops_specified' => $otherCrops,
                'total_crop_types' => $byCrop->count(),
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Farmer Crops Error: ' . $e->getMessage());
            return ['distribution' => collect([]), 'other_crops_specified' => collect([]), 'total_crop_types' => 0];
        }
    }

    /**
     * Get farmworker type breakdown
     */
    private function getFarmworkerType($baseQuery)
    {
        try {
            $byType = (clone $baseQuery)
                ->where('main_livelihood', 'Farmworker/Laborer')
                ->select('farmworker_type', DB::raw('COUNT(*) as count, SUM(CASE WHEN status="approved" THEN 1 ELSE 0 END) as approved'))
                ->whereNotNull('farmworker_type')->where('farmworker_type', '!=', '')
                ->groupBy('farmworker_type')->orderByDesc('count')->get();

            $otherTypes = (clone $baseQuery)
                ->where('farmworker_type', 'Others')
                ->select('farmworker_other_type', DB::raw('COUNT(*) as count'))
                ->whereNotNull('farmworker_other_type')->where('farmworker_other_type', '!=', '')
                ->groupBy('farmworker_other_type')->orderByDesc('count')->get();

            return [
                'total_farmworkers' => (clone $baseQuery)->where('main_livelihood', 'Farmworker/Laborer')->count(),
                'distribution' => $byType,
                'other_types_specified' => $otherTypes,
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Farmworker Type Error: ' . $e->getMessage());
            return ['total_farmworkers' => 0, 'distribution' => collect([]), 'other_types_specified' => collect([])];
        }
    }

    /**
     * Get fisherfolk activity breakdown
     */
    private function getFisherfolkActivity($baseQuery)
    {
        try {
            $byActivity = (clone $baseQuery)
                ->where('main_livelihood', 'Fisherfolk')
                ->select('fisherfolk_activity', DB::raw('COUNT(*) as count, SUM(CASE WHEN status="approved" THEN 1 ELSE 0 END) as approved'))
                ->whereNotNull('fisherfolk_activity')->where('fisherfolk_activity', '!=', '')
                ->groupBy('fisherfolk_activity')->orderByDesc('count')->get();

            $otherActivities = (clone $baseQuery)
                ->where('fisherfolk_activity', 'Others')
                ->select('fisherfolk_other_activity', DB::raw('COUNT(*) as count'))
                ->whereNotNull('fisherfolk_other_activity')->where('fisherfolk_other_activity', '!=', '')
                ->groupBy('fisherfolk_other_activity')->orderByDesc('count')->get();

            return [
                'total_fisherfolk' => (clone $baseQuery)->where('main_livelihood', 'Fisherfolk')->count(),
                'distribution' => $byActivity,
                'other_activities_specified' => $otherActivities,
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Fisherfolk Activity Error: ' . $e->getMessage());
            return ['total_fisherfolk' => 0, 'distribution' => collect([]), 'other_activities_specified' => collect([])];
        }
    }

    /**
     * Get agri-youth analysis
     */
    private function getAgriyouthAnalysis($baseQuery)
    {
        try {
            $ayQuery = (clone $baseQuery)->where('main_livelihood', 'Agri-youth');
            $total = (clone $ayQuery)->count();

            $demographics = (clone $ayQuery)
                ->selectRaw('SUM(CASE WHEN sex="Male" THEN 1 ELSE 0 END) as male_count, SUM(CASE WHEN sex="Female" THEN 1 ELSE 0 END) as female_count, SUM(CASE WHEN status="approved" THEN 1 ELSE 0 END) as approved')
                ->first();

            $byFarmingHousehold = (clone $ayQuery)
                ->select('agriyouth_farming_household', DB::raw('COUNT(*) as count'))
                ->whereNotNull('agriyouth_farming_household')->where('agriyouth_farming_household', '!=', '')
                ->groupBy('agriyouth_farming_household')->orderByDesc('count')->get();

            $byTraining = (clone $ayQuery)
                ->select('agriyouth_training', DB::raw('COUNT(*) as count'))
                ->whereNotNull('agriyouth_training')->where('agriyouth_training', '!=', '')
                ->groupBy('agriyouth_training')->orderByDesc('count')->get();

            $byParticipation = (clone $ayQuery)
                ->select('agriyouth_participation', DB::raw('COUNT(*) as count'))
                ->whereNotNull('agriyouth_participation')->where('agriyouth_participation', '!=', '')
                ->groupBy('agriyouth_participation')->orderByDesc('count')->get();

            return [
                'total' => $total,
                'male_count' => $demographics->male_count ?? 0,
                'female_count' => $demographics->female_count ?? 0,
                'approved' => $demographics->approved ?? 0,
                'approval_rate' => $total > 0 ? round(($demographics->approved / $total) * 100, 2) : 0,
                'by_farming_household' => $byFarmingHousehold,
                'by_training' => $byTraining,
                'by_participation' => $byParticipation,
            ];
        } catch (\Exception $e) {
            Log::error('RSBSA Agri-youth Analysis Error: ' . $e->getMessage());
            return ['total' => 0, 'male_count' => 0, 'female_count' => 0, 'approved' => 0, 'approval_rate' => 0, 'by_farming_household' => collect([]), 'by_training' => collect([]), 'by_participation' => collect([])];
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
            $endDate   = $request->get('end_date', now()->format('Y-m-d'));

            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate   = Carbon::parse($endDate)->format('Y-m-d');

            $baseQuery = RsbsaApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            $filename = 'rsbsa-analytics-' . $startDate . '-to-' . $endDate . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            $overview           = $this->getOverviewStatistics(clone $baseQuery);
            $statusAnalysis     = $this->getStatusAnalysis(clone $baseQuery);
            $monthlyTrends      = $this->getMonthlyTrends($startDate, $endDate);
            $barangayAnalysis   = $this->getBarangayAnalysis(clone $baseQuery);
            $livelihoodAnalysis = $this->getLivelihoodAnalysis(clone $baseQuery);
            $genderAnalysis     = $this->getGenderAnalysis(clone $baseQuery);
            $processingTime     = $this->getProcessingTimeAnalysis(clone $baseQuery);
            $documentAnalysis   = $this->getDocumentAnalysis(clone $baseQuery);
            $performanceMetrics = $this->getPerformanceMetrics(clone $baseQuery);
            $commodityAnalysis  = $this->getCommodityAnalysis(clone $baseQuery);
            $landAreaAnalysis   = $this->getLandAreaAnalysis(clone $baseQuery);
            $farmerDetails      = $this->getFarmerDetails(clone $baseQuery);
            $farmerCrops        = $this->getFarmerCrops(clone $baseQuery);
            $farmworkerType     = $this->getFarmworkerType(clone $baseQuery);
            $fisherfolkActivity = $this->getFisherfolkActivity(clone $baseQuery);
            $agriyouthAnalysis  = $this->getAgriyouthAnalysis(clone $baseQuery);

            $callback = function () use (
                $startDate, $endDate, $overview, $statusAnalysis, $monthlyTrends,
                $barangayAnalysis, $livelihoodAnalysis, $genderAnalysis,
                $processingTime, $documentAnalysis, $performanceMetrics,
                $commodityAnalysis, $landAreaAnalysis, $farmerDetails, $farmerCrops,
                $farmworkerType, $fisherfolkActivity, $agriyouthAnalysis
            ) {
                $file = fopen('php://output', 'w');

                // ── Export Info ───────────────────────────────────────
                fputcsv($file, ['RSBSA ANALYTICS EXPORT']);
                fputcsv($file, ['Generated At', now()->format('Y-m-d H:i:s')]);
                fputcsv($file, ['Date Range', $startDate . ' to ' . $endDate]);
                fputcsv($file, ['Generated By', auth()->user()->name ?? 'System']);
                fputcsv($file, []);

                // ── Overview ──────────────────────────────────────────
                fputcsv($file, ['OVERVIEW']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Total Applications',         $overview['total_applications']]);
                fputcsv($file, ['Approved Applications',      $overview['approved_applications']]);
                fputcsv($file, ['Rejected Applications',      $overview['rejected_applications']]);
                fputcsv($file, ['Pending Applications',       $overview['pending_applications']]);
                fputcsv($file, ['Approval Rate',              $overview['approval_rate'] . '%']);
                fputcsv($file, ['Unique Applicants',          $overview['unique_applicants']]);
                fputcsv($file, ['Active Barangays',           $overview['active_barangays']]);
                fputcsv($file, ['With Documents',             $overview['with_documents']]);
                fputcsv($file, ['Document Submission Rate',   $overview['document_submission_rate'] . '%']);
                fputcsv($file, ['Unique Commodities',         $overview['unique_commodities']]);
                fputcsv($file, ['Total Land Area (ha)',       $overview['total_land_area']]);
                fputcsv($file, ['Avg Land Area (ha)',         $overview['avg_land_area']]);
                fputcsv($file, []);

                // ── Status Analysis ───────────────────────────────────
                fputcsv($file, ['STATUS ANALYSIS']);
                fputcsv($file, ['Status', 'Count', 'Percentage']);
                foreach ($statusAnalysis['counts'] as $status => $count) {
                    fputcsv($file, [
                        ucfirst(str_replace('_', ' ', $status)),
                        $count,
                        ($statusAnalysis['percentages'][$status] ?? 0) . '%',
                    ]);
                }
                fputcsv($file, []);

                // ── Monthly Trends ────────────────────────────────────
                fputcsv($file, ['MONTHLY TRENDS']);
                fputcsv($file, ['Month', 'Total Applications', 'Approved', 'Rejected', 'Pending', 'With Documents', 'Unique Barangays', 'Total Land Area (ha)', 'Unique Commodities']);
                foreach ($monthlyTrends as $trend) {
                    fputcsv($file, [
                        $trend->month,
                        $trend->total_applications,
                        $trend->approved,
                        $trend->rejected,
                        $trend->pending,
                        $trend->with_documents,
                        $trend->unique_barangays,
                        $trend->total_land_area,
                        $trend->unique_commodities,
                    ]);
                }
                fputcsv($file, []);

                // ── Barangay Analysis ─────────────────────────────────
                fputcsv($file, ['BARANGAY ANALYSIS']);
                fputcsv($file, ['Barangay', 'Total Applications', 'Approved', 'Rejected', 'Pending', 'Unique Applicants', 'With Documents', 'Total Land Area (ha)', 'Commodities Grown']);
                foreach ($barangayAnalysis as $b) {
                    fputcsv($file, [
                        $b->barangay,
                        $b->total_applications,
                        $b->approved,
                        $b->rejected,
                        $b->pending,
                        $b->unique_applicants,
                        $b->with_documents,
                        $b->total_land_area,
                        $b->commodities_grown,
                    ]);
                }
                fputcsv($file, []);

                // ── Livelihood Analysis ───────────────────────────────
                fputcsv($file, ['LIVELIHOOD ANALYSIS']);
                fputcsv($file, ['Main Livelihood', 'Total Applications', 'Approved', 'Rejected', 'Barangays Served', 'Total Land Area (ha)', 'Avg Land Area (ha)', 'Commodities Grown', 'Avg Processing Days']);
                foreach ($livelihoodAnalysis as $l) {
                    fputcsv($file, [
                        $l->main_livelihood,
                        $l->total_applications,
                        $l->approved,
                        $l->rejected,
                        $l->barangays_served,
                        $l->total_land_area,
                        round($l->avg_land_area ?? 0, 2),
                        $l->commodities_grown,
                        round($l->avg_processing_days ?? 0, 2),
                    ]);
                }
                fputcsv($file, []);

                // ── Gender Analysis ───────────────────────────────────
                fputcsv($file, ['GENDER ANALYSIS']);
                fputcsv($file, ['Gender', 'Total Applications', 'Approved', 'Rejected', 'Barangays Represented', 'Total Land Area (ha)', 'Avg Land Area (ha)', 'Percentage']);
                foreach ($genderAnalysis['stats'] as $g) {
                    fputcsv($file, [
                        ucfirst($g->sex),
                        $g->total_applications,
                        $g->approved,
                        $g->rejected,
                        $g->barangays_represented,
                        $g->total_land_area,
                        round($g->avg_land_area ?? 0, 2),
                        ($genderAnalysis['percentages'][$g->sex] ?? 0) . '%',
                    ]);
                }
                fputcsv($file, []);

                // ── Commodity Analysis ────────────────────────────────
                fputcsv($file, ['COMMODITY ANALYSIS']);
                fputcsv($file, ['Commodity', 'Total Applications', 'Approved', 'Unique Barangays', 'Total Land Area (ha)', 'Avg Land Area (ha)', 'Unique Farmers']);
                foreach ($commodityAnalysis as $c) {
                    fputcsv($file, [
                        $c->commodity,
                        $c->total_applications,
                        $c->approved,
                        $c->unique_barangays,
                        $c->total_land_area,
                        round($c->avg_land_area ?? 0, 2),
                        $c->unique_farmers,
                    ]);
                }
                fputcsv($file, []);

                // ── Land Area Analysis ────────────────────────────────
                fputcsv($file, ['LAND AREA DISTRIBUTION']);
                fputcsv($file, ['Range', 'Farmer Count', 'Total Area (ha)', 'Approved']);
                foreach ($landAreaAnalysis['land_ranges'] as $r) {
                    fputcsv($file, [
                        $r->land_range,
                        $r->farmer_count,
                        $r->total_area,
                        $r->approved,
                    ]);
                }
                fputcsv($file, []);

                fputcsv($file, ['TOP FARMERS BY LAND AREA']);
                fputcsv($file, ['Full Name', 'Barangay', 'Commodity', 'Land Area (ha)', 'Status']);
                foreach ($landAreaAnalysis['top_farmers'] as $f) {
                    fputcsv($file, [
                        $f->full_name,
                        $f->barangay,
                        $f->commodity,
                        $f->land_area,
                        ucfirst($f->status),
                    ]);
                }
                fputcsv($file, []);

                // ── Processing Time ───────────────────────────────────
                fputcsv($file, ['PROCESSING TIME ANALYSIS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Avg Processing Days',    $processingTime['avg_processing_days']]);
                fputcsv($file, ['Min Processing Days',    $processingTime['min_processing_days']]);
                fputcsv($file, ['Max Processing Days',    $processingTime['max_processing_days']]);
                fputcsv($file, ['Median Processing Days', $processingTime['median_processing_days']]);
                fputcsv($file, ['Processed Count',        $processingTime['processed_count']]);
                fputcsv($file, []);

                // ── Document Analysis ─────────────────────────────────
                fputcsv($file, ['DOCUMENT ANALYSIS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['With Documents',             $documentAnalysis['with_documents']]);
                fputcsv($file, ['Without Documents',          $documentAnalysis['without_documents']]);
                fputcsv($file, ['Submission Rate',            $documentAnalysis['submission_rate'] . '%']);
                fputcsv($file, ['Approval Rate With Docs',    $documentAnalysis['approval_rate_with_docs'] . '%']);
                fputcsv($file, ['Approval Rate Without Docs', $documentAnalysis['approval_rate_without_docs'] . '%']);
                fputcsv($file, []);

                // ── Performance Metrics ───────────────────────────────
                fputcsv($file, ['PERFORMANCE METRICS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Completion Rate',          $performanceMetrics['completion_rate'] . '%']);
                fputcsv($file, ['Avg Applications Per Day', $performanceMetrics['avg_applications_per_day']]);
                fputcsv($file, ['Quality Score',            $performanceMetrics['quality_score'] . '%']);
                fputcsv($file, ['Total Land Impact (ha)',   $performanceMetrics['total_land_impact']]);
                fputcsv($file, []);

                // ── Farmer Details ───────────────────────────────────────────────────
                fputcsv($file, ['FARMER DETAILS (Type of Farm, Land Ownership, Special Status)']);
                fputcsv($file, ['Type of Farm', 'Count']);
                foreach ($farmerDetails['by_type_of_farm'] as $item) {
                    fputcsv($file, [$item->farmer_type_of_farm, $item->count]);
                }
                fputcsv($file, []);
                fputcsv($file, ['Land Ownership', 'Count']);
                foreach ($farmerDetails['by_land_ownership'] as $item) {
                    fputcsv($file, [$item->farmer_land_ownership, $item->count]);
                }
                fputcsv($file, []);
                fputcsv($file, ['Special Status', 'Count']);
                foreach ($farmerDetails['by_special_status'] as $item) {
                    fputcsv($file, [$item->farmer_special_status, $item->count]);
                }
                fputcsv($file, []);

                // ── Farmer Crops ─────────────────────────────────────────────────────
                fputcsv($file, ['FARMER CROPS BREAKDOWN']);
                fputcsv($file, ['Crop', 'Count', 'Total Land Area (ha)', 'Approved']);
                foreach ($farmerCrops['distribution'] as $crop) {
                    fputcsv($file, [$crop->farmer_crops, $crop->count, round($crop->total_land, 2), $crop->approved]);
                }
                if ($farmerCrops['other_crops_specified']->isNotEmpty()) {
                    fputcsv($file, []);
                    fputcsv($file, ['Other Crops Specified', 'Count']);
                    foreach ($farmerCrops['other_crops_specified'] as $other) {
                        fputcsv($file, [$other->farmer_other_crops, $other->count]);
                    }
                }
                fputcsv($file, []);

                // ── Farmworker Type ─────────────────────────────────────────────────
                fputcsv($file, ['FARMWORKER TYPE BREAKDOWN']);
                fputcsv($file, ['Total Farmworkers', $farmworkerType['total_farmworkers']]);
                fputcsv($file, ['Type', 'Count', 'Approved']);
                foreach ($farmworkerType['distribution'] as $type) {
                    fputcsv($file, [$type->farmworker_type, $type->count, $type->approved]);
                }
                if ($farmworkerType['other_types_specified']->isNotEmpty()) {
                    fputcsv($file, []);
                    fputcsv($file, ['Other Types Specified', 'Count']);
                    foreach ($farmworkerType['other_types_specified'] as $other) {
                        fputcsv($file, [$other->farmworker_other_type, $other->count]);
                    }
                }
                fputcsv($file, []);

                // ── Fisherfolk Activity ──────────────────────────────────────────────
                fputcsv($file, ['FISHERFOLK ACTIVITY BREAKDOWN']);
                fputcsv($file, ['Total Fisherfolk', $fisherfolkActivity['total_fisherfolk']]);
                fputcsv($file, ['Activity', 'Count', 'Approved']);
                foreach ($fisherfolkActivity['distribution'] as $act) {
                    fputcsv($file, [$act->fisherfolk_activity, $act->count, $act->approved]);
                }
                if ($fisherfolkActivity['other_activities_specified']->isNotEmpty()) {
                    fputcsv($file, []);
                    fputcsv($file, ['Other Activities Specified', 'Count']);
                    foreach ($fisherfolkActivity['other_activities_specified'] as $other) {
                        fputcsv($file, [$other->fisherfolk_other_activity, $other->count]);
                    }
                }
                fputcsv($file, []);

                // ── Agri-youth Analysis ─────────────────────────────────────────────
                fputcsv($file, ['AGRI-YOUTH ANALYSIS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Total Agri-youth',    $agriyouthAnalysis['total']]);
                fputcsv($file, ['Male',                $agriyouthAnalysis['male_count']]);
                fputcsv($file, ['Female',              $agriyouthAnalysis['female_count']]);
                fputcsv($file, ['Approved',            $agriyouthAnalysis['approved']]);
                fputcsv($file, ['Approval Rate',       $agriyouthAnalysis['approval_rate'] . '%']);
                if ($agriyouthAnalysis['by_farming_household']->isNotEmpty()) {
                    fputcsv($file, []);
                    fputcsv($file, ['Farming Household', 'Count']);
                    foreach ($agriyouthAnalysis['by_farming_household'] as $item) {
                        fputcsv($file, [$item->agriyouth_farming_household, $item->count]);
                    }
                }
                if ($agriyouthAnalysis['by_training']->isNotEmpty()) {
                    fputcsv($file, []);
                    fputcsv($file, ['Training', 'Count']);
                    foreach ($agriyouthAnalysis['by_training'] as $item) {
                        fputcsv($file, [$item->agriyouth_training, $item->count]);
                    }
                }
                if ($agriyouthAnalysis['by_participation']->isNotEmpty()) {
                    fputcsv($file, []);
                    fputcsv($file, ['Participation', 'Count']);
                    foreach ($agriyouthAnalysis['by_participation'] as $item) {
                        fputcsv($file, [$item->agriyouth_participation, $item->count]);
                    }
                }

                fclose($file);
            };

            // Log activity
            $this->logActivity('exported', 'RsbsaApplication', null, [
                'format'     => 'CSV',
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ], 'Exported RSBSA analytics data from ' . $startDate . ' to ' . $endDate);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('RSBSA Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting RSBSA data: ' . $e->getMessage());
        }
    }
}
