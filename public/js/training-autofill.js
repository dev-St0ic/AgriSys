// ==============================================
// TRAINING AUTO-FILL SYSTEM - PROFESSIONAL VERSION
// Updated: Enhanced UI, Better Validation, Complete Field Coverage
// File: public/js/training-autofill.js
// ==============================================

/**
 * Auto-fill Training form with user profile data
 */
function autoFillTrainingFromProfile() {
    console.log('Auto-filling Training form from user profile...');
    console.log('Available userData:', window.userData); // DEBUG

    // Check if user is logged in and has profile data
    if (!window.userData) {
        console.log('No user data available for auto-fill');
        showNotification('info', 'Please log in to use auto-fill');
        return;
    }

    const form = document.querySelector('#training-request-form');
    if (!form) {
        console.error('Training form not found');
        return;
    }

    const userData = window.userData;
    let filledCount = 0;

    // Helper function to safely set field value
    function setFieldValue(fieldName, value) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field && value) {
            field.value = value;

            // Trigger change event for selects
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

    // Fill Name Extension
    setFieldValue('name_extension', userData.name_extension || userData.extension_name);

    // Fill Sex/Gender
    setFieldValue('sex', userData.sex || userData.gender);

    // Fill Age
    setFieldValue('age', userData.age);

    // Fill Date of Birth
    setFieldValue('date_of_birth', userData.date_of_birth || userData.dob);

    // Fill Civil Status
    setFieldValue('civil_status', userData.civil_status);

    // Fill Contact Number
    setFieldValue('contact_number', userData.contact_number || userData.mobile || userData.phone);

    // Fill Email
    setFieldValue('email', userData.email);

    // Fill Barangay
    setFieldValue('barangay', userData.barangay);

    // Fill Occupation
    setFieldValue('occupation', userData.occupation);

    // Fill Educational Attainment
    setFieldValue('educational_attainment', userData.educational_attainment);

    // Show results
    if (filledCount > 0) {
        showNotification('success', `Successfully auto-filled ${filledCount} field${filledCount > 1 ? 's' : ''} from your profile`);
        console.log(`Auto-filled ${filledCount} Training form fields`);
    } else {
        console.warn('No fields were auto-filled. userData:', userData);
        showNotification('warning', 'Could not auto-fill form. Please complete your profile verification first.');
    }
}

/**
 * Fetch fresh user profile data from backend and auto-fill
 */
async function fetchAndAutoFillTraining() {
    console.log('Fetching fresh user profile data...');

    // Show loading state
    const btn = document.getElementById('training-autofill-btn');
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
            autoFillTrainingFromProfile();
        } else {
            showNotification('error', 'Could not load profile data');
        }

    } catch (error) {
        console.error('Error fetching profile:', error);
        // Fall back to cached userData
        console.log('Falling back to cached userData');
        autoFillTrainingFromProfile();
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
function clearTrainingAutoFill() {
    if (confirm('This will clear all form fields. Continue?')) {
        const form = document.getElementById('training-request-form');
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
 * Add auto-fill button to Training form
 */
function addAutoFillButtonToTraining() {
    const form = document.querySelector('#training-request-form');
    if (!form) return;

    // Check if button already exists
    if (document.getElementById('training-autofill-btn')) return;

    // Only show button if user is logged in
    if (!window.userData) {
        console.log('No userData - skipping auto-fill button');
        return;
    }

    console.log('Adding auto-fill button for user:', window.userData.username);

    // Create auto-fill button container
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'training-autofill-container';
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
            <button type="button" id="training-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillTraining()"
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
                    onclick="clearTrainingAutoFill()"
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

    // Insert button at the top of the form
    const firstGroup = form.querySelector('.training-form-group');
    if (firstGroup) {
        firstGroup.parentNode.insertBefore(buttonContainer, firstGroup);
        console.log('Auto-fill button added to Training form');
    } else {
        // Fallback: insert at beginning of form
        form.insertBefore(buttonContainer, form.firstChild);
        console.log('Auto-fill button added to Training form (fallback position)');
    }
}

/**
 * Initialize auto-fill functionality
 */
function initializeTrainingAutoFill() {
    console.log('Initializing Training auto-fill functionality...');

    // Add auto-fill button when form is displayed
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' &&
                mutation.attributeName === 'style') {
                const trainingForm = document.getElementById('training-form');
                if (trainingForm && trainingForm.style.display !== 'none') {
                    setTimeout(addAutoFillButtonToTraining, 100);
                }
            }
        });
    });

    const trainingSection = document.getElementById('training-form');
    if (trainingSection) {
        observer.observe(trainingSection, {
            attributes: true,
            attributeFilter: ['style']
        });
        console.log('MutationObserver attached to Training form');
    } else {
        console.warn('Training section not found');
    }

    // Also check immediately if form is already visible
    if (trainingSection && trainingSection.style.display !== 'none') {
        setTimeout(addAutoFillButtonToTraining, 100);
    }
}

/**
 * Validate required fields
 */
function validateTrainingForm() {
    const form = document.getElementById('training-request-form');
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
window.autoFillTrainingFromProfile = autoFillTrainingFromProfile;
window.fetchAndAutoFillTraining = fetchAndAutoFillTraining;
window.clearTrainingAutoFill = clearTrainingAutoFill;
window.validateTrainingForm = validateTrainingForm;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready - initializing Training auto-fill...');
    initializeTrainingAutoFill();
});

// Also initialize on window load for additional safety
window.addEventListener('load', function() {
    setTimeout(initializeTrainingAutoFill, 500);
});

console.log('Training Auto-fill module loaded');