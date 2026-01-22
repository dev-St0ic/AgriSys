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
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <div class="stat-number mb-2" id="total-count">{{ $stats['total'] ?? 0 }}</div>
                    <div class="stat-label text-primary">Total Registrations</div>
                </div>
            </div>
        </div>

         <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="stat-number mb-2" id="approved-count">{{ $stats['approved'] ?? 0 }}</div>
                    <div class="stat-label text-success">Approved</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-user-clock text-warning"></i>
                    </div>
                    <div class="stat-number mb-2" id="unverified-count">{{ $stats['unverified'] ?? 0 }}</div>
                    <div class="stat-label text-warning">Unverified <br>(Basic Signup)</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-clock text-info"></i>
                    </div>
                    <div class="stat-number mb-2" id="pending-count">{{ $stats['pending'] ?? 0 }}</div>
                    <div class="stat-label text-info">Pending Review</div>
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
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.registrations.index') }}" id="filterForm">
                <!-- Hidden date inputs -->
                <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                <div class="row g-2">
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
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search username, name..." value="{{ request('search') }}"
                                oninput="autoSearch()" id="searchInput">
                            <button class="btn btn-outline-secondary" type="submit" title="Search"
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
                        <a href="{{ route('admin.registrations.index') }}" class="btn btn-secondary btn-sm w-100">
                            <i></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Registrations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div></div>
            <div class="text-center flex-fill">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-edit me-2"></i>User Registration Records
                </h6>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="showAddUserModal()">
                    <i class="fas fa-user-plus me-2"></i>Add User
                </button>
                <a href="{{ route('admin.registrations.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Registration Date</th>
                            <th class="text-center">Username</th>
                            <th class="text-center">Full Name</th>
                            <th class="text-center">Sector</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Documents</th>
                            <th class="text-center">Actions</th>
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
                                    @php
                                        $docs = [];
                                        if ($registration->location_document_path) {
                                            $docs[] = [
                                                'type' => 'location',
                                                'icon' => 'fas fa-map-marker-alt',
                                                'color' => 'primary',
                                                'name' => 'Location',
                                            ];
                                        }
                                        if ($registration->id_front_path) {
                                            $docs[] = [
                                                'type' => 'id_front',
                                                'icon' => 'fas fa-id-card',
                                                'color' => 'success',
                                                'name' => 'ID Front',
                                            ];
                                        }
                                        if ($registration->id_back_path) {
                                            $docs[] = [
                                                'type' => 'id_back',
                                                'icon' => 'fas fa-id-card-alt',
                                                'color' => 'info',
                                                'name' => 'ID Back',
                                            ];
                                        }
                                    @endphp
                                    <div id="documents-cell-{{ $registration->id }}" class="fishr-table-documents">
                                        @if (count($docs) > 0)
                                            <div class="fishr-document-previews">
                                                @foreach ($docs as $index => $doc)
                                                    @if ($index < 2)
                                                        <div class="fishr-mini-doc"
                                                            onclick="viewDocumentDirect({{ $registration->id }}, '{{ $doc['type'] }}')"
                                                            title="View {{ $doc['name'] }}">
                                                            <div class="fishr-mini-doc-icon">
                                                                <i
                                                                    class="{{ $doc['icon'] }} text-{{ $doc['color'] }}"></i>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                @if (count($docs) > 2)
                                                    <div class="fishr-mini-doc fishr-mini-doc-more"
                                                        onclick="viewDocuments({{ $registration->id }})"
                                                        title="View all {{ count($docs) }} documents">
                                                        <div class="fishr-mini-doc-icon">
                                                            <span class="fishr-more-count">+{{ count($docs) - 2 }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="fishr-document-summary"
                                                onclick="viewDocuments({{ $registration->id }})">
                                                <small class="text-muted">{{ count($docs) }}
                                                    document{{ count($docs) > 1 ? 's' : '' }}</small>
                                            </div>
                                        @else
                                            <div class="fishr-no-documents">
                                                <i class="fas fa-folder-open text-muted"></i>
                                                <small class="text-muted">No documents</small>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="viewRegistration({{ $registration->id }})" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                        <button class="btn btn-sm btn-outline-dark"
                                            onclick="showUpdateModal({{ $registration->id }}, '{{ $registration->status }}')"
                                            title="Update Status">
                                            <i class="fas fa-sync"></i> Change Status
                                        </button>

                                        <div class="btn-group" role="group">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                        onclick="showEditUserModal({{ $registration->id }})">
                                                        <i class="fas fa-edit me-2" style="color: #28a745;"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                        onclick="deleteRegistration({{ $registration->id }})">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
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

    <!-- Add User Modal - ENHANCED UI (Consistent with Event Modal) -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Add New User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <!-- Account Credentials -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-lock me-2"></i>Account Credentials
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="add_username" class="form-label fw-semibold">
                                            Username 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="add_username" required
                                            pattern="^[a-zA-Z0-9_]{3,50}$" minlength="3" maxlength="50"
                                            placeholder="3-50 characters">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Letters, numbers, and underscores only
                                        </small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="add_user_type" class="form-label fw-semibold">
                                            Sector 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_user_type" required>
                                            <option value="" disabled selected>Select sector</option>
                                            <option value="farmer">Farmer</option>
                                            <option value="fisherfolk">Fisherfolk</option>
                                            <option value="general">General Public</option>
                                            <option value="agri-entrepreneur">Agricultural Entrepreneur</option>
                                            <option value="cooperative-member">Cooperative Member</option>
                                            <option value="government-employee">Government Employee</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="add_sex" class="form-label fw-semibold">
                                            Sex 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_sex" name="sex" required>
                                            <option value="">Select Sex</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="add_password" class="form-label fw-semibold">
                                            Password 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="add_password" required
                                                minlength="8" placeholder="Min 8 characters">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="toggleAddPasswordVisibility('add_password')">
                                                <i class="fas fa-eye" id="add_password_icon"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Uppercase, lowercase, number, and special character required
                                        </small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="add_password_confirmation" class="form-label fw-semibold">
                                            Confirm Password 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="add_password_confirmation"
                                                required placeholder="Re-enter password">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="toggleAddPasswordVisibility('add_password_confirmation')">
                                                <i class="fas fa-eye" id="add_password_confirmation_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-id-card me-2"></i>Personal Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="add_first_name" class="form-label fw-semibold">
                                            First Name 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="add_first_name" required
                                            maxlength="100" placeholder="First name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="add_middle_name" class="form-label fw-semibold">
                                            Middle Name
                                        </label>
                                        <input type="text" class="form-control" id="add_middle_name" maxlength="100"
                                            placeholder="Middle name (optional)">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="add_last_name" class="form-label fw-semibold">
                                            Last Name 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="add_last_name" required
                                            maxlength="100" placeholder="Last name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="add_name_extension" class="form-label fw-semibold">
                                            Extension
                                        </label>
                                        <select class="form-select" id="add_name_extension" name="name_extension">
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
                                    <div class="col-md-4 mb-3">
                                        <label for="add_date_of_birth" class="form-label fw-semibold">
                                            Date of Birth 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="add_date_of_birth" required>
                                        <small class="text-muted d-block mt-2">Must be at least 18 years old</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="add_contact_number" class="form-label fw-semibold">
                                            Contact Number 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="add_contact_number" required
                                            placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <small class="text-muted d-block mt-2">09XXXXXXXXX or +639XXXXXXXXX</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="add_barangay" class="form-label fw-semibold">
                                            Barangay 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_barangay" required>
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
                                    <div class="col-md-6 mb-3">
                                        <label for="add_complete_address" class="form-label fw-semibold">
                                            Complete Address 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="add_complete_address" required rows="3"
                                            maxlength="500" placeholder="Street address, building, etc."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="add_emergency_contact_name" class="form-label fw-semibold">
                                            Contact Name 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="add_emergency_contact_name"
                                            required maxlength="100" placeholder="Full name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="add_emergency_contact_phone" class="form-label fw-semibold">
                                            Contact Phone 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="add_emergency_contact_phone"
                                            required placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$"
                                            maxlength="20">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Document Uploads (REQUIRED) -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-file-upload me-2"></i>Documents
                                    <span class="badge bg-danger text-white ms-2">REQUIRED</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-4">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Upload documents to associate with this user. Supported formats: JPG, PNG (Max 5MB each)
                                </p>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="add_id_front" class="form-label fw-semibold">
                                            Government ID - Front 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="add_id_front" accept="image/*"
                                            required onchange="previewAddDocument('add_id_front', 'add_id_front_preview')">
                                        <div id="add_id_front_preview" style="margin-top: 10px;"></div>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="add_id_back" class="form-label fw-semibold">
                                            Government ID - Back 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="add_id_back" accept="image/*"
                                            required onchange="previewAddDocument('add_id_back', 'add_id_back_preview')">
                                        <div id="add_id_back_preview" style="margin-top: 10px;"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="add_location_proof" class="form-label fw-semibold">
                                            Location/Role Proof 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="add_location_proof"
                                            accept="image/*" required
                                            onchange="previewAddDocument('add_location_proof', 'add_location_proof_preview')">
                                        <div id="add_location_proof_preview" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-toggle-on me-2"></i>Account Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="add_status" class="form-label fw-semibold">
                                            Initial Status 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_status" required>
                                            <option value="unverified">Unverified (Basic Signup)</option>
                                            <option value="pending">Pending Review</option>
                                            <option value="approved">Approved</option>
                                        </select>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Choose the initial verification status
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="create_submit_btn_user" onclick="submitAddUser()">
                        <span class="btn-text">
                            <i class="fas fa-save me-1"></i>Create User
                        </span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner-border spinner-border-sm me-2"></span>Creating...
                        </span>
                    </button>
                </div>
            </div>
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
                <!-- close button -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Registration Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-pencil-alt me-2"></i>
                        Edit Registration - <span id="editAppUsername"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" enctype="multipart/form-data">
                        <!-- Personal Information Card -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="edit_first_name" class="form-label">First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_first_name"
                                            name="first_name" required maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="edit_middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="edit_middle_name"
                                            name="middle_name" maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="edit_last_name" class="form-label">Last Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_last_name" name="last_name"
                                            required maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="edit_name_extension" class="form-label">Extension</label>
                                        <select class="form-select" id="edit_name_extension" name="name_extension">
                                            <option value="">None</option>
                                            <option value="Jr.">Jr.</option>
                                            <option value="Sr.">Sr.</option>
                                            <option value="II">II</option>
                                            <option value="III">III</option>
                                            <option value="IV">IV</option>
                                            <option value="V">V</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="edit_sex" class="form-label">Sex</label>
                                        <select class="form-select" id="edit_sex" name="sex">
                                            <option value="">Not Specified</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="edit_contact_number" class="form-label">Contact Number <span
                                                class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="edit_contact_number"
                                            name="contact_number" required placeholder="09XXXXXXXXX"
                                            pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <div class="form-text">09XXXXXXXXX or +639XXXXXXXXX</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="edit_user_type" class="form-label">User Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_user_type" name="user_type" required>
                                            <option value="" disabled selected>Select sector</option>
                                            <option value="farmer">Farmer</option>
                                            <option value="fisherfolk">Fisherfolk</option>
                                            <option value="general">General Public</option>
                                            <option value="agri-entrepreneur">Agricultural Entrepreneur</option>
                                            <option value="cooperative-member">Cooperative Member</option>
                                            <option value="government-employee">Government Employee</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Card -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_barangay" class="form-label">Barangay <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_barangay" name="barangay" required>
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
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_complete_address" class="form-label">Complete Address</label>
                                        <textarea class="form-control" id="edit_complete_address" name="complete_address" rows="3" maxlength="500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Card -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_emergency_contact_name" class="form-label">Emergency Contact Name
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="edit_emergency_contact_name"
                                            name="emergency_contact_name" required maxlength="100">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_emergency_contact_phone" class="form-label">Emergency Contact
                                            Phone <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="edit_emergency_contact_phone"
                                            name="emergency_contact_phone" required placeholder="09XXXXXXXXX"
                                            pattern="^(\+639|09)\d{9}$" maxlength="20">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Status (Read-only) -->
                        <div class="card mb-3 bg-light">
                            <div class="card-header bg-light border-0">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Account Status (Read-only)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted">Current Status:</small>
                                        <div>
                                            <span id="edit_status_badge" class="badge bg-secondary"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted">Registration Date:</small>
                                        <div id="edit_created_at"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Note:</strong> You can edit personal information, contact details, and location
                            information.
                            To change registration status, use the "Update" button from the main table.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="editUserSubmitBtn"
                        onclick="handleEditUserSubmit()">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Document Viewer Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="documentModalTitle">
                        <i class="fas fa-file-image me-2"></i>Document View
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="documentModalBody">
                    <div id="documentViewerLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading documents...</p>
                    </div>
                    <div id="documentViewer" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
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
                <div class="modal-header bg-info text-white">
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
            color: #495057;
            line-height: 1;
        }

        .stat-label {
            font-size: 1rem;
            font-weight: 500;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        /* Toast Header (for confirmation toasts) */
        .toast-notification .toast-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            font-weight: 600;
        }

        .toast-notification .btn-close-toast {
            width: auto;
            height: auto;
            padding: 0;
            font-size: 1.2rem;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .toast-notification .btn-close-toast:hover {
            opacity: 1;
        }

        /* Toast Body */
        .toast-notification .toast-body {
            padding: 16px;
        }

        .toast-notification .toast-body p {
            margin: 0;
            font-size: 0.95rem;
            color: #333;
            line-height: 1.5;
        }

        /* Toast Content (for simple notifications) */
        .toast-notification .toast-content {
            display: flex;
            align-items: center;
            padding: 20px;
            font-size: 1.05rem;
        }

        .toast-notification .toast-content i {
            font-size: 1.5rem;
            /* Make icon bigger */
        }

        .toast-notification .toast-content span {
            flex: 1;
            color: #333;
        }

        /* Type-specific styles */
        .toast-notification.toast-success {
            border-left: 4px solid #28a745;
        }

        .toast-notification.toast-success .toast-content i,
        .toast-notification.toast-success .toast-header i {
            color: #28a745;
        }

        .toast-notification.toast-error {
            border-left: 4px solid #dc3545;
        }

        .toast-notification.toast-error .toast-content i,
        .toast-notification.toast-error .toast-header i {
            color: #dc3545;
        }

        .toast-notification.toast-warning {
            border-left: 4px solid #ffc107;
        }

        .toast-notification.toast-warning .toast-content i,
        .toast-notification.toast-warning .toast-header i {
            color: #ffc107;
        }

        .toast-notification.toast-info {
            border-left: 4px solid #17a2b8;
        }

        .toast-notification.toast-info .toast-content i,
        .toast-notification.toast-info .toast-header i {
            color: #17a2b8;
        }

        /* Confirmation Toast */
        .confirmation-toast {
            min-width: 420px;
            max-width: 650px;
        }

        .confirmation-toast .toast-body {
            background: #f8f9fa;
        }

        .confirmation-toast .d-flex.gap-2 button {
            pointer-events: auto;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
            }

            .toast-notification {
                min-width: auto;
                max-width: 100%;
            }

            .confirmation-toast {
                min-width: auto;
                max-width: 100%;
            }
        }

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

        /* Center document image */
        #documentModal .document-image {
            max-width: 90%;
            max-height: 55vh;
            object-fit: contain;
            margin: 0 auto;
            display: block;
            transition: transform 0.3s ease;
        }


        /* Fix for lingering modal backdrop */
        .modal-backdrop {
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }

        .modal-backdrop.fade {
            opacity: 0;
        }

        .modal-backdrop.show {
            opacity: 0.5;
        }

        /* Ensure body scrolling is restored */
        body {
            overflow: auto !important;
            padding-right: 0 !important;
        }

        body.modal-open {
            overflow: hidden;
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

        /* Document Thumbnail Gallery Styles - FISHR STYLE */
        .document-thumbnail-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
            border: 2px solid transparent;
            height: 100%;
        }

        .document-thumbnail-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }

        .document-thumbnail-container {
            position: relative;
            height: 200px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .document-thumbnail-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .document-thumbnail-card:hover .document-thumbnail-image {
            transform: scale(1.05);
        }

        .document-thumbnail-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .document-thumbnail-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: #f8f9fa;
        }

        .document-thumbnail-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 123, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .document-thumbnail-card:hover .document-thumbnail-overlay {
            opacity: 1;
        }

        .document-thumbnail-info {
            padding: 1rem;
            text-align: center;
            background: white;
        }

        .document-thumbnail-title {
            margin: 0 0 0.25rem 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #343a40;
            line-height: 1.3;
        }

        .document-thumbnail-info small {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Document status indicators */
        .document-status-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 10;
        }

        /* Responsive grid for document thumbnails */
        @media (max-width: 768px) {
            .document-thumbnail-container {
                height: 150px;
            }

            .document-thumbnail-info {
                padding: 0.75rem;
            }

            .document-thumbnail-title {
                font-size: 0.9rem;
            }
        }

        /* Animation for document grid loading */
        .document-thumbnail-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .document-thumbnail-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .document-thumbnail-card:nth-child(3) {
            animation-delay: 0.2s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* FISHR-Style Document Viewer */
        .fishr-document-viewer {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            min-height: 400px;
        }

        .fishr-document-container {
            position: relative;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 100%;
            margin-bottom: 1rem;
        }

        .fishr-document-image {
            max-width: 100%;
            max-height: 60vh;
            object-fit: contain;
            display: block;
            transition: transform 0.3s ease;
        }

        .fishr-document-image:hover {
            transform: scale(1.02);
        }

        .fishr-document-image.zoomed {
            transform: scale(1.5);
            cursor: zoom-out;
        }

        .fishr-document-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .fishr-document-size-badge {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .fishr-document-actions {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .fishr-btn {
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            border: 1px solid;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 140px;
        }

        .fishr-btn-outline {
            background: white;
            color: #6c757d;
            border-color: #dee2e6;
        }

        .fishr-btn-outline:hover {
            background: #f8f9fa;
            color: #495057;
            border-color: #adb5bd;
            transform: translateY(-1px);
        }

        .fishr-btn-primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .fishr-btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
            color: white;
            transform: translateY(-1px);
        }

        .fishr-document-info {
            text-align: center;
            color: #6c757d;
        }

        .fishr-file-name {
            margin: 0;
            font-size: 0.9rem;
            word-break: break-word;
        }

        .fishr-document-placeholder {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .fishr-file-details {
            margin-top: 1rem;
            text-align: left;
        }

        .fishr-file-details p {
            margin: 0.25rem 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Responsive design for FISHR style */
        @media (max-width: 768px) {
            .fishr-document-actions {
                flex-direction: column;
                width: 100%;
            }

            .fishr-btn {
                width: 100%;
                min-width: auto;
            }

            .fishr-document-image {
                max-height: 40vh;
            }
        }

        /* FISHR-Style Table Document Previews */
        .fishr-table-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        .fishr-document-previews {
            display: flex;
            gap: 0.25rem;
            align-items: center;
        }

        .fishr-mini-doc {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: white;
            border: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .fishr-mini-doc:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }

        .fishr-mini-doc-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .fishr-mini-doc-more {
            background: #f8f9fa;
            border-color: #dee2e6;
        }

        .fishr-mini-doc-more:hover {
            background: #e9ecef;
            border-color: #6c757d;
        }

        .fishr-more-count {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
        }

        .fishr-mini-doc-more:hover .fishr-more-count {
            color: #495057;
        }

        .fishr-document-summary {
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .fishr-document-summary:hover {
            color: #007bff !important;
        }

        .fishr-document-summary:hover small {
            color: #007bff !important;
        }

        .fishr-no-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem;
            opacity: 0.7;
        }

        .fishr-no-documents i {
            font-size: 1.25rem;
        }

        /* Document type specific colors for mini previews */
        .fishr-mini-doc[title*="Location"] {
            border-color: #007bff;
        }

        .fishr-mini-doc[title*="Location"]:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .fishr-mini-doc[title*="ID Front"] {
            border-color: #28a745;
        }

        .fishr-mini-doc[title*="ID Front"]:hover {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .fishr-mini-doc[title*="ID Back"] {
            border-color: #17a2b8;
        }

        .fishr-mini-doc[title*="ID Back"]:hover {
            background-color: rgba(23, 162, 184, 0.1);
        }

        /* Responsive adjustments for table documents */
        @media (max-width: 768px) {
            .fishr-mini-doc {
                width: 28px;
                height: 28px;
            }

            .fishr-mini-doc-icon {
                font-size: 0.75rem;
            }

            .fishr-more-count {
                font-size: 0.7rem;
            }
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
        /* Enhanced modal styling */
        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
        }

        .card {
            transition: all 0.2s ease;
        }

        .card.border-0.bg-light {
            border: 1px solid #e9ecef !important;
        }

        .card.border-0.bg-light:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .card-header.bg-white {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1rem;
        }

        .form-label {
            margin-bottom: 0.5rem;
            color: #495057;
            font-weight: 500;
        }

        .form-label .text-danger {
            margin-left: 2px;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        }

        .form-control.is-valid,
        .form-select.is-valid {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
        }

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .btn-secondary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }

        /* Document preview styling */
        #add_id_front_preview,
        #add_id_back_preview,
        #add_location_proof_preview {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #add_id_front_preview img,
        #add_id_back_preview img,
        #add_location_proof_preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 0.375rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .document-preview-item {
            text-align: center;
        }

        .document-preview-item p {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #6c757d;
            word-break: break-word;
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
                        <strong>Basic Signup User:</strong> This user has only provided username, contact number, and password.
                        They need to complete their profile verification to access full services.
                    </div>
                </div>
            </div>
        </div>` : '';

            const statusBadgeColor = getStatusBadgeColor(data.status);
            const verificationStatus = data.is_verified ? 
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
                            <div class="col-12"><strong>Sex:</strong> ${data.sex || '<span class="text-muted">Not specified</span>'}</div>
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
                            <div class="col-12"><strong>Contact Number:</strong> ${data.contact_number ? `<a href="tel:${data.contact_number}" class="text-decoration-none">${data.contact_number}</a>` : '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>User Type:</strong> ${data.user_type ? `<span class="badge bg-secondary">${data.user_type}</span>` : '<span class="badge bg-warning">Not selected yet</span>'}</div>
                            <div class="col-12"><strong>Current Status:</strong> <span class="badge bg-${statusBadgeColor}">${getStatusText(data.status)}</span></div>
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
                showToast('error', 'Registration ID not found');
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
                            <div class="fishr-document-viewer">
                                <div class="fishr-document-container">
                                    <img src="${data.document_url}"
                                         alt="${titles[documentType]}"
                                         class="fishr-document-image"
                                         id="documentImage"
                                         onclick="toggleZoom()"
                                         style="cursor: zoom-in;"
                                         onerror="showImageError(this)">
                                    <div class="fishr-document-overlay">
                                        <div class="fishr-document-size-badge">
                                            ${Math.round(data.file_info.size / 1024)}KB
                                        </div>
                                    </div>
                                </div>
                                <div class="fishr-document-actions">
                                    <button class="btn fishr-btn fishr-btn-outline" onclick="openInNewTab()">
                                        <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                                    </button>
                                    <button class="btn fishr-btn fishr-btn-primary" onclick="downloadDocument()">
                                        <i class="fas fa-download me-2"></i>Download
                                    </button>
                                </div>
                                <div class="fishr-document-info">
                                    <p class="fishr-file-name">File: ${data.file_info.name}</p>
                                </div>
                            </div>`;
                            } else {
                                modalBody.innerHTML = `
                            <div class="fishr-document-viewer">
                                <div class="fishr-document-placeholder">
                                    <i class="fas fa-file fa-4x mb-3 text-primary"></i>
                                    <h5>File Preview Not Available</h5>
                                    <p>This file type cannot be displayed inline.</p>
                                    <div class="fishr-file-details">
                                        <p><strong>File:</strong> ${data.file_info.name}</p>
                                        <p><strong>Size:</strong> ${formatFileSize(data.file_info.size)}</p>
                                        <p><strong>Type:</strong> ${data.file_info.mime_type}</p>
                                    </div>
                                </div>
                                <div class="fishr-document-actions">
                                    <button class="btn fishr-btn fishr-btn-outline" onclick="openInNewTab()">
                                        <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                                    </button>
                                    <button class="btn fishr-btn fishr-btn-primary" onclick="downloadDocument()">
                                        <i class="fas fa-download me-2"></i>Download
                                    </button>
                                </div>
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
                    <div class="fishr-document-viewer">
                        <div class="fishr-document-placeholder">
                            <i class="fas fa-exclamation-triangle fa-4x mb-3 text-warning"></i>
                            <h5>Document Could Not Be Loaded</h5>
                            <p class="text-muted">${error.message}</p>
                            <small class="text-muted">Please check the console for more details.</small>
                        </div>
                        <div class="fishr-document-actions">
                            <button class="btn fishr-btn fishr-btn-outline" onclick="location.reload()">
                                <i class="fas fa-refresh me-2"></i>Retry
                            </button>
                        </div>
                    </div>`;
                    }
                    currentDocumentUrl = null;
                    currentDocumentInfo = null;
                });
        }

        // Show image error
        function showImageError(img) {
            const container = img.parentElement.parentElement;
            container.innerHTML = `
        <div class="fishr-document-placeholder">
            <i class="fas fa-image fa-4x mb-3 text-muted"></i>
            <h5>Image Could Not Be Loaded</h5>
            <p>The image file may be corrupted or in an unsupported format.</p>
        </div>
        <div class="fishr-document-actions">
            <button class="btn fishr-btn fishr-btn-outline" onclick="openInNewTab()">
                <i class="fas fa-external-link-alt me-2"></i>Try Opening in New Tab
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

        // Enhanced view documents function with thumbnail grid layout - FISHR STYLE
        function viewDocuments(id) {
            currentRegistrationId = id;

            const documentModal = new bootstrap.Modal(document.getElementById('documentModal'));

            // Show loading state - make sure these elements exist
            let loadingDiv = document.getElementById('documentViewerLoading');
            let viewerDiv = document.getElementById('documentViewer');

            // If they don't exist, create them
            if (!loadingDiv) {
                const modalBody = document.querySelector('#documentModal .modal-body');
                if (modalBody) {
                    modalBody.innerHTML = `
                        <div id="documentViewerLoading" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading documents...</p>
                        </div>
                        <div id="documentViewer" style="display: none;"></div>
                    `;
                    loadingDiv = document.getElementById('documentViewerLoading');
                    viewerDiv = document.getElementById('documentViewer');
                }
            }

            // Show loading, hide viewer
            if (loadingDiv) loadingDiv.style.display = 'block';
            if (viewerDiv) viewerDiv.style.display = 'none';

            // Show modal
            documentModal.show();

            // Update modal title
            const modalTitle = document.getElementById('documentModalTitle');
            if (modalTitle) {
                modalTitle.innerHTML = '<i class="fas fa-images me-2"></i>Document Gallery';
            }

            console.log('Fetching documents for registration:', id);

            // Fetch registration details to get documents
            fetch(`/admin/registrations/${id}/details`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    console.log('Registration data received:', response);

                    if (!response.success) {
                        throw new Error(response.message || 'Failed to load documents');
                    }

                    const data = response.data;

                    // Hide loading
                    if (loadingDiv) loadingDiv.style.display = 'none';
                    if (viewerDiv) viewerDiv.style.display = 'block';

                    // Build documents array with thumbnail URLs
                    const docs = [];
                    if (data.location_document_path) {
                        docs.push({
                            type: 'location',
                            name: 'Location Proof Document',
                            icon: 'fas fa-map-marker-alt',
                            color: 'primary'
                        });
                    }
                    if (data.id_front_path) {
                        docs.push({
                            type: 'id_front',
                            name: 'Government ID - Front',
                            icon: 'fas fa-id-card',
                            color: 'success'
                        });
                    }
                    if (data.id_back_path) {
                        docs.push({
                            type: 'id_back',
                            name: 'Government ID - Back',
                            icon: 'fas fa-id-card-alt',
                            color: 'info'
                        });
                    }

                    console.log('Documents found:', docs);

                    let documentsHtml = '';

                    if (docs.length > 0) {
                        documentsHtml = `
                            <div class="row g-3">
                                ${docs.map(doc => `
                                            <div class="col-md-4">
                                                <div class="document-thumbnail-card" onclick="viewDocumentDirect(${id}, '${doc.type}')">
                                                    <div class="document-thumbnail-container">
                                                        <div class="document-thumbnail-loading" id="thumb-loading-${doc.type}">
                                                            <div class="spinner-border spinner-border-sm text-${doc.color}" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                        </div>
                                                        <img class="document-thumbnail-image"
                                                             id="thumb-${doc.type}"
                                                             style="display: none;"
                                                             alt="${doc.name}"
                                                             onload="showThumbnail('${doc.type}')"
                                                             onerror="showThumbnailError('${doc.type}', '${doc.icon}', '${doc.color}')">
                                                        <div class="document-thumbnail-overlay">
                                                            <i class="fas fa-eye fa-2x text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div class="document-thumbnail-info">
                                                        <h6 class="document-thumbnail-title">${doc.name}</h6>
                                                        <small class="text-muted">Click to view full size</small>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                            </div>
                        `;
                    } else {
                        documentsHtml = `
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No Documents Available</h5>
                                <p class="text-muted">No documents have been uploaded for this registration yet.</p>
                            </div>
                        `;
                    }

                    if (viewerDiv) {
                        viewerDiv.innerHTML = documentsHtml;

                        // Load thumbnails
                        docs.forEach(doc => {
                            loadDocumentThumbnail(id, doc.type);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading documents:', error);

                    // Hide loading
                    if (loadingDiv) loadingDiv.style.display = 'none';
                    if (viewerDiv) {
                        viewerDiv.style.display = 'block';
                        viewerDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error loading documents:</strong> ${error.message}
                            </div>
                        `;
                    }
                });
        }

        // Load document thumbnail
        function loadDocumentThumbnail(registrationId, documentType) {
            fetch(`/admin/registrations/${registrationId}/document/${documentType}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Document not found');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.document_url) {
                        const thumbImg = document.getElementById(`thumb-${documentType}`);
                        if (thumbImg) {
                            thumbImg.src = data.document_url;
                        }
                    } else {
                        throw new Error('Document URL not available');
                    }
                })
                .catch(error => {
                    console.error(`Error loading thumbnail for ${documentType}:`, error);
                    showThumbnailError(documentType, 'fas fa-file', 'secondary');
                });
        }

        // Show thumbnail when loaded
        function showThumbnail(documentType) {
            const loading = document.getElementById(`thumb-loading-${documentType}`);
            const img = document.getElementById(`thumb-${documentType}`);

            if (loading) loading.style.display = 'none';
            if (img) img.style.display = 'block';
        }

        // Show thumbnail error state
        function showThumbnailError(documentType, icon, color) {
            const container = document.querySelector(`#thumb-${documentType}`).parentElement;
            if (container) {
                container.innerHTML = `
                    <div class="document-thumbnail-placeholder">
                        <i class="${icon} fa-3x text-${color}"></i>
                    </div>
                `;
            }
        }

        // Open current document in new tab
        function openInNewTab() {
            if (currentDocumentUrl) {
                window.open(currentDocumentUrl, '_blank');
            } else {
                showToast('error', 'Document URL not available');
            }
        }

        // Download current document
        function downloadDocument() {
            if (currentDocumentUrl && currentDocumentInfo) {
                const link = document.createElement('a');
                link.href = currentDocumentUrl;
                link.download = currentDocumentInfo.name || 'document';
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast('success', 'Download started');
            } else {
                showToast('error', 'Document not available for download');
            }
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
                        showToast('success', response.message);

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
                    showToast('error', 'Error updating registration status: ' + error.message);
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
                    showToast('error', 'Error loading registration details: ' + error.message);
                });
        }

        // enhance version with toast notif

        function updateRegistrationStatus() {
            const id = document.getElementById('updateRegistrationId').value;
            const newStatus = document.getElementById('newStatus').value;
            const remarks = document.getElementById('remarks').value;

            console.log('=== Update Status Debug ===');
            console.log('Registration ID:', id);
            console.log('New Status:', newStatus);
            console.log('Remarks:', remarks);

            if (!newStatus) {
                showToast('error', 'Please select a status');
                return;
            }

            const originalStatus = document.getElementById('newStatus').dataset.originalStatus;
            const originalRemarks = document.getElementById('remarks').dataset.originalRemarks || '';

            if (newStatus === originalStatus && remarks.trim() === originalRemarks.trim()) {
                showToast('warning', 'No changes detected. Please modify the status or remarks before updating.');
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

            // Show confirmation toast with action buttons
            showConfirmationToast(
                'Confirm Update',
                `Update this registration with the following changes?\n\n${changesSummary.join('\n')}`,
                () => proceedWithStatusUpdate(id, newStatus, remarks)
            );
        }

        // New confirmation toast function
        function showConfirmationToast(title, message, onConfirm) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const toast = document.createElement('div');
            toast.className = 'toast-notification confirmation-toast';

            // Store the callback function on the toast element
            toast.dataset.confirmCallback = Math.random().toString(36);
            window[toast.dataset.confirmCallback] = onConfirm;

            toast.innerHTML = `
                <div class="toast-header">
                    <i class="fas fa-question-circle me-2 text-info"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-toast" onclick="removeToast(this.closest('.toast-notification'))"></button>
                </div>
                <div class="toast-body">
                    <p class="mb-3" style="white-space: pre-wrap;">${message}</p>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="removeToast(this.closest('.toast-notification'))">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="confirmToastAction(this)">
                            <i class="fas fa-check me-1"></i>Confirm
                        </button>
                    </div>
                </div>
            `;

            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);

            // Auto-dismiss after 10 seconds
            setTimeout(() => {
                if (document.contains(toast)) {
                    removeToast(toast);
                }
            }, 10000);
        }

        // NEW helper function to execute confirmation action
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

            // Clean up the callback reference
            delete window[callbackId];

            removeToast(toast);
        }

        // Proceed with actual status update
        function proceedWithStatusUpdate(id, newStatus, remarks) {
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

            console.log('Sending request to:', endpoint);
            console.log('Request data:', requestData);

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
                    console.log('Response Status:', response.status);
                    const clonedResponse = response.clone();

                    return clonedResponse.json().then(jsonData => {
                        console.log('Response JSON:', jsonData);
                        return {
                            status: response.status,
                            ok: response.ok,
                            data: jsonData
                        };
                    }).catch(jsonError => {
                        console.warn('Could not parse JSON response:', jsonError);
                        return response.text().then(textData => {
                            console.log('Response Text:', textData);
                            return {
                                status: response.status,
                                ok: response.ok,
                                text: textData
                            };
                        });
                    });
                })
                .then(result => {
                    console.log('Processing result:', result);

                    if (!result.ok) {
                        let errorMessage = `Server Error (${result.status}): `;

                        if (result.data && result.data.message) {
                            errorMessage += result.data.message;
                            if (result.data.errors) {
                                console.error('Validation errors:', result.data.errors);
                                errorMessage += '\n\nValidation errors:\n' + JSON.stringify(result.data.errors, null,
                                    2);
                            }
                        } else if (result.text) {
                            errorMessage += result.text.slice(0, 200);
                        } else {
                            errorMessage += result.statusText || 'Unknown error';
                        }

                        throw new Error(errorMessage);
                    }

                    if (result.data && result.data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                        modal.hide();

                        showToast('success', result.data.message || 'Registration status updated successfully');

                        console.log('Update successful, reloading...');

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(result.data?.message || 'Unknown error occurred');
                    }
                })
                .catch(error => {
                    console.error('Complete error object:', error);
                    console.error('Error message:', error.message);
                    showToast('error', 'Error updating registration status: ' + error.message);
                })
                .finally(() => {
                    updateButton.innerHTML = originalText;
                    updateButton.disabled = false;
                });
        }

        // Toast notification function (similar to event page)
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

            // Auto-dismiss after 5 seconds for non-confirmation toasts
            setTimeout(() => {
                if (document.contains(toast)) {
                    removeToast(toast);
                }
            }, 5000);
        }

        // Create toast container if it doesn't exist
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

        // Remove toast notification
        function removeToast(toastElement) {
            toastElement.classList.remove('show');
            setTimeout(() => {
                if (toastElement.parentElement) {
                    toastElement.remove();
                }
            }, 300);
        }

        // Show add user modal
        function showAddUserModal() {
            const modal = new bootstrap.Modal(document.getElementById('addUserModal'));

            // Reset form
            document.getElementById('addUserForm').reset();

            // Remove any validation errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            modal.show();
        }

        // Toggle password visibility
        function toggleAddPasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '_icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        // ==============================================
        // ADD USER FORM VALIDATION
        // ==============================================

        /**
         * Real-time validation for username (admin)
         */
        document.getElementById('add_username')?.addEventListener('input', function() {
            validateAddUsername(this.value);
        });

        function validateAddUsername(username) {
            const input = document.getElementById('add_username');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            // Remove existing feedback
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!username || username.trim() === '') {
                return;
            }

            let errors = [];

            // Length check (3-50 characters)
            if (username.length < 3) {
                errors.push('Username must be at least 3 characters');
            }
            if (username.length > 50) {
                errors.push('Username must not exceed 50 characters');
            }

            // No spaces allowed
            if (/\s/.test(username)) {
                errors.push('Username cannot contain spaces');
            }

            // Only letters, numbers, and underscores allowed
            if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                errors.push('Username can only contain letters, numbers, and underscores');
            }

            // Cannot start with a number
            if (/^[0-9]/.test(username)) {
                errors.push('Username cannot start with a number');
            }

            if (errors.length > 0) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = errors[0];
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            // Check availability on server
            checkAddUsernameAvailability(username);
            return true;
        }

        let addUsernameCheckTimeout;

        function checkAddUsernameAvailability(username) {
            clearTimeout(addUsernameCheckTimeout);
            const input = document.getElementById('add_username');

            addUsernameCheckTimeout = setTimeout(() => {
                fetch('/auth/check-username', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify({
                            username: username
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const feedback = input.parentNode.querySelector('.invalid-feedback');
                        if (feedback) feedback.remove();

                        if (data.available) {
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                        } else {
                            input.classList.remove('is-valid');
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback d-block';
                            errorDiv.textContent = 'Username already taken';
                            input.parentNode.appendChild(errorDiv);
                        }
                    })
                    .catch(error => {
                        console.error('Error checking username:', error);
                    });
            }, 500);
        }

        /**
         * Real-time validation for password (admin)
         */
        document.getElementById('add_password')?.addEventListener('input', function() {
            const password = this.value;
            validateAddPassword(password);

            // Re-validate confirmation if it has value
            const confirmPassword = document.getElementById('add_password_confirmation').value;
            if (confirmPassword) {
                validateAddPasswordMatch(password, confirmPassword);
            }
        });

        function validateAddPassword(password) {
            const input = document.getElementById('add_password');
            const feedback = input.parentNode.parentNode.querySelector('.invalid-feedback');

            // Remove existing feedback
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!password) {
                return;
            }

            let errors = [];

            // Check for spaces
            if (/\s/.test(password)) {
                errors.push('Password cannot contain spaces');
            }

            // Check minimum length (8 characters)
            if (password.length < 8) {
                errors.push('Password must be at least 8 characters');
            }

            // Check for uppercase
            if (!/[A-Z]/.test(password)) {
                errors.push('Password must contain at least one uppercase letter');
            }

            // Check for lowercase
            if (!/[a-z]/.test(password)) {
                errors.push('Password must contain at least one lowercase letter');
            }

            // Check for number
            if (!/\d/.test(password)) {
                errors.push('Password must contain at least one number');
            }

            // Check for special character
            if (!/[@#!$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
                errors.push('Password must contain at least one special character');
            }

            if (errors.length > 0) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = errors[0];
                input.parentNode.parentNode.appendChild(errorDiv);
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }

        /**
         * Real-time validation for password confirmation (admin)
         */
        document.getElementById('add_password_confirmation')?.addEventListener('input', function() {
            const password = document.getElementById('add_password').value;
            const confirmPassword = this.value;
            validateAddPasswordMatch(password, confirmPassword);
        });

        function validateAddPasswordMatch(password, confirmPassword) {
            const input = document.getElementById('add_password_confirmation');
            const feedback = input.parentNode.parentNode.querySelector('.invalid-feedback');

            // Remove existing feedback
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!confirmPassword) {
                return;
            }

            if (password !== confirmPassword) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Passwords do not match';
                input.parentNode.parentNode.appendChild(errorDiv);
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }

        /**
         * Real-time validation for contact number (admin)
         */
        document.getElementById('add_contact_number')?.addEventListener('input', function() {
            validateAddContactNumber(this.value);
        });

        document.getElementById('add_contact_number')?.addEventListener('blur', function() {
            validateAddContactNumber(this.value);
        });

        function validateAddContactNumber(contactNumber) {
            const input = document.getElementById('add_contact_number');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            // Remove existing feedback temporarily
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!contactNumber || contactNumber.trim() === '') {
                return;
            }

            // Philippine mobile number validation (09XXXXXXXXX)
            const phoneRegex = /^09\d{9}$/;

            if (!phoneRegex.test(contactNumber.trim())) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            return true;
        }
        /**
         * Real-time validation for contact number (admin) - with live duplicate check
         */
        document.getElementById('add_contact_number')?.addEventListener('input', function() {
            validateAddContactNumber(this.value);
            // Check for duplicates on input
            if (this.value.length === 11) {
                validateAddContactNumberWithDuplicate(this.value);
            }
        });

        document.getElementById('add_contact_number')?.addEventListener('blur', function() {
            validateAddContactNumberWithDuplicate(this.value);
        });

        let contactNumberCheckTimeout;

        function validateAddContactNumber(contactNumber) {
            const input = document.getElementById('add_contact_number');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!contactNumber || contactNumber.trim() === '') {
                return;
            }

            const phoneRegex = /^09\d{9}$/;

            if (!phoneRegex.test(contactNumber.trim())) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            return true;
        }

        function validateAddContactNumberWithDuplicate(contactNumber) {
            clearTimeout(contactNumberCheckTimeout);
            const input = document.getElementById('add_contact_number');
            
            if (!contactNumber || contactNumber.trim() === '') {
                return;
            }

            const phoneRegex = /^09\d{9}$/;
            
            if (!phoneRegex.test(contactNumber.trim())) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                return;
            }

            // Check for duplicates on server with shorter delay for real-time feel
            contactNumberCheckTimeout = setTimeout(() => {
                fetch('/check-contact', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify({
                            contact_number: contactNumber.trim()
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const feedback = input.parentNode.querySelector('.invalid-feedback');
                        if (feedback) feedback.remove();

                        if (data.available) {
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                        } else {
                            input.classList.remove('is-valid');
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback d-block';
                            errorDiv.textContent = 'This contact number is already registered';
                            input.parentNode.appendChild(errorDiv);
                        }
                    })
                    .catch(error => {
                        console.error('Error checking contact number:', error);
                    });
            }, 300); // Reduced from 800ms for faster real-time feedback
        }

        /**
         * Real-time validation for emergency contact phone (admin)
         */
        document.getElementById('add_emergency_contact_phone')?.addEventListener('input', function() {
            validateAddEmergencyPhone(this.value);
        });

        document.getElementById('add_emergency_contact_phone')?.addEventListener('blur', function() {
            validateAddEmergencyPhone(this.value);
        });

        function validateAddEmergencyPhone(phone) {
            const input = document.getElementById('add_emergency_contact_phone');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            // Remove existing feedback
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!phone || phone.trim() === '') {
                return;
            }

            const phoneRegex = /^(\+639|09)\d{9}$/;

            if (!phoneRegex.test(phone.trim())) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please enter a valid Philippine mobile number';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }
        /**
         * Real-time validation for emergency contact phone with duplicate check
         */
        document.getElementById('add_emergency_contact_phone')?.addEventListener('input', function() {
            validateAddEmergencyPhone(this.value);
            if (this.value.length === 11) {
                validateAddEmergencyPhoneWithDuplicate(this.value);
            }
        });

        document.getElementById('add_emergency_contact_phone')?.addEventListener('blur', function() {
            validateAddEmergencyPhoneWithDuplicate(this.value);
        });

        function validateAddEmergencyPhone(phone) {
            const input = document.getElementById('add_emergency_contact_phone');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!phone || phone.trim() === '') {
                return;
            }

            const phoneRegex = /^09\d{9}$/;

            if (!phoneRegex.test(phone.trim())) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please enter a valid Philippine mobile number';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }

        function validateAddEmergencyPhoneWithDuplicate(phone) {
            const input = document.getElementById('add_emergency_contact_phone');
            const phoneRegex = /^09\d{9}$/;
            
            if (!phoneRegex.test(phone.trim())) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                return;
            }

            input.classList.add('is-valid');
        }

        /**
         * Real-time validation for date of birth (admin)
         */
        document.getElementById('add_date_of_birth')?.addEventListener('change', function() {
            validateAddDateOfBirth(this.value);
        });

        function validateAddDateOfBirth(dob) {
            const input = document.getElementById('add_date_of_birth');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            // Remove existing feedback
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!dob) {
                return;
            }

            const birthDate = new Date(dob);
            const today = new Date();
            const age = Math.floor((today - birthDate) / (365.25 * 24 * 60 * 60 * 1000));

            if (age < 18) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'User must be at least 18 years old';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            if (age > 100) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please enter a valid date of birth';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }

        /**
         * Document preview for file inputs
         */
        function previewAddDocument(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            if (!input.files || !input.files[0]) {
                if (preview) {
                    preview.innerHTML = '';
                    preview.style.display = 'none';
                }
                return;
            }

            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                if (preview) {
                    preview.innerHTML = `
                        <div class="document-preview-item">
                            <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                            <p style="margin-top: 8px; font-size: 12px; color: #666;">${file.name}</p>
                        </div>
                    `;
                    preview.style.display = 'block';
                }
            };

            reader.readAsDataURL(file);
        }

        /**
         * Comprehensive form validation before submission (admin)
         */
        function validateAddUserForm() {
            let isValid = true;

            // Validate username
            const username = document.getElementById('add_username').value.trim();
            if (!validateAddUsername(username)) {
                isValid = false;
            }

            // Validate password
            const password = document.getElementById('add_password').value;
            if (!validateAddPassword(password)) {
                isValid = false;
            }

            // Validate password confirmation
            const passwordConfirm = document.getElementById('add_password_confirmation').value;
            if (!validateAddPasswordMatch(password, passwordConfirm)) {
                isValid = false;
            }

            // Validate contact number
            const contactNumber = document.getElementById('add_contact_number').value.trim();
            if (!validateAddContactNumber(contactNumber)) {
                isValid = false;
            }

            // Validate emergency contact phone
            const emergencyPhone = document.getElementById('add_emergency_contact_phone').value.trim();
            if (!validateAddEmergencyPhone(emergencyPhone)) {
                isValid = false;
            }

            // Validate date of birth
            const dob = document.getElementById('add_date_of_birth').value;
            if (!validateAddDateOfBirth(dob)) {
                isValid = false;
            }

            // validate sex
            const sexSelect = document.getElementById('add_sex');
            if (!sexSelect.value) {
                sexSelect.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Sex is required';
                sexSelect.parentNode.appendChild(errorDiv);
                isValid = false;
            }

            // Check required fields
            const requiredFields = [{
                    id: 'add_username',
                    label: 'Username'
                },
                {
                    id: 'add_password',
                    label: 'Password'
                },
                {
                    id: 'add_password_confirmation',
                    label: 'Password Confirmation'
                },
                {
                    id: 'add_first_name',
                    label: 'First Name'
                },
                {
                    id: 'add_last_name',
                    label: 'Last Name'
                },
                {
                    id: 'add_date_of_birth',
                    label: 'Date of Birth'
                },
                {
                    id: 'add_contact_number',
                    label: 'Contact Number'
                },
                {
                    id: 'add_user_type',
                    label: 'User Type'
                },
                {
                    id: 'add_barangay',
                    label: 'Barangay'
                },
                {
                    id: 'add_complete_address',
                    label: 'Complete Address'
                },
                {
                    id: 'add_emergency_contact_name',
                    label: 'Emergency Contact Name'
                },
                {
                    id: 'add_emergency_contact_phone',
                    label: 'Emergency Contact Phone'
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

            // Validate documents (REQUIRED)
            if (!validateAddDocuments()) {
                isValid = false;
            }

            return isValid;
        }
        /**
         * Validate all document uploads (required)
         */
        function validateAddDocuments() {
            let isValid = true;

            const documents = [{
                    id: 'add_id_front',
                    label: 'Government ID - Front'
                },
                {
                    id: 'add_id_back',
                    label: 'Government ID - Back'
                },
                {
                    id: 'add_location_proof',
                    label: 'Location/Role Proof'
                }
            ];

            documents.forEach(doc => {
                const input = document.getElementById(doc.id);
                if (!input) return;

                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
                input.classList.remove('is-invalid', 'is-valid');

                if (!input.files || !input.files[0]) {
                    input.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = doc.label + ' is required';
                    input.parentNode.appendChild(errorDiv);
                    isValid = false;
                    return;
                }

                input.classList.add('is-valid');
            });

            return isValid;
        }

        /**
         * Submit add user form (admin) with toast notif
         */
        function submitAddUser() {
            // Run comprehensive validation
            if (!validateAddUserForm()) {
                showToast('error', 'Please fix all validation errors before submitting');
                return;
            }

            const form = document.getElementById('addUserForm');

            // Get form data - ALIGNED WITH BACKEND EXPECTATIONS
            const formData = new FormData();

            formData.append('username', document.getElementById('add_username').value.trim());
            formData.append('password', document.getElementById('add_password').value);
            formData.append('password_confirmation', document.getElementById('add_password_confirmation').value);
            formData.append('first_name', document.getElementById('add_first_name').value.trim());
            formData.append('middle_name', document.getElementById('add_middle_name').value.trim());
            formData.append('last_name', document.getElementById('add_last_name').value.trim());
            formData.append('name_extension', document.getElementById('add_name_extension').value);
            formData.append('date_of_birth', document.getElementById('add_date_of_birth').value);
            formData.append('sex', document.getElementById('add_sex').value);
            formData.append('contact_number', document.getElementById('add_contact_number').value.trim());
            formData.append('barangay', document.getElementById('add_barangay').value);
            formData.append('complete_address', document.getElementById('add_complete_address').value.trim());
            formData.append('user_type', document.getElementById('add_user_type').value);
            formData.append('emergency_contact_name', document.getElementById('add_emergency_contact_name').value.trim());
            formData.append('emergency_contact_phone', document.getElementById('add_emergency_contact_phone').value.trim());
            formData.append('status', document.getElementById('add_status').value);

            // Add file uploads if present
            const idFrontInput = document.getElementById('add_id_front');
            const idBackInput = document.getElementById('add_id_back');
            const locationProofInput = document.getElementById('add_location_proof');

            if (idFrontInput?.files[0]) {
                formData.append('id_front', idFrontInput.files[0]);
            }
            if (idBackInput?.files[0]) {
                formData.append('id_back', idBackInput.files[0]);
            }
            if (locationProofInput?.files[0]) {
                formData.append('location_proof', locationProofInput.files[0]);
            }

            // Find the submit button (the one that triggered this function)
            const submitBtn = document.getElementById('create_submit_btn_user');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating...';
            submitBtn.disabled = true;

            // Submit to backend
            fetch('/admin/registrations/create', {
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
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                        modal.hide();

                        // Show success message using TOAST instead of alert
                        showToast('success', data.message || 'User created successfully');

                        // Reload page after short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Show validation errors
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = document.getElementById('add_' + field);
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
                        showToast('error', data.message || 'Failed to create user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred while creating the user');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Delete registration - UPDATED with confirmation toast
        function deleteRegistration(id) {
            // Show confirmation toast instead of browser confirm
            showConfirmationToast(
                'Delete Registration',
                'Are you sure you want to delete this registration?\n\nThis action cannot be undone.',
                () => proceedWithDelete(id)
            );
        }

        // Proceed with actual delete
        function proceedWithDelete(id) {
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
                        showToast('success', data.message || 'Registration deleted successfully');
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 300);
                        }
                        refreshStats();
                    } else {
                        showToast('error', data.message || 'Failed to delete registration');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred while deleting the registration.');
                });
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
                showToast('error', 'From date cannot be later than to date');
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

        // Fix modal backdrop lingering issue
        document.addEventListener('DOMContentLoaded', function() {
            // Get all modals
            const modals = document.querySelectorAll('.modal');

            modals.forEach(modal => {
                // Handle modal hidden event
                modal.addEventListener('hidden.bs.modal', function() {
                    // Remove any lingering backdrops
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Remove modal-open class from body
                    document.body.classList.remove('modal-open');

                    // Reset scroll
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    console.log('Modal cleaned up:', this.id);
                });
            });
        });

        // Alternative: More aggressive cleanup for documentModal specifically
        const documentModal = document.getElementById('documentModal');
        if (documentModal) {
            documentModal.addEventListener('hidden.bs.modal', function() {
                // Remove backdrop
                const backdrop = document.querySelector('.modal-backdrop.fade.show');
                if (backdrop) {
                    backdrop.remove();
                }

                // Remove all backdrops as fallback
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                // Ensure body is scrollable
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';

                // Force remove modal-open if no other modals are open
                const openModals = document.querySelectorAll('.modal.show');
                if (openModals.length === 0) {
                    document.body.classList.remove('modal-open');
                }

                console.log('Document modal cleaned up');
            });
        }

        /**
         * Auto-capitalize first letter of each word in name fields (Title Case)
         */
        function capitalizeName(input) {
            const value = input.value;
            if (value.length > 0) {
                // Split by spaces, capitalize each word, then join back
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        }

        // Apply auto-capitalization to name fields with debounce to avoid issues while typing
        let nameCapitalizeTimeout;

        function handleNameInput(input) {
            clearTimeout(nameCapitalizeTimeout);
            nameCapitalizeTimeout = setTimeout(() => {
                capitalizeName(input);
            }, 500); // Wait 500ms after user stops typing
        }

        // Apply to name fields on blur (when user leaves the field) for immediate effect
        document.getElementById('add_first_name')?.addEventListener('blur', function() {
            capitalizeName(this);
        });

        document.getElementById('add_middle_name')?.addEventListener('blur', function() {
            capitalizeName(this);
        });

        document.getElementById('add_last_name')?.addEventListener('blur', function() {
            capitalizeName(this);
        });

        document.getElementById('add_emergency_contact_name')?.addEventListener('blur', function() {
            capitalizeName(this);
        });

        /**
         * Real-time validation for contact number (admin)
         */
        document.getElementById('add_contact_number')?.addEventListener('input', function() {
            validateAddContactNumber(this.value);
        });

        /**
         * Show edit user modal and load registration data
         */
        function showEditUserModal(registrationId) {
            if (!registrationId) {
                showToast('error', 'Invalid registration ID');
                return;
            }

            // Fetch registration data
            fetch(`/admin/registrations/${registrationId}/details`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(response => {
                    if (!response.success) {
                        throw new Error(response.message || 'Failed to load registration');
                    }

                    const data = response.data;

                    // Populate editable fields
                    document.getElementById('edit_first_name').value = data.first_name || '';
                    document.getElementById('edit_middle_name').value = data.middle_name || '';
                    document.getElementById('edit_last_name').value = data.last_name || '';
                    document.getElementById('edit_name_extension').value = data.name_extension || '';
                    document.getElementById('edit_sex').value = data.sex || '';
                    document.getElementById('edit_contact_number').value = data.contact_number || '';
                    document.getElementById('edit_barangay').value = data.barangay || '';
                    document.getElementById('edit_complete_address').value = data.complete_address || '';
                    document.getElementById('edit_user_type').value = data.user_type || '';
                    document.getElementById('edit_emergency_contact_name').value = data.emergency_contact_name || '';
                    document.getElementById('edit_emergency_contact_phone').value = data.emergency_contact_phone || '';

                    // Read-only fields
                    document.getElementById('editAppUsername').textContent = data.username || '';
                    const statusBadge = document.getElementById('edit_status_badge');
                    const statusColor = getStatusBadgeColor(data.status);
                    statusBadge.className = `badge bg-${statusColor}`;
                    statusBadge.textContent = getStatusText(data.status);

                    const createdAt = new Date(data.created_at);
                    document.getElementById('edit_created_at').textContent = createdAt.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    // Initialize form for change detection
                    initializeEditUserForm(registrationId, data);

                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();

                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error loading registration: ' + error.message);
                });
        }

        /**
         * Initialize edit form with original data for change detection
         */
        function initializeEditUserForm(registrationId, data) {
            const form = document.getElementById('editUserForm');
            const submitBtn = document.getElementById('editUserSubmitBtn');

            // Store original data for comparison
            const originalData = {
                first_name: data.first_name || '',
                middle_name: data.middle_name || '',
                last_name: data.last_name || '',
                name_extension: data.name_extension || '',
                sex: data.sex || '',
                contact_number: data.contact_number || '',
                barangay: data.barangay || '',
                complete_address: data.complete_address || '',
                user_type: data.user_type || '',
                emergency_contact_name: data.emergency_contact_name || '',
                emergency_contact_phone: data.emergency_contact_phone || ''
            };

            form.dataset.originalData = JSON.stringify(originalData);
            form.dataset.registrationId = registrationId;

            // Clear validation states
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            form.querySelectorAll('.form-changed').forEach(el => el.classList.remove('form-changed'));

            // Reset button state - ALWAYS KEEP ENABLED
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
            submitBtn.disabled = false;
            submitBtn.dataset.hasChanges = 'false';

            // Add change listeners
            addEditUserFormChangeListeners(registrationId);
        }

        /**
         * Add event listeners to detect form changes
         */
        function addEditUserFormChangeListeners(registrationId) {
            const form = document.getElementById('editUserForm');
            const inputs = form.querySelectorAll(
                'input[type="text"], input[type="tel"], textarea, select');

            inputs.forEach(input => {
                input.removeEventListener('input', handleEditUserFormChange);
                input.removeEventListener('change', handleEditUserFormChange);

                input.addEventListener('input', handleEditUserFormChange);
                input.addEventListener('change', handleEditUserFormChange);
            });
        }

        /**
         * Handle form change event
         */
        function handleEditUserFormChange() {
            const form = document.getElementById('editUserForm');
            const registrationId = form.dataset.registrationId;
            checkEditUserFormChanges(registrationId);
        }

        /**
         * Check for changes in the form and update button state
         */
        function checkEditUserFormChanges(registrationId) {
            const form = document.getElementById('editUserForm');
            const submitBtn = document.getElementById('editUserSubmitBtn');

            if (!form || !submitBtn) return;

            const originalData = JSON.parse(form.dataset.originalData || '{}');
            let hasChanges = false;

            const fieldMap = {
                'first_name': 'edit_first_name',
                'middle_name': 'edit_middle_name',
                'last_name': 'edit_last_name',
                'name_extension': 'edit_name_extension',
                'sex': 'edit_sex',
                'contact_number': 'edit_contact_number',
                'barangay': 'edit_barangay',
                'complete_address': 'edit_complete_address',
                'user_type': 'edit_user_type',
                'emergency_contact_name': 'edit_emergency_contact_name',
                'emergency_contact_phone': 'edit_emergency_contact_phone'
            };

            Object.keys(fieldMap).forEach(fieldName => {
                const elementId = fieldMap[fieldName];
                const input = document.getElementById(elementId);

                if (input) {
                    const currentValue = (input.value || '').trim();
                    const originalValue = (originalData[fieldName] || '').trim();

                    if (currentValue !== originalValue) {
                        hasChanges = true;
                        input.classList.add('form-changed');
                    } else {
                        input.classList.remove('form-changed');
                    }
                }
            });

            // Update button state - ALWAYS KEEP ENABLED
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
        }

        /**
         * Validate edit user form
         */
        function validateEditUserForm() {
            const form = document.getElementById('editUserForm');
            let isValid = true;

            // Clear all previous validation states
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            const requiredFields = [{
                    elementId: 'edit_first_name',
                    label: 'First Name'
                },
                {
                    elementId: 'edit_last_name',
                    label: 'Last Name'
                },
                {
                    elementId: 'edit_contact_number',
                    label: 'Contact Number'
                },
                {
                    elementId: 'edit_barangay',
                    label: 'Barangay'
                },
                {
                    elementId: 'edit_user_type',
                    label: 'User Type'
                },
                {
                    elementId: 'edit_emergency_contact_name',
                    label: 'Emergency Contact Name'
                },
                {
                    elementId: 'edit_emergency_contact_phone',
                    label: 'Emergency Contact Phone'
                }
            ];

            // Validate required fields
            requiredFields.forEach(field => {
                const input = document.getElementById(field.elementId);
                if (input && (!input.value || input.value.trim() === '')) {
                    input.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = field.label + ' is required';
                    input.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            });

            // Validate contact number format
            const contactInput = document.getElementById('edit_contact_number');
            if (contactInput && contactInput.value.trim()) {
                const phoneRegex = /^(\+639|09)\d{9}$/;
                if (!phoneRegex.test(contactInput.value.trim())) {
                    contactInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)';
                    contactInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            }

            // Validate emergency phone
            const emergencyPhoneInput = document.getElementById('edit_emergency_contact_phone');
            if (emergencyPhoneInput && emergencyPhoneInput.value.trim()) {
                const phoneRegex = /^(\+639|09)\d{9}$/;
                if (!phoneRegex.test(emergencyPhoneInput.value.trim())) {
                    emergencyPhoneInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Please enter a valid Philippine mobile number';
                    emergencyPhoneInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            }

            return isValid;
        }

        /**
         * Handle edit form submission with confirmation
         */
        function handleEditUserSubmit() {
            const form = document.getElementById('editUserForm');
            const submitBtn = document.getElementById('editUserSubmitBtn');
            const registrationId = form.dataset.registrationId;

            // Validate form
            if (!validateEditUserForm()) {
                showToast('error', 'Please fix all validation errors before saving');
                return;
            }

            // Check if there are changes
            if (submitBtn.dataset.hasChanges === 'false') {
                showToast('warning', 'No changes detected. Please modify the fields before saving.');
                return;
            }

            // Build changes summary
            const originalData = JSON.parse(form.dataset.originalData || '{}');
            const changedFields = [];

            const fieldLabels = {
                'first_name': 'First Name',
                'middle_name': 'Middle Name',
                'last_name': 'Last Name',
                'name_extension': 'Extension',
                'contact_number': 'Contact Number',
                'barangay': 'Barangay',
                'complete_address': 'Complete Address',
                'user_type': 'User Type',
                'emergency_contact_name': 'Emergency Contact Name',
                'emergency_contact_phone': 'Emergency Contact Phone'
            };

            const fieldMap = {
                'first_name': 'edit_first_name',
                'middle_name': 'edit_middle_name',
                'last_name': 'edit_last_name',
                'name_extension': 'edit_name_extension',
                'contact_number': 'edit_contact_number',
                'barangay': 'edit_barangay',
                'complete_address': 'edit_complete_address',
                'user_type': 'edit_user_type',
                'emergency_contact_name': 'edit_emergency_contact_name',
                'emergency_contact_phone': 'edit_emergency_contact_phone'
            };

            Object.keys(fieldMap).forEach(fieldName => {
                const elementId = fieldMap[fieldName];
                const input = document.getElementById(elementId);
                const currentValue = (input?.value || '').trim();
                const originalValue = (originalData[fieldName] || '').trim();

                if (currentValue !== originalValue) {
                    changedFields.push(fieldLabels[fieldName] || fieldName);
                }
            });

            // Show confirmation toast
            showConfirmationToast(
                'Confirm Update',
                `Save the following changes to this registration?\n\n• ${changedFields.join('\n• ')}`,
                () => proceedWithEditUser(form, registrationId)
            );
        }

        /**
         * Proceed with edit submission after confirmation
         */
        function proceedWithEditUser(form, registrationId) {
            const submitBtn = document.getElementById('editUserSubmitBtn');

            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
            submitBtn.disabled = true;

            // Disable form inputs during submission
            const formInputs = form.querySelectorAll('input, select, textarea');
            formInputs.forEach(input => input.disabled = true);

            // Get values
            const jsonData = {
                first_name: (document.getElementById('edit_first_name')?.value || '').trim(),
                middle_name: (document.getElementById('edit_middle_name')?.value || '').trim(),
                last_name: (document.getElementById('edit_last_name')?.value || '').trim(),
                name_extension: (document.getElementById('edit_name_extension')?.value || '') || null,
                sex: (document.getElementById('edit_sex')?.value || '') || null,
                contact_number: (document.getElementById('edit_contact_number')?.value || '').trim(),
                barangay: (document.getElementById('edit_barangay')?.value || '').trim(),
                complete_address: (document.getElementById('edit_complete_address')?.value || '').trim(),
                user_type: (document.getElementById('edit_user_type')?.value || '').trim(),
                emergency_contact_name: (document.getElementById('edit_emergency_contact_name')?.value || '').trim(),
                emergency_contact_phone: (document.getElementById('edit_emergency_contact_phone')?.value || '').trim()
            };

            console.log('Sending update data:', jsonData);

            // Submit to backend
            fetch(`/admin/registrations/${registrationId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => {
                    return response.json().then(data => {
                        if (!response.ok) {
                            throw {
                                status: response.status,
                                message: data.message || 'Update failed',
                                errors: data.errors || {}
                            };
                        }
                        return data;
                    });
                })
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                        if (modal) modal.hide();

                        showToast('success', data.message || 'Registration updated successfully');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw {
                            status: 422,
                            message: data.message || 'Failed to update registration',
                            errors: data.errors || {}
                        };
                    }
                })
                .catch(error => {
                    console.error('Error occurred:', error);

                    // Handle validation errors
                    if (error.status === 422 && error.errors && typeof error.errors === 'object') {
                        const fieldMap = {
                            'first_name': 'edit_first_name',
                            'last_name': 'edit_last_name',
                            'contact_number': 'edit_contact_number',
                            'barangay': 'edit_barangay',
                            'user_type': 'edit_user_type',
                            'emergency_contact_name': 'edit_emergency_contact_name',
                            'emergency_contact_phone': 'edit_emergency_contact_phone',
                            'complete_address': 'edit_complete_address',
                            'middle_name': 'edit_middle_name',
                            'name_extension': 'edit_name_extension'
                        };

                        Object.keys(error.errors).forEach(field => {
                            const elementId = fieldMap[field];
                            const input = document.getElementById(elementId);
                            if (input) {
                                input.classList.add('is-invalid');
                                const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                                if (existingFeedback) existingFeedback.remove();

                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback d-block';
                                const errorMessage = Array.isArray(error.errors[field]) ?
                                    error.errors[field][0] :
                                    error.errors[field];
                                errorDiv.textContent = errorMessage;
                                input.parentNode.appendChild(errorDiv);
                            }
                        });

                        showToast('error', error.message);
                    } else {
                        showToast('error', error.message || 'Error updating registration');
                    }

                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    formInputs.forEach(input => input.disabled = false);
                });
        }

        /**
         * Auto-capitalize names in edit form
         */
        function capitalizeEditName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');

                // Trigger change detection after capitalization
                const form = document.getElementById('editUserForm');
                if (form && form.dataset.registrationId) {
                    checkEditUserFormChanges(form.dataset.registrationId);
                }
            }
        }

        // Auto-capitalize on blur
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('focusout', function(e) {
                if (e.target.id === 'edit_first_name' ||
                    e.target.id === 'edit_middle_name' ||
                    e.target.id === 'edit_last_name' ||
                    e.target.id === 'edit_emergency_contact_name') {
                    capitalizeEditName(e.target);
                }
            });
        });

        console.log('Enhanced Admin User Management JavaScript with document viewing loaded successfully');
    </script>
@endsection