<?php

namespace App\Http\Controllers;

use App\Models\UserRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserRegistrationAnalyticsController extends Controller
{
    /**
     * Display User Registration analytics dashboard
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
            $baseQuery = UserRegistration::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            // 1. Overview Statistics
            $overview = $this->getOverviewStatistics(clone $baseQuery);

            // 2. Registration Status Analysis
            $statusAnalysis = $this->getStatusAnalysis(clone $baseQuery);

            // 3. Monthly Trends
            $monthlyTrends = $this->getMonthlyTrends($startDate, $endDate);

            // 4. User Type Analysis
            $userTypeAnalysis = $this->getUserTypeAnalysis(clone $baseQuery);

            // 5. Barangay Distribution
            $barangayAnalysis = $this->getBarangayAnalysis(clone $baseQuery);

            // 6. Gender Distribution Analysis
            $genderAnalysis = $this->getGenderAnalysis(clone $baseQuery);

            // 7. Verification Processing Time
            $processingTimeAnalysis = $this->getProcessingTimeAnalysis(clone $baseQuery);

            // 8. Registration Patterns
            $registrationPatterns = $this->getRegistrationPatterns(clone $baseQuery);

            // 9. Document Submission Analysis
            $documentAnalysis = $this->getDocumentAnalysis(clone $baseQuery);

            // 10. Performance Metrics
            $performanceMetrics = $this->getPerformanceMetrics(clone $baseQuery);

            // 11. Profile Completion Analysis
            $profileCompletionAnalysis = $this->getProfileCompletionAnalysis(clone $baseQuery);

            // 12. Age Distribution Analysis
            $ageAnalysis = $this->getAgeAnalysis(clone $baseQuery);

            return view('admin.analytics.user-registration', compact(
                'overview',
                'statusAnalysis',
                'monthlyTrends',
                'userTypeAnalysis',
                'barangayAnalysis',
                'genderAnalysis',
                'processingTimeAnalysis',
                'registrationPatterns',
                'documentAnalysis',
                'performanceMetrics',
                'profileCompletionAnalysis',
                'ageAnalysis',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('User Registration Analytics Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading user registration analytics data. Please try again.');
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
            $pending = (clone $baseQuery)->where('status', 'pending')->count();
            $unverified = (clone $baseQuery)->where('status', 'unverified')->count();
            $banned = (clone $baseQuery)->where('status', 'banned')->count();

            // Profile completion stats (users who moved from unverified to pending/approved/rejected)
            $profileCompleted = (clone $baseQuery)->whereIn('status', ['pending', 'approved', 'rejected'])->count();

            // Document submission stats
            $withLocationDoc = (clone $baseQuery)->whereNotNull('location_document_path')->where('location_document_path', '!=', '')->count();
            $withIdFront = (clone $baseQuery)->whereNotNull('id_front_path')->where('id_front_path', '!=', '')->count();
            $withIdBack = (clone $baseQuery)->whereNotNull('id_back_path')->where('id_back_path', '!=', '')->count();
            $withAllDocuments = (clone $baseQuery)
                ->whereNotNull('location_document_path')
                ->whereNotNull('id_front_path')
                ->whereNotNull('id_back_path')
                ->where('location_document_path', '!=', '')
                ->where('id_front_path', '!=', '')
                ->where('id_back_path', '!=', '')
                ->count();

            // Active barangays
            $activeBarangays = (clone $baseQuery)->whereNotNull('barangay')->distinct()->count('barangay');

            // User types
            $userTypes = (clone $baseQuery)->whereNotNull('user_type')->distinct()->count('user_type');

            return [
                'total_registrations' => $total,
                'approved_registrations' => $approved,
                'rejected_registrations' => $rejected,
                'pending_registrations' => $pending,
                'unverified_registrations' => $unverified,
                'banned_registrations' => $banned,
                'approval_rate' => $this->calculateApprovalRate($total, $approved),
                'profile_completed' => $profileCompleted,
                'profile_completion_rate' => $total > 0 ? round(($profileCompleted / $total) * 100, 2) : 0,
                'with_location_doc' => $withLocationDoc,
                'with_id_front' => $withIdFront,
                'with_id_back' => $withIdBack,
                'with_all_documents' => $withAllDocuments,
                'document_completion_rate' => $total > 0 ? round(($withAllDocuments / $total) * 100, 2) : 0,
                'active_barangays' => $activeBarangays,
                'user_types' => $userTypes
            ];
        } catch (\Exception $e) {
            Log::error('User Registration Overview Statistics Error: ' . $e->getMessage());
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

            // Ensure all statuses are present
            $defaultStatuses = [
                'unverified' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'banned' => 0
            ];
            $statusCounts = array_merge($defaultStatuses, $statusCounts);

            // Calculate percentages
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
            Log::error('User Registration Status Analysis Error: ' . $e->getMessage());
            return [
                'counts' => ['unverified' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'banned' => 0],
                'percentages' => ['unverified' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'banned' => 0],
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
            $trends = UserRegistration::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_registrations'),
                    DB::raw('SUM(CASE WHEN status = "unverified" THEN 1 ELSE 0 END) as unverified'),
                    DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status = "banned" THEN 1 ELSE 0 END) as banned'),
                    DB::raw('SUM(CASE WHEN status IN ("pending", "approved", "rejected") THEN 1 ELSE 0 END) as profile_completed'),
                    DB::raw('COUNT(DISTINCT barangay) as unique_barangays'),
                    DB::raw('COUNT(DISTINCT user_type) as unique_user_types')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return $trends;
        } catch (\Exception $e) {
            Log::error('User Registration Monthly Trends Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get user type analysis
     */
    private function getUserTypeAnalysis($baseQuery)
    {
        try {
            $userTypeStats = (clone $baseQuery)->select(
                    'user_type',
                    DB::raw('COUNT(*) as total_registrations'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(CASE WHEN status = "unverified" THEN 1 ELSE 0 END) as unverified'),
                    DB::raw('SUM(CASE WHEN status IN ("pending", "approved", "rejected") THEN 1 ELSE 0 END) as profile_completed'),
                    DB::raw('COUNT(DISTINCT barangay) as barangays_represented'),
                    DB::raw('AVG(CASE WHEN approved_at IS NOT NULL AND created_at IS NOT NULL
                             THEN DATEDIFF(approved_at, created_at) END) as avg_processing_days')
                )
                ->whereNotNull('user_type')
                ->where('user_type', '!=', '')
                ->groupBy('user_type')
                ->orderBy('total_registrations', 'desc')
                ->get();

            return $userTypeStats;
        } catch (\Exception $e) {
            Log::error('User Registration Type Analysis Error: ' . $e->getMessage());
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
                    DB::raw('COUNT(*) as total_registrations'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(CASE WHEN status = "unverified" THEN 1 ELSE 0 END) as unverified'),
                    DB::raw('COUNT(DISTINCT user_type) as user_types_count'),
                    DB::raw('SUM(CASE WHEN status IN ("pending", "approved", "rejected") THEN 1 ELSE 0 END) as profile_completed')
                )
                ->whereNotNull('barangay')
                ->where('barangay', '!=', '')
                ->groupBy('barangay')
                ->orderBy('total_registrations', 'desc')
                ->get();

            return $barangayStats;
        } catch (\Exception $e) {
            Log::error('User Registration Barangay Analysis Error: ' . $e->getMessage());
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
                    DB::raw('COUNT(*) as total_registrations'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('COUNT(DISTINCT barangay) as barangays_represented'),
                    DB::raw('AVG(age) as avg_age')
                )
                ->whereNotNull('sex')
                ->where('sex', '!=', '')
                ->groupBy('sex')
                ->get();

            $total = $genderStats->sum('total_registrations');

            $genderPercentages = [];
            foreach ($genderStats as $stat) {
                $genderPercentages[$stat->sex] = $total > 0 ? round(($stat->total_registrations / $total) * 100, 2) : 0;
            }

            return [
                'stats' => $genderStats,
                'percentages' => $genderPercentages,
                'total' => $total
            ];
        } catch (\Exception $e) {
            Log::error('User Registration Gender Analysis Error: ' . $e->getMessage());
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
            $processedRegistrations = (clone $baseQuery)
                ->where(function($q) {
                    $q->whereNotNull('approved_at')->orWhereNotNull('rejected_at');
                })
                ->get();

            $processingTimes = [];
            foreach ($processedRegistrations as $registration) {
                $processedAt = $registration->approved_at ?? $registration->rejected_at;
                if ($processedAt && $registration->created_at) {
                    $processingTime = Carbon::parse($registration->created_at)
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
            Log::error('User Registration Processing Time Error: ' . $e->getMessage());
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
     * Get registration patterns
     */
    private function getRegistrationPatterns($baseQuery)
    {
        try {
            $dayOfWeekStats = (clone $baseQuery)->select(
                    DB::raw('DAYNAME(created_at) as day_name'),
                    DB::raw('DAYOFWEEK(created_at) as day_number'),
                    DB::raw('COUNT(*) as registrations_count')
                )
                ->groupBy('day_name', 'day_number')
                ->orderBy('day_number')
                ->get();

            $hourlyStats = (clone $baseQuery)->select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as registrations_count')
                )
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            return [
                'day_of_week' => $dayOfWeekStats,
                'hourly' => $hourlyStats
            ];
        } catch (\Exception $e) {
            Log::error('User Registration Patterns Error: ' . $e->getMessage());
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
            $total = (clone $baseQuery)->count();

            // Document submission stats
            $withLocationDoc = (clone $baseQuery)->whereNotNull('location_document_path')->where('location_document_path', '!=', '')->count();
            $withIdFront = (clone $baseQuery)->whereNotNull('id_front_path')->where('id_front_path', '!=', '')->count();
            $withIdBack = (clone $baseQuery)->whereNotNull('id_back_path')->where('id_back_path', '!=', '')->count();

            $withAllDocs = (clone $baseQuery)
                ->whereNotNull('location_document_path')
                ->whereNotNull('id_front_path')
                ->whereNotNull('id_back_path')
                ->where('location_document_path', '!=', '')
                ->where('id_front_path', '!=', '')
                ->where('id_back_path', '!=', '')
                ->count();

            $withNoDocs = (clone $baseQuery)
                ->where(function($q) {
                    $q->whereNull('location_document_path')
                      ->orWhere('location_document_path', '');
                })
                ->where(function($q) {
                    $q->whereNull('id_front_path')
                      ->orWhere('id_front_path', '');
                })
                ->where(function($q) {
                    $q->whereNull('id_back_path')
                      ->orWhere('id_back_path', '');
                })
                ->count();

            // Approval rates by document submission
            $approvalWithAllDocs = (clone $baseQuery)
                ->whereNotNull('location_document_path')
                ->whereNotNull('id_front_path')
                ->whereNotNull('id_back_path')
                ->where('location_document_path', '!=', '')
                ->where('id_front_path', '!=', '')
                ->where('id_back_path', '!=', '')
                ->where('status', 'approved')
                ->count();

            $approvalWithoutDocs = (clone $baseQuery)
                ->where(function($q) {
                    $q->whereNull('location_document_path')
                      ->orWhereNull('id_front_path')
                      ->orWhereNull('id_back_path')
                      ->orWhere('location_document_path', '')
                      ->orWhere('id_front_path', '')
                      ->orWhere('id_back_path', '');
                })
                ->where('status', 'approved')
                ->count();

            return [
                'total' => $total,
                'with_location_doc' => $withLocationDoc,
                'with_id_front' => $withIdFront,
                'with_id_back' => $withIdBack,
                'with_all_documents' => $withAllDocs,
                'with_no_documents' => $withNoDocs,
                'location_doc_rate' => $total > 0 ? round(($withLocationDoc / $total) * 100, 2) : 0,
                'id_front_rate' => $total > 0 ? round(($withIdFront / $total) * 100, 2) : 0,
                'id_back_rate' => $total > 0 ? round(($withIdBack / $total) * 100, 2) : 0,
                'complete_docs_rate' => $total > 0 ? round(($withAllDocs / $total) * 100, 2) : 0,
                'approval_rate_with_docs' => $withAllDocs > 0 ? round(($approvalWithAllDocs / $withAllDocs) * 100, 2) : 0,
                'approval_rate_without_docs' => ($total - $withAllDocs) > 0 ? round(($approvalWithoutDocs / ($total - $withAllDocs)) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('User Registration Document Analysis Error: ' . $e->getMessage());
            return [
                'total' => 0,
                'with_location_doc' => 0,
                'with_id_front' => 0,
                'with_id_back' => 0,
                'with_all_documents' => 0,
                'with_no_documents' => 0,
                'location_doc_rate' => 0,
                'id_front_rate' => 0,
                'id_back_rate' => 0,
                'complete_docs_rate' => 0,
                'approval_rate_with_docs' => 0,
                'approval_rate_without_docs' => 0
            ];
        }
    }

    /**
     * Get profile completion analysis
     */
    private function getProfileCompletionAnalysis($baseQuery)
    {
        try {
            $total = (clone $baseQuery)->count();
            $completed = (clone $baseQuery)->whereIn('status', ['pending', 'approved', 'rejected'])->count();
            $unverified = $total - $completed;

            // Approval rates by profile completion
            $approvedCompleted = (clone $baseQuery)
                ->whereIn('status', ['pending', 'approved', 'rejected'])
                ->where('status', 'approved')
                ->count();

            $approvedIncomplete = (clone $baseQuery)
                ->where('status', 'unverified')
                ->count(); // Unverified users can't be approved

            return [
                'total' => $total,
                'completed' => $completed,
                'unverified' => $unverified,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                'approval_rate_completed' => $completed > 0 ? round(($approvedCompleted / $completed) * 100, 2) : 0,
                'unverified_count' => $unverified
            ];
        } catch (\Exception $e) {
            Log::error('Profile Completion Analysis Error: ' . $e->getMessage());
            return [
                'total' => 0,
                'completed' => 0,
                'unverified' => 0,
                'completion_rate' => 0,
                'approval_rate_completed' => 0,
                'unverified_count' => 0
            ];
        }
    }

    /**
     * Get age distribution analysis
     */
    private function getAgeAnalysis($baseQuery)
    {
        try {
            $ageRanges = (clone $baseQuery)
                ->select(
                    DB::raw('CASE
                        WHEN age < 18 THEN "Under 18"
                        WHEN age BETWEEN 18 AND 25 THEN "18-25"
                        WHEN age BETWEEN 26 AND 35 THEN "26-35"
                        WHEN age BETWEEN 36 AND 45 THEN "36-45"
                        WHEN age BETWEEN 46 AND 55 THEN "46-55"
                        WHEN age BETWEEN 56 AND 65 THEN "56-65"
                        ELSE "65+"
                        END as age_range'),
                    DB::raw('COUNT(*) as user_count'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved')
                )
                ->whereNotNull('age')
                ->where('age', '>', 0)
                ->groupBy('age_range')
                ->orderBy(DB::raw('MIN(age)'))
                ->get();

            return $ageRanges;
        } catch (\Exception $e) {
            Log::error('Age Analysis Error: ' . $e->getMessage());
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

            // Overall completion rate (approved + rejected)
            $total = (clone $baseQuery)->count();
            $completed = (clone $baseQuery)->whereIn('status', ['approved', 'rejected'])->count();
            $metrics['completion_rate'] = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

            // Average registrations per day
            $dateRange = (clone $baseQuery)->selectRaw('DATEDIFF(MAX(created_at), MIN(created_at)) + 1 as days')->first();
            $totalDays = $dateRange ? $dateRange->days : 1;
            $metrics['avg_registrations_per_day'] = round($total / max(1, $totalDays), 2);

            // Quality score (based on document submission, profile completion, and approval rates)
            $overview = $this->getOverviewStatistics(clone $baseQuery);
            $docAnalysis = $this->getDocumentAnalysis(clone $baseQuery);
            $metrics['quality_score'] = round(
                ($overview['approval_rate'] * 0.5) +
                ($docAnalysis['complete_docs_rate'] * 0.3) +
                ($overview['profile_completion_rate'] * 0.2), 2
            );

            // User engagement rate
            $metrics['engagement_rate'] = $overview['document_completion_rate'];

            return $metrics;
        } catch (\Exception $e) {
            Log::error('Performance Metrics Error: ' . $e->getMessage());
            return [
                'completion_rate' => 0,
                'avg_registrations_per_day' => 0,
                'quality_score' => 0,
                'engagement_rate' => 0
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
            'total_registrations' => 0,
            'approved_registrations' => 0,
            'rejected_registrations' => 0,
            'pending_registrations' => 0,
            'unverified_registrations' => 0,
            'banned_registrations' => 0,
            'approval_rate' => 0,
            'profile_completed' => 0,
            'profile_completion_rate' => 0,
            'with_location_doc' => 0,
            'with_id_front' => 0,
            'with_id_back' => 0,
            'with_all_documents' => 0,
            'document_completion_rate' => 0,
            'active_barangays' => 0,
            'user_types' => 0
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

            $baseQuery = UserRegistration::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

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
                'user_type_analysis' => $this->getUserTypeAnalysis(clone $baseQuery)->toArray(),
                'barangay_analysis' => $this->getBarangayAnalysis(clone $baseQuery)->toArray(),
                'gender_analysis' => $this->getGenderAnalysis(clone $baseQuery),
                'processing_time' => $this->getProcessingTimeAnalysis(clone $baseQuery),
                'registration_patterns' => $this->getRegistrationPatterns(clone $baseQuery),
                'document_analysis' => $this->getDocumentAnalysis(clone $baseQuery),
                'performance_metrics' => $this->getPerformanceMetrics(clone $baseQuery),
                'profile_completion_analysis' => $this->getProfileCompletionAnalysis(clone $baseQuery),
                'age_analysis' => $this->getAgeAnalysis(clone $baseQuery)->toArray(),
            ];

            $filename = 'user-registration-analytics-' . $startDate . '-to-' . $endDate . '.json';

            // Log activity
            $this->logActivity('exported', 'UserRegistration', null, [
                'format' => 'JSON',
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ], 'Exported User Registration analytics data from ' . $startDate . ' to ' . $endDate);

            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('User Registration Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting user registration data: ' . $e->getMessage());
        }
    }
}
