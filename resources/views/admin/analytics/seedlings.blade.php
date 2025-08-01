@extends('layouts.app')

@section('title', 'Seedling Analytics - AgriSys Admin')
@section('page-title', 'Seedling Analytics Dashboard')

@section('content')
<!-- Date Range Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.analytics.seedlings') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Apply Filter
                        </button>
                        <a href="{{ route('admin.analytics.seedlings.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                           class="btn btn-success">
                            <i class="fas fa-download me-1"></i> Export
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Overview Statistics -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Requests</h6>
                        <h2 class="mb-0">{{ number_format($overview['total_requests']) }}</h2>
                    </div>
                    <i class="fas fa-seedling fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Approved</h6>
                        <h2 class="mb-0">{{ number_format($overview['approved_requests']) }}</h2>
                        <small>{{ $overview['approval_rate'] }}% approval rate</small>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Quantity</h6>
                        <h2 class="mb-0">{{ number_format($overview['total_quantity_requested']) }}</h2>
                        <small>{{ number_format($overview['avg_request_size'], 1) }} avg per request</small>
                    </div>
                    <i class="fas fa-chart-bar fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Active Barangays</h6>
                        <h2 class="mb-0">{{ $overview['active_barangays'] }}</h2>
                        <small>{{ number_format($overview['unique_applicants']) }} unique applicants</small>
                    </div>
                    <i class="fas fa-map-marker-alt fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analytics Tabs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="analyticsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="trends-tab" data-bs-toggle="tab" 
                                data-bs-target="#trends" type="button" role="tab">
                            <i class="fas fa-chart-line me-1"></i> Trends
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="status-tab" data-bs-toggle="tab" 
                                data-bs-target="#status" type="button" role="tab">
                            <i class="fas fa-pie-chart me-1"></i> Status Analysis
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="barangays-tab" data-bs-toggle="tab" 
                                data-bs-target="#barangays" type="button" role="tab">
                            <i class="fas fa-map me-1"></i> Barangays
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="categories-tab" data-bs-toggle="tab" 
                                data-bs-target="#categories" type="button" role="tab">
                            <i class="fas fa-tags me-1"></i> Categories
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="items-tab" data-bs-toggle="tab" 
                                data-bs-target="#items" type="button" role="tab">
                            <i class="fas fa-list me-1"></i> Top Items
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="performance-tab" data-bs-toggle="tab" 
                                data-bs-target="#performance" type="button" role="tab">
                            <i class="fas fa-clock me-1"></i> Performance
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="analyticsTabContent">
                    <!-- Monthly Trends Tab -->
                    <div class="tab-pane fade show active" id="trends" role="tabpanel">
                        <h5>Monthly Request Trends</h5>
                        <div class="row">
                            <div class="col-12">
                                <canvas id="trendsChart" height="100"></canvas>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>Total Requests</th>
                                                <th>Approved</th>
                                                <th>Rejected</th>
                                                <th>Pending</th>
                                                <th>Total Quantity</th>
                                                <th>Avg Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyTrends as $trend)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('M Y') }}</td>
                                                <td>{{ $trend->total_requests }}</td>
                                                <td><span class="badge bg-success">{{ $trend->approved }}</span></td>
                                                <td><span class="badge bg-danger">{{ $trend->rejected }}</span></td>
                                                <td><span class="badge bg-warning">{{ $trend->pending }}</span></td>
                                                <td>{{ number_format($trend->total_quantity) }}</td>
                                                <td>{{ number_format($trend->avg_quantity, 1) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Analysis Tab -->
                    <div class="tab-pane fade" id="status" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Request Status Distribution</h5>
                                <canvas id="statusChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h5>Status Breakdown</h5>
                                <div class="list-group">
                                    @foreach($statusAnalysis['counts'] as $status => $count)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-capitalize">
                                            <i class="fas fa-circle me-2 text-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}"></i>
                                            {{ str_replace('_', ' ', $status) }}
                                        </span>
                                        <div>
                                            <span class="badge bg-primary rounded-pill me-2">{{ $count }}</span>
                                            <small class="text-muted">{{ $statusAnalysis['percentages'][$status] }}%</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <!-- Seasonal Analysis -->
                                <h5 class="mt-4">Seasonal Analysis</h5>
                                <div class="row">
                                    @foreach($seasonalAnalysis as $season => $data)
                                    <div class="col-6">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ $season }}</h6>
                                                <p class="card-text">
                                                    <strong>{{ $data['requests'] }}</strong> requests<br>
                                                    <small class="text-muted">{{ number_format($data['quantity']) }} items</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barangay Analysis Tab -->
                    <div class="tab-pane fade" id="barangays" role="tabpanel">
                        <h5>Barangay Performance Analysis</h5>
                        <div class="row">
                            <div class="col-12">
                                <canvas id="barangayChart" height="80"></canvas>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Barangay</th>
                                                <th>Total Requests</th>
                                                <th>Approved</th>
                                                <th>Total Quantity</th>
                                                <th>Avg Quantity</th>
                                                <th>Unique Applicants</th>
                                                <th>Approval Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($barangayAnalysis as $barangay)
                                            <tr>
                                                <td><strong>{{ $barangay->barangay }}</strong></td>
                                                <td>{{ $barangay->total_requests }}</td>
                                                <td><span class="badge bg-success">{{ $barangay->approved }}</span></td>
                                                <td>{{ number_format($barangay->total_quantity) }}</td>
                                                <td>{{ number_format($barangay->avg_quantity, 1) }}</td>
                                                <td>{{ $barangay->unique_applicants }}</td>
                                                <td>{{ $barangay->total_requests > 0 ? round(($barangay->approved / $barangay->total_requests) * 100, 1) : 0 }}%</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Analysis Tab -->
                    <div class="tab-pane fade" id="categories" role="tabpanel">
                        <h5>Category Distribution Analysis</h5>
                        <div class="row">
                            @foreach($categoryAnalysis as $category => $data)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-{{ $category === 'vegetables' ? 'success' : ($category === 'fruits' ? 'warning' : 'info') }}">
                                        <h6 class="card-title text-white mb-0 text-capitalize">
                                            <i class="fas fa-{{ $category === 'vegetables' ? 'leaf' : ($category === 'fruits' ? 'apple-alt' : 'flask') }} me-2"></i>
                                            {{ $category }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h4 class="text-primary">{{ $data['requests'] }}</h4>
                                                <small class="text-muted">Requests</small>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="text-success">{{ number_format($data['total_items']) }}</h4>
                                                <small class="text-muted">Total Items</small>
                                            </div>
                                        </div>
                                        <hr>
                                        <h6>Top Items:</h6>
                                        <div class="list-group list-group-flush">
                                            @php
                                                $topCategoryItems = collect($data['unique_items'])->sortByDesc(function($quantity) { return $quantity; })->take(3);
                                            @endphp
                                            @foreach($topCategoryItems as $itemName => $quantity)
                                            <div class="list-group-item px-0 py-1 d-flex justify-content-between">
                                                <small>{{ $itemName }}</small>
                                                <span class="badge bg-secondary">{{ $quantity }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Category Comparison Chart -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <canvas id="categoryChart" height="60"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top Items Tab -->
                    <div class="tab-pane fade" id="items" role="tabpanel">
                        <h5>Most Requested Items</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="topItemsChart" height="80"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Item</th>
                                                <th>Category</th>
                                                <th>Total Qty</th>
                                                <th>Requests</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topItems->take(10) as $index => $item)
                                            <tr>
                                                <td><span class="badge bg-primary">#{{ $index + 1 }}</span></td>
                                                <td><strong>{{ $item['name'] }}</strong></td>
                                                <td>
                                                    <span class="badge bg-{{ $item['category'] === 'vegetables' ? 'success' : ($item['category'] === 'fruits' ? 'warning' : 'info') }}">
                                                        {{ substr($item['category'], 0, 4) }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format($item['total_quantity']) }}</td>
                                                <td>{{ $item['request_count'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Tab -->
                    <div class="tab-pane fade" id="performance" role="tabpanel">
                        <h5>System Performance Metrics</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-clock me-2"></i>Processing Time Analysis
                                        </h6>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <h4 class="text-primary">{{ $processingTimeAnalysis['avg_processing_days'] }}d</h4>
                                                <small class="text-muted">Average</small>
                                            </div>
                                            <div class="col-4">
                                                <h4 class="text-success">{{ $processingTimeAnalysis['min_processing_days'] }}d</h4>
                                                <small class="text-muted">Fastest</small>
                                            </div>
                                            <div class="col-4">
                                                <h4 class="text-danger">{{ $processingTimeAnalysis['max_processing_days'] }}d</h4>
                                                <small class="text-muted">Slowest</small>
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="mb-0">
                                            <small class="text-muted">
                                                Based on {{ number_format($processingTimeAnalysis['processed_count']) }} processed requests
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-warehouse me-2"></i>Inventory Impact
                                        </h6>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h4 class="text-info">{{ number_format($inventoryImpact['total_items_distributed']) }}</h4>
                                                <small class="text-muted">Items Distributed</small>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="text-success">{{ number_format($inventoryImpact['requests_fulfilled']) }}</h4>
                                                <small class="text-muted">Requests Fulfilled</small>
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="mb-0">
                                            <small class="text-muted">
                                                Average fulfillment: {{ number_format($inventoryImpact['avg_fulfillment_quantity'], 1) }} items per request
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Performance Insights -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Key Performance Insights</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <strong>{{ $overview['approval_rate'] }}%</strong> approval rate indicates 
                                                        {{ $overview['approval_rate'] > 80 ? 'excellent' : ($overview['approval_rate'] > 60 ? 'good' : 'needs improvement') }} 
                                                        request quality
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-users text-info me-2"></i>
                                                        <strong>{{ $overview['unique_applicants'] }}</strong> unique applicants across 
                                                        <strong>{{ $overview['active_barangays'] }}</strong> barangays shows good community reach
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-seedling text-success me-2"></i>
                                                        Average request size of <strong>{{ number_format($overview['avg_request_size'], 1) }}</strong> items 
                                                        indicates {{ $overview['avg_request_size'] > 50 ? 'large-scale' : 'small-scale' }} farming focus
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-clock text-warning me-2"></i>
                                                        Processing time of <strong>{{ $processingTimeAnalysis['avg_processing_days'] }} days</strong> 
                                                        is {{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'excellent' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'good' : 'needs improvement') }}
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-chart-line text-primary me-2"></i>
                                                        <strong>{{ number_format($inventoryImpact['total_items_distributed']) }}</strong> items distributed 
                                                        shows significant community impact
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-balance-scale text-info me-2"></i>
                                                        Distribution across categories: 
                                                        {{ round(($categoryAnalysis['vegetables']['requests'] / max(1, array_sum(array_column($categoryAnalysis, 'requests')))) * 100) }}% vegetables,
                                                        {{ round(($categoryAnalysis['fruits']['requests'] / max(1, array_sum(array_column($categoryAnalysis, 'requests')))) * 100) }}% fruits,
                                                        {{ round(($categoryAnalysis['fertilizers']['requests'] / max(1, array_sum(array_column($categoryAnalysis, 'requests')))) * 100) }}% fertilizers
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store chart instances to prevent memory leaks
    let chartInstances = {};
    
    // Initialize charts that should be visible on page load
    initializeTrendsChart();
    
    // Handle tab switching and initialize charts when needed
    const tabTriggers = document.querySelectorAll('#analyticsTab button[data-bs-toggle="tab"]');
    tabTriggers.forEach(trigger => {
        trigger.addEventListener('shown.bs.tab', function(event) {
            const targetTab = event.target.getAttribute('data-bs-target');
            
            // Small delay to ensure tab content is fully visible
            setTimeout(() => {
                switch(targetTab) {
                    case '#status':
                        if (!chartInstances.statusChart) {
                            initializeStatusChart();
                        }
                        break;
                    case '#barangays':
                        if (!chartInstances.barangayChart) {
                            initializeBarangayChart();
                        }
                        break;
                    case '#categories':
                        if (!chartInstances.categoryChart) {
                            initializeCategoryChart();
                        }
                        break;
                    case '#items':
                        if (!chartInstances.topItemsChart) {
                            initializeTopItemsChart();
                        }
                        break;
                }
            }, 100);
        });
    });
    
    // Chart initialization functions
    function initializeTrendsChart() {
        const trendsCtx = document.getElementById('trendsChart');
        if (!trendsCtx) return;
        
        chartInstances.trendsChart = new Chart(trendsCtx.getContext('2d'), {
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
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Approved',
                    data: [{{ $monthlyTrends->pluck('approved')->implode(',') }}],
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Total Quantity',
                    data: [{{ $monthlyTrends->pluck('total_quantity')->implode(',') }}],
                    borderColor: '#f39c12',
                    backgroundColor: 'rgba(243, 156, 18, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Requests'
                        },
                        beginAtZero: true
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Total Quantity'
                        },
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });
    }
    
    function initializeStatusChart() {
        const statusCtx = document.getElementById('statusChart');
        if (!statusCtx) return;
        
        chartInstances.statusChart = new Chart(statusCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach($statusAnalysis['counts'] as $status => $count)
                        '{{ ucfirst(str_replace("_", " ", $status)) }}',
                    @endforeach
                ],
                datasets: [{
                    data: [{{ implode(',', $statusAnalysis['counts']) }}],
                    backgroundColor: [
                        '#27ae60',  // approved - green
                        '#e74c3c',  // rejected - red
                        '#f39c12'   // under_review - orange
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    function initializeBarangayChart() {
        const barangayCtx = document.getElementById('barangayChart');
        if (!barangayCtx) return;
        
        chartInstances.barangayChart = new Chart(barangayCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [
                    @foreach($barangayAnalysis->take(10) as $barangay)
                        '{{ $barangay->barangay }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Total Requests',
                    data: [{{ $barangayAnalysis->take(10)->pluck('total_requests')->implode(',') }}],
                    backgroundColor: '#3498db',
                    borderColor: '#2980b9',
                    borderWidth: 1
                }, {
                    label: 'Approved',
                    data: [{{ $barangayAnalysis->take(10)->pluck('approved')->implode(',') }}],
                    backgroundColor: '#27ae60',
                    borderColor: '#229954',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Barangay (Top 10)'
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Requests'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });
    }
    
    function initializeCategoryChart() {
        const categoryCtx = document.getElementById('categoryChart');
        if (!categoryCtx) return;
        
        chartInstances.categoryChart = new Chart(categoryCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Vegetables', 'Fruits', 'Fertilizers'],
                datasets: [{
                    label: 'Number of Requests',
                    data: [
                        {{ $categoryAnalysis['vegetables']['requests'] }},
                        {{ $categoryAnalysis['fruits']['requests'] }},
                        {{ $categoryAnalysis['fertilizers']['requests'] }}
                    ],
                    backgroundColor: ['#27ae60', '#f39c12', '#3498db'],
                    borderColor: ['#229954', '#e67e22', '#2980b9'],
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Total Items',
                    data: [
                        {{ $categoryAnalysis['vegetables']['total_items'] }},
                        {{ $categoryAnalysis['fruits']['total_items'] }},
                        {{ $categoryAnalysis['fertilizers']['total_items'] }}
                    ],
                    backgroundColor: ['rgba(39, 174, 96, 0.5)', 'rgba(243, 156, 18, 0.5)', 'rgba(52, 152, 219, 0.5)'],
                    borderColor: ['#27ae60', '#f39c12', '#3498db'],
                    borderWidth: 1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Requests'
                        },
                        beginAtZero: true
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Total Items'
                        },
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });
    }
    
    function initializeTopItemsChart() {
        const topItemsCtx = document.getElementById('topItemsChart');
        if (!topItemsCtx) return;
        
        chartInstances.topItemsChart = new Chart(topItemsCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [
                    @foreach($topItems->take(10) as $item)
                        '{{ $item["name"] }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Total Quantity Requested',
                    data: [{{ $topItems->take(10)->pluck('total_quantity')->implode(',') }}],
                    backgroundColor: [
                        @foreach($topItems->take(10) as $item)
                            '{{ $item["category"] === "vegetables" ? "#27ae60" : ($item["category"] === "fruits" ? "#f39c12" : "#3498db") }}',
                        @endforeach
                    ],
                    borderColor: [
                        @foreach($topItems->take(10) as $item)
                            '{{ $item["category"] === "vegetables" ? "#229954" : ($item["category"] === "fruits" ? "#e67e22" : "#2980b9") }}',
                        @endforeach
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Total Quantity'
                        },
                        beginAtZero: true
                    },
                    y: {
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const item = @json($topItems->take(10)->values());
                                const itemData = item[context.dataIndex];
                                return `Category: ${itemData.category.charAt(0).toUpperCase() + itemData.category.slice(1)}`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Cleanup function to destroy charts when needed
    window.destroyCharts = function() {
        Object.values(chartInstances).forEach(chart => {
            if (chart) {
                chart.destroy();
            }
        });
        chartInstances = {};
    };
});
</script>
@endsection