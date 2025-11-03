{{-- resources/views/admin/analytics/supply-management.blade.php --}}

@extends('layouts.app')

@section('title', 'Supply Management Analytics - AgriSys Admin')
@section('page-title', 'Supply Management Analytics Dashboard')

@section('content')
    <!-- Header with Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <!-- Title and Description -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-2">Supply Management Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into supply levels, supply trends, and fulfillment
                            metrics</p>
                    </div>

                    <!-- Service Navigation Tabs -->
                    <div class="d-flex justify-content-center">
                        <ul class="nav nav-pills service-nav" id="serviceTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.seedlings') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.seedlings') ? 'active' : '' }}">
                                    <i class="fas fa-seedling me-2"></i>Seedlings
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.rsbsa') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.rsbsa') ? 'active' : '' }}">
                                    <i class="fas fa-user-check me-2"></i>RSBSA
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.fishr') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.fishr') ? 'active' : '' }}">
                                    <i class="fas fa-fish me-2"></i>FISHR
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.boatr') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.boatr') ? 'active' : '' }}">
                                    <i class="fas fa-ship me-2"></i>BOATR
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.training') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.training') ? 'active' : '' }}">
                                    <i class="fas fa-graduation-cap me-2"></i>Training
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.supply-management') }}" class="nav-link active">
                                    <i class="fas fa-boxes me-2"></i>Supply Management
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.user-registration') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.user-registration') ? 'active' : '' }}">
                                    <i class="fas fa-user-plus me-2"></i>User Registration
                                </a>
                            </li>
                        </ul>
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
                    <form method="GET" action="{{ route('admin.analytics.supply-management') }}"
                        class="row g-3 align-items-end">
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
                                <a href="{{ route('admin.analytics.supply-management.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
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

    <!-- Key Metrics Cards -->
    <div class="row mb-4 g-3">
        <!-- Total Items -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Total Items</p>
                            <h2 class="metric-value mb-1">{{ number_format($overview['total_items']) }}</h2>
                            <span class="badge badge-success-soft">
                                <i class="fas fa-check-circle me-1"></i>{{ $overview['active_items'] }} active
                            </span>
                        </div>
                        <div class="metric-icon bg-primary-soft">
                            <i class="fas fa-boxes text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Supply -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Total Supply</p>
                            <h2 class="metric-value mb-1">{{ number_format($overview['total_supply']) }}</h2>
                            <small class="text-muted">Avg {{ number_format($overview['avg_supply_per_item'], 1) }} per
                                item</small>
                        </div>
                        <div class="metric-icon bg-info-soft">
                            <i class="fas fa-layer-group text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supply Health Score -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Supply Health Score</p>
                            <h2 class="metric-value mb-1">{{ $overview['supply_health_score'] }}%</h2>
                            <small class="text-muted">{{ $overview['healthy_stock_items'] }} healthy items</small>
                        </div>
                        <div
                            class="metric-icon bg-{{ $overview['supply_health_score'] >= 80 ? 'success' : ($overview['supply_health_score'] >= 60 ? 'warning' : 'danger') }}-soft">
                            <i
                                class="fas fa-heartbeat text-{{ $overview['supply_health_score'] >= 80 ? 'success' : ($overview['supply_health_score'] >= 60 ? 'warning' : 'danger') }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supply Alerts -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Supply Alerts</p>
                            <h2 class="metric-value mb-1">{{ $supplyAlerts['total_alerts'] }}</h2>
                            <small class="text-muted">{{ $overview['out_of_stock_items'] }} out of stock</small>
                        </div>
                        <div class="metric-icon bg-danger-soft">
                            <i class="fas fa-bell text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analytics Section -->
    <div class="row mb-4 g-3">
        <!-- Supply Status Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Supply Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="status-chart-container mb-3">
                        <canvas id="supplyStatusChart" height="220"></canvas>
                    </div>
                    <div class="status-legends">
                        @foreach ($supplyLevelAnalysis['counts'] as $status => $count)
                            @php
                                $dotColor = match ($status) {
                                    'critical' => '#ef4444',
                                    'low' => '#f59e0b',
                                    'adequate' => '#6b7280',
                                    'optimal' => '#10b981',
                                    'overstocked' => '#06b6d4',
                                    default => '#64748b',
                                };
                            @endphp
                            <div class="legend-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                <div class="d-flex align-items-center">
                                    <span class="fw-medium">{{ ucfirst($status) }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge text-white me-2" style="background-color: {{ $dotColor }};">
                                        {{ $count }}
                                    </span>
                                    <span
                                        class="text-muted fw-semibold">{{ $count > 0 ? round(($count / array_sum($supplyLevelAnalysis['counts'])) * 100, 1) : 0 }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Supply Trends -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line text-info me-2"></i>Supply Trends Over Time
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="supplyTrendsChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics & Fulfillment Analysis -->
    <div class="row mb-4 g-3">
        <!-- Transaction Analysis -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-exchange-alt text-primary me-2"></i>Transaction Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="transaction-chart-container mb-3">
                        <canvas id="transactionChart" height="180"></canvas>
                    </div>
                    <div class="transaction-legends">
                        @foreach ($transactionAnalysis['transactions'] as $transaction)
                            <div class="transaction-item mb-3 p-3 rounded bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</span>
                                    </div>
                                    <span class="badge bg-primary-soft text-primary">
                                        {{ $transactionAnalysis['percentages'][$transaction->transaction_type] ?? 0 }}%
                                    </span>
                                </div>
                                <div class="progress mb-1" style="height: 6px;">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ ($transaction->total_quantity / ($transactionAnalysis['transactions']->max('total_quantity') ?: 1)) * 100 }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($transaction->total_quantity) }} total |
                                    {{ $transaction->unique_items }} items</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Fulfillment Performance -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-check-double text-success me-2"></i>Fulfillment Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-6">
                            <div class="p-3 rounded bg-success-soft">
                                <h3 class="text-success mb-1">{{ $fulfillmentAnalysis['fulfillment_rate'] }}%</h3>
                                <p class="mb-0 small text-muted">Overall Rate</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded bg-primary-soft">
                                <h3 class="text-primary mb-1">{{ $fulfillmentAnalysis['full_fulfillment_rate'] }}%</h3>
                                <p class="mb-0 small text-muted">Full Fulfillment</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fulfillment Breakdown -->
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="text-center p-2 rounded bg-success-soft">
                                <div class="text-success fw-bold fs-4">{{ $fulfillmentAnalysis['fully_fulfilled'] }}</div>
                                <small class="text-muted">Fully Fulfilled</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded bg-warning-soft">
                                <div class="text-warning fw-bold fs-4">{{ $fulfillmentAnalysis['partially_fulfilled'] }}
                                </div>
                                <small class="text-muted">Partially</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded bg-danger-soft">
                                <div class="text-danger fw-bold fs-4">{{ $fulfillmentAnalysis['not_fulfilled'] }}</div>
                                <small class="text-muted">Not Fulfilled</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info-soft border-0">
                        <div class="d-flex">
                            <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                            <small class="text-muted">
                                <strong>{{ number_format($fulfillmentAnalysis['total_approved']) }}</strong> of
                                <strong>{{ number_format($fulfillmentAnalysis['total_requested']) }}</strong> requested
                                items fulfilled.
                            </small>
                        </div>
                    </div>

                    <!-- Recently Fulfilled Items -->
                    @if ($recentlyFulfilledItems->count() > 0)
                        <div class="mt-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-clock me-1"></i>Recently Fulfilled
                            </h6>
                            <div class="recently-fulfilled-list">
                                @foreach ($recentlyFulfilledItems->take(6) as $item)
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-2 py-2 px-2 rounded bg-light">
                                        <div class="flex-fill">
                                            <div class="d-flex align-items-center mb-1">
                                                <small
                                                    class="text-dark fw-bold me-2">{{ Str::limit($item['item_name'], 18) }}</small>
                                                <span
                                                    class="badge badge-{{ $item['is_fully_fulfilled'] ? 'success' : 'warning' }}-soft"
                                                    style="font-size: 0.65rem;">
                                                    {{ $item['fulfillment_percentage'] }}%
                                                </span>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                {{ $item['approved_quantity'] }}/{{ $item['requested_quantity'] }} •
                                                {{ Str::limit($item['barangay'], 12) }}
                                            </small>
                                        </div>
                                        <small class="text-muted ms-2" style="font-size: 0.65rem;">
                                            {{ \Carbon\Carbon::parse($item['reviewed_at'])->format('M d') }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-3 text-center">
                            <div class="py-4">
                                <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No recent fulfillments</p>
                                <small class="text-secondary">Items will appear here when requests are fulfilled</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Items Analysis -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-star text-warning me-2"></i>Most Requested Items
                    </h5>
                </div>
                <div class="card-body">
                    @foreach ($topItemsAnalysis['most_requested']->take(5) as $index => $item)
                        <div class="item-card mb-3 p-3 rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-warning me-2">{{ $index + 1 }}</span>
                                    <span class="fw-semibold">{{ $item->name }}</span>
                                </div>
                                <span class="badge bg-success-soft text-success">
                                    {{ round(($item->total_approved / max(1, $item->total_requested)) * 100, 1) }}%
                                </span>
                            </div>
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar bg-warning"
                                    style="width: {{ ($item->total_requested / ($topItemsAnalysis['most_requested']->first()->total_requested ?: 1)) * 100 }}%">
                                </div>
                            </div>
                            <small class="text-muted">{{ number_format($item->total_requested) }} {{ $item->unit }}
                                requested</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-sitemap me-2 text-primary"></i>Category Performance Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Category</th>
                                    <th class="fw-semibold text-center">Total Items</th>
                                    <th class="fw-semibold text-center">Active Items</th>
                                    <th class="fw-semibold text-center">Total Supply</th>
                                    <th class="fw-semibold text-center">Low Supply</th>
                                    <th class="fw-semibold text-center">Out of Stock</th>
                                    <th class="fw-semibold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categoryPerformance as $category)
                                    <tr>
                                        <td class="fw-semibold">{{ $category['name'] }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $category['total_items'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $category['active_items'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="text-primary fw-semibold">{{ number_format($category['total_supply']) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">{{ $category['low_supply_count'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $category['out_of_stock_count'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $category['is_active'] ? 'success' : 'secondary' }}">
                                                {{ $category['is_active'] ? 'Active' : 'Inactive' }}
                                            </span>
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

    <!-- Top Items Analysis -->
    <div class="row g-3 mb-4">
        <!-- Most Requested Items -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-star me-2 text-warning"></i>Most Requested Items
                    </h5>
                </div>
                <div class="card-body">
                    @forelse ($topItemsAnalysis['most_requested']->take(5) as $index => $item)
                        <div class="item-card mb-3 p-3 rounded">
                            <div class="d-flex align-items-center">
                                <div class="item-rank me-3">
                                    <div class="badge bg-warning text-dark rounded-circle p-2"
                                        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <strong>{{ $index + 1 }}</strong>
                                    </div>
                                </div>
                                <div class="grow">
                                    <h6 class="mb-1 fw-semibold">{{ $item->name }}</h6>
                                    <div class="small text-muted">
                                        <i class="fas fa-box me-1"></i>{{ number_format($item->total_requested) }}
                                        {{ $item->unit }}
                                        <span class="ms-2">
                                            <i class="fas fa-file-alt me-1"></i>{{ $item->request_count }} requests
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No request data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Most Supplied Items -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-truck-loading me-2 text-success"></i>Most Supplied Items
                    </h5>
                </div>
                <div class="card-body">
                    @forelse ($topItemsAnalysis['most_supplied']->take(5) as $index => $item)
                        <div class="item-card mb-3 p-3 rounded">
                            <div class="d-flex align-items-center">
                                <div class="item-rank me-3">
                                    <div class="badge bg-success rounded-circle p-2"
                                        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <strong>{{ $index + 1 }}</strong>
                                    </div>
                                </div>
                                <div class="grow">
                                    <h6 class="mb-1 fw-semibold">{{ $item->name }}</h6>
                                    <div class="small text-muted">
                                        <i class="fas fa-box me-1"></i>{{ number_format($item->total_supplied) }}
                                        {{ $item->unit }}
                                        <span class="ms-2">
                                            <i class="fas fa-exchange-alt me-1"></i>{{ $item->supply_transactions }}
                                            transactions
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No supply data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Highest Loss Items -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-exclamation-circle me-2 text-danger"></i>Highest Loss Items
                    </h5>
                </div>
                <div class="card-body">
                    @forelse ($topItemsAnalysis['highest_loss']->take(5) as $index => $item)
                        <div class="item-card mb-3 p-3 rounded">
                            <div class="d-flex align-items-center">
                                <div class="item-rank me-3">
                                    <div class="badge bg-danger rounded-circle p-2"
                                        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <strong>{{ $index + 1 }}</strong>
                                    </div>
                                </div>
                                <div class="grow">
                                    <h6 class="mb-1 fw-semibold">{{ $item->name }}</h6>
                                    <div class="small text-muted">
                                        <i class="fas fa-box me-1"></i>{{ number_format($item->total_loss) }}
                                        {{ $item->unit }}
                                        <span class="ms-2">
                                            <i class="fas fa-times-circle me-1"></i>{{ $item->loss_incidents }} incidents
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No loss data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Restock Recommendations -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-clipboard-list me-2 text-warning"></i>Restock Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Item</th>
                                    <th class="fw-semibold">Category</th>
                                    <th class="fw-semibold text-center">Current Supply</th>
                                    <th class="fw-semibold text-center">Reorder Point</th>
                                    <th class="fw-semibold text-center">Recommended Qty</th>
                                    <th class="fw-semibold text-center">Priority</th>
                                    <th class="fw-semibold text-center">Urgency</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($restockRecommendations->take(10) as $recommendation)
                                    <tr>
                                        <td class="fw-semibold">{{ $recommendation['item']->name }}</td>
                                        <td>{{ $recommendation['item']->category->display_name ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $recommendation['status'] === 'critical' ? 'danger' : 'warning' }}">
                                                {{ $recommendation['item']->current_supply }}
                                                {{ $recommendation['item']->unit }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted">{{ $recommendation['item']->reorder_point }}
                                                {{ $recommendation['item']->unit }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-primary">{{ number_format($recommendation['recommended_quantity']) }}
                                                {{ $recommendation['item']->unit }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 8px; width: 60px; margin: auto;">
                                                <div class="progress-bar bg-{{ $recommendation['urgency'] === 'critical' ? 'danger' : ($recommendation['urgency'] === 'high' ? 'warning' : 'info') }}"
                                                    style="width: {{ $recommendation['priority'] }}%"></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $recommendation['urgency'] === 'critical' ? 'danger' : ($recommendation['urgency'] === 'high' ? 'warning' : ($recommendation['urgency'] === 'medium' ? 'info' : 'secondary')) }}">
                                                {{ ucfirst($recommendation['urgency']) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No restock recommendations at
                                            this time</td>
                                    </tr>
                                @endforelse
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

        /* Legend dots */
        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Icon circles */
        .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* Service Navigation */
        .service-nav {
            background: #f8fafc;
            padding: 0.5rem;
            border-radius: 50px;
            display: inline-flex;
        }

        .service-nav .nav-link {
            border-radius: 30px;
            padding: 0.5rem 1.25rem;
            margin: 0 0.25rem;
            font-weight: 500;
            color: #64748b;
            transition: all 0.3s ease;
            border: none;
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

        .badge-primary-soft {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary-color);
        }

        .badge-warning-soft {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .badge-danger-soft {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .badge-secondary-soft {
            background-color: rgba(107, 114, 128, 0.1);
            color: #6b7280;
        }

        .badge-purple-soft {
            background-color: rgba(139, 92, 246, 0.1);
            color: var(--purple-color);
        }

        .text-secondary {
            color: #6b7280;
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

        /* Status Legend */
        .status-legends .legend-item {
            transition: all 0.2s ease;
            background: #f8fafc;
        }

        .status-legends .legend-item:hover {
            background: #f1f5f9;
            transform: translateX(5px);
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

        /* Transaction Items */
        .transaction-item {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .transaction-item:hover {
            border-left-color: var(--primary-color);
            transform: translateX(5px);
            background: #f1f5f9 !important;
        }

        /* Item Cards */
        .item-card {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .item-card:hover {
            border-left-color: var(--warning-color);
            transform: translateX(5px);
            background: #f1f5f9 !important;
        }

        /* Recently Fulfilled Items */
        .recently-fulfilled-list .bg-light {
            transition: all 0.2s ease;
            border-left: 2px solid transparent;
        }

        .recently-fulfilled-list .bg-light:hover {
            background: #f8fafc !important;
            border-left-color: var(--success-color);
            transform: translateX(2px);
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

        /* Alert Soft */
        .alert-info-soft {
            background-color: rgba(14, 165, 233, 0.1);
            color: #0e7490;
        }

        /* Chart Containers */
        .status-chart-container {
            position: relative;
            height: 220px;
        }

        .transaction-chart-container {
            position: relative;
            height: 180px;
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

            // Initialize Transaction Chart
            initializeTransactionChart();

            /**
             * Supply Status Distribution Doughnut Chart
             */
            function initializeStatusChart() {
                const ctx = document.getElementById('supplyStatusChart');
                if (!ctx) return;

                const statusData = [
                    @foreach ($supplyLevelAnalysis['counts'] as $count)
                        {{ $count }},
                    @endforeach
                ];
                const statusLabels = [
                    @foreach ($supplyLevelAnalysis['counts'] as $status => $count)
                        '{{ ucfirst($status) }}',
                    @endforeach
                ];

                // Define status colors
                const statusColors = [];
                const statusNames = [
                    @foreach ($supplyLevelAnalysis['counts'] as $status => $count)
                        '{{ $status }}',
                    @endforeach
                ];

                statusNames.forEach(status => {
                    switch (status) {
                        case 'critical':
                            statusColors.push('#ef4444'); // Red
                            break;
                        case 'low':
                            statusColors.push('#f59e0b'); // Amber
                            break;
                        case 'adequate':
                            statusColors.push('#6b7280'); // Gray
                            break;
                        case 'optimal':
                            statusColors.push('#10b981'); // Green
                            break;
                        case 'overstocked':
                            statusColors.push('#06b6d4'); // Cyan
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
                            ctx.fillText('Total Items', centerX, centerY + 15);
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
             * Supply Trends Line Chart
             */
            function initializeTrendsChart() {
                const ctx = document.getElementById('supplyTrendsChart');
                if (!ctx) return;

                chartInstances.trendsChart = new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: [
                            @foreach ($supplyTrends as $trend)
                                '{{ date('M Y', strtotime($trend->month . '-01')) }}',
                            @endforeach
                        ],
                        datasets: [{
                                label: 'Supplies Added',
                                data: [
                                    @foreach ($supplyTrends as $trend)
                                        {{ $trend->supplies_added }},
                                    @endforeach
                                ],
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
                                label: 'Supplies Deducted',
                                data: [
                                    @foreach ($supplyTrends as $trend)
                                        {{ $trend->supplies_deducted }},
                                    @endforeach
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
                                label: 'Supplies Lost',
                                data: [
                                    @foreach ($supplyTrends as $trend)
                                        {{ $trend->supplies_lost }},
                                    @endforeach
                                ],
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#ef4444',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                pointHoverBorderWidth: 3
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
             * Transaction Analysis Bar Chart
             */
            function initializeTransactionChart() {
                const ctx = document.getElementById('transactionChart');
                if (!ctx) return;

                chartInstances.transactionChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: [
                            @foreach ($transactionAnalysis['transactions'] as $transaction)
                                '{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}',
                            @endforeach
                        ],
                        datasets: [{
                            label: 'Total Quantity',
                            data: [
                                @foreach ($transactionAnalysis['transactions'] as $transaction)
                                    {{ $transaction->total_quantity }},
                                @endforeach
                            ],
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(139, 92, 246, 0.8)',
                                'rgba(6, 182, 212, 0.8)'
                            ],
                            borderColor: [
                                '#10b981',
                                '#3b82f6',
                                '#ef4444',
                                '#f59e0b',
                                '#8b5cf6',
                                '#06b6d4'
                            ],
                            borderWidth: 2,
                            borderRadius: 8,
                            barThickness: 30
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
                                }
                            }
                        },
                        scales: {
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
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    },
                                    color: '#64748b'
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
            const filterForm = document.querySelector('form[action*="supply-management"]');
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
