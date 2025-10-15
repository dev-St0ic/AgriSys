<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SeedlingDSSService
{
    private string $anthropicApiKey;
    private string $anthropicApiUrl = 'https://api.anthropic.com/v1/messages';
    private string $model;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        $this->anthropicApiKey = config('services.anthropic.key');
        $this->model = config('services.anthropic.model', 'claude-sonnet-4-5-20250929');
        $this->maxTokens = config('services.anthropic.max_tokens', 4096);
        $this->temperature = config('services.anthropic.temperature', 0.3);

        if (empty($this->anthropicApiKey)) {
            Log::warning('Anthropic API key not configured. Using fallback insights.');
        }
    }

    /**
     * Generate AI-powered insights and prescriptions for seedling analytics data
     */
    public function generateInsights(array $analyticsData): array
    {
        try {
            $cacheKey = 'seedling_dss_insights_' . md5(serialize($analyticsData));
            $cacheDuration = config('services.anthropic.cache_duration', 3600);

            return Cache::remember($cacheKey, $cacheDuration, function() use ($analyticsData) {
                try {
                    if (!empty($this->anthropicApiKey)) {
                        $insights = $this->callClaude($analyticsData);
                        Log::info('Claude Insights Generated:', [
                            'cache_key' => $cacheKey,
                            'data_hash' => md5(serialize($analyticsData))
                        ]);
                        return $insights;
                    }
                    
                    Log::info('Using fallback insights due to missing Anthropic API key');
                    return $this->getFallbackInsights($analyticsData);
                } catch (\Exception $e) {
                    Log::error('Failed to generate insights in cache callback', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return $this->getFallbackInsights($analyticsData);
                }
            });
        } catch (\Exception $e) {
            Log::error('DSS Insights Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getFallbackInsights($analyticsData);
        }
    }

    /**
     * Call Anthropic Claude API for insights generation
     */
    private function callClaude(array $analyticsData): array
    {
        $prompt = $this->buildAnalyticsPrompt($analyticsData);

        Log::info('Sending prompt to Claude:', ['prompt_length' => strlen($prompt)]);

        $response = Http::withHeaders([
            'x-api-key' => $this->anthropicApiKey,
            'anthropic-version' => config('services.anthropic.api_version', '2023-06-01'),
            'Content-Type' => 'application/json',
        ])->timeout(config('services.anthropic.timeout', 90))->post($this->anthropicApiUrl, [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'system' => 'You are an expert agricultural data analyst and decision support system specialist with deep expertise in municipal agriculture programs. You provide detailed, actionable insights for improving seedling distribution programs. Always respond with structured, comprehensive analysis in the exact format requested. Focus on practical, implementable recommendations that municipal agriculture officers can act upon immediately.'
        ]);

        if (!$response->successful()) {
            Log::error('Claude API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Claude API request failed: ' . $response->body());
        }

        $result = $response->json();
        Log::info('Claude Response:', ['response' => $result]);

        // Extract text from Claude's response format
        $messageContent = $result['content'][0]['text'] ?? '';
        
        return $this->parseAIResponse($messageContent);
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

        // Convert barangay analysis to array format
        if (is_object($barangayAnalysis) && method_exists($barangayAnalysis, 'toArray')) {
            $barangayArray = $barangayAnalysis->toArray();
        } else {
            $barangayArray = (array) $barangayAnalysis;
        }

        // Format category analysis
        $categoryBreakdown = collect($categoryAnalysis)->map(function($data, $category) {
            return ucfirst($category) . ": " . ($data['requests'] ?? 0) . " requests, " . 
                   ($data['total_items'] ?? 0) . " items";
        })->implode('; ');

        $topBarangays = collect($barangayArray)->take(3)->map(function($b) {
            $barangay = is_object($b) ? $b : (object) $b;
            return ($barangay->barangay ?? 'Unknown') . " (" . ($barangay->total_requests ?? 0) . " requests, " .
                   number_format(($barangay->approval_rate ?? 0), 1) . "% success rate)";
        })->implode('; ');

        $underperformingBarangays = collect($barangayArray)->slice(-3)->map(function($b) {
            $barangay = is_object($b) ? $b : (object) $b;
            return ($barangay->barangay ?? 'Unknown') . " (" . ($barangay->total_requests ?? 0) . " requests, " .
                   number_format(($barangay->approval_rate ?? 0), 1) . "% success rate)";
        })->implode('; ');

        $topItemsList = collect($topItems)->take(5)->map(function($item, $index) {
            return ($index + 1) . ". " . ($item['name'] ?? 'Unknown') . " (" . ($item['category'] ?? 'general') . "): " .
                   number_format($item['total_quantity'] ?? 0) . " units requested across " . ($item['request_count'] ?? 0) . " applications";
        })->implode("\n");

        $leastItemsList = collect($leastRequestedItems)->take(5)->map(function($item, $index) {
            return ($index + 1) . ". " . ($item['name'] ?? 'Unknown') . " (" . ($item['category'] ?? 'general') . "): Only " .
                   number_format($item['total_quantity'] ?? 0) . " units in " . ($item['request_count'] ?? 0) . " requests";
        })->implode("\n");

        return "
AGRICULTURAL SEEDLING DISTRIBUTION PROGRAM - COMPREHENSIVE ANALYSIS REQUEST

**CRITICAL DATA OVERVIEW:**
- Total Requests Processed: " . number_format($overview['total_requests'] ?? 0) . "
- Program Approval Success Rate: " . ($overview['approval_rate'] ?? 0) . "%
- Total Units Distributed: " . number_format($overview['total_quantity_requested'] ?? 0) . "
- Average Request Volume: " . number_format($overview['avg_request_size'] ?? 0, 1) . " units per application
- Geographic Coverage: " . ($overview['active_barangays'] ?? 0) . " barangays participating
- Community Reach: " . number_format($overview['unique_applicants'] ?? 0) . " individual farmers served

**CATEGORY DISTRIBUTION:**
{$categoryBreakdown}

**GEOGRAPHIC PERFORMANCE DATA:**
Top Performing Areas: {$topBarangays}

Underperforming Areas: {$underperformingBarangays}

**HIGH-DEMAND ITEMS BY CATEGORY:**
{$topItemsList}

**UNDERUTILIZED RESOURCES:**
{$leastItemsList}

**OPERATIONAL METRICS:**
- Application Processing Status: " . json_encode($statusAnalysis) . "
- Processing Time Analysis: Average " . ($processingTime['avg_processing_days'] ?? 0) . " days

**REQUIRED OUTPUT FORMAT:**
You must provide a comprehensive Decision Support System analysis with exactly these sections. Each section must contain 3-5 detailed, actionable insights:

**EXECUTIVE_SUMMARY**
Provide 3-4 key strategic findings that municipal officials need to know immediately. Focus on the most critical insights about program performance and impact.

**PERFORMANCE_INSIGHTS**
Provide 4-5 detailed analytical observations about program effectiveness, efficiency gaps, and operational strengths. Include specific metrics and comparative analysis.

**STRATEGIC_RECOMMENDATIONS**
Provide 4-5 specific, actionable policy and program recommendations that can be implemented within 3-6 months. Each recommendation should be concrete and measurable.

**OPERATIONAL_PRESCRIPTIONS**
Provide 3-4 immediate operational improvements focusing on process optimization, resource allocation, and service delivery. These should be implementable within 30-90 days.

**RISK_ASSESSMENT**
Identify 2-3 critical risks to program sustainability and provide specific mitigation strategies for each risk.

Please structure your response as clear, professional paragraphs under each heading (not as JSON). Write detailed analysis suitable for municipal agricultural administrators. Each insight should be a complete sentence or paragraph that provides actionable intelligence.
        ";
    }

    /**
     * Parse AI response into structured format
     */
    private function parseAIResponse(string $response): array
    {
        Log::info('Parsing Claude Response:', ['response_length' => strlen($response)]);

        $sections = [
            'executive_summary' => [],
            'performance_insights' => [],
            'strategic_recommendations' => [],
            'operational_prescriptions' => [],
            'risk_assessment' => []
        ];

        $response = strip_tags($response);
        $lines = explode("\n", $response);

        $currentSection = null;
        $currentContent = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $lowerLine = strtolower($line);
            
            // Check for section headers
            if (strpos($lowerLine, 'executive') !== false && strpos($lowerLine, 'summary') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'executive_summary';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'performance') !== false && strpos($lowerLine, 'insight') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'performance_insights';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'strategic') !== false && strpos($lowerLine, 'recommendation') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'strategic_recommendations';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'operational') !== false && strpos($lowerLine, 'prescription') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'operational_prescriptions';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'risk') !== false && strpos($lowerLine, 'assessment') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'risk_assessment';
                $currentContent = '';
            } elseif ($currentSection) {
                if (!empty($currentContent)) {
                    $currentContent .= ' ';
                }
                $currentContent .= $line;
            }
        }

        // Save the last section
        $this->saveSectionContent($sections, $currentSection, $currentContent);

        // Break long paragraphs into sentences
        foreach ($sections as $key => &$content) {
            if (count($content) === 1 && strlen($content[0]) > 500) {
                $sentences = preg_split('/(?<=[.!?])\s+/', $content[0]);
                $content = array_filter($sentences, function($sentence) {
                    return strlen(trim($sentence)) > 10;
                });
            }
        }

        $result = [
            'executive_summary' => $sections['executive_summary'],
            'performance_insights' => $sections['performance_insights'],
            'strategic_recommendations' => $sections['strategic_recommendations'],
            'operational_prescriptions' => $sections['operational_prescriptions'],
            'risk_assessment' => $sections['risk_assessment'],
            'generated_at' => Carbon::now()->toISOString(),
            'ai_confidence' => 'high'
        ];

        Log::info('Parsed sections:', ['sections' => array_map('count', $sections)]);
        return $result;
    }

    /**
     * Helper method to save section content
     */
    private function saveSectionContent(array &$sections, ?string $section, string $content): void
    {
        if ($section && !empty(trim($content))) {
            $content = trim($content);
            $sentences = preg_split('/(?<=[.!?])\s+/', $content);
            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                if (strlen($sentence) > 10) {
                    $sections[$section][] = $sentence;
                }
            }
        }
    }

    /**
     * Enhanced fallback insights
     */
    private function getFallbackInsights(array $data): array
    {
        $overview = $data['overview'] ?? [];
        $approvalRate = $overview['approval_rate'] ?? 0;
        $totalRequests = $overview['total_requests'] ?? 0;
        $activeBarangays = $overview['active_barangays'] ?? 0;

        return [
            'executive_summary' => [
                "The municipal seedling distribution program processed {$totalRequests} applications during the reporting period with an overall approval rate of {$approvalRate}%, demonstrating significant community engagement in agricultural development initiatives.",
                "Geographic coverage spans {$activeBarangays} active barangays, indicating broad program reach but potential for expansion to underserved areas.",
                "Program performance metrics reveal both operational successes and opportunities for systematic improvements in distribution efficiency and farmer satisfaction.",
                "Resource allocation analysis shows clear demand patterns that can inform future procurement strategies and inventory management decisions."
            ],
            'performance_insights' => [
                "Current approval rate of {$approvalRate}% indicates room for improvement in application processing efficiency and criteria standardization across all reviewing personnel.",
                "Seasonal demand fluctuations significantly impact resource allocation, with peak periods creating bottlenecks that affect service delivery timelines.",
                "Barangay-level performance varies considerably, suggesting the need for targeted support and capacity building in underperforming areas.",
                "Processing time analysis reveals opportunities for workflow optimization and digital transformation to reduce administrative burden.",
                "Category distribution patterns show distinct farmer preferences that should inform strategic procurement planning and variety selection."
            ],
            'strategic_recommendations' => [
                "Implement a standardized application evaluation framework to ensure consistent approval criteria and reduce processing delays across all administrative levels.",
                "Develop predictive demand forecasting models using historical data to optimize inventory levels and reduce stockouts during peak planting seasons.",
                "Establish performance benchmarks for regional coordinators and implement regular monitoring systems to ensure equitable service delivery across all barangays.",
                "Create comprehensive farmer education programs focusing on application requirements, optimal planting practices, and program guidelines to improve success rates.",
                "Design and implement a digital feedback system to continuously assess farmer satisfaction and identify service delivery improvements."
            ],
            'operational_prescriptions' => [
                "Deploy digital workflow management systems to automate routine administrative tasks and reduce manual processing times by an estimated 40-60%.",
                "Standardize documentation requirements and create user-friendly application templates to reduce incomplete submissions and processing delays.",
                "Implement real-time inventory tracking systems integrated with demand forecasting to maintain optimal stock levels and prevent shortages.",
                "Establish quality assurance protocols for seedling distribution including regular inspections, farmer feedback collection, and outcome monitoring systems."
            ],
            'risk_assessment' => [
                "Supply chain disruptions pose significant risks to program continuity, requiring development of alternative supplier networks and emergency procurement protocols.",
                "Heavy reliance on manual processes creates bottlenecks during peak seasons and increases error rates, necessitating systematic digitization initiatives.",
                "Limited data analytics capabilities restrict evidence-based decision-making and long-term strategic planning effectiveness, requiring investment in analytical infrastructure and staff training."
            ],
            'generated_at' => Carbon::now()->toISOString(),
            'ai_confidence' => 'fallback_system'
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
                    'Conduct comprehensive review of current rejection criteria to ensure consistency and appropriateness',
                    'Provide specialized training to evaluation staff on standardized assessment procedures',
                    'Implement transparent appeal process for rejected applications with clear guidelines',
                    'Establish mentorship programs pairing experienced evaluators with new staff members'
                ],
                'medium' => [
                    'Develop clear, published approval guidelines accessible to farmers and evaluation staff',
                    'Monitor seasonal approval variations to identify and address systemic issues',
                    'Enhance farmer education programs on application requirements and best practices',
                    'Create performance dashboards for real-time monitoring of approval trends'
                ],
                'high' => [
                    'Document and standardize current best practices for replication across all units',
                    'Consider expanding program capacity to meet growing demand while maintaining quality',
                    'Develop advanced training modules for evaluation staff to maintain high standards',
                    'Implement continuous improvement processes based on successful practices'
                ]
            ],
            'processing_time' => [
                'slow' => [
                    'Digitize remaining manual processes to eliminate paper-based bottlenecks',
                    'Implement automated workflow systems with built-in approval routing',
                    'Deploy additional processing staff during identified peak periods',
                    'Create fast-track procedures for straightforward applications meeting all criteria'
                ],
                'average' => [
                    'Optimize current workflows through process mapping and bottleneck analysis',
                    'Establish specific processing time targets for different application types',
                    'Monitor workflow performance with regular bottleneck identification and resolution',
                    'Implement staff scheduling optimization during high-volume periods'
                ],
                'fast' => [
                    'Maintain current efficiency levels through regular process audits',
                    'Share successful time management practices with other regional offices',
                    'Focus quality assurance efforts on maintaining accuracy while preserving speed',
                    'Develop advanced efficiency metrics and continuous monitoring systems'
                ]
            ]
        ];

        return $recommendations[$metric] ?? [];
    }
}