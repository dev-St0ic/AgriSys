{{-- resources/views/admin/training/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Training Requests - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-graduation-cap me-2 text-primary"></i>
        <span class="text-primary fw-bold">Training Requests</span>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-graduation-cap text-primary"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $totalApplications }}</div>
                    <div class="stat-label text-primary">Total Applications</div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $approvedCount }}</div>
                    <div class="stat-label text-success">Approved</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $underReviewCount }}</div>
                    <div class="stat-label text-warning">Under Review</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-hourglass-start text-info"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $pendingCount }}</div>
                    <div class="stat-label text-info">Pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.training.requests') }}" id="filterForm">
                <!-- Hidden date inputs -->
                <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                <div class="row g-2">
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>
                                Under Review
                            </option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                Approved
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="training_type" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Training Types</option>
                            <option value="tilapia_hito"
                                {{ request('training_type') == 'tilapia_hito' ? 'selected' : '' }}>
                                Tilapia and Hito
                            </option>
                            <option value="hydroponics" {{ request('training_type') == 'hydroponics' ? 'selected' : '' }}>
                                Hydroponics
                            </option>
                            <option value="aquaponics" {{ request('training_type') == 'aquaponics' ? 'selected' : '' }}>
                                Aquaponics
                            </option>
                            <option value="mushrooms" {{ request('training_type') == 'mushrooms' ? 'selected' : '' }}>
                                Mushrooms Production
                            </option>
                            <option value="livestock_poultry"
                                {{ request('training_type') == 'livestock_poultry' ? 'selected' : '' }}>
                                Livestock and Poultry
                            </option>
                            <option value="high_value_crops"
                                {{ request('training_type') == 'high_value_crops' ? 'selected' : '' }}>
                                High Value Crops
                            </option>
                            <option value="sampaguita_propagation"
                                {{ request('training_type') == 'sampaguita_propagation' ? 'selected' : '' }}>
                                Sampaguita Propagation
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search name, number..." value="{{ request('search') }}"
                                oninput="autoSearch()" id="searchInput">
                            <button class="btn btn-outline-secondary btn-sm" type="submit" title="Search"
                                id="searchButton">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-sm w-100" data-bs-toggle="modal"
                            data-bs-target="#dateFilterModal">
                            <i class="fas fa-calendar-alt me-1"></i>Date Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.training.requests') }}" class="btn btn-secondary btn-sm w-100">
                            <i></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div></div>
            <div class="text-center flex-fill">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-graduation-cap me-2"></i>Training Requests
                </h6>
            </div>
            <div class="d-flex gap-2">
                 <button type="button" class="btn btn-primary btn-sm" onclick="showAddTrainingModal()">
                    <i class="fas fa-user-plus me-2"></i>Add Registration
                </button>
                <a href="{{ route('admin.training.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="applicationsTable">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Date Applied</th>
                            <th class="text-center">Application #</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Training Type</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Documents</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainings as $training)
                            <tr data-application-id="{{ $training->id }}">
                                <td class="text-start">{{ $training->created_at->format('M d, Y g:i A') }}</td>
                                <td class="text-start">
                                    <strong class="text-primary">{{ $training->application_number }}</strong>
                                </td>
                                <td class="text-start">{{ $training->full_name }}</td>
                                <td class="text-start">
                                    <span class="badge bg-info fs-6">{{ $training->training_type_display }}</span>
                                </td>
                                <td class="text-start">
                                    <span class="badge bg-{{ $training->status_color }} fs-6">
                                        {{ $training->formatted_status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="training-table-documents">
                                        @if ($training->document_path)
                                            <div class="training-document-previews">
                                                <button type="button" class="training-mini-doc"
                                                    onclick="viewDocument('{{ $training->document_path }}', 'Training Request - {{ $training->full_name }}')"
                                                    title="Supporting Document">
                                                    <div class="training-mini-doc-icon">
                                                        <i class="fas fa-file-alt text-primary"></i>
                                                    </div>
                                                </button>
                                            </div>
                                            <button type="button" class="training-document-summary"
                                                onclick="viewDocument('{{ $training->document_path }}', 'Training Request - {{ $training->full_name }}')"
                                                style="background: none; border: none; padding: 0; cursor: pointer;">
                                                <small class="text-muted">1 document</small>
                                            </button>
                                        @else
                                            <div class="training-no-documents">
                                                <i class="fas fa-folder-open text-muted"></i>
                                                <small class="text-muted">No documents</small>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <!-- Primary Actions -->
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="viewApplication({{ $training->id }})" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                        <button class="btn btn-sm btn-outline-dark"
                                            onclick="showUpdateModal({{ $training->id }}, '{{ $training->status }}')"
                                            title="Update Status">
                                            <i class="fas fa-sync"></i> Change Status
                                        </button>

                                        <!-- Dropdown for More Actions -->
                                        <div class="btn-group" role="group">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                        onclick="showEditTrainingModal({{ $training->id }})">
                                                        <i class="fas fa-edit me-2 text-success"></i>Edit Information
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                        onclick="deleteApplication({{ $training->id }}, '{{ $training->application_number }}')">
                                                        <i class="fas fa-trash me-2"></i>Delete Application
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-graduation-cap fa-3x mb-3 text-gray-300"></i>
                                    <p>No training requests found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($trainings->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            {{-- Previous Page Link --}}
                            @if ($trainings->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Back</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $trainings->previousPageUrl() }}"
                                        rel="prev">Back</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $trainings->currentPage();
                                $lastPage = $trainings->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);

                                // Ensure we always show 5 pages when possible
                                if ($endPage - $startPage < 4) {
                                    if ($startPage == 1) {
                                        $endPage = min($lastPage, $startPage + 4);
                                    } else {
                                        $startPage = max(1, $endPage - 4);
                                    }
                                }
                            @endphp

                            @for ($page = $startPage; $page <= $endPage; $page++)
                                @if ($page == $currentPage)
                                    <li class="page-item active">
                                        <span class="page-link bg-primary border-primary">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $trainings->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($trainings->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $trainings->nextPageUrl() }}" rel="next">Next</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">Next</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>

    <!-- UPDATE STATUS MODAL - FIXED REMARKS HANDLING -->
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Update Application Status
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Application Info Card -->
                    <div class="card bg-light border-primary mb-4">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-info-circle me-2"></i>Application Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Application #</small>
                                        <strong class="text-primary" id="updateAppNumber"></strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Applicant Name</small>
                                        <strong id="updateAppName"></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Contact Number</small>
                                        <strong id="updateAppMobile"></strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Training Type</small>
                                        <strong id="updateAppTraining"></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <small class="text-muted d-block mb-2">Current Status</small>
                                    <span id="updateAppCurrentStatus"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Card -->
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-exchange-alt me-2"></i>Change Status
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="updateForm">
                                <input type="hidden" id="updateApplicationId">
                                
                                <div class="mb-3">
                                    <label for="newStatus" class="form-label fw-semibold">
                                        Select New Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="newStatus" required onchange="checkForChanges()">
                                        <option value="">Choose status...</option>
                                        <option value="pending">Pending</option>
                                        <option value="under_review">Under Review</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Remarks Card -->
                    <div class="card border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-comment me-2"></i>Admin Remarks
                            </h6>
                        </div>
                        <div class="card-body">
                            <label for="remarks" class="form-label fw-semibold">
                                Remarks (Optional)
                            </label>
                            <textarea class="form-control" 
                                    id="remarks" 
                                    name="remarks"
                                    rows="4"
                                    placeholder="Add any notes or comments about this status change..."
                                    maxlength="1000"
                                    onchange="checkForChanges()"
                                    oninput="updateRemarksCounter()"></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Provide context for this status change
                                </small>
                                <small class="text-muted" id="remarksCounterDisplay">
                                    <span id="charCountRemarks">0</span>/1000
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info border-left-info mt-3 mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Note:</strong> This will update the application status and store your remarks in the system.
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="updateStatusBtn" onclick="updateApplicationStatus()">
                        <i class="fas fa-save me-2"></i>Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Details Modal enhanced-->
    <div class="modal fade" id="applicationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Application Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="applicationDetails">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Enhanced Document Viewer Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #0d6efd">
                    <h5 class="modal-title w-100 text-center" id="documentModalLabel">
                        <i class="fas fa-file-alt me-2"></i>Supporting Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 d-flex justify-content-center" id="documentViewer">
                    <!-- Documents will be loaded here -->
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Date Filter Modal -->
    <div class="modal fade" id="dateFilterModal" tabindex="-1" aria-labelledby="dateFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center" id="dateFilterModalLabel">
                        <i></i>Select Date Range
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Date Range Inputs -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-calendar-plus me-2"></i>Custom Date Range
                                    </h6>
                                    <div class="mb-3">
                                        <label for="modal_date_from" class="form-label">From Date</label>
                                        <input type="date" id="modal_date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal_date_to" class="form-label">To Date</label>
                                        <input type="date" id="modal_date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                    <button type="button" class="btn btn-primary w-100"
                                        onclick="applyCustomDateRange()">
                                        <i class="fas fa-check me-2"></i>Apply Custom Range
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Date Presets -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-clock me-2"></i>Quick Presets
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-success"
                                            onclick="setDateRangeModal('today')">
                                            <i class="fas fa-calendar-day me-2"></i>Today
                                        </button>
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="setDateRangeModal('week')">
                                            <i class="fas fa-calendar-week me-2"></i>This Week
                                        </button>
                                        <button type="button" class="btn btn-outline-warning"
                                            onclick="setDateRangeModal('month')">
                                            <i class="fas fa-calendar me-2"></i>This Month
                                        </button>
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="setDateRangeModal('year')">
                                            <i class="fas fa-calendar-alt me-2"></i>This Year
                                        </button>
                                        <hr class="my-3">
                                        <button type="button" class="btn btn-outline-secondary w-100"
                                            onclick="clearDateRangeModal()">
                                            <i class="fas fa-times me-2"></i>Clear Date Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Filter Status -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="dateFilterStatus">
                                    @if (request('date_from') || request('date_to'))
                                        Current filter:
                                        @if (request('date_from'))
                                            From {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                                        @endif
                                        @if (request('date_to'))
                                            To {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                        @endif
                                    @else
                                        No date filter applied - showing all applications
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add training Modal - UPDATED WITH FISHR DESIGN -->
    <div class="modal fade" id="addTrainingModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Add New Training Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addTrainingForm" enctype="multipart/form-data">
                        <!-- Personal Information Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="training_first_name" class="form-label fw-semibold">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="training_first_name" required
                                            maxlength="100" placeholder="First name">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="training_middle_name" class="form-label fw-semibold">
                                            Middle Name
                                        </label>
                                        <input type="text" class="form-control" id="training_middle_name"
                                            maxlength="100" placeholder="Middle name (optional)">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="training_last_name" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="training_last_name" required
                                            maxlength="100" placeholder="Last name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="training_name_extension" class="form-label fw-semibold">
                                            Extension
                                        </label>
                                        <select class="form-select" id="training_name_extension">
                                            <option value="">None</option>
                                            <option value="Jr.">Jr.</option>
                                            <option value="Sr.">Sr.</option>
                                            <option value="II">II</option>
                                            <option value="III">III</option>
                                            <option value="IV">IV</option>
                                            <option value="V">V</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="training_contact_number" class="form-label fw-semibold">
                                            Contact Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="training_contact_number" required
                                            placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>09XXXXXXXXX
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-map-marker-alt me-2"></i>Location Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="training_barangay" class="form-label fw-semibold">
                                            Barangay <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="training_barangay" required>
                                            <option value="">Select Barangay</option>
                                            <option value="Bagong Silang">Bagong Silang</option>
                                            <option value="Calendola">Calendola</option>
                                            <option value="Chrysanthemum">Chrysanthemum</option>
                                            <option value="Cuyab">Cuyab</option>
                                            <option value="Estrella">Estrella</option>
                                            <option value="Fatima">Fatima</option>
                                            <option value="G.S.I.S.">G.S.I.S.</option>
                                            <option value="Landayan">Landayan</option>
                                            <option value="Langgam">Langgam</option>
                                            <option value="Laram">Laram</option>
                                            <option value="Magsaysay">Magsaysay</option>
                                            <option value="Maharlika">Maharlika</option>
                                            <option value="Narra">Narra</option>
                                            <option value="Nueva">Nueva</option>
                                            <option value="Pacita 1">Pacita 1</option>
                                            <option value="Pacita 2">Pacita 2</option>
                                            <option value="Poblacion">Poblacion</option>
                                            <option value="Riverside">Riverside</option>
                                            <option value="Rosario">Rosario</option>
                                            <option value="Sampaguita Village">Sampaguita Village</option>
                                            <option value="San Antonio">San Antonio</option>
                                            <option value="San Lorenzo Ruiz">San Lorenzo Ruiz</option>
                                            <option value="San Roque">San Roque</option>
                                            <option value="San Vicente">San Vicente</option>
                                            <option value="Santo Niño">Santo Niño</option>
                                            <option value="United Bayanihan">United Bayanihan</option>
                                            <option value="United Better Living">United Better Living</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Training Information Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-book me-2"></i>Training Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="training_type" class="form-label fw-semibold">
                                            Training Type <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="training_type" required>
                                            <option value="">Select Training Type</option>
                                            <option value="tilapia_hito">Tilapia and Hito</option>
                                            <option value="hydroponics">Hydroponics</option>
                                            <option value="aquaponics">Aquaponics</option>
                                            <option value="mushrooms">Mushrooms Production</option>
                                            <option value="livestock_poultry">Livestock and Poultry</option>
                                            <option value="high_value_crops">High Value Crops</option>
                                            <option value="sampaguita_propagation">Sampaguita Propagation</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Documents Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-file-upload me-2"></i>Supporting Document (Optional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-4">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Upload supporting document. Supported formats: JPG, PNG, PDF (Max 10MB)
                                </p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="training_supporting_document" class="form-label fw-semibold">
                                            Upload Document
                                        </label>
                                        <input type="file" class="form-control" id="training_supporting_document"
                                            accept=".pdf,.jpg,.jpeg,.png" onchange="previewTrainingDocument()">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>Accepted: JPG, PNG, PDF (Max 10MB)
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="training_doc_preview" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Application Status Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-cog me-2"></i>Application Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="training_status" class="form-label fw-semibold">
                                            Initial Status <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="training_status" required>
                                            <option value="pending" selected>Pending</option>
                                            <option value="under_review" selected>Under Review</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks Card -->
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-comment me-2"></i>Admin Remarks
                                </h6>
                            </div>
                            <div class="card-body">
                                <label for="training_remarks" class="form-label fw-semibold">
                                    Remarks (Optional)
                                </label>
                                <textarea class="form-control" id="training_remarks" rows="4"
                                    placeholder="Add any comments about this application..."
                                    maxlength="1000"
                                    oninput="updateTrainingRemarksCounter()"></textarea>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Provide context for this registration
                                    </small>
                                    <small class="text-muted" id="remarksCounterTraining">
                                        <span id="charCountTraining">0</span>/1000
                                    </small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitAddTraining()">
                        <i class="fas fa-save me-1"></i>Create Application
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Training Modal Enhanced-->
  <div class="modal fade" id="editTrainingModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title w-100 text-center">
                    <i></i>Edit Application - <span id="editTrainingNumber"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="editTrainingForm" enctype="multipart/form-data">
                    <!-- Personal Information Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="edit_training_first_name" class="form-label fw-semibold">
                                        First Name 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_training_first_name"
                                        name="first_name" required maxlength="100" placeholder="First name">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_training_middle_name" class="form-label fw-semibold">
                                        Middle Name
                                    </label>
                                    <input type="text" class="form-control" id="edit_training_middle_name"
                                        name="middle_name" maxlength="100" placeholder="Middle name (optional)">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_training_last_name" class="form-label fw-semibold">
                                        Last Name 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_training_last_name"
                                        name="last_name" required maxlength="100" placeholder="Last name">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_training_extension" class="form-label fw-semibold">
                                        Extension
                                    </label>
                                    <select class="form-select" id="edit_training_extension" name="name_extension">
                                        <option value="">None</option>
                                        <option value="Jr.">Jr.</option>
                                        <option value="Sr.">Sr.</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                        <option value="IV">IV</option>
                                        <option value="V">V</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_training_contact_number" class="form-label fw-semibold">
                                        Contact Number 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="edit_training_contact_number"
                                        name="contact_number" required placeholder="09XXXXXXXXX"
                                        pattern="^(\+639|09)\d{9}$" maxlength="20">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>09XXXXXXXXX
                                    </small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_training_app_number" class="form-label fw-semibold">
                                        Application Number
                                    </label>
                                    <input type="text" class="form-control" id="edit_training_app_number" disabled placeholder="-">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>Auto-generated (cannot be changed)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Information Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-map-marker-alt me-2"></i>Location Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="edit_training_barangay" class="form-label fw-semibold">
                                        Barangay 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_training_barangay" name="barangay" required>
                                        <option value="">Select Barangay</option>
                                        <option value="Bagong Silang">Bagong Silang</option>
                                        <option value="Calendola">Calendola</option>
                                        <option value="Chrysanthemum">Chrysanthemum</option>
                                        <option value="Cuyab">Cuyab</option>
                                        <option value="Estrella">Estrella</option>
                                        <option value="Fatima">Fatima</option>
                                        <option value="G.S.I.S.">G.S.I.S.</option>
                                        <option value="Landayan">Landayan</option>
                                        <option value="Langgam">Langgam</option>
                                        <option value="Laram">Laram</option>
                                        <option value="Magsaysay">Magsaysay</option>
                                        <option value="Maharlika">Maharlika</option>
                                        <option value="Narra">Narra</option>
                                        <option value="Nueva">Nueva</option>
                                        <option value="Pacita 1">Pacita 1</option>
                                        <option value="Pacita 2">Pacita 2</option>
                                        <option value="Poblacion">Poblacion</option>
                                        <option value="Riverside">Riverside</option>
                                        <option value="Rosario">Rosario</option>
                                        <option value="Sampaguita Village">Sampaguita Village</option>
                                        <option value="San Antonio">San Antonio</option>
                                        <option value="San Lorenzo Ruiz">San Lorenzo Ruiz</option>
                                        <option value="San Roque">San Roque</option>
                                        <option value="San Vicente">San Vicente</option>
                                        <option value="Santo Niño">Santo Niño</option>
                                        <option value="United Bayanihan">United Bayanihan</option>
                                        <option value="United Better Living">United Better Living</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Training Information Card - NOW FULLY EDITABLE -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-book me-2"></i>Training Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="edit_training_type" class="form-label fw-semibold">
                                        Training Type 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_training_type" name="training_type"
                                        required>
                                        <option value="">Select Training Type</option>
                                        <option value="tilapia_hito">Tilapia and Hito</option>
                                        <option value="hydroponics">Hydroponics</option>
                                        <option value="aquaponics">Aquaponics</option>
                                        <option value="mushrooms">Mushrooms Production</option>
                                        <option value="livestock_poultry">Livestock and Poultry</option>
                                        <option value="high_value_crops">High Value Crops</option>
                                        <option value="sampaguita_propagation">Sampaguita Propagation</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-file-upload me-2"></i>Supporting Document
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-4">
                                <i class="fas fa-info-circle me-1"></i>
                                View or upload supporting document. Supported formats: JPG, PNG, PDF (Max 10MB)
                            </p>

                            <!-- Current Document Display -->
                            <div id="edit_training_current_document" style="display: none; margin-bottom: 1.5rem;">
                                <label class="form-label fw-semibold text-muted mb-2">Current Document</label>
                                <div id="edit_training_current_doc_preview"></div>
                            </div>

                            <!-- Upload New Document Section -->
                            <div class="row">
                                <div class="col-12">
                                    <label for="edit_training_supporting_document" class="form-label fw-semibold">
                                        Supporting Document
                                    </label>
                                    <input type="file" class="form-control" id="edit_training_supporting_document" 
                                        name="supporting_document" accept="image/*,.pdf" 
                                        onchange="previewEditTrainingDocument('edit_training_supporting_document', 'edit_training_supporting_document_preview')">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>Upload a new file to replace it.
                                    </small>
                                </div>
                            </div>

                            <!-- New Document Preview -->
                            <div id="edit_training_supporting_document_preview" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Application Status (Read-only) Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-info-circle me-2"></i>Application Status (Read-only)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block mb-2">Current Status</small>
                                    <div>
                                        <span id="edit_training_status_badge" class="badge bg-secondary fs-6"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block mb-2">Date Applied</small>
                                    <div id="edit_training_created_at" class="fw-semibold">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info border-left-info mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Note:</strong> You can edit all application information here.
                        To change application status or add remarks, use the "Change Status" button from the main table.
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="editTrainingSubmitBtn"
                    onclick="handleEditTrainingSubmit()">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
    <!-- DELETE MODAL FOR TRAINING -->
    <div class="modal fade" id="deleteTrainingModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title w-100 text-center">Permanently Delete Training Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                        <p class="mb-0">This action cannot be undone. Permanently deleting <strong id="delete_training_name"></strong> will:</p>
                    </div>
                    <ul class="mb-0">
                        <li>Remove the training request from the database</li>
                        <li>Delete all associated documents and files</li>
                        <li>Delete all request history and logs</li>
                        <li>Cannot be recovered</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmPermanentDeleteTraining()"
                        id="confirm_delete_training_btn">
                        <span class="btn-text">Yes, Delete Permanently</span>
                        <span class="btn-loader" style="display: none;"><span
                                class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <style>
        /* Modern Statistics Cards */
        .stat-card {
            border: none;
            border-radius: 15px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .stat-icon i {
            font-size: 2.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
        }

        .stat-label {
            font-size: 1rem;
            font-weight: 500;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Enhanced Filter Section Styling */
        .filter-section .form-control,
        .filter-section .form-select {
            border-radius: 8px;
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .filter-section .form-control:focus,
        .filter-section .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .filter-section .input-group-text {
            border-radius: 8px 0 0 8px;
        }

        .filter-section .form-control {
            border-radius: 0 8px 8px 0;
        }

        /* Enhanced visual feedback for changed fields */
        .form-changed {
            border-left: 3px solid #ffc107 !important;
            background-color: #fff3cd;
            transition: all 0.3s ease;
        }

        .no-changes {
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        /* Change indicator */
        .change-indicator {
            position: relative;
        }

        .change-indicator::after {
            content: "●";
            color: #ffc107;
            font-size: 12px;
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .change-indicator.changed::after {
            opacity: 1;
        }

        /* Delete button styling */
        .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .btn-outline-danger:focus,
        .btn-outline-danger:active {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Card styling */
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }

        .text-xs,
        .text-sm {
            font-size: 0.875rem;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            font-size: 0.75em;
        }

        /* Modal enhancements */
        .modal-header {
            border-bottom: 1px solid #e9ecef;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
        }

        /* Loading states */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Card header enhancements */
        .card-header h6 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Status badges */
        .badge.fs-6 {
            font-size: 0.875rem !important;
            padding: 0.5em 0.75em;
        }

        /* Enhanced Document Viewer Styles */
        #documentModal .modal-content {
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }

        #documentModal .modal-header {
            border-radius: 12px 12px 0 0;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
        }

        #documentModal .modal-footer {
            border-radius: 0 0 12px 12px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-top: 1px solid #dee2e6;
        }

        #documentViewer {
            min-height: 400px;
            max-height: 80vh;
            overflow: auto;
        }

        /* Document container styles */
        .document-container {
            background: #ffffff;
            border: 1px solid #e9ecef !important;
            transition: all 0.3s ease;
        }

        .document-container:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        /* Image zoom styles */
        .document-image {
            transition: transform 0.3s ease;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .document-image:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Loading animation */
        .document-loading {
            animation: pulse 1.5s ease-in-out infinite;
        }

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

        /* PDF container */
        .pdf-container embed {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Video and audio controls */
        video,
        audio {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* File info badges */
        .file-info-badge {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        /* Enhanced download buttons */
        .document-actions {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
        }

        /* Centered document viewer layout */
        #documentViewer {
            min-height: 400px;
        }

        #documentViewer .container-fluid {
            min-height: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 20px;
        }

        .document-container {
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
        }

        .document-actions .btn {
            transition: all 0.2s ease;
        }

        .document-actions .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Document separators */
        .document-container+hr {
            border: none;
            height: 2px;
            background: linear-gradient(to right, transparent, #dee2e6, transparent);
            margin: 2rem 0;
        }

        /* Training-Style Table Document Previews - Matching FishR */
        .training-table-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        .training-document-previews {
            display: flex;
            gap: 0.25rem;
            align-items: center;
        }

        .training-mini-doc {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: white;
            border: 2px solid #17a2b8;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .training-mini-doc:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }

        .training-mini-doc-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .training-mini-doc-more {
            background: #f8f9fa;
            border-color: #dee2e6;
        }

        .training-mini-doc-more:hover {
            background: #e9ecef;
            border-color: #6c757d;
        }

        .training-more-count {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
        }

        .training-mini-doc-more:hover .training-more-count {
            color: #495057;
        }

        .training-document-summary {
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .training-document-summary:hover {
            color: #007bff !important;
        }

        .training-document-summary:hover small {
            color: #007bff !important;
        }

        .training-no-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem;
            opacity: 0.7;
        }

        .training-no-documents i {
            font-size: 1.25rem;
        }

        /* Document type specific colors for mini previews */
        .training-mini-doc[title*="Document 1"] {
            border-color: #17a2b8;
        }

        .training-mini-doc[title*="Document 1"]:hover {
            background-color: rgba(23, 162, 184, 0.1);
        }

        .training-mini-doc[title*="Document 2"] {
            border-color: #28a745;
        }

        .training-mini-doc[title*="Document 2"]:hover {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .training-mini-doc[title*="Document 3"] {
            border-color: #ffc107;
        }

        .training-mini-doc[title*="Document 3"]:hover {
            background-color: rgba(255, 193, 7, 0.1);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #documentModal .modal-dialog {
                margin: 0.5rem;
            }

            #documentViewer {
                padding: 0.5rem;
            }

            .document-actions .btn {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }

            .training-table-documents {
                gap: 0.25rem;
            }

            .training-mini-doc {
                width: 28px;
                height: 28px;
            }

            .training-mini-doc-icon {
                font-size: 0.75rem;
            }

            .training-more-count {
                font-size: 0.65rem;
            }
        }

        /* Toast Notification Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        /* Individual Toast Notification */
        .toast-notification {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 380px;
            max-width: 600px;
            overflow: hidden;
            opacity: 0;
            transform: translateX(400px);
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
            pointer-events: auto;
        }

        .toast-notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        /* Toast Content */
        .toast-notification .toast-content {
            display: flex;
            align-items: center;
            padding: 20px;
            font-size: 1.05rem;
        }

        .toast-notification .toast-content i {
            font-size: 1.5rem;
        }

        .toast-notification .toast-content span {
            flex: 1;
            color: #333;
        }

        /* Type-specific styles */
        .toast-notification.toast-success {
            border-left: 4px solid #28a745;
        }

        .toast-notification.toast-success .toast-content i {
            color: #28a745;
        }

        .toast-notification.toast-error {
            border-left: 4px solid #dc3545;
        }

        .toast-notification.toast-error .toast-content i {
            color: #dc3545;
        }

        .toast-notification.toast-warning {
            border-left: 4px solid #ffc107;
        }

        .toast-notification.toast-warning .toast-content i {
            color: #ffc107;
        }

        .toast-notification.toast-info {
            border-left: 4px solid #17a2b8;
        }

        .toast-notification.toast-info .toast-content i {
            color: #17a2b8;
        }

        /* Confirmation Toast */
        .confirmation-toast {
            min-width: 420px;
            max-width: 650px;
        }

        .confirmation-toast .toast-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            font-weight: 600;
        }

        .confirmation-toast .toast-body {
            padding: 16px;
            background: #f8f9fa;
        }

        .confirmation-toast .toast-body p {
            margin: 0;
            font-size: 0.95rem;
            color: #333;
            line-height: 1.5;
        }

        .btn-close-toast {
            width: auto;
            height: auto;
            padding: 0;
            font-size: 1.2rem;
            opacity: 0.5;
            transition: opacity 0.2s;
            background: none;
            border: none;
            cursor: pointer;
        }

        .btn-close-toast:hover {
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
            }

            .toast-notification,
            .confirmation-toast {
                min-width: auto;
                max-width: 100%;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let searchTimeout;

        // Auto search functionality
        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500); // Wait 500ms after user stops typing
        }

        // Submit filter form when dropdowns change
        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        // Helper function to get status display text
        function getStatusText(status) {
            switch (status) {
                case 'pending':
                    return 'Pending';
                case 'under_review':
                    return 'Under Review';
                case 'approved':
                    return 'Approved';
                case 'rejected':
                    return 'Rejected';
                default:
                    return status;
            }
        }

        // Enhanced show update modal function
        function showUpdateModal(id, currentStatus) {
                document.getElementById('updateAppNumber').innerHTML = `
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>`;

                fetch(`/admin/training/requests/${id}`)
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(response => {
                        if (!response.success) throw new Error('Failed to load application details');

                        const data = response.data;
                        document.getElementById('updateApplicationId').value = id;
                        document.getElementById('updateAppNumber').textContent = data.application_number;
                        document.getElementById('updateAppName').textContent = data.full_name;
                        document.getElementById('updateAppMobile').textContent = data.contact_number || 'N/A';
                        document.getElementById('updateAppTraining').textContent = data.training_type_display;
                        document.getElementById('updateAppCurrentStatus').innerHTML = `
                        <span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;

                        const statusSelect = document.getElementById('newStatus');
                        const remarksTextarea = document.getElementById('remarks');

                        statusSelect.value = data.status;
                        statusSelect.dataset.originalStatus = data.status;
                        
                        // CRITICAL FIX: Store remarks exactly as it comes from DB
                        const remarksValue = data.remarks || '';
                        remarksTextarea.value = remarksValue;
                        remarksTextarea.dataset.originalRemarks = remarksValue;

                        statusSelect.classList.remove('form-changed');
                        remarksTextarea.classList.remove('form-changed');
                        statusSelect.parentElement.classList.remove('change-indicator', 'changed');
                        remarksTextarea.parentElement.classList.remove('change-indicator', 'changed');

                        statusSelect.parentElement.classList.add('change-indicator');
                        remarksTextarea.parentElement.classList.add('change-indicator');

                        updateRemarksCounter();

                        const modal = new bootstrap.Modal(document.getElementById('updateModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Error loading application details: ' + error.message);
                    });
            }

                
                function updateApplicationStatus() {
                const id = document.getElementById('updateApplicationId').value;
                const newStatus = document.getElementById('newStatus').value;
                const remarks = document.getElementById('remarks').value;

                if (!newStatus) {
                    showToast('error', 'Please select a status');
                    return;
                }

                const originalStatus = document.getElementById('newStatus').dataset.originalStatus;
                const originalRemarks = document.getElementById('remarks').dataset.originalRemarks || '';

                console.log('Current Status:', newStatus, 'Original:', originalStatus);
                console.log('Current Remarks:', remarks, 'Original:', originalRemarks);

                if (newStatus === originalStatus && remarks === originalRemarks) {
                    showToast('warning', 'No changes detected. Please modify the status or remarks before updating.');
                    return;
                }

                let changesSummary = [];
                if (newStatus !== originalStatus) {
                    const originalStatusText = getStatusText(originalStatus);
                    const newStatusText = getStatusText(newStatus);
                    changesSummary.push(`Status: ${originalStatusText} → ${newStatusText}`);
                }
                if (remarks !== originalRemarks) {
                    if (originalRemarks === '') {
                        changesSummary.push('Remarks: Added new remarks');
                    } else if (remarks === '') {
                        changesSummary.push('Remarks: Removed existing remarks');
                    } else {
                        changesSummary.push('Remarks: Modified');
                    }
                }

                showConfirmationToast(
                    'Confirm Update',
                    `Update this training request with the following changes?\n\n${changesSummary.join('\n')}`,
                    () => proceedWithStatusUpdate(id, newStatus, remarks)
                );
            }

            function proceedWithStatusUpdate(id, newStatus, remarks) {
                const updateButton = document.querySelector('#updateModal .btn-primary');
                const originalText = updateButton.innerHTML;
                updateButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
                updateButton.disabled = true;

                // Build payload - ENSURE remarks is included
                const payload = {
                    status: newStatus,
                    remarks: remarks || null
                };

                console.log('Sending Payload:', payload);

                fetch(`/admin/training/requests/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(response => {
                        console.log('Server Response:', response);
                        if (response.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                            modal.hide();
                            showToast('success', response.message);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            throw new Error(response.message || 'Error updating status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Error updating application status: ' + error.message);
                    })
                    .finally(() => {
                        updateButton.innerHTML = originalText;
                        updateButton.disabled = false;
                    });
            }

            // Update remarks character counter
            function updateRemarksCounter() {
                const textarea = document.getElementById('remarks');
                const charCount = document.getElementById('charCountRemarks');
                
                if (textarea && charCount) {
                    charCount.textContent = textarea.value.length;
                    
                    if (textarea.value.length > 900) {
                        charCount.parentElement.classList.add('text-warning');
                        charCount.parentElement.classList.remove('text-muted');
                    } else {
                        charCount.parentElement.classList.remove('text-warning');
                        charCount.parentElement.classList.add('text-muted');
                    }
                }
            }

            //  Helper - get status text
            function getStatusText(status) {
                switch (status) {
                    case 'pending':
                        return 'Pending';
                    case 'under_review':
                        return 'Under Review';
                    case 'approved':
                        return 'Approved';
                    case 'rejected':
                        return 'Rejected';
                    default:
                        return status;
                }
            }

            // Initialize on document ready
            document.addEventListener('DOMContentLoaded', function() {
                const remarksTextarea = document.getElementById('remarks');
                if (remarksTextarea) {
                    remarksTextarea.addEventListener('input', updateRemarksCounter);
                }
            });
       
        // UPDATED: View application details - FIXED remarks display
        function viewApplication(id) {
            const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
            const detailsContainer = document.getElementById('applicationDetails');
            
            // Show loading state
            detailsContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Loading application details...</p>
                </div>`;
            
            modal.show();

            fetch(`/admin/training/requests/${id}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(response => {
                    if (!response.success) throw new Error('Failed to load application details');

                    const data = response.data;

                    // Supporting documents
                    let documentHtml = '';
                    if (data.document_path) {
                        documentHtml = `
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white text-center">
                                    <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i>Supporting Document</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="p-4 border border-primary rounded bg-light">
                                        <i class="fas fa-file-alt fa-3x mb-3" style="color: #0d6efd;"></i>
                                        <h6>Supporting Document</h6>
                                        <span class="badge bg-primary mb-3">Uploaded</span>
                                        <br>
                                        <button class="btn btn-sm btn-outline-primary mt-2"
                                            onclick="viewDocument('${data.document_path}', 'Training Request - ${escapeHtml(data.full_name)}')">
                                            <i class="fas fa-eye me-1"></i>View Document
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    } else {
                        documentHtml = `
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white text-center">
                                    <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i>Supporting Document</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="p-4 border border-secondary rounded">
                                        <i class="fas fa-file-slash fa-3x mb-3" style="color: #6c757d;"></i>
                                        <h6>No Document Uploaded</h6>
                                        <span class="badge bg-secondary">Not Uploaded</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }

                    // Build remarks HTML - ONLY IF THERE ARE REMARKS
                    let remarksHtml = '';
                    if (data.remarks && String(data.remarks).trim() !== '') {
                        remarksHtml = `
                        <!-- Remarks Card -->
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Admin Remarks</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0" style="white-space: pre-wrap; word-wrap: break-word;">${escapeHtml(data.remarks)}</p>
                                </div>
                            </div>
                        </div>`;
                    }

                    detailsContainer.innerHTML = `
                        <div class="row g-4">

                            <!-- Personal Information Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <strong>Application #:</strong>
                                                <span class="text-primary d-block">${escapeHtml(data.application_number)}</span>
                                            </div>
                                            <div class="col-12">
                                                <strong>Full Name:</strong>
                                                <span class="d-block">${escapeHtml(data.full_name)}</span>
                                            </div>
                                            <div class="col-12">
                                                <strong>Contact Number:</strong>
                                                <span>
                                                    <a href="tel:${data.contact_number}" class="text-decoration-none">${escapeHtml(data.contact_number || 'N/A')}</a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <strong>Barangay:</strong>
                                                <span class="d-block">${escapeHtml(data.barangay || 'N/A')}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Training Information Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-book me-2"></i>Training Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <strong>Training Type:</strong>
                                                <span class="badge bg-info d-block mt-1" style="width: fit-content;">${escapeHtml(data.training_type_display)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Information Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Status & Timeline</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <strong>Current Status:</strong>
                                                <div class="mt-2">
                                                    <span class="badge bg-${data.status_color}" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">${escapeHtml(data.formatted_status)}</span>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <strong>Date Applied:</strong>
                                                <span class="d-block">${escapeHtml(data.created_at)}</span>
                                            </div>
                                            <div class="col-12">
                                                <strong>Last Updated:</strong>
                                                <span class="d-block">${escapeHtml(data.updated_at)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Supporting Document Card -->
                            ${documentHtml}

                            <!-- Remarks Card (ONLY SHOWN IF REMARKS EXIST) -->
                            ${remarksHtml}

                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', error.message || 'Error loading application details. Please try again.');
                    detailsContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${error.message || 'Error loading application details. Please try again.'}
                        </div>`;
                });
        }

        // HELPER: Escape HTML
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(unsafe).replace(/[&<>"']/g, m => map[m]);
        }

        // UNIFIED document viewing function
        function viewDocument(path, filename = null, applicationId = null) {
            // Input validation
            if (!path || path.trim() === '') {
                showToast('error', 'No document path provided');
                return;
            }

            const documentViewer = document.getElementById('documentViewer');
            const modal = new bootstrap.Modal(document.getElementById('documentModal'));

            // Show loading state first
            documentViewer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Loading document...</p>
                </div>`;

            // Show modal immediately with loading state
            modal.show();

            // Update modal title if filename is provided
            const modalTitle = document.querySelector('#documentModal .modal-title');
            if (filename) {
                modalTitle.innerHTML = `<i class="fas fa-file-alt me-2"></i>${filename}`;
            } else {
                modalTitle.innerHTML = `<i class="fas fa-file-alt me-2"></i>Supporting Document`;
            }

            // Extract file extension and name
            const fileExtension = path.split('.').pop().toLowerCase();
            const fileName = filename || path.split('/').pop();
            const fileUrl = `/storage/${path}`;

            // Define supported file types
            const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            const documentTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
            const videoTypes = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
            const audioTypes = ['mp3', 'wav', 'ogg', 'aac', 'm4a'];

            // Function to add download button
            const addDownloadButton = () => {
                return `
                    <div class="text-center mt-3 p-3 bg-light">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                            </a>
                            <a href="${fileUrl}" download="${fileName}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                        <small class="text-muted">File: ${fileName} (${fileExtension.toUpperCase()})</small>
                    </div>`;
            };

            // Handle different file types
            setTimeout(() => {
                try {
                    if (imageTypes.includes(fileExtension)) {
                        // Handle images
                        const img = new Image();
                        img.onload = function() {
                            documentViewer.innerHTML = `
                                <div class="text-center">
                                    <div class="position-relative d-inline-block">
                                        <img src="${fileUrl}"
                                            class="img-fluid border rounded shadow-sm"
                                            alt="Supporting Document"
                                            style="max-height: 70vh; cursor: zoom-in;"
                                            onclick="toggleImageZoom(this)">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-dark bg-opacity-75">${this.naturalWidth}x${this.naturalHeight}</span>
                                        </div>
                                    </div>
                                    ${addDownloadButton()}
                                </div>`;
                        };
                        img.onerror = function() {
                            documentViewer.innerHTML = `
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                    <h5>Unable to Load Image</h5>
                                    <p class="mb-3">The image could not be loaded.</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                            <i class="fas fa-external-link-alt me-2"></i>Open Image
                                        </a>
                                        <a href="${fileUrl}" download="${fileName}" class="btn btn-success">
                                            <i class="fas fa-download me-2"></i>Download
                                        </a>
                                    </div>
                                    <small class="text-muted d-block mt-2">File: ${fileName}</small>
                                </div>`;
                        };
                        img.src = fileUrl;

                    } else if (fileExtension === 'pdf') {
                        // Handle PDF documents
                        documentViewer.innerHTML = `
                            <div class="pdf-container">
                                <embed src="${fileUrl}"
                                    type="application/pdf"
                                    width="100%"
                                    height="600px"
                                    class="border rounded">
                                ${addDownloadButton()}
                            </div>`;

                        // Check if PDF loaded successfully after a short delay
                        setTimeout(() => {
                            const embed = documentViewer.querySelector('embed');
                            if (!embed || embed.offsetHeight === 0) {
                                documentViewer.innerHTML = `
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                        <h5>PDF Preview Unavailable</h5>
                                        <p class="mb-3">Your browser doesn't support PDF preview or the file couldn't be loaded.</p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                                <i class="fas fa-external-link-alt me-2"></i>Open PDF
                                            </a>
                                            <a href="${fileUrl}" download="${fileName}" class="btn btn-success">
                                                <i class="fas fa-download me-2"></i>Download PDF
                                            </a>
                                        </div>
                                        <small class="text-muted d-block mt-2">File: ${fileName}</small>
                                    </div>`;
                            }
                        }, 2000);

                    } else if (videoTypes.includes(fileExtension)) {
                        // Handle video files
                        documentViewer.innerHTML = `
                            <div class="text-center">
                                <video controls class="w-100" style="max-height: 70vh;" preload="metadata">
                                    <source src="${fileUrl}" type="video/${fileExtension}">
                                    Your browser does not support the video tag.
                                </video>
                                ${addDownloadButton()}
                            </div>`;

                    } else if (audioTypes.includes(fileExtension)) {
                        // Handle audio files
                        documentViewer.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fas fa-music fa-4x text-info mb-3"></i>
                                <h5>Audio File</h5>
                                <audio controls class="w-100 mb-3">
                                    <source src="${fileUrl}" type="audio/${fileExtension}">
                                    Your browser does not support the audio tag.
                                </audio>
                                ${addDownloadButton()}
                            </div>`;

                    } else if (documentTypes.includes(fileExtension)) {
                        // Handle other document types
                        const docIcon = fileExtension === 'pdf' ? 'file-pdf' : ['doc', 'docx'].includes(
                            fileExtension) ? 'file-word' : 'file-alt';

                        documentViewer.innerHTML = `
                            <div class="alert alert-info text-center">
                                <i class="fas fa-${docIcon} fa-4x text-primary mb-3"></i>
                                <h5>${fileExtension.toUpperCase()} Document</h5>
                                <p class="mb-3">This document type cannot be previewed directly in the browser.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-2"></i>Open Document
                                    </a>
                                    <a href="${fileUrl}" download="${fileName}" class="btn btn-success">
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                </div>
                                <small class="text-muted d-block mt-2">File: ${fileName}</small>
                            </div>`;

                    } else {
                        // Handle unsupported file types
                        documentViewer.innerHTML = `
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-file fa-4x text-warning mb-3"></i>
                                <h5>Unsupported File Type</h5>
                                <p class="mb-3">The file type ".${fileExtension}" is not supported for preview.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-2"></i>Open File
                                    </a>
                                    <a href="${fileUrl}" download="${fileName}" class="btn btn-success">
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                </div>
                                <small class="text-muted d-block mt-2">File: ${fileName}</small>
                            </div>`;
                    }
                } catch (error) {
                    console.error('Error processing document:', error);
                    documentViewer.innerHTML = `
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                            <h5>Error Loading Document</h5>
                            <p class="mb-3">${error.message}</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt me-2"></i>Try Opening Directly
                                </a>
                                <a href="${fileUrl}" download="${fileName}" class="btn btn-success">
                                    <i class="fas fa-download me-2"></i>Download
                                </a>
                            </div>
                        </div>`;
                }
            }, 500);
        }

        // Helper function to toggle image zoom for training
        function toggleImageZoomTraining(img) {
            if (img.style.transform === 'scale(2)') {
                img.style.transform = 'scale(1)';
                img.style.cursor = 'zoom-in';
                img.style.transition = 'transform 0.3s ease';
                img.style.zIndex = 'auto';
            } else {
                img.style.transform = 'scale(2)';
                img.style.cursor = 'zoom-out';
                img.style.transition = 'transform 0.3s ease';
                img.style.zIndex = '1050';
            }
        }

        // Helper function to show image error
        function showImageError(img, fileName, path) {
            const errorHtml = `
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                    <h6>Unable to preview ${fileName}</h6>
                    <p class="mb-3">The image could not be loaded or displayed.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="/storage/${path}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                        </a>
                        <a href="/storage/${path}" download="${fileName}" class="btn btn-success">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>
                </div>
            `;
            img.parentElement.innerHTML = errorHtml;
        }

        // Function to check for changes and provide visual feedback
        function checkForChanges() {
            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');

            if (!statusSelect.dataset.originalStatus) return;

            const statusChanged = statusSelect.value !== statusSelect.dataset.originalStatus;
            const remarksChanged = remarksTextarea.value.trim() !== (remarksTextarea.dataset.originalRemarks || '').trim();

            // Visual feedback for status field
            statusSelect.classList.toggle('form-changed', statusChanged);
            statusSelect.parentElement.classList.toggle('changed', statusChanged);

            // Visual feedback for remarks field
            remarksTextarea.classList.toggle('form-changed', remarksChanged);
            remarksTextarea.parentElement.classList.toggle('changed', remarksChanged);

            // Update button state
            const updateButton = document.querySelector('#updateModal .btn-primary');
            updateButton.classList.toggle('no-changes', !statusChanged && !remarksChanged);

            // Update button text based on changes
            if (!statusChanged && !remarksChanged) {
                updateButton.innerHTML = '<i class="fas fa-edit me-1"></i>No Changes';
            } else {
                updateButton.innerHTML = '<i class="fas fa-save me-1"></i>Update Status';
            }
        }

        // Add event listeners when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');

            if (statusSelect) {
                statusSelect.addEventListener('change', checkForChanges);
            }

            if (remarksTextarea) {
                remarksTextarea.addEventListener('input', checkForChanges);
            }
        });

        // Enhanced Date Filter Functions
        function setDateRangeModal(period) {
            const today = new Date();
            let startDate, endDate;

            switch (period) {
                case 'today':
                    startDate = endDate = today;
                    break;
                case 'week':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6); // End of week (Saturday)
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of month
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of month
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1); // First day of year
                    endDate = new Date(today.getFullYear(), 11, 31); // Last day of year
                    break;
            }

            // Format dates to YYYY-MM-DD
            const startDateStr = startDate.toISOString().split('T')[0];
            const endDateStr = endDate.toISOString().split('T')[0];

            // Update modal inputs
            document.getElementById('modal_date_from').value = startDateStr;
            document.getElementById('modal_date_to').value = endDateStr;

            // Apply the filter immediately
            applyDateFilter(startDateStr, endDateStr);
        }

        function applyCustomDateRange() {
            const dateFrom = document.getElementById('modal_date_from').value;
            const dateTo = document.getElementById('modal_date_to').value;

            if (dateFrom && dateTo && dateFrom > dateTo) {
                showToast('warning', 'From date cannot be later than To date');
                return;
            }

            applyDateFilter(dateFrom, dateTo);
        }

        function applyDateFilter(dateFrom, dateTo) {
            // Update hidden inputs
            document.getElementById('date_from').value = dateFrom;
            document.getElementById('date_to').value = dateTo;

            // Update status display
            updateDateFilterStatus(dateFrom, dateTo);

            // Close modal and submit form
            const modal = bootstrap.Modal.getInstance(document.getElementById('dateFilterModal'));
            if (modal) modal.hide();

            submitFilterForm();
        }

        function clearDateRangeModal() {
            document.getElementById('modal_date_from').value = '';
            document.getElementById('modal_date_to').value = '';
            applyDateFilter('', '');
        }

        function updateDateFilterStatus(dateFrom, dateTo) {
            const statusElement = document.getElementById('dateFilterStatus');
            if (!dateFrom && !dateTo) {
                statusElement.innerHTML = 'No date filter applied - showing all applications';
            } else {
                let statusText = 'Current filter: ';
                if (dateFrom) {
                    const fromDate = new Date(dateFrom).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    statusText += `From ${fromDate} `;
                }
                if (dateTo) {
                    const toDate = new Date(dateTo).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    statusText += `To ${toDate}`;
                }
                statusElement.innerHTML = statusText;
            }
        }
        // Create toast container
        function createToastContainer() {
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container';
                document.body.appendChild(container);
            }
            return container;
        }

        // Toast notification function
        function showToast(type, message) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const iconMap = {
                'success': {
                    icon: 'fas fa-check-circle',
                    color: 'success'
                },
                'error': {
                    icon: 'fas fa-exclamation-circle',
                    color: 'danger'
                },
                'warning': {
                    icon: 'fas fa-exclamation-triangle',
                    color: 'warning'
                },
                'info': {
                    icon: 'fas fa-info-circle',
                    color: 'info'
                }
            };

            const config = iconMap[type] || iconMap['info'];

            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="${config.icon} me-2" style="color: var(--bs-${config.color});"></i>
                    <span>${message}</span>
                    <button type="button" class="btn-close btn-close-toast ms-auto" onclick="removeToast(this.closest('.toast-notification'))"></button>
                </div>
            `;

            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);

            setTimeout(() => {
                if (document.contains(toast)) {
                    removeToast(toast);
                }
            }, 5000);
        }

        // Confirmation toast function
        function showConfirmationToast(title, message, onConfirm) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const toast = document.createElement('div');
            toast.className = 'toast-notification confirmation-toast';

            toast.dataset.confirmCallback = Math.random().toString(36);
            window[toast.dataset.confirmCallback] = onConfirm;

            toast.innerHTML = `
                <div class="toast-header">
                    <i class="fas fa-question-circle me-2 text-warning"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-toast" onclick="removeToast(this.closest('.toast-notification'))"></button>
                </div>
                <div class="toast-body">
                    <p class="mb-3" style="white-space: pre-wrap;">${message}</p>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="removeToast(this.closest('.toast-notification'))">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmToastAction(this)">
                            <i class="fas fa-check me-1"></i>Confirm
                        </button>
                    </div>
                </div>
            `;

            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);

            setTimeout(() => {
                if (document.contains(toast)) {
                    removeToast(toast);
                }
            }, 10000);
        }

        function confirmToastAction(button) {
            const toast = button.closest('.toast-notification');
            const callbackId = toast.dataset.confirmCallback;
            const callback = window[callbackId];

            if (typeof callback === 'function') {
                try {
                    callback();
                } catch (error) {
                    console.error('Error executing confirmation callback:', error);
                }
            }

            delete window[callbackId];
            removeToast(toast);
        }

        function removeToast(toastElement) {
            toastElement.classList.remove('show');
            setTimeout(() => {
                if (toastElement.parentElement) {
                    toastElement.remove();
                }
            }, 300);
        }

        // Global variable to track current delete ID
        let currentDeleteTrainingId = null;

        /**
         * Updated deleteApplication function to use modal
         */
        function deleteApplication(id, applicationNumber) {
            try {
                // Set the global variable
                currentDeleteTrainingId = id;

                // Update modal with application number
                document.getElementById('delete_training_name').textContent = applicationNumber;

                // Show the delete modal
                new bootstrap.Modal(document.getElementById('deleteTrainingModal')).show();
            } catch (error) {
                console.error('Error preparing delete dialog:', error);
                showToast('error', 'Failed to prepare delete dialog');
            }
        }

        /**
         * Confirm permanent delete for Training request
         */
        async function confirmPermanentDeleteTraining() {
            if (!currentDeleteTrainingId) {
                showToast('error', 'Request ID not found');
                return;
            }

            try {
                // Show loading state
                const deleteBtn = document.getElementById('confirm_delete_training_btn');
                deleteBtn.querySelector('.btn-text').style.display = 'none';
                deleteBtn.querySelector('.btn-loader').style.display = 'inline';
                deleteBtn.disabled = true;

                const response = await fetch(`/admin/training/requests/${currentDeleteTrainingId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete application');
                }

                // Close modal
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteTrainingModal'));
                if (deleteModal) {
                    deleteModal.hide();
                }

                // Show success message
                showToast('success', data.message || 'Training request deleted successfully');

                // Remove the row with animation
                const row = document.querySelector(`tr[data-application-id="${currentDeleteTrainingId}"]`);
                if (row) {
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                }

                // Reload page to refresh statistics
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

                // Reset for next use
                currentDeleteTrainingId = null;

            } catch (error) {
                console.error('Error deleting application:', error);
                
                // Close modal first
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteTrainingModal'));
                if (deleteModal) {
                    deleteModal.hide();
                }

                // Show error
                showToast('error', 'Error deleting application: ' + error.message);

            } finally {
                // Reset button state
                const deleteBtn = document.getElementById('confirm_delete_training_btn');
                deleteBtn.querySelector('.btn-text').style.display = 'inline';
                deleteBtn.querySelector('.btn-loader').style.display = 'none';
                deleteBtn.disabled = false;
            }
        }

        /**
         * Clean up modal on close
         */
        document.addEventListener('DOMContentLoaded', function() {
            const deleteTrainingModal = document.getElementById('deleteTrainingModal');
            if (deleteTrainingModal) {
                deleteTrainingModal.addEventListener('hidden.bs.modal', function() {
                    // Reset button state
                    const deleteBtn = document.getElementById('confirm_delete_training_btn');
                    deleteBtn.querySelector('.btn-text').style.display = 'inline';
                    deleteBtn.querySelector('.btn-loader').style.display = 'none';
                    deleteBtn.disabled = false;

                    // Remove any lingering backdrops
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Remove modal-open class from body
                    document.body.classList.remove('modal-open');

                    // Reset body overflow
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    // Reset global variable
                    currentDeleteTrainingId = null;

                    console.log('Delete Training modal cleaned up');
                });
            }
        });

        // Proceed with application deletion
        function proceedWithApplicationDelete(id, applicationNumber) {
            fetch(`/admin/training/requests/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message || 'Application deleted successfully');

                        // Remove row from table with animation
                        const row = document.querySelector(`tr[data-application-id="${id}"]`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();

                                // Check if table is empty
                                const tbody = document.querySelector('#applicationsTable tbody');
                                if (tbody.children.length === 0) {
                                    // Reload page to show empty state
                                    setTimeout(() => window.location.reload(), 1500);
                                }
                            }, 300);
                        } else {
                            // Fallback: reload page
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    } else {
                        throw new Error(data.message || 'Failed to delete application');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Failed to delete application: ' + error.message);
                });
        }

        // Get CSRF token utility function
        function getCSRFToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            return metaTag ? metaTag.getAttribute('content') : '';
        }

        // Show add training modal
        function showAddTrainingModal() {
            const modal = new bootstrap.Modal(document.getElementById('addTrainingModal'));

            // Reset form
            document.getElementById('addTrainingForm').reset();

            // Remove any validation errors
            document.querySelectorAll('#addTrainingModal .is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('#addTrainingModal .invalid-feedback').forEach(el => el.remove());

            // Clear document preview
            const preview = document.getElementById('training_doc_preview');
            if (preview) {
                preview.innerHTML = '';
            }

            modal.show();
        }


        // Document preview for file
        function previewTrainingDocument() {
            const input = document.getElementById('training_supporting_document');
            const preview = document.getElementById('training_doc_preview');

            if (!input.files || input.files.length === 0) {
                if (preview) preview.innerHTML = '';
                return;
            }

            const file = input.files[0];

            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                showToast('error', 'File exceeds 10MB limit');
                input.value = '';
                if (preview) preview.innerHTML = '';
                return;
            }

            const fileExtension = file.name.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension);

            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                    <div style="width: 120px;">
                        <img src="${e.target.result}" alt="Preview"
                            style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <p style="margin-top: 8px; font-size: 11px; color: #666; word-break: break-all;">
                            <i class="fas fa-file-image me-1"></i>${file.name}
                        </p>
                    </div>
                `;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `
                <div style="width: 120px;">
                    <div class="text-center p-3 border rounded" style="height: 120px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                    </div>
                    <p style="margin-top: 8px; font-size: 11px; color: #666; word-break: break-all;">
                        ${file.name}
                    </p>
                </div>
            `;
            }
        }

        // Validate training form
        function validateTrainingForm() {
            let isValid = true;

            // Required fields
            const requiredFields = [{
                    id: 'training_first_name',
                    label: 'First Name'
                },
                {
                    id: 'training_last_name',
                    label: 'Last Name'
                },
                {
                    id: 'training_barangay',
                    label: 'Barangay'
                },
                {
                    id: 'training_contact_number',
                    label: 'Contact Number'
                },
                {
                    id: 'training_type',
                    label: 'Training Type'
                },
                {
                    id: 'training_status',
                    label: 'Status'
                }
            ];

            requiredFields.forEach(field => {
                const input = document.getElementById(field.id);
                if (input && (!input.value || input.value.trim() === '')) {
                    const feedback = input.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();

                    input.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = field.label + ' is required';
                    input.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            });

            // Validate contact number
            const contactNumber = document.getElementById('training_contact_number').value.trim();
            if (!validateTrainingContactNumber(contactNumber)) {
                isValid = false;
            }

            return isValid;
        }

    function submitAddTraining() {
    if (!validateTrainingForm()) {
        showToast('error', 'Please fix all validation errors before submitting');
        return;
    }

     // Auto-capitalize all name fields BEFORE validation
    const firstNameInput = document.getElementById('training_first_name');
    const middleNameInput = document.getElementById('training_middle_name');
    const lastNameInput = document.getElementById('training_last_name');
    
    if (firstNameInput.value) capitalizeTrainingName(firstNameInput);
    if (middleNameInput.value) capitalizeTrainingName(middleNameInput);
    if (lastNameInput.value) capitalizeTrainingName(lastNameInput);

    // NOW validate
    if (!validateTrainingForm()) {
        showToast('error', 'Please fix all validation errors before submitting');
        return;
    }

    const formData = new FormData();
    formData.append('first_name', document.getElementById('training_first_name').value.trim());
    formData.append('middle_name', document.getElementById('training_middle_name').value.trim());
    formData.append('last_name', document.getElementById('training_last_name').value.trim());
    formData.append('name_extension', document.getElementById('training_name_extension').value);
    formData.append('barangay', document.getElementById('training_barangay').value.trim());
    formData.append('contact_number', document.getElementById('training_contact_number').value.trim());
    formData.append('training_type', document.getElementById('training_type').value);
    formData.append('status', document.getElementById('training_status').value);
    formData.append('remarks', document.getElementById('training_remarks').value.trim());

    const docInput = document.getElementById('training_supporting_document');
    if (docInput.files && docInput.files.length > 0) {
        formData.append('supporting_document', docInput.files[0]);
    }

    const submitBtn = document.querySelector('#addTrainingModal .btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating...';
    submitBtn.disabled = true;

    fetch('/admin/training/requests/create', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addTrainingModal'));
                modal.hide();
                showToast('success', data.message || 'Training registration created successfully');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById('training_' + field);
                        if (input) {
                            const feedback = input.parentNode.querySelector('.invalid-feedback');
                            if (feedback) feedback.remove();
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback d-block';
                            errorDiv.textContent = data.errors[field][0];
                            input.parentNode.appendChild(errorDiv);
                        }
                    });
                }
                showToast('error', data.message || 'Failed to create training request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'An error occurred while creating the request');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
}
        // Helper function to format file sizes
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Get all modals on the page
            const modals = document.querySelectorAll('.modal');

            modals.forEach(modal => {
                // Handle modal hidden event
                modal.addEventListener('hidden.bs.modal', function() {
                    // Remove any remaining backdrops
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Remove modal-open class from body if no modals are open
                    const openModals = document.querySelectorAll('.modal.show');
                    if (openModals.length === 0) {
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }

                    // Remove any greyscale filters
                    document.body.style.filter = '';
                    document.body.style.opacity = '';

                    // Re-enable scrolling
                    document.documentElement.style.overflow = '';
                });

                // Handle modal shown event
                modal.addEventListener('show.bs.modal', function() {
                    // Ensure greyscale is removed when opening
                    document.body.style.filter = '';
                    document.body.style.opacity = '';
                });

                // Clean up on close button click
                const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
                closeButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Force cleanup after a short delay
                        setTimeout(() => {
                            const backdrops = document.querySelectorAll(
                                '.modal-backdrop');
                            backdrops.forEach(backdrop => backdrop.remove());

                            document.body.style.filter = '';
                            document.body.style.opacity = '';
                        }, 100);
                    });
                });
            });
        });

        // Additional cleanup function to call if needed
        function cleanupModals() {
            // Remove all modal backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Reset body styles
            document.body.classList.remove('modal-open');
            document.body.style.filter = '';
            document.body.style.opacity = '';
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Reset html styles
            document.documentElement.style.overflow = '';
        }

        // Call cleanup when page becomes visible (in case of browser tab switching)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // Check if any modals are actually open
                const openModals = document.querySelectorAll('.modal.show');
                if (openModals.length === 0) {
                    cleanupModals();
                }
            }
        });

// Global variable to store current editing training ID
let currentEditingTrainingId = null;

/**
 * Show Edit Training Modal - Opens modal and loads training data
 */
function showEditTrainingModal(trainingId) {
    currentEditingTrainingId = trainingId;
    const modal = new bootstrap.Modal(document.getElementById('editTrainingModal'));
    
    // Show loading state
    document.getElementById('editTrainingNumber').textContent = 'Loading...';
    
    // Show modal
    modal.show();
    
    // Fetch training details
    fetch(`/admin/training/requests/${trainingId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(response => {
            if (!response.success) throw new Error(response.message || 'Failed to load training');
            
            const data = response.data;
            
            // Update modal title
            document.getElementById('editTrainingNumber').textContent = data.application_number;
            
            // Initialize form with data
            initializeEditTrainingForm(trainingId, data);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error loading training: ' + error.message);
            modal.hide();
        });
}

/**
 * Initialize Edit Form with training data
 */
function initializeEditTrainingForm(trainingId, data) {
    const form = document.getElementById('editTrainingForm');
    
    // Populate personal information
    document.getElementById('edit_training_first_name').value = data.first_name || '';
    document.getElementById('edit_training_middle_name').value = data.middle_name || '';
    document.getElementById('edit_training_last_name').value = data.last_name || '';
    document.getElementById('edit_training_extension').value = data.name_extension || '';
    document.getElementById('edit_training_contact_number').value = data.contact_number || '';
    
    // Populate location
    document.getElementById('edit_training_barangay').value = data.barangay || '';
    
    // Populate training type
    document.getElementById('edit_training_type').value = data.training_type || '';
    
    // Populate application number (read-only)
    document.getElementById('edit_training_app_number').value = data.application_number || '';
    
    // Populate status badge
    const statusBadge = document.getElementById('edit_training_status_badge');
    statusBadge.className = `badge bg-${data.status_color} fs-6`;
    statusBadge.textContent = data.formatted_status;
    
    // Populate date applied
    document.getElementById('edit_training_created_at').textContent = data.created_at || '-';
    
    // Handle document preview
    const previewContainer = document.getElementById('edit_training_supporting_document_preview');
    if (data.document_path) {
        displayEditTrainingExistingDocument(data.document_path, 'edit_training_supporting_document_preview');
    } else {
        previewContainer.innerHTML = '<small class="text-muted d-block">No document currently uploaded</small>';
    }
    
    // Store original data for change detection
    const originalData = {
        first_name: data.first_name || '',
        middle_name: data.middle_name || '',
        last_name: data.last_name || '',
        name_extension: data.name_extension || '',
        contact_number: data.contact_number || '',
        barangay: data.barangay || '',
        training_type: data.training_type || ''
    };
    
    form.dataset.originalData = JSON.stringify(originalData);
    form.dataset.trainingId = trainingId;
    form.dataset.hasChanges = 'false';
    
    // Clear validation states
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    
    // Reset button state
    const submitBtn = document.getElementById('editTrainingSubmitBtn');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
    
    // Add change listeners
    addEditTrainingFormChangeListeners(trainingId);
}

/**
 * Add Change Listeners to Edit Form
 */
function addEditTrainingFormChangeListeners(trainingId) {
    const form = document.getElementById('editTrainingForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('change', () => handleEditTrainingFormChange());
        input.addEventListener('input', () => handleEditTrainingFormChange());
    });
}

/**
 * Handle Edit Form Change
 */
function handleEditTrainingFormChange() {
    const form = document.getElementById('editTrainingForm');
    if (form.dataset.trainingId) {
        checkEditTrainingFormChanges(form.dataset.trainingId);
    }
}

/**
 * Check for Form Changes and Update UI
 */
function checkEditTrainingFormChanges(trainingId) {
    const form = document.getElementById('editTrainingForm');
    if (!form.dataset.originalData) return;

    const originalData = JSON.parse(form.dataset.originalData || '{}');
    let hasChanges = false;

    const fields = [
        'first_name', 'middle_name', 'last_name', 'name_extension',
        'contact_number', 'barangay', 'training_type'
    ];

    fields.forEach(field => {
        const fieldElement = form.querySelector(`[name="${field}"]`);
        if (fieldElement) {
            const currentValue = fieldElement.value;
            const originalValue = originalData[field] || '';

            if (currentValue !== originalValue) {
                hasChanges = true;
                fieldElement.classList.add('form-changed');
            } else {
                fieldElement.classList.remove('form-changed');
            }
        }
    });

    // Also check for file input changes - ONLY if a NEW file was selected
    const fileInput = document.getElementById('edit_training_supporting_document');
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
        hasChanges = true;
    }

    // Update button state
    const submitBtn = document.getElementById('editTrainingSubmitBtn');
    if (hasChanges) {
        submitBtn.classList.remove('no-changes');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
        submitBtn.disabled = false;
        submitBtn.dataset.hasChanges = 'true';
    } else {
        submitBtn.classList.remove('no-changes');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
        submitBtn.disabled = false;
        submitBtn.dataset.hasChanges = 'false';
    }

    form.dataset.hasChanges = hasChanges;
}

/**
 * Validate Edit Training Form
 */
function validateEditTrainingForm() {
    const form = document.getElementById('editTrainingForm');
    let isValid = true;

    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

    const requiredFields = [
        { name: 'first_name', label: 'First Name', element: 'edit_training_first_name' },
        { name: 'last_name', label: 'Last Name', element: 'edit_training_last_name' },
        { name: 'contact_number', label: 'Contact Number', element: 'edit_training_contact_number' },
        { name: 'barangay', label: 'Barangay', element: 'edit_training_barangay' },
        { name: 'training_type', label: 'Training Type', element: 'edit_training_type' }
    ];

    requiredFields.forEach(field => {
        const input = document.getElementById(field.element);
        if (input && (!input.value || input.value.trim() === '')) {
            input.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = field.label + ' is required';
            input.parentNode.appendChild(errorDiv);
            isValid = false;
        }
    });

    const contactInput = document.getElementById('edit_training_contact_number');
    if (contactInput && contactInput.value.trim()) {
        if (!validateEditTrainingContactNumber(contactInput.value.trim())) {
            isValid = false;
        }
    }

    return isValid;
}
// FIXED: handleEditTrainingSubmit - Add validation check BEFORE submission
function handleEditTrainingSubmit() {
    const form = document.getElementById('editTrainingForm');
    const trainingId = form.dataset.trainingId;
    const submitBtn = document.getElementById('editTrainingSubmitBtn');

     // Auto-capitalize all name fields BEFORE validation
    const firstNameInput = document.getElementById('edit_training_first_name');
    const middleNameInput = document.getElementById('edit_training_middle_name');
    const lastNameInput = document.getElementById('edit_training_last_name');
    
    if (firstNameInput.value) capitalizeEditTrainingName(firstNameInput);
    if (middleNameInput.value) capitalizeEditTrainingName(middleNameInput);
    if (lastNameInput.value) capitalizeEditTrainingName(lastNameInput);

    // NOW validate
    if (!validateEditTrainingForm()) {
        showToast('error', 'Please fix all validation errors');
        return;
    }

    if (!validateEditTrainingForm()) {
        showToast('error', 'Please fix all validation errors');
        return;
    }

    const hasChanges = submitBtn?.dataset.hasChanges === 'true';

    if (!hasChanges) {
        showToast('warning', 'No changes detected. Please modify the fields before updating.');
        return;
    }

    const originalData = JSON.parse(form.dataset.originalData || '{}');
    let changedFields = [];

    const fieldLabels = {
        'first_name': 'First Name',
        'middle_name': 'Middle Name',
        'last_name': 'Last Name',
        'name_extension': 'Extension',
        'contact_number': 'Contact Number',
        'barangay': 'Barangay',
        'training_type': 'Training Type',
        'supporting_document': 'Supporting Document'
    };

    const fields = ['first_name', 'middle_name', 'last_name', 'name_extension', 'contact_number', 'barangay', 'training_type'];

    fields.forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input && input.value !== originalData[field]) {
            changedFields.push(fieldLabels[field] || field);
        }
    });

    const fileInput = document.getElementById('edit_training_supporting_document');
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
        changedFields.push('Supporting Document');
    }

    const changesText = changedFields.length > 0 
        ? `Update this training request with the following changes?\n\n• ${changedFields.join('\n• ')}`
        : 'Update this training request?';

    showConfirmationToast(
        'Confirm Update',
        changesText,
        () => proceedWithEditTraining(form, trainingId)
    );
}
/**
 * Proceed with Edit Training Submission - FIXED with PUT/multipart
 */
function proceedWithEditTraining(form, trainingId) {
    const submitBtn = document.getElementById('editTrainingSubmitBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
    submitBtn.disabled = true;
    
    // Build FormData - CRITICAL FIX: Use POST with _method for multipart
    const formData = new FormData();
    
    // Add _method for Laravel to recognize this as PUT request
    formData.append('_method', 'PUT');
    
    // Append all form fields
    formData.append('first_name', document.getElementById('edit_training_first_name').value.trim());
    formData.append('middle_name', document.getElementById('edit_training_middle_name').value.trim());
    formData.append('last_name', document.getElementById('edit_training_last_name').value.trim());
    formData.append('name_extension', document.getElementById('edit_training_extension').value.trim());
    formData.append('contact_number', document.getElementById('edit_training_contact_number').value.trim());
    formData.append('barangay', document.getElementById('edit_training_barangay').value.trim());
    formData.append('training_type', document.getElementById('edit_training_type').value.trim());
    
    // Add CSRF token explicitly
    formData.append('_token', getCSRFToken());
    
    // Add document file if selected
    const fileInput = document.getElementById('edit_training_supporting_document');
    if (fileInput && fileInput.files && fileInput.files[0]) {
        console.log('File selected:', fileInput.files[0].name);
        formData.append('supporting_document', fileInput.files[0]);
    }
    
    console.log('Sending update request for training:', trainingId);
    
    // CRITICAL FIX: Use POST method (not PUT) with _method field for multipart/form-data
    fetch(`/admin/training/requests/${trainingId}`, {
        method: 'POST',  // Changed from PUT to POST
        body: formData,
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
            // DO NOT set Content-Type - let browser set it automatically for multipart/form-data
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (!response.ok && response.status !== 422) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            showToast('success', data.message || 'Training request updated successfully');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTrainingModal'));
            if (modal) {
                modal.hide();
            }
            
            // Wait a moment then reload to show all changes
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else if (data.errors) {
            // Handle validation errors
            console.log('Validation errors:', data.errors);
            
            let errorCount = 0;
            Object.keys(data.errors).forEach(field => {
                // Map backend field names to form input IDs
                const fieldMap = {
                    'first_name': 'edit_training_first_name',
                    'middle_name': 'edit_training_middle_name',
                    'last_name': 'edit_training_last_name',
                    'name_extension': 'edit_training_extension',
                    'contact_number': 'edit_training_contact_number',
                    'barangay': 'edit_training_barangay',
                    'training_type': 'edit_training_type',
                    'supporting_document': 'edit_training_supporting_document'
                };
                
                const inputId = fieldMap[field] || 'edit_training_' + field;
                const input = document.getElementById(inputId);
                
                if (input) {
                    input.classList.add('is-invalid');
                    
                    // Remove old error message if exists
                    const oldError = input.parentNode.querySelector('.invalid-feedback');
                    if (oldError) oldError.remove();
                    
                    // Add new error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = data.errors[field][0] || 'Invalid value';
                    input.parentNode.appendChild(errorDiv);
                    errorCount++;
                }
            });
            
            showToast('error', `Please fix ${errorCount} validation error(s)`);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error updating training request: ' + error.message);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Display existing Training documents in edit modal
 */
function displayEditTrainingExistingDocument(documentPath, previewElementId) {
    const preview = document.getElementById(previewElementId);
    if (!preview) {
        console.error('Preview element not found:', previewElementId);
        return;
    }
    
    const fileExtension = documentPath.split('.').pop().toLowerCase();
    const fileName = documentPath.split('/').pop();
    const fileUrl = `/storage/${documentPath}`;
    
    // Image types 
    if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(fileExtension)) {
        preview.innerHTML = `
            <div class="row g-3">
                <div class="col-auto">
                    <div class="document-thumbnail" style="width: 120px; height: 160px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                        <img src="${fileUrl}" alt="Current document" 
                            style="max-width: 100%; max-height: 100%; object-fit: cover; cursor: pointer;"
                            onclick="viewDocument('${documentPath}', '${fileName}')"
                            title="Click to view full document">
                    </div>
                </div>
            </div>
        `;
    } 
    // PDF type 
    else if (fileExtension === 'pdf') {
        preview.innerHTML = `
            <div class="row g-3">
                <div class="col-auto">
                    <div class="document-thumbnail" style="width: 120px; height: 160px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; background: #fff3cd; border: 2px solid #ffc107;">
                        <div class="text-center">
                            <i class="fas fa-file-pdf fa-3x mb-2" style="color: #dc3545;"></i>
                            <small style="display: block; color: #666; font-size: 10px;">PDF</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-column h-100 justify-content-start">
                        <div class="mb-2">
                            <small class="d-block text-success fw-semibold">
                                <i class="fas fa-check-circle me-1"></i>Document Uploaded
                            </small>
                            <small class="d-block text-muted mt-1">${fileName}</small>
                        </div>
                        <div class="mt-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="viewDocument('${documentPath}', '${fileName}')"
                                title="View PDF">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="downloadTrainingDocument('${fileUrl}', '${fileName}')"
                                title="Download PDF">
                                <i class="fas fa-download me-1"></i>Download
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    // Other document types
    else {
        preview.innerHTML = `
            <div class="row g-3">
                <div class="col-auto">
                    <div class="document-thumbnail" style="width: 120px; height: 160px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; background: #e2e3e5; border: 2px solid #6c757d;">
                        <div class="text-center">
                            <i class="fas fa-file fa-3x mb-2" style="color: #6c757d;"></i>
                            <small style="display: block; color: #666; font-size: 10px;">${fileExtension.toUpperCase()}</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-column h-100 justify-content-start">
                        <div class="mb-2">
                            <small class="d-block text-success fw-semibold">
                                <i class="fas fa-check-circle me-1"></i>Document Uploaded
                            </small>
                            <small class="d-block text-muted mt-1">${fileName}</small>
                        </div>
                        <div class="mt-auto">
                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="downloadTrainingDocument('${fileUrl}', '${fileName}')"
                                title="Download document">
                                <i class="fas fa-download me-1"></i>Download
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

/**
 * Preview Edit Training Document
 */
function previewEditTrainingDocument(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    
    if (!input.files || !input.files[0]) {
        preview.innerHTML = '';
        preview.style.display = 'none';
        return;
    }
    
    const file = input.files[0];
    
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        showToast('error', 'File size must not exceed 10MB');
        input.value = '';
        preview.innerHTML = '';
        preview.style.display = 'none';
        return;
    }
    
    const fileExtension = file.name.split('.').pop().toLowerCase();
    const allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
    
    if (!allowedTypes.includes(fileExtension)) {
        showToast('error', 'File type not supported. Allowed: JPG, PNG, PDF');
        input.value = '';
        preview.innerHTML = '';
        preview.style.display = 'none';
        return;
    }
    
    if (fileExtension === 'pdf') {
        preview.innerHTML = `
            <div class="alert alert-success mb-2">
                <i class="fas fa-file-pdf text-danger me-2"></i>
                <strong>${file.name}</strong> (${(file.size / 1024).toFixed(2)} KB)
            </div>
        `;
        preview.style.display = 'block';
    } else {
        // For images, show preview
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="document-preview-item">
                    <img src="${e.target.result}" alt="Preview" 
                        style="max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <p style="margin-top: 8px; font-size: 12px; color: #666;">
                        <i class="fas fa-check text-success me-2"></i>${file.name} (${(file.size / 1024).toFixed(2)} KB)
                    </p>
                </div>
            `;
            preview.style.display = 'block';
            
            console.log('Image preview loaded successfully');
            
            // Trigger change detection
            const form = document.getElementById('editTrainingForm');
            if (form && form.dataset.trainingId) {
                checkEditTrainingFormChanges(form.dataset.trainingId);
            }
        };

        reader.onerror = function(error) {
            console.error('File read error:', error);
            preview.innerHTML = `
                <div class="alert alert-danger mb-2">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Failed to load file preview
                </div>
            `;
            preview.style.display = 'block';
            input.value = '';
        };

        reader.readAsDataURL(file);
    }
}

/**
 * Auto-capitalize names in edit form
 */
function capitalizeEditTrainingName(input) {
    const value = input.value;
    if (value.length > 0) {
        input.value = value
            .toLowerCase()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');

        // Trigger change detection after capitalization
        const form = document.getElementById('editTrainingForm');
        if (form && form.dataset.trainingId) {
            checkEditTrainingFormChanges(form.dataset.trainingId);
        }
    }
}

// /**
//  * Initialize name field auto-capitalize when modal is shown
//  */
// document.addEventListener('DOMContentLoaded', function() {
//     // Initialize Add Modal name listeners
//     initAddTrainingNameListeners();

//     // Initialize Edit Modal name listeners
//     initEditTrainingNameListeners();

//     // Add contact number validation for edit modal
//     const editTrainingContactInput = document.getElementById('edit_training_contact_number');
//     if (editTrainingContactInput) {
//         editTrainingContactInput.addEventListener('input', function() {
//             validateEditTrainingContactNumber(this.value);
//         });
//     }
// });
function validateEditTrainingContactNumber(contactNumber) {
    const input = document.getElementById('edit_training_contact_number');
    if (!input) return true;

    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) feedback.remove();
    input.classList.remove('is-invalid', 'is-valid');

    if (!contactNumber || !contactNumber.trim()) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = 'Contact number is required';
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    // Remove spaces, dashes, parentheses
    const cleaned = contactNumber.replace(/[\s\-()]/g, '');
    const digits = cleaned.replace(/\D/g, '');

    // Check digit count - MUST BE 11
    if (digits.length !== 11) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = `Must be 11 digits (you have ${digits.length})`;
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    // FIXED REGEX: ^09\d{9}$ (NOT ^(\+639|09)\d{9}$)
    const phoneRegex = /^09\d{9}$/;
    if (!phoneRegex.test(digits)) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = 'Must start with 09 (e.g., 09123456789)';
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    input.classList.add('is-valid');
    return true;
}
// ============================================
// NAME VALIDATION AND CAPITALIZATION FIXES
// ============================================

/**
 * Auto-capitalize name fields (Add Modal)
 */
function capitalizeTrainingName(input) {
    const value = input.value;
    if (value && value.length > 0) {
        input.value = value
            .toLowerCase()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }
}

/**
 * Validate Training Name Field (Add Modal)
 */
function validateTrainingNameField(fieldId) {
    const input = document.getElementById(fieldId);
    if (!input) return true;

    // Remove old error
    const oldError = input.parentNode.querySelector('.invalid-feedback');
    if (oldError) oldError.remove();
    input.classList.remove('is-invalid', 'is-valid');

    const value = input.value.trim();
    const isRequired = !fieldId.includes('middle');

    // Check if empty and required
    if (!value && isRequired) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        const fieldLabel = fieldId.includes('first') ? 'First' : 'Last';
        errorDiv.textContent = `${fieldLabel} name is required`;
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    // Check if has invalid characters (only allow letters, spaces, hyphens, apostrophes)
    const nameRegex = /^[a-zA-Z\s\-']*$/;
    if (value && !nameRegex.test(value)) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = 'Only letters, spaces, hyphens, and apostrophes are allowed';
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    // Valid
    if (value) {
        input.classList.add('is-valid');
    }
    return true;
}

/**
 * Auto-capitalize name fields (Edit Modal)
 */
function capitalizeEditTrainingName(input) {
    const value = input.value;
    if (value && value.length > 0) {
        input.value = value
            .toLowerCase()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');

        // Trigger change detection after capitalization
        const form = document.getElementById('editTrainingForm');
        if (form && form.dataset.trainingId) {
            checkEditTrainingFormChanges(form.dataset.trainingId);
        }
    }
}

/**
 * Validate Edit Training Name Field (Edit Modal)
 */
function validateEditTrainingNameField(fieldId) {
    const input = document.getElementById(fieldId);
    if (!input) return true;

    // Remove old error
    const oldError = input.parentNode.querySelector('.invalid-feedback');
    if (oldError) oldError.remove();
    input.classList.remove('is-invalid', 'is-valid');

    const value = input.value.trim();
    const isRequired = !fieldId.includes('middle');

    // Check if empty and required
    if (!value && isRequired) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        const fieldLabel = fieldId.includes('first') ? 'First' : 'Last';
        errorDiv.textContent = `${fieldLabel} name is required`;
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    // Check if has invalid characters
    const nameRegex = /^[a-zA-Z\s\-']*$/;
    if (value && !nameRegex.test(value)) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = 'Only letters, spaces, hyphens, and apostrophes are allowed';
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    // Valid
    if (value) {
        input.classList.add('is-valid');
    }
    return true;
}

/**
 * Initialize Name Field Listeners (Add Modal)
 */
function initAddTrainingNameListeners() {
    const firstNameInput = document.getElementById('training_first_name');
    const middleNameInput = document.getElementById('training_middle_name');
    const lastNameInput = document.getElementById('training_last_name');

    // First Name
    if (firstNameInput) {
        firstNameInput.addEventListener('blur', function() {
            capitalizeTrainingName(this);
            validateTrainingNameField('training_first_name');
        });
        firstNameInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateTrainingNameField('training_first_name');
            }
        });
    }

    // Middle Name
    if (middleNameInput) {
        middleNameInput.addEventListener('blur', function() {
            capitalizeTrainingName(this);
            validateTrainingNameField('training_middle_name');
        });
        middleNameInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateTrainingNameField('training_middle_name');
            }
        });
    }

    // Last Name
    if (lastNameInput) {
        lastNameInput.addEventListener('blur', function() {
            capitalizeTrainingName(this);
            validateTrainingNameField('training_last_name');
        });
        lastNameInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateTrainingNameField('training_last_name');
            }
        });
    }
}

/**
 * Initialize Name Field Listeners (Edit Modal)
 */
function initEditTrainingNameListeners() {
    const firstNameInput = document.getElementById('edit_training_first_name');
    const middleNameInput = document.getElementById('edit_training_middle_name');
    const lastNameInput = document.getElementById('edit_training_last_name');

    // First Name
    if (firstNameInput) {
        firstNameInput.addEventListener('blur', function() {
            capitalizeEditTrainingName(this);
            validateEditTrainingNameField('edit_training_first_name');
        });
        firstNameInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateEditTrainingNameField('edit_training_first_name');
            }
        });
    }

    // Middle Name
    if (middleNameInput) {
        middleNameInput.addEventListener('blur', function() {
            capitalizeEditTrainingName(this);
            validateEditTrainingNameField('edit_training_middle_name');
        });
        middleNameInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateEditTrainingNameField('edit_training_middle_name');
            }
        });
    }

    // Last Name
    if (lastNameInput) {
        lastNameInput.addEventListener('blur', function() {
            capitalizeEditTrainingName(this);
            validateEditTrainingNameField('edit_training_last_name');
        });
        lastNameInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateEditTrainingNameField('edit_training_last_name');
            }
        });
    }
}


// FIXED: validateTrainingContactNumber - For Add Modal
function validateTrainingContactNumber(contactNumber) {
    const input = document.getElementById('training_contact_number');
    if (!input) return true;

    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) feedback.remove();
    input.classList.remove('is-invalid', 'is-valid');

    if (!contactNumber || !contactNumber.trim()) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = 'Contact number is required';
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    // Remove spaces, dashes, parentheses
    const cleaned = contactNumber.replace(/[\s\-()]/g, '');
    const digits = cleaned.replace(/\D/g, '');

    // Check digit count - MUST BE 11
    if (digits.length !== 11) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = `Must be 11 digits (you have ${digits.length})`;
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    // FIXED REGEX: ^09\d{9}$ (NOT ^(\+639|09)\d{9}$)
    const phoneRegex = /^09\d{9}$/;
    if (!phoneRegex.test(digits)) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = 'Must start with 09 (e.g., 09123456789)';
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    input.classList.add('is-valid');
    return true;
}

/**
 * Download helper for Training documents
 */
function downloadTrainingDocument(fileUrl, fileName) {
    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Get CSRF token utility function
 */
function getCSRFToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
}

 document.addEventListener('DOMContentLoaded', function() {
    // Initialize Add Modal name listeners
    initAddTrainingNameListeners();

    // Initialize Edit Modal name listeners
    initEditTrainingNameListeners();

    // Add contact number validation for edit modal
    const editTrainingContactInput = document.getElementById('edit_training_contact_number');
    if (editTrainingContactInput) {
        editTrainingContactInput.addEventListener('blur', function() {
            if (this.value) {
                validateEditTrainingContactNumber(this.value);
            }
        });
    }

    // Add contact number validation for add modal
    const trainingContactInput = document.getElementById('training_contact_number');
    if (trainingContactInput) {
        trainingContactInput.addEventListener('blur', function() {
            if (this.value) {
                validateTrainingContactNumber(this.value);
            }
        });
    }
});
    </script>
@endsection
