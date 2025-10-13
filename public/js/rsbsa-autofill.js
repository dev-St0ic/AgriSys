// RSBSA Auto-Fill Enhancement - FIXED VERSION
// Automatically populates form fields from authenticated user's profile data

/**
 * Auto-fill RSBSA form with user profile data
 */
function autoFillRSBSAFromProfile() {
    console.log('Auto-filling RSBSA form from user profile...');
    console.log('Available userData:', window.userData); // DEBUG

    // Check if user is logged in and has profile data
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

    // Helper function to safely set field value
    function setFieldValue(fieldName, value) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field && value) {
            field.value = value;

            // Trigger change event for selects
            if (field.tagName === 'SELECT') {
                field.dispatchEvent(new Event('change', { bubbles: true }));
            }

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

    // Fill Name Extension
    setFieldValue('name_extension', userData.name_extension || userData.extension_name);

    // Fill Sex/Gender
    if (userData.sex) {
        setFieldValue('sex', userData.sex);
    } else if (userData.gender) {
        const genderMap = {
            'male': 'Male',
            'female': 'Female',
            'other': 'Preferred not to say',
            'prefer_not_to_say': 'Preferred not to say'
        };
        const mappedGender = genderMap[userData.gender.toLowerCase()];
        if (mappedGender) {
            setFieldValue('sex', mappedGender);
        }
    }

    // Fill Barangay
    setFieldValue('barangay', userData.barangay);

    // Fill Mobile Number (try different property names)
    setFieldValue('mobile', userData.contact_number || userData.mobile || userData.phone);

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
        }
    }

    // Fill Farm Location from complete address - ALWAYS overwrite
    setFieldValue('farm_location', userData.complete_address);

    // Show results
    if (filledCount > 0) {
        showNotification('success', `✓ Auto-filled ${filledCount} field${filledCount > 1 ? 's' : ''} from your profile!`);
        console.log(`✅ Successfully auto-filled ${filledCount} RSBSA form fields`);
    } else {
        console.warn('⚠️ No fields were auto-filled. userData:', userData);
        showNotification('warning', 'Could not auto-fill form. Please complete your profile verification first.');
    }
}

/**
 * Fetch fresh user profile data from backend and auto-fill
 */
async function fetchAndAutoFillRSBSA() {
    console.log('Fetching fresh user profile data...');

    // Show loading state
    const btn = document.getElementById('rsbsa-autofill-btn');
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
            autoFillRSBSAFromProfile();
        } else {
            showNotification('error', 'Could not load profile data');
        }

    } catch (error) {
        console.error('Error fetching profile:', error);
        // Fall back to cached userData
        console.log('Falling back to cached userData');
        autoFillRSBSAFromProfile();
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
function clearRSBSAAutoFill() {
    if (confirm('This will clear all form fields. Continue?')) {
        resetRSBSAForm();
        showNotification('info', 'Form cleared successfully');
    }
}

/**
 * Check if form is empty (no user-entered data)
 */
function isFormEmpty(form) {
    const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], select');

    for (let input of inputs) {
        if (input.value && input.value.trim() !== '') {
            return false;
        }
    }

    return true;
}

/**
 * Add auto-fill button to RSBSA form
 */
function addAutoFillButtonToRSBSA() {
    const form = document.querySelector('#rsbsa-form');
    if (!form) return;

    // Check if button already exists
    if (document.getElementById('rsbsa-autofill-btn')) return;

    // Only show button if user is logged in
    if (!window.userData) {
        console.log('No userData - skipping auto-fill button');
        return;
    }

    console.log('Adding auto-fill button for user:', window.userData.username);

    // Create auto-fill button container
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'rsbsa-autofill-container';
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
            <button type="button" id="rsbsa-autofill-btn" class="btn-autofill"
                    onclick="fetchAndAutoFillRSBSA()"
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
                    onclick="clearRSBSAAutoFill()"
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
    const firstLabel = form.querySelector('label');
    if (firstLabel) {
        firstLabel.parentNode.insertBefore(buttonContainer, firstLabel);
        console.log('✓ Auto-fill button added to RSBSA form');
    } else {
        console.error('Could not find insertion point for auto-fill button');
    }
}

/**
 * Initialize auto-fill functionality
 */
function initializeRSBSAAutoFill() {
    console.log('Initializing RSBSA auto-fill functionality...');

    // Add auto-fill button when form is displayed
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' &&
                mutation.attributeName === 'style') {
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
        console.log('✓ MutationObserver attached to RSBSA form');
    } else {
        console.warn('⚠️ RSBSA section not found');
    }

    // Also check immediately if form is already visible
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

console.log('✅ RSBSA Auto-fill module loaded');
