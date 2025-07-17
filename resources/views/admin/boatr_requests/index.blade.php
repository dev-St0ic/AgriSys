@extends('layouts.app')

@section('title', 'BoatR Registrations - AgriSys Admin')
@section('page-title', 'BoatR Registrations')

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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRegistrations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ship fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
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
                                Inspection Required
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inspectionRequiredCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
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
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.boatr.requests') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="inspection_required" {{ request('status') == 'inspection_required' ? 'selected' : '' }}>Inspection Required</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="boat_type" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Boat Types</option>
                            <option value="Spoon" {{ request('boat_type') == 'Spoon' ? 'selected' : '' }}>Spoon</option>
                            <option value="Plumb" {{ request('boat_type') == 'Plumb' ? 'selected' : '' }}>Plumb</option>
                            <option value="Banca" {{ request('boat_type') == 'Banca' ? 'selected' : '' }}>Banca</option>
                            <option value="Rake Stem - Rake Stern" {{ request('boat_type') == 'Rake Stem - Rake Stern' ? 'selected' : '' }}>Rake Stem - Rake Stern</option>
                            <option value="Rake Stem - Transom/Spoon/Plumb Stern" {{ request('boat_type') == 'Rake Stem - Transom/Spoon/Plumb Stern' ? 'selected' : '' }}>Rake Stem - Transom/Spoon/Plumb Stern</option>
                            <option value="Skiff (Typical Design)" {{ request('boat_type') == 'Skiff (Typical Design)' ? 'selected' : '' }}>Skiff (Typical Design)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="primary_fishing_gear" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Fishing Gear</option>
                            <option value="Hook and Line" {{ request('primary_fishing_gear') == 'Hook and Line' ? 'selected' : '' }}>Hook and Line</option>
                            <option value="Bottom Set Gill Net" {{ request('primary_fishing_gear') == 'Bottom Set Gill Net' ? 'selected' : '' }}>Bottom Set Gill Net</option>
                            <option value="Fish Trap" {{ request('primary_fishing_gear') == 'Fish Trap' ? 'selected' : '' }}>Fish Trap</option>
                            <option value="Fish Coral" {{ request('primary_fishing_gear') == 'Fish Coral' ? 'selected' : '' }}>Fish Coral</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="Search name, vessel name, FishR number..." value="{{ request('search') }}" 
                               oninput="autoSearch()" id="searchInput">
                    </div>
                    <div class="col-md-2">
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
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-ship me-2"></i>BoatR Applications
            </h6>
            <a href="{{ route('admin.boatr.export') }}" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="registrationsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Application #</th>
                            <th>Name</th>
                            <th>FishR Number</th>
                            <th>Vessel Name</th>
                            <th>Boat Type</th>
                            <th>Dimensions</th>
                            <th>Engine HP</th>
                            <th>Status</th>
                            <th>Inspection</th>
                            <th>Date Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $registration->application_number }}</strong>
                                </td>
                                <td>{{ $registration->full_name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $registration->fishr_number }}</span>
                                </td>
                                <td>{{ $registration->vessel_name }}</td>
                                <td>{{ $registration->boat_type }}</td>
                                <td>
                                    <small>{{ $registration->boat_dimensions }}</small>
                                </td>
                                <td>{{ $registration->engine_horsepower }} HP</td>
                                <td>
                                    <span class="badge bg-{{ $registration->status_color }}">
                                        {{ $registration->formatted_status }}
                                    </span>
                                </td>
                                <td>
                                    @if($registration->inspection_completed)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Completed
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $registration->created_at->format('M d, Y g:i A') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewRegistration({{ $registration->id }})" 
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="showUpdateModal({{ $registration->id }}, '{{ $registration->status }}')" 
                                                title="Update Status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        @if(!$registration->inspection_completed && in_array($registration->status, ['pending', 'inspection_required']))
                                            <button class="btn btn-sm btn-outline-warning" 
                                                    onclick="showInspectionModal({{ $registration->id }})" 
                                                    title="Complete Inspection">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        @endif
                                        
                                        @if($registration->supporting_document_path)
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="viewDocument('{{ $registration->supporting_document_path }}')" 
                                                    title="View Document">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-ship fa-3x mb-3"></i>
                                    <p>No BoatR applications found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($registrations->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $registrations->links() }}
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
                                    <p class="mb-1"><strong>ID:</strong> <span id="updateRegId"></span></p>
                                    <p class="mb-1"><strong>Application #:</strong> <span id="updateRegNumber"></span></p>
                                    <p class="mb-1"><strong>Name:</strong> <span id="updateRegName"></span></p>
                                    <p class="mb-1"><strong>Vessel:</strong> <span id="updateRegVessel"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>FishR #:</strong> <span id="updateRegFishR"></span></p>
                                    <p class="mb-1"><strong>Boat Type:</strong> <span id="updateRegBoatType"></span></p>
                                    <p class="mb-1"><strong>Current Status:</strong> <span id="updateRegCurrentStatus"></span></p>
                                    <p class="mb-1"><strong>Inspection:</strong> <span id="updateRegInspection"></span></p>
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
                                <option value="pending">Pending</option>
                                <option value="inspection_required">Inspection Required</option>
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
                    <button type="button" class="btn btn-primary" onclick="updateRegistrationStatus()">Update Status</button>
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
                            <div class="form-text">Upload inspection report, boat photos, or other supporting documents. (PDF, JPG, JPEG, PNG - Max 10MB)</div>
                        </div>
                        <div class="mb-3">
                            <label for="inspection_notes" class="form-label">Inspection Notes (Optional):</label>
                            <textarea class="form-control" id="inspection_notes" rows="3" 
                                      placeholder="Add any notes about the inspection..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="completeInspection()">Complete Inspection</button>
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
                <div class="modal-body" id="registrationDetails">
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
                        <i class="fas fa-file-alt me-2"></i>Supporting Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="documentViewer">
                    <!-- Document will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <style>
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
    </style>
@endsection

@section('scripts')
<script>
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

    // Show update modal
    function showUpdateModal(id, currentStatus) {
        fetch(`/admin/boatr-applications/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('updateRegistrationId').value = id;
                
                // Populate application info display
                document.getElementById('updateRegId').textContent = data.id;
                document.getElementById('updateRegNumber').textContent = data.application_number;
                document.getElementById('updateRegName').textContent = data.full_name;
                document.getElementById('updateRegVessel').textContent = data.vessel_name;
                document.getElementById('updateRegFishR').textContent = data.fishr_number;
                document.getElementById('updateRegBoatType').textContent = data.boat_type;
                
                // Show current status
                const currentStatusElement = document.getElementById('updateRegCurrentStatus');
                currentStatusElement.innerHTML = `<span class="badge bg-${data.status_color}">${data.formatted_status}</span>`;
                
                // Show inspection status
                const inspectionElement = document.getElementById('updateRegInspection');
                inspectionElement.innerHTML = data.inspection_completed ? 
                    '<span class="badge bg-success">Completed</span>' : 
                    '<span class="badge bg-warning">Pending</span>';
                
                // Set form values
                document.getElementById('newStatus').value = currentStatus;
                document.getElementById('remarks').value = '';
                
                const modal = new bootstrap.Modal(document.getElementById('updateModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading application details:', error);
                alert('Error loading application details');
            });
    }

    // Show inspection modal
    function showInspectionModal(id) {
        document.getElementById('inspectionRegistrationId').value = id;
        document.getElementById('supporting_document').value = '';
        document.getElementById('inspection_notes').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('inspectionModal'));
        modal.show();
    }

    // Update registration status
    function updateRegistrationStatus() {
        const id = document.getElementById('updateRegistrationId').value;
        const newStatus = document.getElementById('newStatus').value;
        const remarks = document.getElementById('remarks').value;
        
        if (!newStatus) {
            alert('Please select a status');
            return;
        }

        if (confirm(`Are you sure you want to change the status to "${newStatus}"?`)) {
            fetch(`/admin/boatr-applications/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    status: newStatus,
                    remarks: remarks 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('updateModal')).hide();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating application status');
            });
        }
    }

    // Complete inspection
    function completeInspection() {
        const id = document.getElementById('inspectionRegistrationId').value;
        const fileInput = document.getElementById('supporting_document');
        const notes = document.getElementById('inspection_notes').value;
        
        if (!fileInput.files[0]) {
            alert('Please select a supporting document');
            return;
        }

        const formData = new FormData();
        formData.append('supporting_document', fileInput.files[0]);
        formData.append('document_notes', notes);

        if (confirm('Are you sure you want to complete the inspection for this application?')) {
            fetch(`/admin/boatr-applications/${id}/complete-inspection`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('inspectionModal')).hide();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error completing inspection');
            });
        }
    }

    // View application details
    function viewRegistration(id) {
        fetch(`/admin/boatr-applications/${id}`)
            .then(response => response.json())
            .then(data => {
                let remarksHtml = '';
                if (data.remarks) {
                    remarksHtml = `
                        <div class="col-12 mt-3">
                            <h6>Remarks</h6>
                            <div class="alert alert-info">
                                <p class="mb-1"><strong>Note:</strong> ${data.remarks}</p>
                                ${data.reviewed_at ? `<small class="text-muted">Updated on ${data.reviewed_at}${data.reviewed_by_name ? ` by ${data.reviewed_by_name}` : ''}</small>` : ''}
                            </div>
                        </div>
                    `;
                }

                document.getElementById('registrationDetails').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Personal Information</h6>
                            <p><strong>Application #:</strong> ${data.application_number}</p>
                            <p><strong>Name:</strong> ${data.full_name}</p>
                            <p><strong>FishR Number:</strong> ${data.fishr_number}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Vessel Information</h6>
                            <p><strong>Vessel Name:</strong> ${data.vessel_name}</p>
                            <p><strong>Boat Type:</strong> ${data.boat_type}</p>
                            <p><strong>Dimensions:</strong> ${data.boat_dimensions}</p>
                            <p><strong>Engine Type:</strong> ${data.engine_type}</p>
                            <p><strong>Engine HP:</strong> ${data.engine_horsepower} HP</p>
                            <p><strong>Primary Fishing Gear:</strong> ${data.primary_fishing_gear}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Application Status</h6>
                            <p><strong>Status:</strong> <span class="badge bg-${data.status_color}">${data.formatted_status}</span></p>
                            <p><strong>Inspection:</strong> ${data.inspection_completed ? '<span class="badge bg-success">Completed</span>' : '<span class="badge bg-warning">Pending</span>'}</p>
                            ${data.inspection_date ? `<p><strong>Inspection Date:</strong> ${data.inspection_date}</p>` : ''}
                        </div>
                        <div class="col-md-6">
                            <h6>Timeline</h6>
                            <p><strong>Date Applied:</strong> ${data.created_at}</p>
                            <p><strong>Last Updated:</strong> ${data.updated_at}</p>
                            ${data.reviewed_at ? `<p><strong>Reviewed:</strong> ${data.reviewed_at}</p>` : ''}
                        </div>
                        ${remarksHtml}
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading application details');
            });
    }

    // View document
    function viewDocument(path) {
        const documentViewer = document.getElementById('documentViewer');
        const fileExtension = path.split('.').pop().toLowerCase();
        
        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            documentViewer.innerHTML = `<img src="/storage/${path}" class="img-fluid" alt="Supporting Document">`;
        } else if (fileExtension === 'pdf') {
            documentViewer.innerHTML = `<embed src="/storage/${path}" type="application/pdf" width="100%" height="600px">`;
        } else {
            documentViewer.innerHTML = `<p>Document type not supported for preview. <a href="/storage/${path}" target="_blank">Download</a></p>`;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('documentModal'));
        modal.show();
    }
</script>
@endsection