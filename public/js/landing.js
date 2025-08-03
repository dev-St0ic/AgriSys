// ==============================================
// MAIN LANDING PAGE JAVASCRIPT
// Core navigation and form management
// ==============================================

// ==============================================
// UTILITY FUNCTIONS - Show/Hide Helpers
// ==============================================

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
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}

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
            const choice = document.getElementById('rsbsa-choice');
            if (choice) choice.style.display = 'block';
        },
        '/services/rsbsa/new': () => {
            hideAllMainSections();
            const form = document.getElementById('new-rsbsa');
            if (form) {
                form.style.display = 'block';
                activateApplicationTab('new-rsbsa');
            }
        },
        '/services/rsbsa/old': () => {
            hideAllMainSections();
            const form = document.getElementById('old-rsbsa');
            if (form) {
                form.style.display = 'block';
                activateApplicationTab('old-rsbsa');
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
            }
        }
    };

    const routeHandler = routeMap[path];
    if (routeHandler) {
        routeHandler();
    }
}

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
        const choice = document.getElementById('rsbsa-choice');
        if (choice) choice.style.display = 'block';
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