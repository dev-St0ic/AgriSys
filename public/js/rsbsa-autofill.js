// RSBSA AUTO-FILL ENHANCEMENT - UPDATED PROFESSIONAL VERSION
// Automatically populates form fields from authenticated user's profile data
// Fixed: Sex/Gender field mapping, proper event dispatching, professional UI

/**
 * Auto-fill RSBSA form with user profile data
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

            // Trigger change and input events for selects
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

    // Fill Mobile Number
    setFieldValue('mobile', userData.contact_number || userData.mobile_number || userData.mobile || userData.phone);

    // Fill Email
    setFieldValue('email', userData.email);

    // Fill Main Livelihood from user_type
    if (userData.user_type) {
        const livelihoodMap = {
            'farmer': 'Farmer',
            'fisherfolk': 'Fisherfolk',
            'agri-youth': 'Agri-youth',
            'agri-entrepreneur': 'Farmer',
            'cooperative-member': 'Farmer',
            'government-employee': 'Farmworker/Laborer',
            'agricultural worker': 'Farmworker/Laborer',
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

    // Fill Farm Location from complete address
    setFieldValue('farm_location', userData.complete_address);

    // Show results
    if (filledCount > 0) {
        showNotification('success', `Successfully auto-filled ${filledCount} field${filledCount > 1 ? 's' : ''}`);
        console.log(`Successfully auto-filled ${filledCount} RSBSA form fields`);
    } else {
        console.warn('No fields were auto-filled. userData:', userData);
        showNotification('warning', 'Could not auto-fill form. Please verify your profile is complete.');
    }
}

/**
 * Fetch fresh user profile data from backend and auto-fill
 */
async function fetchAndAutoFillRSBSA() {
    console.log('Fetching fresh user profile data...');

    const btn = document.getElementById('rsbsa-autofill-btn');
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
            btn.textContent = originalText;
            btn.disabled = false;
        }
    }
}

/**
 * Clear auto-filled data
 */
function clearRSBSAAutoFill() {
    if (confirm('This will clear all form fields. Continue?')) {
        resetRSBSAForm();
        showNotification('info', 'Form cleared successfully');
    }
}

/**
 * Add auto-fill button to RSBSA form
 */
function addAutoFillButtonToRSBSA() {
    const form = document.querySelector('#rsbsa-form');
    if (!form) return;

    if (document.getElementById('rsbsa-autofill-btn')) return;

    if (!window.userData) {
        console.log('No userData - skipping auto-fill button');
        return;
    }

    console.log('Adding auto-fill button for user:', window.userData.username);

    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'rsbsa-autofill-container';
    buttonContainer.style.cssText = `
        margin-bottom: 25px;
        padding: 16px 18px;
        background: linear-gradient(135deg, #f0f9f7 0%, #f5fbfa 100%);
        border-left: 4px solid #2d6a4f;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    `;

    buttonContainer.innerHTML = `
        <div class="autofill-info">
            <strong style="color: #2d6a4f; font-size: 14px; display: block; margin-bottom: 4px;">Quick Fill</strong>
            <span style="color: #558b2f; font-size: 13px;">Use your verified profile data to auto-complete this form</span>
        </div>
        <div class="autofill-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" id="rsbsa-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillRSBSA()"
                    style="
                        background: #2d6a4f;
                        color: white;
                        border: none;
                        padding: 10px 18px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 13px;
                        font-weight: 500;
                        transition: all 0.3s ease;
                        white-space: nowrap;
                    "
                    onmouseover="this.style.background='#1f4d38'"
                    onmouseout="this.style.background='#2d6a4f'">
                Use Profile Data
            </button>
            <button type="button" class="btn-clear"
                    onclick="clearRSBSAAutoFill()"
                    style="
                        background: #6c757d;
                        color: white;
                        border: none;
                        padding: 10px 18px;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 13px;
                        font-weight: 500;
                        transition: all 0.3s ease;
                        white-space: nowrap;
                    "
                    onmouseover="this.style.background='#5a6268'"
                    onmouseout="this.style.background='#6c757d'">
                Clear Form
            </button>
        </div>
    `;

    const firstLabel = form.querySelector('label');
    if (firstLabel) {
        firstLabel.parentNode.insertBefore(buttonContainer, firstLabel);
        console.log('Auto-fill button added to RSBSA form');
    } else {
        form.insertBefore(buttonContainer, form.firstChild);
        console.log('Auto-fill button added to RSBSA form (fallback position)');
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
                const rsbsaForm = document.getElementById('new-rsbsa');
                if (rsbsaForm && rsbsaForm.style.display !== 'none') {
                    setTimeout(addAutoFillButtonToRSBSA, 100);
                }
            }
        });
    });

    const rsbsaSection = document.getElementById('new-rsbsa');
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

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready - initializing RSBSA auto-fill...');
    initializeRSBSAAutoFill();
});

// Also initialize on window load for additional safety
window.addEventListener('load', function() {
    setTimeout(initializeRSBSAAutoFill, 500);
});

console.log('RSBSA Auto-fill module loaded');