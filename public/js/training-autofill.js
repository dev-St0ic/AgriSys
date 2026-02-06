// ==============================================
// TRAINING AUTO-FILL SYSTEM - PROFESSIONAL VERSION
// Updated: Mobile-Responsive Design
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
 * Show professional clear form confirmation - MOBILE RESPONSIVE
 */
function showClearFormConfirmation(onConfirm) {
    // Create professional confirmation modal
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'training-modal-overlay';

    const modalContent = document.createElement('div');
    modalContent.className = 'training-modal-content';

    modalContent.innerHTML = `
        <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 48 48" style="color: #40916c;">
                <path fill="currentColor" d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm0 36c-8.84 0-16-7.16-16-16s7.16-16 16-16 16 7.16 16 16-7.16 16-16 16z"/>
            </svg>
        </div>

        <h3 class="modal-title">Clear Form Data?</h3>
        
        <p class="modal-text">
            This will remove all information currently in the form. You can always use auto-fill again to repopulate your profile data.
        </p>

        <div class="modal-buttons">
            <button class="clear-form-cancel modal-btn btn-secondary">
                Keep Form
            </button>
            <button class="clear-form-confirm modal-btn btn-primary">
                Clear Form
            </button>
        </div>
    `;

    modalOverlay.appendChild(modalContent);
    document.body.appendChild(modalOverlay);

    // Add animation styles if not present
    if (!document.querySelector('style[data-training-modal-responsive]')) {
        const style = document.createElement('style');
        style.setAttribute('data-training-modal-responsive', 'true');
        style.textContent = `
            /* Modal Responsive Styles */
            .training-modal-overlay {
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
                padding: 16px;
                animation: fadeIn 0.2s ease-out;
            }

            .training-modal-content {
                background: white;
                padding: 24px;
                border-radius: 12px;
                width: 100%;
                max-width: 400px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
                animation: slideUp 0.3s ease-out;
            }

            .modal-icon {
                text-align: center;
                margin-bottom: 16px;
                display: flex;
                justify-content: center;
            }

            .modal-icon svg {
                width: 44px;
                height: 44px;
            }

            .modal-title {
                color: #2d6a4f;
                text-align: center;
                margin: 0 0 12px 0;
                font-size: 18px;
                font-weight: 600;
                line-height: 1.2;
            }

            .modal-text {
                color: #555;
                text-align: center;
                margin: 0 0 24px 0;
                font-size: 14px;
                line-height: 1.5;
            }

            .modal-buttons {
                display: flex;
                gap: 10px;
                flex-direction: column;
            }

            .modal-btn {
                padding: 12px 16px;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.3s ease;
                border: none;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }

            .btn-secondary {
                border: 1px solid #d0d0d0;
                background: #f5f5f5;
                color: #333;
            }

            .btn-secondary:active {
                background: #e8e8e8;
            }

            .btn-primary {
                background: #40916c;
                color: white;
            }

            .btn-primary:active {
                background: #2d6a4f;
            }

            /* Tablet & Desktop */
            @media (min-width: 480px) {
                .training-modal-content {
                    padding: 30px;
                }

                .modal-buttons {
                    flex-direction: row;
                }

                .modal-btn {
                    flex: 1;
                }

                .btn-secondary:hover {
                    background: #e8e8e8;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }

                .btn-primary:hover {
                    background: #2d6a4f;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }
            }

            /* Accessibility */
            .modal-btn:focus {
                outline: 2px solid #40916c;
                outline-offset: 2px;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

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
    confirmBtn.addEventListener('click', () => {
        closeModal();
        onConfirm();
    });

    // Close modal when clicking outside
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });
}

/**
 * Add auto-fill button to Training form - MOBILE RESPONSIVE
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

    console.log('Adding mobile-responsive auto-fill button for user:', window.userData.username);

    // Create auto-fill button container with consistent green styling
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'training-autofill-container';

    buttonContainer.innerHTML = `
        <div class="autofill-info">
            <strong class="autofill-title">Quick Fill Available</strong>
            <span class="autofill-subtitle">Use your verified profile information to auto-complete this form</span>
        </div>
        <div class="autofill-actions">
            <button type="button" id="training-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillTraining()">
                Auto-fill
            </button>
            <button type="button" class="btn-clear"
                    onclick="clearTrainingAutoFill()">
                Clear
            </button>
        </div>
    `;

    // Add mobile-responsive CSS
    if (!document.getElementById('training-mobile-responsive-style')) {
        const style = document.createElement('style');
        style.id = 'training-mobile-responsive-style';
        style.textContent = `
            .training-autofill-container {
                margin-bottom: 20px;
                padding: 16px;
                background-color: #40916c;
                border-radius: 8px;
                display: flex;
                flex-direction: column;
                gap: 14px;
                box-shadow: 0 4px 12px rgba(64, 145, 108, 0.2);
                transition: all 0.3s ease;
            }

            .autofill-info {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .autofill-title {
                color: #ffffff;
                display: block;
                font-size: 14px;
                font-weight: 600;
                line-height: 1.2;
            }

            .autofill-subtitle {
                color: rgba(255, 255, 255, 0.85);
                font-size: 12px;
                line-height: 1.3;
            }

            .autofill-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .btn-autofill,
            .btn-clear {
                flex: 1;
                min-width: 120px;
                padding: 12px 16px;
                border-radius: 6px;
                cursor: pointer;
                font-size: 13px;
                font-weight: 600;
                border: 1px solid rgba(255, 255, 255, 0.5);
                transition: all 0.3s ease;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }

            .btn-autofill {
                background: rgba(255, 255, 255, 0.25);
                color: white;
                backdrop-filter: blur(4px);
            }

            .btn-autofill:active {
                background: rgba(255, 255, 255, 0.35);
                transform: scale(0.98);
            }

            .btn-clear {
                background: rgba(255, 255, 255, 0.15);
                color: white;
                backdrop-filter: blur(4px);
            }

            .btn-clear:active {
                background: rgba(255, 255, 255, 0.25);
                transform: scale(0.98);
            }

            /* Tablet & Desktop (768px and up) */
            @media (min-width: 768px) {
                .training-autofill-container {
                    flex-direction: row;
                    justify-content: space-between;
                    align-items: center;
                    gap: 20px;
                    padding: 18px 20px;
                    margin-bottom: 25px;
                }

                .autofill-info {
                    flex: 1;
                }

                .autofill-title {
                    font-size: 15px;
                    margin-bottom: 4px;
                }

                .autofill-subtitle {
                    font-size: 13px;
                }

                .autofill-actions {
                    gap: 12px;
                    flex-wrap: nowrap;
                }

                .btn-autofill,
                .btn-clear {
                    min-width: auto;
                    flex: 0 1 auto;
                    padding: 10px 20px;
                }

                .btn-autofill:hover {
                    background: rgba(255, 255, 255, 0.35);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                    transform: translateY(-2px);
                }

                .btn-clear:hover {
                    background: rgba(255, 255, 255, 0.25);
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                    transform: translateY(-2px);
                }
            }

            /* Very small screens */
            @media (max-width: 480px) {
                .btn-autofill,
                .btn-clear {
                    min-width: 100px;
                    padding: 10px 12px;
                    font-size: 12px;
                }

                .autofill-title {
                    font-size: 13px;
                }

                .autofill-subtitle {
                    font-size: 11px;
                }
            }

            /* Focus states for accessibility */
            .btn-autofill:focus,
            .btn-clear:focus {
                outline: 2px solid rgba(255, 255, 255, 0.8);
                outline-offset: 2px;
            }
        `;
        document.head.appendChild(style);
    }

    // Insert button at the top of the form
    const firstGroup = form.querySelector('.training-form-group');
    if (firstGroup) {
        firstGroup.parentNode.insertBefore(buttonContainer, firstGroup);
        console.log('Mobile-responsive auto-fill button added to Training form');
    } else {
        // Fallback: insert at beginning of form
        form.insertBefore(buttonContainer, form.firstChild);
        console.log('Mobile-responsive auto-fill button added to Training form (fallback position)');
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

console.log('Training Auto-fill module loaded with mobile-responsive design');