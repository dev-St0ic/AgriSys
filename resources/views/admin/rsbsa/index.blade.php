{{-- resources/views/admin/rsbsa_applications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'RSBSA Applications - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-file-alt text-primary me-2"></i>
        <span class="text-primary fw-bold">RSBSA Applications</span>
    </div>
@endsection

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
                        <i class="fas fa-hourglass-start text-info"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $pendingCount }}</div>
                    <div class="stat-label text-info">Pending</div>
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
                        <a href="{{ route('admin.rsbsa.applications') }}" class="btn btn-secondary btn-sm w-100">
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
                    <i class="fas fa-file-alt me-2"></i>RSBSA Applications
                </h6>
            </div>
            <div class="d-flex gap-2">
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
                            <th class="text-center">Land Area</th>
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
                                    <span class="badge bg-info fs-6">{{ $application->main_livelihood }}</span>
                                </td>
                                <td class="text-start">
                                    {{ $application->land_area ? $application->land_area . ' ha' : 'N/A' }}</td>
                                <td class="text-start">
                                    <span class="badge bg-{{ $application->status_color }} fs-6">
                                        {{ $application->formatted_status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="fishr-table-documents">
                                        @if ($application->supporting_document_path)
                                            <div class="fishr-document-previews">
                                                <div class="fishr-mini-doc"
                                                    onclick="viewDocument('{{ $application->supporting_document_path }}', 'Application #{{ $application->application_number }} - Supporting Document')"
                                                    title="Supporting Document">
                                                    <div class="fishr-mini-doc-icon">
                                                        <i class="fas fa-file-alt text-primary"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="fishr-document-summary"
                                                onclick="viewDocument('{{ $application->supporting_document_path }}', 'Application #{{ $application->application_number }} - Supporting Document')">
                                                <small class="text-muted">1 document</small>
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
                                            onclick="viewApplication({{ $application->id }})" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>

                                        <button class="btn btn-sm btn-outline-success"
                                            onclick="showUpdateModal({{ $application->id }}, '{{ $application->status }}')"
                                            title="Update Status">
                                            <i class="fas fa-edit"></i> Update
                                        </button>

                                        <!--  DELETE BUTTON -->
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteApplication({{ $application->id }})" title="Delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                                    <p>No RSBSA applications found.</p>
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
                                    <p class="mb-1"><strong>ID:</strong> <span id="updateAppId"></span></p>
                                    <p class="mb-1"><strong>Application #:</strong> <span id="updateAppNumber"></span>
                                    </p>
                                    <p class="mb-1"><strong>Name:</strong> <span id="updateAppName"></span></p>
                                    <p class="mb-1"><strong>Type:</strong> <span id="updateAppType"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Barangay:</strong> <span id="updateAppBarangay"></span></p>
                                    <p class="mb-1"><strong>Livelihood:</strong> <span id="updateAppLivelihood"></span>
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
                                <option value="pending">Pending</option>
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
                        <i class="fas fa-file-alt me-2"></i>Application Details
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

        /* ============================================
            VIEW MODAL STYLING - CONSISTENT WITH OTHER SERVICES
            ============================================ */

        /* Application Details Modal - Enhanced Styling */
        #applicationModal .modal-content {
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }

        #applicationModal .modal-header {
            border-radius: 12px 12px 0 0;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            padding: 1.5rem;
        }

        #applicationModal .modal-header .modal-title {
            color: white;
            font-weight: 600;
            font-size: 1.25rem;
        }

        #applicationModal .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        #applicationModal .modal-footer {
            border-radius: 0 0 12px 12px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-top: 1px solid #dee2e6;
            padding: 1.5rem;
        }

        #applicationModal .modal-body {
            padding: 2rem;
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Application Details Cards */
        #applicationDetails .row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        #applicationDetails .col-md-6 {
            flex: 0 0 calc(50% - 0.75rem);
        }

        #applicationDetails .col-12 {
            flex: 0 0 100%;
        }

        #applicationDetails h6 {
            color: #007bff;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #applicationDetails p {
            margin-bottom: 0.75rem;
            color: #333;
            line-height: 1.6;
        }

        #applicationDetails strong {
            color: #495057;
            font-weight: 600;
        }

        /* Remarks Alert Styling */
        #applicationDetails .alert {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            border: none;
            border-radius: 10px;
            border-left: 4px solid #17a2b8;
            margin-top: 1rem;
        }

        #applicationDetails .alert p {
            margin: 0;
            color: #0c5460;
        }

        #applicationDetails .alert small {
            color: #0c5460;
            opacity: 0.8;
        }

        /* Badge Styling */
        #applicationDetails .badge {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Scrollbar Styling for Modal Body */
        #applicationModal .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        #applicationModal .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #applicationModal .modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        #applicationModal .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Update Modal - Enhanced Styling */
        #updateModal .modal-content {
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }

        #updateModal .modal-header {
            border-radius: 12px 12px 0 0;
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            border: none;
            padding: 1.5rem;
        }

        #updateModal .modal-header .modal-title {
            color: white;
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

        /* Modal Button Styling */
        #applicationModal .modal-footer .btn,
        #updateModal .modal-footer .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        #applicationModal .modal-footer .btn-secondary,
        #updateModal .modal-footer .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        #applicationModal .modal-footer .btn-secondary:hover,
        #updateModal .modal-footer .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
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
        .fishr-mini-doc[title*="Supporting"] {
            border-color: #007bff;
        }

        .fishr-mini-doc[title*="Supporting"]:hover {
            background-color: rgba(0, 123, 255, 0.1);
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
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="clearDateRangeModal()">
                                            <i class="fas fa-calendar-times me-2"></i>Clear Date Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Filter Display -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info mb-0" id="currentDateFilter">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
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

        function clearDateRange() {
            clearDateRangeModal();
        }

        // Date picker modal functions
        function openDatePicker() {
            const modal = document.getElementById('datePickerModal');
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            // Set current values in modal
            document.getElementById('modalStartDate').value = startDate;
            document.getElementById('modalEndDate').value = endDate;

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeDatePicker() {
            const modal = document.getElementById('datePickerModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
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
                document.getElementById('modalStartDate').value = startDate.toISOString().split('T')[0];
                document.getElementById('modalEndDate').value = endDate.toISOString().split('T')[0];

                // Add active class to clicked button
                event.target.classList.add('active');
            }
        }

        function clearModalDates() {
            document.getElementById('modalStartDate').value = '';
            document.getElementById('modalEndDate').value = '';
            document.querySelectorAll('.quick-date-btn').forEach(btn => btn.classList.remove('active'));
        }

        function applyDateRange() {
            const startDate = document.getElementById('modalStartDate').value;
            const endDate = document.getElementById('modalEndDate').value;

            // Validate date range
            if (startDate && endDate && startDate > endDate) {
                alert('Start date cannot be after end date');
                return;
            }

            // Update hidden inputs
            document.getElementById('startDate').value = startDate;
            document.getElementById('endDate').value = endDate;

            // Update display
            updateDateRangeDisplay(startDate, endDate);

            // Close modal
            closeDatePicker();

            // Submit form
            submitFilterForm();
        }

        function updateDateRangeDisplay(startDate, endDate) {
            const displayInput = document.getElementById('dateRangePicker');

            if (startDate && endDate) {
                const start = new Date(startDate).toLocaleDateString();
                const end = new Date(endDate).toLocaleDateString();
                displayInput.value = `${start} - ${end}`;
            } else if (startDate) {
                displayInput.value = new Date(startDate).toLocaleDateString();
            } else {
                displayInput.value = '';
            }
        }

        // Initialize date picker
        document.addEventListener('DOMContentLoaded', function() {
            // Make date range picker clickable
            document.getElementById('dateRangePicker').addEventListener('click', openDatePicker);

            // Close modal when clicking outside
            document.getElementById('datePickerModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDatePicker();
                }
            });

            // Add Enter key listeners to date inputs
            document.getElementById('modal_date_from').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyCustomDateRange();
                }
            });

            document.getElementById('modal_date_to').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyCustomDateRange();
                }
            });

            // Initialize display with current values
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            if (startDate || endDate) {
                updateDateRangeDisplay(startDate, endDate);
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
                alert('Invalid application ID');
                return;
            }

            // Show loading state in modal
            document.getElementById('updateAppId').innerHTML = `
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
                    document.getElementById('updateAppId').textContent = data.application_number || 'N/A';
                    document.getElementById('updateAppNumber').textContent = data.application_number || 'N/A';
                    document.getElementById('updateAppName').textContent = data.full_name || 'N/A';

                    // Handle registration type with null safety
                    const regType = data.registration_type || 'new';
                    document.getElementById('updateAppType').innerHTML = `
                <span class="badge bg-${regType === 'new' ? 'primary' : 'warning'}">
                    ${regType.charAt(0).toUpperCase() + regType.slice(1)}
                </span>`;

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
                    alert('Error loading application details: ' + error.message);
                });
        }

        // Update application status function with enhanced validation
        function updateApplicationStatus() {
            const id = document.getElementById('updateApplicationId').value;
            const newStatus = document.getElementById('newStatus').value;
            const remarks = document.getElementById('remarks').value;

            // Validate inputs
            if (!id) {
                alert('Invalid application ID');
                return;
            }

            if (!newStatus) {
                alert('Please select a status');
                return;
            }

            // Get the original values to compare changes
            const originalStatus = document.getElementById('newStatus').dataset.originalStatus || '';
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

            fetch(`/admin/rsbsa-applications/${id}/status`, {
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
                        throw new Error(`HTTP error! status: ${response.status}`);
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

        // View application details with enhanced error handling
        function viewApplication(id) {
            if (!id) {
                alert('Invalid application ID');
                return;
            }

            // Show loading state
            document.getElementById('applicationDetails').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>`;

            // Show modal while loading
            const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
            modal.show();

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

                    // Format the details HTML with null safety
                    const remarksHtml = data.remarks ? `
                <div class="col-12 mt-3">
                    <h6 class="border-bottom pb-2">Remarks</h6>
                    <div class="alert alert-info">
                        <p class="mb-1">${data.remarks}</p>
                        <small class="text-muted">
                            ${data.reviewed_at ? `Updated on ${data.reviewed_at}` : ''}
                            ${data.reviewer_name ? ` by ${data.reviewer_name}` : ''}
                        </small>
                    </div>
                </div>` : '';

                    // Safely get registration type
                    const regType = data.registration_type || 'new';
                    const regTypeClass = regType === 'new' ? 'primary' : 'warning';
                    const regTypeText = regType.charAt(0).toUpperCase() + regType.slice(1);

                    // Update modal content
                    document.getElementById('applicationDetails').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Personal Information</h6>
                        <p><strong>Application #:</strong> ${data.application_number || 'N/A'}</p>
                        <p><strong>Name:</strong> ${data.full_name || 'N/A'}</p>
                        <p><strong>Sex:</strong> ${data.sex || 'N/A'}</p>
                        ${data.date_of_birth ? `<p><strong>Date of Birth:</strong> ${data.date_of_birth}</p>` : ''}
                        <p><strong>Contact:</strong> ${data.contact_number || 'N/A'}</p>
                        <p><strong>Barangay:</strong> ${data.barangay || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Registration Information</h6>
                        <p><strong>Type:</strong>
                            <span class="badge bg-${regTypeClass}">
                                ${regTypeText}
                            </span>
                        </p>
                        <p><strong>Main Livelihood:</strong> ${data.main_livelihood || 'N/A'}</p>
                        <p><strong>Land Area:</strong> ${data.land_area ? data.land_area + ' hectares' : 'N/A'}</p>
                        <p><strong>Farm Location:</strong> ${data.farm_location || 'N/A'}</p>
                        <p><strong>Commodity:</strong> ${data.commodity || 'N/A'}</p>
                        <p><strong>Current Status:</strong>
                            <span class="badge bg-${data.status_color || 'secondary'}">${data.formatted_status || getStatusText(data.status)}</span>
                        </p>
                    </div>
                    <div class="col-12">
                        <h6 class="border-bottom pb-2">Application Timeline</h6>
                        <p><strong>Date Applied:</strong> ${data.created_at || 'N/A'}</p>
                        <p><strong>Last Updated:</strong> ${data.updated_at || 'N/A'}</p>
                        ${data.reviewed_at ? `<p><strong>Reviewed At:</strong> ${data.reviewed_at}</p>` : ''}
                        ${data.number_assigned_at ? `<p><strong>Number Assigned:</strong> ${data.number_assigned_at}</p>` : ''}
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

        // Enhanced view document function
        function viewDocument(path, filename = null) {
            // Input validation
            if (!path || path.trim() === '') {
                alert('No document path provided');
                return;
            }

            const documentViewer = document.getElementById('documentViewer');
            const modal = new bootstrap.Modal(document.getElementById('documentModal'));

            // Show loading state first
            documentViewer.innerHTML = `
                <div class="fishr-document-viewer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted">Loading document...</p>
                    </div>
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

            // Function to handle loading errors
            const handleLoadError = (type, error = null) => {
                console.error(`Error loading ${type}:`, error);
                documentViewer.innerHTML = `
                    <div class="fishr-document-viewer">
                        <div class="fishr-document-placeholder">
                            <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                            <h5>Unable to preview ${type}</h5>
                            <p class="mb-3">The ${type} could not be loaded or displayed.</p>
                        </div>
                        <div class="fishr-document-actions">
                            <button class="btn fishr-btn fishr-btn-outline" onclick="window.open('${fileUrl}', '_blank')">
                                <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                            </button>
                            <button class="btn fishr-btn fishr-btn-primary" onclick="downloadFile('${fileUrl}', '${fileName}')">
                                <i class="fas fa-download me-2"></i>Download
                            </button>
                        </div>
                        <div class="fishr-document-info">
                            <p class="fishr-file-name">File: ${fileName}</p>
                        </div>
                    </div>`;
            };

            // Function to add FISHR-style download actions
            const addFishrActions = () => {
                return `
                    <div class="fishr-document-actions">
                        <button class="btn fishr-btn fishr-btn-outline" onclick="window.open('${fileUrl}', '_blank')">
                            <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                        </button>
                        <button class="btn fishr-btn fishr-btn-primary" onclick="downloadFile('${fileUrl}', '${fileName}')">
                            <i class="fas fa-download me-2"></i>Download
                        </button>
                    </div>
                    <div class="fishr-document-info">
                        <p class="fishr-file-name">File: ${fileName} (${fileExtension.toUpperCase()})</p>
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
                                <div class="fishr-document-viewer">
                                    <div class="fishr-document-container">
                                        <img src="${fileUrl}"
                                             class="fishr-document-image"
                                             alt="Supporting Document"
                                             onclick="toggleImageZoom(this)"
                                             style="cursor: zoom-in;">
                                        <div class="fishr-document-overlay">
                                            <div class="fishr-document-size-badge">
                                                ${Math.round((this.naturalWidth * this.naturalHeight) / 1024)}KB
                                            </div>
                                        </div>
                                    </div>
                                    ${addFishrActions()}
                                </div>`;
                        };
                        img.onerror = function() {
                            handleLoadError('image');
                        };
                        img.src = fileUrl;

                    } else if (fileExtension === 'pdf') {
                        // Handle PDF documents
                        documentViewer.innerHTML = `
                            <div class="fishr-document-viewer">
                                <div class="fishr-document-container">
                                    <embed src="${fileUrl}"
                                           type="application/pdf"
                                           width="100%"
                                           height="600px"
                                           class="fishr-pdf-embed">
                                </div>
                                ${addFishrActions()}
                            </div>`;

                        // Check if PDF loaded successfully after a short delay
                        setTimeout(() => {
                            const embed = documentViewer.querySelector('embed');
                            if (!embed || embed.offsetHeight === 0) {
                                documentViewer.innerHTML = `
                                    <div class="fishr-document-viewer">
                                        <div class="fishr-document-placeholder">
                                            <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                                            <h5>PDF Preview Unavailable</h5>
                                            <p class="mb-3">Your browser doesn't support PDF preview or the file couldn't be loaded.</p>
                                        </div>
                                        <div class="fishr-document-actions">
                                            <button class="btn fishr-btn fishr-btn-outline" onclick="window.open('${fileUrl}', '_blank')">
                                                <i class="fas fa-external-link-alt me-2"></i>Open PDF
                                            </button>
                                            <button class="btn fishr-btn fishr-btn-primary" onclick="downloadFile('${fileUrl}', '${fileName}')">
                                                <i class="fas fa-download me-2"></i>Download PDF
                                            </button>
                                        </div>
                                        <div class="fishr-document-info">
                                            <p class="fishr-file-name">File: ${fileName}</p>
                                        </div>
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
                    handleLoadError('document', error);
                }
            }, 500); // Small delay to show loading state
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

        // Function to check for changes and provide visual feedback
        function checkForChanges() {
            const statusSelect = document.getElementById('newStatus');
            const remarksTextarea = document.getElementById('remarks');

            if (!statusSelect || !statusSelect.dataset.originalStatus) return;

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
                statusSelect.addEventListener('change', checkForChanges);
            }

            if (remarksTextarea) {
                remarksTextarea.addEventListener('input', checkForChanges);
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

        // Delete application with confirmation toast
        function deleteApplication(id) {
            // Show confirmation toast instead of browser confirm
            showConfirmationToast(
                'Delete RSBSA Application',
                'Are you sure you want to delete this RSBSA application?\n\nThis action cannot be undone and will remove all associated data.',
                () => proceedWithApplicationDelete(id)
            );
        }

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

        // Get CSRF token utility function
        function getCSRFToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            return metaTag ? metaTag.getAttribute('content') : '';
        }

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

        // REPLACE deleteApplication function
        function deleteApplication(id) {
            showConfirmationToast(
                'Delete RSBSA Application',
                'Are you sure you want to delete this RSBSA application?\n\nThis action cannot be undone and will remove all associated data.',
                () => proceedWithApplicationDelete(id)
            );
        }

        // NEW proceedWithApplicationDelete function
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

                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                window.location.reload();
                            }, 300);
                        } else {
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

        // REPLACE updateApplicationStatus function
        function updateApplicationStatus() {
            const id = document.getElementById('updateApplicationId').value;
            const newStatus = document.getElementById('newStatus').value;
            const remarks = document.getElementById('remarks').value;

            if (!id) {
                showToast('error', 'Invalid application ID');
                return;
            }

            if (!newStatus) {
                showToast('error', 'Please select a status');
                return;
            }

            const originalStatus = document.getElementById('newStatus').dataset.originalStatus || '';
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
                `Update this application with the following changes?\n\n${changesSummary.join('\n')}`,
                () => proceedWithStatusUpdate(id, newStatus, remarks)
            );
        }

        // NEW proceedWithStatusUpdate function
        function proceedWithStatusUpdate(id, newStatus, remarks) {
            const updateButton = document.querySelector('#updateModal .btn-primary');
            const originalText = updateButton.innerHTML;
            updateButton.innerHTML =
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
            updateButton.disabled = true;

            fetch(`/admin/rsbsa-applications/${id}/status`, {
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
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    if (response.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                        modal.hide();
                        showToast('success', response.message || 'Application status updated successfully');

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
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
    </script>
@endsection
