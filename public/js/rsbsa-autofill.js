/**
 * RSBSA AUTO-FILL - MOBILE-RESPONSIVE VERSION
 * Automatically populates form fields from authenticated user's profile data
 */

/**
 * Auto-fill RSBSA form with user profile data - UPDATED VERSION
 */
function autoFillRSBSAFromProfile() {
    console.log('Auto-filling RSBSA form from user profile...');
    console.log('Available userData:', window.userData);

    if (!window.userData) {
        console.log('No user data available for auto-fill');
        showNotification('info', 'Please log in to use auto-fill');
        return;
    }

    const form = document.querySelector('#rsbsa-form');
    if (!form) {
        console.error('RSBSA form not found');
        return;
    }

    const userData = window.userData;
    let filledCount = 0;
    const errors = [];

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
        if (value === undefined || value === null || value === '') {
            console.log(`- ${fieldName} is empty in user data`);
        } else {
            console.log(`Could not find field: ${fieldName}`);
            errors.push(fieldName);
        }
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

    // Fill Sex/Gender with proper mapping and capitalization
    if (userData.sex) {
        const sexValue = userData.sex.charAt(0).toUpperCase() + userData.sex.slice(1).toLowerCase();
        setFieldValue('sex', sexValue);
    } else if (userData.gender) {
        const genderMap = {
            'male': 'Male',
            'female': 'Female',
            'other': 'Preferred not to say',
            'prefer_not_to_say': 'Preferred not to say'
        };
        const mappedSex = genderMap[userData.gender.toLowerCase()];
        if (mappedSex) {
            setFieldValue('sex', mappedSex);
        } else {
            console.log('Gender mapping not found for:', userData.gender);
        }
    }

    // Fill Barangay with case-insensitive matching
    if (userData.barangay) {
        const barangayField = form.querySelector('[name="barangay"]');
        if (barangayField) {
            const options = barangayField.querySelectorAll('option');
            let found = false;
            options.forEach(option => {
                if (option.value.toLowerCase() === userData.barangay.toLowerCase()) {
                    barangayField.value = option.value;
                    found = true;
                }
            });
            if (found) {
                barangayField.dispatchEvent(new Event('change', { bubbles: true }));
                filledCount++;
                console.log(`Filled barangay with: ${userData.barangay}`);
            } else {
                console.log(`Barangay not found in options: ${userData.barangay}`);
                errors.push('barangay');
            }
        }
    }

    // Fill Address - NEW FIELD
    // Priority: complete_address > address > street_address
    const addressValue = userData.complete_address || userData.address || userData.street_address;
    if (addressValue) {
        setFieldValue('address', addressValue);
    }

    // Fill Mobile Number
    const mobileValue = userData.contact_number || userData.mobile_number || userData.mobile || userData.phone;
    setFieldValue('contact_number', mobileValue);

    // Fill Main Livelihood from user_type
    if (userData.user_type) {
        const livelihoodMap = {
            'farmer': 'Farmer',
            'fisherfolk': 'Fisherfolk',
            'agri-youth': 'Agri-youth',
            'farm worker': 'Farmworker/Laborer',
            'farmworker': 'Farmworker/Laborer'
        };
        const mappedLivelihood = livelihoodMap[userData.user_type.toLowerCase()];
        if (mappedLivelihood) {
            setFieldValue('main_livelihood', mappedLivelihood);
        } else {
            console.log('Livelihood mapping not found for:', userData.user_type);
        }
    }

    // Show results
    if (filledCount > 0) {
        showNotification('success', `Successfully auto-filled ${filledCount} field${filledCount > 1 ? 's' : ''} from your profile`);
        console.log(`Successfully auto-filled ${filledCount} RSBSA form fields`);
    } else {
        console.warn('No fields were auto-filled. userData:', userData);
        showNotification('warning', 'Could not auto-fill form. Please complete your profile verification first.');
    }
}

/**
 * Fetch fresh user profile data from backend and auto-fill
 */
async function fetchAndAutoFillRSBSA() {
    console.log('Fetching fresh user profile data...');

    const btn = document.getElementById('rsbsa-autofill-btn');
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
            autoFillRSBSAFromProfile();
        } else {
            showNotification('error', 'Could not load profile data');
        }

    } catch (error) {
        console.error('Error fetching profile:', error);
        console.log('Falling back to cached userData');
        autoFillRSBSAFromProfile();
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
function clearRSBSAAutoFill() {
    const form = document.getElementById('rsbsa-form');
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

        clearRSBSAErrors();

        showNotification('info', 'Form has been cleared and is ready for new information');
        console.log('RSBSA form cleared successfully');
    });
}

/**
 * Show professional clear form confirmation - MOBILE RESPONSIVE
 */
function showClearFormConfirmation(onConfirm) {
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'rsbsa-modal-overlay';

    const modalContent = document.createElement('div');
    modalContent.className = 'rsbsa-modal-content';

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

    if (!document.querySelector('style[data-rsbsa-modal-responsive]')) {
        const style = document.createElement('style');
        style.setAttribute('data-rsbsa-modal-responsive', 'true');
        style.textContent = `
            /* Modal Responsive Styles */
            .rsbsa-modal-overlay {
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

            .rsbsa-modal-content {
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
                .rsbsa-modal-content {
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

    // Close modal when clicking outside
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });
}

/**
 * Add auto-fill button to RSBSA form - MOBILE RESPONSIVE
 */
function addAutoFillButtonToRSBSA() {
    const form = document.querySelector('#rsbsa-form');
    if (!form) return;

    if (document.getElementById('rsbsa-autofill-btn')) return;

    if (!window.userData) {
        console.log('No userData - skipping auto-fill button');
        return;
    }

    console.log('Adding mobile-responsive auto-fill button for user:', window.userData.username);

    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'rsbsa-autofill-container';

    buttonContainer.innerHTML = `
        <div class="autofill-info">
            <strong class="autofill-title">Quick Fill Available</strong>
            <span class="autofill-subtitle">Use your verified profile information to auto-complete this form</span>
        </div>
        <div class="autofill-actions">
            <button type="button" id="rsbsa-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillRSBSA()">
                Auto-fill
            </button>
            <button type="button" class="btn-clear"
                    onclick="clearRSBSAAutoFill()">
                Clear
            </button>
        </div>
    `;

    // Add mobile-responsive CSS
    if (!document.getElementById('rsbsa-mobile-responsive-style')) {
        const style = document.createElement('style');
        style.id = 'rsbsa-mobile-responsive-style';
        style.textContent = `
            .rsbsa-autofill-container {
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
                .rsbsa-autofill-container {
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

    const firstLabel = form.querySelector('label');
    if (firstLabel) {
        firstLabel.parentNode.insertBefore(buttonContainer, firstLabel);
        console.log('Mobile-responsive auto-fill button added to RSBSA form');
    } else {
        form.insertBefore(buttonContainer, form.firstChild);
        console.log('Mobile-responsive auto-fill button added to RSBSA form (fallback position)');
    }
}

/**
 * Initialize auto-fill functionality
 */
function initializeRSBSAAutoFill() {
    console.log('Initializing RSBSA auto-fill functionality...');

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                const rsbsaForm = document.getElementById('rsbsa-form');
                if (rsbsaForm && rsbsaForm.style.display !== 'none') {
                    setTimeout(addAutoFillButtonToRSBSA, 100);
                }
            }
        });
    });

    const rsbsaSection = document.getElementById('rsbsa-form');
    if (rsbsaSection) {
        observer.observe(rsbsaSection, {
            attributes: true,
            attributeFilter: ['style']
        });
        console.log('MutationObserver attached to RSBSA form');
    } else {
        console.warn('RSBSA section not found');
    }

    if (rsbsaSection && rsbsaSection.style.display !== 'none') {
        setTimeout(addAutoFillButtonToRSBSA, 100);
    }
}

// Export functions for global access
window.autoFillRSBSAFromProfile = autoFillRSBSAFromProfile;
window.fetchAndAutoFillRSBSA = fetchAndAutoFillRSBSA;
window.clearRSBSAAutoFill = clearRSBSAAutoFill;
window.addAutoFillButtonToRSBSA = addAutoFillButtonToRSBSA;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready - initializing RSBSA auto-fill...');
    initializeRSBSAAutoFill();
});

// Also initialize on window load for additional safety
window.addEventListener('load', function() {
    setTimeout(initializeRSBSAAutoFill, 500);
});

console.log('RSBSA Auto-fill module loaded with address field support and mobile-responsive design');