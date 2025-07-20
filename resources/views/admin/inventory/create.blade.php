@extends('layouts.app')

@section('title', 'FishR Registrations - AgriSys Admin')
@section('page-title', 'FishR Registrations')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Registrations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRegistrations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fish fa-2x text-gray-300"></i>
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

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fishr.requests') }}">
                <div class="row">
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="livelihood" class="form-select form-select-sm">
                            <option value="">All Livelihoods</option>
                            <option value="capture" {{ request('livelihood') == 'capture' ? 'selected' : '' }}>Capture Fishing</option>
                            <option value="aquaculture" {{ request('livelihood') == 'aquaculture' ? 'selected' : '' }}>Aquaculture</option>
                            <option value="vending" {{ request('livelihood') == 'vending' ? 'selected' : '' }}>Fish Vending</option>
                            <option value="processing" {{ request('livelihood') == 'processing' ? 'selected' : '' }}>Fish Processing</option>
                            <option value="others" {{ request('livelihood') == 'others' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="Search name, number, contact..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.fishr.requests') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                               placeholder="From Date" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                               placeholder="To Date" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="barangay" class="form-select form-select-sm">
                            <option value="">All Barangays</option>
                            <option value="Bagong Silang" {{ request('barangay') == 'Bagong Silang' ? 'selected' : '' }}>Bagong Silang</option>
                            <option value="Cuyab" {{ request('barangay') == 'Cuyab' ? 'selected' : '' }}>Cuyab</option>
                            <option value="Estrella" {{ request('barangay') == 'Estrella' ? 'selected' : '' }}>Estrella</option>
                            <option value="Poblacion" {{ request('barangay') == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                            <option value="Riverside" {{ request('barangay') == 'Riverside' ? 'selected' : '' }}>Riverside</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Registrations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-fish me-2"></i>FishR Registrations
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="registrationsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Registration #</th>
                            <th>Name</th>
                            <th>Sex</th>
                            <th>Barangay</th>
                            <th>Contact</th>
                            <th>Livelihood</th>
                            <th>Status</th>
                            <th>Date Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $registration->registration_number }}</strong>
                                </td>
                                <td>{{ $registration->full_name }}</td>
                                <td>{{ $registration->sex }}</td>
                                <td>{{ $registration->barangay }}</td>
                                <td>{{ $registration->contact_number }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $registration->livelihood_description }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $registration->status_color }}">
                                        {{ $registration->formatted_status }}
                                    </span>
                                </td>
                                <td>{{ $registration->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewRegistration({{ $registration->id }})" 
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($registration->status === 'under_review')
                                            <button class="btn btn-sm btn-outline-success" 
                                                    onclick="approveRegistration({{ $registration->id }})" 
                                                    title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="rejectRegistration({{ $registration->id }})" 
                                                    title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        @if($registration->document_path)
                                            <button class="btn btn-sm btn-outline-info" 
                                                    onclick="viewDocument('{{ $registration->document_path }}')" 
                                                    title="View Document">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        @endif
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteRegistration({{ $registration->id }})" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-fish fa-3x mb-3"></i>
                                    <p>No FishR registrations found.</p>
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
    // View registration details
    function viewRegistration(id) {
        fetch(`/admin/fishr-registrations/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('registrationDetails').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Personal Information</h6>
                            <p><strong>Registration #:</strong> ${data.registration_number}</p>
                            <p><strong>Name:</strong> ${data.full_name}</p>
                            <p><strong>Sex:</strong> ${data.sex}</p>
                            <p><strong>Contact:</strong> ${data.contact_number}</p>
                            <p><strong>Barangay:</strong> ${data.barangay}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Livelihood Information</h6>
                            <p><strong>Main Livelihood:</strong> ${data.livelihood_description}</p>
                            ${data.other_livelihood ? `<p><strong>Other Livelihood:</strong> ${data.other_livelihood}</p>` : ''}
                            <p><strong>Status:</strong> <span class="badge bg-${data.status_color}">${data.formatted_status}</span></p>
                            <p><strong>Date Applied:</strong> ${data.created_at}</p>
                            <p><strong>Last Updated:</strong> ${data.updated_at}</p>
                        </div>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading registration details');
            });
    }

    // Approve registration
    function approveRegistration(id) {
        if (confirm('Are you sure you want to approve this registration?')) {
            fetch(`/admin/fishr-registrations/${id}/approve`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error approving registration');
            });
        }
    }

    // Reject registration
    function rejectRegistration(id) {
        if (confirm('Are you sure you want to reject this registration?')) {
            fetch(`/admin/fishr-registrations/${id}/reject`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error rejecting registration');
            });
        }
    }

    // Delete registration
    function deleteRegistration(id) {
        if (confirm('Are you sure you want to delete this registration? This action cannot be undone.')) {
            fetch(`/admin/fishr-registrations/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting registration');
            });
        }
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
@endsection mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Registrations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRegistrations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fish fa-2x text-gray-300"></i>
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

    <!-- Registrations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-fish me-2"></i>FishR Registrations
            </h6>
            <div class="d-flex align-items-center">
                <input type="text" class="form-control form-control-sm me-2" placeholder="Search..." 
                       id="searchInput" onkeyup="searchRegistrations()">
                <button class="btn btn-sm btn-outline-primary" onclick="clearSearch()">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="registrationsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Registration #</th>
                            <th>Name</th>
                            <th>Sex</th>
                            <th>Barangay</th>
                            <th>Contact</th>
                            <th>Livelihood</th>
                            <th>Status</th>
                            <th>Date Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $registration->registration_number }}</strong>
                                </td>
                                <td>{{ $registration->full_name }}</td>
                                <td>{{ $registration->sex }}</td>
                                <td>{{ $registration->barangay }}</td>
                                <td>{{ $registration->contact_number }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $registration->livelihood_description }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $registration->status_color }}">
                                        {{ $registration->formatted_status }}
                                    </span>
                                </td>
                                <td>{{ $registration->created_at->format('M d, Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="viewRegistration({{ $registration->id }})" 
                                            title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    @if($registration->document_path)
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="viewDocument('{{ $registration->document_path }}')" 
                                                title="View Document">
                                            <i class="fas fa-file-alt"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-fish fa-3x mb-3"></i>
                                    <p>No FishR registrations found.</p>
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
    // Simple search functionality
    function searchRegistrations() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('registrationsTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            
            rows[i].style.display = found ? '' : 'none';
        }
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        
        // Show all rows
        const rows = document.querySelectorAll('#registrationsTable tbody tr');
        rows.forEach(row => row.style.display = '');
    }

    // View registration details
    function viewRegistration(id) {
        fetch(`/admin/fishr-registrations/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('registrationDetails').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Personal Information</h6>
                            <p><strong>Registration #:</strong> ${data.registration_number}</p>
                            <p><strong>Name:</strong> ${data.full_name}</p>
                            <p><strong>Sex:</strong> ${data.sex}</p>
                            <p><strong>Contact:</strong> ${data.contact_number}</p>
                            <p><strong>Barangay:</strong> ${data.barangay}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Livelihood Information</h6>
                            <p><strong>Main Livelihood:</strong> ${data.livelihood_description}</p>
                            ${data.other_livelihood ? `<p><strong>Other Livelihood:</strong> ${data.other_livelihood}</p>` : ''}
                            <p><strong>Status:</strong> <span class="badge bg-${data.status_color}">${data.formatted_status}</span></p>
                            <p><strong>Date Applied:</strong> ${data.created_at}</p>
                            <p><strong>Last Updated:</strong> ${data.updated_at}</p>
                        </div>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading registration details');
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