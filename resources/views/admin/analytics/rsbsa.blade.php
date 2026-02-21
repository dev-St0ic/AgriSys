{{-- resources/views/admin/analytics/rsbsa.blade.php --}}

@extends('layouts.app')

@section('title', 'RSBSA Analytics - AgriSys Admin')
@section('page-icon', 'fas fa-chart-bar')
@section('page-title', 'RSBSA Analytics Dashboard')

@section('content')
    <!-- Enhanced Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            @include('admin.analytics.partials.nav')
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            @include('admin.analytics.partials.filter', [
                'filterRoute' => 'admin.analytics.rsbsa',
                'exportRoute' => 'admin.analytics.rsbsa.export',
            ])
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
                    @if ($commodityAnalysis->isNotEmpty())
                        @foreach ($commodityAnalysis->take(5) as $index => $commodity)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-semibold">{{ ucfirst($commodity->commodity) }}</h6>
                                    <span
                                        class="badge bg-primary text-white rounded-pill px-3">{{ $commodity->total_applications }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ $commodityAnalysis->first()->total_applications > 0 ? ($commodity->total_applications / $commodityAnalysis->first()->total_applications) * 100 : 0 }}%"
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
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No commodity data available for the selected period</p>
                        </div>
                    @endif
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
                    @if ($livelihoodAnalysis->isNotEmpty())
                        @foreach ($livelihoodAnalysis->take(5) as $index => $livelihood)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-semibold">{{ ucfirst($livelihood->main_livelihood) }}</h6>
                                    <span
                                        class="badge bg-primary text-white rounded-pill px-3">{{ $livelihood->total_applications }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ $livelihoodAnalysis->first()->total_applications > 0 ? ($livelihood->total_applications / $livelihoodAnalysis->first()->total_applications) * 100 : 0 }}%"
                                        role="progressbar"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Approval:
                                        {{ round(($livelihood->approved / max(1, $livelihood->total_applications)) * 100, 1) }}%</span>
                                    <span>Share:
                                        {{ round(($livelihood->total_applications / max(1, $livelihoodAnalysis->sum('total_applications'))) * 100, 1) }}%
                                        of total</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No livelihood data available for the selected period</p>
                        </div>
                    @endif
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
                                @forelse ($barangayAnalysis as $index => $barangay)
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
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-map-marker-alt fa-2x mb-2 d-block"></i>
                                            No barangay data available for the selected period
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

    {{-- ── Farmer Details ─────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-tractor me-2 text-success"></i>Type of Farm</h5>
                    <small class="text-muted">Farmers only &bull; {{ $farmerDetails['total_farmers'] }} total</small>
                </div>
                <div class="card-body">
                    @forelse ($farmerDetails['by_type_of_farm'] as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">{{ ucfirst($item->farmer_type_of_farm) }}</span>
                            <span class="badge bg-success rounded-pill">{{ $item->count }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-file-contract me-2 text-warning"></i>Land Ownership</h5>
                    <small class="text-muted">Farmers only</small>
                </div>
                <div class="card-body">
                    @forelse ($farmerDetails['by_land_ownership'] as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">{{ ucfirst($item->farmer_land_ownership) }}</span>
                            <span class="badge bg-warning text-dark rounded-pill">{{ $item->count }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-star me-2 text-info"></i>Special Status</h5>
                    <small class="text-muted">Ancestral Domain / ARB</small>
                </div>
                <div class="card-body">
                    @forelse ($farmerDetails['by_special_status'] as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">{{ ucfirst($item->farmer_special_status) }}</span>
                            <span class="badge bg-info rounded-pill">{{ $item->count }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ── Farmer Crops & Farmworker Type ──────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-seedling me-2 text-success"></i>Farmer Crops Breakdown</h5>
                    <small class="text-muted">By crop type &bull; {{ $farmerCrops['total_crop_types'] }} types</small>
                </div>
                <div class="card-body">
                    @forelse ($farmerCrops['distribution'] as $crop)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ ucfirst($crop->farmer_crops) }}</span>
                                <span class="badge bg-success rounded-pill">{{ $crop->count }}</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span>Land: {{ round($crop->total_land, 2) }} ha</span>
                                <span>Approved: {{ $crop->approved }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No crop data available</p>
                    @endforelse
                    @if ($farmerCrops['other_crops_specified']->isNotEmpty())
                        <hr>
                        <small class="text-muted fw-semibold">Other Crops Specified:</small>
                        @foreach ($farmerCrops['other_crops_specified'] as $other)
                            <div class="d-flex justify-content-between small mt-1">
                                <span>{{ ucfirst($other->farmer_other_crops) }}</span>
                                <span class="text-muted">{{ $other->count }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-hard-hat me-2 text-warning"></i>Farmworker Type</h5>
                    <small class="text-muted">Farmworker/Laborer only &bull; {{ $farmworkerType['total_farmworkers'] }}
                        total</small>
                </div>
                <div class="card-body">
                    @forelse ($farmworkerType['distribution'] as $type)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ ucfirst($type->farmworker_type) }}</span>
                                <span class="badge bg-warning text-dark rounded-pill">{{ $type->count }}</span>
                            </div>
                            <div class="small text-muted">Approved: {{ $type->approved }} / {{ $type->count }}</div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No farmworker data available</p>
                    @endforelse
                    @if ($farmworkerType['other_types_specified']->isNotEmpty())
                        <hr>
                        <small class="text-muted fw-semibold">Other Types Specified:</small>
                        @foreach ($farmworkerType['other_types_specified'] as $other)
                            <div class="d-flex justify-content-between small mt-1">
                                <span>{{ ucfirst($other->farmworker_other_type) }}</span>
                                <span class="text-muted">{{ $other->count }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Fisherfolk Activity & Agri-youth ────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-fish me-2 text-info"></i>Fisherfolk Activity</h5>
                    <small class="text-muted">RSBSA Fisherfolk &bull; {{ $fisherfolkActivity['total_fisherfolk'] }}
                        total</small>
                </div>
                <div class="card-body">
                    @forelse ($fisherfolkActivity['distribution'] as $activity)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ ucfirst($activity->fisherfolk_activity) }}</span>
                                <span class="badge bg-info rounded-pill">{{ $activity->count }}</span>
                            </div>
                            <div class="small text-muted">Approved: {{ $activity->approved }} / {{ $activity->count }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No fisherfolk activity data available</p>
                    @endforelse
                    @if ($fisherfolkActivity['other_activities_specified']->isNotEmpty())
                        <hr>
                        <small class="text-muted fw-semibold">Other Activities Specified:</small>
                        @foreach ($fisherfolkActivity['other_activities_specified'] as $other)
                            <div class="d-flex justify-content-between small mt-1">
                                <span>{{ ucfirst($other->fisherfolk_other_activity) }}</span>
                                <span class="text-muted">{{ $other->count }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-graduate me-2 text-primary"></i>Agri-Youth Analysis
                    </h5>
                    <small class="text-muted">{{ $agriyouthAnalysis['total'] }} total &bull;
                        {{ $agriyouthAnalysis['approval_rate'] }}% approval</small>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="text-center bg-light rounded p-2">
                                <div class="h5 text-primary mb-0">{{ $agriyouthAnalysis['total'] }}</div>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center bg-light rounded p-2">
                                <div class="h5 text-info mb-0">{{ $agriyouthAnalysis['male_count'] }}</div>
                                <small class="text-muted">Male</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center bg-light rounded p-2">
                                <div class="h5 text-danger mb-0">{{ $agriyouthAnalysis['female_count'] }}</div>
                                <small class="text-muted">Female</small>
                            </div>
                        </div>
                    </div>
                    @if ($agriyouthAnalysis['by_farming_household']->isNotEmpty())
                        <h6 class="fw-semibold text-muted">Farming Household</h6>
                        @foreach ($agriyouthAnalysis['by_farming_household'] as $item)
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ ucfirst($item->agriyouth_farming_household) }}</span>
                                <span class="badge bg-primary rounded-pill">{{ $item->count }}</span>
                            </div>
                        @endforeach
                    @endif
                    @if ($agriyouthAnalysis['by_training']->isNotEmpty())
                        <h6 class="fw-semibold text-muted mt-2">Training</h6>
                        @foreach ($agriyouthAnalysis['by_training'] as $item)
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ ucfirst($item->agriyouth_training) }}</span>
                                <span class="badge bg-success rounded-pill">{{ $item->count }}</span>
                            </div>
                        @endforeach
                    @endif
                    @if ($agriyouthAnalysis['by_participation']->isNotEmpty())
                        <h6 class="fw-semibold text-muted mt-2">Participation</h6>
                        @foreach ($agriyouthAnalysis['by_participation'] as $item)
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ ucfirst($item->agriyouth_participation) }}</span>
                                <span class="badge bg-warning text-dark rounded-pill">{{ $item->count }}</span>
                            </div>
                        @endforeach
                    @endif
                    @if ($agriyouthAnalysis['total'] === 0)
                        <p class="text-muted text-center py-3">No agri-youth data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rsbsaInsightsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
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
                            const totalCount = statusData.reduce((a, b) => a + b, 0);

                            @if ($statusAnalysis['total'] == 0)
                                // Display message when no data available
                                ctx.parentElement.innerHTML =
                                    '<div class="text-center py-5"><i class="fas fa-chart-pie fa-3x text-muted mb-3"></i><p class="text-muted">No status data available</p></div>';
                                return;
                            @endif

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

                            @if ($monthlyTrends->isEmpty())
                                // Display message when no data available
                                ctx.parentElement.innerHTML =
                                    '<div class="text-center py-5"><i class="fas fa-chart-line fa-3x text-muted mb-3"></i><p class="text-muted">No trend data available for the selected period</p></div>';
                                return;
                            @endif

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
