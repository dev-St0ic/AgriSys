<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BOATR DSS Report - {{ $data['period']['month'] }}</title>
    <style>
        /* ===== BASE ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1a1a1a;
            background: #ffffff;
        }

        .page { padding: 20px 28px; }

        /* ===== HEADER ===== */
        .header {
            border-bottom: 2px solid #6366f1;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { border: none; padding: 0; background: none; vertical-align: middle; }
        .header-table td:first-child { width: 65%; }
        .header-table td:last-child  { width: 35%; text-align: right; }

        .org-name { font-size: 14px; font-weight: 800; color: #6366f1; letter-spacing: 0.2px; }
        .org-sub  { font-size: 9px; color: #818cf8; font-weight: 600; margin-top: 1px; }

        .header-meta { font-size: 8.5px; color: #555; line-height: 1.7; }
        .header-meta .period-label { font-size: 12px; font-weight: 800; color: #6366f1; display: block; }

        /* ===== SECTION ===== */
        .section { margin-bottom: 12px; page-break-inside: avoid; }

        .section-title {
            background: #6366f1;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 4px 9px;
            margin-bottom: 8px;
        }

        /* ===== EXEC BOX ===== */
        .exec-box {
            background: #EEF2FF;
            border-left: 3px solid #818cf8;
            padding: 7px 10px;
            font-size: 10px;
            color: #2d2d2d;
            line-height: 1.6;
            margin-bottom: 9px;
        }

        /* ===== STAT ROW ===== */
        .stat-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .stat-table td {
            border: 1px solid #E0E0E0;
            background: #FAFAFA;
            padding: 7px 4px;
            text-align: center;
            vertical-align: middle;
        }
        .stat-num { font-size: 20px; font-weight: 800; color: #6366f1; line-height: 1; display: block; }
        .stat-num.red   { color: #C62828; }
        .stat-num.amber { color: #E65100; }
        .stat-num.blue  { color: #1565C0; }
        .stat-lbl { font-size: 7.5px; color: #555; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; margin-top: 2px; display: block; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .badge-danger   { background: #C62828; color: #fff; }
        .badge-warning  { background: #E65100; color: #fff; }
        .badge-success  { background: #2E7D32; color: #fff; }
        .badge-primary  { background: #1565C0; color: #fff; }
        .badge-indigo   { background: #6366f1; color: #fff; }
        .badge-secondary { background: #757575; color: #fff; }

        /* ===== TWO-COL LAYOUT ===== */
        .two-col-table { width: 100%; border-collapse: collapse; page-break-inside: avoid; }
        .two-col-table > tbody > tr > td { vertical-align: top; border: none; background: none; padding: 0; width: 50%; }
        .two-col-table > tbody > tr > td:first-child { padding-right: 7px; }
        .two-col-table > tbody > tr > td:last-child  { padding-left: 7px; }

        /* ===== COL HEADERS ===== */
        .col-head { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #fff; padding: 4px 8px; margin-bottom: 5px; }
        .col-head.indigo { background: #6366f1; }
        .col-head.green  { background: #2E7D32; }
        .col-head.red    { background: #C62828; }

        /* ===== ITEM LIST ===== */
        .item-list { list-style: none; padding: 0; margin: 0; }
        .item-list li { font-size: 9.5px; padding: 3px 0; border-bottom: 1px solid #EEEEEE; color: #333; }
        .item-list li:last-child { border-bottom: none; }
        .icon-ok  { color: #6366f1; font-weight: 700; margin-right: 4px; }
        .icon-err { color: #C62828; font-weight: 700; margin-right: 4px; }

        /* ===== REC CARDS ===== */
        .rec-head { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; padding: 4px 8px; }
        .rec-head.now  { background: #FFEBEE; color: #B71C1C; border-left: 3px solid #C62828; }
        .rec-head.soon { background: #FFF3E0; color: #BF360C; border-left: 3px solid #E65100; }
        .rec-head.long { background: #EEF2FF; color: #3730a3; border-left: 3px solid #6366f1; }
        .rec-body { border: 1px solid #E0E0E0; border-top: none; padding: 4px 8px; background: #FAFAFA; list-style: none; margin: 0 0 8px 0; }
        .rec-body li { font-size: 9px; padding: 3px 0; border-bottom: 1px solid #EEEEEE; color: #333; }
        .rec-body li:last-child { border-bottom: none; }

        /* ===== PERFORMANCE GRID ===== */
        .perf-table { width: 100%; border-collapse: collapse; page-break-inside: avoid; margin-bottom: 8px; }
        .perf-table td { border: 1px solid #E0E0E0; background: #FAFAFA; padding: 7px; vertical-align: top; width: 50%; }
        .perf-lbl { font-size: 8px; font-weight: 700; text-transform: uppercase; color: #888; letter-spacing: 0.3px; margin-bottom: 2px; display: block; }
        .perf-val { font-size: 9px; color: #222; line-height: 1.4; }

        /* ===== DATA TABLES ===== */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 9px; font-size: 9px; }
        .data-table thead tr { background: #6366f1; color: #fff; }
        .data-table thead th { padding: 4px 6px; text-align: left; font-weight: 700; font-size: 8px; letter-spacing: 0.3px; text-transform: uppercase; border: none; }
        .data-table tbody tr:nth-child(even) { background: #F5F5F5; }
        .data-table tbody tr:nth-child(odd)  { background: #FFFFFF; }
        .data-table tbody td { padding: 4px 6px; color: #333; border-bottom: 1px solid #E8E8E8; vertical-align: middle; }
        .data-table tbody tr:last-child td { border-bottom: none; }

        /* ===== ALERT ===== */
        .alert-ok { background: #EEF2FF; border-left: 3px solid #818cf8; color: #3730a3; padding: 6px 9px; font-size: 9.5px; margin-bottom: 6px; }

        /* ===== FOOTER ===== */
        .footer { border-top: 1.5px solid #6366f1; padding-top: 7px; margin-top: 12px; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { font-size: 8px; color: #555; line-height: 1.7; border: none; background: none; vertical-align: top; padding: 0; }
        .footer-table td:last-child { text-align: right; }
        .f-indigo { color: #6366f1; font-weight: 700; }
    </style>
</head>

<body>
<div class="page">

    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="org-name">BOATR Registry Decision Support System</div>
                    <div class="org-sub">AI-Powered Agricultural Intelligence &nbsp;&bull;&nbsp; City Agriculture Office, San Pedro, Laguna</div>
                </td>
                <td>
                    <div class="header-meta">
                        <span class="period-label">{{ $data['period']['month'] }}</span>
                        Generated: {{ now()->format('F j, Y \a\t g:i A') }}<br>
                        Period: {{ $data['period']['start_date'] }} &ndash; {{ $data['period']['end_date'] }}<br>
                        Source:
                        @if($report['source'] === 'llm')
                            Claude AI ({{ $report['model_used'] ?? 'AI Model' }})
                        @else
                            Rule-Based Engine
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <div class="section-title">01 &nbsp;&bull;&nbsp; Executive Summary</div>

        <div class="exec-box">{{ $report['report_data']['executive_summary'] }}</div>

        @if(isset($report['report_data']['performance_assessment']))
            @php
                $rating = $report['report_data']['performance_assessment']['overall_rating'] ?? '';
                $ratingClass = match(strtolower($rating)) {
                    'excellent', 'very good'               => 'success',
                    'good'                                 => 'primary',
                    'fair', 'average', 'needs improvement' => 'warning',
                    'poor', 'critical'                     => 'danger',
                    default                                => 'secondary',
                };
            @endphp
            <p style="font-size:9.5px; font-weight:700; color:#444; margin-bottom:6px;">
                Overall Rating:&nbsp;
                <span class="badge badge-{{ $ratingClass }}" style="font-size:9.5px; padding:2px 9px;">{{ strtoupper($rating) }}</span>
                &nbsp;&nbsp; Confidence Level: <strong>{{ $report['report_data']['confidence_level'] ?? 'Medium' }} ({{ $report['report_data']['confidence_score'] ?? 65 }}%)</strong>
            </p>
            <table class="perf-table">
                <tr>
                    <td>
                        <span class="perf-lbl">Approval Efficiency</span>
                        <span class="perf-val">{{ $report['report_data']['performance_assessment']['approval_efficiency'] ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="perf-lbl">Inspection Effectiveness</span>
                        <span class="perf-val">{{ $report['report_data']['performance_assessment']['inspection_effectiveness'] ?? 'N/A' }}</span>
                    </td>
                </tr>
            </table>
        @endif
    </div>

    <!-- BOATR Statistics -->
    <div class="section">
        <div class="section-title">02 &nbsp;&bull;&nbsp; BOATR Statistics</div>

        <table class="stat-table">
            <tr>
                <td>
                    <span class="stat-num">{{ $data['boatr_stats']['total_applications'] }}</span>
                    <span class="stat-lbl">Total Applications</span>
                </td>
                <td>
                    <span class="stat-num">{{ $data['boatr_stats']['approved'] }}</span>
                    <span class="stat-lbl">Approved</span>
                </td>
                <td>
                    <span class="stat-num blue">{{ $data['boatr_stats']['inspections_completed'] }}</span>
                    <span class="stat-lbl">Inspections</span>
                </td>
                <td>
                    <span class="stat-num amber">{{ $data['boatr_stats']['pending'] }}</span>
                    <span class="stat-lbl">Pending</span>
                </td>
            </tr>
        </table>

        <p style="font-size:9.5px; margin-bottom:3px;"><strong>Approval Rate:</strong> {{ $data['boatr_stats']['approval_rate'] }}%</p>
        <p style="font-size:9.5px; margin-bottom:3px;"><strong>Inspection Rate:</strong> {{ $data['boatr_stats']['inspection_rate'] }}%</p>
        <p style="font-size:9.5px; margin-bottom:3px;"><strong>Average Boat Length:</strong> {{ $data['boatr_stats']['avg_boat_length'] }}</p>
        <p style="font-size:9.5px;"><strong>Average Engine Power:</strong> {{ $data['boatr_stats']['avg_horsepower'] }}</p>
    </div>

    <!-- Inspection Analysis -->
    <div class="section">
        <div class="section-title">03 &nbsp;&bull;&nbsp; Inspection Analysis</div>
        <table class="two-col-table">
            <tr>
                <td>
                    <div class="col-head indigo">Inspection Summary</div>
                    <ul class="item-list">
                        <li><span class="icon-ok">+</span>Completed: {{ $data['boatr_inspection_analysis']['inspections_completed'] }}</li>
                        <li><span class="icon-ok">+</span>Pending: {{ $data['boatr_inspection_analysis']['inspections_pending'] }}</li>
                        <li><span class="icon-ok">+</span>Completion Rate: {{ $data['boatr_inspection_analysis']['completion_rate'] }}%</li>
                    </ul>
                </td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Key Findings & Critical Issues -->
    <div class="section">
        <div class="section-title">04 &nbsp;&bull;&nbsp; Key Findings &amp; Critical Issues</div>
        <table class="two-col-table">
            <tr>
                <td>
                    <div class="col-head green">[ + ] Key Findings</div>
                    @if(isset($report['report_data']['key_findings']))
                        <ul class="item-list">
                            @foreach($report['report_data']['key_findings'] as $finding)
                                <li><span class="icon-ok">+</span>{{ $finding }}</li>
                            @endforeach
                        </ul>
                    @endif
                </td>
                <td>
                    <div class="col-head red">[ ! ] Critical Issues</div>
                    @if(isset($report['report_data']['critical_issues']) && count($report['report_data']['critical_issues']) > 0)
                        <ul class="item-list">
                            @foreach($report['report_data']['critical_issues'] as $issue)
                                <li><span class="icon-err">!</span>{{ $issue }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert-ok">No critical issues identified for this period.</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Vessel Insights -->
    @if(isset($report['report_data']['vessel_insights']))
    <div class="section">
        <div class="section-title">05 &nbsp;&bull;&nbsp; Vessel Insights</div>
        <ul class="item-list">
            @foreach($report['report_data']['vessel_insights'] as $insight)
                <li><span class="icon-ok">+</span>{{ $insight }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Recommendations -->
    @if(isset($report['report_data']['recommendations']))
    <div class="section">
        <div class="section-title">06 &nbsp;&bull;&nbsp; AI-Generated Recommendations</div>
        <table class="two-col-table">
            <tr>
                <td>
                    <div class="rec-head now">Immediate Actions</div>
                    <ul class="rec-body">
                        @foreach($report['report_data']['recommendations']['immediate_actions'] as $action)
                            <li><span class="badge badge-danger" style="margin-right:4px;">NOW</span>{{ $action }}</li>
                        @endforeach
                    </ul>
                    <div class="rec-head long">Long-Term Vision</div>
                    <ul class="rec-body">
                        @foreach($report['report_data']['recommendations']['long_term_vision'] as $vision)
                            <li><span class="badge badge-indigo" style="margin-right:4px;">LONG</span>{{ $vision }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <div class="rec-head soon">Short-Term Strategies (1&ndash;3 Months)</div>
                    <ul class="rec-body">
                        @foreach($report['report_data']['recommendations']['short_term_strategies'] as $strategy)
                            <li><span class="badge badge-warning" style="margin-right:4px;">1-3M</span>{{ $strategy }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Boat Type Distribution -->
    <div class="section">
        <div class="section-title">07 &nbsp;&bull;&nbsp; Boat Type Distribution</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Boat Type</th>
                    <th>Count</th>
                    <th>Avg Length (m)</th>
                    <th>Avg HP</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($data['boatr_by_boat_type']['distribution'], 0, 10) as $boat)
                    <tr>
                        <td>{{ $boat['boat_type'] }}</td>
                        <td>{{ $boat['total'] }}</td>
                        <td>{{ $boat['avg_length'] }}</td>
                        <td>{{ $boat['avg_horsepower'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Geographic Distribution -->
    <div class="section">
        <div class="section-title">08 &nbsp;&bull;&nbsp; Geographic Distribution</div>
        <p style="font-size:9.5px; margin-bottom:6px;"><strong>Barangays Covered:</strong> {{ $data['boatr_by_barangay']['total_barangays_covered'] }}</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Barangay</th>
                    <th>Applications</th>
                    <th>Approved</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($data['boatr_by_barangay']['distribution'], 0, 10) as $brgy)
                    <tr>
                        <td>{{ $brgy['barangay'] }}</td>
                        <td>{{ $brgy['applications'] }}</td>
                        <td>{{ $brgy['approved'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <span class="f-indigo">AgriSys</span> &mdash; Agricultural Management System<br>
                    City Agriculture Office &nbsp;&bull;&nbsp; San Pedro, Laguna<br>
                    Data Period: {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}
                </td>
                <td>
                    Generated: {{ now()->toIso8601String() }}<br>
                    Source: {{ ucfirst($report['source']) }}<br>
                    Confidence Level:
                    <strong>{{ $report['report_data']['confidence_score'] ?? 65 }}% ({{ $report['report_data']['confidence_level'] ?? 'Medium' }})</strong>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>