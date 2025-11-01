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
                    <div class="text-center mb-3">
                        <h4 class="mb-2 fw-bold">Supply Management Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into inventory levels, supply trends, and
                            fulfillment metrics</p>
                    </div>
                    <!-- Service Tabs -->
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
                                    <i class="fas fa-user-plus me-1"></i> User Registration
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
                        <div class="col-md-3">
                            <label for="start_date" class="form-label fw-semibold">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label fw-semibold">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Apply Filter
                            </button>
                            <a href="{{ route('admin.analytics.supply-management.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                                class="btn btn-success">
                                <i class="fas fa-download me-1"></i> Export
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4 g-3">
        <!-- Total Items Card -->
        <div class="col-lg-3 col-md-6">
            <div class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label mb-2">Total Items</p>
                            <h2 class="metric-value mb-1">{{ number_format($overview['total_items']) }}</h2>
                            <small class="metric-subtitle">
                                <i class="fas fa-check-circle me-1"></i>{{ $overview['active_items'] }} active items
                            </small>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Supply Card -->
        <div class="col-lg-3 col-md-6">
            <div class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label mb-2">Total Supply</p>
                            <h2 class="metric-value mb-1">{{ number_format($overview['total_supply']) }}</h2>
                            <small class="metric-subtitle">
                                Avg {{ number_format($overview['avg_supply_per_item'], 1) }} per item
                            </small>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-layer-group fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supply Health Score Card -->
        <div class="col-lg-3 col-md-6">
            <div class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body text-white"
                    style="background: linear-gradient(135deg, {{ $overview['supply_health_score'] >= 80 ? '#10b981, #059669' : ($overview['supply_health_score'] >= 60 ? '#f59e0b, #d97706' : '#ef4444, #dc2626') }});">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label mb-2">Supply Health Score</p>
                            <h2 class="metric-value mb-1">{{ $overview['supply_health_score'] }}%</h2>
                            <small class="metric-subtitle">
                                {{ $overview['healthy_stock_items'] }} healthy items
                            </small>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-heartbeat fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Card -->
        <div class="col-lg-3 col-md-6">
            <div class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label mb-2">Supply Alerts</p>
                            <h2 class="metric-value mb-1">{{ $supplyAlerts['total_alerts'] }}</h2>
                            <small class="metric-subtitle">
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $overview['out_of_stock_items'] }} out
                                of stock
                            </small>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supply Status Overview -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Supply Trends Over Time
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="supplyTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Supply Status Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-pie me-2 text-success"></i>Supply Status
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="supplyStatusChart"></canvas>
                    <div class="mt-3">
                        @foreach ($supplyLevelAnalysis['counts'] as $status => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded status-item">
                                <span class="d-flex align-items-center">
                                    <i
                                        class="fas fa-circle me-2 {{ $status === 'critical' ? 'text-danger' : ($status === 'low' ? 'text-warning' : ($status === 'optimal' ? 'text-success' : ($status === 'overstocked' ? 'text-info' : 'text-secondary'))) }}"></i>
                                    {{ ucfirst($status) }}
                                </span>
                                <span
                                    class="badge bg-{{ $status === 'critical' ? 'danger' : ($status === 'low' ? 'warning' : ($status === 'optimal' ? 'success' : ($status === 'overstocked' ? 'info' : 'secondary'))) }}">
                                    {{ $count }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Analysis & Fulfillment -->
    <div class="row g-3 mb-4">
        <!-- Transaction Types -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-exchange-alt me-2 text-info"></i>Transaction Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="transactionChart"></canvas>
                    <div class="mt-3">
                        @foreach ($transactionAnalysis['transactions'] as $transaction)
                            <div
                                class="d-flex justify-content-between align-items-center mb-2 p-2 rounded transaction-item">
                                <div>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</strong>
                                    <small class="d-block text-muted">{{ $transaction->unique_items }} items</small>
                                </div>
                                <div class="text-end">
                                    <span
                                        class="badge bg-primary">{{ number_format($transaction->total_quantity) }}</span>
                                    <small
                                        class="d-block text-muted">{{ $transactionAnalysis['percentages'][$transaction->transaction_type] ?? 0 }}%</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Fulfillment Metrics -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-check-double me-2 text-success"></i>Fulfillment Performance
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Overall Fulfillment Rate -->
                    <div class="metric-item mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Overall Fulfillment Rate</h6>
                            <span
                                class="badge bg-{{ $fulfillmentAnalysis['fulfillment_rate'] >= 80 ? 'success' : ($fulfillmentAnalysis['fulfillment_rate'] >= 60 ? 'warning' : 'danger') }} fs-6">
                                {{ $fulfillmentAnalysis['fulfillment_rate'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-{{ $fulfillmentAnalysis['fulfillment_rate'] >= 80 ? 'success' : ($fulfillmentAnalysis['fulfillment_rate'] >= 60 ? 'warning' : 'danger') }}"
                                style="width: {{ $fulfillmentAnalysis['fulfillment_rate'] }}%"></div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ number_format($fulfillmentAnalysis['total_approved']) }} of
                            {{ number_format($fulfillmentAnalysis['total_requested']) }} requested
                        </small>
                    </div>

                    <!-- Full Fulfillment Rate -->
                    <div class="metric-item mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Full Fulfillment Rate</h6>
                            <span
                                class="badge bg-primary fs-6">{{ $fulfillmentAnalysis['full_fulfillment_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-primary"
                                style="width: {{ $fulfillmentAnalysis['full_fulfillment_rate'] }}%"></div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ number_format($fulfillmentAnalysis['fully_fulfilled']) }} requests fully fulfilled
                        </small>
                    </div>

                    <!-- Fulfillment Breakdown -->
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="text-center p-2 rounded" style="background: rgba(16, 185, 129, 0.1);">
                                <div class="text-success fw-bold fs-4">{{ $fulfillmentAnalysis['fully_fulfilled'] }}</div>
                                <small class="text-muted">Fully Fulfilled</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded" style="background: rgba(245, 158, 11, 0.1);">
                                <div class="text-warning fw-bold fs-4">{{ $fulfillmentAnalysis['partially_fulfilled'] }}
                                </div>
                                <small class="text-muted">Partially</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-2 rounded" style="background: rgba(239, 68, 68, 0.1);">
                                <div class="text-danger fw-bold fs-4">{{ $fulfillmentAnalysis['not_fulfilled'] }}</div>
                                <small class="text-muted">Not Fulfilled</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Efficiency Metrics & Loss Analysis -->
    <div class="row g-3 mb-4">
        <!-- Efficiency Metrics -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-tachometer-alt me-2 text-info"></i>Supply Efficiency Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="text-center p-3 rounded"
                                style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);">
                                <i class="fas fa-arrow-up fa-2x text-success mb-2"></i>
                                <div class="text-success fw-bold fs-4">
                                    {{ number_format($efficiencyMetrics['supply_added']) }}</div>
                                <small class="text-muted">Supply Added</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 rounded"
                                style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(14, 165, 233, 0.05) 100%);">
                                <i class="fas fa-arrow-down fa-2x text-primary mb-2"></i>
                                <div class="text-primary fw-bold fs-4">
                                    {{ number_format($efficiencyMetrics['supply_deducted']) }}</div>
                                <small class="text-muted">Supply Deducted</small>
                            </div>
                        </div>
                    </div>

                    <!-- Utilization Rate -->
                    <div class="metric-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Utilization Rate</h6>
                            <span class="badge bg-primary fs-6">{{ $efficiencyMetrics['utilization_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary"
                                style="width: {{ min(100, $efficiencyMetrics['utilization_rate']) }}%"></div>
                        </div>
                    </div>

                    <!-- Loss Rate -->
                    <div class="metric-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Loss Rate</h6>
                            <span
                                class="badge bg-{{ $efficiencyMetrics['loss_rate'] > 10 ? 'danger' : ($efficiencyMetrics['loss_rate'] > 5 ? 'warning' : 'success') }} fs-6">
                                {{ $efficiencyMetrics['loss_rate'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $efficiencyMetrics['loss_rate'] > 10 ? 'danger' : ($efficiencyMetrics['loss_rate'] > 5 ? 'warning' : 'success') }}"
                                style="width: {{ min(100, $efficiencyMetrics['loss_rate']) }}%"></div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ number_format($efficiencyMetrics['supply_lost']) }} units lost
                        </small>
                    </div>

                    <!-- Net Supply Change -->
                    <div
                        class="alert alert-{{ $efficiencyMetrics['net_supply_change'] >= 0 ? 'success' : 'warning' }} mb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Net Supply Change</strong>
                            </div>
                            <div class="fs-5 fw-bold">
                                {{ $efficiencyMetrics['net_supply_change'] >= 0 ? '+' : '' }}{{ number_format($efficiencyMetrics['net_supply_change']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loss Analysis -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>Loss & Waste Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background: rgba(239, 68, 68, 0.1);">
                                <i class="fas fa-minus-circle fa-2x text-danger mb-2"></i>
                                <div class="text-danger fw-bold fs-4">{{ number_format($lossAnalysis['total_loss']) }}
                                </div>
                                <small class="text-muted">Total Loss</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background: rgba(245, 158, 11, 0.1);">
                                <i class="fas fa-times-circle fa-2x text-warning mb-2"></i>
                                <div class="text-warning fw-bold fs-4">{{ $lossAnalysis['loss_incidents'] }}</div>
                                <small class="text-muted">Incidents</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 rounded" style="background: rgba(107, 114, 128, 0.1);">
                                <i class="fas fa-chart-bar fa-2x text-secondary mb-2"></i>
                                <div class="text-secondary fw-bold fs-4">
                                    {{ number_format($lossAnalysis['avg_loss_per_incident'], 1) }}</div>
                                <small class="text-muted">Avg per Incident</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-semibold mb-3">Top Items by Loss</h6>
                    <div class="loss-items-list" style="max-height: 250px; overflow-y: auto;">
                        @forelse ($lossAnalysis['loss_by_item']->take(5) as $index => $item)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded loss-item">
                                <div>
                                    <strong class="d-block">{{ $item['item_name'] }}</strong>
                                    <small class="text-muted">{{ $item['category'] }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger">{{ number_format($item['total_loss']) }}
                                        {{ $item['unit'] }}</span>
                                    <small class="d-block text-muted">{{ $item['loss_incidents'] }} incidents</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">No loss data available</p>
                        @endforelse
                    </div>
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
                                <div class="flex-grow-1">
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
                                <div class="flex-grow-1">
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
                                <div class="flex-grow-1">
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


    <div class="modal fade" id="supplyInsightsModal" tabindex="-1" aria-labelledby="supplyInsightsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
        @endsection

        @section('styles')
            <style>
                /* Global Styles */
                body {
                    background-color: #f8f9fa;
                }

                /* Card Styles */
                .card {
                    border-radius: 12px;
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }

                .card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
                }

                /* Metric Cards */
                .metric-card .card-body {
                    border-radius: 12px;
                    position: relative;
                    overflow: hidden;
                }

                .metric-label {
                    font-size: 0.875rem;
                    opacity: 0.9;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .metric-value {
                    font-size: 2.5rem;
                    font-weight: 700;
                    line-height: 1;
                }

                .metric-subtitle {
                    opacity: 0.85;
                    font-size: 0.875rem;
                }

                .metric-icon {
                    opacity: 0.2;
                    position: absolute;
                    right: 20px;
                    top: 50%;
                    transform: translateY(-50%);
                }

                /* Status Items */
                .status-item {
                    transition: all 0.2s ease;
                    background-color: #f8f9fa;
                }

                .status-item:hover {
                    background-color: #e9ecef;
                    transform: translateX(5px);
                }

                /* Transaction Items */
                .transaction-item {
                    transition: all 0.2s ease;
                    background-color: #f8f9fa;
                }

                .transaction-item:hover {
                    background-color: #e9ecef;
                    transform: translateX(5px);
                }

                /* Loss Items */
                .loss-item {
                    transition: all 0.2s ease;
                    background-color: #f8f9fa;
                    border-left: 3px solid #ef4444;
                }

                .loss-item:hover {
                    background-color: #fee2e2;
                    transform: translateX(5px);
                }

                /* Item Cards */
                .item-card {
                    background: linear-gradient(90deg, rgba(59, 130, 246, 0.08) 0%, rgba(59, 130, 246, 0.02) 100%);
                    border-left: 4px solid #3b82f6;
                    transition: all 0.2s ease;
                }

                .item-card:hover {
                    background: linear-gradient(90deg, rgba(59, 130, 246, 0.12) 0%, rgba(59, 130, 246, 0.04) 100%);
                    transform: translateX(5px);
                }

                /* Progress Bars */
                .progress {
                    border-radius: 10px;
                    background-color: rgba(0, 0, 0, 0.05);
                }

                .progress-bar {
                    border-radius: 10px;
                    transition: width 0.6s ease;
                }

                /* Table Styles */
                .table {
                    font-size: 0.9rem;
                }

                .table thead th {
                    font-weight: 600;
                    text-transform: uppercase;
                    font-size: 0.75rem;
                    letter-spacing: 0.5px;
                    color: #6b7280;
                    border-bottom: 2px solid #e5e7eb;
                }

                .table tbody tr {
                    transition: all 0.2s ease;
                }

                .table tbody tr:hover {
                    background-color: #f9fafb;
                    transform: scale(1.01);
                }

                /* Navigation Pills */
                .nav-pills .nav-link {
                    border-radius: 25px;
                    transition: all 0.3s ease;
                    padding: 0.5rem 1.25rem;
                    font-weight: 500;
                }

                .nav-pills .nav-link:hover:not(.active) {
                    background-color: #f3f4f6;
                    transform: translateY(-2px);
                }

                .nav-pills .nav-link.active {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                }

                /* Card Headers */
                .card-header {
                    padding: 1.25rem 1.5rem;
                }

                .card-header h5 {
                    font-size: 1.1rem;
                }

                /* Badge Styles */
                .badge {
                    font-weight: 600;
                    padding: 0.4em 0.8em;
                    font-size: 0.875em;
                }

                /* Chart Container */
                canvas {
                    max-height: 350px;
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
                    border-radius: 15px;
                }

                .modal-header {
                    border-radius: 15px 15px 0 0;
                }

                /* Metric Item Styles */
                .metric-item .progress {
                    border-radius: 10px;
                }

                /* Text Colors */
                .text-purple {
                    color: #8b5cf6 !important;
                }

                /* Scrollbar Styling */
                .loss-items-list::-webkit-scrollbar {
                    width: 6px;
                }

                .loss-items-list::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 10px;
                }

                .loss-items-list::-webkit-scrollbar-thumb {
                    background: #888;
                    border-radius: 10px;
                }

                .loss-items-list::-webkit-scrollbar-thumb:hover {
                    background: #555;
                }

                /* Responsive Adjustments */
                @media (max-width: 768px) {
                    .metric-value {
                        font-size: 2rem;
                    }

                    .metric-label {
                        font-size: 0.75rem;
                    }

                    .nav-pills .nav-link {
                        padding: 0.4rem 0.8rem;
                        font-size: 0.875rem;
                    }

                    .table {
                        font-size: 0.8rem;
                    }

                    .item-card {
                        margin-bottom: 0.75rem !important;
                    }
                }

                @media (max-width: 576px) {
                    .card-body {
                        padding: 1rem;
                    }

                    .metric-icon {
                        display: none;
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
                    animation: fadeInUp 0.5s ease-out;
                }
            </style>
        @endsection

        @section('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Chart.js Global Configuration
                    Chart.defaults.font.family = "'Inter', 'Segoe UI', 'Roboto', sans-serif";
                    Chart.defaults.color = '#6b7280';
                    Chart.defaults.plugins.legend.labels.usePointStyle = true;
                    Chart.defaults.plugins.legend.labels.padding = 15;

                    // Supply Status Distribution Donut Chart
                    const statusCtx = document.getElementById('supplyStatusChart').getContext('2d');
                    new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: [
                                @foreach ($supplyLevelAnalysis['counts'] as $status => $count)
                                    '{{ ucfirst($status) }}',
                                @endforeach
                            ],
                            datasets: [{
                                data: [
                                    @foreach ($supplyLevelAnalysis['counts'] as $count)
                                        {{ $count }},
                                    @endforeach
                                ],
                                backgroundColor: [
                                    '#ef4444', // critical - red
                                    '#f59e0b', // low - yellow
                                    '#6b7280', // adequate - gray
                                    '#10b981', // optimal - green
                                    '#06b6d4' // overstocked - cyan
                                ],
                                borderWidth: 4,
                                borderColor: '#fff',
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            aspectRatio: 1.2,
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
                                            let label = context.label || '';
                                            let value = context.parsed || 0;
                                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    });

                    // Supply Trends Over Time Chart
                    const trendsCtx = document.getElementById('supplyTrendsChart').getContext('2d');
                    new Chart(trendsCtx, {
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
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    pointBackgroundColor: '#10b981',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2
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
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    pointBackgroundColor: '#3b82f6',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2
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
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    pointBackgroundColor: '#ef4444',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            aspectRatio: 2.5,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        }
                                    }
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
                                    title: {
                                        display: true,
                                        text: 'Quantity',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    title: {
                                        display: true,
                                        text: 'Month',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Transaction Analysis Chart
                    const transactionCtx = document.getElementById('transactionChart').getContext('2d');
                    new Chart(transactionCtx, {
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
                                barThickness: 40
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            aspectRatio: 2,
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
                                        afterLabel: function(context) {
                                            let index = context.dataIndex;
                                            let transactions = [
                                                @foreach ($transactionAnalysis['transactions'] as $transaction)
                                                    {
                                                        items: {{ $transaction->unique_items }},
                                                        percentage: {{ $transactionAnalysis['percentages'][$transaction->transaction_type] ?? 0 }}
                                                    },
                                                @endforeach
                                            ];
                                            return [
                                                'Items: ' + transactions[index].items,
                                                'Percentage: ' + transactions[index].percentage + '%'
                                            ];
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Total Quantity',
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
        @endsection
