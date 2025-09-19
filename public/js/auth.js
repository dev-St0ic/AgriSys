// Updated Auth Modal Functions with username-based signup
function openAuthModal(type = 'login') {
    const modal = document.getElementById('auth-modal');
    if (!modal) {
        console.error('Auth modal not found');
        return;
    }
    
    modal.style.display = 'flex';
    
    // Always show login form by default
    showLogInForm();
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeAuthModal() {
    const modal = document.getElementById('auth-modal');
    if (!modal) return;
    
    modal.style.display = 'none';
    
    // Reset forms
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form-submit');
    
    if (loginForm) loginForm.reset();
    if (signupForm) signupForm.reset();
    
    // Clear messages
    hideAuthMessages();
    
    // Clear any validation styling
    clearValidationErrors();
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
    
    // Always reset to login form when closing
    showLogInForm();
}

function showLogInForm() {
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const modalTitle = document.getElementById('auth-modal-title');
    
    if (loginForm) loginForm.style.display = 'block';
    if (signupForm) signupForm.style.display = 'none';
    
    if (modalTitle) modalTitle.textContent = 'LOG IN';
    
    hideAuthMessages();
    clearValidationErrors();
}

function showSignUpForm() {
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const modalTitle = document.getElementById('auth-modal-title');
    
    if (loginForm) loginForm.style.display = 'none';
    if (signupForm) signupForm.style.display = 'block';
    
    if (modalTitle) modalTitle.textContent = 'SIGN UP';
    
    hideAuthMessages();
    clearValidationErrors();
    
    // Focus first input
    setTimeout(() => {
        const firstInput = signupForm.querySelector('input');
        if (firstInput) firstInput.focus();
    }, 100);
}

// Username availability checker with debouncing
let usernameCheckTimeout;

function checkUsernameAvailability(username) {
    clearTimeout(usernameCheckTimeout);
    
    const usernameInput = document.getElementById('signup-username');
    const usernameStatus = document.querySelector('.username-status');
    
    if (!username || username.length < 3) {
        if (usernameStatus) {
            usernameStatus.innerHTML = '';
        }
        return;
    }
    
    // Show checking status
    if (usernameStatus) {
        usernameStatus.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Checking...</span>';
    }
    
    usernameCheckTimeout = setTimeout(() => {
        fetch('/auth/check-username', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ username: username })
        })
        .then(response => response.json())
        .then(data => {
            if (usernameStatus) {
                if (data.available) {
                    usernameStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Username available</span>';
                    usernameInput.classList.remove('is-invalid');
                    usernameInput.classList.add('is-valid');
                } else {
                    usernameStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Username already taken</span>';
                    usernameInput.classList.remove('is-valid');
                    usernameInput.classList.add('is-invalid');
                }
            }
        })
        .catch(error => {
            console.error('Error checking username:', error);
            if (usernameStatus) {
                usernameStatus.innerHTML = '<span class="text-muted"><i class="fas fa-exclamation-triangle"></i> Could not check availability</span>';
            }
        });
    }, 500); // Wait 500ms after user stops typing
}

// Simplified validation for username-based signup
function validateBasicSignupForm() {
    const username = document.getElementById('signup-username').value.trim();
    const email = document.getElementById('signup-email').value.trim();
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('signup-confirm-password').value;
    const agreeTerms = document.getElementById('agree-terms').checked;
    
    let isValid = true;
    let errors = [];
    
    // Username validation
    if (!username) {
        errors.push('Username is required');
        isValid = false;
    } else if (username.length < 3) {
        errors.push('Username must be at least 3 characters');
        isValid = false;
    } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        errors.push('Username can only contain letters, numbers, and underscores');
        isValid = false;
    }
    
    // Email validation
    if (!email) {
        errors.push('Email is required');
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.push('Please enter a valid email address');
        isValid = false;
    }
    
    // Password validation
    if (!password) {
        errors.push('Password is required');
        isValid = false;
    } else if (password.length < 8) {
        errors.push('Password must be at least 8 characters');
        isValid = false;
    }
    
    // Password confirmation
    if (password !== confirmPassword) {
        errors.push('Passwords do not match');
        isValid = false;
    }
    
    // Terms agreement
    if (!agreeTerms) {
        errors.push('You must agree to the Terms of Service and Privacy Policy');
        isValid = false;
    }
    
    // Check if username validation shows error
    const usernameInput = document.getElementById('signup-username');
    if (usernameInput.classList.contains('is-invalid')) {
        errors.push('Please choose a different username');
        isValid = false;
    }
    
    if (!isValid) {
        showAuthError(errors.join(', '));
    } else {
        hideAuthMessages();
    }
    
    return isValid;
}

// Password strength checker
function checkPasswordStrength(password) {
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    if (!strengthBar || !strengthText) return;
    
    let strength = 0;
    let strengthLabel = 'Too weak';
    
    // Length check
    if (password.length >= 8) strength++;
    
    // Lowercase letter
    if (/[a-z]/.test(password)) strength++;
    
    // Uppercase letter
    if (/[A-Z]/.test(password)) strength++;
    
    // Number
    if (/\d/.test(password)) strength++;
    
    // Special character
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    // Update strength indicator
    strengthBar.className = 'strength-fill';
    
    switch (strength) {
        case 0:
        case 1:
            strengthBar.classList.add('weak');
            strengthLabel = 'Weak';
            break;
        case 2:
            strengthBar.classList.add('fair');
            strengthLabel = 'Fair';
            break;
        case 3:
        case 4:
            strengthBar.classList.add('good');
            strengthLabel = 'Good';
            break;
        case 5:
            strengthBar.classList.add('strong');
            strengthLabel = 'Strong';
            break;
    }
    
    strengthText.textContent = `Password strength: ${strengthLabel}`;
}

// Password match checker
function checkPasswordMatch(password, confirmPassword) {
    const matchStatus = document.querySelector('.password-match-status');
    
    if (!matchStatus || !confirmPassword) {
        if (matchStatus) matchStatus.innerHTML = '';
        return;
    }
    
    if (password === confirmPassword) {
        matchStatus.className = 'password-match-status match';
        matchStatus.textContent = 'Passwords match';
    } else {
        matchStatus.className = 'password-match-status no-match';
        matchStatus.textContent = 'Passwords do not match';
    }
}

// Utility Functions
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const toggle = input.nextElementSibling;
    if (!toggle) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        toggle.textContent = 'Hide';
    } else {
        input.type = 'password';
        toggle.textContent = 'Show';
    }
}

// Show notification without emoji by default
function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
        </div>
    `;
    
    // Add styles if not exist
    if (!document.querySelector('#notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                padding: 16px 20px;
                z-index: 10000;
                transform: translateX(400px);
                transition: all 0.3s ease;
                border-left: 4px solid;
                min-width: 300px;
            }
            
            .notification.show {
                transform: translateX(0);
            }
            
            .notification-success {
                border-left-color: #28a745;
                background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
            }
            
            .notification-error {
                border-left-color: #dc3545;
                background: linear-gradient(135deg, #fff8f8 0%, #f5e8e8 100%);
            }
            
            .notification-warning {
                border-left-color: #ffc107;
                background: linear-gradient(135deg, #fffdf8 0%, #f5f2e8 100%);
            }
            
            .notification-content {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .notification-message {
                font-weight: 500;
                color: #333;
            }
        `;
        document.head.appendChild(styles);
    }
    
    // Add to page
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Google Authentication Functions
function signInWithGoogle() {
    console.log('Google Sign In clicked');
    showAuthError('Google Sign In will be implemented soon!');
}

function signUpWithGoogle() {
    console.log('Google Sign Up clicked');
    showAuthError('Google Sign Up will be implemented soon!');
}

// Message Functions
function showAuthError(message) {
    const errorDiv = document.getElementById('auth-error-message');
    const successDiv = document.getElementById('auth-success-message');
    
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'flex';
    }
    if (successDiv) {
        successDiv.style.display = 'none';
    }
}

function showAuthSuccess(message) {
    const successDiv = document.getElementById('auth-success-message');
    const errorDiv = document.getElementById('auth-error-message');
    
    if (successDiv) {
        successDiv.textContent = message;
        successDiv.style.display = 'flex';
    }
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

function hideAuthMessages() {
    const errorDiv = document.getElementById('auth-error-message');
    const successDiv = document.getElementById('auth-success-message');
    
    if (errorDiv) errorDiv.style.display = 'none';
    if (successDiv) successDiv.style.display = 'none';
}

function clearValidationErrors() {
    const inputs = document.querySelectorAll('.auth-form input');
    inputs.forEach(input => {
        input.classList.remove('error', 'invalid', 'valid', 'is-invalid', 'is-valid');
        input.style.borderColor = '';
    });
    
    const errorMessages = document.querySelectorAll('.field-error');
    errorMessages.forEach(msg => msg.remove());
    
    // Clear username status
    const usernameStatus = document.querySelector('.username-status');
    if (usernameStatus) {
        usernameStatus.innerHTML = '';
    }
}

// Form Submission Functions
function handleLoginSubmit(event) {
    event.preventDefault();
    
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('login-password').value;
    
    if (!username || !password) {
        showAuthError('Please fill in all fields');
        return false;
    }
    
    const form = event.target;
    const submitBtn = form.querySelector('.auth-submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    // Show loading state
    submitBtn.classList.add('loading');
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline-block';
    
    const formData = new FormData(form);
    
    fetch('/auth/login', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAuthSuccess('Login successful! Redirecting...');
            setTimeout(() => {
                window.location.href = data.redirect || '/dashboard';
            }, 1500);
        } else {
            showAuthError(data.message || 'Login failed. Please check your credentials.');
        }
    })
    .catch(error => {
        console.error('Login error:', error);
        showAuthError('An error occurred. Please try again.');
    })
    .finally(() => {
        // Reset loading state
        submitBtn.classList.remove('loading');
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
    });
    
    return false;
}

// Updated signup form submission with username-based signup
function handleSignupSubmit(event) {
    event.preventDefault();
    
    if (!validateBasicSignupForm()) {
        return false;
    }
    
    const form = event.target;
    const submitBtn = form.querySelector('.auth-submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    // Show loading state with "Creating..." text
    submitBtn.classList.add('loading');
    btnText.textContent = 'Creating...';
    btnText.style.display = 'inline';
    btnLoader.style.display = 'inline-block';
    submitBtn.disabled = true;
    
    // Get form data
    const formData = {
        username: document.getElementById('signup-username').value.trim(),
        email: document.getElementById('signup-email').value.trim(),
        password: document.getElementById('signup-password').value,
        password_confirmation: document.getElementById('signup-confirm-password').value,
        terms_accepted: document.getElementById('agree-terms').checked
    };
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    fetch('/auth/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success notification
            showNotification('success', 'Account created successfully! Your account has been added to our database.');
            
            // Show success message in modal
            showAuthSuccess('Account created! You can now log in with your credentials.');
            
            // Clear the form
            form.reset();
            
            // Clear username status
            const usernameStatus = document.querySelector('.username-status');
            if (usernameStatus) {
                usernameStatus.innerHTML = '';
            }
            
            // Switch to login form after delay
            setTimeout(() => {
                showLogInForm();
                hideAuthMessages();
                
                // Pre-fill username in login form
                const loginUsernameField = document.getElementById('username');
                if (loginUsernameField) {
                    loginUsernameField.value = formData.username;
                }
                
                // Show another notification
                showNotification('success', 'You can now log in with your new account!');
            }, 2000);
            
        } else {
            // Show error notification
            showNotification('error', data.message || 'Registration failed. Please try again.');
            showAuthError(data.message || 'Registration failed. Please try again.');
            
            // Handle field-specific errors
            if (data.errors) {
                handleValidationErrors(data.errors);
            }
        }
    })
    .catch(error => {
        console.error('Signup error:', error);
        const errorMessage = 'An error occurred. Please check your connection and try again.';
        showNotification('error', errorMessage);
        showAuthError(errorMessage);
    })
    .finally(() => {
        // Reset loading state
        submitBtn.classList.remove('loading');
        btnText.textContent = 'SIGN UP';
        btnLoader.style.display = 'none';
        submitBtn.disabled = false;
    });
    
    return false;
}

function handleValidationErrors(errors) {
    // Clear previous errors
    clearValidationErrors();
    
    // Show specific field errors
    const errorFields = Object.keys(errors);
    
    errorFields.forEach(field => {
        // Map API field names to form field names
        const fieldMapping = {
            'username': 'signup-username',
            'email': 'signup-email',
            'password': 'signup-password',
            'terms_accepted': 'agree-terms'
        };
        
        const fieldName = fieldMapping[field] || field;
        const input = document.getElementById(fieldName);
        
        if (input) {
            input.classList.add('error', 'is-invalid');
            input.style.borderColor = '#dc3545';
            
            // Create error message element
            const errorMsg = document.createElement('div');
            errorMsg.className = 'field-error';
            errorMsg.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            errorMsg.style.color = '#dc3545';
            errorMsg.style.fontSize = '12px';
            errorMsg.style.marginTop = '4px';
            
            // Insert after the input's parent element
            const parent = input.closest('.form-group') || input.parentElement;
            parent.appendChild(errorMsg);
        }
    });
}

// Forgot Password Function
function showForgotPassword() {
    showNotification('info', 'Forgot password feature will be available soon!');
    console.log('Show forgot password modal');
}

// Event Listeners Setup
document.addEventListener('DOMContentLoaded', function() {
    // Login form submission
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }
    
    // Signup form submission
    const signupForm = document.getElementById('signup-form-submit');
    if (signupForm) {
        signupForm.addEventListener('submit', handleSignupSubmit);
    }
    
    // Username availability checker
    const usernameInput = document.getElementById('signup-username');
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            checkUsernameAvailability(this.value);
        });
    }
    
    // Password strength checker
    const passwordInput = document.getElementById('signup-password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    }
    
    // Password confirmation checker
    const confirmPasswordInput = document.getElementById('signup-confirm-password');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = document.getElementById('signup-password').value;
            checkPasswordMatch(password, this.value);
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('auth-modal');
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeAuthModal();
            }
        });
    }
    
    // ESC key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('auth-modal');
            if (modal && modal.style.display !== 'none') {
                closeAuthModal();
            }
        }
    });
});

// Global functions to be called from HTML
window.openAuthModal = openAuthModal;
window.closeAuthModal = closeAuthModal;
window.showLogInForm = showLogInForm;
window.showSignUpForm = showSignUpForm;
window.togglePasswordVisibility = togglePasswordVisibility;
window.signInWithGoogle = signInWithGoogle;
window.signUpWithGoogle = signUpWithGoogle;
window.showForgotPassword = showForgotPassword;