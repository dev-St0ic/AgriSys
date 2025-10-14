{{-- resources/views/admin/analytics/user-registration.blade.php --}}

@extends('layouts.app')

@section('title', 'User Registration Analytics - AgriSys Admin')
@section('page-title', 'User Registration Analytics Dashboard')

@section('content')
    <!-- Header with Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h4 class="mb-2 fw-bold">User Registration Analytics</h4>
                        <p class="text-muted mb-0">Comprehensive insights for better decision-making</p>
                    </div>
                    <!-- Service Tabs -->
                    <div class="d-flex justify-content-center flex-wrap gap-2">
                        <a href="{{ route('admin.analytics.seedlings') }}"
                            class="btn btn-sm {{ request()->routeIs('admin.analytics.seedlings') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-seedling me-1"></i> Seedlings
                        </a>
                        <a href="{{ route('admin.analytics.rsbsa') }}"
                            class="btn btn-sm {{ request()->routeIs('admin.analytics.rsbsa') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-user-check me-1"></i> RSBSA
                        </a>
                        <a href="{{ route('admin.analytics.fishr') }}"
                            class="btn btn-sm {{ request()->routeIs('admin.analytics.fishr') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-fish me-1"></i> FISHR
                        </a>
                        <a href="{{ route('admin.analytics.boatr') }}"
                            class="btn btn-sm {{ request()->routeIs('admin.analytics.boatr') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-ship me-1"></i> BOATR
                        </a>
                        <a href="{{ route('admin.analytics.training') }}" 
                            class="btn btn-sm {{ request()->routeIs('admin.analytics.training') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-graduation-cap me-1"></i> Training
                        </a>
                        <a href="{{ route('admin.analytics.supply-management') }}"
                                class="btn btn-sm  {{ request()->routeIs('admin.analytics.supply-management') ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-boxes me-1"></i> Supply Management
                        </a>
                        <a href="{{ route('admin.analytics.user-registration') }}"
                            class="btn btn-sm {{ request()->routeIs('admin.analytics.user-registration') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-user-plus me-1"></i> User Registration
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
                    <form method="GET" action="{{ route('admin.analytics.user-registration') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label small fw-semibold">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label small fw-semibold">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Apply Filter
                                </button>
                                <a href="{{ route('admin.analytics.user-registration.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                                    class="btn btn-success">
                                    <i class="fas fa-download me-1"></i> Export
                                </a>
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#userRegInsightsModal">
                                    <i class="fas fa-lightbulb me-1"></i> AI Insights
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="metric-card metric-primary">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <h6>Total Registrations</h6>
                    <h2>{{ number_format($overview['total_registrations']) }}</h2>
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-1"></i>{{ $overview['active_barangays'] }} barangays</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="metric-card metric-success">
                <div class="metric-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="metric-content">
                    <h6>Approval Rate</h6>
                    <h2>{{ $overview['approval_rate'] }}%</h2>
                    <p class="mb-0">{{ number_format($overview['approved_registrations']) }} approved</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="metric-card metric-info">
                <div class="metric-icon">
                    <i class="fas fa-envelope-circle-check"></i>
                </div>
                <div class="metric-content">
                    <h6>Email Verification</h6>
                    <h2>{{ $overview['email_verification_rate'] }}%</h2>
                    <p class="mb-0">{{ number_format($overview['email_verified']) }} verified</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="metric-card metric-warning">
                <div class="metric-icon">
                    <i class="fas fa-file-circle-check"></i>
                </div>
                <div class="metric-content">
                    <h6>Document Completion</h6>
                    <h2>{{ $overview['document_completion_rate'] }}%</h2>
                    <p class="mb-0">{{ number_format($overview['with_all_documents']) }} complete</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Registration Trends -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-chart-line me-2 text-primary"></i>Registration Trends Over Time</h5>
                </div>
                <div class="card-body">
                    <canvas id="userRegTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-chart-pie me-2 text-info"></i>Status Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="userRegStatusChart"></canvas>
                    <div class="mt-3">
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded status-item">
                                <span class="d-flex align-items-center">
                                    <span class="status-dot status-{{ $status }}"></span>
                                    {{ ucfirst($status) }}
                                </span>
                                <span class="badge bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : ($status === 'pending' ? 'warning' : 'secondary')) }}">
                                    {{ $count }} ({{ $statusAnalysis['percentages'][$status] }}%)
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & Critical Metrics -->
    <div class="row g-3 mb-4">
        <!-- Performance Indicators -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-tachometer-alt me-2 text-success"></i>Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <!-- Processing Time -->
                    <div class="metric-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold">Processing Time</span>
                            <span class="badge bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}">
                                {{ $processingTimeAnalysis['avg_processing_days'] }}d avg
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}"
                                style="width: {{ min(100, ((14 - $processingTimeAnalysis['avg_processing_days']) / 14) * 100) }}%">
                            </div>
                        </div>
                        <small class="text-muted">Target: < 7 days</small>
                    </div>

                    <!-- Quality Score -->
                    <div class="metric-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold">Quality Score</span>
                            <span class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}">
                                {{ $performanceMetrics['quality_score'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}"
                                style="width: {{ $performanceMetrics['quality_score'] }}%"></div>
                        </div>
                        <small class="text-muted">Based on docs, email, approval rates</small>
                    </div>

                    <!-- User Engagement -->
                    <div class="metric-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold">User Engagement</span>
                            <span class="badge bg-info">{{ $performanceMetrics['engagement_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" style="width: {{ $performanceMetrics['engagement_rate'] }}%"></div>
                        </div>
                        <small class="text-muted">Document completion rate</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document & Email Analysis -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-file-alt me-2 text-warning"></i>Verification Status</h5>
                </div>
                <div class="card-body">
                    <!-- Document Stats -->
                    <div class="verification-grid mb-3">
                        <div class="verification-item">
                            <div class="verification-value text-primary">{{ $documentAnalysis['location_doc_rate'] }}%</div>
                            <div class="verification-label">Location Doc</div>
                        </div>
                        <div class="verification-item">
                            <div class="verification-value text-success">{{ $documentAnalysis['id_front_rate'] }}%</div>
                            <div class="verification-label">ID Front</div>
                        </div>
                        <div class="verification-item">
                            <div class="verification-value text-warning">{{ $documentAnalysis['id_back_rate'] }}%</div>
                            <div class="verification-label">ID Back</div>
                        </div>
                    </div>

                    <!-- Complete Documentation -->
                    <div class="alert alert-info mb-3 p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small><i class="fas fa-check-double me-1"></i>Complete Documents</small>
                            <span class="badge bg-info">{{ $documentAnalysis['with_all_documents'] }} users</span>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $documentAnalysis['complete_docs_rate'] }}%"></div>
                        </div>
                    </div>

                    <!-- Email Verification -->
                    <div class="alert alert-success mb-0 p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small><i class="fas fa-envelope-check me-1"></i>Email Verified</small>
                            <span class="badge bg-success">{{ $emailVerificationAnalysis['verified'] }} users</span>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $emailVerificationAnalysis['verification_rate'] }}%"></div>
                        </div>
                    </div>

                    <!-- Impact Info -->
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">
                            <strong>Impact:</strong> Complete docs improve approval by 
                            <strong class="text-success">+{{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}%</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Demographics -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-users me-2 text-secondary"></i>User Demographics</h5>
                </div>
                <div class="card-body">
                    <!-- Gender Distribution -->
                    <div class="row mb-3">
                        @foreach ($genderAnalysis['stats'] as $gender)
                            <div class="col-6">
                                <div class="text-center p-3 rounded demographic-card demographic-{{ strtolower($gender->gender) }}">
                                    <h3 class="mb-1">{{ $gender->total_registrations }}</h3>
                                    <p class="mb-1 fw-bold">{{ $gender->gender }}</p>
                                    <small>{{ $genderAnalysis['percentages'][$gender->gender] ?? 0 }}%</small>
                                    <div class="mt-2">
                                        <small class="text-muted">Avg Age: {{ round($gender->avg_age ?? 0, 1) }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- User Type Summary -->
                    <div class="mt-3">
                        <h6 class="small fw-semibold mb-2">Top User Types</h6>
                        @foreach ($userTypeAnalysis->take(3) as $userType)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                                <span class="small">{{ ucfirst($userType->user_type) }}</span>
                                <span class="badge bg-primary">{{ $userType->total_registrations }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Barangays -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-semibold"><i class="fas fa-trophy me-2 text-dark"></i>Top Performing Barangays</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th width="80">Rank</th>
                                    <th>Barangay</th>
                                    <th class="text-center" width="120">Total</th>
                                    <th class="text-center" width="120">Approved</th>
                                    <th width="200">Approval Rate</th>
                                    <th class="text-center" width="120">Verified</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangayAnalysis->take(10) as $index => $barangay)
                                    <tr>
                                        <td>
                                            <span class="rank-badge rank-{{ $index + 1 }}">
                                                #{{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $barangay->barangay }}</strong></td>
                                        <td class="text-center"><span class="badge bg-secondary">{{ $barangay->total_registrations }}</span></td>
                                        <td class="text-center"><span class="badge bg-success">{{ $barangay->approved }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ round(($barangay->approved / max(1, $barangay->total_registrations)) * 100, 1) }}%">
                                                    </div>
                                                </div>
                                                <small class="fw-semibold" style="min-width: 45px;">
                                                    {{ round(($barangay->approved / max(1, $barangay->total_registrations)) * 100, 1) }}%
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center"><span class="badge bg-info">{{ $barangay->email_verified }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Modal -->
    <div class="modal fade" id="userRegInsightsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-lightbulb me-2"></i>AI-Powered Insights & Recommendations
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info border-0">
                        <h6 class="fw-bold"><i class="fas fa-chart-line me-2"></i>Key Performance Insights</h6>
                        <ul class="mb-0">
                            <li><strong>Approval Efficiency:</strong> {{ $overview['approval_rate'] }}% approval rate
                                {{ $overview['approval_rate'] > 80 ? '- Excellent performance! ✅' : ($overview['approval_rate'] > 60 ? '- Good, but can improve 📈' : '- Needs optimization ⚠️') }}
                            </li>
                            <li><strong>Email Verification:</strong> {{ $overview['email_verification_rate'] }}% verification
                                {{ $overview['email_verification_rate'] > 80 ? '- Strong user engagement ✅' : '- Consider email campaigns 📧' }}
                            </li>
                            <li><strong>Document Compliance:</strong> {{ $overview['document_completion_rate'] }}% completion
                                {{ $overview['document_completion_rate'] > 70 ? '- Users are well-informed ✅' : '- Improve onboarding guides 📚' }}
                            </li>
                        </ul>
                    </div>

                    <div class="alert alert-success border-0">
                        <h6 class="fw-bold"><i class="fas fa-thumbs-up me-2"></i>Positive Trends</h6>
                        <ul class="mb-0">
                            <li><strong>Geographic Reach:</strong> {{ $overview['active_barangays'] }} barangays - 
                                {{ $overview['active_barangays'] > 20 ? 'Excellent coverage' : 'Growing steadily' }}
                            </li>
                            <li><strong>Processing Speed:</strong> {{ $processingTimeAnalysis['avg_processing_days'] }} days average -
                                {{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'Exceptional! ⚡' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'Good efficiency' : 'Room for improvement') }}
                            </li>
                            <li><strong>Quality Score:</strong> {{ $performanceMetrics['quality_score'] }}% overall quality</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning border-0">
                        <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Action Items</h6>
                        <ul class="mb-0">
                            @if ($overview['email_verification_rate'] < 70)
                                <li>📧 Send automated reminders to {{ $emailVerificationAnalysis['unverified'] }} unverified users</li>
                            @endif
                            @if ($overview['document_completion_rate'] < 60)
                                <li>📄 Create step-by-step document upload guides</li>
                            @endif
                            @if ($processingTimeAnalysis['avg_processing_days'] > 7)
                                <li>⚡ Streamline approval workflow (currently {{ $processingTimeAnalysis['avg_processing_days'] }} days)</li>
                            @endif
                            <li>🎯 Documents boost approval by {{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}% - emphasize this to users</li>
                            <li>📊 Replicate best practices from top-performing barangays</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('admin.analytics.user-registration.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                        class="btn btn-success">
                        <i class="fas fa-download me-1"></i>Export Report
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --info-gradient: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        /* Metric Cards */
        .metric-card {
            padding: 1.5rem;
            border-radius: 12px;
            color: white;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        .metric-primary {
            background: var(--primary-gradient);
        }

        .metric-success {
            background: var(--success-gradient);
        }

        .metric-info {
            background: var(--info-gradient);
        }

        .metric-warning {
            background: var(--warning-gradient);
        }

        .metric-icon {
            font-size: 2.5rem;
            opacity: 0.9;
        }

        .metric-content h6 {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .metric-content h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .metric-content p {
            font-size: 0.875rem;
            opacity: 0.85;
        }

        /* Card Styling */
        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 1rem 1.5rem;
        }

        /* Status Items */
        .status-item {
            background: rgba(0,0,0,0.02);
            transition: background 0.2s;
        }

        .status-item:hover {
            background: rgba(0,0,0,0.04);
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-approved { background: #10b981; }
        .status-rejected { background: #ef4444; }
        .status-pending { background: #f59e0b; }
        .status-unverified { background: #6b7280; }
        .status-banned { background: #1f2937; }

        /* Metric Items */
        .metric-item {
            padding: 1rem;
            border-radius: 8px;
            background: rgba(0,0,0,0.02);
            margin-bottom: 1rem;
        }

        .metric-item:last-child {
            margin-bottom: 0;
        }

        /* Verification Grid */
        .verification-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }

        .verification-item {
            text-align: center;
            padding: 0.75rem;
            background: rgba(0,0,0,0.02);
            border-radius: 8px;
        }

        .verification-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .verification-label {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* Demographics Cards */
        .demographic-card {
            transition: transform 0.2s;
        }

        .demographic-card:hover {
            transform: scale(1.05);
        }

        .demographic-male {
            background: rgba(59, 130, 246, 0.1);
            border: 2px solid rgba(59, 130, 246, 0.3);
        }

        .demographic-female {
            background: rgba(236, 72, 153, 0.1);
            border: 2px solid rgba(236, 72, 153, 0.3);
        }

        /* Rank Badges */
        .rank-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.875rem;
        }

        .rank-1 {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
        }

        .rank-2 {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
            color: white;
        }

        .rank-3 {
            background: linear-gradient(135deg, #fb923c, #ea580c);
            color: white;
        }

        .rank-badge:not(.rank-1):not(.rank-2):not(.rank-3) {
            background: #e5e7eb;
            color: #374151;
        }

        /* Progress Bars */
        .progress {
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Table Styling */
        .table thead th {
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        .table tbody tr {
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.05);
        }

        /* Modal Styling */
        .bg-gradient-info {
            background: var(--info-gradient);
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            border-bottom: none;
        }

        .alert {
            border-radius: 10px;
        }

        /* Badge Styling */
        .badge {
            font-weight: 600;
            padding: 0.4em 0.65em;
        }

        /* Button Styling */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .metric-card {
                padding: 1.25rem;
            }

            .metric-icon {
                font-size: 2rem;
            }

            .metric-content h2 {
                font-size: 1.75rem;
            }

            .verification-grid {
                gap: 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .metric-card {
                flex-direction: column;
                text-align: center;
            }

            .verification-grid {
                grid-template-columns: 1fr;
            }

            .metric-content h2 {
                font-size: 1.5rem;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Common chart configuration
            const chartConfig = {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1
                    }
                }
            };

            // 1. Registration Trends Chart
            const trendsCtx = document.getElementById('userRegTrendsChart').getContext('2d');
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach ($monthlyTrends as $trend)
                            '{{ date('M Y', strtotime($trend->month . '-01')) }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Total Registrations',
                        data: [
                            @foreach ($monthlyTrends as $trend)
                                {{ $trend->total_registrations }},
                            @endforeach
                        ],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Approved',
                        data: [
                            @foreach ($monthlyTrends as $trend)
                                {{ $trend->approved }},
                            @endforeach
                        ],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    ...chartConfig,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                padding: 8
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                padding: 8
                            }
                        }
                    }
                }
            });

            // 2. Status Distribution Pie Chart
            const statusCtx = document.getElementById('userRegStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            '{{ ucfirst($status) }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($statusAnalysis['counts'] as $count)
                                {{ $count }},
                            @endforeach
                        ],
                        backgroundColor: [
                            '#6b7280',  // unverified
                            '#f59e0b',  // pending
                            '#10b981',  // approved
                            '#ef4444',  // rejected
                            '#1f2937'   // banned
                        ],
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    ...chartConfig,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            ...chartConfig.plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection