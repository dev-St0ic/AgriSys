{{-- resources/views/admin/analytics/rsbsa.blade.php --}}

@extends('layouts.app')

@section('title', 'RSBSA Analytics - AgriSys Admin')
@section('page-icon', 'fas fa-chart-bar')
@section('page-title', 'RSBSA Analytics Dashboard')

@section('styles')
<style>
    .metric-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border-radius: 12px;
    }
    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
    }
    .analytics-nav-btn {
        background: #fff;
        border: 2px solid #e0e0e0;
        color: #495057;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.55rem 1.1rem;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .analytics-nav-btn:hover {
        background: #e8f5e9;
        border-color: #40916c;
        color: #2d6a4f;
        text-decoration: none;
        transform: translateY(-2px);
    }
    .analytics-nav-btn.active {
        background: linear-gradient(135deg, #40916c 0%, #52b788 100%);
        border-color: #40916c;
        color: #fff;
    }
    .section-divider {
        border: none;
        border-top: 2px dashed #e9ecef;
        margin: 2rem 0;
    }
    .insight-badge {
        font-size: 0.72rem;
        padding: 3px 8px;
        border-radius: 20px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .stat-row:last-child { border-bottom: none; }
    .progress { border-radius: 8px; background: #f1f3f4; }
    .chart-wrapper { position: relative; width: 100%; }
</style>
@endsection

@section('content')

    {{-- ── Service Navigation ──────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col-12">
            @include('admin.analytics.partials.nav')
        </div>
    </div>

    {{-- ── Date Range Filter ───────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col-12">
            @include('admin.analytics.partials.filter', [
                'filterRoute' => 'admin.analytics.rsbsa',
            ])
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 1 — KEY METRICS
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm metric-card h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2"><i class="fas fa-file-alt fa-2x text-primary"></i></div>
                    <h2 class="fw-bold text-dark mb-1">{{ number_format($overview['total_applications']) }}</h2>
                    <div class="text-muted small mb-2">Total Applications</div>
                    <span class="insight-badge bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-users me-1"></i>{{ number_format($overview['unique_applicants']) }} unique farmers
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm metric-card h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2"><i class="fas fa-check-circle fa-2x text-success"></i></div>
                    <h2 class="fw-bold text-dark mb-1">{{ $overview['approval_rate'] }}%</h2>
                    <div class="text-muted small mb-2">Approval Rate</div>
                    @php
                        $approvalClass = $overview['approval_rate'] >= 70 ? 'success' : ($overview['approval_rate'] >= 40 ? 'warning' : 'danger');
                        $approvalLabel = $overview['approval_rate'] >= 70 ? 'High' : ($overview['approval_rate'] >= 40 ? 'Moderate' : 'Low');
                    @endphp
                    <span class="insight-badge bg-{{ $approvalClass }} bg-opacity-10 text-{{ $approvalClass }}">
                        {{ $approvalLabel }} &bull; {{ number_format($overview['approved_applications']) }} approved
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm metric-card h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2"><i class="fas fa-map-marked-alt fa-2x" style="color:#7c3aed"></i></div>
                    <h2 class="fw-bold text-dark mb-1">{{ number_format($overview['total_land_area'], 1) }} ha</h2>
                    <div class="text-muted small mb-2">Total Land Coverage</div>
                    <span class="insight-badge bg-purple bg-opacity-10" style="background:rgba(124,58,237,.1);color:#7c3aed">
                        <i class="fas fa-map-pin me-1"></i>{{ $overview['active_barangays'] }} barangays covered
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm metric-card h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2"><i class="fas fa-clock fa-2x text-warning"></i></div>
                    <h2 class="fw-bold text-dark mb-1">{{ $processingTimeAnalysis['avg_processing_days'] }}d</h2>
                    <div class="text-muted small mb-2">Avg Processing Time</div>
                    @php
                        $speedClass = $processingTimeAnalysis['avg_processing_days'] <= 7 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] <= 14 ? 'warning' : 'danger');
                        $speedLabel = $processingTimeAnalysis['avg_processing_days'] <= 7 ? 'Fast' : ($processingTimeAnalysis['avg_processing_days'] <= 14 ? 'Moderate' : 'Slow');
                    @endphp
                    <span class="insight-badge bg-{{ $speedClass }} bg-opacity-10 text-{{ $speedClass }}">
                        {{ $speedLabel }} &bull; {{ $processingTimeAnalysis['median_processing_days'] }}d median
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 2 — STATUS + TRENDS
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Status Doughnut --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Application Status
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    @if($statusAnalysis['total'] > 0)
                        <div class="chart-wrapper mb-3" style="height:220px">
                            <canvas id="rsbsaStatusChart"></canvas>
                        </div>
                        <div class="mt-auto">
                            @foreach($statusAnalysis['counts'] as $status => $count)
                                @php
                                    $hex = match($status) {
                                        'approved'    => '#10b981',
                                        'rejected'    => '#ef4444',
                                        default       => '#f59e0b',
                                    };
                                    $pct = $statusAnalysis['percentages'][$status] ?? 0;
                                @endphp
                                <div class="stat-row">
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="width:10px;height:10px;border-radius:2px;background:{{ $hex }};display:inline-block"></span>
                                        <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold">{{ number_format($count) }}</span>
                                        <span class="text-muted small">({{ $pct }}%)</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted flex-grow-1 d-flex flex-column justify-content-center">
                            <i class="fas fa-chart-pie fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No data for selected period</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Monthly Trends Line Chart --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line text-info me-2"></i>Monthly Application Trends
                    </h5>
                    @if($monthlyTrends->isNotEmpty())
                        <span class="badge bg-info bg-opacity-10 text-info">
                            {{ $monthlyTrends->count() }} month{{ $monthlyTrends->count() > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    @if($monthlyTrends->isNotEmpty())
                        <div class="chart-wrapper" style="height:260px">
                            <canvas id="rsbsaTrendsChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-chart-line fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No trend data for selected period</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 3 — LIVELIHOOD + COMMODITY
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Livelihood Bar Chart --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-briefcase text-success me-2"></i>Main Livelihood Distribution
                    </h5>
                </div>
                <div class="card-body">
                    @if($livelihoodAnalysis->isNotEmpty())
                        @php $totalLivelihood = $livelihoodAnalysis->sum('total_applications'); @endphp
                        @foreach($livelihoodAnalysis as $l)
                            @php
                                $pct = $totalLivelihood > 0 ? round(($l->total_applications / $totalLivelihood) * 100, 1) : 0;
                                $approvalRate = round(($l->approved / max(1, $l->total_applications)) * 100, 1);
                                $approvalColor = $approvalRate >= 70 ? 'success' : ($approvalRate >= 40 ? 'warning' : 'danger');
                                $icon = match($l->main_livelihood) {
                                    'Farmer'              => 'fa-tractor',
                                    'Farmworker/Laborer'  => 'fa-hard-hat',
                                    'Fisherfolk'          => 'fa-fish',
                                    'Agri-youth'          => 'fa-user-graduate',
                                    default               => 'fa-leaf',
                                };
                            @endphp
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas {{ $icon }} text-success small"></i>
                                        <span class="fw-semibold">{{ $l->main_livelihood }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-pill">{{ number_format($l->total_applications) }}</span>
                                        <span class="badge bg-{{ $approvalColor }} bg-opacity-15 text-{{ $approvalColor }} rounded-pill">
                                            {{ $approvalRate }}% approved
                                        </span>
                                    </div>
                                </div>
                                <div class="progress" style="height:8px">
                                    <div class="progress-bar bg-success" style="width:{{ $pct }}%" role="progressbar"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mt-1">
                                    <span>{{ $pct }}% of total</span>
                                    @if($l->total_land_area > 0)
                                        <span>{{ round($l->total_land_area, 1) }} ha covered</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                            <p class="mb-0">No livelihood data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Top Commodities --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-seedling text-success me-2"></i>Top Commodities
                    </h5>
                </div>
                <div class="card-body">
                    @if($commodityAnalysis->isNotEmpty())
                        @php $maxCommodity = $commodityAnalysis->first()->total_applications; @endphp
                        @foreach($commodityAnalysis->take(6) as $commodity)
                            @php
                                $cpct = $maxCommodity > 0 ? round(($commodity->total_applications / $maxCommodity) * 100, 1) : 0;
                                $capRate = round(($commodity->approved / max(1, $commodity->total_applications)) * 100, 1);
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">{{ ucfirst($commodity->commodity) }}</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success rounded-pill">{{ number_format($commodity->total_applications) }}</span>
                                        <span class="text-muted small">{{ $capRate }}% ✓</span>
                                    </div>
                                </div>
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar bg-primary" style="width:{{ $cpct }}%" role="progressbar"></div>
                                </div>
                                <div class="small text-muted mt-1">
                                    {{ round($commodity->total_land_area, 1) }} ha &bull; {{ $commodity->unique_barangays }} barangay{{ $commodity->unique_barangays != 1 ? 's' : '' }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                            <p class="mb-0">No commodity data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 4 — GENDER + DOCUMENT ANALYSIS (chart-driven)
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Gender Doughnut --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-venus-mars text-info me-2"></i>Gender Distribution
                    </h5>
                </div>
                <div class="card-body">
                    @if($genderAnalysis['total'] > 0)
                        <div class="chart-wrapper mb-3" style="height:200px">
                            <canvas id="rsbsaGenderChart"></canvas>
                        </div>
                        @foreach($genderAnalysis['stats'] as $g)
                            @php
                                $gpct = $genderAnalysis['percentages'][$g->sex] ?? 0;
                                $ghex = match($g->sex) {
                                    'Male'   => '#3b82f6',
                                    'Female' => '#ec4899',
                                    default  => '#94a3b8',
                                };
                            @endphp
                            <div class="stat-row">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width:10px;height:10px;border-radius:50%;background:{{ $ghex }};display:inline-block"></span>
                                    <span class="fw-medium">{{ $g->sex }}</span>
                                </div>
                                <div>
                                    <span class="fw-bold">{{ number_format($g->total_applications) }}</span>
                                    <span class="text-muted small ms-1">({{ $gpct }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-venus-mars fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No gender data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Document Analysis --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-paperclip text-warning me-2"></i>Document Submission
                    </h5>
                </div>
                <div class="card-body">
                    @if($documentAnalysis['total'] > 0)
                        <div class="chart-wrapper mb-3" style="height:200px">
                            <canvas id="rsbsaDocChart"></canvas>
                        </div>
                        <div class="stat-row">
                            <span class="fw-medium">Submission rate</span>
                            <span class="fw-bold text-success">{{ $documentAnalysis['submission_rate'] }}%</span>
                        </div>
                        <div class="stat-row">
                            <span class="fw-medium">Approval with docs</span>
                            <span class="fw-bold text-primary">{{ $documentAnalysis['approval_rate_with_docs'] }}%</span>
                        </div>
                        <div class="stat-row">
                            <span class="fw-medium">Approval without docs</span>
                            <span class="fw-bold text-secondary">{{ $documentAnalysis['approval_rate_without_docs'] }}%</span>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-paperclip fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No document data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Performance Metrics --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-tachometer-alt text-danger me-2"></i>Performance Metrics
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $completionColor = $performanceMetrics['completion_rate'] >= 70 ? 'success' : ($performanceMetrics['completion_rate'] >= 40 ? 'warning' : 'danger');
                        $qualityColor = $performanceMetrics['quality_score'] >= 70 ? 'success' : ($performanceMetrics['quality_score'] >= 40 ? 'warning' : 'danger');
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium small">Completion Rate</span>
                            <span class="fw-bold small text-{{ $completionColor }}">{{ $performanceMetrics['completion_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height:8px">
                            <div class="progress-bar bg-{{ $completionColor }}" style="width:{{ $performanceMetrics['completion_rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-medium small">Quality Score</span>
                            <span class="fw-bold small text-{{ $qualityColor }}">{{ $performanceMetrics['quality_score'] }}%</span>
                        </div>
                        <div class="progress" style="height:8px">
                            <div class="progress-bar bg-{{ $qualityColor }}" style="width:{{ $performanceMetrics['quality_score'] }}%"></div>
                        </div>
                        <div class="text-muted small mt-1">Based on approval rate + doc submission rate</div>
                    </div>
                    <div class="stat-row">
                        <span class="fw-medium">Avg applications/day</span>
                        <span class="fw-bold">{{ $performanceMetrics['avg_applications_per_day'] }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="fw-medium">Land area impact</span>
                        <span class="fw-bold text-success">{{ number_format($performanceMetrics['total_land_impact'], 1) }} ha</span>
                    </div>
                    <div class="stat-row">
                        <span class="fw-medium">Pending / Under review</span>
                        <span class="fw-bold text-warning">{{ number_format($overview['pending_applications']) }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="fw-medium">Rejected</span>
                        <span class="fw-bold text-danger">{{ number_format($overview['rejected_applications']) }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 5 — TOP BARANGAYS TABLE
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-trophy text-warning me-2"></i>Top Barangays by Registration
                    </h5>
                    @if($barangayAnalysis->isNotEmpty())
                        <span class="badge bg-warning bg-opacity-10 text-warning">
                            {{ $barangayAnalysis->count() }} barangay{{ $barangayAnalysis->count() != 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($barangayAnalysis->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Rank</th>
                                        <th>Barangay</th>
                                        <th class="text-center">Applications</th>
                                        <th class="text-center">Approved</th>
                                        <th>Approval Rate</th>
                                        <th class="text-center">Land Area</th>
                                        <th class="text-center">Commodities</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangayAnalysis as $index => $barangay)
                                        @php
                                            $rate = round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1);
                                            $rateColor = $rate >= 70 ? 'success' : ($rate >= 40 ? 'warning' : 'danger');
                                            $rankBadge = match($index) {
                                                0 => 'warning',
                                                1 => 'secondary',
                                                2 => 'info',
                                                default => 'light text-dark'
                                            };
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge bg-{{ $rankBadge }} rounded-pill px-3">#{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-semibold">{{ $barangay->barangay }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill">{{ number_format($barangay->total_applications) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success rounded-pill">{{ number_format($barangay->approved) }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height:7px;max-width:90px">
                                                        <div class="progress-bar bg-{{ $rateColor }}" style="width:{{ $rate }}%"></div>
                                                    </div>
                                                    <small class="fw-semibold text-{{ $rateColor }}">{{ $rate }}%</small>
                                                </div>
                                            </td>
                                            <td class="text-center fw-semibold text-success">{{ round($barangay->total_land_area, 1) }} ha</td>
                                            <td class="text-center">
                                                <span class="badge bg-info bg-opacity-15 text-info">{{ $barangay->commodities_grown }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-map-marker-alt fa-2x mb-2 opacity-25"></i>
                            <p class="mb-0">No barangay data available for the selected period</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <hr class="section-divider">

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 6 — FARMER DETAILS
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="row mb-3">
        <div class="col-12">
            <h6 class="text-muted fw-semibold text-uppercase letter-spacing-1">
                <i class="fas fa-tractor me-2 text-success"></i>Farmer Details
                <span class="ms-2 badge bg-success bg-opacity-10 text-success fw-normal">{{ $farmerDetails['total_farmers'] }} farmers</span>
            </h6>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-2">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-water me-2 text-info"></i>Type of Farm</h6>
                </div>
                <div class="card-body pt-2">
                    @if($farmerDetails['total_farmers'] > 0)
                        <div class="chart-wrapper mb-3" style="height:160px">
                            <canvas id="rsbsaFarmTypeChart"></canvas>
                        </div>
                    @endif
                    @forelse($farmerDetails['by_type_of_farm'] as $item)
                        @php $tpct = $farmerDetails['total_farmers'] > 0 ? round(($item->count / $farmerDetails['total_farmers']) * 100, 1) : 0; @endphp
                        <div class="stat-row">
                            <span class="fw-medium">{{ $item->farmer_type_of_farm }}</span>
                            <div>
                                <span class="fw-bold">{{ number_format($item->count) }}</span>
                                <span class="text-muted small ms-1">{{ $tpct }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 small">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-2">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-file-contract me-2 text-warning"></i>Land Ownership</h6>
                </div>
                <div class="card-body pt-2">
                    @if($farmerDetails['total_farmers'] > 0)
                        <div class="chart-wrapper mb-3" style="height:160px">
                            <canvas id="rsbsaLandOwnershipChart"></canvas>
                        </div>
                    @endif
                    @forelse($farmerDetails['by_land_ownership'] as $item)
                        @php $opct = $farmerDetails['total_farmers'] > 0 ? round(($item->count / $farmerDetails['total_farmers']) * 100, 1) : 0; @endphp
                        <div class="stat-row">
                            <span class="fw-medium">{{ $item->farmer_land_ownership }}</span>
                            <div>
                                <span class="fw-bold">{{ number_format($item->count) }}</span>
                                <span class="text-muted small ms-1">{{ $opct }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 small">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 7 — CROPS + FARMWORKER
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-seedling me-2 text-success"></i>Farmer Crops</h6>
                    <span class="badge bg-success bg-opacity-10 text-success">{{ $farmerCrops['total_crop_types'] }} types</span>
                </div>
                <div class="card-body pt-2">
                    @if($farmerCrops['distribution']->isNotEmpty())
                        @php $maxCrop = $farmerCrops['distribution']->first()->count; @endphp
                        @foreach($farmerCrops['distribution'] as $crop)
                            @php $cpct = $maxCrop > 0 ? round(($crop->count / $maxCrop) * 100) : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-medium">{{ ucfirst($crop->farmer_crops) }}</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge bg-success rounded-pill">{{ number_format($crop->count) }}</span>
                                        <span class="text-muted small">{{ round($crop->total_land, 1) }} ha</span>
                                    </div>
                                </div>
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar bg-success" style="width:{{ $cpct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                        @if($farmerCrops['other_crops_specified']->isNotEmpty())
                            <div class="border-top pt-2 mt-2">
                                <p class="text-muted small fw-semibold mb-2">Other specified crops:</p>
                                @foreach($farmerCrops['other_crops_specified'] as $other)
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>{{ ucfirst($other->farmer_other_crops) }}</span>
                                        <span class="text-muted">{{ $other->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center py-3 small">No crop data available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-hard-hat me-2 text-warning"></i>Farmworker Types</h6>
                    <span class="badge bg-warning bg-opacity-10 text-warning">{{ number_format($farmworkerType['total_farmworkers']) }} total</span>
                </div>
                <div class="card-body pt-2">
                    @if($farmworkerType['distribution']->isNotEmpty())
                        @php $maxFw = $farmworkerType['distribution']->first()->count; @endphp
                        @foreach($farmworkerType['distribution'] as $type)
                            @php $fpct = $maxFw > 0 ? round(($type->count / $maxFw) * 100) : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-medium">{{ ucfirst($type->farmworker_type) }}</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge bg-warning text-dark rounded-pill">{{ number_format($type->count) }}</span>
                                        <span class="text-muted small">{{ $type->approved }} approved</span>
                                    </div>
                                </div>
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar bg-warning" style="width:{{ $fpct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                        @if($farmworkerType['other_types_specified']->isNotEmpty())
                            <div class="border-top pt-2 mt-2">
                                <p class="text-muted small fw-semibold mb-2">Other specified types:</p>
                                @foreach($farmworkerType['other_types_specified'] as $other)
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>{{ ucfirst($other->farmworker_other_type) }}</span>
                                        <span class="text-muted">{{ $other->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center py-3 small">No farmworker data available</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SECTION 8 — FISHERFOLK + AGRI-YOUTH
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-fish me-2 text-info"></i>Fisherfolk Activity</h6>
                    <span class="badge bg-info bg-opacity-10 text-info">{{ number_format($fisherfolkActivity['total_fisherfolk']) }} total</span>
                </div>
                <div class="card-body pt-2">
                    @if($fisherfolkActivity['distribution']->isNotEmpty())
                        @php $maxFish = $fisherfolkActivity['distribution']->first()->count; @endphp
                        @foreach($fisherfolkActivity['distribution'] as $activity)
                            @php $apct = $maxFish > 0 ? round(($activity->count / $maxFish) * 100) : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-medium">{{ ucfirst($activity->fisherfolk_activity) }}</span>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge bg-info rounded-pill">{{ number_format($activity->count) }}</span>
                                        <span class="text-muted small">{{ $activity->approved }} approved</span>
                                    </div>
                                </div>
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar bg-info" style="width:{{ $apct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                        @if($fisherfolkActivity['other_activities_specified']->isNotEmpty())
                            <div class="border-top pt-2 mt-2">
                                <p class="text-muted small fw-semibold mb-2">Other activities specified:</p>
                                @foreach($fisherfolkActivity['other_activities_specified'] as $other)
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>{{ ucfirst($other->fisherfolk_other_activity) }}</span>
                                        <span class="text-muted">{{ $other->count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center py-3 small">No fisherfolk data available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-user-graduate me-2 text-primary"></i>Agri-Youth</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ number_format($agriyouthAnalysis['total']) }} total</span>
                </div>
                <div class="card-body pt-2">
                    @if($agriyouthAnalysis['total'] > 0)
                        {{-- Gender breakdown mini stats --}}
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="text-center bg-light rounded p-2">
                                    <div class="h5 text-primary mb-0 fw-bold">{{ $agriyouthAnalysis['total'] }}</div>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center bg-light rounded p-2">
                                    <div class="h5 text-info mb-0 fw-bold">{{ $agriyouthAnalysis['male_count'] }}</div>
                                    <small class="text-muted">Male</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center bg-light rounded p-2">
                                    <div class="h5 text-danger mb-0 fw-bold">{{ $agriyouthAnalysis['female_count'] }}</div>
                                    <small class="text-muted">Female</small>
                                </div>
                            </div>
                        </div>

                        @php
                            $ayApprovalColor = $agriyouthAnalysis['approval_rate'] >= 70 ? 'success' : ($agriyouthAnalysis['approval_rate'] >= 40 ? 'warning' : 'danger');
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium small">Approval Rate</span>
                                <span class="fw-bold small text-{{ $ayApprovalColor }}">{{ $agriyouthAnalysis['approval_rate'] }}%</span>
                            </div>
                            <div class="progress" style="height:7px">
                                <div class="progress-bar bg-{{ $ayApprovalColor }}" style="width:{{ $agriyouthAnalysis['approval_rate'] }}%"></div>
                            </div>
                        </div>

                        @if($agriyouthAnalysis['by_farming_household']->isNotEmpty())
                            <p class="text-muted small fw-semibold mb-1">From farming household:</p>
                            @foreach($agriyouthAnalysis['by_farming_household'] as $item)
                                <div class="stat-row">
                                    <span class="small">{{ $item->agriyouth_farming_household }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $item->count }}</span>
                                </div>
                            @endforeach
                        @endif
                        @if($agriyouthAnalysis['by_participation']->isNotEmpty())
                            <p class="text-muted small fw-semibold mb-1 mt-2">Participation:</p>
                            @foreach($agriyouthAnalysis['by_participation'] as $item)
                                <div class="stat-row">
                                    <span class="small">{{ $item->agriyouth_participation }}</span>
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $item->count }}</span>
                                </div>
                            @endforeach
                        @endif
                    @else
                        <p class="text-muted text-center py-3 small">No agri-youth data available</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

{{-- ── END OF CONTENT ──────────────────────────────────────── --}}
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';

    /* ─── helpers ──────────────────────────────────────────────── */
    function doughnut(id, labels, data, colors) {
        const el = document.getElementById(id);
        if (!el) return;
        const total = data.reduce((a, b) => a + b, 0);
        return new Chart(el, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    cutout: '62%',
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
                                const pct = ((ctx.parsed / total) * 100).toFixed(1);
                                return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw(chart) {
                    const { ctx, chartArea: { left, right, top, bottom } } = chart;
                    const cx = (left + right) / 2, cy = (top + bottom) / 2;
                    ctx.save();
                    ctx.font = 'bold 20px Inter';
                    ctx.fillStyle = '#1f2937';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(total.toLocaleString(), cx, cy - 8);
                    ctx.font = '11px Inter';
                    ctx.fillStyle = '#94a3b8';
                    ctx.fillText('total', cx, cy + 10);
                    ctx.restore();
                }
            }]
        });
    }

    function horizontalBar(id, labels, data, color) {
        const el = document.getElementById(id);
        if (!el) return;
        return new Chart(el, {
            type: 'bar',
            data: {
                labels,
                datasets: [{ data, backgroundColor: color, borderRadius: 4, borderSkipped: false }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.x}` } }
                },
                scales: {
                    x: { beginAtZero: true, grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    /* ─── 1. Status doughnut ──────────────────────────────────── */
    @if($statusAnalysis['total'] > 0)
    (function () {
        const labels = @json(array_keys($statusAnalysis['counts']));
        const data   = @json(array_values($statusAnalysis['counts']));
        const colorMap = { approved: '#10b981', rejected: '#ef4444', pending: '#f59e0b', under_review: '#f59e0b' };
        const colors = labels.map(l => colorMap[l] ?? '#94a3b8');
        doughnut('rsbsaStatusChart', labels.map(l => l.replace('_', ' ')), data, colors);
    })();
    @endif

    /* ─── 2. Trends line chart ────────────────────────────────── */
    @if($monthlyTrends->isNotEmpty())
    (function () {
        const labels   = @json($monthlyTrends->map(fn($t) => \Carbon\Carbon::createFromFormat('Y-m', $t->month)->format('M Y')));
        const totals   = @json($monthlyTrends->pluck('total_applications'));
        const approved = @json($monthlyTrends->pluck('approved'));
        const pending  = @json($monthlyTrends->pluck('pending'));
        const land     = @json($monthlyTrends->pluck('total_land_area'));

        new Chart(document.getElementById('rsbsaTrendsChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Total',
                        data: totals,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Approved',
                        data: approved,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.06)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Pending',
                        data: pending,
                        borderColor: '#f59e0b',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        borderDash: [4, 3],
                        tension: 0.4,
                        fill: false,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Land (ha)',
                        data: land,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        tension: 0.4,
                        fill: false,
                        pointRadius: 3,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, padding: 16, font: { size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, autoSkip: false, maxRotation: 40 }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { size: 11 } },
                        title: { display: true, text: 'Applications', font: { size: 11 } }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: { font: { size: 11 } },
                        title: { display: true, text: 'Land (ha)', font: { size: 11 } }
                    }
                }
            }
        });
    })();
    @endif

    /* ─── 3. Gender doughnut ──────────────────────────────────── */
    @if($genderAnalysis['total'] > 0)
    (function () {
        const labels = @json($genderAnalysis['stats']->pluck('sex')->toArray());
        const data   = @json($genderAnalysis['stats']->pluck('total_applications')->toArray());
        const colorMap = { Male: '#3b82f6', Female: '#ec4899', 'Preferred not to say': '#94a3b8' };
        doughnut('rsbsaGenderChart', labels, data, labels.map(l => colorMap[l] ?? '#94a3b8'));
    })();
    @endif

    /* ─── 4. Document doughnut ────────────────────────────────── */
    @if($documentAnalysis['total'] > 0)
    (function () {
        doughnut(
            'rsbsaDocChart',
            ['With Documents', 'Without Documents'],
            [{{ $documentAnalysis['with_documents'] }}, {{ $documentAnalysis['without_documents'] }}],
            ['#10b981', '#e5e7eb']
        );
    })();
    @endif

    /* ─── 5. Farm type horizontal bar ────────────────────────── */
    @if($farmerDetails['by_type_of_farm']->isNotEmpty())
    (function () {
        const labels = @json($farmerDetails['by_type_of_farm']->pluck('farmer_type_of_farm')->toArray());
        const data   = @json($farmerDetails['by_type_of_farm']->pluck('count')->toArray());
        horizontalBar('rsbsaFarmTypeChart', labels, data, '#0ea5e9');
    })();
    @endif

    /* ─── 6. Land ownership horizontal bar ───────────────────── */
    @if($farmerDetails['by_land_ownership']->isNotEmpty())
    (function () {
        const labels = @json($farmerDetails['by_land_ownership']->pluck('farmer_land_ownership')->toArray());
        const data   = @json($farmerDetails['by_land_ownership']->pluck('count')->toArray());
        horizontalBar('rsbsaLandOwnershipChart', labels, data, '#f59e0b');
    })();
    @endif

});
</script>
@endsection