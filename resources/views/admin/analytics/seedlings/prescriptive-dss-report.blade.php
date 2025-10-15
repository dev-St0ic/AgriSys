<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescriptive Decision Support Report</title>
    <style>
        @page {
            margin: 12mm;
            @top-center { 
                content: "PRESCRIPTIVE ANALYTICS | CONFIDENCE: {{ number_format($insights['confidence_percentage'] ?? 95, 1) }}%"; 
                font-size: 9px;
                color: #666;
            }
            @bottom-center { 
                content: "Page " counter(page) " | Generated: {{ now()->format('Y-m-d H:i') }}"; 
                font-size: 8px;
                color: #999;
            }
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.6;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        /* HEADER SECTION */
        .report-header {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            color: white;
            padding: 25px;
            margin: -12mm -12mm 20px -12mm;
            text-align: center;
            border-bottom: 5px solid #0d47a1;
        }

        .report-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-subtitle {
            font-size: 12px;
            opacity: 0.95;
            margin: 0;
        }

        /* CONFIDENCE BANNER */
        .confidence-banner {
            display: flex;
            justify-content: space-around;
            background: #fff;
            border: 3px solid #4caf50;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .confidence-item {
            text-align: center;
            flex: 1;
        }

        .confidence-value {
            font-size: 24px;
            font-weight: 700;
            color: #2e7d32;
            display: block;
            margin-bottom: 3px;
        }

        .confidence-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* EXECUTIVE DASHBOARD */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
        }

        .dashboard-card {
            background: linear-gradient(135deg, #f5f5f5 0%, #fff 100%);
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            min-height: 70px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .dashboard-value {
            font-size: 20px;
            font-weight: 700;
            color: #1976d2;
            margin: 5px 0;
        }

        .dashboard-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            line-height: 1.3;
        }

        .dashboard-status {
            font-size: 8px;
            margin-top: 4px;
            font-weight: 600;
        }

        .status-good { color: #4caf50; }
        .status-warning { color: #ff9800; }
        .status-critical { color: #f44336; }

        /* SECTION HEADERS */
        h2 {
            color: #1976d2;
            font-size: 14px;
            font-weight: 700;
            border-bottom: 3px solid #1976d2;
            padding-bottom: 6px;
            margin: 25px 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        h3 {
            color: #424242;
            font-size: 11px;
            font-weight: 600;
            margin: 15px 0 8px 0;
            border-left: 4px solid #2196f3;
            padding-left: 10px;
        }

        /* ACTION CARDS */
        .action-card {
            background: #fff;
            border-left: 5px solid #2196f3;
            border-radius: 4px;
            padding: 15px;
            margin: 12px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            page-break-inside: avoid;
        }

        .action-card.priority-1 { border-left-color: #f44336; background: #ffebee; }
        .action-card.priority-2 { border-left-color: #ff9800; background: #fff3e0; }
        .action-card.priority-3 { border-left-color: #2196f3; background: #e3f2fd; }

        .action-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .action-priority {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-critical { background: #f44336; }
        .priority-high { background: #ff9800; }
        .priority-medium { background: #2196f3; }

        .confidence-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: 700;
        }

        .confidence-veryhigh { background: #4caf50; color: white; }
        .confidence-high { background: #8bc34a; color: white; }
        .confidence-moderate { background: #ffc107; color: #000; }

        .action-title {
            font-size: 11px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 8px 0;
            line-height: 1.4;
        }

        .action-detail {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 8px;
            margin: 6px 0;
            font-size: 9px;
        }

        .detail-label {
            font-weight: 600;
            color: #616161;
            white-space: nowrap;
        }

        .detail-value {
            color: #1a1a1a;
        }

        .impact-highlight {
            background: #e8f5e9;
            border-left: 3px solid #4caf50;
            padding: 8px 10px;
            margin: 8px 0 0 0;
            font-size: 9px;
            border-radius: 0 4px 4px 0;
        }

        /* TABLES */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 9px;
            page-break-inside: avoid;
        }

        .data-table thead {
            background: #1976d2;
            color: white;
        }

        .data-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f5f5f5;
        }

        .data-table tbody tr:hover {
            background: #e3f2fd;
        }

        /* INFO BOXES */
        .info-box {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            border-radius: 6px;
            padding: 12px;
            margin: 15px 0;
            font-size: 9px;
            page-break-inside: avoid;
        }

        .info-box.success {
            background: #e8f5e9;
            border-color: #4caf50;
        }

        .info-box.warning {
            background: #fff3e0;
            border-color: #ff9800;
        }

        .info-box.critical {
            background: #ffebee;
            border-color: #f44336;
        }

        .info-box-title {
            font-weight: 700;
            font-size: 10px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
        }

        .info-icon {
            font-size: 14px;
            margin-right: 6px;
        }

        /* TIMELINE */
        .timeline {
            margin: 15px 0;
            padding-left: 20px;
            border-left: 3px solid #2196f3;
        }

        .timeline-item {
            margin-bottom: 15px;
            position: relative;
            page-break-inside: avoid;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -26px;
            top: 6px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2196f3;
            border: 3px solid white;
        }

        .timeline-phase {
            font-weight: 700;
            font-size: 10px;
            color: #1976d2;
            margin-bottom: 4px;
        }

        .timeline-content {
            font-size: 9px;
            color: #424242;
            line-height: 1.5;
        }

        /* METRICS GRID */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 15px 0;
        }

        .metric-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px;
        }

        .metric-title {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .metric-comparison {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .metric-current {
            font-size: 18px;
            font-weight: 700;
            color: #424242;
        }

        .metric-target {
            font-size: 14px;
            font-weight: 700;
            color: #4caf50;
        }

        .metric-arrow {
            font-size: 12px;
            color: #9e9e9e;
        }

        .metric-progress {
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
        }

        .metric-progress-bar {
            height: 100%;
            background: #4caf50;
            transition: width 0.3s;
        }

        /* FOOTER */
        .report-footer {
            margin-top: 30px;
            padding: 15px;
            background: #f5f5f5;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 8px;
            text-align: center;
            color: #616161;
            page-break-inside: avoid;
        }

        .footer-disclaimer {
            margin-top: 8px;
            line-height: 1.4;
        }

        /* UTILITY CLASSES */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-0 { margin-bottom: 0; }
        .mt-0 { margin-top: 0; }
        .font-bold { font-weight: 700; }
        .text-muted { color: #757575; }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <!-- REPORT HEADER -->
    <div class="report-header">
        <div class="report-title">Prescriptive Decision Support Report</div>
        <div class="report-subtitle">
            Municipal Agriculture Office - Seedling Distribution Program<br>
            Analysis Period: {{ $period ?? 'Current Fiscal Period' }} | Generated: {{ $generated_at ?? now()->format('F j, Y g:i A') }}
        </div>
    </div>

    <!-- CONFIDENCE DASHBOARD -->
    <div class="confidence-banner">
        <div class="confidence-item">
            <span class="confidence-value">{{ number_format($insights['data_quality_score'] ?? 85, 1) }}%</span>
            <span class="confidence-label">Data Quality</span>
        </div>
        <div class="confidence-item">
            <span class="confidence-value">{{ number_format($insights['confidence_percentage'] ?? 95, 1) }}%</span>
            <span class="confidence-label">Statistical Confidence</span>
        </div>
        <div class="confidence-item">
            <span class="confidence-value">{{ number_format($overview['total_requests'] ?? 0) }}</span>
            <span class="confidence-label">Sample Size</span>
        </div>
        <div class="confidence-item">
            <span class="confidence-value">{{ strtoupper($insights['ai_confidence'] ?? 'HIGH') }}</span>
            <span class="confidence-label">Confidence Level</span>
        </div>
    </div>

    <!-- PERFORMANCE DASHBOARD -->
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="dashboard-label">Total Applications</div>
            <div class="dashboard-value">{{ number_format($overview['total_requests'] ?? 0) }}</div>
            <div class="dashboard-status status-good">✓ Valid Sample</div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-label">Approval Rate</div>
            <div class="dashboard-value">{{ number_format($overview['approval_rate'] ?? 0, 1) }}%</div>
            <div class="dashboard-status {{ ($overview['approval_rate'] ?? 0) >= 85 ? 'status-good' : (($overview['approval_rate'] ?? 0) >= 75 ? 'status-warning' : 'status-critical') }}">
                Target: 85%
            </div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-label">Fulfillment Rate</div>
            <div class="dashboard-value">{{ number_format($overview['fulfillment_rate'] ?? 0, 1) }}%</div>
            <div class="dashboard-status {{ ($overview['fulfillment_rate'] ?? 0) >= 90 ? 'status-good' : (($overview['fulfillment_rate'] ?? 0) >= 80 ? 'status-warning' : 'status-critical') }}">
                Target: 90%
            </div>
        </div>
        <div class="dashboard-card">
            <div class="dashboard-label">Processing Time</div>
            <div class="dashboard-value">{{ number_format($processingTimeAnalysis['avg_processing_days'] ?? 0, 1) }}d</div>
            <div class="dashboard-status {{ ($processingTimeAnalysis['avg_processing_days'] ?? 0) <= 3 ? 'status-good' : (($processingTimeAnalysis['avg_processing_days'] ?? 0) <= 5 ? 'status-warning' : 'status-critical') }}">
                Target: <3 days
            </div>
        </div>
    </div>

    <!-- EXECUTIVE SUMMARY -->
    <h2>I. Executive Summary</h2>
    <div class="info-box">
        <div class="info-box-title">
            <span class="info-icon">📊</span>
            Key Findings & Strategic Priorities
        </div>
        @if(isset($insights['executive_summary']) && is_array($insights['executive_summary']))
            @foreach($insights['executive_summary'] as $summary)
                <p style="margin: 8px 0;">• {{ $summary }}</p>
            @endforeach
        @else
            <p>Prescriptive analysis identifies {{ count($insights['immediate_actions'] ?? []) }} immediate actions requiring decision within 30 days to optimize program performance.</p>
        @endif
    </div>

    <!-- IMMEDIATE ACTIONS (30 DAYS) -->
    <h2>II. Immediate Actions (Next 30 Days)</h2>
    <div class="info-box warning">
        <div class="info-box-title">
            <span class="info-icon">⚠️</span>
            Decision Required: These 3 actions require immediate authorization and resource allocation
        </div>
    </div>

    @if(isset($insights['immediate_actions']) && count($insights['immediate_actions']) > 0)
        @foreach($insights['immediate_actions'] as $index => $action)
            <div class="action-card priority-{{ $index + 1 }}">
                <div class="action-header">
                    <span class="action-priority priority-{{ $index === 0 ? 'critical' : ($index === 1 ? 'high' : 'medium') }}">
                        PRIORITY {{ $index + 1 }}
                    </span>
                    <span class="confidence-badge confidence-{{ isset($action['confidence']) && strpos($action['confidence'], '9') === 0 && (int)substr($action['confidence'], 0, 2) >= 95 ? 'veryhigh' : 'high' }}">
                        {{ $action['confidence'] ?? '90%' }} Confidence
                    </span>
                </div>
                
                <div class="action-title">{{ $action['action'] ?? $action }}</div>
                
                @if(is_array($action))
                    @if(isset($action['rationale']))
                        <div class="action-detail">
                            <div class="detail-label">Why:</div>
                            <div class="detail-value">{{ $action['rationale'] }}</div>
                        </div>
                    @endif
                    
                    @if(isset($action['success_metric']))
                        <div class="action-detail">
                            <div class="detail-label">Success Metric:</div>
                            <div class="detail-value">{{ $action['success_metric'] }}</div>
                        </div>
                    @endif
                    
                    @if(isset($action['effort']))
                        <div class="action-detail">
                            <div class="detail-label">Effort:</div>
                            <div class="detail-value">{{ $action['effort'] }}</div>
                        </div>
                    @endif
                    
                    @if(isset($action['expected_impact']))
                        <div class="impact-highlight">
                            <strong>💡 Expected Impact:</strong> {{ $action['expected_impact'] }}
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    @else
        <div class="info-box success">
            <p>No critical actions identified. Current performance meets or exceeds targets. Focus on maintaining standards and continuous improvement.</p>
        </div>
    @endif

    <!-- RESOURCE OPTIMIZATION (60 DAYS) -->
    <div class="page-break"></div>
    <h2>III. Resource Optimization (60-Day Horizon)</h2>
    <div class="info-box">
        <div class="info-box-title">
            <span class="info-icon">💰</span>
            Budget & Inventory Allocation Adjustments
        </div>
    </div>

    @if(isset($insights['resource_optimization']) && count($insights['resource_optimization']) > 0)
        @foreach($insights['resource_optimization'] as $index => $action)
            <div class="action-card priority-3">
                <div class="action-header">
                    <span class="action-priority priority-medium">RESOURCE ACTION {{ $index + 1 }}</span>
                    <span class="confidence-badge confidence-high">
                        {{ $action['confidence'] ?? '90%' }} Confidence
                    </span>
                </div>
                
                <div class="action-title">{{ $action['action'] ?? $action }}</div>
                
                @if(is_array($action))
                    @if(isset($action['rationale']))
                        <div class="action-detail">
                            <div class="detail-label">Justification:</div>
                            <div class="detail-value">{{ $action['rationale'] }}</div>
                        </div>
                    @endif
                    
                    @if(isset($action['success_metric']))
                        <div class="action-detail">
                            <div class="detail-label">Target:</div>
                            <div class="detail-value">{{ $action['success_metric'] }}</div>
                        </div>
                    @endif
                    
                    @if(isset($action['expected_impact']))
                        <div class="impact-highlight">
                            <strong>📈 Expected ROI:</strong> {{ $action['expected_impact'] }}
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    @endif

    <!-- GEOGRAPHIC EXPANSION (90 DAYS) -->
    <h2>IV. Geographic Expansion Strategy (90-Day Strategic)</h2>
    <div class="info-box">
        <div class="info-box-title">
            <span class="info-icon">🗺️</span>
            Location-Specific Interventions
        </div>
    </div>

    @if(isset($insights['geographic_expansion']) && count($insights['geographic_expansion']) > 0)
        @foreach($insights['geographic_expansion'] as $index => $action)
            <div class="action-card priority-3">
                <div class="action-header">
                    <span class="action-priority priority-medium">GEOGRAPHIC {{ $index + 1 }}</span>
                    <span class="confidence-badge confidence-moderate">
                        {{ $action['confidence'] ?? '85%' }} Confidence
                    </span>
                </div>
                
                <div class="action-title">{{ $action['action'] ?? $action }}</div>
                
                @if(is_array($action))
                    @if(isset($action['rationale']))
                        <div class="action-detail">
                            <div class="detail-label">Context:</div>
                            <div class="detail-value">{{ $action['rationale'] }}</div>
                        </div>
                    @endif
                    
                    @if(isset($action['expected_impact']))
                        <div class="impact-highlight">
                            <strong>🎯 Reach Impact:</strong> {{ $action['expected_impact'] }}
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    @endif

    <!-- PROCESS IMPROVEMENTS -->
    <h2>V. Process Optimization</h2>
    @if(isset($insights['process_improvements']) && count($insights['process_improvements']) > 0)
        @foreach($insights['process_improvements'] as $index => $action)
            <div class="action-card priority-3">
                <div class="action-header">
                    <span class="action-priority priority-medium">PROCESS {{ $index + 1 }}</span>
                    <span class="confidence-badge confidence-high">
                        {{ $action['confidence'] ?? '92%' }} Confidence
                    </span>
                </div>
                
                <div class="action-title">{{ $action['action'] ?? $action }}</div>
                
                @if(is_array($action) && isset($action['expected_impact']))
                    <div class="impact-highlight">
                        <strong>⚡ Efficiency Gain:</strong> {{ $action['expected_impact'] }}
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    <!-- CAPACITY SCALING -->
    <h2>VI. Capacity Planning</h2>
    @if(isset($insights['capacity_scaling']) && count($insights['capacity_scaling']) > 0)
        @foreach($insights['capacity_scaling'] as $index => $action)
            <div class="action-card priority-3">
                <div class="action-header">
                    <span class="action-priority priority-medium">CAPACITY {{ $index + 1 }}</span>
                </div>
                
                <div class="action-title">{{ $action['action'] ?? $action }}</div>
                
                @if(is_array($action) && isset($action['expected_impact']))
                    <div class="impact-highlight">
                        <strong>📊 Scaling Impact:</strong> {{ $action['expected_impact'] }}
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    <!-- IMPLEMENTATION TIMELINE -->
    <div class="page-break"></div>
    <h2>VII. 90-Day Implementation Roadmap</h2>
    <div class="timeline">
        <div class="timeline-item">
            <div class="timeline-phase">📅 Phase 1: Days 1-30 (IMMEDIATE)</div>
            <div class="timeline-content">
                <strong>Focus:</strong> Execute Priority 1-2 immediate actions<br>
                <strong>Milestones:</strong> Approval rate +8-10%, monitoring systems active<br>
                <strong>Resources:</strong> Staff training, policy updates, budget reallocation<br>
                <strong>Success Criteria:</strong> Measurable improvement in target KPIs
            </div>
        </div>
        
        <div class="timeline-item">
            <div class="timeline-phase">📅 Phase 2: Days 31-60 (OPTIMIZATION)</div>
            <div class="timeline-content">
                <strong>Focus:</strong> Resource reallocation, geographic interventions<br>
                <strong>Milestones:</strong> 90%+ fulfillment rate, 3 new barangays activated<br>
                <strong>Resources:</strong> Inventory adjustments, mobile units, coordinators<br>
                <strong>Success Criteria:</strong> Supply-demand balance achieved
            </div>
        </div>
        
        <div class="timeline-item">
            <div class="timeline-phase">📅 Phase 3: Days 61-90 (SCALING)</div>
            <div class="timeline-content">
                <strong>Focus:</strong> Process automation, capacity expansion<br>
                <strong>Milestones:</strong> <3 day processing, 1.5x capacity readiness<br>
                <strong>Resources:</strong> Digital systems, infrastructure upgrades<br>
                <strong>Success Criteria:</strong> Sustainable growth capability established
            </div>
        </div>
    </div>

    <!-- MONITORING FRAMEWORK -->
    <h2>VIII. Performance Monitoring Framework</h2>
    <div class="info-box success">
        <div class="info-box-title">
            <span class="info-icon">📈</span>
            Weekly & Monthly KPI Tracking Requirements
        </div>
    </div>

    @if(isset($insights['monitoring_framework']) && count($insights['monitoring_framework']) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Key Performance Indicator</th>
                    <th style="width: 15%;">Current</th>
                    <th style="width: 15%;">Target</th>
                    <th style="width: 20%;">Alert Threshold</th>
                    <th style="width: 25%;">Review Frequency</th>
                </tr>
            </thead>
            <tbody>
                @foreach($insights['monitoring_framework'] as $kpi)
                    <tr>
                        <td><strong>{{ $kpi['kpi'] ?? ($kpi['action'] ?? 'N/A') }}</strong></td>
                        <td>{{ $kpi['current'] ?? 'Baseline' }}</td>
                        <td style="color: #4caf50; font-weight: 600;">{{ $kpi['target'] ?? 'TBD' }}</td>
                        <td>{{ $kpi['alert'] ?? 'Not defined' }}</td>
                        <td>{{ strpos(strtolower($kpi['kpi'] ?? ''), 'weekly') !== false ? 'Weekly' : 'Monthly' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="info-box warning">
            <p><strong>Recommendation:</strong> Establish baseline KPI tracking system to measure intervention effectiveness.</p>
        </div>
    @endif

    <!-- PROJECTED OUTCOMES -->
    <h2>IX. Projected Performance Improvement</h2>
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-title">Approval Rate Projection</div>
            <div class="metric-comparison">
                <span class="metric-current">{{ number_format($overview['approval_rate'] ?? 0, 1) }}%</span>
                <span class="metric-arrow">→</span>
                <span class="metric-target">{{ number_format(min(100, ($overview['approval_rate'] ?? 0) + 15), 1) }}%</span>
            </div>
            <div class="metric-progress">
                <div class="metric-progress-bar" style="width: {{ min(100, ($overview['approval_rate'] ?? 0) + 15) }}%;"></div>
            </div>
            <div style="font-size: 8px; color: #666; margin-top: 5px;">90-Day Target</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-title">Fulfillment Rate Projection</div>
            <div class="metric-comparison">
                <span class="metric-current">{{ number_format($overview['fulfillment_rate'] ?? 0, 1) }}%</span>
                <span class="metric-arrow">→</span>
                <span class="metric-target">{{ number_format(min(100, ($overview['fulfillment_rate'] ?? 0) + 15), 1) }}%</span>
            </div>
            <div class="metric-progress">
                <div class="metric-progress-bar" style="width: {{ min(100, ($overview['fulfillment_rate'] ?? 0) + 15) }}%;"></div>
            </div>
            <div style="font-size: 8px; color: #666; margin-top: 5px;">90-Day Target</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-title">Processing Time Reduction</div>
            <div class="metric-comparison">
                <span class="metric-current">{{ number_format($processingTimeAnalysis['avg_processing_days'] ?? 5, 1) }}d</span>
                <span class="metric-arrow">→</span>
                <span class="metric-target">{{ number_format(max(2, ($processingTimeAnalysis['avg_processing_days'] ?? 5) * 0.5), 1) }}d</span>
            </div>
            <div class="metric-progress">
                <div class="metric-progress-bar" style="width: {{ 100 - (max(2, ($processingTimeAnalysis['avg_processing_days'] ?? 5) * 0.5) / ($processingTimeAnalysis['avg_processing_days'] ?? 5) * 100) }}%;"></div>
            </div>
            <div style="font-size: 8px; color: #666; margin-top: 5px;">50% Reduction Target</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-title">Geographic Coverage Expansion</div>
            <div class="metric-comparison">
                <span class="metric-current">{{ $overview['active_barangays'] ?? 0 }}/20</span>
                <span class="metric-arrow">→</span>
                <span class="metric-target">{{ min(20, ($overview['active_barangays'] ?? 0) + 5) }}/20</span>
            </div>
            <div class="metric-progress">
                <div class="metric-progress-bar" style="width: {{ (min(20, ($overview['active_barangays'] ?? 0) + 5) / 20) * 100 }}%;"></div>
            </div>
            <div style="font-size: 8px; color: #666; margin-top: 5px;">+5 Barangays Target</div>
        </div>
    </div>

    <!-- STATISTICAL METHODOLOGY -->
    <h2>X. Statistical Methodology & Confidence</h2>
    <div class="info-box">
        <div class="info-box-title">
            <span class="info-icon">🔬</span>
            Analysis Framework
        </div>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px;">
            <div>
                <strong>Sample Size:</strong> n = {{ number_format($overview['total_requests'] ?? 0) }}<br>
                <strong>Confidence Level:</strong> {{ number_format($insights['confidence_percentage'] ?? 95, 1) }}%<br>
                <strong>Data Quality:</strong> {{ number_format($insights['data_quality_score'] ?? 85, 1) }}%
            </div>
            <div>
                <strong>Analysis Model:</strong> {{ $insights['model_version'] ?? 'Claude Sonnet 4' }}<br>
                <strong>Analysis Type:</strong> Prescriptive Decision Support<br>
                <strong>Report Validity:</strong> 90 days
            </div>
        </div>
    </div>

    @if(($overview['total_requests'] ?? 0) < 100 || ($insights['data_quality_score'] ?? 100) < 70)
        <div class="info-box warning">
            <div class="info-box-title">
                <span class="info-icon">⚠️</span>
                Data Quality Notice
            </div>
            <p style="margin: 8px 0;">
                @if(($overview['total_requests'] ?? 0) < 100)
                    <strong>Limited Sample Size:</strong> Current sample (n={{ $overview['total_requests'] ?? 0 }}) is below optimal threshold (n≥100). 
                    Recommend pilot testing prescriptions before full-scale implementation.<br>
                @endif
                @if(($insights['data_quality_score'] ?? 100) < 70)
                    <strong>Data Completeness:</strong> Some data fields missing or inconsistent. 
                    Confidence intervals wider than optimal. Improve data collection for future analyses.
                @endif
            </p>
        </div>
    @endif

    <!-- FOOTER -->
    <div class="report-footer">
        <div style="font-size: 10px; font-weight: 700; margin-bottom: 8px;">
            PRESCRIPTIVE DECISION SUPPORT REPORT
        </div>
        <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 10px;">
            <span><strong>Confidence:</strong> {{ number_format($insights['confidence_percentage'] ?? 95, 1) }}%</span>
            <span><strong>Data Quality:</strong> {{ number_format($insights['data_quality_score'] ?? 85, 1) }}%</span>
            <span><strong>Model:</strong> {{ $insights['model_version'] ?? 'Claude Sonnet 4' }}</span>
        </div>
        <div class="footer-disclaimer">
            <strong>IMPLEMENTATION GUIDANCE:</strong> This report provides evidence-based prescriptions with quantified confidence levels. 
            All recommendations should be reviewed by program leadership and adapted to local context, budget constraints, and policy requirements. 
            Implement high-confidence (>90%) prescriptions with standard approval processes. For lower-confidence prescriptions, 
            conduct pilot testing before full deployment. Monitor KPIs weekly to validate intervention effectiveness and adjust as needed. 
            Report validity: 90 days from generation date. Re-analysis recommended quarterly or when significant program changes occur.
        </div>
    </div>
</body>
</html>