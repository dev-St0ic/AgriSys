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
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $rejectedCount }}</div>
                    <div class="stat-label text-danger">Rejected</div>
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

                <div class="row">
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Status</option>
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
                        <a href="{{ route('admin.fishr.requests') }}" class="btn btn-secondary btn-sm w-100">
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
            <div></div>
            <div class="text-center flex-fill">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-fish me-2"></i>FishR Applications
                </h6>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.fishr.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Export CSV
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="showAddFishrModal()">
                    <i class="fas fa-plus me-2"></i>Add Registration
                </button>
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
                                                        <i class="fas fa-file-image text-info"></i>
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

                                        <button class="btn btn-sm btn-outline-success"
                                            onclick="showUpdateModal({{ $registration->id }}, '{{ $registration->status }}')"
                                            title="Update Status">
                                            <i class="fas fa-edit"></i> Update
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
                                                        <i class="fas fa-pencil-alt text-warning me-2"></i>Edit Information
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                        onclick="deleteRegistration({{ $registration->id }}, '{{ $registration->registration_number }}')">
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


    <!-- Update Status Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Update Registration Status
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                                    <p class="mb-1"><strong>Registration #:</strong> <span id="updateRegNumber"></span>
                                    </p>
                                    <p class="mb-1"><strong>Name:</strong> <span id="updateRegName"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Barangay:</strong> <span id="updateRegBarangay"></span></p>
                                    <p class="mb-1"><strong>Livelihood:</strong> <span id="updateRegLivelihood"></span>
                                    </p>
                                    <p class="mb-1"><strong>Current Status:</strong> <span
                                            id="updateRegCurrentStatus"></span></p>
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
                                <option value="under_review">Under Review</option>
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

    <!-- Registration Details Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-fish me-2"></i>Registration Details
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

    <!-- Enhanced Document Viewer Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="documentModalLabel">
                        <i class="fas fa-file-alt me-2"></i>Supporting Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="documentViewer">
                    <!-- Document will be loaded here -->
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Annexes Modal -->
    <div class="modal fade" id="annexesModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-folder-plus me-2"></i>Manage Annexes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Loading State -->
                    <div id="annexesLoading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading annexes...</p>
                    </div>

                    <!-- Content -->
                    <div id="annexesContent">
                        <!-- Registration Info -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Registration #:</strong> <span id="annexRegNumber"></span><br>
                                        <strong>Applicant:</strong> <span id="annexApplicantName"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Barangay:</strong> <span id="annexBarangay"></span><br>
                                        <strong>Status:</strong> <span id="annexStatus"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload New Annex -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-upload me-2"></i>Upload New Annex
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="annexUploadForm" enctype="multipart/form-data">
                                    <input type="hidden" id="annexRegistrationId">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="annexFile" class="form-label">Select File *</label>
                                                <input type="file" class="form-control" id="annexFile"
                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif" required>
                                                <div class="invalid-feedback" id="annexFileError"></div>
                                                <small class="text-muted">Supported formats: PDF, DOC, DOCX, JPG, PNG, GIF
                                                    (Max: 10MB)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="annexTitle" class="form-label">Document Title *</label>
                                                <input type="text" class="form-control" id="annexTitle"
                                                    placeholder="e.g., Additional Certificate, Supporting Document"
                                                    required>
                                                <div class="invalid-feedback" id="annexTitleError"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="annexDescription" class="form-label">Description</label>
                                        <textarea class="form-control" id="annexDescription" rows="3"
                                            placeholder="Brief description of the document (optional)"></textarea>
                                        <small class="text-muted"><span id="annexDescCount">0</span>/500
                                            characters</small>
                                    </div>
                                    <button type="button" class="btn btn-primary" onclick="uploadAnnex()">
                                        <i class="fas fa-upload me-1"></i>Upload Annex
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Existing Annexes -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit FishR Registration Modal -->
    <div class="modal fade" id="editFishrModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-pencil-alt me-2"></i>Edit Registration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editFishrForm" class="needs-validation">
                        @csrf
                        @method('PUT')

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
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="edit_sex" class="form-label">Sex <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_sex" name="sex" required>
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Preferred not to say">Preferred not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="edit_contact_number" class="form-label">Contact Number <span
                                                class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="edit_contact_number"
                                            name="contact_number" required placeholder="09XXXXXXXXX"
                                            pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <div class="form-text">09XXXXXXXXX or +639XXXXXXXXX</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="edit_email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="edit_email" name="email"
                                            maxlength="254">
                                        <div class="form-text">For status notifications</div>
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
                                <div class="mb-3">
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
                            </div>
                        </div>

                        <!-- Status Information (Read-only) -->
                        <div class="card mb-3 bg-light">
                            <div class="card-header bg-light border-0">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Registration Status (Read-only)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <small class="text-muted">Current Status:</small>
                                        <div>
                                            <span id="edit_status_display" class="badge bg-secondary"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <small class="text-muted">Date Applied:</small>
                                        <div id="edit_date_applied"></div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <small class="text-muted">Last Updated:</small>
                                        <div id="edit_last_updated"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Note:</strong> You can only edit personal and location information here.
                            To update the registration status, use the "Update Status" button from the main table.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="editSubmitBtn" onclick="handleEditFishrSubmit()">
                        <i class="fas fa-save me-2"></i>Save Changes
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
    <!-- Add FishR Registration Modal -->
    <div class="modal fade" id="addFishrModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-fish me-2"></i>Add New FishR Registration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addFishrForm" enctype="multipart/form-data">
                        <!-- Personal Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="fishr_first_name" class="form-label">First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="fishr_first_name" required
                                            maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="fishr_middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="fishr_middle_name"
                                            maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="fishr_last_name" class="form-label">Last Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="fishr_last_name" required
                                            maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="fishr_name_extension" class="form-label">Extension</label>
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
                                        <label for="fishr_sex" class="form-label">Sex <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="fishr_sex" required>
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Preferred not to say">Preferred not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="fishr_contact_number" class="form-label">Contact Number <span
                                                class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="fishr_contact_number" required
                                            placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <div class="form-text">09XXXXXXXXX or +639XXXXXXXXX</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="fishr_email" class="form-label">Email (Optional)</label>
                                        <input type="email" class="form-control" id="fishr_email" maxlength="254">
                                        <div class="form-text">For status notifications</div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="fishr_user_id" class="form-label">Link to User Account
                                            (Optional)</label>
                                        <input type="number" class="form-control" id="fishr_user_id"
                                            placeholder="Enter User ID if exists">
                                        <div class="form-text">Leave blank if not associated with any user account</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="fishr_barangay" class="form-label">Barangay <span
                                                class="text-danger">*</span></label>
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

                        <!-- Livelihood Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-fish me-2"></i>Livelihood Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fishr_main_livelihood" class="form-label">Main Livelihood <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="fishr_main_livelihood" required
                                            onchange="toggleOtherLivelihood()">
                                            <option value="">Select Livelihood</option>
                                            <option value="capture">Capture Fishing</option>
                                            <option value="aquaculture">Aquaculture</option>
                                            <option value="vending">Fish Vending</option>
                                            <option value="processing">Fish Processing</option>
                                            <option value="others">Others</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3" id="other_livelihood_container" style="display: none;">
                                        <label for="fishr_other_livelihood" class="form-label">Specify Other Livelihood
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="fishr_other_livelihood"
                                            maxlength="255" placeholder="Please specify...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supporting Document -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-file-upload me-2"></i>Supporting Document (Optional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fishr_supporting_document" class="form-label">Upload Document</label>
                                        <input type="file" class="form-control" id="fishr_supporting_document"
                                            accept="image/*,.pdf"
                                            onchange="previewFishrDocument('fishr_supporting_document', 'fishr_doc_preview')">
                                        <div class="form-text">Accepted: JPG, PNG, PDF (Max 5MB)</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="fishr_doc_preview" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Status -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Registration Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fishr_status" class="form-label">Initial Status <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="fishr_status" required>
                                            <option value="under_review" selected>Under Review</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fishr_remarks" class="form-label">Remarks (Optional)</label>
                                        <textarea class="form-control" id="fishr_remarks" rows="3" maxlength="1000"
                                            placeholder="Any notes or comments..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddFishr()">
                        <i class="fas fa-save me-1"></i>Create Registration
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
        }

        .annex-item:hover {
            background: #e9ecef;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .annex-title {
            color: #6f42c1;
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
            document.getElementById('updateRegId').innerHTML = `
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
                    document.getElementById('updateRegId').textContent = data.registration_number;
                    document.getElementById('updateRegNumber').textContent = data.registration_number;
                    document.getElementById('updateRegName').textContent = data.full_name;
                    document.getElementById('updateRegBarangay').textContent = data.barangay;
                    document.getElementById('updateRegLivelihood').textContent = data.livelihood_description;

                    // Show current status with badge styling
                    const currentStatusElement = document.getElementById('updateRegCurrentStatus');
                    currentStatusElement.innerHTML = `
                    <span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;

                    // Set form values and store original values for comparison
                    const statusSelect = document.getElementById('newStatus');
                    const remarksTextarea = document.getElementById('remarks');

                    statusSelect.value = data.status;
                    statusSelect.dataset.originalStatus = data.status; // Store original status

                    remarksTextarea.value = data.remarks || '';
                    remarksTextarea.dataset.originalRemarks = data.remarks || ''; // Store original remarks

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
                    showToast('error', 'Error loading registration details: ' + error.message);
                });
        }



        // View registration details
        function viewRegistration(id) {
            if (!id) {
                showToast('error', 'Invalid registration ID');
                return;
            }

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
                    console.log('Response:', response);

                    if (!response.success) {
                        throw new Error('Failed to load registration details');
                    }

                    const data = response.data;

                    if (!data) {
                        throw new Error('No registration data received');
                    }

                    // Build remarks HTML if exists
                    const remarksHtml = data.remarks ? `
                    <div class="col-12 mt-3">
                        <h6 class="border-bottom pb-2">Remarks</h6>
                        <div class="alert alert-info">
                            <p class="mb-1">${data.remarks}</p>
                            <small class="text-muted">
                                ${data.status_updated_at ? `Updated on ${data.status_updated_at}` : ''}
                                ${data.updated_by_name ? ` by ${data.updated_by_name}` : ''}
                            </small>
                        </div>
                    </div>` : '';

                    // Build document section HTML - ADD THIS SECTION
                    let documentHtml = '';
                    if (data.document_path) {
                        documentHtml = `
                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0" style="color: #495057;"><i class="fas fa-folder-open me-2" style="color: #6c757d;"></i>Supporting Document</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center p-3 border border-secondary rounded bg-light">
                                        <i class="fas fa-file-alt fa-3x mb-2" style="color: #6c757d;"></i>
                                        <h6>Supporting Document</h6>
                                        <span class="badge bg-secondary mb-2">Uploaded</span>
                                        <br>
                                        <button class="btn btn-sm btn-outline-info mt-2" onclick="viewDocument('${data.document_path}', 'FishR Registration #${data.registration_number} - Supporting Document')">
                                            <i class="fas fa-eye"></i> View Document
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    } else {
                        documentHtml = `
                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0" style="color: #495057;"><i class="fas fa-folder-open me-2" style="color: #6c757d;"></i>Supporting Document</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center p-3 border border-secondary rounded">
                                        <i class="fas fa-file-slash fa-3x mb-2" style="color: #6c757d;"></i>
                                        <h6>No Document Uploaded</h6>
                                        <span class="badge bg-secondary mb-2">Not Uploaded</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }

                    // Update modal content
                    document.getElementById('registrationDetails').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Personal Information</h6>
                            <p><strong>Registration #:</strong> ${data.registration_number || 'N/A'}</p>
                            <p><strong>Name:</strong> ${data.full_name || 'N/A'}</p>
                            <p><strong>Sex:</strong> ${data.sex || 'N/A'}</p>
                            <p><strong>Contact:</strong> ${data.contact_number || 'N/A'}</p>
                            <p><strong>Email:</strong> ${data.email || 'N/A'}</p>
                            <p><strong>Barangay:</strong> ${data.barangay || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Livelihood Information</h6>
                            <p><strong>Main Livelihood:</strong> ${data.livelihood_description || 'N/A'}</p>
                            ${data.other_livelihood ? `<p><strong>Other Livelihood:</strong> ${data.other_livelihood}</p>` : ''}
                            <p><strong>Current Status:</strong>
                                <span class="badge bg-${data.status_color}">${data.formatted_status}</span>
                            </p>
                            <p><strong>Date Applied:</strong> ${data.created_at || 'N/A'}</p>
                            <p><strong>Last Updated:</strong> ${data.updated_at || 'N/A'}</p>
                        </div>
                        ${documentHtml}
                        ${remarksHtml}
                    </div>`;
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', error.message || 'Error loading registration details. Please try again.');
                    document.getElementById('registrationDetails').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${error.message || 'Error loading registration details. Please try again.'}
                    </div>`;
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

        // Function to check for changes and provide visual feedback
        function checkForChanges() {
            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');

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

        // Load existing annexes
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

                            annexesHtml += `
                            <div class="document-item border rounded p-3 mb-3" id="annex-${annex.id}">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-1 text-primary">${annex.title}</h6>
                                        <p class="mb-1 text-muted small">${annex.description || 'No description'}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>Uploaded: ${uploadDate}
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-file me-1"></i>Size: ${formatFileSize(annex.file_size)}
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    onclick="previewAnnex(${id}, ${annex.id})" title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success"
                                                    onclick="downloadAnnex(${id}, ${annex.id})" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteAnnex(${id}, ${annex.id})" title="Delete">
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
                    document.getElementById('annexesList').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading annexes: ${error.message}
                    </div>
                `;
                });
        }

        // Upload annex with toast notifications
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
            if (fileInput.files[0].size > 10 * 1024 * 1024) {
                showValidationError('annexFile', 'annexFileError', 'File size must be less than 10MB');
                return;
            }

            // Clear validation errors
            clearValidationErrors();

            // Show confirmation toast instead of browser confirm
            showConfirmationToast(
                'Upload Annex',
                `Are you sure you want to upload this annex?\n\nFile: ${fileInput.files[0].name}`,
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

        // Delete annex with confirmation toast
        function deleteAnnex(registrationId, annexId) {
            showConfirmationToast(
                'Delete Annex',
                'Are you sure you want to delete this annex?\n\nThis action cannot be undone.',
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

                        // Remove from UI
                        const annexElement = document.getElementById(`annex-${annexId}`);
                        if (annexElement) {
                            annexElement.remove();
                        }

                        // Reload if no annexes left
                        const annexesList = document.getElementById('annexesList');
                        if (!annexesList.querySelector('.document-item')) {
                            loadExistingAnnexes(registrationId);
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

        // Updated updateRegistrationStatus with toast notifications
        function updateRegistrationStatus() {
            const id = document.getElementById('updateRegistrationId').value;
            const newStatus = document.getElementById('newStatus').value;
            const remarks = document.getElementById('remarks').value;

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

            // Only show confirmation - callback handles the actual update
            showConfirmationToast(
                'Confirm Update',
                `Update this registration with the following changes?\n\n${changesSummary.join('\n')}`,
                () => proceedWithStatusUpdate(id, newStatus, remarks)
            );
        }

        // And the separate proceedWithStatusUpdate function handles the fetch
        function proceedWithStatusUpdate(id, newStatus, remarks) {
            const updateButton = document.querySelector('#updateModal .btn-primary');
            const originalText = updateButton.innerHTML;
            updateButton.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
            updateButton.disabled = true;

            fetch(`/admin/fishr-registrations/${id}/status`, {
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
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    let data;

                    // Try to parse JSON response
                    if (contentType && contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        // If not JSON, get text for debugging
                        const text = await response.text();
                        console.error('Non-JSON response:', text);
                        throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                    }

                    // Check if response was successful
                    if (!response.ok) {
                        throw new Error(data.message ||
                        `Server error (${response.status}): ${response.statusText}`);
                    }

                    return data;
                })
                .then(response => {
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
                    console.error('Error details:', {
                        message: error.message,
                        stack: error.stack,
                        id: id,
                        newStatus: newStatus
                    });

                    let errorMessage = 'Error updating registration status: ';

                    // Provide more specific error messages
                    if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                        errorMessage += 'Network connection failed. Please check your internet connection.';
                    } else if (error.message.includes('500')) {
                        errorMessage += 'Server error occurred. Please check the server logs for details.';
                    } else if (error.message.includes('404')) {
                        errorMessage += 'Registration not found. It may have been deleted.';
                    } else if (error.message.includes('401') || error.message.includes('403')) {
                        errorMessage += 'You do not have permission to perform this action.';
                    } else {
                        errorMessage += error.message;
                    }

                    showToast('error', errorMessage);
                })
                .finally(() => {
                    updateButton.innerHTML = originalText;
                    updateButton.disabled = false;
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

        // Utility function to format file sizes
        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return 'Unknown size';

            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Character count for annex description
        document.addEventListener('DOMContentLoaded', function() {
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

        // Show add FishR modal
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

        // Toggle other livelihood field
        function toggleOtherLivelihood() {
            const livelihood = document.getElementById('fishr_main_livelihood').value;
            const container = document.getElementById('other_livelihood_container');
            const input = document.getElementById('fishr_other_livelihood');

            if (livelihood === 'others') {
                container.style.display = 'block';
                input.required = true;
            } else {
                container.style.display = 'none';
                input.required = false;
                input.value = '';
            }
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
                errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            input.classList.add('is-valid');
            return true;
        }

        // Real-time validation for email
        document.getElementById('fishr_email')?.addEventListener('input', function() {
            validateFishrEmail(this.value);
        });

        function validateFishrEmail(email) {
            const input = document.getElementById('fishr_email');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!email || email.trim() === '') {
                return true; // Email is optional
            }

            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!emailPattern.test(email.trim())) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Invalid email format';
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

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showToast('error', 'File size must not exceed 5MB');
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

        // Validate FishR form
        function validateFishrForm() {
            let isValid = true;

            // Required fields
            const requiredFields = [{
                    id: 'fishr_first_name',
                    label: 'First Name'
                },
                {
                    id: 'fishr_last_name',
                    label: 'Last Name'
                },
                {
                    id: 'fishr_sex',
                    label: 'Sex'
                },
                {
                    id: 'fishr_contact_number',
                    label: 'Contact Number'
                },
                {
                    id: 'fishr_barangay',
                    label: 'Barangay'
                },
                {
                    id: 'fishr_main_livelihood',
                    label: 'Main Livelihood'
                },
                {
                    id: 'fishr_status',
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

            // Validate other livelihood if selected
            const mainLivelihood = document.getElementById('fishr_main_livelihood').value;
            if (mainLivelihood === 'others') {
                const otherLivelihood = document.getElementById('fishr_other_livelihood');
                if (!otherLivelihood.value || otherLivelihood.value.trim() === '') {
                    const feedback = otherLivelihood.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();

                    otherLivelihood.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Please specify the other livelihood';
                    otherLivelihood.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            }

            // Validate contact number
            const contactNumber = document.getElementById('fishr_contact_number').value.trim();
            if (!validateFishrContactNumber(contactNumber)) {
                isValid = false;
            }

            // Validate email if provided
            const email = document.getElementById('fishr_email').value.trim();
            if (email && !validateFishrEmail(email)) {
                isValid = false;
            }

            return isValid;
        }

        // Submit add FishR form
        function submitAddFishr() {
            // Validate form
            if (!validateFishrForm()) {
                showToast('error', 'Please fix all validation errors before submitting');
                return;
            }

            // Prepare form data
            const formData = new FormData();

            formData.append('first_name', document.getElementById('fishr_first_name').value.trim());
            formData.append('middle_name', document.getElementById('fishr_middle_name').value.trim());
            formData.append('last_name', document.getElementById('fishr_last_name').value.trim());
            formData.append('name_extension', document.getElementById('fishr_name_extension').value);
            formData.append('sex', document.getElementById('fishr_sex').value);
            formData.append('contact_number', document.getElementById('fishr_contact_number').value.trim());
            formData.append('email', document.getElementById('fishr_email').value.trim());
            formData.append('barangay', document.getElementById('fishr_barangay').value);
            formData.append('main_livelihood', document.getElementById('fishr_main_livelihood').value);

            // Add other livelihood if 'others' is selected
            if (document.getElementById('fishr_main_livelihood').value === 'others') {
                formData.append('other_livelihood', document.getElementById('fishr_other_livelihood').value.trim());
            }

            formData.append('status', document.getElementById('fishr_status').value);
            formData.append('remarks', document.getElementById('fishr_remarks').value.trim());

            const userId = document.getElementById('fishr_user_id').value.trim();
            if (userId) {
                formData.append('user_id', userId);
            }
            // Add document if uploaded
            const docInput = document.getElementById('fishr_supporting_document');
            if (docInput.files && docInput.files[0]) {
                formData.append('supporting_document', docInput.files[0]);
            }

            // Find submit button
            const submitBtn = document.querySelector('#addFishrModal .btn-primary');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating...';
            submitBtn.disabled = true;

            // Submit to backend
            fetch('/admin/fishr-registrations/create', {
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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addFishrModal'));
                        modal.hide();

                        // Show success message
                        showToast('success', data.message || 'FishR registration created successfully');

                        // Reload page after short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Show validation errors
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = document.getElementById('fishr_' + field);
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
                        showToast('error', data.message || 'Failed to create FishR registration');
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

        // Show edit modal
        function showEditFishrModal(id) {
            const modal = new bootstrap.Modal(document.getElementById('editFishrModal'));

            // Initialize the modal with existing values
            initializeEditFishrModal(id);

            modal.show();
        }

        // Initialize edit modal with existing data
        function initializeEditFishrModal(id) {
            // Wait for modal to be fully loaded
            setTimeout(() => {
                // Fetch registration details
                fetch(`/admin/fishr-registrations/${id}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(response => {
                        if (!response.success) throw new Error(response.message || 'Failed to load data');

                        const data = response.data;

                        // Populate form fields - CHECK IF ELEMENT EXISTS FIRST
                        const firstNameEl = document.getElementById('edit_first_name');
                        if (!firstNameEl) {
                            throw new Error(
                                'Form elements not found. Make sure the modal is properly rendered.');
                        }

                        firstNameEl.value = data.first_name || '';

                        const middleNameEl = document.getElementById('edit_middle_name');
                        if (middleNameEl) middleNameEl.value = data.middle_name || '';

                        const lastNameEl = document.getElementById('edit_last_name');
                        if (lastNameEl) lastNameEl.value = data.last_name || '';

                        const extensionEl = document.getElementById('edit_name_extension');
                        if (extensionEl) extensionEl.value = data.name_extension || '';

                        const sexEl = document.getElementById('edit_sex');
                        if (sexEl) sexEl.value = data.sex || '';

                        const contactEl = document.getElementById('edit_contact_number');
                        if (contactEl) contactEl.value = data.contact_number || '';

                        const emailEl = document.getElementById('edit_email');
                        if (emailEl) emailEl.value = data.email || '';

                        const barangayEl = document.getElementById('edit_barangay');
                        if (barangayEl) barangayEl.value = data.barangay || '';

                        // Populate read-only fields
                        const statusEl = document.getElementById('edit_status_display');
                        if (statusEl) {
                            statusEl.innerHTML =
                                `<span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;
                        }

                        const dateEl = document.getElementById('edit_date_applied');
                        if (dateEl) dateEl.textContent = data.created_at || 'N/A';

                        const updatedEl = document.getElementById('edit_last_updated');
                        if (updatedEl) updatedEl.textContent = data.updated_at || 'N/A';

                        // Store original data for change detection
                        const originalData = {
                            first_name: data.first_name || '',
                            middle_name: data.middle_name || '',
                            last_name: data.last_name || '',
                            name_extension: data.name_extension || '',
                            sex: data.sex || '',
                            contact_number: data.contact_number || '',
                            email: data.email || '',
                            barangay: data.barangay || ''
                        };

                        const form = document.getElementById('editFishrForm');
                        if (form) {
                            form.dataset.originalData = JSON.stringify(originalData);
                            form.dataset.registrationId = id;
                        }

                        // Clear validation states
                        document.querySelectorAll('#editFishrForm .is-invalid').forEach(el => el.classList
                            .remove('is-invalid'));
                        document.querySelectorAll('#editFishrForm .invalid-feedback').forEach(el => el
                    .remove());

                        // Reset button state
                        const submitBtn = document.getElementById('editSubmitBtn');
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
                            submitBtn.disabled = false;
                            submitBtn.dataset.hasChanges = 'false';
                        }

                        // Add change detection
                        addEditFishrChangeDetection();

                    })
                    .catch(error => {
                        console.error('Error loading registration:', error);
                        showToast('error', 'Failed to load registration: ' + error.message);
                    });
            }, 300); // Wait for modal animation
        }

        // Add change detection to edit form
        function addEditFishrChangeDetection() {
            const form = document.getElementById('editFishrForm');
            const inputs = form.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                input.addEventListener('change', () => checkForEditFishrChanges());
                input.addEventListener('input', () => checkForEditFishrChanges());
            });
        }

        // Check for changes in edit form
        function checkForEditFishrChanges() {
            const form = document.getElementById('editFishrForm');
            const submitBtn = document.getElementById('editSubmitBtn');

            if (!form.dataset.originalData) return;

            const originalData = JSON.parse(form.dataset.originalData);
            let hasChanges = false;

            const fields = [
                'first_name', 'middle_name', 'last_name', 'name_extension',
                'sex', 'contact_number', 'email', 'barangay'
            ];

            fields.forEach(field => {
                const input = form.querySelector(`[name="${field}"]`);
                if (input && input.value !== originalData[field]) {
                    hasChanges = true;
                    input.classList.add('form-changed');
                } else if (input) {
                    input.classList.remove('form-changed');
                }
            });

            // Update button state
            if (hasChanges) {
                submitBtn.classList.remove('no-changes');
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
                submitBtn.disabled = false;
                submitBtn.dataset.hasChanges = 'true';
            } else {
                submitBtn.classList.add('no-changes');
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
                submitBtn.disabled = false;
                submitBtn.dataset.hasChanges = 'false';
            }
        }

        // Handle edit form submission
        function handleEditFishrSubmit() {
            const form = document.getElementById('editFishrForm');
            const submitBtn = document.getElementById('editSubmitBtn');
            const registrationId = form.dataset.registrationId;

            // Check if there are no changes
            if (submitBtn.dataset.hasChanges === 'false') {
                showToast('warning', 'No changes detected. Please modify the fields before saving.');
                return;
            }

            // Validate form
            if (!validateEditFishrForm()) {
                showToast('error', 'Please fix all validation errors');
                return;
            }

            // Show confirmation
            showConfirmationToast(
                'Confirm Update',
                'Are you sure you want to save the changes to this registration?',
                () => proceedWithEditFishr(form, registrationId)
            );
        }

        // Validate edit form
        function validateEditFishrForm() {
            const form = document.getElementById('editFishrForm');
            let isValid = true;

            const requiredFields = [{
                    name: 'first_name',
                    label: 'First Name'
                },
                {
                    name: 'last_name',
                    label: 'Last Name'
                },
                {
                    name: 'sex',
                    label: 'Sex'
                },
                {
                    name: 'contact_number',
                    label: 'Contact Number'
                },
                {
                    name: 'barangay',
                    label: 'Barangay'
                }
            ];

            requiredFields.forEach(field => {
                const input = form.querySelector(`[name="${field.name}"]`);
                if (input && (!input.value || input.value.trim() === '')) {
                    input.classList.add('is-invalid');
                    const feedback = input.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = field.label + ' is required';
                    input.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            });

            // Validate contact number
            const contactInput = form.querySelector('[name="contact_number"]');
            if (contactInput && contactInput.value.trim()) {
                const phoneRegex = /^(\+639|09)\d{9}$/;
                if (!phoneRegex.test(contactInput.value.trim())) {
                    contactInput.classList.add('is-invalid');
                    const feedback = contactInput.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)';
                    contactInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            }

            // Validate email if provided
            const emailInput = form.querySelector('[name="email"]');
            if (emailInput && emailInput.value.trim()) {
                const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailPattern.test(emailInput.value.trim())) {
                    emailInput.classList.add('is-invalid');
                    const feedback = emailInput.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.textContent = 'Invalid email format';
                    emailInput.parentNode.appendChild(errorDiv);
                    isValid = false;
                }
            }

            return isValid;
        }

        // Proceed with edit submission
        function proceedWithEditFishr(form, registrationId) {
            const submitBtn = document.getElementById('editSubmitBtn');

            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
            submitBtn.disabled = true;

            const formData = new FormData(form);

            fetch(`/admin/fishr-registrations/${registrationId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Close modal
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('editFishrModal'));
                        if (modalInstance) modalInstance.hide();

                        showToast('success', data.message || 'Registration updated successfully');

                        // Reload page
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Failed to update registration');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error: ' + error.message);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }


        // Auto-capitalize names in edit form
        function capitalizeEditFishrName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
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
        console.log('FishR Add Registration functionality loaded successfully');
    </script>
@endsection
