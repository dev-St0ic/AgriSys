<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSS Report - {{ $data['period']['month'] }}</title>
    <style>
        /* ===== BASE ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1a1a1a;
            background: #ffffff;
        }

        .page { padding: 24px 32px; }

        /* ===== HEADER ===== */
        .header {
            border-bottom: 3px solid #1B5E20;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { border: none; padding: 0; background: none; vertical-align: middle; }
        .header-table td:first-child { width: 65%; }
        .header-table td:last-child  { width: 35%; text-align: right; }

        .org-name { font-size: 15px; font-weight: 800; color: #1B5E20; letter-spacing: 0.3px; }
        .org-sub  { font-size: 9.5px; color: #4CAF50; font-weight: 600; margin-top: 2px; }

        .header-meta { font-size: 9px; color: #555; line-height: 1.8; }
        .header-meta .period-label { font-size: 13px; font-weight: 800; color: #1B5E20; display: block; }

        /* ===== SECTION ===== */
        .section { margin-bottom: 16px; }

        .section-title {
            background: #1B5E20;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 5px 10px;
            margin-bottom: 10px;
        }

        /* ===== EXEC BOX ===== */
        .exec-box {
            background: #F9FBE7;
            border-left: 4px solid #8BC34A;
            padding: 9px 12px;
            font-size: 10.5px;
            color: #2d2d2d;
            line-height: 1.65;
            margin-bottom: 12px;
        }

        /* ===== STAT ROW ===== */
        .stat-table { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin-bottom: 10px; }
        .stat-table td {
            border: 1px solid #E0E0E0;
            background: #FAFAFA;
            padding: 9px 4px;
            text-align: center;
            vertical-align: middle;
        }
        .stat-num { font-size: 22px; font-weight: 800; color: #1B5E20; line-height: 1; display: block; }
        .stat-num.red   { color: #C62828; }
        .stat-num.amber { color: #E65100; }
        .stat-num.blue  { color: #1565C0; }
        .stat-lbl { font-size: 8px; color: #555; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; margin-top: 3px; display: block; }

        /* ===== HEALTH BAR — FIXED ===== */
        .health-wrap {
            background: #F1F8E9;
            border-left: 4px solid #388E3C;
            padding: 9px 12px;       /* more breathing room top/bottom */
            margin-bottom: 10px;
        }

        .health-table { width: 100%; border-collapse: collapse; }
        .health-table td { border: none; background: none; vertical-align: middle; }

        /* each cell has its own padding so nothing is squished */
        .health-label {
            font-size: 10px;
            font-weight: 700;
            color: #1B5E20;
            white-space: nowrap;
            width: 1%;
            padding-right: 12px;
        }

        /* bar stretches to fill remaining space */
        .health-bar-cell { width: 100%; padding: 0; }

        .health-bar-bg {
            background: #C8E6C9;
            height: 10px;
            overflow: hidden;
        }
        .health-bar-fill { height: 10px; background: #1B5E20; }

        .health-score {
            font-size: 11px;
            font-weight: 800;
            color: #1B5E20;
            white-space: nowrap;
            width: 1%;
            padding: 0 12px;        /* space either side of score */
        }

        .health-badge-cell {
            width: 1%;
            white-space: nowrap;
            padding-right: 0;
        }

        /* confidence sits after a visible separator */
        .health-conf-cell {
            width: 1%;
            white-space: nowrap;
            font-size: 9px;
            color: #444;
            border-left: 1px solid #A5D6A7;
            padding-left: 12px;
            padding-right: 0;
        }
        .health-conf-cell strong { color: #1B5E20; }

        /* ===== MINI STATS ===== */
        .mini-table { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin-bottom: 12px; }
        .mini-table td { text-align: center; padding: 7px 4px; vertical-align: middle; border-radius: 3px; }
        .ms-red   { background: #FFEBEE; border: 1px solid #FFCDD2; }
        .ms-amber { background: #FFF3E0; border: 1px solid #FFE0B2; }
        .ms-green { background: #E8F5E9; border: 1px solid #C8E6C9; }
        .ms-num { font-size: 18px; font-weight: 800; line-height: 1; display: block; }
        .ms-red   .ms-num { color: #C62828; }
        .ms-amber .ms-num { color: #E65100; }
        .ms-green .ms-num { color: #2E7D32; }
        .ms-lbl { font-size: 7.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.3px; margin-top: 2px; color: #555; display: block; }

        /* ===== TWO-COL LAYOUT ===== */
        .two-col-table { width: 100%; border-collapse: collapse; }
        .two-col-table > tbody > tr > td { vertical-align: top; border: none; background: none; padding: 0; width: 50%; }
        .two-col-table > tbody > tr > td:first-child { padding-right: 8px; }
        .two-col-table > tbody > tr > td:last-child  { padding-left: 8px; }

        /* ===== COL HEADERS ===== */
        .col-head { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; color: #fff; padding: 5px 8px; margin-bottom: 6px; }
        .col-head.green { background: #2E7D32; }
        .col-head.red   { background: #C62828; }

        /* ===== ITEM LIST ===== */
        .item-list { list-style: none; padding: 0; margin: 0; }
        .item-list li { font-size: 10px; padding: 4px 0; border-bottom: 1px solid #EEEEEE; color: #333; }
        .item-list li:last-child { border-bottom: none; }
        .icon-ok  { color: #2E7D32; font-weight: 700; margin-right: 5px; }
        .icon-err { color: #C62828; font-weight: 700; margin-right: 5px; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .badge-danger  { background: #C62828; color: #fff; }
        .badge-warning { background: #E65100; color: #fff; }
        .badge-success { background: #2E7D32; color: #fff; }
        .badge-primary { background: #1565C0; color: #fff; }

        /* ===== REC CARDS ===== */
        .rec-head { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; padding: 5px 8px; }
        .rec-head.now  { background: #FFEBEE; color: #B71C1C; border-left: 3px solid #C62828; }
        .rec-head.soon { background: #FFF3E0; color: #BF360C; border-left: 3px solid #E65100; }
        .rec-body { border: 1px solid #E0E0E0; border-top: none; padding: 5px 8px; background: #FAFAFA; list-style: none; margin: 0; }
        .rec-body li { font-size: 9.5px; padding: 3px 0; border-bottom: 1px solid #EEEEEE; color: #333; }
        .rec-body li:last-child { border-bottom: none; }

        /* ===== PERFORMANCE GRID ===== */
        .perf-table { width: 100%; border-collapse: separate; border-spacing: 5px 0; }
        .perf-table td { border: 1px solid #E0E0E0; background: #FAFAFA; padding: 8px; vertical-align: top; width: 33.33%; }
        .perf-lbl { font-size: 8.5px; font-weight: 700; text-transform: uppercase; color: #888; letter-spacing: 0.3px; margin-bottom: 3px; display: block; }
        .perf-val { font-size: 9.5px; color: #222; line-height: 1.4; }

        /* ===== DATA TABLES ===== */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9.5px; }
        .data-table thead tr { background: #2E7D32; color: #fff; }
        .data-table thead th { padding: 5px 7px; text-align: left; font-weight: 700; font-size: 8.5px; letter-spacing: 0.3px; text-transform: uppercase; border: none; }
        .data-table tbody tr:nth-child(even) { background: #F5F5F5; }
        .data-table tbody tr:nth-child(odd)  { background: #FFFFFF; }
        .data-table tbody td { padding: 5px 7px; color: #333; border-bottom: 1px solid #E8E8E8; vertical-align: middle; }
        .data-table tbody tr:last-child td { border-bottom: none; }

        .text-red   { color: #C62828; font-weight: 700; }
        .text-amber { color: #E65100; font-weight: 700; }
        .text-green { color: #2E7D32; font-weight: 700; }
        .text-bold  { font-weight: 700; }

        /* ===== SUB HEADING ===== */
        .sub-head {
            font-size: 9.5px; font-weight: 700; color: #1B5E20;
            border-bottom: 1px solid #C8E6C9;
            padding-bottom: 3px; margin: 10px 0 7px 0;
            text-transform: uppercase; letter-spacing: 0.3px;
        }

        /* ===== ALERT ===== */
        .alert-ok { background: #E8F5E9; border-left: 3px solid #43A047; color: #1B5E20; padding: 7px 10px; font-size: 10px; margin-bottom: 8px; }

        /* ===== INFO TABLE ===== */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { font-size: 10px; padding: 3px 4px; vertical-align: top; border: none; background: none; }
        .info-table td.ik { font-weight: 700; color: #444; white-space: nowrap; width: 45%; }
        .info-table td.iv { color: #333; }

        /* ===== PAGE BREAK — FIXED: no height, use padding on content after break ===== */
        .page-break { page-break-before: always; }
        .page-break-spacer { height: 0; margin: 0; padding: 0; }

        /* prevent bad mid-section breaks */
        .section         { page-break-inside: avoid; }
        .health-wrap     { page-break-inside: avoid; }
        .stat-table      { page-break-inside: avoid; }
        .mini-table      { page-break-inside: avoid; }
        .perf-table      { page-break-inside: avoid; }
        .two-col-table   { page-break-inside: avoid; }

        /* ===== FOOTER ===== */
        .footer { border-top: 2px solid #1B5E20; padding-top: 9px; margin-top: 16px; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { font-size: 8.5px; color: #555; line-height: 1.7; border: none; background: none; vertical-align: top; padding: 0; }
        .footer-table td:last-child { text-align: right; }
        .f-green { color: #1B5E20; font-weight: 700; }
    </style>
</head>

<body>
<div class="page">

{{-- ===== HEADER ===== --}}
<div class="header">
    <table class="header-table">
        <tr>
            <td>
                <div class="org-name">Supplies Request Decision Support System</div>
                <div class="org-sub">AI-Powered Agricultural Intelligence &nbsp;&bull;&nbsp; City Agriculture Office, San Pedro, Laguna</div>
            </td>
            <td>
                <div class="header-meta">
                    <span class="period-label">{{ $data['period']['month'] }}</span>
                    Generated: {{ now()->format('F j, Y \a\t g:i A') }}<br>
                    Period: {{ $data['period']['start_date'] }} &ndash; {{ $data['period']['end_date'] }}<br>
                    Source:
                    @if($report['source'] === 'llm')
                        Claude AI ({{ $report['model_used'] }})
                    @else
                        Rule-Based Engine
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- PAGE 1 --}}

{{-- SECTION 01: EXECUTIVE SUMMARY --}}
<div class="section">
    <div class="section-title">01 &nbsp;&bull;&nbsp; Executive Summary</div>

    <div class="exec-box">{{ $report['report_data']['executive_summary'] }}</div>

    <table class="stat-table">
        <tr>
            <td>
                <span class="stat-num">{{ $data['requests_data']['total_requests'] }}</span>
                <span class="stat-lbl">Total Requests</span>
            </td>
            <td>
                <span class="stat-num">{{ $data['requests_data']['approved_requests'] }}</span>
                <span class="stat-lbl">Approved</span>
            </td>
            <td>
                <span class="stat-num red">{{ $data['requests_data']['rejected_requests'] }}</span>
                <span class="stat-lbl">Rejected</span>
            </td>
            <td>
                <span class="stat-num amber">{{ $data['requests_data']['pending_requests'] }}</span>
                <span class="stat-lbl">Pending</span>
            </td>
            <td>
                <span class="stat-num blue">{{ $data['barangay_analysis']['total_barangays'] }}</span>
                <span class="stat-lbl">Barangays</span>
            </td>
            <td>
                <span class="stat-num red">{{ $data['shortage_analysis']['critical_shortages'] }}</span>
                <span class="stat-lbl">Critical Shortages</span>
            </td>
        </tr>
    </table>

    {{-- HEALTH BAR — fixed spacing and confidence display --}}
    <div class="health-wrap">
        <table class="health-table">
            <tr>
                <td class="health-label">Supply Health</td>
                <td class="health-bar-cell">
                    <div class="health-bar-bg">
                        <div class="health-bar-fill" style="width:{{ $data['supply_data']['supply_health_score'] }}%;"></div>
                    </div>
                </td>
                <td class="health-score">{{ $data['supply_data']['supply_health_score'] }}/100</td>
                <td class="health-badge-cell">
                    <span class="badge badge-{{ $data['supply_data']['supply_summary']['overall_status'] === 'Good' ? 'success' : ($data['supply_data']['supply_summary']['overall_status'] === 'Critical' ? 'danger' : 'warning') }}">
                        {{ $data['supply_data']['supply_summary']['overall_status'] }}
                    </span>
                </td>
                @if(isset($report['report_data']['confidence_level']))
                <td class="health-conf-cell">
                    @php
                        $cl = $report['report_data']['confidence_level'] ?? null;
                        $cs = $report['report_data']['confidence_score'] ?? null;
                        // If confidence_level is already a number, use it directly — no duplicate
                        $confNum = is_numeric($cl) ? $cl : (is_numeric($cs) ? $cs : null);
                    @endphp
                    Confidence:&nbsp;<strong>{{ $confNum !== null ? $confNum.'%' : $cl }}</strong>
                </td>
                @endif
            </tr>
        </table>
    </div>

    <table class="mini-table">
        <tr>
            <td class="ms-red">
                <span class="ms-num">{{ $data['supply_data']['out_of_stock_items'] }}</span>
                <span class="ms-lbl">Out of Stock</span>
            </td>
            <td class="ms-red">
                <span class="ms-num">{{ $data['supply_data']['critical_items'] }}</span>
                <span class="ms-lbl">Critical Level</span>
            </td>
            <td class="ms-amber">
                <span class="ms-num">{{ $data['supply_data']['low_stock_items'] }}</span>
                <span class="ms-lbl">Low Stock</span>
            </td>
            <td class="ms-amber">
                <span class="ms-num">{{ $data['supply_data']['needs_reorder'] }}</span>
                <span class="ms-lbl">Needs Reorder</span>
            </td>
            <td class="ms-green">
                <span class="ms-num">{{ number_format($data['supply_data']['available_stock']) }}</span>
                <span class="ms-lbl">Units in Stock</span>
            </td>
            <td class="ms-green">
                <span class="ms-num">{{ $data['supply_data']['total_items'] }}</span>
                <span class="ms-lbl">Item Types</span>
            </td>
        </tr>
    </table>
</div>

{{-- SECTION 02: KEY FINDINGS & CRITICAL ISSUES --}}
<div class="section">
    <div class="section-title">02 &nbsp;&bull;&nbsp; Key Findings &amp; Critical Issues</div>
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

{{-- SECTION 03: RECOMMENDATIONS --}}
@if(isset($report['report_data']['recommendations']))
<div class="section">
    <div class="section-title">03 &nbsp;&bull;&nbsp; AI-Generated Recommendations</div>
    <table class="two-col-table">
        <tr>
            <td>
                <div class="rec-head now">Immediate Actions</div>
                <ul class="rec-body">
                    @foreach($report['report_data']['recommendations']['immediate_actions'] as $action)
                        <li><span class="badge badge-danger" style="margin-right:5px;">NOW</span>{{ $action }}</li>
                    @endforeach
                </ul>
            </td>
            <td>
                <div class="rec-head soon">Short-Term Strategies (1&ndash;3 Months)</div>
                <ul class="rec-body">
                    @foreach($report['report_data']['recommendations']['short_term_strategies'] as $strategy)
                        <li><span class="badge badge-warning" style="margin-right:5px;">1-3M</span>{{ $strategy }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
    </table>
</div>
@endif

{{-- SECTION 04: PERFORMANCE ASSESSMENT --}}
@if(isset($report['report_data']['performance_assessment']))
<div class="section">
    <div class="section-title">04 &nbsp;&bull;&nbsp; Performance Assessment</div>

    @php
        $rating = $report['report_data']['performance_assessment']['overall_rating'] ?? 'N/A';
        $ratingBadge = match(strtolower($rating)) {
            'excellent' => 'success',
            'good'      => 'primary',
            'fair'      => 'warning',
            default     => 'danger',
        };
    @endphp

    <p style="font-size:10px; font-weight:700; color:#444; margin-bottom:8px;">
        Overall Rating:&nbsp;
        <span class="badge badge-{{ $ratingBadge }}" style="font-size:10px; padding:3px 10px;">{{ strtoupper($rating) }}</span>
    </p>

    <table class="perf-table">
        <tr>
            <td>
                <span class="perf-lbl">Approval Efficiency</span>
                <span class="perf-val">{{ $report['report_data']['performance_assessment']['approval_efficiency'] }}</span>
            </td>
            <td>
                <span class="perf-lbl">Supply Adequacy</span>
                <span class="perf-val">{{ $report['report_data']['performance_assessment']['supply_adequacy'] }}</span>
            </td>
            <td>
                <span class="perf-lbl">Geographic Coverage</span>
                <span class="perf-val">{{ $report['report_data']['performance_assessment']['geographic_coverage'] }}</span>
            </td>
        </tr>
    </table>
</div>
@endif

{{-- PAGE 2 — explicit break with zero gap div --}}
<div class="page-break"></div>

{{-- SECTION 05: DETAILED SUPPLY ANALYSIS --}}
<div class="section">
    <div class="section-title">05 &nbsp;&bull;&nbsp; Detailed Supply Analysis</div>

    <div class="sub-head">Stock Distribution by Category</div>
    @if(isset($data['supply_data']['items_by_category']) && count($data['supply_data']['items_by_category']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Items</th>
                    <th>Total Stock</th>
                    <th>Avg / Item</th>
                    <th>Out of Stock</th>
                    <th>Low Stock</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['supply_data']['items_by_category'] as $cat)
                    <tr>
                        <td class="text-bold">{{ $cat['category_name'] }}</td>
                        <td>{{ $cat['count'] }}</td>
                        <td>{{ number_format($cat['total_stock']) }}</td>
                        <td>{{ $cat['avg_stock'] }}</td>
                        <td class="{{ $cat['out_of_stock'] > 0 ? 'text-red' : '' }}">{{ $cat['out_of_stock'] }}</td>
                        <td class="{{ $cat['low_stock'] > 0 ? 'text-amber' : '' }}">{{ $cat['low_stock'] }}</td>
                        <td>
                            <span class="badge badge-{{ $cat['stock_status'] === 'Good' ? 'success' : ($cat['stock_status'] === 'Critical' ? 'danger' : 'warning') }}">
                                {{ $cat['stock_status'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert-ok">No category data available.</div>
    @endif

    <div class="sub-head">Critical &amp; Attention Items</div>
    @if(isset($data['supply_data']['attention_items']) && count($data['supply_data']['attention_items']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder Pt.</th>
                    <th>Status</th>
                    <th>Urgency</th>
                    <th>Recommended Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($data['supply_data']['attention_items']->toArray(), 0, 15) as $item)
                    <tr>
                        <td class="text-bold">{{ $item['name'] }}</td>
                        <td>{{ $item['category'] }}</td>
                        <td class="{{ $item['current_supply'] == 0 ? 'text-red' : 'text-amber' }}">
                            {{ $item['current_supply'] }} {{ $item['unit'] }}
                        </td>
                        <td>{{ $item['reorder_point'] ?? '&mdash;' }}</td>
                        <td>
                            <span class="badge badge-{{ in_array($item['status'], ['Out of Stock','Critical Level']) ? 'danger' : 'warning' }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $item['urgency'] === 'CRITICAL' ? 'danger' : ($item['urgency'] === 'HIGH' ? 'warning' : 'primary') }}">
                                {{ $item['urgency'] }}
                            </span>
                        </td>
                        <td style="font-size:8.5px; color:#444;">{{ $item['recommended_action'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert-ok">All items are at adequate stock levels. No immediate attention required.</div>
    @endif
</div>

{{-- SECTION 06: BARANGAY DEMAND & SHORTAGE --}}
<div class="section">
    <div class="section-title">06 &nbsp;&bull;&nbsp; Barangay Demand &amp; Shortage Analysis</div>
    <table class="two-col-table">
        <tr>
            <td>
                <div class="sub-head">Top Requesting Barangays</div>
                @if(count($data['barangay_analysis']['barangay_details']) > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Barangay</th>
                                <th>Requests</th>
                                <th>Quantity</th>
                                <th>Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($data['barangay_analysis']['barangay_details'], 0, 10) as $i => $b)
                                <tr>
                                    <td style="color:#888; font-size:9px;">{{ $i + 1 }}</td>
                                    <td class="text-bold">{{ $b['name'] }}</td>
                                    <td>{{ $b['requests'] }}</td>
                                    <td>{{ number_format($b['total_quantity']) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $b['priority_level'] === 'HIGH' ? 'danger' : ($b['priority_level'] === 'MEDIUM' ? 'warning' : 'success') }}">
                                            {{ $b['priority_level'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert-ok">No barangay demand data for this period.</div>
                @endif
            </td>
            <td>
                <div class="sub-head">Critical Supply Shortages</div>
                @if(count($data['shortage_analysis']['shortages']) > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Demanded</th>
                                <th>Available</th>
                                <th>Gap</th>
                                <th>Severity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($data['shortage_analysis']['shortages'], 0, 10) as $s)
                                <tr>
                                    <td class="text-bold">{{ $s['item'] }}</td>
                                    <td>{{ number_format($s['demanded']) }}</td>
                                    <td>{{ number_format($s['available']) }}</td>
                                    <td class="text-red">{{ number_format($s['shortage']) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $s['severity'] === 'CRITICAL' ? 'danger' : ($s['severity'] === 'HIGH' ? 'warning' : 'primary') }}">
                                            {{ $s['severity'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert-ok">No critical shortages detected for this period.</div>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- SECTION 07: SUPPLY MANAGEMENT SUMMARY --}}
@if(isset($data['supply_data']['supply_summary']))
<div class="section">
    <div class="section-title">07 &nbsp;&bull;&nbsp; Supply Management Summary</div>

    <div class="exec-box" style="margin-bottom:10px;">
        {{ $data['supply_data']['supply_summary']['summary_text'] }}
    </div>

    <table class="two-col-table">
        <tr>
            <td>
                <table class="info-table">
                    @if(count($data['supply_data']['supply_summary']['top_stocked_categories']) > 0)
                        <tr>
                            <td class="ik">Best Categories:</td>
                            <td class="iv text-green">{{ implode(', ', $data['supply_data']['supply_summary']['top_stocked_categories']) }}</td>
                        </tr>
                    @endif
                    @if(count($data['supply_data']['supply_summary']['concern_categories']) > 0)
                        <tr>
                            <td class="ik">Needs Attention:</td>
                            <td class="iv text-amber">{{ implode(', ', $data['supply_data']['supply_summary']['concern_categories']) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="ik">Items Needing Action:</td>
                        <td class="iv text-red">{{ $data['supply_data']['supply_summary']['immediate_attention_count'] }}</td>
                    </tr>
                    <tr>
                        <td class="ik">Items for Reorder:</td>
                        <td class="iv">{{ $data['supply_data']['supply_summary']['needs_reorder'] }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="info-table">
                    <tr>
                        <td class="ik">Out of Stock:</td>
                        <td class="iv text-red">{{ $data['supply_data']['supply_summary']['out_of_stock_percent'] }}% of items</td>
                    </tr>
                    <tr>
                        <td class="ik">Low Stock:</td>
                        <td class="iv text-amber">{{ $data['supply_data']['supply_summary']['low_stock_percent'] }}% of items</td>
                    </tr>
                    <tr>
                        <td class="ik">Total Units in Stock:</td>
                        <td class="iv">{{ number_format($data['supply_data']['supply_summary']['total_units']) }}</td>
                    </tr>
                    <tr>
                        <td class="ik">Supply Health Score:</td>
                        <td class="iv"><strong>{{ $data['supply_data']['supply_health_score'] }}%</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
@endif

{{-- ===== FOOTER ===== --}}
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
                Source: {{ ucfirst($report['source']) }}@if($report['source'] === 'llm') ({{ $report['model_used'] }})@endif<br>
                Confidence:
                <strong>
                    @php
                        $cl = $report['report_data']['confidence_level'] ?? null;
                        $cs = $report['report_data']['confidence_score'] ?? null;
                        $confNum = is_numeric($cl) ? $cl : (is_numeric($cs) ? $cs : null);
                        echo $confNum !== null ? $confNum.'%' : ($cl ?? 'N/A');
                    @endphp
                </strong>
            </td>
        </tr>
    </table>
</div>

</div>{{-- end .page --}}
</body>
</html>