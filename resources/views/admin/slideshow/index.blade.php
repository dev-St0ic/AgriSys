@extends('layouts.app')

@section('title', 'Slideshow Management - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-images text-primary me-2"></i>
        <span class="text-primary fw-bold">Slideshow Management</span>
    </div>
@endsection

@section('content')
    <style>
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
            font-size: 2.5rem !important;
        }

        .stat-number {
            font-size: 2.5rem !important;
            font-weight: 700 !important;
            color: #495057;
            line-height: 1;
        }

        .stat-label {
            font-size: 1rem !important;
            font-weight: 500 !important;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

         /* Image hover effect for preview */
        .position-relative:hover .hover-overlay {
            opacity: 1 !important;
            transition: opacity 0.3s ease;
        }

        .position-relative::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0.375rem;
        }

        .position-relative:hover::after {
            opacity: 1;
        }

        .hover-overlay {
            z-index: 2;
            transition: opacity 0.3s ease;
        }
        /* When image preview is shown on top of view modal */
        #imagePreviewModal.show {
            z-index: 1060;
        }
    </style>

    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-images text-primary"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $slides->count() }}</div>
                    <div class="stat-label text-primary text-uppercase">Total Slides</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $slides->where('is_active', true)->count() }}</div>
                    <div class="stat-label text-success text-uppercase">Active Slides</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-pause-circle text-warning"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $slides->where('is_active', false)->count() }}</div>
                    <div class="stat-label text-warning text-uppercase">Inactive Slides</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-hdd text-info"></i>
                    </div>
                    <div class="stat-number mb-2">{{ number_format($slides->count() * 0.5, 1) }} MB</div>
                    <div class="stat-label text-info text-uppercase">Storage Used</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12"> <!-- Slideshow Images Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <div style="flex: 1;"></div>
                    <h6 class="m-0 font-weight-bold text-primary text-center" style="flex: 1;">
                        <i class="fas fa-list me-2"></i>Slideshow Images
                    </h6>
                    <div class="d-flex gap-2 flex-nowrap" style="flex: 1; justify-content: flex-end;">
                        <button type="button" class="btn btn-primary btn-sm" id="addNewSlideBtn"><i
                                class="fas fa-plus me-1"></i>Add New Slide</button>
                        <button type="button" class="btn btn-info btn-sm" id="previewSlideshowBtn"><i
                                class="fas fa-eye me-1"></i>Preview</button>
                        @if ($slides->count() > 1)
                            <button type="button" class="btn btn-secondary btn-sm" id="reorderBtn"><i
                                    class="fas fa-sort me-1"></i>Reorder</button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="display: {{ $slides->count() > 0 ? 'block' : 'none' }};">
                        <table class="table table-bordered" id="slideshowTable">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" width="50">Order</th>
                                    <th class="text-center" width="120">Preview</th>
                                    <th class="text-center">Title</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center" width="80">Status</th>
                                    <th class="text-center" width="100">Created</th>
                                    <th class="text-center" width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sortableSlides">
                                @foreach ($slides as $slide)
                                    <tr data-slide-id="{{ $slide->id }}">
                                        <td class="text-center">
                                            <div class="drag-handle" style="cursor: move;">
                                                <i class="fas fa-grip-vertical text-muted"></i>
                                                <span class="order-number">{{ $slide->order }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <img src="{{ $slide->image_url }}" alt="Slide preview"
                                                class="img-thumbnail slide-preview"
                                                style="width: 100px; height: 60px; object-fit: cover; cursor: pointer; display: block; margin: 0 auto;"
                                                onclick="showImageModal('{{ $slide->image_url }}', '{{ $slide->title }}')">
                                        </td>
                                        <td class="text-start">
                                            <strong>{{ $slide->title ?: 'Untitled' }}</strong>
                                        </td>
                                        <td class="text-start">
                                            <span class="text-truncate d-block" style="max-width: 200px;">
                                                {{ $slide->description ?: 'No description' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge {{ $slide->is_active ? 'bg-success' : 'bg-secondary' }} fs-6">
                                                {{ $slide->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-start">
                                            <small>{{ $slide->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick='viewSlide(@json($slide))'>
                                                    <i class="fas fa-eye me-1"></i>View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick='editSlide(@json($slide))'>
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                                <div class="btn-group" role="group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                onclick="toggleStatus({{ $slide->id }})">
                                                                <i
                                                                    class="fas fa-{{ $slide->is_active ? 'pause' : 'play' }} me-2"></i>
                                                                {{ $slide->is_active ? 'Deactivate' : 'Activate' }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                                onclick="deleteSlide({{ $slide->id }})">
                                                                <i class="fas fa-trash me-2"></i>Delete
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

                    <div class="text-center py-5" style="display: {{ $slides->count() > 0 ? 'none' : 'block' }};">
                        <i class="fas fa-images fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No slideshow images found</h5>
                        <p class="text-muted">Add your first slideshow image to get started</p>
                        <button type="button" class="btn btn-primary" id="addFirstSlideBtn">
                            <i class="fas fa-plus me-2"></i>Add First Slide
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- UPDATED: New Improved Add Slide Modal with Centered Title and Colored Icons -->
    <div class="modal fade" id="newAddSlideModal" tabindex="-1" aria-labelledby="newAddSlideModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <div style="flex: 1;"></div>
                    <h5 class="modal-title w-100 text-center" id="newAddSlideModalLabel">
                        <i></i>Add New Slideshow Image
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Progress Bar -->
                    <div class="progress mb-4" style="height: 4px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" id="uploadProgress">
                        </div>
                    </div>

                    <form id="newSlideForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Left Column - Image Upload -->
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-image me-2" style="color: #007bff;"></i>Image Upload</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Drag & Drop Area -->
                                        <div class="upload-area border-2 border-dashed rounded p-4 text-center mb-3"
                                            id="uploadArea"
                                            style="min-height: 200px; cursor: pointer; transition: all 0.3s;">
                                            <div id="uploadAreaContent">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Drag & Drop Your Image Here</h5>
                                                <p class="text-muted mb-3">or click to browse files</p>
                                                <button type="button" class="btn btn-outline-primary">Choose
                                                    File</button>
                                            </div>
                                            <div id="imagePreviewArea" style="display: none;">
                                                <img id="newImagePreview" class="img-fluid rounded shadow"
                                                    style="max-height: 300px;">
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        id="removeImageBtn">
                                                        <i class="fas fa-times"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="file" id="newImageInput" name="image" accept="image/*"
                                            style="display: none;">

                                        <!-- Image Info -->
                                        <div id="imageInfo" style="display: none;">
                                            <div class="alert alert-info">
                                                <strong>Image Details:</strong>
                                                <ul class="mb-0 mt-2">
                                                    <li>File: <span id="fileName"></span></li>
                                                    <li>Size: <span id="fileSize"></span></li>
                                                    <li>Dimensions: <span id="fileDimensions"></span></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Validation Messages -->
                                        <div id="imageValidation" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Slide Details -->
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-edit me-2" style="color: #28a745;"></i>Slide Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="newTitle" class="form-label">
                                                <i class="fas fa-heading me-1" style="color: #007bff;"></i>Title
                                            </label>
                                            <input type="text" class="form-control" id="newTitle" name="title"
                                                placeholder="Enter slide title (optional)" maxlength="255">
                                            <div class="form-text">Leave empty for auto-generated title</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="newDescription" class="form-label">
                                                <i class="fas fa-align-left me-1" style="color: #007bff;"></i>Description
                                            </label>
                                            <textarea class="form-control" id="newDescription" name="description" rows="4"
                                                placeholder="Enter slide description (optional)" maxlength="1000"></textarea>
                                            <div class="form-text">
                                                <span id="charCount">0</span>/1000 characters
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="newOrder" class="form-label">
                                                        <i class="fas fa-sort-numeric-up me-1" style="color: #007bff;"></i>Display Order
                                                    </label>
                                                    <input type="number" class="form-control" id="newOrder"
                                                        name="order" value="{{ $slides->max('order') + 1 }}"
                                                        min="1">
                                                    <div class="form-text">Position in slideshow sequence</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i class="fas fa-toggle-on me-1" style="color: #28a745;"></i>Status
                                                    </label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="newIsActive"
                                                            name="is_active" checked>
                                                        <label class="form-check-label" for="newIsActive">
                                                            <span class="status-text">Active - Will appear in
                                                                slideshow</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Advanced Options -->
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <i class="fas fa-cog me-1" style="color: #ffc107;"></i>Advanced Options
                                                </h6>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="optimizeImage">
                                                    <label class="form-check-label" for="optimizeImage">
                                                        Auto-optimize image for web (recommended)
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="generateThumbnail" checked>
                                                    <label class="form-check-label" for="generateThumbnail">
                                                        Generate thumbnail for faster loading
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveSlideBtn" disabled>
                        <i class="fas fa-save me-2"></i>
                        <span class="btn-text">Add Slide</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Slide Modal -->
    <div class="modal fade" id="editSlideModal" tabindex="-1" aria-labelledby="editSlideModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSlideModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Slideshow Image
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSlideForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Current Image</label>
                                    <img id="editCurrentImage" src="#" alt="Current image"
                                        class="img-thumbnail mb-2"
                                        style="width: 100%; max-height: 150px; object-fit: cover;">
                                </div>

                                <div class="mb-3">
                                    <label for="editImage" class="form-label">New Image (Optional)</label>
                                    <input type="file" class="form-control" id="editImage" name="image"
                                        accept="image/*" onchange="previewImage(this, 'editImagePreview')">
                                    <div class="form-text">Leave empty to keep current image</div>
                                </div>

                                <div class="mb-3">
                                    <img id="editImagePreview" src="#" alt="Preview" class="img-thumbnail"
                                        style="display: none; width: 100%; max-height: 150px; object-fit: cover;">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="editTitle" name="title"
                                        placeholder="Enter slide title">
                                </div>

                                <div class="mb-3">
                                    <label for="editDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="editDescription" name="description" rows="3"
                                        placeholder="Enter slide description"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="editOrder" class="form-label">Order</label>
                                    <input type="number" class="form-control" id="editOrder" name="order"
                                        min="0">
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="editIsActive"
                                            name="is_active">
                                        <label class="form-check-label" for="editIsActive">
                                            Active (Display in slideshow)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Slide
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal - Consistent Design with Header -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <div style="flex: 1;"></div>
                    <h5 class="modal-title w-100 text-center" id="imagePreviewModalLabel">
                        <i class="fas fa-image me-2" style="color: #fff;"></i>Image Preview
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img id="previewImage" src="#" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height: 550px; object-fit: contain;">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Slideshow Preview Modal - With Consistent Header Design -->
    <div class="modal fade" id="slideshowPreviewModal" tabindex="-1" aria-labelledby="slideshowPreviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <div style="flex: 1;"></div>
                    <h5 class="modal-title w-100 text-center" id="slideshowPreviewModalLabel">
                        <i></i>Slideshow Preview
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="slideshowPreviewContainer" style="height: 400px; position: relative; overflow: hidden;">
                        <!-- Slideshow will be loaded here -->
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


    <!-- View Slide Modal - View Only (Reference Event Modal) -->
    <div class="modal fade" id="viewSlideModal" tabindex="-1" aria-labelledby="viewSlideModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <div style="flex: 1;"></div>
                    <h5 class="modal-title w-100 text-center" id="viewSlideModalLabel">
                        <i></i>View Slide Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Image Section -->
                        <div class="col-md-4">
                            <div class="position-relative" style="cursor: pointer;" onclick="showImageModal(document.getElementById('viewSlideImage').src, 'Slide Preview')">
                                <img id="viewSlideImage" src="#" alt="Slide image" class="img-fluid rounded shadow-sm w-100" style="max-height: 300px; object-fit: cover;">
                                <div class="position-absolute top-50 start-50 translate-middle opacity-0 hover-overlay">
                                    <i class="fas fa-search-plus fa-2x text-white"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="col-md-8">
                            <h4 class="fw-bold mb-3" id="viewSlideTitle"></h4>
                            <p class="text-muted mb-4" id="viewSlideDescription"></p>

                            <div class="row g-3">
                                <!-- Order -->
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-sort-numeric-up me-1"></i>Display Order
                                            </small>
                                            <span class="fw-semibold" id="viewSlideOrder"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">Status</small>
                                            <p id="viewSlideStatus" class="mb-0"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Created Date -->
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-calendar me-1"></i>Created
                                            </small>
                                            <span class="text-muted small" id="viewSlideCreated"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Updated Date -->
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-edit me-1"></i>Last Updated
                                            </small>
                                            <span class="text-muted small" id="viewSlideUpdated"></span>
                                        </div>
                                    </div>
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


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize sortable for reordering (only if element exists)
            const sortableElement = document.getElementById('sortableSlides');
            let sortable = null;

            if (sortableElement) {
                sortable = Sortable.create(sortableElement, {
                    handle: '.drag-handle',
                    animation: 150,
                    disabled: true, // Initially disabled
                    onEnd: function(evt) {
                        updateSlidesOrder();
                    }
                });
            }

            // Toggle reorder mode
            $('#reorderBtn').click(function() {
                if (!sortable) return; // Exit if sortable wasn't initialized

                const isReorderMode = $(this).hasClass('active');

                if (isReorderMode) {
                    // Exit reorder mode
                    sortable.option('disabled', true);
                    $(this).removeClass('active btn-warning').addClass('btn-secondary');
                    $(this).html('<i class="fas fa-sort me-1"></i>Reorder');
                    $('.drag-handle').css('cursor', 'default');
                } else {
                    // Enter reorder mode
                    sortable.option('disabled', false);
                    $(this).removeClass('btn-secondary').addClass('active btn-warning');
                    $(this).html('<i class="fas fa-times me-1"></i>Done');
                    $('.drag-handle').css('cursor', 'move');

                    // Show instructions
                    showAlert('info', 'Drag and drop slides to reorder them. Click "Done" when finished.');
                }
            });

            // Preview slideshow
            $('#previewSlideshowBtn').click(function() {
                loadSlideshowPreview();
            });

            // ============ NEW ADD SLIDE FUNCTIONALITY ============

            // Add new slide button handlers
            $('#addNewSlideBtn, #addFirstSlideBtn').click(function() {
                resetNewSlideForm();
                $('#newAddSlideModal').modal('show');
            });

            // Drag & Drop functionality
            const uploadArea = $('#uploadArea');
            const fileInput = $('#newImageInput');

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea[0].addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            // Highlight drop area when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea[0].addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea[0].addEventListener(eventName, unhighlight, false);
            });

            // Handle dropped files
            uploadArea[0].addEventListener('drop', handleDrop, false);

            // Handle click to open file dialog
            uploadArea.click(function() {
                fileInput.click();
            });

            // Handle file input change
            fileInput.change(function() {
                handleFiles(this.files);
            });

            // Character counter for description
            $('#newDescription').on('input', function() {
                const length = $(this).val().length;
                $('#charCount').text(length);

                if (length > 950) {
                    $('#charCount').addClass('text-warning');
                }
                if (length > 980) {
                    $('#charCount').removeClass('text-warning').addClass('text-danger');
                }
                if (length <= 950) {
                    $('#charCount').removeClass('text-warning text-danger');
                }
            });

            // Status toggle handler
            $('#newIsActive').change(function() {
                const statusText = $(this).is(':checked') ?
                    'Active - Will appear in slideshow' :
                    'Inactive - Hidden from slideshow';
                $('.status-text').text(statusText);
            });

            // Remove image button
            $('#removeImageBtn').click(function() {
                removeSelectedImage();
            });

            // Save slide button
            $('#saveSlideBtn').click(function() {
                if (validateAndSubmitSlide()) {
                    submitNewSlide();
                }
            });

            // Reset form when modal is hidden
            $('#newAddSlideModal').on('hidden.bs.modal', function() {
                resetNewSlideForm();
            });
        });

        // ============ NEW ADD SLIDE FUNCTIONS ============

        // Prevent default drag behaviors
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area
        function highlight(e) {
            $('#uploadArea').addClass('border-primary bg-light');
        }

        // Unhighlight drop area
        function unhighlight(e) {
            $('#uploadArea').removeClass('border-primary bg-light');
        }

        // Handle dropped files
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        // Handle file selection
        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];

                // Validate file
                if (validateImageFile(file)) {
                    displayImagePreview(file);
                    showImageInfo(file);
                    $('#saveSlideBtn').prop('disabled', false);
                } else {
                    removeSelectedImage();
                }
            }
        }

        // Validate image file
        function validateImageFile(file) {
            const validationDiv = $('#imageValidation');
            validationDiv.empty();

            // Check file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showValidationError('Invalid file type. Please select a JPEG, PNG, or GIF image.');
                return false;
            }

            // Check file size (10MB = 10 * 1024 * 1024 bytes)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                showValidationError('File size too large. Maximum allowed size is 10MB.');
                return false;
            }

            // Show success message
            validationDiv.html('<div class="alert alert-success">✓ Image is valid and ready for upload!</div>');
            return true;
        }

        // Show validation error
        function showValidationError(message) {
            $('#imageValidation').html(`<div class="alert alert-danger">${message}</div>`);
        }

        // Display image preview
        function displayImagePreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#newImagePreview').attr('src', e.target.result);
                $('#uploadAreaContent').hide();
                $('#imagePreviewArea').show();
            };
            reader.readAsDataURL(file);
        }

        // Show image information
        function showImageInfo(file) {
            const fileSize = formatFileSize(file.size);
            $('#fileName').text(file.name);
            $('#fileSize').text(fileSize);

            // Get image dimensions
            const img = new Image();
            img.onload = function() {
                $('#fileDimensions').text(`${this.width} × ${this.height} pixels`);
            };
            img.src = URL.createObjectURL(file);

            $('#imageInfo').show();
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Remove selected image
        function removeSelectedImage() {
            $('#newImageInput').val('');
            $('#newImagePreview').attr('src', '#');
            $('#uploadAreaContent').show();
            $('#imagePreviewArea').hide();
            $('#imageInfo').hide();
            $('#imageValidation').empty();
            $('#saveSlideBtn').prop('disabled', true);
        }

        // Reset form
        function resetNewSlideForm() {
            $('#newSlideForm')[0].reset();
            removeSelectedImage();
            $('#newOrder').val({{ $slides->max('order') + 1 }});
            $('#newIsActive').prop('checked', true);
            $('.status-text').text('Active - Will appear in slideshow');
            $('#charCount').text('0').removeClass('text-warning text-danger');
            $('#uploadProgress').css('width', '0%');
            $('#saveSlideBtn').prop('disabled', true).find('.btn-text').text('Add Slide');
        }

        // Validate and submit slide
        function validateAndSubmitSlide() {
            const fileInput = $('#newImageInput')[0];

            if (!fileInput.files || fileInput.files.length === 0) {
                showAlert('error', 'Please select an image file.');
                return false;
            }

            const file = fileInput.files[0];
            if (!validateImageFile(file)) {
                return false;
            }

            return true;
        }

        // Submit new slide
        function submitNewSlide() {
            const formData = new FormData();
            const fileInput = $('#newImageInput')[0];

            // Add form data
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('image', fileInput.files[0]);
            formData.append('title', $('#newTitle').val());
            formData.append('description', $('#newDescription').val());
            formData.append('order', $('#newOrder').val());

            if ($('#newIsActive').is(':checked')) {
                formData.append('is_active', '1');
            }

            // Update UI
            const saveBtn = $('#saveSlideBtn');
            saveBtn.prop('disabled', true).find('.btn-text').text('Uploading...');

            // Submit via AJAX
            $.ajax({
                url: '{{ route('admin.slideshow.store') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    // Upload progress
                    xhr.upload.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = (evt.loaded / evt.total) * 100;
                            $('#uploadProgress').css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    $('#uploadProgress').css('width', '100%');
                    showAlert('success', 'Slideshow image added successfully!');
                    $('#newAddSlideModal').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while uploading the slide.';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join(', ');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    showAlert('error', errorMessage);
                    saveBtn.prop('disabled', false).find('.btn-text').text('Add Slide');
                    $('#uploadProgress').css('width', '0%');
                }
            });
        }

        // ============ EXISTING FUNCTIONS ============
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + previewId).attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Show image modal
        function showImageModal(src, title) {
            $('#previewImage').attr('src', src);
            $('#imagePreviewModalLabel').text(title || 'Image Preview');
            $('#imagePreviewModal').modal('show');
        }

        // View slide function - Fixed with Updated Date
        function viewSlide(slide) {
            document.getElementById('viewSlideImage').src = slide.image_url;
            document.getElementById('viewSlideTitle').textContent = slide.title || 'Untitled';
            document.getElementById('viewSlideDescription').textContent = slide.description || 'No description';
            document.getElementById('viewSlideOrder').textContent = slide.order;

            const statusBadge = slide.is_active ?
                '<span class="badge bg-success">Active</span>' :
                '<span class="badge bg-secondary">Inactive</span>';
            document.getElementById('viewSlideStatus').innerHTML = statusBadge;

            // Format created date
            const createdDate = new Date(slide.created_at);
            const formattedCreatedDate = createdDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('viewSlideCreated').textContent = formattedCreatedDate;

            // Format updated date
            const updatedDate = new Date(slide.updated_at);
            const formattedUpdatedDate = updatedDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('viewSlideUpdated').textContent = formattedUpdatedDate;

            new bootstrap.Modal(document.getElementById('viewSlideModal')).show();
        }

        // Edit from view modal
        $('#editFromViewBtn').click(function() {
            const slide = $(this).data('slide');
            $('#viewSlideModal').modal('hide');
            editSlide(slide);
        });

        // Edit slide function
        function editSlide(slide) {
            $('#editSlideForm').attr('action', `/admin/slideshow/${slide.id}`);
            $('#editCurrentImage').attr('src', slide.image_url);
            $('#editTitle').val(slide.title);
            $('#editDescription').val(slide.description);
            $('#editOrder').val(slide.order);
            $('#editIsActive').prop('checked', slide.is_active);
            $('#editImagePreview').hide();
            $('#editSlideModal').modal('show');
        }

        // Toggle slide status
        function toggleStatus(slideId) {
            $.ajax({
                url: `/admin/slideshow/${slideId}/toggle-status`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        location.reload();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'An error occurred while updating the slide status.');
                }
            });
        }

        // Delete slide function
        function deleteSlide(slideId) {
            if (confirm('Are you sure you want to delete this slideshow image? This action cannot be undone.')) {
                $.ajax({
                    url: `/admin/slideshow/${slideId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showAlert('success', 'Slideshow image deleted successfully!');
                        location.reload();
                    },
                    error: function() {
                        showAlert('error', 'An error occurred while deleting the slide.');
                    }
                });
            }
        }

        // Update slides order
        function updateSlidesOrder() {
            const slides = [];
            $('#sortableSlides tr').each(function(index) {
                const slideId = $(this).data('slide-id');
                slides.push({
                    id: slideId,
                    order: index + 1
                });

                // Update visual order number
                $(this).find('.order-number').text(index + 1);
            });

            $.ajax({
                url: '/admin/slideshow/update-order',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    slides: slides
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'An error occurred while updating the order.');
                }
            });
        }

        // Load slideshow preview
        function loadSlideshowPreview() {
            $.ajax({
                url: '/api/slideshow/active',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.slides.length > 0) {
                        createSlideshowPreview(response.slides);
                        $('#slideshowPreviewModal').modal('show');
                    } else {
                        showAlert('warning', 'No active slides found for preview.');
                    }
                },
                error: function() {
                    showAlert('error', 'Error loading slideshow preview.');
                }
            });
        }

        // Create slideshow preview
        function createSlideshowPreview(slides) {
            const container = $('#slideshowPreviewContainer');
            container.empty();

            // Create slides
            slides.forEach((slide, index) => {
                const slideDiv = $(`
            <div class="preview-slide ${index === 0 ? 'active' : ''}"
                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                        background-image: url('${slide.image_url}');
                        background-size: cover; background-position: center;
                        opacity: ${index === 0 ? '1' : '0'};
                        transition: opacity 1s ease-in-out;">
                <div style="position: absolute; bottom: 20px; left: 20px; color: white; background: rgba(0,0,0,0.7); padding: 10px; border-radius: 5px;">
                    <h5>${slide.title || 'Slide ' + (index + 1)}</h5>
                    ${slide.description ? `<p style="margin: 0; font-size: 14px;">${slide.description}</p>` : ''}
                </div>
            </div>
        `);
                container.append(slideDiv);
            });

            // Auto-advance slides
            let currentSlide = 0;
            const slideInterval = setInterval(() => {
                if (!$('#slideshowPreviewModal').hasClass('show')) {
                    clearInterval(slideInterval);
                    return;
                }

                $('.preview-slide').css('opacity', '0');
                currentSlide = (currentSlide + 1) % slides.length;
                $('.preview-slide').eq(currentSlide).css('opacity', '1');
            }, 3000);
        }

        // Alert function - Now uses AgriSys Modal system for consistency
        function showAlert(type, message) {
            if (typeof agrisysModal !== 'undefined') {
                switch (type) {
                    case 'success':
                        agrisysModal.success(message);
                        break;
                    case 'error':
                        agrisysModal.error(message);
                        break;
                    case 'warning':
                        agrisysModal.warning(message);
                        break;
                    default:
                        agrisysModal.info(message);
                }
            } else {
                // Fallback to Bootstrap alerts if modal not available
                const alertClass = type === 'success' ? 'alert-success' :
                    type === 'error' ? 'alert-danger' :
                    type === 'warning' ? 'alert-warning' : 'alert-info';

                const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

                $('body').append(alertHtml);

                // Auto-remove after 5 seconds
                setTimeout(() => {
                    $('.alert').fadeOut();
                }, 5000);
            }
        }
    </script>
@endsection
