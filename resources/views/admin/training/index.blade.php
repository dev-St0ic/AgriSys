{{-- resources/views/admin/training/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Training Applications - AgriSys Admin')
@section('page-title', 'Training Applications')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Applications
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalApplications }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
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
                                Under Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $underReviewCount }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedCount }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rejectedCount }}</div>
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
        <div class="card-body p-4">
            <div class="filter-section mb-0">
                <form method="GET" action="{{ route('admin.training.requests') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0"
                                    placeholder="🔍 Search name, number, email..." 
                                    value="{{ request('search') }}"
                                    oninput="autoSearch()" id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="submitFilterForm()">
                                <option value="">📊 All Status</option>
                                <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>
                                    ⏳ Under Review
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                    ✅ Approved
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                    ❌ Rejected
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="training_type" class="form-select" onchange="submitFilterForm()">
                                <option value="">🎓 All Training Types</option>
                                <option value="tilapia_hito" {{ request('training_type') == 'tilapia_hito' ? 'selected' : '' }}>
                                    🐟 Tilapia and Hito Training
                                </option>
                                <option value="hydroponics" {{ request('training_type') == 'hydroponics' ? 'selected' : '' }}>
                                    🌱 Hydroponics Training
                                </option>
                                <option value="aquaponics" {{ request('training_type') == 'aquaponics' ? 'selected' : '' }}>
                                    🐠 Aquaponics Training
                                </option>
                                <option value="mushrooms" {{ request('training_type') == 'mushrooms' ? 'selected' : '' }}>
                                    🍄 Mushrooms Production Training
                                </option>
                                <option value="livestock_poultry" {{ request('training_type') == 'livestock_poultry' ? 'selected' : '' }}>
                                    🐄 Livestock and Poultry Training
                                </option>
                                <option value="high_value_crops" {{ request('training_type') == 'high_value_crops' ? 'selected' : '' }}>
                                    🌾 High Value Crops Training
                                </option>
                                <option value="sampaguita_propagation" {{ request('training_type') == 'sampaguita_propagation' ? 'selected' : '' }}>
                                    🌸 Sampaguita Propagation Training
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group w-100" role="group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.training.requests') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-graduation-cap me-2"></i>Training Applications
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="applicationsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Application #</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Training Type</th>
                            <th>Status</th>
                            <th>Date Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainings as $training)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $training->application_number }}</strong>
                                </td>
                                <td>{{ $training->full_name }}</td>
                                <td>{{ $training->mobile_number }}</td>
                                <td>{{ $training->email }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $training->training_type_display }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $training->status_color }}">
                                        {{ $training->formatted_status }}
                                    </span>
                                </td>
                                <td>{{ $training->created_at->format('M d, Y g:i A') }}</td>
                                <td>
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
                                        @if ($training->document_paths)
                                            <button class="btn btn-sm btn-outline-info"
                                                onclick="viewDocuments({{ json_encode($training->document_paths) }})"
                                                title="View Documents">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                    <p>No training applications found.</p>
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
                                        <a class="page-link"
                                            href="{{ $trainings->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($trainings->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $trainings->nextPageUrl() }}"
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
                                    <p class="mb-1"><strong>Application #:</strong> <span id="updateAppNumber"></span></p>
                                    <p class="mb-1"><strong>Name:</strong> <span id="updateAppName"></span></p>
                                    <p class="mb-1"><strong>Email:</strong> <span id="updateAppEmail"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Mobile:</strong> <span id="updateAppMobile"></span></p>
                                    <p class="mb-1"><strong>Training Type:</strong> <span id="updateAppTraining"></span></p>
                                    <p class="mb-1"><strong>Current Status:</strong> <span id="updateAppCurrentStatus"></span></p>
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
                                placeholder="Add any notes or comments about this status change..."
                                maxlength="1000"></textarea>
                            <div class="form-text">Maximum 1000 characters</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateApplicationStatus()">Update Status</button>
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

    <!-- Document Viewer Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt me-2"></i>Supporting Documents
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="documentViewer">
                    <!-- Documents will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <style>
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

        /* Document viewer styling */
        .document-item {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
        }

        .document-item h6 {
            color: #495057;
            margin-bottom: 10px;
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
        }, 500);
    }

    // Submit filter form when dropdowns change
    function submitFilterForm() {
        document.getElementById('filterForm').submit();
    }

    // Helper function to get status display text
    function getStatusText(status) {
        switch(status) {
            case 'under_review': return 'Under Review';
            case 'approved': return 'Approved';
            case 'rejected': return 'Rejected';
            default: return status;
        }
    }

    // Show update modal function
    function showUpdateModal(id, currentStatus) {
        // Show loading state in modal
        document.getElementById('updateAppNumber').innerHTML = `
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>`;

        // First fetch the application details
        fetch(`/admin/training-applications/${id}`)
            .then(response => response.json())
            .then(response => {
                if (!response.success) {
                    throw new Error('Failed to load application details');
                }

                const data = response.data;
                
                // Populate the hidden field
                document.getElementById('updateApplicationId').value = id;

                // Populate application info display
                document.getElementById('updateAppNumber').textContent = data.application_number;
                document.getElementById('updateAppName').textContent = data.full_name;
                document.getElementById('updateAppEmail').textContent = data.email;
                document.getElementById('updateAppMobile').textContent = data.mobile_number;
                document.getElementById('updateAppTraining').textContent = data.training_type_display;

                // Show current status with badge styling
                const currentStatusElement = document.getElementById('updateAppCurrentStatus');
                currentStatusElement.innerHTML = `
                    <span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;

                // Set form values and store original values for comparison
                const statusSelect = document.getElementById('newStatus');
                const remarksTextarea = document.getElementById('remarks');
                
                statusSelect.value = data.status;
                statusSelect.dataset.originalStatus = data.status;
                
                remarksTextarea.value = data.remarks || '';
                remarksTextarea.dataset.originalRemarks = data.remarks || '';

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

    // Update application status function
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

        const confirmMessage = `Are you sure you want to update this application with the following changes?\n\n${changesSummary.join('\n')}`;
        
        if (!confirm(confirmMessage)) {
            return;
        }

        // Show loading state
        const updateButton = document.querySelector('#updateModal .btn-primary');
        const originalText = updateButton.innerHTML;
        updateButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
        updateButton.disabled = true;

        fetch(`/admin/training-applications/${id}/status`, {
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

        // Show modal while loading
        const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
        modal.show();

        // Fetch application details
        fetch(`/admin/training-applications/${id}`)
            .then(response => response.json())
            .then(response => {
                console.log('Response:', response);

                if (!response.success) {
                    throw new Error('Failed to load application details');
                }

                const data = response.data;

                // Format the details HTML
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

                // Update modal content
                document.getElementById('applicationDetails').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Personal Information</h6>
                            <p><strong>Application #:</strong> ${data.application_number}</p>
                            <p><strong>Name:</strong> ${data.full_name}</p>
                            <p><strong>Mobile:</strong> ${data.mobile_number}</p>
                            <p><strong>Email:</strong> ${data.email}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Training Information</h6>
                            <p><strong>Training Type:</strong> ${data.training_type_display}</p>
                            <p><strong>Current Status:</strong> 
                                <span class="badge bg-${data.status_color}">${data.formatted_status}</span>
                            </p>
                            <p><strong>Date Applied:</strong> ${data.created_at}</p>
                            <p><strong>Last Updated:</strong> ${data.updated_at}</p>
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

    // View documents
    function viewDocuments(documentPaths) {
        const documentViewer = document.getElementById('documentViewer');
        
        if (!documentPaths || documentPaths.length === 0) {
            documentViewer.innerHTML = '<p>No documents available.</p>';
        } else {
            let documentsHtml = '';
            
            documentPaths.forEach((path, index) => {
                const fileExtension = path.split('.').pop().toLowerCase();
                const fileName = path.split('/').pop();
                
                documentsHtml += `<div class="document-item">
                    <h6>Document ${index + 1}: ${fileName}</h6>`;
                
                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                    documentsHtml += `<img src="/storage/${path}" class="img-fluid" alt="Supporting Document ${index + 1}">`;
                } else if (fileExtension === 'pdf') {
                    documentsHtml += `<embed src="/storage/${path}" type="application/pdf" width="100%" height="400px">`;
                } else {
                    documentsHtml += `<p>Document type not supported for preview. <a href="/storage/${path}" target="_blank">Download</a></p>`;
                }
                
                documentsHtml += '</div>';
            });
            
            documentViewer.innerHTML = documentsHtml;
        }

        const modal = new bootstrap.Modal(document.getElementById('documentModal'));
        modal.show();
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
</script>
@endsection