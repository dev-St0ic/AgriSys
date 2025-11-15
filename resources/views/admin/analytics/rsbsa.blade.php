{{-- resources/views/admin/analytics/rsbsa.blade.php --}}

@extends('layouts.app')

@section('title', 'RSBSA Analytics - AgriSys Admin')
@section('page-title')
    <i class="fas fa-chart-bar me-2"></i>RSBSA Analytics Dashboard
@endsection

@section('content')
    <!-- Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 navigation-container">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-center flex-wrap gap-2">
                        <a href="{{ route('admin.analytics.seedlings') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.seedlings') ? 'active' : '' }}">
                            <i class="fas fa-seedling me-2"></i>Seedlings
                        </a>
                        <a href="{{ route('admin.analytics.rsbsa') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.rsbsa') ? 'active' : '' }}">
                            <i class="fas fa-user-check me-2"></i>RSBSA
                        </a>
                        <a href="{{ route('admin.analytics.fishr') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.fishr') ? 'active' : '' }}">
                            <i class="fas fa-fish me-2"></i>FISHR
                        </a>
                        <a href="{{ route('admin.analytics.boatr') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.boatr') ? 'active' : '' }}">
                            <i class="fas fa-ship me-2"></i>BOATR
                        </a>
                        <a href="{{ route('admin.analytics.training') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.training') ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap me-2"></i>Training
                        </a>
                        <a href="{{ route('admin.analytics.supply-management') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.supply-management') ? 'active' : '' }}">
                            <i class="fas fa-boxes me-2"></i>Supply Management
                        </a>
                        <a href="{{ route('admin.analytics.user-registration') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.user-registration') ? 'active' : '' }}">
                            <i class="fas fa-user-plus me-2"></i>User Registration
                        </a>
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
                    <form method="GET" action="{{ route('admin.analytics.rsbsa') }}" class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label for="start_date" class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt text-primary me-1"></i>Start Date
                            </label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label for="end_date" class="form-label fw-semibold">
                                <i class="fas fa-calendar-check text-primary me-1"></i>End Date
                            </label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-filter me-2"></i>Apply Filter
                                </button>
                                <a href="{{ route('admin.analytics.rsbsa.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                                    class="btn btn-success px-4">
                                    <i class="fas fa-download me-2"></i>Export Data
                                </a>
                            </div>
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
                        <i class="fas fa-file-alt fa-2x text-primary"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ number_format($overview['total_applications']) }}</h2>
                    <h6 class="text-muted mb-2">Total Applications</h6>
                    <small class="text-success">
                        <i class="fas fa-users me-1"></i>{{ $overview['unique_applicants'] }} farmers
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $overview['approval_rate'] }}%</h2>
                    <h6 class="text-muted mb-2">Approval Rate</h6>
                    <small class="text-muted">{{ number_format($overview['approved_applications']) }} approved</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3">
                        <i class="fas fa-map-marked-alt fa-2x text-purple"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ number_format($overview['total_land_area'], 1) }}ha</h2>
                    <h6 class="text-muted mb-2">Total Land Coverage</h6>
                    <small class="text-muted">{{ $overview['active_barangays'] }} barangays</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $processingTimeAnalysis['avg_processing_days'] }}d</h2>
                    <h6 class="text-muted mb-2">Avg. Processing Time</h6>
                    <small class="text-muted">{{ $processingTimeAnalysis['median_processing_days'] }}d median</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analytics Section -->
    <div class="row mb-4 g-3">
        <!-- Application Status Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Application Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="status-chart-container mb-3">
                        <canvas id="rsbsaStatusChart" height="220"></canvas>
                    </div>
                    <div class="status-legends">
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            @php
                                $dotColor = match ($status) {
                                    'approved' => '#10b981',
                                    'rejected' => '#ef4444',
                                    'under_review', 'pending' => '#f59e0b',
                                    'cancelled', 'withdrawn' => '#6b7280',
                                    'processing' => '#3b82f6',
                                    'on_hold' => '#8b5cf6',
                                    default => '#64748b',
                                };
                            @endphp
                            <div class="legend-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="legend-dot bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }} me-2"></span>
                                    <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </div>
                                <div>
                                    <span class="badge text-white me-2" style="background-color: {{ $dotColor }};">
                                        {{ $count }}
                                    </span>
                                    <span class="text-muted">{{ $statusAnalysis['percentages'][$status] }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line text-info me-2"></i>Application Trends
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="rsbsaTrendsChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Commodities & Performance Metrics -->
    <div class="row g-3 mb-4">
        <!-- Top Commodities -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-seedling me-2 text-success"></i>Top Commodities by Registration
                    </h5>
                </div>
                <div class="card-body">
                    @foreach ($commodityAnalysis->take(5) as $index => $commodity)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-semibold">{{ ucfirst($commodity->commodity) }}</h6>
                                <span
                                    class="badge bg-primary text-white rounded-pill px-3">{{ $commodity->total_applications }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ ($commodity->total_applications / $commodityAnalysis->first()->total_applications) * 100 }}%"
                                    role="progressbar"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Approval:
                                    {{ round(($commodity->approved / max(1, $commodity->total_applications)) * 100, 1) }}%</span>
                                <span>Avg: {{ round($commodity->total_land_area, 1) }}ha ×
                                    {{ $commodity->unique_barangays }} barangays</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div> <!-- Main Livelihood Distribution -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-briefcase me-2 text-success"></i>Main Livelihood Distribution
                    </h5>
                </div>
                <div class="card-body">
                    @foreach ($livelihoodAnalysis->take(5) as $index => $livelihood)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-semibold">{{ ucfirst($livelihood->main_livelihood) }}</h6>
                                <span
                                    class="badge bg-primary text-white rounded-pill px-3">{{ $livelihood->total_applications }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ ($livelihood->total_applications / $livelihoodAnalysis->first()->total_applications) * 100 }}%"
                                    role="progressbar"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Approval:
                                    {{ round(($livelihood->approved / max(1, $livelihood->total_applications)) * 100, 1) }}%</span>
                                <span>Share:
                                    {{ round(($livelihood->total_applications / $livelihoodAnalysis->sum('total_applications')) * 100, 1) }}%
                                    of total</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Barangays -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-trophy me-2 text-warning"></i>Top Performing Barangays
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Rank</th>
                                    <th class="fw-semibold">Barangay</th>
                                    <th class="fw-semibold text-center">Applications</th>
                                    <th class="fw-semibold text-center">Approved</th>
                                    <th class="fw-semibold">Approval Rate</th>
                                    <th class="fw-semibold text-center">Land Area</th>
                                    <th class="fw-semibold text-center">Commodities</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangayAnalysis->take(10) as $index => $barangay)
                                    <tr>
                                        <td>
                                            <div
                                                class="badge {{ $index < 3 ? ($index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : 'bg-info')) : 'bg-light text-dark' }} rounded-pill px-3">
                                                #{{ $index + 1 }}
                                            </div>
                                        </td>
                                        <td class="fw-semibold">{{ $barangay->barangay }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $barangay->total_applications }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $barangay->approved }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress grow me-2" style="height: 8px; max-width: 100px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%">
                                                    </div>
                                                </div>
                                                <small
                                                    class="fw-semibold">{{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="text-success fw-semibold">{{ round($barangay->total_land_area, 1) }}
                                                ha</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $barangay->commodities_grown }}</span>
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

    <div class="modal fade" id="rsbsaInsightsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
            @endsection

            @section('styles')
                <style>
                    /* Modern Analytics Navigation */
                    .analytics-nav-btn {
                        background: #f8f9fa;
                        border: 1px solid #e9ecef;
                        color: #6c757d;
                        font-weight: 500;
                        font-size: 0.875rem;
                        padding: 0.5rem 1rem;
                        border-radius: 2rem;
                        text-decoration: none;
                        transition: all 0.2s ease;
                        white-space: nowrap;
                    }

                    .analytics-nav-btn:hover {
                        background: #e9ecef;
                        border-color: #dee2e6;
                        color: #495057;
                        text-decoration: none;
                    }

                    .analytics-nav-btn.active {
                        background: #0d6efd;
                        border-color: #0d6efd;
                        color: white;
                        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
                    }

                    .analytics-nav-btn.active:hover {
                        background: #0b5ed7;
                        border-color: #0a58ca;
                        color: white;
                    }

                    .analytics-nav-btn i {
                        font-size: 0.875rem;
                    }

                    /* Responsive adjustments */
                    @media (max-width: 768px) {
                        .analytics-nav-btn {
                            font-size: 0.75rem;
                            padding: 0.375rem 0.75rem;
                        }

                        .analytics-nav-btn i {
                            font-size: 0.75rem;
                        }
                    }

                    /* Custom Color Variables */
                    :root {
                        --primary-color: #3b82f6;
                        --success-color: #10b981;
                        --warning-color: #f59e0b;
                        --danger-color: #ef4444;
                        --info-color: #0ea5e9;
                        --purple-color: #8b5cf6;
                        --dark-color: #1f2937;
                    }



                    .service-nav .nav-link:hover {
                        color: var(--primary-color);
                        background: white;
                        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
                    }

                    .service-nav .nav-link.active {
                        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                        color: white;
                        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
                    }

                    /* Metric Cards */
                    .metric-card {
                        transition: all 0.3s ease;
                        border-radius: 12px;
                        overflow: hidden;
                    }

                    .metric-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
                    }

                    .metric-label {
                        font-size: 0.875rem;
                        font-weight: 500;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }

                    .metric-value {
                        font-size: 2rem;
                        font-weight: 700;
                        color: var(--dark-color);
                    }

                    .metric-icon {
                        width: 56px;
                        height: 56px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 12px;
                        font-size: 1.5rem;
                    }

                    /* Soft Background Colors */
                    .bg-primary-soft {
                        background-color: rgba(59, 130, 246, 0.1);
                    }

                    .bg-success-soft {
                        background-color: rgba(16, 185, 129, 0.1);
                    }

                    .bg-warning-soft {
                        background-color: rgba(245, 158, 11, 0.1);
                    }

                    .bg-danger-soft {
                        background-color: rgba(239, 68, 68, 0.1);
                    }

                    .bg-info-soft {
                        background-color: rgba(14, 165, 233, 0.1);
                    }

                    .bg-purple-soft {
                        background-color: rgba(139, 92, 246, 0.1);
                    }

                    .text-purple {
                        color: var(--purple-color);
                    }

                    /* Badge Soft Colors */
                    .badge-success-soft {
                        background-color: rgba(16, 185, 129, 0.1);
                        color: var(--success-color);
                    }

                    /* Card Styles */
                    .card {
                        border-radius: 12px;
                        transition: all 0.3s ease;
                    }

                    .card-header {
                        border-radius: 12px 12px 0 0 !important;
                        padding: 1.25rem;
                    }

                    /* Gradient Backgrounds */
                    .bg-gradient-primary {
                        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                    }

                    /* Status Legend */
                    .status-legends .legend-item {
                        transition: all 0.2s ease;
                        background: #f8fafc;
                    }

                    .status-legends .legend-item:hover {
                        background: #f1f5f9;
                        transform: translateX(5px);
                    }

                    .legend-dot {
                        width: 12px;
                        height: 12px;
                        border-radius: 50%;
                        display: inline-block;
                    }

                    /* Progress Bars */
                    .progress {
                        border-radius: 10px;
                        background-color: #f1f5f9;
                    }

                    .progress-bar {
                        border-radius: 10px;
                        transition: width 0.6s ease;
                    }

                    /* Metric Items */
                    .metric-item {
                        padding: 1rem;
                        border-radius: 10px;
                        background: #f8fafc;
                        transition: all 0.3s ease;
                    }

                    .metric-item:hover {
                        background: #f1f5f9;
                        transform: scale(1.02);
                    }

                    /* Table Styles */
                    .table-hover tbody tr {
                        transition: all 0.2s ease;
                    }

                    .table-hover tbody tr:hover {
                        background-color: #f8fafc;
                        transform: scale(1.01);
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                    }

                    /* Chart Containers */
                    .status-chart-container {
                        position: relative;
                        height: 220px;
                    }

                    /* Responsive Adjustments */
                    @media (max-width: 768px) {
                        .service-nav {
                            flex-wrap: wrap;
                            border-radius: 12px;
                            padding: 0.25rem;
                        }

                        .service-nav .nav-link {
                            font-size: 0.875rem;
                            padding: 0.5rem 0.75rem;
                            margin: 0.25rem;
                        }

                        .metric-value {
                            font-size: 1.5rem;
                        }

                        .metric-icon {
                            width: 48px;
                            height: 48px;
                            font-size: 1.25rem;
                        }
                    }

                    /* Animation */
                    @keyframes fadeInUp {
                        from {
                            opacity: 0;
                            transform: translateY(20px);
                        }

                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }

                    .card {
                        animation: fadeInUp 0.5s ease;
                    }

                    /* Commodity Items */
                    .commodity-item {
                        background: linear-gradient(90deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%);
                        border-left: 4px solid #10b981;
                        transition: all 0.2s ease;
                    }

                    .commodity-item:hover {
                        background: linear-gradient(90deg, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0.04) 100%);
                        transform: translateX(5px);
                    }

                    /* Livelihood Items */
                    .livelihood-item {
                        background: linear-gradient(90deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%);
                        border-left: 4px solid #10b981;
                        transition: all 0.2s ease;
                    }

                    .livelihood-item:hover {
                        background: linear-gradient(90deg, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0.04) 100%);
                        transform: translateX(5px);
                    }

                    /* Gender Cards */
                    .gender-card {
                        transition: all 0.3s ease;
                        border: 2px solid transparent;
                    }

                    .gender-card:hover {
                        border-color: rgba(59, 130, 246, 0.3);
                        transform: translateY(-5px);
                        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
                    }

                    .text-pink {
                        color: #ec4899 !important;
                    }

                    /* Scrollbar Styling */
                    .table-responsive::-webkit-scrollbar {
                        height: 8px;
                    }

                    .table-responsive::-webkit-scrollbar-track {
                        background: #f1f5f9;
                        border-radius: 10px;
                    }

                    .table-responsive::-webkit-scrollbar-thumb {
                        background: #cbd5e1;
                        border-radius: 10px;
                    }

                    .table-responsive::-webkit-scrollbar-thumb:hover {
                        background: #94a3b8;
                    }

                    /* Focus States */
                    .btn:focus,
                    .form-control:focus {
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                    }

                    /* Loading State */
                    .card.loading {
                        opacity: 0.6;
                        pointer-events: none;
                    }

                    /* Insight Cards */
                    .insight-card {
                        border: 1px solid rgba(0, 0, 0, 0.05);
                    }

                    .insight-card ul li {
                        line-height: 1.6;
                    }

                    /* Modal Styles */
                    .modal-content {
                        border-radius: 16px;
                    }

                    .modal-header {
                        border-radius: 16px 16px 0 0;
                    }
                </style>
            @endsection

            @section('scripts')
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Chart instances
                        let chartInstances = {};

                        // Chart.js default configuration
                        Chart.defaults.font.family = "'Inter', sans-serif";
                        Chart.defaults.color = '#64748b';

                        // Initialize Status Chart
                        initializeStatusChart();

                        // Initialize Trends Chart
                        initializeTrendsChart();

                        /**
                         * Status Distribution Doughnut Chart
                         */
                        function initializeStatusChart() {
                            const ctx = document.getElementById('rsbsaStatusChart');
                            if (!ctx) return;

                            const statusData = [{{ implode(',', $statusAnalysis['counts']) }}];
                            const statusLabels = [
                                @foreach ($statusAnalysis['counts'] as $status => $count)
                                    '{{ ucfirst(str_replace('_', ' ', $status)) }}',
                                @endforeach
                            ];

                            // Define status colors based on status type
                            const statusColors = [];
                            const statusNames = [
                                @foreach ($statusAnalysis['counts'] as $status => $count)
                                    '{{ $status }}',
                                @endforeach
                            ];

                            statusNames.forEach(status => {
                                switch (status) {
                                    case 'approved':
                                        statusColors.push('#10b981'); // Green
                                        break;
                                    case 'rejected':
                                        statusColors.push('#ef4444'); // Red
                                        break;
                                    case 'under_review':
                                    case 'pending':
                                        statusColors.push('#f59e0b'); // Amber
                                        break;
                                    case 'cancelled':
                                    case 'withdrawn':
                                        statusColors.push('#6b7280'); // Gray
                                        break;
                                    case 'processing':
                                        statusColors.push('#3b82f6'); // Blue
                                        break;
                                    case 'on_hold':
                                        statusColors.push('#8b5cf6'); // Purple
                                        break;
                                    default:
                                        statusColors.push('#64748b'); // Default gray
                                }
                            });

                            chartInstances.statusChart = new Chart(ctx.getContext('2d'), {
                                type: 'doughnut',
                                data: {
                                    labels: statusLabels,
                                    datasets: [{
                                        data: statusData,
                                        backgroundColor: statusColors,
                                        borderWidth: 3,
                                        borderColor: '#ffffff',
                                        cutout: '65%',
                                        spacing: 4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                            padding: 12,
                                            cornerRadius: 8,
                                            titleFont: {
                                                size: 14,
                                                weight: 'bold'
                                            },
                                            bodyFont: {
                                                size: 13
                                            },
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.label || '';
                                                    const value = context.parsed;
                                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                    const percentage = ((value / total) * 100).toFixed(1);
                                                    return `${label}: ${value} (${percentage}%)`;
                                                }
                                            }
                                        },
                                        // Custom plugin to display percentages inside the doughnut
                                        datalabels: false
                                    },
                                    animation: {
                                        animateRotate: true,
                                        duration: 1000
                                    }
                                },
                                plugins: [{
                                    id: 'centerText',
                                    beforeDraw: function(chart) {
                                        const ctx = chart.ctx;
                                        const chartArea = chart.chartArea;
                                        const centerX = (chartArea.left + chartArea.right) / 2;
                                        const centerY = (chartArea.top + chartArea.bottom) / 2;

                                        // Get the total
                                        const total = chart.data.datasets[0].data.reduce((a, b) => a + b,
                                            0);

                                        // Draw center text
                                        ctx.save();
                                        ctx.font = 'bold 24px Inter';
                                        ctx.fillStyle = '#1f2937';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        ctx.fillText(total.toLocaleString(), centerX, centerY - 10);

                                        ctx.font = '14px Inter';
                                        ctx.fillStyle = '#64748b';
                                        ctx.fillText('Total Applications', centerX, centerY + 15);
                                        ctx.restore();
                                    },
                                    afterDraw: function(chart) {
                                        const ctx = chart.ctx;
                                        const meta = chart.getDatasetMeta(0);
                                        const total = chart.data.datasets[0].data.reduce((a, b) => a + b,
                                            0);

                                        ctx.save();
                                        ctx.font = 'bold 14px Inter, sans-serif';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';

                                        chart.data.datasets[0].data.forEach((value, index) => {
                                            if (value > 0) {
                                                const percentage = ((value / total) * 100).toFixed(
                                                    1);

                                                // Only show percentage if slice is large enough
                                                if (percentage > 5) {
                                                    const element = meta.data[index];

                                                    // Calculate the middle angle of the segment
                                                    const startAngle = element.startAngle;
                                                    const endAngle = element.endAngle;
                                                    const midAngle = (startAngle + endAngle) / 2;

                                                    // Calculate position based on the segment's center point
                                                    const chartArea = chart.chartArea;
                                                    const centerX = (chartArea.left + chartArea
                                                        .right) / 2;
                                                    const centerY = (chartArea.top + chartArea
                                                        .bottom) / 2;

                                                    // Position the text at 70% of the radius from center
                                                    const radius = (element.outerRadius - element
                                                            .innerRadius) * 0.7 + element
                                                        .innerRadius;
                                                    const x = centerX + Math.cos(midAngle) * radius;
                                                    const y = centerY + Math.sin(midAngle) * radius;

                                                    const text = `${percentage}%`;

                                                    ctx.fillStyle = '#ffffff';
                                                    ctx.strokeStyle = '#000000';
                                                    ctx.lineWidth = 3;
                                                    ctx.textAlign = 'center';
                                                    ctx.textBaseline = 'middle';
                                                    ctx.strokeText(text, x, y);
                                                    ctx.fillText(text, x, y);
                                                }
                                            }
                                        });

                                        ctx.restore();
                                    }
                                }]
                            });
                        }

                        /**
                         * Monthly Trends Line Chart
                         */
                        function initializeTrendsChart() {
                            const ctx = document.getElementById('rsbsaTrendsChart');
                            if (!ctx) return;

                            chartInstances.trendsChart = new Chart(ctx.getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels: [
                                        @foreach ($monthlyTrends as $trend)
                                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('M Y') }}',
                                        @endforeach
                                    ],
                                    datasets: [{
                                            label: 'Total Applications',
                                            data: [
                                                {{ $monthlyTrends->pluck('total_applications')->implode(',') }}
                                            ],
                                            borderColor: '#3b82f6',
                                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                            borderWidth: 3,
                                            tension: 0.4,
                                            fill: true,
                                            pointBackgroundColor: '#3b82f6',
                                            pointBorderColor: '#ffffff',
                                            pointBorderWidth: 2,
                                            pointRadius: 5,
                                            pointHoverRadius: 7,
                                            pointHoverBorderWidth: 3
                                        },
                                        {
                                            label: 'Approved',
                                            data: [{{ $monthlyTrends->pluck('approved')->implode(',') }}],
                                            borderColor: '#10b981',
                                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                            borderWidth: 3,
                                            tension: 0.4,
                                            fill: true,
                                            pointBackgroundColor: '#10b981',
                                            pointBorderColor: '#ffffff',
                                            pointBorderWidth: 2,
                                            pointRadius: 5,
                                            pointHoverRadius: 7,
                                            pointHoverBorderWidth: 3
                                        },
                                        {
                                            label: 'Land Area (ha)',
                                            data: [{{ $monthlyTrends->pluck('total_land_area')->implode(',') }}],
                                            borderColor: '#8b5cf6',
                                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                            borderWidth: 2,
                                            tension: 0.4,
                                            fill: false,
                                            pointBackgroundColor: '#8b5cf6',
                                            pointBorderColor: '#ffffff',
                                            pointBorderWidth: 2,
                                            pointRadius: 4,
                                            pointHoverRadius: 6,
                                            yAxisID: 'y1'
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                display: false,
                                                drawBorder: false
                                            },
                                            ticks: {
                                                font: {
                                                    size: 12,
                                                    weight: '500'
                                                },
                                                color: '#64748b'
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: {
                                                color: 'rgba(0, 0, 0, 0.05)',
                                                drawBorder: false
                                            },
                                            ticks: {
                                                font: {
                                                    size: 12,
                                                    weight: '500'
                                                },
                                                color: '#64748b',
                                                padding: 10
                                            },
                                            title: {
                                                display: true,
                                                text: 'Applications',
                                                font: {
                                                    weight: '600',
                                                    size: 12
                                                }
                                            }
                                        },
                                        y1: {
                                            type: 'linear',
                                            display: true,
                                            position: 'right',
                                            beginAtZero: true,
                                            grid: {
                                                drawOnChartArea: false,
                                            },
                                            ticks: {
                                                font: {
                                                    size: 12,
                                                    weight: '500'
                                                },
                                                color: '#64748b'
                                            },
                                            title: {
                                                display: true,
                                                text: 'Land Area (ha)',
                                                font: {
                                                    weight: '600',
                                                    size: 12
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                            align: 'end',
                                            labels: {
                                                usePointStyle: true,
                                                padding: 20,
                                                font: {
                                                    size: 13,
                                                    weight: '500'
                                                },
                                                color: '#64748b'
                                            }
                                        },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false,
                                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                            titleColor: 'white',
                                            bodyColor: 'white',
                                            borderColor: 'rgba(255, 255, 255, 0.1)',
                                            borderWidth: 1,
                                            cornerRadius: 8,
                                            padding: 12,
                                            displayColors: true,
                                            titleFont: {
                                                size: 14,
                                                weight: 'bold'
                                            },
                                            bodyFont: {
                                                size: 13
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        /**
                         * Cleanup function
                         */
                        window.destroyCharts = function() {
                            Object.values(chartInstances).forEach(chart => {
                                if (chart) {
                                    chart.destroy();
                                }
                            });
                            chartInstances = {};
                        };

                        /**
                         * Add smooth animations on scroll
                         */
                        const observerOptions = {
                            threshold: 0.1,
                            rootMargin: '0px 0px -50px 0px'
                        };

                        const observer = new IntersectionObserver(function(entries) {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    entry.target.style.opacity = '1';
                                    entry.target.style.transform = 'translateY(0)';
                                }
                            });
                        }, observerOptions);

                        document.querySelectorAll('.card').forEach(card => {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            observer.observe(card);
                        });

                        /**
                         * Add loading state to form submission
                         */
                        const filterForm = document.querySelector('form[action*="analytics.rsbsa"]');
                        if (filterForm) {
                            filterForm.addEventListener('submit', function() {
                                const submitBtn = this.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                                    submitBtn.disabled = true;
                                }
                            });
                        }

                        /**
                         * Add animation to metric cards
                         */
                        const metricCards = document.querySelectorAll('.metric-card');
                        metricCards.forEach((card, index) => {
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 100);
                        });
                    });
                </script>
            @endsection

            @section('styles')
                <style>
                    /* Navigation Container */
                    .navigation-container {
                        background: #f8f9fa;
                        border-radius: 15px;
                        border: 1px solid #dee2e6;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
                    }

                    /* Modern Analytics Navigation */
                    .analytics-nav-btn {
                        background: #e9ecef;
                        border: 1px solid #ced4da;
                        color: #495057;
                        font-weight: 500;
                        font-size: 0.875rem;
                        padding: 0.6rem 1.2rem;
                        border-radius: 2rem;
                        text-decoration: none;
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        white-space: nowrap;
                        position: relative;
                        overflow: hidden;
                        transform: translateY(0);
                    }

                    .analytics-nav-btn::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: -100%;
                        width: 100%;
                        height: 100%;
                        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
                        transition: left 0.5s;
                    }

                    .analytics-nav-btn:hover {
                        background: #6c757d;
                        border-color: #5a6268;
                        color: white;
                        text-decoration: none;
                        transform: translateY(-3px);
                        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
                    }

                    .analytics-nav-btn:hover::before {
                        left: 100%;
                    }

                    .analytics-nav-btn:hover i {
                        transform: scale(1.15) rotate(5deg);
                    }

                    .analytics-nav-btn.active {
                        background: linear-gradient(135deg, #495057 0%, #343a40 100%);
                        border-color: #495057;
                        color: white;
                        box-shadow: 0 4px 20px rgba(73, 80, 87, 0.4);
                        transform: translateY(-1px);
                    }

                    .analytics-nav-btn.active:hover {
                        background: linear-gradient(135deg, #343a40 0%, #212529 100%);
                        border-color: #343a40;
                        color: white;
                        transform: translateY(-4px);
                        box-shadow: 0 8px 30px rgba(73, 80, 87, 0.6);
                    }

                    .analytics-nav-btn i {
                        font-size: 0.875rem;
                        transition: transform 0.3s ease;
                    }

                    /* Responsive adjustments */
                    @media (max-width: 768px) {
                        .analytics-nav-btn {
                            font-size: 0.75rem;
                            padding: 0.375rem 0.75rem;
                        }

                        .analytics-nav-btn i {
                            font-size: 0.75rem;
                        }
                    }

                    .metric-card {
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                        border-radius: 10px;
                        cursor: pointer;
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    .metric-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                    }

                    .card {
                        border-radius: 10px;
                        transition: all 0.3s ease;
                    }

                    .card:hover {
                        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
                    }
                </style>
            @endsection
