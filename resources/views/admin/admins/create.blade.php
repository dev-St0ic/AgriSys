@extends('layouts.app')

@section('title', 'Create Admin - AgriSys')
@section('page-title', 'Create New Admin')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-center">
                        <i class="fas fa-user-plus me-2"></i>Add New Admin User
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.admins.store') }}" id="createAdminForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Full Name
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" 
                                    onkeyup="autoCapitalizeName()" required autofocus>
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
                                    id="email" name="email" value="{{ old('email') }}" 
                                    onkeyup="validateEmail()" onblur="validateEmail()" required>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="email-feedback" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">
                                <i class="fas fa-user-shield me-2"></i>Role
                            </label>
                            <input type="hidden" name="role" value="admin">
                            <div class="form-control bg-light d-flex align-items-center" style="cursor: default;">
                                <span class="badge bg-primary me-2">Admin</span>
                                <span class="text-muted small">Can access admin dashboard and manage basic operations</span>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" 
                                        onkeyup="validatePasswordStrength()" required>
                                    <button type="button" class="btn btn-outline-secondary toggle-password" 
                                        onclick="togglePasswordVisibility('password')">
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
                                    <i class="fas fa-lock me-2"></i>Confirm Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" onkeyup="validatePasswordMatch()" required>
                                    <button type="button" class="btn btn-outline-secondary toggle-password" 
                                        onclick="togglePasswordVisibility('password_confirmation')">
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
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="btn-text"><i class="fas fa-save me-2"></i>Create Admin</span>
                                <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Creating...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Password toggle button styles */
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
    </style>

    <script>
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

        /**
         * Handle form submission with spinner and validation
         */
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createAdminForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    const passwordConfirmation = document.getElementById('password_confirmation').value;

                    // Validate email format
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        e.preventDefault();
                        showToast('error', 'Please enter a valid email address');
                        return false;
                    }

                    // Check if passwords match
                    if (password !== passwordConfirmation) {
                        e.preventDefault();
                        showToast('error', 'Passwords do not match');
                        return false;
                    }

                    // Check password strength
                    const hasUpperCase = /[A-Z]/.test(password);
                    const hasNumber = /[0-9]/.test(password);
                    const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
                    const hasMinLength = password.length >= 8;

                    if (!hasMinLength || !hasUpperCase || !hasNumber || !hasSymbol) {
                        e.preventDefault();
                        showToast('error', 'Password must be at least 8 characters with uppercase, numbers, and symbols');
                        return false;
                    }

                    // Show loading state
                    submitBtn.querySelector('.btn-text').style.display = 'none';
                    submitBtn.querySelector('.btn-loader').style.display = 'inline';
                    submitBtn.disabled = true;
                });
            }
        });

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
    </script>
@endsection