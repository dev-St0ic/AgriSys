// ==============================================
// TRAINING REQUEST JAVASCRIPT
// Handles training application form functionality
// ==============================================

// ==============================================
// FORM MANAGEMENT FUNCTIONS
// ==============================================

function openFormTraining(event) {
    event.preventDefault();
    
    console.log('Opening Training form');
    
    // Hide all main sections and forms first
    if (typeof hideAllMainSections === 'function') hideAllMainSections();
    if (typeof hideAllForms === 'function') hideAllForms();
    
    const trainingForm = document.getElementById('training-form');
    if (trainingForm) {
        trainingForm.style.display = 'block';
        
        // Activate the application tab
        if (typeof activateApplicationTab === 'function') {
            activateApplicationTab('training-form');
        }
        
        // Reset form and clear any previous messages
        resetTrainingForm();
        
        // Update URL
        history.pushState({page: 'training'}, '', '/services/training');
      
        // Scroll to the training form smoothly
        setTimeout(() => {
            const trainingForm = document.getElementById('training-form');
            if (trainingForm) {
                trainingForm.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        }, 100); // Small delay to ensure form is visible
        
        console.log('Training form opened successfully');
    } else {
        console.error('Training form section not found');
        alert('Form not available. Please refresh the page and try again.');
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
        console.log('Training form closed');
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
function handleTrainingPopState(event) {
    console.log('Pop state event:', event.state);
    
    if (event.state && event.state.page === 'training') {
        // User navigated back to Training form
        openFormTraining(new Event('popstate'));
    } else {
        // User navigated away from Training form
        closeFormTraining();
    }
}

// ==============================================
// FORM SUBMISSION AND VALIDATION
// ==============================================

document.addEventListener('DOMContentLoaded', function() {
    const trainingForm = document.getElementById('training-request-form');
    
    if (trainingForm) {
        trainingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            if (!validateTrainingForm()) {
                return false;
            }
            
            // Show loading state
            const submitBtn = trainingForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;
            
            // Simulate form submission (frontend only)
            setTimeout(() => {
                showTrainingMessage('Application submitted successfully! You will be contacted regarding training schedule.', 'success');
                trainingForm.reset();
                
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Close form and return to landing after success
                setTimeout(() => {
                    closeFormTraining();
                }, 2000);
            }, 1500); // Simulate network delay
        });
    }
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', handleTrainingPopState);
    
    // Initialize form if page loads directly on training URL
    if (window.location.pathname === '/services/training') {
        setTimeout(() => {
            const trainingForm = document.getElementById('training-form');
            if (trainingForm && trainingForm.style.display === 'none') {
                openFormTraining(new Event('pageload'));
            }
        }, 100);
    }
});

// ==============================================
// VALIDATION FUNCTIONS
// ==============================================

function validateTrainingForm() {
    const form = document.getElementById('training-request-form');
    let isValid = true;
    
    // Clear previous error messages
    clearTrainingErrors();
    
    // Validate required fields
    const requiredFields = [
        { id: 'training_first_name', name: 'First Name' },
        { id: 'training_last_name', name: 'Last Name' },
        { id: 'training_mobile_number', name: 'Mobile Number' },
        { id: 'training_email', name: 'Email Address' },
        { id: 'training_type', name: 'Training Program' }
    ];
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (!element || !element.value.trim()) {
            showFieldError(field.id, `${field.name} is required`);
            isValid = false;
        }
    });
    
    // Validate mobile number format
    const mobileNumberElement = document.getElementById('training_mobile_number');
    if (mobileNumberElement) {
        const mobileNumber = mobileNumberElement.value.trim();
        if (mobileNumber && !validateMobileNumber(mobileNumber)) {
            showFieldError('training_mobile_number', 'Please enter a valid 11-digit mobile number');
            isValid = false;
        }
    }
    
    // Validate email format
    const emailElement = document.getElementById('training_email');
    if (emailElement) {
        const email = emailElement.value.trim();
        if (email && !validateEmail(email)) {
            showFieldError('training_email', 'Please enter a valid email address');
            isValid = false;
        }
    }
    
    // Validate file uploads
    const fileInput = document.getElementById('training_documents');
    if (fileInput && fileInput.files.length > 0) {
        if (!validateFiles(fileInput.files)) {
            isValid = false;
        }
    }
    
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
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    
    for (let file of files) {
        if (file.size > maxSize) {
            showTrainingMessage(`File "${file.name}" is too large. Maximum size is 5MB.`, 'error');
            return false;
        }
        
        if (!allowedTypes.includes(file.type)) {
            showTrainingMessage(`File "${file.name}" is not a supported format. Please upload PDF, JPG, or PNG files only.`, 'error');
            return false;
        }
    }
    
    return true;
}

// ==============================================
// ERROR HANDLING AND MESSAGING
// ==============================================

function showTrainingMessage(message, type) {
    const messagesContainer = document.getElementById('training-messages');
    const successMessage = document.getElementById('training-success-message');
    const errorMessage = document.getElementById('training-error-message');
    
    if (!messagesContainer || !successMessage || !errorMessage) {
        console.error('Training message elements not found');
        return;
    }
    
    // Hide all messages first
    messagesContainer.style.display = 'none';
    successMessage.style.display = 'none';
    errorMessage.style.display = 'none';
    
    // Show appropriate message
    if (type === 'success') {
        successMessage.textContent = message;
        successMessage.style.display = 'block';
    } else {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
    }
    
    messagesContainer.style.display = 'block';
    
    // Scroll to message
    messagesContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Auto-hide success messages after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            messagesContainer.style.display = 'none';
        }, 5000);
    }
}

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    const formGroup = field.closest('.form-group');
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
    const errorTexts = document.querySelectorAll('#training-form .error-text');
    errorTexts.forEach(error => error.remove());
    
    // Clear error styling
    const errorFields = document.querySelectorAll('#training-form .error');
    errorFields.forEach(field => field.classList.remove('error'));
    
    // Hide message containers
    const messagesContainer = document.getElementById('training-messages');
    if (messagesContainer) {
        messagesContainer.style.display = 'none';
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

/**
 * Activate application tab
 */
function activateApplicationTab(formId) {
    const formSection = document.getElementById(formId);
    if (!formSection) return;

    const firstTabBtn = formSection.querySelector('.training-tab-btn');
    const firstTabContent = formSection.querySelector('.training-tab-content');

    if (firstTabBtn && firstTabContent) {
        // Reset all tabs in this form
        formSection.querySelectorAll('.training-tab-btn').forEach(btn => btn.classList.remove('active'));
        formSection.querySelectorAll('.training-tab-content').forEach(tab => tab.style.display = 'none');

        // Activate first tab
        firstTabBtn.classList.add('active');
        firstTabContent.style.display = 'block';
    }
}

// ==============================================
// UTILITY FUNCTIONS
// ==============================================

// Format mobile number as user types
document.addEventListener('DOMContentLoaded', function() {
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
});

// File input change handler
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('training_documents');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                // Clear previous errors
                clearTrainingErrors();
                
                // Validate files
                validateFiles(files);
            }
        });
    }
});