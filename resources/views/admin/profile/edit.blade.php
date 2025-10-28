{{-- resources/views/admin/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Profile - AgriSys Admin')

@section('page-title', 'Edit Profile')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Profile Edit Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Edit Your Profile
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Photo Section -->
                        <div class="mb-4 text-center">
                            <div class="position-relative d-inline-block">
                                @if($user->profile_photo_url)
                                    <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" 
                                         class="rounded-circle border border-3 border-primary" 
                                         style="width: 150px; height: 150px; object-fit: cover;" id="profilePreview">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold border border-3 border-primary mx-auto" 
                                         style="width: 150px; height: 150px; font-size: 48px;" id="profilePreview">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                
                                <label for="profile_photo" class="position-absolute bottom-0 end-0 btn btn-primary btn-sm rounded-circle" 
                                       style="width: 48px; height: 48px; padding: 0; cursor: pointer; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center;" 
                                       data-bs-toggle="tooltip" title="Change Photo">
                                    <i class="fas fa-camera" style="font-size: 1.2rem;"></i>
                                </label>
                                <input type="file" class="d-none @error('profile_photo') is-invalid @enderror" 
                                       id="profile_photo" name="profile_photo" accept="image/*" onchange="previewImage(event)">
                            </div>
                            
                            @if($user->profile_photo)
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeletePhoto()">
                                        <i class="fas fa-trash-alt me-1"></i>Remove Photo
                                    </button>
                                </div>
                            @endif
                            
                            @error('profile_photo')
                                <div class="text-danger mt-2">
                                    <small><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small>
                                </div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                Allowed: JPG, PNG, GIF (Max: 2MB)
                            </small>
                        </div>

                        <hr class="my-4">

                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-1"></i>Full Name
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Contact Number Field -->
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">
                                <i class="fas fa-phone me-1"></i>Contact Number
                            </label>
                            <input type="tel" class="form-control @error('contact_number') is-invalid @enderror" 
                                   id="contact_number" name="contact_number" 
                                   value="{{ old('contact_number', $user->contact_number ?? '') }}"
                                   placeholder="+639XXXXXXXXX">
                            @error('contact_number')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Divider -->
                        <hr class="my-4">

                        <!-- Change Password Section -->
                        <div class="mb-3">
                            <h6 class="text-secondary">
                                <i class="fas fa-lock me-2"></i>Change Password (Optional)
                            </h6>
                            <small class="text-muted">Leave blank if you don't want to change your password</small>
                        </div>

                        <!-- Current Password Field -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- New Password Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Min 8 characters with uppercase, numbers, symbols">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password_icon"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>Must contain at least 8 characters, including uppercase, numbers, and symbols
                            </small>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Information Card -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Account Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Account Type:</div>
                        <div class="col-sm-8 fw-semibold">
                            @if($user->isSuperAdmin())
                                <span class="badge bg-danger">Super Admin</span>
                            @else
                                <span class="badge bg-info">Admin</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Member Since:</div>
                        <div class="col-sm-8 fw-semibold">{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 text-muted">Last Updated:</div>
                        <div class="col-sm-8 fw-semibold">{{ $user->updated_at->format('M d, Y \a\t g:i A') }}</div>
                    </div>
                </div>
            </div>
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
    .bg-gradient {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }

    .card-header.bg-gradient h5 {
        color: white;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .form-label {
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
        box-shadow: 0 0.5rem 1rem rgba(52, 152, 219, 0.3);
        transform: translateY(-2px);
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    hr {
        border-color: rgba(44, 62, 80, 0.1);
    }

    .text-muted {
        color: #95a5a6 !important;
    }

    #profilePreview {
        transition: all 0.3s ease;
    }

    #profilePreview:hover {
        transform: scale(1.05);
    }

    .input-group .btn-outline-secondary {
        border-color: #ced4da;
    }

    .input-group .btn-outline-secondary:hover {
        background-color: #e9ecef;
        color: #495057;
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
                    img.className = 'rounded-circle border border-3 border-primary';
                    img.style.cssText = 'width: 150px; height: 150px; object-fit: cover;';
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