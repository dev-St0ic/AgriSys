<?php

namespace App\Http\Controllers;

use App\Models\TrainingApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TrainingAnalyticsController extends Controller
{
    /**
     * Display Training analytics dashboard
     */
    public function index(Request $request)
    {
        try {
            // Date range filter with better defaults (2 years back to capture all dummy data)
            $startDate = $request->get('start_date', now()->subYears(2)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            // Validate dates
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            // Debug: Log the date range being used
            Log::info('Training Analytics Date Range', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_all_records' => TrainingApplication::count(),
                'total_in_range' => TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count()
            ]);

            // 1. Overview Statistics
            $overview = $this->getOverviewStatistics($startDate, $endDate);

            // 2. Application Status Analysis
            $statusAnalysis = $this->getStatusAnalysis($startDate, $endDate);

            // 3. Monthly Trends
            $monthlyTrends = $this->getMonthlyTrends($startDate, $endDate);

            // 4. Training Type Analysis
            $trainingTypeAnalysis = $this->getTrainingTypeAnalysis($startDate, $endDate);

            // 5. Registration Patterns
            $registrationPatterns = $this->getRegistrationPatterns($startDate, $endDate);

            // 6. Application Processing Time Analysis
            $processingTimeAnalysis = $this->getProcessingTimeAnalysis($startDate, $endDate);

            // 7. Document Submission Analysis
            $documentAnalysis = $this->getDocumentAnalysis($startDate, $endDate);

            // 8. Performance Metrics
            $performanceMetrics = $this->getPerformanceMetrics($startDate, $endDate);

            // 9. Applicant Demographics
            $demographicAnalysis = $this->getDemographicAnalysis($startDate, $endDate);

            // 10. Barangay Analysis
            $barangayAnalysis = $this->getBarangayAnalysis($startDate, $endDate);

            return view('admin.analytics.training', compact(
                'overview',
                'statusAnalysis',
                'monthlyTrends',
                'trainingTypeAnalysis',
                'registrationPatterns',
                'processingTimeAnalysis',
                'documentAnalysis',
                'performanceMetrics',
                'demographicAnalysis',
                'barangayAnalysis',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('Training Analytics Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading Training analytics data. Please try again.');
        }
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStatistics($startDate, $endDate)
    {
        try {
            // IMPORTANT: Create fresh queries for each count
            $total = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
            $approved = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->where('status', 'approved')->count();
            $rejected = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->where('status', 'rejected')->count();
            $pending = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->where('status', 'under_review')->count();

            Log::info('Training Overview Debug', [
                'total' => $total,
                'approved' => $approved,
                'rejected' => $rejected,
                'pending' => $pending
            ]);

            // Count unique applicants
            $uniqueApplicants = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
                ->distinct()
                ->whereNotNull('first_name')
                ->count();

            // Count applications with documents
            $withDocuments = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereNotNull('document_path')->where('document_path', '!=', '')->count();

            // Count unique training types requested
            $uniqueTrainingTypes = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereNotNull('training_type')->distinct()->count('training_type');

            // Count applications with contact number
            $withContactNumber = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereNotNull('contact_number')->where('contact_number', '!=', '')->count();

            return [
                'total_applications' => $total,
                'approved_applications' => $approved,
                'rejected_applications' => $rejected,
                'pending_applications' => $pending,
                'approval_rate' => $this->calculateApprovalRate($total, $approved),
                'unique_applicants' => $uniqueApplicants,
                'with_documents' => $withDocuments,
                'document_submission_rate' => $total > 0 ? round(($withDocuments / $total) * 100, 2) : 0,
                'unique_training_types' => $uniqueTrainingTypes,
                'with_contact_number' => $withContactNumber,
                'contact_provision_rate' => $total > 0 ? round(($withContactNumber / $total) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Training Overview Statistics Error: ' . $e->getMessage());
            return $this->getDefaultOverview();
        }
    }

    /**
     * Get status analysis with trends
     */
    private function getStatusAnalysis($startDate, $endDate)
    {
        try {
            $statusCounts = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select('status', DB::raw('count(*) as count'))
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
            Log::error('Training Status Analysis Error: ' . $e->getMessage());
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
            $trends = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status = "under_review" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(CASE WHEN document_path IS NOT NULL AND document_path != "" THEN 1 ELSE 0 END) as with_documents'),
                    DB::raw('COUNT(DISTINCT training_type) as unique_training_types')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            Log::info('Monthly Trends Query Result', ['count' => $trends->count(), 'data' => $trends->toArray()]);

            return $trends;
        } catch (\Exception $e) {
            Log::error('Training Monthly Trends Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get training type analysis
     */
    private function getTrainingTypeAnalysis($startDate, $endDate)
    {
        try {
            $trainingStats = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    'training_type',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status = "under_review" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('COUNT(DISTINCT CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, ""))) as unique_applicants'),
                    DB::raw('SUM(CASE WHEN document_path IS NOT NULL AND document_path != "" THEN 1 ELSE 0 END) as with_documents'),
                    DB::raw('AVG(CASE WHEN status_updated_at IS NOT NULL AND created_at IS NOT NULL
                             THEN DATEDIFF(status_updated_at, created_at) END) as avg_processing_days')
                )
                ->whereNotNull('training_type')
                ->where('training_type', '!=', '')
                ->groupBy('training_type')
                ->orderBy('total_applications', 'desc')
                ->get();

            return $trainingStats;
        } catch (\Exception $e) {
            Log::error('Training Type Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get registration patterns (day of week, time patterns)
     */
    private function getRegistrationPatterns($startDate, $endDate)
    {
        try {
            $dayOfWeekStats = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DAYNAME(created_at) as day_name'),
                    DB::raw('DAYOFWEEK(created_at) as day_number'),
                    DB::raw('COUNT(*) as applications_count')
                )
                ->groupBy('day_name', 'day_number')
                ->orderBy('day_number')
                ->get();

            $hourlyStats = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
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
            Log::error('Training Registration Patterns Error: ' . $e->getMessage());
            return [
                'day_of_week' => collect([]),
                'hourly' => collect([])
            ];
        }
    }

    /**
     * Get processing time analysis
     */
    private function getProcessingTimeAnalysis($startDate, $endDate)
    {
        try {
            $processedApplications = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereNotNull('status_updated_at')->get();

            $processingTimes = [];
            foreach ($processedApplications as $application) {
                if ($application->status_updated_at && $application->created_at) {
                    $processingTime = Carbon::parse($application->created_at)
                        ->diffInDays(Carbon::parse($application->status_updated_at));
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
            Log::error('Training Processing Time Error: ' . $e->getMessage());
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
     * Get document submission analysis
     */
    private function getDocumentAnalysis($startDate, $endDate)
    {
        try {
            $withDocs = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereNotNull('document_path')->where('document_path', '!=', '')->count();
            $withoutDocs = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where(function($q) {
                    $q->whereNull('document_path')->orWhere('document_path', '=', '');
                })->count();

            $total = $withDocs + $withoutDocs;

            // Approval rates by document submission
            $approvalWithDocs = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereNotNull('document_path')
                ->where('document_path', '!=', '')
                ->where('status', 'approved')
                ->count();

            $approvalWithoutDocs = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->where(function($q) {
                    $q->whereNull('document_path')->orWhere('document_path', '=', '');
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
            Log::error('Training Document Analysis Error: ' . $e->getMessage());
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
     * Get performance metrics
     */
    private function getPerformanceMetrics($startDate, $endDate)
    {
        try {
            $metrics = [];

            // Overall completion rate
            $total = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
            $completed = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->whereIn('status', ['approved', 'rejected'])->count();
            $metrics['completion_rate'] = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

            // Average applications per day
            $dateRange = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->selectRaw('DATEDIFF(MAX(created_at), MIN(created_at)) + 1 as days')->first();
            $totalDays = $dateRange ? $dateRange->days : 1;
            $metrics['avg_applications_per_day'] = round($total / max(1, $totalDays), 2);

            // Quality score (based on document submission and approval rates)
            $overview = $this->getOverviewStatistics($startDate, $endDate);
            $docAnalysis = $this->getDocumentAnalysis($startDate, $endDate);
            $metrics['quality_score'] = round(
                ($overview['approval_rate'] * 0.6) +
                ($docAnalysis['submission_rate'] * 0.4), 2
            );

            return $metrics;
        } catch (\Exception $e) {
            Log::error('Training Performance Metrics Error: ' . $e->getMessage());
            return [
                'completion_rate' => 0,
                'avg_applications_per_day' => 0,
                'quality_score' => 0
            ];
        }
    }

    /**
     * Get demographic analysis
     */
    private function getDemographicAnalysis($startDate, $endDate)
    {
        try {
            // Most common first names
            $commonNames = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select('first_name', DB::raw('COUNT(*) as count'))
                ->whereNotNull('first_name')
                ->where('first_name', '!=', '')
                ->groupBy('first_name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            // Application length patterns
            $applicationsByNameLength = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('CASE
                        WHEN LENGTH(CONCAT(first_name, " ", COALESCE(middle_name, ""), " ", last_name)) <= 20 THEN "Short"
                        WHEN LENGTH(CONCAT(first_name, " ", COALESCE(middle_name, ""), " ", last_name)) <= 35 THEN "Medium"
                        ELSE "Long" END as name_length_category'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('AVG(CASE WHEN status = "approved" THEN 1.0 ELSE 0.0 END) * 100 as approval_rate')
                )
                ->groupBy('name_length_category')
                ->get();

            return [
                'common_names' => $commonNames,
                'name_length_patterns' => $applicationsByNameLength
            ];
        } catch (\Exception $e) {
            Log::error('Training Demographic Analysis Error: ' . $e->getMessage());
            return [
                'common_names' => collect([]),
                'name_length_patterns' => collect([])
            ];
        }
    }

    /**
     * Get barangay analysis
     */
    private function getBarangayAnalysis($startDate, $endDate)
    {
        try {
            $barangayStats = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    'barangay',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status = "under_review" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('COUNT(DISTINCT CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, ""))) as unique_applicants'),
                    DB::raw('SUM(CASE WHEN document_path IS NOT NULL AND document_path != "" THEN 1 ELSE 0 END) as with_documents')
                )
                ->whereNotNull('barangay')
                ->where('barangay', '!=', '')
                ->groupBy('barangay')
                ->orderByDesc('total_applications')
                ->get();

            return $barangayStats;
        } catch (\Exception $e) {
            Log::error('Training Barangay Analysis Error: ' . $e->getMessage());
            return collect([]);
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
            'with_documents' => 0,
            'document_submission_rate' => 0,
            'unique_training_types' => 0,
            'with_contact_number' => 0,
            'contact_provision_rate' => 0
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

            $data = [
                'export_info' => [
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ],
                    'generated_by' => auth()->user()->name ?? 'System'
                ],
                'overview' => $this->getOverviewStatistics($startDate, $endDate),
                'status_analysis' => $this->getStatusAnalysis($startDate, $endDate),
                'monthly_trends' => $this->getMonthlyTrends($startDate, $endDate)->toArray(),
                'training_type_analysis' => $this->getTrainingTypeAnalysis($startDate, $endDate)->toArray(),
                'registration_patterns' => $this->getRegistrationPatterns($startDate, $endDate),
                'processing_time' => $this->getProcessingTimeAnalysis($startDate, $endDate),
                'document_analysis' => $this->getDocumentAnalysis($startDate, $endDate),
                'performance_metrics' => $this->getPerformanceMetrics($startDate, $endDate),
                'demographic_analysis' => $this->getDemographicAnalysis($startDate, $endDate),
                'barangay_analysis' => $this->getBarangayAnalysis($startDate, $endDate)
            ];

            $filename = 'training-analytics-' . $startDate . '-to-' . $endDate . '.json';

            // Log activity
            $this->logActivity('exported', 'TrainingApplication', null, [
                'format' => 'JSON',
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ], 'Exported Training analytics data from ' . $startDate . ' to ' . $endDate);

            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Training Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting Training data: ' . $e->getMessage());
        }
    }
}
