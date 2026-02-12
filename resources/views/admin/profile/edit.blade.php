{{-- resources/views/admin/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Profile - AgriSys Admin')

@section('page-title', 'Edit Profile')

@section('content')
<div class="container-fluid mt-4 mb-5" style="max-width: 1200px;">
    <div class="row">
        <div class="col-lg-12">
            <!-- Success/Info Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <!-- Header with Save Button -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h4 class="mb-0"><strong>My Profile</strong></h4>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4">
                        Cancel
                    </a>
                    <button type="submit" form="profileForm" class="btn btn-primary px-4" id="submitBtn">
                        <span class="btn-text"><i class="fas fa-check me-2"></i>Save</span>
                        <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Saving...</span>
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
                                            onkeyup="autoCapitalizeName()">
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
                                            id="email" name="email" value="{{ old('email', $user->email) }}" required
                                            onkeyup="handleEmailChange()" onblur="validateEmail()">
                                        @error('email')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        <div id="email-feedback" class="mt-2"></div>
                                    </div>

                                    <!-- Current Password for Email Change (shown only when email changes) -->
                                    <div class="col-12" id="email-password-group" style="display: none;">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Email Change Detected:</strong> Please enter your current password to confirm this change.
                                        </div>
                                        <label for="email_change_password" class="form-label">
                                            <i class="fas fa-key me-2 text-muted"></i>Current Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('email_change_password') is-invalid @enderror" 
                                                id="email_change_password" name="email_change_password"
                                                placeholder="Enter current password to change email">
                                            <button class="btn btn-outline-secondary toggle-password" type="button" 
                                                onclick="togglePasswordVisibility('email_change_password')">
                                                <i class="fas fa-eye" id="email_change_password_icon"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>Required to confirm email change for security
                                        </small>
                                        @error('email_change_password')
                                            <div class="invalid-feedback d-block">
                                                <i class="fas fa-times-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        <div id="email-password-feedback" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h6 class="fw-semibold mb-2"><i class="fas fa-lock me-2 text-primary"></i>Change Password</h6>
                                <small class="text-muted d-block mb-3">Leave blank if you don't want to change your password</small>
                                
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-envelope-open-text me-2"></i>
                                    <strong>Email Verification Required:</strong> When you change your password, a verification email will be sent. Your password will only be updated after you click the verification link in your email.
                                </div>

                                <div class="row g-3">
                                    <!-- Current Password -->
                                    <div class="col-12">
                                        <label for="current_password" class="form-label"><i class="fas fa-key me-2 text-muted"></i>Current Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                                id="current_password" name="current_password">
                                            <button class="btn btn-outline-secondary toggle-password" type="button" 
                                                onclick="togglePasswordVisibility('current_password')">
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
                                                id="password" name="password" onkeyup="validatePasswordStrength()">
                                            <button class="btn btn-outline-secondary toggle-password" type="button" 
                                                onclick="togglePasswordVisibility('password')">
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
                                        <div id="password-strength-feedback" class="mt-2"></div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="col-12">
                                        <label for="password_confirmation" class="form-label"><i class="fas fa-check-circle me-2 text-muted"></i>Confirm New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" 
                                                id="password_confirmation" name="password_confirmation" onkeyup="validatePasswordMatch()">
                                            <button class="btn btn-outline-secondary toggle-password" type="button" 
                                                onclick="togglePasswordVisibility('password_confirmation')">
                                                <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                            </button>
                                        </div>
                                        <small id="password-match-feedback" class="d-block mt-2"></small>
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

    .toggle-password {
        border-left: none;
        cursor: pointer;
        transition: all 0.2s;
        border-color: #e0e0e0;
        background-color: #ffffff;
        color: #6c757d;
    }

    .toggle-password:hover {
        background-color: #f8f9fa;
        border-color: #e0e0e0;
        color: #495057;
    }

    .input-group .form-control {
        border-right: none;
    }

    .input-group .form-control:focus {
        border-right: none;
        box-shadow: none;
        border-color: #2ecc71;
    }

    .input-group .form-control:focus + .toggle-password {
        border-color: #2ecc71;
        box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.1);
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
    
    .alert-info {
        background-color: #e7f3ff;
        border-color: #b3d9ff;
        color: #004085;
    }

    /* Email validation feedback */
    #email-feedback {
        font-size: 0.875rem;
        font-weight: 500;
    }

    #email-feedback.text-danger {
        color: #dc3545 !important;
    }

    #email-feedback.text-success {
        color: #198754 !important;
    }

    /* Email password validation feedback */
    #email-password-feedback {
        font-size: 0.875rem;
        font-weight: 500;
    }

    #email-password-feedback.text-danger {
        color: #dc3545 !important;
    }

    #email-password-feedback.text-info {
        color: #0dcaf0 !important;
    }

    /* Password validation feedback */
    #password-match-feedback {
        font-size: 0.875rem;
        font-weight: 500;
    }

    #password-match-feedback.text-danger {
        color: #dc3545 !important;
    }

    #password-match-feedback.text-success {
        color: #198754 !important;
    }

    #password-strength-feedback {
        font-size: 0.875rem;
        font-weight: 500;
    }

    #password-strength-feedback.text-danger {
        color: #dc3545 !important;
    }

    #password-strength-feedback.text-warning {
        color: #ffc107 !important;
    }

    #password-strength-feedback.text-success {
        color: #198754 !important;
    }

    /* Toast notification styles */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .toast-notification {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 6px 10px;
        min-width: 200px;
        max-width: 280px;
        overflow: hidden;
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.3s ease-in-out;
        pointer-events: auto;
    }

    .toast-notification.show {
        opacity: 1;
        transform: translateY(0);
    }

    .toast-content {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 8px;
        font-size: 0.85rem;
    }

    .toast-content i {
        font-size: 0.9rem;
        min-width: 14px;
    }

    .toast-content span {
        flex: 1;
        color: #333;
        line-height: 1.3;
    }

    .toast-notification.toast-success {
        border-left: 4px solid var(--bs-success);
    }

    .toast-notification.toast-error {
        border-left: 4px solid var(--bs-danger);
    }

    .toast-notification.toast-warning {
        border-left: 4px solid var(--bs-warning);
    }

    .toast-notification.toast-info {
        border-left: 4px solid var(--bs-info);
    }

    .btn-close-toast {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        margin: 0;
        opacity: 0.5;
        transition: opacity 0.2s;
        font-size: 0.9rem;
        line-height: 1;
    }

    .btn-close-toast:hover {
        opacity: 1;
    }

    /* Form controls with icon */
    .form-label i {
        opacity: 0.7;
    }

    /* Highlight field with error from server */
    .field-error-highlight {
        animation: shake 0.5s;
        border-color: #dc3545 !important;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
</style>
@endsection

@section('scripts')
<script>
    // Store original email for comparison
    const originalEmail = "{{ $user->email }}";

    /**
     * Handle email change - show/hide email password field
     */
    function handleEmailChange() {
        const email = document.getElementById('email').value;
        const emailPasswordGroup = document.getElementById('email-password-group');
        const emailPasswordInput = document.getElementById('email_change_password');
        
        // Validate email format first
        validateEmail();
        
        // Show email password field if email has changed
        if (email !== originalEmail && email.trim() !== '') {
            emailPasswordGroup.style.display = 'block';
            emailPasswordInput.setAttribute('required', 'required');
        } else {
            emailPasswordGroup.style.display = 'none';
            emailPasswordInput.removeAttribute('required');
            emailPasswordInput.value = ''; // Clear the field
            document.getElementById('email-password-feedback').textContent = '';
        }
    }

    /**
     * Preview image before upload
     */
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

    /**
     * Validate email format in real-time
     */
    function validateEmail() {
        const email = document.getElementById('email').value;
        const feedback = document.getElementById('email-feedback');

        if (!email) {
            feedback.textContent = '';
            feedback.className = '';
            return;
        }

        // Email validation regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (emailRegex.test(email)) {
            feedback.innerHTML = '<i class="fas fa-check-circle me-1"></i>Valid email format';
            feedback.className = 'text-success';
        } else {
            feedback.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>Please enter a valid email address';
            feedback.className = 'text-danger';
        }
    }

    /**
     * Toggle password visibility
     */
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + '_icon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    /**
     * Validate password strength in real-time
     */
    function validatePasswordStrength() {
        const password = document.getElementById('password').value;
        const feedback = document.getElementById('password-strength-feedback');

        if (!password) {
            feedback.textContent = '';
            feedback.className = '';
            validatePasswordMatch();
            return;
        }

        const hasUpperCase = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        const hasMinLength = password.length >= 8;

        const requirements = [];
        if (!hasMinLength) requirements.push('8 characters');
        if (!hasUpperCase) requirements.push('uppercase letter');
        if (!hasNumber) requirements.push('number');
        if (!hasSymbol) requirements.push('symbol');

        if (requirements.length === 0) {
            feedback.innerHTML = '<i class="fas fa-check-circle me-1"></i>Strong password';
            feedback.className = 'text-success';
        } else if (requirements.length <= 2) {
            feedback.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Missing: ' + requirements.join(', ');
            feedback.className = 'text-warning';
        } else {
            feedback.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>Missing: ' + requirements.join(', ');
            feedback.className = 'text-danger';
        }

        validatePasswordMatch();
    }

    /**
     * Validate passwords match in real-time
     */
    function validatePasswordMatch() {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        const feedback = document.getElementById('password-match-feedback');

        if (!password && !passwordConfirmation) {
            feedback.textContent = '';
            feedback.classList.remove('text-danger', 'text-success');
            return;
        }

        if (password && passwordConfirmation) {
            if (password === passwordConfirmation) {
                feedback.innerHTML = '<i class="fas fa-check-circle me-1"></i>Passwords match';
                feedback.classList.remove('text-danger');
                feedback.classList.add('text-success');
            } else {
                feedback.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>Passwords do not match';
                feedback.classList.remove('text-success');
                feedback.classList.add('text-danger');
            }
        }
    }

    /**
     * Toast notification function
     */
    function showToast(type, message) {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        const iconMap = {
            'success': { icon: 'fas fa-check-circle', color: 'success' },
            'error': { icon: 'fas fa-exclamation-circle', color: 'danger' },
            'warning': { icon: 'fas fa-exclamation-triangle', color: 'warning' },
            'info': { icon: 'fas fa-info-circle', color: 'info' }
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
        setTimeout(() => removeToast(toast), 5000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    function removeToast(element) {
        element.classList.remove('show');
        setTimeout(() => element.remove(), 300);
    }

    /**
     * Auto-capitalize name input
     */
    function autoCapitalizeName() {
        const nameInput = document.getElementById('name');
        const cursorPosition = nameInput.selectionStart;
        
        // Capitalize first letter of each word
        nameInput.value = nameInput.value.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
        
        // Restore cursor position
        nameInput.setSelectionRange(cursorPosition, cursorPosition);
    }

    /**
     * Confirm photo deletion
     */
    function confirmDeletePhoto() {
        if (confirm('Are you sure you want to remove your profile photo?')) {
            document.getElementById('deletePhotoForm').submit();
        }
    }

    /**
     * Handle form submission with validation
     */
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('profileForm');
        const submitBtn = document.getElementById('submitBtn');

        // Check if there was an email_change_password error from server
        @error('email_change_password')
            const emailPasswordInput = document.getElementById('email_change_password');
            const emailPasswordGroup = document.getElementById('email-password-group');
            
            // Show the password group if hidden
            emailPasswordGroup.style.display = 'block';
            
            // Add error highlight animation
            emailPasswordInput.classList.add('field-error-highlight');
            emailPasswordInput.focus();
            
            // Show error toast
            showToast('error', '{{ $message }}');
            
            // Remove animation class after it completes
            setTimeout(() => {
                emailPasswordInput.classList.remove('field-error-highlight');
            }, 500);
        @enderror

        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const passwordConfirmation = document.getElementById('password_confirmation').value;
                const currentPassword = document.getElementById('current_password').value;
                const emailChangePassword = document.getElementById('email_change_password').value;

                // Validate email format
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    showToast('error', 'Please enter a valid email address');
                    document.getElementById('email').focus();
                    return false;
                }

                // Check if email changed and password is provided
                if (email !== originalEmail && !emailChangePassword) {
                    e.preventDefault();
                    showToast('error', 'Please enter your current password to change the email address');
                    document.getElementById('email_change_password').focus();
                    return false;
                }

                // If password is being changed, validate current password is provided
                if (password && !currentPassword) {
                    e.preventDefault();
                    showToast('error', 'Current password is required to change your password');
                    document.getElementById('current_password').focus();
                    return false;
                }

                // Check if passwords match
                if (password && password !== passwordConfirmation) {
                    e.preventDefault();
                    showToast('error', 'Passwords do not match');
                    document.getElementById('password_confirmation').focus();
                    return false;
                }

                // Check password strength if password is provided
                if (password) {
                    const hasUpperCase = /[A-Z]/.test(password);
                    const hasNumber = /[0-9]/.test(password);
                    const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
                    const hasMinLength = password.length >= 8;

                    if (!hasMinLength || !hasUpperCase || !hasNumber || !hasSymbol) {
                        e.preventDefault();
                        showToast('error', 'Password must be at least 8 characters with uppercase, numbers, and symbols');
                        document.getElementById('password').focus();
                        return false;
                    }
                }

                submitBtn.querySelector('.btn-text').style.display = 'none';
                submitBtn.querySelector('.btn-loader').style.display = 'inline';
                submitBtn.disabled = true;
            });
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection