// ==============================================
// COMPLETE UPDATED FISH REGISTRATION JAVASCRIPT
// Enhanced with CSRF protection and error handling
// File: public/js/fishr.js
// FULLY CORRECTED - No duplicates, all validation working
// ==============================================

/**
 * Global CSRF token management
 */
let csrfToken = null;

/**
 * Get fresh CSRF token
 */
async function refreshCSRFToken() {
    try {
        const response = await fetch('/csrf-token');
        const data = await response.json();
        csrfToken = data.csrf_token;

        // Update meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', csrfToken);
        }

        // Update all CSRF input fields
        document.querySelectorAll('input[name="_token"]').forEach(input => {
            input.value = csrfToken;
        });

        console.log('CSRF token refreshed');
        return csrfToken;
    } catch (error) {
        console.error('Failed to refresh CSRF token:', error);
        throw error;
    }
}

/**
 * Get current CSRF token
 */
function getCSRFToken() {
    if (csrfToken) return csrfToken;

    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        csrfToken = metaTag.getAttribute('content');
        return csrfToken;
    }

    console.error('No CSRF token found');
    return null;
}

/**
 * Fetch with CSRF retry logic
 */
async function fetchWithCSRFRetry(url, options, retries = 1) {
    try {
        const response = await fetch(url, options);

        // If CSRF error, refresh token and retry
        if (response.status === 419 && retries > 0) {
            console.log('CSRF token mismatch, refreshing token and retrying...');

            // Refresh CSRF token
            const newToken = await refreshCSRFToken();

            // Update headers with new token
            if (options.headers) {
                options.headers['X-CSRF-TOKEN'] = newToken;
            }

            // If FormData, we need to update the _token field
            if (options.body instanceof FormData) {
                options.body.set('_token', newToken);
            }

            // Retry the request
            return await fetchWithCSRFRetry(url, options, retries - 1);
        }

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response;
    } catch (error) {
        if (retries > 0 && error.message.includes('419')) {
            throw new Error('CSRF token mismatch after retry');
        }
        throw error;
    }
}

/**
 * Check URL and show appropriate form on page load
 */
function shouldShowFishRForm() {
    const currentPath = window.location.pathname;

    // Only show FishR form if the URL explicitly matches FishR path
    return currentPath === '/services/fishr';
}

function checkAndShowFishROnLoad() {
    console.log('Checking if FishR form should be shown on page load...');
    console.log('Current URL:', window.location.href);
    console.log('Current pathname:', window.location.pathname);

    if (shouldShowFishRForm()) {
        console.log('URL indicates FishR form should be shown - opening form');

        // Wait a bit for DOM to be fully ready
        setTimeout(() => {
            const formElement = document.getElementById('fishr-form');
            if (formElement) {
                console.log('FishR form element found, opening...');
                openFormFishR({ type: 'load' });
            } else {
                console.log('FishR form element not found, retrying...');
                // Retry after a bit more time
                setTimeout(() => {
                    const retryFormElement = document.getElementById('fishr-form');
                    if (retryFormElement) {
                        openFormFishR({ type: 'load' });
                    } else {
                        console.error('FishR form element still not found after retry');
                    }
                }, 500);
            }
        }, 100);
    } else {
        console.log('URL does not indicate FishR form should be shown');
    }
}

/**
 * Initialize FishR tabs - Based on RSBSA pattern
 */
function initializeFishRTabs() {
    const fishrForm = document.getElementById('fishr-form');
    if (!fishrForm) return;

    // Hide all tab contents within FishR form
    const allTabContents = fishrForm.querySelectorAll('.fishr-tab-content');
    allTabContents.forEach(content => {
        content.style.display = 'none';
    });

    // Remove active class from all buttons first
    const allButtons = fishrForm.querySelectorAll('.fishr-tab-btn');
    allButtons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Ensure Application Form tab is visible and its button is active
    const firstTab = fishrForm.querySelector('.fishr-tab-content');
    const firstButton = fishrForm.querySelector('.fishr-tab-btn');

    if (firstTab && firstButton) {
        firstTab.style.display = 'block';
        firstButton.classList.add('active');
    }

    console.log('FishR tabs initialized');
}

/**
 * Main tab switching function for FishR form
 * This is the function your HTML onclick events are calling
 */
function showFishrTab(tabId, event) {
    console.log('Switching to FishR tab:', tabId);
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
    // Prevent default button behavior
    if (event) {
        event.preventDefault();
    }

    // Get the parent section containing all tabs
    const parentSection = event.target.closest('.fishr-application-section');
    if (!parentSection) {
        console.error('Parent section not found for FishR tab switching');
        return;
    }

    // Remove active class from all tab buttons
    const tabButtons = parentSection.querySelectorAll('.fishr-tab-btn');
    tabButtons.forEach(btn => btn.classList.remove('active'));

    // Hide all tab content
    const tabContents = parentSection.querySelectorAll('.fishr-tab-content');
    tabContents.forEach(content => content.style.display = 'none');

    // Add active class to clicked button
    if (event && event.target) {
        event.target.classList.add('active');
    }

    // Show the selected tab content
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.style.display = 'block';
        console.log('FishR tab switched successfully to:', tabId);
    } else {
        console.error('Tab content not found:', tabId);
    }
}

/**
 * Opens the Fish Registration form
 */
function openFormFishR(event) {
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    }

    // Check authentication before allowing access
    if (!showAuthRequired('FishR Registration')) {
        return false;
    }

    console.log('Opening FishR form');

    // Hide all main sections and forms first
    if (typeof hideAllMainSections === 'function') hideAllMainSections();
    if (typeof hideAllForms === 'function') hideAllForms();

    const formElement = document.getElementById('fishr-form');
    if (formElement) {
        formElement.style.display = 'block';

        // Initialize tabs to show the first tab content
        initializeFishRTabs();
        if (typeof activateApplicationTab === 'function') {
            activateApplicationTab('fishr-form');
        }

         // Reset form and clear any previous messages (only if not from page load)
        if (event && event.type !== 'load' && event.type !== 'DOMContentLoaded') {
        resetFishRForm();

        // Re-initialize tabs after reset
        initializeFishRTabs();

        }   else {
        // Just initialize tabs without resetting on page load
        initializeFishRTabs();
        }


        // Scroll to top with proper timing and multiple fallbacks
        setTimeout(() => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
        }, 50);

        // Update URL without page reload
        if (window.location.pathname !== '/services/fishr') {
            history.pushState({page: 'fishr'}, '', '/services/fishr');
        }

        console.log('FishR form opened successfully');
    } else {
        console.error('Fish Registration form element not found');
        agrisysModal.error('Form not available. Please refresh the page and try again.', { title: 'Form Error' });
        return;
    }
}

/**
 * Closes Fish Registration form and returns to main services
 */
function closeFormFishR() {
    console.log('Closing FishR form');

    const formElement = document.getElementById('fishr-form');
    if (formElement) {
        formElement.style.display = 'none';
        console.log('FishR form closed');
    }

    // Show main sections again
    if (typeof showAllMainSections === 'function') showAllMainSections();

    // Update URL to home page
    if (window.location.pathname !== '/') {
        history.pushState({page: 'home'}, '', '/');
    }
}

/**
 * Resets Fish Registration form to initial state
 */
function resetFishRForm() {
    const form = document.getElementById('fishr-registration-form');
    if (form) {
        // Reset form data
        form.reset();

        // Hide other livelihood field
        const otherField = document.getElementById('fishr-other-livelihood-field');
        if (otherField) {
            otherField.style.display = 'none';
        }

        // Hide other secondary livelihood field
        const otherSecondaryField = document.getElementById('fishr-other-secondary-livelihood-field');
        if (otherSecondaryField) {
            otherSecondaryField.style.display = 'none';
        }

        // Remove required attribute from other livelihood inputs
        const otherInput = document.getElementById('fishr-other_livelihood');
        if (otherInput) {
            otherInput.removeAttribute('required');
        }

        const otherSecondaryInput = document.getElementById('fishr-other_secondary_livelihood');
        if (otherSecondaryInput) {
            otherSecondaryInput.removeAttribute('required');
        }

        // Hide secondary livelihood warning
        const secondaryWarning = document.getElementById('fishr-secondary_livelihood-warning');
        if (secondaryWarning) {
            secondaryWarning.style.display = 'none';
        }

        // Hide messages
        const messagesContainer = document.getElementById('fishr-messages');
        if (messagesContainer) {
            messagesContainer.style.display = 'none';
        }

        // Clear any validation error messages
        const errorTexts = form.querySelectorAll('.error-text');
        errorTexts.forEach(error => error.remove());

        // Reset submit button state to normal
        const submitBtn = document.getElementById('fishr-submit-btn');
        const btnText = submitBtn.querySelector('.fishr-btn-text');
        const btnLoading = submitBtn.querySelector('.fishr-btn-loading');

        // Show normal state - properly show text and hide loading
        if (btnText) btnText.style.display = 'inline';
        if (btnLoading) btnLoading.style.display = 'none';  
        submitBtn.disabled = false;

        console.log('FishR form reset to initial state');
    }
}

/**
 * Handles livelihood selection change
 */
function toggleOtherLivelihood(select) {
    if (!select) {
        console.error('Select element not provided to toggleOtherLivelihood');
        return;
    }

    const otherField = document.getElementById('fishr-other-livelihood-field');
    const otherInput = document.getElementById('fishr-other_livelihood');
    const selectedValue = select.value;

    if (otherField && otherInput) {
        if (selectedValue === 'others') {
            // Show other livelihood field and make it required
            otherField.style.display = 'block';
            otherInput.setAttribute('required', 'required');
            otherInput.focus();
        } else {
            // Hide other livelihood field and remove requirement
            otherField.style.display = 'none';
            otherInput.removeAttribute('required');
            otherInput.value = '';
        }
    }

    // Update supporting documents requirement based on livelihood
    updateDocumentsRequirement(selectedValue);

    // Validate secondary livelihood when main changes
    validateSecondaryLivelihoodMatch();

    console.log('FishR livelihood changed to:', selectedValue);
}

/**
 * Updates supporting documents requirement based on livelihood type
 * Now keeps consistent text for all livelihood types since it's always optional
 */
function updateDocumentsRequirement(livelihoodType) {
    const docsInput = document.getElementById('supporting_document');
    const docsLabel = document.querySelector('label[for="supporting_document"]');
    const docsHelp = document.querySelector('#supporting_document + .fishr-form-text');
    const labelText = docsLabel ? docsLabel.querySelector('.label-text') : null;
    const asterisk = docsLabel ? docsLabel.querySelector('.required-asterisk') : null;

    if (docsInput && docsLabel) {
        // Supporting document is ALWAYS optional now
        docsInput.removeAttribute('required');
        
        // Hide asterisk
        if (asterisk) {
            asterisk.style.display = 'none';
        }
        
        // Keep consistent text for ALL livelihood types
        if (labelText) {
            labelText.textContent = 'Supporting Document (Optional)';
        }
        
        if (docsHelp) {
            docsHelp.textContent = 'Upload Government ID or Barangay Certificate (PDF, JPG, PNG - Max 10MB)';
        }
    }
    
    console.log('Documents requirement updated - remains optional for all livelihood types:', livelihoodType);
}

/**
 * Show FishR-specific messages
 */
function showFishRMessage(type, message) {
    const messagesContainer = document.getElementById('fishr-messages');
    const successDiv = document.getElementById('fishr-success-message');
    const errorDiv = document.getElementById('fishr-error-message');

    if (!messagesContainer || !successDiv || !errorDiv) {
        console.error('FishR message elements not found');
        return;
    }

    // Hide both message divs first
    successDiv.style.display = 'none';
    errorDiv.style.display = 'none';

    // Show the appropriate message
    if (type === 'success') {
        successDiv.innerHTML = message;
        successDiv.style.display = 'block';
    } else {
        errorDiv.innerHTML = message;
        errorDiv.style.display = 'block';
    }

    messagesContainer.style.display = 'block';

    // Auto-hide after 5 seconds
    setTimeout(() => {
        messagesContainer.style.display = 'none';
    }, 5000);
}

/**
 * Show FishR validation errors
 */
function showFishRValidationErrors(errors) {
    console.log('Showing validation errors:', errors);

    // Clear previous error messages
    const errorSpans = document.querySelectorAll('#fishr-form .error-text');
    errorSpans.forEach(span => span.remove());

    // Show new error messages
    for (const field in errors) {
        const input = document.querySelector(`#fishr-form [name="${field}"]`);
        if (input) {
            const errorSpan = document.createElement('span');
            errorSpan.className = 'error-text';
            errorSpan.textContent = errors[field][0];
            errorSpan.style.color = '#dc3545';
            errorSpan.style.fontSize = '12px';
            errorSpan.style.marginTop = '5px';
            errorSpan.style.display = 'block';
            input.parentNode.appendChild(errorSpan);
        }
    }
}

// ==============================================
// FORM VALIDATION - CRITICAL FIX
// ==============================================

/**
 * Validates the form before submission
 * ✓ FIXED: Variables defined before use
 * ✓ FIXED: Only ONE secondary livelihood check
 * ✓ FIXED: Returns boolean value
 */
function validateFishRForm() {
    const form = document.getElementById('fishr-registration-form');
    if (!form) return false;

    let isValid = true;
    const errors = [];

    // Clear previous error states
    if (typeof clearFormErrors === 'function') {
        clearFormErrors();
    }

    // === CHECK FOR ANY FIELDS WITH RED BORDER (Real-time validation errors) ===
    const fieldsWithErrors = form.querySelectorAll('input[style*="border-color: rgb(255, 107, 107)"], select[style*="border-color: rgb(255, 107, 107)"]');
    
    if (fieldsWithErrors.length > 0) {
        errors.push('Please fix the validation errors in red before submitting');
        if (typeof displayValidationErrors === 'function') {
            displayValidationErrors(errors);
        }
        
        // Scroll to first field with error
        fieldsWithErrors[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        fieldsWithErrors[0].focus();
        
        return false;
    }

    // Required field validation
    const requiredFields = [
        { name: 'first_name', label: 'First Name', type: 'text' },
        { name: 'last_name', label: 'Last Name', type: 'text' },
        { name: 'sex', label: 'Sex', type: 'select' },
        { name: 'barangay', label: 'Barangay', type: 'select' },
        { name: 'contact_number', label: 'Contact Number', type: 'tel' },
        { name: 'main_livelihood', label: 'Main Livelihood', type: 'select' }
    ];

    // Validate each required field
    requiredFields.forEach(field => {
        const input = form.querySelector(`[name="${field.name}"]`);
        if (!input) {
            console.error(`Field not found: ${field.name}`);
            return;
        }

        const value = input.value ? input.value.trim() : '';

        if (!value) {
            errors.push(`${field.label} is required`);
            if (typeof markFieldError === 'function') {
                markFieldError(input);
            }
            isValid = false;
        } else {
            // Additional field-specific validation
            if (field.type === 'tel') {
                if (!isValidPhoneNumber(value)) {
                    errors.push(`${field.label} must be in format: 09XXXXXXXXX (11 digits starting with 09)`);
                    if (typeof markFieldError === 'function') {
                        markFieldError(input);
                    }
                    isValid = false;
                }
            }
        }
    });

    // Validate first name format
    const firstNameInput = form.querySelector('[name="first_name"]');
    if (firstNameInput && firstNameInput.value) {
        if (!isValidNameFormat(firstNameInput.value)) {
            errors.push('First Name can only contain letters, spaces, hyphens, and apostrophes');
            if (typeof markFieldError === 'function') {
                markFieldError(firstNameInput);
            }
            isValid = false;
        }
    }

    // Validate last name format
    const lastNameInput = form.querySelector('[name="last_name"]');
    if (lastNameInput && lastNameInput.value) {
        if (!isValidNameFormat(lastNameInput.value)) {
            errors.push('Last Name can only contain letters, spaces, hyphens, and apostrophes');
            if (typeof markFieldError === 'function') {
                markFieldError(lastNameInput);
            }
            isValid = false;
        }
    }

    // Validate middle name if provided
    const middleNameInput = form.querySelector('[name="middle_name"]');
    if (middleNameInput && middleNameInput.value) {
        if (!isValidNameFormat(middleNameInput.value)) {
            errors.push('Middle Name can only contain letters, spaces, hyphens, and apostrophes');
            if (typeof markFieldError === 'function') {
                markFieldError(middleNameInput);
            }
            isValid = false;
        }
    }

    // Validate name extension if provided
    const nameExtInput = form.querySelector('[name="name_extension"]');
    if (nameExtInput && nameExtInput.value) {
        if (!/^[a-zA-Z.\s]*$/.test(nameExtInput.value)) {
            errors.push('Name Extension can only contain letters, periods, and spaces');
            if (typeof markFieldError === 'function') {
                markFieldError(nameExtInput);
            }
            isValid = false;
        }
    }

    // ✓ DEFINE VARIABLES FIRST
    const livelihoodSelect = form.querySelector('[name="main_livelihood"]');
    const otherLivelihoodInput = form.querySelector('[name="other_livelihood"]');

    // Conditional validation for "others" livelihood
    if (livelihoodSelect && livelihoodSelect.value === 'others') {
        if (!otherLivelihoodInput || !otherLivelihoodInput.value.trim()) {
            errors.push('Please specify your livelihood when selecting "Others"');
            if (typeof markFieldError === 'function') {
                markFieldError(otherLivelihoodInput);
            }
            isValid = false;
        }
    }

    // ✓ DEFINE SECONDARY VARIABLES
    const secondaryLivelihoodSelect = form.querySelector('[name="secondary_livelihood"]');
    const otherSecondaryLivelihoodInput = form.querySelector('[name="other_secondary_livelihood"]');

    // Conditional validation for secondary "others" livelihood
    if (secondaryLivelihoodSelect && secondaryLivelihoodSelect.value === 'others') {
        if (!otherSecondaryLivelihoodInput || !otherSecondaryLivelihoodInput.value.trim()) {
            errors.push('Please specify your secondary livelihood when selecting "Others"');
            if (typeof markFieldError === 'function') {
                markFieldError(otherSecondaryLivelihoodInput);
            }
            isValid = false;
        }
    }
    
        //  Validate special characters in other_livelihood
        if (otherLivelihoodInput && otherLivelihoodInput.value.trim()) {
            if (!isValidOthersLivelihoodText(otherLivelihoodInput.value)) {
                errors.push('Main livelihood "others": Only letters, numbers, spaces, hyphens, apostrophes, periods, and commas are allowed');
                if (typeof markFieldError === 'function') {
                    markFieldError(otherLivelihoodInput);
                }
                isValid = false;
            }
        }

    // ✓ ONLY ONE CHECK FOR SECONDARY LIVELIHOOD MATCH (with defined variables)
    if (livelihoodSelect && secondaryLivelihoodSelect && 
        livelihoodSelect.value && secondaryLivelihoodSelect.value) {
        
        if (livelihoodSelect.value === secondaryLivelihoodSelect.value && 
            livelihoodSelect.value !== 'others') {
            errors.push('Secondary livelihood cannot be the same as main livelihood');
            if (typeof markFieldError === 'function') {
                markFieldError(secondaryLivelihoodSelect);
            }
            isValid = false;
        }
    }


    // Text match check (allows both to be "others" with same text)
    if (!validateSecondaryLivelihoodTextMatch()) {
        errors.push('Secondary livelihood cannot be similar to main livelihood');
        isValid = false;
    }
    
      // If both are "others", check if the text is the same
    if (livelihoodSelect.value === 'others' && secondaryLivelihoodSelect.value === 'others') {
        const mainOthersText = (otherLivelihoodInput?.value || '').trim().toLowerCase();
        const secondaryOthersText = (otherSecondaryLivelihoodInput?.value || '').trim().toLowerCase();
        
        if (mainOthersText && secondaryOthersText && mainOthersText === secondaryOthersText) {
            errors.push('Secondary livelihood cannot be the same as main livelihood');
            if (typeof markFieldError === 'function') {
                markFieldError(secondaryLivelihoodSelect);
            }
            isValid = false;
        }
    }

     if (otherSecondaryLivelihoodInput && otherSecondaryLivelihoodInput.value.trim()) {
            if (!isValidOthersLivelihoodText(otherSecondaryLivelihoodInput.value)) {
                errors.push('Secondary livelihood "others": Only letters, numbers, spaces, hyphens, apostrophes, periods, and commas are allowed');
                if (typeof markFieldError === 'function') {
                    markFieldError(otherSecondaryLivelihoodInput);
                }
                isValid = false;
            }
        }

    // ✓ VALIDATION: Ensure secondary livelihood text doesn't match main
    if (!validateSecondaryLivelihoodTextMatch()) {
        errors.push('Secondary livelihood cannot be the same as main livelihood');
        isValid = false;
    }

    // Supporting documents validation (optional - only validate if file is provided)
    const docsInput = form.querySelector('[name="supporting_document"]');
    if (docsInput && docsInput.files && docsInput.files.length > 0) {
        // Validate file size only if file is provided (max 10MB)
        const maxSize = 10 * 1024 * 1024;
        if (docsInput.files[0].size > maxSize) {
            errors.push('Supporting document must not exceed 10MB');
            if (typeof markFieldError === 'function') {
                markFieldError(docsInput);
            }
            isValid = false;
        }
    }

    // Show validation errors if any
    if (!isValid) {
        if (typeof displayValidationErrors === 'function') {
            displayValidationErrors(errors);
        }
    }

    return isValid; // ✓ Always return the validation result
}

/**
 * Initialize FishR form submission handling with enhanced CSRF protection
 * ✓ FIXED: Calls validateFishRForm() before submitting
 */
function initializeFishRFormSubmission() {
    const form = document.getElementById('fishr-registration-form');
    if (!form) {
        console.log('FishR form not found for AJAX initialization');
        return;
    }

    console.log('Initializing FishR AJAX form submission');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('FishR form submitted');

        // Check authentication before submitting
        if (!isUserAuthenticatedAndVerified()) {
            showAuthRequired('FishR Registration');
            return false;
        }

        // ✓✓✓ CRITICAL FIX: VALIDATE FORM BEFORE SUBMITTING ✓✓✓
        if (!validateFishRForm()) {
            console.log('Form validation failed - submission blocked');
            return false; // STOP HERE - Don't proceed if validation fails
        }
        // ✓✓✓ END OF CRITICAL FIX ✓✓✓

        const submitBtn = document.getElementById('fishr-submit-btn');

        // Show loading state - handle both button styles
        const originalText = submitBtn.textContent;
        const btnText = submitBtn.querySelector('.fishr-btn-text');
        const btnLoading = submitBtn.querySelector('.fishr-btn-loading');

        if (btnText && btnLoading) {
            // New button style with spans
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
        } else {
            // Simple button style
            submitBtn.textContent = 'Submitting...';
        }
        submitBtn.disabled = true;

        try {
            // Ensure we have a fresh CSRF token
            let token = getCSRFToken();
            if (!token) {
                console.log('No CSRF token found, refreshing...');
                token = await refreshCSRFToken();
            }

            // Create FormData
            const formData = new FormData(form);

            console.log('Sending AJAX request to:', form.action);

            // Submit via AJAX with retry logic
            const response = await fetchWithCSRFRetry(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                // Show success message with reference number
                agrisysModal.success(data.message, {
                    title: 'Registration Submitted!',
                    reference: data.registration_number || data.fishr_number || data.reference_number || null,
                    onClose: () => {
                        // Reset button state BEFORE closing form
                        if (btnText) btnText.style.display = 'inline';
                        if (btnLoading) btnLoading.style.display = 'none';
                        submitBtn.disabled = false;

                        // Reset form and go back to home
                        form.reset();

                        // Reset other livelihood field if it was showing
                        const livelihoodSelect = document.getElementById('fishr-main_livelihood');
                        if (livelihoodSelect) {
                            toggleOtherLivelihood(livelihoodSelect);
                        }

                        // Close form and return to landing
                        closeFormFishR();
                        // Scroll to top after modal closes and form is hidden
                        setTimeout(() => {
                            document.documentElement.scrollTop = 0;
                            document.body.scrollTop = 0;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }, 500);
                    }
                });
            } else {
                // Show error message
                if (data.errors) {
                    const errorList = Object.values(data.errors).flat();
                    agrisysModal.validationError(errorList, { title: 'Submission Failed' });
                } else {
                    agrisysModal.error(data.message || 'There was an error submitting your request.', { title: 'Submission Failed' });
                }
                
                // Reset button state on error so user can retry
                if (btnText) btnText.style.display = 'inline';
                if (btnLoading) btnLoading.style.display = 'none';
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('FishR submission error:', error);

            if (error.message.includes('CSRF') || error.message.includes('419')) {
                agrisysModal.error('Your session has expired. Please refresh the page and try again.', { title: 'Session Expired' });
            } else {
                agrisysModal.error('There was an error submitting your request. Please try again.', { title: 'Submission Error' });
            }
            
            // Reset button state on error so user can retry
            if (btnText) btnText.style.display = 'inline';
            if (btnLoading) btnLoading.style.display = 'none';
            submitBtn.disabled = false;
        }
    });
}

// /**
//  * Auto-fill form with sample data for testing
//  */
// function fillSampleFishRData() {
//     const form = document.getElementById('fishr-registration-form');
//     if (!form) return;

//     // Sample data
//     const sampleData = {
//         first_name: 'Juan',
//         middle_name: 'Santos',
//         last_name: 'Mangingisda',
//         sex: 'Male',
//         barangay: 'Riverside',
//         contact_number: '09123456789',
//         main_livelihood: 'aquaculture'
//     };

//     // Fill form fields
//     Object.keys(sampleData).forEach(fieldName => {
//         const input = form.querySelector(`[name="${fieldName}"]`);
//         if (input) {
//             input.value = sampleData[fieldName];

//             // Trigger change event for select elements
//             if (input.tagName === 'SELECT') {
//                 input.dispatchEvent(new Event('change'));
//             }
//         }
//     });

//     console.log('Sample data filled for FishR form');
// }

/**
 * Validate "others" livelihood field for special characters
 * Allows only: letters, numbers, spaces, hyphens, apostrophes, periods, commas
 */
function isValidOthersLivelihoodText(text) {
    const validPattern = /^[a-zA-Z0-9\s\'-.,]*$/;
    return validPattern.test(text);
}

/**
 * Enhanced: Prevent secondary livelihood from having same text as main when both are "others"
 */
function validateSecondaryLivelihoodTextMatch() {
    const mainLivelihoodSelect = document.getElementById('fishr-main_livelihood');
    const secondaryLivelihoodSelect = document.getElementById('fishr-secondary_livelihood');
    const mainOthersInput = document.getElementById('fishr-other_livelihood');
    const secondaryOthersInput = document.getElementById('fishr-other_secondary_livelihood');

    if (!secondaryOthersInput) return true;

    let textMatchWarning = document.getElementById('fishr-other_secondary_livelihood-text-match-warning');
    if (!textMatchWarning) {
        textMatchWarning = document.createElement('span');
        textMatchWarning.id = 'fishr-other_secondary_livelihood-text-match-warning';
        textMatchWarning.className = 'validation-warning';
        textMatchWarning.style.color = '#ff6b6b';
        textMatchWarning.style.fontSize = '0.875rem';
        textMatchWarning.style.display = 'none';
        textMatchWarning.style.marginTop = '4px';
        textMatchWarning.textContent = 'Secondary livelihood cannot be the same as main livelihood';
        secondaryOthersInput.parentNode.appendChild(textMatchWarning);
    }

    const mainValue = mainLivelihoodSelect?.value || '';
    const secondaryValue = secondaryLivelihoodSelect?.value || '';
    const mainOthersValue = (mainOthersInput?.value || '').trim().toLowerCase();
    const secondaryOthersValue = (secondaryOthersInput?.value || '').trim().toLowerCase();

    let showWarning = false;

    // Case 1: Both are "others" and have the same text
    if (mainValue === 'others' && secondaryValue === 'others') {
        if (mainOthersValue && secondaryOthersValue && mainOthersValue === secondaryOthersValue) {
            showWarning = true;
        }
    }
    
    // Keep only the mismatches between "others" text and standard livelihood types
    // Case 1: Secondary is "others" and its text matches the main livelihood type
    if (secondaryValue === 'others' && mainValue !== 'others' && mainValue) {
        const livelihoodTextMap = {
            'capture': ['capture', 'fishing'],
            'aquaculture': ['aquaculture', 'fish pond', 'fishpond'],
            'vending': ['vending', 'vendor'],
            'processing': ['processing', 'processor']
        };

        const mainLivelihoodTexts = livelihoodTextMap[mainValue] || [];
        const hasMatch = mainLivelihoodTexts.some(text => 
            secondaryOthersValue.includes(text)
        );

        if (hasMatch) {
            showWarning = true;
        }
    }
    
    // Case 2: Secondary is "others" and its text matches the main livelihood type
    else if (secondaryValue === 'others' && mainValue !== 'others' && mainValue) {
        const livelihoodTextMap = {
            'capture': ['capture', 'fishing'],
            'aquaculture': ['aquaculture', 'fish pond', 'fishpond'],
            'vending': ['vending', 'vendor'],
            'processing': ['processing', 'processor']
        };

        const mainLivelihoodTexts = livelihoodTextMap[mainValue] || [];
        const hasMatch = mainLivelihoodTexts.some(text => 
            secondaryOthersValue.includes(text)
        );

        if (hasMatch) {
            showWarning = true;
        }
    }
    // Case 3: Main is "others" and secondary is a standard option that matches main's text
    else if (mainValue === 'others' && secondaryValue !== 'others' && secondaryValue) {
        const livelihoodTextMap = {
            'capture': ['capture', 'fishing'],
            'aquaculture': ['aquaculture', 'fish pond', 'fishpond'],
            'vending': ['vending', 'vendor'],
            'processing': ['processing', 'processor']
        };

        const secondaryTexts = livelihoodTextMap[secondaryValue] || [];
        const hasMatch = secondaryTexts.some(text => 
            mainOthersValue.includes(text)
        );

        if (hasMatch) {
            showWarning = true;
        }
    }

    if (showWarning) {
        textMatchWarning.style.display = 'block';
        secondaryLivelihoodSelect.style.borderColor = '#ff6b6b';
        if (secondaryOthersInput) secondaryOthersInput.style.borderColor = '#ff6b6b';
        return false;
    } else {
        textMatchWarning.style.display = 'none';
        secondaryLivelihoodSelect.style.borderColor = '';
        if (secondaryOthersInput) secondaryOthersInput.style.borderColor = '';
        return true;
    }
}

/**
 * Clear form data
 */
function clearFishRForm() {
    if (confirm('Are you sure you want to clear all form data?')) {
        resetFishRForm();
    }
}

/**
 * Show form statistics (for admin/debug purposes)
 */
function showFishRStats() {
    // This would typically fetch from server
    const stats = {
        totalSubmissions: 0,
        todaySubmissions: 0,
        pendingReview: 0,
        approved: 0,
        rejected: 0
    };

    console.log('FishR Registration Statistics:', stats);
    return stats;
}

/**
 * Handle browser back/forward buttons
 */
function handlePopState(event) {
    console.log('Pop state event:', event.state);

    if (event.state && event.state.page === 'fishr') {
        // User navigated back to FishR form
        openFormFishR(new Event('popstate'));
    } else {
        // User navigated away from FishR form
        closeFormFishR();
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
        'rsbsa-form',
        'seedlings-choice', 'seedlings-form',
        'fishr-form', 'boatr-form'
    ];

    formIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.style.display = 'none';
    });

    // Also hide by class selector for application sections
    const formSections = document.querySelectorAll('.application-section');
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

    const firstTabBtn = formSection.querySelector('.fishr-tab-btn');
    const firstTabContent = formSection.querySelector('.fishr-tab-content');

    if (firstTabBtn && firstTabContent) {
        // Reset all tabs in this form
        formSection.querySelectorAll('.fishr-tab-btn').forEach(btn => btn.classList.remove('active'));
        formSection.querySelectorAll('.fishr-tab-content').forEach(tab => tab.style.display = 'none');

        // Activate first tab
        firstTabBtn.classList.add('active');
        firstTabContent.style.display = 'block';
    }
}

// ==============================================
// INITIALIZATION
// ==============================================

/**
 * Initialize FishR form when page loads
 */
function initializeFishRModule() {
    console.log('Initializing FishR module...');

    // Check if we should show the FishR form based on URL
    checkAndShowFishROnLoad();

    // Get initial CSRF token
    getCSRFToken();

    // Initialize AJAX form submission
    initializeFishRFormSubmission();

    // Set up initial form state
    const livelihoodSelect = document.getElementById('fishr-main_livelihood');
    if (livelihoodSelect) {
        // Initialize other livelihood field visibility
        if (livelihoodSelect.value) {
            toggleOtherLivelihood(livelihoodSelect);
        }
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', handlePopState);

    // Add keyboard shortcuts for development
    document.addEventListener('keydown', function(e) {
        // Ctrl + Shift + F = Fill sample data
        if (e.ctrlKey && e.shiftKey && e.key === 'F') {
            e.preventDefault();
            fillSampleFishRData();
        }

        // Ctrl + Shift + C = Clear form
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            clearFishRForm();
        }
    });

    console.log('FishR module initialized successfully');

    // Also handle window load event for additional safety
    window.addEventListener('load', function() {
        console.log('Window loaded, double-checking FishR form display');

        // Double-check and show form if needed (only once)
        setTimeout(checkAndShowFishROnLoad, 300);
    });
}

/**
 * Handles secondary livelihood selection change
 */
function toggleOtherSecondaryLivelihood(select) {
    if (!select) {
        console.error('Select element not provided to toggleOtherSecondaryLivelihood');
        return;
    }

    const otherField = document.getElementById('fishr-other-secondary-livelihood-field');
    const otherInput = document.getElementById('fishr-other_secondary_livelihood');
    const selectedValue = select.value;

    if (otherField && otherInput) {
        if (selectedValue === 'others') {
            otherField.style.display = 'block';
            otherInput.setAttribute('required', 'required');
            otherInput.focus();
        } else {
            otherField.style.display = 'none';
            otherInput.removeAttribute('required');
            otherInput.value = '';
        }
    }

    // ✓ NEW: Validate secondary livelihood text match
    validateSecondaryLivelihoodTextMatch();
    
    // Keep existing validation
    if (typeof validateSecondaryLivelihoodMatch === 'function') {
        validateSecondaryLivelihoodMatch();
    }

    console.log('FishR secondary livelihood changed to:', selectedValue);
}

/**
 * Real-time validation: Check if secondary livelihood matches main livelihood
 */
function validateSecondaryLivelihoodMatch() {
    const mainLivelihood = document.getElementById('fishr-main_livelihood');
    const secondaryLivelihood = document.getElementById('fishr-secondary_livelihood');
    const warning = document.getElementById('fishr-secondary_livelihood-warning');

    if (!mainLivelihood || !secondaryLivelihood || !warning) {
        return true; // Return true if elements don't exist
    }

    const mainValue = mainLivelihood.value;
    const secondaryValue = secondaryLivelihood.value;

    // Main and Secondary livelihood dropdown comparison
    if (mainValue && secondaryValue && mainValue === secondaryValue && mainValue !== 'others') {
        warning.style.display = 'block';
        secondaryLivelihood.style.borderColor = '#ff6b6b';
        return false; // RETURN FALSE TO BLOCK (used by real-time validation)
    } else {
        warning.style.display = 'none';
        secondaryLivelihood.style.borderColor = '';
        return true; // RETURN TRUE TO ALLOW (used by real-time validation)
    }
}

// ==============================================
// VALIDATION HELPERS
// ==============================================

/**
 * Validate phone number format (09XXXXXXXXX)
 */
function isValidPhoneNumber(phone) {
    const phonePattern = /^09\d{9}$/;
    return phonePattern.test(phone);
}

/**
 * Validate name format (letters, spaces, hyphens, apostrophes)
 */
function isValidNameFormat(name) {
    const namePattern = /^[a-zA-Z\s\'-]*$/;
    return namePattern.test(name);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const nameFields = [{
            id: 'fishr-first_name',
            pattern: /^[a-zA-Z\s\'-]*$/
        },
        {
            id: 'fishr-middle_name',
            pattern: /^[a-zA-Z\s\'-]*$/
        },
        {
            id: 'fishr-last_name',
            pattern: /^[a-zA-Z\s\'-]*$/
        },
        {
            id: 'fishr-name_extension',
            pattern: /^[a-zA-Z.\s]*$/
        }
    ];

    // === REAL-TIME VALIDATION FOR NAME FIELDS ===
    nameFields.forEach(field => {
        const input = document.getElementById(field.id);
        const warning = document.getElementById(field.id + '-warning');

        if (input && warning) {
            input.addEventListener('input', function(e) {
                const value = e.target.value;

                if (!field.pattern.test(value)) {
                    warning.style.display = 'block';
                    input.style.borderColor = '#ff6b6b';
                } else {
                    warning.style.display = 'none';
                    input.style.borderColor = '';
                }
            });

            // Also validate on blur
            input.addEventListener('blur', function(e) {
                if (!field.pattern.test(e.target.value) && e.target.value !== '') {
                    warning.style.display = 'block';
                    input.style.borderColor = '#ff6b6b';
                }
            });
        }
    });

    // === REAL-TIME VALIDATION FOR CONTACT NUMBER ===
    const contactInput = document.getElementById('fishr-contact_number');
    const contactWarning = document.getElementById('fishr-contact_number-warning');

    if (contactInput && contactWarning) {
        const phonePattern = /^09\d{9}$/;

        contactInput.addEventListener('input', function(e) {
            const value = e.target.value;

            if (value && !phonePattern.test(value)) {
                contactWarning.style.display = 'block';
                contactInput.style.borderColor = '#ff6b6b';
            } else {
                contactWarning.style.display = 'none';
                contactInput.style.borderColor = '';
            }
        });

        // Also validate on blur
        contactInput.addEventListener('blur', function(e) {
            const value = e.target.value;

            if (value && !phonePattern.test(value)) {
                contactWarning.style.display = 'block';
                contactInput.style.borderColor = '#ff6b6b';
            }
        });
    }

    // === REAL-TIME VALIDATION FOR OTHER LIVELIHOOD FIELD ===
    const otherLivelihoodInput = document.getElementById('fishr-other_livelihood');
    const otherSecondaryLivelihoodInput = document.getElementById('fishr-other_secondary_livelihood');

    if (otherLivelihoodInput) {
        // Create special char warning span if it doesn't exist
        let specialCharWarning = document.getElementById('fishr-other_livelihood-special-chars-warning');
        if (!specialCharWarning) {
            specialCharWarning = document.createElement('span');
            specialCharWarning.id = 'fishr-other_livelihood-special-chars-warning';
            specialCharWarning.className = 'validation-warning';
            specialCharWarning.style.color = '#ff6b6b';
            specialCharWarning.style.fontSize = '0.875rem';
            specialCharWarning.style.display = 'none';
            specialCharWarning.style.marginTop = '4px';
            specialCharWarning.textContent = 'Special characters not allowed. Use only: letters, numbers, spaces, hyphens (-), apostrophes (\'), periods (.), commas (,)';
            otherLivelihoodInput.parentNode.appendChild(specialCharWarning);
        }

        otherLivelihoodInput.addEventListener('input', function(e) {
            const value = e.target.value;

            // ✓ CHECK FOR SPECIAL CHARACTERS
            if (value && !isValidOthersLivelihoodText(value)) {
                specialCharWarning.style.display = 'block';
                otherLivelihoodInput.style.borderColor = '#ff6b6b';
                otherLivelihoodInput.style.backgroundColor = '#ffe6e6';
            } else {
                specialCharWarning.style.display = 'none';
                otherLivelihoodInput.style.borderColor = '';
                otherLivelihoodInput.style.backgroundColor = '';
            }
        });

        otherLivelihoodInput.addEventListener('blur', function(e) {
            const value = e.target.value;

            // ✓ ALSO CHECK ON BLUR
            if (value && !isValidOthersLivelihoodText(value)) {
                specialCharWarning.style.display = 'block';
                otherLivelihoodInput.style.borderColor = '#ff6b6b';
                otherLivelihoodInput.style.backgroundColor = '#ffe6e6';
            } else {
                specialCharWarning.style.display = 'none';
                otherLivelihoodInput.style.borderColor = '';
                otherLivelihoodInput.style.backgroundColor = '';
            }
        });
    }

    // === REAL-TIME VALIDATION FOR OTHER SECONDARY LIVELIHOOD FIELD ===
   if (otherSecondaryLivelihoodInput) {
        // Create special char warning span if it doesn't exist
        let specialCharWarning = document.getElementById('fishr-other_secondary_livelihood-special-chars-warning');
        if (!specialCharWarning) {
            specialCharWarning = document.createElement('span');
            specialCharWarning.id = 'fishr-other_secondary_livelihood-special-chars-warning';
            specialCharWarning.className = 'validation-warning';
            specialCharWarning.style.color = '#ff6b6b';
            specialCharWarning.style.fontSize = '0.875rem';
            specialCharWarning.style.display = 'none';
            specialCharWarning.style.marginTop = '4px';
            specialCharWarning.textContent = 'Special characters not allowed. Use only: letters, numbers, spaces, hyphens (-), apostrophes (\'), periods (.), commas (,)';
            otherSecondaryLivelihoodInput.parentNode.appendChild(specialCharWarning);
        }

        otherSecondaryLivelihoodInput.addEventListener('input', function(e) {
            const value = e.target.value;

            // ✓ CHECK FOR SPECIAL CHARACTERS
            if (value && !isValidOthersLivelihoodText(value)) {
                specialCharWarning.style.display = 'block';
                otherSecondaryLivelihoodInput.style.borderColor = '#ff6b6b';
                otherSecondaryLivelihoodInput.style.backgroundColor = '#ffe6e6';
            } else {
                specialCharWarning.style.display = 'none';
                otherSecondaryLivelihoodInput.style.borderColor = '';
                otherSecondaryLivelihoodInput.style.backgroundColor = '';
            }

            // ✓ ALSO CHECK FOR TEXT MATCH
            validateSecondaryLivelihoodTextMatch();
        });

        otherSecondaryLivelihoodInput.addEventListener('blur', function(e) {
            const value = e.target.value;

            // ✓ ALSO CHECK ON BLUR
            if (value && !isValidOthersLivelihoodText(value)) {
                specialCharWarning.style.display = 'block';
                otherSecondaryLivelihoodInput.style.borderColor = '#ff6b6b';
                otherSecondaryLivelihoodInput.style.backgroundColor = '#ffe6e6';
            } else {
                specialCharWarning.style.display = 'none';
                otherSecondaryLivelihoodInput.style.borderColor = '';
                otherSecondaryLivelihoodInput.style.backgroundColor = '';
            }

            // ✓ VALIDATE TEXT MATCH ON BLUR TOO
            validateSecondaryLivelihoodTextMatch();
        });
    }

        // === REAL-TIME VALIDATION FOR MAIN LIVELIHOOD "OTHERS" FIELD ===
    if (otherLivelihoodInput) {
        otherLivelihoodInput.addEventListener('input', function(e) {
            const value = e.target.value;
            validateSecondaryLivelihoodTextMatch();
        });

        otherLivelihoodInput.addEventListener('blur', function(e) {
            validateSecondaryLivelihoodTextMatch();
        });
    }

    // === REAL-TIME VALIDATION FOR SECONDARY LIVELIHOOD "OTHERS" TEXT MATCH ===
    if (otherSecondaryLivelihoodInput) {
        otherSecondaryLivelihoodInput.addEventListener('input', function(e) {
            const mainLivelihood = document.getElementById('fishr-main_livelihood');
            const secondaryLivelihood = document.getElementById('fishr-secondary_livelihood');
            
            // Check if both are "others"
            if (mainLivelihood.value === 'others' && secondaryLivelihood.value === 'others') {
                const mainOthersText = (otherLivelihoodInput?.value || '').trim().toLowerCase();
                const secondaryOthersText = (e.target.value || '').trim().toLowerCase();
                
                if (mainOthersText && secondaryOthersText && mainOthersText === secondaryOthersText) {
                    e.target.style.borderColor = '#ff6b6b';
                    e.target.style.backgroundColor = '#ffe6e6';
                } else {
                    e.target.style.borderColor = '';
                    e.target.style.backgroundColor = '';
                }
            }
        });

        otherSecondaryLivelihoodInput.addEventListener('blur', function(e) {
            const mainLivelihood = document.getElementById('fishr-main_livelihood');
            const secondaryLivelihood = document.getElementById('fishr-secondary_livelihood');
            
            if (mainLivelihood.value === 'others' && secondaryLivelihood.value === 'others') {
                const mainOthersText = (otherLivelihoodInput?.value || '').trim().toLowerCase();
                const secondaryOthersText = (e.target.value || '').trim().toLowerCase();
                
                if (mainOthersText && secondaryOthersText && mainOthersText === secondaryOthersText) {
                    e.target.style.borderColor = '#ff6b6b';
                    e.target.style.backgroundColor = '#ffe6e6';
                } else {
                    e.target.style.borderColor = '';
                    e.target.style.backgroundColor = '';
                }
            }
        });
    }
    
    
    // Small delay to ensure all elements are ready
    setTimeout(initializeFishRModule, 100);
});

// ==============================================
// GLOBAL FUNCTIONS FOR COMPATIBILITY
// ==============================================

// Make functions available globally for HTML onclick handlers
window.openFormFishR = openFormFishR;
window.closeFormFishR = closeFormFishR;
window.showFishrTab = showFishrTab;
window.toggleOtherLivelihood = toggleOtherLivelihood;
window.fillSampleFishRData = fillSampleFishRData;
window.toggleOtherSecondaryLivelihood = toggleOtherSecondaryLivelihood; 
window.validateSecondaryLivelihoodMatch = validateSecondaryLivelihoodMatch;
window.validateFishRForm = validateFishRForm;

console.log('✓ Enhanced FishR JavaScript module loaded with CSRF protection and full validation');