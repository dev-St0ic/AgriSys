// ==============================================
// MAIN LANDING PAGE JAVASCRIPT
// Core navigation and form management
// ==============================================

// ==============================================
// AUTHENTICATION CHECK FUNCTIONS
// ==============================================

/**
 * Check if user is authenticated and verified
 */
function isUserAuthenticatedAndVerified() {
    // Check if user data exists in window (set by Blade template)
    if (typeof window.userData === 'undefined' || !window.userData) {
        return false;
    }

    // Check if user is logged in and has approved status
    return window.userData.status === 'approved';
}

/**
 * Check if user is logged in (regardless of verification status)
 */
function isUserLoggedIn() {
    return typeof window.userData !== 'undefined' && window.userData !== null;
}

/**
 * Show authentication required modal without alert notifications
 */
function showAuthRequired(serviceName) {
    if (!isUserLoggedIn()) {
        // User not logged in - show login modal directly
        openAuthModal('login');
        return false;
    } else if (!isUserAuthenticatedAndVerified()) {
        // User logged in but not verified - show verification alert modal
        showVerificationRequiredModal(serviceName);
        return false;
    }
    return true;
}

/**
 * Show verification required modal for unverified accounts
 */
function showVerificationRequiredModal(serviceName) {
    const userData = window.userData;
    let modalContent = '';

    // Determine user's current status and create appropriate message
    switch (userData.status) {
        case 'unverified':
            modalContent = `
                <div class="verification-alert-modal" id="verification-alert-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><i class="fas fa-shield-alt"></i> Account Verification Required</h3>
                            <span class="auth-modal-close" onclick="closeVerificationAlert()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h4>Complete Your Profile Verification</h4>
                            <p>To access <strong>${serviceName}</strong> services, you need to verify your account by completing your profile information and uploading required documents.</p>
                            <div class="verification-steps">
                                <div class="step">
                                    <span class="step-number">1</span>
                                    <span class="step-text">Complete personal information</span>
                                </div>
                                <div class="step">
                                    <span class="step-number">2</span>
                                    <span class="step-text">Upload valid ID documents</span>
                                </div>
                                <div class="step">
                                    <span class="step-number">3</span>
                                    <span class="step-text">Wait for admin approval</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-verify-now" onclick="startVerificationProcess()">
                                <i class="fas fa-check-circle"></i> Verify Account Now
                            </button>
                            <button type="button" class="btn-cancel" onclick="closeVerificationAlert()">
                                Maybe Later
                            </button>
                        </div>
                    </div>
                </div>
            `;
            break;

        case 'pending':
            modalContent = `
                <div class="verification-alert-modal" id="verification-alert-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><i class="fas fa-clock"></i> Verification Pending</h3>
                            <span class="auth-modal-close" onclick="closeVerificationAlert()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <div class="alert-icon pending">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <h4>Your Account is Under Review</h4>
                            <p>Your verification documents have been submitted and are currently being reviewed by our admin team.</p>
                            <p>To access <strong>${serviceName}</strong> services, please wait for verification approval.</p>
                            <div class="status-info">
                                <p><strong>Current Status:</strong> <span class="status-badge pending">Pending Review</span></p>
                                <p><em>You'll receive a notification once your account is approved.</em></p>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-primary" onclick="closeVerificationAlert()">
                                <i class="fas fa-check"></i> Understood
                            </button>
                        </div>
                    </div>
                </div>
            `;
            break;

        case 'rejected':
            modalContent = `
                <div class="verification-alert-modal" id="verification-alert-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><i class="fas fa-times-circle"></i> Verification Required</h3>
                            <span class="auth-modal-close" onclick="closeVerificationAlert()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <div class="alert-icon rejected">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <h4>Account Verification Needed</h4>
                            <p>Your previous verification was not approved. To access <strong>${serviceName}</strong> services, please resubmit your verification documents.</p>
                            <div class="status-info">
                                <p><strong>Status:</strong> <span class="status-badge rejected">Verification Required</span></p>
                                <p><em>Please update your documents and try again.</em></p>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-verify-now" onclick="startVerificationProcess()">
                                <i class="fas fa-upload"></i> Resubmit Verification
                            </button>
                            <button type="button" class="btn-cancel" onclick="closeVerificationAlert()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            `;
            break;

        default:
            modalContent = `
                <div class="verification-alert-modal" id="verification-alert-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><i class="fas fa-shield-alt"></i> Account Verification Required</h3>
                            <span class="auth-modal-close" onclick="closeVerificationAlert()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <div class="alert-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h4>Verify Your Account</h4>
                            <p>To access <strong>${serviceName}</strong> services, your account must be verified first.</p>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-verify-now" onclick="startVerificationProcess()">
                                <i class="fas fa-check-circle"></i> Start Verification
                            </button>
                            <button type="button" class="btn-cancel" onclick="closeVerificationAlert()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            `;
    }

    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalContent);

    // Show modal with animation
    setTimeout(() => {
        const modal = document.getElementById('verification-alert-modal');
        if (modal) {
            modal.classList.add('show');
        }
    }, 10);
}

/**
 * Close verification alert modal
 */
function closeVerificationAlert() {
    const modal = document.getElementById('verification-alert-modal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

/**
 * Start verification process - opens the verification modal
 */
function startVerificationProcess() {
    closeVerificationAlert();

    // Small delay to allow the alert modal to close smoothly
    setTimeout(() => {
        // Open the existing verification modal from auth.js
        if (typeof showVerificationModal === 'function') {
            showVerificationModal();
        } else {
            // Fallback - show profile modal if verification modal not available
            if (typeof showProfileModal === 'function') {
                showProfileModal();
            } else {
                // Last fallback - try to open verification modal by ID
                const verificationModal = document.getElementById('verification-modal');
                if (verificationModal) {
                    verificationModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    console.error('Verification modal not found');
                }
            }
        }
    }, 300); // Match the modal close animation duration
}

/**
 * Wrapper function to check authentication before opening any service form
 */
function checkAuthBeforeService(serviceName, originalFunction, event) {
    if (!showAuthRequired(serviceName)) {
        event.preventDefault();
        return false;
    }
    // If authenticated and verified, proceed with original function
    return originalFunction(event);
}

// ==============================================
// UTILITY FUNCTIONS - Show/Hide Helpers
// ==============================================

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

function hideAllForms() {
    const formIds = [
        'rsbsa-form',
        'seedlings-choice', 'seedlings-form',
        'fishr-form', 'boatr-form', 'training-form'
    ];

    formIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.style.display = 'none';
    });
}

// ==============================================
// TAB MANAGEMENT
// ==============================================

/**
 * Auto-selects first tab in a form section
 */
function activateApplicationTab(formId) {
    const formSection = document.getElementById(formId);
    if (!formSection) return;

    const firstTabBtn = formSection.querySelector('.tab-btn');
    const firstTabContent = formSection.querySelector('.tab-content');

    if (firstTabBtn && firstTabContent) {
        // Reset all tabs
        formSection.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        formSection.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');

        // Activate first tab
        firstTabBtn.classList.add('active');
        firstTabContent.style.display = 'block';
    }
}

/**
 * Switches between tabs in a form
 */
function showTab(tabId, event) {
    const formSection = event.target.closest('.application-section');
    if (!formSection) return;

    const contents = formSection.querySelectorAll('.tab-content');
    const tabs = formSection.querySelectorAll('.tab-btn');

    // Hide all tabs and remove active class
    contents.forEach(content => content.style.display = 'none');
    tabs.forEach(tab => tab.classList.remove('active'));

    // Show selected tab
    const tabContent = formSection.querySelector(`#${tabId}`);
    if (tabContent) tabContent.style.display = 'block';
    event.target.classList.add('active');
}

// ==============================================
// GENERIC FORM FUNCTIONS
// ==============================================

/**
 * Generic form opener
 */
function openForm(event, formId, path) {
    event.preventDefault();
    hideAllMainSections();
    hideAllForms();

    const formElement = document.getElementById(formId);
    if (formElement) {
        formElement.style.display = 'block';
        activateApplicationTab(formId);
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', path);
}

/**
 * Generic form closer
 */
function closeForm(formId) {
    const formElement = document.getElementById(formId);
    if (formElement) formElement.style.display = 'none';

    showAllMainSections();
    history.pushState(null, '', '/services');
}

// // Event filtering and expand/collapse functionality for event cards
// document.addEventListener('DOMContentLoaded', function() {
//     const filterButtons = document.querySelectorAll('.filter-btn');
//     const eventCards = document.querySelectorAll('.event-card');

//     // Filter functionality
//     filterButtons.forEach(button => {
//         button.addEventListener('click', function() {
//             filterButtons.forEach(btn => btn.classList.remove('active'));
//             this.classList.add('active');

//             const filterValue = this.getAttribute('data-filter');

//             eventCards.forEach(card => {
//                 if (filterValue === 'all') {
//                     card.style.display = 'block';
//                     setTimeout(() => {
//                         card.style.opacity = '1';
//                         card.style.transform = 'scale(1)';
//                     }, 10);
//                 } else {
//                     if (card.getAttribute('data-category') === filterValue) {
//                         card.style.display = 'block';
//                         setTimeout(() => {
//                             card.style.opacity = '1';
//                             card.style.transform = 'scale(1)';
//                         }, 10);
//                     } else {
//                         card.style.opacity = '0';
//                         card.style.transform = 'scale(0.8)';
//                         setTimeout(() => {
//                             card.style.display = 'none';
//                         }, 300);
//                     }
//                 }
//             });
//         });
//     });

//     // Expand/Collapse functionality
//     eventCards.forEach(card => {
//         const expandBtn = card.querySelector('.expand-btn');
//         const expandableDetails = card.querySelector('.expandable-details');

//         if (expandBtn && expandableDetails) {
//             expandBtn.addEventListener('click', function() {
//                 const isExpanded = card.classList.contains('expanded');

//                 if (isExpanded) {
//                     // Collapse
//                     card.classList.remove('expanded');
//                     expandableDetails.style.maxHeight = '0';
//                     expandBtn.innerHTML = '<span>View More Details</span> <span class="arrow">▼</span>';
//                 } else {
//                     // Expand
//                     card.classList.add('expanded');
//                     expandableDetails.style.maxHeight = expandableDetails.scrollHeight + 'px';
//                     expandBtn.innerHTML = '<span>Hide Details</span> <span class="arrow">▲</span>';
//                 }
//             });
//         }
//     });
// });
// ==============================================
// RSBSA FORM FUNCTIONS - Moved to rsbsa.js
// ==============================================

// Note: RSBSA functions have been moved to rsbsa.js module
// - openFormRSBSA(event)
// - openNewRSBSA()
// - openOldRSBSA()
// - closeFormRSBSA()
// - backToRSBSAChoice()
// Include rsbsa.js to use these functions

// ==============================================
// FISH REGISTRATION FUNCTIONS - Moved to fishr.js
// ==============================================

// Note: Fish Registration functions have been moved to fishr.js module
// - openFormFishR(event)
// - closeFormFishR()
// - toggleOtherLivelihood(select)
// Include fishr.js to use these functions

// ==============================================
// BOAT REGISTRATION FUNCTIONS - Moved to boatr.js
// ==============================================

// Note: Boat Registration functions have been moved to boatr.js module
// - openFormBoatR(event)
// - closeFormBoatR()
// - handleBoatTypeChange(select)
// Include boatr.js to use these functions

// ==============================================
// NAVIGATION FUNCTIONS
// ==============================================

/**
 * Home button navigation
 */
function goHome(event) {
    event.preventDefault();
    hideAllForms();
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/');
}

// ==============================================
// BROWSER NAVIGATION HANDLING
// ==============================================

/**
 * Handles browser back/forward navigation
 */
function handlePopState() {
    const path = window.location.pathname;
    hideAllForms();
    showAllMainSections();

    const routeMap = {
        '/services/rsbsa': () => {
            hideAllMainSections();
            const form = document.getElementById('rsbsa-form');
            if (form) {
                form.style.display = 'block';
                activateApplicationTab('rsbsa-form');
            }
        },
        '/services/seedlings': () => {
            hideAllMainSections();
            const choice = document.getElementById('seedlings-choice');
            if (choice) choice.style.display = 'block';
        },
        '/services/fishr': () => {
            hideAllMainSections();
            const form = document.getElementById('fishr-form');
            if (form) {
                form.style.display = 'block';
                activateApplicationTab('fishr-form');
            }
        },
        '/services/boatr': () => {
            hideAllMainSections();
            const form = document.getElementById('boatr-form');
            if (form) {
                form.style.display = 'block';
                activateApplicationTab('boatr-form');
            }
        },
        '/services/training': () => {
            hideAllMainSections();
            const form = document.getElementById('training-form');
            if (form) {
                form.style.display = 'block';
                activateApplicationTab('training-form');
                // Trigger auto-fill button addition
                if (typeof window.addAutoFillButtonToTraining === 'function') {
                    setTimeout(window.addAutoFillButtonToTraining, 100);
                }
            }
        }
    };

    const routeHandler = routeMap[path];
    if (routeHandler) {
        routeHandler();
    }
}

// ==============================================
// APPLICATIONS MODAL FUNCTIONS
// ==============================================


/**
 * Open applications modal
 */
function openApplicationsModal() {
    const modal = document.getElementById('applications-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        loadUserApplicationsInModal();
    }
}

/**
 * Close applications modal
 */
function closeApplicationsModal() {
    const modal = document.getElementById('applications-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    // Reset filter buttons to 'All Applications'
    if (typeof resetApplicationFilters === 'function') {
        resetApplicationFilters();
    }
}

/**
 * Show my applications modal (called from profile dropdown)
 */
function showMyApplicationsModal() {
    openApplicationsModal();
}

// ==============================================
// HELP SECTION FUNCTIONALITY
// ==============================================

/**
 * Shows the contact modal
 */
function showContactModal() {
    const modal = document.getElementById('contact-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
}

/**
 * Hides the contact modal
 */
function hideContactModal() {
    const modal = document.getElementById('contact-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scrolling
    }
}

/**
 * Opens device's default maps app with office directions
 */
function openOfficeDirections() {
    // San Pedro City Hall coordinates (replace with actual coordinates)
    const latitude = 14.3553;
    const longitude = 121.0449;
    const officeName = "San Pedro City Agriculture Office";
    const address = "San Pedro City Hall, Laguna, Philippines";

    // Different map URLs for different devices
    const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`;
    const appleMapsUrl = `maps://maps.google.com/maps?daddr=${latitude},${longitude}`;
    const generalMapsUrl = `https://maps.google.com/maps?daddr=${encodeURIComponent(address)}`;

    // Detect device type
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);

    try {
        if (isMobile && isIOS) {
            window.location.href = appleMapsUrl;
        } else if (isMobile) {
            window.location.href = googleMapsUrl;
        } else {
            window.open(generalMapsUrl, '_blank');
        }
    } catch (error) {
        window.open(generalMapsUrl, '_blank');
    }
}

/**
 * Handles contact form submission
 */
function handleContactSubmit(event) {
    event.preventDefault();

    // Get form data (you can process this as needed)
    const formData = new FormData(event.target);

    // Show success message
    agrisysModal.success('Thank you for your message! We will get back to you within 24 hours.', { title: 'Message Sent!' });

    // Reset form and close modal
    event.target.reset();
    hideContactModal();

    // Here you would normally send the data to your server
    // Example: sendToServer(formData);
}

/**
 * Initialize help section functionality
 */
function initializeHelpButtons() {
    // Get help buttons
    const helpButtons = document.querySelectorAll('.btn-help');

    // Add event listeners
    if (helpButtons[0]) {
        helpButtons[0].addEventListener('click', showContactModal);
    }

    if (helpButtons[1]) {
        helpButtons[1].addEventListener('click', openOfficeDirections);
    }

    // Modal close functionality
    const modal = document.getElementById('contact-modal');
    const closeBtn = document.querySelector('.contact-modal-close');
    const form = document.getElementById('quick-contact-form');

    if (closeBtn) {
        closeBtn.addEventListener('click', hideContactModal);
    }

    if (modal) {
        // Close when clicking outside modal
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                hideContactModal();
            }
        });
    }

    if (form) {
        form.addEventListener('submit', handleContactSubmit);
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideContactModal();
        }
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeHelpButtons();
});

/**
 * Handles page load routing
 */
function handlePageLoad() {
    const path = window.location.pathname;
    hideAllForms();
    showAllMainSections();

    // Handle initial page load routing
    if (path === '/services/rsbsa') {
        hideAllMainSections();
        const form = document.getElementById('rsbsa-form');
        if (form) {
            form.style.display = 'block';
            activateApplicationTab('rsbsa-form');
        }
    } else if (path === '/services/seedlings') {
        hideAllMainSections();
        const choice = document.getElementById('seedlings-choice');
        if (choice) choice.style.display = 'block';
    } else if (path === '/services/fishr') {
        hideAllMainSections();
        const form = document.getElementById('fishr-form');
        if (form) {
            form.style.display = 'block';
            activateApplicationTab('fishr-form');
        }
    } else if (path === '/services/boatr') {
        hideAllMainSections();
        const form = document.getElementById('boatr-form');
        if (form) {
            form.style.display = 'block';
            activateApplicationTab('boatr-form');
        }
    } else if (path === '/services/training') {
        hideAllMainSections();
        const form = document.getElementById('training-form');
        if (form) {
            form.style.display = 'block';
            activateApplicationTab('training-form');
            // Trigger auto-fill button addition
            if (typeof window.addAutoFillButtonToTraining === 'function') {
                setTimeout(window.addAutoFillButtonToTraining, 100);
            }
        }
    }
}

// ==============================================
// INITIALIZATION
// ==============================================

/**
 * Initialize core landing page features
 */
function initializeLandingPage() {
    console.log('Landing page core features initialized');

    // Add any core initialization logic here
    // Module-specific initialization should be handled in their respective modules
}

// ==============================================
// EVENT LISTENERS
// ==============================================

// Browser navigation events
window.addEventListener('popstate', handlePopState);
window.addEventListener('DOMContentLoaded', () => {
    handlePageLoad();
    initializeLandingPage();

    // Note: Module-specific initialization functions should be called
    // from their respective modules if needed:
    // - initializeRSBSAModule() - called from rsbsa.js
    // - initializeSeedlingsModule() - called from seedlings.js
    // - initializeFishRModule() - called from fishr.js
    // - initializeBoatRModule() - called from boatr.js
});


// ==============================================
// GLOBAL FUNCTION EXPORTS
// ==============================================

// Make verification functions available globally
window.showVerificationRequiredModal = showVerificationRequiredModal;
window.closeVerificationAlert = closeVerificationAlert;
window.startVerificationProcess = startVerificationProcess;

console.log('Landing page JavaScript loaded successfully');

// Auto-hide success/error messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
