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

    //CLEAR ALL VALIDATION UI ELEMENTS
    clearAllValidationUI();

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

    clearAllValidationUI();
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

    clearAllValidationUI();
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

    button.disabled = true;
    button.style.pointerEvents = 'none';
    button.style.opacity = '0.8';
    
    const btnText = button.querySelector('.btn-text');
    const btnLoader = button.querySelector('.btn-loader');

    if (btnText) {
        btnText.textContent = loadingText;
        btnText.style.visibility = 'hidden';  // HIDE BUT KEEP SPACE
    }
    
    if (btnLoader) {
        btnLoader.style.display = 'inline-block';
        btnLoader.textContent = loadingText;
    }
}

function resetButtonState(button) {
    if (!button) return;

    button.disabled = false;
    button.style.pointerEvents = 'auto';
    button.style.opacity = '1';
    
    const btnText = button.querySelector('.btn-text');
    const btnLoader = button.querySelector('.btn-loader');
    const originalText = button.dataset.originalText;

    if (btnText) {
        btnText.textContent = originalText || 'Submit';
        btnText.style.visibility = 'visible';  // SHOW AGAIN
    }
    
    if (btnLoader) {
        btnLoader.style.display = 'none';
        btnLoader.textContent = '';
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
            verifyBtn.innerHTML = `
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Account Verified</span>
            `;
            break;

        case 'pending':
        case 'pending_verification':
            // Pending state: neutral colors, disabled, with clock
            verifyBtn.disabled = true;
            verifyBtn.classList.add('pending');
            verifyBtn.innerHTML = `
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
                <span>Under Review</span>
            `;
            break;

        case 'rejected':
            // Rejected state: can retry verification (orange/amber styling)
            verifyBtn.disabled = false;
            verifyBtn.classList.add('rejected');
            verifyBtn.innerHTML = `
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8"/>
                    <polyline points="16 6 12 2 8 6"/>
                    <line x1="12" y1="2" x2="12" y2="15"/>
                </svg>
                <span>Verify Again</span>
            `;
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
            verifyBtn.innerHTML = `
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Verify Account</span>
            `;
            verifyBtn.onclick = () => showVerificationModal();
            break;
    }

    console.log(`Profile verify button updated for status: ${status}`);
}

/**
 * Update header status display dynamically
 * This function runs whenever the status changes
 */
function updateHeaderStatusDisplay(status) {
    const statusText = document.getElementById('status-text');
    const statusDiv = document.getElementById('header-user-status');

    if (!statusText || !statusDiv) return;

    const statusLower = (status || '').toLowerCase();
    let displayText = 'Active';
    let badgeClass = 'status-active';
    let icon = ''; // Will use CSS for icon instead

    // Map status to display text with professional labels
    switch(statusLower) {
        case 'verified':
        case 'approved':
            displayText = 'Verified';
            badgeClass = 'status-verified';
            break;

        case 'pending':
        case 'pending_verification':
            displayText = 'Under Review';
            badgeClass = 'status-pending';
            break;

        case 'rejected':
            displayText = 'Verification Failed';
            badgeClass = 'status-rejected';
            break;

        case 'unverified':
            displayText = 'Not Verified';
            badgeClass = 'status-unverified';
            break;

        case 'banned':
            displayText = 'Account Restricted';
            badgeClass = 'status-banned';
            break;

        default:
            displayText = statusLower.charAt(0).toUpperCase() + statusLower.slice(1);
            badgeClass = `status-${statusLower}`;
    }

    // Update the text content
    statusText.textContent = displayText;

    // Update the parent div class for styling
    statusDiv.className = `user-status ${badgeClass}`;

    console.log('📝 Header status updated to:', displayText);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (window.userData && window.userData.status) {
        updateHeaderStatusDisplay(window.userData.status);
    }
});
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

/**
 * Updated loadProfileData function - Removes emojis from activity display
 * Makes the UI more professional and accessible
 */
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
                    activityList.innerHTML = data.recent_activity.map(activity => {
                        // Get icon SVG based on activity type
                        const iconSVG = getActivityIcon(activity.type);
                        
                        return `
                            <div class="activity-item">
                                <div class="activity-icon">
                                    ${iconSVG}
                                </div>
                                <div class="activity-content">
                                    <div class="activity-text">${activity.text}</div>
                                    <div class="activity-date">${activity.date}</div>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    activityList.innerHTML = `
                        <div class="empty-activity">
                            <div class="empty-activity-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 16v-4M12 8h.01"></path>
                                </svg>
                            </div>
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
                    <div class="error-activity-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <p>Unable to load recent activity</p>
                </div>
            `;
        }
    });
}

/**
 * Get appropriate SVG icon based on activity type
 * Replaces emoji icons with professional SVG icons
 */
function getActivityIcon(type) {
    const iconMap = {
        'application_submitted': `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="13" x2="16" y2="13"></line>
                <line x1="12" y1="17" x2="16" y2="17"></line>
                <line x1="9" y1="13" x2="9.01" y2="13"></line>
                <line x1="9" y1="17" x2="9.01" y2="17"></line>
            </svg>
        `,
        'application_approved': `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        `,
        'application_rejected': `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
        `,
        'supply_requested': `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
        `,
        'supply_received': `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"></path>
                <path d="M10 17l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" fill="currentColor"></path>
            </svg>
        `,
        'profile_updated': `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
        `,
        'training_applied': `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zM22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
        `,
        'account_verified': `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        `,
        'default': `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="1"></circle>
                <path d="M12 1v6m0 6v6"></path>
                <path d="M4.22 4.22l4.24 4.24m5.08 5.08l4.24 4.24"></path>
                <path d="M1 12h6m6 0h6"></path>
                <path d="M4.22 19.78l4.24-4.24m5.08-5.08l4.24-4.24"></path>
            </svg>
        `
    };

    return iconMap[type] || iconMap['default'];
}

// ==============================================
// CHANGE PASSWORD FUNCTIONS WITH VALIDATION
// ==============================================

/**
 * Open change password modal
 */
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

/**
 * Close change password modal
 */
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

    // Clear all validation
    clearChangePasswordValidation();
}

/**
 * Clear all password validation UI
 */
function clearChangePasswordValidation() {
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

    // Remove requirements list if exists
    const requirementsList = document.querySelector('.new-password-requirements-list');
    if (requirementsList) {
        requirementsList.remove();
    }
}

/**
 * Comprehensive password validation
 */
function validateChangePassword(password) {
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

    // Check for uppercase letter
    if (/[A-Z]/.test(password)) {
        validation.requirements.hasUppercase = true;
        validation.strength++;
    } else {
        validation.valid = false;
        validation.error = validation.error || 'Password must contain at least one uppercase letter';
    }

    // Check for lowercase letter
    if (/[a-z]/.test(password)) {
        validation.requirements.hasLowercase = true;
        validation.strength++;
    } else {
        validation.valid = false;
        validation.error = validation.error || 'Password must contain at least one lowercase letter';
    }

    // Check for number
    if (/\d/.test(password)) {
        validation.requirements.hasNumber = true;
        validation.strength++;
    } else {
        validation.valid = false;
        validation.error = validation.error || 'Password must contain at least one number';
    }

    // Check for special character
    if (/[@#!$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
        validation.requirements.hasSpecialChar = true;
        validation.strength++;
    } else {
        validation.valid = false;
        validation.error = validation.error || 'Password must contain at least one special character';
    }

    // Prevent numbers only
    if (/^\d+$/.test(password)) {
        validation.valid = false;
        validation.error = 'Password cannot be numbers only';
        return validation;
    }

    // Prevent letters only
    if (/^[a-zA-Z]+$/.test(password)) {
        validation.valid = false;
        validation.error = 'Password cannot be letters only';
        return validation;
    }

    return validation;
}

/**
 * Check new password strength with visual feedback
 */
function checkNewPasswordStrength(password) {
    const strengthBar = document.querySelector('.new-password-strength .strength-fill');
    const strengthText = document.querySelector('.new-password-strength .strength-text');

    if (!strengthBar || !strengthText) return;

    const validation = validateChangePassword(password);
    const requirements = validation.requirements;

    // Remove all strength classes
    strengthBar.className = 'strength-fill';

    if (!password) {
        strengthText.textContent = 'Password strength';
        showNewPasswordRequirements(requirements);
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

    // Show detailed requirements
    showNewPasswordRequirements(requirements);
}

/**
 * Display password requirements checklist
 */
function showNewPasswordRequirements(requirements) {
    const passwordInput = document.getElementById('new-password');
    if (!passwordInput) return;

    let requirementsDiv = document.querySelector('.new-password-requirements-list');

    if (!requirementsDiv) {
        requirementsDiv = document.createElement('div');
        requirementsDiv.className = 'new-password-requirements-list';
        requirementsDiv.style.cssText = `
            margin-top: 10px;
            padding: 14px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 13px;
            border: 1px solid #e5e7eb;
        `;
        // Insert after the password strength indicator
        const passwordStrength = passwordInput.closest('.form-group').querySelector('.new-password-strength');
        if (passwordStrength) {
            passwordStrength.parentNode.insertBefore(requirementsDiv, passwordStrength.nextSibling);
        }
    }

    const checkIcon = '✓';
    const uncheckIcon = '○';

    requirementsDiv.innerHTML = `
        <div style="margin-bottom: 8px; font-weight: 600; color: #374151;">Password must contain:</div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px; color: ${requirements.minLength ? '#10b981' : '#6b7280'};">
            <span style="font-weight: bold;">${requirements.minLength ? checkIcon : uncheckIcon}</span>
            <span>At least 8 characters</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px; color: ${requirements.hasUppercase ? '#10b981' : '#6b7280'};">
            <span style="font-weight: bold;">${requirements.hasUppercase ? checkIcon : uncheckIcon}</span>
            <span>At least 1 uppercase letter (A-Z)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px; color: ${requirements.hasLowercase ? '#10b981' : '#6b7280'};">
            <span style="font-weight: bold;">${requirements.hasLowercase ? checkIcon : uncheckIcon}</span>
            <span>At least 1 lowercase letter (a-z)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px; color: ${requirements.hasNumber ? '#10b981' : '#6b7280'};">
            <span style="font-weight: bold;">${requirements.hasNumber ? checkIcon : uncheckIcon}</span>
            <span>At least 1 number (0-9)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px; color: ${requirements.hasSpecialChar ? '#10b981' : '#6b7280'};">
            <span style="font-weight: bold;">${requirements.hasSpecialChar ? checkIcon : uncheckIcon}</span>
            <span>At least 1 special character (@, #, !, $, etc.)</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px; color: ${requirements.noSpaces ? '#10b981' : '#ef4444'};">
            <span style="font-weight: bold;">${requirements.noSpaces ? checkIcon : '✗'}</span>
            <span>No spaces allowed</span>
        </div>
    `;
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

/**
 * Handle change password form submission
 */
function handleChangePasswordSubmit(event) {
    event.preventDefault();
    event.stopPropagation();

    const form = event.target;
    const currentPassword = document.getElementById('current-password').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmNewPassword = document.getElementById('confirm-new-password').value;
    const submitBtn = form.querySelector('.change-password-submit-btn');

    console.log('Password change initiated');

    // Validate current password
    if (!currentPassword) {
        showNotification('error', 'Please enter your current password');
        return false;
    }

    // Validate new password using comprehensive validation
    const passwordValidation = validateChangePassword(newPassword);
    if (!passwordValidation.valid) {
        showNotification('error', passwordValidation.error);
        document.getElementById('new-password').style.borderColor = '#ef4444';
        return false;
    }

    // Check if passwords match
    if (newPassword !== confirmNewPassword) {
        showNotification('error', 'New passwords do not match');
        document.getElementById('confirm-new-password').style.borderColor = '#ef4444';
        return false;
    }

    // Check if new password is different from current
    if (currentPassword === newPassword) {
        showNotification('error', 'New password must be different from current password');
        document.getElementById('new-password').style.borderColor = '#ef4444';
        return false;
    }

    // Set button to loading state - CLEAR TEXT, SHOW LOADER
    submitBtn.disabled = true;
    submitBtn.style.pointerEvents = 'none';
    
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    if (btnText) {
        btnText.textContent = '';  // CLEAR TEXT
        btnText.style.visibility = 'hidden';
    }
    if (btnLoader) {
        btnLoader.style.display = 'inline-block';
        btnLoader.innerHTML = '<span style="display: inline-block; width: 16px; height: 16px; border: 3px solid #ffffff; border-top: 3px solid transparent; border-radius: 50%; animation: spin 0.8s linear infinite;"></span>';
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        console.error('CSRF token not found!');
        showNotification('error', 'Security token missing. Please refresh the page.');
        resetButtonState(submitBtn);
        return false;
    }

    // Submit password change via fetch
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            if (btnText) {
                btnText.textContent = 'Password Changed!';
                btnText.style.visibility = 'visible';
            }
            if (btnLoader) {
                btnLoader.style.display = 'none';
            }
            
            showNotification('success', data.message || 'Password changed successfully!');

            form.reset();
            clearChangePasswordValidation();

            setTimeout(() => {
                closeChangePasswordModal();

                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }
            }, 1500);

        } else {
            let errorMessage = data.message || 'Password change failed';

            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                errorMessage = errorMessages.join(', ');

                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field.replace('_', '-'));
                    if (input) {
                        input.style.borderColor = '#ef4444';
                        input.classList.add('error');
                    }
                });
            }

            showNotification('error', errorMessage);
            
            // Reset button on error
            submitBtn.disabled = false;
            submitBtn.style.pointerEvents = 'auto';
            if (btnText) {
                btnText.textContent = 'Change Password';
                btnText.style.visibility = 'visible';
            }
            if (btnLoader) {
                btnLoader.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        showNotification('error', 'Network error. Please check your connection and try again.');
        
        // Reset button on error
        submitBtn.disabled = false;
        submitBtn.style.pointerEvents = 'auto';
        if (btnText) {
            btnText.textContent = 'Change Password';
            btnText.style.visibility = 'visible';
        }
        if (btnLoader) {
            btnLoader.style.display = 'none';
        }
    });

    return false;
}

/**
 * Toggle password visibility
 */
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;

    if (input.type === 'password') {
        input.type = 'text';
        button.textContent = 'Hide';
    } else {
        input.type = 'password';
        button.textContent = 'Show';
    }
}

// ==============================================
// PROFILE VERIFICATION MODAL FUNCTIONS
// ==============================================

function closeVerificationModal() {
    const modal = document.getElementById('verification-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    const form = document.getElementById('verification-form');
    if (form) {
        form.reset();
    }

    clearImagePreviews();
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
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
        preview.style.display = 'none';
    }
}

function showVerificationModal() {
    const modal = document.getElementById('verification-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
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
        { name: 'sex', label: 'Sex' },
        { name: 'role', label: 'Sector' },
        { name: 'dateOfBirth', label: 'Date of Birth' },
        { name: 'barangay', label: 'Barangay' },
        { name: 'completeAddress', label: 'Complete Address' },
        { name: 'emergencyContactName', label: 'Emergency Contact Name' },
        { name: 'emergencyContactPhone', label: 'Emergency Contact Phone' },
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

    // Validate emergency contact phone
    const emergencyPhone = form.querySelector('[name="emergencyContactPhone"]').value;
    if (emergencyPhone) {
        const phoneRegex = /^(\+639|09)\d{9}$/;
        if (!phoneRegex.test(emergencyPhone)) {
            isValid = false;
            showNotification('error', 'Please enter a valid Philippine mobile number for emergency contact (09XXXXXXXXX).');
            return false;
        }
    }

    if (!isValid) {
        showNotification('error', `Please complete all required fields: ${missingFields.join(', ')}`);
        return false;
    }

    // Set button to loading state - CLEAR TEXT, SHOW LOADER
    submitBtn.disabled = true;
    submitBtn.style.pointerEvents = 'none';
    
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    if (btnText) {
        btnText.textContent = '';  // CLEAR TEXT
        btnText.style.visibility = 'hidden';
    }
    if (btnLoader) {
        btnLoader.style.display = 'inline-block';
        btnLoader.innerHTML = '<span style="display: inline-block; width: 16px; height: 16px; border: 3px solid #ffffff; border-top: 3px solid transparent; border-radius: 50%; animation: spin 0.8s linear infinite;"></span>';
    }

    // Create FormData for file upload - EXACTLY as backend expects
    const formData = new FormData();

    // Add form fields with exact names expected by backend
    formData.append('firstName', form.querySelector('[name="firstName"]').value.trim());
    formData.append('lastName', form.querySelector('[name="lastName"]').value.trim());
    formData.append('middleName', form.querySelector('[name="middleName"]').value.trim());
    formData.append('extensionName', form.querySelector('[name="extensionName"]').value.trim());
    formData.append('sex', form.querySelector('[name="sex"]').value);
    formData.append('role', form.querySelector('[name="role"]').value);
    formData.append('dateOfBirth', form.querySelector('[name="dateOfBirth"]').value);
    formData.append('barangay', form.querySelector('[name="barangay"]').value);
    formData.append('completeAddress', form.querySelector('[name="completeAddress"]').value.trim());

    // Add emergency contact fields
    formData.append('emergencyContactName', form.querySelector('[name="emergencyContactName"]').value.trim());
    formData.append('emergencyContactPhone', form.querySelector('[name="emergencyContactPhone"]').value.trim());

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
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Server response:', data);

        if (data.success) {
            // Show success message
            if (btnText) {
                btnText.textContent = 'Verification Submitted!';
                btnText.style.visibility = 'visible';
            }
            if (btnLoader) {
                btnLoader.style.display = 'none';
            }

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
             
             // Reset button on error
             submitBtn.disabled = false;
             submitBtn.style.pointerEvents = 'auto';
             if (btnText) {
                 btnText.textContent = 'Submit for Verification';
                 btnText.style.visibility = 'visible';
             }
             if (btnLoader) {
                 btnLoader.style.display = 'none';
             }
         }
     })
    .catch(error => {
        console.error('Verification error:', error);
        showNotification('error', 'Network error. Please check your connection and try again.');
        
        // Reset button on error
        submitBtn.disabled = false;
        submitBtn.style.pointerEvents = 'auto';
        if (btnText) {
            btnText.textContent = 'Submit for Verification';
            btnText.style.visibility = 'visible';
        }
        if (btnLoader) {
            btnLoader.style.display = 'none';
        }
    });

    return false;
}

// Date of birth change handler to auto-calculate age (for verification form)
const verificationDobInput = document.getElementById('dateOfBirth');
if (verificationDobInput) {
    verificationDobInput.addEventListener('change', function() {
        const dob = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        const ageInput = document.getElementById('age');
        if (ageInput && age >= 0) {
            ageInput.value = age;
        }
    });
}
// ==============================================
// EDIT PROFILE
// ==============================================
/**
 * Load current profile data into edit form
 * UPDATED: Check if username has been changed before to disable editing
 */
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
/**
 * Populate edit form with user data
 * UPDATED: Handle username editability based on whether it was already changed
 */
function populateEditForm(user) {
    // Set readonly fields with current user data
    const usernameInput = document.getElementById('edit-username');
    const profileAvatarLetter = document.getElementById('profile-avatar-letter');
    const usernameEditIndicator = document.getElementById('username-edit-indicator');

    // Populate username (editable only if not changed before)
    if (usernameInput) {
        usernameInput.value = user.username || '';
        usernameInput.setAttribute('data-original-username', user.username || '');

        // Check if username was already changed (look at user's created_at vs username last changed)
        // For now, we'll use a simple approach: check if the system has record of it being changed
        const usernameChanged = user.username_changed_at !== null && user.username_changed_at !== undefined;

        if (usernameChanged) {
            // Username already changed once - disable editing
            usernameInput.disabled = true;
            usernameInput.readOnly = true;
            usernameEditIndicator.style.display = 'flex';
            usernameEditIndicator.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 6c3.314 0 6-1.343 6-3s-2.686-3-6-3-6 1.343-6 3 2.686 3 6 3z"/>
                    <path d="M6 9c-1.654.737-3 1.956-3 3.341 0 2.219 2.686 4 6 4s6-1.781 6-4c0-1.385-1.346-2.604-3-3.341"/>
                </svg>
                <span>Already changed - Cannot edit</span>
            `;
        } else {
            // Username not changed yet - allow editing
            usernameInput.disabled = false;
            usernameInput.readOnly = false;
            usernameEditIndicator.style.display = 'flex';
            usernameEditIndicator.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 6c3.314 0 6-1.343 6-3s-2.686-3-6-3-6 1.343-6 3 2.686 3 6 3z"/>
                    <path d="M6 9c-1.654.737-3 1.956-3 3.341 0 2.219 2.686 4 6 4s6-1.781 6-4c0-1.385-1.346-2.604-3-3.341"/>
                </svg>
                <span>Can only be changed once</span>
            `;

            // Add real-time avatar update on username change
            usernameInput.addEventListener('input', function() {
                if (profileAvatarLetter) {
                    const firstLetter = this.value.charAt(0).toUpperCase();
                    profileAvatarLetter.textContent = firstLetter || 'U';
                }
            });
        }
    }

    // Update profile avatar with first letter of current username
    if (profileAvatarLetter && user.username) {
        profileAvatarLetter.textContent = user.username.charAt(0).toUpperCase();
    }

    // Populate editable fields
    const contactNumberInput = document.getElementById('edit-contact-number');
    const addressInput = document.getElementById('edit-complete-address');
    const barangaySelect = document.getElementById('edit-barangay');

    // Contact number (editable)
    if (contactNumberInput && user.contact_number) {
        contactNumberInput.value = user.contact_number;
    }

    // Complete address (editable)
    if (addressInput && user.complete_address) {
        addressInput.value = user.complete_address;
    }

    // Barangay (editable)
    if (barangaySelect && user.barangay) {
        barangaySelect.value = user.barangay;
    }

    console.log('Profile form populated with user data:', {
        username: user.username,
        contact_number: user.contact_number,
        barangay: user.barangay,
        username_changed_before: user.username_changed_at !== null
    });
}


/**
 * Handle edit profile form submission
 * UPDATED: Include username in submission if it was changed
 */
async function handleEditProfileSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const submitBtn = document.getElementById('save-profile-btn');
    const usernameInput = document.getElementById('edit-username');

    // Get form data
    const formData = new FormData(form);
    const profileData = {};

    // Collect editable fields
    const editableFields = ['username', 'contact_number', 'complete_address', 'barangay'];

    for (let field of editableFields) {
        const value = formData.get(field);
        if (value && value.trim() !== '') {
            profileData[field] = value.trim();
        }
    }

    // Validation - Username
    if (profileData.username) {
        const originalUsername = usernameInput.getAttribute('data-original-username');

        // Check if username is different from original
        if (profileData.username === originalUsername) {
            // Username not changed, don't include in submission
            delete profileData.username;
        } else {
            // Username changed - validate format
            const usernameRegex = /^[a-zA-Z0-9_]{3,50}$/;
            if (!usernameRegex.test(profileData.username)) {
                showNotification('error', 'Username must be 3-50 characters and contain only letters, numbers, and underscores');
                usernameInput.style.borderColor = '#ef4444';
                return;
            }

            // Check if username is available (only if changed)
            try {
                const checkResponse = await fetch('/auth/check-username', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ username: profileData.username })
                });

                const checkData = await checkResponse.json();
                if (!checkData.available) {
                    showNotification('error', 'This username is already taken. Please choose another.');
                    usernameInput.style.borderColor = '#ef4444';
                    return;
                }
            } catch (error) {
                console.error('Username availability check failed:', error);
            }
        }
    }

    // Validation - Contact Number
    const contactNumber = profileData.contact_number;
    if (contactNumber) {
        const phoneRegex = /^(\+639|09)\d{9}$/;
        if (!phoneRegex.test(contactNumber)) {
            showNotification('error', 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX)');
            document.getElementById('edit-contact-number').style.borderColor = '#ef4444';
            return;
        }
    }

    // Validation - Address
    if (!profileData.complete_address) {
        showNotification('error', 'Complete address is required');
        document.getElementById('edit-complete-address').style.borderColor = '#ef4444';
        return;
    }

    // Validation - Barangay
    if (!profileData.barangay) {
        showNotification('error', 'Barangay is required');
        document.getElementById('edit-barangay').style.borderColor = '#ef4444';
        return;
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

        console.log('Profile update response:', data);

        if (data.success) {
            showNotification('success', data.message || 'Profile updated successfully!');

            // Update window.userData with new profile data
            if (window.userData && data.user) {
                Object.assign(window.userData, data.user);

                // Update header with new username if it was changed
                if (profileData.username && window.userData.name) {
                    window.userData.name = data.user.name || data.user.username;
                    // Optionally refresh header display
                    if (typeof refreshProfileVerifyButton === 'function') {
                        refreshProfileVerifyButton();
                    }
                }
            }

            // Close modal after short delay
            setTimeout(() => {
                closeEditProfileModal();
                // Refresh page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }, 1000);

        } else {
            let errorMessage = data.message || 'Failed to update profile';

            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                errorMessage = errorMessages.join(', ');

                // Highlight error fields
                Object.keys(data.errors).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.style.borderColor = '#ef4444';
                    }
                });
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

        // Clear border colors on input
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.style.borderColor = '';
        });
    }
}

/**
 * Open edit profile modal and load data
 */
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

/**
 * Close edit profile modal
 */
function closeEditProfileModal() {
    const modal = document.getElementById('edit-profile-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Reset form
    const form = document.getElementById('edit-profile-form');
    if (form) {
        form.reset();
    }

    // Clear all input error styling
    const inputs = form?.querySelectorAll('input, select, textarea');
    inputs?.forEach(input => {
        input.style.borderColor = '';
        input.classList.remove('error', 'invalid');
    });
}

// Hook: Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const editProfileForm = document.getElementById('edit-profile-form');
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', handleEditProfileSubmit);
    }

    // Close modal on overlay click
    const editProfileModal = document.getElementById('edit-profile-modal');
    if (editProfileModal) {
        editProfileModal.addEventListener('click', function(event) {
            if (event.target === editProfileModal) {
                closeEditProfileModal();
            }
        });
    }

    // Make functions globally available
    window.editProfile = editProfile;
    window.closeEditProfileModal = closeEditProfileModal;
    window.loadCurrentProfileData = loadCurrentProfileData;
    window.handleEditProfileSubmit = handleEditProfileSubmit;
});


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
            <div class="empty-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <polyline points="17 11 19 13 23 9"/>
                </svg>
            </div>
            <h4>Please Log In</h4>
            <p>You need to be logged in to view your applications.</p>
            <button class="quick-action-btn" onclick="closeApplicationsModal(); openAuthModal('login');">
                <svg class="btn-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Log In
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


// Function to remove emojis from text
function removeEmojis(text) {
    if (!text) return '';
    return text
        .replace(/[\u{1F300}-\u{1F9FF}]/gu, '') // Emoji ranges
        .replace(/[\u{2600}-\u{27BF}]/gu, '')   // Miscellaneous Symbols
        .replace(/[\u{2300}-\u{23FF}]/gu, '')   // Miscellaneous Technical
        .replace(/[\u{2000}-\u{206F}]/gu, '')   // General Punctuation
        .trim();
}
// aplication modal
function renderApplicationsInModal(applications) {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    if (applications.length === 0) {
        renderEmptyApplications();
        return;
    }

    grid.innerHTML = applications.map(app => {
        const statusClass = getApplicationStatusClass(app.status);
        const statusLabel = formatApplicationStatus(app.status);

        // Remove emojis from app.type
        const cleanType = removeEmojis(app.type);


        return `
            <div class="application-card ${statusClass}">
                <div class="application-header">
                    <div class="app-type-badge">${app.type}</div>
                    <div class="app-status-tag status-${app.status.toLowerCase().replace(/[_\s]/g, '-')}">
                        ${statusLabel}
                    </div>
                </div>

                <div class="app-reference">
                    <span class="label">Reference:</span>
                    <span class="value">${app.application_number || app.reference_number || 'N/A'}</span>
                </div>

                <p class="app-description">${app.description || 'Application submitted successfully'}</p>

                ${app.full_name || app.livelihood || app.barangay ? `
                    <div class="app-details">
                        ${app.full_name ? `
                            <div class="detail-row">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value">${app.full_name}</span>
                            </div>
                        ` : ''}
                        ${app.livelihood ? `
                            <div class="detail-row">
                                <span class="detail-label">Livelihood:</span>
                                <span class="detail-value">${app.livelihood}</span>
                            </div>
                        ` : ''}
                        ${app.barangay ? `
                            <div class="detail-row">
                                <span class="detail-label">Barangay:</span>
                                <span class="detail-value">${app.barangay}</span>
                            </div>
                        ` : ''}
                    </div>
                ` : ''}

                <div class="application-footer">
                    <div class="application-date">
                        <span class="date-label">Submitted:</span>
                        <span class="date-value">${formatApplicationDate(app.submitted_at || app.date || app.created_at)}</span>
                    </div>
                </div>

                ${app.remarks ? `
                    <div class="app-remarks">
                        <div class="remarks-label">Remarks:</div>
                        <div class="remarks-text">${app.remarks}</div>
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
        <div class="empty-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="12" y1="18" x2="12" y2="12"/>
                <line x1="9" y1="15" x2="15" y2="15"/>
            </svg>
        </div>
        <h4>No Applications Yet</h4>
        <p>Start your journey by exploring our available services and programs designed for farmers and fisherfolks.</p>
        <button class="quick-action-btn" onclick="closeApplicationsModal(); document.getElementById('services').scrollIntoView({ behavior: 'smooth' });">
            <svg class="btn-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            View Available Services
        </button>
    </div>
`;
}

// Helper functions for application display
function getApplicationStatusClass(status) {
    const normalized = status.toLowerCase().replace(/[_\s]/g, '-');
    return `app-status-${normalized}`;
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
/**
 * ENHANCED LOGOUT FUNCTION with confirmation modal
 * Handles both manual logout and automatic session expiration
 * With graceful error handling for already-expired sessions
 */

/**
 * Show logout confirmation modal
 */
function showLogoutConfirmation() {
    // Create modal HTML without close button at the top
    const modalHTML = `
        <div class="logout-confirmation-overlay" id="logout-confirmation-overlay">
            <div class="logout-confirmation-modal">
                <div class="logout-confirmation-header">
                    <div class="logout-title-section">
                        <div class="logout-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                        </div>
                        <h3>Confirm Logout</h3>
                    </div>
                </div>
                <div class="logout-confirmation-body">
                    <p>Are you sure you want to log out?</p>
                </div>
                <div class="logout-confirmation-actions">
                    <button type="button" class="btn-cancel-logout" onclick="closeLogoutConfirmation()">
                        <span class="btn-text">No</span>
                    </button>
                    <button type="button" class="btn-confirm-logout" onclick="confirmLogoutEnhanced()">
                        <span class="btn-text">Yes</span>
                        <span class="btn-loader" style="display: none;">
                            <svg class="spinner" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    `;

    // Add styles if not already present
    if (!document.querySelector('#logout-confirmation-styles')) {
        const styles = document.createElement('style');
        styles.id = 'logout-confirmation-styles';
        styles.textContent = `
            /* Logout Confirmation Modal Styles */
            .logout-confirmation-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10002;
                animation: fadeIn 0.2s ease-out;
                padding: 16px;
                pointer-events: auto;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }
                to {
                    opacity: 1;
                }
            }

            .logout-confirmation-modal {
                background: white;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                width: 100%;
                max-width: 440px;
                animation: slideInScale 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                overflow: hidden;
            }

            @keyframes slideInScale {
                from {
                    transform: scale(0.9);
                    opacity: 0;
                }
                to {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            .logout-confirmation-header {
                padding: 24px 24px 16px;
                border-bottom: 1px solid #f0f0f0;
            }

            .logout-title-section {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .logout-icon {
                width: 48px;
                height: 48px;
                min-width: 48px;
                background: linear-gradient(135deg, #c3e9d0, #a8dfc4);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .logout-icon svg {
                color: #009329;
                width: 24px;
                height: 24px;
            }

            .logout-confirmation-header h3 {
                margin: 0;
                font-size: 20px;
                font-weight: 600;
                color: #1f2937;
            }

            .logout-confirmation-body {
                padding: 24px;
            }

            .logout-confirmation-body p {
                margin: 0;
                font-size: 22px;
                color: #4b5563;
                line-height: 1.6;
                text-align: center;
            }

            .logout-confirmation-actions {
                padding: 16px 24px 24px;
                display: flex;
                gap: 12px;
                justify-content: center;
            }

            .btn-cancel-logout,
            .btn-confirm-logout {
                padding: 12px 24px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                border: none;
                display: flex;
                align-items: center;
                gap: 8px;
                min-width: 140px;
                justify-content: center;
                font-family: inherit;
            }

            .btn-cancel-logout {
                background: #f3f4f6;
                color: #374151;
                border: 1px solid #e5e7eb;
            }

            .btn-cancel-logout:hover:not(:disabled) {
                background: #e5e7eb;
                border-color: #d1d5db;
            }

            .btn-cancel-logout:active:not(:disabled) {
                transform: scale(0.98);
            }

            .btn-confirm-logout {
                background: #009329;
                color: white;
                border: 1px solid #007a21;
            }

            .btn-confirm-logout:hover:not(:disabled) {
                background: #007a21;
                box-shadow: 0 4px 12px rgba(0, 147, 41, 0.3);
                transform: translateY(-1px);
            }

            .btn-confirm-logout:active:not(:disabled) {
                transform: translateY(0) scale(0.98);
            }

            .btn-confirm-logout:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }

            .spinner {
                animation: rotate 1s linear infinite;
            }

            @keyframes rotate {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }

            /* Mobile Responsive */
            @media (max-width: 640px) {
                .logout-confirmation-overlay {
                    padding: 12px;
                }

                .logout-confirmation-modal {
                    max-width: none;
                    width: 100%;
                }

                .logout-confirmation-header {
                    padding: 20px 20px 14px;
                }

                .logout-icon {
                    width: 44px;
                    height: 44px;
                }

                .logout-icon svg {
                    width: 22px;
                    height: 22px;
                }

                .logout-confirmation-header h3 {
                    font-size: 18px;
                }

                .logout-confirmation-body {
                    padding: 20px;
                }

                .logout-confirmation-body p {
                    font-size: 14px;
                }

                .logout-confirmation-actions {
                    padding: 14px 20px 20px;
                    flex-direction: column-reverse;
                    gap: 10px;
                }

                .btn-cancel-logout,
                .btn-confirm-logout {
                    width: 100%;
                    min-width: unset;
                }
            }

            @keyframes fadeOut {
                from {
                    opacity: 1;
                }
                to {
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(styles);
    }

    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Close dropdown menu
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
    }

    // Prevent body scrolling
    document.body.style.overflow = 'hidden';

    // Close on escape key
    document.addEventListener('keydown', handleLogoutEscapeKey);

    // Note: Removed overlay click handler since we want users to explicitly choose Yes or No
}

/**
 * Close logout confirmation modal
 */
function closeLogoutConfirmation() {
    const modal = document.getElementById('logout-confirmation-overlay');
    if (modal) {
        modal.style.animation = 'fadeOut 0.2s ease-out';
        setTimeout(() => {
            modal.remove();
            document.body.style.overflow = '';
        }, 200);
    }

    // Remove event listeners
    document.removeEventListener('keydown', handleLogoutEscapeKey);
}

/**
 * Handle escape key press
 */
function handleLogoutEscapeKey(e) {
    if (e.key === 'Escape') {
        closeLogoutConfirmation();
    }
}

/**
 * Updated logoutUser function
 */
function logoutUser() {
    showLogoutConfirmation();
}

// Add fadeOut animation to styles if not already present
if (!document.querySelector('#logout-fadeout-animation')) {
    const fadeOutStyle = document.createElement('style');
    fadeOutStyle.id = 'logout-fadeout-animation';
    fadeOutStyle.textContent = `
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(fadeOutStyle);
}

console.log('✅ Enhanced Logout functions loaded');
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
        hideAuthMessages();
        return; // Exit early
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

/**
 * Comprehensive contact number validation
 * Validates Philippine mobile numbers and general phone formats
 */
function validateContactNumber(contactNumber) {
    const validation = {
        valid: true,
        error: ''
    };

    // Check if contact number is empty
    if (!contactNumber || contactNumber.trim() === '') {
        validation.valid = false;
        validation.error = 'Contact number is required';
        return validation;
    }

    // Remove whitespace and common separators for validation
    const cleanNumber = contactNumber.trim().replace(/[\s\-\(\)]/g, '');

    // Check if it's exactly 11 digits for Philippine mobile numbers
    if (cleanNumber.length !== 11) {
        validation.valid = false;
        validation.error = 'Contact number must be exactly 11 digits (09XXXXXXXXX)';
        return validation;
    }

    // Check if contains only numbers
    if (!/^[0-9]+$/.test(cleanNumber)) {
        validation.valid = false;
        validation.error = 'Contact number can only contain numbers';
        return validation;
    }

    // Philippine mobile number patterns - only accept numbers starting with 09
    const philippinePatterns = [
        /^09[0-9]{9}$/,                   // 09XXXXXXXXX (11 digits total)
        /^09[0-9]{2}[-\s]?[0-9]{3}[-\s]?[0-9]{4}$/ // 09XX-XXX-XXXX format with separators
    ];

    // Check if it matches Philippine mobile pattern
    const isValidPhilippineNumber = philippinePatterns.some(pattern => pattern.test(cleanNumber));

    if (!isValidPhilippineNumber) {
        validation.valid = false;
        validation.error = 'Please enter a valid Philippine mobile number starting with 09';
        return validation;
    }

    return validation; // Valid Philippine number
}

/**
 * Real-time contact number validation for signup form
 */
let contactCheckTimeout;

function checkContactAvailability(contactNumber) {
    clearTimeout(contactCheckTimeout);

    const contactInput = document.getElementById('signup-contact');
    const contactStatus = document.querySelector('.contact-status');

    if (!contactNumber || contactNumber.trim() === '') {
        if (contactStatus) {
            contactStatus.innerHTML = '';
        }
        contactInput.classList.remove('is-valid', 'is-invalid');
        hideAuthMessages();
        return; // Exit early
    }

    // CLIENT-SIDE VALIDATION RULES
    let errors = [];

    // Remove whitespace and common separators for validation
    const cleanNumber = contactNumber.trim().replace(/[\s\-\(\)]/g, '');

    // 1. Check if it's exactly 11 digits
    if (cleanNumber.length !== 11) {
        errors.push('Contact number must be exactly 11 digits');
    }

    // 2. Check if contains only numbers
    if (!/^[0-9]+$/.test(cleanNumber)) {
        errors.push('Contact number can only contain numbers');
    }

    // 3. Must start with 09 (Philippine mobile)
    if (!cleanNumber.startsWith('09')) {
        errors.push('Contact number must start with 09');
    }

    // Display validation errors immediately
    if (errors.length > 0) {
        if (contactStatus) {
            contactStatus.innerHTML = `<span class="text-danger">✗ ${errors[0]}</span>`;
        }
        contactInput.classList.remove('is-valid');
        contactInput.classList.add('is-invalid');
        return;
    }

    // If validation passes, check availability on server
    if (contactStatus) {
        contactStatus.innerHTML = '<span class="text-info">Checking availability...</span>';
    }

    contactCheckTimeout = setTimeout(() => {
        fetch('/auth/check-contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ contact_number: contactNumber })
        })
        .then(response => response.json())
        .then(data => {
            if (contactStatus) {
                if (data.available) {
                    contactStatus.innerHTML = '<span class="text-success">✓ Contact number available</span>';
                    contactInput.classList.remove('is-invalid');
                    contactInput.classList.add('is-valid');
                } else {
                    contactStatus.innerHTML = '<span class="text-danger">✗ Contact number already registered</span>';
                    contactInput.classList.remove('is-valid');
                    contactInput.classList.add('is-invalid');
                }
            }
        })
        .catch(error => {
            console.error('Error checking contact number:', error);
            if (contactStatus) {
                contactStatus.innerHTML = '<span class="text-muted">⚠ Could not check availability</span>';
            }
        });
    }, 500); // Debounce for 500ms
}

function checkContactValidity(contactNumber) {
    const contactInput = document.getElementById('signup-contact');
    const validation = validateContactNumber(contactNumber);

    if (!contactNumber) {
        contactInput.classList.remove('is-valid', 'is-invalid');
        return;
    }

    if (validation.valid) {
        contactInput.classList.remove('is-invalid');
        contactInput.classList.add('is-valid');
    } else {
        contactInput.classList.remove('is-valid');
        contactInput.classList.add('is-invalid');
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

    // clear everything if empty first
    if (!password || password.trim() === '') {
        strengthBar.className = 'strength-fill';
        strengthBar.style.width = '0%';
        strengthText.textContent = 'Password strength';
        
        // Remove requirements list
        const requirementsList = document.querySelector('.password-requirements-list');
        if (requirementsList) {
            requirementsList.remove();
        }
        
        hideAuthMessages();
        return; // Exit early
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
        // Insert after the password-input-container, not inside it
        const passwordContainer = passwordInput.parentElement;
        const formGroup = passwordContainer.parentElement;
        formGroup.appendChild(requirementsDiv);
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

    // Trim both values
    password = (password || '').trim();
    confirmPassword = (confirmPassword || '').trim();

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

    return validation;
}

// FIXED: Real-time password match validation with proper display
function checkPasswordMatch(password, confirmPassword) {
    const matchStatus = document.querySelector('.password-match-status');
    const confirmInput = document.getElementById('signup-confirm-password');

    if (!matchStatus) return;

    // Trim both inputs
    password = (password || '').trim();
    confirmPassword = (confirmPassword || '').trim();

    // Clear status if confirm password is empty
    if (!confirmPassword || confirmPassword.trim() === '') {
        matchStatus.innerHTML = '';
        matchStatus.style.display = 'none';
        confirmInput.classList.remove('is-valid', 'is-invalid');
        return; // Exit early
    }

    // Only compare when BOTH fields have content
    if (password && confirmPassword) {
        const validation = validatePasswordConfirmation(password, confirmPassword);

        if (validation.valid) {
            // Passwords match - SHOW GREEN SUCCESS
            matchStatus.className = 'password-match-status match';
            matchStatus.innerHTML = '<span style="color: #10b981; font-weight: 600;">✓ Passwords match</span>';
            matchStatus.style.display = 'flex';
            matchStatus.style.alignItems = 'center';
            confirmInput.classList.remove('is-invalid');
            confirmInput.classList.add('is-valid');
        } else {
            // Passwords don't match - SHOW RED ERROR
            matchStatus.className = 'password-match-status no-match';
            matchStatus.innerHTML = `<span style="color: #ef4444; font-weight: 600;">✗ ${validation.error}</span>`;
            matchStatus.style.display = 'flex';
            matchStatus.style.alignItems = 'center';
            confirmInput.classList.remove('is-valid');
            confirmInput.classList.add('is-invalid');
        }
    } else {
        // Not enough data - hide message
        matchStatus.innerHTML = '';
        matchStatus.style.display = 'none';
        confirmInput.classList.remove('is-valid', 'is-invalid');
    }
}

// Make sure event listeners are properly attached
document.addEventListener('DOMContentLoaded', function() {
    const confirmPasswordInput = document.getElementById('signup-confirm-password');
    if (confirmPasswordInput) {
        // Real-time validation on input
        confirmPasswordInput.addEventListener('input', function() {
            const password = document.getElementById('signup-password').value.trim();
            const confirmPassword = this.value.trim();
            console.log('Password match check:', { password: !!password, confirmPassword: !!confirmPassword });
            checkPasswordMatch(password, confirmPassword);
        });

        // Also check on blur
        confirmPasswordInput.addEventListener('blur', function() {
            const password = document.getElementById('signup-password').value.trim();
            const confirmPassword = this.value.trim();
            checkPasswordMatch(password, confirmPassword);
        });
    }

    // When password field changes, re-validate confirmation too
    const passwordInput = document.getElementById('signup-password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const confirmPassword = document.getElementById('signup-confirm-password').value.trim();
            if (confirmPassword) {
                // Only check match if confirm password has value
                checkPasswordMatch(this.value.trim(), confirmPassword);
            }
        });
    }
});


// ==============================================
// FORM VALIDATION
// ==============================================

function validateBasicSignupForm() {
    const username = document.getElementById('signup-username').value.trim();
    const contactNumber = document.getElementById('signup-contact').value.trim();
    const password = document.getElementById('signup-password').value.trim();
    const confirmPassword = document.getElementById('signup-confirm-password').value.trim();
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

   // Contact number validation
    const contactValidation = validateContactNumber(contactNumber);
    if (!contactValidation.valid) {
        errors.push(contactValidation.error);
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
        input.value = '';  // CLEAR VALUES
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

    // Clear password requirements
    const requirementsList = document.querySelector('.password-requirements-list');
    if (requirementsList) {
        requirementsList.remove();
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

// Enhanced Notification System with Sound Support
/**
 * UNIFIED NOTIFICATION SYSTEM - Uses modern toast-notifications.js
 * Provides a consistent, user-friendly notification experience
 * Automatically falls back to console if toast system is not available
 */
function showNotification(type, message) {
    // Use modern toast system if available
    if (typeof toast !== 'undefined' && toast) {
        // Map notification types to toast types
        const typeMap = {
            'success': 'success',
            'error': 'error',
            'info': 'info',
            'warning': 'warning'
        };
        
        const toastType = typeMap[type] || 'info';
        toast.show(message, toastType, { duration: 5500 });
    } else {
        // Fallback to console if toast system not loaded
        console.warn(`[${type.toUpperCase()}] ${message}`);
    }
}

// Note: Sound notifications are now handled by the toast-notifications.js system
// These stub functions are kept for backward compatibility
function playNotificationSound(type) {
    // Sounds are now handled by toast system - no action needed here
}

function playToneNotification(type) {
    // Sounds are now handled by toast system - no action needed here
}

function playSimpleBeep() {
    // Sounds are now handled by toast system - no action needed here
}

// ==============================================
// PLACEHOLDER FUNCTIONS
// ==============================================

function showForgotPassword() {
    // Close auth modal
    closeAuthModal();

    // Open forgot password modal
    const modal = document.getElementById('forgot-password-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Reset to step 1
        resetForgotPasswordModal();

        // Focus on contact input
        setTimeout(() => {
            const contactInput = document.getElementById('forgot-contact');
            if (contactInput) contactInput.focus();
        }, 100);
    }
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
        contact_number: document.getElementById('signup-contact').value.trim(),
        password: document.getElementById('signup-password').value.trim(),
        password_confirmation: document.getElementById('signup-confirm-password').value.trim(),
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

             // IMPORTANT: Clear all input values immediately to stop validation
            document.getElementById('signup-username').value = '';
            document.getElementById('signup-contact').value = '';
            document.getElementById('signup-password').value = '';
            document.getElementById('signup-confirm-password').value = '';
            document.getElementById('agree-terms').checked = false;

            // Reset form after short delay
            setTimeout(() => {
                resetSignupForm();
                hideAuthMessages();
                clearAllValidationUI();  // NEW: Clear all validation UI elements

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

// NEW FUNCTION: Clear all validation UI elements
function clearAllValidationUI() {
    // Clear username status
    const usernameStatus = document.querySelector('.username-status');
    if (usernameStatus) {
        usernameStatus.innerHTML = '';
    }

    // Clear contact status
    const contactStatus = document.querySelector('.contact-status');
    if (contactStatus) {
        contactStatus.innerHTML = '';
    }

    // Clear password strength
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    if (strengthBar) {
        strengthBar.className = 'strength-fill';
         strengthBar.style.width = '0%';
    }
    if (strengthText) {
        strengthText.textContent = 'Password strength';
        strengthText.style.display = 'none';
    }

    // Clear password requirements list
    const requirementsList = document.querySelector('.password-requirements-list');
    if (requirementsList) {
        requirementsList.remove();
    }

    // Clear password match status
    const matchStatus = document.querySelector('.password-match-status');
    if (matchStatus) {
        matchStatus.innerHTML = '';
        matchStatus.style.display = 'none';
        matchStatus.className = 'password-match-status';
    }

    // Clear all input styling
    const inputs = document.querySelectorAll('#signup-form input');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid', 'valid', 'error');
        input.style.borderColor = '';
    });

    // Clear any field errors
    const fieldErrors = document.querySelectorAll('.field-error');
    fieldErrors.forEach(error => error.remove());
}

/**
 * FIXED: Only show real-time errors (.username-status, .contact-status)
 * Don't show form submission errors for username and contact_number
 */
function handleValidationErrors(errors) {
    clearValidationErrors();

    const errorFields = Object.keys(errors);

    errorFields.forEach(field => {
        const fieldMapping = {
            'username': 'signup-username',
            'contact_number': 'signup-contact',
            'password': 'signup-password',
            'password_confirmation': 'signup-confirm-password',
            'terms_accepted': 'agree-terms'
        };

        const fieldName = fieldMapping[field] || field;
        const input = document.getElementById(fieldName);

        if (input) {
            input.classList.add('error', 'is-invalid');
            input.style.borderColor = '#dc3545';

            // SKIP username and contact_number and password - only show real-time validation
            if (field !== 'contact_number' && field !== 'username' && field !== 'password_confirmation') {
                // Only show form error messages for PASSWORD and other fields
                const errorMsg = document.createElement('div');
                errorMsg.className = 'field-error';
                errorMsg.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                errorMsg.style.color = '#dc3545';
                errorMsg.style.fontSize = '12px';
                errorMsg.style.marginTop = '4px';

                const parent = input.closest('.form-group') || input.parentElement;
                parent.appendChild(errorMsg);
            }
        }
    });
}

// ==============================================
// VERIFICATION STATUS POLLING
// ==============================================
// ==============================================
// REAL-TIME VERIFICATION STATUS POLLING
// ==============================================

/**
 * Robust verification-status poller that checks for status updates
 * when user is in 'pending' or 'pending_verification' state.
 *
 * Features:
 * - Auto-starts when verification form is submitted
 * - Probes multiple endpoints for compatibility
 * - Updates UI in real-time when status changes
 * - Smart backoff: stops polling when status is approved/rejected
 * - Respects user logout (stops polling on 401/403)
 */

let verificationStatusPoll = {
    intervalId: null,
    intervalMs: 5000, // Poll every 5 seconds (increased responsiveness)
    maxAttempts: 144, // 12 minutes of polling at 5-second intervals
    attemptCount: 0,
    lastKnownStatus: null,

    /**
     * Probe multiple endpoints to get latest user status
     * Returns: { user, url, stop } object
     */
    async probeEndpoints() {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': tokenMeta ? tokenMeta.content : '',
            'Cache-Control': 'no-cache'
        };

        // Try these endpoints in order
        const endpoints = ['/api/user/profile', '/auth/profile', '/api/profile'];

        for (const url of endpoints) {
            try {
                const res = await fetch(url, {
                    method: 'GET',
                    headers,
                    credentials: 'same-origin'
                });

                // Stop polling if unauthorized (user logged out)
                if (res.status === 401 || res.status === 403) {
                    console.log('User session ended (status:', res.status + ')');
                    return { stop: true, reason: 'unauthorized' };
                }

                if (!res.ok) continue;

                const json = await res.json();

                // Support multiple response shapes
                const user = (json && (
                    json.user ||
                    (json.data && json.data.user) ||
                    (json.data && typeof json.data === 'object' && json.data.status ? json.data : null) ||
                    json
                )) || null;

                return { user, url, stop: false };
            } catch (err) {
                console.debug('Verification probe failed for', url, err.message);
                continue;
            }
        }

        return { user: null, stop: false };
    },

    /**
     * Start polling for verification status changes
     */
    start() {
        // Don't start if already polling
        if (this.intervalId) {
            console.log('Verification polling already active');
            return;
        }

        // Only start if user exists and is in pending state
        if (!window.userData) {
            console.log('No user data available for polling');
            return;
        }

        const status = (window.userData.status || '').toLowerCase();
        if (!['pending', 'pending_verification'].includes(status)) {
            console.log('User status is not pending, polling not needed:', status);
            return;
        }

        this.lastKnownStatus = status;
        this.attemptCount = 0;

        console.log('🔄 Starting verification status polling...', {
            status,
            interval: this.intervalMs + 'ms',
            maxAttempts: this.maxAttempts
        });

        this.intervalId = setInterval(() => this.checkStatus(), this.intervalMs);

        // Perform first check immediately
        this.checkStatus();
    },

    /**
     * Stop polling for status changes
     */
    stop(reason = 'manual') {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
            console.log('⏹️ Stopped verification polling:', reason);
        }
    },

    /**
     * Single status check iteration
     */
    async checkStatus() {
        this.attemptCount++;

        // Auto-stop after max attempts (prevents infinite polling)
        if (this.attemptCount > this.maxAttempts) {
            console.log('Max polling attempts reached');
            this.stop('max_attempts_reached');
            return;
        }

        try {
            const { user, url, stop } = await this.probeEndpoints();

            // Stop if user logged out
            if (stop) {
                this.stop('user_unauthorized');
                return;
            }

            // Skip if no user data returned
            if (!user) {
                console.debug(`Attempt ${this.attemptCount}: No user data returned`);
                return;
            }

            const serverStatus = ((user.status || '') + '').toLowerCase();
            const localStatus = (window.userData.status || '').toLowerCase();

            // Log status changes
            if (serverStatus && serverStatus !== this.lastKnownStatus) {
                console.log('✨ Status changed:', {
                    from: this.lastKnownStatus,
                    to: serverStatus,
                    attempt: this.attemptCount
                });
                this.lastKnownStatus = serverStatus;
            }

            // Check if status has changed from pending
            if (serverStatus && serverStatus !== localStatus) {
                console.log('🎉 Verification status updated!', {
                    old: localStatus,
                    new: serverStatus
                });

                // Update local user data
                window.userData = Object.assign({}, window.userData, user);

                // Refresh UI button
                if (typeof refreshProfileVerifyButton === 'function') {
                    refreshProfileVerifyButton();
                }

                // Handle status-specific notifications
                if (serverStatus === 'verified' || serverStatus === 'approved') {
                    this.handleApproved();
                } else if (serverStatus === 'rejected') {
                    this.handleRejected(user.remarks || 'No reason provided');
                }

                // Stop polling after status change
                this.stop('status_changed_to_' + serverStatus);
            }
        } catch (err) {
            console.error('Verification poll error:', err);
            // Don't stop on error, keep trying
        }
    },

    /**
     * Handle approval notification
     */
    handleApproved() {
    console.log('✅ Verification approved!');

    // 1. Update local user data
    if (window.userData) {
        window.userData.status = 'approved';
    }

    // 2. ✅ UPDATE HEADER IMMEDIATELY (NEW!)
    if (typeof updateHeaderStatusDisplay === 'function') {
        updateHeaderStatusDisplay('approved');
    }

    // 3. Update profile button
    if (typeof refreshProfileVerifyButton === 'function') {
        refreshProfileVerifyButton();
    }

    // 4. Show notification
    if (typeof showNotification === 'function') {
        showNotification('success', 'Your profile has been verified! You can now access all services.');
    }

    // 5. Optional: Close verification modal if open
    const modal = document.getElementById('verification-modal');
    if (modal && modal.style.display !== 'none') {
        setTimeout(() => {
            if (typeof closeVerificationModal === 'function') {
                closeVerificationModal();
            }
        }, 1500);
    }

    // 6. Play success sound
    if (typeof playToneNotification === 'function') {
        playToneNotification('success');
    }
},

    /**
     * Handle rejection notification
     */
  handleRejected(remarks = '') {
    console.log('❌ Verification rejected:', remarks);

    // 1. Update local user data
    if (window.userData) {
        window.userData.status = 'rejected';
    }

    // 2. ✅ UPDATE HEADER IMMEDIATELY (NEW!)
    if (typeof updateHeaderStatusDisplay === 'function') {
        updateHeaderStatusDisplay('rejected');
    }

    // 3. Update profile button
    if (typeof refreshProfileVerifyButton === 'function') {
        refreshProfileVerifyButton();
    }

    // 4. Show error notification
    if (typeof showNotification === 'function') {
        let message = 'Your verification was rejected. You can submit again with updated documents.';
        if (remarks) {
            message += ' Reason: ' + remarks;
        }
        showNotification('error', message);
    }

    // 5. Play error sound
    if (typeof playToneNotification === 'function') {
        playToneNotification('error');
    }
    }
};

/**
 * PUBLIC FUNCTION: Start polling if needed
 * Called after verification form submission
 */
function maybeStartVerificationPoll() {
    try {
        if (!window.userData) {
            console.log('No user data, cannot start polling');
            return;
        }

        const s = (window.userData.status || '').toLowerCase();

        // Only start if status is pending
        if (['pending', 'pending_verification'].includes(s)) {
            verificationStatusPoll.start();
        } else {
            console.log('User status not pending, polling not needed:', s);
        }
    } catch (e) {
        console.error('maybeStartVerificationPoll error:', e);
    }
}

/**
 * PUBLIC FUNCTION: Force stop polling
 * Called on logout or when user manually closes modal
 */
function stopVerificationPolling() {
    verificationStatusPoll.stop('manual_stop');
}

/**
 * PUBLIC FUNCTION: Get current polling status
 * For debugging/monitoring
 */
function getPollingStatus() {
    return {
        active: !!verificationStatusPoll.intervalId,
        attempts: verificationStatusPoll.attemptCount,
        maxAttempts: verificationStatusPoll.maxAttempts,
        lastKnownStatus: verificationStatusPoll.lastKnownStatus,
        interval: verificationStatusPoll.intervalMs + 'ms'
    };
}

// ==============================================
// INTEGRATION HOOKS
// ==============================================

/**
 * Hook: Start polling when page loads if user is pending
 */
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        maybeStartVerificationPoll();
    }, 500);
});

/**
 * Hook: Stop polling when verification modal closes
 */
const originalCloseVerificationModal = window.closeVerificationModal;
window.closeVerificationModal = function() {
    if (originalCloseVerificationModal) {
        originalCloseVerificationModal();
    }
    // Note: Don't stop polling here - user might just be closing the modal
    // Polling should continue checking for status updates
};

/**
 * OPTIONAL: Add visual indicator of polling status
 */
function addPollingIndicator() {
    // Find profile verify button
    const btn = document.getElementById('verify-action-btn');
    if (!btn || btn.classList.contains('verified')) return;

    // Add subtle polling indicator
    const indicator = document.createElement('div');
    indicator.className = 'polling-indicator';
    indicator.innerHTML = `
        <style>
            .polling-indicator {
                display: inline-block;
                width: 8px;
                height: 8px;
                background: #3b82f6;
                border-radius: 50%;
                margin-right: 6px;
                animation: polling-pulse 1.5s ease-in-out infinite;
            }
            @keyframes polling-pulse {
                0%, 100% { opacity: 0.6; transform: scale(1); }
                50% { opacity: 1; transform: scale(1.2); }
            }
        </style>
    `;

    if (btn && !btn.querySelector('.polling-indicator')) {
        btn.insertBefore(indicator, btn.firstChild);
    }
}

// Add indicator when polling starts
const originalStart = verificationStatusPoll.start.bind(verificationStatusPoll);
verificationStatusPoll.start = function() {
    originalStart();
    setTimeout(() => addPollingIndicator(), 100);
};

// ==============================================
// CLEAR VALIDATION MESSAGES WHEN INPUT IS EMPTY
// ==============================================

/**
 * Clear validation messages when input field is empty
 */
function initClearMessagesOnEmpty() {
    // Username field
    const usernameInput = document.getElementById('signup-username');
    if (usernameInput) {
        const originalUsernameCheck = usernameInput.oninput;
        usernameInput.addEventListener('input', function() {
            if (!this.value || this.value.trim() === '') {
                const usernameStatus = document.querySelector('.username-status');
                if (usernameStatus) {
                    usernameStatus.innerHTML = '';
                }
                this.classList.remove('error', 'invalid', 'is-invalid', 'is-valid');
                this.style.borderColor = '';
                hideAuthMessages();
            }
        }, true); // Use capture phase
    }
    
    // Contact number field
    const contactInput = document.getElementById('signup-contact');
    if (contactInput) {
        contactInput.addEventListener('input', function() {
            if (!this.value || this.value.trim() === '') {
                const contactStatus = document.querySelector('.contact-status');
                if (contactStatus) {
                    contactStatus.innerHTML = '';
                }
                this.classList.remove('error', 'invalid', 'is-invalid', 'is-valid');
                this.style.borderColor = '';
                hideAuthMessages();
            }
        }, true); // Use capture phase
    }
    
    // Password field
    const passwordInput = document.getElementById('signup-password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            if (!this.value || this.value.trim() === '') {
                const strengthBar = document.querySelector('.strength-fill');
                const strengthText = document.querySelector('.strength-text');
                if (strengthBar) {
                    strengthBar.className = 'strength-fill';
                    strengthBar.style.width = '0%';
                }
                if (strengthText) {
                    strengthText.textContent = 'Password strength';
                }
                
                // Remove requirements list
                const requirementsList = document.querySelector('.password-requirements-list');
                if (requirementsList) {
                    requirementsList.remove();
                }
                
                this.classList.remove('error', 'invalid', 'is-invalid', 'is-valid');
                this.style.borderColor = '';
                hideAuthMessages();
            }
        }, true); // Use capture phase
    }
    
    // Confirm password field
    const confirmPasswordInput = document.getElementById('signup-confirm-password');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (!this.value || this.value.trim() === '') {
                const matchStatus = document.querySelector('.password-match-status');
                if (matchStatus) {
                    matchStatus.innerHTML = '';
                }
                this.classList.remove('error', 'invalid', 'is-invalid', 'is-valid');
                this.style.borderColor = '';
                hideAuthMessages();
            }
        }, true); // Use capture phase
    }
    
    // Clear all validation on all inputs when empty
    const allAuthInputs = document.querySelectorAll('.auth-form input');
    allAuthInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (!this.value || this.value.trim() === '') {
                this.classList.remove('error', 'invalid', 'is-invalid', 'is-valid');
                this.style.borderColor = '';
                
                const fieldError = this.closest('.form-group')?.querySelector('.field-error');
                if (fieldError) {
                    fieldError.remove();
                }
                
                hideAuthMessages();
            }
        }, true); // Use capture phase
    });
}

// ==============================================
// HOOKS AND LISTENERS
// ==============================================

// Hook: start polling on page load if status already pending
document.addEventListener('DOMContentLoaded', function() {

    // Initialize clear messages when input is empty
    initClearMessagesOnEmpty();

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

    // Contact number availability checker
    const contactInput = document.getElementById('signup-contact');
    if (contactInput) {
        contactInput.addEventListener('input', function() {
            checkContactAvailability(this.value);
        });
    }

    // Password strength and validity checker
    const passwordInput = document.getElementById('signup-password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const trimmedPassword = this.value.trim();  // TRIM
            checkPasswordStrength(trimmedPassword);
            checkPasswordValidity(trimmedPassword);

            // Re-validate confirmation if it has a value
            const confirmPassword = document.getElementById('signup-confirm-password').value.trim();
            if (confirmPassword) {
                checkPasswordMatch(trimmedPassword, confirmPassword);  // USE TRIMMED VALUE
            }
        });
    }

   // Password confirmation checker with validation
    const confirmPasswordInput = document.getElementById('signup-confirm-password');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = document.getElementById('signup-password').value.trim();
            const confirmPassword = this.value.trim();  // TRIM HERE
            checkPasswordMatch(password, confirmPassword);
        });

        // check on blur
        confirmPasswordInput.addEventListener('blur', function() {
            const password = document.getElementById('signup-password').value.trim();
            const confirmPassword = this.value.trim();  // TRIM HERE
            checkPasswordMatch(password, confirmPassword);
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

    // Auto-capitalize text inputs on blur (to ensure actual value is capitalized)
    initAutoCapitalize();
});

// ==============================================
// AUTO-CAPITALIZE TEXT INPUTS
// ==============================================

/**
 * Capitalize the first letter of each word
 */
function capitalizeWords(str) {
    if (!str) return str;
    return str.replace(/\b\w/g, char => char.toUpperCase());
}
/**
 * Disable auto-capitalize on username and password fields on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Prevent auto-capitalize on ALL password and username fields immediately
    const usernameInputs = document.querySelectorAll('input[name="username"], input[id*="username"]');
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    
    // Disable auto-capitalize for username fields
    usernameInputs.forEach(input => {
        input.setAttribute('autocapitalize', 'off');
        input.setAttribute('autocorrect', 'off');
        input.setAttribute('spellcheck', 'false');
        input.style.textTransform = 'none';
    });
    
    // Disable auto-capitalize for password fields
    passwordInputs.forEach(input => {
        input.setAttribute('autocapitalize', 'off');
        input.setAttribute('autocorrect', 'off');
        input.style.textTransform = 'none';
    });
}, true); // Use capture phase to ensure it runs early


/**
 * Initialize auto-capitalize functionality for text inputs
 */
function initAutoCapitalize() {
    // Fields that should NOT be capitalized
    const excludeTypes = ['password', 'url', 'search'];
    const excludeNames = ['password', 'username', 'url'];

    // Check if input should be excluded from capitalization
    function shouldExclude(input) {
        if (excludeTypes.includes(input.type)) return true;
        if (excludeNames.some(name => input.name?.toLowerCase().includes(name))) return true;
        if (input.classList.contains('no-capitalize')) return true;
        if (input.type && !['text', ''].includes(input.type) && input.tagName === 'INPUT') return true;
        return false;
    }

    // Real-time capitalize on input event
    document.addEventListener('input', function(e) {
        const input = e.target;

        // Only process text inputs and textareas
        if (input.tagName !== 'INPUT' && input.tagName !== 'TEXTAREA') return;

        // Skip excluded inputs
        if (shouldExclude(input)) return;

        // Get cursor position
        const start = input.selectionStart;
        const end = input.selectionEnd;

        // Capitalize the value
        const capitalizedValue = capitalizeWords(input.value);

        // Only update if value changed to avoid cursor jump
        if (input.value !== capitalizedValue) {
            input.value = capitalizedValue;
            // Restore cursor position
            input.setSelectionRange(start, end);
        }
    }, true);
}

// Make function globally available
window.capitalizeWords = capitalizeWords;
window.initAutoCapitalize = initAutoCapitalize;

// ==============================================
// FORGOT PASSWORD WITH SMS OTP FUNCTIONS
// ==============================================

let forgotPasswordData = {
    contactNumber: '',
    resetToken: '',
    otpTimer: null,
    otpExpiresAt: null
};

/**
 * Close forgot password modal
 */
function closeForgotPasswordModal() {
    const modal = document.getElementById('forgot-password-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Clear timer
    if (forgotPasswordData.otpTimer) {
        clearInterval(forgotPasswordData.otpTimer);
        forgotPasswordData.otpTimer = null;
    }

    // Reset modal state
    resetForgotPasswordModal();
}

/**
 * Reset forgot password modal to initial state
 */
function resetForgotPasswordModal() {
    // Show step 1, hide others
    document.getElementById('forgot-step-1').style.display = 'block';
    document.getElementById('forgot-step-2').style.display = 'none';
    document.getElementById('forgot-step-3').style.display = 'none';
    document.getElementById('forgot-step-4').style.display = 'none';

    // Clear form fields
    const contactForm = document.getElementById('forgot-contact-form');
    const otpForm = document.getElementById('forgot-otp-form');
    const resetForm = document.getElementById('forgot-reset-form');

    if (contactForm) contactForm.reset();
    if (otpForm) otpForm.reset();
    if (resetForm) resetForm.reset();

    // Clear identifier input validation classes
    const identifierInput = document.getElementById('forgot-identifier');
    if (identifierInput) {
        identifierInput.classList.remove('is-valid', 'is-invalid');
    }

    // Clear OTP inputs
    for (let i = 1; i <= 6; i++) {
        const input = document.getElementById(`forgot-otp-${i}`);
        if (input) input.value = '';
    }

    // Hide username display
    const usernameDisplay = document.getElementById('account-username-display');
    if (usernameDisplay) usernameDisplay.style.display = 'none';

    // Clear messages
    hideForgotMessages();

    // Reset data
    forgotPasswordData = {
        contactNumber: '',
        username: '',
        resetToken: '',
        otpTimer: null,
        otpExpiresAt: null
    };

    // Reset button states
    resetForgotButtonStates();

    // Clear password strength indicators
    const strengthBar = document.querySelector('.reset-password-strength .strength-fill');
    const strengthText = document.querySelector('.reset-password-strength .strength-text');
    if (strengthBar) strengthBar.className = 'strength-fill';
    if (strengthText) strengthText.textContent = 'Password strength';

    const matchStatus = document.querySelector('.reset-password-match');
    if (matchStatus) matchStatus.innerHTML = '';
}

/**
 * Go back to login from forgot password
 */
function backToLogin() {
    closeForgotPasswordModal();
    openAuthModal();
    showLogInForm();
}

/**
 * Go back to step 1 (change number)
 */
function goToStep1() {
    // Clear timer
    if (forgotPasswordData.otpTimer) {
        clearInterval(forgotPasswordData.otpTimer);
        forgotPasswordData.otpTimer = null;
    }

    document.getElementById('forgot-step-1').style.display = 'block';
    document.getElementById('forgot-step-2').style.display = 'none';

    hideForgotMessages();
}

/**
 * Validate forgot identifier input (username or contact number)
 */
function validateForgotIdentifier(value) {
    const input = document.getElementById('forgot-identifier');
    if (!input) return;

    value = value.trim();

    // Check if it looks like a contact number (starts with 09)
    const isContactNumber = /^09[0-9]*$/.test(value);

    if (isContactNumber) {
        // For contact numbers, validate Philippine format
        if (value.length === 11 && /^09[0-9]{9}$/.test(value)) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else if (value.length > 0) {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-valid', 'is-invalid');
        }
    } else {
        // For usernames, validate minimum length
        if (value.length >= 3) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else if (value.length > 0) {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-valid', 'is-invalid');
        }
    }
}

// Keep old function name for backwards compatibility
function validateForgotContact(value) {
    validateForgotIdentifier(value);
}

/**
 * Show forgot password error message
 */
function showForgotError(message) {
    const errorDiv = document.getElementById('forgot-error-message');
    const successDiv = document.getElementById('forgot-success-message');

    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'flex';
    }
    if (successDiv) {
        successDiv.style.display = 'none';
    }
}

/**
 * Show forgot password success message
 */
function showForgotSuccess(message) {
    const successDiv = document.getElementById('forgot-success-message');
    const errorDiv = document.getElementById('forgot-error-message');

    if (successDiv) {
        successDiv.textContent = message;
        successDiv.style.display = 'flex';
    }
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

/**
 * Hide forgot password messages
 */
function hideForgotMessages() {
    const errorDiv = document.getElementById('forgot-error-message');
    const successDiv = document.getElementById('forgot-success-message');

    if (errorDiv) errorDiv.style.display = 'none';
    if (successDiv) successDiv.style.display = 'none';
}

/**
 * Reset forgot password button states
 */
function resetForgotButtonStates() {
    const buttons = ['send-otp-btn', 'verify-otp-btn', 'reset-password-btn'];

    buttons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.disabled = false;
            const btnText = btn.querySelector('.btn-text');
            const btnLoader = btn.querySelector('.btn-loader');
            if (btnText) btnText.style.display = 'inline';
            if (btnLoader) btnLoader.style.display = 'none';
        }
    });
}

/**
 * Set button to loading state
 */
function setForgotButtonLoading(btnId, loadingText) {
    const btn = document.getElementById(btnId);
    if (btn) {
        btn.disabled = true;
        const btnText = btn.querySelector('.btn-text');
        const btnLoader = btn.querySelector('.btn-loader');
        if (btnText) btnText.style.display = 'none';
        if (btnLoader) {
            btnLoader.textContent = loadingText;
            btnLoader.style.display = 'inline';
        }
    }
}

/**
 * Mask contact number for display
 */
function maskContactNumber(contact) {
    if (!contact || contact.length < 11) return contact;
    return contact.substring(0, 4) + '****' + contact.substring(8);
}

/**
 * Start OTP countdown timer
 */
function startOtpTimer(seconds) {
    const countdownEl = document.getElementById('otp-countdown');
    if (!countdownEl) return;

    forgotPasswordData.otpExpiresAt = Date.now() + (seconds * 1000);

    // Clear existing timer
    if (forgotPasswordData.otpTimer) {
        clearInterval(forgotPasswordData.otpTimer);
    }

    function updateTimer() {
        const remaining = Math.max(0, Math.floor((forgotPasswordData.otpExpiresAt - Date.now()) / 1000));
        const minutes = Math.floor(remaining / 60);
        const secs = remaining % 60;

        countdownEl.innerHTML = `Code expires in <strong>${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}</strong>`;

        if (remaining <= 0) {
            clearInterval(forgotPasswordData.otpTimer);
            countdownEl.innerHTML = '<strong style="color: #ef4444;">Code expired</strong>';

            // Enable resend link
            const resendLink = document.getElementById('resend-otp-link');
            if (resendLink) {
                resendLink.style.pointerEvents = 'auto';
                resendLink.style.opacity = '1';
            }
        }
    }

    updateTimer();
    forgotPasswordData.otpTimer = setInterval(updateTimer, 1000);

    // Disable resend link initially
    const resendLink = document.getElementById('resend-otp-link');
    if (resendLink) {
        resendLink.style.pointerEvents = 'none';
        resendLink.style.opacity = '0.5';

        // Enable after 30 seconds
        setTimeout(() => {
            resendLink.style.pointerEvents = 'auto';
            resendLink.style.opacity = '1';
        }, 30000);
    }
}

/**
 * Handle OTP input navigation
 */
function setupOtpInputs() {
    for (let i = 1; i <= 6; i++) {
        const input = document.getElementById(`forgot-otp-${i}`);
        if (!input) continue;

        input.addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');

            // Move to next input
            if (this.value.length === 1 && i < 6) {
                document.getElementById(`forgot-otp-${i + 1}`).focus();
            }

            // Combine OTP
            combineOtpInputs();
        });

        input.addEventListener('keydown', function(e) {
            // Move to previous input on backspace
            if (e.key === 'Backspace' && this.value === '' && i > 1) {
                document.getElementById(`forgot-otp-${i - 1}`).focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedData.replace(/[^0-9]/g, '').substring(0, 6);

            for (let j = 0; j < digits.length; j++) {
                const targetInput = document.getElementById(`forgot-otp-${j + 1}`);
                if (targetInput) {
                    targetInput.value = digits[j];
                }
            }

            // Focus last filled or next empty
            const lastIndex = Math.min(digits.length, 6);
            const focusInput = document.getElementById(`forgot-otp-${lastIndex}`);
            if (focusInput) focusInput.focus();

            combineOtpInputs();
        });
    }
}

/**
 * Combine individual OTP inputs into hidden field
 */
function combineOtpInputs() {
    let otp = '';
    for (let i = 1; i <= 6; i++) {
        const input = document.getElementById(`forgot-otp-${i}`);
        if (input) otp += input.value;
    }

    const combined = document.getElementById('forgot-otp-combined');
    if (combined) combined.value = otp;
}

/**
 * Handle send OTP form submission
 */
async function handleSendOtp(event) {
    event.preventDefault();

    const identifierInput = document.getElementById('forgot-identifier');
    const identifier = identifierInput?.value?.trim();

    if (!identifier || identifier.length < 3) {
        showForgotError('Please enter your username or mobile number');
        return;
    }

    // Check if it's a contact number format
    const isContactNumber = /^09[0-9]{9}$/.test(identifier);
    const looksLikeContactNumber = /^09/.test(identifier);

    // If it looks like a contact number but isn't complete, show error
    if (looksLikeContactNumber && !isContactNumber) {
        showForgotError('Please enter a complete mobile number (e.g., 09123456789)');
        return;
    }

    hideForgotMessages();
    setForgotButtonLoading('send-otp-btn', 'Sending...');

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch('/auth/forgot-password/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ identifier: identifier })
        });

        const data = await response.json();

        if (data.success) {
            // Store the actual contact number returned from server
            forgotPasswordData.contactNumber = data.contact_number;
            forgotPasswordData.username = data.username;
            forgotPasswordData.maskedContact = data.masked_contact || maskContactNumber(data.contact_number);

            // Update masked contact display
            const maskedContactEl = document.getElementById('masked-contact');
            if (maskedContactEl) {
                maskedContactEl.textContent = forgotPasswordData.maskedContact;
            }

            // Show username if available
            const usernameDisplay = document.getElementById('account-username-display');
            const usernameEl = document.getElementById('account-username');
            if (usernameDisplay && usernameEl && data.username) {
                usernameEl.textContent = data.username;
                usernameDisplay.style.display = 'block';
            }

            // Move to step 2
            document.getElementById('forgot-step-1').style.display = 'none';
            document.getElementById('forgot-step-2').style.display = 'block';

            // Start timer
            startOtpTimer(data.expires_in || 300);

            // Focus first OTP input
            setTimeout(() => {
                const firstOtp = document.getElementById('forgot-otp-1');
                if (firstOtp) firstOtp.focus();
            }, 100);

            showForgotSuccess(data.message);
        } else {
            showForgotError(data.message || 'Failed to send OTP');
        }
    } catch (error) {
        console.error('Send OTP error:', error);
        showForgotError('Network error. Please try again.');
    } finally {
        resetForgotButtonStates();
    }
}

/**
 * Handle verify OTP form submission
 */
async function handleVerifyOtp(event) {
    event.preventDefault();

    combineOtpInputs();
    const otp = document.getElementById('forgot-otp-combined')?.value;

    if (!otp || otp.length !== 6) {
        showForgotError('Please enter the complete 6-digit code');
        return;
    }

    hideForgotMessages();
    setForgotButtonLoading('verify-otp-btn', 'Verifying...');

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch('/auth/forgot-password/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                contact_number: forgotPasswordData.contactNumber,
                otp: otp
            })
        });

        const data = await response.json();

        if (data.success) {
            forgotPasswordData.resetToken = data.reset_token;

            // Clear timer
            if (forgotPasswordData.otpTimer) {
                clearInterval(forgotPasswordData.otpTimer);
            }

            // Set hidden fields for reset form
            document.getElementById('reset-token').value = data.reset_token;
            document.getElementById('reset-contact').value = forgotPasswordData.contactNumber;

            // Update account info display in Step 3
            const usernameDisplay = document.getElementById('reset-username-display');
            const contactDisplay = document.getElementById('reset-contact-display');
            if (usernameDisplay) {
                usernameDisplay.textContent = forgotPasswordData.username || '—';
            }
            if (contactDisplay) {
                contactDisplay.textContent = forgotPasswordData.maskedContact || maskContactNumber(forgotPasswordData.contactNumber);
            }

            // Move to step 3
            document.getElementById('forgot-step-2').style.display = 'none';
            document.getElementById('forgot-step-3').style.display = 'block';

            // Focus password input
            setTimeout(() => {
                const passwordInput = document.getElementById('new-password');
                if (passwordInput) passwordInput.focus();
            }, 100);

            showForgotSuccess(data.message);
        } else {
            showForgotError(data.message || 'Invalid OTP');
        }
    } catch (error) {
        console.error('Verify OTP error:', error);
        showForgotError('Network error. Please try again.');
    } finally {
        resetForgotButtonStates();
    }
}

/**
 * Handle resend OTP
 */
async function resendOtp(event) {
    event.preventDefault();

    const resendLink = document.getElementById('resend-otp-link');
    if (resendLink.style.pointerEvents === 'none') return;

    hideForgotMessages();
    resendLink.textContent = 'Sending...';
    resendLink.style.pointerEvents = 'none';

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Send the stored contact number as identifier (it's a valid identifier format)
        const response = await fetch('/auth/forgot-password/resend-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ identifier: forgotPasswordData.contactNumber })
        });

        const data = await response.json();

        if (data.success) {
            // Update stored data in case anything changed
            forgotPasswordData.contactNumber = data.contact_number;
            forgotPasswordData.maskedContact = data.masked_contact || maskContactNumber(data.contact_number);

            // Clear OTP inputs
            for (let i = 1; i <= 6; i++) {
                const input = document.getElementById(`forgot-otp-${i}`);
                if (input) input.value = '';
            }
            document.getElementById('forgot-otp-1')?.focus();

            // Restart timer
            startOtpTimer(data.expires_in || 300);

            showForgotSuccess('New OTP sent successfully!');
        } else {
            showForgotError(data.message || 'Failed to resend OTP');
            resendLink.style.pointerEvents = 'auto';
        }
    } catch (error) {
        console.error('Resend OTP error:', error);
        showForgotError('Network error. Please try again.');
        resendLink.style.pointerEvents = 'auto';
    } finally {
        resendLink.textContent = 'Resend OTP';
    }
}

/**
 * Check reset password strength
 */
function checkResetPasswordStrength(password) {
    const strengthBar = document.querySelector('.reset-password-strength .strength-fill');
    const strengthText = document.querySelector('.reset-password-strength .strength-text');

    if (!strengthBar || !strengthText) return;

    // Reset classes
    strengthBar.className = 'strength-fill';

    if (!password) {
        strengthText.textContent = 'Password strength';
        return;
    }

    let strength = 0;
    const requirements = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[@$!%*?&#^()_+\-=\[\]{};\':\"\\|,.<>\/]/.test(password)
    };

    Object.values(requirements).forEach(met => { if (met) strength++; });

    let label = 'Too weak';
    let className = 'weak';

    if (strength <= 2) {
        label = 'Weak';
        className = 'weak';
    } else if (strength === 3) {
        label = 'Fair';
        className = 'fair';
    } else if (strength === 4) {
        label = 'Good';
        className = 'good';
    } else if (strength === 5) {
        label = 'Strong';
        className = 'strong';
    }

    strengthBar.classList.add(className);
    strengthText.textContent = label;
}

/**
 * Check reset password match
 */
function checkResetPasswordMatch() {
    const password = document.getElementById('new-password')?.value || '';
    const confirmPassword = document.getElementById('confirm-new-password')?.value || '';
    const matchStatus = document.querySelector('.reset-password-match');
    const confirmInput = document.getElementById('confirm-new-password');

    if (!matchStatus) return;

    if (!confirmPassword) {
        matchStatus.innerHTML = '';
        confirmInput?.classList.remove('is-valid', 'is-invalid');
        return;
    }

    if (password === confirmPassword) {
        matchStatus.innerHTML = '<span style="color: #10b981;">✓ Passwords match</span>';
        confirmInput?.classList.remove('is-invalid');
        confirmInput?.classList.add('is-valid');
    } else {
        matchStatus.innerHTML = '<span style="color: #ef4444;">✗ Passwords do not match</span>';
        confirmInput?.classList.remove('is-valid');
        confirmInput?.classList.add('is-invalid');
    }
}

/**
 * Validate reset password
 */
function validateResetPassword(password) {
    const requirements = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[@$!%*?&#^()_+\-=\[\]{};\':\"\\|,.<>\/]/.test(password)
    };

    const allMet = Object.values(requirements).every(v => v);

    return {
        valid: allMet,
        requirements: requirements,
        error: !allMet ? 'Password must be at least 8 characters with uppercase, lowercase, number, and special character' : ''
    };
}

/**
 * Handle reset password form submission
 */
async function handleResetPassword(event) {
    event.preventDefault();

    const password = document.getElementById('new-password')?.value || '';
    const confirmPassword = document.getElementById('confirm-new-password')?.value || '';
    const resetToken = document.getElementById('reset-token')?.value || '';
    const contactNumber = document.getElementById('reset-contact')?.value || forgotPasswordData.contactNumber;

    // Validate password
    const validation = validateResetPassword(password);
    if (!validation.valid) {
        showForgotError(validation.error);
        return;
    }

    // Check passwords match
    if (password !== confirmPassword) {
        showForgotError('Passwords do not match');
        return;
    }

    hideForgotMessages();
    setForgotButtonLoading('reset-password-btn', 'Resetting...');

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch('/auth/forgot-password/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                contact_number: contactNumber,
                reset_token: resetToken,
                password: password,
                password_confirmation: confirmPassword
            })
        });

        const data = await response.json();

        if (data.success) {
            // Move to step 4 (success)
            document.getElementById('forgot-step-3').style.display = 'none';
            document.getElementById('forgot-step-4').style.display = 'block';

            hideForgotMessages();
            showNotification('success', data.message || 'Password reset successfully!');
        } else {
            showForgotError(data.message || 'Failed to reset password');
        }
    } catch (error) {
        console.error('Reset password error:', error);
        showForgotError('Network error. Please try again.');
    } finally {
        resetForgotButtonStates();
    }
}

/**
 * Initialize forgot password event listeners
 */
function initForgotPassword() {
    // Setup OTP inputs
    setupOtpInputs();

    // Contact form submission
    const contactForm = document.getElementById('forgot-contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', handleSendOtp);
    }

    // OTP form submission
    const otpForm = document.getElementById('forgot-otp-form');
    if (otpForm) {
        otpForm.addEventListener('submit', handleVerifyOtp);
    }

    // Reset form submission
    const resetForm = document.getElementById('forgot-reset-form');
    if (resetForm) {
        resetForm.addEventListener('submit', handleResetPassword);
    }

    // Close modal on overlay click
    const modal = document.getElementById('forgot-password-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeForgotPasswordModal();
            }
        });
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('forgot-password-modal');
            if (modal && modal.style.display === 'flex') {
                closeForgotPasswordModal();
            }
        }
    });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', initForgotPassword);


/**
 * Force refresh user session from server
 * Call this after admin makes changes to user account
 */
async function refreshUserSessionFromServer() {
    try {
        const response = await fetch('/api/user/refresh-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success && data.authenticated) {
            console.log('✅ Session refreshed from server:', data.user);

            // Update window.userData with fresh data
            if (window.userData) {
                window.userData.status = data.user.status;
                window.userData.username = data.user.username;
                window.userData.name = data.user.name;
                window.userData.user_type = data.user.user_type;

                console.log('✅ window.userData updated:', window.userData);

                // Update header display
                if (typeof updateHeaderStatusDisplay === 'function') {
                    updateHeaderStatusDisplay(data.user.status);
                }

                // Update profile button
                if (typeof refreshProfileVerifyButton === 'function') {
                    refreshProfileVerifyButton();
                }
            }

            return true;
        } else {
            console.warn('Session refresh failed:', data.message);
            return false;
        }
    } catch (error) {
        console.error('Error refreshing session:', error);
        return false;
    }
}

/**
 * Modified: proceedWithStatusUpdate - now refreshes session after update
 */
function proceedWithStatusUpdate(id, newStatus, remarks) {
    const updateButton = document.querySelector('#updateModal .btn-primary');
    const originalText = updateButton.innerHTML;
    updateButton.innerHTML =
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
    updateButton.disabled = true;

    const endpoint = `/admin/registrations/${id}/update-status`;
    const requestData = {
        status: newStatus,
        remarks: remarks
    };

    console.log('Sending request to:', endpoint);
    console.log('Request data:', requestData);

    fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            console.log('Response Status:', response.status);
            const clonedResponse = response.clone();

            return clonedResponse.json().then(jsonData => {
                console.log('Response JSON:', jsonData);
                return {
                    status: response.status,
                    ok: response.ok,
                    data: jsonData
                };
            }).catch(jsonError => {
                console.warn('Could not parse JSON response:', jsonError);
                return response.text().then(textData => {
                    console.log('Response Text:', textData);
                    return {
                        status: response.status,
                        ok: response.ok,
                        text: textData
                    };
                });
            });
        })
        .then(result => {
            console.log('Processing result:', result);

            if (!result.ok) {
                let errorMessage = `Server Error (${result.status}): `;

                if (result.data && result.data.message) {
                    errorMessage += result.data.message;
                    if (result.data.errors) {
                        console.error('Validation errors:', result.data.errors);
                        errorMessage += '\n\nValidation errors:\n' + JSON.stringify(result.data.errors, null,
                            2);
                    }
                } else if (result.text) {
                    errorMessage += result.text.slice(0, 200);
                } else {
                    errorMessage += result.statusText || 'Unknown error';
                }

                throw new Error(errorMessage);
            }

            if (result.data && result.data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                modal.hide();

                showNotification('success', result.data.message || 'Registration status updated successfully');

                console.log('Update successful, refreshing session...');

                // NEW: Refresh session if user is logged in
                if (window.userData && window.userData.id == id) {
                    // This is the current user's account being updated
                    console.log('Detected current user account update, refreshing session...');
                    
                    refreshUserSessionFromServer().then(() => {
                        console.log('Session refreshed, reloading page...');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    });
                } else {
                    // Different user's account updated, just reload admin page
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } else {
                throw new Error(result.data?.message || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Complete error object:', error);
            console.error('Error message:', error.message);
            showNotification('error', 'Error updating registration status: ' + error.message);
        })
        .finally(() => {
            updateButton.innerHTML = originalText;
            updateButton.disabled = false;
        });
}

/**
 * Modified: proceedWithEditUser - now refreshes session after update
 */
function proceedWithEditUser(form, registrationId) {
    const submitBtn = document.getElementById('editUserSubmitBtn');

    // Show loading state
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...';
    submitBtn.disabled = true;

    // Disable form inputs during submission
    const formInputs = form.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => input.disabled = true);

    // Get values
    const jsonData = {
        first_name: (document.getElementById('edit_first_name')?.value || '').trim(),
        middle_name: (document.getElementById('edit_middle_name')?.value || '').trim(),
        last_name: (document.getElementById('edit_last_name')?.value || '').trim(),
        name_extension: (document.getElementById('edit_name_extension')?.value || '') || null,
        sex: (document.getElementById('edit_sex')?.value || '') || null,
        contact_number: (document.getElementById('edit_contact_number')?.value || '').trim(),
        barangay: (document.getElementById('edit_barangay')?.value || '').trim(),
        complete_address: (document.getElementById('edit_complete_address')?.value || '').trim(),
        user_type: (document.getElementById('edit_user_type')?.value || '').trim(),
        emergency_contact_name: (document.getElementById('edit_emergency_contact_name')?.value || '').trim(),
        emergency_contact_phone: (document.getElementById('edit_emergency_contact_phone')?.value || '').trim()
    };

    console.log('Sending update data:', jsonData);

    // Submit to backend
    fetch(`/admin/registrations/${registrationId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => {
            return response.json().then(data => {
                if (!response.ok) {
                    throw {
                        status: response.status,
                        message: data.message || 'Update failed',
                        errors: data.errors || {}
                    };
                }
                return data;
            });
        })
        .then(data => {
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                if (modal) modal.hide();

                showNotification('success', data.message || 'Registration updated successfully');

                console.log('Update successful, refreshing session...');

                // NEW: Refresh session if user is logged in
                if (window.userData && window.userData.id == registrationId) {
                    console.log('Detected current user account update, refreshing session...');
                    
                    refreshUserSessionFromServer().then(() => {
                        console.log('Session refreshed, reloading page...');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    });
                } else {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } else {
                throw {
                    status: 422,
                    message: data.message || 'Failed to update registration',
                    errors: data.errors || {}
                };
            }
        })
        .catch(error => {
            console.error('Error occurred:', error);

            // Handle validation errors
            if (error.status === 422 && error.errors && typeof error.errors === 'object') {
                const fieldMap = {
                    'first_name': 'edit_first_name',
                    'last_name': 'edit_last_name',
                    'contact_number': 'edit_contact_number',
                    'barangay': 'edit_barangay',
                    'user_type': 'edit_user_type',
                    'emergency_contact_name': 'edit_emergency_contact_name',
                    'emergency_contact_phone': 'edit_emergency_contact_phone',
                    'complete_address': 'edit_complete_address',
                    'middle_name': 'edit_middle_name',
                    'name_extension': 'edit_name_extension'
                };

                Object.keys(error.errors).forEach(field => {
                    const elementId = fieldMap[field];
                    const input = document.getElementById(elementId);
                    if (input) {
                        input.classList.add('is-invalid');
                        const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                        if (existingFeedback) existingFeedback.remove();

                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        const errorMessage = Array.isArray(error.errors[field]) ?
                            error.errors[field][0] :
                            error.errors[field];
                        errorDiv.textContent = errorMessage;
                        input.parentNode.appendChild(errorDiv);
                    }
                });

                showNotification('error', error.message);
            } else {
                showNotification('error', error.message || 'Error updating registration');
            }

            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            formInputs.forEach(input => input.disabled = false);
        });
}

/**
 * Also refresh session after user updates their own profile
 */
async function handleEditProfileSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const submitBtn = document.getElementById('save-profile-btn');
    const usernameInput = document.getElementById('edit-username');

    // Get form data
    const formData = new FormData(form);
    const profileData = {};

    // Collect editable fields
    const editableFields = ['username', 'contact_number', 'complete_address', 'barangay'];

    for (let field of editableFields) {
        const value = formData.get(field);
        if (value && value.trim() !== '') {
            profileData[field] = value.trim();
        }
    }

    // ... validation code here ...

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

            // NEW: Refresh session from server
            const sessionRefreshed = await refreshUserSessionFromServer();

            // Close modal after short delay
            setTimeout(() => {
                closeEditProfileModal();
                if (sessionRefreshed) {
                    // Reload to get updated data everywhere
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
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

        // Clear border colors on input
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.style.borderColor = '';
        });
    }
}

// ==============================================
// GLOBAL FUNCTION EXPORTS
// ==============================================

// Make functions available globally
window.openAuthModal = openAuthModal;
window.closeAuthModal = closeAuthModal;
window.showLogInForm = showLogInForm;
window.showSignUpForm = showSignUpForm;
window.togglePasswordVisibility = togglePasswordVisibility;
window.showForgotPassword = showForgotPassword;
window.closeForgotPasswordModal = closeForgotPasswordModal;
window.backToLogin = backToLogin;
window.goToStep1 = goToStep1;
window.validateForgotContact = validateForgotContact;
window.validateForgotIdentifier = validateForgotIdentifier;
window.resendOtp = resendOtp;
window.checkResetPasswordStrength = checkResetPasswordStrength;
window.checkResetPasswordMatch = checkResetPasswordMatch;
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
// window.logoutUser = logoutUser;
window.showLogoutConfirmation = showLogoutConfirmation;
window.closeLogoutConfirmation = closeLogoutConfirmation;
window.confirmLogout = confirmLogout;
window.confirmLogout = window.confirmLogoutEnhanced;
window.showNotification = showNotification;
window.previewImage = previewImage;
window.refreshProfileVerifyButton = refreshProfileVerifyButton;
window.closeChangePasswordModal = closeChangePasswordModal;
window.handleChangePasswordSubmit = handleChangePasswordSubmit;
window.checkNewPasswordStrength = checkNewPasswordStrength;
window.checkNewPasswordMatch = checkNewPasswordMatch;


console.log('Enhanced Auth.js with Profile Verification and Status Management loaded successfully');