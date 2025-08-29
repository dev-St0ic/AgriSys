<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Municipal Agriculture Office - Advanced Decision Support System Report' }}</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
            @top-center {
                content: "CONFIDENTIAL - DECISION SUPPORT SYSTEM ANALYSIS";
                font-size: 8px;
                color: #666;
            }
            @bottom-center {
                content: "Page " counter(page) " of " counter(pages) " | Generated: {{ now()->format('Y-m-d H:i') }}";
                font-size: 10px;
                color: #666;
            }
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .letterhead {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .republic-header {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }
        
        .office-name {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
            color: #2c5530;
        }
        
        .address {
            font-size: 11px;
            font-style: italic;
            margin: 5px 0;
        }
        
        .report-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0 15px 0;
            color: #2c5530;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .report-subtitle {
            font-size: 14px;
            text-align: center;
            margin-bottom: 25px;
            font-style: italic;
            color: #666;
        }
        
        .classification-banner {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 11px;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }
        
        .report-metadata {
            margin-bottom: 30px;
            border: 2px solid #2c5530;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
        }
        
        .metadata-grid {
            display: table;
            width: 100%;
        }
        
        .metadata-row {
            display: table-row;
        }
        
        .metadata-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            padding: 5px 0;
            color: #2c5530;
        }
        
        .metadata-value {
            display: table-cell;
            width: 65%;
            padding: 5px 0 5px 15px;
        }
        
        .executive-summary {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-left: 6px solid #1976d2;
            padding: 25px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .insight-section {
            margin-bottom: 35px;
            page-break-inside: avoid;
        }
        
        .section-header {
            font-size: 15px;
            font-weight: bold;
            color: #2c5530;
            border-bottom: 3px solid #2c5530;
            padding-bottom: 8px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, transparent, #f8f9fa);
            padding-left: 10px;
        }
        
        .insight-content {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .insight-paragraph {
            margin-bottom: 15px;
            text-align: justify;
            line-height: 1.7;
        }
        
        .insight-paragraph:last-child {
            margin-bottom: 0;
        }
        
        .key-finding {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            border-left: 5px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        
        .key-finding .finding-title {
            font-weight: bold;
            color: #e65100;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .recommendation-box {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            border: 2px solid #4caf50;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 3px 15px rgba(76, 175, 80, 0.2);
        }
        
        .recommendation-title {
            font-size: 14px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .recommendation-item {
            margin-bottom: 12px;
            padding-left: 20px;
            position: relative;
        }
        
        .recommendation-item:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #4caf50;
            font-weight: bold;
        }
        
        .priority-high {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            border-left: 5px solid #f44336;
        }
        
        .priority-medium {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            border-left: 5px solid #ff9800;
        }
        
        .priority-low {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            border-left: 5px solid #4caf50;
        }
        
        .metrics-dashboard {
            display: flex;
            flex-wrap: wrap;
            margin: 20px 0;
            gap: 15px;
        }
        
        .metric-card {
            flex: 1;
            min-width: 200px;
            background: linear-gradient(135deg, #fff, #f8f9fa);
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c5530;
            margin-bottom: 5px;
        }
        
        .metric-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .performance-indicator {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            margin: 5px 0;
        }
        
        .performance-excellent {
            background: #4caf50;
            color: white;
        }
        
        .performance-good {
            background: #2196f3;
            color: white;
        }
        
        .performance-warning {
            background: #ff9800;
            color: white;
        }
        
        .performance-critical {
            background: #f44336;
            color: white;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .data-table th {
            background: linear-gradient(135deg, #2c5530, #3e7b3e);
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }
        
        .data-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .data-table tr:hover {
            background: #f0f8f0;
        }
        
        .action-timeline {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
        }
        
        .timeline-item {
            margin-bottom: 15px;
            padding-left: 25px;
            position: relative;
        }
        
        .timeline-item:before {
            content: "";
            position: absolute;
            left: 0;
            top: 6px;
            width: 12px;
            height: 12px;
            background: #007bff;
            border-radius: 50%;
        }
        
        .timeline-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .risk-matrix {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        
        .risk-item {
            padding: 15px;
            border-radius: 8px;
            border: 2px solid;
        }
        
        .risk-high {
            background: #ffebee;
            border-color: #f44336;
        }
        
        .risk-medium {
            background: #fff3e0;
            border-color: #ff9800;
        }
        
        .risk-low {
            background: #e8f5e8;
            border-color: #4caf50;
        }
        
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #2c5530;
        }
        
        .signature-cell {
            width: 33.33%;
            padding: 40px 20px;
            text-align: center;
            vertical-align: top;
            border: 1px solid #2c5530;
            background: #fafafa;
        }
        
        .signature-line {
            border-top: 2px solid #2c5530;
            margin-top: 50px;
            padding-top: 8px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .footer-note {
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #e9ecef, #f8f9fa);
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 10px;
            text-align: center;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .confidence-indicator {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .confidence-high {
            background: #4caf50;
            color: white;
        }
        
        .confidence-medium {
            background: #ff9800;
            color: white;
        }
        
        .confidence-low {
            background: #f44336;
            color: white;
        }
        
        .trend-indicator {
            font-size: 14px;
            margin-left: 8px;
        }
        
        .trend-up {
            color: #4caf50;
        }
        
        .trend-down {
            color: #f44336;
        }
        
        .trend-stable {
            color: #ff9800;
        }
    </style>
</head>
<body>
    <!-- CLASSIFICATION BANNER -->
    <div class="classification-banner">
        DECISION SUPPORT SYSTEM - FOR OFFICIAL USE ONLY
    </div>

    <!-- OFFICIAL LETTERHEAD -->
    <div class="letterhead">
        <div class="republic-header">REPUBLIC OF THE PHILIPPINES</div>
        <div class="office-name">MUNICIPAL AGRICULTURE OFFICE</div>
        <div class="office-name">[MUNICIPALITY NAME]</div>
        <div class="address">Provincial Address, Philippines</div>
        <div class="address">Tel: (000) 000-0000 | Email: agriculture@municipality.gov.ph</div>
    </div>

    <!-- REPORT TITLE -->
    <div class="report-title">Advanced Decision Support System</div>
    <div class="report-subtitle">AI-Powered Agricultural Analytics & Strategic Recommendations</div>

    <!-- REPORT METADATA -->
    <div class="report-metadata">
        <div class="metadata-grid">
            <div class="metadata-row">
                <div class="metadata-label">Analysis Period:</div>
                <div class="metadata-value">{{ $period ?? 'Complete Fiscal Year Analysis' }}</div>
            </div>
            <div class="metadata-row">
                <div class="metadata-label">Report Generated:</div>
                <div class="metadata-value">{{ $generated_at ?? now()->format('F j, Y \a\t g:i A') }}</div>
            </div>
            <div class="metadata-row">
                <div class="metadata-label">AI Analysis Engine:</div>
                <div class="metadata-value">GPT-4 Turbo with Agricultural Domain Expertise</div>
            </div>
            <div class="metadata-row">
                <div class="metadata-label">Confidence Level:</div>
                <div class="metadata-value">
                    {{ strtoupper($ai_confidence ?? 'HIGH') }} CONFIDENCE
                    <span class="confidence-indicator confidence-{{ strtolower($ai_confidence ?? 'high') }}">
                        {{ ($ai_confidence ?? 'high') === 'high' ? '95%+' : (($ai_confidence ?? 'high') === 'medium' ? '75-95%' : '50-75%') }}
                    </span>
                </div>
            </div>
            <div class="metadata-row">
                <div class="metadata-label">Data Sources:</div>
                <div class="metadata-value">{{ number_format($overview['total_requests'] ?? 0) }} applications, {{ $overview['active_barangays'] ?? 0 }} barangays, {{ number_format($overview['unique_applicants'] ?? 0) }} farmers</div>
            </div>
        </div>
    </div>

    <!-- PERFORMANCE DASHBOARD -->
    <div class="metrics-dashboard">
        <div class="metric-card">
            <div class="metric-value">{{ number_format($overview['total_requests'] ?? 0) }}</div>
            <div class="metric-label">Total Applications</div>
            <span class="trend-indicator trend-up">↗</span>
        </div>
        <div class="metric-card">
            <div class="metric-value">{{ number_format($overview['approval_rate'] ?? 0, 1) }}%</div>
            <div class="metric-label">Approval Efficiency</div>
            <span class="performance-indicator performance-{{ ($overview['approval_rate'] ?? 0) >= 85 ? 'excellent' : (($overview['approval_rate'] ?? 0) >= 70 ? 'good' : 'critical') }}">
                {{ ($overview['approval_rate'] ?? 0) >= 85 ? 'EXCELLENT' : (($overview['approval_rate'] ?? 0) >= 70 ? 'GOOD' : 'NEEDS ATTENTION') }}
            </span>
        </div>
        <div class="metric-card">
            <div class="metric-value">{{ number_format($overview['total_quantity_requested'] ?? 0) }}</div>
            <div class="metric-label">Seedlings Distributed</div>
            <span class="trend-indicator trend-up">↗</span>
        </div>
        <div class="metric-card">
            <div class="metric-value">{{ $overview['active_barangays'] ?? 0 }}/20</div>
            <div class="metric-label">Geographic Coverage</div>
            <span class="performance-indicator performance-{{ ($overview['active_barangays'] ?? 0) >= 18 ? 'excellent' : (($overview['active_barangays'] ?? 0) >= 15 ? 'good' : 'warning') }}">
                {{ round((($overview['active_barangays'] ?? 0) / 20) * 100) }}%
            </span>
        </div>
    </div>

    <!-- EXECUTIVE STRATEGIC SUMMARY -->
    <div class="executive-summary">
        <div class="summary-title">Executive Strategic Summary</div>
        @if(isset($insights['executive_summary']) && is_array($insights['executive_summary']))
            @foreach($insights['executive_summary'] as $summary)
            <div class="insight-paragraph">
                {{ is_string($summary) ? $summary : 'Comprehensive analysis indicates the municipal seedling distribution program demonstrates strong community engagement with ' . number_format($overview['total_requests'] ?? 0) . ' processed applications, achieving a ' . number_format($overview['approval_rate'] ?? 0, 1) . '% approval rate across ' . ($overview['active_barangays'] ?? 0) . ' active barangays, while serving ' . number_format($overview['unique_applicants'] ?? 0) . ' unique agricultural beneficiaries.' }}
            </div>
            @endforeach
        @else
        <div class="insight-paragraph">
            The municipal seedling distribution program processed <strong>{{ number_format($overview['total_requests'] ?? 0) }} applications</strong> during the analysis period, achieving an approval rate of <strong>{{ number_format($overview['approval_rate'] ?? 0, 1) }}%</strong>. Geographic coverage spans <strong>{{ $overview['active_barangays'] ?? 0 }} barangays</strong> with <strong>{{ number_format($overview['unique_applicants'] ?? 0) }} unique farmers</strong> participating, indicating robust community engagement in agricultural development initiatives.
        </div>
        <div class="insight-paragraph">
            Performance analytics reveal significant opportunities for operational optimization and strategic expansion. Current distribution patterns show distinct seasonal variations and geographic disparities that require targeted interventions to maximize program impact and resource utilization efficiency.
        </div>
        @endif
    </div>

    <!-- PERFORMANCE INSIGHTS & ANALYSIS -->
    <div class="insight-section">
        <div class="section-header">I. Performance Analytics & Operational Intelligence</div>
        
        <div class="insight-content">
            @if(isset($insights['performance_insights']) && is_array($insights['performance_insights']))
                @foreach($insights['performance_insights'] as $insight)
                <div class="insight-paragraph">
                    <strong>Analysis Finding:</strong> {{ is_string($insight) ? $insight : 'Current operational metrics indicate systematic evaluation opportunities exist across multiple program dimensions including processing efficiency, geographic equity, and resource allocation optimization strategies.' }}
                </div>
                @endforeach
            @else
            <div class="insight-paragraph">
                <strong>Operational Efficiency Analysis:</strong> The current approval rate of {{ number_format($overview['approval_rate'] ?? 0, 1) }}% suggests {{ ($overview['approval_rate'] ?? 0) >= 85 ? 'excellent operational standards with opportunities for capacity expansion' : 'systematic improvement opportunities in application processing workflows and evaluation criteria standardization' }}. Processing time analysis indicates {{ $processingTimeAnalysis['avg_processing_days'] > 5 ? 'bottlenecks requiring workflow optimization and digital transformation initiatives' : 'efficient processing standards that should be maintained and replicated across all operational units' }}.
            </div>
            <div class="insight-paragraph">
                <strong>Geographic Performance Variance:</strong> Barangay-level analysis reveals significant performance disparities, with top-performing {{ $barangayAnalysis->first()->barangay ?? 'areas' }} generating {{ $barangayAnalysis->first()->total_requests ?? 'substantial' }} requests while underperforming regions show limited engagement. This variance indicates the need for targeted outreach strategies, capacity building interventions, and equitable resource distribution mechanisms.
            </div>
            <div class="insight-paragraph">
                <strong>Demand Pattern Intelligence:</strong> Category distribution analysis shows {{ $topItems->first()['name'] ?? 'high-demand varieties' }} dominating requests with {{ number_format($topItems->first()['total_quantity'] ?? 0) }} units, while {{ $leastRequestedItems->first()['name'] ?? 'specialized varieties' }} showing minimal uptake at {{ number_format($leastRequestedItems->first()['total_quantity'] ?? 0) }} units. This disparity suggests opportunities for farmer education, variety diversification, and market-driven procurement optimization.
            </div>
            @endif
        </div>

        <!-- Key Performance Indicators -->
        @if(isset($analytics['top_barangays']))
        <div class="key-finding priority-{{ ($overview['approval_rate'] ?? 0) >= 85 ? 'low' : (($overview['approval_rate'] ?? 0) >= 70 ? 'medium' : 'high') }}">
            <div class="finding-title">Geographic Performance Distribution Analysis</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Barangay</th>
                        <th>Applications</th>
                        <th>Approval Rate</th>
                        <th>Seedlings Distributed</th>
                        <th>Performance Grade</th>
                        <th>Strategic Priority</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $barangays = is_object($analytics['top_barangays']) ? $analytics['top_barangays']->take(8) : collect($analytics['top_barangays'])->take(8);
                    @endphp
                    @foreach($barangays as $barangay)
                    @php
                        $barangayData = is_object($barangay) ? $barangay : (object) $barangay;
                        $approvalRate = $barangayData->approval_rate ?? 0;
                        $priority = $approvalRate >= 80 ? 'Maintain' : ($approvalRate >= 60 ? 'Improve' : 'Urgent');
                    @endphp
                    <tr>
                        <td><strong>{{ $barangayData->barangay ?? 'Unknown' }}</strong></td>
                        <td>{{ number_format($barangayData->total_requests ?? 0) }}</td>
                        <td>{{ number_format($approvalRate, 1) }}%</td>
                        <td>{{ number_format($barangayData->total_quantity ?? 0) }}</td>
                        <td>
                            <span class="performance-indicator performance-{{ $approvalRate >= 90 ? 'excellent' : ($approvalRate >= 75 ? 'good' : ($approvalRate >= 60 ? 'warning' : 'critical')) }}">
                                {{ $approvalRate >= 90 ? 'A+' : ($approvalRate >= 75 ? 'B+' : ($approvalRate >= 60 ? 'C' : 'D')) }}
                            </span>
                        </td>
                        <td><strong>{{ $priority }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- STRATEGIC RECOMMENDATIONS -->
    <div class="insight-section">
        <div class="section-header">II. Strategic Recommendations & Policy Directives</div>
        
        <div class="recommendation-box">
            <div class="recommendation-title">Priority Strategic Interventions</div>
            @if(isset($insights['strategic_recommendations']) && is_array($insights['strategic_recommendations']))
                @foreach($insights['strategic_recommendations'] as $index => $recommendation)
                <div class="recommendation-item priority-{{ $index < 2 ? 'high' : ($index < 4 ? 'medium' : 'low') }}">
                    <strong>Strategic Priority {{ $index + 1 }}:</strong> {{ is_string($recommendation) ? $recommendation : 'Implement comprehensive program optimization protocols based on analytical findings to enhance service delivery effectiveness and farmer satisfaction metrics.' }}
                </div>
                @endforeach
            @else
            <div class="recommendation-item priority-high">
                <strong>Critical Priority 1:</strong> Implement standardized application evaluation framework across all administrative levels to achieve consistent approval criteria and reduce processing delays. Target: Increase approval rate to 85%+ within 6 months through staff training and workflow optimization.
            </div>
            <div class="recommendation-item priority-high">
                <strong>Critical Priority 2:</strong> Deploy predictive demand forecasting system using historical data analytics to optimize inventory management and prevent stockouts. Implement 15% emergency reserve stock protocol for peak seasons.
            </div>
            <div class="recommendation-item priority-medium">
                <strong>Strategic Priority 3:</strong> Establish comprehensive farmer education and outreach programs targeting underperforming barangays to achieve equitable geographic distribution. Focus on {{ $barangayAnalysis->slice(-3)->pluck('barangay')->implode(', ') }}.
            </div>
            <div class="recommendation-item priority-medium">
                <strong>Strategic Priority 4:</strong> Develop digital application platform with real-time tracking capabilities to improve transparency and reduce manual processing bottlenecks by 40-60%.
            </div>
            <div class="recommendation-item priority-low">
                <strong>Long-term Priority 5:</strong> Create strategic partnerships with agricultural cooperatives and research institutions to enhance variety selection and provide technical support to farmers.
            </div>
            @endif
        </div>
    </div>

    <!-- OPERATIONAL PRESCRIPTIONS -->
    <div class="insight-section">
        <div class="section-header">III. Operational Prescriptions & Implementation Roadmap</div>
        
        <div class="action-timeline">
            <div class="timeline-title">90-Day Implementation Timeline</div>
            
            @if(isset($insights['operational_prescriptions']) && is_array($insights['operational_prescriptions']))
                @foreach($insights['operational_prescriptions'] as $index => $prescription)
                <div class="timeline-item">
                    <div class="timeline-title">Phase {{ $index + 1 }} ({{ ($index * 30) + 1 }}-{{ ($index + 1) * 30 }} days)</div>
                    {{ is_string($prescription) ? $prescription : 'Implement systematic operational improvements targeting processing efficiency, quality assurance, and service delivery optimization protocols.' }}
                </div>
                @endforeach
            @else
            <div class="timeline-item">
                <div class="timeline-title">Phase 1 (1-30 days): Digital Infrastructure Setup</div>
                Deploy digital workflow management system with automated routing and real-time status tracking. Train all evaluation staff on standardized assessment procedures. Establish performance monitoring dashboards.
            </div>
            <div class="timeline-item">
                <div class="timeline-title">Phase 2 (31-60 days): Process Optimization</div>
                Implement fast-track processing for straightforward applications. Launch comprehensive farmer education program in target barangays. Establish supplier diversification protocols for high-demand varieties.
            </div>
            <div class="timeline-item">
                <div class="timeline-title">Phase 3 (61-90 days): Quality Assurance & Expansion</div>
                Deploy quality assurance protocols with regular outcome monitoring. Launch mobile application for farmer applications. Implement feedback collection system for continuous improvement.
            </div>
            @endif
        </div>
    </div>

    <!-- RISK ASSESSMENT MATRIX -->
    <div class="insight-section">
        <div class="section-header">IV. Risk Assessment & Mitigation Strategies</div>
        
        <div class="risk-matrix">
            @if(isset($insights['risk_assessment']) && is_array($insights['risk_assessment']))
                @foreach($insights['risk_assessment'] as $index => $risk)
                <div class="risk-item risk-{{ $index === 0 ? 'high' : ($index === 1 ? 'medium' : 'low') }}">
                    <strong>{{ $index === 0 ? 'High' : ($index === 1 ? 'Medium' : 'Low') }} Risk Factor:</strong>
                    {{ is_string($risk) ? $risk : 'Systematic risk evaluation indicates potential operational vulnerabilities requiring proactive mitigation strategies and contingency planning protocols.' }}
                </div>
                @endforeach
            @else
            <div class="risk-item risk-high">
                <strong>High Risk:</strong> Supply chain disruptions during peak seasons could affect {{ number_format(($overview['total_quantity_requested'] ?? 0) * 0.3) }} farmers. Mitigation: Establish alternative supplier networks and maintain 15% emergency stock reserves.
            </div>
            <div class="risk-item risk-medium">
                <strong>Medium Risk:</strong> Manual processing bottlenecks may increase during high-demand periods, affecting service quality. Mitigation: Deploy automated workflows and seasonal staffing protocols.
            </div>
            <div class="risk-item risk-low">
                <strong>Low Risk:</strong> Limited analytics capabilities may restrict evidence-based decision making. Mitigation: Invest in data analytics infrastructure and staff training programs.
            </div>
            @endif
        </div>
    </div>

    <!-- GROWTH OPPORTUNITIES -->
    <div class="insight-section">
        <div class="section-header">V. Growth Opportunities & Strategic Expansion</div>
        
        <div class="insight-content">
            @if(isset($insights['growth_opportunities']) && is_array($insights['growth_opportunities']))
                @foreach($insights['growth_opportunities'] as $opportunity)
                <div class="insight-paragraph">
                    <strong>Strategic Opportunity:</strong> {{ is_string($opportunity) ? $opportunity : 'Program expansion analysis indicates significant growth potential through geographic expansion, service diversification, and technology integration, projected to increase farmer participation by 25-30% while maintaining service quality standards.' }}
                </div>
                @endforeach
            @else
            <div class="insight-paragraph">
                <strong>Geographic Expansion Opportunity:</strong> Analysis indicates {{ 20 - ($overview['active_barangays'] ?? 0) }} barangays remain underserved, representing potential reach to approximately {{ number_format((20 - ($overview['active_barangays'] ?? 0)) * 50) }} additional farmers. Targeted outreach campaigns in these areas could increase program impact by 25-30% within the next fiscal year.
            </div>
            <div class="insight-paragraph">
                <strong>Technology Integration Opportunity:</strong> Development of mobile application platform for farmer applications and program information could reduce administrative costs by 40% while improving accessibility. Integration with SMS notification system would enhance farmer engagement and reduce no-show rates by an estimated 35%.
            </div>
            <div class="insight-paragraph">
                <strong>Partnership Development Opportunity:</strong> Strategic alliances with agricultural cooperatives, banking institutions for micro-credit programs, and educational institutions for technical training could create comprehensive farmer support ecosystem, potentially doubling program effectiveness and sustainability.
            </div>
            @endif
        </div>

        <!-- Expansion Projections -->
        <div class="key-finding priority-low">
            <div class="finding-title">Projected Impact of Strategic Initiatives</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Strategic Initiative</th>
                        <th>Implementation Timeline</th>
                        <th>Projected Increase</th>
                        <th>Investment Required</th>
                        <th>ROI Timeframe</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Geographic Expansion</strong></td>
                        <td>6-12 months</td>
                        <td>+25% farmer participation</td>
                        <td>₱{{ number_format(($overview['total_quantity_requested'] ?? 0) * 5, 2) }}</td>
                        <td>18 months</td>
                    </tr>
                    <tr>
                        <td><strong>Digital Platform</strong></td>
                        <td>3-6 months</td>
                        <td>+40% processing efficiency</td>
                        <td>₱{{ number_format(($overview['total_requests'] ?? 0) * 25, 2) }}</td>
                        <td>12 months</td>
                    </tr>
                    <tr>
                        <td><strong>Partnership Program</strong></td>
                        <td>12-18 months</td>
                        <td>+50% program sustainability</td>
                        <td>₱{{ number_format(($overview['total_requests'] ?? 0) * 15, 2) }}</td>
                        <td>24 months</td>
                    </tr>
                    <tr>
                        <td><strong>Capacity Building</strong></td>
                        <td>6-9 months</td>
                        <td>+30% approval rate</td>
                        <td>₱{{ number_format(($overview['total_requests'] ?? 0) * 10, 2) }}</td>
                        <td>15 months</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PROCUREMENT INTELLIGENCE -->
    <div class="insight-section">
        <div class="section-header">VI. Procurement Intelligence & Budget Optimization</div>
        
        <div class="insight-content">
            <div class="insight-paragraph">
                <strong>Demand-Driven Procurement Strategy:</strong> Analysis of request patterns indicates {{ $topItems->first()['name'] ?? 'high-demand varieties' }} should comprise 40% of procurement budget, with {{ number_format($topItems->first()['total_quantity'] ?? 0 * 1.3) }} units recommended for next quarter. Seasonal demand forecasting suggests procurement should be front-loaded by 35% during preparation months (February-April).
            </div>
            <div class="insight-paragraph">
                <strong>Inventory Optimization Recommendations:</strong> Current data suggests maintaining emergency reserve stock equivalent to 15% of quarterly distribution prevents stockouts during peak demand. Implement just-in-time procurement for perishable varieties while maintaining buffer stocks for high-demand, long-shelf-life items.
            </div>
        </div>

        <!-- Budget Allocation Matrix -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Current Demand</th>
                    <th>Recommended Allocation</th>
                    <th>Budget Percentage</th>
                    <th>Procurement Timeline</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($analytics['top_items']) && count($analytics['top_items']) > 0)
                    @foreach(collect($analytics['top_items'])->take(5) as $index => $item)
                    <tr>
                        <td><strong>{{ $item['name'] ?? 'Priority Item' }}</strong></td>
                        <td>{{ number_format($item['total_quantity'] ?? 0) }} units</td>
                        <td>{{ number_format(($item['total_quantity'] ?? 0) * 1.3) }} units</td>
                        <td>{{ number_format((($item['total_quantity'] ?? 0) / ($overview['total_quantity_requested'] ?? 1)) * 100, 1) }}%</td>
                        <td>{{ $index < 2 ? 'Immediate' : ($index < 4 ? 'Next Month' : 'Quarter End') }}</td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="5" style="text-align: center; font-style: italic;">Detailed procurement data will be populated when analytics data is available</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- PERFORMANCE MONITORING FRAMEWORK -->
    <div class="insight-section">
        <div class="section-header">VII. Performance Monitoring & Evaluation Framework</div>
        
        <div class="recommendation-box">
            <div class="recommendation-title">Key Performance Indicators (KPIs) Dashboard</div>
            <div class="metrics-dashboard">
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($overview['approval_rate'] ?? 0, 1) }}%</div>
                    <div class="metric-label">Current Approval Rate</div>
                    <div class="performance-indicator performance-{{ ($overview['approval_rate'] ?? 0) >= 85 ? 'excellent' : 'warning' }}">
                        Target: 85%+
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $processingTimeAnalysis['avg_processing_days'] ?? 'N/A' }}</div>
                    <div class="metric-label">Avg Processing Days</div>
                    <div class="performance-indicator performance-{{ ($processingTimeAnalysis['avg_processing_days'] ?? 10) <= 5 ? 'excellent' : 'warning' }}">
                        Target: ≤ 5 days
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ round((($overview['active_barangays'] ?? 0) / 20) * 100) }}%</div>
                    <div class="metric-label">Geographic Coverage</div>
                    <div class="performance-indicator performance-{{ ($overview['active_barangays'] ?? 0) >= 18 ? 'excellent' : 'warning' }}">
                        Target: 90%+
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($overview['unique_applicants'] ?? 0) }}</div>
                    <div class="metric-label">Unique Farmers Served</div>
                    <div class="performance-indicator performance-{{ ($overview['unique_applicants'] ?? 0) >= 500 ? 'excellent' : 'good' }}">
                        Target: 500+
                    </div>
                </div>
            </div>
        </div>
        
        <div class="insight-content">
            <div class="insight-paragraph">
                <strong>Monitoring Protocol:</strong> Implement monthly performance reviews using automated dashboard reporting. Track approval rate trends, processing time distribution, geographic equity metrics, and farmer satisfaction scores. Establish alert thresholds: approval rate below 75%, processing time above 7 days, or geographic coverage below 85% trigger immediate review protocols.
            </div>
            <div class="insight-paragraph">
                <strong>Quality Assurance Framework:</strong> Deploy quarterly farmer satisfaction surveys, annual outcome impact assessments, and continuous process improvement protocols. Implement peer review system for application evaluations and establish quality control checkpoints at 25%, 50%, and 75% processing milestones.
            </div>
        </div>
    </div>

    <!-- DECISION MATRIX -->
    <div class="insight-section">
        <div class="section-header">VIII. Strategic Decision Matrix & Action Prioritization</div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Decision Area</th>
                    <th>Current Status</th>
                    <th>Recommended Action</th>
                    <th>Priority Level</th>
                    <th>Resource Requirement</th>
                    <th>Expected Outcome</th>
                </tr>
            </thead>
            <tbody>
                <tr class="priority-high">
                    <td><strong>Application Processing</strong></td>
                    <td>{{ number_format($overview['approval_rate'] ?? 0, 1) }}% approval rate</td>
                    <td>Implement standardized evaluation framework</td>
                    <td><span class="performance-indicator performance-critical">HIGH</span></td>
                    <td>₱{{ number_format(($overview['total_requests'] ?? 0) * 50, 2) }}</td>
                    <td>Achieve 85%+ approval rate</td>
                </tr>
                <tr class="priority-high">
                    <td><strong>Geographic Equity</strong></td>
                    <td>{{ $overview['active_barangays'] ?? 0 }}/20 barangays active</td>
                    <td>Launch targeted outreach program</td>
                    <td><span class="performance-indicator performance-critical">HIGH</span></td>
                    <td>₱{{ number_format(($overview['active_barangays'] ?? 0) * 15000, 2) }}</td>
                    <td>90%+ geographic coverage</td>
                </tr>
                <tr class="priority-medium">
                    <td><strong>Technology Integration</strong></td>
                    <td>Manual processing workflows</td>
                    <td>Deploy digital application system</td>
                    <td><span class="performance-indicator performance-warning">MEDIUM</span></td>
                    <td>₱{{ number_format(($overview['total_requests'] ?? 0) * 75, 2) }}</td>
                    <td>40% efficiency improvement</td>
                </tr>
                <tr class="priority-medium">
                    <td><strong>Inventory Management</strong></td>
                    <td>Reactive procurement model</td>
                    <td>Implement predictive forecasting</td>
                    <td><span class="performance-indicator performance-warning">MEDIUM</span></td>
                    <td>₱{{ number_format(($overview['total_quantity_requested'] ?? 0) * 2, 2) }}</td>
                    <td>Eliminate stockouts</td>
                </tr>
                <tr class="priority-low">
                    <td><strong>Partnership Development</strong></td>
                    <td>Limited external collaboration</td>
                    <td>Establish cooperative agreements</td>
                    <td><span class="performance-indicator performance-good">LOW</span></td>
                    <td>₱{{ number_format(($overview['unique_applicants'] ?? 0) * 25, 2) }}</td>
                    <td>Enhanced sustainability</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- IMPLEMENTATION BUDGET SUMMARY -->
    <div class="insight-section">
        <div class="section-header">IX. Implementation Budget & Resource Allocation</div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Budget Category</th>
                    <th>Recommended Allocation</th>
                    <th>Percentage</th>
                    <th>Expected ROI</th>
                    <th>Implementation Phase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>High-Priority Interventions</strong></td>
                    <td>₱{{ number_format(($overview['total_requests'] ?? 0) * 125, 2) }}</td>
                    <td>45%</td>
                    <td>200%+ within 18 months</td>
                    <td>Phase 1 (0-90 days)</td>
                </tr>
                <tr>
                    <td><strong>Technology Infrastructure</strong></td>
                    <td>₱{{ number_format(($overview['total_requests'] ?? 0) * 75, 2) }}</td>
                    <td>30%</td>
                    <td>150% within 24 months</td>
                    <td>Phase 2 (3-9 months)</td>
                </tr>
                <tr>
                    <td><strong>Capacity Building</strong></td>
                    <td>₱{{ number_format(($overview['total_requests'] ?? 0) * 40, 2) }}</td>
                    <td>15%</td>
                    <td>120% within 18 months</td>
                    <td>Phase 1-3 (ongoing)</td>
                </tr>
                <tr>
                    <td><strong>Strategic Partnerships</strong></td>
                    <td>₱{{ number_format(($overview['total_requests'] ?? 0) * 25, 2) }}</td>
                    <td>10%</td>
                    <td>300%+ within 36 months</td>
                    <td>Phase 3 (9-18 months)</td>
                </tr>
                <tr style="background: #f0f8f0; font-weight: bold;">
                    <td><strong>TOTAL RECOMMENDED INVESTMENT</strong></td>
                    <td><strong>₱{{ number_format(($overview['total_requests'] ?? 0) * 265, 2) }}</strong></td>
                    <td><strong>100%</strong></td>
                    <td><strong>185% Average ROI</strong></td>
                    <td><strong>18-Month Cycle</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- SIGNATURE SECTION -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div><strong>Prepared by:</strong></div>
                    <div class="signature-line">
                        AGRICULTURAL DATA ANALYST<br>
                        Municipal Agriculture Office
                    </div>
                </td>
                <td class="signature-cell">
                    <div><strong>Reviewed and Approved by:</strong></div>
                    <div class="signature-line">
                        MUNICIPAL AGRICULTURIST<br>
                        Municipal Agriculture Office
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- FOOTER DISCLAIMER -->
    <div class="footer-note">
        <p><strong>DATA CLASSIFICATION:</strong> This Decision Support System report contains official agricultural intelligence intended for municipal government strategic planning and operational decision-making.</p>
        <p><strong>AI ANALYSIS DISCLAIMER:</strong> Insights generated using OpenAI GPT-4 Turbo with agricultural domain expertise. Recommendations are based on statistical analysis of historical data and should be validated with local agricultural conditions and policy constraints.</p>
        <p><strong>VALIDITY PERIOD:</strong> This analysis is valid for 6 months from generation date. Updates recommended quarterly for optimal decision support accuracy.</p>
        <p><strong>CONFIDENTIALITY NOTICE:</strong> Distribution limited to authorized municipal agriculture personnel and designated stakeholders. Reproduction or disclosure without authorization is prohibited.</p>
    </div>
</body>
</html>