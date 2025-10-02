{{-- resources/views/admin/seedlings/create.blade.php --}}

@extends('layouts.app')

@section('title', 'Create Seedling Request - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-plus-circle text-primary me-2"></i>
        <span class="text-primary fw-bold">Create New Seedling Request</span>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-seedling me-2"></i>
                            New Request Form
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.seedlings.store') }}" enctype="multipart/form-data" id="createRequestForm">
                            @csrf

                            <!-- Personal Information Section -->
                            <div class="section-card mb-4">
                                <h6 class="section-title">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    Personal Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="first_name" class="form-label required">First Name</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                            id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                            id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                                        @error('middle_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label for="last_name" class="form-label required">Last Name</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                            id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label for="extension_name" class="form-label">Extension Name</label>
                                        <select class="form-select @error('extension_name') is-invalid @enderror" 
                                            id="extension_name" name="extension_name">
                                            <option value="">None</option>
                                            <option value="Jr." {{ old('extension_name') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                            <option value="Sr." {{ old('extension_name') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                            <option value="II" {{ old('extension_name') == 'II' ? 'selected' : '' }}>II</option>
                                            <option value="III" {{ old('extension_name') == 'III' ? 'selected' : '' }}>III</option>
                                            <option value="IV" {{ old('extension_name') == 'IV' ? 'selected' : '' }}>IV</option>
                                        </select>
                                        @error('extension_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <label for="contact_number" class="form-label required">Contact Number</label>
                                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                                            id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                                        @error('contact_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                            id="email" name="email" value="{{ old('email') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="barangay" class="form-label required">Barangay</label>
                                        <input type="text" class="form-control @error('barangay') is-invalid @enderror" 
                                            id="barangay" name="barangay" value="{{ old('barangay') }}" 
                                            list="barangayList" required>
                                        <datalist id="barangayList">
                                            @foreach($barangays as $barangay)
                                                <option value="{{ $barangay }}">
                                            @endforeach
                                        </datalist>
                                        @error('barangay')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-12">
                                        <label for="address" class="form-label required">Complete Address</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                            id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Request Details Section -->
                            <div class="section-card mb-4">
                                <h6 class="section-title">
                                    <i class="fas fa-clipboard-list text-primary me-2"></i>
                                    Request Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="planting_location" class="form-label">Planting Location</label>
                                        <textarea class="form-control @error('planting_location') is-invalid @enderror" 
                                            id="planting_location" name="planting_location" rows="2">{{ old('planting_location') }}</textarea>
                                        @error('planting_location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="purpose" class="form-label">Purpose</label>
                                        <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                            id="purpose" name="purpose" rows="2">{{ old('purpose') }}</textarea>
                                        @error('purpose')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label for="preferred_delivery_date" class="form-label">Preferred Delivery Date</label>
                                        <input type="date" class="form-control @error('preferred_delivery_date') is-invalid @enderror" 
                                            id="preferred_delivery_date" name="preferred_delivery_date" 
                                            value="{{ old('preferred_delivery_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                        @error('preferred_delivery_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="document" class="form-label">Supporting Document (Optional)</label>
                                        <input type="file" class="form-control @error('document') is-invalid @enderror" 
                                            id="document" name="document" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">Accepted: PDF, JPG, PNG (Max: 5MB)</small>
                                        @error('document')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Items Selection Section -->
                            <div class="section-card mb-4">
                                <h6 class="section-title">
                                    <i class="fas fa-shopping-cart text-primary me-2"></i>
                                    Select Items <span class="text-danger">*</span>
                                </h6>
                                
                                <div id="itemsContainer">
                                    @foreach($categories as $category)
                                        <div class="category-section mb-4">
                                            <div class="category-header d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="mb-0">
                                                    <i class="fas {{ $category->icon }} text-primary me-2"></i>
                                                    {{ $category->display_name }}
                                                </h6>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="addItemRow('{{ $category->id }}', '{{ $category->display_name }}')">
                                                    <i class="fas fa-plus me-1"></i>Add Item
                                                </button>
                                            </div>
                                            <div id="category-{{ $category->id }}-items" class="items-list">
                                                <!-- Dynamic items will be added here -->
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('items')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('admin.seedlings.requests') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Create Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .required::after {
            content: " *";
            color: red;
        }

        .section-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e9ecef;
        }

        .section-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }

        .category-section {
            background: white;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #dee2e6;
        }

        .category-header {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }

        .item-row {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.2s;
        }

        .item-row:hover {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .items-list:empty::after {
            content: "No items added yet. Click 'Add Item' to get started.";
            display: block;
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
        }
    </style>

    <script>
        // Store category items data
        const categoryItems = @json($categories->map(function($cat) {
            return [
                'id' => $cat->id,
                'items' => $cat->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'unit' => $item->unit,
                        'min_quantity' => $item->min_quantity,
                        'max_quantity' => $item->max_quantity
                    ];
                })
            ];
        })->keyBy('id'));

        let itemCounter = 0;

        function addItemRow(categoryId, categoryName) {
            itemCounter++;
            const items = categoryItems[categoryId].items;
            
            const container = document.getElementById(`category-${categoryId}-items`);
            
            const itemRow = document.createElement('div');
            itemRow.className = 'item-row';
            itemRow.id = `item-row-${itemCounter}`;
            
            itemRow.innerHTML = `
                <div class="row g-3 align-items-end">
                    <div class="col-md-7">
                        <label class="form-label">Select Item</label>
                        <select name="items[${itemCounter}][category_item_id]" class="form-select" 
                            onchange="updateItemUnit(${itemCounter}, ${categoryId})" required>
                            <option value="">Choose an item...</option>
                            ${items.map(item => `
                                <option value="${item.id}" 
                                    data-unit="${item.unit}" 
                                    data-min="${item.min_quantity}" 
                                    data-max="${item.max_quantity || ''}">
                                    ${item.name}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quantity</label>
                        <div class="input-group">
                            <input type="number" name="items[${itemCounter}][quantity]" 
                                class="form-control" min="1" required 
                                id="quantity-${itemCounter}">
                            <span class="input-group-text" id="unit-${itemCounter}">pcs</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger w-100" 
                            onclick="removeItemRow(${itemCounter})">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(itemRow);
        }

        function updateItemUnit(rowId, categoryId) {
            const select = event.target;
            const selectedOption = select.options[select.selectedIndex];
            const unit = selectedOption.dataset.unit || 'pcs';
            const min = selectedOption.dataset.min || 1;
            const max = selectedOption.dataset.max || '';
            
            document.getElementById(`unit-${rowId}`).textContent = unit;
            const quantityInput = document.getElementById(`quantity-${rowId}`);
            quantityInput.min = min;
            if (max) {
                quantityInput.max = max;
            }
        }

        function removeItemRow(rowId) {
            const row = document.getElementById(`item-row-${rowId}`);
            if (row) {
                row.remove();
            }
        }

        // Form validation
        document.getElementById('createRequestForm').addEventListener('submit', function(e) {
            const itemInputs = document.querySelectorAll('select[name^="items"]');
            if (itemInputs.length === 0) {
                e.preventDefault();
                alert('Please add at least one item to the request.');
                return false;
            }
        });
    </script>
@endsection