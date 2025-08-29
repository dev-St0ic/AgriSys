<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SeedlingDSSService
{
    private string $openaiApiKey;
    private string $openaiApiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.key');

        if (empty($this->openaiApiKey)) {
            throw new \Exception('OpenAI API key not configured. Please set OPENAI_API_KEY in your .env file.');
        }
    }

    /**
     * Generate AI-powered insights and prescriptions for seedling analytics data
     */
    public function generateInsights(array $analyticsData): array
    {
        try {
            $cacheKey = 'seedling_dss_insights_' . md5(serialize($analyticsData));
            
            // Cache insights for 6 hours to avoid excessive API calls
            return Cache::remember($cacheKey, 6 * 60 * 60, function() use ($analyticsData) {
                return $this->callOpenAI($analyticsData);
            });
        } catch (\Exception $e) {
            Log::error('DSS Insights Generation Failed: ' . $e->getMessage());
            return $this->getFallbackInsights($analyticsData);
        }
    }

    /**
     * Call OpenAI GPT-4 API for insights generation
     */
    private function callOpenAI(array $analyticsData): array
    {
        $prompt = $this->buildAnalyticsPrompt($analyticsData);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiApiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($this->openaiApiUrl, [
            'model' => 'gpt-4-turbo-preview',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an expert agricultural data analyst and decision support system specialist. Analyze agricultural seedling distribution data and provide actionable insights, strategic recommendations, and prescriptions for improving agricultural services delivery.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 2000,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }

        $result = $response->json();
        return $this->parseAIResponse($result['choices'][0]['message']['content']);
    }

    /**
     * Build comprehensive prompt for AI analysis
     */
    private function buildAnalyticsPrompt(array $data): string
    {
        $overview = $data['overview'] ?? [];
        $monthlyTrends = $data['monthlyTrends'] ?? [];
        $barangayAnalysis = $data['barangayAnalysis'] ?? [];
        $topItems = $data['topItems'] ?? [];
        $leastRequestedItems = $data['leastRequestedItems'] ?? [];
        $statusAnalysis = $data['statusAnalysis'] ?? [];
        $categoryAnalysis = $data['categoryAnalysis'] ?? [];
        $processingTime = $data['processingTimeAnalysis'] ?? [];

        return "
**AGRICULTURAL SEEDLING DISTRIBUTION ANALYTICS DATA**

**OVERVIEW METRICS:**
- Total Requests: " . number_format($overview['total_requests'] ?? 0) . "
- Approval Rate: " . ($overview['approval_rate'] ?? 0) . "%
- Total Quantity Distributed: " . number_format($overview['total_quantity_requested'] ?? 0) . " units
- Average Request Size: " . number_format($overview['avg_request_size'] ?? 0, 1) . " units
- Active Barangays: " . ($overview['active_barangays'] ?? 0) . "
- Unique Farmers Served: " . number_format($overview['unique_applicants'] ?? 0) . "

**MONTHLY TRENDS:** " . json_encode($monthlyTrends) . "

**TOP PERFORMING BARANGAYS:**
" . collect($barangayAnalysis)->take(5)->map(function($b) {
    return "- {$b->barangay}: {$b->total_requests} requests, " . round(($b->approved / max(1, $b->total_requests)) * 100, 1) . "% approval rate";
})->implode("\n") . "

**BOTTOM PERFORMING BARANGAYS:**
" . collect($barangayAnalysis)->slice(-3)->map(function($b) {
    return "- {$b->barangay}: {$b->total_requests} requests, " . round(($b->approved / max(1, $b->total_requests)) * 100, 1) . "% approval rate";
})->implode("\n") . "

**MOST REQUESTED ITEMS:**
" . collect($topItems)->take(5)->map(function($item) {
    return "- {$item['name']}: {$item['total_quantity']} units in {$item['request_count']} requests";
})->implode("\n") . "

**LEAST REQUESTED ITEMS:**
" . collect($leastRequestedItems)->take(5)->map(function($item) {
    return "- {$item['name']}: {$item['total_quantity']} units in {$item['request_count']} requests";
})->implode("\n") . "

**REQUEST STATUS DISTRIBUTION:** " . json_encode($statusAnalysis) . "

**CATEGORY DISTRIBUTION:** " . json_encode($categoryAnalysis) . "

**PROCESSING TIME ANALYSIS:** " . json_encode($processingTime) . "

**ANALYSIS REQUEST:**
Please provide a comprehensive decision support analysis with the following structure:

1. **EXECUTIVE SUMMARY** (2-3 key findings)
2. **PERFORMANCE INSIGHTS** (3-4 detailed observations)
3. **STRATEGIC RECOMMENDATIONS** (4-5 actionable recommendations)
4. **OPERATIONAL PRESCRIPTIONS** (3-4 specific operational improvements)
5. **RISK ASSESSMENT** (2-3 identified risks and mitigation strategies)
6. **GROWTH OPPORTUNITIES** (2-3 expansion or improvement opportunities)

Format the response as JSON with clear sections. Focus on actionable insights that agricultural administrators can implement to improve seedling distribution efficiency, farmer satisfaction, and resource allocation.
        ";
    }

    /**
     * Parse AI response into structured format
     */
    private function parseAIResponse(string $response): array
    {
        // Try to extract JSON from response
        $jsonStart = strpos($response, '{');
        $jsonEnd = strrpos($response, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonString = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
            $parsed = json_decode($jsonString, true);
            
            if ($parsed) {
                return $this->structureInsights($parsed);
            }
        }

        // Fallback: parse text response
        return $this->parseTextResponse($response);
    }

    /**
     * Structure insights into standardized format
     */
    private function structureInsights(array $parsed): array
    {
        return [
            'executive_summary' => $parsed['executive_summary'] ?? $parsed['EXECUTIVE_SUMMARY'] ?? [],
            'performance_insights' => $parsed['performance_insights'] ?? $parsed['PERFORMANCE_INSIGHTS'] ?? [],
            'strategic_recommendations' => $parsed['strategic_recommendations'] ?? $parsed['STRATEGIC_RECOMMENDATIONS'] ?? [],
            'operational_prescriptions' => $parsed['operational_prescriptions'] ?? $parsed['OPERATIONAL_PRESCRIPTIONS'] ?? [],
            'risk_assessment' => $parsed['risk_assessment'] ?? $parsed['RISK_ASSESSMENT'] ?? [],
            'growth_opportunities' => $parsed['growth_opportunities'] ?? $parsed['GROWTH_OPPORTUNITIES'] ?? [],
            'generated_at' => Carbon::now()->toISOString(),
            'ai_confidence' => 'high'
        ];
    }

    /**
     * Parse text response when JSON parsing fails
     */
    private function parseTextResponse(string $response): array
    {
        // Add logging to debug what you're receiving
        Log::info('OpenAI Response to Parse:', ['response' => substr($response, 0, 500)]);

        
        $sections = [
            'executive_summary' => [],
            'performance_insights' => [],
            'strategic_recommendations' => [],
            'operational_prescriptions' => [],
            'risk_assessment' => [],
            'growth_opportunities' => []
        ];

        // Clean the response
        $response = preg_replace('/\*\*(.*?)\*\*/', '$1', $response); // Remove markdown bold
        $lines = explode("\n", $response);
        $currentSection = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // More flexible section detection
            $lower = strtolower($line);
            if (strpos($lower, 'executive') !== false && strpos($lower, 'summary') !== false) {
                $currentSection = 'executive_summary';
            } elseif (strpos($lower, 'performance') !== false && strpos($lower, 'insight') !== false) {
                $currentSection = 'performance_insights';
            } elseif (strpos($lower, 'strategic') !== false || strpos($lower, 'recommendation') !== false) {
                $currentSection = 'strategic_recommendations';
            } elseif (strpos($lower, 'operational') !== false || strpos($lower, 'prescription') !== false) {
                $currentSection = 'operational_prescriptions';
            } elseif (strpos($lower, 'risk') !== false) {
                $currentSection = 'risk_assessment';
            } elseif (strpos($lower, 'growth') !== false || strpos($lower, 'opportunit') !== false) {
                $currentSection = 'growth_opportunities';
            } elseif ($currentSection && (strpos($line, '-') === 0 || strpos($line, '•') === 0 || preg_match('/^\d+\./', $line))) {
                // Extract bullet points or numbered lists
                $cleanLine = preg_replace('/^[-•\d\.]\s*/', '', $line);
                if (!empty($cleanLine)) {
                    $sections[$currentSection][] = $cleanLine;
                }
            }
        }

        // Ensure we have some data
        foreach ($sections as $key => $section) {
            if (empty($section)) {
                $sections[$key] = ['Analysis pending - data processing in progress'];
            }
        }

        $sections['generated_at'] = Carbon::now()->toISOString();
        $sections['ai_confidence'] = count(array_filter($sections)) > 4 ? 'high' : 'medium';

        return $sections;
    }
    /**
     * Generate fallback insights when AI service is unavailable
     */
    private function getFallbackInsights(array $data): array
    {
        $overview = $data['overview'] ?? [];
        $approvalRate = $overview['approval_rate'] ?? 0;
        $totalRequests = $overview['total_requests'] ?? 0;

        return [
            'executive_summary' => [
                "System processed {$totalRequests} seedling requests with {$approvalRate}% approval rate",
                "Distribution network serves " . ($overview['active_barangays'] ?? 0) . " active barangays",
                $approvalRate > 80 ? "Performance exceeds target benchmarks" : "Performance improvement opportunities identified"
            ],
            'performance_insights' => [
                "Current approval rate: {$approvalRate}%",
                "Average request processing efficiency needs evaluation",
                "Barangay-level performance varies significantly",
                "Seasonal demand patterns affect resource allocation"
            ],
            'strategic_recommendations' => [
                "Implement standardized approval criteria across all barangays",
                "Develop predictive demand forecasting for better inventory management",
                "Establish performance benchmarks for regional coordinators",
                "Create farmer feedback loop for service improvement"
            ],
            'operational_prescriptions' => [
                "Reduce processing time through digital workflow automation",
                "Standardize documentation requirements across regions",
                "Implement real-time inventory tracking system",
                "Establish quality assurance protocols for seedling distribution"
            ],
            'risk_assessment' => [
                "Supply chain disruptions may affect seedling availability",
                "Manual processes create bottlenecks during peak seasons",
                "Limited data analytics capabilities restrict decision-making effectiveness"
            ],
            'growth_opportunities' => [
                "Expand program to underserved barangays",
                "Develop mobile application for farmer requests",
                "Create partnership programs with agricultural cooperatives"
            ],
            'generated_at' => Carbon::now()->toISOString(),
            'ai_confidence' => 'fallback'
        ];
    }

    /**
     * Generate recommendations for specific metrics
     */
    public function getMetricRecommendations(string $metric, array $data): array
    {
        $recommendations = [
            'approval_rate' => [
                'low' => [
                    'Review rejection criteria for consistency',
                    'Provide additional training to evaluation staff',
                    'Implement appeal process for rejected applications'
                ],
                'medium' => [
                    'Establish clear approval guidelines',
                    'Monitor seasonal approval variations',
                    'Improve farmer education on requirements'
                ],
                'high' => [
                    'Maintain current approval standards',
                    'Document best practices for replication',
                    'Consider expanding capacity to meet demand'
                ]
            ],
            'processing_time' => [
                'slow' => [
                    'Digitize manual processes',
                    'Implement workflow automation',
                    'Add processing staff during peak periods'
                ],
                'average' => [
                    'Optimize current workflows',
                    'Set processing time targets',
                    'Monitor bottlenecks regularly'
                ],
                'fast' => [
                    'Maintain current efficiency levels',
                    'Share best practices with other regions',
                    'Focus on quality assurance'
                ]
            ]
        ];

        return $recommendations[$metric] ?? [];
    }
}