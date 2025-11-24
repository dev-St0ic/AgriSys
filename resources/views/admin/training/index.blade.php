{{-- resources/views/admin/training/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Training Applications - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-graduation-cap me-2 text-primary"></i>
        <span class="text-primary fw-bold">Training Applications</span>
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
            <form method="GET" action="{{ route('admin.training.requests') }}" id="filterForm">
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
                                placeholder="Search name, number, email..." value="{{ request('search') }}"
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
                    <i class="fas fa-graduation-cap me-2"></i>Training Applications
                </h6>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.training.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Export CSV
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="showAddTrainingModal()">
                    <i class="fas fa-plus me-2"></i>Add Registration
                </button>
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
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                onclick="viewDocument('{{ $training->document_path }}', 'Training Request - {{ $training->full_name }}')"
                                                title="View Document">
                                                <i class="fas fa-file-alt"></i> View
                                            </button>
                                        @else
                                            <div class="text-center">
                                                <i class="fas fa-folder-open text-muted"></i>
                                                <small class="text-muted d-block">No document</small>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="viewApplication({{ $training->id }})" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                         <button class="btn btn-sm btn-outline-warning"
                                            onclick="showEditTrainingModal({{ $training->id }})"
                                            title="Edit Personal Information">
                                            <i class="fas fa-pencil-alt"></i> Edit
                                        </button>


                                        <button class="btn btn-sm btn-outline-success"
                                            onclick="showUpdateModal({{ $training->id }}, '{{ $training->status }}')"
                                            title="Update Status">
                                            <i class="fas fa-edit"></i> Update
                                        </button>

                                         <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteApplication({{ $training->id }}, '{{ $training->application_number }}')" 
                                            title="Delete Application">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-graduation-cap fa-3x mb-3 text-gray-300"></i>
                                    <p>No training applications found</p>
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

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Update Application Status
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Application Info -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-2">
                                <i class="fas fa-info-circle me-2"></i>Application Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Application #:</strong> <span id="updateAppNumber"></span>
                                    </p>
                                    <p class="mb-1"><strong>Name:</strong> <span id="updateAppName"></span></p>
                                    <p class="mb-1"><strong>Email:</strong> <span id="updateAppEmail"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Contact:</strong> <span id="updateAppMobile"></span></p>
                                    <p class="mb-1"><strong>Training Type:</strong> <span id="updateAppTraining"></span>
                                    </p>
                                    <p class="mb-1"><strong>Current Status:</strong> <span
                                            id="updateAppCurrentStatus"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Form -->
                    <form id="updateForm">
                        <input type="hidden" id="updateApplicationId">
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
                                placeholder="Add any notes or comments about this status change..." maxlength="1000"></textarea>
                            <div class="form-text">Maximum 1000 characters</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateApplicationStatus()">Update
                        Status</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Details Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-graduation-cap me-2"></i>Application Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

    <!-- Enhanced Document Viewer Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="documentModalLabel">
                        <i class="fas fa-file-alt me-2"></i>Supporting Documents
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 d-flex justify-content-center" id="documentViewer">
                    <!-- Documents will be loaded here -->
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
    
   <!-- Add training Modal  -->
    <div class="modal fade" id="addTrainingModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-graduation-cap me-2"></i>Add New Training Application
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addTrainingForm" enctype="multipart/form-data">
                        <!-- Personal Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="training_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="training_first_name" required maxlength="100">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="training_middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="training_middle_name" maxlength="100">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="training_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="training_last_name" required maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="training_name_extension" class="form-label">Extension</label>
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
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="training_contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="training_contact_number" required placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <div class="form-text">09XXXXXXXXX or +639XXXXXXXXX</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="training_email" class="form-label">Email (Optional)</label>
                                        <input type="email" class="form-control" id="training_email" maxlength="254">
                                        <div class="form-text">For status notifications</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="training_user_id" class="form-label">Link to User Account (Optional)</label>
                                        <input type="number" class="form-control" id="training_user_id" placeholder="Enter User ID if exists">
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
                                        <label for="training_barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
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

                        <!-- Training Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-book me-2"></i>Training Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="training_type" class="form-label">Training Type <span class="text-danger">*</span></label>
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

                        <!-- Supporting Documents -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-file-upload me-2"></i>Supporting Documents (Optional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="training_supporting_documents" class="form-label">Upload Documents</label>
                                        <input type="file" class="form-control" id="training_supporting_document" accept="image/*,.pdf" onchange="previewTrainingDocument()">
                                        <div class="form-text">Accepted: JPG, PNG, PDF (Max 10MB)</div>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="training_doc_preview" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Application Status -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Application Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="training_status" class="form-label">Initial Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="training_status" required>
                                            <option value="under_review" selected>Under Review</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="training_remarks" class="form-label">Remarks (Optional)</label>
                                        <textarea class="form-control" id="training_remarks" rows="3" maxlength="1000" placeholder="Any notes or comments..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddTraining()">
                        <i class="fas fa-save me-1"></i>Create Application
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Training Modal -->
    <div class="modal fade" id="editTrainingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-pencil-alt me-2"></i>
                    <span id="editModalTitle">Edit Application</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editTrainingForm" class="needs-validation">
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
                                    <label for="edit_training_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_training_first_name" 
                                        name="first_name" required maxlength="100">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_training_middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="edit_training_middle_name" 
                                        name="middle_name" maxlength="100">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_training_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_training_last_name" 
                                        name="last_name" required maxlength="100">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="edit_training_extension" class="form-label">Extension</label>
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
                                <div class="col-md-4 mb-3">
                                    <label for="edit_training_contact" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="edit_training_contact" 
                                        name="contact_number" required 
                                        placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$" maxlength="20">
                                    <div class="form-text">09XXXXXXXXX or +639XXXXXXXXX</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_training_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit_training_email" 
                                        name="email" maxlength="254">
                                    <div class="form-text">For status notifications</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Application Number</label>
                                    <input type="text" class="form-control" id="edit_training_app_number" disabled>
                                    <small class="form-text text-muted">Auto-generated (cannot be changed)</small>
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
                                <div class="col-md-12 mb-3">
                                    <label for="edit_training_barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_training_barangay" name="barangay" required>
                                        <option value="">Select Barangay</option>
                                        @foreach ($barangays as $barangay)
                                            <option value="{{ $barangay }}">{{ $barangay }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Training Information Card (NOW EDITABLE) -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-book me-2"></i>Training Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="edit_training_type" class="form-label">Training Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_training_type" name="training_type" required>
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

                    <!-- Status Information (Read-only) -->
                    <div class="card mb-3 bg-light">
                        <div class="card-header bg-light border-0">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Status Information (Read-only)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Current Status:</small>
                                    <div id="edit_training_status_display"></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Date Applied:</small>
                                    <div id="edit_training_date_display"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Note:</strong> Changes to personal information, location, and training type will be saved. 
                        To update application status, use the "Update" button from the main table.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editTrainingSubmitBtn"
                    onclick="handleEditTrainingSubmit()">
                    <i class="fas fa-save me-2"></i>Save Changes
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
            border: 2px solid  #17a2b8;
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
            // Show loading state in modal
            document.getElementById('updateAppNumber').innerHTML = `
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>`;

            // First fetch the application details
            fetch(`/admin/training/requests/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    if (!response.success) {
                        throw new Error('Failed to load application details');
                    }

                    const data = response.data;
                    document.getElementById('updateApplicationId').value = id;

                    // Populate application info
                    document.getElementById('updateAppNumber').textContent = data.application_number;
                    document.getElementById('updateAppName').textContent = data.full_name;
                    document.getElementById('updateAppEmail').textContent = data.email || 'N/A';
                    document.getElementById('updateAppMobile').textContent = data.contact_number || 'N/A';
                    document.getElementById('updateAppTraining').textContent = data.training_type_display;
                    document.getElementById('updateAppCurrentStatus').innerHTML = `
                    <span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;

                    // Set form values and store originals for comparison
                    const statusSelect = document.getElementById('newStatus');
                    const remarksTextarea = document.getElementById('remarks');

                    statusSelect.value = data.status;
                    statusSelect.dataset.originalStatus = data.status;
                    remarksTextarea.value = data.remarks || '';
                    remarksTextarea.dataset.originalRemarks = data.remarks || '';

                    // Reset visual indicators
                    statusSelect.classList.remove('form-changed');
                    remarksTextarea.classList.remove('form-changed');
                    statusSelect.parentElement.classList.remove('change-indicator', 'changed');
                    remarksTextarea.parentElement.classList.remove('change-indicator', 'changed');

                    // Add change indicator classes
                    statusSelect.parentElement.classList.add('change-indicator');
                    remarksTextarea.parentElement.classList.add('change-indicator');

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('updateModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error loading application details: ' + error.message);
                });
        }

        // Enhanced update application status function
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

            showConfirmationToast(
                'Confirm Update',
                `Update this training application with the following changes?\n\n${changesSummary.join('\n')}`,
                () => proceedWithStatusUpdate(id, newStatus, remarks)
            );
        }

        function proceedWithStatusUpdate(id, newStatus, remarks) {
            const updateButton = document.querySelector('#updateModal .btn-primary');
            const originalText = updateButton.innerHTML;
            updateButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
            updateButton.disabled = true;

            fetch(`/admin/training/requests/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: newStatus,
                    remarks: remarks
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
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
                console.error('Error:', error);
                showToast('error', 'Error updating application status: ' + error.message);
            })
            .finally(() => {
                updateButton.innerHTML = originalText;
                updateButton.disabled = false;
            });
        }

        // View application details
        function viewApplication(id) {
    document.getElementById('applicationDetails').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>`;

    const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
    modal.show();

    fetch(`/admin/training/requests/${id}`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(response => {
            if (!response.success) throw new Error('Failed to load application details');

            const data = response.data;

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

            // Supporting documents 
            let documentHtml = '';
            if (data.document_path) {
                documentHtml = `
                    <div class="col-12">
                        <div class="card border-secondary">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i>Supporting Document</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center p-3 border border-secondary rounded bg-light">
                                    <i class="fas fa-file-alt fa-3x mb-2 text-info"></i>
                                    <h6>Supporting Document</h6>
                                    <span class="badge bg-info mb-2">Uploaded</span>
                                    <br>
                                    <button class="btn btn-sm btn-outline-info mt-2" 
                                        onclick="viewDocument('${data.document_path}', 'Training Request - ${data.full_name}')">
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
                                <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i>Supporting Document</h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center p-3 border border-secondary rounded">
                                    <i class="fas fa-file-slash fa-3x mb-2 text-muted"></i>
                                    <h6>No Document Uploaded</h6>
                                    <span class="badge bg-secondary mb-2">Not Uploaded</span>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }

            document.getElementById('applicationDetails').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Personal Information</h6>
                        <p><strong>Application #:</strong> ${data.application_number}</p>
                        <p><strong>Full Name:</strong> ${data.full_name}</p>
                        <p><strong>Contact:</strong> ${data.contact_number || 'N/A'}</p>
                        <p><strong>Email:</strong> ${data.email || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Training Information</h6>
                        <p><strong>Training Type:</strong> ${data.training_type_display}</p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-${data.status_color}">${data.formatted_status}</span>
                        </p>
                        <p><strong>Date Applied:</strong> ${data.created_at}</p>
                        <p><strong>Last Updated:</strong> ${data.updated_at}</p>
                    </div>
                    <div class="col-md-6 mt-3">
                        <h6 class="border-bottom pb-2">Location Information</h6>
                        <p><strong>Barangay:</strong> ${data.barangay || 'N/A'}</p>
                    </div>
                    ${documentHtml}
                    ${remarksHtml}
                </div>`;
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', error.message || 'Error loading application details. Please try again.');
            document.getElementById('applicationDetails').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${error.message || 'Error loading application details. Please try again.'}
                </div>`;
        });
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
                        const docIcon = fileExtension === 'pdf' ? 'file-pdf' : 
                                    ['doc', 'docx'].includes(fileExtension) ? 'file-word' : 'file-alt';

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

        // Delete application with confirmation toast
        function deleteApplication(id, applicationNumber) {
            showConfirmationToast(
                'Delete Application',
                `Are you sure you want to delete application ${applicationNumber}?\n\nThis action cannot be undone and will also delete all associated documents.`,
                () => proceedWithApplicationDelete(id, applicationNumber)
            );
        }

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

        // Real-time validation for contact number
        document.getElementById('training_contact_number')?.addEventListener('input', function() {
            validateTrainingContactNumber(this.value);
        });

        function validateTrainingContactNumber(contactNumber) {
            const input = document.getElementById('training_contact_number');
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
        document.getElementById('training_email')?.addEventListener('input', function() {
            validateTrainingEmail(this.value);
        });

        function validateTrainingEmail(email) {
            const input = document.getElementById('training_email');
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
        function capitalizeTrainingName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        }

        document.getElementById('training_first_name')?.addEventListener('blur', function() {
            capitalizeTrainingName(this);
        });

        document.getElementById('training_middle_name')?.addEventListener('blur', function() {
            capitalizeTrainingName(this);
        });

        document.getElementById('training_last_name')?.addEventListener('blur', function() {
            capitalizeTrainingName(this);
        });

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
            const requiredFields = [
                { id: 'training_first_name', label: 'First Name' },
                { id: 'training_last_name', label: 'Last Name' },
                { id: 'training_barangay', label: 'Barangay' },  
                { id: 'training_contact_number', label: 'Contact Number' },
                { id: 'training_type', label: 'Training Type' },
                { id: 'training_status', label: 'Status' }
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
            
            // Validate email if provided
            const email = document.getElementById('training_email').value.trim();
            if (email && !validateTrainingEmail(email)) {
                isValid = false;
            }
            
            return isValid;
        }

        // Submit add training form
        function submitAddTraining() {
            // Validate form
            if (!validateTrainingForm()) {
                showToast('error', 'Please fix all validation errors before submitting');
                return;
            }
            
            // Prepare form data
            const formData = new FormData();
            
            formData.append('first_name', document.getElementById('training_first_name').value.trim());
            formData.append('middle_name', document.getElementById('training_middle_name').value.trim());
            formData.append('last_name', document.getElementById('training_last_name').value.trim());
            formData.append('name_extension', document.getElementById('training_name_extension').value);  
            formData.append('barangay', document.getElementById('training_barangay').value.trim()); 
            formData.append('contact_number', document.getElementById('training_contact_number').value.trim());
            formData.append('email', document.getElementById('training_email').value.trim());
            formData.append('training_type', document.getElementById('training_type').value);
            formData.append('status', document.getElementById('training_status').value);
            formData.append('remarks', document.getElementById('training_remarks').value.trim());
            
            const userId = document.getElementById('training_user_id').value.trim();
            if (userId) {
                formData.append('user_id', userId);
            }
            
            // Add document if uploaded
            const docInput = document.getElementById('training_supporting_document');
            if (docInput.files && docInput.files.length > 0) {
                formData.append('supporting_document', docInput.files[0]);
            }
            
            // Find submit button
            const submitBtn = document.querySelector('#addTrainingModal .btn-primary');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating...';
            submitBtn.disabled = true;
            
            // Submit to backend
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
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addTrainingModal'));
                    modal.hide();
                    
                    // Show success message
                    showToast('success', data.message || 'Training registration created successfully');
                    
                    // Reload page after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Show validation errors
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
                    showToast('error', data.message || 'Failed to create training application');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while creating the application');
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
                        const backdrops = document.querySelectorAll('.modal-backdrop');
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

// Show edit training modal
function showEditTrainingModal(trainingId) {
    currentEditingTrainingId = trainingId;

    // Show loading state
    const modal = new bootstrap.Modal(document.getElementById('editTrainingModal'));
    modal.show();

    // Fetch training data
    fetch(`/admin/training/requests/${trainingId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const training = data.data;
                
                // Update modal title
                document.getElementById('editModalTitle').textContent = `Edit Application - ${training.application_number}`;
                
                // Populate form fields
                document.getElementById('edit_training_first_name').value = training.first_name || '';
                document.getElementById('edit_training_middle_name').value = training.middle_name || '';
                document.getElementById('edit_training_last_name').value = training.last_name || '';
                document.getElementById('edit_training_extension').value = training.name_extension || '';
                document.getElementById('edit_training_contact').value = training.contact_number || '';
                document.getElementById('edit_training_email').value = training.email || '';
                document.getElementById('edit_training_barangay').value = training.barangay || '';
                document.getElementById('edit_training_type').value = training.training_type || '';
                document.getElementById('edit_training_app_number').value = training.application_number || '';
                
                // Populate read-only fields
                document.getElementById('edit_training_status_display').innerHTML = 
                    `<span class="badge bg-${training.status_color}">${training.formatted_status}</span>`;
                document.getElementById('edit_training_date_display').textContent = training.created_at;
                
                // Store original data for change detection
                storeOriginalEditData();
                
                // Clear validation errors
                document.querySelectorAll('#editTrainingForm .is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('#editTrainingForm .invalid-feedback').forEach(el => el.remove());
            } else {
                showToast('error', 'Failed to load training data');
                modal.hide();
            }
        })
        .catch(error => {
            showToast('error', 'Error loading training data: ' + error.message);
            modal.hide();
        });
}

// Store original data for change detection
function storeOriginalEditData() {
    const form = document.getElementById('editTrainingForm');
    const originalData = {
        first_name: document.getElementById('edit_training_first_name').value,
        middle_name: document.getElementById('edit_training_middle_name').value,
        last_name: document.getElementById('edit_training_last_name').value,
        name_extension: document.getElementById('edit_training_extension').value,
        contact_number: document.getElementById('edit_training_contact').value,
        email: document.getElementById('edit_training_email').value,
        barangay: document.getElementById('edit_training_barangay').value,
        training_type: document.getElementById('edit_training_type').value
    };
    form.dataset.originalData = JSON.stringify(originalData);
    
    // Initialize button state - should show "Save Changes" icon initially
    const submitBtn = document.getElementById('editTrainingSubmitBtn');
    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
    submitBtn.classList.remove('no-changes');
}

// Check for changes in edit form with visual feedback
function checkForEditTrainingChanges() {
    const form = document.getElementById('editTrainingForm');
    const submitBtn = document.getElementById('editTrainingSubmitBtn');
    
    if (!form || !submitBtn) return false;
    
    const originalData = JSON.parse(form.dataset.originalData || '{}');
    
    let hasChanges = false;
    
    const fieldMap = {
        'first_name': 'edit_training_first_name',
        'middle_name': 'edit_training_middle_name',
        'last_name': 'edit_training_last_name',
        'name_extension': 'edit_training_extension',
        'contact_number': 'edit_training_contact',
        'email': 'edit_training_email',
        'barangay': 'edit_training_barangay',
        'training_type': 'edit_training_type'
    };
    
    // Check each field for changes and add/remove highlighting
    for (let fieldName in fieldMap) {
        const elementId = fieldMap[fieldName];
        const element = document.getElementById(elementId);
        const currentValue = element.value;
        const originalValue = originalData[fieldName];
        
        if (currentValue !== originalValue) {
            hasChanges = true;
            element.classList.add('form-changed');
        } else {
            element.classList.remove('form-changed');
        }
    }
    
    // Update button state based on changes
    // Button should only show "No Changes" state if actually no changes detected
    if (hasChanges) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('no-changes');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.add('no-changes');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
    }
    
    return hasChanges;
}

// Handle edit form submission
function handleEditTrainingSubmit() {
    if (!validateEditTrainingForm()) {
        showToast('error', 'Please fix the validation errors');
        return;
    }
    
    if (!checkForEditTrainingChanges()) {
        showToast('warning', 'No changes have been made');
        return;
    }
    
    showConfirmationToast(
        'Confirm Changes',
        'Are you sure you want to save these changes?',
        proceedWithEditTraining
    );
}

// Validate edit training form
function validateEditTrainingForm() {
    let isValid = true;
    
    const requiredFields = [
        { id: 'edit_training_first_name', label: 'First Name' },
        { id: 'edit_training_last_name', label: 'Last Name' },
        { id: 'edit_training_contact', label: 'Contact Number' },
        { id: 'edit_training_barangay', label: 'Barangay' },
        { id: 'edit_training_type', label: 'Training Type' }
    ];
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            const existing = input.parentNode.querySelector('.invalid-feedback');
            if (existing) existing.remove();
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block';
            feedback.textContent = `${field.label} is required`;
            input.parentNode.appendChild(feedback);
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
            const existing = input.parentNode.querySelector('.invalid-feedback');
            if (existing) existing.remove();
        }
    });
    
    const contactNumber = document.getElementById('edit_training_contact').value.trim();
    if (contactNumber && !validateEditTrainingContactNumber(document.getElementById('edit_training_contact'))) {
        isValid = false;
    }
    
    const email = document.getElementById('edit_training_email').value.trim();
    if (email && !validateEditTrainingEmail(document.getElementById('edit_training_email'))) {
        isValid = false;
    }
    
    return isValid;
}

// Validate edit training contact number
function validateEditTrainingContactNumber(input) {
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) feedback.remove();
    input.classList.remove('is-invalid', 'is-valid');
    
    const contactNumber = input.value.trim();
    if (!contactNumber) {
        return true;
    }
    
    const phoneRegex = /^(\+639|09)\d{9}$/;
    
    if (!phoneRegex.test(contactNumber)) {
        input.classList.add('is-invalid');
        const newFeedback = document.createElement('div');
        newFeedback.className = 'invalid-feedback d-block';
        newFeedback.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)';
        input.parentNode.appendChild(newFeedback);
        return false;
    }
    
    input.classList.add('is-valid');
    return true;
}

// Validate edit training email
function validateEditTrainingEmail(input) {
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) feedback.remove();
    input.classList.remove('is-invalid', 'is-valid');
    
    const email = input.value.trim();
    if (!email) {
        return true;
    }
    
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (!emailPattern.test(email)) {
        input.classList.add('is-invalid');
        const newFeedback = document.createElement('div');
        newFeedback.className = 'invalid-feedback d-block';
        newFeedback.textContent = 'Please enter a valid email address';
        input.parentNode.appendChild(newFeedback);
        return false;
    }
    
    input.classList.add('is-valid');
    return true;
}

// Proceed with edit submission
function proceedWithEditTraining() {
    const form = document.getElementById('editTrainingForm');
    const submitBtn = document.getElementById('editTrainingSubmitBtn');
    
    if (!currentEditingTrainingId) {
        showToast('error', 'Training ID not found');
        return;
    }
    
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
    submitBtn.disabled = true;
    
    const formData = new FormData();
    formData.append('first_name', document.getElementById('edit_training_first_name').value.trim());
    formData.append('middle_name', document.getElementById('edit_training_middle_name').value.trim());
    formData.append('last_name', document.getElementById('edit_training_last_name').value.trim());
    formData.append('name_extension', document.getElementById('edit_training_extension').value);
    formData.append('contact_number', document.getElementById('edit_training_contact').value.trim());
    formData.append('email', document.getElementById('edit_training_email').value.trim());
    formData.append('barangay', document.getElementById('edit_training_barangay').value);
    formData.append('training_type', document.getElementById('edit_training_type').value);
    formData.append('_method', 'PUT');
    
    fetch(`/admin/training/requests/${currentEditingTrainingId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Training application updated successfully');
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTrainingModal'));
            if (modal) modal.hide();
            
            // Reload the page
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast('error', data.message || 'Failed to update training');
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        showToast('error', 'Error: ' + error.message);
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
        submitBtn.disabled = false;
    });
}

// Add event listeners for edit training form
function initializeEditTrainingFormListeners() {
    const form = document.getElementById('editTrainingForm');
    if (!form) return;
    
    const fields = ['edit_training_first_name', 'edit_training_middle_name', 'edit_training_last_name', 
                    'edit_training_extension', 'edit_training_contact', 'edit_training_email', 
                    'edit_training_barangay', 'edit_training_type'];
    
    fields.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('blur', function() {
                if (id.includes('contact')) {
                    validateEditTrainingContactNumber(this);
                } else if (id.includes('email')) {
                    validateEditTrainingEmail(this);
                }
                checkForEditTrainingChanges();
            });
            
            element.addEventListener('change', function() {
                checkForEditTrainingChanges();
            });
            
            element.addEventListener('input', function() {
                checkForEditTrainingChanges();
            });
        }
    });
}

// Initialize on document ready
document.addEventListener('DOMContentLoaded', function() {
    initializeEditTrainingFormListeners();
});

// Helper function to get CSRF token
function getCSRFToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
}
        console.log('Training Add Application functionality loaded successfully');
    </script>
@endsection