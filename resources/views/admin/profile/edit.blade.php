{{-- resources/views/admin/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Profile - AgriSys Admin')

@section('page-title', 'Edit Profile')

@section('content')
<div class="container-fluid mt-4 mb-5" style="max-width: 1200px;">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header with Save Button -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h4 class="mb-0"><strong>My Profile</strong></h4>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4">
                        Cancel
                    </a>
                    <button type="submit" form="profileForm" class="btn btn-primary px-4">
                        <i class="fas fa-check me-2"></i>Save
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <form id="profileForm" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Left Column - Profile Photo & Account Info -->
                    <div class="col-lg-4">
                        <!-- Profile Photo Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4 text-center">
                                <div class="position-relative d-inline-block mb-4">
                                    @if($user->profile_photo_url)
                                        <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" 
                                             class="rounded-circle" 
                                             style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #f0f0f0;" id="profilePreview">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary fw-bold" 
                                             style="width: 150px; height: 150px; font-size: 56px; border: 4px solid #f0f0f0;" id="profilePreview">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    
                                    <label for="profile_photo" class="position-absolute bottom-0 end-0 btn btn-primary btn-sm rounded-circle" 
                                        style="width: 48px; height: 48px; padding: 0; cursor: pointer; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-camera" style="font-size: 1rem;"></i>
                                    </label>
                                    <input type="file" class="d-none @error('profile_photo') is-invalid @enderror" 
                                           id="profile_photo" name="profile_photo" accept="image/*" onchange="previewImage(event)">
                                </div>

                                @if($user->profile_photo)
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="confirmDeletePhoto()">
                                            <i class="fas fa-trash-alt me-2"></i>Remove Photo
                                        </button>
                                    </div>
                                @endif

                                @error('profile_photo')
                                    <div class="text-danger mb-3">
                                        <small><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small>
                                    </div>
                                @enderror

                                <small class="text-muted d-block">
                                    <strong>Allowed:</strong> JPG, PNG, GIF<br/>
                                    <strong>Max size:</strong> 10MB
                                </small>
                            </div>
                        </div>

                        <!-- Account Information Card -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-3">Account Information</h6>
                                
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Account Type</small>
                                    <div>
                                        @if($user->isSuperAdmin())
                                            <span class="badge bg-danger">Super Admin</span>
                                        @else
                                            <span class="badge bg-info">Admin</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <small class="text-muted d-block mb-1">Member Since</small>
                                    <strong class="d-block">{{ $user->created_at->format('M d, Y') }}</strong>
                                </div>

                                <div>
                                    <small class="text-muted d-block mb-1">Last Updated</small>
                                    <strong class="d-block">{{ $user->updated_at->format('M d, Y \a\t g:i A') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Form Fields -->
                    <div class="col-lg-8">
                        <!-- Personal Information Section -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-4"><i class="fas fa-user-circle me-2 text-primary"></i>Personal Information</h6>

                                <div class="row g-3">
                                    <!-- Full Name -->
                                    <div class="col-12">
                                        <label for="name" class="form-label"><i class="fas fa-user me-2 text-muted"></i>Full Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                            id="name" name="name" value="{{ old('name', $user->name) }}" required 
                                            style="text-transform: capitalize;" oninput="this.value = this.value.replace(/\b\w/g, l => l.toUpperCase())">
                                        @error('name')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-12">
                                        <label for="email" class="form-label"><i class="fas fa-envelope me-2 text-muted"></i>Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-2"><i class="fas fa-lock me-2 text-primary"></i>Change Password</h6>
                                <small class="text-muted d-block mb-4">Leave blank if you don't want to change your password</small>

                                <div class="row g-3">
                                    <!-- Current Password -->
                                    <div class="col-12">
                                        <label for="current_password" class="form-label"><i class="fas fa-key me-2 text-muted"></i>Current Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                                id="current_password" name="current_password">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                <i class="fas fa-eye" id="current_password_icon"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div class="text-danger mt-2">
                                                <small><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- New Password -->
                                    <div class="col-12">
                                        <label for="password" class="form-label"><i class="fas fa-lock me-2 text-muted"></i>New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                id="password" name="password" placeholder="Min 8 characters with uppercase, numbers, symbols">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                <i class="fas fa-eye" id="password_icon"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>Must contain at least 8 characters, including uppercase, numbers, and symbols
                                        </small>
                                        @error('password')
                                            <div class="text-danger mt-2">
                                                <small><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small>
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="col-12">
                                        <label for="password_confirmation" class="form-label"><i class="fas fa-check-circle me-2 text-muted"></i>Confirm New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" 
                                                id="password_confirmation" name="password_confirmation">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                                <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Photo Form (Hidden) -->
<form id="deletePhotoForm" action="{{ route('admin.profile.deletePhoto') }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('styles')
<style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        border-radius: 8px;
        border: none;
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
    }

    .form-control {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #2ecc71;
        box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.1);
    }

    .form-label {
        color: #2c3e50;
        font-weight: 500;
        margin-bottom: 0.65rem;
        font-size: 0.95rem;
    }

    .btn-primary {
        background-color: #3498db;
        border: none;
        padding: 0.625rem 2rem;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    .btn-outline-secondary {
        border-radius: 6px;
        border-color: #e0e0e0;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        border-color: #e0e0e0;
        color: #495057;
    }

    .input-group .btn-outline-secondary {
        border-left: none;
    }

    .input-group .form-control {
        border-right: none;
    }

    .input-group .form-control:focus {
        border-right: none;
        box-shadow: none;
    }

    .input-group .form-control:focus + .btn {
        border-color: #2ecc71;
    }

    #profilePreview {
        transition: transform 0.3s ease;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .text-muted {
        color: #95a5a6 !important;
    }

    .invalid-feedback {
        color: #e74c3c;
        font-size: 0.85rem;
        display: block;
        margin-top: 0.35rem;
    }

    .is-invalid {
        border-color: #e74c3c !important;
    }

    .is-invalid:focus {
        border-color: #e74c3c !important;
        box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.1) !important;
    }

    .card-body {
        background-color: #ffffff;
    }

    .btn-outline-danger {
        border-radius: 6px;
        border-color: #e74c3c;
        color: #e74c3c;
    }

    .btn-outline-danger:hover {
        background-color: #e74c3c;
        border-color: #e74c3c;
        color: white;
    }

    h6 {
        color: #2c3e50;
    }

    .row.g-4 > div {
        display: flex;
        flex-direction: column;
    }

    @media (max-width: 991px) {
        .col-lg-4,
        .col-lg-8 {
            margin-bottom: 2rem;
        }
    }
    .input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-right: none;
    }

    .input-group .form-control.border-start-0 {
        border-left: none;
    }

    .input-group .form-control.border-end-0 {
        border-right: none;
    }

    .input-group:focus-within .input-group-text {
        border-color: #3498db;
    }

    .input-group:focus-within .form-control {
        border-color: #3498db;
    }
</style>
@endsection

@section('scripts')
<script>
    // Preview image before upload
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profilePreview');
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    // Replace div with img
                    const img = document.createElement('img');
                    img.id = 'profilePreview';
                    img.src = e.target.result;
                    img.className = 'rounded-circle';
                    img.style.cssText = 'width: 150px; height: 150px; object-fit: cover; border: 4px solid #f0f0f0;';
                    preview.replaceWith(img);
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Confirm photo deletion
    function confirmDeletePhoto() {
        if (confirm('Are you sure you want to remove your profile photo?')) {
            document.getElementById('deletePhotoForm').submit();
        }
    }

    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection