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
        $this->model           = config('services.anthropic.model', 'claude-sonnet-4-5-20250929');
        $this->maxTokens       = config('services.anthropic.max_tokens', 8192);
        $this->temperature     = config('services.anthropic.temperature', 0.1);

        if (empty($this->anthropicApiKey)) {
            Log::warning('Anthropic API key not configured. Using fallback report generation.');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // CONFIDENCE SCORING
    // Quarterly reports use 3× higher sample-size thresholds so that
    // confidence reflects data quality, not just a longer time window.
    // ─────────────────────────────────────────────────────────────────

    /**
     * Calculate confidence level based on data quality and completeness.
     * Accepts an optional $periodMode ('monthly'|'quarterly') to adjust thresholds.
     */
    private function calculateConfidenceLevel(array $data, string $periodMode = 'monthly'): array
    {
        $isSupplies = isset($data['requests_data'], $data['supply_data']);
        $isTraining = isset($data['training_stats'], $data['training_by_type']);
        $isRsbsa    = isset($data['rsbsa_stats'], $data['rsbsa_demographics']);
        $isFishr    = isset($data['fishr_stats'], $data['fishr_by_livelihood']);
        $isBoatr    = isset($data['boatr_stats'], $data['boatr_by_boat_type']);

        // Multiplier applied to sample-size breakpoints for quarterly mode
        $q = $periodMode === 'quarterly' ? 3 : 1;

        if ($isSupplies) {
            $score = $this->scoreSuppliesConfidence($data, $q);
        } elseif ($isTraining) {
            $score = $this->scoreTrainingConfidence($data, $q);
        } elseif ($isRsbsa) {
            $score = $this->scoreRsbsaConfidence($data, $q);
        } elseif ($isFishr) {
            $score = $this->scoreFishrConfidence($data, $q);
        } elseif ($isBoatr) {
            $score = $this->scoreBoatrConfidence($data, $q);
        } else {
            $score = 60;
        }

        // Map raw score (0–100) → display range (85–99)
        $finalScore = (int) round(85 + ($score / 100) * 14);
        $finalScore = max(85, min(99, $finalScore));

        return [
            'level' => $finalScore >= 93 ? 'High' : 'Medium-High',
            'score' => $finalScore,
        ];
    }

    // ── SUPPLIES ───────────────────────────────────────────────────────
    private function scoreSuppliesConfidence(array $data, int $q = 1): float
    {
        $score  = 0;
        $req    = $data['requests_data'] ?? [];
        $supply = $data['supply_data']   ?? [];
        $brgy   = $data['barangay_analysis'] ?? [];

        if (($req['total_requests'] ?? 0) === 0) {
            $supFields = ['available_stock','low_stock_items','out_of_stock_items','total_items','supply_health_score'];
            $present   = count(array_filter($supFields, fn ($f) => isset($supply[$f])));
            $score     = ($present / count($supFields)) * 15;
            $score    += ($supply['total_items'] ?? 0) >= 10 ? 8 : 0;
            $score    += ($supply['available_stock'] ?? 0) > 0 ? 4 : 0;
            return min(60, $score);
        }

        // Completeness (30 pts)
        $reqFields = ['total_requests','approved_requests','rejected_requests','pending_requests','approval_rate'];
        $present   = count(array_filter($reqFields, fn ($f) => isset($req[$f])));
        $score    += ($present / count($reqFields)) * 15;

        $supFields = ['available_stock','low_stock_items','out_of_stock_items','total_items','supply_health_score'];
        $present   = count(array_filter($supFields, fn ($f) => isset($supply[$f])));
        $score    += ($present / count($supFields)) * 15;

        // Sample size (25 pts) — thresholds scale with $q
        $total = $req['total_requests'] ?? 0;
        $score += match (true) {
            $total >= 50 * $q => 25,
            $total >= 20 * $q => 20,
            $total >= 10 * $q => 15,
            $total >= 5  * $q => 10,
            $total >= 1       => 6,
            default           => 0,
        };

        // Geographic coverage (20 pts)
        $barangays = count($brgy['barangay_details'] ?? []);
        $score    += match (true) {
            $barangays >= 15 => 20,
            $barangays >= 10 => 17,
            $barangays >= 5  => 13,
            $barangays >= 3  => 9,
            $barangays >= 1  => 5,
            default          => 0,
        };

        // Supply catalog integrity (15 pts)
        $totalItems = $supply['total_items'] ?? 0;
        $score += match (true) { $totalItems >= 10 => 8, $totalItems >= 5 => 5, $totalItems >= 1 => 2, default => 0 };
        if (($supply['available_stock'] ?? 0) > 0) $score += 4;
        if (($supply['supply_health_score'] ?? 0) > 0) $score += 3;

        // Consistency (10 pts)
        $penalty = 0;
        if (isset($req['total_requests'], $req['approved_requests'], $req['approval_rate']) && $req['total_requests'] > 0) {
            $calc = ($req['approved_requests'] / $req['total_requests']) * 100;
            if (abs($calc - $req['approval_rate']) > 5) $penalty += 5;
        }
        if (isset($req['total_requests'], $req['approved_requests'], $req['rejected_requests'], $req['pending_requests'])) {
            $sum = $req['approved_requests'] + $req['rejected_requests'] + $req['pending_requests'];
            if (abs($sum - $req['total_requests']) > 1) $penalty += 5;
        }
        $score += max(0, 10 - $penalty);

        return min(100, $score);
    }

    // ── TRAINING ───────────────────────────────────────────────────────
    private function scoreTrainingConfidence(array $data, int $q = 1): float
    {
        $stats  = $data['training_stats']       ?? [];
        $byType = $data['training_by_type']     ?? [];
        $byBrgy = $data['training_by_barangay'] ?? [];

        if (($stats['total_applications'] ?? 0) === 0) {
            $fields  = ['total_applications','approved','rejected','pending','approval_rate'];
            $present = count(array_filter($fields, fn ($f) => isset($stats[$f])));
            return min(45, ($present / count($fields)) * 30);
        }

        $score   = 0;
        $fields  = ['total_applications','approved','rejected','pending','approval_rate'];
        $present = count(array_filter($fields, fn ($f) => isset($stats[$f])));
        $score  += ($present / count($fields)) * 30;

        $total   = $stats['total_applications'] ?? 0;
        $score  += match (true) {
            $total >= 50 * $q => 25, $total >= 20 * $q => 20, $total >= 10 * $q => 15,
            $total >= 5  * $q => 10, $total >= 1       => 6,  default           => 0,
        };

        $types   = count($byType['distribution'] ?? []);
        $score  += match (true) { $types >= 6 => 20, $types >= 4 => 16, $types >= 2 => 11, $types >= 1 => 6, default => 0 };

        $barangays = $byBrgy['total_barangays_covered'] ?? 0;
        $score    += match (true) { $barangays >= 10 => 15, $barangays >= 5 => 12, $barangays >= 3 => 8, $barangays >= 1 => 4, default => 0 };

        $penalty = 0;
        if (isset($stats['total_applications'], $stats['approved'], $stats['approval_rate']) && $stats['total_applications'] > 0) {
            $calc = ($stats['approved'] / $stats['total_applications']) * 100;
            if (abs($calc - $stats['approval_rate']) > 5) $penalty += 10;
        }
        $score += max(0, 10 - $penalty);

        return min(100, $score);
    }

    // ── RSBSA ──────────────────────────────────────────────────────────
    private function scoreRsbsaConfidence(array $data, int $q = 1): float
    {
        $stats        = $data['rsbsa_stats']        ?? [];
        $demographics = $data['rsbsa_demographics'] ?? [];
        $byCommodity  = $data['rsbsa_by_commodity'] ?? [];
        $byBrgy       = $data['rsbsa_by_barangay']  ?? [];
        $landAnalysis = $data['rsbsa_land_analysis'] ?? [];

        if (($stats['total_applications'] ?? 0) === 0) {
            $fields  = ['total_applications','approved','rejected','pending','approval_rate','total_land_area','avg_land_area'];
            $present = count(array_filter($fields, fn ($f) => isset($stats[$f])));
            return min(45, ($present / count($fields)) * 30);
        }

        $score   = 0;
        $fields  = ['total_applications','approved','rejected','pending','approval_rate','total_land_area','avg_land_area'];
        $present = count(array_filter($fields, fn ($f) => isset($stats[$f])));
        $score  += ($present / count($fields)) * 20;

        if (isset($demographics['male_count'], $demographics['female_count']) &&
            ($demographics['male_count'] + $demographics['female_count']) > 0) {
            $score += 10;
        }

        $total  = $stats['total_applications'] ?? 0;
        $score += match (true) {
            $total >= 100 * $q => 25, $total >= 50 * $q => 21, $total >= 20 * $q => 16,
            $total >= 10  * $q => 11, $total >= 1        => 6,  default           => 0,
        };

        $commodities = count($byCommodity['distribution'] ?? []);
        $score      += match (true) { $commodities >= 5 => 15, $commodities >= 3 => 11, $commodities >= 1 => 6, default => 0 };

        $barangays   = $byBrgy['total_barangays_covered'] ?? 0;
        $score      += match (true) { $barangays >= 15 => 15, $barangays >= 10 => 12, $barangays >= 5 => 8, $barangays >= 1 => 4, default => 0 };

        if (isset($landAnalysis['total_farms']) && $landAnalysis['total_farms'] > 0) $score += 5;

        $penalty = 0;
        if (isset($stats['total_applications'], $stats['approved'], $stats['approval_rate']) && $stats['total_applications'] > 0) {
            $calc = ($stats['approved'] / $stats['total_applications']) * 100;
            if (abs($calc - $stats['approval_rate']) > 5) $penalty += 10;
        }
        $score += max(0, 10 - $penalty);

        return min(100, $score);
    }

    // ── FISHR ──────────────────────────────────────────────────────────
    private function scoreFishrConfidence(array $data, int $q = 1): float
    {
        $stats        = $data['fishr_stats']         ?? [];
        $byLivelihood = $data['fishr_by_livelihood'] ?? [];
        $byBrgy       = $data['fishr_by_barangay']   ?? [];
        $demographics = $data['fishr_demographics']  ?? [];
        $trends       = $data['fishr_trends']         ?? [];

        if (($stats['total_applications'] ?? 0) === 0) {
            $fields  = ['total_applications','approved','rejected','pending','approval_rate','with_fishr_number'];
            $present = count(array_filter($fields, fn ($f) => isset($stats[$f])));
            return min(45, ($present / count($fields)) * 30);
        }

        $score   = 0;
        $fields  = ['total_applications','approved','rejected','pending','approval_rate','with_fishr_number'];
        $present = count(array_filter($fields, fn ($f) => isset($stats[$f])));
        $score  += ($present / count($fields)) * 20;

        if (isset($demographics['male_count'], $demographics['female_count'])) $score += 10;

        $total  = $stats['total_applications'] ?? 0;
        $score += match (true) {
            $total >= 100 * $q => 25, $total >= 50 * $q => 21, $total >= 20 * $q => 16,
            $total >= 10  * $q => 11, $total >= 1        => 6,  default           => 0,
        };

        $types  = count($byLivelihood['distribution'] ?? []);
        $score += match (true) { $types >= 5 => 15, $types >= 3 => 11, $types >= 1 => 6, default => 0 };

        $barangays = $byBrgy['total_barangays_covered'] ?? 0;
        $score    += match (true) { $barangays >= 15 => 15, $barangays >= 10 => 12, $barangays >= 5 => 8, $barangays >= 1 => 4, default => 0 };

        if (isset($trends['current_month_total'], $trends['previous_month_total'])) $score += 5;

        $penalty = 0;
        if (isset($stats['total_applications'], $stats['approved'], $stats['approval_rate']) && $stats['total_applications'] > 0) {
            $calc = ($stats['approved'] / $stats['total_applications']) * 100;
            if (abs($calc - $stats['approval_rate']) > 5) $penalty += 10;
        }
        $score += max(0, 10 - $penalty);

        return min(100, $score);
    }

    // ── BOATR ──────────────────────────────────────────────────────────
    private function scoreBoatrConfidence(array $data, int $q = 1): float
    {
        $stats      = $data['boatr_stats']               ?? [];
        $byBoatType = $data['boatr_by_boat_type']        ?? [];
        $byBrgy     = $data['boatr_by_barangay']         ?? [];
        $inspection = $data['boatr_inspection_analysis'] ?? [];
        $engine     = $data['boatr_engine_analysis']     ?? [];
        $trends     = $data['boatr_trends']              ?? [];

        if (($stats['total_applications'] ?? 0) === 0) {
            $fields  = ['total_applications','approved','rejected','pending','approval_rate','avg_boat_length','avg_horsepower','inspection_rate'];
            $present = count(array_filter($fields, fn ($f) => isset($stats[$f])));
            return min(45, ($present / count($fields)) * 30);
        }

        $score   = 0;
        $fields  = ['total_applications','approved','rejected','pending','approval_rate','avg_boat_length','avg_horsepower','inspection_rate'];
        $present = count(array_filter($fields, fn ($f) => isset($stats[$f])));
        $score  += ($present / count($fields)) * 30;

        $total  = $stats['total_applications'] ?? 0;
        $score += match (true) {
            $total >= 100 * $q => 25, $total >= 50 * $q => 21, $total >= 20 * $q => 16,
            $total >= 10  * $q => 11, $total >= 1        => 6,  default           => 0,
        };

        $types  = count($byBoatType['distribution'] ?? []);
        $score += match (true) { $types >= 4 => 15, $types >= 2 => 11, $types >= 1 => 6, default => 0 };

        $barangays = $byBrgy['total_barangays_covered'] ?? 0;
        $score    += match (true) { $barangays >= 10 => 10, $barangays >= 5 => 8, $barangays >= 1 => 4, default => 0 };

        $inspectionRate = $inspection['completion_rate'] ?? 0;
        $score         += match (true) { $inspectionRate >= 80 => 10, $inspectionRate >= 50 => 7, $inspectionRate >= 1 => 4, default => 0 };

        if (!empty($engine['engine_types'])) $score += 5;
        if (isset($trends['current_month_total'], $trends['previous_month_total'])) $score += 5;

        return min(100, $score);
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPERS — extract period_mode from data array
    // ─────────────────────────────────────────────────────────────────

    private function getPeriodMode(array $data): string
    {
        return $data['period']['period_mode'] ?? 'monthly';
    }

    // ─────────────────────────────────────────────────────────────────
    // SUPPLIES REPORT
    // ─────────────────────────────────────────────────────────────────

    public function generateReport(array $data): array
    {
        $cacheKey      = 'dss_report_' . md5(serialize($data));
        $cacheDuration = config('services.anthropic.cache_duration', 3600);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($data) {
            try {
                if (!empty($this->anthropicApiKey)) {
                    return $this->generateLLMReport($data);
                }
                return $this->generateFallbackReport($data);
            } catch (\Exception $e) {
                Log::error('DSS Report Generation Failed', ['error' => $e->getMessage()]);
                return $this->generateFallbackReport($data);
            }
        });
    }

    private function generateLLMReport(array $data): array
    {
        $prompt   = $this->buildPrompt($data);
        $response = Http::withHeaders([
            'Content-Type'      => 'application/json',
            'x-api-key'         => $this->anthropicApiKey,
            'anthropic-version' => '2023-06-01',
        ])->timeout(300)->post($this->anthropicApiUrl, [
            'model'      => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature'=> $this->temperature,
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ]);

        if ($response->successful()) {
            $generatedText = $response->json()['content'][0]['text'] ?? '';
            return $this->parseGeneratedReport($generatedText, $data);
        }

        throw new \Exception('LLM API request failed: ' . $response->body());
    }

    private function buildPrompt(array $data): string
    {
        $period    = $data['period'];
        $requests  = $data['requests_data'];
        $supply    = $data['supply_data'];
        $barangays = $data['barangay_analysis'];
        $shortages = $data['shortage_analysis'];
        $periodMode= $period['period_mode'] ?? 'monthly';

        $periodContext = $periodMode === 'quarterly'
            ? "This is a QUARTERLY report covering {$period['month']}. Trends and patterns are based on 3 months of aggregated data."
            : "This is a monthly report for {$period['month']}.";

        return "
You are an agricultural decision support assistant for the AgriSys platform.
Analyze the following data and generate a comprehensive report with both descriptive analysis and prescriptive recommendations.

{$periodContext}

## SUPPLY REQUESTS OVERVIEW
- Total Requests: {$requests['total_requests']}
- Approved: {$requests['approved_requests']}  |  Rejected: {$requests['rejected_requests']}  |  Pending: {$requests['pending_requests']}
- Approval Rate: {$requests['approval_rate']}%
- Total Items Requested: {$requests['total_items_requested']}
- Average Processing Time: {$requests['average_processing_time']}

## SUPPLY STATUS
- Available Stock: {$supply['available_stock']} units  |  Total Item Categories: {$supply['total_items']}
- Low Stock Items: {$supply['low_stock_items']}  |  Out of Stock: {$supply['out_of_stock_items']}
- Supply Health Score: {$supply['supply_health_score']}/100
- Overall Supply Status: {$supply['supply_summary']['overall_status']}
- Critical Items: {$supply['critical_items']}  |  Needs Reorder: {$supply['needs_reorder']}
- Categories Needing Attention: {$this->formatList($supply['supply_summary']['concern_categories'] ?? [])}

## BARANGAY DEMAND ANALYSIS
" . $this->formatBarangayData($barangays['barangay_details']) . "

## SHORTAGE ANALYSIS
- Critical Shortages: {$shortages['critical_shortages']}
- Total Shortage Value: {$shortages['total_shortage_value']} units
" . $this->formatShortageData($shortages['shortages']) . "

## REQUIRED OUTPUT FORMAT
```json
{
    \"executive_summary\": \"Brief 2-3 sentence overview\",
    \"key_findings\": [\"Finding 1\", \"Finding 2\", \"Finding 3\"],
    \"performance_assessment\": {
        \"overall_rating\": \"Excellent/Good/Fair/Poor\",
        \"approval_efficiency\": \"Assessment of approval rate and processing time\",
        \"supply_adequacy\": \"Assessment of stock levels vs demand\",
        \"geographic_coverage\": \"Assessment of barangay distribution\"
    },
    \"critical_issues\": [\"Issue 1 with severity\", \"Issue 2 with severity\"],
    \"recommendations\": {
        \"immediate_actions\": [\"Action 1 (Timeline: X days)\"],
        \"short_term_strategies\": [\"Strategy 1 (Timeline: 1-3 months)\"]
    },
    \"allocation_priorities\": [{\"barangay\": \"Name\", \"priority_level\": \"High/Medium/Low\", \"recommended_allocation\": \"X units\", \"justification\": \"Reason\"}],
    \"next_month_forecast\": {\"expected_demand\": \"Predicted demand\", \"recommended_stock_levels\": \"Suggested targets\", \"focus_areas\": [\"Area 1\"]},
    \"confidence_level\": \"85%\",
    \"confidence_score\": 85
}
```

Focus on actionable insights for supply distribution, supply management, and resource allocation.
";
    }

    private function formatBarangayData(array $barangays): string
    {
        $formatted = '';
        foreach (array_slice($barangays, 0, 10) as $b) {
            $formatted .= "- {$b['name']}: {$b['requests']} requests, {$b['total_quantity']} units (Priority: {$b['priority_level']})\n";
        }
        return $formatted;
    }

    private function formatShortageData(array $shortages): string
    {
        $formatted = '';
        foreach (array_slice($shortages, 0, 10) as $s) {
            $formatted .= "- {$s['item']}: Shortage of {$s['shortage']} units (Severity: {$s['severity']})\n";
        }
        return $formatted;
    }

    private function sanitizeOverallRating(string $rating): string
    {
        $lower = strtolower(trim($rating));

        if (str_contains($lower, 'excellent')) return 'Excellent';
        if (str_contains($lower, 'good'))      return 'Good';
        if (str_contains($lower, 'fair'))      return 'Fair';
        if (str_contains($lower, 'poor'))      return 'Poor';
        if (str_contains($lower, 'critical'))  return 'Poor';
        if (str_contains($lower, 'needs improvement')) return 'Poor';

        return 'Fair'; // safe default
    }

    private function parseGeneratedReport(string $generatedText, array $data): array
    {
        preg_match('/```json\s*(.*?)\s*```/s', $generatedText, $matches);

        if (!empty($matches[1])) {
            $jsonData = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE) {

                if (isset($jsonData['performance_assessment']['overall_rating'])) {
                    $jsonData['performance_assessment']['overall_rating'] =
                        $this->sanitizeOverallRating($jsonData['performance_assessment']['overall_rating']);
                }
                $calculatedConfidence = $this->calculateConfidenceLevel($data, $this->getPeriodMode($data));
                $jsonData['confidence_level']  = $calculatedConfidence['level'];
                $jsonData['confidence_score']  = $calculatedConfidence['score'];
                $jsonData['confidence_source'] = 'calculated';

                return [
                    'generated_at' => now()->toDateTimeString(),
                    'period'       => $data['period']['month'],
                    'report_data'  => $jsonData,
                    'raw_response' => $generatedText,
                    'source'       => 'llm',
                    'model_used'   => $this->model,
                ];
            }
        }

        $calculatedConfidence = $this->calculateConfidenceLevel($data, $this->getPeriodMode($data));

        return [
            'generated_at' => now()->toDateTimeString(),
            'period'       => $data['period']['month'],
            'report_data'  => [
                'executive_summary' => $this->extractSection($generatedText, 'executive_summary'),
                'confidence_level'  => $calculatedConfidence['level'],
                'confidence_score'  => $calculatedConfidence['score'],
                'confidence_source' => 'calculated_fallback',
                'raw_analysis'      => $generatedText,
            ],
            'raw_response' => $generatedText,
            'source'       => 'llm_text',
            'model_used'   => $this->model,
        ];
    }

    private function generateFallbackReport(array $data): array
    {
        $requests  = $data['requests_data'];
        $supply    = $data['supply_data'];
        $shortages = $data['shortage_analysis'];
        $confidence= $this->calculateConfidenceLevel($data, $this->getPeriodMode($data));

        return [
            'generated_at' => now()->toDateTimeString(),
            'period'       => $data['period']['month'],
            'report_data'  => [
                'executive_summary'      => $this->generateFallbackSummary($data),
                'key_findings'           => $this->generateFallbackFindings($data),
                'performance_assessment' => [
                    'overall_rating'      => $this->calculateOverallRating($requests, $supply, $shortages),
                    'approval_efficiency' => $this->assessApprovalEfficiency($requests),
                    'supply_adequacy'     => $this->assessSupplyAdequacy($supply, $shortages),
                    'geographic_coverage' => $this->assessGeographicCoverage($data['barangay_analysis']),
                ],
                'critical_issues'        => $this->identifyCriticalIssues($data),
                'recommendations'        => $this->generateFallbackRecommendations($data),
                'confidence_level'       => $confidence['level'],
                'confidence_score'       => $confidence['score'],
                'confidence_source'      => 'calculated',
            ],
            'raw_response' => null,
            'source'       => 'fallback',
            'model_used'   => 'rule_based',
        ];
    }

    private function calculateOverallRating(array $requests, array $supply, array $shortages): string
    {
        $score = 0;
        $approvalRate = $requests['approval_rate'] ?? 0;
        if ($approvalRate >= 90) $score += 3; elseif ($approvalRate >= 70) $score += 2; elseif ($approvalRate >= 50) $score += 1;

        $outOfStock = $supply['out_of_stock_items'] ?? 0;
        $lowStock   = $supply['low_stock_items'] ?? 0;
        if ($outOfStock == 0 && $lowStock <= 2) $score += 3; elseif ($outOfStock <= 2 && $lowStock <= 5) $score += 2; elseif ($outOfStock <= 5) $score += 1;

        $criticalShortages = $shortages['critical_shortages'] ?? 0;
        if ($criticalShortages == 0) $score += 3; elseif ($criticalShortages <= 2) $score += 2; elseif ($criticalShortages <= 5) $score += 1;

        if ($score >= 8) return 'Excellent';
        if ($score >= 6) return 'Good';
        if ($score >= 4) return 'Fair';
        return 'Poor';
    }

    private function generateFallbackSummary(array $data): string
    {
        $requests          = $data['requests_data'];
        $period            = $data['period']['month'];
        $totalRequests     = $requests['total_requests'] ?? 0;
        $approvalRate      = $requests['approval_rate'] ?? 0;
        $outOfStock        = $data['supply_data']['out_of_stock_items'] ?? 0;
        $criticalShortages = $data['shortage_analysis']['critical_shortages'] ?? 0;

        return "During {$period}, the agricultural system processed {$totalRequests} supply requests with an approval rate of {$approvalRate}%. " .
               "Supply levels show {$outOfStock} out-of-stock items and {$criticalShortages} critical shortages requiring immediate attention.";
    }

    private function generateFallbackFindings(array $data): array
    {
        $findings = [];
        $requests = $data['requests_data'];
        $supply   = $data['supply_data'];

        $approvalRate      = $requests['approval_rate'] ?? 0;
        $outOfStock        = $supply['out_of_stock_items'] ?? 0;
        $criticalShortages = $data['shortage_analysis']['critical_shortages'] ?? 0;

        if ($approvalRate >= 80) $findings[] = "High approval rate of {$approvalRate}% indicates efficient processing";
        else $findings[] = "Approval rate of {$approvalRate}% below optimal threshold";

        if ($outOfStock > 0)        $findings[] = "{$outOfStock} items are completely out of stock";
        if ($criticalShortages > 0) $findings[] = "{$criticalShortages} critical shortages identified";

        return $findings;
    }

    private function assessApprovalEfficiency(array $requests): string
    {
        $rate = $requests['approval_rate'] ?? 0;
        $time = $requests['average_processing_time'] ?? 999;
        if ($rate >= 85 && $time <= 3) return 'Excellent efficiency with high approval rate and fast processing';
        if ($rate >= 70 && $time <= 5) return 'Good efficiency with acceptable metrics';
        if ($rate >= 50 && $time <= 7) return 'Fair efficiency with room for improvement';
        return 'Poor efficiency requiring immediate attention';
    }

    private function assessSupplyAdequacy(array $supply, array $shortages): string
    {
        $outOfStock        = $supply['out_of_stock_items'] ?? 0;
        $criticalShortages = $shortages['critical_shortages'] ?? 0;
        if ($outOfStock == 0 && $criticalShortages == 0) return 'Adequate supply levels meeting current demand';
        if ($outOfStock <= 2 && $criticalShortages <= 1) return 'Generally adequate supply with minor gaps';
        return 'Inadequate supply levels with significant shortages';
    }

    private function assessGeographicCoverage(array $barangays): string
    {
        $total = $barangays['total_barangays'];
        if ($total >= 15) return 'Excellent geographic coverage across ' . $total . ' barangays';
        if ($total >= 10) return 'Good geographic coverage across ' . $total . ' barangays';
        if ($total >= 5)  return 'Fair geographic coverage across ' . $total . ' barangays';
        return 'Limited geographic coverage requiring expansion';
    }

    private function identifyCriticalIssues(array $data): array
    {
        $issues            = [];
        $criticalShortages = $data['shortage_analysis']['critical_shortages'] ?? 0;
        $approvalRate      = $data['requests_data']['approval_rate'] ?? 0;
        $processingTime    = $data['requests_data']['average_processing_time'] ?? 0;

        if ($criticalShortages > 0)  $issues[] = "Critical supply shortages in {$criticalShortages} items (Severity: HIGH)";
        if ($approvalRate < 60)      $issues[] = "Low approval rate of {$approvalRate}% (Severity: MEDIUM)";
        if ($processingTime > 7)     $issues[] = "Extended processing time of {$processingTime} days (Severity: MEDIUM)";

        return $issues;
    }

    private function generateFallbackRecommendations(array $data): array
    {
        return [
            'immediate_actions'    => ['Restock critical shortage items within 7 days', 'Process pending requests within 3 days'],
            'short_term_strategies'=> ['Implement automated supply alerts (Timeline: 1 month)', 'Establish supplier agreements for fast restocking (Timeline: 2 months)'],
        ];
    }

    private function extractSection(string $text, string $section): string
    {
        $lines = explode('\n', $text);
        return implode(' ', array_slice($lines, 0, 3));
    }

    private function formatList(array $items): string
    {
        return !empty($items) ? implode(', ', $items) : 'None';
    }

    // ─────────────────────────────────────────────────────────────────
    // TRAINING REPORT
    // ─────────────────────────────────────────────────────────────────

    public function generateTrainingReport(array $data): array
    {
        $cacheKey = 'dss_training_report_' . md5(serialize($data));
        return Cache::remember($cacheKey, config('services.anthropic.cache_duration', 3600), function () use ($data) {
            try {
                if (!empty($this->anthropicApiKey)) return $this->generateTrainingLLMReport($data);
                return $this->generateTrainingFallbackReport($data);
            } catch (\Exception $e) {
                Log::error('Training DSS Report Generation Failed', ['error' => $e->getMessage()]);
                return $this->generateTrainingFallbackReport($data);
            }
        });
    }

    private function generateTrainingLLMReport(array $data): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json', 'x-api-key' => $this->anthropicApiKey, 'anthropic-version' => '2023-06-01',
        ])->timeout(300)->post($this->anthropicApiUrl, [
            'model' => $this->model, 'max_tokens' => $this->maxTokens, 'temperature' => $this->temperature,
            'messages' => [['role' => 'user', 'content' => $this->buildTrainingPrompt($data)]],
        ]);

        if ($response->successful()) {
            return $this->parseGeneratedReport($response->json()['content'][0]['text'] ?? '', $data);
        }
        throw new \Exception('Training LLM API request failed');
    }

    private function buildTrainingPrompt(array $data): string
    {
        $period     = $data['period'];
        $stats      = $data['training_stats'];
        $byType     = $data['training_by_type'];
        $byBarangay = $data['training_by_barangay'];
        $periodMode = $period['period_mode'] ?? 'monthly';

        $periodContext = $periodMode === 'quarterly'
            ? "This is a QUARTERLY report covering {$period['month']}. Identify trends and patterns across the 3-month period."
            : "Period: {$period['month']}";

        return <<<PROMPT
You are an agricultural training program analyst for San Pedro, Laguna, Philippines.

{$periodContext}

TRAINING STATISTICS:
- Total Applications: {$stats['total_applications']}
- Approved: {$stats['approved']} ({$stats['approval_rate']}%)
- Rejected: {$stats['rejected']} ({$stats['rejection_rate']}%)
- Pending: {$stats['pending']}
- Average Processing Time: {$stats['avg_processing_time']}

TRAINING TYPES REQUESTED:
{$this->formatTrainingTypes($byType)}

GEOGRAPHIC DISTRIBUTION:
- Barangays Covered: {$byBarangay['total_barangays_covered']}
- Top Requesting Barangays: {$this->formatBarangays($byBarangay['top_barangays'])}

RESPOND IN THIS EXACT JSON FORMAT:
{
    "executive_summary": "string",
    "performance_assessment": {
        "overall_rating": "string",
        "approval_efficiency": "string",
        "training_diversity": "string",
        "geographic_reach": "string"
    },
    "key_findings": ["string"],
    "critical_issues": ["string"],
    "recommendations": {
        "immediate_actions": ["string"],
        "short_term_strategies": ["string"],
        "long_term_vision": ["string"]
    },
    "training_insights": ["string"],
    "confidence_level": "High/Medium/Low",
    "confidence_score": 85
}
PROMPT;
    }

    private function generateTrainingFallbackReport(array $data): array
    {
        $stats      = $data['training_stats'];
        $byType     = $data['training_by_type'];
        $confidence = $this->calculateConfidenceLevel($data, $this->getPeriodMode($data));
        $rating     = $stats['approval_rate'] >= 80 ? 'Excellent' : ($stats['approval_rate'] >= 60 ? 'Good' : ($stats['approval_rate'] >= 40 ? 'Fair' : 'Poor'));

        return [
            'source'       => 'fallback',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'report_data'  => [
                'executive_summary'      => "Training program for {$data['period']['month']} processed {$stats['total_applications']} applications with a {$stats['approval_rate']}% approval rate.",
                'performance_assessment' => [
                    'overall_rating'     => $rating,
                    'approval_efficiency'=> 'Good',
                    'training_diversity' => count($byType['distribution']) > 5 ? 'High' : 'Medium',
                    'geographic_reach'   => 'Good',
                ],
                'key_findings'           => [
                    "Received {$stats['total_applications']} training applications",
                    "Approval rate: {$stats['approval_rate']}%",
                    "Most popular: " . ($byType['most_popular'][0]['display_name'] ?? 'N/A'),
                ],
                'critical_issues'        => $stats['pending'] > 10 ? ["High number of pending applications ({$stats['pending']})"] : [],
                'recommendations'        => ['immediate_actions' => ['Review pending applications'], 'short_term_strategies' => ['Expand high-demand training programs'], 'long_term_vision' => ['Develop comprehensive training curriculum']],
                'training_insights'      => ["Total training types requested: {$byType['total_types_requested']}"],
                'confidence_level'       => $confidence['level'],
                'confidence_score'       => $confidence['score'],
                'confidence_source'      => 'calculated',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // RSBSA REPORT
    // ─────────────────────────────────────────────────────────────────

    public function generateRsbsaReport(array $data): array
    {
        $cacheKey = 'dss_rsbsa_report_' . md5(serialize($data));
        return Cache::remember($cacheKey, config('services.anthropic.cache_duration', 3600), function () use ($data) {
            try {
                if (!empty($this->anthropicApiKey)) return $this->generateRsbsaLLMReport($data);
                return $this->generateRsbsaFallbackReport($data);
            } catch (\Exception $e) {
                Log::error('RSBSA DSS Report Generation Failed', ['error' => $e->getMessage()]);
                return $this->generateRsbsaFallbackReport($data);
            }
        });
    }

    private function generateRsbsaLLMReport(array $data): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json', 'x-api-key' => $this->anthropicApiKey, 'anthropic-version' => '2023-06-01',
        ])->timeout(300)->post($this->anthropicApiUrl, [
            'model' => $this->model, 'max_tokens' => $this->maxTokens, 'temperature' => $this->temperature,
            'messages' => [['role' => 'user', 'content' => $this->buildRsbsaPrompt($data)]],
        ]);

        if ($response->successful()) {
            return $this->parseGeneratedReport($response->json()['content'][0]['text'] ?? '', $data);
        }
        throw new \Exception('RSBSA LLM API request failed');
    }

    private function buildRsbsaPrompt(array $data): string
    {
        $period      = $data['period'];
        $stats       = $data['rsbsa_stats'];
        $byCommodity = $data['rsbsa_by_commodity'];
        $demographics= $data['rsbsa_demographics'];
        $landAnalysis= $data['rsbsa_land_analysis'];
        $periodMode  = $period['period_mode'] ?? 'monthly';

        $periodContext = $periodMode === 'quarterly'
            ? "This is a QUARTERLY report covering {$period['month']}. Highlight registration trends across the 3-month period."
            : "Period: {$period['month']}";

        return <<<PROMPT
You are an agricultural policy analyst for San Pedro, Laguna, Philippines.

{$periodContext}

RSBSA STATISTICS:
- Total Applications: {$stats['total_applications']}
- Approved: {$stats['approved']} ({$stats['approval_rate']}%)
- Rejected: {$stats['rejected']}  |  Pending: {$stats['pending']}
- Total Land Area: {$stats['total_land_area']}  |  Average Farm Size: {$stats['avg_land_area']}

FARMER DEMOGRAPHICS:
- Male: {$demographics['male_count']} ({$demographics['male_percentage']}%)
- Female: {$demographics['female_count']} ({$demographics['female_percentage']}%)

LAND DISTRIBUTION:
- Small Farms: {$landAnalysis['small_farms']}  |  Medium: {$landAnalysis['medium_farms']}  |  Large: {$landAnalysis['large_farms']}

TOP COMMODITIES:
{$this->formatCommodities($byCommodity)}

RESPOND IN THIS EXACT JSON FORMAT:
{
    "executive_summary": "string",
    "performance_assessment": {
        "overall_rating": "string",
        "registration_efficiency": "string",
        "agricultural_diversity": "string",
        "land_utilization": "string"
    },
    "key_findings": ["string"],
    "critical_issues": ["string"],
    "recommendations": {
        "immediate_actions": ["string"],
        "short_term_strategies": ["string"],
        "long_term_vision": ["string"]
    },
    "agricultural_insights": ["string"],
    "confidence_level": "High/Medium/Low",
    "confidence_score": 85
}
PROMPT;
    }

    private function generateRsbsaFallbackReport(array $data): array
    {
        $stats       = $data['rsbsa_stats'];
        $demographics= $data['rsbsa_demographics'];
        $confidence  = $this->calculateConfidenceLevel($data, $this->getPeriodMode($data));
        $rating      = $stats['approval_rate'] >= 80 ? 'Excellent' : ($stats['approval_rate'] >= 60 ? 'Good' : ($stats['approval_rate'] >= 40 ? 'Fair' : 'Poor'));

        return [
            'source'       => 'fallback',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'report_data'  => [
                'executive_summary'      => "RSBSA registration for {$data['period']['month']} processed {$stats['total_applications']} farmer applications covering {$stats['total_land_area']}.",
                'performance_assessment' => ['overall_rating' => $rating, 'registration_efficiency' => 'Good', 'agricultural_diversity' => 'Medium', 'land_utilization' => 'Fair'],
                'key_findings'           => ["Registered {$stats['total_applications']} farmers", "Gender distribution: {$demographics['male_percentage']}% male, {$demographics['female_percentage']}% female", "Average farm size: {$stats['avg_land_area']}"],
                'critical_issues'        => $stats['pending'] > 20 ? ["High number of pending registrations ({$stats['pending']})"] : [],
                'recommendations'        => ['immediate_actions' => ['Process pending registrations'], 'short_term_strategies' => ['Conduct farmer orientation programs'], 'long_term_vision' => ['Develop integrated farmer database system']],
                'agricultural_insights'  => ["Total land area registered: {$stats['total_land_area']}"],
                'confidence_level'       => $confidence['level'],
                'confidence_score'       => $confidence['score'],
                'confidence_source'      => 'calculated',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // FISHR REPORT
    // ─────────────────────────────────────────────────────────────────

    public function generateFishrReport(array $data): array
    {
        $cacheKey = 'dss_fishr_report_' . md5(serialize($data));
        return Cache::remember($cacheKey, config('services.anthropic.cache_duration', 3600), function () use ($data) {
            try {
                if (!empty($this->anthropicApiKey)) return $this->generateFishrLLMReport($data);
                return $this->generateFishrFallbackReport($data);
            } catch (\Exception $e) {
                Log::error('FISHR DSS Report Generation Failed', ['error' => $e->getMessage()]);
                return $this->generateFishrFallbackReport($data);
            }
        });
    }

    private function generateFishrLLMReport(array $data): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json', 'x-api-key' => $this->anthropicApiKey, 'anthropic-version' => '2023-06-01',
        ])->timeout(300)->post($this->anthropicApiUrl, [
            'model' => $this->model, 'max_tokens' => $this->maxTokens, 'temperature' => $this->temperature,
            'messages' => [['role' => 'user', 'content' => $this->buildFishrPrompt($data)]],
        ]);

        if ($response->successful()) {
            return $this->parseGeneratedFishrReport($response->json()['content'][0]['text'] ?? '', $data);
        }
        throw new \Exception('LLM API request failed: ' . $response->body());
    }

    private function buildFishrPrompt(array $data): string
    {
        $period       = $data['period'];
        $stats        = $data['fishr_stats'];
        $byLivelihood = $data['fishr_by_livelihood'];
        $byBarangay   = $data['fishr_by_barangay'];
        $demographics = $data['fishr_demographics'];
        $trends       = $data['fishr_trends'];
        $periodMode   = $period['period_mode'] ?? 'monthly';

        $periodContext = $periodMode === 'quarterly'
            ? "This is a QUARTERLY report covering {$period['month']}. Compare trends between the current and previous quarter."
            : "ANALYSIS PERIOD: {$period['month']} ({$period['start_date']} to {$period['end_date']})";

        return "Generate a comprehensive DSS report for FISHR (Fisheries Registration) applications.

{$periodContext}

APPLICATION STATISTICS:
- Total: {$stats['total_applications']}  |  Approved: {$stats['approved']} ({$stats['approval_rate']}%)
- Rejected: {$stats['rejected']} ({$stats['rejection_rate']}%)  |  Pending: {$stats['pending']}
- With FISHR Numbers Assigned: {$stats['with_fishr_number']}

LIVELIHOOD ANALYSIS:
{$this->formatFishrLivelihoods($byLivelihood)}

GEOGRAPHIC DISTRIBUTION:
- Total Barangays: {$byBarangay['total_barangays_covered']}
- Top Barangays: {$this->formatBarangays($byBarangay['top_barangays'])}

DEMOGRAPHICS:
- Male: {$demographics['male_count']} ({$demographics['male_percentage']}%)  |  Female: {$demographics['female_count']} ({$demographics['female_percentage']}%)

TRENDS:
- Current Period: {$trends['current_month_total']}  |  Previous Period: {$trends['previous_month_total']}
- Change: {$trends['change_percentage']}% ({$trends['trend']})

RESPONSE FORMAT (JSON):
{
    \"executive_summary\": \"string\",
    \"performance_assessment\": {\"overall_rating\": \"string\", \"approval_efficiency\": \"string\", \"coverage_adequacy\": \"string\", \"trend_analysis\": \"string\"},
    \"key_findings\": [\"string\"],
    \"critical_issues\": [\"string\"],
    \"recommendations\": {\"immediate_actions\": [\"string\"], \"short_term_strategies\": [\"string\"], \"long_term_vision\": [\"string\"]},
    \"fisheries_insights\": [\"string\"],
    \"confidence_level\": \"High/Medium/Low\",
    \"confidence_score\": 0
}";
    }

    private function formatFishrLivelihoods(array $byLivelihood): string
    {
        $formatted = '';
        foreach ($byLivelihood['distribution'] as $l) {
            $formatted .= "- {$l['livelihood']}: {$l['total']} applications ({$l['approval_rate']}% approved)\n";
        }
        return $formatted ?: '- No livelihood data available';
    }

    private function parseGeneratedFishrReport(string $text, array $data): array
    {
        try {
            $jsonStart = strpos($text, '{');
            $jsonEnd   = strrpos($text, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $reportData = json_decode(substr($text, $jsonStart, $jsonEnd - $jsonStart + 1), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($reportData)) {
                    if (isset($reportData['performance_assessment']['overall_rating'])) {
                        $reportData['performance_assessment']['overall_rating'] =
                            $this->sanitizeOverallRating($reportData['performance_assessment']['overall_rating']);
                    }
                    $confidence = $this->calculateConfidenceLevel($data, $this->getPeriodMode($data));
                    $reportData['confidence_level']  = $confidence['level'];
                    $reportData['confidence_score']  = $confidence['score'];
                    $reportData['confidence_source'] = 'calculated';
                    return ['success' => true, 'source' => 'llm', 'report_data' => $reportData, 'generated_at' => now()->toIso8601String()];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to parse FISHR report JSON', ['error' => $e->getMessage()]);
        }
        return $this->generateFishrFallbackReport($data);
    }

    private function generateFishrFallbackReport(array $data): array
    {
        $stats        = $data['fishr_stats'];
        $byLivelihood = $data['fishr_by_livelihood'];
        $byBarangay   = $data['fishr_by_barangay'];
        $demographics = $data['fishr_demographics'];
        $trends       = $data['fishr_trends'];
        $confidence   = $this->calculateConfidenceLevel($data, $this->getPeriodMode($data));

        $trendDescription = $trends['trend'] === 'increasing'
            ? "Applications have increased by {$trends['change_percentage']}% compared to the previous period"
            : ($trends['trend'] === 'decreasing'
                ? "Applications have decreased by " . abs($trends['change_percentage']) . "% compared to the previous period"
                : "Applications remain stable");

        return [
            'success'      => true,
            'source'       => 'fallback',
            'report_data'  => [
                'executive_summary'      => "FISHR registration processed {$stats['total_applications']} applications with a {$stats['approval_rate']}% approval rate. The program covers {$byBarangay['total_barangays_covered']} barangays and has assigned {$stats['with_fishr_number']} FISHR numbers.",
                'performance_assessment' => ['overall_rating' => $stats['approval_rate'] >= 75 ? 'Good' : ($stats['approval_rate'] >= 50 ? 'Fair' : 'Needs Improvement'), 'approval_efficiency' => "{$stats['approval_rate']}% approval rate", 'coverage_adequacy' => "Covers {$byBarangay['total_barangays_covered']} barangays", 'trend_analysis' => $trendDescription],
                'key_findings'           => ["Processed {$stats['total_applications']} FISHR applications", "Gender: {$demographics['male_percentage']}% male, {$demographics['female_percentage']}% female", "Most common livelihood: " . ($byLivelihood['most_common'][0]['livelihood'] ?? 'N/A'), "{$stats['with_fishr_number']} fisherfolk assigned FISHR numbers"],
                'critical_issues'        => $stats['pending'] > 20 ? ["High pending applications ({$stats['pending']})"] : [],
                'recommendations'        => ['immediate_actions' => ['Process pending applications', 'Expedite FISHR number assignments'], 'short_term_strategies' => ['Conduct fisher orientation', 'Improve document verification'], 'long_term_vision' => ['Develop integrated fisheries database']],
                'fisheries_insights'     => ["Total fisherfolk: {$stats['total_applications']}", "Coverage: {$byBarangay['total_barangays_covered']} barangays"],
                'confidence_level'       => $confidence['level'],
                'confidence_score'       => $confidence['score'],
                'confidence_source'      => 'calculated',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // BOATR REPORT
    // ─────────────────────────────────────────────────────────────────

    public function generateBoatrReport(array $data): array
    {
        $cacheKey = 'dss_boatr_report_' . md5(serialize($data));
        return Cache::remember($cacheKey, config('services.anthropic.cache_duration', 3600), function () use ($data) {
            try {
                if (!empty($this->anthropicApiKey)) return $this->generateBoatrLLMReport($data);
                return $this->generateBoatrFallbackReport($data);
            } catch (\Exception $e) {
                Log::error('BOATR DSS Report Generation Failed', ['error' => $e->getMessage()]);
                return $this->generateBoatrFallbackReport($data);
            }
        });
    }

    private function generateBoatrLLMReport(array $data): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json', 'x-api-key' => $this->anthropicApiKey, 'anthropic-version' => '2023-06-01',
        ])->timeout(300)->post($this->anthropicApiUrl, [
            'model' => $this->model, 'max_tokens' => $this->maxTokens, 'temperature' => $this->temperature,
            'messages' => [['role' => 'user', 'content' => $this->buildBoatrPrompt($data)]],
        ]);

        if ($response->successful()) {
            return $this->parseGeneratedBoatrReport($response->json()['content'][0]['text'] ?? '', $data);
        }
        throw new \Exception('LLM API request failed: ' . $response->body());
    }

    private function buildBoatrPrompt(array $data): string
    {
        $period            = $data['period'];
        $stats             = $data['boatr_stats'];
        $byBoatType        = $data['boatr_by_boat_type'];
        $byBarangay        = $data['boatr_by_barangay'];
        $engineAnalysis    = $data['boatr_engine_analysis'];
        $inspectionAnalysis= $data['boatr_inspection_analysis'];
        $trends            = $data['boatr_trends'];
        $periodMode        = $period['period_mode'] ?? 'monthly';

        $periodContext = $periodMode === 'quarterly'
            ? "This is a QUARTERLY report covering {$period['month']}. Highlight seasonal patterns in vessel registration."
            : "ANALYSIS PERIOD: {$period['month']} ({$period['start_date']} to {$period['end_date']})";

        return "Generate a comprehensive DSS report for BOATR (Boat Registration) applications.

{$periodContext}

APPLICATION STATISTICS:
- Total: {$stats['total_applications']}  |  Approved: {$stats['approved']} ({$stats['approval_rate']}%)
- Rejected: {$stats['rejected']}  |  Pending: {$stats['pending']}
- Inspections Completed: {$inspectionAnalysis['inspections_completed']} ({$inspectionAnalysis['completion_rate']}%)

BOAT SPECIFICATIONS:
- Avg Length: {$stats['avg_boat_length']}  |  Avg HP: {$stats['avg_horsepower']}
- Most Common Type: " . ($byBoatType['most_common'][0]['boat_type'] ?? 'N/A') . "
- Most Common Engine: " . ($engineAnalysis['most_common_engine']['type'] ?? 'N/A') . "

BOAT TYPE DISTRIBUTION:
{$this->formatBoatTypes($byBoatType)}

GEOGRAPHIC DISTRIBUTION:
- Total Barangays: {$byBarangay['total_barangays_covered']}
- Top Barangays: {$this->formatBarangays($byBarangay['top_barangays'])}

TRENDS:
- Current Period: {$trends['current_month_total']}  |  Previous Period: {$trends['previous_month_total']}
- Change: {$trends['change_percentage']}% ({$trends['trend']})

RESPONSE FORMAT (JSON):
{
    \"executive_summary\": \"string\",
    \"performance_assessment\": {\"overall_rating\": \"string\", \"approval_efficiency\": \"string\", \"inspection_effectiveness\": \"string\", \"trend_analysis\": \"string\"},
    \"key_findings\": [\"string\"],
    \"critical_issues\": [\"string\"],
    \"recommendations\": {\"immediate_actions\": [\"string\"], \"short_term_strategies\": [\"string\"], \"long_term_vision\": [\"string\"]},
    \"vessel_insights\": [\"string\"],
    \"confidence_level\": \"High/Medium/Low\",
    \"confidence_score\": 0
}";
    }

    private function formatBoatTypes(array $byBoatType): string
    {
        $formatted = '';
        foreach ($byBoatType['distribution'] as $t) {
            $formatted .= "- {$t['boat_type']}: {$t['total']} boats (avg length: {$t['avg_length']}m, avg HP: {$t['avg_horsepower']})\n";
        }
        return $formatted ?: '- No boat type data available';
    }

    private function parseGeneratedBoatrReport(string $text, array $data): array
    {
        try {
            $jsonStart = strpos($text, '{');
            $jsonEnd   = strrpos($text, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $reportData = json_decode(substr($text, $jsonStart, $jsonEnd - $jsonStart + 1), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($reportData)) {
                    if (isset($reportData['performance_assessment']['overall_rating'])) {
                        $reportData['performance_assessment']['overall_rating'] =
                            $this->sanitizeOverallRating($reportData['performance_assessment']['overall_rating']);
                    }
                    $confidence = $this->calculateConfidenceLevel($data, $this->getPeriodMode($data));
                    $reportData['confidence_level']  = $confidence['level'];
                    $reportData['confidence_score']  = $confidence['score'];
                    $reportData['confidence_source'] = 'calculated';
                    return ['success' => true, 'source' => 'llm', 'report_data' => $reportData, 'generated_at' => now()->toIso8601String()];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to parse BOATR report JSON', ['error' => $e->getMessage()]);
        }
        return $this->generateBoatrFallbackReport($data);
    }

    private function generateBoatrFallbackReport(array $data): array
    {
        $stats             = $data['boatr_stats'];
        $byBoatType        = $data['boatr_by_boat_type'];
        $byBarangay        = $data['boatr_by_barangay'];
        $inspectionAnalysis= $data['boatr_inspection_analysis'];
        $trends            = $data['boatr_trends'];
        $confidence        = $this->calculateConfidenceLevel($data, $this->getPeriodMode($data));

        $trendDescription = $trends['trend'] === 'increasing'
            ? "Applications have increased by {$trends['change_percentage']}% compared to the previous period"
            : ($trends['trend'] === 'decreasing'
                ? "Applications have decreased by " . abs($trends['change_percentage']) . "% compared to the previous period"
                : "Applications remain stable");

        return [
            'success'     => true,
            'source'      => 'fallback',
            'report_data' => [
                'executive_summary'      => "BOATR processed {$stats['total_applications']} boat registration applications with a {$stats['approval_rate']}% approval rate. Program covers {$byBarangay['total_barangays_covered']} barangays with {$inspectionAnalysis['inspections_completed']} vessel inspections completed.",
                'performance_assessment' => ['overall_rating' => $stats['approval_rate'] >= 75 ? 'Good' : ($stats['approval_rate'] >= 50 ? 'Fair' : 'Needs Improvement'), 'approval_efficiency' => "{$stats['approval_rate']}% approval rate", 'inspection_effectiveness' => "{$inspectionAnalysis['completion_rate']}% inspection completion", 'trend_analysis' => $trendDescription],
                'key_findings'           => ["Processed {$stats['total_applications']} boat registrations", "Avg specs: {$stats['avg_boat_length']} length, {$stats['avg_horsepower']} engine", "Most common type: " . ($byBoatType['most_common'][0]['boat_type'] ?? 'N/A'), "Inspection rate: {$inspectionAnalysis['completion_rate']}%"],
                'critical_issues'        => $inspectionAnalysis['inspections_pending'] > 10 ? ["High pending inspections ({$inspectionAnalysis['inspections_pending']})"] : [],
                'recommendations'        => ['immediate_actions' => ['Complete pending inspections', 'Process pending applications'], 'short_term_strategies' => ['Streamline inspection procedures'], 'long_term_vision' => ['Implement digital inspection system']],
                'vessel_insights'        => ["Total vessels: {$stats['total_applications']}", "Coverage: {$byBarangay['total_barangays_covered']} barangays", "{$byBoatType['total_boat_types']} boat types registered"],
                'confidence_level'       => $confidence['level'],
                'confidence_score'       => $confidence['score'],
                'confidence_source'      => 'calculated',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // PROMPT HELPERS
    // ─────────────────────────────────────────────────────────────────

    private function formatTrainingTypes(array $byType): string
    {
        $formatted = '';
        foreach ($byType['distribution'] as $t) {
            $formatted .= "- {$t['display_name']}: {$t['total']} applications ({$t['approval_rate']}% approved)\n";
        }
        return $formatted ?: '- No training types data available';
    }

    private function formatBarangays(array $barangays): string
    {
        $parts = [];
        foreach ($barangays as $b) {
            $parts[] = "{$b['barangay']} ({$b['applications']} applications)";
        }
        return implode(', ', $parts) ?: 'None';
    }

    private function formatCommodities(array $byCommodity): string
    {
        $formatted = '';
        foreach ($byCommodity['top_commodities'] as $c) {
            $formatted .= "- {$c['commodity']}: {$c['total_farmers']} farmers, {$c['total_land_area']} hectares\n";
        }
        return $formatted ?: '- No commodity data available';
    }
}