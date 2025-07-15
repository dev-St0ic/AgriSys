@extends('layouts.app')

@section('title', 'View Inventory Item - AgriSys')
@section('page-title', 'Inventory Item Details')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $inventory->item_name }}</h6>
                    <div>
                        <a href="{{ route('admin.inventory.edit', $inventory) }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-edit"></i> Edit Item
                        </a>
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Item Name:</strong></div>
                                        <div class="col-sm-8">{{ $inventory->item_name }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Category:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge badge-info">{{ ucfirst($inventory->category) }}</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Variety:</strong></div>
                                        <div class="col-sm-8">{{ $inventory->variety ?? 'Not specified' }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Unit:</strong></div>
                                        <div class="col-sm-8">{{ ucfirst($inventory->unit) }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Status:</strong></div>
                                        <div class="col-sm-8">
                                            @if ($inventory->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($inventory->description)
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Description:</strong></div>
                                            <div class="col-sm-8">{{ $inventory->description }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Stock Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-boxes me-2"></i>Stock Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Current Stock:</strong></div>
                                        <div class="col-sm-7">
                                            <span
                                                class="h5
                                                @if ($inventory->isOutOfStock()) text-danger
                                                @elseif($inventory->isLowStock()) text-warning
                                                @else text-success @endif">
                                                {{ number_format($inventory->current_stock) }} {{ $inventory->unit }}
                                            </span>
                                            @if ($inventory->isLowStock() && !$inventory->isOutOfStock())
                                                <i class="fas fa-exclamation-triangle text-warning ms-1"
                                                    title="Low Stock"></i>
                                            @elseif($inventory->isOutOfStock())
                                                <i class="fas fa-times-circle text-danger ms-1" title="Out of Stock"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Minimum Stock:</strong></div>
                                        <div class="col-sm-7">{{ number_format($inventory->minimum_stock) }}
                                            {{ $inventory->unit }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Maximum Stock:</strong></div>
                                        <div class="col-sm-7">{{ number_format($inventory->maximum_stock) }}
                                            {{ $inventory->unit }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Stock Status:</strong></div>
                                        <div class="col-sm-7">
                                            @if ($inventory->isOutOfStock())
                                                <span class="badge badge-danger">Out of Stock</span>
                                            @elseif($inventory->isLowStock())
                                                <span class="badge badge-warning">Low Stock</span>
                                            @else
                                                <span class="badge badge-success">Available</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Last Restocked:</strong></div>
                                        <div class="col-sm-7">
                                            {{ $inventory->last_restocked ? $inventory->last_restocked->format('M d, Y') : 'Never' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Progress Bar -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Stock Level Visualization</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $percentage =
                                            $inventory->maximum_stock > 0
                                                ? ($inventory->current_stock / $inventory->maximum_stock) * 100
                                                : 0;
                                        $minPercentage =
                                            $inventory->maximum_stock > 0
                                                ? ($inventory->minimum_stock / $inventory->maximum_stock) * 100
                                                : 0;
                                    @endphp

                                    <div class="mb-2">
                                        <small class="text-muted">Stock Level: {{ number_format($percentage, 1) }}% of
                                            maximum capacity</small>
                                    </div>

                                    <div class="progress mb-3" style="height: 20px;">
                                        <div class="progress-bar
                                            @if ($inventory->isOutOfStock()) bg-danger
                                            @elseif($inventory->isLowStock()) bg-warning
                                            @else bg-success @endif"
                                            role="progressbar" style="width: {{ min($percentage, 100) }}%"
                                            aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($inventory->current_stock) }} {{ $inventory->unit }}
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>Minimum: {{ number_format($inventory->minimum_stock) }}</span>
                                        <span>Current: {{ number_format($inventory->current_stock) }}</span>
                                        <span>Maximum: {{ number_format($inventory->maximum_stock) }}</span>
                                    </div>

                                    <!-- Minimum stock indicator line -->
                                    <div class="position-relative mt-2">
                                        <div
                                            style="position: absolute; left: {{ $minPercentage }}%; width: 2px; height: 20px; background-color: #dc3545; top: -23px;">
                                        </div>
                                        <small class="text-danger"
                                            style="margin-left: {{ max(0, $minPercentage - 5) }}%;">↑ Min Level</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Management Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-user-cog me-2"></i>Management Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <div class="col-sm-4"><strong>Created By:</strong></div>
                                                <div class="col-sm-8">{{ $inventory->creator->name ?? 'System' }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4"><strong>Created At:</strong></div>
                                                <div class="col-sm-8">{{ $inventory->created_at->format('M d, Y h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <div class="col-sm-4"><strong>Last Updated By:</strong></div>
                                                <div class="col-sm-8">{{ $inventory->updater->name ?? 'System' }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4"><strong>Last Updated:</strong></div>
                                                <div class="col-sm-8">{{ $inventory->updated_at->format('M d, Y h:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#adjustStockModal">
                                            <i class="fas fa-plus-minus"></i> Adjust Stock
                                        </button>
                                        <a href="{{ route('admin.inventory.edit', $inventory) }}"
                                            class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit Details
                                        </a>
                                        @if ($inventory->is_active)
                                            <button type="button" class="btn btn-warning" onclick="toggleStatus(false)">
                                                <i class="fas fa-pause"></i> Deactivate
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-success" onclick="toggleStatus(true)">
                                                <i class="fas fa-play"></i> Activate
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div class="modal fade" id="adjustStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.inventory.adjust-stock', $inventory) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Adjust Stock - {{ $inventory->item_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Current Stock: {{ $inventory->current_stock }}
                                {{ $inventory->unit }}</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adjustment Type</label>
                            <select name="adjustment_type" class="form-control" required>
                                <option value="add">Add Stock</option>
                                <option value="subtract">Subtract Stock</option>
                                <option value="set">Set Exact Stock</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason (Optional)</label>
                            <input type="text" name="reason" class="form-control"
                                placeholder="Reason for adjustment">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Adjust Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card-header.bg-light {
            background-color: #f8f9fc !important;
            border-bottom: 1px solid #e3e6f0;
        }

        .progress {
            background-color: #f8f9fc;
        }

        .badge {
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('scripts')
    <script>
        function toggleStatus(activate) {
            const action = activate ? 'activate' : 'deactivate';
            const confirmMessage = `Are you sure you want to ${action} this inventory item?`;

            if (confirm(confirmMessage)) {
                // Create a form to submit the status change
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.inventory.update', $inventory) }}';

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add method spoofing for PUT
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);

                // Add current values to maintain them
                @foreach (['item_name', 'category', 'variety', 'description', 'current_stock', 'minimum_stock', 'maximum_stock', 'unit'] as $field)
                    const {{ $field }}Input = document.createElement('input');
                    {{ $field }}Input.type = 'hidden';
                    {{ $field }}Input.name = '{{ $field }}';
                    {{ $field }}Input.value = '{{ $inventory->$field }}';
                    form.appendChild({{ $field }}Input);
                @endforeach

                @if ($inventory->last_restocked)
                    const lastRestockedInput = document.createElement('input');
                    lastRestockedInput.type = 'hidden';
                    lastRestockedInput.name = 'last_restocked';
                    lastRestockedInput.value = '{{ $inventory->last_restocked->format('Y-m-d') }}';
                    form.appendChild(lastRestockedInput);
                @endif

                // Add the status input
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'is_active';
                statusInput.value = activate ? '1' : '0';
                form.appendChild(statusInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
