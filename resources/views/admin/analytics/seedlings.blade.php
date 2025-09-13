{{-- resources/views/admin/analytics/seedlings.blade.php --}}

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
                            <p class="text-muted mb-0">Comprehensive insights into agricultural services</p>
                        </div>
                        <!-- Service Tabs - Unified Structure -->
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
                                    <a href="{{ route('admin.analytics.inventory') }}"
                                        class="nav-link {{ request()->routeIs('admin.analytics.inventory') ? 'active' : '' }}">
                                        <i class="fas fa-boxes me-1"></i> Inventory
                                    </a>
                                </li>
                            </ul>
                        </div>
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
                                    <!-- new DSS Report button -->
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
                            <small class="text-muted">{{ number_format($overview['avg_request_size'], 1) }} avg per
                                request</small>
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
                            <small class="text-muted">{{ number_format($overview['unique_applicants']) }} farmers
                                served</small>
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
                            <h5 class="text-dark mb-2">Most Requested</h5>
                            <h4 class="text-primary mb-2">{{ $topItems->first()['name'] ?? 'N/A' }}</h4>
                            <p class="text-muted small mb-0">
                                {{ number_format($topItems->first()['total_quantity'] ?? 0) }} units requested
                                <br>in {{ $topItems->first()['request_count'] ?? 0 }} requests
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
                            <h5 class="text-dark mb-2">Highest Request Barangay</h5>
                            <h4 class="text-success mb-2">{{ $barangayAnalysis->first()->barangay ?? 'N/A' }}</h4>
                            <p class="text-muted small mb-0">
                                {{ $barangayAnalysis->first()->total_requests ?? 0 }} total requests
                                <br>{{ round((($barangayAnalysis->first()->approved ?? 0) / max(1, $barangayAnalysis->first()->total_requests ?? 1)) * 100, 1) }}%
                                approval rate
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Least Requested Item -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm border-0 h-100 insight-card">
                        <div class="card-body text-center p-4">
                            <div class="insight-icon mb-3">
                                <i class="fas fa-arrow-down fa-2x text-info"></i>
                            </div>
                            <h5 class="text-dark mb-2">Least Requested</h5>
                            <h4 class="text-info mb-2">{{ $leastRequestedItems->first()['name'] ?? 'N/A' }}</h4>
                            <p class="text-muted small mb-0">
                                {{ number_format($leastRequestedItems->first()['total_quantity'] ?? 0) }} units requested
                                <br>in {{ $leastRequestedItems->first()['request_count'] ?? 0 }} requests
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Low Performer Barangay -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card shadow-sm border-0 h-100 insight-card">
                        <div class="card-body text-center p-4">
                            <div class="insight-icon mb-3">
                                <i class="fas fa-chart-line-down fa-2x text-warning"></i>
                            </div>
                            <h5 class="text-dark mb-2">Lowest Request Barangay</h5>
                            <h4 class="text-warning mb-2">{{ $barangayAnalysis->last()->barangay ?? 'N/A' }}</h4>
                            <p class="text-muted small mb-0">
                                {{ $barangayAnalysis->last()->total_requests ?? 0 }} total requests
                                <br>{{ round((($barangayAnalysis->last()->approved ?? 0) / max(1, $barangayAnalysis->last()->total_requests ?? 1)) * 100, 1) }}%
                                approval rate
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
                                <canvas id="statusPieChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trends -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-chart-bar me-2 text-success"></i>2024 vs 2025 Request Comparison
                            </h5>
                            <small class="text-muted">Monthly comparison showing year-over-year trends</small>
                        </div>
                        <div class="card-body">
                            <canvas id="trendsChart" height="180"></canvas>
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
                            <canvas id="barangayChart" height="220"></canvas>
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
                            <canvas id="categoryChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 3 -->
            <div class="row mb-4">
                <!-- Most Requested Items -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-star me-2 text-danger"></i>Most Requested Items
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="topItemsChart" height="280"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Least Requested Items -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-arrow-down me-2 text-info"></i>Least Requested Items
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="leastItemsChart" height="280"></canvas>
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
                            <small class="text-muted">
                                Monthly distribution showing agricultural planting seasons:
                                Peak activity typically in <strong>Dry Season (Nov-Apr)</strong> and <strong>Wet Season
                                    (May-Oct)</strong>
                            </small>
                        </div>
                        <div class="card-body">
                            <canvas id="seasonalChart" height="150"></canvas>
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
                                        @foreach ($barangayAnalysis->take(10) as $barangay)
                                            @php
                                                $approvalRate =
                                                    $barangay->total_requests > 0
                                                        ? round(
                                                            ($barangay->approved / $barangay->total_requests) * 100,
                                                            1,
                                                        )
                                                        : 0;
                                                $performanceClass =
                                                    $approvalRate >= 80
                                                        ? 'success'
                                                        : ($approvalRate >= 60
                                                            ? 'warning'
                                                            : 'danger');
                                                $performanceText =
                                                    $approvalRate >= 80
                                                        ? 'Excellent'
                                                        : ($approvalRate >= 60
                                                            ? 'Good'
                                                            : 'Needs Improvement');
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $barangay->barangay }}</strong></td>
                                                <td>{{ $barangay->total_requests }}</td>
                                                <td><span class="badge bg-success">{{ $barangay->approved }}</span></td>
                                                <td>{{ $approvalRate }}%</td>
                                                <td>{{ number_format($barangay->total_quantity) }}</td>
                                                <td>{{ number_format($barangay->avg_quantity, 1) }}</td>
                                                <td>{{ $barangay->unique_applicants }}</td>
                                                <td><span
                                                        class="badge bg-{{ $performanceClass }}">{{ $performanceText }}</span>
                                                </td>
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
                                    Consider expanding to
                                    {{ $barangayAnalysis->count() > 5 ? 'underserved' : 'additional' }} barangays
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
        .metric-card,
        .insight-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
        }

        .metric-card:hover,
        .insight-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .metric-icon,
        .insight-icon {
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
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .nav-pills .nav-link:not(.active):hover {
            background-color: rgba(0, 123, 255, 0.1);
            border-color: rgba(0, 123, 255, 0.3);
        }

        .card-header {
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        /* Chart container styling */
        .chart-container {
            position: relative;
            height: 400px;
        }

        /* Enhanced card styling for charts */
        .card-body canvas {
            border-radius: 8px;
        }

        .card {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: none;
            min-height: 280px;
            /* Ensure minimum height for larger charts */
        }

        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        /* Chart specific styling */
        .chart-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }

        .chart-header {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .chart-header h5 {
            color: white !important;
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

        /* Enhanced tooltips */
        .custom-tooltip {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 6px;
            font-size: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js">
    </script>
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

            // Enhanced color palette with gradients
            const colors = {
                primary: '#007bff',
                primaryGradient: ['#007bff', '#0056b3'],
                success: '#28a745',
                successGradient: ['#28a745', '#1e7e34'],
                warning: '#ffc107',
                warningGradient: ['#ffc107', '#e0a800'],
                danger: '#dc3545',
                dangerGradient: ['#dc3545', '#c82333'],
                info: '#17a2b8',
                infoGradient: ['#17a2b8', '#138496'],
                secondary: '#6c757d',
                secondaryGradient: ['#6c757d', '#545b62'],
                purple: '#6f42c1',
                purpleGradient: ['#6f42c1', '#59359a'],
                orange: '#fd7e14',
                orangeGradient: ['#fd7e14', '#e8650e'],
                teal: '#20c997',
                tealGradient: ['#20c997', '#1aa179']
            };

            // Enhanced Chart.js defaults
            Chart.defaults.color = '#495057';
            Chart.defaults.font.family = "'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
            Chart.defaults.font.size = 12;
            Chart.defaults.plugins.legend.display = true;
            Chart.defaults.plugins.tooltip.enabled = true;
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
            Chart.defaults.plugins.tooltip.titleColor = '#fff';
            Chart.defaults.plugins.tooltip.bodyColor = '#fff';
            Chart.defaults.plugins.tooltip.borderColor = 'rgba(255, 255, 255, 0.1)';
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.plugins.tooltip.displayColors = true;
            Chart.defaults.plugins.tooltip.padding = 12;

            // Animation configuration
            const animationConfig = {
                duration: 1500,
                easing: 'easeOutQuart'
            };

            // Initialize all charts for seedlings
            initializeStatusPieChart();
            initializeTrendsChart();
            initializeBarangayChart();
            initializeCategoryChart();
            initializeTopItemsChart();
            initializeLeastItemsChart();
            initializeProcessingTimeChart();
            initializeSeasonalChart();

            function initializeStatusPieChart() {
                const ctx = document.getElementById('statusPieChart');
                if (!ctx) return;

                const data = [{{ implode(',', $statusAnalysis['counts']) }}];
                const labels = [
                    @foreach ($statusAnalysis['counts'] as $status => $count)
                        '{{ ucfirst(str_replace('_', ' ', $status)) }}',
                    @endforeach
                ];

                // Create gradient backgrounds
                const gradients = labels.map((label, index) => {
                    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    if (index === 0) {
                        gradient.addColorStop(0, colors.success);
                        gradient.addColorStop(1, colors.successGradient[1]);
                    } else if (index === 1) {
                        gradient.addColorStop(0, colors.danger);
                        gradient.addColorStop(1, colors.dangerGradient[1]);
                    } else {
                        gradient.addColorStop(0, colors.warning);
                        gradient.addColorStop(1, colors.warningGradient[1]);
                    }
                    return gradient;
                });

                chartInstances.statusPieChart = new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: gradients,
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverBorderWidth: 5,
                            hoverBorderColor: '#fff',
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        animation: {
                            ...animationConfig,
                            animateRotate: true,
                            animateScale: true
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 25,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        size: 13,
                                        weight: '500'
                                    },
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                const dataset = data.datasets[0];
                                                const value = dataset.data[i];
                                                const total = dataset.data.reduce((a, b) => a +
                                                    b, 0);
                                                const percentage = total > 0 ? ((value /
                                                    total) * 100).toFixed(1) : '0.0';
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
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.raw / total) * 100)
                                            .toFixed(1) : '0.0';
                                        return `${context.label}: ${context.raw} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'point'
                        }
                    }
                });
            }

            function initializeTrendsChart() {
                const ctx = document.getElementById('trendsChart');
                if (!ctx) return;

                // Process monthly trends data to separate 2024 and 2025
                const monthlyData = @json($monthlyTrends);
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                // Initialize arrays for 2024 and 2025 data
                const data2024 = new Array(12).fill(0);
                const data2025 = new Array(12).fill(0);

                // Process the data by year and month
                monthlyData.forEach(item => {
                    const [year, month] = item.month.split('-');
                    const monthIndex = parseInt(month) - 1; // Convert to 0-based index

                    if (monthIndex >= 0 && monthIndex < 12) {
                        if (year === '2024') {
                            data2024[monthIndex] = item.total_requests || 0;
                        } else if (year === '2025') {
                            data2025[monthIndex] = item.total_requests || 0;
                        }
                    }
                });

                // Create gradients
                const gradient2024 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
                gradient2024.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
                gradient2024.addColorStop(1, 'rgba(54, 162, 235, 0.2)');

                const gradient2025 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
                gradient2025.addColorStop(0, 'rgba(75, 192, 192, 0.8)');
                gradient2025.addColorStop(1, 'rgba(75, 192, 192, 0.2)');

                chartInstances.trendsChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [{
                            label: '2024 Requests',
                            data: data2024,
                            backgroundColor: gradient2024,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 40
                        }, {
                            label: '2025 Requests',
                            data: data2025,
                            backgroundColor: gradient2025,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            ...animationConfig,
                            delay: (context) => {
                                return context.type === 'data' && context.mode === 'default' ?
                                    (context.datasetIndex * 500) + (context.dataIndex * 100) : 0;
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    color: '#6c757d',
                                    font: {
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        return `Month: ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.parsed.y} requests`;
                                    },
                                    afterBody: function(context) {
                                        // Show comparison if both years have data
                                        const monthIndex = context[0].dataIndex;
                                        const val2024 = data2024[monthIndex];
                                        const val2025 = data2025[monthIndex];

                                        if (val2024 > 0 && val2025 > 0) {
                                            const change = val2025 - val2024;
                                            const changePercent = ((change / val2024) * 100).toFixed(1);
                                            return [``,
                                                `Change: ${change > 0 ? '+' : ''}${change} (${changePercent}%)`
                                            ];
                                        }
                                        return [];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6c757d',
                                    font: {
                                        weight: '500'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(108, 117, 125, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#6c757d',
                                    font: {
                                        weight: '500'
                                    },
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : '';
                                    }
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
                    @foreach ($barangayAnalysis->take(10) as $barangay)
                        {{ $barangay->total_requests }},
                    @endforeach
                ];

                const barangayLabels = [
                    @foreach ($barangayAnalysis->take(10) as $barangay)
                        '{{ $barangay->barangay }}',
                    @endforeach
                ];

                // Create different colors for each bar
                const barColors = barangayData.map((value, index) => {
                    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
                    const colorKeys = ['primary', 'success', 'info', 'warning', 'purple', 'teal', 'orange',
                        'secondary'
                    ];
                    const colorKey = colorKeys[index % colorKeys.length];
                    gradient.addColorStop(0, colors[colorKey]);
                    gradient.addColorStop(1, colors[colorKey + 'Gradient'][1]);
                    return gradient;
                });

                chartInstances.barangayChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: barangayLabels,
                        datasets: [{
                            label: 'Total Requests',
                            data: barangayData,
                            backgroundColor: barColors,
                            borderColor: 'rgba(255, 255, 255, 0.8)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            hoverBackgroundColor: barColors.map(color => color),
                            hoverBorderColor: '#fff',
                            hoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            ...animationConfig,
                            delay: (context) => {
                                return context.type === 'data' && context.mode === 'default' ? context
                                    .dataIndex * 200 : 0;
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        return `Barangay: ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        return `Total Requests: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6c757d',
                                    maxRotation: 45,
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(108, 117, 125, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#6c757d',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : '';
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: true,
                            mode: 'index'
                        }
                    }
                });
            }

            function initializeCategoryChart() {
                const ctx = document.getElementById('categoryChart');
                if (!ctx) return;

                const categoryData = [
                    @foreach ($categoryAnalysis as $category => $data)
                        {{ $data['requests'] }},
                    @endforeach
                ];

                const categoryLabels = [
                    @foreach ($categoryAnalysis as $category => $data)
                        '{{ ucfirst($category) }}',
                    @endforeach
                ];

                // Create gradients for each category
                const categoryGradients = categoryLabels.map((label, index) => {
                    const gradient = ctx.getContext('2d').createRadialGradient(150, 150, 20, 150, 150, 100);
                    const colorKeys = ['success', 'warning', 'info', 'danger', 'purple', 'teal', 'orange'];
                    const colorKey = colorKeys[index % colorKeys.length];
                    gradient.addColorStop(0, colors[colorKey]);
                    gradient.addColorStop(1, colors[colorKey + 'Gradient'][1]);
                    return gradient;
                });

                chartInstances.categoryChart = new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryData,
                            backgroundColor: categoryGradients,
                            borderWidth: 4,
                            borderColor: '#fff',
                            hoverBorderWidth: 6,
                            hoverBorderColor: '#fff',
                            hoverOffset: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        animation: {
                            ...animationConfig,
                            animateRotate: true,
                            animateScale: true
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 25,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        size: 13,
                                        weight: '500'
                                    },
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                const dataset = data.datasets[0];
                                                const value = dataset.data[i];
                                                const total = dataset.data.reduce((a, b) => a +
                                                    b, 0);
                                                const percentage = total > 0 ? ((value /
                                                    total) * 100).toFixed(1) : '0.0';
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
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.raw / total) * 100)
                                            .toFixed(1) : '0.0';
                                        return `${context.label}: ${context.raw} requests (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'point'
                        }
                    }
                });
            }

            function initializeTopItemsChart() {
                const ctx = document.getElementById('topItemsChart');
                if (!ctx) return;

                const itemData = [
                    @foreach ($topItems->take(10) as $item)
                        {{ $item['total_quantity'] }},
                    @endforeach
                ];

                const itemLabels = [
                    @foreach ($topItems->take(10) as $item)
                        '{{ $item['name'] }}',
                    @endforeach
                ];

                // Create gradient colors for each bar (vertical gradient)
                const barColors = itemData.map((value, index) => {
                    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    const intensity = Math.max(0.3, (value / Math.max(...itemData)) * 0.8);
                    gradient.addColorStop(0, `rgba(40, 167, 69, ${intensity})`);
                    gradient.addColorStop(1, `rgba(40, 167, 69, ${intensity * 0.6})`);
                    return gradient;
                });

                chartInstances.topItemsChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: itemLabels,
                        datasets: [{
                            label: 'Total Quantity Requested',
                            data: itemData,
                            backgroundColor: barColors,
                            borderColor: colors.success,
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                            hoverBackgroundColor: barColors.map(color => color),
                            hoverBorderColor: colors.success,
                            hoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        elements: {
                            bar: {
                                borderRadius: 6,
                                borderSkipped: false,
                                barPercentage: 0.8,
                                categoryPercentage: 0.9
                            }
                        },
                        animation: {
                            ...animationConfig,
                            delay: (context) => {
                                return context.type === 'data' && context.mode === 'default' ? context
                                    .dataIndex * 150 : 0;
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        return `Item: ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        return `Total Quantity: ${context.raw} units`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6c757d',
                                    maxRotation: 45,
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(108, 117, 125, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#6c757d',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : '';
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: true,
                            mode: 'index'
                        }
                    }
                });
            }

            function initializeLeastItemsChart() {
                const ctx = document.getElementById('leastItemsChart');
                if (!ctx) return;

                const itemData = [
                    @foreach ($leastRequestedItems->take(10) as $item)
                        {{ $item['total_quantity'] }},
                    @endforeach
                ];

                const itemLabels = [
                    @foreach ($leastRequestedItems->take(10) as $item)
                        '{{ $item['name'] }}',
                    @endforeach
                ];

                // Create gradient colors for each bar (using info/blue theme for least requested - vertical gradient)
                const barColors = itemData.map((value, index) => {
                    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    const intensity = Math.max(0.3, (value / Math.max(...itemData)) * 0.8);
                    gradient.addColorStop(0, `rgba(23, 162, 184, ${intensity})`);
                    gradient.addColorStop(1, `rgba(23, 162, 184, ${intensity * 0.6})`);
                    return gradient;
                });

                chartInstances.leastItemsChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: itemLabels,
                        datasets: [{
                            label: 'Total Quantity Requested',
                            data: itemData,
                            backgroundColor: barColors,
                            borderColor: colors.info,
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                            hoverBackgroundColor: barColors.map(color => color),
                            hoverBorderColor: colors.info,
                            hoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        elements: {
                            bar: {
                                borderRadius: 6,
                                borderSkipped: false,
                                barPercentage: 0.8,
                                categoryPercentage: 0.9
                            }
                        },
                        animation: {
                            ...animationConfig,
                            delay: (context) => {
                                return context.type === 'data' && context.mode === 'default' ? context
                                    .dataIndex * 150 : 0;
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        return `Item: ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        return `Total Quantity: ${context.raw} units`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#6c757d',
                                    maxRotation: 45,
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(108, 117, 125, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: '#6c757d',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    callback: function(value) {
                                        return Number.isInteger(value) ? value : '';
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: true,
                            mode: 'index'
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
                                                const total = dataset.data.reduce((a, b) => a +
                                                    b, 0);
                                                const percentage = total > 0 ? ((value /
                                                    total) * 100).toFixed(1) : '0.0';
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

                // Real seasonal data from the controller
                const seasonalMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ];

                // Get actual monthly data from the database
                const monthlyData = @json($monthlyTrends);

                // Initialize arrays for all 12 months
                const seasonalRequests = new Array(12).fill(0);
                const seasonalApproved = new Array(12).fill(0);

                // Populate with actual data
                monthlyData.forEach(item => {
                    const monthIndex = parseInt(item.month.split('-')[1]) - 1; // Convert to 0-based index
                    if (monthIndex >= 0 && monthIndex < 12) {
                        seasonalRequests[monthIndex] = item.total_requests || 0;
                        seasonalApproved[monthIndex] = item.approved || 0;
                    }
                });

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
                button.addEventListener('shown.bs.tab', function(e) {
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
