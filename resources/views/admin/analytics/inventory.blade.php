{{-- resources/views/admin/analytics/inventory.blade.php --}}

@extends('layouts.app')

@section('title', 'Inventory Analytics - AgriSys Admin')
@section('page-title', 'Inventory Analytics Dashboard')

@section('content')
<!-- Header with Service Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Inventory Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into Inventory Management</p>
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

<!-- Service Content -->
<div class="tab-content" id="serviceTabContent">
    <!-- Inventory Service Tab -->
    <div class="tab-pane fade show active" id="inventory-service" role="tabpanel">
        
        <!-- Date Range Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.analytics.inventory') }}" class="row g-3">
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
                                <a href="{{ route('admin.analytics.inventory.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="btn btn-success me-2">
                                    <i class="fas fa-download me-1"></i> Export
                                </a>
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#inventoryInsightsModal">
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
            <!-- Total Items Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Total Items</h6>
                                <h2 class="mb-1">{{ number_format($overview['total_items']) }}</h2>
                                <small class="opacity-75">
                                    <i class="fas fa-arrow-up me-1"></i>{{ $overview['activity_rate'] }}% active
                                </small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-boxes fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stock Health Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Stock Health</h6>
                                <h2 class="mb-1">{{ 100 - $overview['stock_alert_rate'] }}%</h2>
                                <small class="opacity-75">{{ number_format($overview['active_items']) }} items healthy</small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-heart fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Category Diversity Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Category Diversity</h6>
                                <h2 class="mb-1">{{ $overview['unique_categories'] }}</h2>
                                <small class="opacity-75">{{ $overview['unique_varieties'] }} varieties</small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-layer-group fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stock Utilization Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Stock Utilization</h6>
                                <h2 class="mb-1">{{ $overview['stock_utilization_rate'] }}%</h2>
                                <small class="opacity-75">{{ number_format($overview['total_stock_quantity']) }} total stock</small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-chart-pie fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Analytics Row -->
        <div class="row">
            <!-- Stock Distribution -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-pie-chart me-2"></i>Stock Level Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="position-relative">
                            <canvas id="stockDistributionChart" height="250"></canvas>
                        </div>
                        <div class="mt-3">
                            @foreach($stockAnalysis['counts'] as $status => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-circle me-2 text-{{ $status === 'Normal' ? 'success' : ($status === 'Out of Stock' ? 'danger' : ($status === 'Low Stock' ? 'warning' : 'info')) }}"></i>
                                    {{ $status }}
                                </span>
                                <div>
                                    <span class="badge bg-{{ $status === 'Normal' ? 'success' : ($status === 'Out of Stock' ? 'danger' : ($status === 'Low Stock' ? 'warning' : 'info')) }}">
                                        {{ $count }}
                                    </span>
                                    <small class="text-muted ms-1">{{ $stockAnalysis['percentages'][$status] }}%</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Trends Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Monthly Inventory Trends
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="inventoryTrendsChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Analytics Row -->
        <div class="row">
            <!-- Category Analysis -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>Top Categories by Stock
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($categoryAnalysis->take(5) as $index => $category)
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center p-3 rounded" style="background: linear-gradient(90deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);">
                                    <div class="me-3">
                                        <div class="badge bg-success rounded-pill p-2">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ ucfirst($category->category) }}</h6>
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ $categoryAnalysis->first()->total_stock_quantity > 0 ? ($category->total_stock_quantity / $categoryAnalysis->first()->total_stock_quantity) * 100 : 0 }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ $category->total_items }} items</small>
                                            <small class="text-success">{{ number_format($category->total_stock_quantity) }} stock</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unit Distribution -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-balance-scale me-2"></i>Unit Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-3">
                            <canvas id="unitDistributionChart" height="200"></canvas>
                        </div>
                        <div class="row text-center">
                            @foreach($unitAnalysis->take(4) as $unit)
                            <div class="col-6 mb-2">
                                <div class="p-3 rounded" style="background: rgba(245, 158, 11, 0.1);">
                                    <h4 class="mb-1 text-warning">{{ $unit->total_items }}</h4>
                                    <p class="mb-0 text-muted small">{{ $unit->unit }}</p>
                                    <small class="text-warning">
                                        {{ number_format($unit->total_quantity) }} qty
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Analytics Row -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>Top Performing Categories
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Category</th>
                                        <th>Total Items</th>
                                        <th>Active Items</th>
                                        <th>Total Stock</th>
                                        <th>Stock Health</th>
                                        <th>Varieties</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categoryAnalysis->take(10) as $index => $category)
                                    <tr>
                                        <td>
                                            <div class="badge bg-{{ $index < 3 ? ($index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info')) : 'light text-dark' }} rounded-pill">
                                                #{{ $index + 1 }}
                                            </div>
                                        </td>
                                        <td><strong>{{ $category->category }}</strong></td>
                                        <td>{{ $category->total_items }}</td>
                                        <td>{{ $category->active_items }}</td>
                                        <td>{{ number_format($category->total_stock_quantity) }}</td>
                                        <td>
                                            @php
                                                $healthRate = $category->total_items > 0 ? round((($category->total_items - $category->low_stock_items) / $category->total_items) * 100) : 0;
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 60px; height: 6px;">
                                                    <div class="progress-bar bg-{{ $healthRate > 80 ? 'success' : ($healthRate > 60 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $healthRate }}%"></div>
                                                </div>
                                                <small>{{ $healthRate }}%</small>
                                            </div>
                                        </td>
                                        <td>{{ $category->unique_varieties }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tachometer-alt me-2"></i>Performance Metrics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="metric-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Inventory Utilization</h6>
                                <span class="badge bg-{{ $performanceMetrics['inventory_utilization_rate'] > 80 ? 'success' : ($performanceMetrics['inventory_utilization_rate'] > 60 ? 'warning' : 'danger') }}">
                                    {{ $performanceMetrics['inventory_utilization_rate'] }}%
                                </span>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-{{ $performanceMetrics['inventory_utilization_rate'] > 80 ? 'success' : ($performanceMetrics['inventory_utilization_rate'] > 60 ? 'warning' : 'danger') }}" 
                                     style="width: {{ $performanceMetrics['inventory_utilization_rate'] }}%"></div>
                            </div>
                            <small class="text-muted">{{ $overview['active_items'] }} of {{ $overview['total_items'] }} active</small>
                        </div>
                        
                        <div class="metric-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Stock Health Score</h6>
                                <span class="badge bg-info">{{ $performanceMetrics['stock_health_score'] }}%</span>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: {{ $performanceMetrics['stock_health_score'] }}%"></div>
                            </div>
                            <small class="text-muted">{{ $overview['low_stock_items'] + $overview['out_of_stock_items'] }} items need attention</small>
                        </div>
                        
                        <div class="metric-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Category Diversity</h6>
                                <span class="badge bg-{{ $performanceMetrics['category_diversity'] > 10 ? 'success' : ($performanceMetrics['category_diversity'] > 5 ? 'warning' : 'danger') }}">
                                    {{ $performanceMetrics['category_diversity'] }}
                                </span>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-{{ $performanceMetrics['category_diversity'] > 10 ? 'success' : ($performanceMetrics['category_diversity'] > 5 ? 'warning' : 'danger') }}" 
                                     style="width: {{ min(100, $performanceMetrics['category_diversity'] * 5) }}%"></div>
                            </div>
                            <small class="text-muted">{{ $overview['unique_varieties'] }} varieties total</small>
                        </div>
                        
                        <div class="metric-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Restock Rate</h6>
                                <span class="badge bg-primary">{{ $performanceMetrics['recent_restock_rate'] }}%</span>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-primary" style="width: {{ $performanceMetrics['recent_restock_rate'] }}%"></div>
                            </div>
                            <small class="text-muted">{{ $overview['recently_restocked'] }} restocked recently</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Movement & Efficiency Analysis -->
        <div class="row">
            <!-- Stock Movement Analysis -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exchange-alt me-2"></i>Stock Movement Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-success mb-1">{{ $efficiencyAnalysis['optimal_stock_percentage'] }}%</h4>
                                    <p class="mb-0 text-muted">Optimal Stock</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-warning mb-1">{{ $efficiencyAnalysis['avg_buffer_utilization'] }}%</h4>
                                    <p class="mb-0 text-muted">Buffer Utilization</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Items in Optimal Range</span>
                                <span class="text-success">{{ round(($efficiencyAnalysis['optimal_stock_percentage'] / 100) * $overview['total_items']) }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ $efficiencyAnalysis['optimal_stock_percentage'] }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Items Need Attention</span>
                                <span class="text-warning">{{ $overview['low_stock_items'] + $overview['out_of_stock_items'] }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-warning" 
                                     style="width: {{ $overview['stock_alert_rate'] }}%"></div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                Items with optimal stock levels show {{ $efficiencyAnalysis['optimal_stock_percentage'] }}% efficiency rate.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Restock Patterns -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Restock Patterns
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Recent Restock Activity</h6>
                        @foreach($restockPatterns['monthly']->take(6) as $month)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('M Y') }}</span>
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 100px; height: 6px;">
                                    <div class="progress-bar bg-primary" 
                                         style="width: {{ $restockPatterns['monthly']->max('items_restocked') > 0 ? ($month->items_restocked / $restockPatterns['monthly']->max('items_restocked')) * 100 : 0 }}%"></div>
                                </div>
                                <span class="badge bg-primary">{{ $month->items_restocked }}</span>
                            </div>
                        </div>
                        @endforeach
                        
                        <hr class="my-3">
                        
                        <h6 class="mb-3">Top Categories for Restocking</h6>
                        @foreach($restockPatterns['by_category']->sortByDesc('restocked_last_30_days')->take(3) as $category)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $category->category }}</span>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2">{{ $category->restocked_last_30_days }}</span>
                                <small class="text-muted">avg {{ round($category->avg_days_since_restock) }}d</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Insights Row -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>Key Inventory Insights
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="insight-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="p-2 rounded-circle bg-success bg-opacity-10">
                                                <i class="fas fa-chart-line text-success"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Healthy Inventory</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $performanceMetrics['stock_health_score'] }}% of items maintain healthy stock levels.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="insight-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="p-2 rounded-circle bg-info bg-opacity-10">
                                                <i class="fas fa-layer-group text-info"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Category Diversity</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $overview['unique_categories'] }} categories with {{ $overview['unique_varieties'] }} varieties managed.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="insight-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="p-2 rounded-circle bg-warning bg-opacity-10">
                                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Attention Required</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $overview['low_stock_items'] + $overview['out_of_stock_items'] }} items need restocking attention.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="insight-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="p-2 rounded-circle bg-primary bg-opacity-10">
                                                <i class="fas fa-sync-alt text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Recent Activity</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $overview['recently_restocked'] }} items restocked in the last 30 days.
                                            </p>
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

<!-- Inventory AI Insights Modal -->
<div class="modal fade" id="inventoryInsightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-robot me-2"></i>Inventory AI-Powered Insights
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-line text-success me-2"></i>Optimization Opportunities</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-arrow-up text-success me-2"></i>
                                Focus on categories with high stock turnover rates
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-balance-scale text-info me-2"></i>
                                Optimize stock levels for {{ $overview['low_stock_items'] }} low-stock items
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                Implement predictive restocking for high-demand categories
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Areas for Improvement</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-boxes text-warning me-2"></i>
                                {{ $overview['out_of_stock_items'] > 0 ? 'Address out-of-stock items immediately' : 'Maintain current stock availability' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-chart-pie text-info me-2"></i>
                                {{ $overview['stock_utilization_rate'] < 70 ? 'Improve stock utilization efficiency' : 'Maintain current utilization levels' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-layer-group text-success me-2"></i>
                                Consider expanding variety in underrepresented categories
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb me-2"></i>Recommendation</h6>
                            <p class="mb-0">Consider implementing automated reorder points based on historical usage patterns and seasonal trends. Focus on the top {{ $categoryAnalysis->take(3)->count() }} performing categories for maximum impact.</p>
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

/* Gradient Backgrounds */
.bg-gradient-primary { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
}
.bg-gradient-success { 
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); 
}
.bg-gradient-info { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
}
.bg-gradient-warning { 
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); 
}
.bg-gradient-dark { 
    background: linear-gradient(135deg, #434343 0%, #000000 100%); 
}
.bg-gradient-secondary { 
    background: linear-gradient(135deg, #bdc3c7 0%, #2c3e50 100%); 
}

/* Card Hover Effects */
.card {
    border: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 25px rgba(0,0,0,0.1);
}

/* Metric Cards Specific */
.card.border-0.shadow-sm.h-100 {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card.border-0.shadow-sm.h-100:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

/* Navigation Pills */
.nav-pills .nav-link {
    border-radius: 25px;
    margin: 0 5px;
    transition: all 0.3s;
    font-weight: 500;
    padding: 0.6rem 1.2rem;
}

.nav-pills .nav-link.active {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.nav-pills .nav-link:not(.active):hover {
    background-color: rgba(0,0,0,0.05);
    transform: translateY(-1px);
}

/* Hover Effects */
.hover-bg-light:hover {
    background-color: rgba(0,0,0,0.05);
    transition: background-color 0.2s;
}

/* Insight Items */
.insight-item {
    transition: transform 0.2s;
    padding: 1rem;
    border-radius: 10px;
}

.insight-item:hover {
    transform: translateX(5px);
    background-color: rgba(0,0,0,0.02);
}

/* Metric Items */
.metric-item {
    padding: 1rem;
    border-radius: 8px;
    background: rgba(0,0,0,0.02);
    transition: all 0.2s ease;
}

.metric-item:hover {
    background: rgba(0,0,0,0.05);
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Table Enhancements */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.05);
    cursor: pointer;
}

.table thead th {
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.table tbody td {
    border-top: 1px solid rgba(0,0,0,0.05);
    vertical-align: middle;
}

/* Badge Enhancements */
.badge {
    font-weight: 500;
    letter-spacing: 0.3px;
}

.badge.rounded-pill {
    padding: 0.5rem 1rem;
}

/* Progress Bar Enhancements */
.progress {
    background-color: rgba(0,0,0,0.08);
    border-radius: 50px;
}

.progress-bar {
    border-radius: 50px;
    transition: width 0.6s ease;
}

/* Custom Color Classes */
.text-pink {
    color: #ec4899 !important;
}

/* Button Enhancements */
.btn {
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Form Control Enhancements */
.form-control, .form-select {
    border: 1px solid #e0e6ed;
    border-radius: 8px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Modal Enhancements */
.modal-content {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.modal-header {
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.modal-body {
    padding: 2rem;
}

/* Alert Enhancements */
.alert {
    border: none;
    border-radius: 10px;
}

.alert-info {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(14, 165, 233, 0.05) 100%);
    border-left: 4px solid #0ea5e9;
}

/* Chart Container Enhancements */
.chart-container {
    position: relative;
    height: 300px;
    margin: 1rem 0;
}

/* Custom Animations */
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

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Loading Animations */
@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}

.pulse {
    animation: pulse 2s infinite;
}

/* Responsive Enhancements */
@media (max-width: 768px) {
    .nav-pills {
        flex-wrap: wrap;
    }
    
    .nav-pills .nav-link {
        margin: 2px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .insight-item {
        margin-bottom: 1.5rem;
    }
    
    .card.border-0.shadow-sm.h-100 {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .metric-item {
        padding: 0.8rem;
    }
    
    .card-header h5 {
        font-size: 1rem;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #2d3748;
        color: #e2e8f0;
    }
    
    .table {
        color: #e2e8f0;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(255,255,255,0.05);
    }
    
    .form-control, .form-select {
        background-color: #4a5568;
        border-color: #718096;
        color: #e2e8f0;
    }
    
    .form-control:focus, .form-select:focus {
        background-color: #4a5568;
        border-color: #667eea;
        color: #e2e8f0;
    }
}

/* Accessibility Improvements */
.btn:focus, .nav-link:focus, .form-control:focus, .form-select:focus {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

/* Print Styles */
@media print {
    .card:hover {
        transform: none;
        box-shadow: none;
    }
    
    .btn, .nav-pills {
        display: none;
    }
    
    .card {
        break-inside: avoid;
        margin-bottom: 1rem;
    }
}
</style>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pass PHP data to JavaScript
window.stockAnalysisData = {
    counts: {
        @foreach($stockAnalysis['counts'] as $status => $count)
        '{{ $status }}': {{ $count }},
        @endforeach
    },
    percentages: {
        @foreach($stockAnalysis['percentages'] as $status => $percentage)
        '{{ $status }}': {{ $percentage }},
        @endforeach
    }
};

window.monthlyTrendsData = [
    @foreach($monthlyTrends as $trend)
    {
        month: '{{ $trend->month }}',
        total_items: {{ $trend->total_items ?? 0 }},
        active_items: {{ $trend->active_items ?? 0 }},
        low_stock_items: {{ $trend->low_stock_items ?? 0 }},
        total_stock_quantity: {{ $trend->total_stock_quantity ?? 0 }}
    },
    @endforeach
];

window.categoryAnalysisData = [
    @foreach($categoryAnalysis as $category)
    {
        category: '{{ $category->category }}',
        total_items: {{ $category->total_items ?? 0 }},
        active_items: {{ $category->active_items ?? 0 }},
        low_stock_items: {{ $category->low_stock_items ?? 0 }},
        total_stock_quantity: {{ $category->total_stock_quantity ?? 0 }},
        unique_varieties: {{ $category->unique_varieties ?? 0 }}
    },
    @endforeach
];

window.unitAnalysisData = [
    @foreach($unitAnalysis as $unit)
    {
        unit: '{{ $unit->unit }}',
        total_items: {{ $unit->total_items ?? 0 }},
        total_quantity: {{ $unit->total_quantity ?? 0 }}
    },
    @endforeach
];

window.restockPatternsData = {
    monthly: [
        @foreach($restockPatterns['monthly'] as $pattern)
        {
            month: '{{ $pattern->month }}',
            items_restocked: {{ $pattern->items_restocked ?? 0 }},
            categories_restocked: {{ $pattern->categories_restocked ?? 0 }}
        },
        @endforeach
    ]
};

document.addEventListener('DOMContentLoaded', function() {
    let chartInstances = {};
    
    // Chart.js default configuration
    Chart.defaults.font.family = "'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7280';
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
    
    // Initialize charts
    initializeAllCharts();
    
    function initializeAllCharts() {
        initializeStockDistributionChart();
        initializeInventoryTrendsChart();
        initializeUnitDistributionChart();
    }
    
    // Stock Distribution Doughnut Chart
    function initializeStockDistributionChart() {
        const ctx = document.getElementById('stockDistributionChart');
        if (!ctx) {
            console.warn('Stock distribution chart canvas not found');
            return;
        }
        
        const stockData = window.stockAnalysisData || {};
        const stockCounts = Object.values(stockData.counts || {});
        const stockLabels = Object.keys(stockData.counts || {});
        
        if (stockCounts.length === 0) {
            console.warn('No stock distribution data available');
            return;
        }
        
        try {
            chartInstances.stockDistributionChart = new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: stockLabels,
                    datasets: [{
                        data: stockCounts,
                        backgroundColor: [
                            '#10b981',  // green for Normal
                            '#f59e0b',  // amber for Low Stock  
                            '#ef4444',  // red for Out of Stock
                            '#3b82f6'   // blue for Overstocked
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        cutout: '70%',
                        hoverOffset: 8
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
                            backgroundColor: 'rgba(0,0,0,0.9)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} items (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        duration: 1000
                    }
                }
            });
            console.log('Stock distribution chart initialized successfully');
        } catch (error) {
            console.error('Error initializing stock distribution chart:', error);
        }
    }
    
    // Inventory Trends Line Chart
    function initializeInventoryTrendsChart() {
        const ctx = document.getElementById('inventoryTrendsChart');
        if (!ctx) {
            console.warn('Inventory trends chart canvas not found');
            return;
        }
        
        const trendsData = window.monthlyTrendsData || [];
        
        if (trendsData.length === 0) {
            console.warn('No monthly trends data available');
            // Show a message in the chart area
            ctx.getContext('2d').fillText('No data available for the selected period', 10, 50);
            return;
        }
        
        const labels = trendsData.map(trend => {
            try {
                const date = new Date(trend.month + '-01');
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            } catch (e) {
                return trend.month;
            }
        });
        
        try {
            chartInstances.inventoryTrendsChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Items',
                        data: trendsData.map(d => d.total_items || 0),
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#0ea5e9',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#0ea5e9',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    }, {
                        label: 'Active Items',
                        data: trendsData.map(d => d.active_items || 0),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }, {
                        label: 'Low Stock Items',
                        data: trendsData.map(d => d.low_stock_items || 0),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: false,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
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
                        x: {
                            grid: {
                                display: true,
                                color: 'rgba(0,0,0,0.05)',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                maxRotation: 45
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            },
                            title: {
                                display: true,
                                text: 'Number of Items',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 11,
                                    weight: '500'
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0,0,0,0.9)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: 'rgba(255,255,255,0.2)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label;
                                },
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y.toLocaleString();
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverBorderWidth: 3
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });
            console.log('Inventory trends chart initialized successfully');
        } catch (error) {
            console.error('Error initializing inventory trends chart:', error);
        }
    }
    
    // Unit Distribution Chart
    function initializeUnitDistributionChart() {
        const ctx = document.getElementById('unitDistributionChart');
        if (!ctx) {
            console.warn('Unit distribution chart canvas not found');
            return;
        }
        
        const unitData = window.unitAnalysisData || [];
        
        if (unitData.length === 0) {
            console.warn('No unit distribution data available');
            return;
        }
        
        const unitLabels = unitData.map(unit => unit.unit || 'Unknown');
        const unitCounts = unitData.map(unit => unit.total_items || 0);
        
        try {
            chartInstances.unitDistributionChart = new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: unitLabels,
                    datasets: [{
                        data: unitCounts,
                        backgroundColor: [
                            '#f59e0b',  // amber
                            '#10b981',  // green
                            '#3b82f6',  // blue
                            '#ef4444',  // red
                            '#8b5cf6',  // purple
                            '#06b6d4',  // cyan
                            '#84cc16',  // lime
                            '#f97316',  // orange
                            '#ec4899',  // pink
                            '#6b7280'   // gray
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        cutout: '60%',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const dataset = data.datasets[0];
                                            const value = dataset.data[i] || 0;
                                            const total = dataset.data.reduce((a, b) => (a || 0) + (b || 0), 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return {
                                                text: `${label} (${percentage}%)`,
                                                fillStyle: dataset.backgroundColor[i],
                                                hidden: false,
                                                index: i,
                                                pointStyle: 'circle'
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.9)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => (a || 0) + (b || 0), 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} items (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        duration: 1000
                    }
                }
            });
            console.log('Unit distribution chart initialized successfully');
        } catch (error) {
            console.error('Error initializing unit distribution chart:', error);
        }
    }
    
    // Enhanced interactions and animations
    function initializeInteractions() {
        // Metric cards hover effects
        document.querySelectorAll('.metric-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
                this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                this.style.transition = 'all 0.3s ease';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
            });
        });
        
        // Progress bar animations with intersection observer
        const progressBars = document.querySelectorAll('.progress-bar');
        const progressObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const progressBar = entry.target;
                    const targetWidth = progressBar.style.width;
                    
                    // Reset and animate
                    progressBar.style.width = '0%';
                    progressBar.style.transition = 'width 1.5s ease-in-out';
                    
                    setTimeout(() => {
                        progressBar.style.width = targetWidth;
                    }, 200);
                    
                    progressObserver.unobserve(progressBar);
                }
            });
        }, { threshold: 0.3 });
        
        progressBars.forEach(bar => progressObserver.observe(bar));
    }
    
    // Initialize all interactions
    initializeInteractions();
    
    // Export functionality with enhanced UX
    document.querySelectorAll('a[href*="export"]').forEach(exportLink => {
        exportLink.addEventListener('click', function(e) {
            const originalText = this.innerHTML;
            const originalClasses = this.className;
            
            // Add loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating Export...';
            this.classList.add('disabled');
            this.style.pointerEvents = 'none';
            
            // Simulate export process
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check me-1"></i> Export Complete!';
                this.className = originalClasses.replace('btn-success', 'btn-success-alt');
                
                // Reset after showing success
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.className = originalClasses;
                    this.style.pointerEvents = 'auto';
                    
                    // Show success notification
                    showNotification('Export completed successfully!', 'success');
                }, 2000);
            }, 1500);
        });
    });
    
    // AI Insights Modal enhancements
    const insightsModal = document.getElementById('inventoryInsightsModal');
    if (insightsModal) {
        insightsModal.addEventListener('show.bs.modal', function() {
            // Add loading animation to insights
            const insights = this.querySelectorAll('.list-unstyled li');
            insights.forEach((insight, index) => {
                insight.style.opacity = '0';
                insight.style.transform = 'translateX(-20px)';
                insight.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                
                setTimeout(() => {
                    insight.style.opacity = '1';
                    insight.style.transform = 'translateX(0)';
                }, index * 100);
            });
        });
    }
    
    // Notification system
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.notification-toast').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed notification-toast`;
        notification.style.cssText = `
            top: 20px; 
            right: 20px; 
            z-index: 9999; 
            min-width: 300px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border: none;
            border-radius: 10px;
        `;
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${getNotificationIcon(type)} me-2"></i>
                <span>${message}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Get notification icon based on type
    function getNotificationIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-triangle',
            'warning': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    // Chart resize handler
    function handleChartResize() {
        Object.values(chartInstances).forEach(chart => {
            if (chart && typeof chart.resize === 'function') {
                chart.resize();
            }
        });
    }
    
    // Window resize event
    window.addEventListener('resize', debounce(handleChartResize, 250));
    
    // Debounce utility function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Cleanup function for when leaving the page
    window.destroyCharts = function() {
        Object.values(chartInstances).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        chartInstances = {};
    };
    
    // Error handling and recovery
    window.addEventListener('error', function(e) {
        console.error('JavaScript Error in Inventory Analytics:', e);
        showNotification('An error occurred. Some features may not work properly.', 'error');
    });
    
    // Success message on page load
    setTimeout(() => {
        const chartsLoaded = Object.keys(chartInstances).length;
        showNotification(`Inventory analytics loaded successfully! (${chartsLoaded} charts initialized)`, 'success');
    }, 1000);
    
    console.log('Inventory Analytics Dashboard initialized successfully');
    console.log('Available charts:', Object.keys(chartInstances));
    console.log('Data available:', {
        stockAnalysis: !!window.stockAnalysisData,
        monthlyTrends: !!window.monthlyTrendsData && window.monthlyTrendsData.length,
        categoryAnalysis: !!window.categoryAnalysisData && window.categoryAnalysisData.length,
        unitAnalysis: !!window.unitAnalysisData && window.unitAnalysisData.length
    });
});

// Global utility functions
window.InventoryAnalytics = {
    refreshCharts: function() {
        Object.values(window.chartInstances || {}).forEach(chart => {
            if (chart && typeof chart.update === 'function') {
                chart.update();
            }
        });
    },
    
    exportChartData: function(chartName) {
        const chart = window.chartInstances?.[chartName];
        if (chart) {
            return chart.data;
        }
        return null;
    },
    
    updateChartData: function(chartName, newData) {
        const chart = window.chartInstances?.[chartName];
        if (chart) {
            chart.data = newData;
            chart.update();
        }
    }
};
</script>
@endsection