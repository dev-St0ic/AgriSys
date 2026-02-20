{{-- resources/views/admin/rsbsa_applications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'RSBSA Registrations - AgriSys Admin')
@section('page-icon', 'fas fa-file-alt')
@section('page-title', 'RSBSA Registrations')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-file-alt text-primary"></i>
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
            <form method="GET" action="{{ route('admin.rsbsa.applications') }}" id="filterForm">
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
                        <select name="main_livelihood" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Livelihood</option>
                            <option value="Farmer" {{ request('main_livelihood') == 'Farmer' ? 'selected' : '' }}>
                                Farmer
                            </option>
                            <option value="Farmworker/Laborer"
                                {{ request('main_livelihood') == 'Farmworker/Laborer' ? 'selected' : '' }}>
                                Farmworker/Laborer
                            </option>
                            <option value="Fisherfolk" {{ request('main_livelihood') == 'Fisherfolk' ? 'selected' : '' }}>
                                Fisherfolk
                            </option>
                            <option value="Agri-youth" {{ request('main_livelihood') == 'Agri-youth' ? 'selected' : '' }}>
                                Agri-youth
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
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search name, number, contact..." value="{{ request('search') }}"
                                oninput="autoSearch()" id="searchInput">
                            <button class="btn btn-outline-secondary" type="submit" title="Search" id="searchButton">
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
                        <a href="{{ route('admin.rsbsa.applications') }}" class="btn btn-secondary btn-sm w-100">
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
                    <i class="fas fa-file-alt me-2"></i>RSBSA Registrations
                </h6>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="showAddRsbsaModal()">
                    <i class="fas fa-user-plus me-2"></i>Add Registration
                </button>
                <a href="{{ route('admin.rsbsa.export') }}" class="btn btn-success btn-sm">
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
                            <th class="text-center">Livelihood</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Documents</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr data-id="{{ $application->id }}">
                                <td class="text-start">{{ $application->created_at->format('M d, Y g:i A') }}</td>
                                <td class="text-start">
                                    <strong class="text-primary">{{ $application->application_number }}</strong>
                                </td>
                                <td class="text-start">
                                    {{ $application->first_name }}
                                    @if ($application->middle_name)
                                        {{ $application->middle_name }}
                                    @endif
                                    {{ $application->last_name }}
                                    @if ($application->name_extension)
                                        {{ $application->name_extension }}
                                    @endif
                                </td>
                                <td class="text-start">
                                    @php
                                        $livelihoodColors = [
                                            'Farmer' => 'success',
                                            'Farmworker/Laborer' => 'warning',
                                            'Fisherfolk' => 'info',
                                            'Agri-youth' => 'primary',
                                        ];
                                        $bgColor = $livelihoodColors[$application->main_livelihood] ?? 'secondary';
                                    @endphp

                                    <span
                                        class="badge bg-{{ $bgColor }} fs-6">{{ $application->main_livelihood }}</span>
                                </td>
                                <td class="text-start">
                                    <span class="badge bg-{{ $application->status_color }} fs-6">
                                        {{ $application->formatted_status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="rsbsa-table-documents">
                                        @if ($application->supporting_document_path)
                                            <div class="rsbsa-document-previews">
                                                <div class="rsbsa-mini-doc"
                                                    onclick="viewDocument('{{ $application->supporting_document_path }}', 'Application #{{ $application->application_number }} - Supporting Document')"
                                                    title="Supporting Document">
                                                    <div class="rsbsa-mini-doc-icon">
                                                        <i class="fas fa-file-alt text-primary"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rsbsa-document-summary"
                                                onclick="viewDocument('{{ $application->supporting_document_path }}', 'Application #{{ $application->application_number }} - Supporting Document')">
                                                <small class="text-muted">1 document</small>
                                            </div>
                                        @else
                                            <div class="rsbsa-no-documents">
                                                <i class="fas fa-folder-open text-muted"></i>
                                                <small class="text-muted">No documents</small>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="viewApplication({{ $application->id }})" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                        <button class="btn btn-sm btn-outline-dark"
                                            onclick="showUpdateModal({{ $application->id }}, '{{ $application->status }}')"
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
                                                        onclick="showEditRsbsaModal({{ $application->id }})">
                                                        <i class="fas fa-edit me-2 text-success"></i>Edit Information
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                        onclick="deleteApplication({{ $application->id }})">
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
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                                    <p>No RSBSA registrations found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($applications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            {{-- Previous Page Link --}}
                            @if ($applications->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Back</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $applications->previousPageUrl() }}"
                                        rel="prev">Back</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $applications->currentPage();
                                $lastPage = $applications->lastPage();
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
                                            href="{{ $applications->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($applications->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $applications->nextPageUrl() }}"
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
                                    <!-- <div class="mb-2">
                                                <small class="text-muted d-block">Application ID</small>
                                                <strong id="updateAppId" class="text-primary">-</strong>
                                            </div> -->
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Application #</small>
                                        <strong id="updateAppNumber">-</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Applicant Name</small>
                                        <strong id="updateAppName">-</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- <div class="mb-2">
                                                <small class="text-muted d-block">Application Type</small>
                                                <strong id="updateAppType">-</strong>
                                            </div> -->
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Barangay</small>
                                        <strong id="updateAppBarangay">-</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Current Status</small>
                                        <strong id="updateAppCurrentStatus">-</strong>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block mb-2">Main Livelihood</small>
                                    <strong id="updateAppLivelihood">-</strong>
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
                                    placeholder="Add any notes or comments about this status change..." maxlength="1000"
                                    oninput="updateRemarksCounter()"></textarea>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Provide context for this status update
                                    </small>
                                    <small class="text-muted" id="remarksCounter">
                                        <span id="charCount">0</span>/1000
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Status Change Alert -->
                        <div class="alert alert-info border-left-info mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Note:</strong> Your changes will be logged and the applicant will be notified of the
                            status update.
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="updateApplicationStatus()">
                        <i class="fas fa-save me-2"></i>Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editRsbsaModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Edit Application - <span id="editAppNumber"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="editRsbsaForm" enctype="multipart/form-data">
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
                                        <label for="edit_rsbsa_first_name" class="form-label fw-semibold">
                                            First Name
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="edit_rsbsa_first_name"
                                            name="first_name" required maxlength="100" placeholder="First name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="edit_rsbsa_middle_name" class="form-label fw-semibold">
                                            Middle Name
                                        </label>
                                        <input type="text" class="form-control" id="edit_rsbsa_middle_name"
                                            name="middle_name" maxlength="100" placeholder="Middle name (optional)">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="edit_rsbsa_last_name" class="form-label fw-semibold">
                                            Last Name
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="edit_rsbsa_last_name"
                                            name="last_name" required maxlength="100" placeholder="Last name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="edit_rsbsa_extension" class="form-label fw-semibold">
                                            Extension
                                        </label>
                                        <select class="form-select" id="edit_rsbsa_extension" name="name_extension">
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
                                        <label for="edit_rsbsa_sex" class="form-label fw-semibold">
                                            Sex
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="edit_rsbsa_sex" name="sex" required>
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Preferred not to say">Preferred not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_rsbsa_contact_number" class="form-label fw-semibold">
                                            Contact Number
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="edit_rsbsa_contact_number"
                                            name="contact_number" required placeholder="09XXXXXXXXX"
                                            pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>09XXXXXXXXX
                                        </small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_rsbsa_app_number" class="form-label fw-semibold">
                                            Application Number
                                        </label>
                                        <input type="text" class="form-control" id="edit_rsbsa_app_number" disabled
                                            placeholder="-">
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
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_rsbsa_barangay" class="form-label fw-semibold">
                                            Barangay
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="edit_rsbsa_barangay" name="barangay" required>
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
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_rsbsa_address" class="form-label fw-semibold">
                                            Address
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="edit_rsbsa_address" name="address" rows="2" maxlength="500" required
                                            placeholder="Complete residential address"></textarea>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>Maximum 500 characters
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Livelihood Information Card - NOW FULLY EDITABLE WITH DYNAMIC FIELDS -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-seedling me-2"></i>Livelihood Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="edit_rsbsa_livelihood" class="form-label fw-semibold">
                                            Main Livelihood
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="edit_rsbsa_livelihood" name="main_livelihood"
                                            required onchange="toggleEditRsbsaLivelihoodFields(this)">
                                            <option value="">Select Livelihood</option>
                                            <option value="Farmer">Farmer</option>
                                            <option value="Farmworker/Laborer">Farmworker/Laborer</option>
                                            <option value="Fisherfolk">Fisherfolk</option>
                                            <option value="Agri-youth">Agri-youth</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- FARMER FIELDS -->
                                <div id="edit-farmer-fields" style="display: none;">
                                    <div class="alert alert-info border-0 mb-3">
                                        <i class="fas fa-leaf me-2"></i><strong>Farmer Information</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_farmer_crops" class="form-label fw-semibold">
                                                Main Crops <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="edit_rsbsa_farmer_crops"
                                                name="farmer_crops" maxlength="100"
                                                placeholder="e.g., Rice, Corn, Vegetables">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_farmer_land_area" class="form-label fw-semibold">
                                                Land Area (hectares)</span>
                                            </label>
                                            <input type="number" class="form-control" id="edit_rsbsa_farmer_land_area"
                                                name="farmer_land_area" step="0.01" min="0" max="1000"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_farmer_type_of_farm" class="form-label fw-semibold">
                                                Type of Farm <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="edit_rsbsa_farmer_type_of_farm"
                                                name="farmer_type_of_farm">
                                                <option value="">Select Type</option>
                                                <option value="Irrigated">Irrigated</option>
                                                <option value="Rainfed Upland">Rainfed Upland</option>
                                                <option value="Rainfed Lowland">Rainfed Lowland</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_farmer_land_ownership" class="form-label fw-semibold">
                                                Land Ownership <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="edit_rsbsa_farmer_land_ownership"
                                                name="farmer_land_ownership">
                                                <option value="">Select Ownership</option>
                                                <option value="Owner">Owner</option>
                                                <option value="Tenant">Tenant</option>
                                                <option value="Lessee">Lessee</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_farm_location" class="form-label fw-semibold">
                                                Farm Location <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control" id="edit_rsbsa_farm_location" name="farm_location" rows="2" maxlength="500"
                                                placeholder="Specific location of farm"></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_farmer_special_status" class="form-label fw-semibold">
                                                Special Status
                                            </label>
                                            <select class="form-select" id="edit_rsbsa_farmer_special_status"
                                                name="farmer_special_status">
                                                <option value="">Select Status</option>
                                                <option value="Ancestral Domain">Ancestral Domain</option>
                                                <option value="Agrarian Reform Beneficiary">Agrarian Reform Beneficiary
                                                </option>
                                                <option value="None">None</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="edit_rsbsa_farmer_commodity" class="form-label fw-semibold">
                                                Commodity/Product
                                            </label>
                                            <input type="text" class="form-control" id="edit_rsbsa_farmer_commodity"
                                                name="commodity" maxlength="1000"
                                                placeholder="e.g., Rice, Corn, Vegetables">
                                        </div>
                                    </div>
                                </div>

                                <!-- FARMWORKER FIELDS -->
                                <div id="edit-farmworker-fields" style="display: none;">
                                    <div class="alert alert-info border-0 mb-3">
                                        <i class="fas fa-hammer me-2"></i><strong>Farmworker/Laborer Information</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_farmworker_type" class="form-label fw-semibold">
                                                Type of Work <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="edit_rsbsa_farmworker_type"
                                                name="farmworker_type"
                                                placeholder="e.g., Farm Laborer, Harvester, Planter">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_farmworker_commodity" class="form-label fw-semibold">
                                                Commodity/Crop You Work With
                                            </label>
                                            <input type="text" class="form-control"
                                                id="edit_rsbsa_farmworker_commodity" name="commodity" maxlength="1000"
                                                placeholder="e.g., Rice, Corn, Vegetables">
                                        </div>
                                    </div>
                                </div>

                                <!-- FISHERFOLK FIELDS -->
                                <div id="edit-fisherfolk-fields" style="display: none;">
                                    <div class="alert alert-info border-0 mb-3">
                                        <i class="fas fa-fish me-2"></i><strong>Fisherfolk Information</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_fisherfolk_activity" class="form-label fw-semibold">
                                                Fishing Activity <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                id="edit_rsbsa_fisherfolk_activity" name="fisherfolk_activity"
                                                placeholder="e.g., Bangus Aquaculture, Tilapia Pond Farming">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_fisherfolk_commodity" class="form-label fw-semibold">
                                                Main Fish/Aquatic Product
                                            </label>
                                            <input type="text" class="form-control"
                                                id="edit_rsbsa_fisherfolk_commodity" name="commodity" maxlength="1000"
                                                placeholder="e.g., Bangus, Tilapia, Mud Crab, Seaweed">
                                        </div>
                                    </div>
                                </div>

                                <!-- AGRI-YOUTH FIELDS -->
                                <div id="edit-agriyouth-fields" style="display: none;">
                                    <div class="alert alert-info border-0 mb-3">
                                        <i class="fas fa-user-tie me-2"></i><strong>Agri-Youth Information</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_agriyouth_household" class="form-label fw-semibold">
                                                From Farming Household? <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="edit_rsbsa_agriyouth_household"
                                                name="agriyouth_farming_household">
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_agriyouth_training" class="form-label fw-semibold">
                                                Agricultural Training <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="edit_rsbsa_agriyouth_training"
                                                name="agriyouth_training"
                                                placeholder="e.g., Crop production, Livestock raising">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_agriyouth_participation"
                                                class="form-label fw-semibold">
                                                Program Participation <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="edit_rsbsa_agriyouth_participation"
                                                name="agriyouth_participation">
                                                <option value="">Select Participation</option>
                                                <option value="Participated">Participated</option>
                                                <option value="Not Participated">Not Participated</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_rsbsa_agriyouth_commodity" class="form-label fw-semibold">
                                                Main Agricultural Focus
                                            </label>
                                            <input type="text" class="form-control"
                                                id="edit_rsbsa_agriyouth_commodity" name="commodity" maxlength="1000"
                                                placeholder="e.g., Organic Farming, Livestock, Aquaculture">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i></i>Supporting Document
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-4">
                                    <i class="fas fa-info-circle me-1"></i>
                                    View or upload supporting document. Supported formats: JPG, PNG, PDF (Max 10MB)
                                </p>
                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <label for="edit_rsbsa_supporting_document" class="form-label fw-semibold">
                                            Supporting Document
                                        </label>
                                        <div id="edit_rsbsa_supporting_document_preview" class="mb-3"></div>
                                        <input type="file" class="form-control" id="edit_rsbsa_supporting_document"
                                            name="supporting_document" accept="image/*,.pdf"
                                            onchange="previewEditRsbsaDocument('edit_rsbsa_supporting_document', 'edit_rsbsa_supporting_document_preview')">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>Click to view or upload a new document
                                        </small>
                                    </div>
                                </div>
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
                                            <span id="edit_rsbsa_status_badge" class="badge bg-secondary fs-6"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <small class="text-muted d-block mb-2">Date Applied</small>
                                        <div id="edit_rsbsa_created_at" class="fw-semibold">-</div>
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
                    <button type="button" class="btn btn-primary" id="editRsbsaSubmitBtn"
                        onclick="handleEditRsbsaSubmit()">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE  MODAL -->
    <div class="modal fade" id="deleteRsbsaModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title w-100 text-center">Move RSBSA Registration to Recycle Bin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                        <p class="mb-0">Are you sure you want to delete this RSBSA Registration? <strong
                                id="delete_rsbsa_name"></strong> will be moved to the Recycle Bin.</p>
                    </div>
                    <ul class="mb-0">
                        <li>Remove the rsbsa registration from active records</li>
                        <li>Hide it from users and administrators</li>
                        <li>Keep all documents and attachments</li>
                        <li><strong>Can be restored from the Recycle Bin</strong></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmPermanentDeleteRsbsa()"
                        id="confirm_delete_rsbsa_btn">
                        <span class="btn-text">Move to Recycle Bin</span>
                        <span class="btn-loader" style="display: none;"><span
                                class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Details Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Application Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="applicationDetails">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

        /* Date Filter Modal Styling */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .modal-header.bg-info {
            background: linear-gradient(135deg, #17a2b8, #138496) !important;
            border-radius: 15px 15px 0 0;
        }

        .card.border-0.bg-light {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
            border-radius: 12px;
            transition: transform 0.2s ease;
        }

        .card.border-0.bg-light:hover {
            transform: translateY(-2px);
        }

        .btn-outline-success:hover,
        .btn-outline-info:hover,
        .btn-outline-warning:hover,
        .btn-outline-primary:hover,
        .btn-outline-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Date input styling */
        input[type="date"].form-control {
            padding: 0.75rem;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        input[type="date"].form-control:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            border: none;
            border-radius: 10px;
            border-left: 4px solid #17a2b8;
        }

        /* Date range container styling */
        .date-range-container {
            position: relative;
        }

        .date-range-container .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #495057;
        }

        /* Date input styling for small controls */
        input[type="date"].form-control-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        input[type="date"].form-control-sm:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Date quick filter buttons */
        .btn-group .btn-outline-info {
            border-color: #17a2b8;
            color: #17a2b8;
            font-size: 0.8rem;
            padding: 0.375rem 0.5rem;
        }

        .btn-group .btn-outline-info:hover {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }

        /* Custom Date Range Picker Modal */
        .date-picker-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1050;
            align-items: center;
            justify-content: center;
        }

        .date-picker-content {
            background: white;
            border-radius: 12px;
            padding: 20px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .date-picker-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .date-input-group {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .date-input-group .form-group {
            flex: 1;
        }

        .date-input-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            display: block;
        }

        .date-input-group input[type="date"] {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            font-size: 14px;
        }

        .date-input-group input[type="date"]:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .quick-date-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .quick-date-btn {
            padding: 10px 15px;
            border: 2px solid #dee2e6;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .quick-date-btn:hover {
            border-color: #667eea;
            background: #f8f9ff;
            transform: translateY(-1px);
        }

        .quick-date-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .date-picker-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 15px;
            border-top: 2px solid #e9ecef;
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

        /* Existing styles maintained */
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
            text-align: center;
        }

        #documentModal .modal-footer {
            border-radius: 0 0 12px 12px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-top: 1px solid #dee2e6;
        }

        #documentViewer {
            min-height: auto;
            max-height: none;
            overflow-y: auto;
            overflow-x: hidden;
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

        /* Application Details Modal - Simple Professional Look */
        /* ============================================
                                                    VIEW MODAL STYLING - CONSISTENT WITH OTHER SERVICES
                                                    ============================================ */

        /* Application Details Modal - Enhanced Card-Based Styling */
        #applicationModal .modal-content {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        #applicationModal .modal-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border-bottom: 2px solid #0b5ed7;
            padding: 1.5rem;
        }

        #applicationModal .modal-header .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: white;
        }

        #applicationModal .modal-header .btn-close {
            opacity: 0.8;
        }

        #applicationModal .modal-header .btn-close:hover {
            opacity: 1;
        }

        #applicationModal .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 1.25rem;
        }

        #applicationModal .modal-body {
            padding: 2rem;
            background-color: #fff;
        }

        /* Card Styling within Application Details */
        #applicationDetails .card {
            border-width: 2px;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        #applicationDetails .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        #applicationDetails .card-header {
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: white;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }

        #applicationDetails .card-header.bg-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
        }

        #applicationDetails .card-header.bg-info {
            background: linear-gradient(135deg, #0dcaf0 0%, #0bb5db 100%) !important;
        }

        #applicationDetails .card-header.bg-success {
            background: linear-gradient(135deg, #198754 0%, #157347 100%) !important;
        }

        #applicationDetails .card-header.bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
            color: #000;
        }

        #applicationDetails .card-header.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5c636a 100%) !important;
        }

        #applicationDetails .card-body {
            padding: 1.5rem;
            background-color: #fff;
        }

        #applicationDetails .row.g-2>div {
            padding-bottom: 0.5rem;
        }

        #applicationDetails .card-body>div>div {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        #applicationDetails .card-body>div>div:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        #applicationDetails strong {
            color: #495057;
            font-weight: 600;
            display: block;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.25rem;
        }

        #applicationDetails .card-body span {
            color: #333;
            font-size: 0.95rem;
            display: block;
        }

        #applicationDetails a {
            color: #0d6efd;
            text-decoration: none;
        }

        #applicationDetails a:hover {
            text-decoration: underline;
        }

        #applicationDetails .text-muted {
            color: #6c757d !important;
            font-style: italic;
        }

        /* Badge Styling */
        #applicationDetails .badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-top: 0.25rem;
        }

        /* Document Container Styling */
        #applicationDetails .text-center.p-3 {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 2rem 1.5rem !important;
        }

        #applicationDetails .text-center i {
            opacity: 0.7;
            margin-bottom: 1rem;
        }

        #applicationDetails .text-center h6 {
            font-weight: 600;
            color: #333;
            margin: 0.5rem 0;
            font-size: 0.95rem;
        }

        #applicationDetails .btn-outline-info {
            color: #0dcaf0;
            border-color: #0dcaf0;
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }

        #applicationDetails .btn-outline-info:hover {
            background-color: #0dcaf0;
            border-color: #0dcaf0;
            color: white;
        }
        
        /* Fix View Document button - center icon and text */
        #applicationDetails .card.border-secondary .btn-primary {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
        }

        #applicationDetails .card.border-secondary .btn-primary i.fas {
            margin: 0 !important;
            line-height: 1 !important;
            vertical-align: middle !important;
            font-size: 1em !important;
        }

        #applicationDetails .card.border-secondary {
            background-color: #ffffff !important;
        }

        #applicationDetails .card.border-secondary .card-body {
            background-color: #ffffff !important;
        }

        #applicationDetails .card.border-secondary .card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
            color: #ffffff !important;
        }

        #applicationDetails .card.border-secondary .card-header h6 {
            color: #ffffff !important;
        }

        #applicationDetails .card.border-secondary .card-header i {
            color: #ffffff !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            #applicationModal .modal-dialog {
                margin: 0.5rem;
            }

            #applicationModal .modal-body {
                padding: 1.5rem 1rem;
            }

            #applicationDetails .row.g-4>div {
                margin-bottom: 1rem;
            }

            #applicationDetails .card-header {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }

            #applicationDetails .card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            #applicationModal .modal-header .modal-title {
                font-size: 1.05rem;
            }

            #applicationModal .modal-body {
                padding: 1rem;
            }

            #applicationDetails .text-center.p-3 {
                padding: 1.5rem 1rem !important;
            }

            #applicationDetails .card-body span {
                font-size: 0.9rem;
            }
        }

        /* Update Modal - Enhanced Styling */
        #updateModal .modal-content {
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }

        #updateModal .modal-header {
            border-radius: 12px 12px 0 0;
            border: none;
            padding: 1.5rem;
        }

        /* #updateModal .modal-header {
                                                    border-bottom: 1px solid #dee2e6;
                                                } */

        #updateModal .modal-header .modal-title {
            /* color: black; */
            display: block;
            font-weight: 600;
            font-size: 1.25rem;
        }

        #updateModal .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        #updateModal .modal-footer {
            border-radius: 0 0 12px 12px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-top: 1px solid #dee2e6;
            padding: 1.5rem;
        }

        #updateModal .modal-body {
            padding: 2rem;
        }

        /* Application Info Card in Update Modal */
        #updateModal .card.bg-light {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        #updateModal .card.bg-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        #updateModal .card-body {
            padding: 1.5rem;
        }

        #updateModal .card-title {
            color: #007bff;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #updateModal .card-body p {
            margin-bottom: 0.5rem;
            color: #333;
            font-size: 0.95rem;
        }

        #updateModal .card-body strong {
            color: #495057;
            font-weight: 600;
        }

        #updateModal .badge {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        /* Form Controls in Update Modal */
        #updateModal .form-label {
            color: #495057;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #updateModal .form-select,
        #updateModal .form-control {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 0.75rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        #updateModal .form-select:focus,
        #updateModal .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            outline: none;
        }

        #updateModal .form-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        /* Form Changed Visual Feedback */
        #updateModal .form-changed {
            border-left: 3px solid #ffc107 !important;
            background-color: #fff3cd;
            transition: all 0.3s ease;
        }

        #updateModal .change-indicator {
            position: relative;
        }

        #updateModal .change-indicator::after {
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

        #updateModal .change-indicator.changed::after {
            opacity: 1;
        }

        #applicationModal .modal-footer .btn {
            border-radius: 6px;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        #applicationModal .modal-footer .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        #applicationModal .modal-footer .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }



        #updateModal .modal-footer .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }

        #updateModal .modal-footer .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #updateModal .modal-footer .btn-primary.no-changes {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            #applicationModal .modal-dialog {
                margin: 0.5rem;
            }

            #updateModal .modal-dialog {
                margin: 0.5rem;
            }

            #applicationDetails .col-md-6 {
                flex: 0 0 100%;
            }

            #applicationModal .modal-body {
                padding: 1rem;
            }

            #updateModal .modal-body {
                padding: 1rem;
            }

            #applicationModal .modal-header,
            #updateModal .modal-header {
                padding: 1rem;
            }

            #applicationModal .modal-footer,
            #updateModal .modal-footer {
                padding: 1rem;
            }

            #applicationModal .modal-footer .btn,
            #updateModal .modal-footer .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {

            #applicationModal .modal-dialog,
            #updateModal .modal-dialog {
                margin: 0.25rem;
            }

            #applicationDetails h6 {
                font-size: 0.9rem;
            }

            #applicationDetails p {
                font-size: 0.9rem;
            }

            #updateModal .card-title {
                font-size: 0.9rem;
            }

            #updateModal .form-label {
                font-size: 0.85rem;
            }

            #updateModal .form-select,
            #updateModal .form-control {
                font-size: 0.9rem;
                padding: 0.6rem;
            }
        }

        /* RSBSA-Style Table Document Previews */
        .rsbsa-table-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        .rsbsa-document-previews {
            display: flex;
            gap: 0.25rem;
            align-items: center;
        }

        .rsbsa-mini-doc {
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

        .rsbsa-mini-doc:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }

        .rsbsa-mini-doc-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .rsbsa-mini-doc-more {
            background: #f8f9fa;
            border-color: #dee2e6;
        }

        .rsbsa-mini-doc-more:hover {
            background: #e9ecef;
            border-color: #6c757d;
        }

        .rsbsa-more-count {
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
        }

        .rsbsa-mini-doc-more:hover .rsbsa-more-count {
            color: #495057;
        }

        .rsbsa-document-summary {
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .rsbsa-document-summary:hover {
            color: #007bff !important;
        }

        .rsbsa-document-summary:hover small {
            color: #007bff !important;
        }

        .rsbsa-no-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem;
            opacity: 0.7;
        }

        .rsbsa-no-documents i {
            font-size: 1.25rem;
        }

        /* Document type specific colors for mini previews */
        .rsbsa-mini-doc[title*="Supporting"] {
            border-color: #007bff;
        }

        .rsbsa-mini-doc[title*="Supporting"]:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        /* Responsive adjustments for table documents */
        @media (max-width: 768px) {
            .rsbsa-mini-doc {
                width: 28px;
                height: 28px;
            }

            .rsbsa-mini-doc-icon {
                font-size: 0.75rem;
            }

            .rsbsa-more-count {
                font-size: 0.7rem;
            }
        }

        /* RSBSA-Style Document Viewer */
        .rsbsa-document-viewer {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            min-height: 400px;
        }

        .rsbsa-document-container {
            position: relative;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 100%;
            margin-bottom: 1rem;
        }

        .rsbsa-document-image {
            max-width: 100%;
            max-height: 60vh;
            object-fit: contain;
            display: block;
            transition: transform 0.3s ease;
        }

        .rsbsa-document-image:hover {
            transform: scale(1.02);
        }

        .rsbsa-document-image.zoomed {
            transform: scale(1.5);
            cursor: zoom-out;
        }

        .rsbsa-pdf-embed {
            border-radius: 8px;
        }

        .rsbsa-document-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .rsbsa-document-size-badge {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .rsbsa-document-actions {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .rsbsa-btn {
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

        .rsbsa-btn-outline {
            background: white;
            color: #6c757d;
            border-color: #dee2e6;
        }

        .rsbsa-btn-outline:hover {
            background: #f8f9fa;
            color: #495057;
            border-color: #adb5bd;
            transform: translateY(-1px);
        }

        .rsbsa-btn-primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .rsbsa-btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
            color: white;
            transform: translateY(-1px);
        }

        .rsbsa-document-info {
            text-align: center;
            color: #6c757d;
        }

        .rsbsa-file-name {
            margin: 0;
            font-size: 0.9rem;
            word-break: break-word;
        }

        .rsbsa-document-placeholder {
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

        /* Responsive design for RSBSA document viewer */
        @media (max-width: 768px) {
            .rsbsa-document-actions {
                flex-direction: column;
                width: 100%;
            }

            .rsbsa-btn {
                width: 100%;
                min-width: auto;
            }

            .rsbsa-document-image {
                max-height: 40vh;
            }
        }

        /* Delete Modal Styling for RSBSA */
        #deleteRsbsaModal .modal-header {
            border-bottom: 1px solid #f8d7da;
            padding: 1.25rem 1.5rem;
        }

        #deleteRsbsaModal .modal-body {
            padding: 1.5rem;
            background-color: #fff;
        }

        #deleteRsbsaModal .alert {
            border: 1px solid #f5c6cb;
            margin-bottom: 1rem;
        }

        #deleteRsbsaModal .alert strong {
            font-weight: 600;
        }

        #deleteRsbsaModal ul {
            list-style-position: inside;
            color: #721c24;
        }

        #deleteRsbsaModal ul li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        #deleteRsbsaModal .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
        }

        #deleteRsbsaModal .btn-danger {
            transition: all 0.2s ease;
        }

        #deleteRsbsaModal .btn-danger:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        #deleteRsbsaModal .btn-secondary:hover {
            transform: translateY(-1px);
        }

        #deleteRsbsaModal .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }

        /* Modal backdrop consistency */
        #deleteRsbsaModal .modal-backdrop {
            opacity: 0.5;
        }

        #documentModal .modal-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
            border: none !important;
            padding: 1.5rem !important;
        }

        /* FORCE CENTER THE DOCUMENT MODAL HEADER */
        #documentModal .modal-header {
            justify-content: center !important;
            text-align: center !important;
            position: relative !important;
        }

        #documentModal .modal-title {
            width: 100% !important;
            text-align: center !important;
        }

        #documentModal .btn-close {
            position: absolute !important;
            right: 1rem !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }

        #documentModal .modal-header.bg-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
        }

        #documentModal .modal-title {
            color: white !important;
            font-weight: 600 !important;
            font-size: 1.25rem !important;
        }

        #documentModal .btn-close-white {
            filter: brightness(0) invert(1) !important;
            opacity: 0.8 !important;
        }

        #documentModal .btn-close-white:hover {
            opacity: 1 !important;
        }
    </style>

    <!-- Document Viewer Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white text-center">
                    <h5 class="modal-title align-center"><i></i>Document Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="documentViewer">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary mb-3" role="status"
                                style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted">Loading document...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter Modal -->
    <div class="modal fade" id="dateFilterModal" tabindex="-1" aria-labelledby="dateFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-height: 90vh;">
            <div class="modal-content"
                style="border-radius: 10px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); max-height: 90vh; display: flex; flex-direction: column;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center" id="dateFilterModalLabel"
                        style="color: white; font-weight: 600;">
                        <i></i>Select Date Range
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" style="overflow-y: auto; padding: 2rem; flex: 1;">
                    <div class="row g-4">
                        <!-- Date Range Inputs -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100"
                                style="border-radius: 12px; background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3" style="font-weight: 600;">
                                        <i class="fas fa-calendar-plus me-2"></i>Custom Date Range
                                    </h6>
                                    <div class="mb-3">
                                        <label for="modal_date_from" class="form-label"
                                            style="font-weight: 500; color: #495057;">From Date</label>
                                        <input type="date" id="modal_date_from" class="form-control"
                                            value="{{ request('date_from') }}"
                                            style="border-radius: 8px; border: 1px solid #e9ecef;">
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal_date_to" class="form-label"
                                            style="font-weight: 500; color: #495057;">To Date</label>
                                        <input type="date" id="modal_date_to" class="form-control"
                                            value="{{ request('date_to') }}"
                                            style="border-radius: 8px; border: 1px solid #e9ecef;">
                                    </div>
                                    <button type="button" class="btn btn-primary w-100"
                                        style="border-radius: 8px; font-weight: 500;" onclick="applyCustomDateRange()">
                                        <i class="fas fa-check me-2"></i>Apply Custom Range
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Date Presets -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100"
                                style="border-radius: 12px; background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3" style="font-weight: 600;">
                                        <i class="fas fa-clock me-2"></i>Quick Presets
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-success"
                                            style="border-radius: 8px; font-weight: 500;"
                                            onclick="setDateRangeModal('today')">
                                            <i class="fas fa-calendar-day me-2"></i>Today
                                        </button>
                                        <button type="button" class="btn btn-outline-info"
                                            style="border-radius: 8px; font-weight: 500;"
                                            onclick="setDateRangeModal('week')">
                                            <i class="fas fa-calendar-week me-2"></i>This Week
                                        </button>
                                        <button type="button" class="btn btn-outline-warning"
                                            style="border-radius: 8px; font-weight: 500;"
                                            onclick="setDateRangeModal('month')">
                                            <i class="fas fa-calendar me-2"></i>This Month
                                        </button>
                                        <button type="button" class="btn btn-outline-primary"
                                            style="border-radius: 8px; font-weight: 500;"
                                            onclick="setDateRangeModal('year')">
                                            <i class="fas fa-calendar-alt me-2"></i>This Year
                                        </button>
                                        <hr class="my-3">
                                        <button type="button" class="btn btn-outline-secondary w-100"
                                            style="border-radius: 8px; font-weight: 500;" onclick="clearDateRangeModal()">
                                            <i class="fas fa-times me-2"></i>Clear Date Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Filter Status -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info mb-0"
                                style="border-left: 4px solid #17a2b8; border-radius: 8px;">
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
    <!-- UPDATED: Add Modal with Dynamic Livelihood Fields - CORRECTED -->
    <div class="modal fade" id="addRsbsaModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i class="fas fa-user-plus me-2"></i>Add New RSBSA Registration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addRsbsaForm" enctype="multipart/form-data">
                        <!-- Personal Information -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="rsbsa_first_name" class="form-label fw-semibold">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="rsbsa_first_name"
                                            name="first_name" required maxlength="100" placeholder="First name"
                                            onblur="capitalizeRsbsaName(this)">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="rsbsa_middle_name" class="form-label fw-semibold">
                                            Middle Name
                                        </label>
                                        <input type="text" class="form-control" id="rsbsa_middle_name"
                                            name="middle_name" maxlength="100" placeholder="Middle name (optional)"
                                            onblur="capitalizeRsbsaName(this)">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="rsbsa_last_name" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="rsbsa_last_name" name="last_name"
                                            required maxlength="100" placeholder="Last name"
                                            onblur="capitalizeRsbsaName(this)">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="rsbsa_name_extension" class="form-label fw-semibold">
                                            Extension
                                        </label>
                                        <select class="form-select" id="rsbsa_name_extension" name="name_extension">
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
                                        <label for="rsbsa_sex" class="form-label fw-semibold">
                                            Sex <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="rsbsa_sex" name="sex" required>
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Preferred not to say">Preferred not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="rsbsa_contact_number" class="form-label fw-semibold">
                                            Contact Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="rsbsa_contact_number"
                                            name="contact_number" required placeholder="09XXXXXXXXX"
                                            pattern="^(\+639|09)\d{9}$" maxlength="20"
                                            oninput="formatRsbsaContactNumber(this)">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>09XXXXXXXXX
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-map-marker-alt me-2"></i>Location Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rsbsa_barangay" class="form-label fw-semibold">
                                            Barangay <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="rsbsa_barangay" name="barangay" required>
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
                                        <label for="rsbsa_address" class="form-label fw-semibold">
                                            Address <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="rsbsa_address" name="address" rows="2" maxlength="500" required
                                            placeholder="Complete residential address"></textarea>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>Maximum 500 characters
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Livelihood Information Card - WITH TOGGLEABLE FIELDS -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-seedling me-2"></i>Livelihood Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="rsbsa_main_livelihood" class="form-label fw-semibold">
                                            Main Livelihood <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="rsbsa_main_livelihood" name="main_livelihood"
                                            required onchange="toggleAddRsbsaLivelihoodFields(this)">
                                            <option value="">Select Livelihood</option>
                                            <option value="Farmer">Farmer</option>
                                            <option value="Farmworker/Laborer">Farmworker/Laborer</option>
                                            <option value="Fisherfolk">Fisherfolk</option>
                                            <option value="Agri-youth">Agri-youth</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- FARMER FIELDS -->
                                <div id="farmer-fields" style="display: none;">
                                    <div class="alert alert-info border-0 mb-3">
                                        <i class="fas fa-leaf me-2"></i><strong>Farmer Information</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_farmer_crops" class="form-label fw-semibold">
                                                Main Crops <span class="text-danger" id="farmer_crops_req"
                                                    style="display:none;">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="rsbsa_farmer_crops"
                                                name="farmer_crops" maxlength="100"
                                                placeholder="e.g., Rice, Corn, Vegetables">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_farmer_land_area" class="form-label fw-semibold">
                                                Land Area (hectares) <span id="farmer_area_req"
                                                    style="display:none;"></span>
                                            </label>
                                            <input type="number" class="form-control" id="rsbsa_farmer_land_area"
                                                name="farmer_land_area" step="0.01" min="0" max="1000"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_farmer_type_of_farm" class="form-label fw-semibold">
                                                Type of Farm <span class="text-danger" id="farmer_type_req"
                                                    style="display:none;">*</span>
                                            </label>
                                            <select class="form-select" id="rsbsa_farmer_type_of_farm"
                                                name="farmer_type_of_farm">
                                                <option value="">Select Type</option>
                                                <option value="Irrigated">Irrigated</option>
                                                <option value="Rainfed Upland">Rainfed Upland</option>
                                                <option value="Rainfed Lowland">Rainfed Lowland</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_farmer_land_ownership" class="form-label fw-semibold">
                                                Land Ownership <span class="text-danger" id="farmer_ownership_req"
                                                    style="display:none;">*</span>
                                            </label>
                                            <select class="form-select" id="rsbsa_farmer_land_ownership"
                                                name="farmer_land_ownership">
                                                <option value="">Select Ownership</option>
                                                <option value="Owner">Owner</option>
                                                <option value="Tenant">Tenant</option>
                                                <option value="Lessee">Lessee</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_farm_location" class="form-label fw-semibold">
                                                Farm Location <span class="text-danger" id="farmer_location_req"
                                                    style="display:none;">*</span>
                                            </label>
                                            <textarea class="form-control" id="rsbsa_farm_location" name="farm_location" rows="2" maxlength="500"
                                                placeholder="Specific location of farm"></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_farmer_special_status" class="form-label fw-semibold">
                                                Special Status
                                            </label>
                                            <select class="form-select" id="rsbsa_farmer_special_status"
                                                name="farmer_special_status">
                                                <option value="">Select Status</option>
                                                <option value="Ancestral Domain">Ancestral Domain</option>
                                                <option value="Agrarian Reform Beneficiary">Agrarian Reform Beneficiary
                                                </option>
                                                <option value="None">None</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- FARMWORKER FIELDS -->
                                <div id="farmworker-fields" style="display: none;">
                                    <div class="alert alert-info border-0 mb-3">
                                        <i class="fas fa-hammer me-2"></i><strong>Farmworker/Laborer Information</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_farmworker_type" class="form-label fw-semibold">
                                                Type of Work <span class="text-danger" id="farmworker_type_req"
                                                    style="display:none;">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="rsbsa_farmworker_type"
                                                name="farmworker_type"
                                                placeholder="e.g., Farm Laborer, Harvester, Planter">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_farmworker_commodity" class="form-label fw-semibold">
                                                Commodity/Crop You Work With
                                            </label>
                                            <input type="text" class="form-control" id="rsbsa_farmworker_commodity"
                                                name="commodity" maxlength="1000"
                                                placeholder="e.g., Rice, Corn, Vegetables">
                                        </div>
                                    </div>
                                </div>

                                <!-- FISHERFOLK FIELDS -->
                                <div id="fisherfolk-fields" style="display: none;">
                                    <div class="alert alert-info border-0 mb-3">
                                        <i class="fas fa-fish me-2"></i><strong>Fisherfolk Information</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_fisherfolk_activity" class="form-label fw-semibold">
                                                Fishing Activity <span class="text-danger" id="fisherfolk_activity_req"
                                                    style="display:none;">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="rsbsa_fisherfolk_activity"
                                                name="fisherfolk_activity"
                                                placeholder="e.g., Bangus Aquaculture, Tilapia Pond Farming">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_fisherfolk_commodity" class="form-label fw-semibold">
                                                Main Fish/Aquatic Product
                                            </label>
                                            <input type="text" class="form-control" id="rsbsa_fisherfolk_commodity"
                                                name="commodity" maxlength="1000"
                                                placeholder="e.g., Bangus, Tilapia, Mud Crab, Seaweed">
                                        </div>
                                    </div>
                                </div>

                                <!-- AGRI-YOUTH FIELDS -->
                                <div id="agriyouth-fields" style="display: none;">
                                    <div class="alert alert-info border-0 mb-3">
                                        <i class="fas fa-user-tie me-2"></i><strong>Agri-Youth Information</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_agriyouth_household" class="form-label fw-semibold">
                                                From Farming Household? <span class="text-danger"
                                                    id="agriyouth_household_req" style="display:none;">*</span>
                                            </label>
                                            <select class="form-select" id="rsbsa_agriyouth_household"
                                                name="agriyouth_farming_household">
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_agriyouth_training" class="form-label fw-semibold">
                                                Agricultural Training <span class="text-danger"
                                                    id="agriyouth_training_req" style="display:none;">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="rsbsa_agriyouth_training"
                                                name="agriyouth_training"
                                                placeholder="e.g., Crop production, Livestock raising">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_agriyouth_participation" class="form-label fw-semibold">
                                                Program Participation <span class="text-danger"
                                                    id="agriyouth_participation_req" style="display:none;">*</span>
                                            </label>
                                            <select class="form-select" id="rsbsa_agriyouth_participation"
                                                name="agriyouth_participation">
                                                <option value="">Select Participation</option>
                                                <option value="Participated">Participated</option>
                                                <option value="Not Participated">Not Participated</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rsbsa_agriyouth_commodity" class="form-label fw-semibold">
                                                Main Agricultural Focus
                                            </label>
                                            <input type="text" class="form-control" id="rsbsa_agriyouth_commodity"
                                                name="commodity" maxlength="1000"
                                                placeholder="e.g., Organic Farming, Livestock, Aquaculture">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Document -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-file-upload me-2"></i>Supporting Document (Barangay Certificate)
                                    <span class="text-danger">*</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-4">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Upload a Barangay Certificate. Supported formats: JPG, PNG, PDF (Max 10MB each)
                                </p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rsbsa_supporting_document" class="form-label fw-semibold">
                                            Upload Document
                                        </label>
                                        <input type="file" class="form-control" id="rsbsa_supporting_document"
                                            name="supporting_document" accept="image/*,.pdf"
                                            onchange="previewRsbsaDocument('rsbsa_supporting_document', 'rsbsa_doc_preview')">
                                    </div>
                                    <div class="col-md-6">
                                        <div id="rsbsa_doc_preview" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Application Status -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-toggle-on me-2"></i>Application Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rsbsa_status" class="form-label fw-semibold">
                                            Initial Status <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="rsbsa_status" name="status" required>
                                            <option value="pending" selected>Pending</option>
                                            <option value="under_review">Under Review</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Choose the initial verification status
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Remarks -->
                        <div class="card border-0 bg-light mt-3">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-comment me-2"></i>Admin Remarks
                                </h6>
                            </div>
                            <div class="card-body">
                                <label for="rsbsa_remarks" class="form-label fw-semibold">
                                    Remarks (Optional)
                                </label>
                                <textarea class="form-control" id="rsbsa_remarks" name="remarks" rows="4"
                                    placeholder="Add any comments or notes about this registration..." maxlength="1000"
                                    oninput="updateRsbsaRemarksCounter()"></textarea>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Provide context for this registration
                                    </small>
                                    <small class="text-muted" id="rsbsaRemarksCounter">
                                        <span id="rsbsaCharCount">0</span>/1000
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
                    <button type="button" class="btn btn-primary" onclick="submitAddRsbsa()">
                        <span class="btn-text">
                            <i class="fas fa-save me-1"></i>Create Registration
                        </span>
                        <span class="btn-loader" style="display: none;">
                            <span class="spinner-border spinner-border-sm me-2"></span>Creating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Add this at the top of your scripts section
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Ensure search button works on page load
        $(document).ready(function() {
            // Additional event listener for search button as backup
            $('#searchButton').on('click', function(e) {
                e.preventDefault();
                console.log('Search button clicked'); // Debug log
                performSearch();
            });
        });

        let searchTimeout;

        // Auto search functionality
        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500); // Wait 500ms after user stops typing
        }

        // Perform search when search button is clicked
        function performSearch() {
            try {
                clearTimeout(searchTimeout);
                const form = document.getElementById('filterForm');
                if (form) {
                    form.submit();
                } else {
                    console.error('Filter form not found');
                }
            } catch (error) {
                console.error('Error in performSearch:', error);
            }
        }

        // Submit filter form when dropdowns change
        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        // Date range functions for modal
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
            const fromDate = document.getElementById('modal_date_from').value;
            const toDate = document.getElementById('modal_date_to').value;

            if (fromDate && toDate && fromDate > toDate) {
                showToast('warning', 'From date cannot be later than To date');
                return;
            }

            applyDateFilter(fromDate, toDate);
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

        function clearDateRange() {
            clearDateRangeModal();
        }

        // Date picker modal functions
        function openDatePicker() {
            const modal = document.getElementById('dateFilterModal');
            if (!modal) return;

            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');
            const modalDateFrom = document.getElementById('modal_date_from');
            const modalDateTo = document.getElementById('modal_date_to');

            // Set current values in modal
            if (dateFrom && modalDateFrom) {
                modalDateFrom.value = dateFrom.value;
            }
            if (dateTo && modalDateTo) {
                modalDateTo.value = dateTo.value;
            }

            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }

        function closeDatePicker() {
            const modal = document.getElementById('dateFilterModal');
            if (!modal) return;

            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
        }

        function setModalDateRange(period) {
            const today = new Date();
            let startDate, endDate;

            // Remove active class from all buttons
            document.querySelectorAll('.quick-date-btn').forEach(btn => btn.classList.remove('active'));

            switch (period) {
                case 'today':
                    startDate = endDate = today;
                    break;
                case 'yesterday':
                    startDate = endDate = new Date(today);
                    startDate.setDate(today.getDate() - 1);
                    endDate = startDate;
                    break;
                case 'week':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - today.getDay());
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6);
                    break;
                case 'lastWeek':
                    endDate = new Date(today);
                    endDate.setDate(today.getDate() - today.getDay() - 1);
                    startDate = new Date(endDate);
                    startDate.setDate(endDate.getDate() - 6);
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case 'lastMonth':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    endDate = new Date(today.getFullYear(), 11, 31);
                    break;
            }

            if (startDate && endDate) {
                const modalDateFrom = document.getElementById('modal_date_from');
                const modalDateTo = document.getElementById('modal_date_to');

                if (modalDateFrom) {
                    modalDateFrom.value = startDate.toISOString().split('T')[0];
                }
                if (modalDateTo) {
                    modalDateTo.value = endDate.toISOString().split('T')[0];
                }

                // Add active class to clicked button
                event.target.classList.add('active');
            }
        }

        function clearModalDates() {
            const modalDateFrom = document.getElementById('modal_date_from');
            const modalDateTo = document.getElementById('modal_date_to');

            if (modalDateFrom) modalDateFrom.value = '';
            if (modalDateTo) modalDateTo.value = '';

            document.querySelectorAll('.quick-date-btn').forEach(btn => btn.classList.remove('active'));
        }

        function applyDateRange() {
            const modalDateFrom = document.getElementById('modal_date_from');
            const modalDateTo = document.getElementById('modal_date_to');

            if (!modalDateFrom || !modalDateTo) return;

            const startDate = modalDateFrom.value;
            const endDate = modalDateTo.value;

            // Validate date range
            if (startDate && endDate && startDate > endDate) {
                agrisysModal.warning('Start date cannot be after end date', {
                    title: 'Invalid Date Range'
                });
                return;
            }

            // Update hidden inputs
            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');

            if (dateFrom) dateFrom.value = startDate;
            if (dateTo) dateTo.value = endDate;

            // Update display
            updateDateFilterStatus(startDate, endDate);

            // Close modal
            closeDatePicker();

            // Submit form
            submitFilterForm();
        }

        function updateDateRangeDisplay(startDate, endDate) {
            // This function is kept for compatibility but uses updateDateFilterStatus
            updateDateFilterStatus(startDate, endDate);
        }

        // Initialize date picker
        document.addEventListener('DOMContentLoaded', function() {
            // Add Enter key listeners to date inputs
            const modalDateFrom = document.getElementById('modal_date_from');
            if (modalDateFrom) {
                modalDateFrom.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyCustomDateRange();
                    }
                });
            }

            const modalDateTo = document.getElementById('modal_date_to');
            if (modalDateTo) {
                modalDateTo.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyCustomDateRange();
                    }
                });
            }

            // Initialize display with current values (use correct IDs)
            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');
            if (dateFromInput && dateToInput) {
                const dateFrom = dateFromInput.value;
                const dateTo = dateToInput.value;
                if (dateFrom || dateTo) {
                    updateDateFilterStatus(dateFrom, dateTo);
                }
            }
        });

        // Helper function to get status display text with null safety
        function getStatusText(status) {
            if (!status || status === null || status === undefined) {
                return 'Unknown';
            }

            // Convert to string first to ensure we have a string
            const statusStr = String(status).toLowerCase();

            switch (statusStr) {
                case 'pending':
                    return 'Pending';
                case 'under_review':
                    return 'Under Review';
                case 'approved':
                    return 'Approved';
                case 'rejected':
                    return 'Rejected';
                default:
                    return statusStr.charAt(0).toUpperCase() + statusStr.slice(1);
            }
        }

        // Show update modal function with enhanced error handling
        function showUpdateModal(id, currentStatus) {
            // Validate parameters
            if (!id) {
                agrisysModal.error('Invalid application ID', {
                    title: 'Error'
                });
                return;
            }

            // Show loading state in modal
            document.getElementById('updateAppNumber').innerHTML = `
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>`;

            // First fetch the application details
            fetch(`/admin/rsbsa-applications/${id}`)
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

                    const data = response.data;

                    // Validate data object
                    if (!data) {
                        throw new Error('No application data received');
                    }

                    // Populate the hidden field
                    document.getElementById('updateApplicationId').value = id;

                    // Populate application info display with null checks
                    document.getElementById('updateAppNumber').textContent = data.application_number || 'N/A';
                    document.getElementById('updateAppName').textContent = data.full_name || 'N/A';
                    document.getElementById('updateAppBarangay').textContent = data.barangay || 'N/A';
                    document.getElementById('updateAppLivelihood').textContent = data.main_livelihood || 'N/A';

                    // Show current status with badge styling and null safety
                    const currentStatusElement = document.getElementById('updateAppCurrentStatus');
                    const statusColor = data.status_color || 'secondary';
                    const formattedStatus = data.formatted_status || getStatusText(data.status);

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
                    agrisysModal.error('Error loading application details: ' + error.message, {
                        title: 'Loading Error'
                    });
                });
        }

        // OPTIMIZED UPDATE STATUS FUNCTION WITH CONFIRMATION TOAST
        let isUpdating = false;

        function updateRemarksCounter() {
            const textarea = document.getElementById('remarks');
            const charCount = document.getElementById('charCount');

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

        function updateApplicationStatus() {
            if (isUpdating) return;

            const id = document.getElementById('updateApplicationId').value;
            const newStatus = document.getElementById('newStatus').value;
            const remarks = document.getElementById('remarks').value;

            // Quick validation
            if (!id || !newStatus) {
                showToast('error', 'Missing required information');
                return;
            }

            // Get original values
            const originalStatus = document.getElementById('newStatus').dataset.originalStatus || '';
            const originalRemarks = document.getElementById('remarks').dataset.originalRemarks || '';

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
                const originalStatusText = getStatusText(originalStatus);
                const newStatusText = getStatusText(newStatus);
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

            // Show confirmation toast instead of browser confirm
            showConfirmationToast(
                'Confirm Update',
                `Update this RSBSA registration with the following changes?\n\n${changesSummary.join('\n')}`,
                () => proceedWithStatusUpdate(id, newStatus, remarks)
            );
        }

        function proceedWithStatusUpdate(id, newStatus, remarks) {
            isUpdating = true;

            const updateButton = document.querySelector('#updateModal .btn-primary');
            const originalText = updateButton.innerHTML;
            updateButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Updating...';
            updateButton.disabled = true;

            fetch(`/admin/rsbsa-applications/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        remarks: remarks
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const updateModalInstance = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                        if (updateModalInstance) updateModalInstance.hide();
                        showToast('success', data.message || 'Status updated successfully');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Update failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', error.message || 'Update failed');
                    updateButton.innerHTML = originalText;
                    updateButton.disabled = false;
                })
                .finally(() => isUpdating = false);
        }


        /**
         * COMPLETE: View application with all fields - Updated with all livelihood-specific info
         */
        function viewApplication(id) {
            if (!id) {
                showToast('error', 'Invalid application ID');
                return;
            }

            // Show modal first
            const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
            modal.show();

            // Then show loading state after modal is shown
            setTimeout(() => {
                document.getElementById('applicationDetails').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>`;
            }, 100);

            // Fetch application details
            fetch(`/admin/rsbsa-applications/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    console.log('Response:', response);

                    if (!response.success) {
                        throw new Error(response.message || 'Failed to load application details');
                    }

                    const data = response.data;

                    if (!data) {
                        throw new Error('No application data received');
                    }

                    // Format timestamps
                    const createdAt = new Date(data.created_at);
                    const updatedAt = new Date(data.updated_at);
                    const createdAtFormatted = createdAt.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                    const updatedAtFormatted = updatedAt.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });

                    // Status badge with color coding
                    const statusColor = data.status_color || 'secondary';
                    const formattedStatus = data.formatted_status || getStatusText(data.status);
                    const statusBadge = `<span class="badge bg-${statusColor}">${formattedStatus}</span>`;

                    // Build remarks HTML if exists
                    const remarksHtml = data.remarks ? `
            <div class="col-12 mt-4">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Admin Remarks</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${data.remarks}</p>
                    </div>
                </div>
            </div>` : '';

                    // Build document section HTML
                    const documentHtml = data.supporting_document_path ? `
            <div class="text-center p-4">
                <i class="fas fa-file fa-4x text-success mb-3"></i>
                <p class="text-muted mb-3">Document Available</p>
                <button class="btn btn-primary" onclick="viewDocument('${data.supporting_document_path}', 'Supporting Document')">
                    <i class="fas fa-eye me-2"></i>View Document
                </button>
            </div>` : `
            <div class="text-center p-4">
                <i class="fas fa-file-slash fa-4x text-muted mb-3"></i>
                <p class="text-muted">No Document Uploaded</p>
            </div>`;

                    // Build timeline additional info if available
                    let timelineHtml = '';
                    if (data.reviewed_at) {
                        const reviewedAt = new Date(data.reviewed_at);
                        timelineHtml += `<div class="col-12"><strong>Reviewed At:</strong> ${reviewedAt.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                })}</div>`;
                    }

                    if (data.number_assigned_at) {
                        const assignedAt = new Date(data.number_assigned_at);
                        timelineHtml += `<div class="col-12"><strong>Number Assigned:</strong> ${assignedAt.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                })}</div>`;
                    }

                    // Build Farmer-specific section
                    const farmerHtml = data.main_livelihood === 'Farmer' ? `
            <div class="col-md-6">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-leaf me-2"></i>Farmer Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12"><strong>Main Crops:</strong> ${data.farmer_crops || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Other Crops:</strong> ${data.farmer_other_crops || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Livestock:</strong> ${data.farmer_livestock || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Land Area:</strong> ${data.farmer_land_area ? `${data.farmer_land_area} hectares` : '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Type of Farm:</strong> ${data.farmer_type_of_farm || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Land Ownership:</strong> ${data.farmer_land_ownership || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Special Status:</strong> ${data.farmer_special_status || '<span class="text-muted">Not provided</span>'}</div>
                        </div>
                    </div>
                </div>
            </div>` : '';

                    // Build Farmworker-specific section
                    const farmworkerHtml = data.main_livelihood === 'Farmworker/Laborer' ? `
            <div class="col-md-6">
                <div class="card h-100 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-hammer me-2"></i>Farmworker Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12"><strong>Type of Work:</strong> ${data.farmworker_type || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Other Work Type:</strong> ${data.farmworker_other_type || '<span class="text-muted">Not provided</span>'}</div>
                        </div>
                    </div>
                </div>
            </div>` : '';

                    // Build Fisherfolk-specific section
                    const fisherfolkHtml = data.main_livelihood === 'Fisherfolk' ? `
            <div class="col-md-6">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-fish me-2"></i>Fisherfolk Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12"><strong>Fishing Activity:</strong> ${data.fisherfolk_activity || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Other Activity:</strong> ${data.fisherfolk_other_activity || '<span class="text-muted">Not provided</span>'}</div>
                        </div>
                    </div>
                </div>
            </div>` : '';

                    // Build Agri-Youth-specific section
                    const agriyouthHtml = data.main_livelihood === 'Agri-youth' ? `
            <div class="col-md-6">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Agri-Youth Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12"><strong>From Farming Household:</strong> ${data.agriyouth_farming_household || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Agricultural Training:</strong> ${data.agriyouth_training || '<span class="text-muted">Not provided</span>'}</div>
                            <div class="col-12"><strong>Program Participation:</strong> ${data.agriyouth_participation || '<span class="text-muted">Not provided</span>'}</div>
                        </div>
                    </div>
                </div>
            </div>` : '';

                    // Render the complete card-based layout
                    document.getElementById('applicationDetails').innerHTML = `
            <div class="row g-4">
                <!-- Personal Information Card -->
                <div class="col-md-6">
                    <div class="card h-100 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-12"><strong>Application #:</strong> <span class="text-primary">${data.application_number || 'N/A'}</span></div>
                                <div class="col-12"><strong>Full Name:</strong> ${data.full_name || '<span class="text-muted">Not provided</span>'}</div>
                                <div class="col-12"><strong>Sex:</strong> ${data.sex || '<span class="text-muted">Not specified</span>'}</div>
                                <div class="col-12"><strong>Contact Number:</strong> ${data.contact_number ? `<a href="tel:${data.contact_number}" class="text-decoration-none">${data.contact_number}</a>` : '<span class="text-muted">Not provided</span>'}</div>
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
                                <div class="col-12"><strong>Barangay:</strong> ${data.barangay || '<span class="text-muted">Not provided</span>'}</div>
                                <div class="col-12"><strong>Address:</strong> ${data.address || '<span class="text-muted">Not provided</span>'}</div>
                                ${data.main_livelihood === 'Farmer' ? `<div class="col-12"><strong>Farm Location:</strong> ${data.farm_location || '<span class="text-muted">Not provided</span>'}</div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Livelihood Card -->
                <div class="col-md-6">
                    <div class="card h-100 border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-seedling me-2"></i>Main Livelihood</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-12"><strong>Livelihood Type:</strong> ${data.main_livelihood || '<span class="text-muted">Not provided</span>'}</div>
                                <div class="col-12"><strong>Commodity/Product:</strong> ${data.commodity || '<span class="text-muted">Not provided</span>'}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Status Card -->
                <div class="col-md-6">
                    <div class="card h-100 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-toggle-on me-2"></i>Application Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-12"><strong>Current Status:</strong> ${statusBadge}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Livelihood-Specific Cards (Conditionally displayed) -->
                ${farmerHtml}
                ${farmworkerHtml}
                ${fisherfolkHtml}
                ${agriyouthHtml}

                <!-- Application Timeline Card -->
                <div class="col-md-12">
                    <div class="card h-100 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Application Timeline</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-12"><strong>Date Applied:</strong> ${createdAtFormatted}</div>
                                <div class="col-12"><strong>Last Updated:</strong> ${updatedAtFormatted}</div>
                                ${data.reviewed_at ? `<div class="col-12"><strong>Date Reviewed:</strong> ${data.reviewed_at}</div>` : ''}
                                ${data.reviewer_name ? `<div class="col-12"><strong>Reviewed By:</strong> ${data.reviewer_name}</div>` : ''}
                                ${timelineHtml}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supporting Document Card -->
                <div class="col-12">
                    <div class="card border-secondary">
                        <div class="card-header bg-primary text-center text-white">
                            <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i>Supporting Document</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    ${documentHtml}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                ${remarksHtml}
            </div>`;

                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('applicationDetails').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                ${error.message || 'Error loading application details. Please try again.'}
            </div>`;
                });
        }

        // Helper function to toggle image zoom (reuse existing if available)
        function toggleImageZoom(img) {
            if (img.style.transform === 'scale(2)') {
                img.style.transform = 'scale(1)';
                img.style.cursor = 'zoom-in';
            } else {
                img.style.transform = 'scale(2)';
                img.style.cursor = 'zoom-out';
            }
        }


        // FIXED: Unified document viewing function - use this for ALL document viewing
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
                modalTitle.innerHTML = `<i></i>${filename}`;
            } else {
                modalTitle.innerHTML = `<i></i>Supporting Document`;
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

        // REMOVED BROKEN DUPLICATE CODE HERE - See helper functions below


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


        // Function to check for changes and provide visual feedback
        function checkForChanges() {
            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');

            if (!statusSelect || !remarksTextarea) return;
            if (!statusSelect.dataset.originalStatus) return;

            const originalStatus = statusSelect.dataset.originalStatus || '';
            const originalRemarks = remarksTextarea.dataset.originalRemarks || '';

            const statusChanged = statusSelect.value !== originalStatus;
            const remarksChanged = remarksTextarea.value.trim() !== originalRemarks.trim();

            // Visual feedback for status field
            statusSelect.classList.toggle('form-changed', statusChanged);
            statusSelect.parentElement.classList.toggle('changed', statusChanged);

            // Visual feedback for remarks field
            remarksTextarea.classList.toggle('form-changed', remarksChanged);
            remarksTextarea.parentElement.classList.toggle('changed', remarksChanged);

            // Update button state
            const updateButton = document.querySelector('#updateModal .btn-primary');
            if (updateButton) {
                updateButton.classList.toggle('no-changes', !statusChanged && !remarksChanged);

                // Update button text based on changes
                if (!statusChanged && !remarksChanged) {
                    updateButton.innerHTML = '<i class="fas fa-edit me-1"></i>No Changes';
                } else {
                    updateButton.innerHTML = '<i class="fas fa-save me-1"></i>Update Status';
                }
            }
        }

        // Add event listeners when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');

            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    if (typeof checkForChanges === 'function') {
                        checkForChanges();
                    }
                });
            }

            if (remarksTextarea) {
                remarksTextarea.addEventListener('input', function() {
                    if (typeof checkForChanges === 'function') {
                        checkForChanges();
                    }
                });
            }

            // Add keyboard shortcuts for document modal
            document.addEventListener('keydown', function(e) {
                const documentModal = document.getElementById('documentModal');
                const isModalOpen = documentModal && documentModal.classList.contains('show');

                if (isModalOpen) {
                    if (e.key === 'Escape') {
                        // Allow default ESC behavior to close modal
                        return;
                    } else if (e.key === 'F11') {
                        // Toggle fullscreen mode
                        e.preventDefault();
                        toggleFullscreen();
                    } else if (e.ctrlKey && e.key === 's') {
                        // Ctrl+S to download document
                        e.preventDefault();
                        const downloadLink = documentModal.querySelector('a[download]');
                        if (downloadLink) {
                            downloadLink.click();
                        }
                    } else if (e.ctrlKey && e.key === 'o') {
                        // Ctrl+O to open in new tab
                        e.preventDefault();
                        const openLink = documentModal.querySelector('a[target="_blank"]');
                        if (openLink) {
                            openLink.click();
                        }
                    }
                }
            });
        });

        // Helper function to toggle fullscreen mode
        function toggleFullscreen() {
            const documentModal = document.getElementById('documentModal');
            const modalDialog = documentModal.querySelector('.modal-dialog');

            if (modalDialog.classList.contains('modal-xl')) {
                modalDialog.classList.remove('modal-xl');
                modalDialog.classList.add('modal-fullscreen');
                document.querySelector('#documentModal .modal-title').innerHTML =
                    '<i class="fas fa-compress me-2"></i>Document Viewer (Press F11 to exit fullscreen)';
            } else {
                modalDialog.classList.remove('modal-fullscreen');
                modalDialog.classList.add('modal-xl');
                document.querySelector('#documentModal .modal-title').innerHTML =
                    '<i class="fas fa-file-alt me-2"></i>Supporting Document';
            }
        }

        // Function to handle document loading errors globally
        function handleDocumentError(element, fallbackUrl) {
            element.style.display = 'none';
            const container = element.parentElement;
            if (container) {
                container.innerHTML = `
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                        <h6>Document Loading Failed</h6>
                        <p class="small mb-3">Unable to display this document.</p>
                        <a href="${fallbackUrl}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Try Opening Directly
                        </a>
                    </div>`;
            }
        }

        // delete modal
        // Global variable to track current delete ID
        let currentDeleteRsbsaId = null;

        // Updated deleteApplication function to use modal
        function deleteApplication(id) {
            try {
                // Get application details from the table row
                const row = document.querySelector(`tr[data-id="${id}"]`);
                const appNumber = row ? row.querySelector('.text-primary').textContent : 'this application';

                // Set the global variable
                currentDeleteRsbsaId = id;

                // Update modal with application number
                document.getElementById('delete_rsbsa_name').textContent = appNumber;

                // Show the delete modal
                new bootstrap.Modal(document.getElementById('deleteRsbsaModal')).show();
            } catch (error) {
                console.error('Error preparing delete dialog:', error);
                showToast('error', 'Failed to prepare delete dialog');
            }
        }

        // Confirm permanent delete
        async function confirmPermanentDeleteRsbsa() {
            if (!currentDeleteRsbsaId) {
                showToast('error', 'Application ID not found');
                return;
            }

            try {
                // Show loading state
                const deleteBtn = document.getElementById('confirm_delete_rsbsa_btn');
                deleteBtn.querySelector('.btn-text').style.display = 'none';
                deleteBtn.querySelector('.btn-loader').style.display = 'inline';
                deleteBtn.disabled = true;

                const response = await fetch(`/admin/rsbsa-applications/${currentDeleteRsbsaId}`, {
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
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteRsbsaModal'));
                if (deleteModal) {
                    deleteModal.hide();
                }

                // Show success message
                showToast('success', data.message || 'Application deleted successfully');

                // Remove the row with animation
                const row = document.querySelector(`tr[data-id="${currentDeleteRsbsaId}"]`);
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
                currentDeleteRsbsaId = null;

            } catch (error) {
                console.error('Error deleting application:', error);

                // Close modal first
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteRsbsaModal'));
                if (deleteModal) {
                    deleteModal.hide();
                }

                // Show error
                showToast('error', 'Error deleting application: ' + error.message);

            } finally {
                // Reset button state
                const deleteBtn = document.getElementById('confirm_delete_rsbsa_btn');
                deleteBtn.querySelector('.btn-text').style.display = 'inline';
                deleteBtn.querySelector('.btn-loader').style.display = 'none';
                deleteBtn.disabled = false;
            }
        }

        // Clean up modal on close
        document.addEventListener('DOMContentLoaded', function() {
            const deleteRsbsaModal = document.getElementById('deleteRsbsaModal');
            if (deleteRsbsaModal) {
                deleteRsbsaModal.addEventListener('hidden.bs.modal', function() {
                    // Reset button state
                    const deleteBtn = document.getElementById('confirm_delete_rsbsa_btn');
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
                    currentDeleteRsbsaId = null;

                    console.log('Delete RSBSA modal cleaned up');
                });
            }
        });

        // Proceed with actual delete
        function proceedWithApplicationDelete(id) {
            fetch(`/admin/rsbsa-applications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message || 'Application deleted successfully');

                        // Remove the row from table with fade animation
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                // Reload page to refresh statistics
                                window.location.reload();
                            }, 300);
                        } else {
                            // If row not found, just reload
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } else {
                        showToast('error', data.message || 'Failed to delete application');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred while deleting the application: ' + error.message);
                });
        }

        // Create toast container if it doesn't exist (add this if not already present)
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

        // // Get CSRF token utility function
        // function getCSRFToken() {
        //     const metaTag = document.querySelector('meta[name="csrf-token"]');
        //     return metaTag ? metaTag.getAttribute('content') : '';
        // }

        // Helper function to get status display text with null safety
        function getStatusText(status) {
            if (!status || status === null || status === undefined) {
                return 'Unknown';
            }

            const statusStr = String(status).toLowerCase();

            switch (statusStr) {
                case 'pending':
                    return 'Pending';
                case 'under_review':
                    return 'Under Review';
                case 'approved':
                    return 'Approved';
                case 'rejected':
                    return 'Rejected';
                default:
                    return statusStr.charAt(0).toUpperCase() + statusStr.slice(1);
            }
        }

        // Show add RSBSA modal
        function showAddRsbsaModal() {
            const modal = new bootstrap.Modal(document.getElementById('addRsbsaModal'));

            // Reset form
            document.getElementById('addRsbsaForm').reset();

            // Remove any validation errors
            document.querySelectorAll('#addRsbsaModal .is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('#addRsbsaModal .invalid-feedback').forEach(el => el.remove());

            // Clear document preview
            const preview = document.getElementById('rsbsa_doc_preview');
            if (preview) {
                preview.innerHTML = '';
                preview.style.display = 'none';
            }

            modal.show();
        }

        // Real-time validation for contact number
        const rsbsaContactInput = document.getElementById('rsbsa_contact_number');
        if (rsbsaContactInput) {
            rsbsaContactInput.addEventListener('input', function() {
                validateRsbsaContactNumber(this.value);
            });
        }

        function validateRsbsaContactNumber(contactNumber) {
            const input = document.getElementById('rsbsa_contact_number');
            if (!input) return;
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
        function capitalizeRsbsaName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        }

        const rsbsaFirstName = document.getElementById('rsbsa_first_name');
        if (rsbsaFirstName) {
            rsbsaFirstName.addEventListener('blur', function() {
                capitalizeRsbsaName(this);
            });
        }

        const rsbsaMiddleName = document.getElementById('rsbsa_middle_name');
        if (rsbsaMiddleName) {
            rsbsaMiddleName.addEventListener('blur', function() {
                capitalizeRsbsaName(this);
            });
        }

        const rsbsaLastName = document.getElementById('rsbsa_last_name');
        if (rsbsaLastName) {
            rsbsaLastName.addEventListener('blur', function() {
                capitalizeRsbsaName(this);
            });
        }

        // Document preview
        function previewRsbsaDocument(inputId, previewId) {
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

        // Format file size helper
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        /**
         * CORRECTED: Validate RSBSA form - checks required fields based on livelihood type
         */
        function validateRsbsaForm() {
            const form = document.getElementById('addRsbsaForm'); // FIX: Define form variable
            let isValid = true;

            // Clear previous validation states
            document.querySelectorAll('#addRsbsaForm .is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('#addRsbsaForm .invalid-feedback').forEach(el => el.remove());

            // Required fields present in all forms
            const requiredFields = [{
                    id: 'rsbsa_first_name',
                    label: 'First Name'
                },
                {
                    id: 'rsbsa_last_name',
                    label: 'Last Name'
                },
                {
                    id: 'rsbsa_sex',
                    label: 'Sex'
                },
                {
                    id: 'rsbsa_contact_number',
                    label: 'Contact Number'
                },
                {
                    id: 'rsbsa_barangay',
                    label: 'Barangay'
                },
                {
                    id: 'rsbsa_address',
                    label: 'Address'
                },
                {
                    id: 'rsbsa_main_livelihood',
                    label: 'Main Livelihood'
                },
                {
                    id: 'rsbsa_status',
                    label: 'Status'
                }
            ];

            // VALIDATE FILE UPLOAD - REQUIRED
            const fileField = form.querySelector('[name="supporting_document"]');
            if (!fileField || !fileField.files || fileField.files.length === 0) {
                isValid = false;
                fileField?.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Supporting document is required';
                fileField?.parentNode?.appendChild(errorDiv);
            } else if (fileField.files && fileField.files.length > 0) {
                const file = fileField.files[0];
                const maxSize = 10 * 1024 * 1024; // 10MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];

                if (file.size > maxSize) {
                    isValid = false;
                    fileField.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'File size must be less than 10MB';
                    fileField.parentNode.appendChild(errorDiv);
                }

                if (!allowedTypes.includes(file.type)) {
                    isValid = false;
                    fileField.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'File must be JPG, PNG, or PDF format';
                    fileField.parentNode.appendChild(errorDiv);
                }
            }

            // Validate common required fields
            requiredFields.forEach(field => {
                const input = document.getElementById(field.id);
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
            const contactInput = document.getElementById('rsbsa_contact_number');
            if (contactInput && contactInput.value.trim()) {
                const phoneRegex = /^(\+639|09)\d{9}$/;
                if (!phoneRegex.test(contactInput.value.trim())) {
                    contactInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
                    contactInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            }

            // Validate livelihood-specific required fields
            const livelihoodSelect = document.getElementById('rsbsa_main_livelihood');
            const livelihood = livelihoodSelect?.value;

            switch (livelihood) {
                case 'Farmer':
                    if (!validateLivelihoodRequiredFields(['rsbsa_farmer_crops', 'rsbsa_farmer_type_of_farm',
                            'rsbsa_farmer_land_ownership', 'rsbsa_farm_location'
                        ])) {
                        isValid = false;
                    }
                    break;

                case 'Farmworker/Laborer':
                    if (!validateLivelihoodRequiredFields(['rsbsa_farmworker_type'])) {
                        isValid = false;
                    }
                    break;

                case 'Fisherfolk':
                    if (!validateLivelihoodRequiredFields(['rsbsa_fisherfolk_activity'])) {
                        isValid = false;
                    }
                    break;

                case 'Agri-youth':
                    if (!validateLivelihoodRequiredFields(['rsbsa_agriyouth_household', 'rsbsa_agriyouth_training',
                            'rsbsa_agriyouth_participation'
                        ])) {
                        isValid = false;
                    }
                    break;
            }

            return isValid;
        }
        /**
         * Validate livelihood-specific required fields
         */
        function validateLivelihoodRequiredFields(fieldIds) {
            let allValid = true;

            fieldIds.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (input && (!input.value || input.value.trim() === '')) {
                    input.classList.add('is-invalid');
                    const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                    if (existingFeedback) existingFeedback.remove();

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = input.previousElementSibling?.textContent?.replace('*', '') +
                        ' is required';
                    input.parentNode.appendChild(errorDiv);
                    allValid = false;
                }
            });

            return allValid;
        }

        /**
         * CORRECTED: Submit add RSBSA form with proper field mapping
         */
        function submitAddRsbsa() {
            // Validate form BEFORE checking changes
            if (!validateRsbsaForm()) {
                showToast('error', 'Please fix all validation errors before submitting');
                return;
            }

            // Then proceed with existing logic
            const formData = new FormData();

            // Add personal information
            formData.append('first_name', document.getElementById('rsbsa_first_name').value.trim());
            formData.append('middle_name', document.getElementById('rsbsa_middle_name').value.trim());
            formData.append('last_name', document.getElementById('rsbsa_last_name').value.trim());
            formData.append('name_extension', document.getElementById('rsbsa_name_extension').value);
            formData.append('sex', document.getElementById('rsbsa_sex').value);
            formData.append('contact_number', document.getElementById('rsbsa_contact_number').value.trim());

            // Add location information
            formData.append('barangay', document.getElementById('rsbsa_barangay').value);
            formData.append('address', document.getElementById('rsbsa_address').value.trim());

            // Add livelihood information
            const livelihood = document.getElementById('rsbsa_main_livelihood').value;
            formData.append('main_livelihood', livelihood);

            // Add livelihood-specific fields based on selected type
            switch (livelihood) {
                case 'Farmer':
                    formData.append('farmer_crops', document.getElementById('rsbsa_farmer_crops').value.trim());
                    formData.append('farmer_type_of_farm', document.getElementById('rsbsa_farmer_type_of_farm').value);
                    formData.append('farmer_land_ownership', document.getElementById('rsbsa_farmer_land_ownership').value);
                    formData.append('farm_location', document.getElementById('rsbsa_farm_location').value.trim());
                    formData.append('farmer_land_area', document.getElementById('rsbsa_farmer_land_area').value);
                    formData.append('farmer_special_status', document.getElementById('rsbsa_farmer_special_status').value);
                    formData.append('commodity', document.getElementById('rsbsa_farmer_crops').value.trim());
                    break;

                case 'Farmworker/Laborer':
                    formData.append('farmworker_type', document.getElementById('rsbsa_farmworker_type').value.trim());
                    formData.append('commodity', document.getElementById('rsbsa_farmworker_commodity').value.trim());
                    break;

                case 'Fisherfolk':
                    formData.append('fisherfolk_activity', document.getElementById('rsbsa_fisherfolk_activity').value
                        .trim());
                    formData.append('commodity', document.getElementById('rsbsa_fisherfolk_commodity').value.trim());
                    break;

                case 'Agri-youth':
                    formData.append('agriyouth_farming_household', document.getElementById('rsbsa_agriyouth_household')
                        .value);
                    formData.append('agriyouth_training', document.getElementById('rsbsa_agriyouth_training').value.trim());
                    formData.append('agriyouth_participation', document.getElementById('rsbsa_agriyouth_participation')
                        .value);
                    formData.append('commodity', document.getElementById('rsbsa_agriyouth_commodity').value.trim());
                    break;
            }

            // Add status and remarks
            formData.append('status', document.getElementById('rsbsa_status').value);
            formData.append('remarks', document.getElementById('rsbsa_remarks').value.trim());

            // Add document if uploaded
            const docInput = document.getElementById('rsbsa_supporting_document');
            if (docInput.files && docInput.files[0]) {
                formData.append('supporting_document', docInput.files[0]);
            }

            // Find submit button
            const submitBtn = document.querySelector('#addRsbsaModal .btn-primary');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating...';
            submitBtn.disabled = true;

            // Submit to backend
            fetch('/admin/rsbsa-applications/create', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addRsbsaModal'));
                        if (modal) modal.hide();

                        showToast('success', data.message || 'RSBSA registration created successfully');

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        if (data.errors) {
                            displayAddRsbsaValidationErrors(data.errors);
                        }
                        showToast('error', data.message || 'Failed to create RSBSA registration');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred while creating the registration');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        /**
         * Display validation errors from server in Add modal
         */
        function displayAddRsbsaValidationErrors(errors) {
            // Clear previous errors
            document.querySelectorAll('#addRsbsaModal .is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('#addRsbsaModal .invalid-feedback').forEach(el => el.remove());

            let firstErrorField = null;

            // Field mapping
            const fieldMap = {
                'first_name': 'rsbsa_first_name',
                'middle_name': 'rsbsa_middle_name',
                'last_name': 'rsbsa_last_name',
                'name_extension': 'rsbsa_name_extension',
                'sex': 'rsbsa_sex',
                'contact_number': 'rsbsa_contact_number',
                'barangay': 'rsbsa_barangay',
                'address': 'rsbsa_address',
                'main_livelihood': 'rsbsa_main_livelihood',
                'farmer_crops': 'rsbsa_farmer_crops',
                'farmer_type_of_farm': 'rsbsa_farmer_type_of_farm',
                'farmer_land_ownership': 'rsbsa_farmer_land_ownership',
                'farm_location': 'rsbsa_farm_location',
                'farmworker_type': 'rsbsa_farmworker_type',
                'fisherfolk_activity': 'rsbsa_fisherfolk_activity',
                'agriyouth_farming_household': 'rsbsa_agriyouth_household',
                'agriyouth_training': 'rsbsa_agriyouth_training',
                'agriyouth_participation': 'rsbsa_agriyouth_participation'
            };

            // Display errors
            Object.keys(errors).forEach(field => {
                const elementId = fieldMap[field];
                const input = document.getElementById(elementId);

                if (input) {
                    input.classList.add('is-invalid');

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    const errorMessage = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                    errorDiv.textContent = errorMessage;
                    input.parentNode.appendChild(errorDiv);

                    if (!firstErrorField) {
                        firstErrorField = input;
                    }
                }
            });

            // Scroll to first error
            if (firstErrorField && firstErrorField.offsetParent !== null) {
                firstErrorField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstErrorField.focus();
            }
        }

        console.log('RSBSA Add Registration functionality loaded successfully');
        // Download file function for RSBSA-style buttons
        function downloadFile(url, filename) {
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        /**
         * Preview supporting document in edit modal - FIXED VERSION
         */
        function previewEditRsbsaDocument(inputId, previewId) {
            const fileInput = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            console.log('Preview function called:', inputId, previewId);
            console.log('File input:', fileInput);
            console.log('Preview container:', preview);

            if (!preview) {
                console.error('Preview container not found:', previewId);
                return;
            }

            // Clear previous preview
            preview.innerHTML = '';
            preview.style.display = 'none';

            // If no files selected, just clear the preview
            if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                console.log('No file selected');
                return;
            }

            const file = fileInput.files[0];
            console.log('File selected:', file.name, file.type, file.size);

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(file.type) && !['jpg', 'jpeg', 'png', 'pdf'].includes(fileExtension)) {
                preview.innerHTML = `
                    <div class="alert alert-danger mb-2">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Please select a JPG, PNG, or PDF file
                    </div>
                `;
                preview.style.display = 'block';
                fileInput.value = ''; // Clear the input
                return;
            }

            // Validate file size (10MB max)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                preview.innerHTML = `
                    <div class="alert alert-danger mb-2">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        File must be less than 10MB
                    </div>
                `;
                preview.style.display = 'block';
                fileInput.value = ''; // Clear the input
                return;
            }

            // Check if it's a PDF
            const isPdf = file.type === 'application/pdf' || fileExtension === 'pdf';

            if (isPdf) {
                // Show PDF info instead of preview
                preview.innerHTML = `
                    <div class="document-preview-item">
                        <div class="alert alert-info mb-2">
                            <i class="fas fa-file-pdf me-2"></i>
                            <strong>PDF Selected:</strong> ${file.name}
                            <br><small class="d-block mt-1">File size: ${(file.size / 1024).toFixed(2)} KB</small>
                        </div>
                    </div>
                `;
                preview.style.display = 'block';

                // Trigger change detection
                const form = document.getElementById('editRsbsaForm');
                if (form && form.dataset.applicationId) {
                    checkRsbsaFormChanges(form.dataset.applicationId);
                }

                return;
            }

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
                const form = document.getElementById('editRsbsaForm');
                if (form && form.dataset.applicationId) {
                    checkRsbsaFormChanges(form.dataset.applicationId);
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
                fileInput.value = ''; // Clear the input
            };

            reader.readAsDataURL(file);
        }

        /**
         * Display existing document preview in edit modal
         */
        function displayEditRsbsaExistingDocument(documentPath, previewContainerId) {
            const docPreviewContainer = document.getElementById(previewContainerId);

            if (!docPreviewContainer || !documentPath) return;

            const fileName = documentPath.split('/').pop();
            const fileExtension = fileName.split('.').pop().toLowerCase();
            const isPdf = fileExtension === 'pdf';
            const storageUrl = `/storage/${documentPath}`;

            if (isPdf) {
                const pdfInfo = document.createElement('div');
                pdfInfo.className = 'alert alert-info mb-2';
                pdfInfo.innerHTML = `
            <i class="fas fa-file-pdf me-2"></i>
            <strong>Current PDF:</strong> ${fileName}
        `;
                docPreviewContainer.appendChild(pdfInfo);
            } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
                const previewImg = document.createElement('img');
                previewImg.src = storageUrl;
                previewImg.style.maxWidth = '100%';
                previewImg.style.height = 'auto';
                previewImg.style.maxHeight = '300px';
                previewImg.style.borderRadius = '8px';
                previewImg.style.border = '1px solid #dee2e6';
                previewImg.style.marginBottom = '10px';
                previewImg.onerror = function() {
                    this.style.display = 'none';
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'alert alert-warning mb-2';
                    errorMsg.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>Unable to load image preview';
                    docPreviewContainer.appendChild(errorMsg);
                };
                docPreviewContainer.appendChild(previewImg);
            }

            const existingNote = document.createElement('small');
            existingNote.className = 'text-muted d-block';
            existingNote.innerHTML =
                '<i class="fas fa-check-circle text-success me-1"></i>Upload a new file to replace it.';
            docPreviewContainer.appendChild(existingNote);
        }

        /**
         * BUGFIX: Fixed showEditRsbsaModal function
         * Issue: Was trying to set values on non-existent IDs (edit_rsbsa_land_area, edit_rsbsa_commodity)
         * Solution: Only populate fields that actually exist in the HTML structure
         */
        function showEditRsbsaModal(applicationId) {
            if (!applicationId) {
                showToast('error', 'Invalid registration ID');
                return;
            }

            // Fetch registration data
            fetch(`/admin/rsbsa-applications/${applicationId}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(response => {
                    if (!response.success) {
                        throw new Error(response.message || 'Failed to load registration');
                    }

                    const data = response.data;

                    // Populate editable fields - ONLY THOSE THAT EXIST
                    const firstNameEl = document.getElementById('edit_rsbsa_first_name');
                    const middleNameEl = document.getElementById('edit_rsbsa_middle_name');
                    const lastNameEl = document.getElementById('edit_rsbsa_last_name');
                    const extensionEl = document.getElementById('edit_rsbsa_extension');
                    const contactEl = document.getElementById('edit_rsbsa_contact_number');
                    const barangayEl = document.getElementById('edit_rsbsa_barangay');
                    const farmLocationEl = document.getElementById('edit_rsbsa_farm_location');
                    const livelihoodEl = document.getElementById('edit_rsbsa_livelihood');

                    // FIX: Check if element exists before setting value
                    if (firstNameEl) firstNameEl.value = data.first_name || '';
                    if (middleNameEl) middleNameEl.value = data.middle_name || '';
                    if (lastNameEl) lastNameEl.value = data.last_name || '';
                    if (extensionEl) extensionEl.value = data.name_extension || '';
                    if (contactEl) contactEl.value = data.contact_number || '';
                    if (barangayEl) barangayEl.value = data.barangay || '';
                    if (farmLocationEl) farmLocationEl.value = data.farm_location || '';
                    if (livelihoodEl) livelihoodEl.value = data.main_livelihood || '';

                    // Read-only fields
                    const appNumberEl = document.getElementById('edit_rsbsa_app_number');
                    const editAppNumberEl = document.getElementById('editAppNumber');
                    if (appNumberEl) appNumberEl.value = data.application_number || '';
                    if (editAppNumberEl) editAppNumberEl.textContent = data.application_number || '';

                    // Status badge
                    const statusBadge = document.getElementById('edit_rsbsa_status_badge');
                    if (statusBadge) {
                        statusBadge.className = `badge bg-${data.status_color}`;
                        statusBadge.textContent = data.formatted_status;
                    }

                    // Date applied
                    const createdAtEl = document.getElementById('edit_rsbsa_created_at');
                    if (createdAtEl) createdAtEl.textContent = data.created_at || 'N/A';

                    // Display existing supporting document preview if it exists
                    const docPreviewContainer = document.getElementById('edit_rsbsa_supporting_document_preview');
                    if (docPreviewContainer) {
                        docPreviewContainer.innerHTML = '';
                        if (data.supporting_document_url) {
                            displayEditRsbsaExistingDocument(data.supporting_document_path,
                                'edit_rsbsa_supporting_document_preview');
                        }
                    }

                    // Initialize the form for change detection
                    initializeEditRsbsaForm(applicationId, data);

                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('editRsbsaModal'));
                    modal.show();

                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error loading registration: ' + error.message);
                });
        }
        /**
         * BUGFIX: Fixed initializeEditRsbsaForm function
         * Issue: Was trying to set values on IDs that don't exist or are livelihood-specific
         * Solution: Populate only the fields that exist, and handle livelihood fields properly
         */
        function initializeEditRsbsaForm(applicationId, data) {
            const form = document.getElementById('editRsbsaForm');
            const submitBtn = document.getElementById('editRsbsaSubmitBtn');

            // Populate all basic fields (these always exist)
            const basicFields = {
                'edit_rsbsa_first_name': data.first_name,
                'edit_rsbsa_middle_name': data.middle_name,
                'edit_rsbsa_last_name': data.last_name,
                'edit_rsbsa_extension': data.name_extension,
                'edit_rsbsa_sex': data.sex, // ✅ NOW ADDED
                'edit_rsbsa_contact_number': data.contact_number,
                'edit_rsbsa_address': data.address, // ✅ NOW ADDED
                'edit_rsbsa_barangay': data.barangay,
                'edit_rsbsa_farm_location': data.farm_location,
                'edit_rsbsa_livelihood': data.main_livelihood
            };

            // Only set value if element exists
            Object.keys(basicFields).forEach(elementId => {
                const input = document.getElementById(elementId);
                if (input) {
                    input.value = basicFields[elementId] || '';
                }
            });

            // NOW POPULATE LIVELIHOOD-SPECIFIC FIELDS
            // Farmer fields
            const farmerFields = {
                'edit_rsbsa_farmer_crops': data.farmer_crops,
                'edit_rsbsa_farmer_land_area': data.farmer_land_area,
                'edit_rsbsa_farmer_type_of_farm': data.farmer_type_of_farm,
                'edit_rsbsa_farmer_land_ownership': data.farmer_land_ownership,
                'edit_rsbsa_farmer_special_status': data.farmer_special_status,
                'edit_rsbsa_farmer_commodity': data.commodity
            };

            Object.keys(farmerFields).forEach(elementId => {
                const input = document.getElementById(elementId);
                if (input) {
                    input.value = farmerFields[elementId] || '';
                }
            });

            // Farmworker fields
            const farmworkerFields = {
                'edit_rsbsa_farmworker_type': data.farmworker_type,
                'edit_rsbsa_farmworker_commodity': data.commodity
            };

            Object.keys(farmworkerFields).forEach(elementId => {
                const input = document.getElementById(elementId);
                if (input) {
                    input.value = farmworkerFields[elementId] || '';
                }
            });

            // Fisherfolk fields
            const fisherfolkFields = {
                'edit_rsbsa_fisherfolk_activity': data.fisherfolk_activity,
                'edit_rsbsa_fisherfolk_commodity': data.commodity
            };

            Object.keys(fisherfolkFields).forEach(elementId => {
                const input = document.getElementById(elementId);
                if (input) {
                    input.value = fisherfolkFields[elementId] || '';
                }
            });

            // Agri-youth fields
            const agriyouthFields = {
                'edit_rsbsa_agriyouth_household': data.agriyouth_farming_household,
                'edit_rsbsa_agriyouth_training': data.agriyouth_training,
                'edit_rsbsa_agriyouth_participation': data.agriyouth_participation,
                'edit_rsbsa_agriyouth_commodity': data.commodity
            };

            Object.keys(agriyouthFields).forEach(elementId => {
                const input = document.getElementById(elementId);
                if (input) {
                    input.value = agriyouthFields[elementId] || '';
                }
            });

            // Store original data for comparison - INCLUDING SEX AND ADDRESS
            const originalData = {
                first_name: data.first_name || '',
                middle_name: data.middle_name || '',
                last_name: data.last_name || '',
                name_extension: data.name_extension || '',
                sex: data.sex || '', // ✅ NOW ADDED
                contact_number: data.contact_number || '',
                address: data.address || '', // ✅ NOW ADDED
                barangay: data.barangay || '',
                farm_location: data.farm_location || '',
                main_livelihood: data.main_livelihood || '',
                // Farmer fields
                farmer_crops: data.farmer_crops || '',
                farmer_land_area: data.farmer_land_area || '',
                farmer_type_of_farm: data.farmer_type_of_farm || '',
                farmer_land_ownership: data.farmer_land_ownership || '',
                farmer_special_status: data.farmer_special_status || '',
                // Farmworker fields
                farmworker_type: data.farmworker_type || '',
                // Fisherfolk fields
                fisherfolk_activity: data.fisherfolk_activity || '',
                // Agri-youth fields
                agriyouth_farming_household: data.agriyouth_farming_household || '',
                agriyouth_training: data.agriyouth_training || '',
                agriyouth_participation: data.agriyouth_participation || '',
                // Commodity
                commodity: data.commodity || ''
            };

            form.dataset.originalData = JSON.stringify(originalData);
            form.dataset.applicationId = applicationId;

            // Clear validation states
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            form.querySelectorAll('.form-changed').forEach(el => el.classList.remove('form-changed'));

            // Reset button state
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
            submitBtn.disabled = false;
            submitBtn.dataset.hasChanges = 'false';

            // Trigger livelihood field toggle to show correct section
            if (data.main_livelihood) {
                const livelihoodSelect = document.getElementById('edit_rsbsa_livelihood');
                if (livelihoodSelect) {
                    toggleEditRsbsaLivelihoodFields(livelihoodSelect);
                }
            }

            // Add change listeners
            addRsbsaFormChangeListeners(applicationId);
        }


        /**
         * Helper function to safely get element value
         */
        function getSafeElementValue(elementId) {
            const element = document.getElementById(elementId);
            return element ? (element.value || '') : '';
        }

        /**
         * Helper function to safely set element value
         */
        function setSafeElementValue(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                element.value = value || '';
                return true;
            }
            console.warn(`Element not found: ${elementId}`);
            return false;
        }

        /**
         * Add event listeners to detect form changes
         */
        function addRsbsaFormChangeListeners(applicationId) {
            const form = document.getElementById('editRsbsaForm');
            const inputs = form.querySelectorAll(
                'input[type="text"], input[type="tel"], input[type="number"], textarea, select');

            inputs.forEach(input => {
                // Remove any existing listeners first
                input.removeEventListener('input', handleRsbsaFormChange);
                input.removeEventListener('change', handleRsbsaFormChange);

                // Add new listeners
                input.addEventListener('input', handleRsbsaFormChange);
                input.addEventListener('change', handleRsbsaFormChange);
            });

            // Also add listener for file input
            const fileInput = form.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.removeEventListener('change', handleRsbsaFormChange);
                fileInput.addEventListener('change', handleRsbsaFormChange);
            }
        }


        /**
         * Handle form change event
         */
        function handleRsbsaFormChange() {
            const form = document.getElementById('editRsbsaForm');
            const applicationId = form.dataset.applicationId;
            checkRsbsaFormChanges(applicationId);
        }


        /**
         * UPDATED: Check for changes in the form - NOW INCLUDES SEX AND ADDRESS
         */
        function checkRsbsaFormChanges(applicationId) {
            const form = document.getElementById('editRsbsaForm');
            const submitBtn = document.getElementById('editRsbsaSubmitBtn');

            if (!form || !submitBtn) return;

            const originalData = JSON.parse(form.dataset.originalData || '{}');
            let hasChanges = false;

            // Updated field map - NOW INCLUDES SEX AND ADDRESS
            const fieldMap = {
                'first_name': 'edit_rsbsa_first_name',
                'middle_name': 'edit_rsbsa_middle_name',
                'last_name': 'edit_rsbsa_last_name',
                'name_extension': 'edit_rsbsa_extension',
                'sex': 'edit_rsbsa_sex', // ✅ NOW ADDED
                'contact_number': 'edit_rsbsa_contact_number',
                'address': 'edit_rsbsa_address', // ✅ NOW ADDED
                'barangay': 'edit_rsbsa_barangay',
                'farm_location': 'edit_rsbsa_farm_location',
                'main_livelihood': 'edit_rsbsa_livelihood',
                // Farmer fields
                'farmer_crops': 'edit_rsbsa_farmer_crops',
                'farmer_land_area': 'edit_rsbsa_farmer_land_area',
                'farmer_type_of_farm': 'edit_rsbsa_farmer_type_of_farm',
                'farmer_land_ownership': 'edit_rsbsa_farmer_land_ownership',
                'farmer_special_status': 'edit_rsbsa_farmer_special_status',
                // Farmworker fields
                'farmworker_type': 'edit_rsbsa_farmworker_type',
                // Fisherfolk fields
                'fisherfolk_activity': 'edit_rsbsa_fisherfolk_activity',
                // Agri-youth fields
                'agriyouth_farming_household': 'edit_rsbsa_agriyouth_household',
                'agriyouth_training': 'edit_rsbsa_agriyouth_training',
                'agriyouth_participation': 'edit_rsbsa_agriyouth_participation',
                // Commodity
                'commodity': 'edit_rsbsa_farmer_commodity'
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

            // Check if file has been selected/changed
            const fileInput = document.getElementById('edit_rsbsa_supporting_document');
            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                hasChanges = true;
            }

            // Update button state based on changes
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
         * UPDATED: Validate edit RSBSA form - NOW INCLUDES SEX AND ADDRESS
         */
        function validateEditRsbsaForm() {
            const form = document.getElementById('editRsbsaForm');
            let isValid = true;

            // Clear all previous validation states
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            const requiredFields = [{
                    elementId: 'edit_rsbsa_first_name',
                    label: 'First Name'
                },
                {
                    elementId: 'edit_rsbsa_last_name',
                    label: 'Last Name'
                },
                {
                    elementId: 'edit_rsbsa_sex',
                    label: 'Sex'
                }, // ✅ NOW ADDED
                {
                    elementId: 'edit_rsbsa_contact_number',
                    label: 'Contact Number'
                },
                {
                    elementId: 'edit_rsbsa_address',
                    label: 'Address'
                }, // ✅ NOW ADDED
                {
                    elementId: 'edit_rsbsa_barangay',
                    label: 'Barangay'
                },
                {
                    elementId: 'edit_rsbsa_livelihood',
                    label: 'Main Livelihood'
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
            const contactInput = document.getElementById('edit_rsbsa_contact_number');
            if (contactInput && contactInput.value.trim()) {
                const phoneRegex = /^(\+639|09)\d{9}$/;
                if (!phoneRegex.test(contactInput.value.trim())) {
                    contactInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
                    contactInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            }

            // Validate livelihood-specific required fields
            const livelihoodSelect = document.getElementById('edit_rsbsa_livelihood');
            const livelihood = livelihoodSelect?.value;

            switch (livelihood) {
                case 'Farmer':
                    if (!validateEditLivelihoodRequiredFields([
                            'edit_rsbsa_farmer_crops',
                            'edit_rsbsa_farmer_type_of_farm',
                            'edit_rsbsa_farmer_land_ownership',
                            'edit_rsbsa_farm_location'
                        ])) {
                        isValid = false;
                    }
                    break;

                case 'Farmworker/Laborer':
                    if (!validateEditLivelihoodRequiredFields(['edit_rsbsa_farmworker_type'])) {
                        isValid = false;
                    }
                    break;

                case 'Fisherfolk':
                    if (!validateEditLivelihoodRequiredFields(['edit_rsbsa_fisherfolk_activity'])) {
                        isValid = false;
                    }
                    break;

                case 'Agri-youth':
                    if (!validateEditLivelihoodRequiredFields([
                            'edit_rsbsa_agriyouth_household',
                            'edit_rsbsa_agriyouth_training',
                            'edit_rsbsa_agriyouth_participation'
                        ])) {
                        isValid = false;
                    }
                    break;
            }

            // Validate land area if provided
            const landAreaInput = document.getElementById('edit_rsbsa_farmer_land_area');
            if (landAreaInput && landAreaInput.value) {
                const landArea = parseFloat(landAreaInput.value);
                if (isNaN(landArea) || landArea < 0) {
                    landAreaInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Land area must be a positive number';
                    landAreaInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
                if (landArea > 1000) {
                    landAreaInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Land area cannot exceed 1000 hectares';
                    landAreaInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            }

            return isValid;
        }

        /**
         * Get CSRF token from meta tag
         */
        function getCSRFToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (!metaTag) {
                console.error('CSRF token meta tag not found!');
                return '';
            }
            return metaTag.getAttribute('content');
        }
        /**
         * Validate contact number in edit form
         */
        function validateEditRsbsaContactNumber(input) {
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!input.value.trim()) return true;

            const phoneRegex = /^(\+639|09)\d{9}$/;

            if (!phoneRegex.test(input.value.trim())) {
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
        // handle edit submition
        function handleEditRsbsaSubmit() {
            const form = document.getElementById('editRsbsaForm');
            const submitBtn = document.getElementById('editRsbsaSubmitBtn');
            const applicationId = form.dataset.applicationId;

            // Validate form first
            if (!validateEditRsbsaForm()) {
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
                'sex': 'Sex',
                'contact_number': 'Contact Number',
                'address': 'Address',
                'barangay': 'Barangay',
                'farm_location': 'Farm Location',
                'main_livelihood': 'Main Livelihood',
                'farmer_crops': 'Main Crops',
                'farmer_land_area': 'Land Area',
                'farmer_type_of_farm': 'Type of Farm',
                'farmer_land_ownership': 'Land Ownership',
                'farmer_special_status': 'Special Status',
                'farmworker_type': 'Type of Work',
                'fisherfolk_activity': 'Fishing Activity',
                'agriyouth_farming_household': 'From Farming Household',
                'agriyouth_training': 'Agricultural Training',
                'agriyouth_participation': 'Program Participation'
            };

            const fieldMap = {
                'first_name': 'edit_rsbsa_first_name',
                'middle_name': 'edit_rsbsa_middle_name',
                'last_name': 'edit_rsbsa_last_name',
                'name_extension': 'edit_rsbsa_extension',
                'sex': 'edit_rsbsa_sex',
                'contact_number': 'edit_rsbsa_contact_number',
                'address': 'edit_rsbsa_address',
                'barangay': 'edit_rsbsa_barangay',
                'farm_location': 'edit_rsbsa_farm_location',
                'main_livelihood': 'edit_rsbsa_livelihood',
                'farmer_crops': 'edit_rsbsa_farmer_crops',
                'farmer_land_area': 'edit_rsbsa_farmer_land_area',
                'farmer_type_of_farm': 'edit_rsbsa_farmer_type_of_farm',
                'farmer_land_ownership': 'edit_rsbsa_farmer_land_ownership',
                'farmer_special_status': 'edit_rsbsa_farmer_special_status',
                'farmworker_type': 'edit_rsbsa_farmworker_type',
                'fisherfolk_activity': 'edit_rsbsa_fisherfolk_activity',
                'agriyouth_farming_household': 'edit_rsbsa_agriyouth_household',
                'agriyouth_training': 'edit_rsbsa_agriyouth_training',
                'agriyouth_participation': 'edit_rsbsa_agriyouth_participation'
            };

            Object.keys(fieldMap).forEach(fieldName => {
                const elementId = fieldMap[fieldName];
                const input = document.getElementById(elementId);

                if (input) {
                    const currentValue = (input.value || '').trim();
                    const originalValue = (originalData[fieldName] || '').trim();

                    if (currentValue !== originalValue && currentValue !== '') {
                        changedFields.push(fieldLabels[fieldName] || fieldName);
                    }
                }
            });

            const fileInput = document.getElementById('edit_rsbsa_supporting_document');
            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                changedFields.push('Supporting Document');
            }

            const changesText = changedFields.length > 0 ?
                `Save the following changes to this RSBSA registration?\n\n• ${changedFields.join('\n• ')}` :
                'Save the changes to this RSBSA registration?';

            showConfirmationToast(
                'Confirm Update',
                changesText,
                () => proceedWithEditRsbsa(form, applicationId)
            );
        }

        /**
         * ENHANCED DEBUGGING VERSION: proceedWithEditRsbsa
         * This version logs ALL validation errors clearly so you can see exactly what's failing
         */
        function proceedWithEditRsbsa(form, applicationId) {
            const submitBtn = document.getElementById('editRsbsaSubmitBtn');

            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
            submitBtn.disabled = true;

            // Create FormData - collect ALL form fields including livelihood-specific ones
            const formData = new FormData();

            // Get all form fields (both text and select inputs)
            const formInputs = form.querySelectorAll('input[name], select[name], textarea[name]');

            console.log('📤 COLLECTING FORM DATA:');
            console.log('='.repeat(60));
            let fieldCount = 0;

            // Collect all visible form fields
            const collectedFields = {};
            formInputs.forEach(field => {
                // Skip if field's parent section is hidden (display: none)
                let parent = field.closest('[id*="-fields"]');
                if (parent && parent.style.display === 'none') {
                    console.log(`  ⊗ [HIDDEN] ${field.name}`);
                    return;
                }

                // Store for logging
                collectedFields[field.name] = field.value;

                // Add the field to FormData
                formData.append(field.name, field.value);

                if (field.value !== '') {
                    console.log(`[OK] ${field.name.padEnd(35)} = "${field.value}"`);
                } else {
                    console.log(`[WARN] ${field.name.padEnd(35)} = [EMPTY]`);
                }
                fieldCount++;
            });

            // Add file if present
            const fileInput = document.getElementById('edit_rsbsa_supporting_document');
            if (fileInput && fileInput.files && fileInput.files[0]) {
                formData.append('supporting_document', fileInput.files[0]);
                console.log(`  📎 supporting_document.padEnd(35) = ${fileInput.files[0].name}`);
                fieldCount++;
            }

            // Add method spoofing for PUT
            formData.append('_method', 'PUT');
            console.log(`  🔧 _method.padEnd(35) = PUT`);

            console.log('='.repeat(60));
            console.log(`📊 TOTAL FIELDS: ${fieldCount}`);
            console.log('='.repeat(60));

            // Log the livelihood type
            const livelihood = document.getElementById('edit_rsbsa_livelihood')?.value;
            console.log(`📌 LIVELIHOOD TYPE: ${livelihood}`);
            console.log('='.repeat(60));

            // Disable form inputs AFTER collecting data
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => input.disabled = true);

            // Submit with POST (will be treated as PUT due to _method)
            console.log(`🚀 SENDING REQUEST TO: /admin/rsbsa-applications/${applicationId}`);

            fetch(`/admin/rsbsa-applications/${applicationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    console.log(`📡 RESPONSE STATUS: ${response.status}`);

                    return response.json().then(data => {
                        console.log('📥 RESPONSE DATA:', data);

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
                    console.log('✅ SUCCESS - Server accepted the data');

                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editRsbsaModal'));
                        if (modal) modal.hide();

                        showToast('success', data.message || 'Application updated successfully');

                        // Reload the registration table to reflect changes
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        throw {
                            status: 422,
                            message: data.message || 'Failed to update registration',
                            errors: data.errors || {}
                        };
                    }
                })
                .catch(error => {
                    console.error('❌ ERROR CAUGHT:', error);
                    console.log('='.repeat(60));

                    // Handle validation errors from server
                    if (error.status === 422 && error.errors && typeof error.errors === 'object') {
                        console.error('🚨 SERVER VALIDATION ERRORS:');
                        console.log('='.repeat(60));

                        // Pretty print all errors
                        Object.keys(error.errors).forEach((fieldName, index) => {
                            const errorMsg = error.errors[fieldName];
                            const errorText = Array.isArray(errorMsg) ? errorMsg.join(', ') : errorMsg;
                            console.error(`${index + 1}. [${fieldName}]: ${errorText}`);
                        });

                        console.log('='.repeat(60));
                        console.table(error.errors);

                        // Map backend field names to form element IDs
                        const fieldMap = {
                            'first_name': 'edit_rsbsa_first_name',
                            'middle_name': 'edit_rsbsa_middle_name',
                            'last_name': 'edit_rsbsa_last_name',
                            'name_extension': 'edit_rsbsa_extension',
                            'contact_number': 'edit_rsbsa_contact_number',
                            'barangay': 'edit_rsbsa_barangay',
                            'main_livelihood': 'edit_rsbsa_livelihood',

                            // Farmer fields
                            'farm_location': 'edit_rsbsa_farm_location',
                            'farmer_crops': 'edit_rsbsa_farmer_crops',
                            'farmer_land_area': 'edit_rsbsa_farmer_land_area',
                            'farmer_type_of_farm': 'edit_rsbsa_farmer_type_of_farm',
                            'farmer_land_ownership': 'edit_rsbsa_farmer_land_ownership',
                            'farmer_special_status': 'edit_rsbsa_farmer_special_status',

                            // Farmworker fields
                            'farmworker_type': 'edit_rsbsa_farmworker_type',

                            // Fisherfolk fields
                            'fisherfolk_activity': 'edit_rsbsa_fisherfolk_activity',

                            // Agri-youth fields
                            'agriyouth_farming_household': 'edit_rsbsa_agriyouth_household',
                            'agriyouth_training': 'edit_rsbsa_agriyouth_training',
                            'agriyouth_participation': 'edit_rsbsa_agriyouth_participation',

                            // General
                            'commodity': 'edit_rsbsa_farmer_commodity',
                            'supporting_document': 'edit_rsbsa_supporting_document'
                        };

                        // Display validation errors on fields
                        Object.keys(error.errors).forEach(field => {
                            const elementId = fieldMap[field] || 'edit_rsbsa_' + field;
                            const input = document.getElementById(elementId);

                            if (input) {
                                input.classList.add('is-invalid');

                                // Remove existing feedback if present
                                const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                                if (existingFeedback) existingFeedback.remove();

                                // Add error message
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback d-block';
                                const errorMessage = Array.isArray(error.errors[field]) ?
                                    error.errors[field][0] :
                                    error.errors[field];
                                errorDiv.textContent = errorMessage;
                                input.parentNode.appendChild(errorDiv);

                                console.error(`Field "${field}":`, errorMessage);

                                // Scroll to first error field
                                if (input.offsetParent !== null) {
                                    input.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                }
                            } else {
                                console.warn(
                                    `❓ Element not found for field: ${field} (tried ID: ${elementId})`);
                            }
                        });

                        showToast('error', error.message || 'Validation errors - please check the form');
                    } else {
                        console.error('Unexpected error type:', error.message || error);
                        showToast('error', error.message || 'Error updating registration');
                    }

                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    inputs.forEach(input => input.disabled = false);
                });
        }

        /**
         * ALTERNATIVE: Manual field collection approach
         * Use this if the above doesn't work perfectly
         */
        function proceedWithEditRsbsa_ManualCollection(form, applicationId) {
            const submitBtn = document.getElementById('editRsbsaSubmitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
            submitBtn.disabled = true;

            const formData = new FormData();
            const livelihood = document.getElementById('edit_rsbsa_livelihood').value;

            console.log('📤 Manually collecting form data for livelihood:', livelihood);

            // Basic fields (always present)
            formData.append('first_name', document.getElementById('edit_rsbsa_first_name').value.trim());
            formData.append('middle_name', document.getElementById('edit_rsbsa_middle_name').value.trim());
            formData.append('last_name', document.getElementById('edit_rsbsa_last_name').value.trim());
            formData.append('name_extension', document.getElementById('edit_rsbsa_extension').value);
            formData.append('contact_number', document.getElementById('edit_rsbsa_contact_number').value.trim());
            formData.append('barangay', document.getElementById('edit_rsbsa_barangay').value);
            formData.append('main_livelihood', livelihood);

            // Livelihood-specific fields based on selected type
            switch (livelihood) {
                case 'Farmer':
                    console.log('📋 Collecting Farmer fields...');
                    formData.append('farm_location', document.getElementById('edit_rsbsa_farm_location').value.trim());
                    formData.append('farmer_crops', document.getElementById('edit_rsbsa_farmer_crops').value.trim());
                    formData.append('farmer_land_area', document.getElementById('edit_rsbsa_farmer_land_area').value);
                    formData.append('farmer_type_of_farm', document.getElementById('edit_rsbsa_farmer_type_of_farm').value);
                    formData.append('farmer_land_ownership', document.getElementById('edit_rsbsa_farmer_land_ownership')
                        .value);
                    formData.append('farmer_special_status', document.getElementById('edit_rsbsa_farmer_special_status')
                        .value);
                    formData.append('commodity', document.getElementById('edit_rsbsa_farmer_commodity').value.trim());
                    break;

                case 'Farmworker/Laborer':
                    console.log('📋 Collecting Farmworker fields...');
                    formData.append('farmworker_type', document.getElementById('edit_rsbsa_farmworker_type').value.trim());
                    formData.append('commodity', document.getElementById('edit_rsbsa_farmworker_commodity').value.trim());
                    break;

                case 'Fisherfolk':
                    console.log('📋 Collecting Fisherfolk fields...');
                    formData.append('fisherfolk_activity', document.getElementById('edit_rsbsa_fisherfolk_activity').value
                        .trim());
                    formData.append('commodity', document.getElementById('edit_rsbsa_fisherfolk_commodity').value.trim());
                    break;

                case 'Agri-youth':
                    console.log('📋 Collecting Agri-youth fields...');
                    formData.append('agriyouth_farming_household', document.getElementById('edit_rsbsa_agriyouth_household')
                        .value);
                    formData.append('agriyouth_training', document.getElementById('edit_rsbsa_agriyouth_training').value
                        .trim());
                    formData.append('agriyouth_participation', document.getElementById('edit_rsbsa_agriyouth_participation')
                        .value);
                    formData.append('commodity', document.getElementById('edit_rsbsa_agriyouth_commodity').value.trim());
                    break;
            }

            // Add file if present
            const fileInput = document.getElementById('edit_rsbsa_supporting_document');
            if (fileInput && fileInput.files && fileInput.files[0]) {
                formData.append('supporting_document', fileInput.files[0]);
            }

            // Add method spoofing
            formData.append('_method', 'PUT');

            // Log what we're sending
            console.log('📊 FormData contents:');
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(`  ${key}: [File] ${value.name}`);
                } else {
                    console.log(`  ${key}: "${value}"`);
                }
            }

            // Disable inputs
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => input.disabled = true);

            // Submit
            fetch(`/admin/rsbsa-applications/${applicationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json().then(data => ({
                    status: response.status,
                    ok: response.ok,
                    data: data
                })))
                .then(({
                    status,
                    ok,
                    data
                }) => {
                    console.log('Response:', {
                        status,
                        ok,
                        data
                    });

                    if (!ok) {
                        throw {
                            status: status,
                            message: data.message || 'Update failed',
                            errors: data.errors || {}
                        };
                    }

                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editRsbsaModal'));
                        if (modal) modal.hide();

                        showToast('success', data.message || 'Application updated successfully');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        throw {
                            status: 422,
                            message: data.message || 'Failed to update',
                            errors: data.errors || {}
                        };
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    // Handle validation errors
                    if (error.errors && typeof error.errors === 'object') {
                        const fieldMap = {
                            'farm_location': 'edit_rsbsa_farm_location',
                            'farmer_crops': 'edit_rsbsa_farmer_crops',
                            'farmer_land_area': 'edit_rsbsa_farmer_land_area',
                            'farmer_type_of_farm': 'edit_rsbsa_farmer_type_of_farm',
                            'farmer_land_ownership': 'edit_rsbsa_farmer_land_ownership',
                            'farmworker_type': 'edit_rsbsa_farmworker_type',
                            'fisherfolk_activity': 'edit_rsbsa_fisherfolk_activity',
                            'agriyouth_farming_household': 'edit_rsbsa_agriyouth_household',
                            'agriyouth_training': 'edit_rsbsa_agriyouth_training',
                            'agriyouth_participation': 'edit_rsbsa_agriyouth_participation'
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
                                errorDiv.textContent = Array.isArray(error.errors[field]) ?
                                    error.errors[field][0] :
                                    error.errors[field];
                                input.parentNode.appendChild(errorDiv);
                            }
                        });
                    }

                    showToast('error', error.message || 'Error updating registration');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    inputs.forEach(input => input.disabled = false);
                });
        }

        /**
         * Validate JSON data locally BEFORE sending to server
         */
        function validateJsonDataLocally(data) {
            const errors = {};

            // Check required fields
            if (!data.first_name || data.first_name.trim() === '') {
                errors.first_name = 'First Name is required';
            }

            if (!data.last_name || data.last_name.trim() === '') {
                errors.last_name = 'Last Name is required';
            }

            if (!data.contact_number || data.contact_number.trim() === '') {
                errors.contact_number = 'Contact Number is required';
            }

            if (!data.barangay || data.barangay.trim() === '') {
                errors.barangay = 'Barangay is required';
            }

            if (!data.main_livelihood || data.main_livelihood.trim() === '') {
                errors.main_livelihood = 'Main Livelihood is required';
            }

            // Validate contact number format
            if (data.contact_number && data.contact_number.trim() !== '') {
                const phoneRegex = /^(\+639|09)\d{9}$/;
                if (!phoneRegex.test(data.contact_number.trim())) {
                    errors.contact_number =
                        'Contact number must be in format: 09XXXXXXXXX(exactly 11 digits)';
                }
            }

            // Validate land area if provided
            if (data.land_area !== null && data.land_area !== '') {
                const landArea = parseFloat(data.land_area);
                if (isNaN(landArea) || landArea < 0) {
                    errors.land_area = 'Land area must be a positive number';
                }
                if (landArea > 99999.99) {
                    errors.land_area = 'Land area cannot exceed 99999.99 hectares';
                }
            }

            return errors;
        }

        // Auto-capitalize names in edit form
        function capitalizeRsbsaEditName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');

                // Trigger change detection after capitalization
                const form = document.getElementById('editRsbsaForm');
                if (form && form.dataset.applicationId) {
                    checkRsbsaFormChanges(form.dataset.applicationId);
                }
            }
        }

        // Initialize name field auto-capitalize when modal is shown
        document.addEventListener('DOMContentLoaded', function() {
            // Set up event delegation for dynamically added elements
            document.addEventListener('focusout', function(e) {
                if (e.target.id === 'edit_rsbsa_first_name' ||
                    e.target.id === 'edit_rsbsa_middle_name' ||
                    e.target.id === 'edit_rsbsa_last_name') {
                    capitalizeRsbsaEditName(e.target);
                }
            });
        });

        function updateRsbsaRemarksCounter() {
            const textarea = document.getElementById('rsbsa_remarks');
            const charCount = document.getElementById('rsbsaCharCount');

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
        /**
         * Toggle livelihood-specific fields in Add modal
         */
        function toggleAddRsbsaLivelihoodFields(selectElement) {
            const selectedValue = selectElement.value;
            console.log('🔄 Toggling livelihood fields for:', selectedValue);

            // Hide all livelihood-specific sections first
            document.getElementById('farmer-fields').style.display = 'none';
            document.getElementById('farmworker-fields').style.display = 'none';
            document.getElementById('fisherfolk-fields').style.display = 'none';
            document.getElementById('agriyouth-fields').style.display = 'none';

            // Hide all required field indicators
            document.querySelectorAll('[id$="_req"]').forEach(el => {
                el.style.display = 'none';
            });

            // Show only the selected livelihood section and its required fields
            switch (selectedValue) {
                case 'Farmer':
                    document.getElementById('farmer-fields').style.display = 'block';
                    // Show required field indicators for farmer fields
                    document.getElementById('farmer_crops_req').style.display = 'inline';
                    document.getElementById('farmer_type_req').style.display = 'inline';
                    document.getElementById('farmer_ownership_req').style.display = 'inline';
                    document.getElementById('farmer_location_req').style.display = 'inline';
                    console.log('✅ Showing Farmer fields');
                    break;

                case 'Farmworker/Laborer':
                    document.getElementById('farmworker-fields').style.display = 'block';
                    // Show required field indicator for farmworker type
                    document.getElementById('farmworker_type_req').style.display = 'inline';
                    console.log('✅ Showing Farmworker fields');
                    break;

                case 'Fisherfolk':
                    document.getElementById('fisherfolk-fields').style.display = 'block';
                    // Show required field indicator for fisherfolk activity
                    document.getElementById('fisherfolk_activity_req').style.display = 'inline';
                    console.log('✅ Showing Fisherfolk fields');
                    break;

                case 'Agri-youth':
                    document.getElementById('agriyouth-fields').style.display = 'block';
                    // Show required field indicators for agri-youth fields
                    document.getElementById('agriyouth_household_req').style.display = 'inline';
                    document.getElementById('agriyouth_training_req').style.display = 'inline';
                    document.getElementById('agriyouth_participation_req').style.display = 'inline';
                    console.log('✅ Showing Agri-youth fields');
                    break;

                default:
                    console.log('⚠️ No livelihood selected');
            }

            // Clear any existing validation errors in hidden fields
            clearValidationErrorsForHiddenFields();
        }

        /**
         * Clear validation errors for hidden fields to avoid validation errors on hidden inputs
         */
        function clearValidationErrorsForHiddenFields() {
            const allLivelihoodFields = document.querySelectorAll(
                '#farmer-fields input, #farmer-fields select, #farmworker-fields input, #farmworker-fields select, #fisherfolk-fields input, #fisherfolk-fields select, #agriyouth-fields input, #agriyouth-fields select'
            );

            allLivelihoodFields.forEach(field => {
                const section = field.closest('[id*="-fields"]');
                if (section && section.style.display === 'none') {
                    // Clear the field value and remove validation classes
                    field.classList.remove('is-invalid', 'is-valid');
                    const feedback = field.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();
                }
            });
        }

        /**
         * Toggle livelihood-specific fields in Edit modal
         */
        /**
         * UPDATED: Toggle livelihood-specific fields in Edit modal
         */
        function toggleEditRsbsaLivelihoodFields(selectElement) {
            const selectedValue = selectElement.value;
            console.log('🔄 [EDIT] Toggling livelihood fields for:', selectedValue);

            // Hide all livelihood-specific sections first
            document.getElementById('edit-farmer-fields').style.display = 'none';
            document.getElementById('edit-farmworker-fields').style.display = 'none';
            document.getElementById('edit-fisherfolk-fields').style.display = 'none';
            document.getElementById('edit-agriyouth-fields').style.display = 'none';

            // Show only the selected livelihood section
            switch (selectedValue) {
                case 'Farmer':
                    document.getElementById('edit-farmer-fields').style.display = 'block';
                    console.log('✅ [EDIT] Showing Farmer fields');
                    break;
                case 'Farmworker/Laborer':
                    document.getElementById('edit-farmworker-fields').style.display = 'block';
                    console.log('✅ [EDIT] Showing Farmworker fields');
                    break;
                case 'Fisherfolk':
                    document.getElementById('edit-fisherfolk-fields').style.display = 'block';
                    console.log('✅ [EDIT] Showing Fisherfolk fields');
                    break;
                case 'Agri-youth':
                    document.getElementById('edit-agriyouth-fields').style.display = 'block';
                    console.log('✅ [EDIT] Showing Agri-youth fields');
                    break;
                default:
                    console.log('⚠️ [EDIT] No livelihood selected');
            }

            // Trigger change detection for edit form
            const form = document.getElementById('editRsbsaForm');
            if (form && form.dataset.applicationId) {
                checkRsbsaFormChanges(form.dataset.applicationId);
            }
        }

        /**
         * UPDATED: Validate edit RSBSA form - NOW INCLUDES LIVELIHOOD VALIDATION
         */
        function validateEditRsbsaForm() {
            const form = document.getElementById('editRsbsaForm');
            let isValid = true;

            // Clear all previous validation states
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            const requiredFields = [{
                    elementId: 'edit_rsbsa_first_name',
                    label: 'First Name'
                },
                {
                    elementId: 'edit_rsbsa_last_name',
                    label: 'Last Name'
                },
                {
                    elementId: 'edit_rsbsa_sex',
                    label: 'Sex'
                },
                {
                    elementId: 'edit_rsbsa_contact_number',
                    label: 'Contact Number'
                },
                {
                    elementId: 'edit_rsbsa_barangay',
                    label: 'Barangay'
                },
                {
                    elementId: 'edit_rsbsa_address',
                    label: 'Address'
                },
                {
                    elementId: 'edit_rsbsa_livelihood',
                    label: 'Main Livelihood'
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
            const contactInput = document.getElementById('edit_rsbsa_contact_number');
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

            // Validate livelihood-specific required fields
            const livelihoodSelect = document.getElementById('edit_rsbsa_livelihood');
            const livelihood = livelihoodSelect?.value;

            switch (livelihood) {
                case 'Farmer':
                    if (!validateEditLivelihoodRequiredFields([
                            'edit_rsbsa_farmer_crops',
                            'edit_rsbsa_farmer_type_of_farm',
                            'edit_rsbsa_farmer_land_ownership',
                            'edit_rsbsa_farm_location'
                        ])) {
                        isValid = false;
                    }
                    break;

                case 'Farmworker/Laborer':
                    if (!validateEditLivelihoodRequiredFields(['edit_rsbsa_farmworker_type'])) {
                        isValid = false;
                    }
                    break;

                case 'Fisherfolk':
                    if (!validateEditLivelihoodRequiredFields(['edit_rsbsa_fisherfolk_activity'])) {
                        isValid = false;
                    }
                    break;

                case 'Agri-youth':
                    if (!validateEditLivelihoodRequiredFields([
                            'edit_rsbsa_agriyouth_household',
                            'edit_rsbsa_agriyouth_training',
                            'edit_rsbsa_agriyouth_participation'
                        ])) {
                        isValid = false;
                    }
                    break;
            }

            // Validate land area if provided
            const landAreaInput = document.getElementById('edit_rsbsa_farmer_land_area');
            if (landAreaInput && landAreaInput.value) {
                const landArea = parseFloat(landAreaInput.value);
                if (isNaN(landArea) || landArea < 0) {
                    landAreaInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Land area must be a positive number';
                    landAreaInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
                if (landArea > 1000) {
                    landAreaInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Land area cannot exceed 1000 hectares';
                    landAreaInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            }

            return isValid;
        }

        /**
         * Validate livelihood-specific required fields in edit form
         */
        function validateEditLivelihoodRequiredFields(fieldIds) {
            let allValid = true;

            fieldIds.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (input && (!input.value || input.value.trim() === '')) {
                    input.classList.add('is-invalid');
                    const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                    if (existingFeedback) existingFeedback.remove();

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = input.previousElementSibling?.textContent?.replace('*', '') +
                        ' is required';
                    input.parentNode.appendChild(errorDiv);
                    allValid = false;
                }
            });

            return allValid;
        }

        /**
         * Format contact number in add modal
         */
        function formatRsbsaContactNumber(input) {
            let value = input.value.replace(/\D/g, '');

            if (value.startsWith('63')) {
                value = '+' + value;
            } else if (value.match(/^9\d{9}$/)) {
                value = '0' + value;
            }

            input.value = value;
        }

        /**
         * Auto-capitalize names in add form
         */
        function capitalizeRsbsaName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        }

        /**
         * Update remarks counter in add modal
         */
        function updateRsbsaRemarksCounter() {
            const textarea = document.getElementById('rsbsa_remarks');
            const charCount = document.getElementById('rsbsaCharCount');

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
        /**
         * Real-time validation for Add RSBSA modal
         */
        function setupAddRsbsaRealTimeValidation() {
            // Name fields validation
            const nameFields = [{
                    id: 'rsbsa_first_name',
                    pattern: /^[a-zA-Z\s\'-]*$/,
                    label: 'First Name'
                },
                {
                    id: 'rsbsa_middle_name',
                    pattern: /^[a-zA-Z\s\'-]*$/,
                    label: 'Middle Name'
                },
                {
                    id: 'rsbsa_last_name',
                    pattern: /^[a-zA-Z\s\'-]*$/,
                    label: 'Last Name'
                }
            ];

            nameFields.forEach(field => {
                const input = document.getElementById(field.id);
                if (input) {
                    input.addEventListener('input', function(e) {
                        validateFieldRealTime(this, field.pattern, field.label);
                    });
                    input.addEventListener('blur', function(e) {
                        if (this.value) validateFieldRealTime(this, field.pattern, field.label);
                    });
                }
            });

            // Contact number validation
            const contactInput = document.getElementById('rsbsa_contact_number');
            if (contactInput) {
                contactInput.addEventListener('input', function(e) {
                    const pattern = /^(\+639|09)\d{9}$/;
                    validateFieldRealTime(this, pattern, 'Contact Number', 'Must be 09XXXXXXXXX or +639XXXXXXXXX');
                });
            }

            // Address validation
            const addressInput = document.getElementById('rsbsa_address');
            if (addressInput) {
                addressInput.addEventListener('input', function(e) {
                    const pattern = /^[a-zA-Z0-9\s,.\'-]*$/;
                    validateFieldRealTime(this, pattern, 'Address');
                });
            }
        }

        /**
         * Real-time field validation helper
         */
        function validateFieldRealTime(input, pattern, fieldName, customMessage = null) {
            if (!input.value.trim()) {
                input.classList.remove('is-invalid');
                return;
            }

            if (!pattern.test(input.value)) {
                input.classList.add('is-invalid');
                input.style.borderColor = '#dc3545';

                // Remove existing feedback
                const existing = input.parentNode.querySelector('.invalid-feedback');
                if (existing) existing.remove();

                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback d-block';
                feedback.textContent = customMessage || `${fieldName} contains invalid characters`;
                input.parentNode.appendChild(feedback);
            } else {
                input.classList.remove('is-invalid');
                input.style.borderColor = '';

                const existing = input.parentNode.querySelector('.invalid-feedback');
                if (existing) existing.remove();
            }
        }

        // Initialize when add modal is opened
        document.addEventListener('DOMContentLoaded', function() {
            const addModal = document.getElementById('addRsbsaModal');
            if (addModal) {
                addModal.addEventListener('show.bs.modal', function() {
                    setTimeout(setupAddRsbsaRealTimeValidation, 100);
                });
            }
        });
        /**
         * Real-time validation for Edit RSBSA modal
         */
        function setupEditRsbsaRealTimeValidation() {
            // Name fields validation
            const nameFields = [{
                    id: 'edit_rsbsa_first_name',
                    pattern: /^[a-zA-Z\s\'-]*$/,
                    label: 'First Name'
                },
                {
                    id: 'edit_rsbsa_middle_name',
                    pattern: /^[a-zA-Z\s\'-]*$/,
                    label: 'Middle Name'
                },
                {
                    id: 'edit_rsbsa_last_name',
                    pattern: /^[a-zA-Z\s\'-]*$/,
                    label: 'Last Name'
                }
            ];

            nameFields.forEach(field => {
                const input = document.getElementById(field.id);
                if (input) {
                    input.addEventListener('input', function(e) {
                        validateEditFieldRealTime(this, field.pattern, field.label);
                    });
                    input.addEventListener('blur', function(e) {
                        if (this.value) validateEditFieldRealTime(this, field.pattern, field.label);
                    });
                }
            });

            // Contact number validation
            const contactInput = document.getElementById('edit_rsbsa_contact_number');
            if (contactInput) {
                contactInput.addEventListener('input', function(e) {
                    const pattern = /^(\+639|09)\d{9}$/;
                    validateEditFieldRealTime(this, pattern, 'Contact Number',
                        'Must be 09XXXXXXXXX or +639XXXXXXXXX');
                });
            }

            // Address validation
            const addressInput = document.getElementById('edit_rsbsa_address');
            if (addressInput) {
                addressInput.addEventListener('input', function(e) {
                    const pattern = /^[a-zA-Z0-9\s,.\'-]*$/;
                    validateEditFieldRealTime(this, pattern, 'Address');
                });
            }

            // Farm location validation (for farmers)
            const farmLocationInput = document.getElementById('edit_rsbsa_farm_location');
            if (farmLocationInput) {
                farmLocationInput.addEventListener('input', function(e) {
                    const pattern = /^[a-zA-Z0-9\s,.\'-]*$/;
                    validateEditFieldRealTime(this, pattern, 'Farm Location');
                });
            }
        }

        /**
         * Real-time field validation helper for edit modal
         */
        function validateEditFieldRealTime(input, pattern, fieldName, customMessage = null) {
            if (!input.value.trim()) {
                input.classList.remove('is-invalid');
                return;
            }

            if (!pattern.test(input.value)) {
                input.classList.add('is-invalid');
                input.style.borderColor = '#dc3545';

                const existing = input.parentNode.querySelector('.invalid-feedback');
                if (existing) existing.remove();

                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback d-block';
                feedback.textContent = customMessage || `${fieldName} contains invalid characters`;
                input.parentNode.appendChild(feedback);
            } else {
                input.classList.remove('is-invalid');
                input.style.borderColor = '';

                const existing = input.parentNode.querySelector('.invalid-feedback');
                if (existing) existing.remove();
            }
        }

        // Initialize when edit modal is opened
        document.addEventListener('DOMContentLoaded', function() {
            // const editModal = document.getElementById('editRsbsaModal');
            // if (editModal) {
            //     editModal.addEventListener('show.bs.modal', function() {
            //         setTimeout(setupEditRsbsaRealTimeValidation, 100);
            //     });
            // }
            const addModal = document.getElementById('addRsbsaModal');
            if (addModal) {
                addModal.addEventListener('show.bs.modal', function() {
                    setTimeout(setupAddRsbsaRealTimeValidation, 100);
                });
            }

            const editModal = document.getElementById('editRsbsaModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function() {
                    setTimeout(setupEditRsbsaRealTimeValidation, 100);
                });
            }

            // Auto-highlight and open item from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const highlightId = urlParams.get('highlight');
            if (highlightId) {
                setTimeout(() => {
                    const row = document.querySelector(`tr[data-id="${highlightId}"]`);
                    if (row) {
                        // Scroll to the row
                        row.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        // Add highlight animation
                        row.style.backgroundColor = '#fff3cd';
                        row.style.transition = 'background-color 2s';
                        setTimeout(() => {
                            row.style.backgroundColor = '';
                        }, 2000);

                        // Auto-open the view modal
                        viewApplication(parseInt(highlightId));
                    }
                }, 500);
            }
        });

        // Toast notification function
        function showToast(type, message) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const iconMap = {
                'success': { icon: 'fas fa-check-circle', color: 'success' },
                'error': { icon: 'fas fa-exclamation-circle', color: 'danger' },
                'warning': { icon: 'fas fa-exclamation-triangle', color: 'warning' },
                'info': { icon: 'fas fa-info-circle', color: 'info' }
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
    </script>
@endsection
