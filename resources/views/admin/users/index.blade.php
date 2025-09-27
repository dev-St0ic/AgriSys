{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'User Registration Management - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-user-edit text-primary me-2"></i>
        <span class="text-primary fw-bold">User Registration Management</span>
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
                                Total Registrations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-count">
                                {{ $stats['total'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Unverified (Basic Signup)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="unverified-count">
                                {{ $stats['unverified'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
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
                                Pending Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pending-count">
                                {{ $stats['pending'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="approved-count">
                                {{ $stats['approved'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rejected-count">
                                {{ $stats['rejected'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h6>
            <button type="button" class="btn btn-sm btn-info" onclick="testConnection()">
                <i class="fas fa-network-wired me-1"></i>Test Connection
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.registrations.index') }}" id="filterForm">
                <!-- Hidden date inputs -->
                <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                <div class="row">
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Status</option>
                            <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>
                                Unverified (Basic Signup)
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                Pending Review
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
                        <select name="user_type" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Types</option>
                            <option value="farmer" {{ request('user_type') == 'farmer' ? 'selected' : '' }}>
                                Farmer
                            </option>
                            <option value="fisherfolk" {{ request('user_type') == 'fisherfolk' ? 'selected' : '' }}>
                                Fisherfolk
                            </option>
                            <option value="general" {{ request('user_type') == 'general' ? 'selected' : '' }}>
                                General Public
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="verification_status" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Email Status</option>
                            <option value="verified" {{ request('verification_status') == 'verified' ? 'selected' : '' }}>
                                Email Verified
                            </option>
                            <option value="unverified"
                                {{ request('verification_status') == 'unverified' ? 'selected' : '' }}>
                                Email Unverified
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search username, email, name..." value="{{ request('search') }}"
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
                        <a href="{{ route('admin.registrations.index') }}" class="btn btn-secondary btn-sm w-100">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Registrations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">User Registration Records</h6>
            <div class="btn-group">
                <button type="button" class="btn btn-primary btn-sm" onclick="exportRegistrations()">
                    <i class="fas fa-download me-2"></i>Export Data
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>Registration Date</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact Number</th>
                            <th>User Type</th>
                            <th>Status</th>
                            <th>Email Verified</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr data-id="{{ $registration->id }}">
                                <td>{{ $registration->created_at->format('M d, Y g:i A') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar rounded-circle me-2 d-flex align-items-center justify-content-center text-white font-weight-bold"
                                            style="width: 35px; height: 35px;
                                         background-color: {{ $registration->user_type === 'farmer' ? '#28a745' : ($registration->user_type === 'fisherfolk' ? '#17a2b8' : '#6c757d') }}">
                                            {{ strtoupper(substr($registration->username, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-primary">{{ $registration->username }}</div>
                                            @if ($registration->user_type)
                                                <small class="text-muted">{{ ucfirst($registration->user_type) }}</small>
                                            @else
                                                <small class="text-muted text-warning">Type not selected</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($registration->first_name || $registration->last_name)
                                        <div class="font-weight-bold">
                                            {{ trim($registration->first_name . ' ' . ($registration->middle_name ? $registration->middle_name . ' ' : '') . $registration->last_name . ($registration->name_extension ? ', ' . $registration->name_extension : '')) }}
                                        </div>
                                    @else
                                        <span class="text-muted font-italic">Not provided (Basic signup only)</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="mailto:{{ $registration->email }}" class="text-decoration-none">
                                        {{ $registration->email }}
                                    </a>
                                </td>
                                <td>
                                    @if ($registration->contact_number)
                                        <a href="tel:{{ $registration->contact_number }}" class="text-decoration-none">
                                            {{ $registration->contact_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($registration->user_type)
                                        <span
                                            class="badge fs-6
                                        @if ($registration->user_type === 'farmer') bg-success
                                        @elseif($registration->user_type === 'fisherfolk') bg-info
                                        @else bg-secondary @endif">
                                            {{ ucfirst($registration->user_type) }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning fs-6">Not Selected</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColor = match ($registration->status) {
                                            'unverified' => 'warning',
                                            'pending' => 'info',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary',
                                        };
                                        $statusText = match ($registration->status) {
                                            'unverified' => 'Basic Signup',
                                            'pending' => 'Pending Review',
                                            'approved' => 'Approved',
                                            'rejected' => 'Rejected',
                                            default => ucfirst($registration->status),
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} fs-6">{{ $statusText }}</span>
                                </td>
                                <td>
                                    @if ($registration->hasVerifiedEmail())
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge bg-secondary fs-6">
                                            <i class="fas fa-times"></i> Unverified
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <!-- Location Document -->
                                        @if ($registration->location_document_path)
                                            <span class="badge bg-success fs-6 cursor-pointer"
                                                title="Location Document Uploaded - Click to View"
                                                onclick="viewDocumentDirect({{ $registration->id }}, 'location')">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-secondary fs-6" title="Location Document Missing">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                        @endif

                                        <!-- ID Front -->
                                        @if ($registration->id_front_path)
                                            <span class="badge bg-success fs-6 cursor-pointer"
                                                title="ID Front Uploaded - Click to View"
                                                onclick="viewDocumentDirect({{ $registration->id }}, 'id_front')">
                                                <i class="fas fa-id-card"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-secondary fs-6" title="ID Front Missing">
                                                <i class="fas fa-id-card"></i>
                                            </span>
                                        @endif

                                        <!-- ID Back -->
                                        @if ($registration->id_back_path)
                                            <span class="badge bg-success fs-6 cursor-pointer"
                                                title="ID Back Uploaded - Click to View"
                                                onclick="viewDocumentDirect({{ $registration->id }}, 'id_back')">
                                                <i class="fas fa-id-card-alt"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-secondary fs-6" title="ID Back Missing">
                                                <i class="fas fa-id-card-alt"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="viewRegistration({{ $registration->id }})" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                        <button class="btn btn-sm btn-outline-success"
                                            onclick="showUpdateModal({{ $registration->id }}, '{{ $registration->status }}')"
                                            title="Update Status">
                                            <i class="fas fa-edit"></i> Update
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteRegistration({{ $registration->id }})" title="Delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-user-edit fa-3x mb-3"></i>
                                    <p>No user registrations found matching your criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
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
                        <i class="fas fa-edit me-2"></i>Update Registration Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Registration Info -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-2">
                                <i class="fas fa-info-circle me-2"></i>Registration Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>ID:</strong> <span id="updateRegId"></span></p>
                                    <p class="mb-1"><strong>Username:</strong> <span id="updateRegUsername"></span></p>
                                    <p class="mb-1"><strong>Full Name:</strong> <span id="updateRegName"></span></p>
                                    <p class="mb-1"><strong>Email:</strong> <span id="updateRegEmail"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>User Type:</strong> <span id="updateRegType"></span></p>
                                    <p class="mb-1"><strong>Contact Number:</strong> <span id="updateRegContact"></span>
                                    </p>
                                    <p class="mb-1"><strong>Current Status:</strong> <span
                                            id="updateRegCurrentStatus"></span></p>
                                    <p class="mb-1"><strong>Documents:</strong> <span id="updateRegDocuments"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Form -->
                    <form id="updateForm">
                        <input type="hidden" id="updateRegistrationId">
                        <div class="mb-3">
                            <label for="newStatus" class="form-label">Select New Status:</label>
                            <select class="form-select" id="newStatus" required>
                                <option value="">Choose status...</option>
                                <option value="unverified">Unverified (Basic Signup)</option>
                                <option value="pending">Pending Review</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks (Optional):</label>
                            <textarea class="form-control" id="remarks" rows="3"
                                placeholder="Add any notes or comments about this status change..."></textarea>
                            <div class="form-text">Maximum 1000 characters</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateRegistrationStatus()">Update
                        Status</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Registration Details Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>Complete Registration Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="registrationDetails">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-info btn-sm" onclick="viewDocument('location')"
                            id="viewLocationDoc">
                            <i class="fas fa-map-marker-alt me-2"></i>View Location Document
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="viewDocument('id_front')"
                            id="viewIdFront">
                            <i class="fas fa-id-card me-2"></i>View ID Front
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="viewDocument('id_back')"
                            id="viewIdBack">
                            <i class="fas fa-id-card-alt me-2"></i>View ID Back
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="quickUpdateStatus('approved')"
                            id="quickApprove">
                            <i class="fas fa-check me-2"></i>Quick Approve
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="quickUpdateStatus('rejected')"
                            id="quickReject">
                            <i class="fas fa-times me-2"></i>Quick Reject
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Document Viewer Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="documentModalTitle">
                        <i class="fas fa-file-image me-2"></i>Document View
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="documentModalBody">
                    <!-- Document content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="downloadDocument()">
                            <i class="fas fa-download me-2"></i>Download
                        </button>
                        <button type="button" class="btn btn-success" onclick="openInNewTab()">
                            <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                        </button>
                        <button type="button" class="btn btn-info" onclick="zoomDocument()">
                            <i class="fas fa-search-plus me-2"></i>Zoom
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
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
        /* Border styles for statistics cards */
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }

        .text-xs {
            font-size: 0.7rem;
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

        .cursor-pointer {
            cursor: pointer;
        }

        .cursor-pointer:hover {
            opacity: 0.8;
            transform: scale(1.1);
            transition: all 0.2s ease;
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

        /* Enhanced document viewer styles */
        .document-image {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .document-image:hover {
            transform: scale(1.02);
        }

        .document-image.zoomed {
            transform: scale(1.5);
            cursor: zoom-out;
        }

        .document-placeholder {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }

        /* Loading states */
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Enhanced status badges */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Custom Pagination Styles */
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

        /* Enhanced alert styles */
        .alert {
            border-left: 4px solid;
        }

        .alert-success {
            border-left-color: #28a745;
        }

        .alert-danger {
            border-left-color: #dc3545;
        }

        .alert-warning {
            border-left-color: #ffc107;
        }

        .alert-info {
            border-left-color: #17a2b8;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Enhanced Admin User Management JavaScript with Document Viewing and Auto-refresh

        // Global variables
        let searchTimeout;
        let currentRegistrationId = null;
        let currentDocumentUrl = null;
        let currentDocumentInfo = null;
        let isDocumentZoomed = false;

        // Get CSRF token utility function
        function getCSRFToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            return metaTag ? metaTag.getAttribute('content') : '';
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('JavaScript loaded and CSRF token available:', getCSRFToken());
        });

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

        // Test connection function
        function testConnection() {
            console.log('Testing connection to server...');

            fetch('/admin/debug/auth', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Test response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Test response data:', data);
                    alert('Connection test successful!\nServer is responding and authentication is working.');
                })
                .catch(error => {
                    console.error('Connection test failed:', error);
                    alert('Connection test failed: ' + error.message +
                        '\n\nPlease check:\n1. Is the server running?\n2. Are you logged in as admin?\n3. Check browser console for details.'
                        );
                });
        }

        // Helper function to get status display text
        function getStatusText(status) {
            switch (status) {
                case 'unverified':
                    return 'Unverified (Basic Signup)';
                case 'pending':
                    return 'Pending Review';
                case 'approved':
                    return 'Approved';
                case 'rejected':
                    return 'Rejected';
                default:
                    return status;
            }
        }

        // Enhanced view registration details function
        function viewRegistration(id) {
            currentRegistrationId = id;

            console.log('Viewing registration:', id);

            // Show loading state
            showLoadingInModal('registrationDetails');

            // Show modal while loading
            const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
            modal.show();

            // Fetch registration details
            console.log('Fetching registration details from:', `/admin/registrations/${id}/details`);

            fetch(`/admin/registrations/${id}/details`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);

                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Error response body:', text);
                            throw new Error(
                                `HTTP ${response.status}: ${response.statusText} - ${text.slice(0, 200)}`);
                        });
                    }
                    return response.json();
                })
                .then(response => {
                    console.log('Registration details received:', response);

                    if (!response.success) {
                        throw new Error(response.message || 'Failed to load registration details');
                    }

                    const data = response.data;
                    renderRegistrationDetails(data);
                    updateDocumentButtons(data);
                    updateQuickActionButtons(data);
                })
                .catch(error => {
                    console.error('Detailed error loading registration:', error);
                    console.error('Error stack:', error.stack);

                    let errorMessage = 'Error loading registration details';
                    if (error.message.includes('Failed to fetch')) {
                        errorMessage =
                            'Network error: Unable to connect to server. Please check if the server is running and try again.';
                    } else if (error.message.includes('403')) {
                        errorMessage = 'Access denied: You do not have permission to view this registration.';
                    } else if (error.message.includes('404')) {
                        errorMessage = 'Registration not found: This registration may have been deleted.';
                    } else if (error.message.includes('500')) {
                        errorMessage = 'Server error: Please try again later or contact support.';
                    } else {
                        errorMessage += ': ' + error.message;
                    }

                    showErrorInModal('registrationDetails', 'Error loading registration details', errorMessage);
                });
        }

        // Enhanced render registration details
        function renderRegistrationDetails(data) {
            const remarksHtml = data.rejection_reason ? `
        <div class="col-12 mt-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Admin Remarks</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">${data.rejection_reason}</p>
                    <small class="text-muted">
                        ${data.approved_at || data.rejected_at ? `Updated on ${data.approved_at || data.rejected_at}` : ''}
                        ${data.approved_by ? ` by ${data.approved_by}` : ''}
                    </small>
                </div>
            </div>
        </div>` : '';

            const isBasicSignup = !data.first_name && !data.last_name && !data.contact_number;

            const basicSignupAlert = isBasicSignup ? `
        <div class="col-12 mb-3">
            <div class="alert alert-warning border-warning">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fa-lg"></i>
                    <div>
                        <strong>Basic Signup User:</strong> This user has only provided username, email, and password.
                        They need to complete their profile verification to access full services.
                    </div>
                </div>
            </div>
        </div>` : '';

            const statusBadgeColor = getStatusBadgeColor(data.status);
            const emailVerifiedBadge = data.email_verified ?
                '<span class="badge bg-success"><i class="fas fa-check"></i> Verified</span>' :
                '<span class="badge bg-secondary"><i class="fas fa-times"></i> Unverified</span>';

            document.getElementById('registrationDetails').innerHTML = `
        <div class="row g-4">
            ${basicSignupAlert}

            <!-- Personal Information Card -->
            <div class="col-md-6">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12"><strong>Username:</strong> <span class="text-primary">${data.username || 'N/A'}</span></div>
                            <div class="col-12"><strong>First Name:</strong> ${data.first_name || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Middle Name:</strong> ${data.middle_name || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Last Name:</strong> ${data.last_name || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Name Extension:</strong> ${data.name_extension || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Date of Birth:</strong> ${data.date_of_birth || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Age:</strong> ${data.age || '<span class="text-muted">Not calculated</span>'}</div>
                            <div class="col-12"><strong>Gender:</strong> ${data.gender || '<span class="text-muted">Not specified</span>'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact & Status Card -->
            <div class="col-md-6">
                <div class="card h-100 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-address-card me-2"></i>Contact & Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12"><strong>Email:</strong> <a href="mailto:${data.email}" class="text-decoration-none">${data.email}</a></div>
                            <div class="col-12"><strong>Contact Number:</strong> ${data.contact_number ? `<a href="tel:${data.contact_number}" class="text-decoration-none">${data.contact_number}</a>` : '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>User Type:</strong> ${data.user_type ? `<span class="badge bg-secondary">${data.user_type}</span>` : '<span class="badge bg-warning">Not selected yet</span>'}</div>
                            <div class="col-12"><strong>Current Status:</strong> <span class="badge bg-${statusBadgeColor}">${getStatusText(data.status)}</span></div>
                            <div class="col-12"><strong>Email Verified:</strong> ${emailVerifiedBadge}</div>
                            <div class="col-12"><strong>Registration Date:</strong> ${data.created_at}</div>
                            <div class="col-12"><strong>Last Login:</strong> ${data.last_login_at || '<span class="text-muted">Never</span>'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information Card -->
            <div class="col-md-6">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12"><strong>Complete Address:</strong> ${data.complete_address || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Barangay:</strong> ${data.barangay || '<span class="text-muted">Not provided</span>'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information Card -->
            <div class="col-md-6">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Additional Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12"><strong>Occupation:</strong> ${data.occupation || '<span class="text-muted">Not specified</span>'}</div>
                            <div class="col-12"><strong>Organization:</strong> ${data.organization || '<span class="text-muted">Not specified</span>'}</div>
                            <div class="col-12"><strong>Emergency Contact:</strong> ${data.emergency_contact_name || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Emergency Phone:</strong> ${data.emergency_contact_phone ? `<a href="tel:${data.emergency_contact_phone}" class="text-decoration-none">${data.emergency_contact_phone}</a>` : '<span class="text-muted">Not provided</span>'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Status Card -->
            <div class="col-12">
                <div class="card border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i>Document Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded ${data.location_document_path ? 'border-success bg-light' : 'border-secondary'}">
                                    <i class="fas fa-map-marker-alt fa-3x ${data.location_document_path ? 'text-success' : 'text-secondary'} mb-2"></i>
                                    <h6>Location Proof Document</h6>
                                    <span class="badge ${data.location_document_path ? 'bg-success' : 'bg-secondary'} mb-2">
                                        ${data.location_document_path ? 'Uploaded' : 'Not Uploaded'}
                                    </span>
                                    ${data.location_document_path ? `<br><button class="btn btn-sm btn-outline-info" onclick="viewDocument('location')"><i class="fas fa-eye"></i> View</button>` : ''}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded ${data.id_front_path ? 'border-success bg-light' : 'border-secondary'}">
                                    <i class="fas fa-id-card fa-3x ${data.id_front_path ? 'text-success' : 'text-secondary'} mb-2"></i>
                                    <h6>Government ID - Front</h6>
                                    <span class="badge ${data.id_front_path ? 'bg-success' : 'bg-secondary'} mb-2">
                                        ${data.id_front_path ? 'Uploaded' : 'Not Uploaded'}
                                    </span>
                                    ${data.id_front_path ? `<br><button class="btn btn-sm btn-outline-info" onclick="viewDocument('id_front')"><i class="fas fa-eye"></i> View</button>` : ''}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded ${data.id_back_path ? 'border-success bg-light' : 'border-secondary'}">
                                    <i class="fas fa-id-card-alt fa-3x ${data.id_back_path ? 'text-success' : 'text-secondary'} mb-2"></i>
                                    <h6>Government ID - Back</h6>
                                    <span class="badge ${data.id_back_path ? 'bg-success' : 'bg-secondary'} mb-2">
                                        ${data.id_back_path ? 'Uploaded' : 'Not Uploaded'}
                                    </span>
                                    ${data.id_back_path ? `<br><button class="btn btn-sm btn-outline-info" onclick="viewDocument('id_back')"><i class="fas fa-eye"></i> View</button>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Information Card -->
            <div class="col-12">
                <div class="card border-dark">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Technical Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>Registration IP:</strong><br><code>${data.registration_ip || 'N/A'}</code></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Referral Source:</strong><br>${data.referral_source || 'Direct'}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Terms Accepted:</strong><br>${data.terms_accepted ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'}</p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Privacy Accepted:</strong><br>${data.privacy_accepted ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            ${remarksHtml}
        </div>`;
        }

        // Helper function to get status badge color
        function getStatusBadgeColor(status) {
            switch (status) {
                case 'unverified':
                    return 'warning';
                case 'pending':
                    return 'info';
                case 'approved':
                    return 'success';
                case 'rejected':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }

        // Update document viewer buttons based on available documents
        function updateDocumentButtons(data) {
            const locationBtn = document.getElementById('viewLocationDoc');
            const idFrontBtn = document.getElementById('viewIdFront');
            const idBackBtn = document.getElementById('viewIdBack');

            const hasLocationDoc = data.location_document_path;

            if (locationBtn) {
                locationBtn.style.display = hasLocationDoc ? 'inline-block' : 'none';
                locationBtn.disabled = !hasLocationDoc;
            }

            if (idFrontBtn) {
                idFrontBtn.style.display = data.id_front_path ? 'inline-block' : 'none';
                idFrontBtn.disabled = !data.id_front_path;
            }

            if (idBackBtn) {
                idBackBtn.style.display = data.id_back_path ? 'inline-block' : 'none';
                idBackBtn.disabled = !data.id_back_path;
            }
        }

        // Update quick action buttons
        function updateQuickActionButtons(data) {
            const quickApprove = document.getElementById('quickApprove');
            const quickReject = document.getElementById('quickReject');

            if (quickApprove) {
                quickApprove.style.display = data.status !== 'approved' ? 'inline-block' : 'none';
            }

            if (quickReject) {
                quickReject.style.display = data.status !== 'rejected' ? 'inline-block' : 'none';
            }
        }

        // Enhanced view document function with direct access
        function viewDocumentDirect(registrationId, documentType) {
            currentRegistrationId = registrationId;
            viewDocument(documentType);
        }

        // Enhanced view document function
        function viewDocument(documentType) {
            if (!currentRegistrationId) {
                showAlert('error', 'Registration ID not found');
                return;
            }

            console.log('Viewing document:', documentType, 'for registration:', currentRegistrationId);

            const documentModal = new bootstrap.Modal(document.getElementById('documentModal'));
            const modalTitle = document.getElementById('documentModalTitle');
            const modalBody = document.getElementById('documentModalBody');

            // Set modal title
            const titles = {
                'location': 'Location/Role Proof Document',
                'id_front': 'Government ID - Front',
                'id_back': 'Government ID - Back'
            };

            if (modalTitle) {
                modalTitle.innerHTML = `<i class="fas fa-file-image me-2"></i>${titles[documentType]}`;
            }

            // Show loading
            if (modalBody) {
                modalBody.innerHTML = `
            <div class="text-center">
                <div class="loading-spinner"></div>
                <p class="mt-3">Loading document...</p>
            </div>`;
            }

            // Show modal
            documentModal.show();

            // Reset zoom state
            isDocumentZoomed = false;

            // Fetch document
            fetch(`/admin/registrations/${currentRegistrationId}/document/${documentType}`)
                .then(response => {
                    console.log('Document response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Document response:', data);

                    if (data.success && data.document_url) {
                        currentDocumentUrl = data.document_url;
                        currentDocumentInfo = data.file_info;

                        if (modalBody) {
                            const isImage = data.file_info && data.file_info.is_image;

                            if (isImage) {
                                modalBody.innerHTML = `
                            <div class="document-container">
                                <img src="${data.document_url}"
                                     alt="${titles[documentType]}"
                                     class="document-image"
                                     id="documentImage"
                                     onclick="toggleZoom()"
                                     style="cursor: zoom-in;"
                                     onerror="showImageError(this)">
                            </div>
                            <div class="mt-3 text-muted">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    ${data.file_info.name} • ${formatFileSize(data.file_info.size)} • Click to zoom
                                </small>
                            </div>`;
                            } else {
                                modalBody.innerHTML = `
                            <div class="document-placeholder">
                                <i class="fas fa-file fa-4x mb-3 text-primary"></i>
                                <h5>File Preview Not Available</h5>
                                <p>This file type cannot be displayed inline.</p>
                                <div class="mt-3">
                                    <p><strong>File:</strong> ${data.file_info.name}</p>
                                    <p><strong>Size:</strong> ${formatFileSize(data.file_info.size)}</p>
                                    <p><strong>Type:</strong> ${data.file_info.mime_type}</p>
                                </div>
                                <button class="btn btn-primary" onclick="openInNewTab()">
                                    <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                                </button>
                            </div>`;
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Document not available');
                    }
                })
                .catch(error => {
                    console.error('Error loading document:', error);
                    if (modalBody) {
                        modalBody.innerHTML = `
                    <div class="document-placeholder">
                        <i class="fas fa-exclamation-triangle fa-4x mb-3 text-warning"></i>
                        <h5>Document Could Not Be Loaded</h5>
                        <p class="text-muted">${error.message}</p>
                        <small class="text-muted">Please check the console for more details.</small>
                    </div>`;
                    }
                    currentDocumentUrl = null;
                    currentDocumentInfo = null;
                });
        }

        // Show image error
        function showImageError(img) {
            const container = img.parentElement;
            container.innerHTML = `
        <div class="document-placeholder">
            <i class="fas fa-image fa-4x mb-3 text-muted"></i>
            <h5>Image Could Not Be Loaded</h5>
            <p>The image file may be corrupted or in an unsupported format.</p>
            <button class="btn btn-primary" onclick="openInNewTab()">
                <i class="fas fa-external-link-alt me-1"></i>Try Opening in New Tab
            </button>
        </div>`;
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Toggle document zoom
        function toggleZoom() {
            const img = document.getElementById('documentImage');
            if (!img) return;

            isDocumentZoomed = !isDocumentZoomed;

            if (isDocumentZoomed) {
                img.classList.add('zoomed');
                img.style.cursor = 'zoom-out';
            } else {
                img.classList.remove('zoomed');
                img.style.cursor = 'zoom-in';
            }
        }

        // Zoom document function
        function zoomDocument() {
            toggleZoom();
        }

        // Download document function
        function downloadDocument() {
            if (currentDocumentUrl) {
                const link = document.createElement('a');
                link.href = currentDocumentUrl;
                if (currentDocumentInfo && currentDocumentInfo.name) {
                    link.download = currentDocumentInfo.name;
                }
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showAlert('success', 'Download started');
            } else {
                showAlert('error', 'No document available for download');
            }
        }

        // Open document in new tab
        function openInNewTab() {
            if (currentDocumentUrl) {
                window.open(currentDocumentUrl, '_blank');
            } else {
                showAlert('error', 'No document available to open');
            }
        }

        // Quick status update functions
        function quickUpdateStatus(newStatus) {
            if (!currentRegistrationId) {
                showAlert('error', 'No registration selected');
                return;
            }

            const statusText = getStatusText(newStatus);
            const confirmMessage = `Are you sure you want to ${newStatus} this registration?`;

            if (!confirm(confirmMessage)) {
                return;
            }

            // Close the registration modal first
            const registrationModal = bootstrap.Modal.getInstance(document.getElementById('registrationModal'));
            if (registrationModal) {
                registrationModal.hide();
            }

            // Perform the status update
            updateRegistrationStatusDirect(currentRegistrationId, newStatus);
        }

        // Direct status update
        function updateRegistrationStatusDirect(id, status, remarks = '') {
            const endpoint = `/admin/registrations/${id}/update-status`;
            const requestData = {
                status: status,
                remarks: remarks
            };

            fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(response => {
                    if (response.success) {
                        showAlert('success', response.message);

                        // Auto-refresh the page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(response.message || 'Error updating status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Error updating registration status: ' + error.message);
                });
        }

        // Show update modal function
        function showUpdateModal(id, currentStatus) {
            document.getElementById('updateRegId').innerHTML = `
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>`;

            fetch(`/admin/registrations/${id}/details`)
                .then(response => response.json())
                .then(response => {
                    if (!response.success) {
                        throw new Error('Failed to load registration details');
                    }

                    const data = response.data;
                    document.getElementById('updateRegistrationId').value = id;
                    document.getElementById('updateRegId').textContent = data.id;
                    document.getElementById('updateRegUsername').textContent = data.username || 'N/A';
                    document.getElementById('updateRegName').textContent = data.full_name ||
                        'Not provided (Basic signup only)';
                    document.getElementById('updateRegEmail').textContent = data.email;
                    document.getElementById('updateRegType').textContent = data.user_type || 'Not selected';
                    document.getElementById('updateRegContact').textContent = data.contact_number || 'Not provided';

                    const currentStatusElement = document.getElementById('updateRegCurrentStatus');
                    const statusColor = getStatusBadgeColor(data.status);
                    currentStatusElement.innerHTML = `
                <span class="badge bg-${statusColor}">${getStatusText(data.status)}</span>`;

                    const documentsElement = document.getElementById('updateRegDocuments');
                    let documentStatus = [];
                    if (data.location_document_path) documentStatus.push(
                        '<span class="badge bg-success fs-6">Location</span>');
                    if (data.id_front_path) documentStatus.push('<span class="badge bg-success fs-6">ID Front</span>');
                    if (data.id_back_path) documentStatus.push('<span class="badge bg-success fs-6">ID Back</span>');
                    documentsElement.innerHTML = documentStatus.length > 0 ? documentStatus.join(' ') :
                        '<span class="text-muted">None uploaded</span>';

                    const statusSelect = document.getElementById('newStatus');
                    const remarksTextarea = document.getElementById('remarks');

                    statusSelect.value = data.status;
                    statusSelect.dataset.originalStatus = data.status;

                    remarksTextarea.value = data.rejection_reason || '';
                    remarksTextarea.dataset.originalRemarks = data.rejection_reason || '';

                    statusSelect.classList.remove('form-changed');
                    remarksTextarea.classList.remove('form-changed');
                    statusSelect.parentElement.classList.remove('change-indicator', 'changed');
                    remarksTextarea.parentElement.classList.remove('change-indicator', 'changed');

                    statusSelect.parentElement.classList.add('change-indicator');
                    remarksTextarea.parentElement.classList.add('change-indicator');

                    const updateButton = document.querySelector('#updateModal .btn-primary');
                    updateButton.classList.remove('no-changes');

                    const modal = new bootstrap.Modal(document.getElementById('updateModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Error loading registration details: ' + error.message);
                });
        }

        // Update registration status function
        function updateRegistrationStatus() {
            const id = document.getElementById('updateRegistrationId').value;
            const newStatus = document.getElementById('newStatus').value;
            const remarks = document.getElementById('remarks').value;

            if (!newStatus) {
                showAlert('error', 'Please select a status');
                return;
            }

            const originalStatus = document.getElementById('newStatus').dataset.originalStatus;
            const originalRemarks = document.getElementById('remarks').dataset.originalRemarks || '';

            if (newStatus === originalStatus && remarks.trim() === originalRemarks.trim()) {
                showAlert('warning', 'No changes detected. Please modify the status or remarks before updating.');
                return;
            }

            let changesSummary = [];
            if (newStatus !== originalStatus) {
                const originalStatusText = getStatusText(originalStatus);
                const newStatusText = getStatusText(newStatus);
                changesSummary.push(`Status: ${originalStatusText} → ${newStatusText}`);
            }
            if (remarks.trim() !== originalRemarks.trim()) {
                if (originalRemarks.trim() === '') {
                    changesSummary.push('Remarks: Added new remarks');
                } else if (remarks.trim() === '') {
                    changesSummary.push('Remarks: Removed existing remarks');
                } else {
                    changesSummary.push('Remarks: Modified');
                }
            }

            const confirmMessage =
                `Are you sure you want to update this registration with the following changes?\n\n${changesSummary.join('\n')}`;

            if (!confirm(confirmMessage)) {
                return;
            }

            const updateButton = document.querySelector('#updateModal .btn-primary');
            const originalText = updateButton.innerHTML;
            updateButton.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
            updateButton.disabled = true;

            const endpoint = `/admin/registrations/${id}/update-status`;
            const requestData = {
                status: newStatus,
                remarks: remarks
            };

            fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(response => {
                    if (response.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                        modal.hide();
                        showAlert('success', response.message);

                        // Auto-refresh the page
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(response.message || 'Error updating status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Error updating registration status: ' + error.message);
                })
                .finally(() => {
                    updateButton.innerHTML = originalText;
                    updateButton.disabled = false;
                });
        }

        // Delete registration
        function deleteRegistration(id) {
            if (!confirm('Are you sure you want to delete this registration? This action cannot be undone.')) {
                return;
            }

            fetch(`/admin/registrations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 300);
                        }
                        refreshStats();
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred while deleting the registration.');
                });
        }

        // Enhanced alert system
        function showAlert(type, message) {
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => alert.remove());

            const alertClass = type === 'error' ? 'danger' : type;
            const iconClass = {
                'success': 'fas fa-check-circle',
                'danger': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            } [alertClass] || 'fas fa-info-circle';

            const alertHtml = `
        <div class="alert alert-${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', alertHtml);

            if (type === 'success') {
                setTimeout(() => {
                    const successAlert = document.querySelector('.alert-success');
                    if (successAlert) {
                        successAlert.style.transition = 'opacity 0.3s';
                        successAlert.style.opacity = '0';
                        setTimeout(() => successAlert.remove(), 300);
                    }
                }, 5000);
            }
        }

        // Refresh statistics
        function refreshStats() {
            fetch('/admin/registrations/statistics')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.data;
                        const totalEl = document.getElementById('total-count');
                        const unverifiedEl = document.getElementById('unverified-count');
                        const pendingEl = document.getElementById('pending-count');
                        const approvedEl = document.getElementById('approved-count');
                        const rejectedEl = document.getElementById('rejected-count');

                        if (totalEl) totalEl.textContent = stats.total;
                        if (unverifiedEl) unverifiedEl.textContent = stats.unverified;
                        if (pendingEl) pendingEl.textContent = stats.pending;
                        if (approvedEl) approvedEl.textContent = stats.approved;
                        if (rejectedEl) rejectedEl.textContent = stats.rejected;
                    }
                })
                .catch(error => {
                    console.error('Error refreshing stats:', error);
                });
        }

        // Export registrations function
        function exportRegistrations() {
            const params = new URLSearchParams(window.location.search);
            const exportBtn = document.querySelector('[onclick="exportRegistrations()"]');
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Exporting...';
            exportBtn.disabled = true;

            const exportUrl = '/admin/registrations/export?' + params.toString();

            const link = document.createElement('a');
            link.href = exportUrl;
            link.download = `registrations_${new Date().toISOString().split('T')[0]}.xlsx`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            setTimeout(() => {
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
                showAlert('success', 'Export started successfully');
            }, 2000);
        }

        // Date filter functions
        function setDateRangeModal(preset) {
            const now = new Date();
            let fromDate, toDate;

            switch (preset) {
                case 'today':
                    fromDate = toDate = now.toISOString().split('T')[0];
                    break;
                case 'week':
                    const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
                    fromDate = startOfWeek.toISOString().split('T')[0];
                    toDate = new Date().toISOString().split('T')[0];
                    break;
                case 'month':
                    fromDate = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
                    toDate = new Date().toISOString().split('T')[0];
                    break;
                case 'year':
                    fromDate = new Date(now.getFullYear(), 0, 1).toISOString().split('T')[0];
                    toDate = new Date().toISOString().split('T')[0];
                    break;
            }

            document.getElementById('modal_date_from').value = fromDate;
            document.getElementById('modal_date_to').value = toDate;

            applyCustomDateRange();
        }

        function applyCustomDateRange() {
            const fromDate = document.getElementById('modal_date_from').value;
            const toDate = document.getElementById('modal_date_to').value;

            if (fromDate && toDate && fromDate > toDate) {
                showAlert('error', 'From date cannot be later than to date');
                return;
            }

            document.getElementById('date_from').value = fromDate;
            document.getElementById('date_to').value = toDate;

            const modal = bootstrap.Modal.getInstance(document.getElementById('dateFilterModal'));
            modal.hide();

            document.getElementById('filterForm').submit();
        }

        function clearDateRangeModal() {
            document.getElementById('modal_date_from').value = '';
            document.getElementById('modal_date_to').value = '';
            document.getElementById('date_from').value = '';
            document.getElementById('date_to').value = '';

            const modal = bootstrap.Modal.getInstance(document.getElementById('dateFilterModal'));
            modal.hide();

            document.getElementById('filterForm').submit();
        }

        // Function to check for changes and provide visual feedback
        function checkForChanges() {
            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');

            if (!statusSelect.dataset.originalStatus) return;

            const statusChanged = statusSelect.value !== statusSelect.dataset.originalStatus;
            const remarksChanged = remarksTextarea.value.trim() !== (remarksTextarea.dataset.originalRemarks || '').trim();

            statusSelect.classList.toggle('form-changed', statusChanged);
            statusSelect.parentElement.classList.toggle('changed', statusChanged);

            remarksTextarea.classList.toggle('form-changed', remarksChanged);
            remarksTextarea.parentElement.classList.toggle('changed', remarksChanged);

            const updateButton = document.querySelector('#updateModal .btn-primary');
            updateButton.classList.toggle('no-changes', !statusChanged && !remarksChanged);

            if (!statusChanged && !remarksChanged) {
                updateButton.innerHTML = '<i class="fas fa-edit me-1"></i>No Changes';
            } else {
                updateButton.innerHTML = '<i class="fas fa-save me-1"></i>Update Status';
            }
        }

        // Helper functions for loading states
        function showLoadingInModal(containerId) {
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = `
            <div class="text-center py-5">
                <div class="loading-spinner"></div>
                <p class="mt-3 text-muted">Loading information...</p>
            </div>`;
            }
        }

        function showErrorInModal(containerId, title, message) {
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = `
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-3 fa-2x"></i>
                    <div>
                        <h5 class="mb-1">${title}</h5>
                        <p class="mb-0">${message}</p>
                        <small class="text-muted">Please check the console for more details.</small>
                    </div>
                </div>
            </div>`;
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

            // Initialize tooltips
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // ESC to close modals
                if (e.key === 'Escape') {
                    const modals = document.querySelectorAll('.modal.show');
                    modals.forEach(modal => {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) bsModal.hide();
                    });
                }

                // Ctrl+F to focus search
                if (e.ctrlKey && e.key === 'f') {
                    e.preventDefault();
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
            });
        });

        console.log('Enhanced Admin User Management JavaScript with document viewing loaded successfully');
    </script>
@endsection
