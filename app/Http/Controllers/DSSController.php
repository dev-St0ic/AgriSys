<?php

namespace App\Http\Controllers;

use App\Services\DSSDataService;
use App\Services\DSSReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DSSController extends Controller
{
    protected DSSDataService $dataService;
    protected DSSReportService $reportService;

    public function __construct(DSSDataService $dataService, DSSReportService $reportService)
    {
        $this->dataService = $dataService;
        $this->reportService = $reportService;
    }

    /**
     * Show DSS report preview page
     */
    public function preview(Request $request)
    {
        try {
            // Check if this is an AJAX request for data loading
            if ($request->ajax()) {
                return $this->loadDataAjax($request);
            }

            // Get service type from request (default: comprehensive)
            $service = $request->get('service', 'comprehensive');

            // Get month and year from request or default to current
            $month = $request->get('month', now()->format('m'));
            $year = $request->get('year', now()->format('Y'));

            // For initial page load, show page with loading state
            return view('admin.dss.preview', compact('month', 'year', 'service'));

        } catch (\Exception $e) {
            Log::error('DSS Preview Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to generate DSS preview: ' . $e->getMessage());
        }
    }

    /**
     * Load DSS data via AJAX to prevent timeouts
     */
    public function loadDataAjax(Request $request)
    {
        try {
            $month = $request->get('month', now()->format('m'));
            $year = $request->get('year', now()->format('Y'));
            $service = $request->get('service', 'comprehensive');

            // Set longer timeout for this operation
            set_time_limit(300); // 5 minutes

            // Collect data based on service type
            if ($service === 'training') {
                $data = $this->dataService->collectTrainingData($month, $year);
                $report = $this->reportService->generateTrainingReport($data);
                $viewPartial = 'admin.dss.partials.training-content';
            } elseif ($service === 'rsbsa') {
                $data = $this->dataService->collectRsbsaData($month, $year);
                $report = $this->reportService->generateRsbsaReport($data);
                $viewPartial = 'admin.dss.partials.rsbsa-content';
            } else {
                // Comprehensive report (default)
                $data = $this->dataService->collectMonthlyData($month, $year);
                $report = $this->reportService->generateReport($data);
                $viewPartial = 'admin.dss.partials.report-content';
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'report' => $report,
                'html' => view($viewPartial, compact('data', 'report'))->render()
            ]);

        } catch (\Exception $e) {
            Log::error('DSS AJAX Data Loading Failed', [
                'error' => $e->getMessage(),
                'month' => $request->get('month'),
                'year' => $request->get('year'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load DSS data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and download PDF report
     */
    public function downloadPDF(Request $request)
    {
        try {
            $month = $request->get('month', now()->format('m'));
            $year = $request->get('year', now()->format('Y'));
            $service = $request->get('service', 'comprehensive');

            // Collect data based on service type
            if ($service === 'training') {
                $data = $this->dataService->collectTrainingData($month, $year);
                $report = $this->reportService->generateTrainingReport($data);
                $view = 'admin.dss.pdf-training';
                $filename = 'Training_DSS_Report_';
            } elseif ($service === 'rsbsa') {
                $data = $this->dataService->collectRsbsaData($month, $year);
                $report = $this->reportService->generateRsbsaReport($data);
                $view = 'admin.dss.pdf-rsbsa';
                $filename = 'RSBSA_DSS_Report_';
            } else {
                $data = $this->dataService->collectMonthlyData($month, $year);
                $report = $this->reportService->generateReport($data);
                $view = 'admin.dss.pdf-report';
                $filename = 'Supplies_DSS_Report_';
            }

            // Generate PDF
            $pdf = Pdf::loadView($view, compact('data', 'report'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Arial',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                ]);

            $filename .= Carbon::createFromDate($year, $month, 1)->format('Y_m') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('DSS PDF Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to generate PDF report: ' . $e->getMessage());
        }
    }

    /**
     * Generate and download Word document
     */
    public function downloadWord(Request $request)
    {
        try {
            $month = $request->get('month', now()->format('m'));
            $year = $request->get('year', now()->format('Y'));
            $service = $request->get('service', 'comprehensive');

            // Collect data based on service type
            if ($service === 'training') {
                $data = $this->dataService->collectTrainingData($month, $year);
                $report = $this->reportService->generateTrainingReport($data);
                $filename = 'Training_DSS_Report_';
            } elseif ($service === 'rsbsa') {
                $data = $this->dataService->collectRsbsaData($month, $year);
                $report = $this->reportService->generateRsbsaReport($data);
                $filename = 'RSBSA_DSS_Report_';
            } else {
                $data = $this->dataService->collectMonthlyData($month, $year);
                $report = $this->reportService->generateReport($data);
                $filename = 'Supplies_DSS_Report_';
            }

            // Create Word document content
            $wordContent = $this->generateWordContent($data, $report);

            $filename .= Carbon::createFromDate($year, $month, 1)->format('Y_m') . '.doc';

            return response($wordContent)
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('DSS Word Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to generate Word document: ' . $e->getMessage());
        }
    }

    /**
     * Get fresh data via AJAX
     */
    public function refreshData(Request $request)
    {
        try {
            $month = $request->get('month', now()->format('m'));
            $year = $request->get('year', now()->format('Y'));
            $service = $request->get('service', 'comprehensive');

            // Clear cache based on service type
            if ($service === 'training') {
                $cacheKey = 'dss_training_data_' . $year . '_' . $month;
                cache()->forget($cacheKey);
                $data = $this->dataService->collectTrainingData($month, $year);
                $report = $this->reportService->generateTrainingReport($data);
            } elseif ($service === 'rsbsa') {
                $cacheKey = 'dss_rsbsa_data_' . $year . '_' . $month;
                cache()->forget($cacheKey);
                $data = $this->dataService->collectRsbsaData($month, $year);
                $report = $this->reportService->generateRsbsaReport($data);
            } else {
                $cacheKey = 'dss_report_' . md5(serialize([$month, $year]));
                cache()->forget($cacheKey);
                $data = $this->dataService->collectMonthlyData($month, $year);
                $report = $this->reportService->generateReport($data);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'report' => $report,
                'message' => 'Data refreshed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('DSS Data Refresh Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Word document content
     */
    private function generateWordContent(array $data, array $report): string
    {
        $reportData = $report['report_data'];
        $period = $data['period']['month'];

        $html = "
        <html>
        <head>
            <meta charset='utf-8'>
            <title>DSS Report - {$period}</title>
        </head>
        <body style='font-family: Arial, sans-serif; margin: 20px;'>
            <h1 style='color: #2E7D32; text-align: center;'>Decision Support System Report</h1>
            <h2 style='color: #4CAF50; text-align: center;'>{$period}</h2>
            <hr>

            <h3>Executive Summary</h3>
            <p>{$reportData['executive_summary']}</p>

            <h3>Performance Assessment</h3>
            <p><strong>Overall Rating:</strong> {$reportData['performance_assessment']['overall_rating']}</p>
            <p><strong>Approval Efficiency:</strong> {$reportData['performance_assessment']['approval_efficiency']}</p>
            <p><strong>Supply Adequacy:</strong> {$reportData['performance_assessment']['supply_adequacy']}</p>
            <p><strong>Geographic Coverage:</strong> {$reportData['performance_assessment']['geographic_coverage']}</p>

            <h3>Key Findings</h3>
            <ul>";

        foreach ($reportData['key_findings'] as $finding) {
            $html .= "<li>{$finding}</li>";
        }

        $html .= "</ul>

            <h3>Critical Issues</h3>
            <ul>";

        foreach ($reportData['critical_issues'] as $issue) {
            $html .= "<li>{$issue}</li>";
        }

        $html .= "</ul>

            <h3>Recommendations</h3>

            <h4>Immediate Actions</h4>
            <ul>";

        if (isset($reportData['recommendations']['immediate_actions'])) {
            foreach ($reportData['recommendations']['immediate_actions'] as $action) {
                $html .= "<li>{$action}</li>";
            }
        }

        $html .= "</ul>

            <h4>Short-term Strategies</h4>
            <ul>";

        if (isset($reportData['recommendations']['short_term_strategies'])) {
            foreach ($reportData['recommendations']['short_term_strategies'] as $strategy) {
                $html .= "<li>{$strategy}</li>";
            }
        }

        $html .= "</ul>

            <hr>
            <p style='font-size: 12px; color: #666;'>
                Report generated on " . now()->format('F j, Y \a\t g:i A') . "<br>
                Source: {$report['source']}<br>
                Confidence Level: {$reportData['confidence_level']}" .
                (isset($reportData['confidence_score']) ? " ({$reportData['confidence_score']}%)" : "") . "
            </p>
        </body>
        </html>";

        return $html;
    }

    /**
     * Get available months and years for dropdown
     */
    public function getAvailablePeriods()
    {
        $periods = [];
        $currentDate = now();

        // Generate last 12 months
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $periods[] = [
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
                'label' => $date->format('F Y'),
                'value' => $date->format('Y-m')
            ];
        }

        return response()->json($periods);
    }
}
