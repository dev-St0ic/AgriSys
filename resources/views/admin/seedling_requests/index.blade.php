{{-- resources/views/admin/seedling_requests/index.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Seedling Requests')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.seedling.requests') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search requests..." 
                       value="{{ request('search') }}" style="width: 250px;">
                <select name="status" class="form-select" style="width: 150px;">
                    <option value="">All Status</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.seedling.requests') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    @if($requests->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Request #</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Barangay</th>
                                <th>Items Requested</th>
                                <th>Total Qty</th>
                                <th>Documents</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $request->request_number }}</strong>
                                </td>
                                <td>{{ $request->full_name }}</td>
                                <td>{{ $request->contact_number }}</td>
                                <td>{{ $request->barangay }}</td>
                                <td>
                                    @if($request->vegetables && count($request->vegetables) > 0)
                                        <div class="mb-1">
                                            <strong style="color: #28a745;">🌱 Vegetables:</strong><br>
                                            <small>{{ $request->formatted_vegetables }}</small>
                                        </div>
                                    @endif
                                    
                                    @if($request->fruits && count($request->fruits) > 0)
                                        <div class="mb-1">
                                            <strong style="color: #17a2b8;">🍎 Fruits:</strong><br>
                                            <small>{{ $request->formatted_fruits }}</small>
                                        </div>
                                    @endif
                                    
                                    @if($request->fertilizers && count($request->fertilizers) > 0)
                                        <div class="mb-1">
                                            <strong style="color: #ffc107;">🌿 Fertilizers:</strong><br>
                                            <small>{{ $request->formatted_fertilizers }}</small>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6 px-3 py-2">
                                        {{ $request->total_quantity ?? $request->requested_quantity }} pcs
                                    </span>
                                </td>
                                <td>
                                    @if($request->hasDocuments())
                                        @if(($request->total_quantity ?? $request->requested_quantity) >= 100)
                                            <span class="badge bg-success mb-1">Required ✓</span><br>
                                        @else
                                            <span class="badge bg-info mb-1">Provided ✓</span><br>
                                        @endif
                                        <a href="{{ $request->document_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-alt"></i> View File
                                        </a>
                                    @else
                                        @if(($request->total_quantity ?? $request->requested_quantity) >= 100)
                                            <span class="badge bg-danger">Required ✗</span>
                                        @else
                                            <span class="badge bg-secondary">None</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $request->status_color }} fs-6 px-3 py-2">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal{{ $request->id }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#statusModal{{ $request->id }}">
                                            <i class="fas fa-edit"></i> Update
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- View Modal -->
                            <div class="modal fade" id="viewModal{{ $request->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Request Details - {{ $request->request_number }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Personal Information</h6>
                                                    <p><strong>Name:</strong> {{ $request->full_name }}</p>
                                                    <p><strong>Contact:</strong> {{ $request->contact_number }}</p>
                                                    <p><strong>Barangay:</strong> {{ $request->barangay }}</p>
                                                    <p><strong>Address:</strong> {{ $request->address }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Request Information</h6>
                                                    <p><strong>Total Quantity:</strong> {{ $request->total_quantity }} pcs</p>
                                                    <p><strong>Status:</strong> 
                                                        <span class="badge bg-{{ $request->status_color }}">
                                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                        </span>
                                                    </p>
                                                    <p><strong>Date Submitted:</strong> {{ $request->created_at->format('F d, Y g:i A') }}</p>
                                                    @if($request->remarks)
                                                        <p><strong>Remarks:</strong> {{ $request->remarks }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <hr>
                                            
                                            <h6>Selected Items</h6>
                                            @if($request->vegetables && count($request->vegetables) > 0)
                                                <p><strong>🌱 Vegetables:</strong> {{ $request->formatted_vegetables }}</p>
                                            @endif
                                            @if($request->fruits && count($request->fruits) > 0)
                                                <p><strong>🍎 Fruits:</strong> {{ $request->formatted_fruits }}</p>
                                            @endif
                                            @if($request->fertilizers && count($request->fertilizers) > 0)
                                                <p><strong>🌿 Fertilizers:</strong> {{ $request->formatted_fertilizers }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Update Modal -->
                            <div class="modal fade" id="statusModal{{ $request->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.seedling.update-status', $request) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Status - {{ $request->request_number }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="status{{ $request->id }}" class="form-label">Status</label>
                                                    <select name="status" id="status{{ $request->id }}" class="form-select" required>
                                                        <option value="under_review" {{ $request->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                                        <option value="approved" {{ $request->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="approved_quantity{{ $request->id }}" class="form-label">Approved Quantity</label>
                                                    <input type="number" name="approved_quantity" id="approved_quantity{{ $request->id }}" 
                                                           class="form-control" value="{{ $request->approved_quantity ?? $request->total_quantity }}" 
                                                           min="1" max="{{ $request->total_quantity }}">
                                                    <small class="text-muted">Max: {{ $request->total_quantity }} pcs</small>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="remarks{{ $request->id }}" class="form-label">Remarks</label>
                                                    <textarea name="remarks" id="remarks{{ $request->id }}" class="form-control" rows="3">{{ $request->remarks }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Status</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                <p class="text-muted">
                    Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} results
                </p>
            </div>
            <div>
                {{ $requests->links('pagination::bootstrap-4') }}
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No seedling requests found</h5>
                <p class="text-muted">
                    @if(request('search') || request('status'))
                        No requests match your search criteria.
                    @else
                        There are no seedling requests yet.
                    @endif
                </p>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.seedling.requests') }}" class="btn btn-outline-primary">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection