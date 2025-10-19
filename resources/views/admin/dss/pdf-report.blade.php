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
            margin: 20px 0;
            text-align: center;
        }

        .stat-box {
            flex: 1;
            padding: 15px;
            margin: 0 5px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #2E7D32;
        }

        .stat-label {
            font-size: 11px;
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
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 3px;
        }

        .immediate {
            background-color: #ffebee;
            color: #c62828;
        }

        .short-term {
            background-color: #fff3e0;
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
            padding: 5px 0;
            font-size: 11px;
            border-bottom: 1px solid #eee;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            margin-right: 5px;
        }

        .badge-danger {
            background-color: #f44336;
            color: white;
        }

        .badge-warning {
            background-color: #ff9800;
            color: white;
        }

        .badge-success {
            background-color: #4caf50;
            color: white;
        }

        .badge-primary {
            background-color: #2196f3;
            color: white;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .findings-issues {
            display: flex;
            justify-content: space-between;
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
        .issues li {
            padding: 5px 0;
            font-size: 11px;
            border-bottom: 1px solid #eee;
        }

        .check-icon {
            color: #4caf50;
        }

        .alert-icon {
            color: #f44336;
        }

        .footer {
            margin-top: 30px;
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
                <div class="stat-number">{{ $data['barangay_analysis']['total_barangays'] }}</div>
                <div class="stat-label">Active Barangays</div>
            </div>
        </div>

        @if (isset($report['report_data']['performance_assessment']))
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
            <p><strong>Confidence Level:</strong> {{ $report['report_data']['confidence_level'] ?? 'Medium' }}</p>
        @endif
    </div>

    <!-- Key Findings and Critical Issues -->
    <div class="section">
        <div class="section-title">Key Findings & Critical Issues</div>
        <div class="findings-issues">
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
            <div class="section-title">AI-Generated Recommendations</div>
            <div class="recommendations">
                <div class="rec-column">
                    <h4 class="immediate">⚡ Immediate Actions</h4>
                    <ul>
                        @foreach ($report['report_data']['recommendations']['immediate_actions'] as $action)
                            <li><span class="badge badge-danger">NOW</span>{{ $action }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="rec-column">
                    <h4 class="short-term">📅 Short-term Strategies</h4>
                    <ul>
                        @foreach ($report['report_data']['recommendations']['short_term_strategies'] as $strategy)
                            <li><span class="badge badge-warning">1-3M</span>{{ $strategy }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="rec-column">
                    <h4 class="long-term">🎯 Long-term Improvements</h4>
                    <ul>
                        @foreach ($report['report_data']['recommendations']['long_term_improvements'] as $improvement)
                            <li><span class="badge badge-success">3-6M</span>{{ $improvement }}</li>
                        @endforeach
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
                @foreach (array_slice($data['barangay_analysis']['barangay_details'], 0, 10) as $barangay)
                    <tr>
                        <td>{{ $barangay['name'] }}</td>
                        <td>{{ $barangay['requests'] }}</td>
                        <td>{{ $barangay['total_quantity'] }}</td>
                        <td>
                            <span
                                class="badge badge-{{ $barangay['priority_level'] == 'HIGH' ? 'danger' : ($barangay['priority_level'] == 'MEDIUM' ? 'warning' : 'success') }}">
                                {{ $barangay['priority_level'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
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
                @foreach (array_slice($data['shortage_analysis']['shortages'], 0, 10) as $shortage)
                    <tr>
                        <td>{{ $shortage['item'] }}</td>
                        <td>{{ $shortage['demanded'] }}</td>
                        <td>{{ $shortage['available'] }}</td>
                        <td style="color: #f44336;">{{ $shortage['shortage'] }}</td>
                        <td>
                            <span
                                class="badge badge-{{ $shortage['severity'] == 'CRITICAL' ? 'danger' : ($shortage['severity'] == 'HIGH' ? 'warning' : 'primary') }}">
                                {{ $shortage['severity'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Performance Metrics -->
    @if (isset($report['report_data']['performance_assessment']))
        <div class="section">
            <div class="section-title">Performance Assessment</div>
            <p><strong>Approval Efficiency:</strong>
                {{ $report['report_data']['performance_assessment']['approval_efficiency'] }}</p>
            <p><strong>Supply Adequacy:</strong>
                {{ $report['report_data']['performance_assessment']['supply_adequacy'] }}</p>
            <p><strong>Geographic Coverage:</strong>
                {{ $report['report_data']['performance_assessment']['geographic_coverage'] }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Report Details:</strong></p>
        <p>Generated: {{ $report['generated_at'] }}</p>
        <p>Analysis Source: {{ ucfirst($report['source']) }}
            @if ($report['source'] === 'llm')
                ({{ $report['model_used'] }})
            @endif
        </p>
        <p>Data Period: {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}</p>
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
