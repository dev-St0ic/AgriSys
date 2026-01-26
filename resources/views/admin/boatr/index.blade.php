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
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-ship text-primary"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $totalRegistrations }}</div>
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
                        <i class="fas fa-search text-warning"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $inspectionRequiredCount }}</div>
                    <div class="stat-label text-warning">Inspection Required</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-hourglass-half text-info"></i>
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
            <form method="GET" action="{{ route('admin.boatr.requests') }}" id="filterForm">
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
                    <div class="col-md-2">
                        <select name="barangay" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Barangays</option>
                            @foreach ($barangays as $barangay)
                                <option value="{{ $barangay }}"
                                    {{ request('barangay') == $barangay ? 'selected' : '' }}>
                                    {{ $barangay }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search name, vessel, FishR number, barangay..."
                                value="{{ request('search') }}" oninput="autoSearch()" id="searchInput">
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
                            <i></i>Clear
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
                    <i class="fas fa-ship me-2"></i>BoatR Applications
                </h6>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="showAddBoatrModal()">
                    <i class="fas fa-user-plus me-2"></i>Add Registration
                </button>
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
                            <th class="text-center">Date Applied</th>
                            <th class="text-center">Application #</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Inspection</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Documents</th>
                            <th class="text-center">Actions</th>
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
                                        <span class="badge bg-success" id="inspection-badge-{{ $registration->id }}">
                                            <i class="fas fa-check-circle me-1"></i>Completed
                                        </span>
                                    @else
                                        <span class="badge bg-warning" id="inspection-badge-{{ $registration->id }}">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $registration->status_color }} fs-6"
                                        id="status-badge-{{ $registration->id }}">
                                        {{ $registration->formatted_status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $userDocs = $registration->hasUserDocument() ? 1 : 0;
                                        $inspectionDocs = count($registration->inspection_documents ?? []);
                                        $annexesDocs = $registration->annexes ? $registration->annexes->count() : 0;
                                        $totalDocs = $userDocs + $inspectionDocs + $annexesDocs;
                                    @endphp

                                    <div class="boatr-table-documents">
                                        @if ($totalDocs > 0)
                                            <div class="boatr-document-previews">
                                                @if ($userDocs > 0)
                                                    <div class="boatr-mini-doc"
                                                        onclick="viewDocuments({{ $registration->id }})"
                                                        title="User Documents">
                                                        <div class="boatr-mini-doc-icon">
                                                            <i class="fas fa-file-alt text-primary"></i>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($inspectionDocs > 0)
                                                    <div class="boatr-mini-doc"
                                                        onclick="viewDocuments({{ $registration->id }})"
                                                        title="Inspection Documents">
                                                        <div class="boatr-mini-doc-icon">
                                                            <i class="fas fa-file-alt text-primary"></i>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($annexesDocs > 0)
                                                    <div class="boatr-mini-doc"
                                                        onclick="viewDocuments({{ $registration->id }})" title="Annexes">
                                                        <div class="boatr-mini-doc-icon">
                                                            <i class="fas fa-file-alt text-primary"></i>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($totalDocs > 3)
                                                    <div class="boatr-mini-doc boatr-mini-doc-more"
                                                        onclick="viewDocuments({{ $registration->id }})"
                                                        title="+{{ $totalDocs - 3 }} more documents">
                                                        <div class="boatr-more-count">+{{ $totalDocs - 3 }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="boatr-document-summary"
                                                onclick="viewDocuments({{ $registration->id }})">
                                                <small class="text-muted">{{ $totalDocs }}
                                                    document{{ $totalDocs > 1 ? 's' : '' }}</small>
                                            </div>
                                        @else
                                            <div class="boatr-no-documents">
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
                                            onclick="viewRegistration({{ $registration->id }})" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                        <button class="btn btn-sm btn-outline-dark"
                                            onclick="showUpdateModal({{ $registration->id }}, '{{ $registration->status }}')"
                                            title="Update Status">
                                            <i class="fas fa-sync"></i> Change Status
                                        </button>

                                        <button class="btn btn-sm btn-annexes"
                                            onclick="showAnnexesModal({{ $registration->id }})" title="Manage Annexes">
                                            <i class="fas fa-folder-plus me-1"></i>Annexes
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
                                                        onclick="showInspectionModal({{ $registration->id }})">
                                                        <i class="fas fa-clipboard-check text-info me-2"></i>Complete Inspection
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                        onclick="showEditBoatrModal({{ $registration->id }})">
                                                        <i class="fas fa-edit me-2 text-success"></i>Edit Information
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                        onclick="deleteRegistration({{ $registration->id }}, '{{ $registration->application_number }}')">
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
                                <td colspan="8" class="text-center text-muted py-4">
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

    <!-- IMPROVED: Add BoatR Registration Modal -->
    <div class="modal fade" id="addBoatrModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white" style="background: #0d6efd">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Add New BoatR Registration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <form id="addBoatrForm" enctype="multipart/form-data">
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
                                        <label for="boatr_first_name" class="form-label fw-semibold">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="boatr_first_name" required maxlength="100" placeholder="First name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="boatr_middle_name" class="form-label fw-semibold">
                                            Middle Name
                                        </label>
                                        <input type="text" class="form-control" id="boatr_middle_name" maxlength="100" placeholder="Middle name (optional)">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="boatr_last_name" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="boatr_last_name" required maxlength="100" placeholder="Last name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="boatr_name_extension" class="form-label fw-semibold">
                                            Extension
                                        </label>
                                        <select class="form-select" id="boatr_name_extension">
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
                                        <label for="boatr_contact_number" class="form-label fw-semibold">
                                            Contact Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="boatr_contact_number" required placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>09XXXXXXXXX or +639XXXXXXXXX
                                        </small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="boatr_barangay" class="form-label fw-semibold">
                                            Barangay <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="boatr_barangay" required>
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

                        <!-- FishR & Vessel Information Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-link me-2"></i>FishR & Vessel Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="boatr_fishr_number" class="form-label fw-semibold">
                                            FishR Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="boatr_fishr_number" required maxlength="50" placeholder="FISHR-XXXXXXXX">
                                        <input type="hidden" id="boatr_fishr_app_id" value="">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>FishR registration number
                                        </small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="boatr_vessel_name" class="form-label fw-semibold">
                                            Vessel Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="boatr_vessel_name" required maxlength="100" placeholder="Enter vessel name">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="boatr_boat_type" class="form-label fw-semibold">
                                            Boat Type <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="boatr_boat_type" required>
                                            <option value="">Select Type</option>
                                            <option value="Spoon">Spoon</option>
                                            <option value="Plumb">Plumb</option>
                                            <option value="Banca">Banca</option>
                                            <option value="Rake Stem - Rake Stern">Rake Stem - Rake Stern</option>
                                            <option value="Rake Stem - Transom/Spoon/Plumb Stern">Rake Stem - Transom</option>
                                            <option value="Skiff (Typical Design)">Skiff (Typical Design)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boat Dimensions Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-ruler me-2"></i>Boat Dimensions (in feet)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="boatr_boat_length" class="form-label fw-semibold">
                                            Length <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="boatr_boat_length" step="0.01" min="0.1" required placeholder="0.00">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="boatr_boat_width" class="form-label fw-semibold">
                                            Width <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="boatr_boat_width" step="0.01" min="0.1" required placeholder="0.00">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="boatr_boat_depth" class="form-label fw-semibold">
                                            Depth <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="boatr_boat_depth" step="0.01" min="0.1" required placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Engine Information Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-cog me-2"></i>Engine Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="boatr_engine_type" class="form-label fw-semibold">
                                            Engine Type <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="boatr_engine_type" required maxlength="100" placeholder="e.g., Diesel, Gasoline, Outboard">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="boatr_engine_horsepower" class="form-label fw-semibold">
                                            Engine Horsepower <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="boatr_engine_horsepower" min="1" max="9999" required placeholder="0">
                                        <small class="text-muted d-block mt-2">HP</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fishing Information Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-fish me-2"></i>Fishing Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="boatr_primary_fishing_gear" class="form-label fw-semibold">
                                            Primary Fishing Gear <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="boatr_primary_fishing_gear" required>
                                            <option value="">Select Fishing Gear</option>
                                            <option value="Hook and Line">Hook and Line</option>
                                            <option value="Bottom Set Gill Net">Bottom Set Gill Net</option>
                                            <option value="Fish Trap">Fish Trap</option>
                                            <option value="Fish Coral">Fish Coral</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Document Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary text-white">
                                    <i class="fas fa-file-upload me-2"></i>Supporting Document
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-4">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Upload supporting document (Optional). Supported formats: JPG, PNG, PDF (Max 10MB)
                                </p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="boatr_user_document" class="form-label fw-semibold">
                                            Upload Document
                                        </label>
                                        <input type="file" class="form-control" id="boatr_user_document" accept="image/*,.pdf" onchange="previewBoatrDocument('boatr_user_document', 'boatr_doc_preview')">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>JPG, PNG, PDF (Max 10MB)
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="boatr_doc_preview" style="margin-top: 10px;"></div>
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
                                        <label for="boatr_status" class="form-label fw-semibold">
                                            Initial Status <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="boatr_status" required>
                                            <option value="pending" selected>Pending</option>
                                            <option value="under_review">Under Review</option>
                                            <option value="inspection_required">Inspection Required</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- UPDATED: Remarks Card with Character Counter -->
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-comment me-2"></i>Admin Remarks
                                </h6>
                            </div>
                            <div class="card-body">
                                <label for="boatr_remarks" class="form-label fw-semibold">
                                    Remarks (Optional)
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="boatr_remarks" 
                                    rows="3" 
                                    maxlength="2000" 
                                    placeholder="Add any comments about this registration..."
                                    oninput="updateBoatrRemarksCounter()"></textarea>
                                
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Provide context for this registration
                                    </small>
                                    <small class="text-muted" id="boatrRemarksCounter">
                                        <span id="boatrCharCount">0</span>/2000
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info border-left-info mt-3 mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Note:</strong> Required fields are marked with <span class="text-danger">*</span>. All information should be accurate and complete.
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitAddBoatr()">
                        <i class="fas fa-save me-2"></i>Create Registration
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
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
                                        <strong id="updateAppNumber">-</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Applicant Name</small>
                                        <strong id="updateAppName">-</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Vessel Name</small>
                                        <strong id="updateAppVessel">-</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Barangay</small>
                                        <strong id="updateAppBarangay">-</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Boat Type</small>
                                        <strong id="updateAppBoatType">-</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Current Status</small>
                                        <strong id="updateAppCurrentStatus">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Form Card -->
                    <form id="updateForm">
                        <input type="hidden" id="updateApplicationId">

                        <div class="card border-0 bg-light mb-3">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-toggle-on me-2"></i>Update Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="newStatus" class="form-label fw-semibold">
                                        Select New Status 
                                        <span class="text-danger">*</span>
                                    </label>
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
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Choose the new status for this application
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light mb-3">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-comment me-2"></i>Admin Remarks
                                </h6>
                            </div>
                            <div class="card-body">
                                <label for="remarks" class="form-label fw-semibold">
                                    Remarks (Optional)
                                </label>
                                <textarea class="form-control" id="remarks" rows="4" 
                                    placeholder="Add any notes or comments about this status change..."
                                    maxlength="2000"
                                    oninput="updateBoatrStatusRemarksCounter()"></textarea>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Provide context for this status update
                                    </small>
                                    <small class="text-muted" id="boatrStatusRemarksCounter">
                                        <span id="boatrStatusCharCount">0</span>/2000
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Status Change Alert -->
                        <div class="alert alert-info border-left-info mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Note:</strong> Your changes will be logged and the applicant will be notified of the status update.
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="updateStatusBtn" onclick="updateRegistrationStatus()">
                        <i class="fas fa-save me-2"></i>Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspection Modal - UPDATED with consistent design -->
    <div class="modal fade" id="inspectionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white" style="background: #0d6efd">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Complete Boat Inspection
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Alert Info Card -->
                    <div class="alert alert-info border-left-info mb-4">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Note:</strong> Upload the supporting document after completing the on-site boat inspection. Please provide detailed inspection notes.
                    </div>

                    <form id="inspectionForm" enctype="multipart/form-data">
                        <input type="hidden" id="inspectionRegistrationId">

                        <!-- Supporting Document Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-file-upload me-2"></i>Supporting Document
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="supporting_document" class="form-label fw-semibold">
                                        Upload Document <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="supporting_document"
                                        accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>Upload inspection report, boat photos, or other supporting documents. (PDF, JPG, JPEG, PNG - Max 10MB)
                                    </div>
                                    <div class="invalid-feedback" id="documentError"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Inspection Notes Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-comment me-2"></i>Inspection Notes
                                </h6>
                            </div>
                            <div class="card-body">
                                <label for="inspection_notes" class="form-label fw-semibold">
                                    Notes (Optional)
                                </label>
                                <textarea class="form-control" id="inspection_notes" rows="4"
                                    placeholder="Add detailed inspection findings, measurements, condition assessments, or any observations..."
                                    maxlength="1000"
                                    oninput="updateInspectionNotesCounter()"></textarea>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Detailed inspection observations
                                    </small>
                                    <small class="text-muted" id="inspectionNotesCounter">
                                        <span id="inspectionCharCount">0</span>/1000
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Auto-Approve Option Card -->
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-check-square me-2"></i>Action Options
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="approve_application">
                                    <label class="form-check-label" for="approve_application">
                                        <strong>Auto-approve application after inspection</strong>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle me-1"></i>Check this to automatically approve the application upon successful inspection completion
                                        </small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="completeInspectionBtn"
                        onclick="completeInspection()">
                        <i class="fas fa-check me-1"></i>Complete Inspection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Details Modal Enhanced -->
    <div class="modal fade" id="registrationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Application Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                                            <span class="text-primary" id="viewRegNumber"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Full Name:</strong>
                                            <span id="viewRegName"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Contact Number:</strong>
                                            <span>
                                                <a href="tel:" id="viewRegContact" class="text-decoration-none"></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vessel Information Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-ship me-2"></i>Vessel Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <strong>Vessel Name:</strong>
                                            <span id="viewRegVessel"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Boat Type:</strong>
                                            <span id="viewRegBoatType"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>FishR Number:</strong>
                                            <span id="viewRegFishR"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <strong>Barangay:</strong>
                                            <span id="viewRegBarangay"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boat Specifications Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-ruler me-2"></i>Boat Specifications</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <strong>Dimensions:</strong>
                                            <span id="viewRegDimensions"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Engine Type:</strong>
                                            <span id="viewRegEngineType"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Engine HP:</strong>
                                            <span id="viewRegEngineHP"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fishing Information Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-fish me-2"></i>Fishing Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <strong>Primary Fishing Gear:</strong>
                                            <span id="viewRegGear"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Information Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Status & Timeline</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <strong>Current Status:</strong>
                                            <div id="viewRegStatus" style="margin-top: 0.25rem;"></div>
                                        </div>
                                        <div class="col-12">
                                            <strong>Inspection:</strong>
                                            <div id="viewRegInspection" style="margin-top: 0.25rem;"></div>
                                        </div>
                                        <div class="col-12">
                                            <strong>Date Applied:</strong>
                                            <span id="viewRegCreatedAt"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Last Updated:</strong>
                                            <span id="viewRegUpdatedAt"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Card -->
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white text-center">
                                    <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i>Supporting Documents</h6>
                                </div>
                                <div class="card-body">
                                    <div id="viewRegDocumentContainer">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>User Documents:</strong> <span class="badge bg-info" id="userDocCount">0</span></p>
                                                <p><strong>Inspection Documents:</strong> <span class="badge bg-success" id="inspectionDocCount">0</span></p>
                                                <p><strong>Annexes:</strong> <span class="badge bg-warning" id="annexCount">0</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <button class="btn btn-sm btn-info" onclick="viewDocuments({id})">
                                                    <i class="fas fa-eye me-1"></i>View All Documents
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- TOTAL DOCUMENTS MOVED HERE - BELOW ANNEXES -->
                                        <hr class="my-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Total Documents:</strong> <span class="badge bg-primary" id="totalDocCount">0</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks Card (if exists) -->
                        <div class="col-12" id="viewRemarksContainer" style="display: none;">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Admin Remarks</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0" id="viewRegRemarks"></p>
                                </div>
                            </div>
                        </div>

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
                        <i></i>Application Documents
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="documentViewerLoading" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted">Loading documents...</p>
                    </div>
                    <div id="documentViewer" style="display: none;">
                        <!-- Documents will be loaded here -->
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

    <!-- Document Preview Modal -->
    <div class="modal fade" id="documentPreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: #0d6efd">
                    <h5 class="modal-title w-100 text-center" id="documentPreviewTitle">
                        <i></i>Document Preview
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

    <!-- Annexes Modal -->
    <div class="modal fade" id="annexesModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Manage Annexes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Loading State -->
                    <div id="annexesLoading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading annexes...</p>
                    </div>

                    <!-- Content -->
                    <div id="annexesContent">
                        <!-- Registration Info Card -->
                        <div class="card bg-light border-primary mb-4">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Registration Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Application #</small>
                                            <strong class="text-primary" id="annexAppNumber"></strong>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Applicant</small>
                                            <strong id="annexApplicantName"></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Barangay</small>
                                            <strong id="annexBarangay"></strong>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Status</small>
                                            <div id="annexStatus" style="margin-top: 0.25rem;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload New Annex Card -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-upload me-2"></i>Upload New Annex
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="annexUploadForm" enctype="multipart/form-data">
                                    <input type="hidden" id="annexRegistrationId">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="annexFile" class="form-label fw-semibold">
                                                    Select File <span class="text-danger">*</span>
                                                </label>
                                                <input type="file" class="form-control" id="annexFile"
                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif" required>
                                                <div class="invalid-feedback" id="annexFileError"></div>
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-info-circle me-1"></i>Supported: PDF, DOC, DOCX, JPG, PNG, GIF (Max: 10MB)
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="annexTitle" class="form-label fw-semibold">
                                                    Document Title <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="annexTitle"
                                                    placeholder="e.g., Additional Certificate, Supporting Document"
                                                    required>
                                                <div class="invalid-feedback" id="annexTitleError"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="annexDescription" class="form-label fw-semibold">
                                            Description
                                        </label>
                                        <textarea class="form-control" id="annexDescription" rows="3"
                                            placeholder="Brief description of the document (optional)"
                                            oninput="updateAnnexDescriptionCounter()"></textarea>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>Optional context for this document
                                            </small>
                                            <small class="text-muted">
                                                <span id="annexDescCount">0</span>/500
                                            </small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary" onclick="uploadAnnex()">
                                            <i class="fas fa-upload me-2"></i>Upload Annex
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Existing Annexes Card -->
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-folder me-2"></i>Existing Annexes
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="annexesList">
                                    <!-- Annexes will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
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
                <div class="modal-header text-white" style="background: #0d6efd">
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

     <!-- Edit BoatR Modal - FIXED FORM ID -->
<div class="modal fade" id="editBoatrModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title w-100 text-center">
                    <i></i>Edit BoatR Application - <span id="editBoatrNumber"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- THIS IS THE KEY FIX: id="editBoatrForm" -->
                <form id="editBoatrForm" enctype="multipart/form-data">
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
                                    <label for="edit_boatr_first_name" class="form-label fw-semibold">
                                        First Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_boatr_first_name" 
                                        name="first_name" required maxlength="100" placeholder="First name">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_boatr_middle_name" class="form-label fw-semibold">
                                        Middle Name
                                    </label>
                                    <input type="text" class="form-control" id="edit_boatr_middle_name"
                                        name="middle_name" maxlength="100" placeholder="Middle name (optional)">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_boatr_last_name" class="form-label fw-semibold">
                                        Last Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_boatr_last_name"
                                        name="last_name" required maxlength="100" placeholder="Last name">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_boatr_extension" class="form-label fw-semibold">
                                        Extension
                                    </label>
                                    <select class="form-select" id="edit_boatr_extension" name="name_extension">
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
                                    <label for="edit_boatr_contact_number" class="form-label fw-semibold">
                                        Contact Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="edit_boatr_contact_number"
                                        name="contact_number" required placeholder="09XXXXXXXXX"
                                        pattern="^09\d{9}$" maxlength="11">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>09XXXXXXXXX (11 digits)
                                    </small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_boatr_barangay" class="form-label fw-semibold">
                                        Barangay <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_boatr_barangay" name="barangay" required>
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

                    <!-- Vessel Information Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-ship me-2"></i>Vessel Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="edit_boatr_vessel_name" class="form-label fw-semibold">
                                        Vessel Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_boatr_vessel_name"
                                        name="vessel_name" required maxlength="100" placeholder="Vessel name">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_boatr_boat_type" class="form-label fw-semibold">
                                        Boat Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_boatr_boat_type" name="boat_type" required>
                                        <option value="">Select Boat Type</option>
                                        <option value="Spoon">Spoon</option>
                                        <option value="Plumb">Plumb</option>
                                        <option value="Banca">Banca</option>
                                        <option value="Rake Stem - Rake Stern">Rake Stem - Rake Stern</option>
                                        <option value="Rake Stem - Transom/Spoon/Plumb Stern">Rake Stem - Transom</option>
                                        <option value="Skiff (Typical Design)">Skiff (Typical Design)</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_boatr_fishr_number" class="form-label fw-semibold">
                                        FishR Number
                                    </label>
                                    <input type="text" class="form-control" id="edit_boatr_fishr_number"
                                        name="fishr_number" disabled placeholder="Auto-filled">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>Read-only (cannot be changed)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boat Dimensions Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-ruler me-2"></i>Boat Dimensions (in feet)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="edit_boatr_boat_length" class="form-label fw-semibold">
                                        Length <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="edit_boatr_boat_length"
                                        name="boat_length" step="0.01" min="0.1" required placeholder="0.00">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_boatr_boat_width" class="form-label fw-semibold">
                                        Width <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="edit_boatr_boat_width"
                                        name="boat_width" step="0.01" min="0.1" required placeholder="0.00">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_boatr_boat_depth" class="form-label fw-semibold">
                                        Depth <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="edit_boatr_boat_depth"
                                        name="boat_depth" step="0.01" min="0.1" required placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Engine Information Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-cog me-2"></i>Engine Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_boatr_engine_type" class="form-label fw-semibold">
                                        Engine Type <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_boatr_engine_type"
                                        name="engine_type" required maxlength="100" placeholder="e.g., Diesel, Gasoline">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_boatr_engine_horsepower" class="form-label fw-semibold">
                                        Engine Horsepower <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="edit_boatr_engine_horsepower"
                                        name="engine_horsepower" min="1" max="9999" required placeholder="0">
                                    <small class="text-muted d-block mt-2">HP</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fishing Information Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-fish me-2"></i>Fishing Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="edit_boatr_primary_fishing_gear" class="form-label fw-semibold">
                                        Primary Fishing Gear <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_boatr_primary_fishing_gear"
                                        name="primary_fishing_gear" required>
                                        <option value="">Select Fishing Gear</option>
                                        <option value="Hook and Line">Hook and Line</option>
                                        <option value="Bottom Set Gill Net">Bottom Set Gill Net</option>
                                        <option value="Fish Trap">Fish Trap</option>
                                        <option value="Fish Coral">Fish Coral</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supporting Document Card -->
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
                            <div id="edit_boatr_current_document" style="display: none; margin-bottom: 1.5rem;">
                                <label class="form-label fw-semibold text-muted mb-2">Current Document</label>
                                <div id="edit_boatr_supporting_document_preview"></div>
                            </div>

                            <!-- Upload New Document Section -->
                            <div class="row">
                                <div class="col-12">
                                    <label for="edit_boatr_supporting_document" class="form-label fw-semibold">
                                        Supporting Document
                                    </label>
                                    <input type="file" class="form-control" id="edit_boatr_supporting_document"
                                        name="supporting_document" accept="image/*,.pdf"
                                        onchange="previewEditBoatrDocument('edit_boatr_supporting_document', 'edit_boatr_new_doc_preview')">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>Upload a new file to replace it.
                                    </small>
                                </div>
                            </div>

                            <!-- New Document Preview -->
                            <div id="edit_boatr_new_doc_preview" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Inspection Information Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-clipboard-check me-2"></i>Inspection Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block mb-2">Inspection Status</small>
                                    <div>
                                        <span id="edit_boatr_inspection_status_badge" class="badge bg-secondary fs-6"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block mb-2">Inspection Date</small>
                                    <div id="edit_boatr_inspection_date" class="fw-semibold">-</div>
                                </div>
                            </div>

                            <!-- Current Inspection Document Display -->
                            <div id="edit_boatr_inspection_doc_container" style="display: none; margin-bottom: 1.5rem; padding: 1rem; background: #e7f3ff; border-radius: 8px; border-left: 4px solid #0d6efd;">
                                <label class="form-label fw-semibold text-primary mb-2">Current Inspection Document</label>
                                <div id="edit_boatr_inspection_doc_preview"></div>
                            </div>

                            <!-- Inspection Notes -->
                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <label for="edit_boatr_inspection_notes" class="form-label fw-semibold">
                                        Inspection Notes
                                    </label>
                                    <textarea class="form-control" id="edit_boatr_inspection_notes"
                                        name="inspection_notes" rows="3" maxlength="2000"
                                        placeholder="Add or edit inspection notes..."
                                        oninput="updateEditBoatrInspectionCounter()"></textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Inspection observations
                                        </small>
                                        <small class="text-muted" id="edit_boatr_inspection_counter">
                                            <span id="edit_boatr_inspection_char_count">0</span>/2000
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Replace New Inspection Document -->
                            <div class="row">
                                <div class="col-12">
                                    <label for="edit_boatr_inspection_document" class="form-label fw-semibold">
                                        Replace Inspection Document
                                    </label>
                                    <input type="file" class="form-control" id="edit_boatr_inspection_document"
                                        name="inspection_document" accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="previewEditBoatrInspectionDocument('edit_boatr_inspection_document', 'edit_boatr_inspection_doc_upload_preview')">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>Replace inspection report, boat photos, or supporting documents (PDF, JPG, PNG - Max 10MB)
                                    </small>
                                </div>
                            </div>

                            <!-- New Inspection Document Preview -->
                            <div id="edit_boatr_inspection_doc_upload_preview" class="mt-3"></div>
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
                                        <span id="edit_boatr_status_badge" class="badge bg-secondary fs-6"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block mb-2">Date Applied</small>
                                    <div id="edit_boatr_created_at" class="fw-semibold">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info border-left-info mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Note:</strong> You can edit vessel, boat, and engine information here.
                        To change application status or add admin remarks, use the "Change Status" button from the main table.
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="editBoatrSubmitBtn"
                    onclick="handleEditBoatrSubmit()">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
<!-- DELETE MODAL FOR BOATR -->
<div class="modal fade" id="deleteBoatrModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title w-100 text-center">Permanently Delete BoatR Registration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                    <p class="mb-0">This action cannot be undone. Permanently deleting <strong id="delete_boatr_name"></strong> will:</p>
                </div>
                <ul class="mb-0">
                    <li>Remove the BoatR application from the database</li>
                    <li>Delete all associated documents and files</li>
                    <li>Delete all annexes and attachments</li>
                    <li>Delete all application history and logs</li>
                    <li>Cannot be recovered</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmPermanentDeleteBoatr()"
                    id="confirm_delete_boatr_btn">
                    <span class="btn-text">Yes, Delete Permanently</span>
                    <span class="btn-loader" style="display: none;"><span
                            class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
                </button>
            </div>
        </div>
    </div>
</div>
    <style>
        /* Document count badge on mini docs */
        .boatr-doc-count {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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

        /* Row deletion animation */
        .row-deleting {
            background-color: #f8d7da;
            transition: all 0.3s ease;
        }

        /* Modern Statistics Cards */
        .stat-card {
            background: #ffffff;
            border: 1px solid #e3e6f0;
            border-radius: 15px;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: transparent;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
            border-color: #4e73df;
        }

        .stat-card:hover::before {
            background: transparent;
        }

        .stat-card .card-body {
            position: relative;
            z-index: 2;
            background: transparent;
            margin: 0;
            border-radius: 0;
            backdrop-filter: none;
            padding: 1.5rem;
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
            color: #5a5c69;
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
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
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
            background-color: #198754 !important;
        }

        .bg-danger {
            background-color: #e74a3b !important;
        }

        .bg-secondary {
            background-color: #858796 !important;
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

        /* Enhanced modal styling
        .modal-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
        } */

        /* .modal-header .btn-close {
            filter: invert(1);
        } */

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

        /* Annexes Button Styling */
        .btn-annexes {
            background-color: transparent;
            border-color: #4e73df;
            color: #4e73df;
        }

        .btn-annexes:hover {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }

        .btn-annexes:focus,
        .btn-annexes:active {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Annex Document Items */
        .annex-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .annex-item:hover {
            background: #e9ecef;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .annex-title {
            color: #4e73df;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .annex-meta {
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* Modal z-index fixes for stacking */
        .modal {
            z-index: 1050;
        }

        .modal-backdrop {
            z-index: 1049;
        }

        /* Preview modal when opened from annexes modal should be higher */
        .modal-preview-from-annexes {
            z-index: 1060 !important;
        }

        .modal-preview-from-annexes+.modal-backdrop {
            z-index: 1059 !important;
        }

        /* Fix nested modal backdrop greying */
        .modal {
            background-color: rgba(0, 0, 0, 0) !important;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-backdrop.show {
            opacity: 0.5 !important;
        }

        /* Prevent multiple backdrops from stacking and turning grey */
        .modal-backdrop+.modal-backdrop {
            display: none !important;
        }

        /* Only show one backdrop at a time */
        body.modal-open .modal-backdrop {
            opacity: 0.5;
        }

        body.modal-open .modal-backdrop:nth-child(n+2) {
            display: none !important;
        }

        /* Enhanced Document Viewer Styles */
        #documentModal .modal-content,
        #documentPreviewModal .modal-content {
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }

        #documentModal .modal-header,
        #documentPreviewModal .modal-header {
            border-radius: 12px 12px 0 0;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
        }

        #documentModal .modal-footer,
        #documentPreviewModal .modal-footer {
            border-radius: 0 0 12px 12px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-top: 1px solid #dee2e6;
        }

        #documentViewer,
        #documentPreview {
            min-height: 400px;
            max-height: 80vh;
            overflow: auto;
        }

        /* Enhanced document item styles */
        .document-item {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .document-item:hover {
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

        /* change detection*/

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

        /* Button state transitions */
        #updateStatusBtn {
            transition: all 0.3s ease;
        }

        #updateStatusBtn:disabled {
            cursor: not-allowed;
        }

        #updateStatusBtn.no-changes {
            background-color: #6c757d;
            border-color: #6c757d;
            opacity: 0.8;
        }

        #updateStatusBtn.no-changes:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            opacity: 0.9;
        }

        /* Highlight animations */
        @keyframes highlightChange {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }

        .form-changed {
            animation: highlightChange 1s ease-out;
        }

        /* Modal form styling */
        #updateModal .form-control,
        #updateModal .form-select {
            border-radius: 8px;
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        #updateModal .form-control:focus,
        #updateModal .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        #updateModal .form-control.form-changed,
        #updateModal .form-select.form-changed {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        #updateModal .form-control.form-changed:focus,
        #updateModal .form-select.form-changed:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.5);
        }

        /* Smooth transitions for button text changes */
        #updateStatusBtn i {
            transition: all 0.2s ease;
        }

        /* Visual indicator for change labels */
        .change-indicator label {
            transition: all 0.3s ease;
        }

        .change-indicator.changed label {
            color: #856404;
            font-weight: 500;
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
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .document-actions .btn {
            transition: all 0.2s ease;
        }

        .document-actions .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Document thumbnail improvements */
        .document-thumbnail {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .document-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced card styles for document sections */
        .card-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
        }

        /* BoatR-Style Table Document Previews */
        .boatr-table-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        .boatr-document-previews {
            display: flex;
            gap: 0.25rem;
            align-items: center;
        }

        .boatr-mini-doc {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: white;
            border: 2px solid #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .boatr-mini-doc:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-color: #4e73df;
        }

        .boatr-mini-doc-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .boatr-mini-doc-more {
            background: #f8f9fa;
            border-color: #dee2e6;
        }

        .boatr-mini-doc-more:hover {
            background: #e9ecef;
            border-color: #6c757d;
        }

        .boatr-more-count {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
        }

        .boatr-mini-doc-more:hover .boatr-more-count {
            color: #495057;
        }

        .boatr-document-summary {
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .boatr-document-summary:hover {
            color: #4e73df !important;
        }

        .boatr-document-summary:hover small {
            color: #4e73df !important;
        }

        .boatr-no-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem;
            opacity: 0.7;
        }

        .boatr-no-documents i {
            font-size: 1.25rem;
        }

        /* Fix nested modal backdrop greying */
        .modal {
            background-color: rgba(0, 0, 0, 0) !important;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-backdrop.show {
            opacity: 0.5 !important;
        }

        /* Prevent multiple backdrops from stacking and turning grey */
        .modal-backdrop+.modal-backdrop {
            display: none !important;
        }

        /* Fix z-index for nested modals */
        .modal:nth-of-type(1) {
            z-index: 1050;
        }

        .modal:nth-of-type(1)~.modal-backdrop {
            z-index: 1049;
        }

        .modal:nth-of-type(2) {
            z-index: 1060;
        }

        .modal:nth-of-type(2)~.modal-backdrop {
            z-index: 1059;
        }

        /* Specific nested modal handling */
        .modal.show~.modal {
            z-index: 1060 !important;
        }

        .modal.show~.modal~.modal-backdrop {
            z-index: 1059 !important;
        }

        /* close modal
        .modal-header .btn-close {
            background-color: rgba(255, 255, 255, 0.7);
            opacity: 1;
        }

        .modal-header .btn-close:hover {
            background-color: rgba(255, 255, 255, 1);
        }

        .modal-header .btn-close:focus {
            background-color: rgba(255, 255, 255, 1);
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.5);
        } */

        /* Document modal on top when opened from registration modal */
        #documentModal {
            z-index: 1060 !important;
        }

        #documentPreviewModal {
            z-index: 1060 !important;
        }

        #annexesModal {
            z-index: 1060 !important;
        }

        /* Prevent backdrop color issues */
        body.modal-open {
            overflow: hidden;
        }

        /* Clear backdrop stacking */
        .modal+.modal-backdrop+.modal-backdrop {
            display: none !important;
        }

        /* Document type specific colors for mini previews */
        .boatr-mini-doc[title*="User"] {
            border-color: #17a2b8;
        }

        .boatr-mini-doc[title*="User"]:hover {
            background-color: rgba(23, 162, 184, 0.1);
        }

        .boatr-mini-doc[title*="Inspection"] {
            border-color: #28a745;
        }

        .boatr-mini-doc[title*="Inspection"]:hover {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .boatr-mini-doc[title*="Annexes"] {
            border-color: #ffc107;
        }

        .boatr-mini-doc[title*="Annexes"]:hover {
            background-color: rgba(255, 193, 7, 0.1);
        }

        /* Responsive adjustments for table documents */
        @media (max-width: 768px) {
            .boatr-mini-doc {
                width: 28px;
                height: 28px;
            }

            .boatr-mini-doc-icon {
                font-size: 0.75rem;
            }

            .boatr-more-count {
                font-size: 0.7rem;
            }
        }

        /* BoatR-Style Document Viewer (matching FishR) */
        .boatr-document-viewer {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            min-height: 400px;
        }

        .boatr-document-container {
            position: relative;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 100%;
            margin-bottom: 1rem;
        }

        .boatr-document-image {
            max-width: 100%;
            max-height: 60vh;
            object-fit: contain;
            display: block;
            transition: transform 0.3s ease;
        }

        .boatr-document-image:hover {
            transform: scale(1.02);
        }

        .boatr-document-image.zoomed {
            transform: scale(1.5);
            cursor: zoom-out;
        }

        .boatr-pdf-embed {
            border-radius: 8px;
        }

        .boatr-document-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .boatr-document-size-badge {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .boatr-document-actions {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .boatr-btn {
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
            cursor: pointer;
        }

        .boatr-btn-outline {
            background: white;
            color: #6c757d;
            border-color: #dee2e6;
        }

        .boatr-btn-outline:hover {
            background: #f8f9fa;
            color: #495057;
            border-color: #adb5bd;
            transform: translateY(-1px);
        }

        .boatr-btn-primary {
            background: #4e73df;
            color: white;
            border-color: #4e73df;
        }

        .boatr-btn-primary:hover {
            background: #2653d4;
            border-color: #2653d4;
            color: white;
            transform: translateY(-1px);
        }

        .boatr-document-info {
            text-align: center;
            color: #6c757d;
        }

        .boatr-file-name {
            margin: 0;
            font-size: 0.9rem;
            word-break: break-word;
        }

        .boatr-document-placeholder {
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

        /* Responsive design for BoatR document viewer */
        @media (max-width: 768px) {
            .boatr-document-actions {
                flex-direction: column;
                width: 100%;
            }

            .boatr-btn {
                width: 100%;
                min-width: auto;
            }

            .boatr-document-image {
                max-height: 40vh;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            #documentModal .modal-dialog,
            #documentPreviewModal .modal-dialog {
                margin: 0.5rem;
            }

            #documentViewer,
            #documentPreview {
                padding: 0.5rem;
            }

            .document-actions .btn {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }

            .document-item {
                padding: 10px;
            }
        }
/* BoatR Application Details Modal - Enhanced Card-Based Styling */
#registrationModal .modal-content {
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border-radius: 8px;
}

#registrationModal .modal-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border-bottom: 2px solid #0b5ed7;
    padding: 1.5rem;
}

#registrationModal .modal-header .modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: white;
}

#registrationModal .modal-header .btn-close {
    opacity: 0.8;
}

#registrationModal .modal-header .btn-close:hover {
    opacity: 1;
}

#registrationModal .modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    padding: 1.25rem;
}

#registrationModal .modal-body {
    padding: 2rem;
    background-color: #fff;
}

/* Card Styling within Application Details */
#registrationModal .card {
    border-width: 2px;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    height: 100%;
}

#registrationModal .card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

#registrationModal .card-header {
    padding: 1rem 1.25rem;
    font-weight: 600;
    color: white;
    font-size: 0.95rem;
    letter-spacing: 0.3px;
}

#registrationModal .card-header.bg-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
}

#registrationModal .card-header.bg-info {
    background: linear-gradient(135deg, #0dcaf0 0%, #0bb5db 100%) !important;
}

#registrationModal .card-header.bg-success {
    background: linear-gradient(135deg, #198754 0%, #157347 100%) !important;
}

#registrationModal .card-header.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
    color: #000;
}

#registrationModal .card-header.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%) !important;
}

#registrationModal .card-header.bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5c636a 100%) !important;
}

#registrationModal .card-body {
    padding: 1.5rem;
    background-color: #fff;
}

#registrationModal .row.g-2 > div {
    padding-bottom: 0.5rem;
}

#registrationModal .row.g-2 > div > div {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

#registrationModal .row.g-2 > div > div:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

#registrationModal strong {
    color: #495057;
    font-weight: 600;
    display: block;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 0.25rem;
}

#registrationModal .card-body span {
    color: #333;
    font-size: 0.95rem;
    display: block;
}

#registrationModal a {
    color: #0d6efd;
    text-decoration: none;
}

#registrationModal a:hover {
    text-decoration: underline;
}

#registrationModal .text-muted {
    color: #6c757d !important;
    font-style: italic;
}

/* Badge Styling */
#registrationModal .badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    display: inline-block;
    margin-top: 0.25rem;
}

/* Document Container Styling */
#registrationModal .text-center.p-4 {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 2rem 1.5rem !important;
}

#registrationModal .text-center i {
    opacity: 0.7;
    margin-bottom: 1rem;
}

#registrationModal .text-center h6 {
    font-weight: 600;
    color: #333;
    margin: 0.5rem 0;
    font-size: 0.95rem;
}

#registrationModal .btn-outline-info {
    color: #0dcaf0;
    border-color: #0dcaf0;
    font-size: 0.85rem;
    padding: 0.35rem 0.75rem;
}

#registrationModal .btn-outline-info:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    #registrationModal .modal-dialog {
        margin: 0.5rem;
    }

    #registrationModal .modal-body {
        padding: 1.5rem 1rem;
    }

    #registrationModal .row.g-4 > div {
        margin-bottom: 1rem;
    }

    #registrationModal .card-header {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }

    #registrationModal .card-body {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    #registrationModal .modal-header .modal-title {
        font-size: 1.05rem;
    }

    #registrationModal .modal-body {
        padding: 1rem;
    }

    #registrationModal .text-center.p-4 {
        padding: 1.5rem 1rem !important;
    }

    #registrationModal .card-body span {
        font-size: 0.9rem;
    }

    #registrationModal .card-header {
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
    }
}

/* Annex Document Items */
.annex-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.annex-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #4e73df;
}

.annex-item-content {
    flex: 1;
    min-width: 0;
}

.annex-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 8px;
    word-break: break-word;
    font-size: 0.95rem;
}

.annex-description {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 8px;
    font-style: italic;
    line-height: 1.4;
}

.annex-meta {
    font-size: 0.8rem;
    color: #6c757d;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.annex-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.annex-meta-item i {
    color: #4e73df;
}

.annex-item-actions {
    display: flex;
    gap: 0.5rem;
    margin-left: 1rem;
    flex-shrink: 0;
}

.annex-item-actions .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .annex-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .annex-item-actions {
        margin-left: 0;
        margin-top: 1rem;
        width: 100%;
    }

    .annex-item-actions .btn {
        flex: 1;
    }
}
/* Modal z-index fixes for stacking */
.modal {
    z-index: 1050;
}

.modal-backdrop {
    z-index: 1049;
}

/* Annexes Modal */
#annexesModal {
    z-index: 1060 !important;
}

/* Document Preview Modal - Higher than annexes */
#documentPreviewModal {
    z-index: 1080 !important;
}

#documentModal {
    z-index: 1080 !important;
}

/* Ensure backdrops don't overlap */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
}

/* Hide duplicate backdrops */
.modal-backdrop:nth-of-type(2),
.modal-backdrop:nth-of-type(3) {
    display: none !important;
}

    /* Inspection Modal Styling - Consistent with other modals */
    #inspectionModal .modal-content {
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
    }

    #inspectionModal .modal-header {
        border-bottom: 2px solid #0b5ed7;
        padding: 1.5rem;
    }

    #inspectionModal .modal-header .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: white;
    }

    #inspectionModal .modal-header .btn-close {
        opacity: 0.8;
    }

    #inspectionModal .modal-header .btn-close:hover {
        opacity: 1;
    }

    #inspectionModal .modal-body {
        padding: 2rem;
        background-color: #fff;
    }

    /* Card Styling within Inspection Modal */
    #inspectionModal .card {
        border-width: 1px;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    #inspectionModal .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    #inspectionModal .card-header {
        padding: 1rem 1.25rem;
        font-weight: 600;
        color: white;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    #inspectionModal .card-header h6 {
        color: #0d6efd;
    }

    #inspectionModal .card-body {
        padding: 1.5rem;
        background-color: #fff;
    }

    #inspectionModal .form-label {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.75rem;
        display: block;
    }

    #inspectionModal .form-control,
    #inspectionModal .form-select {
        border-radius: 6px;
        border: 1px solid #e3e6f0;
        transition: all 0.3s ease;
    }

    #inspectionModal .form-control:focus,
    #inspectionModal .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    #inspectionModal .form-text {
        color: #6c757d;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }

    /* Invalid Feedback */
    #inspectionModal .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        display: block;
        margin-top: 0.25rem;
    }

    #inspectionModal .is-invalid.form-control,
    #inspectionModal .is-invalid.form-select {
        border-color: #dc3545;
    }

    #inspectionModal .is-invalid.form-control:focus,
    #inspectionModal .is-invalid.form-select:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Textarea Styling */
    #inspectionModal textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* Character Counter */
    #inspectionNotesCounter {
        font-weight: 500;
    }

    #inspectionCharCount {
        color: #0d6efd;
        font-weight: 600;
    }

    /* Form Check Styling */
    #inspectionModal .form-check {
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 6px;
        /* border-left: 3px solid #0d6efd; */
    }

    #inspectionModal .form-check-input {
        width: 1.25em;
        height: 1.25em;
        margin-top: 0.3em;
        cursor: pointer;
    }

    #inspectionModal .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    #inspectionModal .form-check-label {
        margin-left: 0.5rem;
        cursor: pointer;
        color: #495057;
    }

    #inspectionModal .form-check-label strong {
        color: #212529;
    }

    #inspectionModal .form-check-label small {
        color: #6c757d;
        margin-left: 1.75rem;
    }

    /* Alert Styling
    #inspectionModal .alert {
        border-radius: 8px;
        border-left: 4px solid #17a2b8;
        background-color: #d1ecf1;
        color: #0c5460;
    } */

    #inspectionModal .alert i {
        color: #17a2b8;
    }

    /* Modal Footer */
    #inspectionModal .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 1.25rem;
    }

    #inspectionModal .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    #inspectionModal .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    #inspectionModal .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
        transform: translateY(-1px);
    }

    #inspectionModal .btn-success {
        background-color: #198754;
        border-color: #198754;
        color: white;
    }

    #inspectionModal .btn-success:hover {
        background-color: #157347;
        border-color: #146c43;
        transform: translateY(-1px);
    }

    #inspectionModal .btn-success:disabled,
    #inspectionModal .btn-success.no-changes {
        background-color: #6c757d;
        border-color: #6c757d;
        opacity: 0.8;
        cursor: not-allowed;
    }

    /* Loading State */
    #inspectionModal .btn.btn-loading {
        position: relative;
        pointer-events: none;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        #inspectionModal .modal-body {
            padding: 1.5rem 1rem;
        }

        #inspectionModal .card-header {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        #inspectionModal .card-body {
            padding: 1rem;
        }

        #inspectionModal .form-check-label small {
            margin-left: 0;
        }

        #inspectionModal .modal-footer {
            flex-direction: column;
            gap: 0.5rem;
        }

        #inspectionModal .btn {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        #inspectionModal .modal-header .modal-title {
            font-size: 1.05rem;
        }

        #inspectionModal .modal-body {
            padding: 1rem;
        }

        #inspectionModal .card {
            margin-bottom: 0.75rem;
        }
    }
    </style>
@endsection

@section('scripts')
    <script>
        let searchTimeout;
        let currentData = {};

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

        // ========== CHARACTER COUNTERS ==========
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

            // Annex description character count
            const annexDesc = document.getElementById('annexDescription');
            const annexDescCount = document.getElementById('annexDescCount');

            if (annexDesc && annexDescCount) {
                annexDesc.addEventListener('input', function() {
                    const count = this.value.length;
                    annexDescCount.textContent = count;

                    if (count > 500) {
                        this.value = this.value.substring(0, 500);
                        annexDescCount.textContent = '500';
                    }
                });
            }
        });

        // ========== TOAST SYSTEM ==========
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

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (document.contains(toast)) {
                    removeToast(toast);
                }
            }, 5000);
        }

        function showConfirmationToast(title, message, onConfirm) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const toast = document.createElement('div');
            toast.className = 'toast-notification confirmation-toast';

            const callbackId = 'confirm_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            toast.dataset.confirmCallback = callbackId;
            window[callbackId] = onConfirm;

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
            }, 15000);
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
                    showToast('error', 'An error occurred: ' + error.message);
                }
            }

            delete window[callbackId];
            removeToast(toast);
        }

        function removeToast(toastElement) {
            if (!toastElement || !toastElement.parentElement) return;

            toastElement.classList.remove('show');
            setTimeout(() => {
                if (toastElement.parentElement) {
                    toastElement.remove();
                }
            }, 300);
        }

        // ========== CSRF TOKEN ==========
        function getCSRFToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                throw new Error('CSRF token meta tag not found in document head');
            }
            const csrfToken = token.getAttribute('content');
            if (!csrfToken) {
                throw new Error('CSRF token content is empty');
            }
            return csrfToken;
        }

        // ========== STATUS UPDATE ==========
        // Enhanced show update modal with change tracking
        function showUpdateModal(id, currentStatus) {
            // Validate parameters
            if (!id) {
                showToast('error', 'Invalid application ID');
                return;
            }

            // Show loading state in modal
            document.getElementById('updateAppNumber').innerHTML = `
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>`;

            // First fetch the application details
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
            .then(response => {
                if (!response.success) {
                    throw new Error(response.message || 'Failed to load application details');
                }

                const data = response;

                // Validate data object
                if (!data) {
                    throw new Error('No application data received');
                }

                // Populate the hidden field
                document.getElementById('updateApplicationId').value = id;

                // Populate application info display with null checks
                document.getElementById('updateAppNumber').textContent = data.application_number || 'N/A';
                document.getElementById('updateAppName').textContent = data.full_name || 'N/A';
                document.getElementById('updateAppVessel').textContent = data.vessel_name || 'N/A';
                document.getElementById('updateAppBarangay').textContent = data.barangay || 'N/A';
                document.getElementById('updateAppBoatType').textContent = data.boat_type || 'N/A';

                // Show current status with badge styling and null safety
                const currentStatusElement = document.getElementById('updateAppCurrentStatus');
                const statusColor = data.status_color || 'secondary';
                const formattedStatus = data.formatted_status || getBoatrStatusText(data.status);

                currentStatusElement.innerHTML = `
                    <span class="badge bg-${statusColor}">${formattedStatus}</span>`;

                // Set form values and store original values for comparison
                const statusSelect = document.getElementById('newStatus');
                const remarksTextarea = document.getElementById('remarks');

                // Handle null status values
                const currentStatusValue = data.status || 'pending';
                statusSelect.value = currentStatusValue;
                statusSelect.dataset.originalStatus = currentStatusValue;

                const currentRemarks = data.remarks || '';
                remarksTextarea.value = currentRemarks;
                remarksTextarea.dataset.originalRemarks = currentRemarks;

                // Reset remarks counter
                updateBoatrStatusRemarksCounter();

                // Remove any previous change indicators
                statusSelect.classList.remove('form-changed');
                remarksTextarea.classList.remove('form-changed');
                statusSelect.parentElement.classList.remove('change-indicator', 'changed');
                remarksTextarea.parentElement.classList.remove('change-indicator', 'changed');

                // Add change indicator classes
                statusSelect.parentElement.classList.add('change-indicator');
                remarksTextarea.parentElement.classList.add('change-indicator');

                // Reset update button state - IMPORTANT: Ensure it's enabled and visible
                const updateButton = document.getElementById('updateStatusBtn');
                updateButton.classList.remove('no-changes');
                updateButton.classList.remove('disabled');
                updateButton.disabled = false;
                updateButton.style.opacity = '1';
                updateButton.innerHTML = '<i class="fas fa-save me-2"></i>Update Status';
                updateButton.style.pointerEvents = 'auto';
                updateButton.style.cursor = 'pointer';

                // Remove old listeners to prevent duplicates
                statusSelect.removeEventListener('change', checkBoatrUpdateModalChanges);
                remarksTextarea.removeEventListener('input', checkBoatrUpdateModalChanges);

                // Add change detection event listeners
                statusSelect.addEventListener('change', checkBoatrUpdateModalChanges);
                remarksTextarea.addEventListener('input', checkBoatrUpdateModalChanges);

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('updateModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Error loading application details: ' + error.message);
            });
        }

        // Update remarks character counter in Update Status Modal
        function updateBoatrStatusRemarksCounter() {
            const textarea = document.getElementById('remarks');
            const charCount = document.getElementById('boatrStatusCharCount');
            
            if (textarea && charCount) {
                charCount.textContent = textarea.value.length;
                
                // Change color when approaching limit
                if (textarea.value.length > 1800) {
                    document.getElementById('boatrStatusRemarksCounter').classList.add('text-danger');
                    document.getElementById('boatrStatusRemarksCounter').classList.remove('text-warning', 'text-muted');
                } else if (textarea.value.length > 1500) {
                    document.getElementById('boatrStatusRemarksCounter').classList.add('text-warning');
                    document.getElementById('boatrStatusRemarksCounter').classList.remove('text-danger', 'text-muted');
                } else {
                    document.getElementById('boatrStatusRemarksCounter').classList.remove('text-warning', 'text-danger');
                    document.getElementById('boatrStatusRemarksCounter').classList.add('text-muted');
                }
            }
        }

            // Check for changes in Update Status Modal
            function checkBoatrUpdateModalChanges() {
                const statusSelect = document.getElementById('newStatus');
                const remarksTextarea = document.getElementById('remarks');
                const updateButton = document.getElementById('updateStatusBtn');

                if (!statusSelect.dataset.originalStatus) return;

                const statusChanged = statusSelect.value !== statusSelect.dataset.originalStatus;
                const remarksChanged = remarksTextarea.value.trim() !== (remarksTextarea.dataset.originalRemarks || '').trim();

                // Visual feedback
                statusSelect.classList.toggle('form-changed', statusChanged);
                statusSelect.parentElement.classList.toggle('changed', statusChanged);

                remarksTextarea.classList.toggle('form-changed', remarksChanged);
                remarksTextarea.parentElement.classList.toggle('changed', remarksChanged);

                // Button state
                const hasChanges = statusChanged || remarksChanged;
                updateButton.classList.toggle('no-changes', !hasChanges);

                if (!hasChanges) {
                    updateButton.innerHTML = '<i class="fas fa-check me-1"></i>No Changes';
                } else {
                    updateButton.innerHTML = '<i class="fas fa-save me-1"></i>Update Status';
                }
            }

            // Helper function to get BoatR status display text
            function getBoatrStatusText(status) {
                if (!status || status === null || status === undefined) {
                    return 'Unknown';
                }

                const statusStr = String(status).toLowerCase();

                switch (statusStr) {
                    case 'pending':
                        return 'Pending';
                    case 'under_review':
                        return 'Under Review';
                    case 'inspection_required':
                        return 'Inspection Required';
                    case 'inspection_scheduled':
                        return 'Inspection Scheduled';
                    case 'documents_pending':
                        return 'Documents Pending';
                    case 'approved':
                        return 'Approved';
                    case 'rejected':
                        return 'Rejected';
                    default:
                        return statusStr.charAt(0).toUpperCase() + statusStr.slice(1);
                }
            }

  // Fixed: Enhanced update registration status function with proper element checking
function updateRegistrationStatus() {
    // Get elements with proper null checking
    const applicationIdElement = document.getElementById('updateApplicationId');
    const newStatusElement = document.getElementById('newStatus');
    const remarksElement = document.getElementById('remarks');

    // Validate all required elements exist
    if (!applicationIdElement) {
        showToast('error', 'UI Error: Application ID field not found');
        console.error('Element not found: updateApplicationId');
        return;
    }
    if (!newStatusElement) {
        showToast('error', 'UI Error: Status dropdown not found');
        console.error('Element not found: newStatus');
        return;
    }
    if (!remarksElement) {
        showToast('error', 'UI Error: Remarks field not found');
        console.error('Element not found: remarks');
        return;
    }

    const id = applicationIdElement.value;
    const newStatus = newStatusElement.value;
    const remarks = remarksElement.value;

    // Log for debugging
    console.log('updateRegistrationStatus called with:', {
        id: id,
        newStatus: newStatus,
        remarks: remarks
    });

    // Quick validation
    if (!id || id === '' || id === 'undefined') {
        showToast('error', 'Invalid application ID');
        console.error('Invalid ID:', id);
        return;
    }

    if (!newStatus) {
        showToast('warning', 'Please select a status before updating');
        return;
    }

    // Get original values for change detection
    const originalStatus = newStatusElement.dataset.originalStatus || '';
    const originalRemarks = remarksElement.dataset.originalRemarks || '';

    console.log('Change detection:', {
        originalStatus: originalStatus,
        newStatus: newStatus,
        originalRemarks: originalRemarks,
        remarks: remarks
    });

    // Check for changes
    const statusChanged = (newStatus !== originalStatus);
    const remarksChanged = (remarks.trim() !== originalRemarks.trim());

    if (!statusChanged && !remarksChanged) {
        showToast('info', 'No changes detected');
        return;
    }

    // Build changes summary
    let changesSummary = [];
    if (statusChanged) {
        const originalStatusText = getBoatrStatusText(originalStatus);
        const newStatusText = getBoatrStatusText(newStatus);
        changesSummary.push(`Status: ${originalStatusText} → ${newStatusText}`);
    }
    if (remarksChanged) {
        if (originalRemarks.trim() === '') {
            changesSummary.push('Remarks: Added new remarks');
        } else if (remarks.trim() === '') {
            changesSummary.push('Remarks: Removed existing remarks');
        } else {
            changesSummary.push('Remarks: Modified');
        }
    }

    console.log('Changes summary:', changesSummary);

    // Show confirmation toast
    showConfirmationToast(
        'Confirm Update',
        `Update this application with the following changes?\n\n${changesSummary.join('\n')}`,
        () => proceedWithBoatrStatusUpdate(id, newStatus, remarks)
    );
}

// Proceed with BoatR status update (improved with error handling)
function proceedWithBoatrStatusUpdate(id, newStatus, remarks) {
    console.log('proceedWithBoatrStatusUpdate called:', { id, newStatus, remarks });

    if (!id || id === '' || id === 'undefined') {
        showToast('error', 'Invalid application ID');
        return;
    }

    if (!newStatus) {
        showToast('error', 'Status is required');
        return;
    }

    const updateBtn = document.getElementById('updateStatusBtn');
    if (!updateBtn) {
        showToast('error', 'UI error: Button not found');
        return;
    }

    const originalContent = updateBtn.innerHTML;
    updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
    updateBtn.disabled = true;

    const url = `/admin/boatr/requests/${id}/status`;
    console.log('Sending request to:', url);

    fetch(url, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: newStatus,
            remarks: remarks
        })
    })
    .then(response => {
        console.log('Response status:', response.status);

        if (response.status === 500) {
            return response.json().then(data => {
                throw new Error(data.message || 'Server error occurred');
            }).catch(() => {
                throw new Error('Internal server error. Please check logs.');
            });
        }

        if (response.status === 422) {
            return response.json().then(data => {
                throw new Error(data.message || 'Validation failed');
            });
        }

        if (response.status === 404) {
            throw new Error('Application not found');
        }

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);

        if (!data.success) {
            throw new Error(data.message || 'Update failed');
        }

        showToast('success', 'Status updated successfully');

        if (data.registration) {
            updateTableRow(id, data.registration);
        }

        const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
        if (modal) {
            modal.hide();
        }

        setTimeout(() => window.location.reload(), 1500);
    })
    .catch(error => {
        console.error('Error updating status:', error);

        let errorMessage = 'Update failed: ';

        if (error.name === 'TypeError' && error.message === 'Failed to fetch') {
            errorMessage += 'Cannot connect to server. Please check your internet connection or contact administrator.';
        } else {
            errorMessage += error.message;
        }

        showToast('error', errorMessage);
    })
    .finally(() => {
        updateBtn.innerHTML = originalContent;
        updateBtn.disabled = false;
    });
}

        // Initialize remarks counter when modal is shown
        document.addEventListener('DOMContentLoaded', function() {
            const updateModal = document.getElementById('updateModal');
            
            if (updateModal) {
                updateModal.addEventListener('shown.bs.modal', function() {
                    const textarea = document.getElementById('remarks');
                    
                    if (textarea) {
                        // Reset counter when modal opens
                        updateBoatrStatusRemarksCounter();
                        
                        // Add input listener for real-time counter
                        textarea.removeEventListener('input', updateBoatrStatusRemarksCounter);
                        textarea.addEventListener('input', updateBoatrStatusRemarksCounter);
                    }
                });
            }
        });

        // Helper function to get status display text
        function getStatusText(status) {
            const statusMap = {
                'pending': 'Pending',
                'under_review': 'Under Review',
                'inspection_required': 'Inspection Required',
                'inspection_scheduled': 'Inspection Scheduled',
                'documents_pending': 'Documents Pending',
                'approved': 'Approved',
                'rejected': 'Rejected'
            };
            return statusMap[status] || status;
        }


        // Function to check for changes and provide visual feedback
        function checkBoatrForChanges() {
            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');
            const updateButton = document.getElementById('updateStatusBtn');

            if (!statusSelect.dataset.originalStatus) return; // Don't check if original values aren't set yet

            const statusChanged = statusSelect.value !== statusSelect.dataset.originalStatus;
            const remarksChanged = remarksTextarea.value.trim() !== (remarksTextarea.dataset.originalRemarks || '').trim();

            // Visual feedback for status field
            statusSelect.classList.toggle('form-changed', statusChanged);
            statusSelect.parentElement.classList.toggle('changed', statusChanged);

            // Visual feedback for remarks field
            remarksTextarea.classList.toggle('form-changed', remarksChanged);
            remarksTextarea.parentElement.classList.toggle('changed', remarksChanged);

            // Update button state
            const hasChanges = statusChanged || remarksChanged;
            updateButton.classList.toggle('no-changes', !hasChanges);

            // Update button text and icon based on changes
            if (!hasChanges) {
                updateButton.innerHTML = '<i class="fas fa-check me-1"></i>No Changes';
            } else {
                updateButton.innerHTML = '<i class="fas fa-save me-1"></i>Update Status';
            }
        }

      

        // Helper function to get BoatR status display text
        function getBoatrStatusText(status) {
            const statusMap = {
                'pending': 'Pending',
                'under_review': 'Under Review',
                'inspection_required': 'Inspection Required',
                'inspection_scheduled': 'Inspection Scheduled',
                'documents_pending': 'Documents Pending',
                'approved': 'Approved',
                'rejected': 'Rejected'
            };
            return statusMap[status] || status;
        }


        // ========== INSPECTION MODAL ==========
        function updateInspectionNotesCounter() {
            const textarea = document.getElementById('inspection_notes');
            const charCount = document.getElementById('inspectionCharCount');
            const counter = document.getElementById('inspectionNotesCounter');
            
            if (textarea && charCount) {
                const currentLength = textarea.value.length;
                charCount.textContent = currentLength;
                
                // Change color when approaching limit
                if (currentLength > 800) {
                    counter.classList.add('text-danger');
                    counter.classList.remove('text-warning', 'text-muted');
                } else if (currentLength > 600) {
                    counter.classList.add('text-warning');
                    counter.classList.remove('text-danger', 'text-muted');
                } else {
                    counter.classList.remove('text-warning', 'text-danger');
                    counter.classList.add('text-muted');
                }
            }
        }
        function showInspectionModal(id) {
            console.log('Opening inspection modal for registration:', id);
            
            document.getElementById('inspectionRegistrationId').value = id;
            document.getElementById('supporting_document').value = '';
            document.getElementById('inspection_notes').value = '';
            document.getElementById('approve_application').checked = false;
            document.getElementById('inspectionCharCount').textContent = '0';

            // Clear validation errors
            document.getElementById('supporting_document').classList.remove('is-invalid');
            document.getElementById('documentError').textContent = '';

            // Store original values for change detection
            document.getElementById('supporting_document').dataset.originalFile = '';
            document.getElementById('inspection_notes').dataset.originalNotes = '';
            document.getElementById('approve_application').dataset.originalChecked = 'false';

            // Add change indicator classes
            const fileInput = document.getElementById('supporting_document').parentElement;
            const notesInput = document.getElementById('inspection_notes').parentElement;
            const approveCheck = document.getElementById('approve_application').parentElement;

            fileInput.classList.add('change-indicator');
            notesInput.classList.add('change-indicator');
            approveCheck.classList.add('change-indicator');

            // Remove previous highlighting
            document.getElementById('supporting_document').classList.remove('form-changed');
            document.getElementById('inspection_notes').classList.remove('form-changed');
            document.getElementById('approve_application').classList.remove('form-changed');
            fileInput.classList.remove('changed');
            notesInput.classList.remove('changed');
            approveCheck.classList.remove('changed');

            // Reset button state
            const completeBtn = document.getElementById('completeInspectionBtn');
            completeBtn.classList.remove('no-changes');
            completeBtn.innerHTML = '<i class="fas fa-check me-1"></i>Complete Inspection';
            completeBtn.disabled = false;

            // Remove old listeners
            document.getElementById('supporting_document').removeEventListener('change', checkInspectionModalChanges);
            document.getElementById('inspection_notes').removeEventListener('input', checkInspectionModalChanges);
            document.getElementById('approve_application').removeEventListener('change', checkInspectionModalChanges);

            // Add change detection listeners
            document.getElementById('supporting_document').addEventListener('change', checkInspectionModalChanges);
            document.getElementById('inspection_notes').addEventListener('input', checkInspectionModalChanges);
            document.getElementById('approve_application').addEventListener('change', checkInspectionModalChanges);

            // Reset counter
            updateInspectionNotesCounter();

            // Add event listener for real-time counter
            document.getElementById('inspection_notes').removeEventListener('input', updateInspectionNotesCounter);
            document.getElementById('inspection_notes').addEventListener('input', updateInspectionNotesCounter);

            const modal = new bootstrap.Modal(document.getElementById('inspectionModal'));
            modal.show();
        }

            // Check for changes in Inspection Modal
         function checkInspectionModalChanges() {
            const fileInput = document.getElementById('supporting_document');
            const notesInput = document.getElementById('inspection_notes');
            const approveCheckbox = document.getElementById('approve_application');
            const completeBtn = document.getElementById('completeInspectionBtn');

            const fileChanged = fileInput.files.length > 0;
            const notesChanged = notesInput.value.trim() !== (notesInput.dataset.originalNotes || '').trim();
            const approveChanged = approveCheckbox.checked !== (notesInput.dataset.originalChecked === 'true');

            // Visual feedback
            fileInput.classList.toggle('form-changed', fileChanged);
            fileInput.parentElement.classList.toggle('changed', fileChanged);

            notesInput.classList.toggle('form-changed', notesChanged);
            notesInput.parentElement.classList.toggle('changed', notesChanged);

            approveCheckbox.classList.toggle('form-changed', approveChanged);
            approveCheckbox.parentElement.classList.toggle('changed', approveChanged);

            // Button state
            const hasChanges = fileChanged || notesChanged || approveChanged;
            completeBtn.classList.toggle('no-changes', !hasChanges);

            if (!hasChanges) {
                completeBtn.innerHTML = '<i class="fas fa-check me-1"></i>No Changes';
                completeBtn.disabled = true;
            } else {
                completeBtn.innerHTML = '<i class="fas fa-check me-1"></i>Complete Inspection';
                completeBtn.disabled = false;
            }
        }


        // Complete inspection with change detection
        function completeInspection() {
            const id = document.getElementById('inspectionRegistrationId').value;
            const fileInput = document.getElementById('supporting_document');
            const notes = document.getElementById('inspection_notes').value;
            const autoApprove = document.getElementById('approve_application').checked;

            if (!fileInput.files[0]) {
                fileInput.classList.add('is-invalid');
                document.getElementById('documentError').textContent = 'Please select a supporting document';
                showToast('warning', 'Please select a supporting document');
                return;
            }

            if (fileInput.files[0].size > 10 * 1024 * 1024) {
                fileInput.classList.add('is-invalid');
                document.getElementById('documentError').textContent = 'File size must be less than 10MB';
                showToast('warning', 'File size must be less than 10MB');
                return;
            }

            fileInput.classList.remove('is-invalid');
            document.getElementById('documentError').textContent = '';

            let changesSummary = [];
            changesSummary.push(`File: ${fileInput.files[0].name}`);
            if (notes.trim()) changesSummary.push(`Inspection Notes: Added`);
            if (autoApprove) changesSummary.push(`Auto-approve: Enabled`);

            showConfirmationToast(
                'Complete Inspection',
                `Are you sure you want to complete the inspection?\n\n${changesSummary.join('\n')}`,
                () => proceedWithInspection(id, fileInput, notes, autoApprove)
            );
        }

        // Proceed with inspection
        function proceedWithInspection(id, fileInput, notes, autoApprove) {
            const completeBtn = document.getElementById('completeInspectionBtn');
            if (!completeBtn) {
                showToast('error', 'UI error: Button not found');
                return;
            }

            const originalContent = completeBtn.innerHTML;
            completeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            completeBtn.disabled = true;

            const formData = new FormData();
            formData.append('supporting_document', fileInput.files[0]);
            formData.append('inspection_notes', notes);
            formData.append('approve_application', autoApprove ? '1' : '0');

            fetch(`/admin/boatr/requests/${id}/complete-inspection`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    return response.json();
                })
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Inspection failed');

                    showToast('success', data.message || 'Inspection completed successfully');

                    if (data.registration) {
                        updateTableRow(id, data.registration);
                    }

                    const modal = bootstrap.Modal.getInstance(document.getElementById('inspectionModal'));
                    if (modal) modal.hide();

                    setTimeout(() => window.location.reload(), 1500);
                })
                .catch(error => {
                    console.error('Error completing inspection:', error);
                    showToast('error', 'Inspection failed: ' + error.message);
                })
                .finally(() => {
                    completeBtn.innerHTML = originalContent;
                    completeBtn.disabled = false;
                });
        }

        function deleteRegistration(id, applicationNumber) {
            console.log('=== deleteRegistration called ===', { id, applicationNumber });
            
            try {
                // Validate inputs
                if (!id || !applicationNumber) {
                    showToast('error', 'Invalid application data');
                    return;
                }

                // Set the application number in the modal
                const nameElement = document.getElementById('delete_boatr_name');
                if (!nameElement) {
                    console.error('Modal name element not found');
                    showToast('error', 'Delete modal not found in page');
                    return;
                }

                nameElement.textContent = applicationNumber;

                // Store the ID globally for use in confirmation function
                window.currentDeleteBoatrId = id;
                
                console.log('Showing delete modal for ID:', id);

                // Show the delete modal
                const deleteModal = document.getElementById('deleteBoatrModal');
                if (!deleteModal) {
                    console.error('Delete modal element not found');
                    showToast('error', 'Delete modal not found');
                    return;
                }

                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
                
            } catch (error) {
                console.error('Error preparing delete dialog:', error);
                showToast('error', 'Failed to open delete dialog: ' + error.message);
            }
        }

        /**
 * Confirm permanent delete for BoatR registration
 */
function confirmPermanentDeleteBoatr() {
    console.log('=== confirmPermanentDeleteBoatr called ===');
    
    const id = window.currentDeleteBoatrId;
    
    if (!id) {
        showToast('error', 'Application ID not found');
        console.error('currentDeleteBoatrId is not set');
        return;
    }

    console.log('Deleting application ID:', id);

    try {
        // Show loading state
        const deleteBtn = document.getElementById('confirm_delete_boatr_btn');
        if (!deleteBtn) {
            console.error('Delete button not found');
            showToast('error', 'Delete button not found');
            return;
        }

        const btnText = deleteBtn.querySelector('.btn-text');
        const btnLoader = deleteBtn.querySelector('.btn-loader');
        
        if (btnText) btnText.style.display = 'none';
        if (btnLoader) btnLoader.style.display = 'inline';
        deleteBtn.disabled = true;

        const url = `/admin/boatr/requests/${id}`;
        console.log('Sending DELETE request to:', url);

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': getCSRFToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                return response.json().then(data => {
                    throw {
                        status: response.status,
                        message: data.message || 'Delete failed'
                    };
                }).catch(err => {
                    throw {
                        status: response.status,
                        message: err.message || `HTTP ${response.status}`
                    };
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Delete response:', data);
            
            if (data.success) {
                // Close modal
                const deleteModalEl = document.getElementById('deleteBoatrModal');
                const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
                if (deleteModal) {
                    deleteModal.hide();
                }

                // Show success message
                showToast('success', data.message || 'Application deleted successfully');

                // Remove the row with animation
                const row = document.getElementById(`registration-${id}`);
                if (row) {
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        
                        // Check if table is empty
                        const tbody = document.querySelector('#registrationsTable tbody');
                        if (tbody && tbody.children.length === 0) {
                            // Reload page to show empty state
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    }, 300);
                } else {
                    // Fallback: reload page
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }

                // Reset for next use
                window.currentDeleteBoatrId = null;
            } else {
                throw new Error(data.message || 'Delete operation failed');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            
            // Close modal
            const deleteModalEl = document.getElementById('deleteBoatrModal');
            const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
            if (deleteModal) {
                deleteModal.hide();
            }

            // Show error
            const errorMsg = error.message || 'Unknown error occurred';
            showToast('error', 'Error: ' + errorMsg);

        })
        .finally(() => {
            // Reset button state
            const deleteBtn = document.getElementById('confirm_delete_boatr_btn');
            const btnText = deleteBtn.querySelector('.btn-text');
            const btnLoader = deleteBtn.querySelector('.btn-loader');
            
            if (btnText) btnText.style.display = 'inline';
            if (btnLoader) btnLoader.style.display = 'none';
            deleteBtn.disabled = false;
        });

    } catch (error) {
        console.error('Fatal error in confirmPermanentDeleteBoatr:', error);
        showToast('error', 'Fatal error: ' + error.message);
    }
}

        // Proceed with registration deletion
        function proceedWithRegistrationDelete(id, applicationNumber) {
            // DEBUG: Log the URL being called
            const deleteUrl = `/admin/boatr/requests/${id}`;
            console.log('Delete URL:', deleteUrl);
            console.log('Method: DELETE');

            fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest' // Add this header
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);

                    if (!response.ok) {
                        // Get the response text for debugging
                        return response.text().then(text => {
                            console.error('Error response:', text);
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Delete response:', data);

                    if (data.success) {
                        showToast('success', data.message || 'Application deleted successfully');

                        // Remove row from table with animation
                        const row = document.getElementById(`registration-${id}`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();

                                // Check if table is empty
                                const tbody = document.querySelector('#registrationsTable tbody');
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
                    console.error('Delete error:', error);
                    showToast('error', 'Failed to delete application: ' + error.message);
                });
        }

        // Updated View Registration Details Function for BoatR
        function viewRegistration(id) {
            if (!id) {
                showToast('error', 'Invalid application ID');
                return;
            }

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
            modal.show();

            // Fetch registration details
            fetch(`/admin/boatr/requests/${id}`, {
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
                .then(data => {
                    if (!data.success) throw new Error('Failed to load application details');

                    // Populate Personal Information
                    document.getElementById('viewRegNumber').textContent = data.application_number || 'N/A';
                    document.getElementById('viewRegName').textContent = data.full_name || 'N/A';
                    
                    const contactLink = document.getElementById('viewRegContact');
                    contactLink.href = `tel:${data.contact_number}`;
                    contactLink.textContent = data.contact_number || 'N/A';

                    // Populate Vessel Information
                    document.getElementById('viewRegVessel').textContent = data.vessel_name || 'N/A';
                    document.getElementById('viewRegBoatType').textContent = data.boat_type || 'N/A';
                    document.getElementById('viewRegFishR').textContent = data.fishr_number || 'N/A';

                    // Populate Location
                    document.getElementById('viewRegBarangay').textContent = data.barangay || 'N/A';

                    // Populate Boat Specifications
                    const dimensions = `${data.boat_length || '0'} x ${data.boat_width || '0'} x ${data.boat_depth || '0'} ft`;
                    document.getElementById('viewRegDimensions').textContent = dimensions;
                    document.getElementById('viewRegEngineType').textContent = data.engine_type || 'N/A';
                    document.getElementById('viewRegEngineHP').textContent = (data.engine_horsepower || 'N/A') + ' HP';

                    // Populate Fishing Information
                    document.getElementById('viewRegGear').textContent = data.primary_fishing_gear || 'N/A';

                    // Populate Status Information
                    const statusElement = document.getElementById('viewRegStatus');
                    statusElement.innerHTML = `<span class="badge bg-${data.status_color}" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">${data.formatted_status}</span>`;

                    const inspectionElement = document.getElementById('viewRegInspection');
                    if (data.inspection_completed) {
                        inspectionElement.innerHTML = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span>';
                    } else {
                        inspectionElement.innerHTML = '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pending</span>';
                    }

                    document.getElementById('viewRegCreatedAt').textContent = data.created_at || 'N/A';
                    document.getElementById('viewRegUpdatedAt').textContent = data.updated_at || 'N/A';

                    // Populate Documents
                    const docContainer = document.getElementById('viewRegDocumentContainer');
                    const userDocsCount = data.user_documents ? data.user_documents.length : 0;
                    const inspectionDocsCount = data.inspection_documents ? data.inspection_documents.length : 0;
                    const annexesCount = data.annexes ? data.annexes.length : 0;
                    const totalDocs = userDocsCount + inspectionDocsCount + annexesCount;

                    if (totalDocs > 0) {
                        docContainer.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>User Documents:</strong> <span class="badge bg-info">${userDocsCount}</span></p>
                                    <p><strong>Inspection Documents:</strong> <span class="badge bg-success">${inspectionDocsCount}</span></p>
                                    <p><strong>Annexes:</strong> <span class="badge bg-warning">${annexesCount}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Documents:</strong> <span class="badge bg-primary">${totalDocs}</span></p>
                                    <button class="btn btn-sm btn-info" onclick="viewDocuments(${id})">
                                        <i class="fas fa-eye me-1"></i>View All Documents
                                    </button>
                                </div>
                            </div>
                        `;
                    } else {
                        docContainer.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No documents available</p>
                            </div>
                        `;
                    }

                    // Populate Remarks if exists
                    const remarksContainer = document.getElementById('viewRemarksContainer');
                    if (data.remarks) {
                        remarksContainer.style.display = 'block';
                        document.getElementById('viewRegRemarks').textContent = data.remarks;
                    } else {
                        remarksContainer.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error loading application details: ' + error.message);
                    modal.hide();
                });
        }
        // view documents
        function viewDocuments(id) {
            const modal = new bootstrap.Modal(document.getElementById('documentModal'));
            modal.show();

            document.getElementById('documentViewerLoading').style.display = 'block';
            document.getElementById('documentViewer').style.display = 'none';

            fetch(`/admin/boatr/requests/${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken()
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Failed to load documents');

                    document.getElementById('documentViewerLoading').style.display = 'none';
                    document.getElementById('documentViewer').style.display = 'block';

                    let documentsHtml = '';

                    // User Documents Section
                    if (data.user_documents && data.user_documents.length > 0) {
                        documentsHtml += `
                        <div class="document-section mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-file-image text-info me-2"></i>User Documents
                            </h5>
                    `;
                        data.user_documents.forEach((doc, index) => {
                            documentsHtml += `
                            <div class="document-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${doc.name || 'User Document ' + (index + 1)}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>${doc.uploaded_at || 'N/A'}
                                        </small>
                                    </div>
                                    <button class="btn btn-sm btn-primary" onclick="previewDocument(${id}, 'user', ${index})">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                </div>
                            </div>
                        `;
                        });
                        documentsHtml += `</div>`;
                    }

                    // Inspection Documents Section
                    if (data.inspection_documents && data.inspection_documents.length > 0) {
                        documentsHtml += `
                        <div class="document-section mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-check text-success me-2"></i>Inspection Documents
                            </h5>
                    `;
                        data.inspection_documents.forEach((doc, index) => {
                            documentsHtml += `
                            <div class="document-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${doc.name || 'Inspection Document ' + (index + 1)}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>${doc.uploaded_at || 'N/A'}
                                        </small>
                                    </div>
                                    <button class="btn btn-sm btn-primary" onclick="previewDocument(${id}, 'inspection', ${index})">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                </div>
                            </div>
                        `;
                        });
                        documentsHtml += `</div>`;
                    }

                    // Annexes Section
                    if (data.annexes && data.annexes.length > 0) {
                        documentsHtml += `
                        <div class="document-section mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-folder text-warning me-2"></i>Annexes
                            </h5>
                    `;
                        data.annexes.forEach((annex) => {
                            documentsHtml += `
                            <div class="document-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${annex.title}</h6>
                                        <p class="mb-1 text-muted small">${annex.description || 'No description'}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>${annex.created_at || 'N/A'}
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-file me-1"></i>${formatFileSize(annex.file_size)}
                                        </small>
                                    </div>
                                    <button class="btn btn-sm btn-primary" onclick="previewAnnex(${id}, ${annex.id})">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                </div>
                            </div>
                        `;
                        });
                        documentsHtml += `</div>`;
                    }

                    // No documents message
                    if (!documentsHtml) {
                        documentsHtml = `
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No documents available</p>
                        </div>
                    `;
                    }

                    document.getElementById('documentViewer').innerHTML = documentsHtml;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('documentViewerLoading').style.display = 'none';
                    document.getElementById('documentViewer').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load documents: ${error.message}
                    </div>
                `;
                    document.getElementById('documentViewer').style.display = 'block';
                });
        }

        // FIXED: previewDocument function
        function previewDocument(id, type, index) {
            const previewModalEl = document.getElementById('documentPreviewModal');
            previewModalEl.classList.add('modal-preview-from-annexes');
            const modal = new bootstrap.Modal(previewModalEl);
            modal.show();

            document.getElementById('documentPreview').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Loading document preview...</p>
                </div>
            `;

            document.getElementById('documentPreviewTitle').innerHTML = '<i class="fas fa-eye me-2"></i>Document Preview';

            fetch(`/admin/boatr/requests/${id}/document-preview`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: type,
                        index: index
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Failed to load document preview');

                    document.getElementById('documentPreviewTitle').innerHTML =
                        `<i class="fas fa-eye me-2"></i>${data.document_name}`;

                    // FIXED: Extract file extension from filename, NOT from mime type
                    let fileExtension = '';
                    if (data.document_name) {
                        fileExtension = data.document_name.split('.').pop().toLowerCase();
                    } else {
                        fileExtension = data.document_type?.toLowerCase() || 'unknown';
                    }

                    const fileName = data.document_name;
                    const fileUrl = data.document_url;

                    const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
                    const documentTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
                    const videoTypes = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
                    const audioTypes = ['mp3', 'wav', 'ogg', 'aac', 'm4a'];

                    const addActionButtons = () => {
                        return `
                        <div class="boatr-document-actions">
                            <button class="btn boatr-btn boatr-btn-outline" onclick="window.open('${fileUrl}', '_blank')">
                                <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                            </button>
                            <button class="btn boatr-btn boatr-btn-primary" onclick="downloadFile('${fileUrl}', '${fileName}')">
                                <i class="fas fa-download me-2"></i>Download
                            </button>
                        </div>
                        <div class="boatr-document-info">
                            <p class="boatr-file-name">File: ${fileName} (${fileExtension.toUpperCase()})</p>
                        </div>
                    `;
                    };

                    if (imageTypes.includes(fileExtension)) {
                        const img = new Image();
                        img.onload = function() {
                            document.getElementById('documentPreview').innerHTML = `
                            <div class="boatr-document-viewer">
                                <div class="boatr-document-container">
                                    <img src="${fileUrl}"
                                        class="boatr-document-image"
                                        alt="Document preview"
                                        onclick="toggleImageZoomBoatr(this)"
                                        style="cursor: zoom-in;">
                                    <div class="boatr-document-overlay">
                                        <div class="boatr-document-size-badge">
                                            ${Math.round((this.naturalWidth * this.naturalHeight) / 1024)}KB
                                        </div>
                                    </div>
                                </div>
                                ${addActionButtons()}
                            </div>
                        `;
                        };
                        img.onerror = function() {
                            document.getElementById('documentPreview').innerHTML = `
                            <div class="boatr-document-viewer">
                                <div class="boatr-document-placeholder">
                                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                                    <h6>Unable to load image</h6>
                                    <p class="mb-3">The image could not be displayed.</p>
                                </div>
                                ${addActionButtons()}
                            </div>
                        `;
                        };
                        img.src = fileUrl;

                    } else if (fileExtension === 'pdf') {
                        document.getElementById('documentPreview').innerHTML = `
                        <div class="boatr-document-viewer">
                            <div class="boatr-document-container">
                                <embed src="${fileUrl}"
                                    type="application/pdf"
                                    width="100%"
                                    height="600px"
                                    class="boatr-pdf-embed">
                            </div>
                            ${addActionButtons()}
                        </div>
                    `;

                        setTimeout(() => {
                            const embed = document.querySelector('#documentPreview embed');
                            if (!embed || embed.offsetHeight === 0) {
                                document.getElementById('documentPreview').innerHTML = `
                                <div class="boatr-document-viewer">
                                    <div class="boatr-document-placeholder">
                                        <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                                        <h5>PDF Preview Unavailable</h5>
                                        <p class="mb-3">Your browser doesn't support PDF preview or the file couldn't be loaded.</p>
                                    </div>
                                    <div class="boatr-document-actions">
                                        <button class="btn boatr-btn boatr-btn-outline" onclick="window.open('${fileUrl}', '_blank')">
                                            <i class="fas fa-external-link-alt me-2"></i>Open PDF
                                        </button>
                                        <button class="btn boatr-btn boatr-btn-primary" onclick="downloadFile('${fileUrl}', '${fileName}')">
                                            <i class="fas fa-download me-2"></i>Download PDF
                                        </button>
                                    </div>
                                    <div class="boatr-document-info">
                                        <p class="boatr-file-name">File: ${fileName}</p>
                                    </div>
                                </div>
                            `;
                            }
                        }, 2000);

                    } else if (videoTypes.includes(fileExtension)) {
                        document.getElementById('documentPreview').innerHTML = `
                        <div class="text-center">
                            <video controls class="w-100 rounded shadow" style="max-height: 70vh;" preload="metadata">
                                <source src="${fileUrl}" type="video/${fileExtension}">
                                Your browser does not support the video tag.
                            </video>
                            ${addActionButtons()}
                        </div>
                    `;

                    } else if (audioTypes.includes(fileExtension)) {
                        document.getElementById('documentPreview').innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-music fa-4x text-info mb-3"></i>
                            <h5>${fileName}</h5>
                            <audio controls class="w-100 mb-3">
                                <source src="${fileUrl}" type="audio/${fileExtension}">
                                Your browser does not support the audio tag.
                            </audio>
                            ${addActionButtons()}
                        </div>
                    `;

                    } else if (documentTypes.includes(fileExtension)) {
                        const docIcon = ['doc', 'docx'].includes(fileExtension) ? 'file-word' : 'file-alt';
                        document.getElementById('documentPreview').innerHTML = `
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
                        </div>
                    `;
                    } else {
                        document.getElementById('documentPreview').innerHTML = `
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-file fa-4x text-warning mb-3"></i>
                            <h5>Unsupported File Type</h5>
                            <p class="mb-3">File type ".${fileExtension}" is not supported for preview.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt me-2"></i>Open File
                                </a>
                                <a href="${fileUrl}" download="${fileName}" class="btn btn-success">
                                    <i class="fas fa-download me-2"></i>Download
                                </a>
                            </div>
                            <small class="text-muted d-block mt-2">File: ${fileName}</small>
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

        function toggleImageZoomBoatr(img) {
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

        function downloadFile(url, filename) {
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Utility function to format file sizes
        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return 'Unknown size';

            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }


        // ========== ANNEXES MANAGEMENT ==========
        /**
         * Show Annexes Modal
         */
        function showAnnexesModal(id) {
            const modal = new bootstrap.Modal(document.getElementById('annexesModal'));
            modal.show();

            // Show loading
            document.getElementById('annexesLoading').style.display = 'block';
            document.getElementById('annexesContent').style.display = 'none';

            // Load registration details and annexes
            loadBoatrAnnexesData(id);
        }

        /**
         * Load BoatR Annexes Data
         */
        function loadBoatrAnnexesData(id) {
            fetch(`/admin/boatr/requests/${id}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken()
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Failed to load data');

                    // Hide loading, show content
                    document.getElementById('annexesLoading').style.display = 'none';
                    document.getElementById('annexesContent').style.display = 'block';

                    // Populate registration info
                    document.getElementById('annexRegistrationId').value = id;
                    document.getElementById('annexAppNumber').textContent = data.application_number;
                    document.getElementById('annexApplicantName').textContent = data.full_name;
                    document.getElementById('annexBarangay').textContent = data.barangay || 'N/A';
                    document.getElementById('annexStatus').innerHTML =
                        `<span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;

                    // Load existing annexes
                    loadBoatrExistingAnnexes(id);

                    // Reset form
                    resetBoatrAnnexForm();
                })
                .catch(error => {
                    console.error('Error loading annexes data:', error);
                    showToast('error', 'Failed to load data: ' + error.message);

                    document.getElementById('annexesLoading').style.display = 'none';
                    document.getElementById('annexesContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading data: ${error.message}
                        </div>
                    `;
                    document.getElementById('annexesContent').style.display = 'block';
                });
        }

        /**
         * Load Existing BoatR Annexes
         */
        function loadBoatrExistingAnnexes(id) {
            fetch(`/admin/boatr/requests/${id}/annexes`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken()
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const annexesList = document.getElementById('annexesList');

                    if (data.success && data.annexes && data.annexes.length > 0) {
                        let annexesHtml = '';
                        data.annexes.forEach((annex) => {
                            const uploadDate = new Date(annex.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            // Store annex data in data attribute
                            const filePath = annex.file_path ? String(annex.file_path).trim() : '';
                            const annexDataJson = JSON.stringify({
                                id: annex.id,
                                registrationId: id,
                                filePath: filePath,
                                fileName: annex.file_name || annex.title || 'Document',
                                title: annex.title,
                                fileExtension: annex.file_extension
                            });

                            annexesHtml += `
                                <div class="annex-item border rounded p-3 mb-3" 
                                    id="annex-${annex.id}" 
                                    data-annex-json='${annexDataJson}'>
                                    <div class="annex-item-content">
                                        <h6 class="annex-title">${escapeHtml(annex.title)}</h6>
                                        <p class="annex-description">${escapeHtml(annex.description || 'No description')}</p>
                                        <div class="annex-meta">
                                            <div class="annex-meta-item">
                                                <i class="fas fa-clock"></i>
                                                <span>${uploadDate}</span>
                                            </div>
                                            <div class="annex-meta-item">
                                                <i class="fas fa-file"></i>
                                                <span>${formatFileSize(annex.file_size)}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="annex-item-actions">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="previewBoatrAnnexFixed(${annex.id})" 
                                                title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="downloadBoatrAnnexFixed(${annex.id})" 
                                                title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteBoatrAnnex(${id}, ${annex.id})" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        annexesList.innerHTML = annexesHtml;
                    } else {
                        annexesList.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No annexes uploaded yet</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading annexes:', error);
                    showToast('error', 'Error loading annexes: ' + error.message);
                    document.getElementById('annexesList').innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading annexes: ${error.message}
                        </div>
                    `;
                });
        }
        // preview BoatR Annex
        function previewBoatrAnnexFixed(annexId) {
            try {
                const annexElement = document.getElementById(`annex-${annexId}`);
                
                if (!annexElement) {
                    showToast('error', 'Annex not found');
                    return;
                }
                
                const annexDataJson = annexElement.getAttribute('data-annex-json');
                if (!annexDataJson) {
                    showToast('error', 'Annex data not found');
                    return;
                }

                let annexData;
                try {
                    annexData = JSON.parse(annexDataJson);
                } catch (e) {
                    console.error('Error parsing annex data JSON:', e);
                    showToast('error', 'Error reading annex data');
                    return;
                }

                const filePath = annexData.filePath;
                const fileName = annexData.fileName || annexData.title || 'Document';
                
                if (!filePath || filePath === 'undefined' || filePath === '') {
                    console.error('File path is missing:', annexData);
                    showToast('error', 'File path not available for this annex');
                    return;
                }
                
                console.log('Preview BoatR annex:', { annexId, filePath, fileName });
                
                // Open modal and display directly
                const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
                modal.show();
                
                const fileUrl = `/storage/${filePath}`;
                const fileExtension = fileName.split('.').pop().toLowerCase();
                
                // Preview the file
                previewFileContent(fileUrl, fileName, fileExtension);

            } catch (error) {
                console.error('Error in previewBoatrAnnexFixed:', error);
                showToast('error', 'Error previewing annex: ' + error.message);
            }
        }

        // Helper function to preview file content
        function previewFileContent(fileUrl, fileName, fileExtension) {
            const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            const pdfTypes = ['pdf'];
            
            if (imageTypes.includes(fileExtension)) {
                document.getElementById('documentPreview').innerHTML = `
                    <div class="text-center">
                        <img src="${fileUrl}" alt="Preview" style="max-width: 100%; max-height: 70vh; border-radius: 8px;">
                        <div style="margin-top: 20px;">
                            <a href="${fileUrl}" download="${fileName}" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Download
                            </a>
                        </div>
                    </div>
                `;
            } else if (pdfTypes.includes(fileExtension)) {
                document.getElementById('documentPreview').innerHTML = `
                    <embed src="${fileUrl}" type="application/pdf" width="100%" height="600px;" style="border-radius: 8px;">
                `;
            } else {
                document.getElementById('documentPreview').innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-file fa-4x text-muted mb-3"></i>
                        <p>Preview not available for this file type.</p>
                        <a href="${fileUrl}" download="${fileName}" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Download ${fileExtension.toUpperCase()}
                        </a>
                    </div>
                `;
            }
            
            document.getElementById('documentPreviewTitle').innerHTML = `<i class="fas fa-eye me-2"></i>${fileName}`;
        }

        /**
        * Download BoatR Annex
        */
        function downloadBoatrAnnexFixed(annexId) {
            try {
                const annexElement = document.getElementById(`annex-${annexId}`);
                
                if (!annexElement) {
                    showToast('error', 'Annex not found');
                    return;
                }
                
                const annexDataJson = annexElement.getAttribute('data-annex-json');
                if (!annexDataJson) {
                    showToast('error', 'Annex data not found');
                    return;
                }

                let annexData;
                try {
                    annexData = JSON.parse(annexDataJson);
                } catch (e) {
                    console.error('Error parsing annex data JSON:', e);
                    showToast('error', 'Error reading annex data');
                    return;
                }

                const filePath = annexData.filePath;
                const fileName = annexData.fileName || annexData.title || 'Document';
                
                if (!filePath || filePath === 'undefined' || filePath === '') {
                    console.error('File path is missing:', annexData);
                    showToast('error', 'File path not available for this annex');
                    return;
                }

                console.log('Download BoatR annex:', { annexId, filePath, fileName });

                showConfirmationToast(
                    'Download File',
                    `Download: ${fileName}?`,
                    () => proceedWithBoatrAnnexDownload(filePath, fileName)
                );

            } catch (error) {
                console.error('Error in downloadBoatrAnnexFixed:', error);
                showToast('error', 'Error downloading annex: ' + error.message);
            }
        }

        /**
        * Proceed with BoatR Annex Download
        */
        function proceedWithBoatrAnnexDownload(filePath, fileName) {
            const fileUrl = `/storage/${filePath}`;
            const link = document.createElement('a');
            link.href = fileUrl;
            link.download = fileName;
            link.target = '_blank';
            document.body.appendChild(link);

            try {
                link.click();
                showToast('success', 'File download started');
            } catch (error) {
                console.error('Download error:', error);
                showToast('error', 'Failed to download file');
            } finally {
                document.body.removeChild(link);
            }
        }

        /**
        * Delete BoatR Annex
        */
        function deleteBoatrAnnex(registrationId, annexId) {
            showConfirmationToast(
                'Delete Annex',
                `Are you sure you want to delete this annex?\n\nThis action cannot be undone.`,
                () => proceedWithBoatrAnnexDelete(registrationId, annexId)
            );
        }

        /**
        * Proceed with BoatR Annex Deletion
        */
        function proceedWithBoatrAnnexDelete(registrationId, annexId) {
            fetch(`/admin/boatr/requests/${registrationId}/annexes/${annexId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Failed to delete annex');

                    showToast('success', 'Annex deleted successfully');

                    const annexElement = document.getElementById(`annex-${annexId}`);
                    if (annexElement) {
                        annexElement.style.transition = 'opacity 0.3s ease';
                        annexElement.style.opacity = '0';
                        setTimeout(() => {
                            annexElement.remove();
                            const annexesList = document.getElementById('annexesList');
                            if (!annexesList.querySelector('.annex-item')) {
                                loadBoatrExistingAnnexes(document.getElementById('annexRegistrationId').value);
                            }
                        }, 300);
                    }

                    setTimeout(() => window.location.reload(), 1500);
                })
                .catch(error => {
                    console.error('Error deleting annex:', error);
                    showToast('error', 'Failed to delete annex: ' + error.message);
                });
        }

        /**
        * Upload BoatR Annex
        */
        function uploadAnnex() {
            const id = document.getElementById('annexRegistrationId').value;
            const fileInput = document.getElementById('annexFile');
            const title = document.getElementById('annexTitle').value.trim();
            const description = document.getElementById('annexDescription').value.trim();

            // Validation
            if (!fileInput.files[0]) {
                showValidationError('annexFile', 'annexFileError', 'Please select a file');
                return;
            }

            if (!title) {
                showValidationError('annexTitle', 'annexTitleError', 'Please enter a document title');
                return;
            }

            const maxFileSize = 10 * 1024 * 1024;
            if (fileInput.files[0].size > maxFileSize) {
                showValidationError('annexFile', 'annexFileError', 'File size must be less than 10MB');
                return;
            }

            const allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'];
            const fileExtension = fileInput.files[0].name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                showValidationError('annexFile', 'annexFileError', 'File type not allowed');
                return;
            }

            clearValidationErrors();

            showConfirmationToast(
                'Upload Annex',
                `Upload: ${fileInput.files[0].name}?\nSize: ${formatFileSize(fileInput.files[0].size)}`,
                () => proceedWithBoatrAnnexUpload(id, fileInput, title, description)
            );
        }

        /**
        * Proceed with BoatR Annex Upload
        */
        function proceedWithBoatrAnnexUpload(id, fileInput, title, description) {
            const uploadBtn = document.querySelector('[onclick="uploadAnnex()"]');
            const originalContent = uploadBtn.innerHTML;
            uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
            uploadBtn.disabled = true;

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('title', title);
            formData.append('description', description);

            fetch(`/admin/boatr/requests/${id}/annexes`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Annex uploaded successfully');
                        resetBoatrAnnexForm();
                        loadBoatrExistingAnnexes(id);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Failed to upload annex');
                    }
                })
                .catch(error => {
                    console.error('Error uploading annex:', error);
                    showToast('error', 'Failed to upload annex: ' + error.message);
                })
                .finally(() => {
                    uploadBtn.innerHTML = originalContent;
                    uploadBtn.disabled = false;
                });
        }

        /**
        * Reset BoatR Annex Form
        */
        function resetBoatrAnnexForm() {
            document.getElementById('annexFile').value = '';
            document.getElementById('annexTitle').value = '';
            document.getElementById('annexDescription').value = '';
            document.getElementById('annexDescCount').textContent = '0';
            clearValidationErrors();
        }

        /**
        * Update Annex Description Counter
        */
        function updateAnnexDescriptionCounter() {
            const textarea = document.getElementById('annexDescription');
            const counter = document.getElementById('annexDescCount');

            if (textarea && counter) {
                const charCount = textarea.value.length;
                counter.textContent = charCount;

                if (charCount > 500) {
                    textarea.value = textarea.value.substring(0, 500);
                    counter.textContent = '500';
                }

                if (charCount > 450) {
                    counter.parentElement.classList.add('text-warning');
                    counter.parentElement.classList.remove('text-muted');
                } else {
                    counter.parentElement.classList.remove('text-warning');
                    counter.parentElement.classList.add('text-muted');
                }
            }
        }

        /**
        * Show Validation Error
        */
        function showValidationError(inputId, errorId, message) {
            const input = document.getElementById(inputId);
            const error = document.getElementById(errorId);

            input.classList.add('is-invalid');
            error.textContent = message;
        }

        /**
        * Clear Validation Errors
        */
        function clearValidationErrors() {
            const inputs = ['annexFile', 'annexTitle'];
            const errors = ['annexFileError', 'annexTitleError'];

            inputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) input.classList.remove('is-invalid');
            });

            errors.forEach(errorId => {
                const error = document.getElementById(errorId);
                if (error) error.textContent = '';
            });
        }

        /**
        * Format File Size
        */
        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return 'Unknown size';
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }

        /**
        * Escape HTML
        */
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
        // ========== DATE FILTER FUNCTIONS ==========
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
                showToast('warning', 'From date cannot be later than To date');
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

        // ========== TABLE UPDATE ==========
        function updateTableRow(id, registration) {
            try {
                const row = document.getElementById(`registration-${id}`);
                if (!row) {
                    console.warn(`Row with ID registration-${id} not found`);
                    return;
                }

                const statusBadge = document.getElementById(`status-badge-${id}`);
                if (statusBadge) {
                    statusBadge.className = `badge bg-${registration.status_color} fs-6`;
                    statusBadge.textContent = registration.formatted_status;
                }

                const inspectionBadge = document.getElementById(`inspection-badge-${id}`);
                if (inspectionBadge) {
                    if (registration.inspection_completed) {
                        inspectionBadge.className = 'badge bg-success';
                        inspectionBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i>Completed';
                    } else {
                        inspectionBadge.className = 'badge bg-warning';
                        inspectionBadge.innerHTML = '<i class="fas fa-clock me-1"></i>Pending';
                    }
                }

                row.classList.add('row-updated');
                setTimeout(() => row.classList.remove('row-updated'), 2000);
            } catch (error) {
                console.error('Error updating table row:', error);
            }
        }

        // ========== FILE UPLOAD VALIDATION ==========
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('supporting_document');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    const errorElement = document.getElementById('documentError');

                    this.classList.remove('is-invalid');
                    errorElement.textContent = '';

                    if (file) {
                        if (file.size > 10 * 1024 * 1024) {
                            this.classList.add('is-invalid');
                            errorElement.textContent = 'File size must be less than 10MB';
                            return;
                        }

                        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                        if (!allowedTypes.includes(file.type)) {
                            this.classList.add('is-invalid');
                            errorElement.textContent = 'Only PDF, JPG, JPEG, and PNG files are allowed';
                            return;
                        }

                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        errorElement.className = 'text-success small';
                        errorElement.textContent = `Selected: ${file.name} (${fileSize} MB)`;
                    }
                });
            }
        });

        // ========== KEYBOARD SHORTCUTS ==========
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal.show');
                modals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            }
        });

        // ========== INITIALIZATION ==========
        console.log('✅ BoatR Admin JavaScript loaded successfully');

        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Global window object for debugging
        window.BoatRAdmin = {
            viewDocuments,
            previewDocument,
            viewRegistration,
            formatFileSize,
            showAnnexesModal,
            uploadAnnex,
            previewAnnex,
            downloadAnnex,
            deleteAnnex,
            updateRegistrationStatus,
            completeInspection
        };


        // Real-time validation for contact number
        document.getElementById('boatr_contact_number')?.addEventListener('input', function() {
            validateBoatrContactNumber(this.value);
        });

        function validateBoatrContactNumber(contactNumber) {
            const input = document.getElementById('boatr_contact_number');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            if (feedback) feedback.textContent = '';
            input.classList.remove('is-invalid', 'is-valid');

            if (!contactNumber || contactNumber.trim() === '') {
                return;
            }

            const phoneRegex = /^(\+639|09)\d{9}$/;

            if (!phoneRegex.test(contactNumber.trim())) {
                input.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)';
                }
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }

        // Auto-capitalize name fields
        function capitalizeBoatrName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        }

        document.getElementById('boatr_first_name')?.addEventListener('blur', function() {
            capitalizeBoatrName(this);
        });

        document.getElementById('boatr_middle_name')?.addEventListener('blur', function() {
            capitalizeBoatrName(this);
        });

        document.getElementById('boatr_last_name')?.addEventListener('blur', function() {
            capitalizeBoatrName(this);
        });

        // Document preview
        function previewBoatrDocument(inputId, previewId) {
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

            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                showToast('error', 'File size must not exceed 10MB');
                input.value = '';
                if (preview) {
                    preview.innerHTML = '';
                    preview.style.display = 'none';
                }
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                if (preview) {
                    if (file.type.startsWith('image/')) {
                        preview.innerHTML = `
                            <div class="document-preview-item">
                                <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <p style="margin-top: 8px; font-size: 12px; color: #666;">
                                    <i class="fas fa-file-image me-1"></i>${file.name} (${formatFileSize(file.size)})
                                </p>
                            </div>
                        `;
                    } else {
                        preview.innerHTML = `
                            <div class="document-preview-item">
                                <div class="text-center p-3 border rounded">
                                    <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                    <p style="margin-top: 8px; font-size: 12px; color: #666;">${file.name} (${formatFileSize(file.size)})</p>
                                </div>
                            </div>
                        `;
                    }
                    preview.style.display = 'block';
                }
            };

            reader.readAsDataURL(file);
        }
        // submit add boatr form
  async function submitAddBoatr() {
    console.log('=== submitAddBoatr START ===');
    
    // Remove any old validation errors first
    document.querySelectorAll('#addBoatrModal .is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('#addBoatrModal .invalid-feedback').forEach(el => el.textContent = '');

    // Validate form
    const isValid = await validateBoatrForm();
    console.log('Form validation result:', isValid);
    
    if (!isValid) {
        showToast('error', 'Please fix all validation errors before submitting');
        return;
    }

    // Prepare form data
    const formData = new FormData();

    const firstNameVal = document.getElementById('boatr_first_name').value.trim();
    const lastNameVal = document.getElementById('boatr_last_name').value.trim();
    const contactVal = document.getElementById('boatr_contact_number').value.trim();
    const barangayVal = document.getElementById('boatr_barangay').value.trim();
    const fishrVal = document.getElementById('boatr_fishr_number').value.trim();
    const vesselVal = document.getElementById('boatr_vessel_name').value.trim();
    const boatTypeVal = document.getElementById('boatr_boat_type').value.trim();
    const lengthVal = document.getElementById('boatr_boat_length').value.trim();
    const widthVal = document.getElementById('boatr_boat_width').value.trim();
    const depthVal = document.getElementById('boatr_boat_depth').value.trim();
    const engineTypeVal = document.getElementById('boatr_engine_type').value.trim();
    const engineHpVal = document.getElementById('boatr_engine_horsepower').value.trim();
    const gearVal = document.getElementById('boatr_primary_fishing_gear').value.trim();
    const statusVal = document.getElementById('boatr_status').value.trim();
    const remarksVal = document.getElementById('boatr_remarks').value.trim();
    const fishrAppIdVal = document.getElementById('boatr_fishr_app_id').value.trim();

    console.log('Form values:', {
        firstName: firstNameVal,
        lastName: lastNameVal,
        contact: contactVal,
        barangay: barangayVal,
        fishr: fishrVal,
        vessel: vesselVal,
        boatType: boatTypeVal,
        length: lengthVal,
        width: widthVal,
        depth: depthVal,
        engineType: engineTypeVal,
        engineHp: engineHpVal,
        gear: gearVal,
        status: statusVal,
        fishrAppId: fishrAppIdVal
    });

    formData.append('first_name', firstNameVal);
    formData.append('middle_name', document.getElementById('boatr_middle_name').value.trim());
    formData.append('last_name', lastNameVal);
    formData.append('name_extension', document.getElementById('boatr_name_extension').value || '');
    formData.append('contact_number', contactVal);
    formData.append('barangay', barangayVal);
    formData.append('fishr_number', fishrVal);
    formData.append('vessel_name', vesselVal);
    formData.append('boat_type', boatTypeVal);
    formData.append('boat_length', parseFloat(lengthVal) || 0);
    formData.append('boat_width', parseFloat(widthVal) || 0);
    formData.append('boat_depth', parseFloat(depthVal) || 0);
    formData.append('engine_type', engineTypeVal);
    formData.append('engine_horsepower', parseInt(engineHpVal) || 0);
    formData.append('primary_fishing_gear', gearVal);
    formData.append('status', statusVal || 'pending');
    formData.append('remarks', remarksVal);
    
    // IMPORTANT: Add fishr_application_id if it was validated
    if (fishrAppIdVal) {
        formData.append('fishr_application_id', fishrAppIdVal);
    }

    // Add document if uploaded
    const docInput = document.getElementById('boatr_user_document');
    if (docInput && docInput.files && docInput.files[0]) {
        console.log('File to upload:', docInput.files[0].name);
        formData.append('user_document', docInput.files[0]);
    }

    // Find submit button
    const submitBtn = document.querySelector('#addBoatrModal .modal-footer .btn-primary');
    if (!submitBtn) {
        showToast('error', 'Submit button not found');
        return;
    }

    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
    submitBtn.disabled = true;

    console.log('Sending request to:', '/admin/boatr/requests/create');

    // Submit to backend
    fetch('/admin/boatr/requests/create', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || getCSRFToken(),
                'Accept': 'application/json'
                // NOTE: Do NOT set Content-Type with FormData, browser will set it automatically
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            return response.json().then(data => ({
                status: response.status,
                ok: response.ok,
                data: data
            }));
        })
        .then(({status, ok, data}) => {
            console.log('Response data:', data);

            if (ok && data.success) {
                // IMPORTANT: Close modal BEFORE showing success message
                const modalElement = document.getElementById('addBoatrModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }

                // Clear form
                document.getElementById('addBoatrForm').reset();
                document.getElementById('boatr_fishr_app_id').value = '';

                // Show success message
                showToast('success', data.message || 'BoatR application created successfully');

                console.log('Success! Reloading page in 2 seconds...');

                // CRITICAL: Wait longer to ensure modal is fully closed and database is committed
                setTimeout(() => {
                    console.log('Performing full page reload...');
                    window.location.href = window.location.href;
                }, 2000);

            } else {
                // Show validation errors
                console.log('Validation errors:', data.errors);
                
                if (data.errors && typeof data.errors === 'object') {
                    Object.keys(data.errors).forEach(field => {
                        const fieldName = field.replace(/_/g, '-');
                        const input = document.getElementById('boatr_' + fieldName) || 
                                    document.getElementById('boatr-' + fieldName);
                        
                        if (input) {
                            input.classList.add('is-invalid');
                            const errorDiv = input.parentNode.querySelector('.invalid-feedback') || 
                                        document.createElement('div');
                            errorDiv.className = 'invalid-feedback d-block';
                            errorDiv.textContent = Array.isArray(data.errors[field]) ? 
                                                data.errors[field][0] : 
                                                data.errors[field];
                            
                            if (!input.parentNode.querySelector('.invalid-feedback')) {
                                input.parentNode.appendChild(errorDiv);
                            }
                        }
                    });
                }
                showToast('error', data.message || 'Failed to create BoatR application');
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showToast('error', 'An error occurred while creating the application: ' + error.message);
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
}
        // ========== MODAL BACKDROP CLEANUP ==========
        document.addEventListener('DOMContentLoaded', function() {
            const allModals = document.querySelectorAll('.modal');

            allModals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {

                // Remove old tooltip/popover instances
                    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                        const instance = bootstrap.Tooltip.getInstance(el);
                        if (instance) instance.dispose();
                    });
                    
                    // Clean up dropdowns
                    document.querySelectorAll('.dropdown-toggle').forEach(el => {
                        const instance = bootstrap.Dropdown.getInstance(el);
                        if (instance) instance.dispose();
                    });
                    // Remove all modal backdrops
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop
                        .remove());

                    // Check if any modals are still open
                    const openModals = document.querySelectorAll('.modal.show');

                    if (openModals.length === 0) {
                        // No modals open, clean up body
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }
                });
            });
        });

        // View documents by type (user, inspection, or annexes only)
        function viewDocumentsByType(id, type) {
            const modal = new bootstrap.Modal(document.getElementById('documentModal'));
            modal.show();

            document.getElementById('documentViewerLoading').style.display = 'block';
            document.getElementById('documentViewer').style.display = 'none';

            fetch(`/admin/boatr/requests/${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken()
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Failed to load documents');

                    document.getElementById('documentViewerLoading').style.display = 'none';
                    document.getElementById('documentViewer').style.display = 'block';

                    let documentsHtml = '';

                    // Show only the requested type
                    if (type === 'user' && data.user_documents && data.user_documents.length > 0) {
                        documentsHtml += `
                        <div class="document-section mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-file-image text-info me-2"></i>User Documents
                            </h5>
                    `;
                        data.user_documents.forEach((doc, index) => {
                            documentsHtml += `
                            <div class="document-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${doc.name || 'User Document ' + (index + 1)}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>${doc.uploaded_at || 'N/A'}
                                        </small>
                                    </div>
                                    <button class="btn btn-sm btn-primary" onclick="previewDocument(${id}, 'user', ${index})">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                </div>
                            </div>
                        `;
                        });
                        documentsHtml += `</div>`;
                    }

                    if (type === 'inspection' && data.inspection_documents && data.inspection_documents.length > 0) {
                        documentsHtml += `
                        <div class="document-section mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-clipboard-check text-success me-2"></i>Inspection Documents
                            </h5>
                    `;
                        data.inspection_documents.forEach((doc, index) => {
                            documentsHtml += `
                            <div class="document-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${doc.name || 'Inspection Document ' + (index + 1)}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>${doc.uploaded_at || 'N/A'}
                                        </small>
                                    </div>
                                    <button class="btn btn-sm btn-primary" onclick="previewDocument(${id}, 'inspection', ${index})">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                </div>
                            </div>
                        `;
                        });
                        documentsHtml += `</div>`;
                    }

                    if (type === 'annexes' && data.annexes && data.annexes.length > 0) {
                        documentsHtml += `
                        <div class="document-section mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-folder text-warning me-2"></i>Annexes
                            </h5>
                    `;
                        data.annexes.forEach((annex) => {
                            documentsHtml += `
                            <div class="document-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${annex.title}</h6>
                                        <p class="mb-1 text-muted small">${annex.description || 'No description'}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>${annex.created_at || 'N/A'}
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-file me-1"></i>${formatFileSize(annex.file_size)}
                                        </small>
                                    </div>
                                    <button class="btn btn-sm btn-primary" onclick="previewAnnex(${id}, ${annex.id})">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                </div>
                            </div>
                        `;
                        });
                        documentsHtml += `</div>`;
                    }

                    if (!documentsHtml) {
                        documentsHtml = `
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No documents available in this category</p>
                        </div>
                    `;
                    }

                    document.getElementById('documentViewer').innerHTML = documentsHtml;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('documentViewerLoading').style.display = 'none';
                    document.getElementById('documentViewer').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load documents: ${error.message}
                    </div>
                `;
                    document.getElementById('documentViewer').style.display = 'block';
                });
        }

        // Validate FishR number exists and auto-fill data
        async function validateFishrNumber(fishrNumber) {
            const input = document.getElementById('boatr_fishr_number');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            if (feedback) feedback.textContent = '';
            input.classList.remove('is-invalid', 'is-valid');

            if (!fishrNumber || fishrNumber.trim() === '') {
                return false;
            }

            try {
                // Use the correct endpoint: /admin/boatr/validate-fishr
                const endpoint = `/admin/boatr/validate-fishr/${encodeURIComponent(fishrNumber.trim())}`;
                console.log('Validating FishR at endpoint:', endpoint);

                const response = await fetch(endpoint, {
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('FishR validation response:', data);

                if (data.exists) {
                    input.classList.add('is-valid');
                    if (feedback) {
                        feedback.classList.remove('invalid-feedback');
                        feedback.classList.add('valid-feedback', 'd-block');
                        feedback.textContent = `✓ Valid FishR: ${data.fisher_name}`;
                        feedback.style.color = '#28a745';
                    }

                    // Auto-fill fisher information
                    if (data.first_name) document.getElementById('boatr_first_name').value = data.first_name;
                    if (data.middle_name) document.getElementById('boatr_middle_name').value = data.middle_name;
                    if (data.last_name) document.getElementById('boatr_last_name').value = data.last_name;
                    if (data.name_extension) document.getElementById('boatr_name_extension').value = data
                        .name_extension;
                    if (data.contact_number) document.getElementById('boatr_contact_number').value = data
                        .contact_number;
                    if (data.barangay) document.getElementById('boatr_barangay').value = data.barangay;
                    if (data.fishr_app_id) document.getElementById('boatr_fishr_app_id').value = data.fishr_app_id;

                    showToast('success', `✓ Fisher information auto-filled for: ${data.fisher_name}`);
                    return true;
                } else {
                    input.classList.add('is-invalid');
                    if (feedback) {
                        feedback.textContent = data.message ||
                            'FishR number not found in the system. Please verify the FishR number.';
                    }
                    clearFisherFields();
                    return false;
                }
            } catch (error) {
                console.error('Error validating FishR:', error);
                input.classList.add('is-invalid');
                if (feedback) {
                    feedback.textContent = 'Error validating FishR number. Please check your connection and try again.';
                }
                showToast('error', 'Error validating FishR: ' + error.message);
                return false;
            }
        }


        // Clear fisher fields helper function
        function clearFisherFields() {
            document.getElementById('boatr_first_name').value = '';
            document.getElementById('boatr_middle_name').value = '';
            document.getElementById('boatr_last_name').value = '';
            document.getElementById('boatr_name_extension').value = '';
            document.getElementById('boatr_contact_number').value = '';
            document.getElementById('boatr_fishr_app_id').value = '';
            // Don't clear barangay as it might be manually set
        }

        /**
         * Initialize admin FishR validation when modal is shown
         */
        document.addEventListener('DOMContentLoaded', function() {
            const addBoatrModal = document.getElementById('addBoatrModal');

            if (addBoatrModal) {
                // Show event - initializes when modal opens
                addBoatrModal.addEventListener('shown.bs.modal', function() {
                    console.log('BoatR modal shown - initializing FishR validation');
                    initializeAdminFishRValidation();
                });

                // Hide event - cleans up when modal closes
                addBoatrModal.addEventListener('hidden.bs.modal', function() {
                    console.log('BoatR modal hidden - cleaning up');
                    cleanupFishrValidation();
                });
            }
        });

        /**
         * Initialize FishR validation for admin add form
         */
        function initializeAdminFishRValidation() {
            const fishrInput = document.getElementById('boatr_fishr_number');

            if (!fishrInput) {
                console.error('FishR input not found');
                return;
            }

            console.log('Setting up FishR event listeners');

            // Remove old listeners to prevent duplicates
            fishrInput.removeEventListener('blur', handleAdminFishRBlur);
            fishrInput.removeEventListener('input', handleAdminFishRInput);
            fishrInput.removeEventListener('focus', handleAdminFishRFocus);

            // Add new listeners
            fishrInput.addEventListener('input', handleAdminFishRInput);
            fishrInput.addEventListener('blur', handleAdminFishRBlur);
            fishrInput.addEventListener('focus', handleAdminFishRFocus);
        }

        /**
         * Clean up FishR validation
         */
        function cleanupFishrValidation() {
            const fishrInput = document.getElementById('boatr_fishr_number');
            if (fishrInput) {
                fishrInput.removeEventListener('blur', handleAdminFishRBlur);
                fishrInput.removeEventListener('input', handleAdminFishRInput);
                fishrInput.removeEventListener('focus', handleAdminFishRFocus);

                if (fishrInput.validationTimeout) {
                    clearTimeout(fishrInput.validationTimeout);
                }
            }
        }

        /**
         * Handle FishR input - real-time formatting
         */
        function handleAdminFishRInput(event) {
            const input = event.target;
            let value = input.value.trim().toUpperCase();

            // Auto-format: add FISHR- prefix if not present
            if (value && !value.startsWith('FISHR-')) {
                value = 'FISHR-' + value;
            }

            input.value = value;
            clearAdminValidationMessage(input);

            console.log('FishR input updated:', value);
        }

        /**
         * Handle FishR blur - trigger validation
         */
        function handleAdminFishRBlur(event) {
            const input = event.target;
            const value = input.value.trim();

            console.log('FishR blur event, value:', value);

            if (!value) {
                clearAdminValidationMessage(input);
                input.classList.remove('is-invalid', 'is-valid');
                return;
            }

            // Validate format: FISHR-XXXXXXXX (8 alphanumeric characters)
            const formatValid = /^FISHR-[A-Z0-9]{8}$/i.test(value);

            if (!formatValid) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                showAdminValidationMessage(input, 'Invalid format. Use: FISHR-XXXXXXXX', 'error');
                console.log('Invalid format');
                return;
            }

            console.log('Format valid, checking if exists in database...');

            // Format is valid, check if exists in database
            input.classList.remove('is-invalid', 'is-valid');
            showAdminValidationMessage(input, '🔄 Checking FishR registration...', 'warning');

            // Validate asynchronously - pass the full value with FISHR- prefix
            validateAdminFishRNumber(input, value);
        }
        /**
         * Handle FishR focus - show help text
         */
        function handleAdminFishRFocus(event) {
            const input = event.target;
            if (!input.value.trim()) {
                showAdminValidationMessage(input, 'Format: FISHR-XXXXXXXX', 'info');
            }
        }

        /**
         * Validate FishR number with database (async)
         */
        async function validateAdminFishRNumber(input, fishrNumber) {
            try {
                // URL encode the full registration number (with FISHR- prefix)
                const encodedFishrNumber = encodeURIComponent(fishrNumber);
                const endpoint = `/admin/boatr/validate-fishr/${encodedFishrNumber}`;
                console.log('Fetching from:', endpoint);

                const response = await fetch(endpoint, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    console.error('Server error:', response.status);
                    throw new Error(`Server error ${response.status}`);
                }

                const data = await response.json();
                console.log('FishR validation result:', data);

                if (data.exists) {
                    // ✅ Valid FishR found
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    showAdminValidationMessage(input, `✓ Valid: ${data.fisher_name}`, 'success');

                    // Auto-fill the form
                    autoFillAdminFisherInfo(data);

                    showToast('success', `✓ Auto-filled: ${data.fisher_name}`);
                } else {
                    // ❌ FishR not found
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                    showAdminValidationMessage(input, '❌ ' + (data.message || 'FishR not found in system'), 'error');

                    clearAdminFisherFields();
                    showToast('warning', data.message || 'Registration number not found');
                }
            } catch (error) {
                console.error('Validation error:', error);

                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                showAdminValidationMessage(input, '⚠️ Connection error. Check your internet.', 'error');

                showToast('error', 'Connection error: ' + error.message);
            }
        }

        /**
         * Auto-fill admin form with Fisher info
         */
        function autoFillAdminFisherInfo(data) {
            try {
                const fieldMap = {
                    'boatr_first_name': 'first_name',
                    'boatr_middle_name': 'middle_name',
                    'boatr_last_name': 'last_name',
                    'boatr_name_extension': 'name_extension',
                    'boatr_contact_number': 'contact_number',
                    'boatr_barangay': 'barangay',
                    'boatr_fishr_app_id': 'fishr_app_id'
                };

                Object.keys(fieldMap).forEach(fieldId => {
                    const dataKey = fieldMap[fieldId];
                    const element = document.getElementById(fieldId);

                    if (element && data[dataKey]) {
                        element.value = data[dataKey];
                        console.log(`Filled ${fieldId} with ${data[dataKey]}`);
                    }
                });
            } catch (error) {
                console.error('Auto-fill error:', error);
            }
        }

        /**
         * Show admin validation message
         */
        function showAdminValidationMessage(input, message, type) {
            clearAdminValidationMessage(input);

            const messageDiv = document.createElement('div');
            messageDiv.className = `admin-validation-message admin-validation-${type}`;
            messageDiv.style.cssText = `
                display: block;
                margin-top: 5px;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 13px;
            `;

            // Set colors
            const colors = {
                success: {
                    bg: '#d4edda',
                    border: '#28a745',
                    text: '#155724'
                },
                error: {
                    bg: '#f8d7da',
                    border: '#dc3545',
                    text: '#721c24'
                },
                warning: {
                    bg: '#fff3cd',
                    border: '#ffc107',
                    text: '#856404'
                },
                info: {
                    bg: '#d1ecf1',
                    border: '#17a2b8',
                    text: '#0c5460'
                }
            };

            const color = colors[type] || colors.info;
            messageDiv.style.backgroundColor = color.bg;
            messageDiv.style.borderColor = color.border;
            messageDiv.style.color = color.text;
            messageDiv.innerHTML = `<small>${message}</small>`;

            input.parentNode.insertBefore(messageDiv, input.nextSibling);

            // Auto-remove success/info after 4 seconds
            if (type === 'success' || type === 'info') {
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.remove();
                    }
                }, 4000);
            }
        }

        /**
         * Clear admin validation message
         */
        function clearAdminValidationMessage(input) {
            const existingMessage = input.parentNode.querySelector('.admin-validation-message');
            if (existingMessage) {
                existingMessage.remove();
            }
        }

        /**
         * Clear admin fisher fields
         */
        function clearAdminFisherFields() {
            const fields = [
                'boatr_first_name',
                'boatr_middle_name',
                'boatr_last_name',
                'boatr_name_extension',
                'boatr_contact_number',
                'boatr_fishr_app_id'
            ];

            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) field.value = '';
            });
        }

        /**
         * Update the showAddBoatrModal function
         */
        function showAddBoatrModal() {
            const modal = new bootstrap.Modal(document.getElementById('addBoatrModal'));

            // Reset form
            document.getElementById('addBoatrForm').reset();
            document.getElementById('boatr_fishr_app_id').value = ''; // IMPORTANT

            // Remove validation errors
            document.querySelectorAll('#addBoatrModal .is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });

            document.querySelectorAll('#addBoatrModal .invalid-feedback').forEach(el => {
                el.textContent = '';
            });

            // Remove validation messages
            document.querySelectorAll('#addBoatrModal .admin-validation-message').forEach(el => {
                el.remove();
            });

            // Clear document preview
            const preview = document.getElementById('boatr_doc_preview');
            if (preview) {
                preview.innerHTML = '';
                preview.style.display = 'none';
            }

            modal.show();

            // Init FishR after modal opens
            setTimeout(() => {
                initializeAdminFishRValidation();
            }, 100);
        }

        /**
         * Update validateBoatrForm to NOT validate FishR during submission
         */
        async function validateBoatrForm() {
            let isValid = true;
            const errors = [];

            // Check all required fields
            const requiredFields = [
                { id: 'boatr_first_name', label: 'First Name' },
                { id: 'boatr_last_name', label: 'Last Name' },
                { id: 'boatr_contact_number', label: 'Contact Number' },
                { id: 'boatr_barangay', label: 'Barangay' },
                { id: 'boatr_fishr_number', label: 'FishR Number' },
                { id: 'boatr_vessel_name', label: 'Vessel Name' },
                { id: 'boatr_boat_type', label: 'Boat Type' },
                { id: 'boatr_boat_length', label: 'Boat Length' },
                { id: 'boatr_boat_width', label: 'Boat Width' },
                { id: 'boatr_boat_depth', label: 'Boat Depth' },
                { id: 'boatr_engine_type', label: 'Engine Type' },
                { id: 'boatr_engine_horsepower', label: 'Engine Horsepower' },
                { id: 'boatr_primary_fishing_gear', label: 'Primary Fishing Gear' }
            ];

            requiredFields.forEach(field => {
                const input = document.getElementById(field.id);
                if (!input || !input.value || input.value.trim() === '') {
                    input?.classList.add('is-invalid');
                    errors.push(`${field.label} is required`);
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            // Validate contact number format
            const contactInput = document.getElementById('boatr_contact_number');
            if (contactInput && contactInput.value) {
                const phoneRegex = /^(\+639|09)\d{9}$/;
                if (!phoneRegex.test(contactInput.value.trim())) {
                    contactInput.classList.add('is-invalid');
                    errors.push('Contact number must be in format 09XXXXXXXXX or +639XXXXXXXXX');
                    isValid = false;
                }
            }

            // Validate FishR number was validated
            const fishrInput = document.getElementById('boatr_fishr_number');
            if (!fishrInput.classList.contains('is-valid')) {
                fishrInput.classList.add('is-invalid');
                errors.push('Please validate FishR number by tabbing out of the field');
                isValid = false;
            }

            if (!isValid && errors.length > 0) {
                console.log('Validation errors:', errors);
            }

            return isValid;
        }

  /**
 * FIXED: Show Edit BoatR Modal - Works with correct form ID
 */
function showEditBoatrModal(registrationId) {
    console.log('=== Opening Edit BoatR Modal ===', { registrationId });
    
    const modal = new bootstrap.Modal(document.getElementById('editBoatrModal'));
    
    // Show loading state
    document.getElementById('editBoatrNumber').textContent = 'Loading...';
    
    // Show modal
    modal.show();
    
    // Fetch registration details
    fetch(`/admin/boatr/requests/${registrationId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(response => {
            if (!response.success) throw new Error(response.message || 'Failed to load registration');
            
            const data = response;
            
            // Update modal title
            document.getElementById('editBoatrNumber').textContent = data.application_number;
            
            // Initialize form with data
            initializeEditBoatrFormFixed(registrationId, data);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error loading registration: ' + error.message);
            modal.hide();
        });
}

/**
 * FIXED: Initialize Edit Form with registration data
 */
function initializeEditBoatrFormFixed(registrationId, data) {
    console.log('=== Initializing Edit Form ===', { registrationId, data });
    
    const form = document.getElementById('editBoatrForm');
    
    if (!form) {
        showToast('error', 'Form element not found in DOM');
        console.error('editBoatrForm not found');
        return;
    }

    // === Personal Information ===
    document.getElementById('edit_boatr_first_name').value = data.first_name || '';
    document.getElementById('edit_boatr_middle_name').value = data.middle_name || '';
    document.getElementById('edit_boatr_last_name').value = data.last_name || '';
    document.getElementById('edit_boatr_extension').value = data.name_extension || '';
    document.getElementById('edit_boatr_contact_number').value = data.contact_number || '';
    document.getElementById('edit_boatr_barangay').value = data.barangay || '';
    
    // === Vessel Information ===
    document.getElementById('edit_boatr_vessel_name').value = data.vessel_name || '';
    document.getElementById('edit_boatr_boat_type').value = data.boat_type || '';
    document.getElementById('edit_boatr_fishr_number').value = data.fishr_number || '';
    
    // === Boat Dimensions ===
    document.getElementById('edit_boatr_boat_length').value = data.boat_length || '';
    document.getElementById('edit_boatr_boat_width').value = data.boat_width || '';
    document.getElementById('edit_boatr_boat_depth').value = data.boat_depth || '';
    
    // === Engine Information ===
    document.getElementById('edit_boatr_engine_type').value = data.engine_type || '';
    document.getElementById('edit_boatr_engine_horsepower').value = data.engine_horsepower || '';
    
    // === Fishing Information ===
    document.getElementById('edit_boatr_primary_fishing_gear').value = data.primary_fishing_gear || '';
    
    // === Store Original Data for Change Detection ===
    //  IMPORTANT: Store AFTER populating fields
    const originalData = {
        first_name: data.first_name || '',
        middle_name: data.middle_name || '',
        last_name: data.last_name || '',
        name_extension: data.name_extension || '',
        contact_number: data.contact_number || '',
        barangay: data.barangay || '',
        vessel_name: data.vessel_name || '',
        boat_type: data.boat_type || '',
        boat_length: data.boat_length || '',
        boat_width: data.boat_width || '',
        boat_depth: data.boat_depth || '',
        engine_type: data.engine_type || '',
        engine_horsepower: data.engine_horsepower || '',
        primary_fishing_gear: data.primary_fishing_gear || '',
        inspection_notes: data.inspection_notes || ''
    };
    
    form.dataset.originalData = JSON.stringify(originalData);
    form.dataset.registrationId = registrationId;
    form.dataset.hasChanges = 'false';  // Set to false initially
    
    // === Handle Supporting Document Preview ===
    const previewContainer = document.getElementById('edit_boatr_supporting_document_preview');
    const currentDocContainer = document.getElementById('edit_boatr_current_document');
    
    if (data.user_documents && data.user_documents.length > 0 && data.user_documents[0].path) {
        currentDocContainer.style.display = 'block';
        displayEditBoatrExistingDocument(data.user_documents[0].path, 'edit_boatr_supporting_document_preview');
    } else {
        currentDocContainer.style.display = 'none';
        previewContainer.innerHTML = '<small class="text-muted d-block">No document currently uploaded</small>';
    }
    
    // === Inspection Information ===
    const inspectionStatusBadge = document.getElementById('edit_boatr_inspection_status_badge');
    if (data.inspection_completed) {
        inspectionStatusBadge.className = 'badge bg-success fs-6';
        inspectionStatusBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i>Completed';
    } else {
        inspectionStatusBadge.className = 'badge bg-warning fs-6';
        inspectionStatusBadge.innerHTML = '<i class="fas fa-clock me-1"></i>Pending';
    }
    
    document.getElementById('edit_boatr_inspection_date').textContent = data.inspection_date || '-';
    document.getElementById('edit_boatr_inspection_notes').value = data.inspection_notes || '';
    
    // === Handle Inspection Document Upload ===
    const inspectionDocContainer = document.getElementById('edit_boatr_inspection_doc_container');
    const inspectionDocPreview = document.getElementById('edit_boatr_inspection_doc_preview');
    
    if (data.inspection_completed && data.inspection_documents && data.inspection_documents.length > 0) {
        inspectionDocContainer.style.display = 'block';
        const inspectionDoc = data.inspection_documents[0];
        
        const inspectionDocInfo = `
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="d-block text-success fw-semibold mb-2">
                        <i class="fas fa-check-circle me-1"></i>Inspection Document Uploaded
                    </small>
                    <small class="d-block text-muted">${inspectionDoc.original_name || 'Inspection Report'}</small>
                    <small class="d-block text-muted mt-1">
                        <i class="fas fa-calendar me-1"></i>${inspectionDoc.uploaded_at || 'N/A'}
                    </small>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="viewInspectionDocument('${inspectionDoc.path}', '${inspectionDoc.original_name}')"
                        title="View inspection document">
                        <i class="fas fa-eye me-1"></i>View
                    </button>
                </div>
            </div>
        `;
        inspectionDocPreview.innerHTML = inspectionDocInfo;
    } else if (!data.inspection_completed) {
        inspectionDocContainer.style.display = 'block';
        inspectionDocPreview.innerHTML = '<small class="text-muted d-block">Inspection not yet completed. You can upload a document below.</small>';
    } else {
        inspectionDocContainer.style.display = 'none';
    }
    
    // === Status Information (Read-only) ===
    const statusBadge = document.getElementById('edit_boatr_status_badge');
    statusBadge.className = `badge bg-${data.status_color} fs-6`;
    statusBadge.textContent = data.formatted_status;
    
    document.getElementById('edit_boatr_created_at').textContent = data.created_at || '-';
    
    // Clear validation states
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    form.querySelectorAll('.form-changed').forEach(el => el.classList.remove('form-changed'));
    
    // Reset file inputs
    const supportingDocInput = document.getElementById('edit_boatr_supporting_document');
    if (supportingDocInput) supportingDocInput.value = '';
    
    const inspectionDocInput = document.getElementById('edit_boatr_inspection_document');
    if (inspectionDocInput) inspectionDocInput.value = '';
    
    // Reset button state
    const submitBtn = document.getElementById('editBoatrSubmitBtn');
    submitBtn.disabled = false;
    submitBtn.classList.add('no-changes');  // Add no-changes class
    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>No Changes';
    
    //  CRITICAL: Add listeners AFTER storing original data
    addEditBoatrFormChangeListeners(registrationId);
    
    // Reset counter
    updateEditBoatrInspectionCounter();
    
    console.log('Edit form initialized successfully');
}

/**
 * Display existing document in edit form
 */
function displayEditBoatrExistingDocument(documentPath, previewElementId) {
    const preview = document.getElementById(previewElementId);
    if (!preview) return;
    
    const fileExtension = documentPath.split('.').pop().toLowerCase();
    const fileName = documentPath.split('/').pop();
    const fileUrl = `/storage/${documentPath}`;
    
    if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(fileExtension)) {
        preview.innerHTML = `
            <div class="row g-3">
                <div class="col-auto">
                    <div class="document-thumbnail" style="width: 120px; height: 160px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                        <img src="${fileUrl}" alt="Current document" 
                            style="max-width: 100%; max-height: 100%; object-fit: cover; cursor: pointer;"
                            onclick="viewInspectionDocument('${documentPath}', '${fileName}')"
                            title="Click to view full document">
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
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                onclick="viewInspectionDocument('${documentPath}', '${fileName}')"
                                title="View">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else if (fileExtension === 'pdf') {
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
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                onclick="viewInspectionDocument('${documentPath}', '${fileName}')"
                                title="View">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}
/**
 * FIXED: Add change listeners to edit form
 */
function addEditBoatrFormChangeListeners(registrationId) {
    const form = document.getElementById('editBoatrForm');
    
    if (!form) {
        console.error('Form not found for adding listeners');
        return;
    }

    const inputs = form.querySelectorAll('input, select, textarea');
    
    // Remove old listeners to prevent duplicates
    inputs.forEach(input => {
        input.removeEventListener('change', handleEditBoatrFormChange);
        input.removeEventListener('input', handleEditBoatrFormChange);
    });
    
    // Add new listeners
    inputs.forEach(input => {
        input.addEventListener('change', () => handleEditBoatrFormChange(registrationId));
        input.addEventListener('input', () => handleEditBoatrFormChange(registrationId));
    });
}
/**
 * FIXED: Handle edit form change
 */
function handleEditBoatrFormChange(registrationId) {
    checkEditBoatrFormChanges(registrationId);
}

/**
 * FIXED: Check for form changes - Proper comparison
 */
function checkEditBoatrFormChanges(registrationId) {
    const form = document.getElementById('editBoatrForm');
    
    if (!form || !form.dataset.originalData) {
        console.warn('Form or original data not found');
        return;
    }

    const originalData = JSON.parse(form.dataset.originalData || '{}');
    let hasChanges = false;

    const fields = [
        { id: 'edit_boatr_first_name', key: 'first_name' },
        { id: 'edit_boatr_middle_name', key: 'middle_name' },
        { id: 'edit_boatr_last_name', key: 'last_name' },
        { id: 'edit_boatr_extension', key: 'name_extension' },
        { id: 'edit_boatr_contact_number', key: 'contact_number' },
        { id: 'edit_boatr_barangay', key: 'barangay' },
        { id: 'edit_boatr_vessel_name', key: 'vessel_name' },
        { id: 'edit_boatr_boat_type', key: 'boat_type' },
        { id: 'edit_boatr_boat_length', key: 'boat_length' },
        { id: 'edit_boatr_boat_width', key: 'boat_width' },
        { id: 'edit_boatr_boat_depth', key: 'boat_depth' },
        { id: 'edit_boatr_engine_type', key: 'engine_type' },
        { id: 'edit_boatr_engine_horsepower', key: 'engine_horsepower' },
        { id: 'edit_boatr_primary_fishing_gear', key: 'primary_fishing_gear' },
        { id: 'edit_boatr_inspection_notes', key: 'inspection_notes' }
    ];

    fields.forEach(field => {
        const fieldElement = document.getElementById(field.id);
        if (fieldElement) {
            //  Important: Convert to string and trim for proper comparison
            const currentValue = String(fieldElement.value || '').trim();
            const originalValue = String(originalData[field.key] || '').trim();

            console.log(`${field.id}: "${currentValue}" vs "${originalValue}" = ${currentValue === originalValue ? 'SAME' : 'CHANGED'}`);

            if (currentValue !== originalValue) {
                hasChanges = true;
                fieldElement.classList.add('form-changed');
            } else {
                fieldElement.classList.remove('form-changed');
            }
        }
    });

    // Check file inputs
    const supportingDocInput = document.getElementById('edit_boatr_supporting_document');
    if (supportingDocInput && supportingDocInput.files && supportingDocInput.files.length > 0) {
        hasChanges = true;
        console.log('Supporting document file detected');
    }

    const inspectionDocInput = document.getElementById('edit_boatr_inspection_document');
    if (inspectionDocInput && inspectionDocInput.files && inspectionDocInput.files.length > 0) {
        hasChanges = true;
        console.log('Inspection document file detected');
    }

    // Update button state
    const submitBtn = document.getElementById('editBoatrSubmitBtn');
    if (submitBtn) {
        if (hasChanges) {
            submitBtn.classList.remove('no-changes');
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
            submitBtn.disabled = false;
        } else {
            submitBtn.classList.add('no-changes');
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>No Changes';
            submitBtn.disabled = false;
        }
    }

    form.dataset.hasChanges = hasChanges ? 'true' : 'false';
    console.log('Form has changes:', hasChanges);
}


/**
 * FIXED: Proceed with Edit BoatR Submission
 */
function proceedWithEditBoatrSubmit(form, registrationId) {
    console.log('=== proceedWithEditBoatrSubmit ===', { registrationId });
    
    const submitBtn = document.getElementById('editBoatrSubmitBtn');
    
    if (!submitBtn) {
        console.error('Submit button not found');
        showToast('error', 'Submit button not found');
        return;
    }

    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    submitBtn.disabled = true;

    // Build FormData with proper field mapping
    const formData = new FormData();
    
    const fieldMap = {
        'edit_boatr_first_name': 'first_name',
        'edit_boatr_middle_name': 'middle_name',
        'edit_boatr_last_name': 'last_name',
        'edit_boatr_extension': 'name_extension',
        'edit_boatr_contact_number': 'contact_number',
        'edit_boatr_barangay': 'barangay',
        'edit_boatr_vessel_name': 'vessel_name',
        'edit_boatr_boat_type': 'boat_type',
        'edit_boatr_boat_length': 'boat_length',
        'edit_boatr_boat_width': 'boat_width',
        'edit_boatr_boat_depth': 'boat_depth',
        'edit_boatr_engine_type': 'engine_type',
        'edit_boatr_engine_horsepower': 'engine_horsepower',
        'edit_boatr_primary_fishing_gear': 'primary_fishing_gear',
        'edit_boatr_inspection_notes': 'inspection_notes'
    };

    // Add form fields
    Object.keys(fieldMap).forEach(fieldId => {
        const input = document.getElementById(fieldId);
        if (input) {
            formData.append(fieldMap[fieldId], input.value.trim());
        }
    });

    // Add file uploads if present
    const supportingDoc = document.getElementById('edit_boatr_supporting_document');
    if (supportingDoc && supportingDoc.files[0]) {
        formData.append('supporting_document', supportingDoc.files[0]);
        console.log('Added supporting document:', supportingDoc.files[0].name);
    }

    const inspectionDoc = document.getElementById('edit_boatr_inspection_document');
    if (inspectionDoc && inspectionDoc.files[0]) {
        formData.append('inspection_document', inspectionDoc.files[0]);
        // Signal backend to REPLACE the inspection document, not add
        formData.append('replace_inspection_document', '1');
        console.log('Added inspection document (REPLACE mode):', inspectionDoc.files[0].name);
    }

    // CRITICAL: Add _method = PUT for Laravel method spoofing
    // This tells Laravel to treat the POST request as a PUT request
    formData.append('_method', 'PUT');

    console.log('Sending request with:');
    console.log('- Method: POST (with _method=PUT in FormData)');
    console.log('- URL: /admin/boatr/requests/' + registrationId);
    console.log('- Content-Type: multipart/form-data (auto-set by browser)');

    // Send request
    // NOTE: Use POST method with _method=PUT in FormData
    // Laravel will intercept the _method parameter and treat this as a PUT request
    fetch(`/admin/boatr/requests/${registrationId}`, {
        method: 'POST',  // Use POST with _method=PUT (required for multipart with Laravel)
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
            // IMPORTANT: Do NOT set Content-Type header
            // Let the browser automatically set it to multipart/form-data
        },
        body: formData  // FormData handles the multipart encoding
    })
    .then(response => {
        console.log('Response received');
        console.log('Status:', response.status);
        console.log('Status Text:', response.statusText);
        
        if (!response.ok) {
            return response.json().then(data => {
                throw {
                    status: response.status,
                    statusText: response.statusText,
                    data: data
                };
            }).catch(parseError => {
                // If response is not JSON, throw a generic error
                throw {
                    status: response.status,
                    statusText: response.statusText,
                    message: `HTTP ${response.status}: ${response.statusText}`
                };
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Success response:', data);
        
        if (data.success) {
            showToast('success', data.message || 'Changes saved successfully');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editBoatrModal'));
            if (modal) {
                modal.hide();
            }

            // Reload page after 1.5 seconds
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Error during update:', error);
        
        let errorMsg = 'Error saving changes';
        
        if (error.data && error.data.errors && typeof error.data.errors === 'object') {
            // Handle validation errors
            const firstError = Object.values(error.data.errors)[0];
            errorMsg = Array.isArray(firstError) ? firstError[0] : firstError;
        } else if (error.data && error.data.message) {
            errorMsg = error.data.message;
        } else if (error.message) {
            errorMsg = error.message;
        } else if (error.statusText) {
            errorMsg = `${error.status} ${error.statusText}`;
        }
        
        console.error('Final error message:', errorMsg);
        showToast('error', errorMsg);
        
        // Reset button on error
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}



/**
 * Preview edit BoatR supporting document
 */
function previewEditBoatrDocument(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (!input.files || !input.files[0]) {
        preview.innerHTML = '';
        preview.style.display = 'none';
        return;
    }

    const file = input.files[0];

    if (file.size > 10 * 1024 * 1024) {
        showToast('error', 'File size must not exceed 10MB');
        input.value = '';
        preview.innerHTML = '';
        preview.style.display = 'none';
        return;
    }

    const reader = new FileReader();

    reader.onload = function(e) {
        if (preview) {
            if (file.type.startsWith('image/')) {
                preview.innerHTML = `
                    <div class="document-preview-item">
                        <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <p style="margin-top: 8px; font-size: 12px; color: #666;">
                            <i class="fas fa-file-image me-1"></i>${file.name}
                        </p>
                    </div>
                `;
            } else {
                preview.innerHTML = `
                    <div class="document-preview-item">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                            <p style="margin-top: 8px; font-size: 12px; color: #666;">${file.name}</p>
                        </div>
                    </div>
                `;
            }
            preview.style.display = 'block';
        }
    };

    reader.readAsDataURL(file);
}
/**
 * Preview inspection document file
 */
function previewEditBoatrInspectionDocument(inputId, previewId) {
    previewEditBoatrDocument(inputId, previewId);
}

/**
 * View existing inspection document
 */
function viewInspectionDocument(filePath, fileName) {
    const fileUrl = `/storage/${filePath}`;
    const fileExtension = fileName.split('.').pop().toLowerCase();
    
    const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
    modal.show();
    
    const previewContainer = document.getElementById('documentPreview');
    previewContainer.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Loading document...</p>
        </div>
    `;
    
    // Use existing preview function
    previewFileContent(fileUrl, fileName, fileExtension);
    
    document.getElementById('documentPreviewTitle').innerHTML = `<i class="fas fa-eye me-2"></i>${escapeHtml(fileName)}`;
}
/**
 * Update inspection notes counter
 */
function updateEditBoatrInspectionCounter() {
    const textarea = document.getElementById('edit_boatr_inspection_notes');
    const charCount = document.getElementById('edit_boatr_inspection_char_count');
    
    if (textarea && charCount) {
        charCount.textContent = textarea.value.length;
        
        // Change color based on length
        if (textarea.value.length > 1800) {
            document.getElementById('edit_boatr_inspection_counter').classList.add('text-danger');
            document.getElementById('edit_boatr_inspection_counter').classList.remove('text-warning', 'text-muted');
        } else if (textarea.value.length > 1500) {
            document.getElementById('edit_boatr_inspection_counter').classList.add('text-warning');
            document.getElementById('edit_boatr_inspection_counter').classList.remove('text-danger', 'text-muted');
        } else {
            document.getElementById('edit_boatr_inspection_counter').classList.remove('text-warning', 'text-danger');
            document.getElementById('edit_boatr_inspection_counter').classList.add('text-muted');
        }
    }
}



        // Store original data for change detection
        function storeOriginalEditBoatrData(data) {
            const form = document.getElementById('editBoatrForm');
            form.dataset.originalData = JSON.stringify({
                first_name: data.first_name || '',
                middle_name: data.middle_name || '',
                last_name: data.last_name || '',
                name_extension: data.name_extension || '',
                contact_number: data.contact_number || '',
                barangay: data.barangay || '',
                vessel_name: data.vessel_name || '',
                boat_type: data.boat_type || '',
                boat_length: data.boat_length || '',
                boat_width: data.boat_width || '',
                boat_depth: data.boat_depth || '',
                engine_type: data.engine_type || '',
                engine_horsepower: data.engine_horsepower || '',
                primary_fishing_gear: data.primary_fishing_gear || ''
            });
        }

        // Add change detection listeners
        function addEditBoatrChangeListeners() {
            const inputs = document.querySelectorAll('#editBoatrForm input, #editBoatrForm select');
            inputs.forEach(input => {
                input.removeEventListener('change', checkEditBoatrChanges);
                input.removeEventListener('input', checkEditBoatrChanges);
                input.addEventListener('change', checkEditBoatrChanges);
                input.addEventListener('input', checkEditBoatrChanges);
            });
        }

        // Check for changes
        function checkEditBoatrChanges() {
            const form = document.getElementById('editBoatrForm');
            const originalData = JSON.parse(form.dataset.originalData || '{}');
            const submitBtn = document.getElementById('editBoatrSubmitBtn');

            let hasChanges = false;

            const fieldMap = {
                'edit_boatr_first_name': 'first_name',
                'edit_boatr_middle_name': 'middle_name',
                'edit_boatr_last_name': 'last_name',
                'edit_boatr_extension': 'name_extension',
                'edit_boatr_contact': 'contact_number',
                'edit_boatr_barangay': 'barangay',
                'edit_boatr_vessel': 'vessel_name',
                'edit_boatr_boat_type': 'boat_type',
                'edit_boatr_length': 'boat_length',
                'edit_boatr_width': 'boat_width',
                'edit_boatr_depth': 'boat_depth',
                'edit_boatr_engine_type': 'engine_type',
                'edit_boatr_engine_hp': 'engine_horsepower',
                'edit_boatr_gear': 'primary_fishing_gear'
            };

            for (const [inputId, dataKey] of Object.entries(fieldMap)) {
                const input = document.getElementById(inputId);
                if (input) {
                    let inputValue = input.value;
                    let originalValue = originalData[dataKey] || '';

                    // Convert both to strings for comparison to handle numeric type mismatches
                    inputValue = String(inputValue).trim();
                    originalValue = String(originalValue).trim();

                    if (inputValue !== originalValue) {
                        hasChanges = true;
                        input.classList.add('form-changed');
                    } else {
                        input.classList.remove('form-changed');
                    }
                }
            }

            submitBtn.classList.toggle('no-changes', !hasChanges);
            if (!hasChanges) {
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>No Changes';
            } else {
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
            }
        }

        // Submit edit form
        function submitEditBoatr() {
            const form = document.getElementById('editBoatrForm');
            const registrationId = document.getElementById('edit_boatr_id').value;

            // Validate required fields manually since form validation might have issues
            const firstName = document.getElementById('edit_boatr_first_name').value.trim();
            const lastName = document.getElementById('edit_boatr_last_name').value.trim();
            const contact = document.getElementById('edit_boatr_contact').value.trim();
            const barangay = document.getElementById('edit_boatr_barangay').value.trim();
            const vessel = document.getElementById('edit_boatr_vessel').value.trim();
            const boatType = document.getElementById('edit_boatr_boat_type').value.trim();
            const boatLength = document.getElementById('edit_boatr_length').value.trim();
            const boatWidth = document.getElementById('edit_boatr_width').value.trim();
            const boatDepth = document.getElementById('edit_boatr_depth').value.trim();
            const engineType = document.getElementById('edit_boatr_engine_type').value.trim();
            const engineHp = document.getElementById('edit_boatr_engine_hp').value.trim();
            const gear = document.getElementById('edit_boatr_gear').value.trim();

            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            let isValid = true;
            const errors = [];

            // Check required fields
            if (!firstName) {
                document.getElementById('edit_boatr_first_name').classList.add('is-invalid');
                errors.push('First name is required');
                isValid = false;
            }
            if (!lastName) {
                document.getElementById('edit_boatr_last_name').classList.add('is-invalid');
                errors.push('Last name is required');
                isValid = false;
            }
            if (!contact) {
                document.getElementById('edit_boatr_contact').classList.add('is-invalid');
                errors.push('Contact number is required');
                isValid = false;
            } else if (!/^09\d{9}$/.test(contact)) {
                document.getElementById('edit_boatr_contact').classList.add('is-invalid');
                errors.push('Contact number must start with 09 and have 11 digits');
                isValid = false;
            }
            if (!barangay) {
                document.getElementById('edit_boatr_barangay').classList.add('is-invalid');
                errors.push('Barangay is required');
                isValid = false;
            }
            if (!vessel) {
                document.getElementById('edit_boatr_vessel').classList.add('is-invalid');
                errors.push('Vessel name is required');
                isValid = false;
            }
            if (!boatType) {
                document.getElementById('edit_boatr_boat_type').classList.add('is-invalid');
                errors.push('Boat type is required');
                isValid = false;
            }
            if (!boatLength) {
                document.getElementById('edit_boatr_length').classList.add('is-invalid');
                errors.push('Boat length is required');
                isValid = false;
            }
            if (!boatWidth) {
                document.getElementById('edit_boatr_width').classList.add('is-invalid');
                errors.push('Boat width is required');
                isValid = false;
            }
            if (!boatDepth) {
                document.getElementById('edit_boatr_depth').classList.add('is-invalid');
                errors.push('Boat depth is required');
                isValid = false;
            }
            if (!engineType) {
                document.getElementById('edit_boatr_engine_type').classList.add('is-invalid');
                errors.push('Engine type is required');
                isValid = false;
            }
            if (!engineHp) {
                document.getElementById('edit_boatr_engine_hp').classList.add('is-invalid');
                errors.push('Engine horsepower is required');
                isValid = false;
            }
            if (!gear) {
                document.getElementById('edit_boatr_gear').classList.add('is-invalid');
                errors.push('Primary fishing gear is required');
                isValid = false;
            }

            if (!isValid) {
                showToast('error', errors[0] || 'Please fix all validation errors');
                return;
            }

            const submitBtn = document.getElementById('editBoatrSubmitBtn');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            submitBtn.disabled = true;

            // Log the values being sent for debugging
            console.log('Submitting with values:', {
                firstName,
                lastName,
                contact,
                barangay,
                vessel,
                boatType,
                boatLength,
                boatWidth,
                boatDepth,
                engineType,
                engineHp,
                gear,
                registrationId
            });

            const formData = new FormData();
            formData.append('first_name', firstName);
            formData.append('middle_name', document.getElementById('edit_boatr_middle_name').value.trim());
            formData.append('last_name', lastName);
            formData.append('name_extension', document.getElementById('edit_boatr_extension').value || '');
            formData.append('contact_number', contact);
            formData.append('barangay', barangay);
            formData.append('vessel_name', vessel);
            formData.append('boat_type', boatType);
            formData.append('boat_length', boatLength);
            formData.append('boat_width', boatWidth);
            formData.append('boat_depth', boatDepth);
            formData.append('engine_type', engineType);
            formData.append('engine_horsepower', engineHp);
            formData.append('primary_fishing_gear', gear);

            // Log FormData contents
            console.log('FormData entries:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }

            // Convert FormData to JSON object
            const jsonData = {
                first_name: firstName,
                middle_name: document.getElementById('edit_boatr_middle_name').value.trim(),
                last_name: lastName,
                name_extension: document.getElementById('edit_boatr_extension').value || '',
                contact_number: contact,
                barangay: barangay,
                vessel_name: vessel,
                boat_type: boatType,
                boat_length: boatLength,
                boat_width: boatWidth,
                boat_depth: boatDepth,
                engine_type: engineType,
                engine_horsepower: engineHp,
                primary_fishing_gear: gear
            };

            console.log('Sending JSON:', jsonData);

            fetch(`/admin/boatr/requests/${registrationId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw {
                                status: response.status,
                                message: data.message || 'Validation failed',
                                errors: data.errors || {}
                            };
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editBoatrModal'));
                        if (modal) modal.hide();

                        showToast('success', data.message || 'BoatR application updated successfully');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Failed to update');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    let errorMsg = 'Error: ' + (error.message || 'Unknown error');

                    // If validation error with field details
                    if (error.errors && typeof error.errors === 'object') {
                        const fieldErrors = [];
                        for (const [field, messages] of Object.entries(error.errors)) {
                            if (Array.isArray(messages)) {
                                fieldErrors.push(messages[0]);
                            } else {
                                fieldErrors.push(messages);
                            }
                        }
                        if (fieldErrors.length > 0) {
                            errorMsg = fieldErrors[0]; // Show first validation error
                        }
                    }

                    showToast('error', errorMsg);
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Initialize edit modal with existing data
        function initializeEditBoatrModal(registrationId) {
            const form = document.getElementById('editBoatrForm_' + registrationId);
            if (!form) {
                console.error('Form not found:', 'editBoatrForm_' + registrationId);
                return;
            }

            console.log('Initializing modal for registration:', registrationId);

            fetch(`/admin/boatr/requests/${registrationId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (!data.success) throw new Error('Failed to load data');

                    // Populate form fields
                    form.querySelector('.edit_boatr_first_name').value = data.first_name || '';
                    form.querySelector('.edit_boatr_middle_name').value = data.middle_name || '';
                    form.querySelector('.edit_boatr_last_name').value = data.last_name || '';
                    form.querySelector('.edit_boatr_extension').value = data.name_extension || '';
                    form.querySelector('.edit_boatr_contact').value = data.contact_number || '';
                    form.querySelector('.edit_boatr_barangay').value = data.barangay || '';
                    form.querySelector('.edit_boatr_vessel').value = data.vessel_name || '';
                    form.querySelector('.edit_boatr_boat_type').value = data.boat_type || '';
                    form.querySelector('.edit_boatr_length').value = data.boat_length || '';
                    form.querySelector('.edit_boatr_width').value = data.boat_width || '';
                    form.querySelector('.edit_boatr_depth').value = data.boat_depth || '';
                    form.querySelector('.edit_boatr_engine_type').value = data.engine_type || '';
                    form.querySelector('.edit_boatr_engine_hp').value = data.engine_horsepower || '';
                    form.querySelector('.edit_boatr_gear').value = data.primary_fishing_gear || '';

                    storeOriginalBoatrValues(form, registrationId);
                    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    addBoatrEditListeners(form, registrationId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Failed to load application data: ' + error.message);
                });
        }

        // Store original values
        function storeOriginalBoatrValues(form, registrationId) {
            const originalData = {
                first_name: form.querySelector('.edit_boatr_first_name').value,
                middle_name: form.querySelector('.edit_boatr_middle_name').value,
                last_name: form.querySelector('.edit_boatr_last_name').value,
                name_extension: form.querySelector('.edit_boatr_extension').value,
                contact_number: form.querySelector('.edit_boatr_contact').value,
                barangay: form.querySelector('.edit_boatr_barangay').value,
                vessel_name: form.querySelector('.edit_boatr_vessel').value,
                boat_type: form.querySelector('.edit_boatr_boat_type').value,
                boat_length: form.querySelector('.edit_boatr_length').value,
                boat_width: form.querySelector('.edit_boatr_width').value,
                boat_depth: form.querySelector('.edit_boatr_depth').value,
                engine_type: form.querySelector('.edit_boatr_engine_type').value,
                engine_horsepower: form.querySelector('.edit_boatr_engine_hp').value,
                primary_fishing_gear: form.querySelector('.edit_boatr_gear').value,
            };
            form.dataset.originalData = JSON.stringify(originalData);
        }

        // Add event listeners
        function addBoatrEditListeners(form, registrationId) {
            const inputs = form.querySelectorAll('input, select');
            const submitBtn = document.querySelector('.edit_boatr_submit_btn_' + registrationId);

            inputs.forEach(input => {
                input.addEventListener('change', () => checkForBoatrEditChanges(form, submitBtn, registrationId));
                input.addEventListener('input', () => checkForBoatrEditChanges(form, submitBtn, registrationId));
            });
        }

        // Check for changes
        function checkForBoatrEditChanges(form, submitBtn, registrationId) {
            const originalData = JSON.parse(form.dataset.originalData || '{}');
            let hasChanges = false;

            const fieldMap = {
                'edit_boatr_first_name': 'first_name',
                'edit_boatr_middle_name': 'middle_name',
                'edit_boatr_last_name': 'last_name',
                'edit_boatr_extension': 'name_extension',
                'edit_boatr_contact': 'contact_number',
                'edit_boatr_barangay': 'barangay',
                'edit_boatr_vessel': 'vessel_name',
                'edit_boatr_boat_type': 'boat_type',
                'edit_boatr_length': 'boat_length',
                'edit_boatr_width': 'boat_width',
                'edit_boatr_depth': 'boat_depth',
                'edit_boatr_engine_type': 'engine_type',
                'edit_boatr_engine_hp': 'engine_horsepower',
                'edit_boatr_gear': 'primary_fishing_gear'
            };

            for (const [selector, key] of Object.entries(fieldMap)) {
                const input = form.querySelector('.' + selector);
                if (input && input.value !== originalData[key]) {
                    hasChanges = true;
                    input.classList.add('form-changed');
                } else if (input) {
                    input.classList.remove('form-changed');
                }
            }

            if (submitBtn) {
                submitBtn.disabled = false;
            }
        }

  /**
 * Handle Edit BoatR Form Submission - with changes summary
 */
function handleEditBoatrSubmit() {
    const form = document.getElementById('editBoatrForm');
    const registrationId = form.dataset.registrationId;
    const submitBtn = document.getElementById('editBoatrSubmitBtn');

    // Validate form first
    if (!validateEditBoatrForm()) {
        showToast('error', 'Please fix all validation errors');
        return;
    }

    const hasChanges = form.dataset.hasChanges === 'true';

    // If no changes, show warning and return
    if (!hasChanges) {
        showToast('warning', 'No changes detected. Please modify the fields before updating.');
        return;
    }

    // Build changes summary ONLY from actually changed fields
    const originalData = JSON.parse(form.dataset.originalData || '{}');
    let changedFields = [];

    const fieldLabels = {
        'first_name': 'First Name',
        'middle_name': 'Middle Name',
        'last_name': 'Last Name',
        'name_extension': 'Extension',
        'contact_number': 'Contact Number',
        'barangay': 'Barangay',
        'vessel_name': 'Vessel Name',
        'boat_type': 'Boat Type',
        'boat_length': 'Boat Length',
        'boat_width': 'Boat Width',
        'boat_depth': 'Boat Depth',
        'engine_type': 'Engine Type',
        'engine_horsepower': 'Engine Horsepower',
        'primary_fishing_gear': 'Primary Fishing Gear',
        'inspection_notes': 'Inspection Notes',
        'supporting_document': 'Supporting Document',
        'inspection_document': 'Inspection Document'
    };

    // Check which fields have changed
    const fields = [
        'first_name', 'middle_name', 'last_name', 'name_extension',
        'contact_number', 'barangay', 'vessel_name', 'boat_type',
        'boat_length', 'boat_width', 'boat_depth', 'engine_type',
        'engine_horsepower', 'primary_fishing_gear', 'inspection_notes'
    ];

    fields.forEach(field => {
        const fieldElement = form.querySelector(`[name="${field}"]`);
        if (fieldElement) {
            // Convert to string and trim for proper comparison
            const currentValue = String(fieldElement.value || '').trim();
            const originalValue = String(originalData[field] || '').trim();

            if (currentValue !== originalValue) {
                changedFields.push(fieldLabels[field] || field);
            }
        }
    });

    // Check file inputs - ONLY if a NEW file was selected
    const supportingDocInput = document.getElementById('edit_boatr_supporting_document');
    if (supportingDocInput && supportingDocInput.files && supportingDocInput.files.length > 0) {
        changedFields.push('Supporting Document');
    }

    const inspectionDocInput = document.getElementById('edit_boatr_inspection_document');
    if (inspectionDocInput && inspectionDocInput.files && inspectionDocInput.files.length > 0) {
        changedFields.push('Inspection Document');
    }

    // Build confirmation message
    const changesText = changedFields.length > 0 
        ? `Update this BoatR application with the following changes?\n\n• ${changedFields.join('\n• ')}`
        : 'Update this BoatR application?';

    // Show confirmation with only changed fields
    showConfirmationToast(
        'Confirm Update',
        changesText,
        () => proceedWithEditBoatrSubmit(form, registrationId)
    );
}

/**
 * Validate Edit BoatR Form
 */
function validateEditBoatrForm() {
    const form = document.getElementById('editBoatrForm');
    let isValid = true;
    
    // Required fields
    const requiredFields = [
        { name: 'first_name', label: 'First Name', element: 'edit_boatr_first_name' },
        { name: 'last_name', label: 'Last Name', element: 'edit_boatr_last_name' },
        { name: 'contact_number', label: 'Contact Number', element: 'edit_boatr_contact_number' },
        { name: 'barangay', label: 'Barangay', element: 'edit_boatr_barangay' },
        { name: 'vessel_name', label: 'Vessel Name', element: 'edit_boatr_vessel_name' },
        { name: 'boat_type', label: 'Boat Type', element: 'edit_boatr_boat_type' },
        { name: 'boat_length', label: 'Boat Length', element: 'edit_boatr_boat_length' },
        { name: 'boat_width', label: 'Boat Width', element: 'edit_boatr_boat_width' },
        { name: 'boat_depth', label: 'Boat Depth', element: 'edit_boatr_boat_depth' },
        { name: 'engine_type', label: 'Engine Type', element: 'edit_boatr_engine_type' },
        { name: 'engine_horsepower', label: 'Engine Horsepower', element: 'edit_boatr_engine_horsepower' },
        { name: 'primary_fishing_gear', label: 'Primary Fishing Gear', element: 'edit_boatr_primary_fishing_gear' }
    ];
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field.element);
        if (input && (!input.value || input.value.trim() === '')) {
            input.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = field.label + ' is required';
            
            const existingError = input.parentNode.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();
            
            input.parentNode.appendChild(errorDiv);
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    // Validate contact number
    const contactInput = document.getElementById('edit_boatr_contact_number');
    if (contactInput && contactInput.value.trim()) {
        const phoneRegex = /^(\+639|09)\d{9}$/;
        if (!phoneRegex.test(contactInput.value.trim())) {
            contactInput.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)';
            
            const existingError = contactInput.parentNode.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();
            
            contactInput.parentNode.appendChild(errorDiv);
            isValid = false;
        }
    }
    
    return isValid;
}

        // Proceed with update
        function proceedWithEditBoatr(form, registrationId) {
            const submitBtn = document.querySelector('.edit_boatr_submit_btn_' + registrationId);
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            submitBtn.disabled = true;

            const formData = new FormData();
            const fieldMap = {
                'edit_boatr_first_name': 'first_name',
                'edit_boatr_middle_name': 'middle_name',
                'edit_boatr_last_name': 'last_name',
                'edit_boatr_extension': 'name_extension',
                'edit_boatr_contact': 'contact_number',
                'edit_boatr_barangay': 'barangay',
                'edit_boatr_vessel': 'vessel_name',
                'edit_boatr_boat_type': 'boat_type',
                'edit_boatr_length': 'boat_length',
                'edit_boatr_width': 'boat_width',
                'edit_boatr_depth': 'boat_depth',
                'edit_boatr_engine_type': 'engine_type',
                'edit_boatr_engine_hp': 'engine_horsepower',
                'edit_boatr_gear': 'primary_fishing_gear'
            };

            for (const [selector, key] of Object.entries(fieldMap)) {
                const input = form.querySelector('.' + selector);
                if (input) {
                    formData.append(key, input.value);
                }
            }

            fetch(`/admin/boatr/requests/${registrationId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editBoatrModal_' +
                            registrationId));
                        if (modal) modal.hide();

                        showToast('success', data.message || 'BoatR application updated successfully');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Failed to update');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error: ' + error.message);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        /**
         * Update BoatR remarks character counter
         */
        function updateBoatrRemarksCounter() {
            const textarea = document.getElementById('boatr_remarks');
            const charCount = document.getElementById('boatrCharCount');
            const counter = document.getElementById('boatrRemarksCounter');
            
            if (textarea && charCount) {
                const currentLength = textarea.value.length;
                charCount.textContent = currentLength;
                
                // Change color when approaching limit
                if (currentLength > 1800) {
                    counter.classList.add('text-danger');
                    counter.classList.remove('text-warning', 'text-muted');
                } else if (currentLength > 1500) {
                    counter.classList.add('text-warning');
                    counter.classList.remove('text-danger', 'text-muted');
                } else {
                    counter.classList.remove('text-warning', 'text-danger');
                    counter.classList.add('text-muted');
                }
            }
        }

        /**
         * Initialize remarks counter when add modal is shown
         */
        document.addEventListener('DOMContentLoaded', function() {
            const addBoatrModal = document.getElementById('addBoatrModal');
            
            if (addBoatrModal) {
                addBoatrModal.addEventListener('shown.bs.modal', function() {
                    const textarea = document.getElementById('boatr_remarks');
                    
                    if (textarea) {
                        // Reset counter when modal opens
                        updateBoatrRemarksCounter();
                        
                        // Add input listener for real-time counter
                        textarea.removeEventListener('input', updateBoatrRemarksCounter);
                        textarea.addEventListener('input', updateBoatrRemarksCounter);
                    }
                });
                
                // Clean up when modal closes
                addBoatrModal.addEventListener('hidden.bs.modal', function() {
                    const textarea = document.getElementById('boatr_remarks');
                    if (textarea) {
                        textarea.removeEventListener('input', updateBoatrRemarksCounter);
                    }
                });
            }
        });
        /**
         * FIXED: Preview Annex from Registration Details Modal
         * Gets annex data from the registration object instead of separate endpoint
         */
        function previewAnnex(registrationId, annexId) {
            try {
                console.log('=== Preview Annex ===');
                console.log('Registration ID:', registrationId);
                console.log('Annex ID:', annexId);

                // First, fetch the full registration to get annex details
                fetch(`/admin/boatr/requests/${registrationId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Registration fetch status:', response.status);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Registration data received, success:', data.success);

                    if (!data.success) {
                        throw new Error(data.message || 'Failed to load registration');
                    }

                    // Find the annex in the registration data
                    const annexes = data.annexes || [];
                    console.log('Total annexes found:', annexes.length);
                    
                    const annex = annexes.find(a => a.id == annexId);
                    
                    if (!annex) {
                        throw new Error(`Annex with ID ${annexId} not found in registration`);
                    }

                    console.log('Annex found:', annex);

                    // Get file details
                    const filePath = annex.file_path;
                    const fileName = annex.file_name || annex.title || 'Document';

                    if (!filePath || filePath === 'undefined' || filePath.trim() === '') {
                        throw new Error('File path is empty or invalid');
                    }

                    const fileUrl = `/storage/${filePath}`;
                    const fileExtension = fileName.split('.').pop().toLowerCase();

                    console.log('Preview details:', {
                        filePath: filePath,
                        fileName: fileName,
                        fileUrl: fileUrl,
                        extension: fileExtension
                    });

                    // Open preview modal
                    const previewModalEl = document.getElementById('documentPreviewModal');
                    if (!previewModalEl) {
                        throw new Error('Preview modal not found in DOM');
                    }

                    previewModalEl.classList.add('modal-preview-from-documents');
                    
                    // Reset and show modal
                    const previewContainer = document.getElementById('documentPreview');
                    if (previewContainer) {
                        previewContainer.innerHTML = `
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted">Loading document preview...</p>
                            </div>
                        `;
                    }

                    // Update title
                    const titleEl = document.getElementById('documentPreviewTitle');
                    if (titleEl) {
                        titleEl.innerHTML = `<i class="fas fa-eye me-2"></i>${escapeHtml(fileName)}`;
                    }

                    const modal = new bootstrap.Modal(previewModalEl);
                    modal.show();

                    // Load preview content
                    previewFileContent(fileUrl, fileName, fileExtension);
                })
                .catch(error => {
                    console.error('Error in previewAnnex:', error);
                    showToast('error', 'Error loading annex: ' + error.message);

                    // Try to show error in preview if modal exists
                    const previewContainer = document.getElementById('documentPreview');
                    if (previewContainer) {
                        previewContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <h5>Error Loading Annex</h5>
                                <p>${escapeHtml(error.message)}</p>
                                <small class="text-muted">Please try viewing it later or contact administrator</small>
                            </div>
                        `;
                    }
                });

            } catch (error) {
                console.error('Fatal error in previewAnnex:', error);
                showToast('error', 'Fatal error: ' + error.message);
            }
        }

        /**
         * Helper function to preview file content (already exists in your code)
         * Make sure this function is defined in your existing JavaScript
         */
        function previewFileContent(fileUrl, fileName, fileExtension) {
            const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            const pdfTypes = ['pdf'];
            const documentTypes = ['doc', 'docx', 'txt', 'rtf'];
            
            const previewContainer = document.getElementById('documentPreview');
            
            if (imageTypes.includes(fileExtension)) {
                const img = new Image();
                img.onload = function() {
                    previewContainer.innerHTML = `
                        <div class="text-center">
                            <img src="${fileUrl}" 
                                alt="Preview" 
                                class="document-image"
                                style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); cursor: zoom-in;"
                                onclick="toggleImageZoom(this)">
                            <div style="margin-top: 20px;">
                                <button class="btn btn-primary me-2" onclick="downloadFileAnnex('${fileUrl}', '${fileName}')">
                                    <i class="fas fa-download me-2"></i>Download
                                </button>
                                <button class="btn btn-secondary" onclick="window.open('${fileUrl}', '_blank')">
                                    <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                                </button>
                            </div>
                        </div>
                    `;
                };
                img.onerror = function() {
                    previewContainer.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                            <h6>Unable to load image</h6>
                            <p class="mb-3">The image could not be displayed.</p>
                            <a href="${fileUrl}" download="${fileName}" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Download Image
                            </a>
                        </div>
                    `;
                };
                img.src = fileUrl;

            } else if (pdfTypes.includes(fileExtension)) {
                previewContainer.innerHTML = `
                    <div class="text-center">
                        <embed src="${fileUrl}"
                            type="application/pdf"
                            width="100%"
                            height="600px"
                            style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <div style="margin-top: 20px;">
                            <button class="btn btn-primary me-2" onclick="downloadFileAnnex('${fileUrl}', '${fileName}')">
                                <i class="fas fa-download me-2"></i>Download PDF
                            </button>
                            <button class="btn btn-secondary" onclick="window.open('${fileUrl}', '_blank')">
                                <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                            </button>
                        </div>
                    </div>
                `;

                // Fallback if PDF embed doesn't work
                setTimeout(() => {
                    const embed = previewContainer.querySelector('embed');
                    if (!embed || embed.offsetHeight === 0) {
                        previewContainer.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                                <h5>PDF Preview Unavailable</h5>
                                <p class="mb-3">Your browser doesn't support PDF preview.</p>
                                <a href="${fileUrl}" download="${fileName}" class="btn btn-primary me-2">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </a>
                                <button class="btn btn-secondary" onclick="window.open('${fileUrl}', '_blank')">
                                    <i class="fas fa-external-link-alt me-2"></i>Open PDF
                                </button>
                            </div>
                        `;
                    }
                }, 2000);

            } else if (documentTypes.includes(fileExtension)) {
                previewContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-file-${fileExtension === 'doc' || fileExtension === 'docx' ? 'word' : 'alt'} fa-4x text-primary mb-3"></i>
                        <h5>${fileExtension.toUpperCase()} Document</h5>
                        <p class="mb-3">This document type cannot be previewed in the browser.</p>
                        <a href="${fileUrl}" download="${fileName}" class="btn btn-primary me-2">
                            <i class="fas fa-download me-2"></i>Download
                        </a>
                        <button class="btn btn-secondary" onclick="window.open('${fileUrl}', '_blank')">
                            <i class="fas fa-external-link-alt me-2"></i>Open Document
                        </button>
                    </div>
                `;
            } else {
                previewContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-file fa-4x text-muted mb-3"></i>
                        <h5>File Preview Unavailable</h5>
                        <p class="mb-3">File type ".${fileExtension}" cannot be previewed.</p>
                        <a href="${fileUrl}" download="${fileName}" class="btn btn-primary me-2">
                            <i class="fas fa-download me-2"></i>Download
                        </a>
                        <button class="btn btn-secondary" onclick="window.open('${fileUrl}', '_blank')">
                            <i class="fas fa-external-link-alt me-2"></i>Open File
                        </button>
                    </div>
                `;
            }
        }

        /**
        * Toggle image zoom helper
        */
        function toggleImageZoom(img) {
            if (img.style.maxWidth === '100%') {
                img.style.maxWidth = '200%';
                img.style.cursor = 'zoom-out';
            } else {
                img.style.maxWidth = '100%';
                img.style.cursor = 'zoom-in';
            }
        }

        /**
        * Download file helper
        */
        function downloadFileAnnex(fileUrl, fileName) {
            const link = document.createElement('a');
            link.href = fileUrl;
            link.download = fileName;
            link.target = '_blank';
            document.body.appendChild(link);
            
            try {
                link.click();
                showToast('success', 'Download started');
            } catch (error) {
                console.error('Download error:', error);
                showToast('error', 'Failed to download file');
                window.open(fileUrl, '_blank');
            } finally {
                document.body.removeChild(link);
            }
        }
        // Cleanup modal on close
    // Cleanup modal on close
document.addEventListener('DOMContentLoaded', function() {
    const deleteBoatrModal = document.getElementById('deleteBoatrModal');
    if (deleteBoatrModal) {
        deleteBoatrModal.addEventListener('hidden.bs.modal', function() {
            console.log('Delete modal hidden - cleaning up');
            
            // Reset button state
            const deleteBtn = document.getElementById('confirm_delete_boatr_btn');
            if (deleteBtn) {
                const btnText = deleteBtn.querySelector('.btn-text');
                const btnLoader = deleteBtn.querySelector('.btn-loader');
                
                if (btnText) btnText.style.display = 'inline';
                if (btnLoader) btnLoader.style.display = 'none';
                deleteBtn.disabled = false;
            }

            // Remove any lingering backdrops
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

            // Remove modal-open class from body
            document.body.classList.remove('modal-open');

            // Reset body overflow
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Reset global variable
            window.currentDeleteBoatrId = null;

            console.log('Delete modal cleanup complete');
        });
    }
});
    </script>
@endsection