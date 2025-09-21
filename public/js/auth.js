// Enhanced Auth Modal Functions with Profile Verification and Status Management

// ==============================================
// AUTH MODAL FUNCTIONS
// ==============================================

function openAuthModal(type = 'login') {
    const modal = document.getElementById('auth-modal');
    if (!modal) {
        console.error('Auth modal not found');
        return;
    }
    
    modal.style.display = 'flex';
    showLogInForm();
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
    
    hideAuthMessages();
    clearValidationErrors();
    resetButtonStates();
    document.body.style.overflow = 'auto';
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
    resetButtonStates();
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
    resetButtonStates();
    
    setTimeout(() => {
        const firstInput = signupForm.querySelector('input');
        if (firstInput) firstInput.focus();
    }, 100);
}

// ==============================================
// BUTTON STATE MANAGEMENT
// ==============================================

function setButtonLoading(button, loadingText) {
    if (!button) return;
    
    const btnText = button.querySelector('.btn-text');
    const btnLoader = button.querySelector('.btn-loader');
    
    // Store original text if not already stored
    if (!button.dataset.originalText) {
        button.dataset.originalText = btnText ? btnText.textContent : button.textContent;
    }
    
    button.classList.add('loading');
    button.disabled = true;
    
    if (btnText && btnLoader) {
        btnText.textContent = loadingText;
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none'; // Remove spinner, just show text
    } else {
        button.textContent = loadingText;
    }
}

function resetButtonState(button) {
    if (!button) return;
    
    const btnText = button.querySelector('.btn-text');
    const btnLoader = button.querySelector('.btn-loader');
    const originalText = button.dataset.originalText;
    
    button.classList.remove('loading');
    button.disabled = false;
    
    if (btnText && btnLoader) {
        btnText.textContent = originalText || 'Submit';
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
    } else {
        button.textContent = originalText || 'Submit';
    }
}

function resetButtonStates() {
    // Reset all auth buttons
    const buttons = document.querySelectorAll('.auth-submit-btn, .verification-submit-btn');
    buttons.forEach(resetButtonState);
}

// ==============================================
// ENHANCED PROFILE BUTTON STATE MANAGEMENT
// ==============================================

/**
 * Updates the profile verification button based on current user status
 * Handles all status transitions including rejected → retry verification
 */
function refreshProfileVerifyButton() {
    const verifyBtn = document.getElementById('verify-action-btn');
    if (!verifyBtn || !window.userData) return;

    const status = (window.userData.status || '').toLowerCase();
    
    // Remove all existing classes and states
    verifyBtn.classList.remove('pending', 'verified', 'rejected');
    verifyBtn.disabled = false;
    verifyBtn.onclick = null; // Clear existing handler
    
    switch (status) {
        case 'verified':
        case 'approved':
            // Verified state: green, disabled, with checkmark
            verifyBtn.disabled = true;
            verifyBtn.classList.add('verified');
            verifyBtn.innerHTML = '<span class="btn-icon">✅</span> Verified';
            break;
            
        case 'pending':
        case 'pending_verification':
            // Pending state: neutral colors, disabled, with clock
            verifyBtn.disabled = true;
            verifyBtn.classList.add('pending');
            verifyBtn.innerHTML = '<span class="btn-icon">⏳</span> Pending Verification';
            break;
            
        case 'rejected':
            // Rejected state: can retry verification (orange/amber styling)
            verifyBtn.disabled = false;
            verifyBtn.classList.add('rejected');
            verifyBtn.innerHTML = '<span class="btn-icon">🔄</span> Retry Verification';
            verifyBtn.onclick = () => showVerificationModal();
            break;
            
        case 'unverified':
        case 'active':
        case '':
        case null:
        case undefined:
        default:
            // Unverified/default state: can start verification
            verifyBtn.disabled = false;
            verifyBtn.classList.remove('pending', 'verified', 'rejected');
            verifyBtn.innerHTML = '<span class="btn-icon">✅</span> Verify Now';
            verifyBtn.onclick = () => showVerificationModal();
            break;
    }
    
    console.log(`Profile verify button updated for status: ${status}`);
}

// ==============================================
// USER PROFILE DROPDOWN FUNCTIONS
// ==============================================

function toggleUserDropdown() {
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// ==============================================
// PROFILE MODAL FUNCTIONS
// ==============================================

function showProfileModal() {
    const modal = document.getElementById('profile-modal');
    if (!modal) {
        console.error('Profile modal not found');
        return;
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    loadProfileData();
    
    // Close user dropdown
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
    }
}

function closeProfileModal() {
    const modal = document.getElementById('profile-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function loadProfileData() {
    // Load recent activity (this would normally come from your backend)
    const activityList = document.getElementById('recent-activity-list');
    if (activityList && window.userData) {
        // You can populate this with real data from your backend
        const mockActivity = [
            {
                icon: '📝',
                text: 'Submitted RSBSA Registration',
                date: '2 days ago'
            },
            {
                icon: '✅',
                text: 'Seedling Request Approved',
                date: '1 week ago'
            },
            {
                icon: '👤',
                text: 'Profile Updated',
                date: '2 weeks ago'
            }
        ];

        activityList.innerHTML = mockActivity.map(activity => `
            <div class="activity-item">
                <div class="activity-icon">${activity.icon}</div>
                <div class="activity-content">
                    <div class="activity-text">${activity.text}</div>
                    <div class="activity-date">${activity.date}</div>
                </div>
            </div>
        `).join('');
    }
}

function editProfile() {
    showNotification('info', 'Profile editing feature coming soon!');
}

function changePassword() {
    showNotification('info', 'Password change feature coming soon!');
}

// ==============================================
// PROFILE VERIFICATION MODAL FUNCTIONS
// ==============================================

function showVerificationModal() {
    const modal = document.getElementById('verification-modal');
    if (!modal) {
        console.error('Verification modal not found');
        return;
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Close profile modal if open
    closeProfileModal();
}

function closeVerificationModal() {
    const modal = document.getElementById('verification-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Reset form
    const form = document.getElementById('verification-form');
    if (form) {
        form.reset();
    }
    
    // Clear preview images
    clearImagePreviews();
    resetButtonStates();
}

function clearImagePreviews() {
    const previews = document.querySelectorAll('.image-preview');
    previews.forEach(preview => {
        preview.innerHTML = '';
        preview.style.display = 'none';
    });
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">`;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
        preview.style.display = 'none';
    }
}

// ==============================================
// UPDATED VERIFICATION FORM HANDLER - ALIGNED WITH BACKEND
// ==============================================

function handleVerificationSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = form.querySelector('.verification-submit-btn');
    
    // UPDATED: Validation to match backend requirements exactly
    const requiredFields = [
        { name: 'firstName', label: 'First Name' },
        { name: 'lastName', label: 'Last Name' },
        { name: 'role', label: 'Role' },
        { name: 'contactNumber', label: 'Contact Number' },
        { name: 'dateOfBirth', label: 'Date of Birth' }, // ADDED: Required by backend
        { name: 'barangay', label: 'Barangay' },
        { name: 'completeAddress', label: 'Complete Address' },
        { name: 'idFront', label: 'ID Front', type: 'file' },
        { name: 'idBack', label: 'ID Back', type: 'file' },
        { name: 'locationProof', label: 'Location Proof', type: 'file' }
    ];
    
    let isValid = true;
    let missingFields = [];
    
    requiredFields.forEach(field => {
        const input = form.querySelector(`[name="${field.name}"]`);
        if (!input) {
            console.error(`Field ${field.name} not found in form`);
            return;
        }
        
        if (field.type === 'file') {
            if (!input.files || !input.files.length) {
                isValid = false;
                missingFields.push(field.label);
            }
        } else {
            if (!input.value || !input.value.trim()) {
                isValid = false;
                missingFields.push(field.label);
            }
        }
    });
    
    // Additional validations
    const dateOfBirth = form.querySelector('[name="dateOfBirth"]').value;
    if (dateOfBirth) {
        const birthDate = new Date(dateOfBirth);
        const today = new Date();
        const age = Math.floor((today - birthDate) / (365.25 * 24 * 60 * 60 * 1000));
        
        if (age < 18) {
            isValid = false;
            showNotification('error', 'You must be at least 18 years old to register.');
            return false;
        }
        
        if (age > 100) {
            isValid = false;
            showNotification('error', 'Please enter a valid date of birth.');
            return false;
        }
    }
    
    const contactNumber = form.querySelector('[name="contactNumber"]').value;
    if (contactNumber) {
        // Philippine mobile number validation (09XXXXXXXXX or +639XXXXXXXXX)
        const phoneRegex = /^(\+639|09)\d{9}$/;
        if (!phoneRegex.test(contactNumber)) {
            isValid = false;
            showNotification('error', 'Please enter a valid Philippine mobile number (09XXXXXXXXX).');
            return false;
        }
    }
    
    if (!isValid) {
        showNotification('error', `Please complete all required fields: ${missingFields.join(', ')}`);
        return false;
    }
    
    // Set button to loading state
    setButtonLoading(submitBtn, 'Submitting Verification...');
    
    // Create FormData for file upload - EXACTLY as backend expects
    const formData = new FormData();
    
    // Add form fields with exact names expected by backend
    formData.append('firstName', form.querySelector('[name="firstName"]').value.trim());
    formData.append('lastName', form.querySelector('[name="lastName"]').value.trim());
    formData.append('middleName', form.querySelector('[name="middleName"]').value.trim());
    formData.append('extensionName', form.querySelector('[name="extensionName"]').value.trim());
    formData.append('role', form.querySelector('[name="role"]').value);
    formData.append('contactNumber', form.querySelector('[name="contactNumber"]').value.trim());
    formData.append('dateOfBirth', form.querySelector('[name="dateOfBirth"]').value);
    formData.append('barangay', form.querySelector('[name="barangay"]').value);
    formData.append('completeAddress', form.querySelector('[name="completeAddress"]').value.trim());
    
    // Add file uploads - EXACT names expected by backend
    const idFrontFile = form.querySelector('[name="idFront"]').files[0];
    const idBackFile = form.querySelector('[name="idBack"]').files[0];
    const locationProofFile = form.querySelector('[name="locationProof"]').files[0];
    
    if (idFrontFile) formData.append('idFront', idFrontFile);
    if (idBackFile) formData.append('idBack', idBackFile);
    if (locationProofFile) formData.append('locationProof', locationProofFile);
    
    // Debug logging
    console.log('Submitting verification form with data:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + (pair[1] instanceof File ? `File: ${pair[1].name}` : pair[1]));
    }
    
    // Submit to backend
    fetch('/auth/verify-profile', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
            // Note: Don't set Content-Type header for FormData, browser sets it automatically with boundary
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Server response:', data);
        
        if (data.success) {
            // Update local user status so UI stays consistent
            if (window.userData) {
                window.userData.status = 'pending';
            }

            // Refresh the profile verify button to reflect new status
            refreshProfileVerifyButton();

            showNotification('success', data.message || 'Verification submitted successfully!');

            // Start polling for approval
            maybeStartVerificationPoll();

            // Close modal shortly after showing confirmation
            setTimeout(() => {
                closeVerificationModal();
            }, 1000);
         } else {
             console.error('Verification failed:', data);
             let errorMessage = data.message || 'Verification submission failed';
             
             // Handle validation errors
             if (data.errors) {
                 const errorMessages = Object.values(data.errors).flat();
                 errorMessage = errorMessages.join(', ');
             }
             
             showNotification('error', errorMessage);
             resetButtonState(submitBtn);
         }
     })
    .catch(error => {
        console.error('Verification error:', error);
        showNotification('error', 'Network error. Please check your connection and try again.');
        resetButtonState(submitBtn);
    });
    
    return false;
}

// ==============================================
// MODAL-BASED USER FUNCTIONS
// ==============================================

function showMyApplicationsModal() {
    const modal = document.getElementById('applications-modal');
    if (!modal) {
        console.error('Applications modal not found');
        return;
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    loadUserApplicationsInModal();
    
    // Close user dropdown
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
    }
}

function closeApplicationsModal() {
    const modal = document.getElementById('applications-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function loadUserApplicationsInModal() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;
    
    // Check if user is logged in
    if (!window.userData) {
        grid.innerHTML = `
            <div class="empty-applications">
                <h4>Please Log In</h4>
                <p>You need to be logged in to view your applications.</p>
                <button class="quick-action-btn" onclick="closeApplicationsModal(); openAuthModal('login');">
                    <span>🔐</span> Log In
                </button>
            </div>
        `;
        return;
    }
    
    // Show loading state
    grid.innerHTML = `
        <div class="loading-state">
            <div class="loader"></div>
            <p>Loading your applications...</p>
        </div>
    `;
    
    // Try to fetch real data, fallback to mock data
    fetch('/api/user/applications', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.applications) {
            renderApplicationsInModal(data.applications);
        } else {
            // Fallback to mock data for development
            renderMockApplicationsInModal();
        }
    })
    .catch(error => {
        console.log('Loading mock applications for development');
        renderMockApplicationsInModal();
    });
}

function renderApplicationsInModal(applications) {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;
    
    if (applications.length === 0) {
        grid.innerHTML = `
            <div class="empty-applications">
                <h4>No Applications Yet</h4>
                <p>You haven't submitted any applications yet. Browse our services to get started!</p>
                <button class="quick-action-btn" onclick="closeApplicationsModal(); document.getElementById('services').scrollIntoView({ behavior: 'smooth' });">
                    <span>🌾</span> Browse Services
                </button>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = applications.map(app => `
        <div class="application-card">
            <h4>${app.type}</h4>
            <p>${app.description || 'Application submitted successfully'}</p>
            <div class="application-status status-${app.status.toLowerCase().replace(' ', '_')}">
                ${app.status.charAt(0).toUpperCase() + app.status.slice(1).replace('_', ' ')}
            </div>
            <div class="application-date">
                Submitted: ${formatApplicationDate(app.date)}
            </div>
        </div>
    `).join('');
}

function renderMockApplicationsInModal() {
    const mockApplications = [
        {
            id: 1,
            type: 'Seedlings Request',
            date: '2025-01-15',
            status: 'pending',
            description: 'Request for vegetable seedlings - tomatoes, eggplant, and pepper varieties'
        },
        {
            id: 2,
            type: 'FishR Registration',
            date: '2025-01-10',
            status: 'approved',
            description: 'Fisherfolk registration for coastal fishing activities'
        },
        {
            id: 3,
            type: 'Training Request',
            date: '2025-01-08',
            status: 'processing',
            description: 'Agricultural training program on sustainable farming practices'
        },
        {
            id: 4,
            type: 'RSBSA Registration',
            date: '2025-01-05',
            status: 'approved',
            description: 'Registry System for Basic Sectors in Agriculture enrollment'
        },
        {
            id: 5,
            type: 'BoatR Registration',
            date: '2025-01-03',
            status: 'rejected',
            description: 'Fishing boat registration - requires additional documentation'
        }
    ];
    
    renderApplicationsInModal(mockApplications);
}

function formatApplicationDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function accountSettings() {
    // Open account settings in new tab
    window.open('/account/settings', '_blank');
    
    // Close dropdown
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
    }
}

function logoutUser() {
    if (confirm('Are you sure you want to log out?')) {
        fetch('/auth/logout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', 'Successfully logged out!');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification('error', 'Logout failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Logout error:', error);
            // Fallback: reload page anyway
            window.location.reload();
        });
    }
}

// ==============================================
// USERNAME AVAILABILITY CHECKER
// ==============================================

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
    
    if (usernameStatus) {
        usernameStatus.innerHTML = '<span class="text-info">Checking...</span>';
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
                    usernameStatus.innerHTML = '<span class="text-success">✓ Username available</span>';
                    usernameInput.classList.remove('is-invalid');
                    usernameInput.classList.add('is-valid');
                } else {
                    usernameStatus.innerHTML = '<span class="text-danger">✗ Username already taken</span>';
                    usernameInput.classList.remove('is-valid');
                    usernameInput.classList.add('is-invalid');
                }
            }
        })
        .catch(error => {
            console.error('Error checking username:', error);
            if (usernameStatus) {
                usernameStatus.innerHTML = '<span class="text-muted">⚠ Could not check availability</span>';
            }
        });
    }, 500);
}

// ==============================================
// FORM VALIDATION
// ==============================================

function validateBasicSignupForm() {
    const username = document.getElementById('signup-username').value.trim();
    const email = document.getElementById('signup-email').value.trim();
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('signup-confirm-password').value;
    const agreeTerms = document.getElementById('agree-terms').checked;
    
    let isValid = true;
    let errors = [];
    
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
    
    if (!email) {
        errors.push('Email is required');
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.push('Please enter a valid email address');
        isValid = false;
    }
    
    if (!password) {
        errors.push('Password is required');
        isValid = false;
    } else if (password.length < 8) {
        errors.push('Password must be at least 8 characters');
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

function checkPasswordStrength(password) {
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    if (!strengthBar || !strengthText) return;
    
    let strength = 0;
    let strengthLabel = 'Too weak';
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
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

// ==============================================
// FORM RESET FUNCTION
// ==============================================

function resetSignupForm() {
    const form = document.getElementById('signup-form-submit');
    if (form) {
        form.reset();
    }
    
    // Clear all validation states
    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid', 'valid', 'error');
        input.style.borderColor = '';
    });
    
    // Clear username status
    const usernameStatus = document.querySelector('.username-status');
    if (usernameStatus) {
        usernameStatus.innerHTML = '';
    }
    
    // Clear password strength
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    if (strengthBar) {
        strengthBar.className = 'strength-fill';
    }
    if (strengthText) {
        strengthText.textContent = 'Password strength';
    }
    
    // Clear password match
    const matchStatus = document.querySelector('.password-match-status');
    if (matchStatus) {
        matchStatus.innerHTML = '';
    }
    
    // Clear any field errors
    const fieldErrors = document.querySelectorAll('.field-error');
    fieldErrors.forEach(error => error.remove());
}

// ==============================================
// UTILITY FUNCTIONS
// ==============================================

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

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
        </div>
    `;
    
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
            
            .notification-info {
                border-left-color: #17a2b8;
                background: linear-gradient(135deg, #f8fdff 0%, #e8f5f8 100%);
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
    
    document.body.appendChild(notification);
    
    setTimeout(() => notification.classList.add('show'), 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// ==============================================
// PLACEHOLDER FUNCTIONS
// ==============================================

function signInWithGoogle() {
    showNotification('info', 'Google Sign In will be implemented soon!');
}

function signUpWithGoogle() {
    showNotification('info', 'Google Sign Up will be implemented soon!');
}

function showForgotPassword() {
    showNotification('info', 'Forgot password feature will be available soon!');
}

// ==============================================
// MESSAGE FUNCTIONS
// ==============================================

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
    
    const usernameStatus = document.querySelector('.username-status');
    if (usernameStatus) {
        usernameStatus.innerHTML = '';
    }
}

// ==============================================
// FORM SUBMISSION HANDLERS
// ==============================================

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
    
    // Set button to loading state with text
    setButtonLoading(submitBtn, 'Signing In...');
    
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
            setButtonLoading(submitBtn, 'Success! Redirecting...');
            showAuthSuccess('Login successful! Redirecting...');
            setTimeout(() => {
                window.location.href = data.redirect || '/';
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
        // Reset button state after delay if not successful
        if (!document.querySelector('#auth-success-message[style*="flex"]')) {
            setTimeout(() => resetButtonState(submitBtn), 1000);
        }
    });
    
    return false;
}

function handleSignupSubmit(event) {
    event.preventDefault();
    
    if (!validateBasicSignupForm()) {
        return false;
    }
    
    const form = event.target;
    const submitBtn = form.querySelector('.auth-submit-btn');
    
    // Set button to loading state
    setButtonLoading(submitBtn, 'Creating Account...');
    
    const formData = {
        username: document.getElementById('signup-username').value.trim(),
        email: document.getElementById('signup-email').value.trim(),
        password: document.getElementById('signup-password').value,
        password_confirmation: document.getElementById('signup-confirm-password').value,
        terms_accepted: document.getElementById('agree-terms').checked
    };
    
    // Debug: Log what we're sending
    console.log('Sending data:', formData);
    
    fetch('/auth/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Server response:', data);
        
        if (data.success) {
            // Update button text to success
            setButtonLoading(submitBtn, 'Account Created!');
            
            // Show success message
            showAuthSuccess('Account created successfully! Redirecting to login...');
            showNotification('success', 'Account created successfully!');
            
            // Reset form after short delay
            setTimeout(() => {
                resetSignupForm();
                hideAuthMessages();
                
                // Auto-redirect to login form after 2 seconds
                setTimeout(() => {
                    showLogInForm();
                }, 2000);
            }, 1000);
            
        } else {
            console.log('Validation errors:', data.errors);
            showAuthError(data.message || 'Registration failed');
            if (data.errors) {
                handleValidationErrors(data.errors);
            }
            // Reset button state after error
            setTimeout(() => resetButtonState(submitBtn), 1000);
        }
    })
    .catch(error => {
        console.error('Signup error:', error);
        showAuthError('An error occurred. Please try again.');
        setTimeout(() => resetButtonState(submitBtn), 1000);
    });
    
    return false;
}

function handleValidationErrors(errors) {
    clearValidationErrors();
    
    const errorFields = Object.keys(errors);
    
    errorFields.forEach(field => {
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
            
            const errorMsg = document.createElement('div');
            errorMsg.className = 'field-error';
            errorMsg.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            errorMsg.style.color = '#dc3545';
            errorMsg.style.fontSize = '12px';
            errorMsg.style.marginTop = '4px';
            
            const parent = input.closest('.form-group') || input.parentElement;
            parent.appendChild(errorMsg);
        }
    });
}

// ==============================================
// VERIFICATION STATUS POLLING
// ==============================================

/*
  Add robust verification-status poller.
  - tries multiple endpoints
  - accepts different JSON shapes (data.user, user, data)
  - updates window.userData and calls refreshProfileVerifyButton()
*/
let verificationStatusPoll = {
    intervalId: null,
    intervalMs: 10000, // Poll every 10 seconds
    async probeEndpoints() {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': tokenMeta ? tokenMeta.content : ''
        };

        // Try these endpoints in order — adjust if your backend exposes a different URL
        const endpoints = ['/api/user/profile', '/auth/profile', '/api/profile'];

        for (const url of endpoints) {
            try {
                const res = await fetch(url, { method: 'GET', headers });
                if (!res.ok) {
                    // if unauthorized/forbidden stop polling
                    if (res.status === 401 || res.status === 403) return { stop: true };
                    continue;
                }
                const json = await res.json();
                // Support several shapes: { user: {...} }, { data: { user: {...} } }, or direct user object
                const user = (json && (json.user || (json.data && json.data.user) || json.data || json)) || null;
                return { user, url };
            } catch (err) {
                console.debug('Verification probe failed for', url, err);
                continue;
            }
        }
        return { user: null };
    },
    start() {
        if (this.intervalId) return;
        if (!window.userData) return;
        const status = (window.userData.status || '').toLowerCase();
        if (status !== 'pending' && status !== 'pending_verification') return;

        this.intervalId = setInterval(async () => {
            try {
                const { user, url, stop } = await this.probeEndpoints();
                if (stop) {
                    this.stop();
                    return;
                }
                if (!user) return;
                const serverStatus = ((user.status || '') + '').toLowerCase();
                const localStatus = (window.userData.status || '').toLowerCase();

                if (serverStatus && serverStatus !== localStatus) {
                    // merge user fields (do not overwrite everything in case UI expects other props)
                    window.userData = Object.assign({}, window.userData, user);
                    refreshProfileVerifyButton();

                    if (serverStatus === 'verified' || serverStatus === 'approved') {
                        showNotification('success', 'Your profile has been verified!');
                        this.stop();
                    } else if (serverStatus === 'rejected') {
                        showNotification('error', 'Your verification was rejected. You can submit again with updated documents.');
                        this.stop();
                    }
                }
            } catch (err) {
                console.error('Verification status poll error:', err);
            }
        }, this.intervalMs);

        console.debug('Started verificationStatusPoll');
    },
    stop() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
            console.debug('Stopped verificationStatusPoll');
        }
    }
};

function maybeStartVerificationPoll() {
    try {
        if (!window.userData) return;
        const s = (window.userData.status || '').toLowerCase();
        if (['pending', 'pending_verification'].includes(s)) {
            verificationStatusPoll.start();
        }
    } catch (e) {
        console.error('maybeStartVerificationPoll error', e);
    }
}

// ==============================================
// HOOKS AND LISTENERS
// ==============================================

// Hook: start polling on page load if status already pending
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
    
    // Verification form submission
    const verificationForm = document.getElementById('verification-form');
    if (verificationForm) {
        verificationForm.addEventListener('submit', handleVerificationSubmit);
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
    
    // File input change handlers for image preview
    const idFrontInput = document.getElementById('idFront');
    if (idFrontInput) {
        idFrontInput.addEventListener('change', function() {
            previewImage(this, 'idFrontPreview');
        });
    }
    
    const idBackInput = document.getElementById('idBack');
    if (idBackInput) {
        idBackInput.addEventListener('change', function() {
            previewImage(this, 'idBackPreview');
        });
    }
    
    const locationProofInput = document.getElementById('locationProof');
    if (locationProofInput) {
        locationProofInput.addEventListener('change', function() {
            previewImage(this, 'locationProofPreview');
        });
    }
    
    // Auth modal close functionality
    const authModal = document.getElementById('auth-modal');
    if (authModal) {
        authModal.addEventListener('click', function(event) {
            if (event.target === authModal) {
                closeAuthModal();
            }
        });
    }
    
    // Applications modal close functionality
    const applicationsModal = document.getElementById('applications-modal');
    if (applicationsModal) {
        applicationsModal.addEventListener('click', function(event) {
            if (event.target === applicationsModal) {
                closeApplicationsModal();
            }
        });
    }
    
    // Profile modal close functionality
    const profileModal = document.getElementById('profile-modal');
    if (profileModal) {
        profileModal.addEventListener('click', function(event) {
            if (event.target === profileModal) {
                closeProfileModal();
            }
        });
    }
    
    // Verification modal close functionality
    const verificationModal = document.getElementById('verification-modal');
    if (verificationModal) {
        verificationModal.addEventListener('click', function(event) {
            if (event.target === verificationModal) {
                closeVerificationModal();
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('user-dropdown');
        const profile = document.getElementById('user-profile');
        
        if (dropdown && profile && !profile.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
    
    // ESC key to close modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const authModal = document.getElementById('auth-modal');
            if (authModal && authModal.style.display !== 'none') {
                closeAuthModal();
            }
            
            const applicationsModal = document.getElementById('applications-modal');
            if (applicationsModal && applicationsModal.style.display !== 'none') {
                closeApplicationsModal();
            }
            
            const profileModal = document.getElementById('profile-modal');
            if (profileModal && profileModal.style.display !== 'none') {
                closeProfileModal();
            }
            
            const verificationModal = document.getElementById('verification-modal');
            if (verificationModal && verificationModal.style.display !== 'none') {
                closeVerificationModal();
            }
            
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    });
    
    // ensure verify button matches current user status (useful when userData supplied to window)
    refreshProfileVerifyButton();

    // start polling if user is pending verification
    maybeStartVerificationPoll();
});

// ==============================================
// GLOBAL FUNCTION EXPORTS
// ==============================================

// Make functions available globally
window.openAuthModal = openAuthModal;
window.closeAuthModal = closeAuthModal;
window.showLogInForm = showLogInForm;
window.showSignUpForm = showSignUpForm;
window.togglePasswordVisibility = togglePasswordVisibility;
window.signInWithGoogle = signInWithGoogle;
window.signUpWithGoogle = signUpWithGoogle;
window.showForgotPassword = showForgotPassword;
window.toggleUserDropdown = toggleUserDropdown;
window.showMyApplicationsModal = showMyApplicationsModal;
window.closeApplicationsModal = closeApplicationsModal;
window.showProfileModal = showProfileModal;
window.closeProfileModal = closeProfileModal;
window.showVerificationModal = showVerificationModal;
window.closeVerificationModal = closeVerificationModal;
window.editProfile = editProfile;
window.changePassword = changePassword;
window.accountSettings = accountSettings;
window.logoutUser = logoutUser;
window.showNotification = showNotification;
window.previewImage = previewImage;
window.refreshProfileVerifyButton = refreshProfileVerifyButton;

console.log('Enhanced Auth.js with Profile Verification and Status Management loaded successfully');