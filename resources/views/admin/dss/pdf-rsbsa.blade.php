<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>RSBSA DSS Report - {{ $data['period']['month'] }}</title>
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
            border-bottom: 2px solid #1B5E20;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { border: none; padding: 0; background: none; vertical-align: middle; }
        .header-table td:first-child { width: 65%; }
        .header-table td:last-child  { width: 35%; text-align: right; }

        .org-name { font-size: 14px; font-weight: 800; color: #1B5E20; letter-spacing: 0.2px; }
        .org-sub  { font-size: 9px; color: #4CAF50; font-weight: 600; margin-top: 1px; }

        .header-meta { font-size: 8.5px; color: #555; line-height: 1.7; }
        .header-meta .period-label { font-size: 12px; font-weight: 800; color: #1B5E20; display: block; }

        /* ===== SECTION ===== */
        .section { margin-bottom: 12px; page-break-inside: avoid; }

        .section-title {
            background: #1B5E20;
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
            background: #F9FBE7;
            border-left: 3px solid #8BC34A;
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
        .stat-num { font-size: 20px; font-weight: 800; color: #1B5E20; line-height: 1; display: block; }
        .stat-num.red   { color: #C62828; }
        .stat-num.amber { color: #E65100; }
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
        .badge-secondary { background: #757575; color: #fff; }

        /* ===== TWO-COL LAYOUT ===== */
        .two-col-table { width: 100%; border-collapse: collapse; page-break-inside: avoid; }
        .two-col-table > tbody > tr > td { vertical-align: top; border: none; background: none; padding: 0; width: 50%; }
        .two-col-table > tbody > tr > td:first-child { padding-right: 7px; }
        .two-col-table > tbody > tr > td:last-child  { padding-left: 7px; }

        /* ===== COL HEADERS ===== */
        .col-head { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #fff; padding: 4px 8px; margin-bottom: 5px; }
        .col-head.green { background: #2E7D32; }
        .col-head.red   { background: #C62828; }

        /* ===== ITEM LIST ===== */
        .item-list { list-style: none; padding: 0; margin: 0; }
        .item-list li { font-size: 9.5px; padding: 3px 0; border-bottom: 1px solid #EEEEEE; color: #333; }
        .item-list li:last-child { border-bottom: none; }
        .icon-ok  { color: #2E7D32; font-weight: 700; margin-right: 4px; }
        .icon-err { color: #C62828; font-weight: 700; margin-right: 4px; }

        /* ===== REC CARDS ===== */
        .rec-head { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; padding: 4px 8px; }
        .rec-head.now  { background: #FFEBEE; color: #B71C1C; border-left: 3px solid #C62828; }
        .rec-head.soon { background: #FFF3E0; color: #BF360C; border-left: 3px solid #E65100; }
        .rec-head.long { background: #E8F5E9; color: #1B5E20; border-left: 3px solid #2E7D32; }
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
        .data-table thead tr { background: #2E7D32; color: #fff; }
        .data-table thead th { padding: 4px 6px; text-align: left; font-weight: 700; font-size: 8px; letter-spacing: 0.3px; text-transform: uppercase; border: none; }
        .data-table tbody tr:nth-child(even) { background: #F5F5F5; }
        .data-table tbody tr:nth-child(odd)  { background: #FFFFFF; }
        .data-table tbody td { padding: 4px 6px; color: #333; border-bottom: 1px solid #E8E8E8; vertical-align: middle; }
        .data-table tbody tr:last-child td { border-bottom: none; }

        /* ===== ALERT ===== */
        .alert-ok { background: #E8F5E9; border-left: 3px solid #43A047; color: #1B5E20; padding: 6px 9px; font-size: 9.5px; margin-bottom: 6px; }

        /* ===== FOOTER ===== */
        .footer { border-top: 1.5px solid #1B5E20; padding-top: 7px; margin-top: 12px; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { font-size: 8px; color: #555; line-height: 1.7; border: none; background: none; vertical-align: top; padding: 0; }
        .footer-table td:last-child { text-align: right; }
        .f-green { color: #1B5E20; font-weight: 700; }
    </style>
</head>

<body>
<div class="page">

    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="org-name">RSBSA Registry Decision Support System</div>
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
                    'excellent', 'very good' => 'success',
                    'good'                   => 'primary',
                    'fair', 'average'        => 'warning',
                    'poor', 'critical'       => 'danger',
                    default                  => 'secondary',
                };
            @endphp
            <p style="font-size:9.5px; font-weight:700; color:#444; margin-bottom:6px;">
                Overall Rating:&nbsp;
                <span class="badge badge-{{ $ratingClass }}" style="font-size:9.5px; padding:2px 9px;">{{ strtoupper($rating) }}</span>
                &nbsp;&nbsp; Confidence Level: <strong>{{ $report['report_data']['confidence_level'] ?? 'High' }}</strong>
            </p>
            <table class="perf-table">
                <tr>
                    <td>
                        <span class="perf-lbl">Registration Efficiency</span>
                        <span class="perf-val">{{ $report['report_data']['performance_assessment']['registration_efficiency'] ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="perf-lbl">Agricultural Diversity</span>
                        <span class="perf-val">{{ $report['report_data']['performance_assessment']['agricultural_diversity'] ?? 'N/A' }}</span>
                    </td>
                </tr>
            </table>
        @endif
    </div>

    <!-- RSBSA Statistics -->
    <div class="section">
        <div class="section-title">02 &nbsp;&bull;&nbsp; RSBSA Statistics</div>

        <table class="stat-table">
            <tr>
                <td>
                    <span class="stat-num">{{ $data['rsbsa_stats']['total_applications'] }}</span>
                    <span class="stat-lbl">Total Farmers</span>
                </td>
                <td>
                    <span class="stat-num">{{ $data['rsbsa_stats']['approved'] }}</span>
                    <span class="stat-lbl">Approved</span>
                </td>
                <td>
                    <span class="stat-num red">{{ $data['rsbsa_stats']['rejected'] }}</span>
                    <span class="stat-lbl">Rejected</span>
                </td>
                <td>
                    <span class="stat-num amber">{{ $data['rsbsa_stats']['pending'] }}</span>
                    <span class="stat-lbl">Pending</span>
                </td>
            </tr>
        </table>

        <p style="font-size:9.5px; margin-bottom:3px;"><strong>Approval Rate:</strong> {{ $data['rsbsa_stats']['approval_rate'] }}%</p>
        <p style="font-size:9.5px; margin-bottom:3px;"><strong>Total Land Area:</strong> {{ $data['rsbsa_stats']['total_land_area'] }}</p>
        <p style="font-size:9.5px;"><strong>Average Farm Size:</strong> {{ $data['rsbsa_stats']['avg_land_area'] }}</p>
    </div>

    <!-- Demographics -->
    <div class="section">
        <div class="section-title">03 &nbsp;&bull;&nbsp; Farmer Demographics</div>
        <table class="two-col-table">
            <tr>
                <td>
                    <div class="col-head green">Gender Distribution</div>
                    <ul class="item-list">
                        <li><span class="icon-ok">+</span>Male: {{ $data['rsbsa_demographics']['male_count'] }} ({{ $data['rsbsa_demographics']['male_percentage'] }}%)</li>
                        <li><span class="icon-ok">+</span>Female: {{ $data['rsbsa_demographics']['female_count'] }} ({{ $data['rsbsa_demographics']['female_percentage'] }}%)</li>
                    </ul>
                </td>
                <td>
                    <div class="col-head green">Farm Size Distribution</div>
                    <ul class="item-list">
                        <li><span class="icon-ok">+</span>Small Farms: {{ $data['rsbsa_land_analysis']['small_farms'] }}</li>
                        <li><span class="icon-ok">+</span>Medium Farms: {{ $data['rsbsa_land_analysis']['medium_farms'] }}</li>
                        <li><span class="icon-ok">+</span>Large Farms: {{ $data['rsbsa_land_analysis']['large_farms'] }}</li>
                    </ul>
                </td>
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

    <!-- Recommendations -->
    @if(isset($report['report_data']['recommendations']))
    <div class="section">
        <div class="section-title">05 &nbsp;&bull;&nbsp; AI-Generated Recommendations</div>
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
                            <li><span class="badge badge-success" style="margin-right:4px;">LONG</span>{{ $vision }}</li>
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

    <!-- Top Commodities -->
    <div class="section">
        <div class="section-title">06 &nbsp;&bull;&nbsp; Top Commodities</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Commodity</th>
                    <th>Farmers</th>
                    <th>Land Area (ha)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['rsbsa_by_commodity']['top_commodities'] as $commodity)
                    <tr>
                        <td>{{ $commodity['commodity'] }}</td>
                        <td>{{ $commodity['total_farmers'] }}</td>
                        <td>{{ $commodity['total_land_area'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Geographic Distribution -->
    <div class="section">
        <div class="section-title">07 &nbsp;&bull;&nbsp; Geographic Distribution</div>
        <p style="font-size:9.5px; margin-bottom:6px;"><strong>Barangays Covered:</strong> {{ $data['rsbsa_by_barangay']['total_barangays_covered'] }}</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Barangay</th>
                    <th>Farmers</th>
                    <th>Land Area (ha)</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($data['rsbsa_by_barangay']['distribution'], 0, 10) as $brgy)
                    <tr>
                        <td>{{ $brgy['barangay'] }}</td>
                        <td>{{ $brgy['farmers'] }}</td>
                        <td>{{ $brgy['total_land_area'] }}</td>
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
                    <span class="f-green">AgriSys</span> &mdash; Agricultural Management System<br>
                    City Agriculture Office &nbsp;&bull;&nbsp; San Pedro, Laguna<br>
                    Data Period: {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}
                </td>
                <td>
                    Generated: {{ $report['generated_at'] }}<br>
                    Source: {{ ucfirst($report['source']) }}@if($report['source'] === 'llm') ({{ $report['model_used'] ?? 'AI Model' }})@endif<br>
                    Confidence Level:
                    <strong>
                        @if(isset($report['report_data']['confidence_score'])){{ $report['report_data']['confidence_score'] }}% @endif
                        ({{ $report['report_data']['confidence_level'] ?? 'High' }})
                    </strong>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>