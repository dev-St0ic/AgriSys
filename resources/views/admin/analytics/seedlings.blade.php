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
    $categoryFulfillment = $categoryFulfillment ?? [];
    $processingTimeAnalysis = $processingTimeAnalysis ?? [];

    // ADD THESE NEW DEFAULTS FOR FILTER
    $filterType = $filterType ?? 'preset';
    $datePreset = $datePreset ?? 'this_month';
    $startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
    $endDate = $endDate ?? now()->format('Y-m-d');
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
                            <p class="text-muted mb-0">Analytics Dashboard</p>
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

    <!-- Improved Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.analytics.seedlings') }}" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <!-- Filter Type Selection -->
                            <div class="col-lg-3 col-md-6">
                                <label for="filter_type" class="form-label fw-semibold">
                                    <i class="fas fa-filter me-1"></i>Filter Type
                                </label>
                                <select class="form-select" id="filter_type" name="filter_type">
                                    <option value="preset" {{ $filterType === 'preset' ? 'selected' : '' }}>Quick Preset
                                    </option>
                                    <option value="custom" {{ $filterType === 'custom' ? 'selected' : '' }}>Custom Range
                                    </option>
                                </select>
                            </div>

                            <!-- Preset Options (shown when filter_type = preset) -->
                            <div class="col-lg-3 col-md-6" id="preset_container"
                                style="display: {{ $filterType === 'preset' ? 'block' : 'none' }};">
                                <label for="date_preset" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt me-1"></i>Select Period
                                </label>
                                <select class="form-select" id="date_preset" name="date_preset">
                                    <option value="today" {{ $datePreset === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ $datePreset === 'yesterday' ? 'selected' : '' }}>Yesterday
                                    </option>
                                    <option value="last_7_days" {{ $datePreset === 'last_7_days' ? 'selected' : '' }}>Last
                                        7 Days</option>
                                    <option value="last_14_days" {{ $datePreset === 'last_14_days' ? 'selected' : '' }}>
                                        Last 14 Days</option>
                                    <option value="last_30_days" {{ $datePreset === 'last_30_days' ? 'selected' : '' }}>
                                        Last 30 Days</option>
                                    <option value="this_week" {{ $datePreset === 'this_week' ? 'selected' : '' }}>This Week
                                    </option>
                                    <option value="last_week" {{ $datePreset === 'last_week' ? 'selected' : '' }}>Last Week
                                    </option>
                                    <option value="this_month" {{ $datePreset === 'this_month' ? 'selected' : '' }}>This
                                        Month</option>
                                    <option value="last_month" {{ $datePreset === 'last_month' ? 'selected' : '' }}>Last
                                        Month</option>
                                    <option value="this_quarter" {{ $datePreset === 'this_quarter' ? 'selected' : '' }}>
                                        This Quarter</option>
                                    <option value="last_quarter" {{ $datePreset === 'last_quarter' ? 'selected' : '' }}>
                                        Last Quarter</option>
                                    <option value="this_year" {{ $datePreset === 'this_year' ? 'selected' : '' }}>This Year
                                    </option>
                                    <option value="last_year" {{ $datePreset === 'last_year' ? 'selected' : '' }}>Last Year
                                    </option>
                                    <option value="all_time" {{ $datePreset === 'all_time' ? 'selected' : '' }}>All Time
                                    </option>
                                </select>
                            </div>

                            <!-- Custom Date Range (shown when filter_type = custom) -->
                            <div class="col-lg-6 col-md-12" id="custom_container"
                                style="display: {{ $filterType === 'custom' ? 'block' : 'none' }};">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="start_date" class="form-label fw-semibold">
                                            <i class="fas fa-calendar-day me-1"></i>Start Date
                                        </label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ $startDate }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_date" class="form-label fw-semibold">
                                            <i class="fas fa-calendar-check me-1"></i>End Date
                                        </label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ $endDate }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-lg-6 col-md-12">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-search me-2"></i>Apply Filter
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="resetBtn">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Summary Display -->
                        <div class="mt-3 p-3 bg-light rounded" id="filter_summary">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Current Filter:</strong>
                                <span id="filter_display">
                                    @if ($filterType === 'preset')
                                        {{ ucwords(str_replace('_', ' ', $datePreset)) }}
                                    @else
                                        Custom Range
                                    @endif
                                </span>
                                <span class="mx-2">|</span>
                                <span id="date_range_display">{{ date('M d, Y', strtotime($startDate)) }} -
                                    {{ date('M d, Y', strtotime($endDate)) }}</span>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts & Recommendations Section -->
    @php
        $hasAlerts = false;
        $alerts = [];

        // Check for low approval rate
        if ($overview['approval_rate'] < 70) {
            $hasAlerts = true;
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa-exclamation-triangle',
                'title' => 'Low Approval Rate Alert',
                'message' => "Only {$overview['approval_rate']}% of requests are approved. Review rejection reasons and adjust supply levels.",
                'action' => 'Review pending requests',
            ];
        }

        // Check for high pending requests
        if ($overview['pending_requests'] > 10) {
            $hasAlerts = true;
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fa-clock',
                'title' => 'Pending Requests Need Attention',
                'message' => "{$overview['pending_requests']} requests are awaiting review. Process them to improve service delivery.",
                'action' => 'Process pending requests',
            ];
        }

        // Check for low fulfillment rate
        if ($overview['fulfillment_rate'] < 75) {
            $hasAlerts = true;
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fa-chart-line',
                'title' => 'Low Fulfillment Rate',
                'message' => "Only {$overview['fulfillment_rate']}% of requested quantities are being fulfilled. Check supply availability.",
                'action' => 'Review supply levels',
            ];
        }

        // Check for high demand categories
        foreach ($supplyDemandAnalysis as $category => $data) {
            if ($data['total_demand'] > 500) {
                $hasAlerts = true;
                $alerts[] = [
                    'type' => 'primary',
                    'icon' => 'fa-chart-bar',
                    'title' => 'High Demand: ' . ucfirst($category),
                    'message' =>
                        number_format($data['total_demand']) .
                        " items requested. Top item: {$data['top_demand_item']}. Consider increasing stock.",
                    'action' => 'Increase stock for ' . $category,
                ];
            }
        }

        // Check processing time
        if (
            isset($processingTimeAnalysis['avg_processing_days']) &&
            $processingTimeAnalysis['avg_processing_days'] > 5
        ) {
            $hasAlerts = true;
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa-hourglass-half',
                'title' => 'Slow Processing Time',
                'message' => "Average processing time is {$processingTimeAnalysis['avg_processing_days']} days. Target is 3 days or less.",
                'action' => 'Improve processing efficiency',
            ];
        }
    @endphp

    @if ($hasAlerts)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-start border-warning border-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-bell me-2 text-warning"></i>Alerts & Recommendations
                        </h5>
                        <small class="text-muted">Action items that require your attention</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach (array_slice($alerts, 0, 4) as $alert)
                                <div class="col-md-6 mb-3">
                                    <div class="alert alert-{{ $alert['type'] }} mb-0 d-flex align-items-start">
                                        <i class="fas {{ $alert['icon'] }} me-3 mt-1 fa-lg"></i>
                                        <div class="grow">
                                            <strong class="d-block mb-1">{{ $alert['title'] }}</strong>
                                            <p class="mb-2 small">{{ $alert['message'] }}</p>
                                            <span class="badge bg-{{ $alert['type'] }}">
                                                <i class="fas fa-arrow-right me-1"></i>{{ $alert['action'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                        <i class="fas fa-clock fa-2x text-info"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $processingTimeAnalysis['avg_processing_days'] ?? 0 }}</h2>
                    <h6 class="text-muted mb-2">Avg. Processing Days</h6>
                    <small
                        class="text-{{ ($processingTimeAnalysis['avg_processing_days'] ?? 0) <= 3 ? 'success' : 'warning' }}">
                        Target: ≤ 3 days
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
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
                        <i class="fas fa-balance-scale me-2 text-success"></i>Demand Analysis by Category
                    </h5>
                    <small class="text-muted">Total demand per category - helps prioritize procurement</small>
                </div>
                <div class="card-body">
                    <canvas id="supplyDemandChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Fulfillment Rate -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-check-double me-2 text-success"></i>Fulfillment Rate by Category
                    </h5>
                    <small class="text-muted">Shows how well each category is being fulfilled - identify problem
                        areas</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($categoryFulfillment as $category => $data)
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="p-3 border rounded">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong class="text-capitalize">{{ $category }}</strong>
                                        <span
                                            class="badge bg-{{ $data['rate'] >= 80 ? 'success' : ($data['rate'] >= 60 ? 'warning' : 'danger') }}">
                                            {{ $data['rate'] }}%
                                        </span>
                                    </div>
                                    <div class="progress mb-2" style="height: 25px;">
                                        <div class="progress-bar bg-{{ $data['rate'] >= 80 ? 'success' : ($data['rate'] >= 60 ? 'warning' : 'danger') }}"
                                            role="progressbar" style="width: {{ $data['rate'] }}%"
                                            aria-valuenow="{{ $data['rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                            <strong>{{ $data['rate'] }}%</strong>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i
                                            class="fas fa-check-circle text-success me-1"></i>{{ number_format($data['approved']) }}
                                        approved
                                        <span class="mx-2">|</span>
                                        <i
                                            class="fas fa-list text-primary me-1"></i>{{ number_format($data['requested']) }}
                                        requested
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                        <i class="fas fa-trophy me-2 text-warning"></i>Top Barangays by Volume
                    </h5>
                    <small class="text-muted">Prioritize resource allocation to high-demand areas</small>
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
                        <i class="fas fa-chart-line me-2 text-info"></i>Request Trends
                    </h5>
                    <small class="text-muted">Track request patterns over time</small>
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
                        <i class="fas fa-table me-2 text-primary"></i>Barangay Performance Matrix
                    </h5>
                    <small class="text-muted">Comprehensive metrics for prioritization and resource allocation</small>
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
                                    <th>Performance Score</th>
                                    <th>Priority Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangayPerformance->take(15) as $index => $barangay)
                                    @php
                                        $statusClass =
                                            $barangay['score'] >= 80
                                                ? 'success'
                                                : ($barangay['score'] >= 60
                                                    ? 'warning'
                                                    : 'danger');
                                        $priorityLevel =
                                            $barangay['score'] >= 80
                                                ? 'High'
                                                : ($barangay['score'] >= 60
                                                    ? 'Medium'
                                                    : 'Low');
                                        $priorityClass =
                                            $barangay['score'] >= 80
                                                ? 'success'
                                                : ($barangay['score'] >= 60
                                                    ? 'warning'
                                                    : 'danger');
                                    @endphp
                                    <tr>
                                        <td><strong class="text-primary">#{{ $index + 1 }}</strong></td>
                                        <td><strong>{{ $barangay['barangay'] }}</strong></td>
                                        <td>{{ $barangay['total_requests'] }}</td>
                                        <td><span
                                                class="badge bg-success">{{ round($barangay['total_requests'] * ($barangay['approval_rate'] / 100)) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $barangay['approval_rate'] >= 80 ? 'success' : ($barangay['approval_rate'] >= 60 ? 'warning' : 'danger') }}">
                                                {{ $barangay['approval_rate'] }}%
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress grow me-2" style="height: 20px; min-width: 100px;">
                                                    <div class="progress-bar bg-{{ $statusClass }}" role="progressbar"
                                                        style="width: {{ $barangay['score'] }}%"
                                                        aria-valuenow="{{ $barangay['score'] }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        {{ round($barangay['score'], 1) }}
                                                    </div>
                                                </div>
                                                <span
                                                    class="badge bg-{{ $statusClass }}">{{ $barangay['grade'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $priorityClass }}">
                                                <i
                                                    class="fas fa-{{ $priorityLevel === 'High' ? 'arrow-up' : ($priorityLevel === 'Medium' ? 'minus' : 'arrow-down') }} me-1"></i>
                                                {{ $priorityLevel }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Performance Legend -->
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Performance Scoring Guide</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small>
                                    <strong>Score Calculation:</strong><br>
                                    • 40% Approval Rate<br>
                                    • 30% Request Volume<br>
                                    • 20% Unique Applicants<br>
                                    • 10% Total Quantity
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small>
                                    <strong>Priority Levels:</strong><br>
                                    <span class="badge bg-success me-1">High</span> Score ≥ 80 - Focus resources here<br>
                                    <span class="badge bg-warning me-1">Medium</span> Score 60-79 - Monitor closely<br>
                                    <span class="badge bg-danger me-1">Low</span> Score < 60 - Needs intervention </small>
                            </div>
                        </div>
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

        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: scale(1.005);
        }

        .progress {
            background-color: #e9ecef;
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }

        .alert {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .alert:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced Form Styling */
        .form-select,
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        .form-label {
            color: #495057;
            margin-bottom: 0.5rem;
        }

        #filter_summary {
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
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

            // Improved Date Filter Functionality
            const filterType = document.getElementById('filter_type');
            const presetContainer = document.getElementById('preset_container');
            const customContainer = document.getElementById('custom_container');
            const datePreset = document.getElementById('date_preset');
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            const filterDisplay = document.getElementById('filter_display');
            const dateRangeDisplay = document.getElementById('date_range_display');
            const resetBtn = document.getElementById('resetBtn');

            // Toggle between preset and custom
            filterType.addEventListener('change', function() {
                if (this.value === 'preset') {
                    presetContainer.style.display = 'block';
                    customContainer.style.display = 'none';
                    updateDatesFromPreset();
                } else {
                    presetContainer.style.display = 'none';
                    customContainer.style.display = 'block';
                    updateFilterDisplay();
                }
            });

            // Update dates when preset changes
            datePreset.addEventListener('change', updateDatesFromPreset);

            // Update display when custom dates change
            startDate.addEventListener('change', updateFilterDisplay);
            endDate.addEventListener('change', updateFilterDisplay);

            function updateDatesFromPreset() {
                const today = new Date();
                let start, end = new Date();
                const preset = datePreset.value;

                switch (preset) {
                    case 'today':
                        start = new Date();
                        end = new Date();
                        break;
                    case 'yesterday':
                        start = new Date();
                        start.setDate(start.getDate() - 1);
                        end = new Date(start);
                        break;
                    case 'last_7_days':
                        start = new Date();
                        start.setDate(start.getDate() - 7);
                        break;
                    case 'last_14_days':
                        start = new Date();
                        start.setDate(start.getDate() - 14);
                        break;
                    case 'last_30_days':
                        start = new Date();
                        start.setDate(start.getDate() - 30);
                        break;
                    case 'this_week':
                        start = new Date();
                        const day = start.getDay();
                        start.setDate(start.getDate() - day);
                        break;
                    case 'last_week':
                        end = new Date();
                        end.setDate(end.getDate() - end.getDay() - 1);
                        start = new Date(end);
                        start.setDate(start.getDate() - 6);
                        break;
                    case 'this_month':
                        start = new Date(today.getFullYear(), today.getMonth(), 1);
                        break;
                    case 'last_month':
                        start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        end = new Date(today.getFullYear(), today.getMonth(), 0);
                        break;
                    case 'this_quarter':
                        const quarter = Math.floor(today.getMonth() / 3);
                        start = new Date(today.getFullYear(), quarter * 3, 1);
                        break;
                    case 'last_quarter':
                        const lastQuarter = Math.floor(today.getMonth() / 3) - 1;
                        start = new Date(today.getFullYear(), lastQuarter * 3, 1);
                        end = new Date(today.getFullYear(), (lastQuarter + 1) * 3, 0);
                        break;
                    case 'this_year':
                        start = new Date(today.getFullYear(), 0, 1);
                        break;
                    case 'last_year':
                        start = new Date(today.getFullYear() - 1, 0, 1);
                        end = new Date(today.getFullYear() - 1, 11, 31);
                        break;
                    case 'all_time':
                        start = new Date(2020, 0, 1);
                        break;
                }

                startDate.value = formatDate(start);
                endDate.value = formatDate(end);
                updateFilterDisplay();
            }

            function formatDate(date) {
                return date.toISOString().split('T')[0];
            }

            function updateFilterDisplay() {
                if (filterType.value === 'preset') {
                    const presetText = datePreset.options[datePreset.selectedIndex].text;
                    filterDisplay.textContent = presetText;
                } else {
                    filterDisplay.textContent = 'Custom Range';
                }

                if (startDate.value && endDate.value) {
                    const start = new Date(startDate.value);
                    const end = new Date(endDate.value);
                    dateRangeDisplay.textContent =
                        `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} - ${end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
                }
            }

            // Reset button functionality
            resetBtn.addEventListener('click', function() {
                filterType.value = 'preset';
                datePreset.value = 'this_month';
                presetContainer.style.display = 'block';
                customContainer.style.display = 'none';
                updateDatesFromPreset();
            });

            // Initialize on page load
            updateDatesFromPreset();

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
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 14
                            },
                            formatter: (value, ctx) => {
                                const percentage = statusTotal > 0 ? ((value / statusTotal) * 100)
                                    .toFixed(1) : '0.0';
                                return `${value}\n(${percentage}%)`;
                            }
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
                        backgroundColor: [colors.success, colors.info, colors.warning, colors
                            .danger, colors.purple, colors.orange
                        ],
                        borderRadius: 8,
                        borderWidth: 2,
                        borderColor: '#fff'
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
                            anchor: 'end',
                            align: 'top',
                            color: '#333',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: (value) => value.toLocaleString()
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => value.toLocaleString()
                            }
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
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'right',
                            color: '#333',
                            font: {
                                weight: 'bold',
                                size: 11
                            },
                            formatter: (value) => value
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
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
                return new Date(year, month - 1).toLocaleDateString('en-US', {
                    month: 'short',
                    year: '2-digit'
                });
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
                        legend: {
                            display: false
                        },
                        datalabels: {
                            align: 'top',
                            color: '#333',
                            font: {
                                weight: 'bold',
                                size: 10
                            },
                            formatter: (value) => value
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
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
                        legend: {
                            display: false
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#333',
                            font: {
                                weight: 'bold',
                                size: 11
                            },
                            formatter: (value) => value.toLocaleString()
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => value.toLocaleString()
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });
    </script>
@endsection
