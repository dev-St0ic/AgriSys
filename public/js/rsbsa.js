// RSBSA Frontend JavaScript Functions - COMPLETE VERSION WITH PERSISTENCE AND RELOAD HANDLING
// Updated to maintain form state on page reload and match FishR notification style exactly

/**
 * Global CSRF token management
 */
let rsbsaCSRFToken = null;

/**
 * Get fresh CSRF token for RSBSA
 */
async function refreshRSBSACSRFToken() {
    try {
        const response = await fetch('/csrf-token');
        const data = await response.json();
        rsbsaCSRFToken = data.csrf_token;

        // Update meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', rsbsaCSRFToken);
        }

        // Update all CSRF input fields
        document.querySelectorAll('input[name="_token"]').forEach(input => {
            input.value = rsbsaCSRFToken;
        });

        console.log('RSBSA CSRF token refreshed');
        return rsbsaCSRFToken;
    } catch (error) {
        console.error('Failed to refresh RSBSA CSRF token:', error);
        throw error;
    }
}

/**
 * Get current CSRF token for RSBSA
 */
function getRSBSACSRFToken() {
    if (rsbsaCSRFToken) return rsbsaCSRFToken;

    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        rsbsaCSRFToken = metaTag.getAttribute('content');
        return rsbsaCSRFToken;
    }

    console.error('No RSBSA CSRF token found');
    return null;
}

/**
 * Fetch with CSRF retry logic for RSBSA
 */
async function fetchRSBSAWithCSRFRetry(url, options, retries = 1) {
    try {
        const response = await fetch(url, options);

        // If CSRF error, refresh token and retry
        if (response.status === 419 && retries > 0) {
            console.log('RSBSA CSRF token mismatch, refreshing token and retrying...');

            // Refresh CSRF token
            const newToken = await refreshRSBSACSRFToken();

            // Update headers with new token
            if (options.headers) {
                options.headers['X-CSRF-TOKEN'] = newToken;
            }

            // If FormData, we need to update the _token field
            if (options.body instanceof FormData) {
                options.body.set('_token', newToken);
            }

            // Retry the request
            return await fetchRSBSAWithCSRFRetry(url, options, retries - 1);
        }

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response;
    } catch (error) {
        if (retries > 0 && error.message.includes('419')) {
            throw new Error('RSBSA CSRF token mismatch after retry');
        }
        throw error;
    }
}

/**
 * Check if we should show RSBSA form based on URL
 */
function shouldShowRSBSAForm() {
    const currentPath = window.location.pathname;

    // Only show RSBSA form if the URL explicitly matches RSBSA paths
    return currentPath === '/services/rsbsa' ||
           currentPath === '/services/rsbsa/new' ||
           currentPath === '/services/rsbsa/old';
}

/**
 * Opens the RSBSA Registration form - FIXED VERSION
 */
function openRSBSAForm(event) {
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    }

    // Check authentication before allowing access
    if (!showAuthRequired('RSBSA Registration')) {
        return false;
    }

    console.log('Opening RSBSA form');

    // Hide all main sections and forms first
    if (typeof hideAllMainSections === 'function') hideAllMainSections();
    if (typeof hideAllForms === 'function') hideAllForms();

    const formElement = document.getElementById('new-rsbsa');
    if (formElement) {
        formElement.style.display = 'block';

        // Reset form and clear any previous messages (only if not from page load)
        if (event && event.type !== 'load' && event.type !== 'DOMContentLoaded') {
            resetRSBSAForm();
        }

        // REMOVE THIS CONFLICTING CODE - let the HTML onclick handlers manage tabs
        // The showRSBSATab('form', event) in HTML will handle the initial tab display

        //scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Update URL without page reload
        if (window.location.pathname !== '/services/rsbsa') {
            history.pushState({page: 'rsbsa'}, '', '/services/rsbsa');
        }

        console.log('RSBSA form opened successfully');
    } else {
        console.error('RSBSA Registration form element not found');
        agrisysModal.error('Form not available. Please refresh the page and try again.', { title: 'Form Error' });
        return;
    }
}

/**
 * Closes RSBSA Registration form and returns to main services
 */
function closeFormRSBSA() {
    console.log('Closing RSBSA form');

    const formElement = document.getElementById('new-rsbsa');
    if (formElement) {
        formElement.style.display = 'none';
        console.log('RSBSA form closed');
    }

    // Show main sections again
    if (typeof showAllMainSections === 'function') showAllMainSections();

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Update URL to home page
    if (window.location.pathname !== '/') {
        history.pushState({page: 'home'}, '', '/');
    }
}

/**
 * Resets the RSBSA form to initial state - MATCHES FISHR EXACTLY
 */
function resetRSBSAForm() {
    const form = document.querySelector('#new-rsbsa form') || document.getElementById('rsbsa-form');
    if (form) {
        // Reset form data
        form.reset();

        // Clear any error styling
        const errorFields = form.querySelectorAll('.error');
        errorFields.forEach(field => field.classList.remove('error'));

        // Clear any validation error messages
        const errorTexts = form.querySelectorAll('.error-text');
        errorTexts.forEach(error => error.remove());

        // Reset submit button state
        const submitBtn = form.querySelector('.rsbsa-submit-btn') || form.querySelector('[type="submit"]') || form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            if (btnText) btnText.style.display = 'inline';
            if (btnLoading) btnLoading.style.display = 'none';
        }

        // Clear file input if exists
        const fileInput = form.querySelector('[name="supporting_docs"]');
        if (fileInput) {
            fileInput.value = '';
        }

        // Remove any file display elements
        if (typeof removeFile === 'function') {
            removeFile();
        }

        console.log('RSBSA form reset to initial state');
    }
}
/**
 * RSBSA Tab switching - Based on working BoatR pattern
 */
function showRSBSATab(tabName, event) {
    if (!event) return;

    // Get the RSBSA form container - be specific
    const formSection = event.target.closest('#new-rsbsa') || document.getElementById('new-rsbsa');
    if (!formSection) {
        console.error('RSBSA form section not found');
        return;
    }

    // Hide all tab contents within RSBSA form
    const allTabContents = formSection.querySelectorAll('.rsbsa-tab-content');
    allTabContents.forEach(content => {
        content.style.display = 'none';
        console.log('Hiding tab:', content.id);
    });

    // Remove active class from all tab buttons within RSBSA form
    const allTabButtons = formSection.querySelectorAll('.rsbsa-tab-btn');
    allTabButtons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab and activate button
    const targetTab = document.getElementById(tabName);
    if (targetTab) {
        targetTab.style.display = 'block';
        event.target.classList.add('active');
        console.log('Showing tab:', tabName);
    } else {
        console.error('Target tab not found:', tabName);
    }
}

/**
 * Initialize RSBSA tabs - Based on BoatR pattern
 */
function initializeRSBSATabs() {
    const rsbsaForm = document.getElementById('new-rsbsa');
    if (!rsbsaForm) return;

    // Hide Requirements and Information tabs, keep form visible
    const requirementsTab = document.getElementById('requirements');
    const infoTab = document.getElementById('information');

    if (requirementsTab) requirementsTab.style.display = 'none';
    if (infoTab) infoTab.style.display = 'none';

    // Remove active class from all buttons first
    const allButtons = rsbsaForm.querySelectorAll('.rsbsa-tab-btn');
    allButtons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Ensure Application Form tab is visible and its button is active
    const firstTab = document.getElementById('form');
    const firstButton = rsbsaForm.querySelector('.rsbsa-tab-btn');

    if (firstTab && firstButton) {
        firstTab.style.display = 'block';
        firstButton.classList.add('active');
    }

    console.log('RSBSA tabs initialized');
}

/**
 * FIXED VALIDATION - MATCHES SERVER EXACTLY
 */
function validateRSBSAForm(form) {
    const requiredFields = [
        'first_name',
        'last_name',
        'sex',
        'barangay',
        'mobile',
        'main_livelihood'
    ];

    let isValid = true;
    let errors = [];

    // 1️⃣ VALIDATE REQUIRED FIELDS
    requiredFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (!field || !field.value.trim()) {
            isValid = false;
            const fieldLabel = fieldName.replace('_', ' ').toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
            errors.push(`${fieldLabel} is required`);

            if (field) {
                field.classList.add('error');
                field.addEventListener('input', function() {
                    this.classList.remove('error');
                }, { once: true });
            }
        }
    });

    // 2️⃣ VALIDATE FIRST NAME FORMAT
    const firstNameField = form.querySelector('[name="first_name"]');
    if (firstNameField && firstNameField.value) {
        // Server uses: regex:/^[a-zA-Z\s\'-]+$/
        const namePattern = /^[a-zA-Z\s\'-]+$/;
        if (!namePattern.test(firstNameField.value)) {
            isValid = false;
            errors.push('First name can only contain letters, spaces, hyphens, and apostrophes');
            firstNameField.classList.add('error');
        }
    }

    // 3️⃣ VALIDATE LAST NAME FORMAT
    const lastNameField = form.querySelector('[name="last_name"]');
    if (lastNameField && lastNameField.value) {
        const namePattern = /^[a-zA-Z\s\'-]+$/;
        if (!namePattern.test(lastNameField.value)) {
            isValid = false;
            errors.push('Last name can only contain letters, spaces, hyphens, and apostrophes');
            lastNameField.classList.add('error');
        }
    }

    // 4️⃣ VALIDATE MIDDLE NAME FORMAT (optional but if provided must match pattern)
    const middleNameField = form.querySelector('[name="middle_name"]');
    if (middleNameField && middleNameField.value) {
        const namePattern = /^[a-zA-Z\s\'-]+$/;
        if (!namePattern.test(middleNameField.value)) {
            isValid = false;
            errors.push('Middle name can only contain letters, spaces, hyphens, and apostrophes');
            middleNameField.classList.add('error');
        }
    }

    // 5️⃣ VALIDATE SEX (must be exact match)
    const sexField = form.querySelector('[name="sex"]');
    if (sexField && sexField.value) {
        const validSexOptions = ['Male', 'Female', 'Preferred not to say'];
        if (!validSexOptions.includes(sexField.value)) {
            isValid = false;
            errors.push('Please select a valid sex option');
            sexField.classList.add('error');
        }
    }

    // 6️⃣ VALIDATE MOBILE NUMBER
    const mobileField = form.querySelector('[name="mobile"]');
    if (mobileField && mobileField.value) {
        // Server uses: regex:/^(\+639|09)\d{9}$/
        // Must be: +639XXXXXXXXX or 09XXXXXXXXX (exactly 12 digits after +63 or 09)
        const mobilePattern = /^(\+639|09)\d{9}$/;
        if (!mobilePattern.test(mobileField.value.replace(/\s+/g, ''))) {
            isValid = false;
            errors.push('Mobile number must be: +639XXXXXXXXX or 09XXXXXXXXX (11 digits total)');
            mobileField.classList.add('error');
        }
    }

    // 7️⃣ EMAIL REMOVED - Not required for RSBSA applications

    // 8️⃣ VALIDATE MAIN LIVELIHOOD (must be exact match)
    const livelihoodField = form.querySelector('[name="main_livelihood"]');
    if (livelihoodField && livelihoodField.value) {
        const validOptions = ['Farmer', 'Farmworker/Laborer', 'Fisherfolk', 'Agri-youth'];
        if (!validOptions.includes(livelihoodField.value)) {
            isValid = false;
            errors.push('Please select a valid main livelihood option');
            livelihoodField.classList.add('error');
        }
    }

    // 9️⃣ VALIDATE LAND AREA (optional but if provided must be 0-1000)
    const landAreaField = form.querySelector('[name="land_area"]');
    if (landAreaField && landAreaField.value) {
        const landArea = parseFloat(landAreaField.value);
        if (isNaN(landArea) || landArea < 0 || landArea > 1000) {
            isValid = false;
            errors.push('Land area must be between 0 and 1000 hectares');
            landAreaField.classList.add('error');
        }
    }

    // 🔟 VALIDATE FILE UPLOAD
    const fileField = form.querySelector('[name="supporting_docs"]');
    if (fileField && fileField.files.length > 0) {
        const file = fileField.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];

        if (file.size > maxSize) {
            isValid = false;
            errors.push('File size must be less than 10MB');
            fileField.classList.add('error');
        }

        if (!allowedTypes.includes(file.type)) {
            isValid = false;
            errors.push('File must be JPG, PNG, or PDF format');
            fileField.classList.add('error');
        }
    }

    return { isValid, errors };
}

/**
 * Format mobile number to match server requirements
 */
function formatMobileNumber(input) {
    let value = input.value.replace(/\D/g, ''); // Remove non-digits

    // If starts with 63, add +
    if (value.startsWith('63') && value.length >= 11) {
        value = '+' + value;
    }
    // If starts with 9 (single 9), add 0
    else if (value.match(/^9\d{9}$/)) {
        value = '0' + value;
    }
    // If already starts with 09, keep it
    else if (value.startsWith('0') && value.length === 11) {
        // Already correct
    }
    // Otherwise ensure it's 09XXXXXXXXX format
    else if (!value.startsWith('0') && !value.startsWith('+')) {
        if (value.length === 10 && value.startsWith('9')) {
            value = '0' + value;
        }
    }

    input.value = value;
}

/**
 * Format mobile number input
 */
function formatMobileNumber(input) {
    let value = input.value.replace(/\D/g, ''); // Remove non-digits

    // Format mobile number
    if (value.startsWith('63') && value.length === 12) {
        value = '+' + value;
    } else if (value.startsWith('9') && value.length === 10) {
        value = '0' + value;
    }

    input.value = value;
}

/**
 * Main form submission handler - EXACTLY LIKE FISHR WITH PROPER ALERT
 */
function handleRSBSAFormSubmission() {
    const rsbsaForm = document.querySelector('#rsbsa-form') || document.querySelector('#new-rsbsa form');

    if (!rsbsaForm) {
        console.error('RSBSA form not found');
        return;
    }

    console.log('RSBSA form found, attaching event listener');

    rsbsaForm.addEventListener('submit', async function(e) {
        // Prevent default form submission immediately
        e.preventDefault();
        e.stopPropagation();

        console.log('RSBSA form submission intercepted');

        // Check authentication before submitting
        if (!isUserAuthenticatedAndVerified()) {
            showAuthRequired('RSBSA Registration');
            return false;
        }

        // Validate form
        const validation = validateRSBSAForm(this);
        if (!validation.isValid) {
            agrisysModal.validationError(validation.errors, { title: 'Please Correct the Following' });
            return false;
        }

        // Find submit button
        const submitButton = this.querySelector('.rsbsa-submit-btn') || this.querySelector('[type="submit"]') || this.querySelector('button[type="submit"]');
        if (!submitButton) {
            console.error('Submit button not found');
            return false;
        }

        // Show loading state - EXACTLY LIKE FISHR
        const originalText = submitButton.textContent;
        const btnText = submitButton.querySelector('.btn-text');
        const btnLoading = submitButton.querySelector('.btn-loading');

        if (btnText && btnLoading) {
            // New button style with spans
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
        } else {
            // Simple button style
            submitButton.textContent = 'Submitting...';
        }
        submitButton.disabled = true;

        try {
            // Ensure CSRF token
            console.log('Ensuring CSRF token...');
            let csrfToken = getRSBSACSRFToken();

            if (!csrfToken) {
                console.log('No cached CSRF token, fetching fresh one...');
                csrfToken = await refreshRSBSACSRFToken();
            }

            if (!csrfToken) {
                throw new Error('CSRF token could not be obtained');
            }

            console.log('CSRF token obtained:', csrfToken.substring(0, 10) + '...');

            // Prepare form data
            const formData = new FormData(this);

            // Ensure CSRF token is in form data
            formData.set('_token', csrfToken);

            console.log('Submitting to /apply/rsbsa');

            // Submit form via fetch with retry logic
            const response = await fetchRSBSAWithCSRFRetry('/apply/rsbsa', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            console.log('Response status:', response.status);

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text.substring(0, 500));
                throw new Error('Server returned non-JSON response. Please check server logs.');
            }

            const result = await response.json();
            console.log('Response data:', result);

            if (response.ok && result.success) {
                // Show success message - EXACTLY LIKE FISHR STYLE
                const successMessage = result.message || 'Your RSBSA application has been submitted successfully!';
                const applicationNumber = result.application_number || result.reference_number || null;

                // Modern modal notification
                agrisysModal.success(successMessage, {
                    title: 'Application Submitted!',
                    reference: applicationNumber,
                    onClose: () => {
                        // Reset form and close immediately
                        this.reset();
                        if (typeof removeFile === 'function') {
                            removeFile();
                        }
                        closeFormRSBSA();
                    }
                });
            } else {
                let errorMessage = result.message || 'There was an error submitting your application.';

                // Handle validation errors
                if (result.errors) {
                    const errorList = Object.values(result.errors).flat();
                    agrisysModal.validationError(errorList, { title: 'Submission Failed' });
                } else {
                    console.error('Application submission failed:', result);
                    agrisysModal.error(errorMessage, { title: 'Submission Failed' });
                }
            }

        } catch (error) {
            console.error('Submission error:', error);

            // Handle specific error types - EXACTLY LIKE FISHR
            if (error.message.includes('419') || error.message.includes('CSRF')) {
                agrisysModal.error('Your session has expired. Please refresh the page and try again.', { title: 'Session Expired' });
            } else if (error.message.includes('Network')) {
                agrisysModal.error('Network error. Please check your connection and try again.', { title: 'Connection Error' });
            } else {
                agrisysModal.error('There was an error submitting your request. Please try again.', { title: 'Submission Error' });
            }
        } finally {
            // Reset button state - EXACTLY LIKE FISHR
            if (btnText && btnLoading) {
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            } else {
                submitButton.textContent = originalText;
            }
            submitButton.disabled = false;
        }

        return false;
    });
}

/**
 * Auto-fill form with sample data for testing
 */
function fillSampleRSBSAData() {
    const form = document.querySelector('#new-rsbsa form') || document.getElementById('rsbsa-form');
    if (!form) return;

    // Sample data
    const sampleData = {
        first_name: 'Maria',
        middle_name: 'Santos',
        last_name: 'Cruz',
        sex: 'Female',
        barangay: 'San Jose',
        mobile: '09123456789',
        main_livelihood: 'Farmer',
        main_livelihood: 'rice'
    };

    // Fill form fields
    Object.keys(sampleData).forEach(fieldName => {
        const input = form.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.value = sampleData[fieldName];

            // Trigger change event for select elements
            if (input.tagName === 'SELECT') {
                input.dispatchEvent(new Event('change'));
            }
        }
    });

    console.log('Sample data filled for RSBSA form');
}

/**
 * Clear form data
 */
function clearRSBSAForm() {
    if (confirm('Are you sure you want to clear all form data?')) {
        resetRSBSAForm();
    }
}

/**
 * Handle browser back/forward buttons
 */
function handleRSBSAPopState(event) {
    console.log('RSBSA Pop state event:', event.state);

    if (event.state && event.state.page === 'rsbsa') {
        // User navigated back to RSBSA form
        openRSBSAForm(new Event('popstate'));
    } else {
        // User navigated away from RSBSA form
        closeFormRSBSA();
    }
}

// ==============================================
// UTILITY FUNCTIONS - Fallback if not in main landing.js
// ==============================================

/**
 * Hide all main page sections
 */
function hideAllMainSections() {
    const sections = [
        'home',
        'events',
        'services',
        'how-it-works',
        '.help-section'
    ];

    sections.forEach(selector => {
        const element = selector.startsWith('.')
            ? document.querySelector(selector)
            : document.getElementById(selector);
        if (element) element.style.display = 'none';
    });
}

/**
 * Show all main page sections
 */
function showAllMainSections() {
    const sections = [
        'home',
        'events',
        'services',
        'how-it-works',
        '.help-section'
    ];

    sections.forEach(selector => {
        const element = selector.startsWith('.')
            ? document.querySelector(selector)
            : document.getElementById(selector);
        if (element) element.style.display = 'block';
    });
}

/**
 * Hide all application forms
 */
function hideAllForms() {
    const formIds = [
        'rsbsa-choice', 'new-rsbsa', 'old-rsbsa',
        'seedlings-choice', 'seedlings-form',
        'fishr-form', 'boatr-form'
    ];

    formIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.style.display = 'none';
    });

    // Also hide by class selector for application sections
    const formSections = document.querySelectorAll('.rsbsa-application-section');
    formSections.forEach(section => {
        section.style.display = 'none';
    });
}

/**
 * Activate application tab
 */
function activateApplicationTab(formId) {
    const formSection = document.getElementById(formId);
    if (!formSection) return;

    const firstTabBtn = formSection.querySelector('.rsbsa-tab-btn');
    const firstTabContent = formSection.querySelector('.rsbsa-tab-content');

    if (firstTabBtn && firstTabContent) {
        // Reset all tabs in this form
        formSection.querySelectorAll('.rsbsa-tab-btn').forEach(btn => btn.classList.remove('active'));
        formSection.querySelectorAll('.rsbsa-tab-content').forEach(tab => tab.style.display = 'none');

        // Activate first tab
        firstTabBtn.classList.add('active');
        firstTabContent.style.display = 'block';
    }
}

/**
 * Remove file display (placeholder function)
 */
function removeFile() {
    // Clear any file display elements
    const fileDisplay = document.querySelector('.file-display');
    if (fileDisplay) {
        fileDisplay.remove();
    }

    // Reset file input
    const fileInput = document.querySelector('[name="supporting_docs"]');
    if (fileInput) {
        fileInput.value = '';
    }

    console.log('File removed from RSBSA form');
}

/**
 * Check and show RSBSA form on page load if URL matches
 */
function checkAndShowRSBSAOnLoad() {
    console.log('Checking if RSBSA form should be shown on page load...');
    console.log('Current URL:', window.location.href);
    console.log('Current pathname:', window.location.pathname);

    if (shouldShowRSBSAForm()) {
        console.log('URL indicates RSBSA form should be shown - opening form');

        // Wait a bit for DOM to be fully ready
        setTimeout(() => {
            const formElement = document.getElementById('new-rsbsa');
            if (formElement) {
                console.log('RSBSA form element found, opening...');
                openRSBSAForm({ type: 'load' });
            } else {
                console.log('RSBSA form element not found, retrying...');
                // Retry after a bit more time
                setTimeout(() => {
                    const retryFormElement = document.getElementById('new-rsbsa');
                    if (retryFormElement) {
                        openRSBSAForm({ type: 'load' });
                    } else {
                        console.error('RSBSA form element still not found after retry');
                    }
                }, 500);
            }
        }, 100);
    } else {
        console.log('URL does not indicate RSBSA form should be shown');
    }
}

/**
 * Initialize RSBSA module
 */
function initializeRSBSAModule() {
    console.log('Initializing RSBSA module...');

    // Check if we should show the RSBSA form based on URL
    checkAndShowRSBSAOnLoad();

    // Get initial CSRF token
    getRSBSACSRFToken();

    // Initialize form submission handler
    handleRSBSAFormSubmission();

    // Initialize mobile number formatting
    const mobileInput = document.querySelector('#new-rsbsa [name="mobile"]');
    if (mobileInput) {
        mobileInput.addEventListener('input', function(e) {
            formatMobileNumber(e.target);
        });
    }

    // Initialize error removal on focus
    const allInputs = document.querySelectorAll('#new-rsbsa input, #new-rsbsa select');
    allInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.remove('error');
        });
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', handleRSBSAPopState);

    // Add keyboard shortcuts for development - EXACTLY LIKE FISHR
    document.addEventListener('keydown', function(e) {
        // Ctrl + Shift + R = Fill sample RSBSA data
        if (e.ctrlKey && e.shiftKey && e.key === 'R') {
            e.preventDefault();
            fillSampleRSBSAData();
        }

        // Ctrl + Shift + C = Clear RSBSA form
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            clearRSBSAForm();
        }
    });

    console.log('RSBSA module initialized successfully');
}

/**
 * Initialize when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing RSBSA form');

    // Initialize module with delay to ensure all elements are ready
    setTimeout(initializeRSBSAModule, 200);

    console.log('RSBSA form initialization scheduled');
});

/**
 * Also handle window load event for additional safety
 */
window.addEventListener('load', function() {
    console.log('Window loaded, double-checking RSBSA form display');

    // Double-check and show form if needed
    setTimeout(checkAndShowRSBSAOnLoad, 300);
});

/**
 * Handle page visibility change (when user returns to tab)
 */
document.addEventListener('visibilitychange', function() {
    if (!document.hidden && shouldShowRSBSAForm()) {
        console.log('Page became visible and should show RSBSA form');
        setTimeout(checkAndShowRSBSAOnLoad, 100);
    }
});

// Export functions for global access
window.openRSBSAForm = openRSBSAForm;
window.closeFormRSBSA = closeFormRSBSA;
window.resetRSBSAForm = resetRSBSAForm;
window.showRSBSATab = showRSBSATab;
window.removeFile = removeFile;
window.fillSampleRSBSAData = fillSampleRSBSAData;
window.clearRSBSAForm = clearRSBSAForm;

console.log('Complete RSBSA JavaScript module loaded with full persistence and reload handling');
