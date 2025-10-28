<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSS Report - {{ $data['period']['month'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2E7D32;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2E7D32;
            font-size: 24px;

            margin: 0;
        }

        .header h2 {
            color: #4CAF50;
            font-size: 18px;

            margin: 5px 0;
        }

        .header p {
            color: #666;

            margin: 5px 0;
        }

        .section {

            margin-bottom: 25px;
        }

        .section-title {
            background-color: #f5f5f5;
            color: #2E7D32;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            border-left: 4px solid #2E7D32;
            margin-bottom: 15px;
        }

        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin:
                20px 0;
            text-align: center;
        }

        .stat-box {
            flex: 1;
            padding: 15px;
            margin: 0 5px;
            background- color: #f9f9f9;
            border-radius: 5px;
        }

        .stat-number {
            font-size: 18px;

            font-weight: bold;
            color: #2E7D32;
        }

        .stat-label {
            font-size: 11p x;
            color: #666;
            margin-top: 5px;
        }

        .recommendations {

            display: flex;
            justify-content: space-between;
        }

        .rec-column {
            flex: 1;
            margin: 0 10px;
        }

        .rec-column h4 {
            font-size: 13px;
            margin-b ottom: 10px;

            padding: 5px;

            border-radi u s:
                3px;
        }

        .immediate {
            backgroun d -color: #ffebee;
            c olor: #c62828;
        }

        .short-term {
            backgro u nd-color: #fff3 e 0;
            color: #ef6c00;
        }

        .long-term {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .rec-column ul {

            list-style: none;
            padding: 0;
            margin: 0;
        }

        .rec-column li {
            paddi ng: 5px 0;
            font-size: 11px;
            border-bottom: 1px solid #eee;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;

            font-size:
                9px;
            font-wei g ht: bold;


            margin-right: 5px;
        }

        .badg e -danger {
            bac kg round-color: #f44336;
            col o r: white;
        }

        .badg e -warning {
            ba c k ground-color: #ff9800;
            co l or: white;
        }

        .bad g e-success {
            b a ck ground-color: #4caf50;
            color: white;
        }

        .badge-primary {
            background-color: #2196f3;
            color: white;
        }

        .t able {

            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .table th,
        .table td {
            bor der: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-siz e: 11px;
        }

        .table th {
            background-color: #f5f5f5;
            font-weight: bo ld;
        }

        .findings-issues {
            display: flex;
            jus tify-content: space-be t ween;
        }

        .findings,
        .issues {
            flex: 1;

            margin: 0 10px;

        }

        .findings ul,
        .issues ul {
            list-style: none;
            padding: 0;
        }

        .findings li,
        .issues l i {

            p adding: 5px 0;

            font-size: 1 1px;
            border-bottom: 1px solid #eee;
        }

        .check-icon {
            color: #4caf50;
        }

        .alert-icon {
            color: #f44336;
        }

        .footer {
            margin-to p: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;

            font-size: 10px;
            color: #666;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>Decision Support System Report</h1>
        <h2>{{ $data['period']['month'] }}</h2>
        <p>AI-Powered Agricultural Intelligence Analysis</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <div class="section-title">Executive Summary</div>
        <p>{{ $report['report_data']['executive_summary'] }}</p>

        <div class="summary-stats">
            <div class="stat-box">
                <div class="stat-number">{{ $data['requests_data']['total_requests'] }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $data['supply_data']['available_stock'] }}</div>
                <div class="stat-label">Available Stock</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $data['shortage_analysis']['critical_shortages'] }}</div>
                <div class="stat-label">Critical Shortages</div>
            </div>
            <div class="stat-box">
                <div clas s="stat-number">{{ $data['barangay_analysis']['total_barangays'] }}</div>
                <div class="stat-label">Active Barangays</div>
            </div>
        </div>

        @if (isset($report['report_ data']['performance_assessment']))
            @php
                $rating = $report['report_data']['performance_assessment']['overall_rating'] ?? '';
                $ratingClass = match (strtolower($rating)) {
                    'excellent', 'very good' => 'success',
                    'good' => 'primary',
                    'fair', 'average' => 'warning',
                    'poor', 'critical' => 'danger',
                    default => 'secondary',
                };
            @endphp
            <p><strong>Overall Rating:</strong>
                <span class="badge badge-{{ $ratingClass }}">
                    {{ $report['report_data']['performance_assessment']['overall_rating'] }}
                </span>
            </p>
            <p><strong>Confidence Level:</strong>
                {{ $report['report_data']['confidence_level'] ?? 'Medium' }}
                @if (isset($report['report_data']['confidence_score']))
                    ({{ $report['report_data']['confidence_score'] }}%)
                @endif
            </p>
        @endif
    </div>

    <!-- Key Findings and Critical Issues -->
    <div class="secti on">
        <div class="section-title">Key Findings & Critical Issues</div>
        <div class="findin gs-issues">
            <div class="findings">
                <h4>✓ Key Findings</h4>
                @if (isset($report['report_data']['key_findings']))
                    <ul>
                        @foreach ($report['report_data']['key_findings'] as $finding)
                            <li><span class="check-icon">✓</span> {{ $finding }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="issues">
                <h4>⚠ Critical Issues</h4>
                @if (isset($report['report_data']['critical_issues']) && count($report['report_data']['critical_issues']) > 0)
                    <ul>
                        @foreach ($report['report_data']['critical_issues'] as $issue)
                            <li><span class="alert-icon">⚠</span> {{ $issue }}</li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: #4caf50;">No critical issues identified.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    @if (isset($report['report_data']['recommendations']))
        <div class="section">
            <div class="section-title">AI -Generated Recommendations</div>
            <div class="recommendations">
                <div class="rec-column">
                    <h4 class="immediate">⚡ Immediate Actions</h4>
                    <ul>
                        @if (isset($report['report_data']['recommendations']['immediate_actions']))
                            @foreach ($report['report_data']['recommendations']['immediate_actions'] as $action)
                                <li><span class="badge badge-danger">NOW</span>{{ $action }}</li>
                            @endforeach
                        @else
                            <li>No immediate actions identified.</li>
                        @endif
                    </ul>
                </div>
                <div class="rec-column">
                    <h4 class="short-term">📅 Short-term Strategies</h4>
                    <ul>
                        @if (isset($report['report_data']['recommendations']['short_term_strategies']))
                            @foreach ($report['report_data']['recommendations']['short_term_strategies'] as $strategy)
                                <li><span class="badge badge-warning">1-3M</span>{{ $strategy }}</li>
                            @endforeach
                        @else
                            <li>No short-term strategies available.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Detailed Data Analysis -->
    <div class="section page-break">
        <div class="section-title">Detailed Data Analysis</div>

        <h4>Top Requesting Barangays</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Barangay</th>
                    <th>Requests</th>
                    <th>Total Quantity</th>
                    <th>Priority Level</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['barangay_analysis']['barangay_details']) && is_array($data['barangay_analysis']['barangay_details']))
                    @foreach (array_slice($data['barangay_analysis']['barangay_details'], 0, 10) as $barangay)
                        <tr>
                            <td>{{ $barangay['name'] ?? 'Unknown' }}</td>
                            <td>{{ $barangay['requests'] ?? 0 }}</td>
                            <td>{{ $barangay['total_quantity'] ?? 0 }}</td>
                            <td>
                                <span
                                    class="badge badge-{{ ($barangay['priority_level'] ?? 'LOW') == 'HIGH' ? 'danger' : (($barangay['priority_level'] ?? 'LOW') == 'MEDIUM' ? 'warning' : 'success') }}">
                                    {{ $barangay['priority_level'] ?? 'LOW' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">No barangay data available</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <h4>Critical Supply Shortages</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Demanded</th>
                    <th>Available</th>
                    <th>Shortage</th>
                    <th>Severity</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['shortage_analysis']['shortages']) && is_array($data['shortage_analysis']['shortages']))
                    @foreach (array_slice($data['shortage_analysis']['shortages'], 0, 10) as $shortage)
                        <tr>
                            <td>{{ $shortage['item'] ?? 'Unknown' }}</td>
                            <td>{{ $shortage['demanded'] ?? 0 }}</td>
                            <td>{{ $shortage['available'] ?? 0 }}</td>
                            <td style="color: #f44336;">{{ $shortage['shortage'] ?? 0 }}</td>
                            <td>
                                <span
                                    class="badge badge-{{ ($shortage['severity'] ?? 'MEDIUM') == 'CRITICAL' ? 'danger' : (($shortage['severity'] ?? 'MEDIUM') == 'HIGH' ? 'warning' : 'primary') }}">
                                    {{ $shortage['severity'] ?? 'MEDIUM' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No shortage data available</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Performance Metrics -->
    @if (isset($report['report_data']['performance_assessment']))
        <div class="section">
            <div class="section-title">Performance Assessment</div>
            <p><strong>Approval Efficiency:</strong>
                {{ $report['report_data']['performance_assessment']['approval_efficiency'] ?? 'Not assessed' }}</p>
            <p><strong>Supply Adequacy:</strong>
                {{ $report['report_data']['performance_assessment']['supply_adequacy'] ?? 'Not assessed' }}</p>
            <p><strong>Geographic Coverage:</strong>
                {{ $report['report_data']['performance_assessment']['geographic_coverage'] ?? 'Not assessed' }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Report Details:</strong></p>
        <p>Generated: {{ $report['generated_at'] ?? now() }}</p>
        <p>Analysis Source: {{ ucfirst($report['source'] ?? 'system') }}
            @if (isset($report['source']) && $report['source'] === 'llm')
                ({{ $report['model_used'] ?? 'AI Model' }})
            @endif
        </p>
        <p>Data Period: {{ $data['period']['start_date'] ?? 'Unknown' }} to
            {{ $data['period']['end_date'] ?? 'Unknown' }}</p>
        <p>AgriSys - Agricultural Management System | City Agriculture Office, San Pedro, Laguna</p>
    </div>
</body>

</html>

@php
    function getRatingClass($rating)
    {
        switch (strtolower($rating)) {
            case 'excellent':
                return 'success';
            case 'good':
                return 'primary';
            case 'fair':
                return 'warning';
            case 'poor':
                return 'danger';
            default:
                return 'primary';
        }
    }
@endphp
