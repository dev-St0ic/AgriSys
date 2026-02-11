@extends('layouts.app')

@section('title', 'Edit Admin - AgriSys')
@section('page-title', 'Edit Admin User')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-center">
                        <i class="fas fa-user-edit me-2"></i>Edit Admin User: {{ $admin->name }}
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.admins.update', $admin) }}" id="editAdminForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-user me-2"></i>Full Name
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $admin->name) }}" required
                                            autofocus>
                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Email Address
                                        </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="role" class="form-label">
                                        <i class="fas fa-user-shield me-2"></i>Role
                                    </label>

                                    @if($admin->isSuperAdmin())
                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-crown me-2"></i><strong>Super Admin Account</strong><br>
                                            This user is the system's Super Admin and cannot be changed.
                                        </div>
                                        <input type="hidden" name="role" value="superadmin">
                                        <input type="text" class="form-control" value="Super Admin" disabled readonly>
                                    @else
                                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                            <option value="" disabled>Select Role</option>
                                            <option value="admin" {{ old('role', $admin->role) === 'admin' ? 'selected' : '' }}>
                                                Admin
                                            </option>
                                        </select>
                                    @endif

                                    @error('role')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        <strong>Admin:</strong> Can access admin dashboard and manage basic operations
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Leave password fields empty if you don't want to change the password.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>New Password (Optional)
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Min 8 characters with uppercase, numbers, symbols" 
                                        onkeyup="validatePasswordStrength()">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" 
                                        data-target="password" onclick="togglePasswordVisibility('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Must contain at least 8 characters, including uppercase, numbers, and symbols
                                </small>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="password-strength-feedback" class="mt-2"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Confirm New Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" onkeyup="validatePasswordMatch()">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" 
                                        data-target="password_confirmation" onclick="togglePasswordVisibility('password_confirmation')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small id="password-match-feedback" class="d-block mt-2"></small>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                            <div>
                                <a href="{{ route('admin.admins.show', $admin) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span class="btn-text"><i class="fas fa-sync me-2"></i>Update Admin</span>
                                    <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Updating...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-lg {
            height: 5rem;
            width: 5rem;
        }

        .avatar-title {
            align-items: center;
            background-color: #6c757d;
            color: #fff;
            display: flex;
            font-size: 1.5rem;
            font-weight: 500;
            height: 100%;
            justify-content: center;
            width: 100%;
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

        /* Password toggle button styles */
        .toggle-password {
            border-right: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .toggle-password:hover {
            background-color: #e9ecef;
        }

        .input-group .form-control:focus + .toggle-password {
            border-color: #86b7fe;
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

        /* Form controls with icon */
        .form-label i {
            opacity: 0.7;
        }
    </style>

    <script>
        /**
         * Toggle password visibility
         */
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const button = event.currentTarget;
            const icon = button.querySelector('i');

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

        // Handle form submission with spinner and validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editAdminForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const passwordConfirmation = document.getElementById('password_confirmation').value;

                    // Check if passwords match
                    if (password && password !== passwordConfirmation) {
                        e.preventDefault();
                        showToast('error', 'Passwords do not match');
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
                            return false;
                        }
                    }

                    submitBtn.querySelector('.btn-text').style.display = 'none';
                    submitBtn.querySelector('.btn-loader').style.display = 'inline';
                    submitBtn.disabled = true;
                });
            }
        });
    </script>
@endsection