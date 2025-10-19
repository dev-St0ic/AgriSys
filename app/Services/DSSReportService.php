<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DSSReportService
{
    private string $anthropicApiKey;
    private string $anthropicApiUrl = 'https://api.anthropic.com/v1/messages';
    private string $model;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        $this->anthropicApiKey = config('services.anthropic.key');
        $this->model = config('services.anthropic.model', 'claude-3-5-sonnet-20241022');
        $this->maxTokens = config('services.anthropic.max_tokens', 4096);
        $this->temperature = config('services.anthropic.temperature', 0.1);

        if (empty($this->anthropicApiKey)) {
            Log::warning('Anthropic API key not configured. Using fallback report generation.');
        }
    }

    /**
     * Generate comprehensive DSS report using LLM
     */
    public function generateReport(array $data): array
    {
        $cacheKey = 'dss_report_' . md5(serialize($data));
        $cacheDuration = config('services.anthropic.cache_duration', 3600);

        return Cache::remember($cacheKey, $cacheDuration, function() use ($data) {
            try {
                if (!empty($this->anthropicApiKey)) {
                    return $this->generateLLMReport($data);
                }

                return $this->generateFallbackReport($data);

            } catch (\Exception $e) {
                Log::error('DSS Report Generation Failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return $this->generateFallbackReport($data);
            }
        });
    }

    /**
     * Generate report using LLM (Claude)
     */
    private function generateLLMReport(array $data): array
    {
        $prompt = $this->buildPrompt($data);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-key' => $this->anthropicApiKey,
            'anthropic-version' => '2023-06-01'
        ])->timeout(300)->post($this->anthropicApiUrl, [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            $generatedText = $responseData['content'][0]['text'] ?? '';

            return $this->parseGeneratedReport($generatedText, $data);
        }

        throw new \Exception('LLM API request failed: ' . $response->body());
    }

    /**
     * Build comprehensive prompt for LLM
     */
    private function buildPrompt(array $data): string
    {
        $period = $data['period'];
        $requests = $data['requests_data'];
        $supply = $data['supply_data'];
        $barangays = $data['barangay_analysis'];
        $shortages = $data['shortage_analysis'];

        return "
You are an agricultural decision support assistant for the AgriSys platform.
Analyze the following data and generate a comprehensive monthly report with both descriptive analysis and prescriptive recommendations.

## MONTHLY DATA ANALYSIS
**Period:** {$period['month']}

### SEEDLING REQUESTS OVERVIEW
- Total Requests: {$requests['total_requests']}
- Approved Requests: {$requests['approved_requests']}
- Rejected Requests: {$requests['rejected_requests']}
- Pending Requests: {$requests['pending_requests']}
- Approval Rate: {$requests['approval_rate']}%
- Total Items Requested: {$requests['total_items_requested']}
- Average Processing Time: {$requests['average_processing_time']} days

### SUPPLY STATUS
- Available Stock: {$supply['available_stock']} units
- Low Stock Items: {$supply['low_stock_items']}
- Out of Stock Items: {$supply['out_of_stock_items']}
- Total Item Categories: {$supply['total_items']}

### BARANGAY DEMAND ANALYSIS
" . $this->formatBarangayData($barangays['barangay_details']) . "

### SHORTAGE ANALYSIS
- Critical Shortages: {$shortages['critical_shortages']}
- Total Shortage Value: {$shortages['total_shortage_value']} units
" . $this->formatShortageData($shortages['shortages']) . "

## REQUIRED OUTPUT FORMAT
Please provide your analysis in the following JSON structure:

```json
{
    \"executive_summary\": \"Brief 2-3 sentence overview of the month's performance\",
    \"key_findings\": [
        \"Finding 1\",
        \"Finding 2\",
        \"Finding 3\"
    ],
    \"performance_assessment\": {
        \"overall_rating\": \"Excellent/Good/Fair/Poor\",
        \"approval_efficiency\": \"Assessment of approval rate and processing time\",
        \"supply_adequacy\": \"Assessment of stock levels vs demand\",
        \"geographic_coverage\": \"Assessment of barangay distribution\"
    },
    \"critical_issues\": [
        \"Issue 1 with severity level\",
        \"Issue 2 with severity level\"
    ],
    \"recommendations\": {
        \"immediate_actions\": [
            \"Action 1 (Timeline: X days)\",
            \"Action 2 (Timeline: X days)\"
        ],
        \"short_term_strategies\": [
            \"Strategy 1 (Timeline: 1-3 months)\",
            \"Strategy 2 (Timeline: 1-3 months)\"
        ],
        \"long_term_improvements\": [
            \"Improvement 1 (Timeline: 3-6 months)\",
            \"Improvement 2 (Timeline: 3-6 months)\"
        ]
    },
    \"allocation_priorities\": [
        {
            \"barangay\": \"Barangay Name\",
            \"priority_level\": \"High/Medium/Low\",
            \"recommended_allocation\": \"X units\",
            \"justification\": \"Reason for priority\"
        }
    ],
    \"next_month_forecast\": {
        \"expected_demand\": \"Predicted demand based on trends\",
        \"recommended_stock_levels\": \"Suggested inventory targets\",
        \"focus_areas\": [\"Area 1\", \"Area 2\"]
    },
    \"confidence_level\": \"High/Medium/Low\",
    \"data_quality_notes\": \"Any limitations or data quality concerns\"
}
```

Focus on actionable insights that can help agricultural officers make informed decisions about seedling distribution, inventory management, and resource allocation.
";
    }

    /**
     * Format barangay data for prompt
     */
    private function formatBarangayData(array $barangays): string
    {
        $formatted = "";
        foreach (array_slice($barangays, 0, 10) as $barangay) {
            $formatted .= "- {$barangay['name']}: {$barangay['requests']} requests, {$barangay['total_quantity']} units (Priority: {$barangay['priority_level']})\n";
        }
        return $formatted;
    }

    /**
     * Format shortage data for prompt
     */
    private function formatShortageData(array $shortages): string
    {
        $formatted = "";
        foreach (array_slice($shortages, 0, 10) as $shortage) {
            $formatted .= "- {$shortage['item']}: Shortage of {$shortage['shortage']} units (Severity: {$shortage['severity']})\n";
        }
        return $formatted;
    }

    /**
     * Parse the generated report from LLM
     */
    private function parseGeneratedReport(string $generatedText, array $data): array
    {
        // Try to extract JSON from the response
        preg_match('/```json\s*(.*?)\s*```/s', $generatedText, $matches);

        if (!empty($matches[1])) {
            $jsonData = json_decode($matches[1], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return [
                    'generated_at' => now()->toDateTimeString(),
                    'period' => $data['period']['month'],
                    'report_data' => $jsonData,
                    'raw_response' => $generatedText,
                    'source' => 'llm',
                    'model_used' => $this->model,
                ];
            }
        }

        // If JSON parsing fails, create structured response from raw text
        return [
            'generated_at' => now()->toDateTimeString(),
            'period' => $data['period']['month'],
            'report_data' => [
                'executive_summary' => $this->extractSection($generatedText, 'executive_summary'),
                'raw_analysis' => $generatedText,
            ],
            'raw_response' => $generatedText,
            'source' => 'llm_text',
            'model_used' => $this->model,
        ];
    }

    /**
     * Generate fallback report when LLM is not available
     */
    private function generateFallbackReport(array $data): array
    {
        $requests = $data['requests_data'];
        $supply = $data['supply_data'];
        $shortages = $data['shortage_analysis'];

        $overallRating = $this->calculateOverallRating($requests, $supply, $shortages);

        return [
            'generated_at' => now()->toDateTimeString(),
            'period' => $data['period']['month'],
            'report_data' => [
                'executive_summary' => $this->generateFallbackSummary($data),
                'key_findings' => $this->generateFallbackFindings($data),
                'performance_assessment' => [
                    'overall_rating' => $overallRating,
                    'approval_efficiency' => $this->assessApprovalEfficiency($requests),
                    'supply_adequacy' => $this->assessSupplyAdequacy($supply, $shortages),
                    'geographic_coverage' => $this->assessGeographicCoverage($data['barangay_analysis']),
                ],
                'critical_issues' => $this->identifyCriticalIssues($data),
                'recommendations' => $this->generateFallbackRecommendations($data),
                'confidence_level' => 'Medium',
                'data_quality_notes' => 'Generated using fallback system due to LLM unavailability',
            ],
            'raw_response' => null,
            'source' => 'fallback',
            'model_used' => 'rule_based',
        ];
    }

    /**
     * Helper methods for fallback report generation
     */
    private function calculateOverallRating(array $requests, array $supply, array $shortages): string
    {
        $score = 0;

        // Approval rate scoring
        if ($requests['approval_rate'] >= 90) $score += 3;
        elseif ($requests['approval_rate'] >= 70) $score += 2;
        elseif ($requests['approval_rate'] >= 50) $score += 1;

        // Supply adequacy scoring
        if ($supply['out_of_stock_items'] == 0 && $supply['low_stock_items'] <= 2) $score += 3;
        elseif ($supply['out_of_stock_items'] <= 2 && $supply['low_stock_items'] <= 5) $score += 2;
        elseif ($supply['out_of_stock_items'] <= 5) $score += 1;

        // Shortage impact scoring
        if ($shortages['critical_shortages'] == 0) $score += 3;
        elseif ($shortages['critical_shortages'] <= 2) $score += 2;
        elseif ($shortages['critical_shortages'] <= 5) $score += 1;

        if ($score >= 8) return 'Excellent';
        if ($score >= 6) return 'Good';
        if ($score >= 4) return 'Fair';
        return 'Poor';
    }

    private function generateFallbackSummary(array $data): string
    {
        $requests = $data['requests_data'];
        $period = $data['period']['month'];

        return "During {$period}, the agricultural system processed {$requests['total_requests']} seedling requests with an approval rate of {$requests['approval_rate']}%. " .
               "Supply levels show {$data['supply_data']['out_of_stock_items']} out-of-stock items and {$data['shortage_analysis']['critical_shortages']} critical shortages requiring immediate attention.";
    }

    private function generateFallbackFindings(array $data): array
    {
        $findings = [];
        $requests = $data['requests_data'];
        $supply = $data['supply_data'];

        if ($requests['approval_rate'] >= 80) {
            $findings[] = "High approval rate of {$requests['approval_rate']}% indicates efficient processing";
        } else {
            $findings[] = "Approval rate of {$requests['approval_rate']}% below optimal threshold";
        }

        if ($supply['out_of_stock_items'] > 0) {
            $findings[] = "{$supply['out_of_stock_items']} items are completely out of stock";
        }

        if ($data['shortage_analysis']['critical_shortages'] > 0) {
            $findings[] = "{$data['shortage_analysis']['critical_shortages']} critical shortages identified";
        }

        return $findings;
    }

    private function assessApprovalEfficiency(array $requests): string
    {
        $rate = $requests['approval_rate'];
        $time = $requests['average_processing_time'];

        if ($rate >= 85 && $time <= 3) return 'Excellent efficiency with high approval rate and fast processing';
        if ($rate >= 70 && $time <= 5) return 'Good efficiency with acceptable metrics';
        if ($rate >= 50 && $time <= 7) return 'Fair efficiency with room for improvement';
        return 'Poor efficiency requiring immediate attention';
    }

    private function assessSupplyAdequacy(array $supply, array $shortages): string
    {
        if ($supply['out_of_stock_items'] == 0 && $shortages['critical_shortages'] == 0) {
            return 'Adequate supply levels meeting current demand';
        }
        if ($supply['out_of_stock_items'] <= 2 && $shortages['critical_shortages'] <= 1) {
            return 'Generally adequate supply with minor gaps';
        }
        return 'Inadequate supply levels with significant shortages';
    }

    private function assessGeographicCoverage(array $barangays): string
    {
        $total = $barangays['total_barangays'];

        if ($total >= 15) return 'Excellent geographic coverage across ' . $total . ' barangays';
        if ($total >= 10) return 'Good geographic coverage across ' . $total . ' barangays';
        if ($total >= 5) return 'Fair geographic coverage across ' . $total . ' barangays';
        return 'Limited geographic coverage requiring expansion';
    }

    private function identifyCriticalIssues(array $data): array
    {
        $issues = [];

        if ($data['shortage_analysis']['critical_shortages'] > 0) {
            $issues[] = "Critical supply shortages in {$data['shortage_analysis']['critical_shortages']} items (Severity: HIGH)";
        }

        if ($data['requests_data']['approval_rate'] < 60) {
            $issues[] = "Low approval rate of {$data['requests_data']['approval_rate']}% (Severity: MEDIUM)";
        }

        if ($data['requests_data']['average_processing_time'] > 7) {
            $issues[] = "Extended processing time of {$data['requests_data']['average_processing_time']} days (Severity: MEDIUM)";
        }

        return $issues;
    }

    private function generateFallbackRecommendations(array $data): array
    {
        return [
            'immediate_actions' => [
                'Restock critical shortage items within 7 days',
                'Process pending requests within 3 days',
            ],
            'short_term_strategies' => [
                'Implement automated inventory alerts (Timeline: 1 month)',
                'Establish supplier agreements for fast restocking (Timeline: 2 months)',
            ],
            'long_term_improvements' => [
                'Develop demand forecasting system (Timeline: 6 months)',
                'Expand distribution network to underserved areas (Timeline: 4-6 months)',
            ],
        ];
    }

    private function extractSection(string $text, string $section): string
    {
        // Simple text extraction for fallback
        $lines = explode('\n', $text);
        return implode(' ', array_slice($lines, 0, 3));
    }
}
