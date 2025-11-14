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
            <div></div>
            <div class="text-center flex-fill">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-ship me-2"></i>BoatR Applications
                </h6>
            </div>
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
                                                            <i class="fas fa-file-image text-info"></i>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($inspectionDocs > 0)
                                                    <div class="boatr-mini-doc"
                                                        onclick="viewDocuments({{ $registration->id }})"
                                                        title="Inspection Documents">
                                                        <div class="boatr-mini-doc-icon">
                                                            <i class="fas fa-clipboard-check text-success"></i>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($annexesDocs > 0)
                                                    <div class="boatr-mini-doc"
                                                        onclick="viewDocuments({{ $registration->id }})" title="Annexes">
                                                        <div class="boatr-mini-doc-icon">
                                                            <i class="fas fa-folder text-warning"></i>
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

                                        @if (!$registration->inspection_completed)
                                            <button class="btn btn-sm btn-outline-success"
                                                onclick="showInspectionModal({{ $registration->id }})"
                                                title="Complete Inspection">
                                                <i class="fas fa-clipboard-check me-1"></i>Inspection
                                            </button>
                                        @endif
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
                                    <p class="mb-1"><strong>Barangay:</strong> <span id="updateRegBarangay"></span></p>
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

    <!-- Enhanced Document Viewer Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="documentModalLabel">
                        <i class="fas fa-file-alt me-2"></i>Application Documents
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
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
                        <i class="fas fa-times me-1"></i>Close
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
                        <!-- Application Info -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Application #:</strong> <span id="annexAppNumber"></span><br>
                                        <strong>Applicant:</strong> <span id="annexApplicantName"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Vessel Name:</strong> <span id="annexVesselName"></span><br>
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

        /* Enhanced modal styling */
        .modal-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
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
            border: 2px solid  #1cc88a;
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
        .modal-backdrop + .modal-backdrop {
            display: none !important;
        }

        /* Fix z-index for nested modals */
        .modal:nth-of-type(1) {
            z-index: 1050;
        }

        .modal:nth-of-type(1) ~ .modal-backdrop {
            z-index: 1049;
        }

        .modal:nth-of-type(2) {
            z-index: 1060;
        }

        .modal:nth-of-type(2) ~ .modal-backdrop {
            z-index: 1059;
        }

        /* Specific nested modal handling */
        .modal.show ~ .modal {
            z-index: 1060 !important;
        }

        .modal.show ~ .modal ~ .modal-backdrop {
            z-index: 1059 !important;
        }

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
        .modal + .modal-backdrop + .modal-backdrop {
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
        function showUpdateModal(id, currentStatus) {
            const modal = new bootstrap.Modal(document.getElementById('updateModal'));
            modal.show();

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
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Failed to load application details');

                currentData[id] = data;

                document.getElementById('updateModalLoading').style.display = 'none';
                document.getElementById('updateModalContent').style.display = 'block';
                document.getElementById('updateForm').style.display = 'block';
                document.getElementById('updateStatusBtn').style.display = 'inline-block';

                document.getElementById('updateRegistrationId').value = id;
                document.getElementById('updateRegId').textContent = data.id;
                document.getElementById('updateRegNumber').textContent = data.application_number;
                document.getElementById('updateRegName').textContent = data.full_name;
                document.getElementById('updateRegBarangay').textContent = data.barangay || 'N/A';
                document.getElementById('updateRegVessel').textContent = data.vessel_name;
                document.getElementById('updateRegFishR').textContent = data.fishr_number;
                document.getElementById('updateRegBoatType').textContent = data.boat_type;

                document.getElementById('updateRegCurrentStatus').innerHTML =
                    `<span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;

                document.getElementById('updateRegInspection').innerHTML = data.inspection_completed ?
                    '<span class="badge bg-success">Completed</span>' :
                    '<span class="badge bg-warning">Pending</span>';

                document.getElementById('newStatus').value = currentStatus;
                document.getElementById('remarks').value = '';
                document.getElementById('remarksCount').textContent = '0';
            })
            .catch(error => {
                console.error('Error loading application details:', error);
                showToast('error', 'Failed to load application details: ' + error.message);
                modal.hide();
            });
        }

        function updateRegistrationStatus() {
            const id = document.getElementById('updateRegistrationId').value;
            const newStatus = document.getElementById('newStatus').value;
            const remarks = document.getElementById('remarks').value;

            if (!newStatus) {
                showToast('warning', 'Please select a status before updating');
                return;
            }

            const statusOption = document.querySelector(`#newStatus option[value="${newStatus}"]`);
            if (!statusOption) {
                showToast('error', 'Invalid status selected');
                return;
            }
            
            const statusText = statusOption.textContent.trim();

            showConfirmationToast(
                'Update Application Status',
                `Are you sure you want to change the status to: "${statusText}"?${remarks ? '\n\nRemarks: ' + remarks : ''}`,
                () => proceedWithStatusUpdate(id, newStatus, remarks)
            );
        }

        function proceedWithStatusUpdate(id, newStatus, remarks) {
            const updateBtn = document.getElementById('updateStatusBtn');
            if (!updateBtn) {
                showToast('error', 'UI error: Button not found');
                return;
            }

            const originalContent = updateBtn.innerHTML;
            updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            updateBtn.disabled = true;

            fetch(`/admin/boatr/requests/${id}/status`, {
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
                if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                return response.json();
            })
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Update failed');

                showToast('success', data.message || 'Status updated successfully');
                
                if (data.registration) {
                    updateTableRow(id, data.registration);
                }

                const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                if (modal) modal.hide();

                setTimeout(() => window.location.reload(), 1500);
            })
            .catch(error => {
                console.error('Error updating status:', error);
                showToast('error', 'Update failed: ' + error.message);
            })
            .finally(() => {
                updateBtn.innerHTML = originalContent;
                updateBtn.disabled = false;
            });
        }

        // ========== INSPECTION ==========
        function showInspectionModal(id) {
            document.getElementById('inspectionRegistrationId').value = id;
            document.getElementById('supporting_document').value = '';
            document.getElementById('inspection_notes').value = '';
            document.getElementById('approve_application').checked = false;
            document.getElementById('notesCount').textContent = '0';

            document.getElementById('supporting_document').classList.remove('is-invalid');
            document.getElementById('documentError').textContent = '';

            const modal = new bootstrap.Modal(document.getElementById('inspectionModal'));
            modal.show();
        }

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

            showConfirmationToast(
                'Complete Inspection',
                `Are you sure you want to complete the inspection?\n\nFile: ${fileInput.files[0].name}`,
                () => proceedWithInspection(id, fileInput, notes, autoApprove)
            );
        }

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

        // ========== VIEW REGISTRATION ==========
        function viewRegistration(id) {
            const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
            modal.show();

            document.getElementById('registrationDetailsLoading').style.display = 'block';
            document.getElementById('registrationDetails').style.display = 'none';

            fetch(`/admin/boatr/requests/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Failed to load application details');

                document.getElementById('registrationDetailsLoading').style.display = 'none';
                document.getElementById('registrationDetails').style.display = 'block';

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

                const userDocsCount = data.user_documents ? data.user_documents.length : 0;
                const inspectionDocsCount = data.inspection_documents ? data.inspection_documents.length : 0;

                let documentHtml = '';
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
                                    <p><strong>Barangay:</strong> ${data.barangay || 'N/A'}</p>
                                    <p><strong>Contact Number:</strong> ${data.contact_number || 'N/A'}</p>
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
                                    <p><strong>Inspection:</strong> ${data.inspection_completed ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span>' : '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pending</span>'}</p>
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

        // ========== DOCUMENT VIEWING ==========
        function viewDocuments(id) {
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

                let docToPreview = null;
                let docType = '';
                let docIndex = 0;

                if (data.user_documents && data.user_documents.length > 0) {
                    docToPreview = data.user_documents[0];
                    docType = 'user';
                    docIndex = 0;
                } else if (data.inspection_documents && data.inspection_documents.length > 0) {
                    docToPreview = data.inspection_documents[0];
                    docType = 'inspection';
                    docIndex = 0;
                } else if (data.annexes && data.annexes.length > 0) {
                    previewAnnex(id, data.annexes[0].id);
                    return;
                }

                if (docToPreview) {
                    previewDocument(id, docType, docIndex);
                } else {
                    showToast('info', 'No documents available for this application');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Failed to load documents: ' + error.message);
            });
        }

        function previewDocument(id, type, index) {
            const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
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

                const fileExtension = data.document_type?.toLowerCase() ||
                    data.document_name.split('.').pop().toLowerCase();
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

        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return 'Unknown size';
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }

        // ========== ANNEXES MANAGEMENT ==========
        function showAnnexesModal(id) {
            const modal = new bootstrap.Modal(document.getElementById('annexesModal'));
            modal.show();

            document.getElementById('annexesLoading').style.display = 'block';
            document.getElementById('annexesContent').style.display = 'none';

            loadAnnexesData(id);
        }

        function loadAnnexesData(id) {
            fetch(`/admin/boatr/requests/${id}`, {
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
                if (!data.success) throw new Error(data.message || 'Failed to load data');

                document.getElementById('annexesLoading').style.display = 'none';
                document.getElementById('annexesContent').style.display = 'block';

                document.getElementById('annexRegistrationId').value = id;
                document.getElementById('annexAppNumber').textContent = data.application_number;
                document.getElementById('annexApplicantName').textContent = data.full_name;
                document.getElementById('annexVesselName').textContent = data.vessel_name || 'N/A';
                document.getElementById('annexStatus').innerHTML =
                    `<span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;

                loadExistingAnnexes(id);
                resetAnnexForm();
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

        function loadExistingAnnexes(id) {
            fetch(`/admin/boatr/requests/${id}/annexes`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
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

        function uploadAnnex() {
            const id = document.getElementById('annexRegistrationId').value;
            const fileInput = document.getElementById('annexFile');
            const title = document.getElementById('annexTitle').value.trim();
            const description = document.getElementById('annexDescription').value.trim();

            if (!fileInput.files[0]) {
                showValidationError('annexFile', 'annexFileError', 'Please select a file');
                return;
            }

            if (!title) {
                showValidationError('annexTitle', 'annexTitleError', 'Please enter a document title');
                return;
            }

            if (fileInput.files[0].size > 10 * 1024 * 1024) {
                showValidationError('annexFile', 'annexFileError', 'File size must be less than 10MB');
                return;
            }

            clearValidationErrors();

            showConfirmationToast(
                'Upload Annex',
                `Are you sure you want to upload this annex?\n\nFile: ${fileInput.files[0].name}`,
                () => proceedWithAnnexUpload(id, fileInput, title, description)
            );
        }

        function proceedWithAnnexUpload(id, fileInput, title, description) {
            const uploadBtn = document.querySelector('[onclick="uploadAnnex()"]');
            if (!uploadBtn) {
                showToast('error', 'UI error: Button not found');
                return;
            }

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
                if (!data.success) throw new Error(data.message || 'Failed to upload annex');

                showToast('success', 'Annex uploaded successfully');
                resetAnnexForm();
                loadExistingAnnexes(id);
                
                setTimeout(() => window.location.reload(), 1500);
            })
            .catch(error => {
                console.error('Error uploading annex:', error);
                showToast('error', 'Upload failed: ' + error.message);
            })
            .finally(() => {
                uploadBtn.innerHTML = originalContent;
                uploadBtn.disabled = false;
            });
        }

        function previewAnnex(registrationId, annexId) {
            const previewModal = document.getElementById('documentPreviewModal');
            const annexesModal = document.getElementById('annexesModal');
            const modal = new bootstrap.Modal(previewModal);

            previewModal.style.zIndex = '1060';

            const annexesBackdrop = document.querySelector('.modal-backdrop');
            if (annexesBackdrop) {
                annexesBackdrop.style.zIndex = '1058';
            }

            previewModal.addEventListener('hidden.bs.modal', function() {
                previewModal.style.zIndex = '';
                if (annexesBackdrop) {
                    annexesBackdrop.style.zIndex = '';
                }
            }, { once: true });

            modal.show();

            document.getElementById('documentPreview').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading annex preview...</p>
                </div>
            `;

            fetch(`/admin/boatr/requests/${registrationId}/annexes/${annexId}/preview`, {
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
                if (!data.success) throw new Error(data.message || 'Failed to load preview');

                document.getElementById('documentPreviewTitle').innerHTML =
                    `<i class="fas fa-folder me-2"></i>${data.title}`;

                const fileExtension = data.file_extension?.toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension);
                const isPdf = fileExtension === 'pdf';

                if (isPdf) {
                    document.getElementById('documentPreview').innerHTML = `
                        <div class="text-center">
                            <embed src="${data.file_url}" type="application/pdf" width="100%" height="600px"
                                   style="border: none; border-radius: 8px;" />
                            <div class="mt-2">
                                <a href="${data.file_url}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>Open in new tab
                                </a>
                            </div>
                        </div>
                    `;
                } else if (isImage) {
                    document.getElementById('documentPreview').innerHTML = `
                        <div class="text-center">
                            <img src="${data.file_url}" class="img-fluid" alt="Annex preview"
                                 style="max-height: 600px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);" />
                        </div>
                    `;
                } else {
                    document.getElementById('documentPreview').innerHTML = `
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <h5>Preview not available for this file type</h5>
                            <p>File type: ${fileExtension?.toUpperCase() || 'Unknown'}</p>
                            <a href="${data.file_url}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-download me-1"></i>Download to view
                            </a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading annex preview:', error);
                document.getElementById('documentPreview').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <h5>Error loading annex preview</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            });
        }

        function downloadAnnex(registrationId, annexId) {
            window.open(`/admin/boatr/requests/${registrationId}/annexes/${annexId}/download`, '_blank');
        }

        function deleteAnnex(registrationId, annexId) {
            showConfirmationToast(
                'Delete Annex',
                'Are you sure you want to delete this annex?\n\nThis action cannot be undone.',
                () => proceedWithAnnexDelete(registrationId, annexId)
            );
        }

        function proceedWithAnnexDelete(registrationId, annexId) {
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
                    annexElement.remove();
                }

                const annexesList = document.getElementById('annexesList');
                if (annexesList && !annexesList.querySelector('.document-item')) {
                    loadExistingAnnexes(registrationId);
                }

                setTimeout(() => window.location.reload(), 1500);
            })
            .catch(error => {
                console.error('Error deleting annex:', error);
                showToast('error', 'Delete failed: ' + error.message);
            });
        }

        function resetAnnexForm() {
            document.getElementById('annexFile').value = '';
            document.getElementById('annexTitle').value = '';
            document.getElementById('annexDescription').value = '';
            document.getElementById('annexDescCount').textContent = '0';
            clearValidationErrors();
        }

        function showValidationError(inputId, errorId, message) {
            const input = document.getElementById(inputId);
            const error = document.getElementById(errorId);

            if (input) input.classList.add('is-invalid');
            if (error) error.textContent = message;
        }

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

        console.log('BoatR Admin utilities available via window.BoatRAdmin');
    </script>
@endsection