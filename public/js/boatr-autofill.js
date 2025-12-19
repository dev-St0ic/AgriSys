// ==============================================
// UPDATED BOATR AUTOFILL SYSTEM - PROFESSIONAL VERSION
// File: public/js/boatr-autofill.js
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
 * Show professional clear form confirmation
 */
function showClearFormConfirmation(onConfirm) {
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

    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });
}

/**
 * Add auto-fill button to BoatR form
 * UPDATED: Professional styling matching Training form
 */
function addAutoFillButtonToBoatR() {
    const form = document.querySelector('#boatr-registration-form');
    if (!form) return;

    if (document.getElementById('boatr-autofill-btn')) return;

    if (!window.userData) {
        console.log('No userData - skipping auto-fill button');
        return;
    }

    console.log('Adding auto-fill button for user:', window.userData.username);

    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'boatr-autofill-container';
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
            <button type="button" id="boatr-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillBoatR()"
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
                    onclick="clearBoatRAutoFill()"
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

    const firstFormGroup = form.querySelector('.boatr-form-group');
    if (firstFormGroup) {
        firstFormGroup.parentNode.insertBefore(buttonContainer, firstFormGroup);
        console.log('Auto-fill button added to BoatR form');
    } else {
        form.insertBefore(buttonContainer, form.firstChild);
        console.log('Auto-fill button added to BoatR form (fallback position)');
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