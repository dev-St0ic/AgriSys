{{-- resources/views/admin/analytics/seedlings.blade.php --}}

@extends('layouts.app')

@section('title', 'Analytics - AgriSys Admin')
@section('page-title', 'Analytics Dashboard')

@section('content')
<!-- Header with Service Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 text-dark">Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into agricultural services</p>
                    </div>
                    <!-- Service Tabs - Unified Structure -->
                    <ul class="nav nav-pills" id="serviceTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.seedlings') }}" 
                            class="nav-link {{ request()->routeIs('admin.analytics.seedlings') ? 'active' : '' }}">
                                <i class="fas fa-seedling me-1"></i> Seedlings
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.rsbsa') }}" 
                            class="nav-link {{ request()->routeIs('admin.analytics.rsbsa') ? 'active' : '' }}">
                                <i class="fas fa-user-check me-1"></i> RSBSA
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.fishr') }}" 
                            class="nav-link {{ request()->routeIs('admin.analytics.fishr') ? 'active' : '' }}">
                                <i class="fas fa-fish me-1"></i> FISHR
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#" class="nav-link disabled">
                                <i class="fas fa-tools me-1"></i> Equipment
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Content -->
<div class="tab-content" id="serviceTabContent">
    <!-- Seedlings Service Tab -->
    <div class="tab-pane fade show active" id="seedlings-service" role="tabpanel">
        
        <!-- Date Range Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.analytics.seedlings') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ $endDate }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter me-1"></i> Apply Filter
                                </button>
                                <a href="{{ route('admin.analytics.seedlings.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="btn btn-success me-2">
                                    <i class="fas fa-download me-1"></i> Export
                                </a>
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#insightsModal">
                                    <i class="fas fa-lightbulb me-1"></i> AI Insights
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics Row -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 metric-card">
                    <div class="card-body text-center p-4">
                        <div class="metric-icon mb-3">
                            <i class="fas fa-seedling fa-2x text-success"></i>
                        </div>
                        <h2 class="text-dark mb-1">{{ number_format($overview['total_requests']) }}</h2>
                        <h6 class="text-muted mb-2">Total Requests</h6>
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i>12% from last period
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 metric-card">
                    <div class="card-body text-center p-4">
                        <div class="metric-icon mb-3">
                            <i class="fas fa-check-circle fa-2x text-primary"></i>
                        </div>
                        <h2 class="text-dark mb-1">{{ $overview['approval_rate'] }}%</h2>
                        <h6 class="text-muted mb-2">Approval Rate</h6>
                        <small class="text-muted">{{ number_format($overview['approved_requests']) }} approved</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 metric-card">
                    <div class="card-body text-center p-4">
                        <div class="metric-icon mb-3">
                            <i class="fas fa-chart-bar fa-2x text-warning"></i>
                        </div>
                        <h2 class="text-dark mb-1">{{ number_format($overview['total_quantity_requested']) }}</h2>
                        <h6 class="text-muted mb-2">Items Distributed</h6>
                        <small class="text-muted">{{ number_format($overview['avg_request_size'], 1) }} avg per request</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 metric-card">
                    <div class="card-body text-center p-4">
                        <div class="metric-icon mb-3">
                            <i class="fas fa-map-marker-alt fa-2x text-info"></i>
                        </div>
                        <h2 class="text-dark mb-1">{{ $overview['active_barangays'] }}</h2>
                        <h6 class="text-muted mb-2">Active Barangays</h6>
                        <small class="text-muted">{{ number_format($overview['unique_applicants']) }} farmers served</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descriptive Analytics Cards -->
        <div class="row mb-4">
            <!-- Most Popular Items -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100 insight-card">
                    <div class="card-body text-center p-4">
                        <div class="insight-icon mb-3">
                            <i class="fas fa-fire fa-2x text-danger"></i>
                        </div>
                        <h5 class="text-dark mb-2">Most Popular</h5>
                        <h6 class="text-primary mb-2">{{ $topItems->first()['name'] ?? 'N/A' }}</h6>
                        <p class="text-muted small mb-0">
                            {{ number_format($topItems->first()['total_quantity'] ?? 0) }} units requested
                            <br>in {{ $topItems->first()['request_count'] ?? 0 }} requests
                        </p>
                    </div>
                </div>
            </div>

            <!-- Least Requested Category -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100 insight-card">
                    <div class="card-body text-center p-4">
                        <div class="insight-icon mb-3">
                            <i class="fas fa-chart-line-down fa-2x text-warning"></i>
                        </div>
                        <h5 class="text-dark mb-2">Needs Attention</h5>
                        @php
                            $leastCategory = collect($categoryAnalysis)->sortBy('requests')->first();
                            $leastCategoryName = collect($categoryAnalysis)->sortBy('requests')->keys()->first();
                        @endphp
                        <h6 class="text-warning mb-2">{{ ucfirst($leastCategoryName ?? 'N/A') }}</h6>
                        <p class="text-muted small mb-0">
                            Only {{ $leastCategory['requests'] ?? 0 }} requests
                            <br>Consider promotion campaigns
                        </p>
                    </div>
                </div>
            </div>

            <!-- Best Performing Barangay -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100 insight-card">
                    <div class="card-body text-center p-4">
                        <div class="insight-icon mb-3">
                            <i class="fas fa-crown fa-2x text-success"></i>
                        </div>
                        <h5 class="text-dark mb-2">Top Performer</h5>
                        <h6 class="text-success mb-2">{{ $barangayAnalysis->first()->barangay ?? 'N/A' }}</h6>
                        <p class="text-muted small mb-0">
                            {{ $barangayAnalysis->first()->total_requests ?? 0 }} total requests
                            <br>{{ round(($barangayAnalysis->first()->approved ?? 0) / max(1, $barangayAnalysis->first()->total_requests ?? 1) * 100, 1) }}% approval rate
                        </p>
                    </div>
                </div>
            </div>

            <!-- Average Processing Insight -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100 insight-card">
                    <div class="card-body text-center p-4">
                        <div class="insight-icon mb-3">
                            <i class="fas fa-tachometer-alt fa-2x text-info"></i>
                        </div>
                        <h5 class="text-dark mb-2">Efficiency</h5>
                        <h6 class="text-{{ $processingTimeAnalysis['avg_processing_days'] <= 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] <= 7 ? 'warning' : 'danger') }} mb-2">
                            {{ $processingTimeAnalysis['avg_processing_days'] }}d Average
                        </h6>
                        <p class="text-muted small mb-0">
                            {{ $processingTimeAnalysis['avg_processing_days'] <= 3 ? 'Excellent' : ($processingTimeAnalysis['avg_processing_days'] <= 7 ? 'Good' : 'Needs Improvement') }} processing time
                            <br>Range: {{ $processingTimeAnalysis['min_processing_days'] }}-{{ $processingTimeAnalysis['max_processing_days'] }} days
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <!-- Request Status with Labels -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-pie-chart me-2 text-primary"></i>Request Status Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="position-relative">
                            <canvas id="statusPieChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-chart-line me-2 text-success"></i>Monthly Request Trends
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendsChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row mb-4">
            <!-- Top Barangays Performance -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-trophy me-2 text-warning"></i>Top Performing Barangays
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="barangayChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-leaf me-2 text-success"></i>Category Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="row mb-4">
            <!-- Most Requested Items -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-star me-2 text-danger"></i>Most Requested Items
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topItemsChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            <!-- Processing Time Analysis -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-clock me-2 text-info"></i>Processing Time
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="processingTimeChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seasonal Analysis -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-calendar-alt me-2 text-success"></i>Seasonal Request Patterns
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="seasonalChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Summary Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-table me-2 text-primary"></i>Detailed Performance Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Barangay</th>
                                        <th>Total Requests</th>
                                        <th>Approved</th>
                                        <th>Approval Rate</th>
                                        <th>Total Quantity</th>
                                        <th>Avg Quantity</th>
                                        <th>Unique Farmers</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangayAnalysis->take(10) as $barangay)
                                    @php
                                        $approvalRate = $barangay->total_requests > 0 ? round(($barangay->approved / $barangay->total_requests) * 100, 1) : 0;
                                        $performanceClass = $approvalRate >= 80 ? 'success' : ($approvalRate >= 60 ? 'warning' : 'danger');
                                        $performanceText = $approvalRate >= 80 ? 'Excellent' : ($approvalRate >= 60 ? 'Good' : 'Needs Improvement');
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $barangay->barangay }}</strong></td>
                                        <td>{{ $barangay->total_requests }}</td>
                                        <td><span class="badge bg-success">{{ $barangay->approved }}</span></td>
                                        <td>{{ $approvalRate }}%</td>
                                        <td>{{ number_format($barangay->total_quantity) }}</td>
                                        <td>{{ number_format($barangay->avg_quantity, 1) }}</td>
                                        <td>{{ $barangay->unique_applicants }}</td>
                                        <td><span class="badge bg-{{ $performanceClass }}">{{ $performanceText }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Service Tab -->
    <div class="tab-pane fade" id="equipment-service" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                <h4>Equipment Analytics</h4>
                <p class="text-muted">Analytics for equipment loans and maintenance will be displayed here.</p>
                <button class="btn btn-primary">Configure Equipment Analytics</button>
            </div>
        </div>
    </div>

    <!-- Training Service Tab -->
    <div class="tab-pane fade" id="training-service" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                <h4>Training Analytics</h4>
                <p class="text-muted">Analytics for training programs and attendance will be displayed here.</p>
                <button class="btn btn-primary">Configure Training Analytics</button>
            </div>
        </div>
    </div>
</div>

<!-- AI Insights Modal -->
<div class="modal fade" id="insightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-robot me-2"></i>AI-Powered Insights
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-line text-success me-2"></i>Growth Opportunities</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-arrow-up text-success me-2"></i>
                                Consider expanding to {{ $barangayAnalysis->count() > 5 ? 'underserved' : 'additional' }} barangays
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-seedling text-info me-2"></i>
                                Focus on high-demand items like {{ $topItems->first()['name'] ?? 'popular crops' }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Areas for Improvement</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                {{ $processingTimeAnalysis['avg_processing_days'] > 5 ? 'Reduce processing time' : 'Maintain current processing speed' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-balance-scale text-info me-2"></i>
                                Balance distribution across crop categories
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.metric-card, .insight-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 10px;
}

.metric-card:hover, .insight-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.metric-icon, .insight-icon {
    opacity: 0.8;
}

.card {
    border-radius: 10px;
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.nav-pills .nav-link {
    border-radius: 25px;
    margin: 0 5px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.nav-pills .nav-link.active {
    background-color: #007bff;
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
}

.nav-pills .nav-link:not(.active):hover {
    background-color: rgba(0,123,255,0.1);
    border-color: rgba(0,123,255,0.3);
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.03);
}

/* Chart container styling */
.chart-container {
    position: relative;
    height: 300px;
}

/* Loading animation */
.chart-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let chartInstances = {};
    
    // Chart.js defaults for clean appearance
    Chart.defaults.color = '#666';
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.plugins.legend.display = true;
    Chart.defaults.plugins.tooltip.enabled = true;
    Chart.defaults.plugins.tooltip.backgroundColor = '#fff';
    Chart.defaults.plugins.tooltip.titleColor = '#333';
    Chart.defaults.plugins.tooltip.bodyColor = '#333';
    Chart.defaults.plugins.tooltip.borderColor = '#ddd';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    
    // Color palette
    const colors = {
        primary: '#007bff',
        success: '#28a745',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#17a2b8',
        secondary: '#6c757d'
    };
    
    // Initialize all charts for seedlings
    initializeStatusPieChart();
    initializeTrendsChart();
    initializeBarangayChart();
    initializeCategoryChart();
    initializeTopItemsChart();
    initializeProcessingTimeChart();
    initializeSeasonalChart();
    
    function initializeStatusPieChart() {
        const ctx = document.getElementById('statusPieChart');
        if (!ctx) return;
        
        const data = [{{ implode(',', $statusAnalysis['counts']) }}];
        const labels = [
            @foreach($statusAnalysis['counts'] as $status => $count)
                '{{ ucfirst(str_replace("_", " ", $status)) }}',
            @endforeach
        ];
        
        const backgroundColors = [colors.success, colors.danger, colors.warning];
        
        chartInstances.statusPieChart = new Chart(ctx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const dataset = data.datasets[0];
                                        const value = dataset.data[i];
                                        const total = dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label}: ${value} (${percentage}%)`,
                                            fillStyle: dataset.backgroundColor[i],
                                            pointStyle: 'circle'
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#fff',
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value, ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return percentage + '%';
                        }
                    }
                }
            }
        });
    }
    
    function initializeTrendsChart() {
        const ctx = document.getElementById('trendsChart');
        if (!ctx) return;
        
        chartInstances.trendsChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: [
                    @foreach($monthlyTrends as $trend)
                        '{{ \Carbon\Carbon::createFromFormat("Y-m", $trend->month)->format("M Y") }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Total Requests',
                    data: [{{ $monthlyTrends->pluck('total_requests')->implode(',') }}],
                    borderColor: colors.primary,
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: colors.primary,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }, {
                    label: 'Approved',
                    data: [{{ $monthlyTrends->pluck('approved')->implode(',') }}],
                    borderColor: colors.success,
                    backgroundColor: 'rgba(40,167,69,0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: colors.success,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { 
                            display: false 
                        },
                        ticks: {
                            color: '#666'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: '#f0f0f0',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
    
    function initializeBarangayChart() {
        const ctx = document.getElementById('barangayChart');
        if (!ctx) return;
        
        const barangayData = [
            @foreach($barangayAnalysis->take(10) as $barangay)
                {{ $barangay->total_requests }},
            @endforeach
        ];
        
        const barangayLabels = [
            @foreach($barangayAnalysis->take(10) as $barangay)
                '{{ $barangay->barangay }}',
            @endforeach
        ];
        
        chartInstances.barangayChart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: barangayLabels,
                datasets: [{
                    label: 'Total Requests',
                    data: barangayData,
                    backgroundColor: colors.primary,
                    borderColor: colors.primary,
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'top',
                        color: '#666',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { 
                            display: false 
                        },
                        ticks: {
                            color: '#666',
                            maxRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: '#f0f0f0',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666'
                        }
                    }
                }
            }
        });
    }
    
    function initializeCategoryChart() {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;
        
        const categoryData = [
            @foreach($categoryAnalysis as $category => $data)
                {{ $data['requests'] }},
            @endforeach
        ];
        
        const categoryLabels = [
            @foreach($categoryAnalysis as $category => $data)
                '{{ ucfirst($category) }}',
            @endforeach
        ];
        
        const categoryColors = [
            colors.success, colors.warning, colors.info, colors.danger, colors.secondary
        ];
        
        chartInstances.categoryChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: categoryColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const dataset = data.datasets[0];
                                        const value = dataset.data[i];
                                        const total = dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label}: ${value} (${percentage}%)`,
                                            fillStyle: dataset.backgroundColor[i],
                                            pointStyle: 'circle'
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#fff',
                        font: {
                            weight: 'bold'
                        },
                        formatter: (value, ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return percentage + '%';
                        }
                    }
                }
            }
        });
    }
    
    function initializeTopItemsChart() {
        const ctx = document.getElementById('topItemsChart');
        if (!ctx) return;
        
        const itemData = [
            @foreach($topItems->take(10) as $item)
                {{ $item['total_quantity'] }},
            @endforeach
        ];
        
        const itemLabels = [
            @foreach($topItems->take(10) as $item)
                '{{ $item['name'] }}',
            @endforeach
        ];
        
        chartInstances.topItemsChart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: itemLabels,
                datasets: [{
                    label: 'Total Quantity Requested',
                    data: itemData,
                    backgroundColor: colors.success,
                    borderColor: colors.success,
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'right',
                        color: '#666',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { 
                            color: '#f0f0f0',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666'
                        }
                    },
                    y: {
                        grid: { 
                            display: false 
                        },
                        ticks: {
                            color: '#666'
                        }
                    }
                }
            }
        });
    }
    
    function initializeProcessingTimeChart() {
        const ctx = document.getElementById('processingTimeChart');
        if (!ctx) return;
        
        // Sample processing time distribution data
        const processingData = [
            {{ $processingTimeAnalysis['same_day'] ?? 0 }},
            {{ $processingTimeAnalysis['1_3_days'] ?? 0 }},
            {{ $processingTimeAnalysis['4_7_days'] ?? 0 }},
            {{ $processingTimeAnalysis['over_7_days'] ?? 0 }}
        ];
        
        const processingLabels = ['Same Day', '1-3 Days', '4-7 Days', 'Over 7 Days'];
        const processingColors = [colors.success, colors.info, colors.warning, colors.danger];
        
        chartInstances.processingTimeChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: processingLabels,
                datasets: [{
                    data: processingData,
                    backgroundColor: processingColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const dataset = data.datasets[0];
                                        const value = dataset.data[i];
                                        const total = dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                        return {
                                            text: `${label}: ${percentage}%`,
                                            fillStyle: dataset.backgroundColor[i],
                                            pointStyle: 'circle'
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: (value, ctx) => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            if (total === 0) return '0%';
                            const percentage = ((value / total) * 100).toFixed(1);
                            return percentage + '%';
                        }
                    }
                }
            }
        });
    }
    
    function initializeSeasonalChart() {
        const ctx = document.getElementById('seasonalChart');
        if (!ctx) return;
        
        // Sample seasonal data - you'll need to replace with actual data
        const seasonalMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const seasonalRequests = [
            @php
                // Generate sample seasonal data or use actual data
                $sampleSeasonalData = [45, 52, 78, 95, 120, 85, 92, 88, 76, 82, 65, 58];
                echo implode(',', $sampleSeasonalData);
            @endphp
        ];
        const seasonalApproved = [
            @php
                $sampleApprovedData = [40, 48, 72, 88, 110, 78, 85, 82, 70, 76, 60, 52];
                echo implode(',', $sampleApprovedData);
            @endphp
        ];
        
        chartInstances.seasonalChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: seasonalMonths,
                datasets: [{
                    label: 'Total Requests',
                    data: seasonalRequests,
                    borderColor: colors.primary,
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: colors.primary,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }, {
                    label: 'Approved',
                    data: seasonalApproved,
                    borderColor: colors.success,
                    backgroundColor: 'rgba(40,167,69,0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: colors.success,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { 
                            display: false 
                        },
                        ticks: {
                            color: '#666'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: '#f0f0f0',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
    
    // Tab switching functionality
    const serviceTabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    serviceTabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            // Resize charts when tab is shown
            Object.values(chartInstances).forEach(chart => {
                if (chart) {
                    chart.resize();
                }
            });
        });
    });
    
    // Cleanup chart instances when needed
    window.addEventListener('beforeunload', function() {
        Object.values(chartInstances).forEach(chart => {
            if (chart) {
                chart.destroy();
            }
        });
    });
});
</script>
@endsection