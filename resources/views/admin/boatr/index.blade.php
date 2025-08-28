{{-- resources/views/admin/boatr/index.blade.php --}}
@extends('layouts.app')

@section('title', 'BoatR Registrations - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-ship text-primary me-2"></i>
        <span class="text-primary fw-bold">BoatR Registrations</span>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Applications
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRegistrations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ship fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Inspection Required
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inspectionRequiredCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
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
            <form method="GET" action="{{ route('admin.boatr.requests') }}" id="filterForm">
                <!-- Hidden date inputs -->
                <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                <div class="row">
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>
                                Under Review
                            </option>
                            <option value="inspection_required"
                                {{ request('status') == 'inspection_required' ? 'selected' : '' }}>
                                Inspection Required
                            </option>
                            <option value="inspection_scheduled"
                                {{ request('status') == 'inspection_scheduled' ? 'selected' : '' }}>
                                Inspection Scheduled
                            </option>
                            <option value="documents_pending"
                                {{ request('status') == 'documents_pending' ? 'selected' : '' }}>
                                Documents Pending
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
                        <select name="boat_type" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Boat Types</option>
                            <option value="Spoon" {{ request('boat_type') == 'Spoon' ? 'selected' : '' }}>
                                Spoon
                            </option>
                            <option value="Plumb" {{ request('boat_type') == 'Plumb' ? 'selected' : '' }}>
                                Plumb
                            </option>
                            <option value="Banca" {{ request('boat_type') == 'Banca' ? 'selected' : '' }}>
                                Banca
                            </option>
                            <option value="Rake Stem - Rake Stern"
                                {{ request('boat_type') == 'Rake Stem - Rake Stern' ? 'selected' : '' }}>
                                Rake Stem - Rake Stern
                            </option>
                            <option value="Rake Stem - Transom/Spoon/Plumb Stern"
                                {{ request('boat_type') == 'Rake Stem - Transom/Spoon/Plumb Stern' ? 'selected' : '' }}>
                                Rake Stem - Transom
                            </option>
                            <option value="Skiff (Typical Design)"
                                {{ request('boat_type') == 'Skiff (Typical Design)' ? 'selected' : '' }}>
                                Skiff (Typical Design)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search name, vessel, FishR number..." value="{{ request('search') }}"
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
                    <div class="col-md-1">
                        <a href="{{ route('admin.boatr.requests') }}" class="btn btn-secondary btn-sm w-100">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-ship me-2"></i>BoatR Applications
            </h6>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.boatr.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="registrationsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Date Applied</th>
                            <th>Application #</th>
                            <th>Name</th>
                            <th>Inspection</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr id="registration-{{ $registration->id }}">
                                <td>{{ $registration->created_at->format('M d, Y g:i A') }}</td>
                                <td>
                                    <strong class="text-primary">{{ $registration->application_number }}</strong>
                                </td>
                                <td>{{ $registration->full_name }}</td>
                                <td>
                                    @if ($registration->inspection_completed)
                                        <span class="badge bg-success"
                                            id="inspection-badge-{{ $registration->id }}">Completed</span>
                                    @else
                                        <span class="badge bg-warning"
                                            id="inspection-badge-{{ $registration->id }}">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $userDocs = $registration->hasUserDocument() ? 1 : 0;
                                        $inspectionDocs = count($registration->inspection_documents ?? []);
                                        $totalDocs = $userDocs + $inspectionDocs;
                                    @endphp
                                    <div id="documents-cell-{{ $registration->id }}">
                                        @if ($totalDocs > 0)
                                            <button class="btn btn-sm btn-info"
                                                onclick="viewDocuments({{ $registration->id }})">
                                                <i class="fas fa-file-alt"></i>
                                                View ({{ $totalDocs }})
                                            </button>
                                        @else
                                            <span class="badge bg-secondary">No documents</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $registration->status_color }} fs-6"
                                        id="status-badge-{{ $registration->id }}">
                                        {{ $registration->formatted_status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1" id="actions-{{ $registration->id }}">
                                        <button class="btn btn-sm btn-primary"
                                            onclick="viewRegistration({{ $registration->id }})" title="View Details">
                                            <i class="fas fa-eye me-1"></i>View
                                        </button>
                                        <button class="btn btn-sm btn-warning"
                                            onclick="showUpdateModal({{ $registration->id }}, '{{ $registration->status }}')"
                                            title="Update Status">
                                            <i class="fas fa-edit me-1"></i>Update
                                        </button>
                                        @if (!$registration->inspection_completed)
                                            <button class="btn btn-sm btn-success"
                                                onclick="showInspectionModal({{ $registration->id }})"
                                                title="Complete Inspection">
                                                <i class="fas fa-search me-1"></i>Inspection
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-ship fa-3x mb-3 text-gray-300"></i>
                                    <p>No registrations found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Custom Pagination (Same as FishR) -->
            @if ($registrations->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            {{-- Previous Page Link --}}
                            @if ($registrations->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Back</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $registrations->previousPageUrl() }}"
                                        rel="prev">Back</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $registrations->currentPage();
                                $lastPage = $registrations->lastPage();
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
                                        <a class="page-link"
                                            href="{{ $registrations->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($registrations->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $registrations->nextPageUrl() }}"
                                        rel="next">Next</a>
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

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Update Application Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Loading State -->
                    <div id="updateModalLoading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading application details...</p>
                    </div>

                    <!-- Application Info -->
                    <div class="card bg-light mb-3" id="updateModalContent" style="display: none;">
                        <div class="card-body">
                            <h6 class="card-title mb-2">
                                <i class="fas fa-info-circle me-2"></i>Application Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>ID:</strong> <span id="updateRegId"></span></p>
                                    <p class="mb-1"><strong>Application #:</strong> <span id="updateRegNumber"></span>
                                    </p>
                                    <p class="mb-1"><strong>Name:</strong> <span id="updateRegName"></span></p>
                                    <p class="mb-1"><strong>Vessel:</strong> <span id="updateRegVessel"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>FishR #:</strong> <span id="updateRegFishR"></span></p>
                                    <p class="mb-1"><strong>Boat Type:</strong> <span id="updateRegBoatType"></span></p>
                                    <p class="mb-1"><strong>Current Status:</strong> <span
                                            id="updateRegCurrentStatus"></span></p>
                                    <p class="mb-1"><strong>Inspection:</strong> <span id="updateRegInspection"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Form -->
                    <form id="updateForm" style="display: none;">
                        <input type="hidden" id="updateRegistrationId">
                        <div class="mb-3">
                            <label for="newStatus" class="form-label">Select New Status:</label>
                            <select class="form-select" id="newStatus" required>
                                <option value="">Choose status...</option>
                                <option value="pending">Pending</option>
                                <option value="under_review">Under Review</option>
                                <option value="inspection_required">Inspection Required</option>
                                <option value="inspection_scheduled">Inspection Scheduled</option>
                                <option value="documents_pending">Documents Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks (Optional):</label>
                            <textarea class="form-control" id="remarks" rows="3"
                                placeholder="Add any notes or comments about this status change..." maxlength="2000"></textarea>
                            <div class="form-text">Maximum 2000 characters (<span id="remarksCount">0</span>)</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateStatusBtn"
                        onclick="updateRegistrationStatus()" style="display: none;">
                        <i class="fas fa-save me-1"></i>Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspection Modal -->
    <div class="modal fade" id="inspectionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-search me-2"></i>Complete Boat Inspection
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Upload the supporting document after completing the on-site boat inspection.
                    </div>

                    <form id="inspectionForm" enctype="multipart/form-data">
                        <input type="hidden" id="inspectionRegistrationId">
                        <div class="mb-3">
                            <label for="supporting_document" class="form-label">Supporting Document *</label>
                            <input type="file" class="form-control" id="supporting_document"
                                accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">Upload inspection report, boat photos, or other supporting documents.
                                (PDF, JPG, JPEG, PNG - Max 10MB)</div>
                            <div class="invalid-feedback" id="documentError"></div>
                        </div>
                        <div class="mb-3">
                            <label for="inspection_notes" class="form-label">Inspection Notes (Optional):</label>
                            <textarea class="form-control" id="inspection_notes" rows="3"
                                placeholder="Add any notes about the inspection..." maxlength="1000"></textarea>
                            <div class="form-text">Maximum 1000 characters (<span id="notesCount">0</span>)</div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="approve_application">
                                <label class="form-check-label" for="approve_application">
                                    Auto-approve application after inspection
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="completeInspectionBtn"
                        onclick="completeInspection()">
                        <i class="fas fa-check me-1"></i>Complete Inspection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Details Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-ship me-2"></i>Application Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="registrationDetailsLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading application details...</p>
                    </div>
                    <div id="registrationDetails" style="display: none;">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Viewer Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt me-2"></i>Application Documents
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="documentViewerLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading documents...</p>
                    </div>
                    <div id="documentViewer" style="display: none;">
                        <!-- Documents will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Preview Modal -->
    <div class="modal fade" id="documentPreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentPreviewTitle">
                        <i class="fas fa-eye me-2"></i>Document Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="documentPreview" class="text-center">
                        <!-- Document preview will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="actionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true"
            data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header">
                <i class="fas fa-check-circle text-success me-2" id="toastIcon"></i>
                <strong class="me-auto" id="toastTitle">Success</strong>
                <small id="toastTime">Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Action completed successfully.
            </div>
        </div>
    </div>

    <!-- Date Filter Modal -->
    <div class="modal fade" id="dateFilterModal" tabindex="-1" aria-labelledby="dateFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="dateFilterModalLabel">
                        <i class="fas fa-calendar-alt me-2"></i>Select Date Range
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
                                        No date filter applied - showing all registrations
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
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

        /* Status badge colors */
        .bg-primary {
            background-color: #4e73df !important;
        }

        .bg-info {
            background-color: #36b9cc !important;
        }

        .bg-warning {
            background-color: #f6c23e !important;
        }

        .bg-success {
            background-color: #1cc88a !important;
        }

        .bg-danger {
            background-color: #e74a3b !important;
        }

        .bg-secondary {
            background-color: #858796 !important;
        }

        /* Card border colors */
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

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        /* Text utilities */
        .text-xs {
            font-size: 0.7rem;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        /* Table enhancements */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            font-size: 0.75em;
        }

        /* Loading states */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Document preview */
        .document-thumbnail {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .document-thumbnail:hover {
            transform: scale(1.05);
        }

        /* Button loading state */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading .btn-text {
            opacity: 0;
        }

        .btn-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 1rem;
            height: 1rem;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Real-time update animations */
        .row-updated {
            animation: highlightUpdate 2s ease-in-out;
        }

        @keyframes highlightUpdate {
            0% {
                background-color: #d4edda;
            }

            100% {
                background-color: transparent;
            }
        }

        /* Enhanced modal styling */
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        /* Document list styling */
        .document-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .document-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        /* Custom Pagination Styles (Same as FishR) */
        .pagination {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 8px;
            margin: 0;
        }

        .pagination .page-item .page-link {
            color: #6c757d;
            background-color: transparent;
            border: none;
            padding: 8px 12px;
            margin: 0 2px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .pagination .page-item .page-link:hover {
            color: #495057;
            background-color: #e9ecef;
            text-decoration: none;
        }

        .pagination .page-item.active .page-link {
            color: white;
            background-color: #007bff;
            border-color: #007bff;
            font-weight: 600;
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            background-color: transparent;
            cursor: not-allowed;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            font-weight: 600;
        }
    </style>
@endsection

@section('scripts')
    <script>
        let searchTimeout;
        let currentData = {};

        // Unified refresh function for BoatR (exact same pattern as FishR)
        function refreshData() {
            fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Update table content
                    const newTable = doc.querySelector('#registrationsTable tbody');
                    const currentTable = document.querySelector('#registrationsTable tbody');
                    if (newTable && currentTable && newTable.innerHTML !== currentTable.innerHTML) {
                        currentTable.innerHTML = newTable.innerHTML;
                    }

                    // Update statistics cards - BoatR structure
                    const cards = {
                        total: ['.border-left-primary .h5'], // Total Applications
                        pending: ['.border-left-info .h5'], // Pending
                        inspection: ['.border-left-warning .h5'], // Inspection Required
                        approved: ['.border-left-success .h5'] // Approved
                    };

                    Object.entries(cards).forEach(([key, [selector]]) => {
                        const newCard = doc.querySelector(selector);
                        const currentCard = document.querySelector(selector);
                        if (newCard && currentCard && newCard.textContent !== currentCard.textContent) {
                            currentCard.textContent = newCard.textContent;
                        }
                    });
                })
                .catch(error => console.error('Refresh error:', error));
        }

        // Auto search functionality
        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        }

        // Submit filter form when dropdowns change
        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        // Character count for textareas
        document.addEventListener('DOMContentLoaded', function() {
            // Remarks character count
            const remarksTextarea = document.getElementById('remarks');
            const remarksCount = document.getElementById('remarksCount');

            if (remarksTextarea && remarksCount) {
                remarksTextarea.addEventListener('input', function() {
                    remarksCount.textContent = this.value.length;
                });
            }

            // Inspection notes character count
            const notesTextarea = document.getElementById('inspection_notes');
            const notesCount = document.getElementById('notesCount');

            if (notesTextarea && notesCount) {
                notesTextarea.addEventListener('input', function() {
                    notesCount.textContent = this.value.length;
                });
            }
        });

        // Show toast notification
        function showToast(type, title, message) {
            const toast = document.getElementById('actionToast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            const toastTime = document.getElementById('toastTime');

            // Set icon and colors based on type
            const iconClass = type === 'success' ? 'fas fa-check-circle text-success' :
                type === 'error' ? 'fas fa-exclamation-circle text-danger' :
                type === 'warning' ? 'fas fa-exclamation-triangle text-warning' :
                'fas fa-info-circle text-info';

            toastIcon.className = iconClass + ' me-2';
            toastTitle.textContent = title;
            toastMessage.textContent = message;
            toastTime.textContent = 'Just now';

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }

        // Update table row in real-time
        function updateTableRow(id, data) {
            const row = document.getElementById(`registration-${id}`);
            if (!row) return;

            // Update status badge
            const statusBadge = document.getElementById(`status-badge-${id}`);
            if (statusBadge) {
                statusBadge.className = `badge bg-${data.status_color}`;
                statusBadge.textContent = data.formatted_status;
            }

            // Update inspection badge
            const inspectionBadge = document.getElementById(`inspection-badge-${id}`);
            if (inspectionBadge) {
                if (data.inspection_completed) {
                    inspectionBadge.className = 'badge bg-success';
                    inspectionBadge.textContent = 'Completed';
                } else {
                    inspectionBadge.className = 'badge bg-warning';
                    inspectionBadge.textContent = 'Pending';
                }
            }

            // Update documents cell
            const documentsCell = document.getElementById(`documents-cell-${id}`);
            if (documentsCell && data.total_documents !== undefined) {
                if (data.total_documents > 0) {
                    documentsCell.innerHTML = `
                    <button class="btn btn-sm btn-info" onclick="viewDocuments(${id})">
                        <i class="fas fa-file-alt"></i>
                        View (${data.total_documents})
                    </button>
                `;
                } else {
                    documentsCell.innerHTML = '<span class="badge bg-secondary">No documents</span>';
                }
            }

            // Update actions buttons
            const actionsCell = document.getElementById(`actions-${id}`);
            if (actionsCell && !data.inspection_completed) {
                // Remove inspection button if inspection is completed
                const inspectionBtn = actionsCell.querySelector('[onclick*="showInspectionModal"]');
                if (inspectionBtn && data.inspection_completed) {
                    inspectionBtn.remove();
                }
            }

            // Add update animation
            row.classList.add('row-updated');
            setTimeout(() => {
                row.classList.remove('row-updated');
            }, 2000);
        }

        // Enhanced show update modal with loading state
        function showUpdateModal(id, currentStatus) {
            const modal = new bootstrap.Modal(document.getElementById('updateModal'));
            modal.show();

            // Show loading state
            document.getElementById('updateModalLoading').style.display = 'block';
            document.getElementById('updateModalContent').style.display = 'none';
            document.getElementById('updateForm').style.display = 'none';
            document.getElementById('updateStatusBtn').style.display = 'none';

            fetch(`/admin/boatr/requests/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load application details');
                    }

                    // Store current data
                    currentData[id] = data;

                    // Hide loading, show content
                    document.getElementById('updateModalLoading').style.display = 'none';
                    document.getElementById('updateModalContent').style.display = 'block';
                    document.getElementById('updateForm').style.display = 'block';
                    document.getElementById('updateStatusBtn').style.display = 'inline-block';

                    // Populate application info
                    document.getElementById('updateRegistrationId').value = id;
                    document.getElementById('updateRegId').textContent = data.id;
                    document.getElementById('updateRegNumber').textContent = data.application_number;
                    document.getElementById('updateRegName').textContent = data.full_name;
                    document.getElementById('updateRegVessel').textContent = data.vessel_name;
                    document.getElementById('updateRegFishR').textContent = data.fishr_number;
                    document.getElementById('updateRegBoatType').textContent = data.boat_type;

                    // Show current status
                    document.getElementById('updateRegCurrentStatus').innerHTML =
                        `<span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;

                    // Show inspection status
                    document.getElementById('updateRegInspection').innerHTML = data.inspection_completed ?
                        '<span class="badge bg-success">Completed</span>' :
                        '<span class="badge bg-warning">Pending</span>';

                    // Set form values
                    document.getElementById('newStatus').value = currentStatus;
                    document.getElementById('remarks').value = '';
                    document.getElementById('remarksCount').textContent = '0';
                })
                .catch(error => {
                    console.error('Error loading application details:', error);
                    showToast('error', 'Error', 'Failed to load application details: ' + error.message);
                    modal.hide();
                });
        }

        // Enhanced show inspection modal
        function showInspectionModal(id) {
            document.getElementById('inspectionRegistrationId').value = id;
            document.getElementById('supporting_document').value = '';
            document.getElementById('inspection_notes').value = '';
            document.getElementById('approve_application').checked = false;
            document.getElementById('notesCount').textContent = '0';

            // Clear any previous error states
            document.getElementById('supporting_document').classList.remove('is-invalid');
            document.getElementById('documentError').textContent = '';

            const modal = new bootstrap.Modal(document.getElementById('inspectionModal'));
            modal.show();
        }

        // Enhanced update registration status with real-time updates and auto-refresh
        function updateRegistrationStatus() {
            const id = document.getElementById('updateRegistrationId').value;
            const newStatus = document.getElementById('newStatus').value;
            const remarks = document.getElementById('remarks').value;

            if (!newStatus) {
                showToast('warning', 'Warning', 'Please select a status');
                return;
            }

            if (!confirm(
                    `Are you sure you want to change the status to "${document.querySelector(`#newStatus option[value="${newStatus}"]`).textContent}"?`
                )) {
                return;
            }

            // Show loading state
            const updateBtn = document.getElementById('updateStatusBtn');
            const originalContent = updateBtn.innerHTML;
            updateBtn.classList.add('btn-loading');
            updateBtn.innerHTML = '<span class="btn-text">Updating...</span>';
            updateBtn.disabled = true;

            fetch(`/admin/boatr/requests/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        remarks: remarks
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Success', data.message);

                        // Update table row in real-time
                        if (data.registration) {
                            updateTableRow(id, data.registration);
                        }

                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('updateModal')).hide();

                        // AUTO-REFRESH: Refresh data immediately after successful update
                        setTimeout(() => {
                            refreshData();
                        }, 500);

                        // Optional: Update statistics cards if provided
                        if (data.statistics) {
                            updateStatisticsCards(data.statistics);
                        }
                    } else {
                        throw new Error(data.message || 'Unknown error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error', 'Failed to update status: ' + error.message);
                })
                .finally(() => {
                    // Restore button state
                    updateBtn.classList.remove('btn-loading');
                    updateBtn.innerHTML = originalContent;
                    updateBtn.disabled = false;
                });
        }

        // Enhanced complete inspection with real-time updates and auto-refresh
        function completeInspection() {
            const id = document.getElementById('inspectionRegistrationId').value;
            const fileInput = document.getElementById('supporting_document');
            const notes = document.getElementById('inspection_notes').value;
            const autoApprove = document.getElementById('approve_application').checked;

            // Validation
            if (!fileInput.files[0]) {
                fileInput.classList.add('is-invalid');
                document.getElementById('documentError').textContent = 'Please select a supporting document';
                showToast('warning', 'Warning', 'Please select a supporting document');
                return;
            }

            // Validate file size (10MB)
            if (fileInput.files[0].size > 10 * 1024 * 1024) {
                fileInput.classList.add('is-invalid');
                document.getElementById('documentError').textContent = 'File size must be less than 10MB';
                showToast('warning', 'Warning', 'File size must be less than 10MB');
                return;
            }

            // Clear validation errors
            fileInput.classList.remove('is-invalid');
            document.getElementById('documentError').textContent = '';

            if (!confirm('Are you sure you want to complete the inspection for this application?')) {
                return;
            }

            // Show loading state
            const completeBtn = document.getElementById('completeInspectionBtn');
            const originalContent = completeBtn.innerHTML;
            completeBtn.classList.add('btn-loading');
            completeBtn.innerHTML = '<span class="btn-text">Processing...</span>';
            completeBtn.disabled = true;

            const formData = new FormData();
            formData.append('supporting_document', fileInput.files[0]);
            formData.append('inspection_notes', notes);
            formData.append('approve_application', autoApprove ? '1' : '0');

            fetch(`/admin/boatr/requests/${id}/complete-inspection`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Success', data.message);

                        // Update table row in real-time
                        if (data.registration) {
                            updateTableRow(id, data.registration);
                        }

                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('inspectionModal')).hide();

                        // AUTO-REFRESH: Refresh data immediately after successful completion
                        setTimeout(() => {
                            refreshData();
                        }, 500);

                        // Optional: Update statistics cards
                        if (data.statistics) {
                            updateStatisticsCards(data.statistics);
                        }
                    } else {
                        throw new Error(data.message || 'Unknown error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error', 'Failed to complete inspection: ' + error.message);
                })
                .finally(() => {
                    // Restore button state
                    completeBtn.classList.remove('btn-loading');
                    completeBtn.innerHTML = originalContent;
                    completeBtn.disabled = false;
                });
        }

        // Enhanced view application details
        function viewRegistration(id) {
            const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
            modal.show();

            // Show loading state
            document.getElementById('registrationDetailsLoading').style.display = 'block';
            document.getElementById('registrationDetails').style.display = 'none';

            fetch(`/admin/boatr/requests/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load application details');
                    }

                    // Hide loading, show content
                    document.getElementById('registrationDetailsLoading').style.display = 'none';
                    document.getElementById('registrationDetails').style.display = 'block';

                    // Build remarks HTML
                    let remarksHtml = '';
                    if (data.remarks) {
                        remarksHtml = `
                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-comment me-2"></i>Remarks</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-0">
                                    <p class="mb-1">${data.remarks}</p>
                                    ${data.reviewed_at ? `<small class="text-muted">Updated on ${data.reviewed_at}${data.reviewed_by_name ? ` by ${data.reviewed_by_name}` : ''}</small>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                    }

                    // Build documents HTML - FIXED to show both user and inspection documents
                    let documentHtml = '';
                    const userDocsCount = data.user_documents ? data.user_documents.length : 0;
                    const inspectionDocsCount = data.inspection_documents ? data.inspection_documents.length : 0;

                    if (userDocsCount > 0 || inspectionDocsCount > 0) {
                        documentHtml = `
                    <div class="col-12 mt-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Documents</h6>
                                <button class="btn btn-sm btn-info" onclick="viewDocuments(${id})">
                                    <i class="fas fa-eye me-1"></i>View All Documents
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>User Documents:</strong> ${userDocsCount}</p>
                                        <p><strong>Inspection Documents:</strong> ${inspectionDocsCount}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Documents Verified:</strong> ${data.documents_verified ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No</span>'}</p>
                                        ${data.documents_verified_at ? `<p><strong>Verified At:</strong> ${data.documents_verified_at}</p>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                    }

                    // Populate modal content
                    document.getElementById('registrationDetails').innerHTML = `
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Application #:</strong> <span class="badge bg-primary">${data.application_number}</span></p>
                                <p><strong>Name:</strong> ${data.full_name}</p>
                                <p><strong>Mobile:</strong> ${data.mobile || 'N/A'}</p>
                                <p><strong>Email:</strong> ${data.email || 'N/A'}</p>
                                <p><strong>FishR Number:</strong>${data.fishr_number}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-ship me-2"></i>Vessel Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Vessel Name:</strong> ${data.vessel_name}</p>
                                <p><strong>Boat Type:</strong> ${data.boat_type}</p>
                                <p><strong>Dimensions:</strong> ${data.boat_dimensions}</p>
                                <p><strong>Engine Type:</strong> ${data.engine_type}</p>
                                <p><strong>Engine HP:</strong> ${data.engine_horsepower} HP</p>
                                <p><strong>Primary Fishing Gear:</strong> ${data.primary_fishing_gear}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Application Status</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Status:</strong> <span class="badge bg-${data.status_color}">${data.formatted_status}</span></p>
                                <p><strong>Inspection:</strong> ${data.inspection_completed ? '<span class="badge bg-success">Completed</span>' : '<span class="badge bg-warning">Pending</span>'}</p>
                                ${data.inspection_date ? `<p><strong>Inspection Date:</strong> ${data.inspection_date}</p>` : ''}
                                ${data.inspection_notes ? `<p><strong>Inspection Notes:</strong> ${data.inspection_notes}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Date Applied:</strong> ${data.created_at}</p>
                                <p><strong>Last Updated:</strong> ${data.updated_at}</p>
                                ${data.reviewed_at ? `<p><strong>Last Reviewed:</strong> ${data.reviewed_at}</p>` : ''}
                                ${data.reviewed_by_name ? `<p><strong>Reviewed By:</strong> ${data.reviewed_by_name}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    ${documentHtml}
                    ${remarksHtml}
                </div>
            `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('registrationDetailsLoading').style.display = 'none';
                    document.getElementById('registrationDetails').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading application details: ${error.message}
                </div>
            `;
                    document.getElementById('registrationDetails').style.display = 'block';
                });
        }

        // Fixed BoatR Admin JavaScript - Complete Document Handling
        // Add this to your admin BoatR index view or separate JS file

        // Enhanced view documents function - FIXED to handle both user and inspection documents correctly
        function viewDocuments(id) {
            const modal = new bootstrap.Modal(document.getElementById('documentModal'));
            modal.show();

            // Show loading state
            document.getElementById('documentViewerLoading').style.display = 'block';
            document.getElementById('documentViewer').style.display = 'none';

            fetch(`/admin/boatr/requests/${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load documents');
                    }

                    // Hide loading, show content
                    document.getElementById('documentViewerLoading').style.display = 'none';
                    document.getElementById('documentViewer').style.display = 'block';

                    let documentsHtml = '<div class="row">';

                    // User Documents - FIXED to handle single user document as array
                    if (data.user_documents && data.user_documents.length > 0) {
                        documentsHtml += `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>User Uploaded Documents</h5>
                        </div>
                        <div class="card-body">
            `;

                        data.user_documents.forEach((doc, index) => {
                            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(doc.type?.toLowerCase());

                            documentsHtml += `
                    <div class="document-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">User Document</h6>
                                <p class="mb-1 small text-muted">${doc.original_name || 'Document'}</p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>Uploaded: ${doc.uploaded_at || 'Unknown'}
                                    <br><i class="fas fa-weight me-1"></i>Size: ${formatFileSize(doc.size)}
                                </small>
                            </div>
                            <div class="btn-group-vertical">
                                <a href="/admin/boatr/requests/${id}/download-document?type=user&index=${index}"
                                   class="btn btn-sm btn-primary mb-1" target="_blank" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button class="btn btn-sm btn-info" onclick="previewDocument(${id}, 'user', ${index})" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        ${isImage && doc.url ? `
                                        <div class="mt-2 text-center">
                                            <img src="${doc.url}" class="document-thumbnail" alt="Document preview"
                                                 onclick="previewDocument(${id}, 'user', ${index})" style="max-width: 150px; cursor: pointer;">
                                        </div>
                                    ` : ''}
                    </div>
                `;
                        });

                        documentsHtml += '</div></div></div>';
                    }

                    // Inspection Documents
                    if (data.inspection_documents && data.inspection_documents.length > 0) {
                        documentsHtml += `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-search me-2"></i>Inspection Documents</h5>
                        </div>
                        <div class="card-body">
            `;

                        data.inspection_documents.forEach((doc, index) => {
                            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(doc.extension
                                ?.toLowerCase());

                            documentsHtml += `
                    <div class="document-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Inspection Document ${index + 1}</h6>
                                <p class="mb-1 small text-muted">${doc.original_name || 'Document'}</p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>Uploaded: ${doc.uploaded_at || 'Unknown'}
                                    ${doc.uploader ? `<br><i class="fas fa-user me-1"></i>By: ${doc.uploader}` : ''}
                                    <br><i class="fas fa-weight me-1"></i>Size: ${formatFileSize(doc.size)}
                                    ${doc.notes ? `<br><i class="fas fa-comment me-1"></i>Notes: ${doc.notes}` : ''}
                                </small>
                            </div>
                            <div class="btn-group-vertical">
                                <a href="/admin/boatr/requests/${id}/download-document?type=inspection&index=${index}"
                                   class="btn btn-sm btn-primary mb-1" target="_blank" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button class="btn btn-sm btn-info" onclick="previewDocument(${id}, 'inspection', ${index})" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        ${isImage && doc.url ? `
                                        <div class="mt-2 text-center">
                                            <img src="${doc.url}" class="document-thumbnail" alt="Document preview"
                                                 onclick="previewDocument(${id}, 'inspection', ${index})" style="max-width: 150px; cursor: pointer;">
                                        </div>
                                    ` : ''}
                    </div>
                `;
                        });

                        documentsHtml += '</div></div></div>';
                    }

                    // No documents message
                    if ((!data.user_documents || data.user_documents.length === 0) &&
                        (!data.inspection_documents || data.inspection_documents.length === 0)) {
                        documentsHtml += `
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No documents found for this application.
                    </div>
                </div>
            `;
                    }

                    documentsHtml += '</div>';
                    document.getElementById('documentViewer').innerHTML = documentsHtml;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('documentViewerLoading').style.display = 'none';
                    document.getElementById('documentViewer').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading documents: ${error.message}
            </div>
        `;
                    document.getElementById('documentViewer').style.display = 'block';
                });
        }

        // Enhanced preview document function - FIXED route and error handling
        function previewDocument(id, type, index) {
            const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
            modal.show();

            // Show loading in preview
            document.getElementById('documentPreview').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading document preview...</p>
        </div>
    `;

            // Clear previous title
            document.getElementById('documentPreviewTitle').innerHTML = '<i class="fas fa-eye me-2"></i>Document Preview';

            fetch(`/admin/boatr/requests/${id}/document-preview`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: type,
                        index: index
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load document preview');
                    }

                    // Update modal title
                    document.getElementById('documentPreviewTitle').innerHTML =
                        `<i class="fas fa-eye me-2"></i>${data.document_name}`;

                    const fileExtension = data.document_type?.toLowerCase() ||
                        data.document_name.split('.').pop().toLowerCase();
                    const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension);
                    const isPdf = fileExtension === 'pdf';

                    if (isPdf) {
                        document.getElementById('documentPreview').innerHTML = `
                <div class="text-center">
                    <embed src="${data.document_url}" type="application/pdf" width="100%" height="600px"
                           style="border: none; border-radius: 8px;" />
                    <div class="mt-2">
                        <a href="${data.document_url}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                        </a>
                    </div>
                </div>
            `;
                    } else if (isImage) {
                        document.getElementById('documentPreview').innerHTML = `
                <div class="text-center">
                    <img src="${data.document_url}" class="img-fluid" alt="Document preview"
                         style="max-height: 600px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" />
                </div>
            `;
                    } else {
                        document.getElementById('documentPreview').innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <h5>Preview not available for this file type</h5>
                    <p>File type: ${fileExtension.toUpperCase()}</p>
                    <a href="${data.document_url}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i>Download to view
                    </a>
                </div>
            `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('documentPreview').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <h5>Error loading document preview</h5>
                <p>${error.message}</p>
                <small class="text-muted">Please try downloading the document instead.</small>
            </div>
        `;
                });
        }

        // Utility function to format file sizes
        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return 'Unknown size';

            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Enhanced view application details function - FIXED user documents structure
        function viewRegistration(id) {
            const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
            modal.show();

            // Show loading state
            document.getElementById('registrationDetailsLoading').style.display = 'block';
            document.getElementById('registrationDetails').style.display = 'none';

            fetch(`/admin/boatr/requests/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load application details');
                    }

                    // Hide loading, show content
                    document.getElementById('registrationDetailsLoading').style.display = 'none';
                    document.getElementById('registrationDetails').style.display = 'block';

                    // Build remarks HTML
                    let remarksHtml = '';
                    if (data.remarks) {
                        remarksHtml = `
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-comment me-2"></i>Remarks</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                <p class="mb-1">${data.remarks}</p>
                                ${data.reviewed_at ? `<small class="text-muted">Updated on ${data.reviewed_at}${data.reviewed_by_name ? ` by ${data.reviewed_by_name}` : ''}</small>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
                    }

                    // Build documents HTML - FIXED to show correct document counts
                    let documentHtml = '';
                    const userDocsCount = data.user_documents ? data.user_documents.length : 0;
                    const inspectionDocsCount = data.inspection_documents ? data.inspection_documents.length : 0;

                    if (userDocsCount > 0 || inspectionDocsCount > 0) {
                        documentHtml = `
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Documents</h6>
                            <button class="btn btn-sm btn-info" onclick="viewDocuments(${id})">
                                <i class="fas fa-eye me-1"></i>View All Documents
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>User Documents:</strong> ${userDocsCount}</p>
                                    <p><strong>Inspection Documents:</strong> ${inspectionDocsCount}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Documents Verified:</strong> ${data.documents_verified ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No</span>'}</p>
                                    ${data.documents_verified_at ? `<p><strong>Verified At:</strong> ${data.documents_verified_at}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                    }

                    // Populate modal content
                    document.getElementById('registrationDetails').innerHTML = `
            <div class="row">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Application #:</strong> <span class="badge bg-primary">${data.application_number}</span></p>
                            <p><strong>Name:</strong> ${data.full_name}</p>
                            <p><strong>Mobile:</strong> ${data.mobile || 'N/A'}</p>
                            <p><strong>Email:</strong> ${data.email || 'N/A'}</p>
                            <p><strong>FishR Number:</strong> ${data.fishr_number}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-ship me-2"></i>Vessel Information</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Vessel Name:</strong> ${data.vessel_name}</p>
                            <p><strong>Boat Type:</strong> ${data.boat_type}</p>
                            <p><strong>Dimensions:</strong> ${data.boat_dimensions}</p>
                            <p><strong>Engine Type:</strong> ${data.engine_type}</p>
                            <p><strong>Engine HP:</strong> ${data.engine_horsepower} HP</p>
                            <p><strong>Primary Fishing Gear:</strong> ${data.primary_fishing_gear}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Application Status</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Status:</strong> <span class="badge bg-${data.status_color}">${data.formatted_status}</span></p>
                            <p><strong>Inspection:</strong> ${data.inspection_completed ? '<span class="badge bg-success">Completed</span>' : '<span class="badge bg-warning">Pending</span>'}</p>
                            ${data.inspection_date ? `<p><strong>Inspection Date:</strong> ${data.inspection_date}</p>` : ''}
                            ${data.inspection_notes ? `<p><strong>Inspection Notes:</strong> ${data.inspection_notes}</p>` : ''}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Date Applied:</strong> ${data.created_at}</p>
                            <p><strong>Last Updated:</strong> ${data.updated_at}</p>
                            ${data.reviewed_at ? `<p><strong>Last Reviewed:</strong> ${data.reviewed_at}</p>` : ''}
                            ${data.reviewed_by_name ? `<p><strong>Reviewed By:</strong> ${data.reviewed_by_name}</p>` : ''}
                        </div>
                    </div>
                </div>
                ${documentHtml}
                ${remarksHtml}
            </div>
        `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('registrationDetailsLoading').style.display = 'none';
                    document.getElementById('registrationDetails').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading application details: ${error.message}
            </div>
        `;
                    document.getElementById('registrationDetails').style.display = 'block';
                });
        }



        // Console log for debugging
        console.log('BoatR Admin JavaScript loaded successfully ✅');

        // Initialize tooltips if Bootstrap is available
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap !== 'undefined') {
                // Initialize tooltips
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });

        // Error handling for missing elements
        function safeElementAction(elementId, action) {
            const element = document.getElementById(elementId);
            if (element) {
                action(element);
            } else {
                console.warn(`Element with ID '${elementId}' not found`);
            }
        }

        // Global error handler for AJAX requests
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled promise rejection:', event.reason);
            // Optionally show a user-friendly error message
            if (typeof showToast === 'function') {
                showToast('error', 'Error', 'An unexpected error occurred. Please try again.');
            }
        });

        // Add network status detection
        window.addEventListener('online', function() {
            console.log('Network connection restored');
            if (typeof showToast === 'function') {
                showToast('success', 'Connected', 'Network connection restored');
            }
        });

        window.addEventListener('offline', function() {
            console.log('Network connection lost');
            if (typeof showToast === 'function') {
                showToast('warning', 'Offline', 'Network connection lost. Some features may not work.');
            }
        });

        // Utility function for safe JSON parsing
        function safeJsonParse(jsonString, fallback = {}) {
            try {
                return JSON.parse(jsonString);
            } catch (error) {
                console.error('JSON parsing error:', error);
                return fallback;
            }
        }

        // Enhanced logging function
        function logAction(action, data = {}) {
            const timestamp = new Date().toISOString();
            const logEntry = {
                timestamp,
                action,
                data,
                userAgent: navigator.userAgent,
                url: window.location.href
            };

            console.log(`[${timestamp}] ${action}:`, data);

            // Store in sessionStorage for debugging
            try {
                const logs = safeJsonParse(sessionStorage.getItem('boatr_admin_logs') || '[]', []);
                logs.push(logEntry);

                // Keep only last 50 logs
                if (logs.length > 50) {
                    logs.splice(0, logs.length - 50);
                }

                sessionStorage.setItem('boatr_admin_logs', JSON.stringify(logs));
            } catch (error) {
                console.warn('Could not store log entry:', error);
            }
        }

        // Function to get debug logs
        function getDebugLogs() {
            return safeJsonParse(sessionStorage.getItem('boatr_admin_logs') || '[]', []);
        }

        // Function to clear debug logs
        function clearDebugLogs() {
            sessionStorage.removeItem('boatr_admin_logs');
            console.log('Debug logs cleared');
        }

        // Export functions for console debugging
        window.BoatRAdmin = {
            viewDocuments,
            previewDocument,
            viewRegistration,
            formatFileSize,
            getDebugLogs,
            clearDebugLogs,
            logAction
        };

        console.log('BoatR Admin utilities available via window.BoatRAdmin');


        // Update statistics cards (optional enhancement)
        function updateStatisticsCards(statistics) {
            if (statistics.total !== undefined) {
                const totalElement = document.querySelector('.border-left-primary .h5');
                if (totalElement) {
                    totalElement.textContent = statistics.total;
                }
            }

            if (statistics.pending !== undefined) {
                const pendingElement = document.querySelector('.border-left-info .h5');
                if (pendingElement) {
                    pendingElement.textContent = statistics.pending;
                }
            }

            if (statistics.inspection_required !== undefined) {
                const inspectionElement = document.querySelector('.border-left-warning .h5');
                if (inspectionElement) {
                    inspectionElement.textContent = statistics.inspection_required;
                }
            }

            if (statistics.approved !== undefined) {
                const approvedElement = document.querySelector('.border-left-success .h5');
                if (approvedElement) {
                    approvedElement.textContent = statistics.approved;
                }
            }
        }

        // File upload validation for inspection modal
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('supporting_document');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    const errorElement = document.getElementById('documentError');

                    // Clear previous errors
                    this.classList.remove('is-invalid');
                    errorElement.textContent = '';

                    if (file) {
                        // Check file size (10MB)
                        if (file.size > 10 * 1024 * 1024) {
                            this.classList.add('is-invalid');
                            errorElement.textContent = 'File size must be less than 10MB';
                            return;
                        }

                        // Check file type
                        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                        if (!allowedTypes.includes(file.type)) {
                            this.classList.add('is-invalid');
                            errorElement.textContent = 'Only PDF, JPG, JPEG, and PNG files are allowed';
                            return;
                        }

                        // Show file info
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        errorElement.className = 'text-success small';
                        errorElement.textContent = `Selected: ${file.name} (${fileSize} MB)`;
                    }
                });
            }
        });

        // Date Filter Functions
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
                alert('From date cannot be later than To date');
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
                statusElement.innerHTML = 'No date filter applied - showing all registrations';
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

        // Add keyboard shortcut listener for refresh and modal closing
        document.addEventListener('keydown', function(event) {
            // ESC to close modals
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal.show');
                modals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            }

            // Ctrl+R to refresh data
            if (event.ctrlKey && event.key === 'r') {
                event.preventDefault();
                refreshData();
            }
        });
    </script>
@endsection
