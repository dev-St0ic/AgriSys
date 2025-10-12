// ==============================================
// BOATR AUTOFILL SYSTEM
// Matches RSBSA, Training, and FishR pattern with buttons
// File: public/js/boatr-autofill.js
// ==============================================

/**
 * Auto-fill BoatR form with user profile data
 */
function autoFillBoatRFromProfile() {
    console.log('Auto-filling BoatR form from user profile...');
    console.log('Available userData:', window.userData); // DEBUG

    // Check if user is logged in and has profile data
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

    // Helper function to safely set field value
    function setFieldValue(fieldName, value) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field && value) {
            field.value = value;

            // Trigger change event for selects
            if (field.tagName === 'SELECT') {
                field.dispatchEvent(new Event('change', { bubbles: true }));
            }

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

    // Fill Contact Number
    setFieldValue('contact_number', userData.contact_number || userData.mobile_number || userData.phone);

    // Fill Email
    setFieldValue('email', userData.email);

    // Fill Barangay
    setFieldValue('barangay', userData.barangay);

    // Show results
    if (filledCount > 0) {
        showNotification('success', `✓ Auto-filled ${filledCount} field${filledCount > 1 ? 's' : ''} from your profile!`);
        console.log(`✅ Successfully auto-filled ${filledCount} BoatR form fields`);
    } else {
        console.warn('⚠️ No fields were auto-filled. userData:', userData);
        showNotification('warning', 'Could not auto-fill form. Please complete your profile verification first.');
    }
}

/**
 * Fetch fresh user profile data from backend and auto-fill
 */
async function fetchAndAutoFillBoatR() {
    console.log('Fetching fresh user profile data...');

    // Show loading state
    const btn = document.getElementById('boatr-autofill-btn');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = '⏳ Loading...';
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
            autoFillBoatRFromProfile();
        } else {
            showNotification('error', 'Could not load profile data');
        }

    } catch (error) {
        console.error('Error fetching profile:', error);
        // Fall back to cached userData
        console.log('Falling back to cached userData');
        autoFillBoatRFromProfile();
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
function clearBoatRAutoFill() {
    if (confirm('This will clear all form fields. Continue?')) {
        resetBoatRForm();
        showNotification('info', 'Form cleared successfully');
    }
}

/**
 * Add auto-fill button to BoatR form
 */
function addAutoFillButtonToBoatR() {
    const form = document.querySelector('#boatr-registration-form');
    if (!form) return;

    // Check if button already exists
    if (document.getElementById('boatr-autofill-btn')) return;

    // Only show button if user is logged in
    if (!window.userData) {
        console.log('No userData - skipping auto-fill button');
        return;
    }

    console.log('Adding auto-fill button for user:', window.userData.username);

    // Create auto-fill button container
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'boatr-autofill-container';
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
            <strong style="color: #2e7d32;">💡 Quick Fill:</strong>
            <span style="color: #558b2f;">Use your verified profile data to auto-complete this form</span>
        </div>
        <div class="autofill-actions">
            <button type="button" id="boatr-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillBoatR()"
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
                ✓ Use My Profile Data
            </button>
            <button type="button" class="btn-clear"
                    onclick="clearBoatRAutoFill()"
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
    const firstFormGroup = form.querySelector('.boatr-form-group');
    if (firstFormGroup) {
        firstFormGroup.parentNode.insertBefore(buttonContainer, firstFormGroup);
        console.log('✓ Auto-fill button added to BoatR form');
    } else {
        // Fallback: insert at beginning of form
        form.insertBefore(buttonContainer, form.firstChild);
        console.log('✓ Auto-fill button added to BoatR form (fallback position)');
    }
}

/**
 * Initialize auto-fill functionality
 */
function initializeBoatRAutoFill() {
    console.log('Initializing BoatR auto-fill functionality...');

    // Add auto-fill button when form is displayed
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
        console.log('✓ MutationObserver attached to BoatR form');
    } else {
        console.warn('⚠️ BoatR section not found');
    }

    // Also check immediately if form is already visible
    if (boatrSection && boatrSection.style.display !== 'none') {
        setTimeout(addAutoFillButtonToBoatR, 100);
    }
}

// Export functions for global access
window.autoFillBoatRFromProfile = autoFillBoatRFromProfile;
window.fetchAndAutoFillBoatR = fetchAndAutoFillBoatR;
window.clearBoatRAutoFill = clearBoatRAutoFill;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready - initializing BoatR auto-fill...');
    initializeBoatRAutoFill();
});

// Also initialize on window load for additional safety
window.addEventListener('load', function() {
    setTimeout(initializeBoatRAutoFill, 500);
});

console.log('✅ BoatR Auto-fill module loaded');
