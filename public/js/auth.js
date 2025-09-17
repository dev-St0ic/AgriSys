// ==========================================
// UPDATED AUTH MODAL JAVASCRIPT
// ==========================================

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
    const signupForm = document.getElementById('signup-form');
    
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
    
    if (modalTitle) modalTitle.textContent = 'Welcome Back';
    
    hideAuthMessages();
    clearValidationErrors();
}

function showSignUpForm() {
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    const modalTitle = document.getElementById('auth-modal-title');
    
    if (loginForm) loginForm.style.display = 'none';
    if (signupForm) signupForm.style.display = 'block';
    
    if (modalTitle) modalTitle.textContent = 'Create Your Account';
    
    hideAuthMessages();
    clearValidationErrors();
}

function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const toggle = input.nextElementSibling;
    if (!toggle) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        toggle.textContent = '🙈';
    } else {
        input.type = 'password';
        toggle.textContent = '👁️';
    }
}

// Google Authentication Functions
function signInWithGoogle() {
    // Template function for Google Sign In
    console.log('Google Sign In clicked');
    showAuthError('Google Sign In will be implemented soon!');
    
    // TODO: Implement Google Sign In API
    // Example implementation:
    /*
    gapi.load('auth2', function() {
        gapi.auth2.init({
            client_id: 'YOUR_GOOGLE_CLIENT_ID.googleusercontent.com'
        }).then(function() {
            const authInstance = gapi.auth2.getAuthInstance();
            authInstance.signIn().then(function(googleUser) {
                const profile = googleUser.getBasicProfile();
                const id_token = googleUser.getAuthResponse().id_token;
                
                // Send token to your server for verification
                handleGoogleSignIn(id_token, profile);
            });
        });
    });
    */
}

function signUpWithGoogle() {
    // Template function for Google Sign Up
    console.log('Google Sign Up clicked');
    showAuthError('Google Sign Up will be implemented soon!');
    
    // TODO: Implement Google Sign Up API
    // This would typically be the same as sign in, but you might handle new users differently
}

function handleGoogleSignIn(idToken, profile) {
    // Template function to handle Google authentication response
    // Send the ID token to your Laravel backend for verification
    
    /*
    fetch('/auth/google', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            id_token: idToken,
            name: profile.getName(),
            email: profile.getEmail(),
            image: profile.getImageUrl()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAuthSuccess('Google sign in successful! Redirecting...');
            setTimeout(() => {
                window.location.href = data.redirect || '/dashboard';
            }, 1500);
        } else {
            showAuthError(data.message || 'Google sign in failed');
        }
    })
    .catch(error => {
        console.error('Google sign in error:', error);
        showAuthError('An error occurred with Google sign in');
    });
    */
}

// Message Functions
function showAuthError(message) {
    const errorDiv = document.getElementById('auth-error-message');
    const successDiv = document.getElementById('auth-success-message');
    
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
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
        successDiv.style.display = 'block';
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
    // Remove any validation error classes from inputs
    const inputs = document.querySelectorAll('.auth-form input');
    inputs.forEach(input => {
        input.classList.remove('error', 'invalid');
        input.style.borderColor = '';
    });
    
    // Clear any existing error messages under inputs
    const errorMessages = document.querySelectorAll('.field-error');
    errorMessages.forEach(msg => msg.remove());
}

// Form Validation Functions
function validateLoginForm() {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('login-password').value;
    
    let isValid = true;
    let errors = [];
    
    if (!username) {
        errors.push('Username or email is required');
        isValid = false;
    }
    
    if (!password) {
        errors.push('Password is required');
        isValid = false;
    }
    
    if (!isValid) {
        showAuthError(errors.join(', '));
    }
    
    return isValid;
}

function validateSignUpForm() {
    const firstname = document.getElementById('signup-firstname').value.trim();
    const lastname = document.getElementById('signup-lastname').value.trim();
    const role = document.getElementById('signup-role').value;
    const contact = document.getElementById('signup-contact').value.trim();
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('signup-confirm-password').value;
    const agreeTerms = document.getElementById('agree-terms').checked;
    
    let isValid = true;
    let errors = [];
    
    if (!firstname) {
        errors.push('First name is required');
        isValid = false;
    }
    
    if (!lastname) {
        errors.push('Last name is required');
        isValid = false;
    }
    
    if (!role) {
        errors.push('Role selection is required');
        isValid = false;
    }
    
    if (!contact) {
        errors.push('Contact number is required');
        isValid = false;
    } else if (!/^[0-9+\-\s()]+$/.test(contact)) {
        errors.push('Please enter a valid contact number');
        isValid = false;
    }
    
    if (!password) {
        errors.push('Password is required');
        isValid = false;
    } else if (password.length < 8) {
        errors.push('Password must be at least 8 characters long');
        isValid = false;
    }
    
    if (password !== confirmPassword) {
        errors.push('Passwords do not match');
        isValid = false;
    }
    
    if (!agreeTerms) {
        errors.push('You must agree to the Terms of Service and Privacy Policy');
        isValid = false;
    }
    
    if (!isValid) {
        showAuthError(errors.join(', '));
    }
    
    return isValid;
}

// Form Submission Functions
function handleLoginSubmit(event) {
    event.preventDefault();
    
    if (!validateLoginForm()) {
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
        showAuthError('An error occurred during login. Please try again.');
    })
    .finally(() => {
        // Reset loading state
        submitBtn.classList.remove('loading');
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
    });
    
    return false;
}

function handleSignUpSubmit(event) {
    event.preventDefault();
    
    if (!validateSignUpForm()) {
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
    
    // TODO: Replace with your actual registration endpoint
    fetch('/auth/register', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAuthSuccess('Account created successfully! Please check your email to verify your account.');
            // Optionally switch to login form after a delay
            setTimeout(() => {
                showLogInForm();
            }, 3000);
        } else {
            showAuthError(data.message || 'Registration failed. Please try again.');
        }
    })
    .catch(error => {
        console.error('Registration error:', error);
        showAuthError('An error occurred during registration. Please try again.');
    })
    .finally(() => {
        // Reset loading state
        submitBtn.classList.remove('loading');
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
    });
    
    return false;
}

// Forgot Password Function
function showForgotPassword() {
    // TODO: Implement forgot password functionality
    showAuthError('Forgot password feature will be implemented soon!');
    
    /*
    // Example implementation:
    const email = prompt('Please enter your email address:');
    if (email && email.includes('@')) {
        fetch('/auth/forgot-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAuthSuccess('Password reset link sent to your email!');
            } else {
                showAuthError(data.message || 'Failed to send reset link');
            }
        })
        .catch(error => {
            console.error('Forgot password error:', error);
            showAuthError('An error occurred. Please try again.');
        });
    }
    */
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Add form submit event listeners
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }
    
    if (signupForm) {
        signupForm.addEventListener('submit', handleSignUpSubmit);
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
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('auth-modal');
            if (modal && modal.style.display === 'flex') {
                closeAuthModal();
            }
        }
    });
    
    // Real-time password confirmation validation
    const confirmPasswordInput = document.getElementById('signup-confirm-password');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = document.getElementById('signup-password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#e53e3e';
            } else {
                this.style.borderColor = '';
            }
        });
    }
    
    // Contact number formatting
    const contactInput = document.getElementById('signup-contact');
    if (contactInput) {
        contactInput.addEventListener('input', function() {
            // Remove any non-numeric characters except +, -, (, ), and spaces
            let value = this.value.replace(/[^0-9+\-\s()]/g, '');
            this.value = value;
        });
    }
});

// // Navigation Functions (referenced in your header)
// function goHome(event) {
//     event.preventDefault();
//     window.location.href = '/';
// }

// function openRSBSAForm(event) {
//     event.preventDefault();
//     // TODO: Implement RSBSA form opening logic
//     console.log('Opening RSBSA form');
// }

// function openFormSeedlings(event) {
//     event.preventDefault();
//     // TODO: Implement Seedlings form opening logic
//     console.log('Opening Seedlings form');
// }

// function openFormFishR(event) {
//     event.preventDefault();
//     // TODO: Implement FishR form opening logic
//     console.log('Opening FishR form');
// }

// function openFormBoatR(event) {
//     event.preventDefault();
//     // TODO: Implement BoatR form opening logic
//     console.log('Opening BoatR form');
// }

// function openFormTraining(event) {
//     event.preventDefault();
//     // TODO: Implement Training form opening logic
//     console.log('Opening Training form');
// }