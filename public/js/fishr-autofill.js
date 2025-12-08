// ==============================================
// FISHR AUTOFILL SYSTEM - UPDATED
// Professional version with proper field mapping
// File: public/js/fishr-autofill.js
// ==============================================

/**
 * Auto-fill FishR form with user profile data
 */
function autoFillFishRFromProfile() {
    console.log('Auto-filling FishR form from user profile...');
    console.log('Available userData:', window.userData);

    if (!window.userData) {
        console.log('No user data available for auto-fill');
        showNotification('info', 'Please log in to use auto-fill');
        return;
    }

    const form = document.querySelector('#fishr-registration-form');
    if (!form) {
        console.error('FishR form not found');
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
                field.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // Add visual feedback - subtle highlight
            field.style.backgroundColor = '#fafafa';
            setTimeout(() => {
                field.style.backgroundColor = '';
            }, 1500);

            filledCount++;
            console.log(`✓ Filled ${fieldName} with: ${value}`);
            return true;
        }
        if (value === undefined || value === null || value === '') {
            console.log(`- ${fieldName} is empty in user data`);
        } else {
            console.log(`✗ Could not find field: ${fieldName}`);
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

    // Fill Sex/Gender with proper mapping
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

    // Fill Barangay
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

    // Fill Contact Number
    setFieldValue('contact_number', userData.contact_number || userData.mobile_number || userData.phone);

    // Email removed - not required for FishR applications

    // Show results
    if (filledCount > 0) {
        showNotification('success', `Successfully auto-filled ${filledCount} field${filledCount > 1 ? 's' : ''}`);
        console.log(`Successfully auto-filled ${filledCount} FishR form fields`);
    } else {
        console.warn('No fields were auto-filled. userData:', userData);
        showNotification('warning', 'Could not auto-fill form. Please verify your profile is complete.');
    }
}

/**
 * Fetch fresh user profile data from backend and auto-fill
 */
async function fetchAndAutoFillFishR() {
    console.log('Fetching fresh user profile data...');

    const btn = document.getElementById('fishr-autofill-btn');
    const originalText = btn ? btn.textContent : '';
    if (btn) {
        btn.textContent = 'Loading...';
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
            autoFillFishRFromProfile();
        } else {
            showNotification('error', 'Could not load profile data');
        }

    } catch (error) {
        console.error('Error fetching profile:', error);
        console.log('Falling back to cached userData');
        autoFillFishRFromProfile();
    } finally {
        if (btn) {
            btn.textContent = originalText;
            btn.disabled = false;
        }
    }
}

/**
 * Clear auto-filled data
 */
function clearFishRAutoFill() {
    if (confirm('This will clear all form fields. Continue?')) {
        resetFishRForm();
        showNotification('info', 'Form cleared successfully');
    }
}

/**
 * Add auto-fill button to FishR form
 */
function addAutoFillButtonToFishR() {
    const form = document.querySelector('#fishr-registration-form');
    if (!form) return;

    if (document.getElementById('fishr-autofill-btn')) return;

    if (!window.userData) {
        console.log('No userData - skipping auto-fill button');
        return;
    }

    console.log('Adding auto-fill button for user:', window.userData.username);

    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'fishr-autofill-container';
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
            <button type="button" id="fishr-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillFishR()"
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
                    onclick="clearFishRAutoFill()"
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

    const firstFormGroup = form.querySelector('.fishr-form-group');
    if (firstFormGroup) {
        firstFormGroup.parentNode.insertBefore(buttonContainer, firstFormGroup);
        console.log('Auto-fill button added to FishR form');
    } else {
        form.insertBefore(buttonContainer, form.firstChild);
        console.log('Auto-fill button added to FishR form (fallback position)');
    }
}

/**
 * Initialize auto-fill functionality
 */
function initializeFishRAutoFill() {
    console.log('Initializing FishR auto-fill functionality...');

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                const fishrForm = document.getElementById('fishr-form');
                if (fishrForm && fishrForm.style.display !== 'none') {
                    setTimeout(addAutoFillButtonToFishR, 100);
                }
            }
        });
    });

    const fishrSection = document.getElementById('fishr-form');
    if (fishrSection) {
        observer.observe(fishrSection, {
            attributes: true,
            attributeFilter: ['style']
        });
        console.log('MutationObserver attached to FishR form');
    } else {
        console.warn('FishR section not found');
    }

    if (fishrSection && fishrSection.style.display !== 'none') {
        setTimeout(addAutoFillButtonToFishR, 100);
    }
}

// Export functions for global access
window.autoFillFishRFromProfile = autoFillFishRFromProfile;
window.fetchAndAutoFillFishR = fetchAndAutoFillFishR;
window.clearFishRAutoFill = clearFishRAutoFill;

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready - initializing FishR auto-fill...');
    initializeFishRAutoFill();
});

// Also initialize on window load for additional safety
window.addEventListener('load', function() {
    setTimeout(initializeFishRAutoFill, 500);
});

console.log('FishR Auto-fill module loaded');
