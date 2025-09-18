// Auth Modal Functions
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

// Validation Functions
function validateSignupForm() {
    const firstname = document.getElementById('signup-firstname').value.trim();
    const lastname = document.getElementById('signup-lastname').value.trim();
    const email = document.getElementById('signup-email').value.trim();
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('signup-confirm-password').value;
    const agreeTerms = document.getElementById('agree-terms').checked;
    
    let isValid = true;
    let errors = [];
    
    // First name validation
    if (!firstname) {
        errors.push('First name is required');
        isValid = false;
    }
    
    // Last name validation
    if (!lastname) {
        errors.push('Last name is required');
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

// Google Authentication Functions
function signInWithGoogle() {
    console.log('Google Sign In clicked');
    showAuthError('Google Sign In will be implemented soon!');
    
    // TODO: Implement Google Sign In API
}

function signUpWithGoogle() {
    console.log('Google Sign Up clicked');
    showAuthError('Google Sign Up will be implemented soon!');
    
    // TODO: Implement Google Sign Up API
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
        input.classList.remove('error', 'invalid', 'valid');
        input.style.borderColor = '';
    });
    
    const errorMessages = document.querySelectorAll('.field-error');
    errorMessages.forEach(msg => msg.remove());
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
    
    // TODO: Replace with your actual login endpoint
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

function handleSignupSubmit(event) {
    event.preventDefault();
    
    if (!validateSignupForm()) {
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
    
    // Create FormData
    const signupData = new FormData(form);
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        signupData.append('_token', csrfToken.content);
    }
    
    // TODO: Replace with your actual signup endpoint
    fetch('/auth/register', {
        method: 'POST',
        body: signupData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAuthSuccess('Account created successfully! Please check your email to verify your account.');
            setTimeout(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    closeAuthModal();
                    showLogInForm();
                }
            }, 2000);
        } else {
            showAuthError(data.message || 'Registration failed. Please try again.');
            // If there are field-specific errors, handle them
            if (data.errors) {
                handleValidationErrors(data.errors);
            }
        }
    })
    .catch(error => {
        console.error('Signup error:', error);
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

function handleValidationErrors(errors) {
    // Show specific field errors
    const errorFields = Object.keys(errors);
    
    errorFields.forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('error');
            
            // Create error message element
            const errorMsg = document.createElement('div');
            errorMsg.className = 'field-error';
            errorMsg.textContent = errors[field][0]; // Laravel returns arrays
            errorMsg.style.color = '#ef4444';
            errorMsg.style.fontSize = '12px';
            errorMsg.style.marginTop = '4px';
            
            // Insert after the input
            input.parentElement.insertBefore(errorMsg, input.nextSibling);
        }
    });
}

// Forgot Password Function
function showForgotPassword() {
    // TODO: Implement forgot password modal or redirect
    showAuthError('Forgot password feature will be implemented soon!');
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