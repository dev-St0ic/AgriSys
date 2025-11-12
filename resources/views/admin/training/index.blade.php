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
                    <div class="col-md-3">
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
                    <div class="col-md-1">
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
                            <tr>
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
                                        @if ($training->document_paths && count($training->document_paths) > 0)
                                            <div class="training-document-previews">
                                                @foreach (array_slice($training->document_paths, 0, 3) as $index => $path)
                                                    <div class="training-mini-doc"
                                                        onclick="viewDocuments({{ json_encode($training->document_paths) }}, 'Training Request - {{ $training->full_name }}')"
                                                        title="Document {{ $index + 1 }}">
                                                        <div class="training-mini-doc-icon">
                                                            <i class="fas fa-file-image text-info"></i>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if (count($training->document_paths) > 3)
                                                    <div class="training-mini-doc training-mini-doc-more"
                                                        onclick="viewDocuments({{ json_encode($training->document_paths) }}, 'Training Request - {{ $training->full_name }}')"
                                                        title="View all {{ count($training->document_paths) }} documents">
                                                        <span
                                                            class="training-more-count">+{{ count($training->document_paths) - 3 }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="training-document-summary"
                                                onclick="viewDocuments({{ json_encode($training->document_paths) }}, 'Training Request - {{ $training->full_name }}')">
                                                <small class="text-muted">{{ count($training->document_paths) }}
                                                    document{{ count($training->document_paths) > 1 ? 's' : '' }}</small>
                                            </div>
                                        @else
                                            <div class="training-no-documents">
                                                <i class="fas fa-folder-open text-muted"></i>
                                                <small class="text-muted">No documents</small>
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

                                        <button class="btn btn-sm btn-outline-success"
                                            onclick="showUpdateModal({{ $training->id }}, '{{ $training->status }}')"
                                            title="Update Status">
                                            <i class="fas fa-edit"></i> Update
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
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Update Application Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                    <p class="mb-1"><strong>Mobile:</strong> <span id="updateAppMobile"></span></p>
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
            border: 2px solid #e9ecef;
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
                    document.getElementById('updateAppMobile').textContent = data.mobile_number || 'N/A';
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
                    alert('Error loading application details: ' + error.message);
                });
        }

        // Enhanced update application status function
        function updateApplicationStatus() {
            const id = document.getElementById('updateApplicationId').value;
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

            const confirmMessage =
                `Are you sure you want to update this application with the following changes?\n\n${changesSummary.join('\n')}`;

            if (!confirm(confirmMessage)) {
                return;
            }

            // Show loading state
            const updateButton = document.querySelector('#updateModal .btn-primary');
            const originalText = updateButton.innerHTML;
            updateButton.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
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
                        alert(response.message);
                        window.location.reload();
                    } else {
                        throw new Error(response.message || 'Error updating status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating application status: ' + error.message);
                })
                .finally(() => {
                    // Reset button state
                    updateButton.innerHTML = originalText;
                    updateButton.disabled = false;
                });
        }

        // View application details
        function viewApplication(id) {
            // Show loading state
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

                    // Format the details HTML with the same style as FishR
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

                    document.getElementById('applicationDetails').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Application Information</h6>
                            <p><strong>Application #:</strong> ${data.application_number}</p>
                            <p><strong>Full Name:</strong> ${data.full_name}</p>
                            <p><strong>Mobile:</strong> ${data.mobile_number || 'N/A'}</p>
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
                        ${data.document_paths && data.document_paths.length > 0 ? `
                                                                        <div class="col-12">
                                                                            <h6 class="border-bottom pb-2">Supporting Documents</h6>
                                                                            <div class="row g-2">
                                                                                ${data.document_paths.map((path, index) => `
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title">Document ${index + 1}</h6>
                                                    <button class="btn btn-sm btn-outline-primary"
                                                        onclick="viewDocuments(['${path}'])">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                                                            </div>
                                                                        </div>
                                                                    ` : ''}
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

        // Enhanced view documents function for training module
        function viewDocuments(paths, title = null) {
            // Input validation
            if (!paths || paths.length === 0) {
                alert('No documents to display');
                return;
            }

            const documentViewer = document.getElementById('documentViewer');
            const modal = new bootstrap.Modal(document.getElementById('documentModal'));

            // Show loading state first
            documentViewer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading documents...</span>
                    </div>
                    <p class="text-muted">Loading ${paths.length} document(s)...</p>
                </div>`;

            // Show modal immediately with loading state
            modal.show();

            // Update modal title if provided
            const modalTitle = document.querySelector('#documentModal .modal-title');
            if (title) {
                modalTitle.innerHTML = `<i class="fas fa-file-alt me-2"></i>${title}`;
            } else {
                modalTitle.innerHTML = `<i class="fas fa-file-alt me-2"></i>Supporting Documents (${paths.length})`;
            }

            // Define supported file types
            const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            const documentTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
            const videoTypes = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
            const audioTypes = ['mp3', 'wav', 'ogg', 'aac', 'm4a'];

            // Function to handle loading errors
            const handleLoadError = (fileName, type, path) => {
                return `
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <h6>Unable to preview ${fileName}</h6>
                        <p class="mb-3">The ${type} could not be loaded or displayed.</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="/storage/${path}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                            </a>
                            <a href="/storage/${path}" download="${fileName}" class="btn btn-success">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                    </div>`;
            };

            // Function to add action buttons for each document
            const addDocumentActions = (path, fileName) => {
                return `
                    <div class="document-actions mt-3 p-3 bg-light rounded text-center">
                        <div class="d-flex justify-content-center gap-2 mb-2">
                            <a href="/storage/${path}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                            </a>
                            <a href="/storage/${path}" download="${fileName}" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                        <small class="text-muted d-block">${fileName}</small>
                    </div>`;
            };

            // Process documents with delay to show loading state
            setTimeout(() => {
                let documentsHtml = '<div class="container-fluid p-3 d-flex flex-column align-items-center">';

                paths.forEach((path, index) => {
                    const fileExtension = path.split('.').pop().toLowerCase();
                    const fileName = path.split('/').pop();
                    const fileUrl = `/storage/${path}`;

                    documentsHtml += `
                        <div class="document-container mb-4 border rounded p-3" style="max-width: 600px; width: 100%;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-file me-2"></i>Document ${index + 1}
                                </h6>
                                <span class="badge bg-secondary">${fileExtension.toUpperCase()}</span>
                            </div>
                            <div class="document-content" id="doc-content-${index}">`;

                    // Handle different file types
                    if (imageTypes.includes(fileExtension)) {
                        // Handle images with error fallback
                        documentsHtml += `
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <img src="${fileUrl}"
                                         class="img-fluid border rounded shadow-sm document-image"
                                         alt="Supporting Document"
                                         style="max-height: 400px; cursor: zoom-in;"
                                         onclick="toggleImageZoomTraining(this)"
                                         onerror="showImageError(this, '${fileName}', '${path}')">
                                </div>
                                ${addDocumentActions(path, fileName)}
                            </div>`;

                    } else if (fileExtension === 'pdf') {
                        // Handle PDF documents
                        documentsHtml += `
                            <div class="pdf-container">
                                <embed src="${fileUrl}"
                                       type="application/pdf"
                                       width="100%"
                                       height="500px"
                                       class="border rounded"
                                       onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="display:none;" class="pdf-fallback">
                                    ${handleLoadError(fileName, 'PDF document', path)}
                                </div>
                                ${addDocumentActions(path, fileName)}
                            </div>`;

                    } else if (videoTypes.includes(fileExtension)) {
                        // Handle video files
                        documentsHtml += `
                            <div class="text-center">
                                <video controls class="w-100 rounded shadow" style="max-height: 400px;" preload="metadata">
                                    <source src="${fileUrl}" type="video/${fileExtension}">
                                    Your browser does not support the video tag.
                                </video>
                                ${addDocumentActions(path, fileName)}
                            </div>`;

                    } else if (audioTypes.includes(fileExtension)) {
                        // Handle audio files
                        documentsHtml += `
                            <div class="text-center py-4">
                                <i class="fas fa-music fa-3x text-info mb-3"></i>
                                <h6>${fileName}</h6>
                                <audio controls class="w-100 mb-3">
                                    <source src="${fileUrl}" type="audio/${fileExtension}">
                                    Your browser does not support the audio tag.
                                </audio>
                                ${addDocumentActions(path, fileName)}
                            </div>`;

                    } else if (documentTypes.includes(fileExtension)) {
                        // Handle other document types
                        const docIcon = fileExtension === 'pdf' ? 'file-pdf' : ['doc', 'docx'].includes(
                            fileExtension) ? 'file-word' : 'file-alt';

                        documentsHtml += `
                            <div class="alert alert-info text-center">
                                <i class="fas fa-${docIcon} fa-3x text-primary mb-3"></i>
                                <h6>${fileName}</h6>
                                <p class="mb-3">This document type cannot be previewed directly.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                                    </a>
                                    <a href="${fileUrl}" download="${fileName}" class="btn btn-success">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>`;

                    } else {
                        // Handle unsupported file types
                        documentsHtml += `
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-file fa-3x text-warning mb-3"></i>
                                <h6>Unsupported File Type</h6>
                                <p class="mb-3">File type ".${fileExtension}" is not supported for preview.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                                    </a>
                                    <a href="${fileUrl}" download="${fileName}" class="btn btn-success">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>`;
                    }

                    documentsHtml += `
                            </div>
                        </div>`;

                    // Add separator between documents (except for the last one)
                    if (index < paths.length - 1) {
                        documentsHtml += '<hr class="my-4">';
                    }
                });

                documentsHtml += '</div>';

                // Update the document viewer
                documentViewer.innerHTML = documentsHtml;

                // Add timeout check for PDF embeds
                setTimeout(() => {
                    document.querySelectorAll('.pdf-container embed').forEach(embed => {
                        if (embed.offsetHeight === 0) {
                            embed.style.display = 'none';
                            embed.nextElementSibling.style.display = 'block';
                        }
                    });
                }, 2000);

            }, 500); // Small delay to show loading state
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
    </script>

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
@endsection
