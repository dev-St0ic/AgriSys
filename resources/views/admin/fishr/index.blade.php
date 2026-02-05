{{-- resources/views/admin/fishr/index.blade.php --}}
@extends('layouts.app')

@section('title', 'FishR Registrations - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-fish text-primary me-2"></i>
        <span class="text-primary fw-bold">FishR Registrations</span>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-fish text-primary"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $totalRegistrations }}</div>
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
            <form method="GET" action="{{ route('admin.fishr.requests') }}" id="filterForm">
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
                        <select name="livelihood" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Livelihood</option>
                            <option value="capture" {{ request('livelihood') == 'capture' ? 'selected' : '' }}>
                                Capture Fishing
                            </option>
                            <option value="aquaculture" {{ request('livelihood') == 'aquaculture' ? 'selected' : '' }}>
                                Aquaculture
                            </option>
                            <option value="vending" {{ request('livelihood') == 'vending' ? 'selected' : '' }}>
                                Fish Vending
                            </option>
                            <option value="processing" {{ request('livelihood') == 'processing' ? 'selected' : '' }}>
                                Fish Processing
                            </option>
                            <option value="others" {{ request('livelihood') == 'others' ? 'selected' : '' }}>
                                Others
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="barangay" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Barangay</option>
                            <option value="Bagong Silang" {{ request('barangay') == 'Bagong Silang' ? 'selected' : '' }}>
                                Bagong Silang
                            </option>
                            <option value="Calendola" {{ request('barangay') == 'Calendola' ? 'selected' : '' }}>
                                Calendola
                            </option>
                            <option value="Chrysanthemum" {{ request('barangay') == 'Chrysanthemum' ? 'selected' : '' }}>
                                Chrysanthemum
                            </option>
                            <option value="Cuyab" {{ request('barangay') == 'Cuyab' ? 'selected' : '' }}>
                                Cuyab
                            </option>
                            <option value="Estrella" {{ request('barangay') == 'Estrella' ? 'selected' : '' }}>
                                Estrella
                            </option>
                            <option value="Fatima" {{ request('barangay') == 'Fatima' ? 'selected' : '' }}>
                                Fatima
                            </option>
                            <option value="G.S.I.S." {{ request('barangay') == 'G.S.I.S.' ? 'selected' : '' }}>
                                G.S.I.S.
                            </option>
                            <option value="Landayan" {{ request('barangay') == 'Landayan' ? 'selected' : '' }}>
                                Landayan
                            </option>
                            <option value="Langgam" {{ request('barangay') == 'Langgam' ? 'selected' : '' }}>
                                Langgam
                            </option>
                            <option value="Laram" {{ request('barangay') == 'Laram' ? 'selected' : '' }}>
                                Laram
                            </option>
                            <option value="Magsaysay" {{ request('barangay') == 'Magsaysay' ? 'selected' : '' }}>
                                Magsaysay
                            </option>
                            <option value="Maharlika" {{ request('barangay') == 'Maharlika' ? 'selected' : '' }}>
                                Maharlika
                            </option>
                            <option value="Narra" {{ request('barangay') == 'Narra' ? 'selected' : '' }}>
                                Narra
                            </option>
                            <option value="Nueva" {{ request('barangay') == 'Nueva' ? 'selected' : '' }}>
                                Nueva
                            </option>
                            <option value="Pacita 1" {{ request('barangay') == 'Pacita 1' ? 'selected' : '' }}>
                                Pacita 1
                            </option>
                            <option value="Pacita 2" {{ request('barangay') == 'Pacita 2' ? 'selected' : '' }}>
                                Pacita 2
                            </option>
                            <option value="Poblacion" {{ request('barangay') == 'Poblacion' ? 'selected' : '' }}>
                                Poblacion
                            </option>
                            <option value="Riverside" {{ request('barangay') == 'Riverside' ? 'selected' : '' }}>
                                Riverside
                            </option>
                            <option value="Rosario" {{ request('barangay') == 'Rosario' ? 'selected' : '' }}>
                                Rosario
                            </option>
                            <option value="Sampaguita Village"
                                {{ request('barangay') == 'Sampaguita Village' ? 'selected' : '' }}>
                                Sampaguita Village
                            </option>
                            <option value="San Antonio" {{ request('barangay') == 'San Antonio' ? 'selected' : '' }}>
                                San Antonio
                            </option>
                            <option value="San Lorenzo Ruiz"
                                {{ request('barangay') == 'San Lorenzo Ruiz' ? 'selected' : '' }}>
                                San Lorenzo Ruiz
                            </option>
                            <option value="San Roque" {{ request('barangay') == 'San Roque' ? 'selected' : '' }}>
                                San Roque
                            </option>
                            <option value="San Vicente" {{ request('barangay') == 'San Vicente' ? 'selected' : '' }}>
                                San Vicente
                            </option>
                            <option value="Santo Niño" {{ request('barangay') == 'Santo Niño' ? 'selected' : '' }}>
                                Santo Niño
                            </option>
                            <option value="United Bayanihan"
                                {{ request('barangay') == 'United Bayanihan' ? 'selected' : '' }}>
                                United Bayanihan
                            </option>
                            <option value="United Better Living"
                                {{ request('barangay') == 'United Better Living' ? 'selected' : '' }}>
                                United Better Living
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search name, number, contact..." value="{{ request('search') }}"
                                oninput="handleSearchInput()" id="searchInput">
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
                        <a href="{{ route('admin.fishr.requests') }}" class="btn btn-secondary btn-sm w-100">
                            <i></i> Clear
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
                    <i class="fas fa-fish me-2"></i>FishR Registrations
                </h6>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="showAddFishrModal()">
                    <i class="fas fa-user-plus me-2"></i>Add Registration
                </button>
                <a href="{{ route('admin.fishr.export') }}" class="btn btn-success btn-sm">
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
                            <th class="text-center">Registration #</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Livelihood</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Documents</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr data-registration-id="{{ $registration->id }}">
                                <td class="text-start">{{ $registration->created_at->format('M d, Y g:i A') }}</td>
                                <td class="text-start">
                                    <strong class="text-primary">{{ $registration->registration_number }}</strong>
                                </td>
                                <td class="text-start">{{ $registration->full_name }}</td>
                                <td class="text-start">
                                    <span class="badge bg-info fs-6">{{ $registration->livelihood_description }}</span>
                                    @if($registration->secondary_livelihood)
                                        <br>
                                        <span class="badge bg-info fs-6" style="margin-top: 4px;">{{ $registration->secondary_livelihood_description }}</span>
                                    @endif
                                </td>
                                <td class="text-start">
                                    <span class="badge bg-{{ $registration->status_color }} fs-6">
                                        {{ $registration->formatted_status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="fishr-table-documents">
                                        @if ($registration->document_path)
                                            <div class="fishr-document-previews">
                                                <button type="button" class="fishr-mini-doc"
                                                    onclick="viewDocument('{{ $registration->document_path }}', 'Fisherfolk Registration - {{ $registration->first_name }} {{ $registration->last_name }}')"
                                                    title="Registration Document">
                                                    <div class="fishr-mini-doc-icon">
                                                        <i class="fas fa-file-alt text-primary"></i>
                                                    </div>
                                                </button>
                                            </div>
                                            <button type="button" class="fishr-document-summary"
                                                onclick="viewDocument('{{ $registration->document_path }}', 'Fisherfolk Registration - {{ $registration->first_name }} {{ $registration->last_name }}')"
                                                style="background: none; border: none; padding: 0; cursor: pointer;">
                                                <small class="text-muted">1 document</small>
                                            </button>
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
                                                        onclick="showEditFishrModal({{ $registration->id }})">
                                                        <i class="fas fa-edit me-2 text-success"></i>Edit Information
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                        onclick="deleteFishrRegistration({{ $registration->id }}, '{{ $registration->registration_number }}')">
                                                        <i class="fas fa-trash me-2"></i>Delete Registration
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
                                    <i class="fas fa-fish fa-3x mb-3"></i>
                                    <p>No FishR registrations found.</p>
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


   <!-- UPDATED: Change Status Modal with Consistent Design -->
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Update Registration Status
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
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
                                        <small class="text-muted d-block">Registration #</small>
                                        <strong class="text-primary" id="updateRegNumber"></strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Applicant Name</small>
                                        <strong id="updateRegName"></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Barangay</small>
                                        <strong id="updateRegBarangay"></strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Current Status</small>
                                        <span id="updateRegCurrentStatus" style="margin-top: 0.25rem;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Card -->
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-sync me-2"></i>Change Status
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="updateForm">
                                <input type="hidden" id="updateRegistrationId">
                                
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
                            <textarea class="form-control" id="remarks" rows="4"
                                placeholder="Add any notes or comments about this status change..."
                                maxlength="1000"
                                onchange="checkForChanges()"
                                oninput="updateFishrRemarksCounterUpdate()"></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Provide context for this status change
                                </small>
                                <small class="text-muted" id="remarksCounterUpdate">
                                    <span id="charCountUpdate">0</span>/1000
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info border-left-info mt-3 mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Note:</strong> This will update the registration status and store your remarks in the system.
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="updateStatusBtn" onclick="updateRegistrationStatus()" disabled>
                        <i class="fas fa-save me-2"></i>Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Details View Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Registration Details
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
                                            <strong>Registration #:</strong>
                                            <span class="text-primary" id="viewRegNumber"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Full Name:</strong>
                                            <span id="viewRegName"></span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Sex:</strong>
                                            <span id="viewRegSex"></span>
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
                                            <span id="viewRegBarangay"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Livelihood Information Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-fish me-2"></i>Livelihood Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <strong>Main Livelihood:</strong>
                                            <span id="viewRegLivelihood"></span>
                                        </div>
                                        <div class="col-12" id="viewOtherLivelihoodContainer" style="display: none;">
                                            <strong>Other Livelihood:</strong>
                                            <span id="viewRegOtherLivelihood"></span>
                                        </div>
                                        <div class="col-12" id="viewSecondaryLivelihoodContainer" style="display: none;">
                                            <strong>Secondary Livelihood:</strong>
                                            <span id="viewRegSecondaryLivelihood"></span>
                                        </div>
                                        <div class="col-12" id="viewOtherSecondaryLivelihoodContainer" style="display: none;">
                                            <strong>Other Secondary Livelihood:</strong>
                                            <span id="viewRegOtherSecondaryLivelihood"></span>
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
                                            <div id="viewRegStatus" style="margin-top: 0.25rem;"></div>
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

                        <!-- Supporting Document Card -->
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white text-center">
                                    <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i>Supporting Document</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div id="viewRegDocumentContainer">
                                        <!-- Document info will be loaded here -->
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
                    <h5 class="modal-title  w-100 text-center" id="documentModalLabel">
                        <i></i>Supporting Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="documentViewer">
                    <!-- Document will be loaded here -->
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Close
                    </button>
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
                                            <small class="text-muted d-block">Registration #</small>
                                            <strong class="text-primary" id="annexRegNumber"></strong>
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
                        <i></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
   <!-- Edit FishR Registration Modal - RSBSA Design -->
<div class="modal fade" id="editFishrModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title w-100 text-center">
                    <i></i>Edit Registration - <span id="editFishrNumber"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="editFishrForm" enctype="multipart/form-data">
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
                                    <label for="edit_fishr_first_name" class="form-label fw-semibold">
                                        First Name 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_fishr_first_name"
                                        name="first_name" required maxlength="100" placeholder="First name">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_fishr_middle_name" class="form-label fw-semibold">
                                        Middle Name
                                    </label>
                                    <input type="text" class="form-control" id="edit_fishr_middle_name"
                                        name="middle_name" maxlength="100" placeholder="Middle name (optional)">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_fishr_last_name" class="form-label fw-semibold">
                                        Last Name 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_fishr_last_name"
                                        name="last_name" required maxlength="100" placeholder="Last name">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_fishr_extension" class="form-label fw-semibold">
                                        Extension
                                    </label>
                                    <select class="form-select" id="edit_fishr_extension" name="name_extension">
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
                                    <label for="edit_fishr_sex" class="form-label fw-semibold">
                                        Sex 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_fishr_sex" name="sex" required>
                                        <option value="">Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Preferred not to say">Preferred not to say</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_fishr_contact_number" class="form-label fw-semibold">
                                        Contact Number 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="edit_fishr_contact_number"
                                        name="contact_number" required placeholder="09XXXXXXXXX"
                                        pattern="^09\d{9}$" maxlength="11">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>09XXXXXXXXX 
                                    </small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_fishr_reg_number" class="form-label fw-semibold">
                                        Registration Number
                                    </label>
                                    <input type="text" class="form-control" id="edit_fishr_reg_number" disabled placeholder="-">
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
                                    <label for="edit_fishr_barangay" class="form-label fw-semibold">
                                        Barangay 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_fishr_barangay" name="barangay" required>
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

                    <!-- Livelihood Information Card - NOW FULLY EDITABLE -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-fish me-2"></i>Livelihood Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Main Livelihood -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_fishr_livelihood" class="form-label fw-semibold">
                                        Main Livelihood 
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_fishr_livelihood" name="main_livelihood"
                                        required onchange="toggleEditOtherFishrLivelihood(); validateEditSecondaryLivelihood()">
                                        <option value="">Select Livelihood</option>
                                        <option value="capture">Capture Fishing</option>
                                        <option value="aquaculture">Aquaculture</option>
                                        <option value="vending">Fish Vending</option>
                                        <option value="processing">Fish Processing</option>
                                        <option value="others">Others</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="edit_other_fishr_livelihood_container" style="display: none;">
                                    <label for="edit_fishr_other_livelihood" class="form-label fw-semibold">
                                        Specify Other Livelihood <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_fishr_other_livelihood" 
                                        name="other_livelihood" maxlength="255" placeholder="Please specify...">
                                </div>
                            </div>

                            <!-- Secondary Livelihood -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="edit_fishr_secondary_livelihood" class="form-label fw-semibold">
                                        Secondary Livelihood (Optional)
                                    </label>
                                    <select class="form-select" id="edit_fishr_secondary_livelihood" name="secondary_livelihood"
                                        onchange="toggleEditOtherSecondaryFishrLivelihood(); validateEditSecondaryLivelihood()">
                                        <option value="">Select Livelihood</option>
                                        <option value="capture">Capture Fishing</option>
                                        <option value="aquaculture">Aquaculture</option>
                                        <option value="vending">Fish Vending</option>
                                        <option value="processing">Fish Processing</option>
                                        <option value="others">Others</option>
                                    </select>
                                    <small class="text-muted d-block mt-2" id="edit_secondary_livelihood_warning" 
                                        style="color: #ff6b6b; display: none;">
                                        <!-- Secondary livelihood cannot be the same as main livelihood -->
                                    </small>
                                </div>
                                <div class="col-md-6" id="edit_other_fishr_secondary_livelihood_container" style="display: none;">
                                    <label for="edit_fishr_other_secondary_livelihood" class="form-label fw-semibold">
                                        Specify Other Secondary Livelihood <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_fishr_other_secondary_livelihood" 
                                        name="other_secondary_livelihood" maxlength="255" placeholder="Please specify...">
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
                            <div id="edit_fishr_current_document" style="display: none; margin-bottom: 1.5rem;">
                                <label class="form-label fw-semibold text-muted mb-2">Current Document</label>
                                <div id="edit_fishr_current_doc_preview"></div>
                            </div>

                            <!-- Upload New Document Section -->
                            <div class="row">
                                <div class="col-12">
                                    <label for="edit_fishr_supporting_document" class="form-label fw-semibold">
                                        Supporting Document
                                    </label>
                                    <input type="file" class="form-control" id="edit_fishr_supporting_document" 
                                        name="supporting_document" accept="image/*,.pdf" 
                                        onchange="previewEditFishrDocument('edit_fishr_supporting_document', 'edit_fishr_supporting_document_preview')">
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>Upload a new file to replace it.
                                    </small>
                                </div>
                            </div>

                            <!-- New Document Preview -->
                            <div id="edit_fishr_supporting_document_preview" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Registration Status (Read-only) Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="fas fa-info-circle me-2"></i>Registration Status (Read-only)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block mb-2">Current Status</small>
                                    <div>
                                        <span id="edit_fishr_status_badge" class="badge bg-secondary fs-6"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block mb-2">Date Applied</small>
                                    <div id="edit_fishr_created_at" class="fw-semibold">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info border-left-info mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Note:</strong> You can edit all registration information here.
                        To change registration status or add remarks, use the "Change Status" button from the actions table.
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="editFishrSubmitBtn"
                    onclick="handleEditFishrSubmit()">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- DELETE  MODAL -->
    <div class="modal fade" id="deleteFishrModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title w-100 text-center">Move FishR Registration to Recycle Bin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                        <p class="mb-0">Are you sure you want to delete this FishR registration? <strong id="delete_fishr_name"></strong> will be moved to the Recycle Bin.</p>
                    </div>
                    <ul class="mb-0">
                        <li>Remove the registration from active records</li>
                        <li>Hide it from users and administrators</li>
                        <li>Keep all documents, annexes, and attachments</li>
                        <li><strong>Can be restored from the Recycle Bin</strong></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmPermanentDeleteFishr()"
                        id="confirm_delete_fishr_btn">
                        <span class="btn-text">Move to Recycle Bin</span>
                        <span class="btn-loader" style="display: none;"><span
                                class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
                    </button>
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
                            <div class="alert alert-info mb-0" style="border-left: 4px solid #17a2b8; border-radius: 8px;">
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
    <!-- Add FishR Registration Modal updated -->
    <div class="modal fade" id="addFishrModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Add New FishR Registration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addFishrForm" enctype="multipart/form-data">
                        @csrf
                        
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
                                        <label for="fishr_first_name" class="form-label fw-semibold">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="fishr_first_name" required maxlength="100" placeholder="First name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="fishr_middle_name" class="form-label fw-semibold">
                                            Middle Name
                                        </label>
                                        <input type="text" class="form-control" id="fishr_middle_name" maxlength="100" placeholder="Middle name (optional)">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="fishr_last_name" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="fishr_last_name" required maxlength="100" placeholder="Last name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="fishr_name_extension" class="form-label fw-semibold">
                                            Extension
                                        </label>
                                        <select class="form-select" id="fishr_name_extension">
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
                                        <label for="fishr_sex" class="form-label fw-semibold">
                                            Sex <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="fishr_sex" required>
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Preferred not to say">Preferred not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="fishr_contact_number" class="form-label fw-semibold">
                                            Contact Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="fishr_contact_number" required placeholder="09XXXXXXXXX" pattern="^09\d{9}$" maxlength="11">
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
                                        <label for="fishr_barangay" class="form-label fw-semibold">
                                            Barangay <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="fishr_barangay" required>
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
 
                        <!-- Livelihood Information Card -->
                       <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-fish me-2"></i>Livelihood Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Main Livelihood -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="fishr_main_livelihood" class="form-label fw-semibold">
                                            Main Livelihood <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="fishr_main_livelihood" required onchange="toggleOtherLivelihood(); validateAddSecondaryLivelihood()">
                                            <option value="">Select Livelihood</option>
                                            <option value="capture">Capture Fishing</option>
                                            <option value="aquaculture">Aquaculture</option>
                                            <option value="vending">Fish Vending</option>
                                            <option value="processing">Fish Processing</option>
                                            <option value="others">Others</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="other_livelihood_container" style="display: none;">
                                        <label for="fishr_other_livelihood" class="form-label fw-semibold">
                                            Specify Other Livelihood <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="fishr_other_livelihood" maxlength="255" placeholder="Please specify..." oninput="validateAddOtherLivelihoodText()" onblur="capitalizeAddOtherLivelihood()">
                                        <small class="text-muted d-block mt-2" id="add_other_livelihood_warning" style="color: #ff6b6b; display: none;">
                                            <!-- Only letters, numbers, spaces, hyphens, apostrophes, periods, and commas allowed -->
                                        </small>
                                    </div>
                                </div>

                                <!-- Secondary Livelihood -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="fishr_secondary_livelihood" class="form-label fw-semibold">
                                            Secondary Livelihood (Optional)
                                        </label>
                                        <select class="form-select" id="fishr_secondary_livelihood" onchange="toggleAddOtherSecondaryLivelihood(); validateAddSecondaryLivelihood()">
                                            <option value="">Select Livelihood</option>
                                            <option value="capture">Capture Fishing</option>
                                            <option value="aquaculture">Aquaculture</option>
                                            <option value="vending">Fish Vending</option>
                                            <option value="processing">Fish Processing</option>
                                            <option value="others">Others</option>
                                        </select>
                                        <small class="text-muted d-block mt-2" id="add_secondary_livelihood_warning" 
                                            style="color: #ff6b6b; display: none;">
                                        </small>
                                    </div>
                                    <div class="col-md-6" id="add_other_secondary_livelihood_container" style="display: none;">
                                        <label for="fishr_other_secondary_livelihood" class="form-label fw-semibold">
                                            Specify Other Secondary Livelihood <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="fishr_other_secondary_livelihood" 
                                            maxlength="255" placeholder="Please specify..." oninput="validateAddOtherSecondaryLivelihoodText()" onblur="capitalizeAddOtherSecondaryLivelihood()">
                                        <small class="text-muted d-block mt-2" id="add_other_secondary_livelihood_warning" style="color: #ff6b6b; display: none;">
                                            <!-- Only letters, numbers, spaces, hyphens, apostrophes, periods, and commas allowed -->
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Document Card -->
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
                                        <label for="fishr_supporting_document" class="form-label fw-semibold">
                                            Upload Document
                                        </label>
                                        <input type="file" class="form-control" id="fishr_supporting_document" name="supporting_document" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFishrDocument('fishr_supporting_document', 'fishr_doc_preview')">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>Accepted: JPG, PNG, PDF (Max 10MB)
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="fishr_doc_preview" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Status Card -->
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-cog me-2"></i>Registration Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fishr_status" class="form-label fw-semibold">
                                            Initial Status <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="fishr_status" required>
                                            <option value="under_review" selected>Under Review</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks Card -->
                        <div class="card border-0 bg-light mt-3">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-comment me-2"></i>Admin Remarks
                                </h6>
                            </div>
                            <div class="card-body">
                                <label for="fishr_remarks" class="form-label fw-semibold">
                                    Remarks (Optional)
                                </label>
                                <textarea class="form-control" id="fishr_remarks" rows="4"
                                    placeholder="Add any comments about this registration..."
                                    maxlength="1000"
                                    oninput="updateFishrRemarksCounter()"></textarea>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Provide context for this registration
                                    </small>
                                    <small class="text-muted" id="remarksCounterFishr">
                                        <span id="charCountFishr">0</span>/1000
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
                    <button type="button" class="btn btn-primary" onclick="submitAddFishr()">
                        <i class="fas fa-save me-1"></i>Create Registration
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Status Card
    <div class="card border-0 bg-light">
        <div class="card-header bg-white border-0 pb-0">
            <h6 class="mb-0 fw-semibold text-primary">
                <i class="fas fa-check-double me-2"></i>Confirmation Status
            </h6>
        </div>
        <div class="card-body">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="confirmStatusChange">
                <label class="form-check-label" for="confirmStatusChange">
                    I confirm that all information is accurate and I want to proceed with this status change
                </label>
            </div>
        </div>
    </div> -->

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

        /* Annexes Button Styling */
        .btn-annexes {
            background-color: transparent;
            border-color: #6f42c1;
            color: #6f42c1;
        }

        .btn-annexes:hover {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }

        .btn-annexes:focus,
        .btn-annexes:active {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
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
        border-color: #6f42c1;
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
        color: #6f42c1;
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

    /* Empty State */
    .annex-empty-state {
        text-center py-4;
        padding: 2rem;
    }

    .annex-empty-state i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .annex-empty-state p {
        color: #6c757d;
        font-size: 0.95rem;
    }

    /* Modal specific styles */
    #annexesModal .modal-content {
        border: none;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        border-radius: 12px;
    }

    #annexesModal .modal-header {
        border-radius: 12px 12px 0 0;
        border: none;
        padding: 1.5rem;
    }

    #annexesModal .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
        letter-spacing: 0.5px;
    }

    #annexesModal .modal-footer {
        border-radius: 0 0 12px 12px;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        padding: 1.25rem;
    }

    #annexesModal .modal-body {
        padding: 2rem;
    }

    #annexesModal .card {
        border-width: 1px !important;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    #annexesModal .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transform: translateY(-1px);
    }

    #annexesModal .card-header {
        padding: 1rem 1.25rem;
        font-weight: 600;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    #annexesModal .card-header h6 {
        color: #0d6efd;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    #annexesModal .card-body {
        padding: 1.5rem;
    }

    #annexesModal .form-label {
        color: #495057;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    #annexesModal .form-control,
    #annexesModal .form-select {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        padding: 0.75rem;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    #annexesModal .form-control:focus,
    #annexesModal .form-select:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
        outline: none;
    }

    #annexesModal .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    #annexesModal .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    #annexesModal .btn-outline-primary,
    #annexesModal .btn-outline-success,
    #annexesModal .btn-outline-danger {
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    #annexesModal .btn-outline-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3);
    }

    #annexesModal .btn-outline-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(25, 135, 84, 0.3);
    }

    #annexesModal .btn-outline-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
    }

    /* Spinner */
    #annexesModal .spinner-border {
        color: #0d6efd;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #annexesModal .modal-dialog {
            margin: 0.5rem;
        }

        #annexesModal .modal-body {
            padding: 1.5rem 1rem;
        }

        #annexesModal .modal-header,
        #annexesModal .modal-footer {
            padding: 1rem;
        }

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

        #annexesModal .card-body {
            padding: 1rem;
        }
    }

    @media (max-width: 576px) {
        #annexesModal .modal-header .modal-title {
            font-size: 1.05rem;
        }

        .annex-title {
            font-size: 0.9rem;
        }

        .annex-meta {
            font-size: 0.75rem;
        }
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

        /* Document item styling */
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
            padding: 1rem;
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
        .fishr-mini-doc[title*="Registration"] {
            border-color: #17a2b8;
        }

        .fishr-mini-doc[title*="Registration"]:hover {
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

        .fishr-pdf-embed {
            border-radius: 8px;
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
            cursor: pointer;
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

        /* Responsive design for FISHR document viewer */
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
        /*  Modal z-index fixes for stacking */ 
        #documentModal {
            z-index: 1060 !important;
        }

        #documentModal .modal-backdrop {
            z-index: 1059 !important;
        }

        #annexesModal {
            z-index: 1050 !important;
        }

        #annexesModal .modal-backdrop {
            z-index: 1049 !important;
        }

        /* Ensure document viewer doesn't have dark background */
        #documentViewer {
            background: white !important;
            min-height: 400px;
        }

        #documentModal {
            z-index: 9999 !important;
        }

        #documentModal .modal-backdrop {
            z-index: 9998 !important;
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

            // Auto-dismiss after 5 seconds
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

            // Store the callback function on the toast element
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
                            <i></i>Cancel
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmToastAction(this)">
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

        // Execute confirmation action
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

        // Remove toast notification
        function removeToast(toastElement) {
            toastElement.classList.remove('show');
            setTimeout(() => {
                if (toastElement.parentElement) {
                    toastElement.remove();
                }
            }, 300);
        }

        // Get CSRF token utility function
        function getCSRFToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            return metaTag ? metaTag.getAttribute('content') : '';
        }

        // Submit filter form when dropdowns change
        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        // Helper function to get status display text
        function getStatusText(status) {
            switch (status) {
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

       // Enhanced show update modal function to store original values
        function showUpdateModal(id, currentStatus) {
            // Show loading state in modal
            document.getElementById('updateRegNumber').innerHTML = `
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>`;

            // First fetch the registration details
            fetch(`/admin/fishr-registrations/${id}`, {
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
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    if (!response.success) {
                        throw new Error('Failed to load registration details');
                    }

                    const data = response.data;

                    // Populate the hidden field
                    document.getElementById('updateRegistrationId').value = id;

                    // Populate registration info display
                    document.getElementById('updateRegNumber').textContent = data.registration_number;
                    document.getElementById('updateRegName').textContent = data.full_name;
                    document.getElementById('updateRegBarangay').textContent = data.barangay;

                    // Show current status with badge styling
                    const currentStatusElement = document.getElementById('updateRegCurrentStatus');
                    currentStatusElement.innerHTML = `
                        <span class="badge bg-${data.status_color}" style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">${data.formatted_status}</span>`;

                    // Set form values and store original values for comparison
                    const statusSelect = document.getElementById('newStatus');
                    const remarksTextarea = document.getElementById('remarks');

                    statusSelect.value = data.status;
                    statusSelect.dataset.originalStatus = data.status;

                    remarksTextarea.value = data.remarks || '';
                    remarksTextarea.dataset.originalRemarks = data.remarks || '';

                    // Update remarks counter
                    updateFishrRemarksCounterUpdate();

                    // Remove any previous change indicators
                    statusSelect.classList.remove('form-changed');
                    remarksTextarea.classList.remove('form-changed');

                    // Reset update button state
                    const updateButton = document.getElementById('updateStatusBtn');
                    updateButton.classList.add('no-changes');
                    updateButton.innerHTML = '<i class="fas fa-check me-2"></i>No Changes';
                    updateButton.disabled = false;

                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('updateModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error loading registration details: ' + error.message);
                });
        }


        // Updated View Registration Details Function
        function viewRegistration(id) {
            if (!id) {
                showToast('error', 'Invalid registration ID');
                return;
            }

            // Show loading state
            const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
            modal.show();

            // Fetch registration details
            fetch(`/admin/fishr-registrations/${id}`, {
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
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    if (!response.success) {
                        throw new Error('Failed to load registration details');
                    }

                    const data = response.data;

                    // Populate Personal Information
                    document.getElementById('viewRegNumber').textContent = data.registration_number || 'N/A';
                    document.getElementById('viewRegName').textContent = data.full_name || 'N/A';
                    document.getElementById('viewRegSex').textContent = data.sex || 'N/A';
                    
                    const contactLink = document.getElementById('viewRegContact');
                    contactLink.href = `tel:${data.contact_number}`;
                    contactLink.textContent = data.contact_number || 'N/A';
                    

                    // Populate Location Information
                    document.getElementById('viewRegBarangay').textContent = data.barangay || 'N/A';

                    // Populate Livelihood Information
                    document.getElementById('viewRegLivelihood').textContent = data.livelihood_description || 'N/A';

                    // Show other livelihood if exists
                    if (data.other_livelihood) {
                        document.getElementById('viewOtherLivelihoodContainer').style.display = 'block';
                        document.getElementById('viewRegOtherLivelihood').textContent = data.other_livelihood;
                    } else {
                        document.getElementById('viewOtherLivelihoodContainer').style.display = 'none';
                    }

                    // Show secondary livelihood if exists
                    if (data.secondary_livelihood) {
                        document.getElementById('viewSecondaryLivelihoodContainer').style.display = 'block';
                        document.getElementById('viewRegSecondaryLivelihood').textContent = data.secondary_livelihood_description || data.secondary_livelihood;
                    } else {
                        document.getElementById('viewSecondaryLivelihoodContainer').style.display = 'none';
                    }

                    // Show other secondary livelihood if exists
                    if (data.other_secondary_livelihood) {
                        document.getElementById('viewOtherSecondaryLivelihoodContainer').style.display = 'block';
                        document.getElementById('viewRegOtherSecondaryLivelihood').textContent = data.other_secondary_livelihood;
                    } else {
                        document.getElementById('viewOtherSecondaryLivelihoodContainer').style.display = 'none';
                    }

                    // Populate Status Information
                    const statusElement = document.getElementById('viewRegStatus');
                    statusElement.innerHTML = `<span class="badge bg-${data.status_color}" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">${data.formatted_status}</span>`;
                    
                    document.getElementById('viewRegCreatedAt').textContent = data.created_at || 'N/A';
                    document.getElementById('viewRegUpdatedAt').textContent = data.updated_at || 'N/A';

                    // Populate Supporting Document
                    const docContainer = document.getElementById('viewRegDocumentContainer');
                    if (data.document_path) {
                        docContainer.innerHTML = `
                            <div class="p-4 border border-primary rounded bg-light">
                                <i class="fas fa-file-alt fa-3x mb-3" style="color: #0d6efd;"></i>
                                <h6>Supporting Document</h6>
                                <span class="badge bg-primary mb-3">Uploaded</span>
                                <br>
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="viewDocument('${data.document_path}', 'FishR Registration #${data.registration_number} - Supporting Document')">
                                    <i class="fas fa-eye me-1"></i>View Document
                                </button>
                            </div>
                        `;
                    } else {
                        docContainer.innerHTML = `
                            <div class="p-4 border border-secondary rounded">
                                <i class="fas fa-file-slash fa-3x mb-3" style="color: #6c757d;"></i>
                                <h6>No Document Uploaded</h6>
                                <span class="badge bg-secondary">Not Uploaded</span>
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
                    showToast('error', error.message || 'Error loading registration details. Please try again.');
                    modal.hide();
                });
        }

        // FIXED: Unified document viewing function - use this ONLY ONCE
        function viewDocument(path, filename = null, applicationId = null) {
            // Input validation
            if (!path || path.trim() === '') {
                showToast('error', 'No document path provided');
                return;
            }

            const documentViewer = document.getElementById('documentViewer');
            const documentModal = document.getElementById('documentModal');
            const modal = new bootstrap.Modal(documentModal);

            // Show loading state first
            documentViewer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Loading document...</p>
                </div>`;

            // Show modal
            modal.show();

            // Manage z-index after modal is shown
            setTimeout(() => {
                const backdrop = documentModal.previousElementSibling;
                const annexesModal = document.getElementById('annexesModal');
                
                if (annexesModal && annexesModal.classList.contains('show')) {
                    // If annexes modal is open, put document modal above it
                    documentModal.style.zIndex = '1070';
                    if (backdrop) backdrop.style.zIndex = '1069';
                } else {
                    // Otherwise, use normal z-index
                    documentModal.style.zIndex = '1060';
                    if (backdrop) backdrop.style.zIndex = '1059';
                }
            }, 100);

            // Update modal title if filename is provided
            const modalTitle = document.querySelector('#documentModal .modal-title');
            if (filename) {
                modalTitle.innerHTML = `<i class="fas fa-file-alt me-2"></i>${escapeHtml(filename)}`;
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


        // Helper function to toggle image zoom
        function toggleImageZoom(img) {
            if (img.style.transform === 'scale(2)') {
                img.style.transform = 'scale(1)';
                img.style.cursor = 'zoom-in';
                img.style.transition = 'transform 0.3s ease';
            } else {
                img.style.transform = 'scale(2)';
                img.style.cursor = 'zoom-out';
                img.style.transition = 'transform 0.3s ease';
                img.style.zIndex = '1050';
            }
        }

        // Download file function
        function downloadFile(url, filename) {
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Check for changes and provide visual feedback
        function checkForChanges() {
            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');
            const updateButton = document.getElementById('updateStatusBtn');

            if (!statusSelect.dataset.originalStatus) return;

            const statusChanged = statusSelect.value !== statusSelect.dataset.originalStatus;
            const remarksChanged = remarksTextarea.value.trim() !== (remarksTextarea.dataset.originalRemarks || '').trim();

            // Visual feedback for status field
            statusSelect.classList.toggle('form-changed', statusChanged);

            // Visual feedback for remarks field
            remarksTextarea.classList.toggle('form-changed', remarksChanged);

            // Update button state
            const hasChanges = statusChanged || remarksChanged;

            if (hasChanges) {
                updateButton.classList.remove('no-changes');
                updateButton.innerHTML = '<i class="fas fa-save me-2"></i>Update Status';
                updateButton.disabled = false;
            } else {
                updateButton.classList.add('no-changes');
                updateButton.innerHTML = '<i class="fas fa-check me-2"></i>No Changes';
                updateButton.disabled = false;
            }
        }

        // Update remarks character counter
        function updateFishrRemarksCounterUpdate() {
            const textarea = document.getElementById('remarks');
            const charCount = document.getElementById('charCountUpdate');
            const counter = document.getElementById('remarksCounterUpdate');
            
            if (textarea && charCount) {
                charCount.textContent = textarea.value.length;
                
                // Change color based on length
                if (textarea.value.length > 900) {
                    counter.classList.add('text-warning');
                    counter.classList.remove('text-muted');
                } else {
                    counter.classList.remove('text-warning');
                    counter.classList.add('text-muted');
                }
            }
        }

        /// Add event listeners when document is ready
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

        // ========== ANNEXES FUNCTIONALITY ==========
        // Show annexes modal
        function showAnnexesModal(id) {
            const modal = new bootstrap.Modal(document.getElementById('annexesModal'));
            modal.show();

            // Show loading
            document.getElementById('annexesLoading').style.display = 'block';
            document.getElementById('annexesContent').style.display = 'none';

            // Load registration details and annexes
            loadAnnexesData(id);
        }

        // Load annexes data
        function loadAnnexesData(id) {
            fetch(`/admin/fishr-registrations/${id}`, {
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
                    if (!data.success) throw new Error(data.message || 'Failed to load data');

                    // Hide loading, show content
                    document.getElementById('annexesLoading').style.display = 'none';
                    document.getElementById('annexesContent').style.display = 'block';

                    // Populate registration info
                    document.getElementById('annexRegistrationId').value = id;
                    document.getElementById('annexRegNumber').textContent = data.data.registration_number;
                    document.getElementById('annexApplicantName').textContent = data.data.full_name;
                    document.getElementById('annexBarangay').textContent = data.data.barangay || 'N/A';
                    document.getElementById('annexStatus').innerHTML =
                        `<span class="badge bg-${data.data.status_color}">${data.data.formatted_status}</span>`;

                    // Load existing annexes
                    loadExistingAnnexes(id);

                    // Reset form
                    resetAnnexForm();
                })
                .catch(error => {
                    console.error('Error loading annexes data:', error);
                    showToast('error', 'Failed to load data: ' + error.message);

                    // Hide loading, show error
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
// FIXED: Load existing annexes with proper file path handling
function loadExistingAnnexes(id) {
    fetch(`/admin/fishr-registrations/${id}/annexes`, {
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
            const annexesList = document.getElementById('annexesList');

            if (data.success && data.annexes && data.annexes.length > 0) {
                let annexesHtml = '';
                data.annexes.forEach((annex, index) => {
                    const uploadDate = new Date(annex.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    // IMPORTANT: Ensure file_path is properly set
                    const filePath = annex.file_path ? String(annex.file_path).trim() : '';
                    
                    // Store the annex data in a dataset on the row element
                    // This will be retrieved when preview/download is clicked
                    const annexDataJson = JSON.stringify({
                        id: annex.id,
                        registrationId: id,
                        filePath: filePath,
                        fileName: annex.file_name || annex.title || 'Document',
                        title: annex.title,
                        fileExtension: annex.file_extension
                    });

                    annexesHtml += `
                        <div class="document-item border rounded p-3 mb-3" 
                             id="annex-${annex.id}" 
                             data-annex-json='${annexDataJson}'>
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1 text-primary">${escapeHtml(annex.title)}</h6>
                                    <p class="mb-1 text-muted small">${escapeHtml(annex.description || 'No description')}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>Uploaded: ${uploadDate}
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-file me-1"></i>Size: ${formatFileSize(annex.file_size)}
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="previewAnnexFixed(${annex.id})" 
                                                title="Preview"
                                                type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="downloadAnnexFixed(${annex.id})" 
                                                title="Download"
                                                type="button">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteAnnex(${id}, ${annex.id})" 
                                                title="Delete"
                                                type="button">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
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

// FIXED: Preview Annex - Retrieve data from data attribute
function previewAnnexFixed(annexId) {
    try {
        // Get the annex element
        const annexElement = document.getElementById(`annex-${annexId}`);
        
        if (!annexElement) {
            showToast('error', 'Annex not found');
            return;
        }
        
        // Parse the stored JSON data
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
            console.error('File path is missing or invalid:', {
                filePath,
                annexData
            });
            showToast('error', 'File path not available for this annex');
            return;
        }
        
        console.log('Preview annex:', {
            annexId,
            filePath,
            fileName
        });

        // Use the existing viewDocument function
        viewDocument(filePath, fileName, annexData.registrationId);

    } catch (error) {
        console.error('Error in previewAnnexFixed:', error);
        showToast('error', 'Error previewing annex: ' + error.message);
    }
}

// FIXED: Download Annex - Retrieve data from data attribute
function downloadAnnexFixed(annexId) {
    try {
        // Get the annex element
        const annexElement = document.getElementById(`annex-${annexId}`);
        
        if (!annexElement) {
            showToast('error', 'Annex not found');
            return;
        }
        
        // Parse the stored JSON data
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
            console.error('File path is missing or invalid:', {
                filePath,
                annexData
            });
            showToast('error', 'File path not available for this annex');
            return;
        }

        console.log('Download annex:', {
            annexId,
            filePath,
            fileName
        });

        // Show confirmation before downloading
        showConfirmationToast(
            'Download File',
            `Download: ${fileName}?`,
            () => proceedWithDownload(filePath, fileName)
        );

    } catch (error) {
        console.error('Error in downloadAnnexFixed:', error);
        showToast('error', 'Error downloading annex: ' + error.message);
    }
}

// Utility: Format file size (if not already defined)
function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return 'Unknown size';
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
}

// Utility: Escape HTML (if not already defined)
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// FIXED: Preview Annex - Get file path from annexes list
function previewAnnex(registrationId, annexId) {
    // Get the annex element to retrieve file path from data attribute
    const annexElement = document.getElementById(`annex-${annexId}`);
    
    if (!annexElement) {
        showToast('error', 'Annex not found');
        return;
    }
    
    const filePath = annexElement.getAttribute('data-file-path');
    const fileName = annexElement.getAttribute('data-file-name');
    
    if (!filePath || filePath === 'undefined' || filePath === '') {
        showToast('error', 'File path not available');
        return;
    }
    
    // Use the existing viewDocument function
    viewDocument(filePath, fileName, registrationId);
}

// FIXED: Download Annex - Get file path from annexes list
function downloadAnnex(registrationId, annexId) {
    // Get the annex element to retrieve file path from data attribute
    const annexElement = document.getElementById(`annex-${annexId}`);
    
    if (!annexElement) {
        showToast('error', 'Annex not found');
        return;
    }
    
    const filePath = annexElement.getAttribute('data-file-path');
    const fileName = annexElement.getAttribute('data-file-name');
    
    if (!filePath || filePath === 'undefined' || filePath === '') {
        showToast('error', 'File path not available');
        return;
    }
    
    // Show confirmation before downloading
    showConfirmationToast(
        'Download File',
        `Download: ${fileName}?`,
        () => proceedWithDownload(filePath, fileName)
    );
}

        // Proceed with download
        function proceedWithDownload(filePath, fileName) {
            const fileUrl = `/storage/${filePath}`;
            const link = document.createElement('a');
            link.href = fileUrl;
            link.download = fileName || 'document';
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

        // Upload annex with validation
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

            // File size validation (10MB)
            const maxFileSize = 10 * 1024 * 1024; // 10MB
            if (fileInput.files[0].size > maxFileSize) {
                showValidationError('annexFile', 'annexFileError', 'File size must be less than 10MB');
                return;
            }

            // Validate file type
            const allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'];
            const fileExtension = fileInput.files[0].name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                showValidationError('annexFile', 'annexFileError', 'File type not allowed. Supported: PDF, DOC, DOCX, JPG, PNG, GIF');
                return;
            }

            // Clear validation errors
            clearValidationErrors();

            // Show confirmation toast
            showConfirmationToast(
                'Upload Annex',
                `Are you sure you want to upload this annex?\n\nFile: ${fileInput.files[0].name}\nSize: ${formatFileSize(fileInput.files[0].size)}`,
                () => proceedWithAnnexUpload(id, fileInput, title, description)
            );
        }

        // Proceed with annex upload
        function proceedWithAnnexUpload(id, fileInput, title, description) {
            // Show loading state
            const uploadBtn = document.querySelector('[onclick="uploadAnnex()"]');
            const originalContent = uploadBtn.innerHTML;
            uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Uploading...';
            uploadBtn.disabled = true;

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('title', title);
            formData.append('description', description);

            fetch(`/admin/fishr-registrations/${id}/annexes`, {
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
                        resetAnnexForm();
                        loadExistingAnnexes(id);
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

        // Delete annex with confirmation
        function deleteAnnex(registrationId, annexId) {
            const button = document.querySelector(`button[onclick="deleteAnnex(${registrationId}, ${annexId})"]`);
            const fileName = button?.dataset.fileName || 'this annex';

            showConfirmationToast(
                'Delete Annex',
                `Are you sure you want to delete this annex?\n\nPlease confirm to continue.`,
                () => proceedWithAnnexDelete(registrationId, annexId)
            );
        }

        // Proceed with annex deletion
        function proceedWithAnnexDelete(registrationId, annexId) {
            fetch(`/admin/fishr-registrations/${registrationId}/annexes/${annexId}`, {
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
                    if (data.success) {
                        showToast('success', 'Annex deleted successfully');

                        // Remove from UI with animation
                        const annexElement = document.getElementById(`annex-${annexId}`);
                        if (annexElement) {
                            annexElement.style.transition = 'opacity 0.3s ease';
                            annexElement.style.opacity = '0';
                            setTimeout(() => {
                                annexElement.remove();
                                // Reload if no annexes left
                                const annexesList = document.getElementById('annexesList');
                                if (!annexesList.querySelector('.document-item')) {
                                    loadExistingAnnexes(document.getElementById('annexRegistrationId').value);
                                }
                            }, 300);
                        }
                    } else {
                        throw new Error(data.message || 'Failed to delete annex');
                    }
                })
                .catch(error => {
                    console.error('Error deleting annex:', error);
                    showToast('error', 'Failed to delete annex: ' + error.message);
                });
        }

        // Reset annex form
        function resetAnnexForm() {
            document.getElementById('annexFile').value = '';
            document.getElementById('annexTitle').value = '';
            document.getElementById('annexDescription').value = '';
            document.getElementById('annexDescCount').textContent = '0';
            clearValidationErrors();
        }

        // Show validation error
        function showValidationError(inputId, errorId, message) {
            const input = document.getElementById(inputId);
            const error = document.getElementById(errorId);

            input.classList.add('is-invalid');
            error.textContent = message;
        }

        // Clear validation errors
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

        // UPDATE ANNEX DESCRIPTION COUNTER - NEW IMPLEMENTATION
        function updateAnnexDescriptionCounter() {
            const textarea = document.getElementById('annexDescription');
            const counter = document.getElementById('annexDescCount');

            if (textarea && counter) {
                const charCount = textarea.value.length;
                counter.textContent = charCount;

                // Enforce max length (500)
                if (charCount > 500) {
                    textarea.value = textarea.value.substring(0, 500);
                    counter.textContent = '500';
                }

                // Change color when approaching limit
                if (charCount > 450) {
                    counter.parentElement.classList.add('text-warning');
                    counter.parentElement.classList.remove('text-muted');
                } else {
                    counter.parentElement.classList.remove('text-warning');
                    counter.parentElement.classList.add('text-muted');
                }
            }
        }

        // Utility: Format file size
        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return 'Unknown size';

            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Utility: Escape HTML to prevent XSS
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Initialize character counter on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            const annexDesc = document.getElementById('annexDescription');
            
            if (annexDesc) {
                annexDesc.addEventListener('input', updateAnnexDescriptionCounter);
            }
        });


        // ========== END ANNEXES FUNCTIONALITY ==========

        // Download file function for FISHR-style buttons
        function downloadFile(url, filename) {
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Delete registration with confirmation toast
        function deleteRegistration(id, registrationNumber) {
            showConfirmationToast(
                'Delete Registration',
                `Are you sure you want to delete registration ${registrationNumber}?\n\nThis action cannot be undone and will also delete all associated documents and annexes.`,
                () => proceedWithRegistrationDelete(id, registrationNumber)
            );
        }
        // Proceed with registration deletion
        function proceedWithRegistrationDelete(id, registrationNumber) {
            fetch(`/admin/fishr-registrations/${id}`, {
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
                        showToast('success', data.message || 'Registration deleted successfully');

                        // Remove row from table with animation
                        const row = document.querySelector(`tr[data-registration-id="${id}"]`);
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
                        throw new Error(data.message || 'Failed to delete registration');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Failed to delete registration: ' + error.message);
                });
        }

        // Show add FishR modal - Enhanced
        function showAddFishrModal() {
            const modal = new bootstrap.Modal(document.getElementById('addFishrModal'));

            // Reset form
            document.getElementById('addFishrForm').reset();

            // Remove any validation errors
            document.querySelectorAll('#addFishrModal .is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('#addFishrModal .invalid-feedback').forEach(el => el.remove());

            // Clear document preview
            const preview = document.getElementById('fishr_doc_preview');
            if (preview) {
                preview.innerHTML = '';
                preview.style.display = 'none';
            }

            // Hide other livelihood field
            document.getElementById('other_livelihood_container').style.display = 'none';

            modal.show();
        }

      
        // Real-time validation for contact number
        document.getElementById('fishr_contact_number')?.addEventListener('input', function() {
            validateFishrContactNumber(this.value);
        });

        function validateFishrContactNumber(contactNumber) {
            const input = document.getElementById('fishr_contact_number');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!contactNumber || contactNumber.trim() === '') {
                return;
            }

            const phoneRegex = /^(\+639|09)\d{9}$/;

            if (!phoneRegex.test(contactNumber.trim())) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }

        // Auto-capitalize name fields
        function capitalizeFishrName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        }

        document.getElementById('fishr_first_name')?.addEventListener('blur', function() {
            capitalizeFishrName(this);
        });

        document.getElementById('fishr_middle_name')?.addEventListener('blur', function() {
            capitalizeFishrName(this);
        });

        document.getElementById('fishr_last_name')?.addEventListener('blur', function() {
            capitalizeFishrName(this);
        });

        // Document preview
        function previewFishrDocument(inputId, previewId) {
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


function isValidPhoneNumber(phone) {
    const phonePattern = /^09\d{9}$/;
    return phonePattern.test(phone);
}

        // Validate FishR contact number - Enhanced
        function validateFishrContactNumber(contactNumber) {
            const input = document.getElementById('fishr_contact_number');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!contactNumber || contactNumber.trim() === '') {
                return;
            }

            const phoneRegex = /^(\+639|09)\d{9}$/;

            if (!phoneRegex.test(contactNumber.trim())) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }

  /**
 * FIXED: submitAddFishr - Complete validation before submission */
 function submitAddFishr() {
    console.log('🔍 Starting FishR form validation...');
    
    const form = document.getElementById('addFishrForm');
    
    // STEP 1: CHECK IF FORM ALREADY HAS VALIDATION ERRORS FROM REAL-TIME VALIDATION
    const hasExistingErrors = form.querySelectorAll('.is-invalid').length > 0;
    
    if (hasExistingErrors) {
        showToast('error', 'Please fix all validation errors before submitting');
        console.log('❌ FORM HAS EXISTING ERRORS - STOPPING SUBMISSION');
        return;
    }
    
    // STEP 2: VALIDATE ALL FIELDS
    const isValid = validateFishrFormOnSubmit(false);
    console.log('✅ Validation complete. Result:', isValid);
    
    if (!isValid) {
        console.error('❌ Form validation failed');
        showToast('error', 'Please fix all validation errors before submitting');
        return;
    }

    console.log('✅ Form validation passed');

    // STEP 3: PREPARE FORMDATA
    const formData = new FormData();
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    console.log('CSRF Token:', csrfToken ? 'Found' : 'NOT FOUND');
    
    if (!csrfToken) {
        showToast('error', 'Security token not found. Please refresh the page.');
        return;
    }
    
    formData.append('_token', csrfToken);

    // Get all form values
    const firstName = document.getElementById('fishr_first_name')?.value?.trim() || '';
    const lastName = document.getElementById('fishr_last_name')?.value?.trim() || '';
    const sex = document.getElementById('fishr_sex')?.value?.trim() || '';
    const contact = document.getElementById('fishr_contact_number')?.value?.trim() || '';
    const barangay = document.getElementById('fishr_barangay')?.value?.trim() || '';
    const mainLivelihood = document.getElementById('fishr_main_livelihood')?.value?.trim() || '';
    const otherLivelihood = document.getElementById('fishr_other_livelihood')?.value?.trim() || '';
    const secondaryLivelihood = document.getElementById('fishr_secondary_livelihood')?.value?.trim() || '';
    const otherSecondaryLivelihood = document.getElementById('fishr_other_secondary_livelihood')?.value?.trim() || '';
    const status = document.getElementById('fishr_status')?.value?.trim() || 'pending';
    const remarks = document.getElementById('fishr_remarks')?.value?.trim() || '';

    // Append all form data
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('sex', sex);
    formData.append('contact_number', contact);
    formData.append('barangay', barangay);
    formData.append('main_livelihood', mainLivelihood);
    
    if (mainLivelihood === 'others') {
        formData.append('other_livelihood', otherLivelihood);
    }

    if (secondaryLivelihood) {
        formData.append('secondary_livelihood', secondaryLivelihood);
        if (secondaryLivelihood === 'others') {
            formData.append('other_secondary_livelihood', otherSecondaryLivelihood);
        }
    }

    formData.append('status', status);
    formData.append('remarks', remarks);

    // Add document if uploaded
    const docInput = document.getElementById('fishr_supporting_document');
    if (docInput?.files?.[0]) {
        formData.append('supporting_document', docInput.files[0]);
    }

    // STEP 4: SHOW LOADING STATE
    const submitBtn = document.querySelector('#addFishrModal .btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
    submitBtn.disabled = true;

    console.log('📨 Sending POST request to /admin/fishr-registrations');

    // STEP 5: SUBMIT TO SERVER
    fetch('/admin/fishr-registrations/create', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('📦 Response status:', response.status);
        if (!response.ok && response.status !== 422) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Response data:', data);
        
        if (data.success) {
            showToast('success', data.message || 'Registration created successfully');
            const modal = bootstrap.Modal.getInstance(document.getElementById('addFishrModal'));
            if (modal) modal.hide();
            
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else if (data.errors) {
            console.error('❌ Validation errors from server:', data.errors);
            showToast('error', 'Please fix validation errors');
        } else {
            throw new Error(data.message || 'Failed to create registration');
        }
    })
    .catch(error => {
        console.error('🔴 Error:', error);
        showToast('error', error.message || 'An error occurred while creating the registration');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
// ====================================================
// HELPER FUNCTIONS - ADMIN MODAL ERROR HANDLING
// ====================================================

/**
 * Mark field error for Admin Modals (Add/Edit)
 */
function markAdminFieldError(input, message = '') {
    if (!input) return;
    
    const existingError = input.parentNode.querySelector('.invalid-feedback');
    if (existingError) existingError.remove();
    
    input.classList.add('is-invalid');
    input.style.borderColor = '#ff6b6b';
    
    if (message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
    }
}

/**
 * Clear field error for Admin Modals
 */
function clearAdminFieldError(input) {
    if (!input) return;
    
    input.classList.remove('is-invalid');
    input.style.borderColor = '';
    
    const errorDiv = input.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) errorDiv.remove();
}

/**
 * Mark edit field error
 */
function markEditFieldError(input, message = '') {
    markAdminFieldError(input, message);
}

/**
 * Clear edit field error
 */
function clearEditFieldError(input) {
    clearAdminFieldError(input);
}

/**
 * Validate secondary livelihood in Admin context
 */
function validateAdminSecondaryLivelihood() {
    const mainValue = document.getElementById('fishr_main_livelihood')?.value || '';
    const secondaryValue = document.getElementById('fishr_secondary_livelihood')?.value || '';
    const warning = document.getElementById('add_secondary_livelihood_warning');

    if (secondaryValue && mainValue && secondaryValue === mainValue) {
        if (warning) warning.style.display = 'block';
        return false;
    } else {
        if (warning) warning.style.display = 'none';
        return true;
    }
}
/**
 * Validate Secondary Livelihood Text Cannot Match Main Text (Edit Modal)
 */
function validateEditSecondaryLivelihoodTextMatch() {
    const mainLivelihoodSelect = document.getElementById('edit_fishr_livelihood');
    const secondaryLivelihoodSelect = document.getElementById('edit_fishr_secondary_livelihood');
    const mainOthersInput = document.getElementById('edit_fishr_other_livelihood');
    const secondaryOthersInput = document.getElementById('edit_fishr_other_secondary_livelihood');

    if (!secondaryOthersInput || !secondaryLivelihoodSelect) return true;

    const mainValue = mainLivelihoodSelect?.value || '';
    const secondaryValue = secondaryLivelihoodSelect?.value || '';
    const mainOthersValue = (mainOthersInput?.value || '').trim().toLowerCase();
    const secondaryOthersValue = (secondaryOthersInput?.value || '').trim().toLowerCase();

    // Only warn if BOTH are "others" AND have identical text
    if (mainValue === 'others' && secondaryValue === 'others') {
        if (mainOthersValue && secondaryOthersValue && mainOthersValue === secondaryOthersValue) {
            return false;
        }
    }

    return true;
}

/**
 * Validate Secondary in form submission
 */
function validateAddSecondaryLivelihoodInForm() {
    const secondaryLivelihood = document.getElementById('fishr_secondary_livelihood').value;
    const mainLivelihood = document.getElementById('fishr_main_livelihood').value;
    
    if (secondaryLivelihood === 'others') {
        const otherSecondaryLivelihood = document.getElementById('fishr_other_secondary_livelihood');
        if (!otherSecondaryLivelihood.value || otherSecondaryLivelihood.value.trim() === '') {
            otherSecondaryLivelihood.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = 'Please specify the other secondary livelihood';
            
            const existingError = otherSecondaryLivelihood.parentNode.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();
            
            otherSecondaryLivelihood.parentNode.appendChild(errorDiv);
            return false;
        }
    }
    
    // Allow if both are "others"
    if (secondaryLivelihood === 'others' && mainLivelihood === 'others') {
        return true;
    }
    
    // Validate secondary livelihood is not same as main (if not both "others")
    if (secondaryLivelihood && mainLivelihood && secondaryLivelihood === mainLivelihood) {
        const secondaryInput = document.getElementById('fishr_secondary_livelihood');
        secondaryInput.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = 'Secondary livelihood cannot be the same as main livelihood';
        
        const existingError = secondaryInput.parentNode.querySelector('.invalid-feedback');
        if (existingError) existingError.remove();
        
        secondaryInput.parentNode.appendChild(errorDiv);
        return false;
    }
    
    return true;
}
// ====================================================
// SETUP FUNCTIONS FOR ADD & EDIT MODALS
// ====================================================

/**
 * Setup Add Modal Listeners - Called when modal is shown
 */
function setupAddModalListeners() {
    console.log('✅ Setting up Add Modal listeners');
    
    // Name fields - Auto-capitalize on blur
    ['fishr_first_name', 'fishr_middle_name', 'fishr_last_name'].forEach(id => {
        const input = document.getElementById(id);
        if (!input) return;
        
        input.addEventListener('blur', function(e) {
            capitalizeNameField(this);
        });
    });

    // Main livelihood - Toggle other field and validate secondary
    const mainLivelih = document.getElementById('fishr_main_livelihood');
    if (mainLivelih) {
        mainLivelih.addEventListener('change', () => {
            toggleOtherLivelihood();
            validateAdminSecondaryLivelihood();
        });
    }

    // Secondary livelihood - Toggle other field and validate
    const secondaryLivelih = document.getElementById('fishr_secondary_livelihood');
    if (secondaryLivelih) {
        secondaryLivelih.addEventListener('change', () => {
            toggleAddOtherSecondaryLivelihood();
            validateAdminSecondaryLivelihood();
        });
    }

    // Contact number - Real-time validation
    const contact = document.getElementById('fishr_contact_number');
    if (contact) {
        contact.addEventListener('input', function() {
            validateFishrContactNumber(this.value);
        });
    }

    // Other livelihood - Auto-capitalize on blur
    const otherLivelih = document.getElementById('fishr_other_livelihood');
    if (otherLivelih) {
        otherLivelih.addEventListener('blur', function() {
            capitalizeAddOtherLivelihood();
        });
    }

    // Other secondary livelihood - Auto-capitalize on blur
    const otherSecondaryLivelih = document.getElementById('fishr_other_secondary_livelihood');
    if (otherSecondaryLivelih) {
        otherSecondaryLivelih.addEventListener('blur', function() {
            capitalizeAddOtherSecondaryLivelihood();
        });
    }
}

/**
 * Setup Edit Modal Listeners - Called when modal is shown
 */
function setupEditModalListeners() {
    console.log('✅ Setting up Edit Modal listeners');
    
    // Name fields - Auto-capitalize on blur
    ['edit_fishr_first_name', 'edit_fishr_middle_name', 'edit_fishr_last_name'].forEach(id => {
        const input = document.getElementById(id);
        if (!input) return;
        
        input.addEventListener('blur', function(e) {
            capitalizeNameField(this);
            const form = document.getElementById('editFishrForm');
            if (form?.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
            }
        });
    });

    // Contact number - Real-time validation
    const contact = document.getElementById('edit_fishr_contact_number');
    if (contact) {
        contact.addEventListener('input', function() {
            validateEditFishrContactNumber(this.value);
        });
    }

    // Main livelihood
    const mainLivelih = document.getElementById('edit_fishr_livelihood');
    if (mainLivelih) {
        mainLivelih.addEventListener('change', () => {
            toggleEditOtherFishrLivelihood();
            validateEditSecondaryLivelihood();
            const form = document.getElementById('editFishrForm');
            if (form?.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
            }
        });
    }

    // Secondary livelihood
    const secondaryLivelih = document.getElementById('edit_fishr_secondary_livelihood');
    if (secondaryLivelih) {
        secondaryLivelih.addEventListener('change', () => {
            toggleEditOtherSecondaryFishrLivelihood();
            validateEditSecondaryLivelihood();
            const form = document.getElementById('editFishrForm');
            if (form?.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
            }
        });
    }
}

        // Auto-capitalize name fields
        function capitalizeFishrName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        }

        // Add event listeners for auto-capitalization on blur
        document.addEventListener('DOMContentLoaded', function() {
            const addFishrModal = document.getElementById('addFishrModal');
            
            if (addFishrModal) {
                addFishrModal.addEventListener('shown.bs.modal', function() {
                    const firstNameInput = document.getElementById('fishr_first_name');
                    const middleNameInput = document.getElementById('fishr_middle_name');
                    const lastNameInput = document.getElementById('fishr_last_name');

                    if (firstNameInput) {
                        firstNameInput.addEventListener('blur', function() {
                            capitalizeFishrName(this);
                        });
                    }

                    if (middleNameInput) {
                        middleNameInput.addEventListener('blur', function() {
                            capitalizeFishrName(this);
                        });
                    }

                    if (lastNameInput) {
                        lastNameInput.addEventListener('blur', function() {
                            capitalizeFishrName(this);
                        });
                    }

                    // Real-time validation for contact number
                    const contactInput = document.getElementById('fishr_contact_number');
                    if (contactInput) {
                        contactInput.addEventListener('input', function() {
                            validateFishrContactNumber(this.value);
                        });
                    }

                });
            }
        });

       /**
 * Show Edit FishR Modal - Opens modal and loads registration data
 */
function showEditFishrModal(registrationId) {
    const modal = new bootstrap.Modal(document.getElementById('editFishrModal'));
    
    // Show loading state
    document.getElementById('editFishrNumber').textContent = 'Loading...';
    
    // Show modal
    modal.show();
    
    // Fetch registration details
    fetch(`/admin/fishr-registrations/${registrationId}`, {
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
            if (!response.success) throw new Error(response.message || 'Failed to load registration');
            
            const data = response.data;
            
            // Update modal title
            document.getElementById('editFishrNumber').textContent = data.registration_number;
            
            // Initialize form with data
            initializeEditFishrForm(registrationId, data);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error loading registration: ' + error.message);
            modal.hide();
        });
}

/**
 * Initialize Edit Form with registration data
 */
function initializeEditFishrForm(registrationId, data) {
    const form = document.getElementById('editFishrForm');
    
    // Populate personal information
    document.getElementById('edit_fishr_first_name').value = data.first_name || '';
    document.getElementById('edit_fishr_middle_name').value = data.middle_name || '';
    document.getElementById('edit_fishr_last_name').value = data.last_name || '';
    document.getElementById('edit_fishr_extension').value = data.name_extension || '';
    document.getElementById('edit_fishr_sex').value = data.sex || '';
    document.getElementById('edit_fishr_contact_number').value = data.contact_number || '';
    
    // Populate location
    document.getElementById('edit_fishr_barangay').value = data.barangay || '';
    
    // Populate main livelihood
    document.getElementById('edit_fishr_livelihood').value = data.main_livelihood || '';
    document.getElementById('edit_fishr_other_livelihood').value = data.other_livelihood || '';

    // Populate secondary livelihood (NEW)
    document.getElementById('edit_fishr_secondary_livelihood').value = data.secondary_livelihood || '';
    document.getElementById('edit_fishr_other_secondary_livelihood').value = data.other_secondary_livelihood || '';

    // Toggle other livelihood fields
    toggleEditOtherFishrLivelihood();
    toggleEditOtherSecondaryFishrLivelihood();

    // Validate secondary livelihood match
    validateEditSecondaryLivelihood();
    
    // Populate registration number (read-only)
    document.getElementById('edit_fishr_reg_number').value = data.registration_number || '';
    
    // Populate status badge
    const statusBadge = document.getElementById('edit_fishr_status_badge');
    statusBadge.className = `badge bg-${data.status_color} fs-6`;
    statusBadge.textContent = data.formatted_status;
    
    // Populate date applied
    document.getElementById('edit_fishr_created_at').textContent = data.created_at || '-';
    
   // Handle document preview
    const previewContainer = document.getElementById('edit_fishr_supporting_document_preview');
    if (data.document_path) {
        displayEditFishrExistingDocument(data.document_path, 'edit_fishr_supporting_document_preview');
    } else {
        previewContainer.innerHTML = '<small class="text-muted d-block">No document currently uploaded</small>';
    }
    
    // Store original data for change detection
    const originalData = {
        first_name: data.first_name || '',
        middle_name: data.middle_name || '',
        last_name: data.last_name || '',
        name_extension: data.name_extension || '',
        sex: data.sex || '',
        contact_number: data.contact_number || '',
        barangay: data.barangay || '',
        main_livelihood: data.main_livelihood || '',
        other_livelihood: data.other_livelihood || '',
        secondary_livelihood: data.secondary_livelihood || '',
        other_secondary_livelihood: data.other_secondary_livelihood || ''
    };
    
    form.dataset.originalData = JSON.stringify(originalData);
    form.dataset.registrationId = registrationId;
    form.dataset.hasChanges = 'false';
    
    // Clear validation states
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    
    // Reset button state
    const submitBtn = document.getElementById('editFishrSubmitBtn');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
    
    // Add change listeners
    addEditFishrFormChangeListeners(registrationId);
}

/**
 * Add Change Listeners to Edit Form
 */
function addEditFishrFormChangeListeners(registrationId) {
    const form = document.getElementById('editFishrForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('change', () => handleEditFishrFormChange());
        input.addEventListener('input', () => handleEditFishrFormChange());
    });
    
    // Special handler for livelihood changes
    document.getElementById('edit_fishr_livelihood').addEventListener('change', function() {
        toggleEditOtherFishrLivelihood();
        validateEditSecondaryLivelihood();
    });

    document.getElementById('edit_fishr_secondary_livelihood').addEventListener('change', function() {
        toggleEditOtherSecondaryFishrLivelihood();
        validateEditSecondaryLivelihood();
    });
}

/**
 * Handle Edit Form Change
 */
function handleEditFishrFormChange() {
    const form = document.getElementById('editFishrForm');
    if (form.dataset.registrationId) {
        checkEditFishrFormChanges(form.dataset.registrationId);
    }
}
/**
 * Check for Form Changes and Update UI - UPDATED
 */
function checkEditFishrFormChanges(registrationId) {
    const form = document.getElementById('editFishrForm');
    if (!form.dataset.originalData) return;

    const originalData = JSON.parse(form.dataset.originalData);
    let hasChanges = false;

    const fields = [
    'first_name', 'middle_name', 'last_name', 'name_extension',
    'sex', 'contact_number', 'barangay', 'main_livelihood', 'other_livelihood',
    'secondary_livelihood', 'other_secondary_livelihood'
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

    // Also check for file input changes
    const fileInput = document.getElementById('edit_fishr_supporting_document');
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
        hasChanges = true;
    }

    // Update button state
    const submitBtn = document.getElementById('editFishrSubmitBtn');
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
 * Handle Edit FishR Form Submission - with changes summary
 */
function handleEditFishrSubmit() {
    const form = document.getElementById('editFishrForm');
    const registrationId = form.dataset.registrationId;
    const submitBtn = document.getElementById('editFishrSubmitBtn');

    // STEP 1: CHECK IF FORM ALREADY HAS VALIDATION ERRORS FROM REAL-TIME VALIDATION
    const hasExistingErrors = form.querySelectorAll('.is-invalid').length > 0;
    
    if (hasExistingErrors) {
        showToast('error', 'Please fix all validation errors before submitting');
        console.log('❌ FORM HAS EXISTING ERRORS - STOPPING SUBMISSION');
        return;
    }

    // STEP 2: VALIDATE FORM
    if (!validateFishrFormOnSubmit(true)) {
        showToast('error', 'Please fix all validation errors');
        return;
    }

    // STEP 3: CHECK FOR ACTUAL CHANGES
    const hasChanges = submitBtn?.dataset.hasChanges === 'true';
    if (!hasChanges) {
        showToast('warning', 'No changes detected. Please modify the fields before updating.');
        return;
    }

    // STEP 4: BUILD CHANGES SUMMARY
    const originalData = JSON.parse(form.dataset.originalData || '{}');
    let changedFields = [];

    const fieldLabels = {
        'first_name': 'First Name',
        'middle_name': 'Middle Name',
        'last_name': 'Last Name',
        'name_extension': 'Extension',
        'sex': 'Sex',
        'contact_number': 'Contact Number',
        'barangay': 'Barangay',
        'main_livelihood': 'Main Livelihood',
        'other_livelihood': 'Other Livelihood',
        'secondary_livelihood': 'Secondary Livelihood', 
        'other_secondary_livelihood': 'Other Secondary Livelihood', 
        'supporting_document': 'Supporting Document'
    };

    const fields = [
        'first_name', 'middle_name', 'last_name', 'name_extension',
        'sex', 'contact_number', 'barangay', 'main_livelihood', 'other_livelihood',
        'secondary_livelihood', 'other_secondary_livelihood'
    ];

    fields.forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input && input.value !== originalData[field]) {
            changedFields.push(fieldLabels[field] || field);
        }
    });

    // Check file input
    const fileInput = document.getElementById('edit_fishr_supporting_document');
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
        changedFields.push('Supporting Document');
    }

    // STEP 5: SHOW CONFIRMATION
    const changesText = changedFields.length > 0 
        ? `Update this registration with the following changes?\n\n• ${changedFields.join('\n• ')}`
        : 'Update this registration?';

    showConfirmationToast(
        'Confirm Update',
        changesText,
        () => proceedWithEditFishr(form, registrationId)
    );
}
/**
 * Proceed with Edit FishR Submission - FIXED
 */
function proceedWithEditFishr(form, registrationId) {
    const submitBtn = document.getElementById('editFishrSubmitBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
    submitBtn.disabled = true;
    
    // Build FormData - CRITICAL FIX: Use POST with _method for multipart
    const formData = new FormData();
    
    // Add _method for Laravel to recognize this as PUT request
    formData.append('_method', 'PUT');
    
    // Append all form fields
    formData.append('first_name', document.getElementById('edit_fishr_first_name').value.trim());
    formData.append('middle_name', document.getElementById('edit_fishr_middle_name').value.trim());
    formData.append('last_name', document.getElementById('edit_fishr_last_name').value.trim());
    formData.append('name_extension', document.getElementById('edit_fishr_extension').value.trim());
    formData.append('sex', document.getElementById('edit_fishr_sex').value.trim());
    formData.append('contact_number', document.getElementById('edit_fishr_contact_number').value.trim());
    formData.append('barangay', document.getElementById('edit_fishr_barangay').value.trim());
    formData.append('main_livelihood', document.getElementById('edit_fishr_livelihood').value.trim());
    formData.append('other_livelihood', document.getElementById('edit_fishr_other_livelihood').value.trim());
    formData.append('secondary_livelihood', document.getElementById('edit_fishr_secondary_livelihood').value.trim());
    formData.append('other_secondary_livelihood', document.getElementById('edit_fishr_other_secondary_livelihood').value.trim());
    
    // Add CSRF token explicitly
    formData.append('_token', getCSRFToken());
    
    // Add document file if selected
    const fileInput = document.getElementById('edit_fishr_supporting_document');
    if (fileInput && fileInput.files && fileInput.files[0]) {
        console.log('File selected:', fileInput.files[0].name);
        formData.append('supporting_document', fileInput.files[0]);
    }
    
    console.log('Sending update request for registration:', registrationId);
    
    // CRITICAL FIX: Use POST method (not PUT) with _method field for multipart/form-data
    fetch(`/admin/fishr-registrations/${registrationId}`, {
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
            showToast('success', data.message || 'Registration updated successfully');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editFishrModal'));
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
                    'first_name': 'edit_fishr_first_name',
                    'middle_name': 'edit_fishr_middle_name',
                    'last_name': 'edit_fishr_last_name',
                    'name_extension': 'edit_fishr_extension',
                    'sex': 'edit_fishr_sex',
                    'contact_number': 'edit_fishr_contact_number',
                    'barangay': 'edit_fishr_barangay',
                    'main_livelihood': 'edit_fishr_livelihood',
                    'other_livelihood': 'edit_fishr_other_livelihood',
                    'secondary_livelihood': 'edit_fishr_secondary_livelihood',             
                    'other_secondary_livelihood': 'edit_fishr_other_secondary_livelihood', 
                    'supporting_document': 'edit_fishr_supporting_document'
                };
                
                const inputId = fieldMap[field] || 'edit_fishr_' + field;
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
        showToast('error', 'Error updating registration: ' + error.message);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
/**
 * Update table row with latest data
 */
function updateTableRow(registrationId) {
    try {
        fetch(`/admin/fishr-registrations/${registrationId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch updated data');
            return response.json();
        })
        .then(response => {
            if (!response.success) return;

            const data = response.data;
            const row = document.querySelector(`tr[data-registration-id="${registrationId}"]`);
            
            if (!row) return;

            const cells = row.querySelectorAll('td');
            
            // Update Name (Column 2)
            if (cells[2]) {
                cells[2].textContent = data.full_name;
            }

            // Update Livelihood (Column 3)
            if (cells[3]) {
                cells[3].innerHTML = `<span class="badge bg-info fs-6">${data.livelihood_description}</span>`;
            }

            // Highlight row
            row.style.backgroundColor = '#fff3cd';
            setTimeout(() => {
                row.style.transition = 'background-color 0.3s ease';
                row.style.backgroundColor = '';
            }, 100);
        })
        .catch(error => console.error('Error updating row:', error));
    } catch (error) {
        console.error('Error in updateTableRow:', error);
    }
}

/**
 * Preview Edit FishR Document
 */
function previewEditFishrDocument(inputId, previewId) {
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
            const form = document.getElementById('editFishrForm');
            if (form && form.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
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
function capitalizeEditFishrName(input) {
    const value = input.value;
    if (value.length > 0) {
        input.value = value
            .toLowerCase()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');

        // Trigger change detection after capitalization
        const form = document.getElementById('editFishrForm');
        if (form && form.dataset.registrationId) {
            checkEditFishrFormChanges(form.dataset.registrationId);
        }
    }
}

/**
 * Initialize name field auto-capitalize when modal is shown
 */
document.addEventListener('DOMContentLoaded', function() {
    // Set up event delegation for dynamically added elements
    document.addEventListener('focusout', function(e) {
        if (e.target.id === 'edit_fishr_first_name' ||
            e.target.id === 'edit_fishr_middle_name' ||
            e.target.id === 'edit_fishr_last_name') {
            capitalizeEditFishrName(e.target);
        }
    });

    // Add contact number validation
    const contactInput = document.getElementById('edit_fishr_contact_number');
    if (contactInput) {
        contactInput.addEventListener('input', function() {
            validateEditFishrContactNumber(this.value);
        });
    }
});

/**
 * Validate contact number in edit form
 */
function validateEditFishrContactNumber(contactNumber) {
    const input = document.getElementById('edit_fishr_contact_number');
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) feedback.remove();
    input.classList.remove('is-invalid', 'is-valid');

    if (!contactNumber.trim()) return true;

    const phoneRegex = /^(\+639|09)\d{9}$/;

    if (!phoneRegex.test(contactNumber.trim())) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
        input.parentNode.appendChild(errorDiv);
        return false;
    }

    input.classList.add('is-valid');
    return true;
}


        // Add event listeners for blur on name fields
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editFishrModal');

            if (editModal) {
                editModal.addEventListener('shown.bs.modal', function() {
                    // Add blur listeners for auto-capitalization
                    const firstNameInput = document.getElementById('edit_first_name');
                    const middleNameInput = document.getElementById('edit_middle_name');
                    const lastNameInput = document.getElementById('edit_last_name');

                    if (firstNameInput) {
                        firstNameInput.addEventListener('blur', function() {
                            capitalizeEditFishrName(this);
                        });
                    }

                    if (middleNameInput) {
                        middleNameInput.addEventListener('blur', function() {
                            capitalizeEditFishrName(this);
                        });
                    }

                    if (lastNameInput) {
                        lastNameInput.addEventListener('blur', function() {
                            capitalizeEditFishrName(this);
                        });
                    }
                });
            }
        });

        // Handle search input - auto-reset when empty
        function handleSearchInput() {
            const searchInput = document.getElementById('searchInput');
            const filterForm = document.getElementById('filterForm');
            
            if (searchInput.value.trim() === '') {
                // If search is empty, reset and submit
                filterForm.submit();
            } else {
                // If search has value, use auto-search
                autoSearch();
            }
        }
        // Update remarks character counter 
        function updateFishrRemarksCounter() {
            const textarea = document.getElementById('fishr_remarks');
            const charCount = document.getElementById('charCountFishr');
            
            if (textarea && charCount) {
                charCount.textContent = textarea.value.length;
                
                // Change color based on length
                if (textarea.value.length > 900) {
                    charCount.parentElement.classList.add('text-warning');
                    charCount.parentElement.classList.remove('text-muted');
                } else {
                    charCount.parentElement.classList.remove('text-warning');
                    charCount.parentElement.classList.add('text-muted');
                }
            }
        }

      
document.addEventListener('shown.bs.modal', function(e) {
    const modal = e.target;
    const backdrop = modal.previousElementSibling;
    
    // Get all open modals
    const openModals = document.querySelectorAll('.modal.show');
    const modalCount = openModals.length;
    
    // Set z-index based on modal count
    const baseZIndex = 1050;
    modal.style.zIndex = baseZIndex + (modalCount * 20);
    
    if (backdrop && backdrop.classList.contains('modal-backdrop')) {
        backdrop.style.zIndex = baseZIndex + (modalCount * 20) - 1;
    }
});
        /**
         * Auto-capitalize names in edit form
         */
        function capitalizeEditFishrName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');

                // Trigger change detection after capitalization
                const form = document.getElementById('editFishrForm');
                if (form && form.dataset.registrationId) {
                    checkEditFishrFormChanges(form.dataset.registrationId);
                }
            }
        }

        /**
         * Initialize name field auto-capitalize when modal is shown
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event delegation for dynamically added elements
            document.addEventListener('focusout', function(e) {
                if (e.target.id === 'edit_fishr_first_name' ||
                    e.target.id === 'edit_fishr_middle_name' ||
                    e.target.id === 'edit_fishr_last_name') {
                    capitalizeEditFishrName(e.target);
                }
            });

            // Add contact number validation
            const contactInput = document.getElementById('edit_fishr_contact_number');
            if (contactInput) {
                contactInput.addEventListener('input', function() {
                    validateEditFishrContactNumber(this.value);
                });
            }
        });

        /**
         * Validate contact number in edit form
         */
        function validateEditFishrContactNumber(contactNumber) {
            const input = document.getElementById('edit_fishr_contact_number');
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!contactNumber.trim()) return true;

            const phoneRegex = /^(\+639|09)\d{9}$/;

            if (!phoneRegex.test(contactNumber.trim())) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }

    //Display existing FishR documents in edit modal
    function displayEditFishrExistingDocument(documentPath, previewElementId) {
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
                                    onclick="downloadFishrDocument('${fileUrl}', '${fileName}')"
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
                                    onclick="downloadFishrDocument('${fileUrl}', '${fileName}')"
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

    // Download helper for FishR documents
    function downloadFishrDocument(fileUrl, fileName) {
        const link = document.createElement('a');
        link.href = fileUrl;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
        // delete registration 
        // Global variable to track current delete ID
        let currentDeleteFishrId = null;

        /**
         * Updated deleteFishrRegistration function to use modal
         */
        function deleteFishrRegistration(id, registrationNumber) {
            try {
                // Set the global variable
                currentDeleteFishrId = id;

                // Update modal with registration number
                document.getElementById('delete_fishr_name').textContent = registrationNumber;

                // Show the delete modal
                new bootstrap.Modal(document.getElementById('deleteFishrModal')).show();
            } catch (error) {
                console.error('Error preparing delete dialog:', error);
                showToast('error', 'Failed to prepare delete dialog');
            }
        }

        /**
         * Confirm permanent delete for FishR registration
         */
        async function confirmPermanentDeleteFishr() {
            if (!currentDeleteFishrId) {
                showToast('error', 'Registration ID not found');
                return;
            }

            try {
                // Show loading state
                const deleteBtn = document.getElementById('confirm_delete_fishr_btn');
                deleteBtn.querySelector('.btn-text').style.display = 'none';
                deleteBtn.querySelector('.btn-loader').style.display = 'inline';
                deleteBtn.disabled = true;

                const response = await fetch(`/admin/fishr-registrations/${currentDeleteFishrId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete registration');
                }

                // Close modal
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteFishrModal'));
                if (deleteModal) {
                    deleteModal.hide();
                }

                // Show success message
                showToast('success', data.message || 'FishR registration deleted successfully');

                // Remove the row with animation
                const row = document.querySelector(`tr[data-registration-id="${currentDeleteFishrId}"]`);
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
                currentDeleteFishrId = null;

            } catch (error) {
                console.error('Error deleting registration:', error);
                
                // Close modal first
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteFishrModal'));
                if (deleteModal) {
                    deleteModal.hide();
                }

                // Show error
                showToast('error', 'Error deleting registration: ' + error.message);

            } finally {
                // Reset button state
                const deleteBtn = document.getElementById('confirm_delete_fishr_btn');
                deleteBtn.querySelector('.btn-text').style.display = 'inline';
                deleteBtn.querySelector('.btn-loader').style.display = 'none';
                deleteBtn.disabled = false;
            }
        }

        /**
         * Clean up modal on close
         */
        document.addEventListener('DOMContentLoaded', function() {
            const deleteFishrModal = document.getElementById('deleteFishrModal');
            if (deleteFishrModal) {
                deleteFishrModal.addEventListener('hidden.bs.modal', function() {
                    // Reset button state
                    const deleteBtn = document.getElementById('confirm_delete_fishr_btn');
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
                    currentDeleteFishrId = null;

                    console.log('Delete FishR modal cleaned up');
                });
            }
        });

        // Update Registration Status - FIXED
function updateRegistrationStatus() {
    const statusSelect = document.getElementById('newStatus');
    const remarksTextarea = document.getElementById('remarks');
    const registrationId = document.getElementById('updateRegistrationId').value;
    const updateButton = document.getElementById('updateStatusBtn');

    if (!registrationId) {
        showToast('error', 'Registration ID not found');
        return;
    }

    if (!statusSelect.value) {
        showToast('error', 'Please select a status');
        return;
    }

    // Get the data
    const formData = {
        status: statusSelect.value,
        remarks: remarksTextarea.value.trim()
    };

    // Show loading
    const originalText = updateButton.innerHTML;
    updateButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...';
    updateButton.disabled = true;

    // Send request
    fetch(`/admin/fishr-registrations/${registrationId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message || 'Status updated successfully');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
            if (modal) modal.hide();
            
            // Reload table
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('error', data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error updating status: ' + error.message);
    })
    .finally(() => {
        updateButton.innerHTML = originalText;
        updateButton.disabled = false;
    });
}
 
/**
 * NEW: validateEditOtherSecondaryLivelihoodText
 * Real-time validation for special characters in secondary "others" field
 */
function validateEditOtherSecondaryLivelihoodText() {
    const input = document.getElementById('edit_fishr_other_secondary_livelihood');
    const warning = document.getElementById('edit_other_secondary_livelihood_special_chars_warning');
    const value = input.value;

    // Pattern: Only letters, numbers, spaces, hyphens, apostrophes, periods, commas
    const validPattern = /^[a-zA-Z0-9\s\'-.,]*$/;

    if (value && !validPattern.test(value)) {
        if (warning) {
            warning.style.display = 'block';
        }
        input.style.borderColor = '#ff6b6b';
        input.style.backgroundColor = '#ffe6e6';
    } else {
        if (warning) {
            warning.style.display = 'none';
        }
        input.style.borderColor = '';
        input.style.backgroundColor = '';
    }

    // Also check for text match with main livelihood
    validateEditSecondaryLivelihoodTextMatch();
}

/**
 * NEW: capitalizeEditOtherSecondaryLivelihood
 * Auto-capitalize on blur
 */
function capitalizeEditOtherSecondaryLivelihood() {
    const input = document.getElementById('edit_fishr_other_secondary_livelihood');
    if (!input || !input.value) return;

    const value = input.value.trim();
    
    // Capitalize first letter of each word
    const capitalized = value
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');

    input.value = capitalized;

    // Trigger change detection
    const form = document.getElementById('editFishrForm');
    if (form.dataset.registrationId) {
        checkEditFishrFormChanges(form.dataset.registrationId);
    }
}

/**
 * NEW: clearEditOtherSecondaryLivelihoodWarning
 * Clears all warnings for secondary "others" field
 */
function clearEditOtherSecondaryLivelihoodWarning() {
    const warning = document.getElementById('edit_other_secondary_livelihood_special_chars_warning');
    const input = document.getElementById('edit_fishr_other_secondary_livelihood');
    const textMatchWarning = document.getElementById('edit_other_secondary_livelihood_text_match_warning');
    
    if (warning) warning.style.display = 'none';
    if (textMatchWarning) textMatchWarning.style.display = 'none';
    if (input) {
        input.style.borderColor = '';
        input.style.backgroundColor = '';
    }
}

    /**
     * Toggle Add Other Secondary Livelihood Field
     */
    function toggleAddOtherSecondaryLivelihood() {
        const secondaryLivelihood = document.getElementById('fishr_secondary_livelihood').value;
        const container = document.getElementById('add_other_secondary_livelihood_container');
        const input = document.getElementById('fishr_other_secondary_livelihood');
        
        if (secondaryLivelihood === 'others') {
            container.style.display = 'block';
            input.required = true;
        } else {
            container.style.display = 'none';
            input.required = false;
            input.value = '';
        }
    }
    /**
     * Validate Secondary Livelihood in Add Form - Complete validation
     */
    function validateAddSecondaryLivelihoodInForm() {
        const secondaryLivelihood = document.getElementById('fishr_secondary_livelihood').value;
        const mainLivelihood = document.getElementById('fishr_main_livelihood').value;
        
        if (secondaryLivelihood === 'others') {
            const otherSecondaryLivelihood = document.getElementById('fishr_other_secondary_livelihood');
            if (!otherSecondaryLivelihood.value || otherSecondaryLivelihood.value.trim() === '') {
                otherSecondaryLivelihood.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please specify the other secondary livelihood';
                
                const existingError = otherSecondaryLivelihood.parentNode.querySelector('.invalid-feedback');
                if (existingError) existingError.remove();
                
                otherSecondaryLivelihood.parentNode.appendChild(errorDiv);
                return false;
            }
        }
        
        // Validate secondary livelihood is not same as main
        if (secondaryLivelihood && mainLivelihood && secondaryLivelihood === mainLivelihood) {
            const secondaryInput = document.getElementById('fishr_secondary_livelihood');
            secondaryInput.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = 'Secondary livelihood cannot be the same as main livelihood';
            
            const existingError = secondaryInput.parentNode.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();
            
            secondaryInput.parentNode.appendChild(errorDiv);
            return false;
        }
        
        return true;
    }

    // ====================================================
// ADD MODAL: LIVELIHOOD VALIDATION & AUTO-CAPITALIZE
// ====================================================

/**
 * Toggle Other Livelihood Field - Updated for Add Modal
 */
function toggleOtherLivelihood() {
    const livelihood = document.getElementById('fishr_main_livelihood').value;
    const container = document.getElementById('other_livelihood_container');
    const input = document.getElementById('fishr_other_livelihood');

    if (livelihood === 'others') {
        container.style.display = 'block';
        input.required = true;
        input.focus();
    } else {
        container.style.display = 'none';
        input.required = false;
        input.value = '';
        clearAddOtherLivelihoodWarning();
    }
}


// /**// Setup in setupAddModalValidation()
const otherLivelihoodInput = document.getElementById('fishr_other_livelihood');
if (otherLivelihoodInput) {
    // Validate only on BLUR (allows user to type freely)
    otherLivelihoodInput.addEventListener('blur', function(e) {  // ← CHANGED: 'blur' not 'input'
        const value = e.target.value.trim();
        if (value && !isValidOthersLivelihoodText(value)) {
            markFieldError(otherLivelihoodInput, 'Only letters, numbers, spaces, hyphens, apostrophes, periods, commas allowed');
        } else {
            clearFieldError(otherLivelihoodInput);
        }
        
        // Auto-capitalize
        if (value) {
            e.target.value = value
                .toLowerCase()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }
    });
}
//  * Validate Other Livelihood Text - Real-time validation
//  */
// function validateAddOtherLivelihoodText() {
//     const input = document.getElementById('fishr_other_livelihood');
//     const warning = document.getElementById('add_other_livelihood_warning');
//     const value = input.value;

//     // Pattern: Only letters, numbers, spaces, hyphens, apostrophes, periods, commas
//     const validPattern = /^[a-zA-Z0-9\s\'-.,]*$/;

//     if (value && !validPattern.test(value)) {
//         warning.style.display = 'block';
//         input.style.borderColor = '#ff6b6b';
//         input.style.backgroundColor = '#ffe6e6';
//     } else {
//         warning.style.display = 'none';
//         input.style.borderColor = '';
//         input.style.backgroundColor = '';
//     }
// }

// /**
//  * Validate Other Secondary Livelihood Text - Real-time validation
//  */
// function validateAddOtherSecondaryLivelihoodText() {
//     const input = document.getElementById('fishr_other_secondary_livelihood');
//     const warning = document.getElementById('add_other_secondary_livelihood_warning');
//     const value = input.value;

//     // Pattern: Only letters, numbers, spaces, hyphens, apostrophes, periods, commas
//     const validPattern = /^[a-zA-Z0-9\s\'-.,]*$/;

//     if (value && !validPattern.test(value)) {
//         warning.style.display = 'block';
//         input.style.borderColor = '#ff6b6b';
//         input.style.backgroundColor = '#ffe6e6';
//     } else {
//         warning.style.display = 'none';
//         input.style.borderColor = '';
//         input.style.backgroundColor = '';
//     }

//     // Also check for text match with main livelihood
//     validateAddSecondaryLivelihoodTextMatch();
// }

/**
 * Auto-capitalize Other Livelihood on blur
 */
function capitalizeAddOtherLivelihood() {
    const input = document.getElementById('fishr_other_livelihood');
    if (!input || !input.value) return;

    const value = input.value.trim();
    
    // Capitalize first letter of each word
    const capitalized = value
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');

    input.value = capitalized;
}

/**
 * Auto-capitalize Other Secondary Livelihood on blur
 */
function capitalizeAddOtherSecondaryLivelihood() {
    const input = document.getElementById('fishr_other_secondary_livelihood');
    if (!input || !input.value) return;

    const value = input.value.trim();
    
    // Capitalize first letter of each word
    const capitalized = value
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');

    input.value = capitalized;
}

/**
 * Validate Secondary Livelihood cannot be same as Main
 */
/**
 * Validate Secondary Livelihood - Allow both "others" if different text
 */
function validateAddSecondaryLivelihood() {
    const mainLivelihood = document.getElementById('fishr_main_livelihood').value;
    const secondaryLivelihood = document.getElementById('fishr_secondary_livelihood').value;
    const warning = document.getElementById('add_secondary_livelihood_warning');

    // If no secondary selected, no validation needed
    if (!secondaryLivelihood) {
        if (warning) warning.style.display = 'none';
        return true;
    }

    // CASE 1: Both are "others" - Allow it (they specify different text in other fields)
    if (mainLivelihood === 'others' && secondaryLivelihood === 'others') {
        if (warning) warning.style.display = 'none';
        document.getElementById('fishr_secondary_livelihood').classList.remove('is-invalid');
        return true;
    }

    // CASE 2: Same standard type (both capture, both aquaculture, etc.) - NOT ALLOWED
    if (secondaryLivelihood === mainLivelihood && mainLivelihood !== 'others') {
        if (warning) {
            warning.textContent = 'Secondary livelihood cannot be the same as main livelihood';
            warning.style.display = 'block';
        }
        document.getElementById('fishr_secondary_livelihood').classList.add('is-invalid');
        return false;
    }

    // CASE 3: Secondary is standard type, main is "others" - Allow it
    if (mainLivelihood === 'others' && secondaryLivelihood !== 'others') {
        if (warning) warning.style.display = 'none';
        document.getElementById('fishr_secondary_livelihood').classList.remove('is-invalid');
        return true;
    }

    // CASE 4: Main is standard type, secondary is "others" - Allow it
    if (mainLivelihood !== 'others' && secondaryLivelihood === 'others') {
        if (warning) warning.style.display = 'none';
        document.getElementById('fishr_secondary_livelihood').classList.remove('is-invalid');
        return true;
    }

    // Default: Allow
    if (warning) warning.style.display = 'none';
    document.getElementById('fishr_secondary_livelihood').classList.remove('is-invalid');
    return true;
}

/**
 * Validate Secondary Livelihood Text Cannot Match Main Text
 */
function validateAddSecondaryLivelihoodTextMatch() {
    const mainLivelihoodSelect = document.getElementById('fishr_main_livelihood');
    const secondaryLivelihoodSelect = document.getElementById('fishr_secondary_livelihood');
    const mainOthersInput = document.getElementById('fishr_other_livelihood');
    const secondaryOthersInput = document.getElementById('fishr_other_secondary_livelihood');

    if (!secondaryOthersInput) return true;

    let textMatchWarning = document.getElementById('add_other_secondary_livelihood_text_match_warning');
    if (!textMatchWarning) {
        textMatchWarning = document.createElement('span');
        textMatchWarning.id = 'add_other_secondary_livelihood_text_match_warning';
        textMatchWarning.className = 'validation-warning';
        textMatchWarning.style.color = '#ff6b6b';
        textMatchWarning.style.fontSize = '0.875rem';
        textMatchWarning.style.display = 'none';
        textMatchWarning.style.marginTop = '4px';
        textMatchWarning.textContent = 'Secondary livelihood cannot be the same as main livelihood';
        secondaryOthersInput.parentNode.appendChild(textMatchWarning);
    }

    const mainValue = mainLivelihoodSelect?.value || '';
    const secondaryValue = secondaryLivelihoodSelect?.value || '';
    const mainOthersValue = (mainOthersInput?.value || '').trim().toLowerCase();
    const secondaryOthersValue = (secondaryOthersInput?.value || '').trim().toLowerCase();

    let showWarning = false;

    // Case 1: Both are "others" and have the same text
    if (mainValue === 'others' && secondaryValue === 'others') {
        if (mainOthersValue && secondaryOthersValue && mainOthersValue === secondaryOthersValue) {
            showWarning = true;
        }
    }
    // Case 2: Secondary is "others" and its text matches the main livelihood type
    else if (secondaryValue === 'others' && mainValue !== 'others' && mainValue) {
        const livelihoodTextMap = {
            'capture': ['capture', 'fishing'],
            'aquaculture': ['aquaculture', 'fish pond', 'fishpond'],
            'vending': ['vending', 'vendor'],
            'processing': ['processing', 'processor']
        };

        const mainLivelihoodTexts = livelihoodTextMap[mainValue] || [];
        const hasMatch = mainLivelihoodTexts.some(text => 
            secondaryOthersValue.includes(text)
        );

        if (hasMatch) {
            showWarning = true;
        }
    }
    // Case 3: Main is "others" and secondary is a standard option that matches main's text
    else if (mainValue === 'others' && secondaryValue !== 'others' && secondaryValue) {
        const livelihoodTextMap = {
            'capture': ['capture', 'fishing'],
            'aquaculture': ['aquaculture', 'fish pond', 'fishpond'],
            'vending': ['vending', 'vendor'],
            'processing': ['processing', 'processor']
        };

        const secondaryTexts = livelihoodTextMap[secondaryValue] || [];
        const hasMatch = secondaryTexts.some(text => 
            mainOthersValue.includes(text)
        );

        if (hasMatch) {
            showWarning = true;
        }
    }

    if (showWarning) {
        textMatchWarning.style.display = 'block';
        secondaryLivelihoodSelect.style.borderColor = '#ff6b6b';
        if (secondaryOthersInput) secondaryOthersInput.style.borderColor = '#ff6b6b';
        return false;
    } else {
        textMatchWarning.style.display = 'none';
        secondaryLivelihoodSelect.style.borderColor = '';
        if (secondaryOthersInput) secondaryOthersInput.style.borderColor = '';
        return true;
    }
}

/**
 * Clear Other Livelihood Warning
 */
function clearAddOtherLivelihoodWarning() {
    const warning = document.getElementById('add_other_livelihood_warning');
    const input = document.getElementById('fishr_other_livelihood');
    if (warning) warning.style.display = 'none';
    if (input) {
        input.style.borderColor = '';
        input.style.backgroundColor = '';
    }
}

/**
 * Clear Other Secondary Livelihood Warning
 */
function clearAddOtherSecondaryLivelihoodWarning() {
    const warning = document.getElementById('add_other_secondary_livelihood_warning');
    const input = document.getElementById('fishr_other_secondary_livelihood');
    if (warning) warning.style.display = 'none';
    if (input) {
        input.style.borderColor = '';
        input.style.backgroundColor = '';
    }
}

/**
 * Complete validation for secondary livelihood in form submission
 * Called during validateFishrForm()
 */
function validateAddSecondaryLivelihoodInForm() {
    const secondaryLivelihood = document.getElementById('fishr_secondary_livelihood').value;
    const mainLivelihood = document.getElementById('fishr_main_livelihood').value;
    
    if (secondaryLivelihood === 'others') {
        const otherSecondaryLivelihood = document.getElementById('fishr_other_secondary_livelihood');
        if (!otherSecondaryLivelihood.value || otherSecondaryLivelihood.value.trim() === '') {
            otherSecondaryLivelihood.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = 'Please specify the other secondary livelihood';
            
            const existingError = otherSecondaryLivelihood.parentNode.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();
            
            otherSecondaryLivelihood.parentNode.appendChild(errorDiv);
            return false;
        }
    }
    
    // Validate secondary livelihood is not same as main
    if (secondaryLivelihood && mainLivelihood && secondaryLivelihood === mainLivelihood) {
        const secondaryInput = document.getElementById('fishr_secondary_livelihood');
        secondaryInput.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = 'Secondary livelihood cannot be the same as main livelihood';
        
        const existingError = secondaryInput.parentNode.querySelector('.invalid-feedback');
        if (existingError) existingError.remove();
        
        secondaryInput.parentNode.appendChild(errorDiv);
        return false;
    }
    
    return true;
}
// =====================================================
// add FISHR MODAL - REAL-TIME VALIDATION
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    // Name fields validation
    const nameFields = [
        { id: 'fishr_first_name', pattern: /^[a-zA-Z\s\'-]*$/ },
        { id: 'fishr_middle_name', pattern: /^[a-zA-Z\s\'-]*$/ },
        { id: 'fishr_last_name', pattern: /^[a-zA-Z\s\'-]*$/ },
        { id: 'fishr_name_extension', pattern: /^[a-zA-Z.\s]*$/ }
    ];

    nameFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (!input) return;

        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            if (value && !field.pattern.test(value)) {
                markAdminFieldError(input, 'Invalid characters');
            } else {
                clearAdminFieldError(input);
            }
        });

        input.addEventListener('blur', function(e) {
            if (e.target.value && !field.pattern.test(e.target.value)) {
                markAdminFieldError(input, 'Invalid characters');
            }
        });
    });

    // Contact number validation
    const contactInput = document.getElementById('fishr_contact_number');
    if (contactInput) {
        const phonePattern = /^09\d{9}$/;

        contactInput.addEventListener('input', function(e) {
            const value = e.target.value;
            
            if (value && !phonePattern.test(value)) {
                markAdminFieldError(contactInput, 'Format: 09XXXXXXXXX');
            } else {
                clearAdminFieldError(contactInput);
            }
        });

        contactInput.addEventListener('blur', function(e) {
            if (e.target.value && !phonePattern.test(e.target.value)) {
                markAdminFieldError(contactInput, 'Format: 09XXXXXXXXX');
            }
        });
    }

    // Main livelihood change
    const mainLivelihoodSelect = document.getElementById('fishr_main_livelihood');
    if (mainLivelihoodSelect) {
        mainLivelihoodSelect.addEventListener('change', function() {
            toggleOtherLivelihood();
            validateAdminSecondaryLivelihood();
        });
    }

    // Secondary livelihood change
    const secondaryLivelihoodSelect = document.getElementById('fishr_secondary_livelihood');
    if (secondaryLivelihoodSelect) {
        secondaryLivelihoodSelect.addEventListener('change', function() {
            toggleAddOtherSecondaryLivelihood();
            validateAdminSecondaryLivelihood();
        });
    }

    // Other livelihood validation
    const otherLivelihoodInput = document.getElementById('fishr_other_livelihood');
    if (otherLivelihoodInput) {
        otherLivelihoodInput.addEventListener('input', function(e) {
            const value = e.target.value;
            
            if (value && !isValidOthersLivelihoodText(value)) {
                markAdminFieldError(otherLivelihoodInput, 'Only: letters, numbers, spaces, hyphens, apostrophes, periods, commas');
            } else {
                clearAdminFieldError(otherLivelihoodInput);
            }
            
            validateAdminSecondaryLivelihood();
        });

        otherLivelihoodInput.addEventListener('blur', function(e) {
            if (e.target.value && !isValidOthersLivelihoodText(e.target.value)) {
                markAdminFieldError(otherLivelihoodInput, 'Invalid characters');
            }
        });
    }

    // Other secondary livelihood validation
    const otherSecondaryLivelihoodInput = document.getElementById('fishr_other_secondary_livelihood');
    if (otherSecondaryLivelihoodInput) {
        otherSecondaryLivelihoodInput.addEventListener('input', function(e) {
            const value = e.target.value;
            
            if (value && !isValidOthersLivelihoodText(value)) {
                markAdminFieldError(otherSecondaryLivelihoodInput, 'Invalid characters');
            } else {
                clearAdminFieldError(otherSecondaryLivelihoodInput);
            }
            
            validateAdminSecondaryLivelihood();
        });

        otherSecondaryLivelihoodInput.addEventListener('blur', function(e) {
            if (e.target.value && !isValidOthersLivelihoodText(e.target.value)) {
                markAdminFieldError(otherSecondaryLivelihoodInput, 'Invalid characters');
            }
        });
    }
});


// =====================================================
// EDIT FISHR MODAL - REAL-TIME VALIDATION
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editFishrModal');
    
    if (editModal) {
        editModal.addEventListener('shown.bs.modal', function() {
            setupEditModalValidation();
        });
    }
});

function setupEditModalValidation() {
    // Name fields validation
    const nameFields = [
        { id: 'edit_fishr_first_name', pattern: /^[a-zA-Z\s\'-]*$/ },
        { id: 'edit_fishr_middle_name', pattern: /^[a-zA-Z\s\'-]*$/ },
        { id: 'edit_fishr_last_name', pattern: /^[a-zA-Z\s\'-]*$/ },
        { id: 'edit_fishr_extension', pattern: /^[a-zA-Z.\s]*$/ }
    ];

    nameFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (!input) return;

        input.addEventListener('input', function(e) {
            const value = e.target.value;
            if (value && !field.pattern.test(value)) {
                markEditFieldError(input, 'Invalid characters');
            } else {
                clearEditFieldError(input);
            }
            checkEditFishrFormChanges(document.getElementById('editFishrForm').dataset.registrationId);
        });

        input.addEventListener('blur', function(e) {
            if (e.target.value && !field.pattern.test(e.target.value)) {
                markEditFieldError(input, 'Invalid characters');
            }
        });
    });

    // Contact number validation
    const contactInput = document.getElementById('edit_fishr_contact_number');
    if (contactInput) {
        const phonePattern = /^09\d{9}$/;

        contactInput.addEventListener('input', function(e) {
            const value = e.target.value;
            if (value && !phonePattern.test(value)) {
                markEditFieldError(contactInput, 'Format: 09XXXXXXXXX');
            } else {
                clearEditFieldError(contactInput);
            }
            checkEditFishrFormChanges(document.getElementById('editFishrForm').dataset.registrationId);
        });
    }

    // Main livelihood change
    const mainLivelihoodSelect = document.getElementById('edit_fishr_livelihood');
    if (mainLivelihoodSelect) {
        mainLivelihoodSelect.addEventListener('change', function() {
            toggleEditOtherFishrLivelihood();
            validateEditSecondaryLivelihood();
            checkEditFishrFormChanges(document.getElementById('editFishrForm').dataset.registrationId);
        });
    }

    // Secondary livelihood change
    const secondaryLivelihoodSelect = document.getElementById('edit_fishr_secondary_livelihood');
    if (secondaryLivelihoodSelect) {
        secondaryLivelihoodSelect.addEventListener('change', function() {
            toggleEditOtherSecondaryFishrLivelihood();
            validateEditSecondaryLivelihood();
            checkEditFishrFormChanges(document.getElementById('editFishrForm').dataset.registrationId);
        });
    }

    // Other livelihood validation
    const otherLivelihoodInput = document.getElementById('edit_fishr_other_livelihood');
    if (otherLivelihoodInput) {
        otherLivelihoodInput.addEventListener('input', function(e) {
            const value = e.target.value;
            if (value && !isValidOthersLivelihoodText(value)) {
                markEditFieldError(otherLivelihoodInput, 'Only: letters, numbers, spaces, hyphens, apostrophes');
            } else {
                clearEditFieldError(otherLivelihoodInput);
            }
            checkEditFishrFormChanges(document.getElementById('editFishrForm').dataset.registrationId);
        });

        otherLivelihoodInput.addEventListener('blur', function(e) {
            if (e.target.value) {
                capitalizeEditOtherLivelihood(e.target);
            }
        });
    }

    // Other secondary livelihood validation
    const otherSecondaryLivelihoodInput = document.getElementById('edit_fishr_other_secondary_livelihood');
    if (otherSecondaryLivelihoodInput) {
        otherSecondaryLivelihoodInput.addEventListener('input', function(e) {
            const value = e.target.value;
            if (value && !isValidOthersLivelihoodText(value)) {
                markEditFieldError(otherSecondaryLivelihoodInput, 'Invalid characters');
            } else {
                clearEditFieldError(otherSecondaryLivelihoodInput);
            }
            validateEditSecondaryLivelihoodTextMatch();
            checkEditFishrFormChanges(document.getElementById('editFishrForm').dataset.registrationId);
        });

        otherSecondaryLivelihoodInput.addEventListener('blur', function(e) {
            if (e.target.value) {
                capitalizeEditOtherSecondaryLivelihood();
            }
        });
    }
}

// Capitalize other livelihood
function capitalizeEditOtherLivelihood(input) {
    const value = input.value.trim();
    if (value) {
        input.value = value
            .toLowerCase()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
        
        checkEditFishrFormChanges(document.getElementById('editFishrForm').dataset.registrationId);
    }
}

// =====================================================
// UNIFIED ERROR HANDLING (for both Add and Edit modals)
// =====================================================

/**
 * Mark field with error - removes existing errors first
 */
function markFieldError(input, message = '', modalPrefix = 'fishr') {
    if (!input) return;
    
    // CRITICAL FIX: Remove ANY existing error divs FIRST
    const existingError = input.parentNode.querySelector('.invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
    
    input.classList.add('is-invalid');
    input.style.borderColor = '#ff6b6b';
    input.style.backgroundColor = '#ffe6e6';
    
    if (message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
    }
}

/**
 * Clear field error completely
 */
function clearFieldError(input) {
    if (!input) return;
    
    input.classList.remove('is-invalid');
    input.style.borderColor = '';
    input.style.backgroundColor = '';
    
    // Remove error div
    const errorDiv = input.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// =====================================================
// REAL-TIME VALIDATION LISTENERS (gentle, non-intrusive)
// =====================================================

/**
 * Setup validation for Add Modal
 * Called when modal is shown
 */
function setupAddModalValidation() {
    // ===== TEXT FIELDS: Name & Extension =====
    const textFields = [
        { id: 'fishr_first_name', pattern: /^[a-zA-Z\s\'-]*$/, message: 'Only letters, spaces, hyphens, apostrophes' },
        { id: 'fishr_middle_name', pattern: /^[a-zA-Z\s\'-]*$/, message: 'Only letters, spaces, hyphens, apostrophes' },
        { id: 'fishr_last_name', pattern: /^[a-zA-Z\s\'-]*$/, message: 'Only letters, spaces, hyphens, apostrophes' },
        { id: 'fishr_name_extension', pattern: /^[a-zA-Z.\s]*$/, message: 'Only letters, periods, spaces' }
    ];

    textFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (!input) return;

        // Validate only on BLUR (not while typing)
        input.addEventListener('blur', function(e) {
            const value = e.target.value;
            if (value && !field.pattern.test(value)) {
                markFieldError(input, field.message);
            } else {
                clearFieldError(input);
            }
        });

        // Auto-capitalize on blur
        input.addEventListener('blur', function(e) {
            if (e.target.value) {
                e.target.value = e.target.value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        });
    });

    // ===== CONTACT NUMBER =====
    const contactInput = document.getElementById('fishr_contact_number');
    if (contactInput) {
        contactInput.addEventListener('blur', function(e) {
            const value = e.target.value.trim();
            const phoneRegex = /^09\d{9}$/;
            
            if (value && !phoneRegex.test(value)) {
                markFieldError(contactInput, 'Format: 09XXXXXXXXX (11 digits)');
            } else if (value) {
                clearFieldError(contactInput);
            }
        });
    }

    // ===== MAIN LIVELIHOOD =====
    const mainLivelihoodSelect = document.getElementById('fishr_main_livelihood');
    if (mainLivelihoodSelect) {
        mainLivelihoodSelect.addEventListener('change', function() {
            toggleOtherLivelihood();
            validateSecondaryLivelihoodMatch();
        });
    }

    // ===== OTHER LIVELIHOOD (when main = "others") =====
    const otherLivelihoodInput = document.getElementById('fishr_other_livelihood');
    if (otherLivelihoodInput) {
        // Validate only on BLUR (allows user to type freely)
        otherLivelihoodInput.addEventListener('blur', function(e) {
            const value = e.target.value.trim();
            if (value && !isValidOthersLivelihoodText(value)) {
                markFieldError(otherLivelihoodInput, 'Only letters, numbers, spaces, hyphens, apostrophes, periods, commas allowed');
            } else {
                clearFieldError(otherLivelihoodInput);
            }
            
            // Auto-capitalize
            if (value) {
                e.target.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        });
    }

    // ===== SECONDARY LIVELIHOOD =====
    const secondaryLivelihoodSelect = document.getElementById('fishr_secondary_livelihood');
    if (secondaryLivelihoodSelect) {
        secondaryLivelihoodSelect.addEventListener('change', function() {
            toggleAddOtherSecondaryLivelihood();
            validateSecondaryLivelihoodMatch();
        });
    }

    // ===== OTHER SECONDARY LIVELIHOOD =====
    const otherSecondaryInput = document.getElementById('fishr_other_secondary_livelihood');
    if (otherSecondaryInput) {
        otherSecondaryInput.addEventListener('blur', function(e) {
            const value = e.target.value.trim();
            if (value && !isValidOthersLivelihoodText(value)) {
                markFieldError(otherSecondaryInput, 'Only letters, numbers, spaces, hyphens, apostrophes, periods, commas allowed');
            } else {
                clearFieldError(otherSecondaryInput);
            }
            
            // Auto-capitalize
            if (value) {
                e.target.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        });
    }
}

/**
 * Setup validation for Edit Modal
 */
function setupEditModalValidation() {
    // Similar to Add modal but with 'edit_fishr_' prefix
    const textFields = [
        { id: 'edit_fishr_first_name', pattern: /^[a-zA-Z\s\'-]*$/, message: 'Only letters, spaces, hyphens, apostrophes' },
        { id: 'edit_fishr_middle_name', pattern: /^[a-zA-Z\s\'-]*$/, message: 'Only letters, spaces, hyphens, apostrophes' },
        { id: 'edit_fishr_last_name', pattern: /^[a-zA-Z\s\'-]*$/, message: 'Only letters, spaces, hyphens, apostrophes' },
        { id: 'edit_fishr_extension', pattern: /^[a-zA-Z.\s]*$/, message: 'Only letters, periods, spaces' }
    ];

    textFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (!input) return;

        input.addEventListener('blur', function(e) {
            const value = e.target.value;
            if (value && !field.pattern.test(value)) {
                markFieldError(input, field.message);
            } else {
                clearFieldError(input);
            }
            
            // Trigger change detection
            const form = document.getElementById('editFishrForm');
            if (form?.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
            }
        });

        // Auto-capitalize
        input.addEventListener('blur', function(e) {
            if (e.target.value) {
                e.target.value = e.target.value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        });
    });

    // Contact number
    const contactInput = document.getElementById('edit_fishr_contact_number');
    if (contactInput) {
        contactInput.addEventListener('blur', function(e) {
            const value = e.target.value.trim();
            const phoneRegex = /^09\d{9}$/;
            
            if (value && !phoneRegex.test(value)) {
                markFieldError(contactInput, 'Format: 09XXXXXXXXX (11 digits)');
            } else if (value) {
                clearFieldError(contactInput);
            }
            
            const form = document.getElementById('editFishrForm');
            if (form?.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
            }
        });
    }

    // Main livelihood
    const mainLivelihoodSelect = document.getElementById('edit_fishr_livelihood');
    if (mainLivelihoodSelect) {
        mainLivelihoodSelect.addEventListener('change', function() {
            toggleEditOtherFishrLivelihood();
            validateEditSecondaryLivelihood();
            
            const form = document.getElementById('editFishrForm');
            if (form?.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
            }
        });
    }

    // Other livelihood
    const otherLivelihoodInput = document.getElementById('edit_fishr_other_livelihood');
    if (otherLivelihoodInput) {
        otherLivelihoodInput.addEventListener('blur', function(e) {
            const value = e.target.value.trim();
            if (value && !isValidOthersLivelihoodText(value)) {
                markFieldError(otherLivelihoodInput, 'Only letters, numbers, spaces, hyphens, apostrophes, periods, commas allowed');
            } else {
                clearFieldError(otherLivelihoodInput);
            }
            
            // Auto-capitalize
            if (value) {
                e.target.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
            
            const form = document.getElementById('editFishrForm');
            if (form?.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
            }
        });
    }

    // Secondary livelihood
    const secondaryLivelihoodSelect = document.getElementById('edit_fishr_secondary_livelihood');
    if (secondaryLivelihoodSelect) {
        secondaryLivelihoodSelect.addEventListener('change', function() {
            toggleEditOtherSecondaryFishrLivelihood();
            validateEditSecondaryLivelihood();
            
            const form = document.getElementById('editFishrForm');
            if (form?.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
            }
        });
    }

    // Other secondary livelihood
    const otherSecondaryInput = document.getElementById('edit_fishr_other_secondary_livelihood');
    if (otherSecondaryInput) {
        otherSecondaryInput.addEventListener('blur', function(e) {
            const value = e.target.value.trim();
            if (value && !isValidOthersLivelihoodText(value)) {
                markFieldError(otherSecondaryInput, 'Only letters, numbers, spaces, hyphens, apostrophes, periods, commas allowed');
            } else {
                clearFieldError(otherSecondaryInput);
            }
            
            // Auto-capitalize
            if (value) {
                e.target.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
            
            const form = document.getElementById('editFishrForm');
            if (form?.dataset.registrationId) {
                checkEditFishrFormChanges(form.dataset.registrationId);
            }
        });
    }
}

// =====================================================
// UNIFIED HELPER FUNCTIONS
// =====================================================

/**
 * Validate "others" text field pattern
 */
function isValidOthersLivelihoodText(text) {
    if (!text) return true;
    const validPattern = /^[a-zA-Z0-9\s\'-.,]*$/;
    return validPattern.test(text);
}

/**
 * Toggle other secondary livelihood field visibility (Add Modal)
 */
function toggleAddOtherSecondaryLivelihood() {
    const secondaryLivelihood = document.getElementById('fishr_secondary_livelihood').value;
    const container = document.getElementById('add_other_secondary_livelihood_container');
    const input = document.getElementById('fishr_other_secondary_livelihood');

    if (secondaryLivelihood === 'others') {
        container.style.display = 'block';
        input.required = true;
    } else {
        container.style.display = 'none';
        input.required = false;
        input.value = '';
        clearFieldError(input);
    }
}

/**
 * Validate secondary cannot match main (Add Modal)
 */
function validateSecondaryLivelihoodMatch() {
    const mainValue = document.getElementById('fishr_main_livelihood').value;
    const secondaryValue = document.getElementById('fishr_secondary_livelihood').value;
    const warningElement = document.getElementById('add_secondary_livelihood_warning');

    if (secondaryValue && mainValue && secondaryValue === mainValue) {
        if (warningElement) warningElement.style.display = 'block';
        return false;
    } else {
        if (warningElement) warningElement.style.display = 'none';
        return true;
    }
}

/**
 * Toggle other livelihood field visibility (Edit Modal)
 */
function toggleEditOtherFishrLivelihood() {
    const livelihood = document.getElementById('edit_fishr_livelihood').value;
    const container = document.getElementById('edit_other_fishr_livelihood_container');
    const input = document.getElementById('edit_fishr_other_livelihood');

    if (livelihood === 'others') {
        container.style.display = 'block';
        input.required = true;
    } else {
        container.style.display = 'none';
        input.required = false;
        input.value = '';
        clearFieldError(input);
    }
}

/**
 * Toggle other secondary livelihood field visibility (Edit Modal)
 */
function toggleEditOtherSecondaryFishrLivelihood() {
    const secondaryLivelihood = document.getElementById('edit_fishr_secondary_livelihood').value;
    const container = document.getElementById('edit_other_fishr_secondary_livelihood_container');
    const input = document.getElementById('edit_fishr_other_secondary_livelihood');

    if (secondaryLivelihood === 'others') {
        container.style.display = 'block';
        input.required = true;
    } else {
        container.style.display = 'none';
        input.required = false;
        input.value = '';
        clearFieldError(input);
    }
}

/**
 * Validate secondary in edit modal
 */
function validateEditSecondaryLivelihood() {
    const mainValue = document.getElementById('edit_fishr_livelihood').value;
    const secondaryValue = document.getElementById('edit_fishr_secondary_livelihood').value;
    const warning = document.getElementById('edit_secondary_livelihood_warning');

    // If no secondary selected, no validation needed
    if (!secondaryValue) {
        if (warning) warning.style.display = 'none';
        document.getElementById('edit_fishr_secondary_livelihood').classList.remove('is-invalid');
        return true;
    }

    // CASE 1: Both are "others" - Allow it (they specify different text in other fields)
    if (mainValue === 'others' && secondaryValue === 'others') {
        if (warning) warning.style.display = 'none';
        document.getElementById('edit_fishr_secondary_livelihood').classList.remove('is-invalid');
        return true;
    }

    // CASE 2: Same standard type (both capture, both aquaculture, etc.) - NOT ALLOWED
    if (secondaryValue === mainValue && mainValue !== 'others') {
        if (warning) {
            warning.textContent = 'Secondary livelihood cannot be the same as main livelihood';
            warning.style.display = 'block';
        }
        document.getElementById('edit_fishr_secondary_livelihood').classList.add('is-invalid');
        return false;
    }

    // CASE 3: Secondary is standard type, main is "others" - Allow it
    if (mainValue === 'others' && secondaryValue !== 'others') {
        if (warning) warning.style.display = 'none';
        document.getElementById('edit_fishr_secondary_livelihood').classList.remove('is-invalid');
        return true;
    }

    // CASE 4: Main is standard type, secondary is "others" - Allow it
    if (mainValue !== 'others' && secondaryValue === 'others') {
        if (warning) warning.style.display = 'none';
        document.getElementById('edit_fishr_secondary_livelihood').classList.remove('is-invalid');
        return true;
    }

    // Default: Allow
    if (warning) warning.style.display = 'none';
    document.getElementById('edit_fishr_secondary_livelihood').classList.remove('is-invalid');
    return true;
}

/**
 * COMPLETE FORM VALIDATION - FIXED VERSION
 * Properly validates all required fields including barangay and main livelihood
 * @param {boolean} isEditMode - true for edit, false for add
 * @returns {boolean} - true if valid
 */
function validateFishrFormOnSubmit(isEditMode = false) {
    console.log('🔍 validateFishrFormOnSubmit called, isEditMode:', isEditMode);
    
    let isValid = true;
    const prefix = isEditMode ? 'edit_fishr_' : 'fishr_';
    
    // Clear all previous errors
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
        const error = el.parentNode.querySelector('.invalid-feedback');
        if (error) error.remove();
    });
    
    // ========== REQUIRED FIELDS ==========
    const requiredFields = [
        { id: `${prefix}first_name`, label: 'First Name' },
        { id: `${prefix}last_name`, label: 'Last Name' },
        { id: `${prefix}sex`, label: 'Sex' },
        { id: `${prefix}contact_number`, label: 'Contact Number' },
        { id: `${prefix}barangay`, label: 'Barangay' }
    ];
    
    // Add main livelihood with proper ID mapping
    if (isEditMode) {
        requiredFields.push({ id: 'edit_fishr_livelihood', label: 'Main Livelihood' });
    } else {
        requiredFields.push({ id: 'fishr_main_livelihood', label: 'Main Livelihood' });
    }
    
    // For Add form, also require status
    if (!isEditMode) {
        requiredFields.push({ id: 'fishr_status', label: 'Status' });
    }
    
    console.log(`Checking ${requiredFields.length} required fields...`);
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field.id);
        
        if (!input) {
            console.warn(`❌ Field not found: ${field.id}`);
            return;
        }
        
        const value = input.value ? input.value.trim() : '';
        console.log(`Field ${field.id}: "${value}" (exists: ${!!input})`);
        
        if (!value) {
            console.error(`❌ ${field.label} is empty`);
            input.classList.add('is-invalid');
            input.style.borderColor = '#dc3545';
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = `${field.label} is required`;
            
            // Remove existing error first
            const existingError = input.parentNode.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();
            
            input.parentNode.appendChild(errorDiv);
            
            isValid = false;
        } else {
            console.log(`✅ ${field.label} is filled`);
        }
    });
    
    // ========== CONTACT NUMBER FORMAT ==========
    const contactInput = document.getElementById(`${prefix}contact_number`);
    if (contactInput && contactInput.value) {
        const contactValue = contactInput.value.trim();
        const phoneRegex = /^09\d{9}$/;
        
        if (!phoneRegex.test(contactValue)) {
            console.error(`❌ Invalid contact format: "${contactValue}"`);
            contactInput.classList.add('is-invalid');
            contactInput.style.borderColor = '#dc3545';
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = 'Format: 09XXXXXXXXX (11 digits starting with 09)';
            
            const existing = contactInput.parentNode.querySelector('.invalid-feedback');
            if (existing) existing.remove();
            
            contactInput.parentNode.appendChild(errorDiv);
            isValid = false;
        } else {
            console.log(`✅ Contact format valid: "${contactValue}"`);
        }
    }
    
    // ========== MAIN LIVELIHOOD "OTHERS" ==========
    // Find main livelihood field
    let mainLivelihoodField = null;
    if (isEditMode) {
        mainLivelihoodField = document.getElementById('edit_fishr_livelihood');
    } else {
        mainLivelihoodField = document.getElementById('fishr_main_livelihood');
    }
    
    if (mainLivelihoodField && mainLivelihoodField.value === 'others') {
        const otherLivelihoodId = `${prefix}other_livelihood`;
        const otherLivelihood = document.getElementById(otherLivelihoodId);
        
        if (!otherLivelihood) {
            console.warn(`❌ Other livelihood field not found: ${otherLivelihoodId}`);
        } else {
            const otherValue = otherLivelihood.value ? otherLivelihood.value.trim() : '';
            
            if (!otherValue) {
                console.error(`❌ Other livelihood is required when main = "others"`);
                otherLivelihood.classList.add('is-invalid');
                otherLivelihood.style.borderColor = '#dc3545';
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please specify your livelihood type';
                
                const existing = otherLivelihood.parentNode.querySelector('.invalid-feedback');
                if (existing) existing.remove();
                
                otherLivelihood.parentNode.appendChild(errorDiv);
                
                isValid = false;
            } else {
                console.log(`✅ Other livelihood specified: "${otherValue}"`);
            }
        }
    }
    
    // ========== SECONDARY LIVELIHOOD ==========
    const secondaryLivelihood = document.getElementById(`${prefix}secondary_livelihood`);
    if (secondaryLivelihood && secondaryLivelihood.value) {
        const secondaryValue = secondaryLivelihood.value;
        const mainValue = mainLivelihoodField ? mainLivelihoodField.value : '';
        
        // Cannot be same as main (UNLESS both are "others")
        if (secondaryValue === mainValue && mainValue !== 'others') {
            console.error(`❌ Secondary cannot equal main livelihood`);
            secondaryLivelihood.classList.add('is-invalid');
            secondaryLivelihood.style.borderColor = '#dc3545';
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = 'Secondary livelihood cannot be the same as main livelihood';
            
            const existing = secondaryLivelihood.parentNode.querySelector('.invalid-feedback');
            if (existing) existing.remove();
            
            secondaryLivelihood.parentNode.appendChild(errorDiv);
            
            isValid = false;
        } else {
            console.log(`✅ Secondary livelihood valid: "${secondaryValue}"`);
        }
        
        // If secondary is "others", must have text
        if (secondaryValue === 'others') {
            const otherSecondaryId = `${prefix}other_secondary_livelihood`;
            const otherSecondary = document.getElementById(otherSecondaryId);
            
            if (!otherSecondary) {
                console.warn(`❌ Other secondary livelihood field not found: ${otherSecondaryId}`);
            } else {
                const otherSecondaryValue = otherSecondary.value ? otherSecondary.value.trim() : '';
                
                if (!otherSecondaryValue) {
                    console.error(`❌ Other secondary livelihood is required when secondary = "others"`);
                    otherSecondary.classList.add('is-invalid');
                    otherSecondary.style.borderColor = '#dc3545';
                    
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Please specify secondary livelihood';
                    
                    const existing = otherSecondary.parentNode.querySelector('.invalid-feedback');
                    if (existing) existing.remove();
                    
                    otherSecondary.parentNode.appendChild(errorDiv);
                    
                    isValid = false;
                }
            }
        }
    }
    
    // ========== FILE VALIDATION ==========
    const fileInput = document.getElementById(`${prefix}supporting_document`);
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (file.size > maxSize) {
            console.error(`❌ File too large: ${file.size} bytes (max: ${maxSize})`);
            fileInput.classList.add('is-invalid');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = 'File must not exceed 10MB';
            
            const existing = fileInput.parentNode.querySelector('.invalid-feedback');
            if (existing) existing.remove();
            
            fileInput.parentNode.appendChild(errorDiv);
            
            isValid = false;
        } else {
            console.log(`✅ File size valid: ${file.size} bytes`);
        }
    }
    
    console.log('✅ Validation complete. Result:', isValid);
    return isValid;
}


/**
 * Auto-capitalize on blur (universal)
 */
function capitalizeNameField(input) {
    if (!input || !input.value) return;
    const value = input.value.trim();
    input.value = value
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

/**
 * Auto-capitalize livelihood "others" field (universal)
 */
function capitalizeLivelihoodOthers(input) {
    if (!input || !input.value) return;
    const value = input.value.trim();
    input.value = value
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}
// =====================================================
// SINGLE INITIALIZATION (runs once on page load)
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    // Setup Add Modal
    const addModal = document.getElementById('addFishrModal');
    if (addModal) {
        addModal.addEventListener('shown.bs.modal', setupAddModalListeners);
    }

    // Setup Edit Modal
    const editModal = document.getElementById('editFishrModal');
    if (editModal) {
        editModal.addEventListener('shown.bs.modal', setupEditModalListeners);
    }

    console.log('✅ FishR Validation setup complete');
});
    </script>
@endsection