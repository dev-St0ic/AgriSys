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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h6>
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
                            <option value="unverified" {{ request('verification_status') == 'unverified' ? 'selected' : '' }}>
                                Email Unverified
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search name, email, phone..." value="{{ request('search') }}"
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
                <button type="button" class="btn btn-success btn-sm" onclick="showBulkActions()">
                    <i class="fas fa-tasks me-2"></i>Bulk Actions
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th width="30"><input type="checkbox" class="form-check-input" id="selectAllTable"></th>
                            <th>Registration Date</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>User Type</th>
                            <th>Status</th>
                            <th>Email Verified</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                        <tr data-id="{{ $registration->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input row-select" 
                                       value="{{ $registration->id }}">
                            </td>
                            <td>{{ $registration->created_at->format('M d, Y g:i A') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar rounded-circle me-2 d-flex align-items-center justify-content-center text-white font-weight-bold" 
                                         style="width: 35px; height: 35px; 
                                         background-color: {{ $registration->user_type === 'farmer' ? '#28a745' : ($registration->user_type === 'fisherfolk' ? '#17a2b8' : '#6c757d') }}">
                                        {{ strtoupper(substr($registration->first_name ?? '', 0, 1) . substr($registration->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $registration->full_name }}</div>
                                        <small class="text-muted">{{ $registration->user_type_display ?? ucfirst($registration->user_type) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:{{ $registration->email }}" class="text-decoration-none">
                                    {{ $registration->email }}
                                </a>
                            </td>
                            <td>
                                @if($registration->phone)
                                    <a href="tel:{{ $registration->phone }}" class="text-decoration-none">
                                        {{ $registration->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge fs-6
                                    @if($registration->user_type === 'farmer') bg-success 
                                    @elseif($registration->user_type === 'fisherfolk') bg-info 
                                    @else bg-secondary @endif">
                                    {{ ucfirst($registration->user_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $registration->status === 'pending' ? 'warning' : ($registration->status === 'approved' ? 'success' : 'danger') }} fs-6">
                                    {{ ucfirst($registration->status) }}
                                </span>
                            </td>
                            <td>
                                @if($registration->hasVerifiedEmail())
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
                                @if($registration->address)
                                    <span title="{{ $registration->address }}">
                                        {{ Str::limit($registration->address, 30) }}
                                    </span>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
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
                                    <p class="mb-1"><strong>Name:</strong> <span id="updateRegName"></span></p>
                                    <p class="mb-1"><strong>Email:</strong> <span id="updateRegEmail"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>User Type:</strong> <span id="updateRegType"></span></p>
                                    <p class="mb-1"><strong>Phone:</strong> <span id="updateRegPhone"></span></p>
                                    <p class="mb-1"><strong>Current Status:</strong> <span id="updateRegCurrentStatus"></span></p>
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
                    <button type="button" class="btn btn-primary" onclick="updateRegistrationStatus()">Update Status</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Details Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>Registration Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="registrationDetails">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Modal -->
    <div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-tasks me-2"></i>Bulk Actions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Select an action to perform on <span id="selectedCount">0</span> selected registrations:</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" onclick="bulkApprove()">
                            <i class="fas fa-check me-2"></i>Approve Selected
                        </button>
                        <button type="button" class="btn btn-danger" onclick="bulkReject()">
                            <i class="fas fa-times me-2"></i>Reject Selected
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash me-2"></i>Delete Selected
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter Modal -->
    <div class="modal fade" id="dateFilterModal" tabindex="-1" aria-labelledby="dateFilterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="dateFilterModalLabel">
                        <i class="fas fa-calendar-alt me-2"></i>Select Date Range
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                        <input type="date" id="modal_date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal_date_to" class="form-label">To Date</label>
                                        <input type="date" id="modal_date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                    <button type="button" class="btn btn-primary w-100" onclick="applyCustomDateRange()">
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
                                        <button type="button" class="btn btn-outline-success" onclick="setDateRangeModal('today')">
                                            <i class="fas fa-calendar-day me-2"></i>Today
                                        </button>
                                        <button type="button" class="btn btn-outline-info" onclick="setDateRangeModal('week')">
                                            <i class="fas fa-calendar-week me-2"></i>This Week
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" onclick="setDateRangeModal('month')">
                                            <i class="fas fa-calendar me-2"></i>This Month
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="setDateRangeModal('year')">
                                            <i class="fas fa-calendar-alt me-2"></i>This Year
                                        </button>
                                        <hr class="my-3">
                                        <button type="button" class="btn btn-outline-secondary w-100" onclick="clearDateRangeModal()">
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
    </style>
@endsection

@section('scripts')
    <script>
        // Add this at the top of your scripts section
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

        // Initialize select all functionality
        function initializeSelectAll() {
            $('#selectAllTable').change(function() {
                const isChecked = $(this).is(':checked');
                $('.row-select').prop('checked', isChecked);
                updateSelectedCount();
            });
            
            $('.row-select').change(function() {
                updateSelectAllState();
                updateSelectedCount();
            });
        }

        function updateSelectAllState() {
            const totalCheckboxes = $('.row-select').length;
            const checkedCheckboxes = $('.row-select:checked').length;
            
            $('#selectAllTable').prop('checked', totalCheckboxes === checkedCheckboxes);
        }

        function updateSelectedCount() {
            const count = $('.row-select:checked').length;
            $('#selectedCount').text(count);
        }

        // Helper function to get status display text
        function getStatusText(status) {
            switch (status) {
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

        // Show update modal function
        function showUpdateModal(id, currentStatus) {
            // Show loading state in modal
            document.getElementById('updateRegId').innerHTML = `
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>`;

            // First fetch the registration details
            fetch(`/admin/registrations/${id}/details`)
                .then(response => response.json())
                .then(response => {
                    if (!response.success) {
                        throw new Error('Failed to load registration details');
                    }

                    const data = response.data;

                    // Populate the hidden field
                    document.getElementById('updateRegistrationId').value = id;

                    // Populate registration info display
                    document.getElementById('updateRegId').textContent = data.id;
                    document.getElementById('updateRegName').textContent = data.full_name;
                    document.getElementById('updateRegEmail').textContent = data.email;
                    document.getElementById('updateRegType').textContent = data.user_type;
                    document.getElementById('updateRegPhone').textContent = data.phone || 'Not provided';

                    // Show current status with badge styling
                    const currentStatusElement = document.getElementById('updateRegCurrentStatus');
                    const statusColor = data.status === 'pending' ? 'warning' : (data.status === 'approved' ? 'success' : 'danger');
                    currentStatusElement.innerHTML = `
                    <span class="badge bg-${statusColor}">${data.status}</span>`;

                    // Set form values and store original values for comparison
                    const statusSelect = document.getElementById('newStatus');
                    const remarksTextarea = document.getElementById('remarks');

                    statusSelect.value = data.status;
                    statusSelect.dataset.originalStatus = data.status;

                    remarksTextarea.value = data.rejection_reason || '';
                    remarksTextarea.dataset.originalRemarks = data.rejection_reason || '';

                    // Remove any previous change indicators
                    statusSelect.classList.remove('form-changed');
                    remarksTextarea.classList.remove('form-changed');
                    statusSelect.parentElement.classList.remove('change-indicator', 'changed');
                    remarksTextarea.parentElement.classList.remove('change-indicator', 'changed');

                    // Add change indicator classes
                    statusSelect.parentElement.classList.add('change-indicator');
                    remarksTextarea.parentElement.classList.add('change-indicator');

                    // Reset update button state
                    const updateButton = document.querySelector('#updateModal .btn-primary');
                    updateButton.classList.remove('no-changes');

                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('updateModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading registration details: ' + error.message);
                });
        }

        // Update registration status function
        function updateRegistrationStatus() {
            const id = document.getElementById('updateRegistrationId').value;
            const newStatus = document.getElementById('newStatus').value;
            const remarks = document.getElementById('remarks').value;

            if (!newStatus) {
                alert('Please select a status');
                return;
            }

            // Get the original values to compare changes
            const originalStatus = document.getElementById('newStatus').dataset.originalStatus;
            const originalRemarks = document.getElementById('remarks').dataset.originalRemarks || '';

            // Check if nothing has changed
            if (newStatus === originalStatus && remarks.trim() === originalRemarks.trim()) {
                alert('No changes detected. Please modify the status or remarks before updating.');
                return;
            }

            // Show confirmation dialog with changes summary
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

            const confirmMessage = `Are you sure you want to update this registration with the following changes?\n\n${changesSummary.join('\n')}`;

            if (!confirm(confirmMessage)) {
                return;
            }

            // Show loading state
            const updateButton = document.querySelector('#updateModal .btn-primary');
            const originalText = updateButton.innerHTML;
            updateButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
            updateButton.disabled = true;

            // Determine the endpoint based on the new status
            let endpoint = '';
            let requestData = {};

            if (newStatus === 'approved') {
                endpoint = `/admin/registrations/${id}/approve`;
            } else if (newStatus === 'rejected') {
                endpoint = `/admin/registrations/${id}/reject`;
                requestData = {
                    reason: remarks,
                    send_notification: true
                };
            } else {
                // For pending status or other updates, use a generic update endpoint if available
                endpoint = `/admin/registrations/${id}/update-status`;
                requestData = {
                    status: newStatus,
                    remarks: remarks
                };
            }

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                    // Show success message and reload page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                    modal.hide();
                    showAlert('success', response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(response.message || 'Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Error updating registration status: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                updateButton.innerHTML = originalText;
                updateButton.disabled = false;
            });
        }

        // View registration details
        function viewRegistration(id) {
            // Show loading state
            document.getElementById('registrationDetails').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`;

            // Show modal while loading
            const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
            modal.show();

            // Fetch registration details
            fetch(`/admin/registrations/${id}/details`)
                .then(response => response.json())
                .then(response => {
                    if (!response.success) {
                        throw new Error('Failed to load registration details');
                    }

                    const data = response.data;

                    // Format the details HTML
                    const remarksHtml = data.rejection_reason ? `
                    <div class="col-12 mt-3">
                        <h6 class="border-bottom pb-2">Admin Remarks</h6>
                        <div class="alert alert-info">
                            <p class="mb-1">${data.rejection_reason}</p>
                            <small class="text-muted">
                                ${data.approved_at || data.rejected_at ? `Updated on ${data.approved_at || data.rejected_at}` : ''}
                                ${data.approved_by ? ` by ${data.approved_by}` : ''}
                            </small>
                        </div>
                    </div>` : '';

                    // Update modal content
                    document.getElementById('registrationDetails').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Personal Information</h6>
                            <p><strong>Full Name:</strong> ${data.full_name}</p>
                            <p><strong>Email:</strong> ${data.email}</p>
                            <p><strong>Phone:</strong> ${data.phone || 'Not provided'}</p>
                            <p><strong>Date of Birth:</strong> ${data.date_of_birth || 'Not provided'}</p>
                            <p><strong>Gender:</strong> ${data.gender || 'Not specified'}</p>
                            <p><strong>Address:</strong> ${data.address || 'Not provided'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Registration Information</h6>
                            <p><strong>User Type:</strong> ${data.user_type}</p>
                            <p><strong>Occupation:</strong> ${data.occupation || 'Not specified'}</p>
                            <p><strong>Organization:</strong> ${data.organization || 'Not specified'}</p>
                            <p><strong>Current Status:</strong>
                                <span class="badge bg-${data.status === 'pending' ? 'warning' : (data.status === 'approved' ? 'success' : 'danger')}">${data.status}</span>
                            </p>
                            <p><strong>Email Verified:</strong> ${data.email_verified ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}</p>
                            <p><strong>Registration Date:</strong> ${data.created_at}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Emergency Contact</h6>
                            <p><strong>Contact Name:</strong> ${data.emergency_contact_name || 'Not provided'}</p>
                            <p><strong>Contact Phone:</strong> ${data.emergency_contact_phone || 'Not provided'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Technical Information</h6>
                            <p><strong>Registration IP:</strong> <code>${data.registration_ip || 'N/A'}</code></p>
                            <p><strong>Referral Source:</strong> ${data.referral_source || 'Direct'}</p>
                        </div>
                        ${remarksHtml}
                    </div>`;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('registrationDetails').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${error.message || 'Error loading registration details. Please try again.'}
                    </div>`;
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    // Remove the row from table
                    $(`tr[data-id="${id}"]`).fadeOut(300, function() {
                        $(this).remove();
                    });
                    // Refresh stats
                    refreshStats();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while deleting the registration.');
            });
        }

        // Bulk actions
        function showBulkActions() {
            const selectedCount = $('.row-select:checked').length;
            
            if (selectedCount === 0) {
                showAlert('warning', 'Please select at least one registration.');
                return;
            }
            
            updateSelectedCount();
            $('#bulkActionsModal').modal('show');
        }

        function bulkApprove() {
            const selectedIds = $('.row-select:checked').map(function() {
                return this.value;
            }).get();
            
            if (selectedIds.length === 0) {
                showAlert('warning', 'No registrations selected.');
                return;
            }
            
            if (!confirm(`Are you sure you want to approve ${selectedIds.length} selected registrations?`)) {
                return;
            }
            
            fetch('/admin/registrations/bulk/approve', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    registration_ids: selectedIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    $('#bulkActionsModal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred during bulk approval.');
            });
        }

        function bulkReject() {
            const selectedIds = $('.row-select:checked').map(function() {
                return this.value;
            }).get();
            
            if (selectedIds.length === 0) {
                showAlert('warning', 'No registrations selected.');
                return;
            }
            
            const reason = prompt(`Enter rejection reason for ${selectedIds.length} selected registrations (optional):`);
            
            if (reason === null) {
                return; // User cancelled
            }
            
            if (!confirm(`Are you sure you want to reject ${selectedIds.length} selected registrations?`)) {
                return;
            }
            
            fetch('/admin/registrations/bulk/reject', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    registration_ids: selectedIds,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    $('#bulkActionsModal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred during bulk rejection.');
            });
        }

        function bulkDelete() {
            const selectedIds = $('.row-select:checked').map(function() {
                return this.value;
            }).get();
            
            if (selectedIds.length === 0) {
                showAlert('warning', 'No registrations selected.');
                return;
            }
            
            if (!confirm(`Are you sure you want to delete ${selectedIds.length} selected registrations? This action cannot be undone.`)) {
                return;
            }
            
            fetch('/admin/registrations/bulk/delete', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    registration_ids: selectedIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    $('#bulkActionsModal').modal('hide');
                    // Remove selected rows
                    selectedIds.forEach(id => {
                        $(`tr[data-id="${id}"]`).fadeOut(300, function() {
                            $(this).remove();
                        });
                    });
                    setTimeout(refreshStats, 500);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred during bulk deletion.');
            });
        }

        // Export registrations
        function exportRegistrations() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', '1');
            
            const exportUrl = `${window.location.pathname}?${params.toString()}`;
            window.open(exportUrl, '_blank');
        }

        // Refresh statistics
        function refreshStats() {
            fetch('/admin/registrations/statistics')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stats = data.data;
                        $('#total-count').text(stats.total);
                        $('#pending-count').text(stats.pending);
                        $('#approved-count').text(stats.approved);
                        $('#rejected-count').text(stats.rejected);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing stats:', error);
                });
        }

        // Show alert messages
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Remove existing alerts
            $('.alert').remove();
            
            // Add new alert at the top of the container
            $('.container-fluid').prepend(alertHtml);
            
            // Auto-hide success alerts after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    $('.alert-success').fadeOut();
                }, 5000);
            }
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
                    startDate.setDate(today.getDate() - today.getDay());
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6);
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    endDate = new Date(today.getFullYear(), 11, 31);
                    break;
            }

            const startDateStr = startDate.toISOString().split('T')[0];
            const endDateStr = endDate.toISOString().split('T')[0];

            document.getElementById('modal_date_from').value = startDateStr;
            document.getElementById('modal_date_to').value = endDateStr;

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
            document.getElementById('date_from').value = dateFrom;
            document.getElementById('date_to').value = dateTo;

            updateDateFilterStatus(dateFrom, dateTo);

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

        // Add event listeners when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeSelectAll();

            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');

            if (statusSelect) {
                statusSelect.addEventListener('change', checkForChanges);
            }

            if (remarksTextarea) {
                remarksTextarea.addEventListener('input', checkForChanges);
            }
        });
    </script>
@endsection
