{{-- resources/views/admin/seedlings/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Supply Requests - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-seedling text-primary me-2"></i>
        <span class="text-primary fw-bold">Supply Requests</span>
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
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $pendingCount }}</div>
                        <div class="stat-label text-warning">Pending</div>
                    </div>
                </div>
            </div>

            <div class="col-xl col-md-6 mb-4 mb-xl-0">
                <div class="card stat-card shadow h-100">
                    <div class="card-body text-center py-3">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-clipboard-check text-info"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $partiallyApprovedCount }}</div>
                        <div class="stat-label text-info">Partially Approved</div>
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
                    <!-- Hidden date inputs -->
                    <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                    <!-- FIXED: layout exactly -->
                    <div class="row g-2">
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
                                <option value="Bagong Silang" {{ request('barangay') == 'Bagong Silang' ? 'selected' : '' }}>Bagong Silang</option>
                                <option value="Calendola" {{ request('barangay') == 'Calendola' ? 'selected' : '' }}>Calendola</option>
                                <option value="Chrysanthemum" {{ request('barangay') == 'Chrysanthemum' ? 'selected' : '' }}>Chrysanthemum</option>
                                <option value="Cuyab" {{ request('barangay') == 'Cuyab' ? 'selected' : '' }}>Cuyab</option>
                                <option value="Estrella" {{ request('barangay') == 'Estrella' ? 'selected' : '' }}>Estrella</option>
                                <option value="Fatima" {{ request('barangay') == 'Fatima' ? 'selected' : '' }}>Fatima</option>
                                <option value="G.S.I.S." {{ request('barangay') == 'G.S.I.S.' ? 'selected' : '' }}>G.S.I.S.</option>
                                <option value="Landayan" {{ request('barangay') == 'Landayan' ? 'selected' : '' }}>Landayan</option>
                                <option value="Langgam" {{ request('barangay') == 'Langgam' ? 'selected' : '' }}>Langgam</option>
                                <option value="Laram" {{ request('barangay') == 'Laram' ? 'selected' : '' }}>Laram</option>
                                <option value="Magsaysay" {{ request('barangay') == 'Magsaysay' ? 'selected' : '' }}>Magsaysay</option>
                                <option value="Maharlika" {{ request('barangay') == 'Maharlika' ? 'selected' : '' }}>Maharlika</option>
                                <option value="Narra" {{ request('barangay') == 'Narra' ? 'selected' : '' }}>Narra</option>
                                <option value="Nueva" {{ request('barangay') == 'Nueva' ? 'selected' : '' }}>Nueva</option>
                                <option value="Pacita 1" {{ request('barangay') == 'Pacita 1' ? 'selected' : '' }}>Pacita 1</option>
                                <option value="Pacita 2" {{ request('barangay') == 'Pacita 2' ? 'selected' : '' }}>Pacita 2</option>
                                <option value="Poblacion" {{ request('barangay') == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                                <option value="Riverside" {{ request('barangay') == 'Riverside' ? 'selected' : '' }}>Riverside</option>
                                <option value="Rosario" {{ request('barangay') == 'Rosario' ? 'selected' : '' }}>Rosario</option>
                                <option value="Sampaguita Village" {{ request('barangay') == 'Sampaguita Village' ? 'selected' : '' }}>Sampaguita Village</option>
                                <option value="San Antonio" {{ request('barangay') == 'San Antonio' ? 'selected' : '' }}>San Antonio</option>
                                <option value="San Lorenzo Ruiz" {{ request('barangay') == 'San Lorenzo Ruiz' ? 'selected' : '' }}>San Lorenzo Ruiz</option>
                                <option value="San Roque" {{ request('barangay') == 'San Roque' ? 'selected' : '' }}>San Roque</option>
                                <option value="San Vicente" {{ request('barangay') == 'San Vicente' ? 'selected' : '' }}>San Vicente</option>
                                <option value="Santo Niño" {{ request('barangay') == 'Santo Niño' ? 'selected' : '' }}>Santo Niño</option>
                                <option value="United Bayanihan" {{ request('barangay') == 'United Bayanihan' ? 'selected' : '' }}>United Bayanihan</option>
                                <option value="United Better Living" {{ request('barangay') == 'United Better Living' ? 'selected' : '' }}>United Better Living</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search name, number, contact..." value="{{ request('search') }}"
                                    oninput="autoSearch()" id="searchInput">
                                <button class="btn btn-outline-secondary" type="submit" title="Search" id="searchButton">
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
                                <i></i>Clear
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
                            <i class="fas fa-seedling me-2"></i>Supply Requests
                        </h6>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm" onclick="showAddSeedlingModal()">
                            <i class="fas fa-user-plus me-2"></i>Add Request
                        </button>
                        <a href="{{ route('admin.seedlings.export', request()->query()) }}"
                            class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
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
                                    <th class="px-3 py-3 fw-medium text-white border-end text-center">Pickup Date</th>
                                    <th class="px-3 py-3 fw-medium text-white border-end text-center">Claimed</th>
                                    <th class="px-3 py-3 fw-medium text-white text-center">Documents</th>
                                    <th class="px-3 py-3 fw-medium text-white text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $request)
                                    <tr class="border-bottom" data-request-id="{{ $request->id }}"
                                        data-document-path="{{ $request->document_path }}">
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
                                        <!--  NEW: Pickup Date Column -->
                                        
<td class="px-3 py-3 border-end text-center">
    @if($request->pickup_date)
        <div class="pickup-date-cell">
            <small class="d-block fw-semibold">
                {{ \Carbon\Carbon::parse($request->pickup_date)->format('M d, Y') }}
            </small>
            
            @php
                $pickupDate = \Carbon\Carbon::parse($request->pickup_date);
                $daysLeft = now()->diffInDays($pickupDate, false);
            @endphp
            
            @if($pickupDate->isPast())
                <span class="badge bg-danger badge-sm mt-2">
                    <i class="fas fa-times-circle me-1"></i>Expired
                </span>
            @elseif($daysLeft > 0 && $daysLeft <= 3)
                <span class="badge bg-warning text-dark badge-sm mt-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    {{ (int)$daysLeft }}d left
                </span>
            @else
                <span class="badge bg-success badge-sm mt-2">
                    <i class="fas fa-check-circle me-1"></i>Active
                </span>
            @endif
        </div>
    @else
        <span class="text-muted text-center d-block">
            <i class="fas fa-minus me-1"></i>Not set
        </span>
    @endif
</td>
<td class="px-3 py-3 border-end text-center">
    @if($request->claimed_at)
        <div class="claimed-status">
            <small class="d-block fw-semibold text-success">
                <i class="fas fa-check-circle me-1"></i>Claimed
            </small>
            <small class="text-muted">
                {{ $request->claimed_at->format('M d, Y') }}
            </small>
        </div>
    @elseif($request->pickup_date && $request->pickup_date->isPast())
        <span class="badge bg-danger">
            <i class="fas fa-times-circle me-1"></i>Expired - Not Claimed
        </span>
    @elseif(in_array($request->status, ['approved', 'partially_approved']))
        <div class="claimed-actions">
            <button type="button" class="btn btn-sm btn-outline-success" 
                onclick="markAsClaimed({{ $request->id }}, '{{ $request->request_number }}')">
                <i class="fas fa-check me-1"></i>Mark Claimed
            </button>
        </div>
    @else
        <span class="text-muted">—</span>
    @endif
</td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="seedling-table-documents">
                                                @if ($request->hasDocuments())
                                                    <div class="seedling-document-previews">
                                                        <button type="button" class="seedling-mini-doc"
                                                            onclick="viewDocument('{{ $request->document_path }}', 'Supply Request #{{ $request->request_number }} - Supporting Document')"
                                                            title="Supporting Document">
                                                            <div class="seedling-mini-doc-icon">
                                                                <i class="fas fa-file-alt text-primary"></i>
                                                            </div>
                                                        </button>
                                                    </div>
                                                    <button type="button" class="seedling-document-summary"
                                                        onclick="viewDocument('{{ $request->document_path }}', 'Supply Request #{{ $request->request_number }} - Supporting Document')"
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
                                                <!-- Primary Actions -->
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal{{ $request->id }}">
                                                    <i class="fas fa-eye"></i> View
                                                </button>

                                                <button type="button" class="btn btn-outline-dark"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateModal{{ $request->id }}">
                                                    <i class="fas fa-sync"></i> Change Status
                                                </button>

                                                <!-- Dropdown for More Actions -->
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        title="More Actions">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                onclick="showEditSeedlingModal({{ $request->id }})">
                                                                <i class="fas fa-edit me-2 text-success"></i>Edit
                                                                Information
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                                onclick="deleteSeedlingRequest({{ $request->id }}, '{{ $request->request_number }}')">
                                                                <i class="fas fa-trash me-2"></i>Delete Request
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
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
                <!-- View Modal Enhanced -->
                <div class="modal fade" id="viewModal{{ $request->id }}" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title w-100 text-center">
                                    <i></i>
                                    Supply Request Details - {{ $request->request_number }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-4">

                                    <!-- Personal Information Card -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <strong>Request #:</strong>
                                                        <span class="text-primary">{{ $request->request_number }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <strong>Full Name:</strong>
                                                        <span>{{ $request->full_name }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <strong>Contact Number:</strong>
                                                        <span>
                                                            <a href="tel:{{ $request->contact_number }}"
                                                                class="text-decoration-none">
                                                                {{ $request->contact_number }}
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Information Card -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-success">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location
                                                    Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <strong>Barangay:</strong>
                                                        <span>{{ $request->barangay }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Request Information Card -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-info">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Request
                                                    Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <strong>Total Quantity:</strong>
                                                        <span>{{ $request->total_quantity }} items</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <strong>Current Status:</strong>
                                                        <span>
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
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timeline Information Card -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-warning">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <strong>Date Applied:</strong>
                                                        <span>{{ $request->created_at->format('F d, Y g:i A') }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <strong>Last Updated:</strong>
                                                        <span>{{ $request->updated_at->format('F d, Y g:i A') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ✅ NEW: Pickup Date Card -->
                                    <div class="col-12">
    <div class="card border-info">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Pickup Information</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @php
                    $viewPickupDate = null;
                    if($request->pickup_date) {
                        $viewPickupDate = \Carbon\Carbon::parse($request->pickup_date);
                    }
                @endphp
                
                @if($viewPickupDate)
                    <div class="col-md-6">
                        <strong>Pickup Date:</strong>
                        <p class="mb-0">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $viewPickupDate->format('F d, Y') }}
                        </p>
                        <small class="text-muted">
                            ({{ $viewPickupDate->diffForHumans() }})
                        </small>
                    </div>
                    <div class="col-md-6">
                        <strong>Pickup Status:</strong>
                        <p class="mb-0">
                            @php
                                $daysLeft = now()->diffInDays($viewPickupDate, false);
                            @endphp
                            
                            @if($viewPickupDate->isFuture())
                                @if($daysLeft > 0 && $daysLeft <= 3)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Expiring Soon - {{ (int)$daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }} left
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Active
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Expired
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    @if($request->pickup_expired_at && $viewPickupDate->isFuture())
                        <div class="col-12">
                            <strong>Expiration Deadline:</strong>
                            <p class="mb-0">
                                <i class="fas fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($request->pickup_expired_at)->format('F d, Y') }}
                            </p>
                            <small class="text-muted">
                                @php
                                    $daysUntilExpiry = now()->diffInDays(\Carbon\Carbon::parse($request->pickup_expired_at), false);
                                @endphp
                                @if($daysUntilExpiry > 0)
                                    Valid for {{ (int)$daysUntilExpiry }} more day{{ $daysUntilExpiry !== 1 ? 's' : '' }}
                                @else
                                    Expired
                                @endif
                            </small>
                        </div>
                    @endif
                @else
                    <div class="col-12">
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i>No pickup date set
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>



                                    <!-- Requested Items by Category Card -->
                                    <div class="col-12">
                                        <div class="card border-primary">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0 text-dark">
                                                  <strong>Requested Items by Category</strong>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $itemsByCategory = $request->items->groupBy('category_id');
                                                @endphp

                                                @forelse($itemsByCategory as $categoryId => $items)
                                                    @php
                                                        $category = $items->first()->category;
                                                    @endphp
                                                    <div class="mb-4 p-3 border rounded {{ !$loop->last ? 'mb-3' : '' }}">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <strong class="text-primary">
                                                                <i
                                                                    class="fas {{ $category->icon ?? 'fa-leaf' }} me-2"></i>
                                                                {{ $category->display_name }}
                                                            </strong>
                                                            <span class="badge bg-secondary">{{ $items->count() }}
                                                                items</span>
                                                        </div>

                                                        <!-- Items List -->
                                                        <div class="ms-3">
                                                            @php
                                                                $approvedItems = $items->where('status', 'approved');
                                                                $pendingItems = $items->where('status', 'pending');
                                                                $rejectedItems = $items->where('status', 'rejected');
                                                            @endphp

                                                            @if ($approvedItems->count() > 0)
                                                                <div class="mb-2">
                                                                    <small class="text-success fw-semibold">✓
                                                                        Approved:</small>
                                                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                                                        @foreach ($approvedItems as $item)
                                                                            <span class="badge bg-success">
                                                                                {{ $item->item_name }}
                                                                                <strong>({{ $item->requested_quantity }})</strong>
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if ($pendingItems->count() > 0)
                                                                <div class="mb-2">
                                                                    <small class="text-warning fw-semibold">
                                                                        Pending:</small>
                                                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                                                        @foreach ($pendingItems as $item)
                                                                            <span class="badge bg-warning text-white">
                                                                                {{ $item->item_name }}
                                                                                <strong>({{ $item->requested_quantity }})</strong>
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if ($rejectedItems->count() > 0)
                                                                <div class="mb-2">
                                                                    <small class="text-danger fw-semibold">✗
                                                                        Rejected:</small>
                                                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                                                        @foreach ($rejectedItems as $item)
                                                                            <span class="badge bg-danger">
                                                                                {{ $item->item_name }}
                                                                                <strong>({{ $item->requested_quantity }})</strong>
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center py-3 text-muted">
                                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                                        <p>No items requested</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Supporting Document Card -->
                                    <div class="col-12">
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white text-center">
                                                <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i>Supporting
                                                    Document</h6>
                                            </div>
                                            <div class="card-body text-center">
                                                @if ($request->hasDocuments())
                                                    <div class="p-4 border border-primary rounded bg-light">
                                                        <i class="fas fa-file-alt fa-3x mb-3" style="color: #0d6efd;"></i>
                                                        <h6>Supporting Document</h6>
                                                        <span class="badge bg-primary mb-3">Uploaded</span>
                                                        <br>
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            onclick="viewDocument('{{ $request->document_path }}', 'Supply Request #{{ $request->request_number }} - Supporting Document')">
                                                            <i class="fas fa-eye me-1"></i>View Document
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="p-4 border border-secondary rounded">
                                                        <i class="fas fa-file-slash fa-3x mb-3"
                                                            style="color: #6c757d;"></i>
                                                        <h6>No Document Uploaded</h6>
                                                        <span class="badge bg-secondary">Not Uploaded</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Remarks Card - Always Show -->
                                    <div class="col-12">
                                        <div class="card border-info">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Admin Remarks</h6>
                                            </div>
                                            <div class="card-body">
                                                @if ($request->remarks && !empty(trim($request->remarks)))
                                                    <p class="mb-0">{{ $request->remarks }}</p>
                                                @else
                                                    <p class="mb-0 text-muted">
                                                        <i class="fas fa-minus-circle me-1"></i>No remarks added
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal enhanced -->
                <div class="modal fade" id="editSeedlingModal{{ $request->id }}" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title w-100 text-center">
                                    <i></i>Edit Request - <span
                                        id="editRequestNumber{{ $request->id }}">{{ $request->request_number }}</span>
                                </h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <form id="editForm{{ $request->id }}" class="needs-validation"
                                    action="{{ route('admin.seedlings.update', $request) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Personal Information Card -->
                                    <div class="card mb-3 border-0 bg-light">
                                        <div class="card-header bg-white border-0 pb-0">
                                            <h6 class="mb-0 fw-semibold text-primary">
                                                <i class="fas fa-user me-2"></i>Personal Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="edit_first_name_{{ $request->id }}"
                                                        class="form-label fw-semibold">
                                                        First Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control"
                                                        id="edit_first_name_{{ $request->id }}" name="first_name"
                                                        value="{{ $request->first_name }}" required maxlength="100"
                                                        placeholder="First name"
                                                        onchange="checkForEditChanges({{ $request->id }})"
                                                        oninput="checkForEditChanges({{ $request->id }})">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="edit_middle_name_{{ $request->id }}"
                                                        class="form-label fw-semibold">
                                                        Middle Name
                                                    </label>
                                                    <input type="text" class="form-control"
                                                        id="edit_middle_name_{{ $request->id }}" name="middle_name"
                                                        value="{{ $request->middle_name }}" maxlength="100"
                                                        placeholder="Middle name (optional)"
                                                        onchange="checkForEditChanges({{ $request->id }})"
                                                        oninput="checkForEditChanges({{ $request->id }})">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="edit_last_name_{{ $request->id }}"
                                                        class="form-label fw-semibold">
                                                        Last Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control"
                                                        id="edit_last_name_{{ $request->id }}" name="last_name"
                                                        value="{{ $request->last_name }}" required maxlength="100"
                                                        placeholder="Last name"
                                                        onchange="checkForEditChanges({{ $request->id }})"
                                                        oninput="checkForEditChanges({{ $request->id }})">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="edit_extension_{{ $request->id }}"
                                                        class="form-label fw-semibold">
                                                        Extension
                                                    </label>
                                                    <select class="form-select" id="edit_extension_{{ $request->id }}"
                                                        name="extension_name"
                                                        onchange="checkForEditChanges({{ $request->id }})">
                                                        <option value="">None</option>
                                                        <option value="Jr."
                                                            {{ $request->extension_name === 'Jr.' ? 'selected' : '' }}>Jr.
                                                        </option>
                                                        <option value="Sr."
                                                            {{ $request->extension_name === 'Sr.' ? 'selected' : '' }}>Sr.
                                                        </option>
                                                        <option value="II"
                                                            {{ $request->extension_name === 'II' ? 'selected' : '' }}>II
                                                        </option>
                                                        <option value="III"
                                                            {{ $request->extension_name === 'III' ? 'selected' : '' }}>III
                                                        </option>
                                                        <option value="IV"
                                                            {{ $request->extension_name === 'IV' ? 'selected' : '' }}>IV
                                                        </option>
                                                        <option value="V"
                                                            {{ $request->extension_name === 'V' ? 'selected' : '' }}>V
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_contact_number_{{ $request->id }}"
                                                        class="form-label fw-semibold">
                                                        Contact Number <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="tel" class="form-control"
                                                        id="edit_contact_number_{{ $request->id }}"
                                                        name="contact_number" value="{{ $request->contact_number }}"
                                                        required placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$"
                                                        maxlength="20"
                                                        onchange="checkForEditChanges({{ $request->id }})"
                                                        oninput="checkForEditChanges({{ $request->id }})">
                                                    <small class="text-muted d-block mt-2">
                                                        <i class="fas fa-info-circle me-1"></i>09XXXXXXXXX
                                                    </small>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Request Number
                                                    </label>
                                                    <input type="text" class="form-control"
                                                        id="edit_request_number_{{ $request->id }}"
                                                        value="{{ $request->request_number }}" disabled placeholder="-">
                                                    <small class="text-muted d-block mt-2">
                                                        <i class="fas fa-info-circle me-1"></i>Auto-generated (cannot be
                                                        changed)
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Information Card -->
                                    <div class="card mb-3 border-0 bg-light">
                                        <div class="card-header bg-white border-0 pb-0">
                                            <h6 class="mb-0 fw-semibold text-primary">
                                                <i class="fas fa-map-marker-alt me-2"></i>Location Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="edit_barangay_{{ $request->id }}"
                                                        class="form-label fw-semibold">
                                                        Barangay <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select" id="edit_barangay_{{ $request->id }}"
                                                        name="barangay" required
                                                        onchange="checkForEditChanges({{ $request->id }})">
                                                        <option value="">Select Barangay</option>
                                                        <option value="Bagong Silang" {{ $request->barangay === 'Bagong Silang' ? 'selected' : '' }}>Bagong Silang</option>
                                                        <option value="Calendola" {{ $request->barangay === 'Calendola' ? 'selected' : '' }}>Calendola</option>
                                                        <option value="Chrysanthemum" {{ $request->barangay === 'Chrysanthemum' ? 'selected' : '' }}>Chrysanthemum</option>
                                                        <option value="Cuyab" {{ $request->barangay === 'Cuyab' ? 'selected' : '' }}>Cuyab</option>
                                                        <option value="Estrella" {{ $request->barangay === 'Estrella' ? 'selected' : '' }}>Estrella</option>
                                                        <option value="Fatima" {{ $request->barangay === 'Fatima' ? 'selected' : '' }}>Fatima</option>
                                                        <option value="G.S.I.S." {{ $request->barangay === 'G.S.I.S.' ? 'selected' : '' }}>G.S.I.S.</option>
                                                        <option value="Landayan" {{ $request->barangay === 'Landayan' ? 'selected' : '' }}>Landayan</option>
                                                        <option value="Langgam" {{ $request->barangay === 'Langgam' ? 'selected' : '' }}>Langgam</option>
                                                        <option value="Laram" {{ $request->barangay === 'Laram' ? 'selected' : '' }}>Laram</option>
                                                        <option value="Magsaysay" {{ $request->barangay === 'Magsaysay' ? 'selected' : '' }}>Magsaysay</option>
                                                        <option value="Maharlika" {{ $request->barangay === 'Maharlika' ? 'selected' : '' }}>Maharlika</option>
                                                        <option value="Narra" {{ $request->barangay === 'Narra' ? 'selected' : '' }}>Narra</option>
                                                        <option value="Nueva" {{ $request->barangay === 'Nueva' ? 'selected' : '' }}>Nueva</option>
                                                        <option value="Pacita 1" {{ $request->barangay === 'Pacita 1' ? 'selected' : '' }}>Pacita 1</option>
                                                        <option value="Pacita 2" {{ $request->barangay === 'Pacita 2' ? 'selected' : '' }}>Pacita 2</option>
                                                        <option value="Poblacion" {{ $request->barangay === 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                                                        <option value="Riverside" {{ $request->barangay === 'Riverside' ? 'selected' : '' }}>Riverside</option>
                                                        <option value="Rosario" {{ $request->barangay === 'Rosario' ? 'selected' : '' }}>Rosario</option>
                                                        <option value="Sampaguita Village" {{ $request->barangay === 'Sampaguita Village' ? 'selected' : '' }}>Sampaguita Village</option>
                                                        <option value="San Antonio" {{ $request->barangay === 'San Antonio' ? 'selected' : '' }}>San Antonio</option>
                                                        <option value="San Lorenzo Ruiz" {{ $request->barangay === 'San Lorenzo Ruiz' ? 'selected' : '' }}>San Lorenzo Ruiz</option>
                                                        <option value="San Roque" {{ $request->barangay === 'San Roque' ? 'selected' : '' }}>San Roque</option>
                                                        <option value="San Vicente" {{ $request->barangay === 'San Vicente' ? 'selected' : '' }}>San Vicente</option>
                                                        <option value="Santo Niño" {{ $request->barangay === 'Santo Niño' ? 'selected' : '' }}>Santo Niño</option>
                                                        <option value="United Bayanihan" {{ $request->barangay === 'United Bayanihan' ? 'selected' : '' }}>United Bayanihan</option>
                                                        <option value="United Better Living" {{ $request->barangay === 'United Better Living' ? 'selected' : '' }}>United Better Living</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
<!-- ✅ NEW: Pickup Date Section in Edit Modal - EDITABLE -->
<div class="card border-0 bg-light mb-3">
    <div class="card-header bg-white border-0 pb-0">
        <h6 class="mb-0 fw-semibold text-primary">
            <i class="fas fa-calendar-check me-2"></i>Pickup Date
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="edit_seedling_pickup_date_{{ $request->id }}" class="form-label fw-semibold">
                    Set/Update Pickup Date
                </label>
                <input 
                    type="date" 
                    id="edit_seedling_pickup_date_{{ $request->id }}" 
                    name="pickup_date"
                    class="form-control"
                    value="{{ $request->pickup_date ? $request->pickup_date->format('Y-m-d') : '' }}"
                    onchange="checkForEditChanges({{ $request->id }})">
                
                <div id="edit_pickup_date_display_{{ $request->id }}" style="margin-top: 12px; padding: 12px; background: #f5f5f5; border-radius: 6px; display: none;">
                    <strong>Selected:</strong> <span id="edit_pickup_date_text_{{ $request->id }}"></span>
                </div>
            </div>
            @if($request->pickup_date)
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Current Status</label>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ $request->pickup_date->format('F d, Y') }}
                        
                        @if($request->pickup_date->isPast())
                            <span class="badge bg-danger float-end">
                                <i class="fas fa-exclamation-circle me-1"></i>Expired
                            </span>
                       @elseif($request->pickup_expired_at && now()->diffInDays($request->pickup_expired_at) <= 3 && now()->diffInDays($request->pickup_expired_at) > 0)
                            <span class="badge bg-warning float-end text-dark">
                                <i class="fas fa-exclamation-triangle me-1"></i>Expiring Soon
                            </span>
                        @else
                            <span class="badge bg-success float-end">
                                <i class="fas fa-check-circle me-1"></i>Active
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

                                    <!-- Supporting Document Card EDIT MODAL-->
                                    <div class="card mb-3 border-0 bg-light">
                                        <div class="card-header bg-white border-0 pb-0">
                                            <h6 class="mb-0 fw-semibold text-primary">
                                                <i class="fas fa-file-upload me-2"></i>Supporting Document
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted small mb-4">
                                                <i class="fas fa-info-circle me-1"></i>
                                                View or upload supporting document. Supported formats: JPG, PNG, PDF (Max
                                                10MB)
                                            </p>

                                            <!-- Current Document Display -->
                                            <div id="edit_seedling_current_document_{{ $request->id }}"
                                                style="display: none; margin-bottom: 1.5rem;">
                                                <div id="edit_seedling_current_doc_preview_{{ $request->id }}"></div>
                                            </div>

                                            <!-- Upload New Document Section -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="edit_seedling_supporting_document_{{ $request->id }}"
                                                        class="form-label fw-semibold">
                                                        Supporting Document
                                                    </label>
                                                    <input type="file" class="form-control"
                                                        id="edit_seedling_supporting_document_{{ $request->id }}"
                                                        name="document" accept=".pdf,.jpg,.jpeg,.png"
                                                        onchange="previewEditSeedlingDocument('edit_seedling_supporting_document_{{ $request->id }}', 'edit_seedling_doc_preview_{{ $request->id }}')">
                                                    <small class="text-muted d-block mt-2">
                                                        <i class="fas fa-info-circle me-1"></i>Upload a new file to replace
                                                        it.
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- New Document Preview -->
                                            <div id="edit_seedling_doc_preview_{{ $request->id }}" class="mt-3">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Request Status (Read-only) Card -->
                                    <div class="card mb-3 border-0 bg-light">
                                        <div class="card-header bg-white border-0 pb-0">
                                            <h6 class="mb-0 fw-semibold text-primary">
                                                <i class="fas fa-info-circle me-2"></i>Request Status (Read-only)
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <small class="text-muted d-block mb-2">Current Status</small>
                                                    <div>
                                                        <span id="edit_status_badge_{{ $request->id }}"
                                                            class="badge bg-{{ match ($request->status) {
                                                                'approved' => 'success',
                                                                'partially_approved' => 'info',
                                                                'rejected' => 'danger',
                                                                'under_review', 'pending' => 'warning',
                                                                default => 'secondary',
                                                            } }} fs-6">
                                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <small class="text-muted d-block mb-2">Date Applied</small>
                                                    <div id="edit_created_at_{{ $request->id }}" class="fw-semibold">
                                                        {{ $request->created_at->format('M d, Y g:i A') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info Alert -->
                                    <div class="alert alert-info border-left-info mb-0">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <strong>Note:</strong> You can edit all request information here.
                                        To change item statuses or add remarks, use the "Change Status" button from the main
                                        table.
                                    </div>
                                </form>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i></i>Cancel
                                </button>
                                <button type="button" class="btn btn-primary" id="editSubmitBtn{{ $request->id }}"
                                    onclick="handleEditSeedlingSubmit({{ $request->id }})">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
         <!-- DELETE SEEDLING MODAL - FISHR DESIGN (CONSISTENT) -->
                <div class="modal fade" id="deleteSeedlingModal" tabindex="-1" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title w-100 text-center">Move Supply Request to Recycle Bin</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger" role="alert">
                                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                                    <p class="mb-0">Are you sure you want to delete this Supply Request? <strong id="delete_seedling_name"></strong> will be moved to the Recycle Bin.</p>
                                </div>
                                <ul class="mb-0" style="padding-left: 1.25rem;">
                                    <li>Remove the supply request from active records</li>
                                    <li>Hide it from users and administrators</li>
                                    <li>Keep all documents and attachments</li>
                                    <li><strong>Can be restored from the Recycle Bin</strong></li>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" onclick="confirmPermanentDeleteSeedling()"
                                    id="confirm_delete_seedling_btn">
                                    <span class="btn-text">Move to Recycle Bin</span>
                                    <span class="btn-loader" style="display: none;"><span
                                            class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- UPDATED: Change Status Modal -->
                <div class="modal fade" id="updateModal{{ $request->id }}" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title w-100 text-center">
                                    <i></i>
                                    Change Status Items - {{ $request->request_number }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('admin.seedlings.update-items', $request) }}"
                                    id="updateForm{{ $request->id }}">
                                    @csrf
                                    @method('PATCH')

                                    <!-- Request Information Card -->
                                    <div class="card bg-light border-primary mb-4">
                                        <div class="card-header bg-white border-0 pb-0">
                                            <h6 class="mb-0 fw-semibold text-primary">
                                                <i class="fas fa-info-circle me-2"></i>Request Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Request #</small>
                                                        <strong
                                                            class="text-primary">{{ $request->request_number }}</strong>
                                                    </div>
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Applicant Name</small>
                                                        <strong>{{ $request->full_name }}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Barangay</small>
                                                        <strong>{{ $request->barangay }}</strong>
                                                    </div>
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block">Current Status</small>
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
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Items Update Card -->
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-header bg-white border-0 pb-0">
                                            <h6 class="mb-0 fw-semibold text-primary">
                                                <i class="fas fa-seedling me-2"></i>Update Item Status
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
                                                <div class="mb-4 p-3 border-0 bg-white rounded-3 shadow-sm">
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
                                                                : 'bg-light border') }}
                                                        rounded-3"
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
                                                                    style="min-width: 130px;"
                                                                    data-item-id="{{ $item->id }}"
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
                                        </div>
                                    </div>

                                    <!-- Remarks Card -->
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-header bg-white border-0 pb-0">
                                            <h6 class="mb-0 fw-semibold text-primary">
                                                <i class="fas fa-comment me-2"></i>Admin Remarks
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <label for="remarks{{ $request->id }}" class="form-label fw-semibold">
                                                Remarks (Optional)
                                            </label>
                                            <textarea name="remarks" id="remarks{{ $request->id }}" class="form-control" rows="4"
                                                placeholder="Add any comments about this status change..." maxlength="1000"
                                                onchange="checkForSeedlingChanges({{ $request->id }})"
                                                oninput="updateSeedlingRemarksCounter({{ $request->id }}); checkForSeedlingChanges({{ $request->id }})">{{ $request->remarks }}</textarea>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Provide context for this status update
                                                </small>
                                                <small class="text-muted" id="remarksCounter{{ $request->id }}">
                                                    <span
                                                        id="charCount{{ $request->id }}">{{ strlen($request->remarks ?? '') }}</span>/1000
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ✅ NEW: Pickup Date Section in Edit Modal -->
@if($request->status === 'approved' || $request->status === 'partially_approved')
    <div class="card border-0 bg-light mb-3">
        <div class="card-header bg-white border-0 pb-0">
            <h6 class="mb-0 fw-semibold text-primary">
                <i class="fas fa-calendar-check me-2"></i>Pickup Date
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="edit_seedling_pickup_date_{{ $request->id }}" class="form-label fw-semibold">
                        Set/Update Pickup Date
                    </label>
                    <input 
                        type="date" 
                        id="edit_seedling_pickup_date_{{ $request->id }}" 
                        name="pickup_date"
                        class="form-control"
                        value="{{ $request->pickup_date ? $request->pickup_date->format('Y-m-d') : '' }}"
                        onchange="checkForEditChanges({{ $request->id }})">
                    
                    <div id="edit_pickup_date_display_{{ $request->id }}" style="margin-top: 12px; padding: 12px; background: #f5f5f5; border-radius: 6px; display: none;">
                        <strong>Selected:</strong> <span id="edit_pickup_date_text_{{ $request->id }}"></span>
                    </div>
                </div>
                @if($request->pickup_date)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Current Status</label>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ $request->pickup_date->format('F d, Y') }}
                            
                            @if($request->pickup_date->isPast())
                                <span class="badge bg-danger float-end">
                                    <i class="fas fa-exclamation-circle me-1"></i>Expired
                                </span>
                           @elseif($request->pickup_expired_at && now()->diffInDays($request->pickup_expired_at) <= 3 && now()->diffInDays($request->pickup_expired_at) > 0)
                                <span class="badge bg-warning float-end text-dark">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Expiring Soon
                                </span>
                            @else
                                <span class="badge bg-success float-end">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

                                    <!-- Info Alert -->
                                    <div class="alert alert-info border-left-info mb-0">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <strong>Note:</strong> Your changes will be logged and item statuses will be updated
                                        accordingly.
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i></i>Cancel
                                </button>
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
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No supply requests found</h5>
            <p class="text-muted">
                @if (request('search') || request('status'))
                    No requests match your search criteria.
                @else
                    There are no supply requests yet.
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

    <!-- Add Supply Request Modal - IMPROVED DESIGN -->
    <div class="modal fade" id="addSeedlingModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Add New Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSeedlingForm" enctype="multipart/form-data">
                        @csrf
                        <!-- Personal Information Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="seedling_first_name" class="form-label fw-semibold">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="seedling_first_name" required
                                            maxlength="100" placeholder="First name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="seedling_middle_name" class="form-label fw-semibold">
                                            Middle Name
                                        </label>
                                        <input type="text" class="form-control" id="seedling_middle_name"
                                            maxlength="100" placeholder="Middle name (optional)">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="seedling_last_name" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="seedling_last_name" required
                                            maxlength="100" placeholder="Last name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="seedling_extension" class="form-label fw-semibold">
                                            Extension
                                        </label>
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
                                        <label for="seedling_contact_number" class="form-label fw-semibold">
                                            Contact Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="seedling_contact_number" required
                                            placeholder="09XXXXXXXXX" pattern="^(\+639|09)\d{9}$" maxlength="20">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>09XXXXXXXXX 
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-map-marker-alt me-2"></i>Location Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="seedling_barangay" class="form-label fw-semibold">
                                            Barangay <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="seedling_barangay" required>
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

                        <!-- Request Items Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-semibold text-primary">
                                        <i class="fas fa-leaf me-2"></i>Requested Items
                                    </h6>
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
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-info-circle me-1"></i>Add at least one item to the request
                                </p>
                            </div>
                        </div>

                        <!-- Pickup Date Card in Add Modal -->
<div class="card mb-3 border-0 bg-light">
    <div class="card-header bg-white border-0 pb-0">
        <h6 class="mb-0 fw-semibold text-primary">
            <i class="fas fa-calendar-check me-2"></i>Date
        </h6>
    </div>
    <div class="card-body">
        <div class="seedlings-form-group">
            <label for="seedling_pickup_date_add">
                <i class="fas fa-calendar-check"></i> Pickup Date 
                <span class="text-danger">*</span>
            </label>
            
            <div class="pickup-info-box" style="margin-bottom: 12px; padding: 12px; background: #e8f5e9; border-radius: 6px; border-left: 4px solid #40916c;">
                <i class="fas fa-info-circle" style="color: #40916c; margin-right: 8px;"></i>
                <strong>Weekdays only (Mon-Fri)</strong> • Valid for 30 days from approval
            </div>

            <input 
                type="date" 
                id="seedling_pickup_date_add" 
                name="pickup_date"
                class="form-control"
                required>

            <div id="seedling_pickup_date_display_add" style="margin-top: 12px; padding: 12px; background: #f5f5f5; border-radius: 6px; display: none;">
                <strong>Selected:</strong> <span id="seedling_pickup_date_text_add"></span>
            </div>
        </div>
    </div>
</div>

                        <!-- Supporting Document Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-file-upload me-2"></i>Supporting Document (Optional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-4">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Upload supporting document. Supported formats: JPG, PNG, PDF (Max 10MB)
                                </p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="seedling_supporting_document" class="form-label fw-semibold">
                                            Upload Document
                                        </label>
                                        <input type="file" class="form-control" id="seedling_supporting_document"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            onchange="previewSeedlingDocument('seedling_supporting_document', 'seedling_doc_preview')">
                                    </div>
                                    <div class="col-md-6">
                                        <div id="seedling_doc_preview"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information Card -->
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-pencil-alt me-2"></i>Additional Information
                                </h6>
                            </div>
                        </div>

                        <!-- Request Status Card -->
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="mb-0 fw-semibold text-primary">
                                    <i class="fas fa-cog me-2"></i>Request Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="seedling_status" class="form-label fw-semibold">
                                        Initial Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="seedling_status" required>
                                        <option value="pending" selected>Pending</option>
                                        <option value="under_review">Under Review</option>
                                        <option value="approved">Approved</option>
                                        <option value="partially_approved">Partially Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="seedling_remarks" class="form-label fw-semibold">
                                        Remarks (Optional)
                                    </label>
                                    <textarea class="form-control" id="seedling_remarks" rows="3" maxlength="1000"
                                            placeholder="Any notes or comments..."
                                            oninput="updateAddSeedlingRemarksCounter()"></textarea>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Provide context for this request
                                            </small>
                                            <small class="text-muted" id="addRemarksCounter">
                                                <span id="addCharCount">0</span>/1000
                                            </small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitAddSeedling()">
                        <i class="fas fa-save me-1"></i>Create Request
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
                    <h5 class="modal-title w-100 text-center" id="dateFilterModalLabel">
                        <i></i>Select Date Range
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
            border-radius: 0px;
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
            margin-right: 0;
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
            border: 2px solid #007bff;
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
            border: 2px solid #007bff;
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

        /* Seedling View Modal Styling */
        [id^="viewModal"] .modal-content {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        [id^="viewModal"] .modal-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%)!important;
            border-bottom: 2px solid #0b5ed7;
            padding: 1.5rem;
        }

        [id^="viewModal"] .modal-header .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: white;
        }

        [id^="viewModal"] .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 1.25rem;
        }

        [id^="viewModal"] .modal-body {
            padding: 2rem;
            background-color: #fff;
        }

        [id^="viewModal"] .card {
            border-width: 2px;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        [id^="viewModal"] .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        [id^="viewModal"] .card-header {
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: white;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }

        [id^="viewModal"] .card-header.bg-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
        }

        [id^="viewModal"] .card-header.bg-success {
            background: linear-gradient(135deg, #198754 0%, #157347 100%) !important;
        }

        [id^="viewModal"] .card-header.bg-info {
            background: linear-gradient(135deg, #0dcaf0 0%, #0bb5db 100%) !important;
        }

        [id^="viewModal"] .card-header.bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
            color: #000;
        }

        [id^="viewModal"] .card-header.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5c636a 100%) !important;
        }

        [id^="viewModal"] .card-header.bg-light {
            background: linear-gradient(135deg, #e7f3ff 0%, #d4e8ff 100%) !important;
            font-weight: 600;
            color: #0d6efd !important;
        }

        [id^="viewModal"] .card-header.bg-light h6 {
            color: #0d6efd !important;
            font-weight: 700;
            margin: 0;
        }

        [id^="viewModal"] .card-header.bg-light i {
            color: #0d6efd !important;
        }

        [id^="viewModal"] .card-body {
            padding: 1.5rem;
            background-color: #fff;
        }

        [id^="viewModal"] .card-body>div>div {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        [id^="viewModal"] .card-body>div>div:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        [id^="viewModal"] strong {
            color: #495057;
            font-weight: 600;
            display: block;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.25rem;
        }

        [id^="viewModal"] span {
            color: #333;
            font-size: 0.95rem;
            display: block;
        }

        [id^="viewModal"] a {
            color: #0d6efd;
            text-decoration: none;
        }

        [id^="viewModal"] a:hover {
            text-decoration: underline;
        }

        [id^="viewModal"] .badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-top: 0.25rem;
            color: white !important;
        }

        [id^="viewModal"] .badge.bg-success {
            background-color: #198754 !important;
            color: white !important;
        }

        [id^="viewModal"] .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        [id^="viewModal"] .badge.bg-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }

        [id^="viewModal"] .badge.bg-secondary {
            background-color: #6c757d !important;
            color: white !important;
        }

        [id^="viewModal"] .badge strong {
            color: white !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            [id^="viewModal"] .modal-dialog {
                margin: 0.5rem;
            }

            [id^="viewModal"] .modal-body {
                padding: 1.5rem 1rem;
            }

            [id^="viewModal"] .row.g-4>div {
                margin-bottom: 1rem;
            }

            [id^="viewModal"] .card-header {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }

            [id^="viewModal"] .card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            [id^="viewModal"] .modal-header .modal-title {
                font-size: 1.05rem;
            }

            [id^="viewModal"] .modal-body {
                padding: 1rem;
            }

            [id^="viewModal"] .card-body span {
                font-size: 0.9rem;
            }
        }

        /* Seedling Update Modal */
        #updateModal .modal-content {
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }

        #updateModal .modal-header {
            border-radius: 12px 12px 0 0;
            border: none;
            padding: 1.5rem;
        }

        #updateModal .modal-header .modal-title {
            display: block;
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

        /* Request Info Card in Update Modal */
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

        #updateModal .card-title,
        #updateModal .card-header h6 {
            color: #007bff;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        /* Buttons */
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
            #updateModal .modal-dialog {
                margin: 0.5rem;
            }

            #updateModal .modal-body {
                padding: 1rem;
            }

            #updateModal .modal-header,
            #updateModal .modal-footer {
                padding: 1rem;
            }

            #updateModal .modal-footer .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
            }
        }

        /* Document preview styling */
        .document-thumbnail {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .document-thumbnail:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px);
        }

        .document-preview-item {
            transition: all 0.2s ease;
        }

        .document-preview-item:hover {
            transform: scale(1.02);
        }

        /* Delete Modal Styling  */
        #deleteSeedlingModal .modal-header {
            border-bottom: 1px solid #f8d7da;
            padding: 1.25rem 1.5rem;
        }

        #deleteSeedlingModal .modal-body {
            padding: 1.5rem;
            background-color: #fff;
        }

        #deleteSeedlingModal .alert {
            border: 1px solid #f5c6cb;
            margin-bottom: 1rem;
        }

        #deleteSeedlingModal .alert strong {
            font-weight: 600;
        }

        #deleteSeedlingModal ul {
            list-style-position: inside;
            color: #721c24;
        }

        #deleteSeedlingModal ul li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        #deleteSeedlingModal .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
        }

        #deleteSeedlingModal .btn-danger {
            transition: all 0.2s ease;
        }

        #deleteSeedlingModal .btn-danger:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        #deleteSeedlingModal .btn-secondary:hover {
            transform: translateY(-1px);
        }

        #deleteSeedlingModal .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }

        /* Modal backdrop consistency */
        #deleteSeedlingModal .modal-backdrop {
            opacity: 0.5;
        }
        /* Pickup Date Column Styling */
.pickup-date-cell {
    padding: 0.5rem 0;
}

.pickup-date-cell small {
    line-height: 1.4;
}

.pickup-date-cell .badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    display: inline-block;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .pickup-date-cell {
        font-size: 0.85rem;
    }
    
    .pickup-date-cell .badge-sm {
        font-size: 0.65rem;
        padding: 0.2rem 0.4rem;
    }
}

/* Force white text on warning badges in modals */
[id^="viewModal"] .badge.bg-warning {
    color: white !important;
    background-color: #ffc107 !important;
}

[id^="viewModal"] .badge.bg-warning strong {
    color: white !important;
}
/* Fix modal z-index stacking */
.modal {
    z-index: 1060 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
}

/* Ensure modal is visible when shown */
.modal.show {
    display: flex !important;
    z-index: 1060 !important;
}

.modal.show .modal-dialog {
    z-index: 1060 !important;
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
                                <div class="modal-header bg-primary text-white position-relative">
                                    <h5 class="modal-title w-100 text-center" id="documentModalLabel">
                                        Supporting Document
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-0" id="documentViewer">
                                    <!-- Document will be loaded here -->
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i></i>Close
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
                    <i></i>Cancel
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

        // // Delete supply request with confirmation toast
        // function deleteSeedlingRequest(id, requestNumber) {
        //     showConfirmationToast(
        //         'Delete Supply Request',
        //         `Are you sure you want to delete request ${requestNumber}?\n\nThis action cannot be undone and will:\n• Delete all associated documents\n• Return approved supplies back to inventory`,
        //         () => proceedWithSeedlingDelete(id, requestNumber)
        //     );
        // }

        // // Proceed with supply request deletion
        // function proceedWithSeedlingDelete(id, requestNumber) {
        //     fetch(`/admin/seedlings/requests/${id}`, {
        //             method: 'DELETE',
        //             headers: {
        //                 'X-CSRF-TOKEN': getCSRFToken(),
        //                 'Accept': 'application/json',
        //                 'Content-Type': 'application/json'
        //             }
        //         })
        //         .then(response => {
        //             if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        //             return response.json();
        //         })
        //         .then(data => {
        //             if (data.success) {
        //                 showToast('success', data.message || 'Supply request deleted successfully');

        //                 const row = document.querySelector(`tr[data-request-id="${id}"]`);
        //                 if (row) {
        //                     row.style.transition = 'opacity 0.3s ease';
        //                     row.style.opacity = '0';
        //                     setTimeout(() => {
        //                         row.remove();

        //                         const tbody = document.querySelector('table tbody');
        //                         if (tbody && tbody.children.length === 0) {
        //                             setTimeout(() => window.location.reload(), 1500);
        //                         }
        //                     }, 300);
        //                 } else {
        //                     setTimeout(() => window.location.reload(), 1500);
        //                 }
        //             } else {
        //                 throw new Error(data.message || 'Failed to delete supply request');
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Error:', error);
        //             showToast('error', 'Failed to delete supply request: ' + error.message);
        //         });
        // }
// Delete
// Global delete tracking
let currentDeleteSeedlingId = null;

function deleteSeedlingRequest(id, requestNumber) {
    currentDeleteSeedlingId = id;
    
    // Update modal content
    const nameElement = document.getElementById('delete_seedling_name');
    if (nameElement) {
        nameElement.textContent = `Supply Request #${requestNumber}`;
    }
    
    // Get modal element
    const modalElement = document.getElementById('deleteSeedlingModal');
    if (!modalElement) {
        console.error('Delete modal not found');
        showToast('error', 'Modal not found');
        return;
    }
    
    // Create new modal instance
    const modal = new bootstrap.Modal(modalElement, {
        backdrop: 'static',
        keyboard: false
    });
    
    // Show modal
    modal.show();
    
    // Reset button state
    const deleteBtn = document.getElementById('confirm_delete_seedling_btn');
    if (deleteBtn) {
        deleteBtn.dataset.isDeleting = 'false';
        deleteBtn.disabled = false;
        const btnText = deleteBtn.querySelector('.btn-text');
        const btnLoader = deleteBtn.querySelector('.btn-loader');
        if (btnText) btnText.style.display = 'inline';
        if (btnLoader) btnLoader.style.display = 'none';
    }
}

function confirmPermanentDeleteSeedling() {
    if (!currentDeleteSeedlingId) {
        showToast('error', 'Request ID not found');
        return;
    }

    const deleteBtn = document.getElementById('confirm_delete_seedling_btn');
    const btnText = deleteBtn.querySelector('.btn-text');
    const btnLoader = deleteBtn.querySelector('.btn-loader');

    // Prevent double-click
    if (deleteBtn.dataset.isDeleting === 'true') return;
    deleteBtn.dataset.isDeleting = 'true';

    // Show loading state
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline';
    deleteBtn.disabled = true;

    // Make DELETE request
    fetch(`/admin/seedlings/requests/${currentDeleteSeedlingId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Hide modal
        const modalElement = document.getElementById('deleteSeedlingModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }

        // Clean up after modal closes
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        }, 300);

        // Show success message
        showToast('success', data.message || 'Request deleted successfully');
        
        // Reload page
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    })
    .catch(error => {
        console.error('Delete error:', error);
        showToast('error', error.message || 'Failed to delete request');
        
        // Reset button
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
        deleteBtn.disabled = false;
        deleteBtn.dataset.isDeleting = 'false';
    });
}

// Cleanup on modal hide
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteSeedlingModal');
    if (deleteModal) {
        deleteModal.addEventListener('hidden.bs.modal', function() {
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }, 100);
        });
    }
});
        // Initialize the update modal with original values
        function initializeSeedlingUpdateModal(requestId) {
            const form = document.getElementById('updateForm' + requestId);
            const remarksTextarea = document.getElementById('remarks' + requestId);
            const statusSelects = form.querySelectorAll('select[name^="item_statuses"]');
            const submitButton = document.getElementById('submitBtn' + requestId);

            // FIXED: Store original remarks value properly
            if (remarksTextarea) {
                // Get the original remarks value from the textarea (it's pre-populated from server)
                const originalRemarks = remarksTextarea.value;

                // Store both the original value and the form reference
                form.dataset.originalRemarks = JSON.stringify(originalRemarks);
                remarksTextarea.dataset.originalValue = originalRemarks;

                console.log('Modal initialized - Original remarks:', originalRemarks);
                console.log('Stored in data attribute:', form.dataset.originalRemarks);

                // Initialize character counter
                updateSeedlingRemarksCounter(requestId);
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
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Update Items';
                submitButton.disabled = false;
                submitButton.dataset.hasChanges = 'false';
            }
            initializePickupDateEditModal(requestId);
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

            // FIXED: Get original remarks from the stored data attribute
            let originalRemarks = '';
            try {
                // First try to get from form dataset
                originalRemarks = JSON.parse(form.dataset.originalRemarks || '""');
            } catch (e) {
                // Fallback to textarea dataset or empty string
                originalRemarks = remarksTextarea?.dataset.originalValue || '';
            }

            console.log('Current check:', {
                stored: form.dataset.originalRemarks,
                parsed: originalRemarks,
                current: remarksTextarea?.value || '',
                match: (remarksTextarea?.value || '') === originalRemarks
            });

            // Check remarks for changes - FIXED COMPARISON
            if (remarksTextarea) {
                const currentRemarks = remarksTextarea.value || '';

                // Use strict comparison after trimming
                const remarksChanged = currentRemarks !== originalRemarks;

                console.log('Remarks comparison:', {
                    original: `"${originalRemarks}"`,
                    current: `"${currentRemarks}"`,
                    originalTrimmed: `"${originalRemarks.trim()}"`,
                    currentTrimmed: `"${currentRemarks.trim()}"`,
                    changed: remarksChanged
                });

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

            console.log('Total changes detected:', hasChanges);

            // Update button state based on changes - ALWAYS KEEP ENABLED
            if (submitButton) {
                if (hasChanges) {
                    submitButton.classList.remove('no-changes');
                    submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Update Items';
                    submitButton.dataset.hasChanges = 'true';
                } else {
                    submitButton.classList.add('no-changes');
                    submitButton.innerHTML = '<i class="fas fa-check me-2"></i>No Changes';
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
                            throw new Error('Validation failed: ' + JSON.stringify(data.errors || data
                                .message));
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

                // Initialize modal when opened
                modalElement.addEventListener('show.bs.modal', function() {
                    console.log('Modal opened for request:', requestId);
                    initializeSeedlingUpdateModal(requestId);
                });
            });

            // Add event listeners for real-time change detection
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
            setTimeout(() => {
                initializePickupDateAddModal();
            }, 100);
        }

        // // Populate barangays dropdown
        // function populateSeedlingBarangays() {
        //     const barangaySelect = document.getElementById('seedling_barangay');

        //     // Get barangays from the page's data (if available)
        //     const barangayElements = document.querySelectorAll('option[value*=""]');

        //     // If barangays are already populated, return
        //     if (barangaySelect.querySelectorAll('option').length > 1) {
        //         return;
        //     }

        //     // Fetch barangays from server or use hardcoded list
        //     const barangays = @json($barangays ?? collect());

        //     if (barangays && barangays.length > 0) {
        //         barangays.forEach(barangay => {
        //             const option = document.createElement('option');
        //             option.value = barangay;
        //             option.textContent = barangay;
        //             barangaySelect.appendChild(option);
        //         });
        //     }
        // }

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

            // const phoneRegex = /^(\+639|09)\d{9}$/;

            // if (!phoneRegex.test(contactNumber.trim())) {
            //     input.classList.add('is-invalid');
            //     const errorDiv = document.createElement('div');
            //     errorDiv.className = 'invalid-feedback d-block';
            //     errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)';
            //     input.parentNode.appendChild(errorDiv);
            //     return false;
            // }

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
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword'];

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

    if (!allowedTypes.includes(file.type)) {
        showToast('error', 'File type not supported');
        input.value = '';
        return;
    }

    const reader = new FileReader();

    reader.onload = function(e) {
        if (preview) {
            const iconClass = file.type.startsWith('image/') ? 'fa-file-image' : 'fa-file-pdf';
            const iconColor = file.type.startsWith('image/') ? '' : 'text-danger';
            
            preview.innerHTML = file.type.startsWith('image/') 
                ? `<div class="document-preview-item">
                    <img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <p style="margin-top: 8px; font-size: 12px; color: #666;">
                        <i class="fas fa-file-image me-1"></i>${file.name}
                    </p>
                </div>`
                : `<div class="document-preview-item">
                    <div class="text-center p-3 border rounded">
                        <i class="fas ${iconClass} fa-3x ${iconColor} mb-2"></i>
                        <p style="margin-top: 8px; font-size: 12px; color: #666;">${file.name}</p>
                    </div>
                </div>`;
            
            preview.style.display = 'block';
        }
    };

    reader.onerror = function() {
        showToast('error', 'Failed to read file');
        input.value = '';
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

            // if (!itemsValid) {
            //     showToast('error', 'Please add at least one valid item');
            //     isValid = false;
            // }

            return isValid;
        }
function validateAddSeedlingPickupDate() {
    const pickupDateInput = document.getElementById('seedling_pickup_date_add');
    
    if (!pickupDateInput.value || pickupDateInput.value.trim() === '') {
        showToast('error', 'Pickup date is required');
        pickupDateInput.classList.add('is-invalid');
        return false;
    }
    
    pickupDateInput.classList.remove('is-invalid');
    return true;
}
    // Submit add seedling form
function submitAddSeedling() {
    // VALIDATE PICKUP DATE FIRST
    const pickupDateInput = document.getElementById('seedling_pickup_date_add');
    if (!pickupDateInput.value || pickupDateInput.value.trim() === '') {
        showToast('error', 'Pickup date is required');
        pickupDateInput.classList.add('is-invalid');
        return false;
    }
    pickupDateInput.classList.remove('is-invalid');

    // VALIDATE OTHER FIELDS
    if (!validateSeedlingForm()) {
        showToast('error', 'Please fix all validation errors before submitting');
        return;
    }

    // Remove red border from pickup date
    pickupDateInput.classList.remove('is-invalid');

    // Prepare form data
    const formData = new FormData();

    formData.append('first_name', document.getElementById('seedling_first_name').value.trim());
    formData.append('middle_name', document.getElementById('seedling_middle_name').value.trim());
    formData.append('last_name', document.getElementById('seedling_last_name').value.trim());
    formData.append('extension_name', document.getElementById('seedling_extension').value);
    formData.append('contact_number', document.getElementById('seedling_contact_number').value.trim());
    formData.append('barangay', document.getElementById('seedling_barangay').value);
    formData.append('status', document.getElementById('seedling_status').value);
    formData.append('remarks', document.getElementById('seedling_remarks').value.trim());
    formData.append('pickup_date', pickupDateInput.value);

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

                showToast('success', data.message || 'Supply request created successfully');

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
                showToast('error', data.message || 'Failed to create supply request');
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

            // Get request data from the page
            const requestElement = document.querySelector(`tr[data-request-id="${requestId}"]`);
            const hasDocument = requestElement ? requestElement.querySelector('.seedling-mini-doc') !== null : false;
            const documentPath = requestElement ? requestElement.getAttribute('data-document-path') : null;

            // Store original values for change detection
            const originalData = {};

            // Store personal info
            originalData.first_name = document.getElementById('edit_first_name_' + requestId).value;
            originalData.middle_name = document.getElementById('edit_middle_name_' + requestId).value;
            originalData.last_name = document.getElementById('edit_last_name_' + requestId).value;
            originalData.extension_name = document.getElementById('edit_extension_' + requestId).value;
            originalData.contact_number = document.getElementById('edit_contact_number_' + requestId).value;
            originalData.barangay = document.getElementById('edit_barangay_' + requestId).value;
            // ✅ STORE ORIGINAL PICKUP DATE
            const pickupDateInput = document.getElementById(`edit_seedling_pickup_date_${requestId}`);
            const originalPickupDate = pickupDateInput ? pickupDateInput.value : '';
            // Store in form data attribute
            form.dataset.originalData = JSON.stringify(originalData);
            form.dataset.originalPickupDate = originalPickupDate;  
            form.dataset.hasOriginalDocument = hasDocument ? 'true' : 'false';
            form.dataset.originalDocumentPath = documentPath || '';

            // Display current document if exists
            if (documentPath) {
                const currentDocContainer = document.getElementById(`edit_seedling_current_document_${requestId}`);
                const currentDocPreview = document.getElementById(`edit_seedling_current_doc_preview_${requestId}`);

                if (currentDocContainer && currentDocPreview) {
                    currentDocContainer.style.display = 'block';
                    displayEditSeedlingExistingDocument(documentPath, `edit_seedling_current_doc_preview_${requestId}`);
                }
            }

            // Clear validation states
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            // Reset submit button - NO CHANGES STATE (similar to update modal)
            const submitBtn = document.getElementById('editSubmitBtn' + requestId);
            if (submitBtn) {
                submitBtn.innerHTML = 'Save Changes';
                submitBtn.disabled = false;
                submitBtn.dataset.hasChanges = 'false';
            }
            initializeEditModalValidation(requestId);
        }


        // Check for changes in edit form
       function checkForEditChanges(requestId) {
    const form = document.getElementById('editForm' + requestId);
    const submitBtn = document.getElementById('editSubmitBtn' + requestId);

    if (!form || !submitBtn) return;

    const originalData = JSON.parse(form.dataset.originalData || '{}');
    let hasChanges = false;
    let changedFields = [];

    // Check all form fields
    const fields = [
        'first_name', 'middle_name', 'last_name', 'extension_name',
        'contact_number', 'barangay'
    ];

    fields.forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input && input.value !== originalData[field]) {
            hasChanges = true;
            changedFields.push(field);
            input.classList.add('form-changed');
        } else if (input) {
            input.classList.remove('form-changed');
        }
    });

    // ✅ ADD PICKUP DATE CHANGE DETECTION
    const pickupDateInput = document.getElementById(`edit_seedling_pickup_date_${requestId}`);
    if (pickupDateInput) {
        const originalPickupDate = form.dataset.originalPickupDate || '';
        const currentPickupDate = pickupDateInput.value || '';
        
        if (currentPickupDate !== originalPickupDate) {
            hasChanges = true;
            changedFields.push('pickup_date');
            pickupDateInput.classList.add('form-changed');
        } else {
            pickupDateInput.classList.remove('form-changed');
        }
    }

    // Check file input - ONLY if a NEW file was selected
    const fileInput = document.getElementById(`edit_seedling_supporting_document_${requestId}`);
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
        hasChanges = true;
        changedFields.push('supporting_document');
        console.log('New file selected:', fileInput.files[0].name);
    }

    // Update button state
    if (hasChanges) {
        submitBtn.classList.remove('no-changes');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
        submitBtn.disabled = false;
        submitBtn.dataset.hasChanges = 'true';
        submitBtn.dataset.changedFields = JSON.stringify(changedFields);
    } else {
        submitBtn.classList.remove('no-changes');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
        submitBtn.disabled = false;
        submitBtn.dataset.hasChanges = 'false';
    }
}

        // Handle edit form submission with changes summary
        function handleEditSeedlingSubmit(requestId) {
            const form = document.getElementById('editForm' + requestId);
            const submitBtn = document.getElementById('editSubmitBtn' + requestId);
            const modal = document.getElementById('editSeedlingModal' + requestId); 
         

            // CHECK FOR VALIDATION ERRORS FIRST
            const invalidFields = modal.querySelectorAll('.is-invalid');
            const visibleWarnings = Array.from(modal.querySelectorAll('[id$="-warning"]'))
                .filter(warning => warning.style.display !== 'none');

            if (invalidFields.length > 0 || visibleWarnings.length > 0) {
                showToast('error', 'Please fix all validation errors before submitting');
                return false;
            }

            if (!form) {
                console.error('Form not found:', 'editForm' + requestId);
                showToast('error', 'Form not found. Please try again.');
                return;
            }

            const hasChanges = submitBtn?.dataset.hasChanges === 'true';

            // If no changes, show warning and return
            if (!hasChanges) {
                showToast('warning', 'No changes detected. Please modify the fields before updating.');
                return;
            }

            // Build changes summary ONLY from actually changed fields
            const changedFieldsData = submitBtn?.dataset.changedFields ?
                JSON.parse(submitBtn.dataset.changedFields) : [];

            const fieldLabels = {
                'first_name': 'First Name',
                'middle_name': 'Middle Name',
                'last_name': 'Last Name',
                'extension_name': 'Extension',
                'contact_number': 'Contact Number',
                'barangay': 'Barangay',
                'supporting_document': 'Supporting Document'
            };

            const changedFields = changedFieldsData.map(field => fieldLabels[field] || field);

            // Show confirmation with only changed fields
            const changesText = changedFields.length > 0 ?
                `Update this request with the following changes?\n\n• ${changedFields.join('\n• ')}` :
                'Update this request?';

            showConfirmationToast(
                'Confirm Update',
                changesText,
                () => proceedWithEditSeedling(form, requestId)
            );
        }



        // Validate edit form
        function validateEditSeedlingForm(requestId) {
            const form = document.getElementById('editForm' + requestId);
            let isValid = true;

            const requiredFields = [{
                    id: 'edit_first_name_' + requestId,
                    label: 'First Name'
                },
                {
                    id: 'edit_last_name_' + requestId,
                    label: 'Last Name'
                },
                {
                    id: 'edit_contact_number_' + requestId,
                    label: 'Contact Number'
                },
                {
                    id: 'edit_barangay_' + requestId,
                    label: 'Barangay'
                }
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
                errorDiv.textContent = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
                input.parentNode.appendChild(errorDiv);
                return false;
            }

            if (input.value.trim()) {
                input.classList.add('is-valid');
            }
            return true;
        }

        // Preview edit seedling document
        function previewEditSeedlingDocument(inputId, previewId) {
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

            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                showToast('error', 'File size must not exceed 10MB');
                input.value = '';
                if (preview) {
                    preview.innerHTML = '';
                    preview.style.display = 'none';
                }
                return;
            }

            // Validate file type
            const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                showToast('error', 'Only PDF, JPG, and PNG files are allowed');
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
                    } else if (file.type === 'application/pdf') {
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

        // Proceed with edit seedling submission with document support
        function proceedWithEditSeedling(form, requestId) {
            const submitBtn = document.getElementById('editSubmitBtn' + requestId);

            if (!form) {
                showToast('error', 'Form not found');
                return;
            }

            // Validate form first
            if (!validateEditSeedlingForm(requestId)) {
                showToast('error', 'Please fix all validation errors');
                return;
            }

            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
            submitBtn.disabled = true;

            // Get form action
            const actionUrl = form.getAttribute('action');
            if (!actionUrl) {
                showToast('error', 'Form action URL not found');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }

            // Prepare FormData to handle file upload
            const formData = new FormData(form);

            // Add document file if selected
            const docInput = document.getElementById(`edit_seedling_supporting_document_${requestId}`);
            if (docInput && docInput.files && docInput.files[0]) {
                // Remove old document field if exists
                formData.delete('document');
                // Add new document
                formData.append('document', docInput.files[0]);
            }

            // Ensure _method is set for PUT/PATCH
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }

            // Fetch request
            fetch(actionUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw {
                                status: response.status,
                                data: data
                            };
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Update response:', data);

                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById('editSeedlingModal' + requestId)
                        );
                        if (modal) modal.hide();

                        showToast('success', data.message || 'Updated successfully!');

                        // Reload page
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast('error', data.message || 'Update failed');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);

                    let errorMsg = 'Error updating request';

                    if (error.data?.message) {
                        errorMsg = error.data.message;
                    } else if (error.data?.errors) {
                        errorMsg = Object.values(error.data.errors).flat().join(', ');
                    } else if (error.message) {
                        errorMsg = error.message;
                    }

                    showToast('error', errorMsg);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Clear document upload in edit modal
        function clearEditSeedlingDocument(requestId) {
            const docInput = document.getElementById(`edit_seedling_supporting_document_${requestId}`);
            const preview = document.getElementById(`edit_seedling_doc_preview_${requestId}`);

            if (docInput) {
                docInput.value = '';
            }

            if (preview) {
                preview.innerHTML = '';
                preview.style.display = 'none';
            }

            showToast('info', 'Document selection cleared');
        }
        // Display existing document in edit modal
        function displayEditSeedlingExistingDocument(documentPath, previewElementId) {
            const preview = document.getElementById(previewElementId);
            if (!preview) {
                console.error('Preview element not found:', previewElementId);
                return;
            }

            const fileExtension = documentPath.split('.').pop().toLowerCase();
            const fileName = documentPath.split('/').pop();
            const fileUrl = `/storage/${documentPath}`;

            // Image types
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(fileExtension)) {
                preview.innerHTML = `
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="document-thumbnail" style="width: 120px; height: 160px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                <img src="${fileUrl}" alt="Current document"
                                    style="max-width: 100%; max-height: 100%; object-fit: cover; cursor: pointer;"
                                    onclick="viewDocument('${documentPath}', '${fileName}')"
                                    title="Click to view full document">
                            </div>
                        </div>
                    </div>
                `;
            }
            // PDF type
            else if (fileExtension === 'pdf') {
                preview.innerHTML = `
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="document-thumbnail" style="width: 120px; height: 160px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; background: #fff3cd; border: 2px solid #ffc107;">
                                <div class="text-center">
                                    <i class="fas fa-file-pdf fa-3x mb-2" style="color: #dc3545;"></i>
                                    <small style="display: block; color: #666; font-size: 10px;">PDF</small>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column h-100 justify-content-start">
                                <div class="mb-2">
                                    <small class="d-block text-success fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>Document Uploaded
                                    </small>
                                    <small class="d-block text-muted mt-1">${fileName}</small>
                                </div>
                                <div class="mt-auto">
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="viewDocument('${documentPath}', '${fileName}')"
                                        title="View PDF">
                                        <i class="fas fa-eye me-1"></i>View
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="downloadSeedlingDocument('${fileUrl}', '${fileName}')"
                                        title="Download PDF">
                                        <i class="fas fa-download me-1"></i>Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            // Other document types
            else {
                preview.innerHTML = `
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="document-thumbnail" style="width: 120px; height: 160px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; background: #e2e3e5; border: 2px solid #6c757d;">
                                <div class="text-center">
                                    <i class="fas fa-file fa-3x mb-2" style="color: #6c757d;"></i>
                                    <small style="display: block; color: #666; font-size: 10px;">${fileExtension.toUpperCase()}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-column h-100 justify-content-start">
                                <div class="mb-2">
                                    <small class="d-block text-success fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>Document Uploaded
                                    </small>
                                    <small class="d-block text-muted mt-1">${fileName}</small>
                                </div>
                                <div class="mt-auto">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="downloadSeedlingDocument('${fileUrl}', '${fileName}')"
                                        title="Download document">
                                        <i class="fas fa-download me-1"></i>Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        // Download document helper
        function downloadSeedlingDocument(fileUrl, fileName) {
            const link = document.createElement('a');
            link.href = fileUrl;
            link.download = fileName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Preview new document upload
        function previewEditSeedlingDocument(inputId, previewId) {
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

            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                showToast('error', 'File size must not exceed 10MB');
                input.value = '';
                if (preview) {
                    preview.innerHTML = '';
                    preview.style.display = 'none';
                }
                return;
            }

            // Validate file type
            const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                showToast('error', 'Only PDF, JPG, and PNG files are allowed');
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
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    if (file.type.startsWith('image/')) {
                        preview.innerHTML = `
                            <div style="margin-top: 1rem;">
                                <div class="alert alert-info mb-2" style="padding: 0.5rem; font-size: 0.85rem;">
                                    <i class="fas fa-info-circle me-1"></i>New image selected
                                </div>
                                <div class="document-preview-item">
                                    <img src="${e.target.result}" alt="Preview"
                                        style="max-width: 100%; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 2px solid #007bff;">
                                    <p style="margin-top: 8px; font-size: 12px; color: #666;">
                                        <i class="fas fa-file-image me-1"></i>${file.name}
                                        <span class="text-muted">(${(file.size / 1024).toFixed(2)} KB)</span>
                                    </p>
                                </div>
                            </div>
                        `;
                    } else if (file.type === 'application/pdf') {
                        preview.innerHTML = `
                            <div style="margin-top: 1rem;">
                                <div class="alert alert-info mb-2" style="padding: 0.5rem; font-size: 0.85rem;">
                                    <i class="fas fa-info-circle me-1"></i>New PDF selected
                                </div>
                                <div class="document-preview-item">
                                    <div class="text-center p-3 border rounded" style="border-color: #dc3545; background: #fff3cd;">
                                        <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                        <p style="margin-top: 8px; font-size: 12px; color: #666;">
                                            ${file.name}
                                            <span class="text-muted d-block">(${(file.size / 1024).toFixed(2)} KB)</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    preview.style.display = 'block';
                }
            };

            reader.readAsDataURL(file);
        }

        // Clear document upload
        function clearEditSeedlingDocument(requestId) {
            const docInput = document.getElementById(`edit_seedling_supporting_document_${requestId}`);
            const preview = document.getElementById(`edit_seedling_doc_preview_${requestId}`);
            const currentDocContainer = document.getElementById(`edit_seedling_current_document_${requestId}`);

            if (docInput) {
                docInput.value = '';
            }

            if (preview) {
                preview.innerHTML = '';
                preview.style.display = 'none';
            }

            // Show current document again if exists
            const row = document.querySelector(`tr[data-request-id="${requestId}"]`);
            if (row) {
                const documentPath = row.getAttribute('data-document-path');
                if (documentPath && currentDocContainer) {
                    currentDocContainer.style.display = 'block';
                    displayEditSeedlingExistingDocument(documentPath, `edit_seedling_current_doc_preview_${requestId}`);
                }
            }

            showToast('info', 'Document selection cleared');
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
                    firstName.addEventListener('blur', function() {
                        capitalizeEditName(this);
                    });
                }

                const middleName = form.querySelector(`#edit_middle_name_${requestId}`);
                if (middleName) {
                    middleName.addEventListener('blur', function() {
                        capitalizeEditName(this);
                    });
                }

                const lastName = form.querySelector(`#edit_last_name_${requestId}`);
                if (lastName) {
                    lastName.addEventListener('blur', function() {
                        capitalizeEditName(this);
                    });
                }
            });
        }

        // Update remarks character counter for seedlings
        function updateSeedlingRemarksCounter(requestId) {
            const textarea = document.getElementById('remarks' + requestId);
            const charCount = document.getElementById('charCount' + requestId);

            if (textarea && charCount) {
                charCount.textContent = textarea.value.length;

                // Change color based on length
                if (textarea.value.length > 900) {
                    charCount.parentElement.classList.add('text-warning');
                    charCount.parentElement.classList.remove('text-muted');
                } else {
                    charCount.parentElement.classList.remove('text-warning');
                    charCount.parentElement.classList.add('text-muted');
                }
            }
        }

 

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeEditFormListeners();
        });

        // =============================================
// REAL-TIME VALIDATION FOR ADD SEEDLING MODAL
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    // Real-time validation for ADD modal name fields
    const addNameFields = [
        {
            id: 'seedling_first_name',
            pattern: /^[a-zA-Z\s\'-]*$/,
            message: 'Only letters, spaces, hyphens, and apostrophes are allowed'
        },
        {
            id: 'seedling_middle_name',
            pattern: /^[a-zA-Z\s\'-]*$/,
            message: 'Only letters, spaces, hyphens, and apostrophes are allowed'
        },
        {
            id: 'seedling_last_name',
            pattern: /^[a-zA-Z\s\'-]*$/,
            message: 'Only letters, spaces, hyphens, and apostrophes are allowed'
        }
    ];

    // Setup ADD modal validations
    addNameFields.forEach(field => {
        const input = document.getElementById(field.id);
        
        if (input) {
            // Create warning message element if it doesn't exist
            let warning = document.getElementById(field.id + '-warning');
            if (!warning) {
                warning = document.createElement('span');
                warning.id = field.id + '-warning';
                warning.className = 'validation-warning';
                warning.style.cssText = 'color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px; margin-left: 0;';
                warning.textContent = field.message;
                input.parentNode.appendChild(warning);
            }

            // Real-time input validation
            input.addEventListener('input', function(e) {
                const value = e.target.value;

                if (!field.pattern.test(value)) {
                    warning.style.display = 'block';
                    input.style.borderColor = '#ff6b6b';
                    input.classList.add('is-invalid');
                } else {
                    warning.style.display = 'none';
                    input.style.borderColor = '';
                    input.classList.remove('is-invalid');
                }
            });

            // Validation on blur
            input.addEventListener('blur', function(e) {
                const value = e.target.value;

                if (!field.pattern.test(value) && value !== '') {
                    warning.style.display = 'block';
                    input.style.borderColor = '#ff6b6b';
                    input.classList.add('is-invalid');
                } else {
                    warning.style.display = 'none';
                    input.style.borderColor = '';
                    input.classList.remove('is-invalid');
                }
            });
        }
    });

    // Real-time validation for ADD modal contact number
    const addContactInput = document.getElementById('seedling_contact_number');
    if (addContactInput) {
        let contactWarning = document.getElementById('seedling_contact_number-warning');
        if (!contactWarning) {
            contactWarning = document.createElement('span');
            contactWarning.id = 'seedling_contact_number-warning';
            contactWarning.className = 'validation-warning';
            contactWarning.style.cssText = 'color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px; margin-left: 0;';
            contactWarning.textContent = 'Contact number must be in format 09XXXXXXXXX (11 digits)';
            addContactInput.parentNode.appendChild(contactWarning);
        }

        addContactInput.addEventListener('input', function(e) {
            const value = e.target.value;
            const phonePattern = /^09\d{9}$/;

            if (value !== '' && !phonePattern.test(value)) {
                contactWarning.style.display = 'block';
                addContactInput.style.borderColor = '#ff6b6b';
                addContactInput.classList.add('is-invalid');
            } else {
                contactWarning.style.display = 'none';
                addContactInput.style.borderColor = '';
                addContactInput.classList.remove('is-invalid');
            }
        });

        // addContactInput.addEventListener('blur', function(e) {
        //     const value = e.target.value;
        //     const phonePattern = /^09\d{9}$/;

        //     if (value !== '' && !phonePattern.test(value)) {
        //         contactWarning.style.display = 'block';
        //         addContactInput.style.borderColor = '#ff6b6b';
        //         addContactInput.classList.add('is-invalid');
        //     }
        // });
    }
});

// =============================================
// REAL-TIME VALIDATION FOR EDIT MODALS
// =============================================

function initializeEditModalValidation(requestId) {
    // Real-time validation for EDIT modal name fields
    const editNameFields = [
        {
            id: 'edit_first_name_' + requestId,
            pattern: /^[a-zA-Z\s\'-]*$/,
            message: 'Only letters, spaces, hyphens, and apostrophes are allowed'
        },
        {
            id: 'edit_middle_name_' + requestId,
            pattern: /^[a-zA-Z\s\'-]*$/,
            message: 'Only letters, spaces, hyphens, and apostrophes are allowed'
        },
        {
            id: 'edit_last_name_' + requestId,
            pattern: /^[a-zA-Z\s\'-]*$/,
            message: 'Only letters, spaces, hyphens, and apostrophes are allowed'
        }
    ];

    editNameFields.forEach(field => {
        const input = document.getElementById(field.id);
        
        if (input) {
            // Create warning message element if it doesn't exist
            let warning = document.getElementById(field.id + '-warning');
            if (!warning) {
                warning = document.createElement('span');
                warning.id = field.id + '-warning';
                warning.className = 'validation-warning';
                warning.style.cssText = 'color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px; margin-left: 0;';
                warning.textContent = field.message;
                input.parentNode.appendChild(warning);
            }

            // Real-time input validation
            input.addEventListener('input', function(e) {
                const value = e.target.value;

                if (!field.pattern.test(value)) {
                    warning.style.display = 'block';
                    input.style.borderColor = '#ff6b6b';
                    input.classList.add('is-invalid');
                } else {
                    warning.style.display = 'none';
                    input.style.borderColor = '';
                    input.classList.remove('is-invalid');
                }
            });

            // Validation on blur
            input.addEventListener('blur', function(e) {
                const value = e.target.value;

                if (!field.pattern.test(value) && value !== '') {
                    warning.style.display = 'block';
                    input.style.borderColor = '#ff6b6b';
                    input.classList.add('is-invalid');
                } else {
                    warning.style.display = 'none';
                    input.style.borderColor = '';
                    input.classList.remove('is-invalid');
                }
            });
        }
    });

    // Real-time validation for EDIT modal contact number
    const editContactInput = document.getElementById('edit_contact_number_' + requestId);
    if (editContactInput) {
        let contactWarning = document.getElementById('edit_contact_number_' + requestId + '-warning');
        if (!contactWarning) {
            contactWarning = document.createElement('span');
            contactWarning.id = 'edit_contact_number_' + requestId + '-warning';
            contactWarning.className = 'validation-warning';
            contactWarning.style.cssText = 'color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px; margin-left: 0;';
            contactWarning.textContent = 'Contact number must be in format 09XXXXXXXXX (11 digits)';
            editContactInput.parentNode.appendChild(contactWarning);
        }

        editContactInput.addEventListener('input', function(e) {
            const value = e.target.value;
            const phonePattern = /^09\d{9}$/;

            if (value !== '' && !phonePattern.test(value)) {
                contactWarning.style.display = 'block';
                editContactInput.style.borderColor = '#ff6b6b';
                editContactInput.classList.add('is-invalid');
            } else {
                contactWarning.style.display = 'none';
                editContactInput.style.borderColor = '';
                editContactInput.classList.remove('is-invalid');
            }
        });

        // editContactInput.addEventListener('blur', function(e) {
        //     const value = e.target.value;
        //     const phonePattern = /^09\d{9}$/;

        //     if (value !== '' && !phonePattern.test(value)) {
        //         contactWarning.style.display = 'block';
        //         editContactInput.style.borderColor = '#ff6b6b';
        //         editContactInput.classList.add('is-invalid');
        //     }
        // });
    }
}
// =============================================
// PICKUP DATE FIELD - EDIT MODAL
// =============================================

function initializePickupDateEditModal(requestId) {
    const pickupInput = document.getElementById(`edit_seedling_pickup_date_${requestId}`);
    const displayDiv = document.getElementById(`edit_pickup_date_display_${requestId}`);
    const displayText = document.getElementById(`edit_pickup_date_text_${requestId}`);

    if (!pickupInput) return;

    // Set min/max dates
    const today = new Date();
    const minDate = new Date(today);
    minDate.setDate(minDate.getDate() + 1);
    
    const maxDate = new Date(today);
    maxDate.setDate(maxDate.getDate() + 30);

    const formatDate = (date) => date.toISOString().split('T')[0];
    
    pickupInput.min = formatDate(minDate);
    pickupInput.max = formatDate(maxDate);

    // Validate on change
    pickupInput.addEventListener('change', function() {
        if (!this.value) {
            displayDiv.style.display = 'none';
            return;
        }

        const selectedDate = new Date(this.value + 'T00:00:00');
        const dayOfWeek = selectedDate.getDay();
        const dayName = selectedDate.toLocaleDateString('en-US', { weekday: 'long' });
        const fullDate = selectedDate.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });

        if (dayOfWeek === 0 || dayOfWeek === 6) {
            this.value = '';
            displayDiv.style.display = 'none';
            showToast('warning', `${dayName}s are closed. Please select a weekday (Monday-Friday).`);
            return;
        }

        displayText.innerHTML = `
            <i class="fas fa-check-circle" style="color: #40916c; margin-right: 8px;"></i>
            <strong>${fullDate}</strong> <span style="color: #666;">(${dayName})</span>
        `;
        displayDiv.style.display = 'block';
        checkForEditChanges(requestId);
    });

    pickupInput.addEventListener('blur', function() {
        if (!this.value) return;

        const selectedDate = new Date(this.value + 'T00:00:00');
        const dayOfWeek = selectedDate.getDay();

        if (dayOfWeek === 0 || dayOfWeek === 6) {
            this.value = '';
            displayDiv.style.display = 'none';
            showToast('warning', 'Weekends are not available for pickup. Please select a weekday.');
        }
    });
}

// =============================================
// PICKUP DATE FIELD - ADD MODAL
// =============================================

function initializePickupDateAddModal() {
    const pickupInput = document.getElementById('seedling_pickup_date_add');
    const displayDiv = document.getElementById('seedling_pickup_date_display_add');
    const displayText = document.getElementById('seedling_pickup_date_text_add');

    if (!pickupInput) return;

    const today = new Date();
    const minDate = new Date(today);
    minDate.setDate(minDate.getDate() + 1);
    
    const maxDate = new Date(today);
    maxDate.setDate(maxDate.getDate() + 30);

    const formatDate = (date) => date.toISOString().split('T')[0];
    
    pickupInput.min = formatDate(minDate);
    pickupInput.max = formatDate(maxDate);

    pickupInput.addEventListener('change', function() {
        if (!this.value) {
            displayDiv.style.display = 'none';
            return;
        }

        const selectedDate = new Date(this.value + 'T00:00:00');
        const dayOfWeek = selectedDate.getDay();
        const dayName = selectedDate.toLocaleDateString('en-US', { weekday: 'long' });
        const fullDate = selectedDate.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });

        if (dayOfWeek === 0 || dayOfWeek === 6) {
            this.value = '';
            displayDiv.style.display = 'none';
            showToast('warning', `${dayName}s are closed. Please select a weekday (Monday-Friday).`);
            return;
        }

        displayText.innerHTML = `
            <i class="fas fa-check-circle" style="color: #40916c; margin-right: 8px;"></i>
            <strong>${fullDate}</strong> <span style="color: #666;">(${dayName})</span>
        `;
        displayDiv.style.display = 'block';
    });

    pickupInput.addEventListener('blur', function() {
        if (!this.value) return;

        const selectedDate = new Date(this.value + 'T00:00:00');
        const dayOfWeek = selectedDate.getDay();

        if (dayOfWeek === 0 || dayOfWeek === 6) {
            this.value = '';
            displayDiv.style.display = 'none';
            showToast('warning', 'Weekends are not available for pickup. Please select a weekday.');
        }
    });
    pickupInput.addEventListener('blur', function() {
    if (!this.value) {
        this.classList.add('is-invalid');
        displayDiv.style.display = 'none';
    } else {
        this.classList.remove('is-invalid');
    }

    const selectedDate = new Date(this.value + 'T00:00:00');
    const dayOfWeek = selectedDate.getDay();

    if (dayOfWeek === 0 || dayOfWeek === 6) {
        this.value = '';
        this.classList.add('is-invalid');
        displayDiv.style.display = 'none';
        showToast('warning', 'Weekends are not available for pickup. Please select a weekday.');
    }
});
}
// Update remarks character counter for add modal
function updateAddSeedlingRemarksCounter() {
    const textarea = document.getElementById('seedling_remarks');
    const charCount = document.getElementById('addCharCount');

    if (textarea && charCount) {
        charCount.textContent = textarea.value.length;

        // Change color based on length
        if (textarea.value.length > 900) {
            document.getElementById('addRemarksCounter').classList.add('text-warning');
            document.getElementById('addRemarksCounter').classList.remove('text-muted');
        } else {
            document.getElementById('addRemarksCounter').classList.remove('text-warning');
            document.getElementById('addRemarksCounter').classList.add('text-muted');
        }
    }

}
// // Initialize pickup date field in edit modal (add to existing initializePickupDateEditModal function or create new)
// function initializePickupDateEditModal(requestId) {
//     const pickupInput = document.getElementById(`edit_seedling_pickup_date_${requestId}`);
//     const displayDiv = document.getElementById(`edit_pickup_date_display_${requestId}`);
//     const displayText = document.getElementById(`edit_pickup_date_text_${requestId}`);

//     if (!pickupInput) return;

//     // Set min/max dates
//     const today = new Date();
//     const minDate = new Date(today);
//     minDate.setDate(minDate.getDate() + 1);
    
//     const maxDate = new Date(today);
//     maxDate.setDate(maxDate.getDate() + 30);

//     const formatDate = (date) => date.toISOString().split('T')[0];
    
//     pickupInput.min = formatDate(minDate);
//     pickupInput.max = formatDate(maxDate);

//     // Validate on change
//     pickupInput.addEventListener('change', function() {
//         if (!this.value) {
//             displayDiv.style.display = 'none';
//             return;
//         }

//         const selectedDate = new Date(this.value + 'T00:00:00');
//         const dayOfWeek = selectedDate.getDay();
//         const dayName = selectedDate.toLocaleDateString('en-US', { weekday: 'long' });
//         const fullDate = selectedDate.toLocaleDateString('en-US', { 
//             year: 'numeric', 
//             month: 'long', 
//             day: 'numeric' 
//         });

//         if (dayOfWeek === 0 || dayOfWeek === 6) {
//             this.value = '';
//             displayDiv.style.display = 'none';
//             showToast('warning', `${dayName}s are closed. Please select a weekday (Monday-Friday).`);
//             return;
//         }

//         displayText.innerHTML = `
//             <i class="fas fa-check-circle" style="color: #40916c; margin-right: 8px;"></i>
//             <strong>${fullDate}</strong> <span style="color: #666;">(${dayName})</span>
//         `;
//         displayDiv.style.display = 'block';
//         checkForEditChanges(requestId);
//     });

//     pickupInput.addEventListener('blur', function() {
//         if (!this.value) return;

//         const selectedDate = new Date(this.value + 'T00:00:00');
//         const dayOfWeek = selectedDate.getDay();

//         if (dayOfWeek === 0 || dayOfWeek === 6) {
//             this.value = '';
//             displayDiv.style.display = 'none';
//             showToast('warning', 'Weekends are not available for pickup. Please select a weekday.');
//         }
//     });
// }
function markAsClaimed(requestId, requestNumber) {
    showConfirmationToast(
        'Mark as Claimed',
        `Mark request ${requestNumber} as claimed by the applicant?`,
        () => proceedMarkAsClaimed(requestId, requestNumber)
    );
}

function proceedMarkAsClaimed(requestId, requestNumber) {
    const csrfToken = getCSRFToken();

    fetch(`/admin/seedlings/requests/${requestId}/mark-claimed`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Failed to mark as claimed');
    });
}
    </script>
@endsection