// ==============================================
// SEEDLINGS AUTOFILL SYSTEM - PROFESSIONAL VERSION
// Updated: Enhanced UI, Better Validation, Complete Field Coverage
// ==============================================

/**
 * Auto-fill Seedlings form with user profile data
 */
function autoFillSeedlingsFromProfile() {
    console.log('Auto-filling Seedlings form from user profile...');
    console.log('Available userData:', window.userData);

    // Check if user is logged in and has profile data
    if (!window.userData) {
        console.log('No user data available for auto-fill');
        showNotification('info', 'Please log in to use auto-fill');
        return;
    }

    const form = document.querySelector('#seedlings-request-form');
    if (!form) {
        console.error('Seedlings form not found');
        return;
    }

    const userData = window.userData;
    let filledCount = 0;

    // Helper function to safely set field value
    function setFieldValue(fieldName, value) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field && value) {
            field.value = value;

            // Trigger change event for selects and validation
            if (field.tagName === 'SELECT') {
                field.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Trigger input event for validation
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('blur', { bubbles: true }));

            // Add visual feedback
            field.style.backgroundColor = '#f0f8ff';
            setTimeout(() => {
                field.style.backgroundColor = '';
            }, 2000);

            filledCount++;
            console.log(`Filled ${fieldName} with: ${value}`);
            return true;
        }
        console.log(`Could not fill ${fieldName} - field:`, !!field, 'value:', value);
        return false;
    }

    // Fill First Name
    setFieldValue('first_name', userData.first_name);

    // Fill Middle Name
    setFieldValue('middle_name', userData.middle_name);

    // Fill Last Name
    setFieldValue('last_name', userData.last_name);

    // Fill Extension Name
    setFieldValue('extension_name', userData.extension_name || userData.name_extension);

    // Fill Sex/Gender
    setFieldValue('sex', userData.sex || userData.gender);

    // Fill Age/Date of Birth
    setFieldValue('age', userData.age);
    setFieldValue('date_of_birth', userData.date_of_birth || userData.dob);

    // Fill Civil Status
    setFieldValue('civil_status', userData.civil_status);

    // Fill Mobile Number
    setFieldValue('mobile', userData.contact_number || userData.mobile_number || userData.phone || userData.mobile);

    // Fill Barangay
    setFieldValue('barangay', userData.barangay);

    // Fill Complete Address
    setFieldValue('address', userData.complete_address || userData.address);

    // Fill Additional fields if available
    setFieldValue('occupation', userData.occupation);
    setFieldValue('educational_attainment', userData.educational_attainment);

    // Show results
    if (filledCount > 0) {
        showNotification('success', `Successfully auto-filled ${filledCount} field${filledCount > 1 ? 's' : ''} from your profile`);
        console.log(`Auto-filled ${filledCount} Seedlings form fields`);
    } else {
        console.warn('No fields were auto-filled. userData:', userData);
        showNotification('warning', 'Could not auto-fill form. Please complete your profile verification first.');
    }
}

/**
 * Fetch fresh user profile data from backend and auto-fill
 */
async function fetchAndAutoFillSeedlings() {
    console.log('Fetching fresh user profile data...');

    // Show loading state
    const btn = document.getElementById('seedlings-autofill-btn');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = 'Loading...';
        btn.disabled = true;
    }

    try {
        const response = await fetch('/api/user/profile', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Fetched user profile:', data);

        if (data.success && data.user) {
            // Update window.userData with fresh data
            window.userData = Object.assign({}, window.userData, data.user);

            // Now auto-fill
            autoFillSeedlingsFromProfile();
        } else {
            showNotification('error', 'Could not load profile data');
        }

    } catch (error) {
        console.error('Error fetching profile:', error);
        // Fall back to cached userData
        console.log('Falling back to cached userData');
        autoFillSeedlingsFromProfile();
    } finally {
        // Restore button
        if (btn) {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
}

/**
 * Clear auto-filled data - Professional approach
 */
function clearSeedlingsAutoFill() {
    const form = document.getElementById('seedlings-request-form');
    if (!form) return;

    showClearFormConfirmation(() => {
        // Clear form data
        form.reset();

        // Clear validation warnings
        form.querySelectorAll('.validation-warning').forEach(warning => {
            warning.style.display = 'none';
        });

        // Reset field styling
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.style.borderColor = '';
            field.style.backgroundColor = '';
        });

        // Hide any error messages
        clearSeedlingsErrors();

        // Show completion message
        showNotification('info', 'Form has been cleared and is ready for new information');
        console.log('Seedlings form cleared successfully');
    });
}

/**
 * Show professional clear form confirmation
 */
function showClearFormConfirmation(onConfirm) {
    // Create professional confirmation modal
    const modalOverlay = document.createElement('div');
    modalOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;

    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 12px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        animation: slideUp 0.3s ease-out;
    `;

    modalContent.innerHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
            <svg width="48" height="48" viewBox="0 0 48 48" style="color: #40916c; margin: 0 auto;">
                <path fill="currentColor" d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm0 36c-8.84 0-16-7.16-16-16s7.16-16 16-16 16 7.16 16 16-7.16 16-16 16z"/>
            </svg>
        </div>

        <h3 style="color: #2d6a4f; text-align: center; margin: 0 0 12px 0; font-size: 18px;">Clear Form Data?</h3>
        
        <p style="color: #555; text-align: center; margin: 0 0 24px 0; font-size: 14px; line-height: 1.5;">
            This will remove all information currently in the form. You can always use auto-fill again to repopulate your profile data.
        </p>

        <div style="display: flex; gap: 12px;">
            <button class="clear-form-cancel" style="
                flex: 1;
                padding: 12px 16px;
                border: 1px solid #d0d0d0;
                background: #f5f5f5;
                color: #333;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 500;
                font-size: 14px;
                transition: all 0.2s ease;
            ">
                Keep Form
            </button>
            <button class="clear-form-confirm" style="
                flex: 1;
                padding: 12px 16px;
                border: none;
                background: #40916c;
                color: white;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.2s ease;
            ">
                Clear Form
            </button>
        </div>
    `;

    modalOverlay.appendChild(modalContent);
    document.body.appendChild(modalOverlay);

    // Add animation styles if not present
    if (!document.querySelector('style[data-autofill-modal]')) {
        const style = document.createElement('style');
        style.setAttribute('data-autofill-modal', 'true');
        style.textContent = `
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Handle button clicks
    const cancelBtn = modalContent.querySelector('.clear-form-cancel');
    const confirmBtn = modalContent.querySelector('.clear-form-confirm');

    const closeModal = () => {
        modalOverlay.style.opacity = '0';
        modalOverlay.style.transition = 'opacity 0.2s ease';
        setTimeout(() => modalOverlay.remove(), 200);
    };

    cancelBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('mouseover', function() {
        this.style.background = '#e8e8e8';
    });
    cancelBtn.addEventListener('mouseout', function() {
        this.style.background = '#f5f5f5';
    });

    confirmBtn.addEventListener('click', () => {
        closeModal();
        onConfirm();
    });
    confirmBtn.addEventListener('mouseover', function() {
        this.style.background = '#2d6a4f';
    });
    confirmBtn.addEventListener('mouseout', function() {
        this.style.background = '#40916c';
    });

    // Close modal when clicking outside
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });
}

/**
 * Add auto-fill button to Seedlings form
 * Professional styling matching Training form
 */
function addAutoFillButtonToSeedlings() {
    const form = document.querySelector('#seedlings-request-form');
    if (!form) return;

    // Check if button already exists
    if (document.getElementById('seedlings-autofill-btn')) return;

    // Only show button if user is logged in
    if (!window.userData) {
        console.log('No userData - skipping auto-fill button');
        return;
    }

    console.log('Adding auto-fill button for user:', window.userData.username);

    // Create auto-fill button container with consistent green styling
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'seedlings-autofill-container';
    buttonContainer.style.cssText = `
        margin-bottom: 25px;
        padding: 18px 20px;
        background-color: #40916c;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(64, 145, 108, 0.2);
    `;

    buttonContainer.innerHTML = `
        <div class="autofill-info">
            <strong style="color: #ffffff; display: block; margin-bottom: 4px; font-size: 15px;">Quick Fill Available</strong>
            <span style="color: rgba(255, 255, 255, 0.9); font-size: 13px;">Use your verified profile information to auto-complete this form</span>
        </div>
        <div class="autofill-actions">
            <button type="button" id="seedlings-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillSeedlings()"
                    style="
                        background: rgba(255, 255, 255, 0.25);
                        color: white;
                        border: 1px solid rgba(255, 255, 255, 0.5);
                        padding: 10px 20px;
                        border-radius: 6px;
                        cursor: pointer;
                        font-size: 13px;
                        font-weight: 600;
                        margin-right: 10px;
                        transition: all 0.3s ease;
                        backdrop-filter: blur(4px);
                    "
                    onmouseover="this.style.background='rgba(255, 255, 255, 0.35)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.15)'"
                    onmouseout="this.style.background='rgba(255, 255, 255, 0.25)'; this.style.boxShadow='none'">
                Auto-fill
            </button>
            <button type="button" class="btn-clear"
                    onclick="clearSeedlingsAutoFill()"
                    style="
                        background: rgba(255, 255, 255, 0.15);
                        color: white;
                        border: 1px solid rgba(255, 255, 255, 0.5);
                        padding: 10px 20px;
                        border-radius: 6px;
                        cursor: pointer;
                        font-size: 13px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                        backdrop-filter: blur(4px);
                    "
                    onmouseover="this.style.background='rgba(255, 255, 255, 0.25)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.15)'"
                    onmouseout="this.style.background='rgba(255, 255, 255, 0.15)'; this.style.boxShadow='none'">
                Clear
            </button>
        </div>
    `;

    // Insert button at the top of the form
    const firstLabel = form.querySelector('label');
    if (firstLabel) {
        firstLabel.parentNode.insertBefore(buttonContainer, firstLabel);
        console.log('Auto-fill button added to Seedlings form');
    } else {
        // Fallback: insert at beginning of form after hidden inputs
        const hiddenInputs = form.querySelectorAll('input[type="hidden"]');
        if (hiddenInputs.length > 0) {
            const lastHidden = hiddenInputs[hiddenInputs.length - 1];
            lastHidden.parentNode.insertBefore(buttonContainer, lastHidden.nextSibling);
        } else {
            form.insertBefore(buttonContainer, form.firstChild);
        }
        console.log('Auto-fill button added to Seedlings form (fallback position)');
    }
}

/**
 * Initialize auto-fill functionality
 */
function initializeSeedlingsAutoFill() {
    console.log('Initializing Seedlings auto-fill functionality...');

    // Add auto-fill button when form is displayed
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' &&
                mutation.attributeName === 'style') {
                const seedlingsForm = document.getElementById('seedlings-form');
                if (seedlingsForm && seedlingsForm.style.display !== 'none') {
                    setTimeout(addAutoFillButtonToSeedlings, 100);
                }
            }
        });
    });

    const seedlingsSection = document.getElementById('seedlings-form');
    if (seedlingsSection) {
        observer.observe(seedlingsSection, {
            attributes: true,
            attributeFilter: ['style']
        });
        console.log('MutationObserver attached to Seedlings form');
    } else {
        console.warn('Seedlings section not found');
    }

    // Also check immediately if form is already visible
    if (seedlingsSection && seedlingsSection.style.display !== 'none') {
        setTimeout(addAutoFillButtonToSeedlings, 100);
    }
}

/**
 * Validate required fields
 */
function validateSeedlingsForm() {
    const form = document.getElementById('seedlings-request-form');
    if (!form) return true;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        const value = field.value.trim();
        const warning = document.getElementById(field.id + '-warning');

        if (!value) {
            field.style.borderColor = '#ff6b6b';
            if (warning) warning.style.display = 'block';
            isValid = false;
        } else {
            field.style.borderColor = '';
            if (warning) warning.style.display = 'none';
        }
    });

    if (!isValid) {
        showNotification('error', 'Please fill in all required fields');
    }

    return isValid;
}

// Export functions for global access
window.autoFillSeedlingsFromProfile = autoFillSeedlingsFromProfile;
window.fetchAndAutoFillSeedlings = fetchAndAutoFillSeedlings;
window.clearSeedlingsAutoFill = clearSeedlingsAutoFill;
window.validateSeedlingsForm = validateSeedlingsForm;
window.addAutoFillButtonToSeedlings = addAutoFillButtonToSeedlings;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready - initializing Seedlings auto-fill...');
    initializeSeedlingsAutoFill();
});

// Also initialize on window load for additional safety
window.addEventListener('load', function() {
    setTimeout(initializeSeedlingsAutoFill, 500);
});

console.log('Seedlings Auto-fill module loaded');