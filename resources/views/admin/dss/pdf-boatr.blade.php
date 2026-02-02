<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>BOATR DSS Report - {{ $data['period']['month'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #6366f1;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #6366f1;
            margin: 5px 0;
            font-size: 20px;
        }

        .header h2 {
            color: #818cf8;
            margin: 5px 0;
            font-size: 16px;
        }

        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #6366f1;
            color: white;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stats-grid {
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-box {
            display: inline-block;
            width: 23%;
            text-align: center;
            padding: 10px;
            background-color: #f5f5f5;
            margin-right: 1%;
            border-radius: 5px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #6366f1;
        }

        .stat-label {
            font-size: 10px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            display: inline-block;
        }

        .badge-success {
            background-color: #4caf50;
        }

        .badge-danger {
            background-color: #f44336;
        }

        .badge-warning {
            background-color: #ff9800;
            color: #000;
        }

        .badge-primary {
            background-color: #2196f3;
        }

        ul {
            margin: 5px 0;
            padding-left: 20px;
        }

        li {
            margin-bottom: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
        }

        .two-column {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>🚤 BOATR REGISTRY DSS REPORT</h1>
        <h2>{{ $data['period']['month'] }}</h2>
        <p>City Agriculture Office, San Pedro, Laguna</p>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <div class="section-title">Executive Summary</div>
        <p>{{ $report['report_data']['executive_summary'] }}</p>

        @if (isset($report['report_data']['performance_assessment']))
            @php
                $rating = $report['report_data']['performance_assessment']['overall_rating'] ?? '';
                $ratingClass = match (strtolower($rating)) {
                    'excellent', 'very good' => 'success',
                    'good' => 'primary',
                    'fair', 'average', 'needs improvement' => 'warning',
                    'poor', 'critical' => 'danger',
                    default => 'secondary',
                };
            @endphp
            <p><strong>Overall Rating:</strong>
                <span class="badge badge-{{ $ratingClass }}">
                    {{ $report['report_data']['performance_assessment']['overall_rating'] }}
                </span>
            </p>
            <p><strong>Confidence Level:</strong> {{ $report['report_data']['confidence_level'] ?? 'Medium' }}
                ({{ $report['report_data']['confidence_score'] ?? 65 }}%)</p>
            <p><strong>Approval Efficiency:</strong>
                {{ $report['report_data']['performance_assessment']['approval_efficiency'] ?? 'N/A' }}</p>
            <p><strong>Inspection Effectiveness:</strong>
                {{ $report['report_data']['performance_assessment']['inspection_effectiveness'] ?? 'N/A' }}</p>
        @endif
    </div>

    <!-- Key Statistics -->
    <div class="section">
        <div class="section-title">BOATR Statistics</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $data['boatr_stats']['total_applications'] }}</div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: #4caf50;">{{ $data['boatr_stats']['approved'] }}</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: #2196f3;">{{ $data['boatr_stats']['inspections_completed'] }}
                </div>
                <div class="stat-label">Inspections</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: #ff9800;">{{ $data['boatr_stats']['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <p><strong>Approval Rate:</strong> {{ $data['boatr_stats']['approval_rate'] }}%</p>
        <p><strong>Inspection Rate:</strong> {{ $data['boatr_stats']['inspection_rate'] }}%</p>
        <p><strong>Average Boat Length:</strong> {{ $data['boatr_stats']['avg_boat_length'] }}</p>
        <p><strong>Average Engine Power:</strong> {{ $data['boatr_stats']['avg_horsepower'] }}</p>
    </div>

    <!-- Inspection Analysis -->
    <div class="section">
        <div class="section-title">Inspection Analysis</div>
        <p><strong>Inspections Completed:</strong> {{ $data['boatr_inspection_analysis']['inspections_completed'] }}
        </p>
        <p><strong>Inspections Pending:</strong> {{ $data['boatr_inspection_analysis']['inspections_pending'] }}</p>
        <p><strong>Completion Rate:</strong> {{ $data['boatr_inspection_analysis']['completion_rate'] }}%</p>
    </div>

    <!-- Key Findings -->
    <div class="section">
        <div class="section-title">Key Findings</div>
        @if (isset($report['report_data']['key_findings']))
            <ul>
                @foreach ($report['report_data']['key_findings'] as $finding)
                    <li>{{ $finding }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Vessel Insights -->
    @if (isset($report['report_data']['vessel_insights']))
        <div class="section">
            <div class="section-title">Vessel Insights</div>
            <ul>
                @foreach ($report['report_data']['vessel_insights'] as $insight)
                    <li>{{ $insight }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Critical Issues -->
    @if (isset($report['report_data']['critical_issues']) && count($report['report_data']['critical_issues']) > 0)
        <div class="section">
            <div class="section-title">Critical Issues</div>
            <ul>
                @foreach ($report['report_data']['critical_issues'] as $issue)
                    <li>⚠ {{ $issue }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Recommendations -->
    @if (isset($report['report_data']['recommendations']))
        <div class="section">
            <div class="section-title">AI-Generated Recommendations</div>
            <h4>⚡ Immediate Actions</h4>
            <ul>
                @foreach ($report['report_data']['recommendations']['immediate_actions'] as $action)
                    <li>{{ $action }}</li>
                @endforeach
            </ul>
            <h4>📅 Short-term Strategies</h4>
            <ul>
                @foreach ($report['report_data']['recommendations']['short_term_strategies'] as $strategy)
                    <li>{{ $strategy }}</li>
                @endforeach
            </ul>
            <h4>🚀 Long-term Vision</h4>
            <ul>
                @foreach ($report['report_data']['recommendations']['long_term_vision'] as $vision)
                    <li>{{ $vision }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Boat Type Distribution -->
    <div class="section">
        <div class="section-title">Boat Type Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>Boat Type</th>
                    <th style="text-align: center;">Count</th>
                    <th style="text-align: center;">Avg Length (m)</th>
                    <th style="text-align: center;">Avg HP</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($data['boatr_by_boat_type']['distribution'], 0, 10) as $boat)
                    <tr>
                        <td>{{ $boat['boat_type'] }}</td>
                        <td style="text-align: center;">{{ $boat['total'] }}</td>
                        <td style="text-align: center;">{{ $boat['avg_length'] }}</td>
                        <td style="text-align: center;">{{ $boat['avg_horsepower'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Geographic Distribution -->
    <div class="section">
        <div class="section-title">Geographic Distribution</div>
        <p><strong>Barangays Covered:</strong> {{ $data['boatr_by_barangay']['total_barangays_covered'] }}</p>
        <table>
            <thead>
                <tr>
                    <th>Barangay</th>
                    <th style="text-align: center;">Applications</th>
                    <th style="text-align: center;">Approved</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($data['boatr_by_barangay']['distribution'], 0, 10) as $brgy)
                    <tr>
                        <td>{{ $brgy['barangay'] }}</td>
                        <td style="text-align: center;">{{ $brgy['applications'] }}</td>
                        <td style="text-align: center;">{{ $brgy['approved'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Report Details:</strong></p>
        <p>Generated: {{ now()->toIso8601String() }} | Source: {{ ucfirst($report['source']) }}</p>
        <p>Data Period: {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}</p>
        <p><strong>Confidence Level:</strong>
            {{ $report['report_data']['confidence_score'] ?? 65 }}%
            ({{ $report['report_data']['confidence_level'] ?? 'Medium' }})
        </p>
        <p>AgriSys - Agricultural Management System | City Agriculture Office, San Pedro, Laguna</p>
    </div>
</body>

</html>
