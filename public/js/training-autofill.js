// ==============================================
// TRAINING AUTO-FILL SYSTEM - PROFESSIONAL VERSION
// Updated: Consistent Green Styling, Professional Clear Form
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

    // Email removed - not required for training applications

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
 * Clear auto-filled data - Professional approach
 */
function clearTrainingAutoFill() {
    const form = document.getElementById('training-request-form');
    if (!form) return;

    // Show professional confirmation modal
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
        clearTrainingErrors();

        // Show completion message
        showNotification('info', 'Form has been cleared and is ready for new information');
        console.log('Training form cleared successfully');
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

    // Create auto-fill button container with consistent green styling
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'training-autofill-container';
    buttonContainer.style.cssText = `
        margin-bottom: 25px;
        padding: 18px 20px;
        background-color:  #40916c;
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
            <button type="button" id="training-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillTraining()"
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
                    onclick="clearTrainingAutoFill()"
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
window.addAutoFillButtonToTraining = addAutoFillButtonToTraining;

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