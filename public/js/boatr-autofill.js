// ==============================================
// UPDATED BOATR AUTOFILL SYSTEM - PROFESSIONAL VERSION
// File: public/js/boatr-autofill.js
// UPDATED: Mobile-Responsive Design
// ==============================================

/**
 * Auto-fill BoatR form with user profile data
 * UPDATED: Professional styling 
 */
function autoFillBoatRFromProfile() {
    console.log('Auto-filling BoatR form from user profile...');
    console.log('Available userData:', window.userData);

    if (!window.userData) {
        console.log('No user data available for auto-fill');
        showNotification('info', 'Please log in to use auto-fill');
        return;
    }

    const form = document.querySelector('#boatr-registration-form');
    if (!form) {
        console.error('BoatR form not found');
        return;
    }

    const userData = window.userData;
    let filledCount = 0;

    function setFieldValue(fieldName, value) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field && value) {
            field.value = value;

            if (field.tagName === 'SELECT') {
                field.dispatchEvent(new Event('change', { bubbles: true }));
            }

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

    // Fill all profile fields
    setFieldValue('first_name', userData.first_name);
    setFieldValue('middle_name', userData.middle_name);
    setFieldValue('last_name', userData.last_name);
    setFieldValue('name_extension', userData.name_extension || userData.extension_name);
    setFieldValue('sex', userData.sex || userData.gender);
    setFieldValue('age', userData.age);
    setFieldValue('date_of_birth', userData.date_of_birth || userData.dob);
    setFieldValue('civil_status', userData.civil_status);
    setFieldValue('contact_number', userData.contact_number || userData.mobile || userData.phone);
    setFieldValue('barangay', userData.barangay);
    setFieldValue('occupation', userData.occupation);
    setFieldValue('educational_attainment', userData.educational_attainment);

    if (filledCount > 0) {
        showNotification('success', `Successfully auto-filled ${filledCount} field${filledCount > 1 ? 's' : ''} from your profile`);
        console.log(`Successfully auto-filled ${filledCount} BoatR form fields`);
    } else {
        console.warn('No fields were auto-filled. userData:', userData);
        showNotification('warning', 'Could not auto-fill form. Please complete your profile verification first.');
    }
}

/**
 * Fetch fresh user profile data from backend and auto-fill
 */
async function fetchAndAutoFillBoatR() {
    console.log('Fetching fresh user profile data...');

    const btn = document.getElementById('boatr-autofill-btn');
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
            window.userData = Object.assign({}, window.userData, data.user);
            autoFillBoatRFromProfile();
        } else {
            showNotification('error', 'Could not load profile data');
        }

    } catch (error) {
        console.error('Error fetching profile:', error);
        console.log('Falling back to cached userData');
        autoFillBoatRFromProfile();
    } finally {
        if (btn) {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
}

/**
 * Clear auto-filled data - Professional approach
 */
function clearBoatRAutoFill() {
    const form = document.getElementById('boatr-registration-form');
    if (!form) return;

    showClearFormConfirmation(() => {
        form.reset();

        form.querySelectorAll('.validation-warning').forEach(warning => {
            warning.style.display = 'none';
        });

        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.style.borderColor = '';
            field.style.backgroundColor = '';
        });

        clearBoatRErrors();

        showNotification('info', 'Form has been cleared and is ready for new information');
        console.log('BoatR form cleared successfully');
    });
}

/**
 * Show professional clear form confirmation - MOBILE RESPONSIVE
 */
function showClearFormConfirmation(onConfirm) {
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'boatr-modal-overlay';

    const modalContent = document.createElement('div');
    modalContent.className = 'boatr-modal-content';

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

    if (!document.querySelector('style[data-autofill-modal-responsive]')) {
        const style = document.createElement('style');
        style.setAttribute('data-autofill-modal-responsive', 'true');
        style.textContent = `
            /* Modal Responsive Styles */
            .boatr-modal-overlay {
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

            .boatr-modal-content {
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
                .boatr-modal-content {
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

    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });
}

/**
 * Add auto-fill button to BoatR form
 * UPDATED: Mobile-responsive and professional styling
 */
function addAutoFillButtonToBoatR() {
    const form = document.querySelector('#boatr-registration-form');
    if (!form) return;

    if (document.getElementById('boatr-autofill-btn')) return;

    if (!window.userData) {
        console.log('No userData - skipping auto-fill button');
        return;
    }

    console.log('Adding mobile-responsive auto-fill button for user:', window.userData.username);

    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'boatr-autofill-container';

    buttonContainer.innerHTML = `
        <div class="autofill-info">
            <strong class="autofill-title">Quick Fill Available</strong>
            <span class="autofill-subtitle">Use your verified profile information to auto-complete this form</span>
        </div>
        <div class="autofill-actions">
            <button type="button" id="boatr-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillBoatR()">
                Auto-fill
            </button>
            <button type="button" class="btn-clear"
                    onclick="clearBoatRAutoFill()">
                Clear
            </button>
        </div>
    `;

    // Add mobile-responsive CSS
    if (!document.getElementById('boatr-mobile-responsive-style')) {
        const style = document.createElement('style');
        style.id = 'boatr-mobile-responsive-style';
        style.textContent = `
            .boatr-autofill-container {
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
                .boatr-autofill-container {
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

    const firstFormGroup = form.querySelector('.boatr-form-group');
    if (firstFormGroup) {
        firstFormGroup.parentNode.insertBefore(buttonContainer, firstFormGroup);
        console.log('Mobile-responsive auto-fill button added to BoatR form');
    } else {
        form.insertBefore(buttonContainer, form.firstChild);
        console.log('Mobile-responsive auto-fill button added to BoatR form (fallback position)');
    }
}

/**
 * Initialize auto-fill functionality
 */
function initializeBoatRAutoFill() {
    console.log('Initializing BoatR auto-fill functionality...');

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' &&
                mutation.attributeName === 'style') {
                const boatrForm = document.getElementById('boatr-form');
                if (boatrForm && boatrForm.style.display !== 'none') {
                    setTimeout(addAutoFillButtonToBoatR, 100);
                }
            }
        });
    });

    const boatrSection = document.getElementById('boatr-form');
    if (boatrSection) {
        observer.observe(boatrSection, {
            attributes: true,
            attributeFilter: ['style']
        });
        console.log('MutationObserver attached to BoatR form');
    } else {
        console.warn('BoatR section not found');
    }

    if (boatrSection && boatrSection.style.display !== 'none') {
        setTimeout(addAutoFillButtonToBoatR, 100);
    }
}

// Export functions for global access
window.autoFillBoatRFromProfile = autoFillBoatRFromProfile;
window.fetchAndAutoFillBoatR = fetchAndAutoFillBoatR;
window.clearBoatRAutoFill = clearBoatRAutoFill;
window.addAutoFillButtonToBoatR = addAutoFillButtonToBoatR;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready - initializing BoatR auto-fill...');
    initializeBoatRAutoFill();
});

// Also initialize on window load for additional safety
window.addEventListener('load', function() {
    setTimeout(initializeBoatRAutoFill, 500);
});

console.log('BoatR Auto-fill module loaded');