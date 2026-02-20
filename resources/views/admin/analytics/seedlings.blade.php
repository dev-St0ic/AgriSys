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
    $claimAnalysis = $claimAnalysis ?? [];

    // ADD THESE NEW DEFAULTS FOR FILTER
    $filterType = $filterType ?? 'preset';
    $datePreset = $datePreset ?? 'this_month';
    $startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
    $endDate = $endDate ?? now()->format('Y-m-d');
@endphp

@extends('layouts.app')

@section('title', 'Analytics - AgriSys Admin')
@section('page-icon', 'fas fa-chart-bar')
@section('page-title', 'Supply Request Analytics Dashboard')

@section('content')
    <!-- Enhanced Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            @include('admin.analytics.partials.nav')
        </div>
    </div> <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            @include('admin.analytics.partials.filter', [
                'filterRoute' => 'admin.analytics.seedlings',
                'exportRoute' => 'admin.analytics.seedlings.export',
            ])
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
                        <i class="fas fa-clock fa-2x text-info"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $processingTimeAnalysis['avg_processing_days'] ?? 0 }}</h2>
                    <h6 class="text-muted mb-2">Avg. Processing Days</h6>
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
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Request Status
                    </h5>
                    <small class="text-muted">Current distribution of request statuses</small>
                </div>
                <div class="card-body">
                    <div class="status-chart-container mb-3">
                        <canvas id="statusChart" height="220"></canvas>
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
                                $total = array_sum($statusAnalysis['counts']);
                                $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                            @endphp
                            <div class="legend-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                <div class="d-flex align-items-center">
                                    <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge text-white me-2" style="background-color: {{ $dotColor }};">
                                        {{ $count }}
                                    </span>
                                    <span class="text-muted fw-semibold">{{ $percentage }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Supply vs Demand -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
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
                    <h5 class="mb-0 fw-semibold">
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
                                        <strong class="text-capitalize">{{ str_replace('_', ' ', $category) }}</strong>
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
                    <h5 class="mb-0 fw-semibold">
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
                    <h5 class="mb-0 fw-semibold">
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
                    <h5 class="mb-0 fw-semibold">
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

       {{-- ── CLAIM STATUS SECTION ─────────────────────────────────── --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-hand-holding me-2 text-success"></i>Claim Status Overview
                    </h5>
                    <small class="text-muted">Pickup tracking for approved &amp; partially approved requests</small>
                </div>
                @if(($claimAnalysis['overdue_unclaimed'] ?? 0) > 0)
                    <span class="badge bg-danger fs-6">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        {{ $claimAnalysis['overdue_unclaimed'] }} Overdue
                    </span>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="p-3 rounded-3 text-center" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);">
                            <div class="mb-2"><i class="fas fa-check-circle fa-2x text-success"></i></div>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($claimAnalysis['claimed'] ?? 0) }}</h3>
                            <p class="mb-1 text-muted small fw-semibold">Claimed</p>
                            <span class="badge bg-success">{{ $claimAnalysis['claim_rate'] ?? 0 }}% claim rate</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="p-3 rounded-3 text-center" style="background:linear-gradient(135deg,#fef3c7,#fde68a);">
                            <div class="mb-2"><i class="fas fa-clock fa-2x text-warning"></i></div>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format($claimAnalysis['pending_pickup'] ?? 0) }}</h3>
                            <p class="mb-1 text-muted small fw-semibold">Pending Pickup</p>
                            <span class="badge bg-warning text-dark">Within deadline</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="p-3 rounded-3 text-center" style="background:linear-gradient(135deg,#fee2e2,#fecaca);">
                            <div class="mb-2"><i class="fas fa-exclamation-circle fa-2x text-danger"></i></div>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($claimAnalysis['overdue_unclaimed'] ?? 0) }}</h3>
                            <p class="mb-1 text-muted small fw-semibold">Overdue Unclaimed</p>
                            <span class="badge bg-danger">Past pickup deadline</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="p-3 rounded-3 text-center" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
                            <div class="mb-2"><i class="fas fa-calendar-check fa-2x text-primary"></i></div>
                            <h3 class="mb-0 fw-bold text-primary">{{ $claimAnalysis['avg_days_to_claim'] ?? 0 }}</h3>
                            <p class="mb-1 text-muted small fw-semibold">Avg. Days to Claim</p>
                            <span class="badge bg-primary">After approval</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    {{-- Claim Donut --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-chart-pie me-2 text-success"></i>Claimed vs Unclaimed</h5>
                <small class="text-muted">Breakdown of approved requests</small>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div style="position:relative;height:200px;">
                    <canvas id="claimDonutChart"></canvas>
                </div>
                <div class="mt-3">
                    @foreach([
                        ['color'=>'#10b981','label'=>'Claimed',        'key'=>'claimed'],
                        ['color'=>'#f59e0b','label'=>'Pending Pickup', 'key'=>'pending_pickup'],
                        ['color'=>'#ef4444','label'=>'Overdue',        'key'=>'overdue_unclaimed'],
                    ] as $leg)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded" style="background:#f8fafc;">
                        <span>
                            <span style="width:12px;height:12px;border-radius:50%;display:inline-block;background:{{ $leg['color'] }};margin-right:6px;"></span>
                            {{ $leg['label'] }}
                        </span>
                        <strong>{{ $claimAnalysis[$leg['key']] ?? 0 }}</strong>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Stacked Bar --}}
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-chart-bar me-2 text-primary"></i>Monthly Claimed vs Unclaimed</h5>
                <small class="text-muted">Track pickup completion rate over time</small>
            </div>
            <div class="card-body">
                <canvas id="claimMonthlyChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    {{-- Barangay Claim Rate --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-map-marker-alt me-2 text-info"></i>Claim Rate by Barangay</h5>
                <small class="text-muted">Which barangays have low pickup completion?</small>
            </div>
            <div class="card-body" style="max-height:420px;overflow-y:auto;">
                @forelse($claimAnalysis['barangay_claim_rate'] ?? [] as $row)
                    @php $rate = $row->claim_rate ?? 0;
                         $bc   = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger'); @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">{{ $row->barangay }}</span>
                            <span class="badge bg-{{ $bc }}">{{ $rate }}%</span>
                        </div>
                        <div class="progress" style="height:18px;">
                            <div class="progress-bar bg-{{ $bc }}" role="progressbar" style="width:{{ $rate }}%">
                                {{ $row->claimed }}/{{ $row->total }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4">No data for selected period.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- At-Risk Unclaimed Table --}}
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>At-Risk Unclaimed Requests</h5>
                <small class="text-muted">Soonest pickup deadlines — follow up immediately</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Request #</th>
                                <th>Applicant</th>
                                <th>Barangay</th>
                                <th>Deadline</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($claimAnalysis['unclaimed_list'] ?? [] as $req)
                                @php
                                    $isOverdue   = $req->pickup_expired_at && \Carbon\Carbon::parse($req->pickup_expired_at)->isPast();
                                    $hasDeadline = !empty($req->pickup_expired_at);
                                @endphp
                                <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                    <td class="ps-3"><strong class="text-primary">{{ $req->request_number }}</strong></td>
                                    <td>{{ $req->first_name }} {{ $req->last_name }}</td>
                                    <td>{{ $req->barangay }}</td>
                                    <td>
                                        @if($hasDeadline)
                                            <span class="badge bg-{{ $isOverdue ? 'danger' : 'warning text-dark' }}">
                                                {{ \Carbon\Carbon::parse($req->pickup_expired_at)->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted small">No deadline</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $isOverdue ? 'danger' : 'warning text-dark' }}">
                                            {{ $isOverdue ? 'Overdue' : 'Pending' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-success py-4">
                                        <i class="fas fa-check-circle me-2"></i>All approved requests claimed!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Detailed Performance Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
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
        /* Compact Navigation Container */
        .navigation-container {
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        /* Compact Horizontal Navigation Buttons */
        .analytics-nav-btn {
            background: #ffffff;
            border: 2px solid #e0e0e0;
            color: #495057;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }

        .nav-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-icon-wrapper i {
            font-size: 1rem;
            transition: all 0.2s ease;
            color: #6c757d;
        }

        .nav-label {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .analytics-nav-btn:hover {
            background: #e8f5e9;
            border-color: #40916c;
            color: #2d6a4f;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(64, 145, 108, 0.2);
        }

        .analytics-nav-btn:hover .nav-icon-wrapper i {
            color: #40916c;
        }

        .analytics-nav-btn.active {
            background: linear-gradient(135deg, #40916c 0%, #52b788 100%);
            border-color: #40916c;
            color: white;
            box-shadow: 0 3px 10px rgba(64, 145, 108, 0.3);
        }

        .analytics-nav-btn.active .nav-icon-wrapper i {
            color: #ffffff;
        }

        .analytics-nav-btn.active:hover {
            background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
            border-color: #2d6a4f;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(64, 145, 108, 0.35);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .analytics-nav-btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }

            .nav-icon-wrapper i {
                font-size: 0.9rem;
            }

            .nav-label {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            .analytics-nav-btn {
                padding: 0.45rem 0.8rem;
                font-size: 0.75rem;
                gap: 6px;
            }

            .nav-icon-wrapper i {
                font-size: 0.85rem;
            }

            .nav-label {
                font-size: 0.75rem;
            }
        }

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
            border-radius: 20px;
            padding: 0.5rem 1.25rem;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
            color: #495057;
        }

        .nav-pills .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.8);
            color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.5) !important;
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

        /* Status Legend Styles */
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

        /* Legend Count Badge */
        .legend-count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 2px 8px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            font-size: 12px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Vertical Legend Styles */
        .status-legends-vertical {
            padding: 1rem 0;
        }

        .legend-item-vertical {
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 8px;
        }

        .legend-item-vertical:hover {
            background-color: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .legend-number-badge {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .legend-item-vertical:hover .legend-number-badge {
            transform: scale(1.1);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .legend-percentage {
            font-size: 1.1rem;
            color: #374151;
            margin-left: 0.5rem;
        }

        /* Chart Containers */
        .status-chart-container {
            position: relative;
            height: 220px;
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

            // Register ChartDataLabels plugin
            Chart.register(ChartDataLabels);

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
            const statusLabels = Object.keys(statusData).map(key => key.replace('_', ' ').charAt(0).toUpperCase() +
                key.replace('_', ' ').slice(1));
            const statusValues = Object.values(statusData);
            const statusTotal = statusValues.reduce((a, b) => a + b, 0);

            // Define status colors based on status type
            const statusColors = [];
            const statusNames = Object.keys(statusData);

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
                    default:
                        statusColors.push('#64748b'); // Default gray
                }
            });

            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
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
                                    return `${label}: ${value.toLocaleString()} (${percentage}%)`;
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
                        const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);

                        // Draw center text
                        ctx.save();
                        ctx.font = 'bold 24px Inter';
                        ctx.fillStyle = '#1f2937';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(total.toLocaleString(), centerX, centerY - 10);

                        ctx.font = '14px Inter';
                        ctx.fillStyle = '#64748b';
                        ctx.fillText('Total Requests', centerX, centerY + 15);
                        ctx.restore();
                    },
                    afterDraw: function(chart) {
                        const ctx = chart.ctx;
                        const meta = chart.getDatasetMeta(0);
                        const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);

                        ctx.save();
                        ctx.font = 'bold 14px Inter, sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';

                        chart.data.datasets[0].data.forEach((value, index) => {
                            if (value > 0) {
                                const percentage = ((value / total) * 100).toFixed(1);

                                // Only show percentage if slice is large enough
                                if (percentage > 5) {
                                    const element = meta.data[index];

                                    // Calculate the middle angle of the segment
                                    const startAngle = element.startAngle;
                                    const endAngle = element.endAngle;
                                    const midAngle = (startAngle + endAngle) / 2;

                                    // Calculate position based on the segment's center point
                                    const chartArea = chart.chartArea;
                                    const centerX = (chartArea.left + chartArea.right) /
                                        2;
                                    const centerY = (chartArea.top + chartArea.bottom) /
                                        2;

                                    // Position the text at 70% of the radius from center
                                    const radius = (element.outerRadius - element
                                        .innerRadius) * 0.7 + element.innerRadius;
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

            // Supply Demand Chart
            const supplyDemandCtx = document.getElementById('supplyDemandChart');
            const supplyDemandData = @json($supplyDemandAnalysis);
            const categories = Object.keys(supplyDemandData);
            const demands = categories.map(cat => supplyDemandData[cat].total_demand);

            new Chart(supplyDemandCtx, {
                type: 'bar',
                data: {
                    labels: categories.map(c => c.replaceAll('_', ' ').toUpperCase()),
                    datasets: [{
                        label: 'Total Demand',
                        data: demands,
                        backgroundColor: [colors.success, colors.info, colors.purple, '#6366f1',
                            '#ec4899', colors.orange
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
            const barangayData = @json($barangayAnalysis->toArray());
            const barangayLabels = barangayData.map(b => b.barangay.replaceAll('_', ' '));
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
            const topItemsData = @json($topItems->toArray());
            const itemLabels = topItemsData.map(item => item.name.replaceAll('_', ' '));
            const itemValues = topItemsData.map(item => item.total_quantity);

            new Chart(topItemsCtx, {
                type: 'bar',
                data: {
                    labels: itemLabels,
                    datasets: [{
                        label: 'Total Quantity',
                        data: itemValues,
                        backgroundColor: '#8b5cf6',
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
                // ── Claim Donut Chart ──────────────────────────────────────────
                const claimDonutCtx = document.getElementById('claimDonutChart');
                new Chart(claimDonutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Claimed', 'Pending Pickup', 'Overdue'],
                        datasets: [{
                            data: [
                                {{ $claimAnalysis['claimed'] ?? 0 }},
                                {{ $claimAnalysis['pending_pickup'] ?? 0 }},
                                {{ $claimAnalysis['overdue_unclaimed'] ?? 0 }}
                            ],
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            cutout: '65%',
                            spacing: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                                        const pct   = total > 0 ? ((ctx.parsed/total)*100).toFixed(1) : 0;
                                        return `${ctx.label}: ${ctx.parsed.toLocaleString()} (${pct}%)`;
                                    }
                                }
                            },
                            datalabels: false
                        }
                    },
                    plugins: [{
                        id: 'claimCenter',
                        beforeDraw(chart) {
                            const {ctx, chartArea} = chart;
                            const cx = (chartArea.left + chartArea.right) / 2;
                            const cy = (chartArea.top  + chartArea.bottom) / 2;
                            const total = chart.data.datasets[0].data.reduce((a,b)=>a+b,0);
                            ctx.save();
                            ctx.font = 'bold 22px Inter, sans-serif';
                            ctx.fillStyle = '#1f2937';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(total.toLocaleString(), cx, cy - 10);
                            ctx.font = '12px Inter, sans-serif';
                            ctx.fillStyle = '#64748b';
                            ctx.fillText('Approved', cx, cy + 12);
                            ctx.restore();
                        }
                    }]
                });

                // ── Claim Monthly Stacked Bar ──────────────────────────────────
                const claimMonthlyCtx = document.getElementById('claimMonthlyChart');
                const claimMonthly    = @json($claimAnalysis['monthly_breakdown'] ?? []);
                const claimMonthLabels = claimMonthly.map(m => {
                    const [yr, mo] = m.month.split('-');
                    return new Date(yr, mo-1).toLocaleDateString('en-US', {month:'short', year:'2-digit'});
                });
                new Chart(claimMonthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: claimMonthLabels,
                        datasets: [
                            {
                                label: 'Claimed',
                                data: claimMonthly.map(m => m.claimed),
                                backgroundColor: '#10b981',
                                borderRadius: 4,
                                stack: 'stack'
                            },
                            {
                                label: 'Unclaimed',
                                data: claimMonthly.map(m => m.unclaimed),
                                backgroundColor: '#ef4444',
                                borderRadius: 4,
                                stack: 'stack'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true, position: 'top' },
                            datalabels: {
                                display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                                color: '#fff',
                                font: { weight: 'bold', size: 11 },
                                formatter: v => v
                            }
                        },
                        scales: {
                            x: { stacked: true },
                            y: { stacked: true, beginAtZero: true,
                                ticks: { callback: v => v.toLocaleString() } }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
        });
    </script>
@endsection
