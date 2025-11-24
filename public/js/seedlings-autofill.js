// ==============================================
// SEEDLINGS AUTOFILL SYSTEM - PROFESSIONAL VERSION
// Updated: Enhanced UI, Better Validation, Complete Field Coverage
// File: public/js/seedlings-autofill.js
// ==============================================

/**
 * Auto-fill Seedlings form with user profile data
 */
function autoFillSeedlingsFromProfile() {
    console.log('Auto-filling Seedlings form from user profile...');
    console.log('Available userData:', window.userData); // DEBUG

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
            console.log(`✓ Filled ${fieldName} with: ${value}`);
            return true;
        }
        console.log(`✗ Could not fill ${fieldName} - field:`, !!field, 'value:', value);
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

    // Fill Email
    setFieldValue('email', userData.email);

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
 * Clear auto-filled data
 */
function clearSeedlingsAutoFill() {
    if (confirm('This will clear all form fields. Continue?')) {
        const form = document.getElementById('seedlings-request-form');
        if (form) {
            form.reset();
            // Clear validation warnings
            form.querySelectorAll('.validation-warning').forEach(warning => {
                warning.style.display = 'none';
            });
            form.querySelectorAll('input, select, textarea').forEach(field => {
                field.style.borderColor = '';
            });
        }
        showNotification('info', 'Form cleared successfully');
    }
}

/**
 * Add auto-fill button to Seedlings form
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

    // Create auto-fill button container
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'seedlings-autofill-container';
    buttonContainer.style.cssText = `
        margin-bottom: 20px;
        padding: 15px;
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        border-left: 4px solid #4caf50;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    `;

    buttonContainer.innerHTML = `
        <div class="autofill-info">
            <strong style="color: #2e7d32;">Quick Fill:</strong>
            <span style="color: #558b2f;">Use your verified profile data to auto-complete this form</span>
        </div>
        <div class="autofill-actions">
            <button type="button" id="seedlings-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillSeedlings()"
                    style="
                        background: #4caf50;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        font-weight: 500;
                        margin-right: 8px;
                        transition: all 0.3s ease;
                    "
                    onmouseover="this.style.background='#388e3c'"
                    onmouseout="this.style.background='#4caf50'">
                Use My Profile Data
            </button>
            <button type="button" class="btn-clear"
                    onclick="clearSeedlingsAutoFill()"
                    style="
                        background: #757575;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        font-weight: 500;
                        transition: all 0.3s ease;
                    "
                    onmouseover="this.style.background='#616161'"
                    onmouseout="this.style.background='#757575'">
                Clear Form
            </button>
        </div>
    `;

    // Insert button at the top of the form (after CSRF token)
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