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
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl col-md-6 mb-4 mb-xl-0">
                <div class="card stat-card shadow h-100">
                    <div class="card-body text-center py-3">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-seedling text-primary"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $totalRequests }}</div>
                        <div class="stat-label text-primary">Total Requests</div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6 mb-4 mb-xl-0">
                <div class="card stat-card shadow h-100">
                    <div class="card-body text-center py-3">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $underReviewCount }}</div>
                        <div class="stat-label text-warning">Pending</div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6 mb-4 mb-xl-0">
                <div class="card stat-card shadow h-100">
                    <div class="card-body text-center py-3">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $approvedCount }}</div>
                        <div class="stat-label text-success">Fully Approved</div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6 mb-4 mb-xl-0">
                <div class="card stat-card shadow h-100">
                    <div class="card-body text-center py-3">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-check-double text-info"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $partiallyApprovedCount }}</div>
                        <div class="stat-label text-info">Partially Approved</div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6 mb-4 mb-xl-0">
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

        <!-- Filters & Search -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Filters & Search
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.seedlings.requests') }}" id="filterForm">
                    <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                    <div class="row">
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>
                                    Under Review</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Fully
                                    Approved</option>
                                <option value="partially_approved"
                                    {{ request('status') == 'partially_approved' ? 'selected' : '' }}>Partially Approved
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="barangay" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All Barangay</option>
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
                            <a href="{{ route('admin.seedlings.requests') }}" class="btn btn-secondary btn-sm w-100">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($requests->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <div></div>
                    <div class="text-center flex-fill">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-seedling me-2"></i>Seedling Requests
                        </h6>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.seedlings.export', request()->query()) }}"
                            class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" onclick="showAddSeedlingModal()">
                            <i class="fas fa-plus me-2"></i>Add Request
                        </button>
                    </div>
                </div>
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
                                    <th class="px-3 py-3 fw-medium text-white text-center">Documents</th>
                                    <th class="px-3 py-3 fw-medium text-white text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $request)
                                    <tr class="border-bottom" data-request-id="{{ $request->id }}">
                                        <td class="px-3 py-3 border-end">
                                            <small
                                                class="text-muted">{{ $request->created_at->format('M d, Y') }}</small><br>
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
                                        <td class="px-3 py-2 border-end" style="max-width: 400px;">
                                            @php
                                                $itemsByCategory = $request->items->groupBy('category_id');
                                                $totalItems = $request->items->count();
                                            @endphp

                                            <div class="requested-items-container">
                                                @foreach ($itemsByCategory as $categoryId => $items)
                                                    @php
                                                        $category = $items->first()->category;
                                                        $approvedItems = $items->where('status', 'approved');
                                                        $rejectedItems = $items->where('status', 'rejected');
                                                        $pendingItems = $items->where('status', 'pending');
                                                        $categoryTotal = $items->count();
                                                    @endphp

                                                    <div class="category-group mb-2">
                                                        <!-- Category Header -->
                                                        <div class="category-header d-flex align-items-center justify-content-between mb-1 p-2 bg-light rounded"
                                                            style="cursor: pointer;" data-bs-toggle="collapse"
                                                            data-bs-target="#items-{{ $request->id }}-{{ $categoryId }}"
                                                            aria-expanded="false">
                                                            <div class="d-flex align-items-center flex-grow-1">
                                                                <i class="fas {{ $category->icon ?? 'fa-leaf' }} text-primary me-2"
                                                                    style="font-size: 0.9rem;"></i>
                                                                <strong class="text-dark"
                                                                    style="font-size: 0.85rem;">{{ $category->display_name }}</strong>
                                                                <span class="badge bg-secondary ms-2"
                                                                    style="font-size: 0.7rem;">{{ $categoryTotal }}</span>
                                                            </div>
                                                            <i class="fas fa-chevron-down text-muted"
                                                                style="font-size: 0.7rem;"></i>
                                                        </div>

                                                        <!-- Category Items - Collapsible -->
                                                        <div class="collapse"
                                                            id="items-{{ $request->id }}-{{ $categoryId }}">
                                                            <div class="ps-2 pe-1">
                                                                @if ($approvedItems->count() > 0)
                                                                    <div class="status-group mb-1">
                                                                        <div class="d-flex flex-wrap gap-1">
                                                                            @foreach ($approvedItems as $item)
                                                                                <span
                                                                                    class="border border-success text-success"
                                                                                    style="font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 0.25rem; display: inline-block;">
                                                                                    <i
                                                                                        class="fas fa-check me-1"></i>{{ $item->item_name }}
                                                                                    <strong>({{ $item->requested_quantity }})</strong>
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if ($pendingItems->count() > 0)
                                                                    <div class="status-group mb-1">
                                                                        <div class="d-flex flex-wrap gap-1">
                                                                            @foreach ($pendingItems as $item)
                                                                                <span
                                                                                    class="border border-warning text-warning"
                                                                                    style="font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 0.25rem; display: inline-block;">
                                                                                    <i
                                                                                        class="fas fa-clock me-1"></i>{{ $item->item_name }}
                                                                                    <strong>({{ $item->requested_quantity }})</strong>
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if ($rejectedItems->count() > 0)
                                                                    <div class="status-group mb-1">
                                                                        <div class="d-flex flex-wrap gap-1">
                                                                            @foreach ($rejectedItems as $item)
                                                                                <span
                                                                                    class="border border-danger text-danger"
                                                                                    style="font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 0.25rem; display: inline-block;">
                                                                                    <i
                                                                                        class="fas fa-times me-1"></i>{{ $item->item_name }}
                                                                                    <strong>({{ $item->requested_quantity }})</strong>
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <!-- Total Items Summary -->
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Total: <strong>{{ $totalItems }}</strong>
                                                        item{{ $totalItems !== 1 ? 's' : '' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 border-end">
                                            <span
                                                class="badge badge-status-lg bg-{{ match ($request->status) {
                                                    'approved' => 'success',
                                                    'partially_approved' => 'info',
                                                    'rejected' => 'danger',
                                                    'under_review', 'pending' => 'warning',
                                                    default => 'secondary',
                                                } }}">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="seedling-table-documents">
                                                @if ($request->hasDocuments())
                                                    <div class="seedling-document-previews">
                                                        <button type="button" class="seedling-mini-doc"
                                                            onclick="viewDocument('{{ $request->document_path }}', 'Seedling Request #{{ $request->request_number }} - Supporting Document')"
                                                            title="Supporting Document">
                                                            <div class="seedling-mini-doc-icon">
                                                                <i class="fas fa-file-image text-info"></i>
                                                            </div>
                                                        </button>
                                                    </div>
                                                    <button type="button" class="seedling-document-summary"
                                                        onclick="viewDocument('{{ $request->document_path }}', 'Seedling Request #{{ $request->request_number }} - Supporting Document')"
                                                        style="background: none; border: none; padding: 0; cursor: pointer;">
                                                        <small class="text-muted">1 document</small>
                                                    </button>
                                                @else
                                                    <div class="seedling-no-documents">
                                                        <i class="fas fa-folder-open text-muted"></i>
                                                        <small class="text-muted">No documents</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal{{ $request->id }}">
                                                    <i class="fas fa-eye"></i> View
                                                </button>

                                                <button type="button" class="btn btn-outline-warning"
                                                    onclick="showEditSeedlingModal({{ $request->id }})"
                                                    title="Edit Personal Information">
                                                    <i class="fas fa-pencil-alt"></i> Edit
                                                </button>

                                                <button type="button" class="btn btn-outline-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateModal{{ $request->id }}">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>

                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="deleteSeedlingRequest({{ $request->id }}, '{{ $request->request_number }}')"
                                                    title="Delete Request">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modals Section - OUTSIDE the table -->
            @foreach ($requests as $request)
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
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2">Personal Information</h6>
                                        <p><strong>Request #:</strong> {{ $request->request_number }}</p>
                                        <p><strong>Name:</strong> {{ $request->full_name }}</p>
                                        <p><strong>Contact:</strong> {{ $request->contact_number }}</p>
                                        <p><strong>Email:</strong> {{ $request->email ?? 'N/A' }}</p>
                                        <p><strong>Barangay:</strong> {{ $request->barangay }}</p>
                                        <p><strong>Address:</strong> {{ $request->address }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2">Request Information</h6>
                                        <p><strong>Total Quantity:</strong>
                                            {{ $request->total_quantity }}</p>
                                        <p><strong>Current Status:</strong>
                                            <span
                                                class="badge bg-{{ match ($request->status) {
                                                    'approved' => 'success',
                                                    'partially_approved' => 'info',
                                                    'rejected' => 'danger',
                                                    'under_review', 'pending' => 'warning',
                                                    default => 'secondary',
                                                } }}">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </p>
                                        <p><strong>Date Applied:</strong>
                                            {{ $request->created_at->format('F d, Y g:i A') }}</p>
                                        <p><strong>Last Updated:</strong>
                                            {{ $request->updated_at->format('F d, Y g:i A') }}</p>
                                    </div>

                                    <!-- Requested Items Section -->
                                    <div class="col-12">
                                        <div class="card border-primary">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0" style="color: #495057;">
                                                    <i class="fas fa-seedling me-2 text-primary"></i>Requested Items by
                                                    Category
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $itemsByCategory = $request->items->groupBy('category_id');
                                                @endphp

                                                @foreach ($itemsByCategory as $categoryId => $items)
                                                    @php
                                                        $category = $items->first()->category;
                                                    @endphp
                                                    <div class="mb-3 p-3 border rounded {{ !$loop->last ? 'mb-3' : '' }}">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <strong class="text-primary">
                                                                <i
                                                                    class="fas {{ $category->icon ?? 'fa-leaf' }} me-2"></i>
                                                                {{ $category->display_name }}
                                                            </strong>
                                                            <span class="badge bg-secondary">{{ $items->count() }}
                                                                items</span>
                                                        </div>
                                                        <ul class="mb-0">
                                                            @foreach ($items as $item)
                                                                <li>
                                                                    {{ $item->item_name }} -
                                                                    {{ $item->requested_quantity }}
                                                                    {{ $item->categoryItem->unit ?? 'pcs' }}
                                                                    <span class="badge bg-{{ $item->status_color }} ms-2">
                                                                        {{ ucfirst($item->status) }}
                                                                    </span>
                                                                    @if ($item->status === 'approved')
                                                                        <small class="text-muted">(Stock deducted)</small>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Supporting Document Section -->
                                    <div class="col-12">
                                        @if ($request->hasDocuments())
                                            <div class="card border-secondary">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0" style="color: #495057;">
                                                        <i class="fas fa-folder-open me-2"
                                                            style="color: #6c757d;"></i>Supporting Document
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="text-center p-3 border border-secondary rounded bg-light">
                                                        <i class="fas fa-file-alt fa-3x mb-2" style="color: #6c757d;"></i>
                                                        <h6>Supporting Document</h6>
                                                        <span class="badge bg-secondary mb-2">Uploaded</span>
                                                        <br>
                                                        <button class="btn btn-sm btn-outline-info mt-2"
                                                            onclick="viewDocument('{{ $request->document_path }}', 'Seedling Request #{{ $request->request_number }} - Supporting Document')">
                                                            <i class="fas fa-eye"></i> View Document
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="card border-secondary">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0" style="color: #495057;">
                                                        <i class="fas fa-folder-open me-2"
                                                            style="color: #6c757d;"></i>Supporting Document
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="text-center p-3 border border-secondary rounded">
                                                        <i class="fas fa-file-slash fa-3x mb-2"
                                                            style="color: #6c757d;"></i>
                                                        <h6>No Document Uploaded</h6>
                                                        <span class="badge bg-secondary mb-2">Not Uploaded</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Remarks Section -->
                                    @if ($request->remarks)
                                        <div class="col-12">
                                            <h6 class="border-bottom pb-2">Remarks</h6>
                                            <div class="alert alert-info">
                                                <p class="mb-0">{{ $request->remarks }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editSeedlingModal{{ $request->id }}" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-light border-bottom">
                                <h5 class="modal-title fw-bold text-dark">
                                    <i class="fas fa-pencil-alt text-warning me-2"></i>
                                    Edit Request - {{ $request->request_number }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('admin.seedlings.update', $request) }}" 
                                    id="editForm{{ $request->id }}" class="needs-validation">
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
                                                    <label for="edit_first_name_{{ $request->id }}" class="form-label">First Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edit_first_name_{{ $request->id }}" 
                                                        name="first_name" value="{{ $request->first_name }}" required maxlength="100">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="edit_middle_name_{{ $request->id }}" class="form-label">Middle Name</label>
                                                    <input type="text" class="form-control" id="edit_middle_name_{{ $request->id }}" 
                                                        name="middle_name" value="{{ $request->middle_name }}" maxlength="100">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="edit_last_name_{{ $request->id }}" class="form-label">Last Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edit_last_name_{{ $request->id }}" 
                                                        name="last_name" value="{{ $request->last_name }}" required maxlength="100">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="edit_extension_{{ $request->id }}" class="form-label">Extension</label>
                                                    <select class="form-select" id="edit_extension_{{ $request->id }}" name="extension_name">
                                                        <option value="">None</option>
                                                        <option value="Jr." {{ $request->extension_name === 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                                        <option value="Sr." {{ $request->extension_name === 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                                        <option value="II" {{ $request->extension_name === 'II' ? 'selected' : '' }}>II</option>
                                                        <option value="III" {{ $request->extension_name === 'III' ? 'selected' : '' }}>III</option>
                                                        <option value="IV" {{ $request->extension_name === 'IV' ? 'selected' : '' }}>IV</option>
                                                        <option value="V" {{ $request->extension_name === 'V' ? 'selected' : '' }}>V</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="edit_contact_number_{{ $request->id }}" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                                    <input type="tel" class="form-control" id="edit_contact_number_{{ $request->id }}" 
                                                        name="contact_number" value="{{ $request->contact_number }}" required 
                                                        placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$" maxlength="20">
                                                    <div class="form-text">09XXXXXXXXX or +639XXXXXXXXX</div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="edit_email_{{ $request->id }}" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="edit_email_{{ $request->id }}" 
                                                        name="email" value="{{ $request->email }}" maxlength="254">
                                                    <div class="form-text">For status notifications</div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Request Number</label>
                                                    <input type="text" class="form-control" value="{{ $request->request_number }}" disabled>
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
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_barangay_{{ $request->id }}" class="form-label">Barangay <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="edit_barangay_{{ $request->id }}" name="barangay" required>
                                                        <option value="">Select Barangay</option>
                                                        @foreach ($barangays as $barangay)
                                                            <option value="{{ $barangay }}" {{ $request->barangay === $barangay ? 'selected' : '' }}>
                                                                {{ $barangay }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_address_{{ $request->id }}" class="form-label">Address <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edit_address_{{ $request->id }}" 
                                                        name="address" value="{{ $request->address }}" required maxlength="500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Information Card -->
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Additional Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_planting_location_{{ $request->id }}" class="form-label">Planting Location</label>
                                                    <input type="text" class="form-control" id="edit_planting_location_{{ $request->id }}" 
                                                        name="planting_location" value="{{ $request->planting_location }}" maxlength="500">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_preferred_delivery_date_{{ $request->id }}" class="form-label">Preferred Delivery Date</label>
                                                    <input type="date" class="form-control" id="edit_preferred_delivery_date_{{ $request->id }}" 
                                                        name="preferred_delivery_date" value="{{ $request->preferred_delivery_date?->format('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_purpose_{{ $request->id }}" class="form-label">Purpose</label>
                                                <textarea class="form-control" id="edit_purpose_{{ $request->id }}" 
                                                    name="purpose" rows="3" maxlength="1000">{{ $request->purpose }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Information (Read-only) -->
                                    <div class="card mb-3 bg-light">
                                        <div class="card-header bg-light border-0">
                                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Request Status (Read-only)</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <small class="text-muted">Current Status:</small>
                                                    <div>
                                                        <span class="badge bg-{{ match($request->status) {
                                                            'approved' => 'success',
                                                            'partially_approved' => 'info',
                                                            'rejected' => 'danger',
                                                            'under_review', 'pending' => 'warning',
                                                            default => 'secondary',
                                                        } }}">
                                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <small class="text-muted">Date Applied:</small>
                                                    <div>{{ $request->created_at->format('M d, Y g:i A') }}</div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <small class="text-muted">Last Updated:</small>
                                                    <div>{{ $request->updated_at->format('M d, Y g:i A') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info Alert -->
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <strong>Note:</strong> Changes to personal or location information will be saved. 
                                        To update item statuses and approvals, use the "Update Items" button from the main table.
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="editSubmitBtn{{ $request->id }}"
                                    onclick="handleEditSeedlingSubmit({{ $request->id }})">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
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
                                <form method="POST" action="{{ route('admin.seedlings.update-items', $request) }}"
                                    id="updateForm{{ $request->id }}">
                                    @csrf
                                    @method('PATCH')

                                    @php
                                        $itemsByCategory = $request->items->groupBy('category_id');
                                    @endphp

                                    @foreach ($itemsByCategory as $categoryId => $items)
                                        @php
                                            $category = $items->first()->category;
                                        @endphp
                                        <div class="mb-4 p-3 border-0 bg-light rounded-3">
                                            <h6 class="mb-3 fw-bold text-primary">
                                                <i class="fas {{ $category->icon ?? 'fa-leaf' }} me-2"></i>
                                                {{ $category->display_name }}
                                            </h6>

                                            @foreach ($items as $item)
                                                @php
                                                    $stockCheck = $item->categoryItem
                                                        ? $item->categoryItem->checkSupplyAvailability(
                                                            $item->requested_quantity,
                                                        )
                                                        : [
                                                            'available' => false,
                                                            'current_supply' => 0,
                                                        ];
                                                @endphp

                                                <div class="item-card d-flex align-items-center justify-content-between mb-3 p-3
                                                    {{ $item->status === 'approved'
                                                        ? 'bg-success bg-opacity-10 border border-success'
                                                        : ($item->status === 'rejected'
                                                            ? 'bg-danger bg-opacity-10 border border-danger'
                                                            : 'bg-white border') }}
                                                    rounded-3 shadow-sm"
                                                    data-item-id="{{ $item->id }}"
                                                    data-original-status="{{ $item->status }}">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span
                                                                class="fw-medium text-dark">{{ $item->item_name }}</span>
                                                            <span class="badge bg-light text-muted ms-2">
                                                                {{ $item->requested_quantity }}
                                                                {{ $item->categoryItem->unit ?? 'pcs' }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <small class="text-muted">Requested:</small>
                                                            <small
                                                                class="fw-medium">{{ $item->requested_quantity }}</small>
                                                            <span class="text-muted">•</span>
                                                            <small
                                                                class="{{ $stockCheck['available'] ? 'text-success' : 'text-warning' }}">
                                                                <i class="fas fa-box me-1"></i>Stock:
                                                                <span
                                                                    class="fw-bold">{{ $stockCheck['current_supply'] }}</span>
                                                                @if ($stockCheck['available'])
                                                                    <i class="fas fa-check text-success ms-1"></i>
                                                                @else
                                                                    <i
                                                                        class="fas fa-exclamation-triangle text-warning ms-1"></i>
                                                                @endif
                                                            </small>
                                                        </div>
                                                        @if (!$stockCheck['available'])
                                                            <span class="badge bg-warning text-dark mt-2">
                                                                <i
                                                                    class="fas fa-exclamation-triangle me-1"></i>Insufficient
                                                                Stock
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="ms-3">
                                                        <select name="item_statuses[{{ $item->id }}]"
                                                            class="form-select form-select-sm border-light"
                                                            style="min-width: 130px;" data-item-id="{{ $item->id }}"
                                                            onchange="checkForSeedlingChanges({{ $request->id }})">
                                                            <option value="pending"
                                                                {{ $item->status === 'pending' ? 'selected' : '' }}>
                                                                Pending
                                                            </option>
                                                            <option value="approved"
                                                                {{ $item->status === 'approved' ? 'selected' : '' }}
                                                                {{ !$stockCheck['available'] ? 'disabled' : '' }}>
                                                                Approved{{ !$stockCheck['available'] ? ' (No Stock)' : '' }}
                                                            </option>
                                                            <option value="rejected"
                                                                {{ $item->status === 'rejected' ? 'selected' : '' }}>
                                                                Rejected
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach

                                    <div class="mb-3">
                                        <label for="remarks{{ $request->id }}" class="form-label">General
                                            Remarks</label>
                                        <textarea name="remarks" id="remarks{{ $request->id }}" class="form-control" rows="3"
                                            placeholder="Add any comments..." onchange="checkForSeedlingChanges({{ $request->id }})"
                                            oninput="checkForSeedlingChanges({{ $request->id }})">{{ $request->remarks }}</textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="submitBtn{{ $request->id }}"
                                    onclick="handleSeedlingUpdateSubmit({{ $request->id }})">
                                    <i class="fas fa-save me-2"></i>Update Items
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
    </div>
    </div>
    </div>

    <!-- Add Seedling Request Modal -->
    <div class="modal fade" id="addSeedlingModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-seedling me-2"></i>Add New Seedling Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSeedlingForm" enctype="multipart/form-data">
                        <!-- Personal Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="seedling_first_name" class="form-label">First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="seedling_first_name" required
                                            maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="seedling_middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="seedling_middle_name"
                                            maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="seedling_last_name" class="form-label">Last Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="seedling_last_name" required
                                            maxlength="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="seedling_extension" class="form-label">Extension</label>
                                        <select class="form-select" id="seedling_extension">
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
                                        <label for="seedling_contact_number" class="form-label">Contact Number <span
                                                class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="seedling_contact_number" required
                                            placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <div class="form-text">09XXXXXXXXX or +639XXXXXXXXX</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="seedling_email" class="form-label">Email (Optional)</label>
                                        <input type="email" class="form-control" id="seedling_email" maxlength="254">
                                        <div class="form-text">For status notifications</div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="seedling_user_id" class="form-label">Link to User Account
                                            (Optional)</label>
                                        <input type="number" class="form-control" id="seedling_user_id"
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
                                    <div class="col-md-6 mb-3">
                                        <label for="seedling_barangay" class="form-label">Barangay <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="seedling_barangay" required>
                                            <option value="">Select Barangay</option>
                                            <!-- Barangays will be populated from server data -->
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="seedling_address" class="form-label">Address <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="seedling_address" required
                                            maxlength="500" placeholder="Full address">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Request Items -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-leaf me-2"></i>Requested Items</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addSeedlingItemRow()">
                                        <i class="fas fa-plus me-1"></i>Add Item
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="seedling_items_container">
                                    <!-- Item rows will be added here -->
                                </div>
                                <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i>Add at least one
                                    item to the request</p>
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
                                        <label for="seedling_supporting_document" class="form-label">Upload
                                            Document</label>
                                        <input type="file" class="form-control" id="seedling_supporting_document"
                                            accept="image/*,.pdf"
                                            onchange="previewSeedlingDocument('seedling_supporting_document', 'seedling_doc_preview')">
                                        <div class="form-text">Accepted: JPG, PNG, PDF (Max 10MB)</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="seedling_doc_preview" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i>Additional Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="seedling_planting_location" class="form-label">Planting
                                            Location</label>
                                        <input type="text" class="form-control" id="seedling_planting_location"
                                            maxlength="500" placeholder="Where will the seedlings be planted?">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="seedling_preferred_delivery_date" class="form-label">Preferred
                                            Delivery Date</label>
                                        <input type="date" class="form-control" id="seedling_preferred_delivery_date">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="seedling_purpose" class="form-label">Purpose</label>
                                    <textarea class="form-control" id="seedling_purpose" rows="3" maxlength="1000"
                                        placeholder="Why are you requesting these seedlings?"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Request Status -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Request Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="seedling_status" class="form-label">Initial Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="seedling_status" required>
                                        <option value="pending" selected>Pending</option>
                                        <option value="under_review">Under Review</option>
                                        <option value="approved">Approved</option>
                                        <option value="partially_approved">Partially Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="seedling_remarks" class="form-label">Remarks (Optional)</label>
                                    <textarea class="form-control" id="seedling_remarks" rows="3" maxlength="500"
                                        placeholder="Any notes or comments..."></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddSeedling()">
                        <i class="fas fa-save me-1"></i>Create Request
                    </button>
                </div>
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

        .btn-group>.btn {
            margin-right: 0.25rem;
        }

        .btn-group>.btn:last-child {
            margin-right: 0;
        }

        /* Requested Items Column Improvements */
        .requested-items-container {
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .requested-items-container::-webkit-scrollbar {
            width: 6px;
        }

        .requested-items-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .requested-items-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .requested-items-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .category-header {
            transition: all 0.2s ease;
        }

        .category-header:hover {
            background-color: #e9ecef !important;
        }

        .category-header[aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
            transition: transform 0.2s ease;
        }

        .category-header[aria-expanded="false"] .fa-chevron-down {
            transform: rotate(0deg);
            transition: transform 0.2s ease;
        }

        .category-group {
            border-left: 3px solid transparent;
            padding-left: 4px;
            transition: border-color 0.2s ease;
        }

        .category-group:hover {
            border-left-color: #007bff;
        }

        .status-group .badge {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
            display: inline-flex;
            align-items: center;
        }

        .status-group .badge:hover {
            max-width: none;
            z-index: 10;
            position: relative;
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

        /* Responsive mobile */
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

        /* SEEDLING-Style Table Document Previews */
        .seedling-table-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        .seedling-document-previews {
            display: flex;
            gap: 0.25rem;
            align-items: center;
        }

        .seedling-mini-doc {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: white;
            border: 2px solid #28a745;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .seedling-mini-doc:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-color: #28a745;
        }

        .seedling-mini-doc-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .seedling-document-summary {
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .seedling-document-summary:hover {
            color: #28a745 !important;
        }

        .seedling-document-summary:hover small {
            color: #28a745 !important;
        }

        .seedling-no-documents {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem;
            opacity: 0.7;
        }

        .seedling-no-documents i {
            font-size: 1.25rem;
        }

        /* Responsive adjustments for table documents */
        @media (max-width: 768px) {
            .seedling-mini-doc {
                width: 28px;
                height: 28px;
            }

            .seedling-mini-doc-icon {
                font-size: 0.75rem;
            }
        }

        /* Form Change Detection Styles */
        .form-changed {
            background-color: #fff3cd !important;
            border-left: 3px solid #ffc107 !important;
            transition: all 0.2s ease;
        }

        .change-indicator {
            position: relative;
            display: block;
        }

        .change-indicator::after {
            content: "●";
            color: #ffc107;
            font-size: 12px;
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .change-indicator.changed::after {
            opacity: 1;
        }

        /* Button "No Changes" State */
        .no-changes {
            opacity: 0.65 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        .no-changes:hover,
        .no-changes:focus,
        .no-changes:active {
            background-color: inherit !important;
            border-color: inherit !important;
            box-shadow: none !important;
        }

        /* Item card change highlight */
        .item-card.form-changed {
            background: #fff3cd !important;
            border: 1px solid #ffc107 !important;
            transition: all 0.2s ease;
        }

        .item-card.form-changed:hover {
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2) !important;
        }

        /* Remarks textarea change highlight */
        textarea.form-changed {
            border-color: #ffc107 !important;
            background-color: #fff3cd !important;
            transition: all 0.2s ease;
        }

        textarea.form-changed:focus {
            border-color: #ffc107 !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
            background-color: #fff3cd !important;
        }
    </style>

    <script>
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

        // View document function
        function viewDocument(path, filename = null, applicationId = null) {
            // Input validation
            if (!path || path.trim() === '') {
                showToast('error', 'No document path provided');
                return;
            }

            // Create modal if it doesn't exist
            if (!document.getElementById('documentModal')) {
                const modalHTML = `
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
        `;
                document.body.insertAdjacentHTML('beforeend', modalHTML);
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
                        <div class="text-center p-3">
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
                        <div class="alert alert-warning text-center m-3">
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
                    <div class="pdf-container p-3">
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
                            <div class="alert alert-info text-center m-3">
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
                    <div class="text-center p-3">
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
                    <div class="alert alert-info text-center m-3">
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
                    <div class="alert alert-warning text-center m-3">
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
                <div class="alert alert-danger text-center m-3">
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

        // Basic toast notification
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

            setTimeout(() => {
                if (document.contains(toast)) {
                    removeToast(toast);
                }
            }, 5000);
        }

        // Confirmation toast with 8 second auto-dismiss
        function showConfirmationToast(title, message, onConfirm) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const toast = document.createElement('div');
            toast.className = 'toast-notification confirmation-toast';

            toast.dataset.confirmCallback = Math.random().toString(36);
            window[toast.dataset.confirmCallback] = onConfirm;

            toast.innerHTML = `
        <div class="toast-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 12px 16px; display: flex; align-items: center; font-weight: 600;">
            <i class="fas fa-question-circle me-2 text-warning"></i>
            <strong class="me-auto">${title}</strong>
            <button type="button" class="btn-close btn-close-toast" onclick="removeToast(this.closest('.toast-notification'))" style="width: auto; height: auto; padding: 0; font-size: 1.2rem; opacity: 0.5; transition: opacity 0.2s; background: none; border: none; cursor: pointer;"></button>
        </div>
        <div class="toast-body" style="padding: 16px; background: #f8f9fa;">
            <p class="mb-3" style="margin: 0; font-size: 0.95rem; color: #333; line-height: 1.5; white-space: pre-wrap;">${message}</p>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-sm btn-secondary" onclick="removeToast(this.closest('.toast-notification'))" style="padding: 0.375rem 0.75rem; font-size: 0.875rem;">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmToastAction(this)" style="padding: 0.375rem 0.75rem; font-size: 0.875rem;">
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
            }, 8000);
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

        // Delete seedling request with confirmation toast
        function deleteSeedlingRequest(id, requestNumber) {
            showConfirmationToast(
                'Delete Seedling Request',
                `Are you sure you want to delete request ${requestNumber}?\n\nThis action cannot be undone and will:\n• Delete all associated documents\n• Return approved supplies back to inventory`,
                () => proceedWithSeedlingDelete(id, requestNumber)
            );
        }

        // Proceed with seedling request deletion
        function proceedWithSeedlingDelete(id, requestNumber) {
            fetch(`/admin/seedlings/requests/${id}`, {
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
                        showToast('success', data.message || 'Seedling request deleted successfully');

                        const row = document.querySelector(`tr[data-request-id="${id}"]`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();

                                const tbody = document.querySelector('table tbody');
                                if (tbody && tbody.children.length === 0) {
                                    setTimeout(() => window.location.reload(), 1500);
                                }
                            }, 300);
                        } else {
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    } else {
                        throw new Error(data.message || 'Failed to delete seedling request');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Failed to delete seedling request: ' + error.message);
                });
        }

  // Initialize the update modal with original values
function initializeSeedlingUpdateModal(requestId) {
    const form = document.getElementById('updateForm' + requestId);
    const remarksTextarea = document.getElementById('remarks' + requestId);
    const statusSelects = form.querySelectorAll('select[name^="item_statuses"]');
    const submitButton = document.getElementById('submitBtn' + requestId);

    // Store original remarks value
    if (remarksTextarea) {
        remarksTextarea.dataset.originalRemarks = remarksTextarea.value;
    }

    // Clear any previous change indicators
    if (remarksTextarea) {
        remarksTextarea.classList.remove('form-changed');
    }

    statusSelects.forEach(select => {
        select.classList.remove('form-changed');
        const itemCard = select.closest('.item-card');
        if (itemCard) {
            itemCard.classList.remove('form-changed');
        }
    });

    // Reset button state - KEEP IT ENABLED
    if (submitButton) {
        submitButton.classList.remove('no-changes');
        submitButton.innerHTML = 'Update Items';
        submitButton.disabled = false; // Keep enabled
    }
}

        // Open update modal and initialize
        function openUpdateModal(requestId) {
            const modal = new bootstrap.Modal(document.getElementById('updateModal' + requestId));
            modal.show();
        }

    // Check for changes and update button/visual states
    function checkForSeedlingChanges(requestId) {
    const form = document.getElementById('updateForm' + requestId);
    if (!form) return;

    const remarksTextarea = document.getElementById('remarks' + requestId);
    const statusSelects = form.querySelectorAll('select[name^="item_statuses"]');
    const submitButton = document.getElementById('submitBtn' + requestId);

    let hasChanges = false;
    const originalRemarks = remarksTextarea?.dataset.originalRemarks || '';

    // Check remarks for changes
    if (remarksTextarea) {
        const remarksChanged = remarksTextarea.value.trim() !== originalRemarks.trim();

        if (remarksChanged) {
            hasChanges = true;
            remarksTextarea.classList.add('form-changed');
        } else {
            remarksTextarea.classList.remove('form-changed');
        }
    }

    // Check item statuses for changes - USE ITEM CARD DATA ATTRIBUTE
    statusSelects.forEach(select => {
        const itemCard = select.closest('.item-card');
        const originalStatus = itemCard ? itemCard.dataset.originalStatus : null;

        if (select.value !== originalStatus) {
            hasChanges = true;
            if (itemCard) {
                itemCard.classList.add('form-changed');
            }
        } else {
            if (itemCard) {
                itemCard.classList.remove('form-changed');
            }
        }
    });

    // Update button state based on changes - ALWAYS KEEP ENABLED
    if (submitButton) {
        if (hasChanges) {
            submitButton.classList.remove('no-changes');
            submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Update Items';
            // Store flag for use in submission
            submitButton.dataset.hasChanges = 'true';
        } else {
            submitButton.classList.add('no-changes');
            submitButton.innerHTML = '<i class="fas fa-check me-2"></i>No Changes';
            // Store flag for use in submission
            submitButton.dataset.hasChanges = 'false';
        }
        // IMPORTANT: Button remains enabled
        submitButton.disabled = false;
    }
}


// Handle update form submission with confirmation
function handleSeedlingUpdateSubmit(requestId) {
    const form = document.getElementById('updateForm' + requestId);

    if (!form) {
        console.error('Form not found:', 'updateForm' + requestId);
        showToast('error', 'Form not found. Please try again.');
        return;
    }

    const submitButton = document.getElementById('submitBtn' + requestId);
    const hasChanges = submitButton?.dataset.hasChanges === 'true';

    // If no changes, show warning and return
    if (!hasChanges) {
        showToast('warning', 'No changes detected. Please modify the status or remarks before updating.');
        return;
    }

    console.log('=== DEBUG: Form Submission Started ===');
    console.log('Request ID:', requestId);
    console.log('Form found:', !!form);
    console.log('Form action:', form.getAttribute('action'));
    console.log('Form method:', form.getAttribute('method'));

    const remarksTextarea = form.querySelector('textarea[id="remarks' + requestId + '"]');
    const statusSelects = form.querySelectorAll('select[name^="item_statuses"]');

    console.log('Remarks textarea found:', !!remarksTextarea);
    console.log('Status selects found:', statusSelects.length);

    let changesSummary = [];

    // Check for changes in item statuses
    statusSelects.forEach((select, index) => {
        const itemCard = select.closest('.item-card');
        const originalStatus = itemCard ? itemCard.dataset.originalStatus : null;
        const currentStatus = select.value;

        console.log(`Item ${index + 1}:`, {
            original: originalStatus,
            current: currentStatus,
            different: currentStatus !== originalStatus
        });

        if (currentStatus !== originalStatus) {
            const itemName = itemCard?.querySelector('.fw-medium')?.textContent || 'Item';
            const oldStatusText = getStatusText(originalStatus);
            const newStatusText = getStatusText(currentStatus);
            changesSummary.push(`${itemName.trim()}: ${oldStatusText} → ${newStatusText}`);
        }
    });

    // Check for changes in remarks
    const originalRemarks = remarksTextarea?.dataset.originalRemarks || '';
    const currentRemarks = remarksTextarea?.value || '';

    console.log('Remarks:', {
        original: originalRemarks,
        current: currentRemarks,
        hasChanged: currentRemarks.trim() !== originalRemarks.trim()
    });

    if (remarksTextarea && currentRemarks.trim() !== originalRemarks.trim()) {
        if (originalRemarks.trim() === '') {
            changesSummary.push('Remarks: Added new remarks');
        } else if (currentRemarks.trim() === '') {
            changesSummary.push('Remarks: Removed remarks');
        } else {
            changesSummary.push('Remarks: Modified');
        }
    }

    console.log('Changes summary:', changesSummary);

    // Show confirmation toast with changes
    showConfirmationToast(
        'Confirm Update',
        `Update this request with the following changes?\n\n${changesSummary.join('\n')}`,
        () => proceedWithSeedlingUpdate(form, requestId)
    );
}

// Helper function to get status text
function getStatusText(status) {
    switch (status) {
        case 'pending':
            return 'Pending';
        case 'approved':
            return 'Approved';
        case 'rejected':
            return 'Rejected';
        case 'under_review':
            return 'Under Review';
        default:
            return status;
    }
}


// Proceed with seedling update after confirmation
function proceedWithSeedlingUpdate(form, requestId) {
    // Create a new FormData object to ensure all fields are properly included
    const formData = new FormData(form);

    // Ensure _method field is included for PATCH request
    if (!formData.has('_method')) {
        formData.append('_method', 'PATCH');
    }

    // Ensure CSRF token is in FormData
    const csrfToken = getCSRFToken();
    if (!formData.has('_token')) {
        formData.append('_token', csrfToken);
    }

    // Debug: Log what's being sent
    console.log('=== FormData Contents ===');
    for (let [key, value] of formData.entries()) {
        console.log(key, ':', value);
    }

    // Verify that item_statuses exists in the form data
    let hasItemStatuses = false;
    for (let [key] of formData.entries()) {
        if (key.startsWith('item_statuses')) {
            hasItemStatuses = true;
            break;
        }
    }

    if (!hasItemStatuses) {
        console.error('ERROR: item_statuses not found in form data!');
        showToast('error', 'Form validation error: Item statuses missing');
        return;
    }

    const submitButton = document.getElementById('submitBtn' + requestId);

    // Show loading state
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...';
    submitButton.disabled = true;

    // OPTIMIZATION: Disable all form inputs to prevent changes during submission
    const formInputs = form.querySelectorAll('select, textarea, input');
    formInputs.forEach(input => input.disabled = true);

    // Get the form action URL
    const formAction = form.getAttribute('action');
    console.log('Form action:', formAction);
    console.log('CSRF Token:', csrfToken);

    fetch(formAction, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            // Clone the response so we can read it multiple times
            const responseClone = response.clone();

            if (response.status === 422) {
                // Handle validation errors
                return response.json().then(data => {
                    console.error('Validation errors:', data);
                    throw new Error('Validation failed: ' + JSON.stringify(data.errors || data.message));
                });
            }

            if (!response.ok) {
                // Try to get error message from response
                return responseClone.text().then(text => {
                    console.error('Error response body:', text);
                    let errorMessage = `HTTP error! status: ${response.status}`;
                    try {
                        const data = JSON.parse(text);
                        if (data.message) {
                            errorMessage = data.message;
                        } else if (data.error) {
                            errorMessage = data.error;
                        }
                    } catch (e) {
                        // If not JSON, use the text directly (truncated)
                        if (text.length > 0) {
                            errorMessage += ` - ${text.substring(0, 200)}`;
                        }
                    }
                    throw new Error(errorMessage);
                });
            }

            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);

            if (data.success) {
                // Close modal immediately
                const modalId = 'updateModal' + requestId;
                const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                if (modal) modal.hide();

                // Show success toast
                showToast('success', data.message || 'Items updated successfully');

                // OPTIMIZATION: Reload page immediately without delay
                window.location.reload();
            } else {
                showToast('error', data.message || 'Failed to update items');
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error during update:', error);
            showToast('error', 'Error: ' + error.message);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;

            // Re-enable form inputs
            const formInputs = form.querySelectorAll('select, textarea, input');
            formInputs.forEach(input => input.disabled = false);
        });
}

        // Get CSRF token utility function
        function getCSRFToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            return metaTag ? metaTag.getAttribute('content') : '';
        }

// Initialize modal when opened
document.addEventListener('DOMContentLoaded', function() {
    // Get all update modals for seedlings
    const updateModals = document.querySelectorAll('[id^="updateModal"]');

    updateModals.forEach(modalElement => {
        const requestId = modalElement.id.replace('updateModal', '');

        modalElement.addEventListener('show.bs.modal', function() {
            initializeSeedlingUpdateModal(requestId);
        });
    });

    // Add event listeners for real-time change detection only
    const updateForms = document.querySelectorAll('form[id^="updateForm"]');

    updateForms.forEach(form => {
        const requestId = form.id.replace('updateForm', '');

        // Add event listeners for real-time change detection
        const statusSelects = form.querySelectorAll('select[name^="item_statuses"]');
        const remarksTextarea = form.querySelector('textarea[id="remarks' + requestId + '"]');

        statusSelects.forEach(select => {
            select.addEventListener('change', () => checkForSeedlingChanges(requestId));
        });

        if (remarksTextarea) {
            remarksTextarea.addEventListener('input', () => checkForSeedlingChanges(requestId));
            remarksTextarea.addEventListener('change', () => checkForSeedlingChanges(requestId));
        }
    });
});

        // Initialize seedling item counter
        let seedlingItemCounter = 0;

        // Show add seedling modal
        function showAddSeedlingModal() {
            const modal = new bootstrap.Modal(document.getElementById('addSeedlingModal'));

            // Reset form
            document.getElementById('addSeedlingForm').reset();
            seedlingItemCounter = 0;

            // Populate barangays
            populateSeedlingBarangays();

            // Add one empty item row
            addSeedlingItemRow();

            // Remove any validation errors
            document.querySelectorAll('#addSeedlingModal .is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('#addSeedlingModal .invalid-feedback').forEach(el => el.remove());

            // Clear document preview
            const preview = document.getElementById('seedling_doc_preview');
            if (preview) {
                preview.innerHTML = '';
                preview.style.display = 'none';
            }

            modal.show();
        }

        // Populate barangays dropdown
        function populateSeedlingBarangays() {
            const barangaySelect = document.getElementById('seedling_barangay');

            // Get barangays from the page's data (if available)
            const barangayElements = document.querySelectorAll('option[value*=""]');

            // If barangays are already populated, return
            if (barangaySelect.querySelectorAll('option').length > 1) {
                return;
            }

            // Fetch barangays from server or use hardcoded list
            const barangays = @json($barangays ?? collect());

            if (barangays && barangays.length > 0) {
                barangays.forEach(barangay => {
                    const option = document.createElement('option');
                    option.value = barangay;
                    option.textContent = barangay;
                    barangaySelect.appendChild(option);
                });
            }
        }

        // Add item row
        function addSeedlingItemRow() {
            seedlingItemCounter++;
            const container = document.getElementById('seedling_items_container');

            const itemRow = document.createElement('div');
            itemRow.className = 'seedling-item-row mb-3 p-3 border rounded';
            itemRow.id = `seedling_item_${seedlingItemCounter}`;
            itemRow.innerHTML = `
        <div class="row align-items-end">
            <div class="col-md-5 mb-3">
                <label class="form-label">Category & Item <span class="text-danger">*</span></label>
                <select class="form-select seedling-category-select" data-row-id="${seedlingItemCounter}" required onchange="loadSeedlingItems(this)">
                    <option value="">Select Item</option>
                    <!-- Categories and items will be populated here -->
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" class="form-control seedling-quantity" data-row-id="${seedlingItemCounter}" min="1" required placeholder="Enter quantity">
            </div>
            <div class="col-md-3 mb-3">
                <button type="button" class="btn btn-outline-danger w-100" onclick="removeSeedlingItemRow(${seedlingItemCounter})">
                    <i class="fas fa-trash me-1"></i>Remove
                </button>
            </div>
        </div>
    `;

            container.appendChild(itemRow);

            // Populate category select
            populateSeedlingCategorySelect(container.querySelector(`select[data-row-id="${seedlingItemCounter}"]`));
        }

        // Populate category select
        function populateSeedlingCategorySelect(selectElement) {
            const categories = @json($categories ?? collect());

            selectElement.innerHTML = '<option value="">Select Item</option>';

            categories.forEach(category => {
                if (category.items && category.items.length > 0) {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = category.display_name;

                    category.items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = `${item.name}`;
                        optgroup.appendChild(option);
                    });

                    selectElement.appendChild(optgroup);
                }
            });
        }

        // Remove item row
        function removeSeedlingItemRow(rowId) {
            const row = document.getElementById(`seedling_item_${rowId}`);
            if (row) {
                row.remove();
            }

            // Check if at least one item remains
            const itemCount = document.querySelectorAll('.seedling-item-row').length;
            if (itemCount === 0) {
                addSeedlingItemRow();
            }
        }

        // Load items for category
        function loadSeedlingItems(selectElement) {
            // This is handled by the optgroup structure
        }

        // Validate seedling contact number
        function validateSeedlingContactNumber(contactNumber) {
            const input = document.getElementById('seedling_contact_number');
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

        // Validate seedling email
        function validateSeedlingEmail(email) {
            const input = document.getElementById('seedling_email');
            const feedback = input.parentNode.querySelector('.invalid-feedback');

            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');

            if (!email || email.trim() === '') {
                return true;
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
        function capitalizeSeedlingName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        }

        document.getElementById('seedling_first_name')?.addEventListener('blur', function() {
            capitalizeSeedlingName(this);
        });

        document.getElementById('seedling_middle_name')?.addEventListener('blur', function() {
            capitalizeSeedlingName(this);
        });

        document.getElementById('seedling_last_name')?.addEventListener('blur', function() {
            capitalizeSeedlingName(this);
        });

        // Document preview
        function previewSeedlingDocument(inputId, previewId) {
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

            if (file.size > 10 * 1024 * 1024) {
                showToast('error', 'File size must not exceed 10MB');
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
                            <i class="fas fa-file-image me-1"></i>${file.name}
                        </p>
                    </div>
                `;
                    } else {
                        preview.innerHTML = `
                    <div class="document-preview-item">
                        <div class="text-center p-3 border rounded">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                            <p style="margin-top: 8px; font-size: 12px; color: #666;">${file.name}</p>
                        </div>
                    </div>
                `;
                    }
                    preview.style.display = 'block';
                }
            };

            reader.readAsDataURL(file);
        }

        // Validate seedling form
        function validateSeedlingForm() {
            let isValid = true;

            const requiredFields = [{
                    id: 'seedling_first_name',
                    label: 'First Name'
                },
                {
                    id: 'seedling_last_name',
                    label: 'Last Name'
                },
                {
                    id: 'seedling_contact_number',
                    label: 'Contact Number'
                },
                {
                    id: 'seedling_barangay',
                    label: 'Barangay'
                },
                {
                    id: 'seedling_address',
                    label: 'Address'
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

            // Validate contact number
            const contactNumber = document.getElementById('seedling_contact_number').value.trim();
            if (!validateSeedlingContactNumber(contactNumber)) {
                isValid = false;
            }

            // Validate email if provided
            const email = document.getElementById('seedling_email').value.trim();
            if (email && !validateSeedlingEmail(email)) {
                isValid = false;
            }

            // Validate at least one item
            const items = document.querySelectorAll('.seedling-item-row');
            let itemsValid = true;

            items.forEach(item => {
                const categorySelect = item.querySelector('.seedling-category-select');
                const quantityInput = item.querySelector('.seedling-quantity');

                if (!categorySelect.value) {
                    categorySelect.classList.add('is-invalid');
                    itemsValid = false;
                }

                if (!quantityInput.value || quantityInput.value < 1) {
                    quantityInput.classList.add('is-invalid');
                    itemsValid = false;
                }
            });

            if (!itemsValid) {
                showToast('error', 'Please add at least one valid item');
                isValid = false;
            }

            return isValid;
        }

        // Submit add seedling form
        function submitAddSeedling() {
            if (!validateSeedlingForm()) {
                showToast('error', 'Please fix all validation errors before submitting');
                return;
            }

            // Prepare form data
            const formData = new FormData();

            formData.append('first_name', document.getElementById('seedling_first_name').value.trim());
            formData.append('middle_name', document.getElementById('seedling_middle_name').value.trim());
            formData.append('last_name', document.getElementById('seedling_last_name').value.trim());
            formData.append('extension_name', document.getElementById('seedling_extension').value);
            formData.append('contact_number', document.getElementById('seedling_contact_number').value.trim());
            formData.append('email', document.getElementById('seedling_email').value.trim());
            formData.append('barangay', document.getElementById('seedling_barangay').value);
            formData.append('address', document.getElementById('seedling_address').value.trim());
            formData.append('planting_location', document.getElementById('seedling_planting_location').value.trim());
            formData.append('purpose', document.getElementById('seedling_purpose').value.trim());
            formData.append('preferred_delivery_date', document.getElementById('seedling_preferred_delivery_date').value);
            formData.append('status', document.getElementById('seedling_status').value);
            formData.append('remarks', document.getElementById('seedling_remarks').value.trim());

            const userId = document.getElementById('seedling_user_id').value.trim();
            if (userId) {
                formData.append('user_id', userId);
            }

            // Add items
            const items = document.querySelectorAll('.seedling-item-row');
            items.forEach((item, index) => {
                const categorySelect = item.querySelector('.seedling-category-select');
                const quantityInput = item.querySelector('.seedling-quantity');

                formData.append(`items[${index}][category_item_id]`, categorySelect.value);
                formData.append(`items[${index}][quantity]`, quantityInput.value);
            });

            // Add document if uploaded
            const docInput = document.getElementById('seedling_supporting_document');
            if (docInput.files && docInput.files[0]) {
                formData.append('document', docInput.files[0]);
            }

            // Find submit button
            const submitBtn = document.querySelector('#addSeedlingModal .btn-primary');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating...';
            submitBtn.disabled = true;

            // Submit to backend
            fetch('/admin/seedlings/requests', {
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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addSeedlingModal'));
                        modal.hide();

                        showToast('success', data.message || 'Seedling request created successfully');

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = document.getElementById('seedling_' + field);
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
                        showToast('error', data.message || 'Failed to create seedling request');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred while creating the request');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Add event listeners for real-time validation
        document.getElementById('seedling_contact_number')?.addEventListener('input', function() {
            validateSeedlingContactNumber(this.value);
        });

        document.getElementById('seedling_email')?.addEventListener('input', function() {
            validateSeedlingEmail(this.value);
        });


                
        // Show edit modal
        function showEditSeedlingModal(requestId) {
            const modal = new bootstrap.Modal(document.getElementById('editSeedlingModal' + requestId));
            
            // Initialize the modal with existing values
            initializeEditSeedlingModal(requestId);
            
            modal.show();
        }

        // Initialize edit modal with existing data
      function initializeEditSeedlingModal(requestId) {
        const form = document.getElementById('editForm' + requestId);
        if (!form) return;
        
        // Store original values for change detection
        const originalData = {};
        
        // Store personal info
        originalData.first_name = document.getElementById('edit_first_name_' + requestId).value;
        originalData.middle_name = document.getElementById('edit_middle_name_' + requestId).value;
        originalData.last_name = document.getElementById('edit_last_name_' + requestId).value;
        originalData.extension_name = document.getElementById('edit_extension_' + requestId).value;
        originalData.contact_number = document.getElementById('edit_contact_number_' + requestId).value;
        originalData.email = document.getElementById('edit_email_' + requestId).value;
        originalData.barangay = document.getElementById('edit_barangay_' + requestId).value;
        originalData.address = document.getElementById('edit_address_' + requestId).value;
        originalData.planting_location = document.getElementById('edit_planting_location_' + requestId).value;
        originalData.purpose = document.getElementById('edit_purpose_' + requestId).value;
        originalData.preferred_delivery_date = document.getElementById('edit_preferred_delivery_date_' + requestId).value;
        
        // Store in form data attribute
        form.dataset.originalData = JSON.stringify(originalData);
        
        // Clear validation states
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        // Reset submit button - NO CHANGES STATE (similar to update modal)
        const submitBtn = document.getElementById('editSubmitBtn' + requestId);
        if (submitBtn) {
            submitBtn.innerHTML = 'Save Changes'; // No icon initially
            submitBtn.disabled = false; // Keep enabled like update modal
            submitBtn.dataset.hasChanges = 'false';
        }
    }



// Check for changes in edit form
function checkForEditChanges(requestId) {
    const form = document.getElementById('editForm' + requestId);
    const submitBtn = document.getElementById('editSubmitBtn' + requestId);
    
    if (!form || !submitBtn) return;
    
    const originalData = JSON.parse(form.dataset.originalData || '{}');
    
    let hasChanges = false;
    
    // Check all form fields
    const fields = [
        'first_name', 'middle_name', 'last_name', 'extension_name',
        'contact_number', 'email', 'barangay', 'address',
        'planting_location', 'purpose', 'preferred_delivery_date'
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
    
    // Update button state based on changes - ALWAYS KEEP ENABLED
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
   function handleEditSeedlingSubmit(requestId) {
        const form = document.getElementById('editForm' + requestId);
        const submitBtn = document.getElementById('editSubmitBtn' + requestId);
        
        if (!form) {
            showToast('error', 'Form not found');
            return;
        }
        
        // Check if there are no changes
        if (submitBtn && submitBtn.dataset.hasChanges === 'false') {
            showToast('warning', 'No changes detected. Please modify the fields before saving.');
            return;
        }
        
        // Validate form
        if (!validateEditSeedlingForm(requestId)) {
            showToast('error', 'Please fix all validation errors');
            return;
        }
        
        // Show confirmation
        showConfirmationToast(
            'Confirm Update',
            'Are you sure you want to save the changes to this seedling request?',
            () => proceedWithEditSeedling(form, requestId)
        );
    }



        // Validate edit form
        function validateEditSeedlingForm(requestId) {
            const form = document.getElementById('editForm' + requestId);
            let isValid = true;
            
            const requiredFields = [
                { id: 'edit_first_name_' + requestId, label: 'First Name' },
                { id: 'edit_last_name_' + requestId, label: 'Last Name' },
                { id: 'edit_contact_number_' + requestId, label: 'Contact Number' },
                { id: 'edit_barangay_' + requestId, label: 'Barangay' },
                { id: 'edit_address_' + requestId, label: 'Address' }
            ];
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field.id);
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
            const contactInput = document.getElementById('edit_contact_number_' + requestId);
            if (contactInput) {
                validateEditContactNumber(contactInput, requestId);
            }
            
            // Validate email if provided
            const emailInput = document.getElementById('edit_email_' + requestId);
            if (emailInput && emailInput.value.trim()) {
                if (!validateEditEmail(emailInput, requestId)) {
                    isValid = false;
                }
            }
            
            return isValid;
        }

        // Validate edit contact number
        function validateEditContactNumber(input, requestId) {
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');
            
            const phoneRegex = /^(\+639|09)\d{9}$/;
            
            if (input.value.trim() && !phoneRegex.test(input.value.trim())) {
                input.classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)';
                input.parentNode.appendChild(errorDiv);
                return false;
            }
            
            if (input.value.trim()) {
                input.classList.add('is-valid');
            }
            return true;
        }

        // Validate edit email
        function validateEditEmail(input, requestId) {
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
            input.classList.remove('is-invalid', 'is-valid');
            
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            if (!emailPattern.test(input.value.trim())) {
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

        // Proceed with edit submission
        function proceedWithEditSeedling(form, requestId) {
            const submitBtn = document.getElementById('editSubmitBtn' + requestId);
            
            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
            submitBtn.disabled = true;
            
            const formData = new FormData(form);
            
            fetch(form.getAttribute('action'), {
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
                    const modalId = 'editSeedlingModal' + requestId;
                    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    if (modal) modal.hide();
                    
                    showToast('success', data.message || 'Seedling request updated successfully');
                    
                    // Reload page
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Failed to update request');
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
        function capitalizeEditName(input) {
            const value = input.value;
            if (value.length > 0) {
                input.value = value
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
        }

   // Add event listeners for edit form
function initializeEditFormListeners() {
    const editForms = document.querySelectorAll('form[id^="editForm"]');
    
    editForms.forEach(form => {
        const requestId = form.id.replace('editForm', '');
        
        // Add change listeners to all inputs
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('change', () => checkForEditChanges(requestId));
            input.addEventListener('input', () => checkForEditChanges(requestId));
        });
        
        // Name auto-capitalize
        const firstName = form.querySelector(`#edit_first_name_${requestId}`);
        if (firstName) {
            firstName.addEventListener('blur', function() { capitalizeEditName(this); });
        }
        
        const middleName = form.querySelector(`#edit_middle_name_${requestId}`);
        if (middleName) {
            middleName.addEventListener('blur', function() { capitalizeEditName(this); });
        }
        
        const lastName = form.querySelector(`#edit_last_name_${requestId}`);
        if (lastName) {
            lastName.addEventListener('blur', function() { capitalizeEditName(this); });
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeEditFormListeners();
});
    </script>
@endsection
