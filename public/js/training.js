// ==============================================
// TRAINING REQUEST JAVASCRIPT
// Handles training application form functionality
// ==============================================

// ==============================================
// FORM MANAGEMENT FUNCTIONS
// ==============================================
function openFormTraining(event) {
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    }

    // Check authentication before allowing access
    if (!showAuthRequired('Training Registration')) {
        return false;
    }

    console.log('Opening Training form');

    // Hide all main sections and forms first
    if (typeof hideAllMainSections === 'function') hideAllMainSections();
    if (typeof hideAllForms === 'function') hideAllForms();

    const trainingForm = document.getElementById('training-form');
    if (trainingForm) {
        trainingForm.style.display = 'block';

        // Initialize tabs
        initializeTrainingTabs();

        // Reset form only if not from page load
        if (event && event.type !== 'load' && event.type !== 'DOMContentLoaded') {
            resetTrainingForm();
            // Re-initialize tabs after reset
            initializeTrainingTabs();
        }

        // Update URL
        // Update URL only if we're not already on the training page
        if (window.location.pathname !== '/services/training') {
            history.pushState({page: 'training'}, 'Training Application', '/services/training');
        }

        // Scroll to top with proper timing and multiple fallbacks
        setTimeout(() => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
        }, 50);

        console.log('Training form opened successfully');
    } else {
        console.error('Training form section not found');
        agrisysModal.error('Form not available. Please refresh the page and try again.', { title: 'Form Error' });
    }
}

/**
 * Check and show Training form on page load if URL matches
 */
function checkAndShowTrainingOnLoad() {
    console.log('=== IMPROVED: Checking if Training form should be shown ===');

    const currentPath = window.location.pathname;

    // Only show if URL exactly matches training path
    const shouldShow = currentPath === '/services/training';

    if (shouldShow) {
        console.log('Training form should be shown');

        // Force hide other sections
        if (typeof hideAllMainSections === 'function') hideAllMainSections();
        if (typeof hideAllForms === 'function') hideAllForms();

        const formElement = document.getElementById('training-form');
        if (formElement) {
            formElement.style.display = 'block';
            initializeTrainingTabs();

            // Ensure URL is correct without being aggressive
            if (window.location.pathname !== '/services/training') {
                history.replaceState({page: 'training'}, '', '/services/training');
            }

            return true;
        }
    }
    return false;
}

/**
 * Initialize Training tabs
 */
function initializeTrainingTabs() {
    const trainingForm = document.getElementById('training-form');
    if (!trainingForm) return;

    const allTabContents = trainingForm.querySelectorAll('.training-tab-content');
    allTabContents.forEach(content => content.style.display = 'none');

    const allButtons = trainingForm.querySelectorAll('.training-tab-btn');
    allButtons.forEach(btn => btn.classList.remove('active'));

    const firstTab = trainingForm.querySelector('.training-tab-content');
    const firstButton = trainingForm.querySelector('.training-tab-btn');

    if (firstTab && firstButton) {
        firstTab.style.display = 'block';
        firstButton.classList.add('active');
    }
}

/**
 * Initialize Training module
 */
function initializeTrainingModule() {
    console.log('Initializing Training module...');

    // Get initial CSRF token
    getTrainingCSRFToken();

    // Check if we should show the Training form based on URL (only once on init)
    checkAndShowTrainingOnLoad();
}

/**
 * Resets Training form to initial state
 */
function resetTrainingForm() {
    const form = document.getElementById('training-request-form');
    if (form) {
        // Reset form data
        form.reset();

        // Hide messages
        const messagesContainer = document.getElementById('training-messages');
        if (messagesContainer) {
            messagesContainer.style.display = 'none';
        }

        // Clear any validation error messages
        clearTrainingErrors();

        // Reset submit button state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Application';
        }

        console.log('Training form reset to initial state');
    }
}

/**
 * Closes Training form and returns to main services
 */
function closeFormTraining() {
    console.log('Closing Training form');

    const formElement = document.getElementById('training-form');
    if (formElement) {
        formElement.style.display = 'none';

        // Show main sections again
        if (typeof showAllMainSections === 'function') showAllMainSections();

        // Update URL to services page
        history.pushState({page: 'services'}, '', '/services');
    }
}


/**
 * Main tab switching function for Training form
 * This is the function your HTML onclick events are calling
 */
function showTrainingTab(tabId, event) {
    console.log('Switching to Training tab:', tabId);

    // Prevent default button behavior
    if (event) {
        event.preventDefault();
    }

    // Get the parent section containing all tabs
    const parentSection = event.target.closest('.training-application-section');
    if (!parentSection) {
        console.error('Parent section not found for Training tab switching');
        return;
    }

    // Remove active class from all tab buttons
    const tabButtons = parentSection.querySelectorAll('.training-tab-btn');
    tabButtons.forEach(btn => btn.classList.remove('active'));

    // Hide all tab content
    const tabContents = parentSection.querySelectorAll('.training-tab-content');
    tabContents.forEach(content => content.style.display = 'none');

    // Add active class to clicked button
    if (event && event.target) {
        event.target.classList.add('active');
    }

    // Show the selected tab content
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.style.display = 'block';
        console.log('Training tab switched successfully to:', tabId);
    } else {
        console.error('Tab content not found:', tabId);
    }
}

/**
 * Handle browser back/forward buttons
 */
// ==============================================
// FORM SUBMISSION AND VALIDATION
// ==============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing Training form');

    const trainingForm = document.getElementById('training-request-form');

    if (trainingForm) {
        trainingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Training form submission started');

            // Check authentication before submitting
            if (!isUserAuthenticatedAndVerified()) {
                showAuthRequired('Training Registration');
                return false;
            }

            // Basic validation
            if (!validateTrainingForm()) {
                console.log('Form validation failed');
                return false;
            }

            console.log('Form validation passed');

            // Show loading state
            const submitBtn = trainingForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;

            // Create FormData object
            const formData = new FormData(trainingForm);
            console.log('FormData created, submitting to /apply/training');

            // Submit form via AJAX
            fetch('/apply/training', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                   document.querySelector('input[name="_token"]')?.value
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message using modal with reference number
                    agrisysModal.success(data.message, {
                        title: 'Application Submitted!',
                        reference: data.application_number || data.reference_number || null,
                        onClose: () => {
                            // Reset form
                            trainingForm.reset();
                            // Close form
                            closeFormTraining();
                            // Scroll to top after modal closes and form is hidden
                            setTimeout(() => {
                                document.documentElement.scrollTop = 0;
                                document.body.scrollTop = 0;
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }, 500);
                        }
                    });
                } else {
                    // Show error message using modal
                    if (data.errors) {
                        const errorList = Object.values(data.errors).flat();
                        agrisysModal.validationError(errorList, { title: 'Submission Failed' });
                        Object.keys(data.errors).forEach(field => {
                            showFieldError(field, data.errors[field][0]);
                        });
                    } else {
                        agrisysModal.error(data.message || 'An error occurred while submitting your application.', { title: 'Submission Failed' });
                    }
                }
            })
            .catch(error => {
                console.error('Error submitting training application:', error);
                agrisysModal.error('An error occurred while submitting your application. Please try again.', { title: 'Submission Error' });
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Handle browser back/forward buttons
    // Note: Removed conflicting popstate handler - let landing.js handle routing

    // ENHANCED: Initialize with better timing and retry logic
    setTimeout(() => {
        initializeTrainingModule();
        initializeMobileNumberInput(); // Initialize mobile input formatting
    }, 200);

    console.log('Training form initialization completed');
});

// ==============================================
// VALIDATION FUNCTIONS
// ==============================================

function validateTrainingForm() {
    const form = document.getElementById('training-request-form');
    let isValid = true;

    console.log('Starting form validation...');

    // Clear previous error messages
    clearTrainingErrors();

    // Validate required fields
    const requiredFields = [
        { id: 'training_first_name', name: 'First Name' },
        { id: 'training_last_name', name: 'Last Name' },
        { id: 'training_contact_number', name: 'Contact Number' },
        { id: 'training_barangay', name: 'Barangay' },
        { id: 'training_type', name: 'Training Program' }
    ];

    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (!element) {
            console.warn(`Field with ID '${field.id}' not found`);
            isValid = false;
        } else if (!element.value.trim()) {
            console.log(`Field '${field.name}' is empty`);
            showFieldError(field.id, `${field.name} is required`);
            isValid = false;
        } else {
            console.log(`Field '${field.name}' validated successfully`);
        }
    });

    // Validate contact number format
    const contactNumberElement = document.getElementById('training_contact_number');
    if (contactNumberElement) {
        const contactNumber = contactNumberElement.value.trim();
        if (contactNumber && !validateMobileNumber(contactNumber)) {
            showFieldError('training_contact_number', 'Please enter a valid 11-digit contact number');
            isValid = false;
        }
    }

    // Email validation removed - not required for training applications

    // Validate file uploads
    const fileInput = document.getElementById('training_document');
    if (fileInput && fileInput.files.length > 0) {
        if (!validateFiles(fileInput.files)) {
            isValid = false;
        }
    }

    console.log('Form validation result:', isValid);
    return isValid;
}

function validateMobileNumber(mobile) {
    // Philippine mobile number format: 11 digits starting with 09
    const mobileRegex = /^09\d{9}$/;
    return mobileRegex.test(mobile);
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validateFiles(files) {
    const maxSize = 10 * 1024 * 1024; // 10MB
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

    // Since we only allow one file now
    const file = files[0];
    
    if (!file) {
        return true; // No file selected is valid (unless required by HTML)
    }

    if (file.size > maxSize) {
        agrisysModal.warning('File "' + file.name + '" is too large. Maximum size is 10MB.', { 
            title: 'File Too Large' 
        });
        return false;
    }

    if (!allowedTypes.includes(file.type)) {
        agrisysModal.warning('File "' + file.name + '" is not a supported format. Please upload PDF, JPG, or PNG files only.', { 
            title: 'Invalid File Type' 
        });
        return false;
    }

    return true;
}

// ==============================================
// ERROR HANDLING AND MESSAGING
// ==============================================

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    const formGroup = field.closest('.training-form-group');
    if (!formGroup) return;

    // Remove existing error
    const existingError = formGroup.querySelector('.error-text');
    if (existingError) {
        existingError.remove();
    }

    // Add new error
    const errorSpan = document.createElement('span');
    errorSpan.className = 'error-text';
    errorSpan.textContent = message;
    errorSpan.style.color = '#dc3545';
    errorSpan.style.fontSize = '12px';
    errorSpan.style.marginTop = '5px';
    errorSpan.style.display = 'block';
    formGroup.appendChild(errorSpan);

    // Add error styling to field
    field.classList.add('error');
}

function clearTrainingErrors() {
    // Clear all error messages
    const errorTexts = document.querySelectorAll('#training-request-form .error-text');
    errorTexts.forEach(error => error.remove());

    // Clear error styling
    const errorFields = document.querySelectorAll('#training-request-form .error');
    errorFields.forEach(field => field.classList.remove('error'));

    // Hide message containers
    const messagesContainer = document.getElementById('training-messages');
    if (messagesContainer) {
        messagesContainer.style.display = 'none';
    }
}

function showTrainingMessage(message, type = 'info') {
    const messagesContainer = document.getElementById('training-messages');
    const messageContent = document.getElementById('training-message-content');

    if (!messagesContainer || !messageContent) {
        console.warn('Training message containers not found');
        return;
    }

    // Clear existing classes
    messagesContainer.className = 'training-messages';

    // Add type-specific class
    if (type === 'success') {
        messagesContainer.classList.add('training-alert-success');
    } else if (type === 'error') {
        messagesContainer.classList.add('training-alert-danger');
    } else {
        messagesContainer.classList.add('training-alert-info');
    }

    // Set message content
    messageContent.textContent = message;

    // Show the message
    messagesContainer.style.display = 'block';

    // Auto-hide after 5 seconds for success messages
    if (type === 'success') {
        setTimeout(() => {
            messagesContainer.style.display = 'none';
        }, 5000);
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
        'fishr-form', 'boatr-form', 'training-form'
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

// ==============================================
// UTILITY FUNCTIONS
// ==============================================



// Format mobile number as user types
function initializeMobileNumberInput() {
    const mobileInput = document.getElementById('training_mobile_number');

    if (mobileInput) {
        mobileInput.addEventListener('input', function(e) {
            // Remove non-numeric characters
            let value = e.target.value.replace(/\D/g, '');

            // Limit to 11 digits
            if (value.length > 11) {
                value = value.slice(0, 11);
            }

            e.target.value = value;
        });
    }
}

// Add this function to check if Training form should be shown
function shouldShowTrainingForm() {
    const currentPath = window.location.pathname;

    // Only show training form if the URL explicitly matches the training path
    return currentPath === '/services/training';
}

// Remove the aggressive URL preservation function that was causing conflicts

/**
 * Get fresh CSRF token
 */
async function refreshTrainingCSRFToken() {
    try {
        const response = await fetch('/csrf-token');
        const data = await response.json();
        csrfToken = data.csrf_token;

        // Update meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', csrfToken);
        }

        console.log('CSRF token refreshed for training');
        return csrfToken;
    } catch (error) {
        console.error('Failed to refresh CSRF token:', error);
        throw error;
    }
}

/**
 * Get current CSRF token
 */
function getTrainingCSRFToken() {
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
 * Submit Training form with enhanced error handling and notifications
 */
function submitTrainingForm(event) {
    event.preventDefault();

    const form = document.getElementById('training-request-form');
    const formData = new FormData(form);

    // Show loading state - matches FishR style
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    fetch('/apply/training', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(response => {
        if (response.success) {
            // Show success modal
            agrisysModal.success(response.message, {
                title: 'Application Submitted!',
                reference: response.data.application_number || null,
                onClose: () => {
                    // Reset form
                    resetTrainingForm();
                    // Close form after delay
                    closeFormTraining();
                }
            });

        } else {
            // Show error modal
            if (response.errors) {
                const errorList = Object.values(response.errors).flat();
                agrisysModal.validationError(errorList, { title: 'Please Correct the Following' });

                // Show field errors
                Object.keys(response.errors).forEach(field => {
                    showFieldError(field, response.errors[field][0]);
                });
            } else {
                agrisysModal.error(response.message || 'An error occurred while submitting your application', { title: 'Submission Failed' });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        agrisysModal.error('An error occurred while submitting your application. Please try again.', { title: 'Submission Error' });
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

// ==============================================
// GLOBAL FUNCTIONS FOR COMPATIBILITY
// ==============================================

// Make functions available globally for HTML onclick handlers
window.openFormTraining = openFormTraining;
window.closeFormTraining = closeFormTraining;
window.showTrainingTab = showTrainingTab;

console.log('Training JavaScript module loaded successfully');
