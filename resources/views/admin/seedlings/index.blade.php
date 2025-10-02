{{-- resources/views/admin/seedlings/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Seedling Requests - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-seedling text-primary me-2"></i>
        <span class="text-primary fw-bold">Seedling Requests</span>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Action Buttons Row -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Seedling Requests Management</h3>
            <div class="btn-group">
                <a href="{{ route('admin.seedlings.categories.index') }}" class="btn btn-success">
                    <i class="fas fa-layer-group me-2"></i>Manage Categories & Items
                </a>
                <a href="{{ route('admin.seedlings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>Create Request
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                    Total Requests
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $totalRequests }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-primary bg-opacity-10">
                                    <i class="fas fa-seedling text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                    Pending
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $underReviewCount }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-warning bg-opacity-10">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                    Fully Approved
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $approvedCount }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-success bg-opacity-10">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Partially Approved
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $partiallyApprovedCount }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-info bg-opacity-10">
                                    <i class="fas fa-check-double text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                    Rejected
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $rejectedCount }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-danger bg-opacity-10">
                                    <i class="fas fa-times-circle text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-3 border-bottom">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Filters & Search
                </h6>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.seedlings.requests') }}" id="filterForm">
                    <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                    <div class="row g-3">
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm border-light" onchange="submitFilterForm()">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Fully Approved</option>
                                <option value="partially_approved" {{ request('status') == 'partially_approved' ? 'selected' : '' }}>Partially Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select form-select-sm border-light" onchange="submitFilterForm()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="barangay" class="form-select form-select-sm border-light" onchange="submitFilterForm()">
                                <option value="">All Barangay</option>
                                @foreach ($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                                        {{ $barangay }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="search" class="form-control form-control-sm border-light"
                                placeholder="Search..." value="{{ request('search') }}" oninput="autoSearch()">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#dateFilterModal">
                                <i class="fas fa-calendar-alt me-1"></i>Date Filter
                            </button>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ route('admin.seedlings.requests') }}" class="btn btn-light btn-sm w-100 border">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($requests->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="px-3 py-3 fw-medium text-white border-end">Date Applied</th>
                                    <th class="px-3 py-3 fw-medium text-white border-end">Request #</th>
                                    <th class="px-3 py-3 fw-medium text-white border-end">Name</th>
                                    <th class="px-3 py-3 fw-medium text-white border-end">Barangay</th>
                                    <th class="px-3 py-3 fw-medium text-white border-end">Requested Items</th>
                                    <th class="px-3 py-3 fw-medium text-white border-end">Status</th>
                                    <th class="px-3 py-3 fw-medium text-white text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $request)
                                    <tr class="border-bottom">
                                        <td class="px-3 py-3 border-end">
                                            <small class="text-muted">{{ $request->created_at->format('M d, Y') }}</small><br>
                                            <small class="text-muted">{{ $request->created_at->format('g:i A') }}</small>
                                        </td>
                                        <td class="px-3 py-3 border-end">
                                            <span class="fw-bold text-primary">{{ $request->request_number }}</span>
                                        </td>
                                        <td class="px-3 py-3 border-end">
                                            <span class="fw-medium">{{ $request->full_name }}</span>
                                        </td>
                                        <td class="px-3 py-3 border-end">
                                            <span class="text-dark">{{ $request->barangay }}</span>
                                        </td>
                                        <td class="px-3 py-3 border-end">
                                            @php
                                                $itemsByCategory = $request->items->groupBy('category_id');
                                            @endphp
                                            
                                            @foreach($itemsByCategory as $categoryId => $items)
                                                @php
                                                    $category = $items->first()->category;
                                                    $approvedItems = $items->where('status', 'approved');
                                                    $rejectedItems = $items->where('status', 'rejected');
                                                    $pendingItems = $items->where('status', 'pending');
                                                @endphp
                                                
                                                <div class="mb-3">
                                                    <strong class="text-primary">
                                                        <i class="fas {{ $category->icon ?? 'fa-leaf' }} me-1"></i>
                                                        {{ $category->display_name }}:
                                                    </strong>
                                                    
                                                    @if($approvedItems->count() > 0)
                                                        <div class="mt-1">
                                                            <small class="text-success fw-medium">✓ Approved:</small>
                                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                                @foreach($approvedItems as $item)
                                                                    <span class="badge bg-success text-white">
                                                                        <i class="fas fa-check-circle me-1"></i>
                                                                        {{ $item->item_name }} ({{ $item->requested_quantity }})
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($rejectedItems->count() > 0)
                                                        <div class="mt-1">
                                                            <small class="text-danger fw-medium">✗ Rejected:</small>
                                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                                @foreach($rejectedItems as $item)
                                                                    <span class="badge bg-danger text-white">
                                                                        <i class="fas fa-times-circle me-1"></i>
                                                                        {{ $item->item_name }} ({{ $item->requested_quantity }})
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if($pendingItems->count() > 0)
                                                        <div class="mt-1">
                                                            <small class="text-warning fw-medium">⏳ Pending:</small>
                                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                                @foreach($pendingItems as $item)
                                                                    <span class="badge bg-warning text-white">
                                                                        <i class="fas fa-clock me-1"></i>
                                                                        {{ $item->item_name }} ({{ $item->requested_quantity }})
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="px-3 py-3 border-end">
                                            <span class="badge badge-status-lg bg-{{ match ($request->status) {
                                                'approved' => 'success',
                                                'partially_approved' => 'warning',
                                                'rejected' => 'danger',
                                                'under_review', 'pending' => 'secondary',
                                                default => 'secondary',
                                            } }}">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#viewModal{{ $request->id }}">
                                                    <i class="fas fa-eye"></i> View
                                                </button>

                                                  <!-- Add New Request Button
                                                <div class="mb-3">
                                                    <a href="{{ route('admin.seedlings.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus-circle me-2"></i>Create New Request
                                                    </a>
                                                </div>
                                                 @if(in_array($request->status, ['pending', 'under_review']))
                                                    <a href="{{ route('admin.seedlings.edit', $request) }}" class="btn btn-outline-warning">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif -->
                                                <button type="button" class="btn btn-outline-success"
                                                    data-bs-toggle="modal" data-bs-target="#updateModal{{ $request->id }}">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>
                                            </div>
                                            
                                            
                                            @if ($request->hasDocuments())
                                                <button type="button" class="btn btn-sm btn-outline-info mt-1"
                                                    onclick="viewDocument('{{ $request->document_path }}')" title="View Document">
                                                    <i class="fas fa-file-alt"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal{{ $request->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-light border-bottom">
                                                    <h5 class="modal-title fw-bold text-dark">
                                                        <i class="fas fa-eye text-primary me-2"></i>
                                                        Request Details - {{ $request->request_number }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>Personal Information</h6>
                                                            <p><strong>Name:</strong> {{ $request->full_name }}</p>
                                                            <p><strong>Contact:</strong> {{ $request->contact_number }}</p>
                                                            <p><strong>Email:</strong> {{ $request->email ?? 'N/A' }}</p>
                                                            <p><strong>Barangay:</strong> {{ $request->barangay }}</p>
                                                            <p><strong>Address:</strong> {{ $request->address }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Request Information</h6>
                                                            <p><strong>Total Quantity:</strong> {{ $request->total_quantity }}</p>
                                                            <p><strong>Status:</strong>
                                                                <span class="badge bg-{{ match ($request->status) {
                                                                    'approved' => 'success',
                                                                    'partially_approved' => 'warning',
                                                                    'rejected' => 'danger',
                                                                    default => 'secondary',
                                                                } }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                                </span>
                                                            </p>
                                                            <p><strong>Date:</strong> {{ $request->created_at->format('F d, Y g:i A') }}</p>
                                                            @if ($request->remarks)
                                                                <p><strong>Remarks:</strong> {{ $request->remarks }}</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <h6>Requested Items by Category</h6>

                                                    @php
                                                        $itemsByCategory = $request->items->groupBy('category_id');
                                                    @endphp

                                                    @foreach($itemsByCategory as $categoryId => $items)
                                                        @php
                                                            $category = $items->first()->category;
                                                        @endphp
                                                        <div class="mb-3 p-3 border rounded">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <strong class="text-primary">
                                                                    <i class="fas {{ $category->icon ?? 'fa-leaf' }} me-2"></i>
                                                                    {{ $category->display_name }}
                                                                </strong>
                                                            </div>
                                                            <ul class="mb-0">
                                                                @foreach($items as $item)
                                                                    <li>
                                                                        {{ $item->item_name }} - {{ $item->requested_quantity }} {{ $item->categoryItem->unit ?? 'pcs' }}
                                                                        <span class="badge bg-{{ $item->status_color }} ms-2">
                                                                            {{ ucfirst($item->status) }}
                                                                        </span>
                                                                        @if($item->status === 'approved')
                                                                            <small class="text-muted">(Stock deducted)</small>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endforeach

                                                    @if ($request->hasDocuments())
                                                        <hr>
                                                        <h6>Supporting Documents</h6>
                                                        <a href="{{ $request->document_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-file-alt"></i> View Document
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Update Modal -->
                                    <div class="modal fade" id="updateModal{{ $request->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header bg-light border-bottom">
                                                    <h5 class="modal-title fw-bold text-dark">
                                                        <i class="fas fa-edit text-success me-2"></i>
                                                        Update Items - {{ $request->request_number }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="{{ route('admin.seedlings.update-items', $request) }}" id="updateForm{{ $request->id }}">
                                                        @csrf
                                                        @method('PATCH')

                                                        @php
                                                            $itemsByCategory = $request->items->groupBy('category_id');
                                                        @endphp

                                                        @foreach($itemsByCategory as $categoryId => $items)
                                                            @php
                                                                $category = $items->first()->category;
                                                            @endphp
                                                            <div class="mb-4 p-3 border-0 bg-light rounded-3">
                                                                <h6 class="mb-3 fw-bold text-primary">
                                                                    <i class="fas {{ $category->icon ?? 'fa-leaf' }} me-2"></i>
                                                                    {{ $category->display_name }}
                                                                </h6>

                                                                @foreach($items as $item)
                                                                    @php
                                                                        $stockCheck = $item->categoryItem 
                                                                            ? $item->categoryItem->checkInventoryAvailability($item->requested_quantity)
                                                                            : ['available' => false, 'current_stock' => 0];
                                                                    @endphp
                                                                    
                                                                    <div class="item-card d-flex align-items-center justify-content-between mb-3 p-3 
                                                                        {{ $item->status === 'approved' ? 'bg-success bg-opacity-10 border border-success' : 
                                                                           ($item->status === 'rejected' ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-white border') }} 
                                                                        rounded-3 shadow-sm">
                                                                        <div class="flex-grow-1">
                                                                            <div class="d-flex align-items-center mb-2">
                                                                                <span class="fw-medium text-dark">{{ $item->item_name }}</span>
                                                                                <span class="badge bg-light text-muted ms-2">
                                                                                    {{ $item->requested_quantity }} {{ $item->categoryItem->unit ?? 'pcs' }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <small class="text-muted">Requested:</small>
                                                                                <small class="fw-medium">{{ $item->requested_quantity }}</small>
                                                                                <span class="text-muted">•</span>
                                                                                <small class="{{ $stockCheck['available'] ? 'text-success' : 'text-warning' }}">
                                                                                    <i class="fas fa-box me-1"></i>Stock:
                                                                                    <span class="fw-bold">{{ $stockCheck['current_stock'] }}</span>
                                                                                    @if($stockCheck['available'])
                                                                                        <i class="fas fa-check text-success ms-1"></i>
                                                                                    @else
                                                                                        <i class="fas fa-exclamation-triangle text-warning ms-1"></i>
                                                                                    @endif
                                                                                </small>
                                                                            </div>
                                                                            @if(!$stockCheck['available'])
                                                                                <span class="badge bg-warning text-dark mt-2">
                                                                                    <i class="fas fa-exclamation-triangle me-1"></i>Insufficient Stock
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="ms-3">
                                                                            <select name="item_statuses[{{ $item->id }}]" 
                                                                                class="form-select form-select-sm border-light" style="min-width: 130px;">
                                                                                <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>
                                                                                    Pending
                                                                                </option>
                                                                                <option value="approved" 
                                                                                    {{ $item->status === 'approved' ? 'selected' : '' }}
                                                                                    {{ !$stockCheck['available'] ? 'disabled' : '' }}>
                                                                                    Approved{{ !$stockCheck['available'] ? ' (No Stock)' : '' }}
                                                                                </option>
                                                                                <option value="rejected" {{ $item->status === 'rejected' ? 'selected' : '' }}>
                                                                                    Rejected
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endforeach

                                                        <div class="mb-3">
                                                            <label for="remarks{{ $request->id }}" class="form-label">General Remarks</label>
                                                            <textarea name="remarks" id="remarks{{ $request->id }}" 
                                                                class="form-control" rows="3" 
                                                                placeholder="Add any comments...">{{ $request->remarks }}</textarea>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" form="updateForm{{ $request->id }}" class="btn btn-primary">
                                                        <i class="fas fa-save me-2"></i>Update Items
                                                    </button>
                                                </div>
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
            @if ($requests->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            {{-- Previous Page Link --}}
                            @if ($requests->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Back</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $requests->previousPageUrl() }}" rel="prev">Back</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $requests->currentPage();
                                $lastPage = $requests->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);

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
                                        <a class="page-link" href="{{ $requests->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($requests->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $requests->nextPageUrl() }}" rel="next">Next</a>
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
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No seedling requests found</h5>
                    <p class="text-muted">
                        @if (request('search') || request('status'))
                            No requests match your search criteria.
                        @else
                            There are no seedling requests yet.
                        @endif
                    </p>
                    @if (request('search') || request('status'))
                        <a href="{{ route('admin.seedlings.requests') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Date Filter Modal -->
        <div class="modal fade" id="dateFilterModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-alt me-2"></i>Select Date Range
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary mb-3">
                                            <i class="fas fa-calendar-plus me-2"></i>Custom Date Range
                                        </h6>
                                        <div class="mb-3">
                                            <label for="modal_date_from" class="form-label">From Date</label>
                                            <input type="date" id="modal_date_from" class="form-control" value="{{ request('date_from') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="modal_date_to" class="form-label">To Date</label>
                                            <input type="date" id="modal_date_to" class="form-control" value="{{ request('date_to') }}">
                                        </div>
                                        <button type="button" class="btn btn-primary w-100" onclick="applyCustomDateRange()">
                                            <i class="fas fa-check me-2"></i>Apply Custom Range
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary mb-3">
                                            <i class="fas fa-clock me-2"></i>Quick Presets
                                        </h6>
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-success" onclick="setDateRange('today')">
                                                <i class="fas fa-calendar-day me-2"></i>Today
                                            </button>
                                            <button type="button" class="btn btn-outline-info" onclick="setDateRange('week')">
                                                <i class="fas fa-calendar-week me-2"></i>This Week
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" onclick="setDateRange('month')">
                                                <i class="fas fa-calendar me-2"></i>This Month
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" onclick="setDateRange('year')">
                                                <i class="fas fa-calendar-alt me-2"></i>This Year
                                            </button>
                                            <hr>
                                            <button type="button" class="btn btn-outline-danger" onclick="clearDateRange()">
                                                <i class="fas fa-calendar-times me-2"></i>Clear Date Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                            No date filter applied - showing all requests
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-status-lg {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            font-weight: 600;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .item-card {
            transition: all 0.2s ease-in-out;
        }

        .item-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .table-dark th {
            background-color: #212529 !important;
            color: #ffffff !important;
        }

        .border-light {
            border-color: #e9ecef !important;
        }

        .card {
            transition: box-shadow 0.15s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

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

        .btn-group > .btn {
            margin-right: 0.25rem;
        }

        .btn-group > .btn:last-child {
            margin-right: 0;
        }
    </style>

    <script>
        let searchTimeout;

        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        }

        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        function viewDocument(path) {
            window.open('/storage/' + path, '_blank');
        }

        function setDateRange(range) {
            const today = new Date();
            let startDate, endDate;

            switch (range) {
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

            document.getElementById('modal_date_from').value = startDate.toISOString().split('T')[0];
            document.getElementById('modal_date_to').value = endDate.toISOString().split('T')[0];
            applyCustomDateRange();
        }

        function applyCustomDateRange() {
            const dateFrom = document.getElementById('modal_date_from').value;
            const dateTo = document.getElementById('modal_date_to').value;

            if (dateFrom && dateTo && dateFrom > dateTo) {
                alert('From date cannot be later than To date');
                return;
            }

            document.getElementById('date_from').value = dateFrom;
            document.getElementById('date_to').value = dateTo;

            updateDateFilterStatus(dateFrom, dateTo);

            const modalElement = document.getElementById('dateFilterModal');
            if (modalElement) {
                let modal = bootstrap.Modal.getInstance(modalElement);
                if (!modal) {
                    modal = new bootstrap.Modal(modalElement);
                }
                modal.hide();
            }

            submitFilterForm();
        }

        function clearDateRange() {
            document.getElementById('modal_date_from').value = '';
            document.getElementById('modal_date_to').value = '';
            applyCustomDateRange();
        }

        function updateDateFilterStatus(dateFrom, dateTo) {
            const statusElement = document.getElementById('dateFilterStatus');
            if (!dateFrom && !dateTo) {
                statusElement.innerHTML = 'No date filter applied - showing all requests';
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
@endsection