// ==============================================
// COMPLETE UPDATED FISH REGISTRATION JAVASCRIPT
// Enhanced with CSRF protection and error handling
// File: public/js/fishr.js
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
    // Auto-scroll to the active tab content
        setTimeout(() => {
            selectedTab.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }, 50); // Small delay for smooth transition

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

        // Scroll to the fish registration form smoothly
        setTimeout(() => {
            const formElement = document.getElementById('fishr-form');
            if (formElement) {
                formElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 100); // Small delay to ensure form is visible

        // Update URL without page reload
        if (window.location.pathname !== '/services/fishr') {
            history.pushState({page: 'fishr'}, '', '/services/fishr');
        }

        console.log('FishR form opened successfully');
    } else {
        console.error('Fish Registration form element not found');
        alert('Form not available. Please refresh the page and try again.');
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

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });

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
        const otherField = document.getElementById('other-livelihood-field');
        if (otherField) {
            otherField.style.display = 'none';
        }

        // Remove required attribute from other livelihood input
        const otherInput = document.getElementById('other_livelihood');
        if (otherInput) {
            otherInput.removeAttribute('required');
        }

        // Hide messages
        const messagesContainer = document.getElementById('fishr-messages');
        if (messagesContainer) {
            messagesContainer.style.display = 'none';
        }

        // Clear any validation error messages
        const errorTexts = form.querySelectorAll('.error-text');
        errorTexts.forEach(error => error.remove());

        // Reset submit button state
        const submitBtn = document.getElementById('fishr-submit-btn');
        if (submitBtn) {
            submitBtn.disabled = false;
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            if (btnText) btnText.style.display = 'inline';
            if (btnLoading) btnLoading.style.display = 'none';
        }

        // Reset to first tab if showTab function exists
        if (typeof showFishrTab === 'function') {
            const firstTabBtn = document.querySelector('#fishr-form .tab-btn');
            if (firstTabBtn) {
                showTab('fishr-form-tab', { target: firstTabBtn });
            }
        }

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

    const otherField = document.getElementById('other-livelihood-field');
    const otherInput = document.getElementById('other_livelihood');
    const selectedValue = select.value;

    if (otherField && otherInput) {
        if (selectedValue === 'others') {
            // Show other livelihood field and make it required
            otherField.style.display = 'block';
            otherInput.setAttribute('required', 'required');
            otherInput.focus(); // Focus on the input for better UX
        } else {
            // Hide other livelihood field and remove requirement
            otherField.style.display = 'none';
            otherInput.removeAttribute('required');
            otherInput.value = ''; // Clear the value
        }
    }

    // Update supporting documents requirement based on livelihood
    updateDocumentsRequirement(selectedValue);

    console.log('FishR livelihood changed to:', selectedValue);
}

/**
 * Updates supporting documents requirement based on livelihood type
 */
function updateDocumentsRequirement(livelihoodType) {
    const docsInput = document.getElementById('supporting_documents');
    const docsLabel = document.querySelector('label[for="supporting_documents"]');
    const docsHelp = document.querySelector('#supporting_documents + .form-text');

    if (docsInput && docsLabel) {
        if (livelihoodType === 'capture') {
            // Capture fishing - documents optional
            docsInput.removeAttribute('required');
            docsLabel.innerHTML = 'Supporting Documents (Optional)';
            if (docsHelp) {
                docsHelp.textContent = 'Optional for Capture Fishing. Max size: 10MB';
            }
        } else if (livelihoodType && livelihoodType !== '') {
            // Other livelihood types - documents required
            docsInput.setAttribute('required', 'required');
            docsLabel.innerHTML = 'Supporting Documents *';
            if (docsHelp) {
                docsHelp.textContent = 'Required for this livelihood type. Max size: 10MB';
            }
        } else {
            // No livelihood selected - neutral state
            docsInput.removeAttribute('required');
            docsLabel.innerHTML = 'Supporting Documents';
            if (docsHelp) {
                docsHelp.textContent = 'Required for all livelihood types except Capture Fishing. Max size: 10MB';
            }
        }
    }
}

/**
 * Initialize FishR form submission handling with enhanced CSRF protection
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

        const submitBtn = document.getElementById('fishr-submit-btn');

        // Show loading state - handle both button styles
        const originalText = submitBtn.textContent;
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');

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
                // Show success message
                alert('✅ ' + data.message);

                // Reset form and go back to home
                form.reset();

                // Reset other livelihood field if it was showing
                const livelihoodSelect = document.getElementById('main_livelihood');
                if (livelihoodSelect) {
                    toggleOtherLivelihood(livelihoodSelect);
                }

                // Close form and return to landing
                closeFormFishR();
            } else {
                // Show error message
                alert('❌ ' + (data.message || 'There was an error submitting your request.'));

                // Show validation errors if available
                if (data.errors) {
                    showFishRValidationErrors(data.errors);
                }
            }
        } catch (error) {
            console.error('FishR submission error:', error);

            if (error.message.includes('CSRF') || error.message.includes('419')) {
                alert('❌ Your session has expired. Please refresh the page and try again.');
            } else {
                alert('❌ There was an error submitting your request. Please try again.');
            }
        } finally {
            // Reset button state - handle both styles
            if (btnText && btnLoading) {
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            } else {
                submitBtn.textContent = originalText;
            }
            submitBtn.disabled = false;
        }
    });
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

/**
 * Validates the form before submission
 */
function validateFishRForm() {
    const form = document.getElementById('fishr-registration-form');
    if (!form) return false;

    let isValid = true;
    const errors = [];

    // Required field validation
    const requiredFields = [
        { name: 'first_name', label: 'First Name' },
        { name: 'last_name', label: 'Last Name' },
        { name: 'sex', label: 'Sex' },
        { name: 'barangay', label: 'Barangay' },
        { name: 'mobile_number', label: 'Mobile Number' },
        { name: 'main_livelihood', label: 'Main Livelihood' }
    ];

    requiredFields.forEach(field => {
        const input = form.querySelector(`[name="${field.name}"]`);
        if (!input || !input.value.trim()) {
            errors.push(`${field.label} is required`);
            isValid = false;
        }
    });

    // Conditional validation for "others" livelihood
    const livelihoodSelect = form.querySelector('[name="main_livelihood"]');
    const otherLivelihoodInput = form.querySelector('[name="other_livelihood"]');

    if (livelihoodSelect && livelihoodSelect.value === 'others') {
        if (!otherLivelihoodInput || !otherLivelihoodInput.value.trim()) {
            errors.push('Please specify your livelihood when selecting "Others"');
            isValid = false;
        }
    }

    // Mobile number format validation
    const mobileInput = form.querySelector('[name="mobile_number"]');
    if (mobileInput && mobileInput.value) {
        const mobilePattern = /^(09|\+639)\d{9}$/;
        const cleanMobile = mobileInput.value.replace(/\s+/g, '');
        if (!mobilePattern.test(cleanMobile)) {
            errors.push('Please enter a valid mobile number (e.g., 09123456789)');
            isValid = false;
        }
    }

    // Supporting documents validation for non-capture livelihoods
    const docsInput = form.querySelector('[name="supporting_documents"]');
    if (livelihoodSelect && livelihoodSelect.value && livelihoodSelect.value !== 'capture') {
        if (!docsInput || !docsInput.files || docsInput.files.length === 0) {
            errors.push('Supporting documents are required for this livelihood type');
            isValid = false;
        }
    }

    // Show validation errors if any
    if (!isValid) {
        const errorMessage = 'Please correct the following errors:\n• ' + errors.join('\n• ');
        alert(errorMessage);
    }

    return isValid;
}

/**
 * Auto-fill form with sample data for testing
 */
function fillSampleFishRData() {
    const form = document.getElementById('fishr-registration-form');
    if (!form) return;

    // Sample data
    const sampleData = {
        first_name: 'Juan',
        middle_name: 'Santos',
        last_name: 'Mangingisda',
        sex: 'Male',
        barangay: 'Riverside',
        mobile_number: '09123456789',
        main_livelihood: 'aquaculture'
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

    console.log('Sample data filled for FishR form');
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
        '.announcement',
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
        '.announcement',
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
    const livelihoodSelect = document.getElementById('main_livelihood');
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

    /**
 * Also handle window load event for additional safety*/
    window.addEventListener('load', function() {
        console.log('Window loaded, double-checking FishR form display');

        // Double-check and show form if needed (only once)
        setTimeout(checkAndShowFishROnLoad, 300);
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
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

console.log('Enhanced FishR JavaScript module loaded with CSRF protection');
