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

      if (type === 'signup') {
        showSignUpForm();
    } else {
        showLogInForm();
    }
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
    // Load application statistics and recent activity from the combined endpoint
    fetch('/api/user/applications/all', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the statistics in the modal
            const totalStat = document.querySelector('.stat-item:nth-child(1) .stat-number');
            const approvedStat = document.querySelector('.stat-item:nth-child(2) .stat-number');
            const pendingStat = document.querySelector('.stat-item:nth-child(3) .stat-number');
            
            if (totalStat) totalStat.textContent = data.total || '0';
            if (approvedStat) {
                // Calculate approved from all applications
                const approvedCount = data.applications.filter(app => 
                    app.status.toLowerCase() === 'approved'
                ).length;
                approvedStat.textContent = approvedCount || '0';
            }
            if (pendingStat) {
                // Calculate pending from all applications
                const pendingCount = data.applications.filter(app => 
                    ['pending', 'under_review', 'processing'].includes(app.status.toLowerCase())
                ).length;
                pendingStat.textContent = pendingCount || '0';
            }

            // Load recent activity from the response
            const activityList = document.getElementById('recent-activity-list');
            if (activityList) {
                if (data.recent_activity && data.recent_activity.length > 0) {
                    activityList.innerHTML = data.recent_activity.map(activity => `
                        <div class="activity-item">
                            <div class="activity-icon">${activity.icon}</div>
                            <div class="activity-content">
                                <div class="activity-text">${activity.text}</div>
                                <div class="activity-date">${activity.date}</div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    activityList.innerHTML = `
                        <div class="empty-activity">
                            <p>No recent activity yet</p>
                        </div>
                    `;
                }
            }
        }
    })
    .catch(error => {
        console.error('Error loading profile data:', error);
        // Fallback: show error message
        const activityList = document.getElementById('recent-activity-list');
        if (activityList) {
            activityList.innerHTML = `
                <div class="error-activity">
                    <p>Unable to load recent activity</p>
                </div>
            `;
        }
    });
}

function editProfile() {
    const modal = document.getElementById('edit-profile-modal');
    if (!modal) {
        console.error('Edit profile modal not found');
        return;
    }

    // Load current profile data
    loadCurrentProfileData();

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Close profile modal if open
    closeProfileModal();
}

function closeEditProfileModal() {
    const modal = document.getElementById('edit-profile-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

async function loadCurrentProfileData() {
    try {
        const response = await fetch('/api/user/profile', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success && data.user) {
            populateEditForm(data.user);
        } else {
            showNotification('error', 'Failed to load profile data');
        }
    } catch (error) {
        console.error('Error loading profile data:', error);
        showNotification('error', 'Failed to load profile data');
    }
}

function populateEditForm(user) {
    // Populate form fields with current data
    document.getElementById('edit-first-name').value = user.first_name || '';
    document.getElementById('edit-middle-name').value = user.middle_name || '';
    document.getElementById('edit-last-name').value = user.last_name || '';
    document.getElementById('edit-name-extension').value = user.name_extension || '';
    document.getElementById('edit-contact-number').value = user.contact_number || '';
    document.getElementById('edit-gender').value = user.gender || '';
    document.getElementById('edit-date-of-birth').value = user.date_of_birth || '';
    document.getElementById('edit-age').value = user.age || '';
    document.getElementById('edit-user-type').value = user.user_type || '';
    document.getElementById('edit-complete-address').value = user.complete_address || '';
    document.getElementById('edit-barangay').value = user.barangay || '';
}

function changePassword() {
    const modal = document.getElementById('change-password-modal');
    if (!modal) {
        console.error('Change password modal not found');
        return;
    }

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Close profile modal if open
    closeProfileModal();
}

function closeChangePasswordModal() {
    const modal = document.getElementById('change-password-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Reset form
    const form = document.getElementById('change-password-form');
    if (form) {
        form.reset();
    }

    // Clear any validation messages
    clearPasswordValidation();
}

function clearPasswordValidation() {
    const inputs = document.querySelectorAll('#change-password-form input');
    inputs.forEach(input => {
        input.classList.remove('error', 'invalid', 'valid');
        input.style.borderColor = '';
    });

    const errorMessages = document.querySelectorAll('.password-error');
    errorMessages.forEach(msg => msg.remove());

    const strengthBar = document.querySelector('.new-password-strength .strength-fill');
    const strengthText = document.querySelector('.new-password-strength .strength-text');
    if (strengthBar) {
        strengthBar.className = 'strength-fill';
    }
    if (strengthText) {
        strengthText.textContent = 'Password strength';
    }

    const matchStatus = document.querySelector('.confirm-new-password-match');
    if (matchStatus) {
        matchStatus.innerHTML = '';
    }
}

/**
 *Handle change password form submission
 * Prevents default form submission that causes GET request
 */
function handleChangePasswordSubmit(event) {
    // Prevent default form submission
    event.preventDefault();
    event.stopPropagation();

    const form = event.target;
    const currentPassword = document.getElementById('current-password').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmNewPassword = document.getElementById('confirm-new-password').value;
    const submitBtn = form.querySelector('.change-password-submit-btn');

    console.log('🔐 Password change initiated');

    // Client-side validation (UX improvement)
    let errors = [];

    if (!currentPassword) {
        errors.push('Current password is required');
    }

    if (!newPassword) {
        errors.push('New password is required');
    } else if (newPassword.length < 8) {
        errors.push('New password must be at least 8 characters');
    }

    if (newPassword !== confirmNewPassword) {
        errors.push('New passwords do not match');
    }

    if (currentPassword === newPassword) {
        errors.push('New password must be different from current password');
    }

    if (errors.length > 0) {
        showNotification('error', errors.join(', '));
        return false; // CRITICAL: Return false to prevent submission
    }

    // Set button to loading state
    setButtonLoading(submitBtn, 'Changing Password...');

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        console.error('❌ CSRF token not found!');
        showNotification('error', 'Security token missing. Please refresh the page.');
        resetButtonState(submitBtn);
        return false;
    }

    console.log('📤 Sending password change request...');

    // Submit password change via AJAX/Fetch
    fetch('/api/user/change-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword,
            new_password_confirmation: confirmNewPassword
        }),
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('📥 Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📦 Response data:', data);

        if (data.success) {
            setButtonLoading(submitBtn, 'Password Changed!');
            showNotification('success', data.message || 'Password changed successfully!');

            // Clear form
            form.reset();
            clearPasswordValidation();

            // Close modal and redirect after short delay
            setTimeout(() => {
                closeChangePasswordModal();
                
                // Force logout and redirect to login
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }
            }, 1500);

        } else {
            console.error('❌ Password change failed:', data);
            
            let errorMessage = data.message || 'Password change failed';

            // Handle validation errors
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                errorMessage = errorMessages.join(', ');
                
                // Highlight specific fields with errors
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field.replace('_', '-'));
                    if (input) {
                        input.style.borderColor = '#dc2626';
                        input.classList.add('error');
                    }
                });
            }

            showNotification('error', errorMessage);
            resetButtonState(submitBtn);
        }
    })
    .catch(error => {
        console.error('❌ Network error:', error);
        showNotification('error', 'Network error. Please check your connection and try again.');
        resetButtonState(submitBtn);
    });

    // CRITICAL: Return false to prevent any form submission
    return false;
}

/**
 * Clear password validation UI
 */
function clearPasswordValidation() {
    const inputs = document.querySelectorAll('#change-password-form input');
    inputs.forEach(input => {
        input.classList.remove('error', 'invalid', 'valid');
        input.style.borderColor = '';
    });

    const errorMessages = document.querySelectorAll('.password-error');
    errorMessages.forEach(msg => msg.remove());

    const strengthBar = document.querySelector('.new-password-strength .strength-fill');
    const strengthText = document.querySelector('.new-password-strength .strength-text');
    if (strengthBar) {
        strengthBar.className = 'strength-fill';
    }
    if (strengthText) {
        strengthText.textContent = 'Password strength';
    }

    const matchStatus = document.querySelector('.confirm-new-password-match');
    if (matchStatus) {
        matchStatus.innerHTML = '';
    }
}

/**
 * Check new password strength
 */
function checkNewPasswordStrength(password) {
    const strengthBar = document.querySelector('.new-password-strength .strength-fill');
    const strengthText = document.querySelector('.new-password-strength .strength-text');

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

/**
 * Check if new password matches confirmation
 */
function checkNewPasswordMatch(newPassword, confirmPassword) {
    const matchStatus = document.querySelector('.confirm-new-password-match');

    if (!matchStatus || !confirmPassword) {
        if (matchStatus) matchStatus.innerHTML = '';
        return;
    }

    if (newPassword === confirmPassword) {
        matchStatus.className = 'confirm-new-password-match match';
        matchStatus.textContent = '✓ Passwords match';
    } else {
        matchStatus.className = 'confirm-new-password-match no-match';
        matchStatus.textContent = '✗ Passwords do not match';
    }
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
// EDIT PROFILE FORM HANDLER
// ==============================================

async function handleEditProfileSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const submitBtn = document.getElementById('save-profile-btn');

    // Get form data
    const formData = new FormData(form);
    const profileData = {};

    // Convert FormData to regular object
    for (let [key, value] of formData.entries()) {
        if (value.trim() !== '') {
            profileData[key] = value.trim();
        }
    }

    // Validation
    if (!profileData.first_name) {
        showNotification('error', 'First name is required');
        return;
    }

    if (!profileData.last_name) {
        showNotification('error', 'Last name is required');
        return;
    }

    if (profileData.contact_number) {
        // Philippine mobile number validation
        const phoneRegex = /^(\+639|09)\d{9}$/;
        if (!phoneRegex.test(profileData.contact_number)) {
            showNotification('error', 'Please enter a valid Philippine mobile number (09XXXXXXXXX)');
            return;
        }
    }

    if (profileData.date_of_birth) {
        const birthDate = new Date(profileData.date_of_birth);
        const today = new Date();
        const age = Math.floor((today - birthDate) / (365.25 * 24 * 60 * 60 * 1000));

        if (age < 18) {
            showNotification('error', 'You must be at least 18 years old');
            return;
        }

        if (age > 100) {
            showNotification('error', 'Please enter a valid date of birth');
            return;
        }
    }

    // Set loading state
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');

    if (btnText) btnText.style.display = 'none';
    if (btnLoader) btnLoader.style.display = 'inline';
    submitBtn.disabled = true;

    try {
        const response = await fetch('/api/user/update-profile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(profileData),
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            showNotification('success', data.message || 'Profile updated successfully!');

            // Update window.userData with new profile data
            if (window.userData && data.user) {
                Object.assign(window.userData, data.user);
            }

            // Close modal after short delay
            setTimeout(() => {
                closeEditProfileModal();
            }, 1000);

        } else {
            let errorMessage = data.message || 'Failed to update profile';

            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                errorMessage = errorMessages.join(', ');
            }

            showNotification('error', errorMessage);
        }
    } catch (error) {
        console.error('Profile update error:', error);
        showNotification('error', 'Network error. Please check your connection and try again.');
    } finally {
        // Reset button state
        if (btnText) btnText.style.display = 'inline';
        if (btnLoader) btnLoader.style.display = 'none';
        submitBtn.disabled = false;
    }
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

// function loadUserApplicationsInModal() {
//     const grid = document.getElementById('applications-modal-grid');
//     if (!grid) return;

//     // Check if user is logged in
//     if (!window.userData) {
//         grid.innerHTML = `
//             <div class="empty-applications">
//                 <h4>Please Log In</h4>
//                 <p>You need to be logged in to view your applications.</p>
//                 <button class="quick-action-btn" onclick="closeApplicationsModal(); openAuthModal('login');">
//                     <span>🔐</span> Log In
//                 </button>
//             </div>
//         `;
//         return;
//     }

//     // Show loading state
//     grid.innerHTML = `
//         <div class="loading-state">
//             <div class="loader"></div>
//             <p>Loading your applications...</p>
//         </div>
//     `;

//     // Try to fetch real data, fallback to mock data
//     fetch('/api/user/applications', {
//         headers: {
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
//             'Accept': 'application/json'
//         }
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success && data.applications) {
//             renderApplicationsInModal(data.applications);
//         } else {
//             // Fallback to mock data for development
//             renderMockApplicationsInModal();
//         }
//     })
//     .catch(error => {
//         console.log('Loading mock applications for development');
//         renderMockApplicationsInModal();
//     });
// }

// function renderApplicationsInModal(applications) {
//     const grid = document.getElementById('applications-modal-grid');
//     if (!grid) return;

//     if (applications.length === 0) {
//         grid.innerHTML = `
//             <div class="empty-applications">
//                 <h4>No Applications Yet</h4>
//                 <p>You haven't submitted any applications yet. Browse our services to get started!</p>
//                 <button class="quick-action-btn" onclick="closeApplicationsModal(); document.getElementById('services').scrollIntoView({ behavior: 'smooth' });">
//                     <span>🌾</span> Browse Services
//                 </button>
//             </div>
//         `;
//         return;
//     }

//     grid.innerHTML = applications.map(app => `
//         <div class="application-card">
//             <h4>${app.type}</h4>
//             <p>${app.description || 'Application submitted successfully'}</p>
//             <div class="application-status status-${app.status.toLowerCase().replace(' ', '_')}">
//                 ${app.status.charAt(0).toUpperCase() + app.status.slice(1).replace('_', ' ')}
//             </div>
//             <div class="application-date">
//                 Submitted: ${formatApplicationDate(app.date)}
//             </div>
//         </div>
//     `).join('');
// }

// function renderMockApplicationsInModal() {
//     const mockApplications = [
//         {
//             id: 1,
//             type: 'Seedlings Request',
//             date: '2025-01-15',
//             status: 'pending',
//             description: 'Request for vegetable seedlings - tomatoes, eggplant, and pepper varieties'
//         },
//         {
//             id: 2,
//             type: 'FishR Registration',
//             date: '2025-01-10',
//             status: 'approved',
//             description: 'Fisherfolk registration for coastal fishing activities'
//         },
//         {
//             id: 3,
//             type: 'Training Request',
//             date: '2025-01-08',
//             status: 'processing',
//             description: 'Agricultural training program on sustainable farming practices'
//         },
//         {
//             id: 4,
//             type: 'RSBSA Registration',
//             date: '2025-01-05',
//             status: 'approved',
//             description: 'Registry System for Basic Sectors in Agriculture enrollment'
//         },
//         {
//             id: 5,
//             type: 'BoatR Registration',
//             date: '2025-01-03',
//             status: 'rejected',
//             description: 'Fishing boat registration - requires additional documentation'
//         }
//     ];

//     renderApplicationsInModal(mockApplications);
// }

// function formatApplicationDate(dateString) {
//     const date = new Date(dateString);
//     return date.toLocaleDateString('en-US', {
//         year: 'numeric',
//         month: 'short',
//         day: 'numeric'
//     });
// }

// ==============================================
// UPDATED: MY APPLICATIONS MODAL - WITH REAL RSBSA DATA
// ==============================================

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

    // Fetch ALL applications (RSBSA, Seedlings, FishR, BoatR, Training)
    fetch('/api/user/applications/all', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        console.log('Applications loaded:', data);
        
        if (data.success && data.applications) {
            renderApplicationsInModal(data.applications);
        } else {
            renderEmptyApplications();
        }
    })
    .catch(error => {
        console.error('Error loading applications:', error);
        renderEmptyApplications();
    });
}

function renderApplicationsInModal(applications) {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    if (applications.length === 0) {
        renderEmptyApplications();
        return;
    }

    grid.innerHTML = applications.map(app => {
        const statusClass = getApplicationStatusClass(app.status);
        const statusIcon = getApplicationStatusIcon(app.status);
        const typeIcon = getApplicationTypeIcon(app.type);

        return `
            <div class="application-card ${statusClass}">
                <div class="application-header">
                    <div class="app-icon">${typeIcon}</div>
                    <div class="app-info">
                        <h4>${app.type}</h4>
                        <p class="app-number">${app.application_number || app.reference_number || 'N/A'}</p>
                    </div>
                </div>
                
                <p class="app-description">${app.description || 'Application submitted'}</p>
                
                ${app.full_name || app.livelihood || app.barangay ? `
                    <div class="app-details">
                        ${app.full_name ? `<div class="detail-item"><strong>Name:</strong> ${app.full_name}</div>` : ''}
                        ${app.livelihood ? `<div class="detail-item"><strong>Livelihood:</strong> ${app.livelihood}</div>` : ''}
                        ${app.barangay ? `<div class="detail-item"><strong>Barangay:</strong> ${app.barangay}</div>` : ''}
                    </div>
                ` : ''}
                
                <div class="application-footer">
                    <div class="application-status status-badge-${app.status.toLowerCase().replace(/[_\s]/g, '-')}">
                        ${statusIcon} ${formatApplicationStatus(app.status)}
                    </div>
                    <div class="application-date">
                        ${formatApplicationDate(app.submitted_at || app.date || app.created_at)}
                    </div>
                </div>

                ${app.remarks ? `
                    <div class="app-remarks">
                        <strong>Remarks:</strong> ${app.remarks}
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

function renderEmptyApplications() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    grid.innerHTML = `
        <div class="empty-applications">
            <div class="empty-icon">📋</div>
            <h4>No Applications Yet</h4>
            <p>You haven't submitted any applications yet. Browse our services to get started!</p>
            <button class="quick-action-btn" onclick="closeApplicationsModal(); document.getElementById('services').scrollIntoView({ behavior: 'smooth' });">
                <span>🌾</span> Browse Services
            </button>
        </div>
    `;
}

// Helper functions for application display
function getApplicationStatusClass(status) {
    const normalized = status.toLowerCase().replace(/[_\s]/g, '-');
    return `app-status-${normalized}`;
}

function getApplicationStatusIcon(status) {
    const icons = {
        'pending': '⏳',
        'under_review': '🔍',
        'processing': '⚙️',
        'approved': '✅',
        'rejected': '❌',
        'cancelled': '🚫'
    };
    return icons[status.toLowerCase()] || '📄';
}

function getApplicationTypeIcon(type) {
    const icons = {
        'RSBSA Registration': '📋',
        'Seedlings Request': '🌱',
        'FishR Registration': '🐟',
        'BoatR Registration': '⛵',
        'Training Request': '📚'
    };
    return icons[type] || '📄';
}

function formatApplicationStatus(status) {
    return status
        .replace(/_/g, ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
}

function formatApplicationDate(dateString) {
    if (!dateString) return 'N/A';

    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Add CSS for improved application cards (inject once)
if (!document.getElementById('application-cards-styles')) {
    const styles = document.createElement('style');
    styles.id = 'application-cards-styles';
    styles.textContent = `
        .application-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-left: 4px solid #dee2e6;
        }

        .application-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .application-card.app-status-pending {
            border-left-color: #ffc107;
        }

        .application-card.app-status-under-review,
        .application-card.app-status-processing {
            border-left-color: #17a2b8;
        }

        .application-card.app-status-approved {
            border-left-color: #28a745;
        }

        .application-card.app-status-rejected {
            border-left-color: #dc3545;
        }

        .application-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .app-icon {
            font-size: 32px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .app-info h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .app-number {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #6c757d;
            font-family: 'Courier New', monospace;
        }

        .app-description {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .app-details {
            background: #f8f9fa;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .detail-item {
            font-size: 13px;
            color: #555;
            margin-bottom: 4px;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-item strong {
            color: #333;
            font-weight: 600;
        }

        .application-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e9ecef;
        }

        .application-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge-pending {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #fff3cd;
            color: #856404;
        }

        .status-badge-under-review,
        .status-badge-processing {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge-approved {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #d4edda;
            color: #155724;
        }

        .status-badge-rejected {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #f8d7da;
            color: #721c24;
        }

        .application-date {
            font-size: 12px;
            color: #999;
        }

        .app-remarks {
            margin-top: 12px;
            padding: 10px 12px;
            background: #fff3cd;
            border-radius: 6px;
            font-size: 13px;
            color: #856404;
        }

        .app-remarks strong {
            font-weight: 600;
        }

        .empty-applications {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-applications h4 {
            font-size: 24px;
            color: #333;
            margin-bottom: 12px;
        }

        .empty-applications p {
            color: #666;
            margin-bottom: 24px;
            font-size: 16px;
        }

        .quick-action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .loading-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
        }

        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(styles);
}

console.log('✅ Updated My Applications with RSBSA integration loaded');

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

    if (!username || username.trim() === '') {
        if (usernameStatus) {
            usernameStatus.innerHTML = '';
        }
        usernameInput.classList.remove('is-valid', 'is-invalid');
        return; // Exit function early - no checking needed
    }

    // CLIENT-SIDE VALIDATION RULES
    let errors = [];

    // 1. Length check (3-20 characters)
    if (username.length < 3) {
        errors.push('Username must be at least 3 characters');
    }
    if (username.length > 20) {
        errors.push('Username must not exceed 20 characters');
    }

    // 2. No spaces allowed
    if (/\s/.test(username)) {
        errors.push('Username cannot contain spaces');
    }

    // 3. Only letters, numbers, underscores, and dots allowed
    if (!/^[a-zA-Z0-9_.]+$/.test(username)) {
        errors.push('Username can only contain letters, numbers, underscores, and dots');
    }

    // 4. Cannot start with a number
    if (/^[0-9]/.test(username)) {
        errors.push('Username cannot start with a number');
    }

    // 5. Must contain at least one letter (cannot be only numbers)
    if (!/[a-zA-Z]/.test(username)) {
        errors.push('Username must contain at least one letter');
    }

    // 6. Cannot start or end with underscore or dot
    if (/^[_.]|[_.]$/.test(username)) {
        errors.push('Username cannot start or end with underscore or dot');
    }

    // 7. No consecutive dots or underscores
    if (/[_.]{2,}/.test(username)) {
        errors.push('Username cannot have consecutive dots or underscores');
    }

    // Display validation errors immediately
    if (errors.length > 0) {
        if (usernameStatus) {
            usernameStatus.innerHTML = `<span class="text-danger">✗ ${errors[0]}</span>`;
        }
        usernameInput.classList.remove('is-valid');
        usernameInput.classList.add('is-invalid');
        return;
    }

    // If validation passes, check availability on server
    if (usernameStatus) {
        usernameStatus.innerHTML = '<span class="text-info">Checking availability...</span>';
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
    }, 500); // Debounce for 500ms
}

// ==============================================
// EMAIL VALIDATION FUNCTION
// ==============================================

/**
 * Comprehensive email validation
 * Validates email format according to standard patterns
 */
function validateEmail(email) {
    const validation = {
        valid: true,
        error: ''
    };

    // Check if email is empty
    if (!email || email.trim() === '') {
        validation.valid = false;
        validation.error = 'Email is required';
        return validation;
    }

    // Remove whitespace
    email = email.trim();

    // Check for spaces
    if (/\s/.test(email)) {
        validation.valid = false;
        validation.error = 'Email cannot contain spaces';
        return validation;
    }

    // Check length (standard email max is 254 characters)
    if (email.length > 254) {
        validation.valid = false;
        validation.error = 'Email is too long (max 254 characters)';
        return validation;
    }

    // Check basic format: must have @ and at least one dot after @
    if (!email.includes('@') || !email.split('@')[1]?.includes('.')) {
        validation.valid = false;
        validation.error = 'Invalid email format (must be name@domain.com)';
        return validation;
    }

    // Comprehensive email regex pattern
    // Allows: letters, numbers, dots, underscores, hyphens
    // Format: localpart@domain.tld
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (!emailPattern.test(email)) {
        validation.valid = false;
        validation.error = 'Invalid email format. Use letters, numbers, dots, underscores, or hyphens';
        return validation;
    }

    // Split email into local part and domain
    const [localPart, domain] = email.split('@');

    // Validate local part (before @)
    if (localPart.length === 0 || localPart.length > 64) {
        validation.valid = false;
        validation.error = 'Email username part is invalid (max 64 characters)';
        return validation;
    }

    // Check for consecutive dots
    if (/\.\./.test(email)) {
        validation.valid = false;
        validation.error = 'Email cannot have consecutive dots';
        return validation;
    }

    // Check if starts or ends with dot, underscore, or hyphen
    if (/^[._-]|[._-]@/.test(email)) {
        validation.valid = false;
        validation.error = 'Email cannot start with a dot, underscore, or hyphen';
        return validation;
    }

    // Validate domain part (after @)
    if (domain.length === 0 || domain.length > 255) {
        validation.valid = false;
        validation.error = 'Email domain is invalid';
        return validation;
    }

    // Check domain has valid TLD (at least 2 characters)
    const domainParts = domain.split('.');
    const tld = domainParts[domainParts.length - 1];
    if (tld.length < 2) {
        validation.valid = false;
        validation.error = 'Email must have a valid domain extension (e.g., .com, .ph)';
        return validation;
    }

    return validation;
}

/**
 * Real-time email validation for signup form
 */
function checkEmailValidity(email) {
    const emailInput = document.getElementById('signup-email');
    const validation = validateEmail(email);

    if (!email) {
        emailInput.classList.remove('is-valid', 'is-invalid');
        return;
    }

    if (validation.valid) {
        emailInput.classList.remove('is-invalid');
        emailInput.classList.add('is-valid');
    } else {
        emailInput.classList.remove('is-valid');
        emailInput.classList.add('is-invalid');
        
        // Show validation message
        let errorMsg = emailInput.parentElement.querySelector('.email-error');
        if (!errorMsg) {
            errorMsg = document.createElement('div');
            errorMsg.className = 'email-error field-error';
            errorMsg.style.color = '#dc3545';
            errorMsg.style.fontSize = '12px';
            errorMsg.style.marginTop = '4px';
            emailInput.parentElement.appendChild(errorMsg);
        }
        errorMsg.textContent = validation.error;
    }
}

// ==============================================
// PASSWORD VALIDATION FUNCTION
// ==============================================

/**
 * Comprehensive password validation
 * Enforces strong password requirements
 */
function validatePassword(password) {
    const validation = {
        valid: true,
        error: '',
        strength: 0,
        requirements: {
            minLength: false,
            hasUppercase: false,
            hasLowercase: false,
            hasNumber: false,
            hasSpecialChar: false,
            noSpaces: true
        }
    };

    // Check if password is empty
    if (!password) {
        validation.valid = false;
        validation.error = 'Password is required';
        return validation;
    }

    // Check for spaces
    if (/\s/.test(password)) {
        validation.valid = false;
        validation.error = 'Password cannot contain spaces';
        validation.requirements.noSpaces = false;
        return validation;
    }

    // Check minimum length (8 characters)
    if (password.length >= 8) {
        validation.requirements.minLength = true;
        validation.strength++;
    } else {
        validation.valid = false;
        validation.error = 'Password must be at least 8 characters';
    }

    // Check for at least one uppercase letter
    if (/[A-Z]/.test(password)) {
        validation.requirements.hasUppercase = true;
        validation.strength++;
    } else {
        validation.valid = false;
        validation.error = validation.error || 'Password must contain at least one uppercase letter';
    }

    // Check for at least one lowercase letter
    if (/[a-z]/.test(password)) {
        validation.requirements.hasLowercase = true;
        validation.strength++;
    } else {
        validation.valid = false;
        validation.error = validation.error || 'Password must contain at least one lowercase letter';
    }

    // Check for at least one number
    if (/\d/.test(password)) {
        validation.requirements.hasNumber = true;
        validation.strength++;
    } else {
        validation.valid = false;
        validation.error = validation.error || 'Password must contain at least one number';
    }

    // Check for at least one special character (@, #, !, $, %, ^, &, *, etc.)
    if (/[@#!$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
        validation.requirements.hasSpecialChar = true;
        validation.strength++;
    } else {
        validation.valid = false;
        validation.error = validation.error || 'Password must contain at least one special character (@, #, !, $, etc.)';
    }

    // Ensure password is not numbers only
    if (/^\d+$/.test(password)) {
        validation.valid = false;
        validation.error = 'Password cannot be numbers only';
        return validation;
    }

    // Ensure password is not letters only
    if (/^[a-zA-Z]+$/.test(password)) {
        validation.valid = false;
        validation.error = 'Password cannot be letters only';
        return validation;
    }

    return validation;
}


function checkPasswordStrength(password) {
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');

    if (!strengthBar || !strengthText) return;

    const validation = validatePassword(password);
    const requirements = validation.requirements;

     // Remove all strength classes
    strengthBar.className = 'strength-fill';

    if (!password) {
        strengthText.textContent = 'Password strength';
        return;
    }

       // Calculate strength based on requirements met
    const strength = validation.strength;
    let strengthLabel = 'Too weak';
    let strengthClass = 'weak';

    if (strength <= 2) {
        strengthLabel = 'Weak';
        strengthClass = 'weak';
    } else if (strength === 3) {
        strengthLabel = 'Fair';
        strengthClass = 'fair';
    } else if (strength === 4) {
        strengthLabel = 'Good';
        strengthClass = 'good';
    } else if (strength === 5) {
        strengthLabel = 'Strong';
        strengthClass = 'strong';
    }

    strengthBar.classList.add(strengthClass);
    strengthText.textContent = `Password strength: ${strengthLabel}`;

    // Show detailed requirements below password input
    showPasswordRequirements(requirements);
}

/**
 * Display password requirements checklist
 */
function showPasswordRequirements(requirements) {
    const passwordInput = document.getElementById('signup-password');
    if (!passwordInput) return;

    let requirementsDiv = document.querySelector('.password-requirements-list');
    
    if (!requirementsDiv) {
        requirementsDiv = document.createElement('div');
        requirementsDiv.className = 'password-requirements-list';
        requirementsDiv.style.cssText = `
            margin-top: 8px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 12px;
        `;
        passwordInput.parentElement.appendChild(requirementsDiv);
    }

    requirementsDiv.innerHTML = `
        <div style="margin-bottom: 4px; font-weight: 600; color: #374151;">Password must contain:</div>
        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px; color: ${requirements.minLength ? '#10b981' : '#6b7280'};">
            <span>${requirements.minLength ? '✓' : '○'}</span>
            <span>At least 8 characters</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px; color: ${requirements.hasUppercase ? '#10b981' : '#6b7280'};">
            <span>${requirements.hasUppercase ? '✓' : '○'}</span>
            <span>At least 1 uppercase letter (A-Z)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px; color: ${requirements.hasLowercase ? '#10b981' : '#6b7280'};">
            <span>${requirements.hasLowercase ? '✓' : '○'}</span>
            <span>At least 1 lowercase letter (a-z)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px; color: ${requirements.hasNumber ? '#10b981' : '#6b7280'};">
            <span>${requirements.hasNumber ? '✓' : '○'}</span>
            <span>At least 1 number (0-9)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px; color: ${requirements.hasSpecialChar ? '#10b981' : '#6b7280'};">
            <span>${requirements.hasSpecialChar ? '✓' : '○'}</span>
            <span>At least 1 special character (@, #, !, $, etc.)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px; color: ${requirements.noSpaces ? '#10b981' : '#ef4444'};">
            <span>${requirements.noSpaces ? '✓' : '✗'}</span>
            <span>No spaces allowed</span>
        </div>
    `;
}

/**
 * Real-time password validation for signup form
 */
function checkPasswordValidity(password) {
    const passwordInput = document.getElementById('signup-password');
    if (!passwordInput) return;

    const validation = validatePassword(password);

    if (!password) {
        passwordInput.classList.remove('is-valid', 'is-invalid');
        // Remove requirements list if password is empty
        const requirementsList = document.querySelector('.password-requirements-list');
        if (requirementsList) requirementsList.remove();
        return;
    }

    if (validation.valid) {
        passwordInput.classList.remove('is-invalid');
        passwordInput.classList.add('is-valid');
    } else {
        passwordInput.classList.remove('is-valid');
        passwordInput.classList.add('is-invalid');
    }
}

// ==============================================
// CONFIRM PASSWORD VALIDATION
// ==============================================

/**
 * Validate password confirmation matches original password
 */
function validatePasswordConfirmation(password, confirmPassword) {
    const validation = {
        valid: true,
        error: ''
    };

    // Check if confirm password is empty
    if (!confirmPassword) {
        validation.valid = false;
        validation.error = 'Please confirm your password';
        return validation;
    }

    // Check if passwords match
    if (password !== confirmPassword) {
        validation.valid = false;
        validation.error = 'Passwords do not match';
        return validation;
    }

    // Check if confirm password meets the same requirements as password
    const passwordValidation = validatePassword(confirmPassword);
    if (!passwordValidation.valid) {
        validation.valid = false;
        validation.error = 'Confirmation password must meet password requirements';
        return validation;
    }

    return validation;
}

function checkPasswordMatch(password, confirmPassword) {
    const matchStatus = document.querySelector('.password-match-status');
    const confirmInput = document.getElementById('signup-confirm-password');

    if (!matchStatus || !confirmPassword) return;

    // Clear status if confirm password is empty
    if (!confirmPassword) {
        matchStatus.innerHTML = '';
        confirmInput.classList.remove('is-valid', 'is-invalid');
        return;
    }

    const validation = validatePasswordConfirmation(password, confirmPassword);

    if (validation.valid) {
        matchStatus.className = 'password-match-status match';
        matchStatus.innerHTML = '<span style="color: #10b981;">✓ Passwords match</span>';
        confirmInput.classList.remove('is-invalid');
        confirmInput.classList.add('is-valid');
    } else {
        matchStatus.className = 'password-match-status no-match';
        matchStatus.innerHTML = `<span style="color: #ef4444;">✗ ${validation.error}</span>`;
        confirmInput.classList.remove('is-valid');
        confirmInput.classList.add('is-invalid');
    }
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

   // Updated email validation
    const emailValidation = validateEmail(email);
    if (!emailValidation.valid) {
        errors.push(emailValidation.error);
        isValid = false;
    }

    // updated password validation
    const passwordValidation = validatePassword(password);
    if (!passwordValidation.valid) {
        errors.push(passwordValidation.error);
        isValid = false;
    }

    // Confirm password validation
    const confirmValidation = validatePasswordConfirmation(password, confirmPassword);
    if (!confirmValidation.valid) {
        errors.push(confirmValidation.error);
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

function signInWithFacebook() {
    showNotification('info', 'Facebook Sign In will be implemented soon!');
}

function signUpWithFacebook() {
    showNotification('info', 'Facebook Sign Up will be implemented soon!');
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

    // Edit profile form submission
    const editProfileForm = document.getElementById('edit-profile-form');
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', handleEditProfileSubmit);
    }

    // Username availability checker
    const usernameInput = document.getElementById('signup-username');
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            checkUsernameAvailability(this.value);
        });
    }

    // Email validation checker
    const emailInput = document.getElementById('signup-email');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            checkEmailValidity(this.value);
        });
        
        // Also validate on blur
        emailInput.addEventListener('blur', function() {
            checkEmailValidity(this.value);
        });
    }

    // Password strength and validity checker
    const passwordInput = document.getElementById('signup-password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordValidity(this.value);

            // Re-validate confirmation if it has a value
        const confirmPassword = document.getElementById('signup-confirm-password').value;
        if (confirmPassword) {
            checkPasswordMatch(this.value, confirmPassword);
        }
        });
    }

   // Password confirmation checker with validation
    const confirmPasswordInput = document.getElementById('signup-confirm-password');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = document.getElementById('signup-password').value;
            checkPasswordMatch(password, this.value);
        });
        
        // Also check on blur
        confirmPasswordInput.addEventListener('blur', function() {
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

    // Edit profile modal close functionality
    const editProfileModal = document.getElementById('edit-profile-modal');
    if (editProfileModal) {
        editProfileModal.addEventListener('click', function(event) {
            if (event.target === editProfileModal) {
                closeEditProfileModal();
            }
        });
    }

    // Date of birth change handler to auto-calculate age
    const dobInput = document.getElementById('edit-date-of-birth');
    if (dobInput) {
        dobInput.addEventListener('change', function() {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            const ageInput = document.getElementById('edit-age');
            if (ageInput && age >= 0) {
                ageInput.value = age;
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

    document.addEventListener('DOMContentLoaded', function() {
        // Change password form submission
        const changePasswordForm = document.getElementById('change-password-form');
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', handleChangePasswordSubmit);
        }

        // New password strength checker
        const newPasswordInput = document.getElementById('new-password');
        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', function() {
                checkNewPasswordStrength(this.value);
            });
        }

        // Confirm new password checker
        const confirmNewPasswordInput = document.getElementById('confirm-new-password');
        if (confirmNewPasswordInput) {
            confirmNewPasswordInput.addEventListener('input', function() {
                const newPassword = document.getElementById('new-password').value;
                checkNewPasswordMatch(newPassword, this.value);
            });
        }

        // Change password modal close functionality
        const changePasswordModal = document.getElementById('change-password-modal');
        if (changePasswordModal) {
            changePasswordModal.addEventListener('click', function(event) {
                if (event.target === changePasswordModal) {
                    closeChangePasswordModal();
                }
            });
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
window.signInWithFacebook = signInWithFacebook;
window.signUpWithFacebook = signUpWithFacebook;
window.showForgotPassword = showForgotPassword;
window.toggleUserDropdown = toggleUserDropdown;
window.showMyApplicationsModal = showMyApplicationsModal;
window.closeApplicationsModal = closeApplicationsModal;
window.showProfileModal = showProfileModal;
window.closeProfileModal = closeProfileModal;
window.showVerificationModal = showVerificationModal;
window.closeVerificationModal = closeVerificationModal;
window.editProfile = editProfile;
window.closeEditProfileModal = closeEditProfileModal;
window.loadCurrentProfileData = loadCurrentProfileData;
window.populateEditForm = populateEditForm;
window.handleEditProfileSubmit = handleEditProfileSubmit;
window.changePassword = changePassword;
window.accountSettings = accountSettings;
window.logoutUser = logoutUser;
window.showNotification = showNotification;
window.previewImage = previewImage;
window.refreshProfileVerifyButton = refreshProfileVerifyButton;
window.changePassword = changePassword;
window.closeChangePasswordModal = closeChangePasswordModal;
window.handleChangePasswordSubmit = handleChangePasswordSubmit;
window.checkNewPasswordStrength = checkNewPasswordStrength;
window.checkNewPasswordMatch = checkNewPasswordMatch;


console.log('Enhanced Auth.js with Profile Verification and Status Management loaded successfully');
