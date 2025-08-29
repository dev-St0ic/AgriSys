<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Municipal Agriculture Office - Seedling Distribution Report' }}</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
            @top-center {
                content: "CONFIDENTIAL - FOR OFFICIAL USE ONLY";
                font-size: 8px;
                color: #666;
            }
            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 10px;
                color: #666;
            }
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.5;
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
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0 20px 0;
            text-decoration: underline;
            color: #2c5530;
        }
        
        .report-subtitle {
            font-size: 14px;
            text-align: center;
            margin-bottom: 25px;
            font-style: italic;
        }
        
        .report-metadata {
            margin-bottom: 30px;
            border: 1px solid #000;
            padding: 15px;
            background-color: #f8f9fa;
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
            width: 30%;
            font-weight: bold;
            padding: 3px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .metadata-value {
            display: table-cell;
            width: 70%;
            padding: 3px 0 3px 15px;
            border-bottom: 1px dotted #ccc;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-header {
            font-size: 14px;
            font-weight: bold;
            color: #2c5530;
            border-bottom: 2px solid #2c5530;
            padding-bottom: 5px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .subsection {
            margin-bottom: 20px;
        }
        
        .subsection-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        
        .executive-summary {
            background-color: #f0f8ff;
            border: 1px solid #4169e1;
            padding: 20px;
            margin: 20px 0;
            border-left: 5px solid #4169e1;
        }
        
        .summary-title {
            font-size: 13px;
            font-weight: bold;
            color: #4169e1;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .metrics-table th,
        .metrics-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        
        .metrics-table th {
            background-color: #e8f4f8;
            font-weight: bold;
            font-size: 11px;
        }
        
        .metrics-table .metric-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c5530;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #e8f4f8;
            font-weight: bold;
            text-align: center;
        }
        
        .data-table .number {
            text-align: right;
        }
        
        .shortage-alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 5px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
        }
        
        .shortage-title {
            font-size: 13px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }
        
        .critical-alert {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
            border-left: 5px solid #dc3545;
            padding: 15px;
            margin: 15px 0;
        }
        
        .critical-title {
            font-size: 13px;
            font-weight: bold;
            color: #721c24;
            margin-bottom: 10px;
        }
        
        .recommendation-box {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-left: 5px solid #17a2b8;
            padding: 15px;
            margin: 15px 0;
        }
        
        .recommendation-title {
            font-size: 13px;
            font-weight: bold;
            color: #0c5460;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .procurement-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .procurement-table th,
        .procurement-table td {
            border: 2px solid #000;
            padding: 10px;
            text-align: left;
        }
        
        .procurement-table th {
            background-color: #2c5530;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .procurement-table .priority-high {
            background-color: #ffebee;
            font-weight: bold;
        }
        
        .procurement-table .priority-medium {
            background-color: #fff8e1;
        }
        
        .procurement-table .priority-low {
            background-color: #e8f5e8;
        }
        
        .status-indicator {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-critical {
            background-color: #dc3545;
            color: white;
        }
        
        .status-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .status-good {
            background-color: #28a745;
            color: white;
        }
        
        .status-excellent {
            background-color: #007bff;
            color: white;
        }
        
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-cell {
            width: 50%;
            padding: 30px 20px;
            text-align: center;
            vertical-align: top;
            border: 1px solid #000;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .footer-note {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-size: 10px;
            text-align: center;
            font-style: italic;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .list-style {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .list-style li {
            margin-bottom: 5px;
        }
        
        .budget-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-weight: bold;
        }
        
        .budget-table th,
        .budget-table td {
            border: 2px solid #000;
            padding: 8px;
            text-align: right;
        }
        
        .budget-table th {
            background-color: #2c5530;
            color: white;
            text-align: center;
        }
        
        .budget-total {
            background-color: #f0f0f0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- OFFICIAL LETTERHEAD -->
    <div class="letterhead">
        <div class="republic-header">REPUBLIC OF THE PHILIPPINES</div>
        <div class="office-name">MUNICIPAL AGRICULTURE OFFICE</div>
        <div class="office-name">[MUNICIPALITY NAME]</div>
        <div class="address">Provincial Address, Philippines</div>
        <div class="address">Tel: (000) 000-0000 | Email: agriculture@municipality.gov.ph</div>
    </div>

    <!-- REPORT TITLE -->
    <div class="report-title">SEEDLING DISTRIBUTION PROGRAM</div>
    <div class="report-title">DECISION SUPPORT SYSTEM REPORT</div>
    <div class="report-subtitle">Agricultural Analytics and Procurement Recommendations</div>

    <!-- REPORT METADATA -->
    <div class="report-metadata">
        <div class="metadata-grid">
            <div class="metadata-row">
                <div class="metadata-label">Report Period:</div>
                <div class="metadata-value">{{ $period ?? 'Complete Fiscal Year Analysis' }}</div>
            </div>
            <div class="metadata-row">
                <div class="metadata-label">Date Generated:</div>
                <div class="metadata-value">{{ $generated_at ?? now()->format('F j, Y \a\t g:i A') }}</div>
            </div>
            <div class="metadata-row">
                <div class="metadata-label">Report Classification:</div>
                <div class="metadata-value">OFFICIAL USE ONLY</div>
            </div>
            <div class="metadata-row">
                <div class="metadata-label">AI Analysis Confidence:</div>
                <div class="metadata-value">{{ strtoupper($ai_confidence ?? 'HIGH') }} CONFIDENCE LEVEL</div>
            </div>
            <div class="metadata-row">
                <div class="metadata-label">Prepared By:</div>
                <div class="metadata-value">Municipal Agricultural Data Analytics Division</div>
            </div>
        </div>
    </div>

    <!-- EXECUTIVE SUMMARY -->
    <div class="executive-summary">
        <div class="summary-title">Executive Summary</div>
        @if(isset($insights['executive_summary']) && is_array($insights['executive_summary']))
        <ul class="list-style">
            @foreach($insights['executive_summary'] as $summary)
            <li>{{ is_string($summary) ? $summary : 'Data analysis indicates systematic evaluation required for optimal resource allocation.' }}</li>
            @endforeach
        </ul>
        @else
        <ul class="list-style">
            <li>Total seedling requests processed: {{ number_format($overview['total_requests'] ?? 0) }} applications</li>
            <li>Program approval efficiency: {{ number_format($overview['approval_rate'] ?? 0, 1) }}% approval rate</li>
            <li>Geographic coverage: {{ $overview['active_barangays'] ?? 0 }} barangays actively participating</li>
            <li>Farmer participation: {{ number_format($overview['unique_applicants'] ?? 0) }} unique agricultural beneficiaries</li>
        </ul>
        @endif
    </div>

    <!-- PERFORMANCE METRICS -->
    <div class="section">
        <div class="section-header">I. Program Performance Metrics</div>
        
        <table class="metrics-table">
            <thead>
                <tr>
                    <th>Performance Indicator</th>
                    <th>Current Value</th>
                    <th>Target Benchmark</th>
                    <th>Performance Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Total Applications Processed</strong></td>
                    <td class="metric-value">{{ number_format($overview['total_requests'] ?? 0) }}</td>
                    <td>1,000 annually</td>
                    <td>
                        <span class="status-indicator status-{{ ($overview['total_requests'] ?? 0) > 800 ? 'excellent' : (($overview['total_requests'] ?? 0) > 500 ? 'good' : 'warning') }}">
                            {{ ($overview['total_requests'] ?? 0) > 800 ? 'EXCELLENT' : (($overview['total_requests'] ?? 0) > 500 ? 'SATISFACTORY' : 'NEEDS IMPROVEMENT') }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Approval Rate</strong></td>
                    <td class="metric-value">{{ number_format($overview['approval_rate'] ?? 0, 1) }}%</td>
                    <td>85% minimum</td>
                    <td>
                        <span class="status-indicator status-{{ ($overview['approval_rate'] ?? 0) >= 85 ? 'excellent' : (($overview['approval_rate'] ?? 0) >= 70 ? 'good' : 'critical') }}">
                            {{ ($overview['approval_rate'] ?? 0) >= 85 ? 'EXCEEDS TARGET' : (($overview['approval_rate'] ?? 0) >= 70 ? 'MEETS STANDARD' : 'BELOW TARGET') }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Barangay Coverage</strong></td>
                    <td class="metric-value">{{ $overview['active_barangays'] ?? 0 }}</td>
                    <td>All 20 Barangays</td>
                    <td>
                        <span class="status-indicator status-{{ ($overview['active_barangays'] ?? 0) >= 18 ? 'excellent' : (($overview['active_barangays'] ?? 0) >= 15 ? 'good' : 'warning') }}">
                            {{ round((($overview['active_barangays'] ?? 0) / 20) * 100) }}% COVERAGE
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Farmer Participation</strong></td>
                    <td class="metric-value">{{ number_format($overview['unique_applicants'] ?? 0) }}</td>
                    <td>500 unique farmers</td>
                    <td>
                        <span class="status-indicator status-{{ ($overview['unique_applicants'] ?? 0) >= 500 ? 'excellent' : (($overview['unique_applicants'] ?? 0) >= 300 ? 'good' : 'warning') }}">
                            {{ ($overview['unique_applicants'] ?? 0) >= 500 ? 'TARGET MET' : 'EXPANSION NEEDED' }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- SHORTAGE ANALYSIS -->
    <div class="section">
        <div class="section-header">II. Critical Shortage Analysis</div>
        
        @if(isset($analytics['least_items']) && count($analytics['least_items']) > 0)
        <div class="critical-alert">
            <div class="critical-title">CRITICAL: Underutilized Seedling Varieties</div>
            <p>The following seedling varieties show critically low demand, indicating potential supply chain issues, farmer awareness gaps, or market demand problems:</p>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Seedling Variety</th>
                    <th>Category</th>
                    <th>Total Requests</th>
                    <th>Quantity Distributed</th>
                    <th>Risk Assessment</th>
                </tr>
            </thead>
            <tbody>
                @foreach(collect($analytics['least_items'])->take(10) as $item)
                <tr>
                    <td>{{ $item['name'] ?? 'Unspecified Variety' }}</td>
                    <td>{{ ucfirst($item['category'] ?? 'General') }}</td>
                    <td class="number">{{ number_format($item['request_count'] ?? 0) }}</td>
                    <td class="number">{{ number_format($item['total_quantity'] ?? 0) }}</td>
                    <td>
                        <span class="status-indicator status-{{ ($item['request_count'] ?? 0) < 5 ? 'critical' : 'warning' }}">
                            {{ ($item['request_count'] ?? 0) < 5 ? 'CRITICAL' : 'MONITOR' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(isset($analytics['top_items']) && count($analytics['top_items']) > 0)
        <div class="shortage-alert">
            <div class="shortage-title">HIGH DEMAND ALERT: Supply Strain Analysis</div>
            <p>The following varieties show extremely high demand that may exceed current supply capacity:</p>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>High-Demand Variety</th>
                    <th>Category</th>
                    <th>Total Requests</th>
                    <th>Quantity Needed</th>
                    <th>Supply Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach(collect($analytics['top_items'])->take(5) as $item)
                <tr>
                    <td>{{ $item['name'] ?? 'High-Demand Variety' }}</td>
                    <td>{{ ucfirst($item['category'] ?? 'General') }}</td>
                    <td class="number">{{ number_format($item['request_count'] ?? 0) }}</td>
                    <td class="number">{{ number_format($item['total_quantity'] ?? 0) }}</td>
                    <td>
                        <span class="status-indicator status-{{ ($item['total_quantity'] ?? 0) > 1000 ? 'critical' : 'warning' }}">
                            {{ ($item['total_quantity'] ?? 0) > 1000 ? 'SHORTAGE RISK' : 'MONITOR' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- PROCUREMENT RECOMMENDATIONS -->
    <div class="section">
        <div class="section-header">III. Strategic Procurement Recommendations</div>
        
        <div class="recommendation-box">
            <div class="recommendation-title">Immediate Procurement Requirements</div>
            @if(isset($insights['strategic_recommendations']) && is_array($insights['strategic_recommendations']))
            <ul class="list-style">
                @foreach($insights['strategic_recommendations'] as $recommendation)
                <li>{{ is_string($recommendation) ? $recommendation : 'Implement systematic procurement protocols based on demand analysis.' }}</li>
                @endforeach
            </ul>
            @else
            <ul class="list-style">
                <li>Increase procurement of high-demand vegetable seedlings by 25% for next quarter</li>
                <li>Diversify fruit tree seedling varieties based on regional climate adaptability</li>
                <li>Establish emergency seedling reserve stock equivalent to 15% of quarterly distribution</li>
                <li>Implement pre-order system for farmers to improve demand forecasting accuracy</li>
            </ul>
            @endif
        </div>

        <div class="subsection">
            <div class="subsection-title">Priority Procurement Schedule</div>
            <table class="procurement-table">
                <thead>
                    <tr>
                        <th>Seedling Category</th>
                        <th>Recommended Quantity</th>
                        <th>Estimated Budget</th>
                        <th>Procurement Priority</th>
                        <th>Target Month</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($analytics['top_items']) && count($analytics['top_items']) > 0)
                        @foreach(collect($analytics['top_items'])->take(5) as $index => $item)
                        <tr class="priority-{{ $index < 2 ? 'high' : ($index < 4 ? 'medium' : 'low') }}">
                            <td>{{ $item['name'] ?? 'Priority Variety' }}</td>
                            <td class="number">{{ number_format(($item['total_quantity'] ?? 0) * 1.3) }} units</td>
                            <td class="number">₱{{ number_format(($item['total_quantity'] ?? 0) * 1.3 * 15, 2) }}</td>
                            <td>
                                <span class="status-indicator status-{{ $index < 2 ? 'critical' : ($index < 4 ? 'warning' : 'good') }}">
                                    {{ $index < 2 ? 'HIGH' : ($index < 4 ? 'MEDIUM' : 'LOW') }}
                                </span>
                            </td>
                            <td>{{ $index < 2 ? 'IMMEDIATE' : ($index < 4 ? 'NEXT MONTH' : 'QUARTER END') }}</td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="subsection">
            <div class="subsection-title">Budget Allocation Summary</div>
            <table class="budget-table">
                <thead>
                    <tr>
                        <th>Budget Category</th>
                        <th>Recommended Allocation</th>
                        <th>% of Total Budget</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>High-Priority Vegetables</td>
                        <td>₱{{ number_format(($overview['total_quantity_requested'] ?? 0) * 0.4 * 15, 2) }}</td>
                        <td>40%</td>
                    </tr>
                    <tr>
                        <td>Fruit Tree Seedlings</td>
                        <td>₱{{ number_format(($overview['total_quantity_requested'] ?? 0) * 0.35 * 18, 2) }}</td>
                        <td>35%</td>
                    </tr>
                    <tr>
                        <td>Organic Fertilizers</td>
                        <td>₱{{ number_format(($overview['total_quantity_requested'] ?? 0) * 0.15 * 25, 2) }}</td>
                        <td>15%</td>
                    </tr>
                    <tr>
                        <td>Emergency Reserve Stock</td>
                        <td>₱{{ number_format(($overview['total_quantity_requested'] ?? 0) * 0.1 * 15, 2) }}</td>
                        <td>10%</td>
                    </tr>
                    <tr class="budget-total">
                        <td><strong>TOTAL RECOMMENDED BUDGET</strong></td>
                        <td><strong>₱{{ number_format(($overview['total_quantity_requested'] ?? 0) * 16.25, 2) }}</strong></td>
                        <td><strong>100%</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- BARANGAY PERFORMANCE ANALYSIS -->
    @if(isset($analytics['top_barangays']))
    <div class="section">
        <div class="section-header">IV. Barangay-Level Distribution Analysis</div>
        
        <div class="subsection">
            <div class="subsection-title">Top Performing Barangays</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Barangay</th>
                        <th>Total Requests</th>
                        <th>Approved</th>
                        <th>Approval Rate</th>
                        <th>Total Seedlings</th>
                        <th>Performance Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $barangays = is_object($analytics['top_barangays']) ? $analytics['top_barangays']->take(10) : collect($analytics['top_barangays'])->take(10);
                    @endphp
                    @foreach($barangays as $barangay)
                    @php
                        $barangayData = is_object($barangay) ? $barangay : (object) $barangay;
                        $approvalRate = $barangayData->approval_rate ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $barangayData->barangay ?? 'Unknown Barangay' }}</td>
                        <td class="number">{{ number_format($barangayData->total_requests ?? 0) }}</td>
                        <td class="number">{{ number_format($barangayData->approved ?? 0) }}</td>
                        <td class="number">{{ number_format($approvalRate, 1) }}%</td>
                        <td class="number">{{ number_format($barangayData->total_quantity ?? 0) }}</td>
                        <td>
                            <span class="status-indicator status-{{ $approvalRate >= 90 ? 'excellent' : ($approvalRate >= 75 ? 'good' : 'warning') }}">
                                {{ $approvalRate >= 90 ? 'A+' : ($approvalRate >= 75 ? 'B+' : 'C') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- OPERATIONAL RECOMMENDATIONS -->
    <div class="section">
        <div class="section-header">V. Operational Prescriptions and Action Items</div>
        
        @if(isset($insights['operational_prescriptions']) && is_array($insights['operational_prescriptions']))
        <div class="recommendation-box">
            <div class="recommendation-title">Immediate Implementation Required</div>
            <ul class="list-style">
                @foreach($insights['operational_prescriptions'] as $prescription)
                <li><strong>Action Item:</strong> {{ is_string($prescription) ? $prescription : 'Implement systematic operational improvements as identified by data analysis.' }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(isset($insights['risk_assessment']) && is_array($insights['risk_assessment']))
        <div class="critical-alert">
            <div class="critical-title">Risk Mitigation Requirements</div>
            <ul class="list-style">
                @foreach($insights['risk_assessment'] as $risk)
                <li><strong>Risk Factor:</strong> {{ is_string($risk) ? $risk : 'Systematic risk assessment indicates need for operational review and mitigation strategies.' }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- GROWTH OPPORTUNITIES -->
    @if(isset($insights['growth_opportunities']) && is_array($insights['growth_opportunities']))
    <div class="section">
        <div class="section-header">VI. Strategic Growth and Expansion Opportunities</div>
        
        <div class="recommendation-box">
            <div class="recommendation-title">Program Expansion Recommendations</div>
            <ul class="list-style">
                @foreach($insights['growth_opportunities'] as $opportunity)
                <li><strong>Opportunity:</strong> {{ is_string($opportunity) ? $opportunity : 'Expand program capacity based on demonstrated community demand and successful implementation metrics.' }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- SIGNATURE SECTION -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div>Prepared by:</div>
                    <div class="signature-line">
                        AGRICULTURAL DATA ANALYST<br>
                        Municipal Agriculture Office
                    </div>
                </td>
                <td class="signature-cell">
                    <div>Reviewed and Approved by:</div>
                    <div class="signature-line">
                        MUNICIPAL AGRICULTURIST<br>
                        Municipal Agriculture Office
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- FOOTER NOTE -->
    <div class="footer-note">
        <p><strong>CLASSIFICATION:</strong> This report contains official information intended for municipal government use only.</p>
        <p><strong>DATA SOURCE:</strong> Municipal Agriculture Office Seedling Distribution Database</p>
        <p><strong>AI ANALYSIS:</strong> Generated using OpenAI GPT-4
    </div>
</body>
</html>