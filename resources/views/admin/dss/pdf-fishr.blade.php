<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>FISHR DSS Report - {{ $data['period']['month'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #0891b2;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #0891b2;
            margin: 5px 0;
            font-size: 20px;
        }

        .header h2 {
            color: #06b6d4;
            margin: 5px 0;
            font-size: 16px;
        }

        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #0891b2;
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
            color: #0891b2;
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
        <h1>🐟 FISHR REGISTRY DSS REPORT</h1>
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
            <p><strong>Coverage Adequacy:</strong>
                {{ $report['report_data']['performance_assessment']['coverage_adequacy'] ?? 'N/A' }}</p>
        @endif
    </div>

    <!-- Key Statistics -->
    <div class="section">
        <div class="section-title">FISHR Statistics</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $data['fishr_stats']['total_applications'] }}</div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: #4caf50;">{{ $data['fishr_stats']['approved'] }}</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: #2196f3;">{{ $data['fishr_stats']['with_fishr_number'] }}</div>
                <div class="stat-label">FISHR Numbers</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: #ff9800;">{{ $data['fishr_stats']['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <p><strong>Approval Rate:</strong> {{ $data['fishr_stats']['approval_rate'] }}%</p>
        <p><strong>Rejection Rate:</strong> {{ $data['fishr_stats']['rejection_rate'] }}%</p>
    </div>

    <!-- Demographics -->
    <div class="section">
        <div class="section-title">Fisher Demographics</div>
        <p><strong>Gender Distribution:</strong></p>
        <ul>
            <li>Male: {{ $data['fishr_demographics']['male_count'] }}
                ({{ $data['fishr_demographics']['male_percentage'] }}%)</li>
            <li>Female: {{ $data['fishr_demographics']['female_count'] }}
                ({{ $data['fishr_demographics']['female_percentage'] }}%)</li>
        </ul>
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

    <!-- Fisheries Insights -->
    @if (isset($report['report_data']['fisheries_insights']))
        <div class="section">
            <div class="section-title">Fisheries Insights</div>
            <ul>
                @foreach ($report['report_data']['fisheries_insights'] as $insight)
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

    <!-- Livelihood Distribution -->
    <div class="section">
        <div class="section-title">Livelihood Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>Livelihood Type</th>
                    <th style="text-align: center;">Applications</th>
                    <th style="text-align: center;">Approval Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($data['fishr_by_livelihood']['distribution'], 0, 10) as $livelihood)
                    <tr>
                        <td>{{ $livelihood['livelihood'] }}</td>
                        <td style="text-align: center;">{{ $livelihood['total'] }}</td>
                        <td style="text-align: center;">{{ $livelihood['approval_rate'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Geographic Distribution -->
    <div class="section">
        <div class="section-title">Geographic Distribution</div>
        <p><strong>Barangays Covered:</strong> {{ $data['fishr_by_barangay']['total_barangays_covered'] }}</p>
        <table>
            <thead>
                <tr>
                    <th>Barangay</th>
                    <th style="text-align: center;">Applications</th>
                    <th style="text-align: center;">Approved</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($data['fishr_by_barangay']['distribution'], 0, 10) as $brgy)
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
