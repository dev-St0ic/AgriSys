{{-- resources/views/admin/analytics/seedlings.blade.php --}}

@php
    $overview = $overview ?? [];
    $topItems = $topItems ?? [];
    $barangayAnalysis = $barangayAnalysis ?? collect();
    $categoryAnalysis = $categoryAnalysis ?? [];
    $monthlyTrends = $monthlyTrends ?? [];
    $statusAnalysis = $statusAnalysis ?? ['counts' => []];
    $supplyDemandAnalysis = $supplyDemandAnalysis ?? [];
    $barangayPerformance = $barangayPerformance ?? collect();
@endphp

@extends('layouts.app')

@section('title', 'Analytics - AgriSys Admin')
@section('page-title', 'Seedling Analytics Dashboard')

@section('content')
    <!-- Header with Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div>
                            <h4 class="mb-2 text-dark">Seedling Analytics</h4>
                            <p class="text-muted mb-0">Decision Support Dashboard</p>
                        </div>
                        <div class="d-flex justify-content-center">
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
                                    <a href="{{ route('admin.analytics.boatr') }}"
                                        class="nav-link {{ request()->routeIs('admin.analytics.boatr') ? 'active' : '' }}">
                                        <i class="fas fa-ship me-1"></i> BOATR
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="{{ route('admin.analytics.training') }}"
                                        class="nav-link {{ request()->routeIs('admin.analytics.training') ? 'active' : '' }}">
                                        <i class="fas fa-graduation-cap me-1"></i> Training
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="{{ route('admin.analytics.supply-management') }}"
                                        class="nav-link {{ request()->routeIs('admin.analytics.supply-management') ? 'active' : '' }}">
                                        <i class="fas fa-boxes me-1"></i> Supply Management
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="{{ route('admin.analytics.user-registration') }}"
                                        class="nav-link {{ request()->routeIs('admin.analytics.user-registration') ? 'active' : '' }}">
                                        <i class="fas fa-user-edit"></i> User Registration
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <a href="{{ route('admin.analytics.seedlings.dss-report') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                                class="btn btn-info me-2" target="_blank">
                                <i class="fas fa-file-download me-1"></i> DSS Report
                            </a>
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
                    <small class="text-{{ $overview['change_percentage'] >= 0 ? 'success' : 'danger' }}">
                        <i class="fas fa-arrow-{{ $overview['change_percentage'] >= 0 ? 'up' : 'down' }} me-1"></i>
                        {{ abs($overview['change_percentage']) }}% from last period
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
                    <h2 class="text-dark mb-1">{{ number_format($overview['total_quantity_approved']) }}</h2>
                    <h6 class="text-muted mb-2">Items Distributed</h6>
                    <small class="text-muted">{{ $overview['fulfillment_rate'] }}% fulfillment rate</small>
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

    <!-- Decision Support Charts -->
    <div class="row mb-4">
        <!-- Request Status Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-pie-chart me-2 text-primary"></i>Request Status
                    </h5>
                    <small class="text-muted">Current distribution of request statuses</small>
                </div>
                <div class="card-body d-flex flex-column">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Supply vs Demand -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-balance-scale me-2 text-success"></i>Supply Demand Analysis
                    </h5>
                    <small class="text-muted">Total demand per category - helps prioritize procurement</small>
                </div>
                <div class="card-body">
                    <canvas id="supplyDemandChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & Trends -->
    <div class="row mb-4">
        <!-- Barangay Performance -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-trophy me-2 text-warning"></i>Barangay Performance
                    </h5>
                    <small class="text-muted">Top 10 barangays by request volume</small>
                </div>
                <div class="card-body">
                    <canvas id="barangayChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-chart-line me-2 text-info"></i>Monthly Request Trends
                    </h5>
                    <small class="text-muted">Request patterns over time</small>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Items Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-star me-2 text-danger"></i>Most Requested Items
                    </h5>
                    <small class="text-muted">Top 10 items by demand - prioritize stock availability</small>
                </div>
                <div class="card-body">
                    <canvas id="topItemsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-table me-2 text-primary"></i>Barangay Performance Details
                    </h5>
                    <small class="text-muted">Comprehensive performance metrics for decision making</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>Barangay</th>
                                    <th>Total Requests</th>
                                    <th>Approved</th>
                                    <th>Approval Rate</th>
                                    <th>Total Quantity</th>
                                    <th>Performance Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangayPerformance->take(15) as $index => $barangay)
                                    @php
                                        $statusClass = $barangay['approval_rate'] >= 80 ? 'success' : ($barangay['approval_rate'] >= 60 ? 'warning' : 'danger');
                                        $statusText = $barangay['approval_rate'] >= 80 ? 'Excellent' : ($barangay['approval_rate'] >= 60 ? 'Good' : 'Needs Attention');
                                    @endphp
                                    <tr>
                                        <td><strong>#{{ $index + 1 }}</strong></td>
                                        <td><strong>{{ $barangay['barangay'] }}</strong></td>
                                        <td>{{ $barangay['total_requests'] }}</td>
                                        <td><span class="badge bg-success">{{ $barangay['total_requests'] * ($barangay['approval_rate']/100) }}</span></td>
                                        <td>{{ $barangay['approval_rate'] }}%</td>
                                        <td>{{ number_format($barangay['total_requests'] * 50) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $statusClass }}" role="progressbar" 
                                                         style="width: {{ $barangay['score'] }}%" 
                                                         aria-valuenow="{{ $barangay['score'] }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ round($barangay['score'], 1) }}
                                                    </div>
                                                </div>
                                                <span class="badge bg-{{ $statusClass }}">{{ $barangay['grade'] }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .metric-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
            cursor: pointer;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .metric-card:active {
            transform: scale(0.98);
        }

        .card {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .nav-pills .nav-link {
            border-radius: 25px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link.active {
            background-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        canvas {
            cursor: pointer;
        }

        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: scale(1.01);
        }

        .progress {
            background-color: #e9ecef;
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js global configuration
            Chart.defaults.font.family = "'Segoe UI', 'Roboto', sans-serif";
            Chart.defaults.plugins.legend.display = true;
            Chart.defaults.plugins.tooltip.enabled = true;

            // Color palette
            const colors = {
                primary: '#007bff',
                success: '#28a745',
                danger: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8',
                purple: '#6f42c1',
                orange: '#fd7e14'
            };

            // Status Chart
            const statusCtx = document.getElementById('statusChart');
            const statusData = @json($statusAnalysis['counts']);
            const statusLabels = Object.keys(statusData).map(key => key.replace('_', ' ').toUpperCase());
            const statusValues = Object.values(statusData);
            const statusTotal = statusValues.reduce((a, b) => a + b, 0);

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: [colors.success, colors.danger, colors.warning],
                        borderWidth: 3,
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
                                padding: 15,
                                font: { size: 12, weight: 'bold' }
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold', size: 14 },
                            formatter: (value, ctx) => {
                                const percentage = statusTotal > 0 ? ((value / statusTotal) * 100).toFixed(1) : '0.0';
                                return `${value}\n(${percentage}%)`;
                            }
                        }
                    },
                    onClick: (e, activeEls) => {
                        if (activeEls.length > 0) {
                            const chart = activeEls[0].element.$context.chart;
                            chart.update('active');
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Supply Demand Chart
            const supplyDemandCtx = document.getElementById('supplyDemandChart');
            const supplyDemandData = @json($supplyDemandAnalysis);
            const categories = Object.keys(supplyDemandData);
            const demands = categories.map(cat => supplyDemandData[cat].total_demand);

            new Chart(supplyDemandCtx, {
                type: 'bar',
                data: {
                    labels: categories.map(c => c.toUpperCase()),
                    datasets: [{
                        label: 'Total Demand',
                        data: demands,
                        backgroundColor: [colors.success, colors.info, colors.warning, colors.danger, colors.purple, colors.orange],
                        borderRadius: 8,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#333',
                            font: { weight: 'bold', size: 12 },
                            formatter: (value) => value.toLocaleString()
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: (value) => value.toLocaleString() }
                        }
                    },
                    onClick: (e, activeEls) => {
                        if (activeEls.length > 0) {
                            const chart = activeEls[0].element.$context.chart;
                            chart.update('active');
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Barangay Performance Chart
            const barangayCtx = document.getElementById('barangayChart');
            const barangayData = @json($barangayAnalysis->take(10)->toArray());
            const barangayLabels = barangayData.map(b => b.barangay);
            const barangayValues = barangayData.map(b => b.total_requests);

            new Chart(barangayCtx, {
                type: 'bar',
                data: {
                    labels: barangayLabels,
                    datasets: [{
                        label: 'Total Requests',
                        data: barangayValues,
                        backgroundColor: colors.primary,
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            anchor: 'end',
                            align: 'right',
                            color: '#333',
                            font: { weight: 'bold', size: 11 },
                            formatter: (value) => value
                        }
                    },
                    scales: {
                        x: { beginAtZero: true }
                    },
                    onClick: (e, activeEls) => {
                        if (activeEls.length > 0) {
                            const chart = activeEls[0].element.$context.chart;
                            chart.update('active');
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Monthly Trends Chart
            const trendsCtx = document.getElementById('trendsChart');
            const monthlyData = @json($monthlyTrends);
            const trendLabels = monthlyData.map(m => {
                const [year, month] = m.month.split('-');
                return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
            });
            const trendValues = monthlyData.map(m => m.total_requests);

            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Total Requests',
                        data: trendValues,
                        borderColor: colors.info,
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            align: 'top',
                            color: '#333',
                            font: { weight: 'bold', size: 10 },
                            formatter: (value) => value
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    },
                    onClick: (e, activeEls) => {
                        if (activeEls.length > 0) {
                            const chart = activeEls[0].element.$context.chart;
                            chart.update('active');
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Top Items Chart
            const topItemsCtx = document.getElementById('topItemsChart');
            const topItemsData = @json($topItems->take(10)->toArray());
            const itemLabels = topItemsData.map(item => item.name);
            const itemValues = topItemsData.map(item => item.total_quantity);

            new Chart(topItemsCtx, {
                type: 'bar',
                data: {
                    labels: itemLabels,
                    datasets: [{
                        label: 'Total Quantity',
                        data: itemValues,
                        backgroundColor: colors.danger,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#333',
                            font: { weight: 'bold', size: 11 },
                            formatter: (value) => value.toLocaleString()
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: { callback: (value) => value.toLocaleString() }
                        }
                    },
                    onClick: (e, activeEls) => {
                        if (activeEls.length > 0) {
                            const chart = activeEls[0].element.$context.chart;
                            chart.update('active');
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Add click animation to metric cards
            document.querySelectorAll('.metric-card').forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 200);
                });
            });
        });
    </script>
@endsection