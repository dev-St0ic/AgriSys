// Show/Hide Helpers
function showAllMainSections() {
    const home = document.getElementById('home');
    const announcement = document.querySelector('.announcement');
    const services = document.getElementById('services');
    const howItWorks = document.getElementById('how-it-works');
    const help = document.querySelector('.help-section');

    if (home) home.style.display = 'block';
    if (announcement) announcement.style.display = 'block';
    if (services) services.style.display = 'block';
    if (howItWorks) howItWorks.style.display = 'block';
    if (help) help.style.display = 'block';
}

function hideAllMainSections() {
    const home = document.getElementById('home');
    const announcement = document.querySelector('.announcement');
    const services = document.getElementById('services');
    const howItWorks = document.getElementById('how-it-works');
    const help = document.querySelector('.help-section');

    if (home) home.style.display = 'none';
    if (announcement) announcement.style.display = 'none';
    if (services) services.style.display = 'none';
    if (howItWorks) howItWorks.style.display = 'none';
    if (help) help.style.display = 'none';
}

function hideAllForms() {
    const forms = [
        'rsbsa-choice', 'new-rsbsa', 'old-rsbsa',
        'seedlings-choice', 'seedlings-form',
        'fishr-form', 'boatr-form'
    ];
    forms.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}

// Auto-select first tab (Application Form)
function activateApplicationTab(formId) {
    const formSection = document.getElementById(formId);
    if (!formSection) return;

    const firstTabBtn = formSection.querySelector('.tab-btn');
    const firstTabContent = formSection.querySelector('.tab-content');

    if (firstTabBtn && firstTabContent) {
        formSection.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        formSection.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');

        firstTabBtn.classList.add('active');
        firstTabContent.style.display = 'block';
    }
}

// Generic Form Open/Close
function openForm(event, formId, path) {
    event.preventDefault();
    hideAllMainSections();
    hideAllForms();

    const formEl = document.getElementById(formId);
    if (formEl) {
        formEl.style.display = 'block';
        activateApplicationTab(formId);
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', path);
}

function closeForm(formId) {
    const formEl = document.getElementById(formId);
    if (formEl) formEl.style.display = 'none';
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}

// Specific Form Openers
function openFormRSBSA(event) {
    event.preventDefault();
    hideAllMainSections();
    hideAllForms();
    const choice = document.getElementById('rsbsa-choice');
    if (choice) choice.style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/rsbsa');
}
function openNewRSBSA() {
    hideAllForms();
    const form = document.getElementById('new-rsbsa');
    if (form) form.style.display = 'block';
    activateApplicationTab('new-rsbsa'); // If you use tabs in this form
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/rsbsa/new');
}
function openOldRSBSA() {
    hideAllForms();
    const form = document.getElementById('old-rsbsa');
    if (form) form.style.display = 'block';
    activateApplicationTab('old-rsbsa'); // If you use tabs in this form
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/rsbsa/old');
}
function openFormSeedlings(event) {
    event.preventDefault();
    hideAllMainSections();
    hideAllForms();
    const choice = document.getElementById('seedlings-choice');
    if (choice) choice.style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/seedlings');
}
function openFormFishR(event) {
    openForm(event, 'fishr-form', '/services/fishr');
}
function openFormBoatR(event) {
    openForm(event, 'boatr-form', '/services/boatr');
}

// Specific Form Closers
function closeFormRSBSA() {
    hideAllForms();
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}
function closeFormSeedlings() {
    closeForm('seedlings-form');
}
function closeFormFishR() {
    closeForm('fishr-form');
}
function closeFormBoatR() {
    closeForm('boatr-form');
}

// Home button
function goHome(event) {
    event.preventDefault();
    hideAllForms();
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/');
}

// Tab switcher (shared)
function showTab(tabId, event) {
    const formSection = event.target.closest('.application-section');
    if (!formSection) return;

    const contents = formSection.querySelectorAll('.tab-content');
    const tabs = formSection.querySelectorAll('.tab-btn');

    contents.forEach(content => content.style.display = 'none');
    tabs.forEach(tab => tab.classList.remove('active'));

    const tabContent = formSection.querySelector(`#${tabId}`);
    if (tabContent) tabContent.style.display = 'block';
    event.target.classList.add('active');
}

//Livelihood for specific forms fish r
function toggleOtherLivelihood(select) {
    const otherField = document.getElementById('other-livelihood-field');
    if (select.value === 'others') {
        otherField.style.display = 'block';
    } else {
        otherField.style.display = 'none';
    }

    // Optional: Handle required logic for supporting documents
    const docsInput = document.getElementById('fishr-docs');
    if (select.value === 'capture') {
        docsInput.removeAttribute('required');
    } else {
        docsInput.setAttribute('required', 'required');
    }
}

//boat r for specific forms
// BoatR - Handle admin-only upload & future extensions
function handleBoatTypeChange(select) {
    const boatType = select.value;
    // Example placeholder – customize if needed
    console.log("Selected Boat Type:", boatType);
}

// Disable document upload field (admin-only) boatr
document.addEventListener('DOMContentLoaded', function () {
    const uploadInput = document.querySelector('#boatr-form-tab input[type="file"]');
    if (uploadInput) {
        uploadInput.disabled = true;
        uploadInput.title = "Upload disabled - for admin use only after on-site inspection";
    }
});

// Update browser navigation
window.addEventListener('popstate', () => {
    const path = window.location.pathname;
    hideAllForms();
    showAllMainSections();

    if (path === '/services/rsbsa') {
        hideAllMainSections();
        const choice = document.getElementById('rsbsa-choice');
        if (choice) choice.style.display = 'block';
    } else if (path === '/services/rsbsa/new') {
        hideAllMainSections();
        const form = document.getElementById('new-rsbsa');
        if (form) form.style.display = 'block';
        activateApplicationTab('new-rsbsa');
    } else if (path === '/services/rsbsa/old') {
        hideAllMainSections();
        const form = document.getElementById('old-rsbsa');
        if (form) form.style.display = 'block';
        activateApplicationTab('old-rsbsa');
    } else if (path === '/services/seedlings') {
        hideAllMainSections();
        const choice = document.getElementById('seedlings-choice');
        if (choice) choice.style.display = 'block';
    } else if (path === '/services/fishr') {
        hideAllMainSections();
        const el = document.getElementById('fishr-form');
        if (el) el.style.display = 'block';
        activateApplicationTab('fishr-form');
    } else if (path === '/services/boatr') {
        hideAllMainSections();
        const el = document.getElementById('boatr-form');
        if (el) el.style.display = 'block';
        activateApplicationTab('boatr-form');
    }
});

// On page load
window.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;
    hideAllForms();
    showAllMainSections();

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
        const el = document.getElementById('fishr-form');
        if (el) el.style.display = 'block';
        activateApplicationTab('fishr-form');
    } else if (path === '/services/boatr') {
        hideAllMainSections();
        const el = document.getElementById('boatr-form');
        if (el) el.style.display = 'block';
        activateApplicationTab('boatr-form');
    }
});

function openSeedlingsForm(type) {
    hideAllForms();
    const form = document.getElementById('seedlings-form');
    if (form) form.style.display = 'block';

    // Hide or show form sections based on type
    // Hide all first
    form.querySelectorAll('label, input[type="checkbox"][name="vegetables"], input[type="checkbox"][name="fruits"], input[type="radio"][name="fertilizer"]').forEach(el => {
        el.parentElement.style.display = '';
    });

    // Show only relevant fields
    if (type === 'vegetable') {
        // Hide fruit and fertilizer
        form.querySelectorAll('input[type="checkbox"][name="fruits"]').forEach(cb => cb.parentElement.style.display = 'none');
        form.querySelectorAll('input[type="radio"][name="fertilizer"]').forEach(cb => cb.parentElement.style.display = 'none');
    } else if (type === 'fruit') {
        // Hide vegetable and fertilizer
        form.querySelectorAll('input[type="checkbox"][name="vegetables"]').forEach(cb => cb.parentElement.style.display = 'none');
        form.querySelectorAll('input[type="radio"][name="fertilizer"]').forEach(cb => cb.parentElement.style.display = 'none');
    } else if (type === 'fertilizer') {
        // Hide vegetable and fruit
        form.querySelectorAll('input[type="checkbox"][name="vegetables"]').forEach(cb => cb.parentElement.style.display = 'none');
        form.querySelectorAll('input[type="checkbox"][name="fruits"]').forEach(cb => cb.parentElement.style.display = 'none');
    }
    // If 'all', show everything

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function backToSeedlingsChoice() {
    hideAllForms();
    const choice = document.getElementById('seedlings-choice');
    if (choice) choice.style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/seedlings');
}

function proceedToSeedlingsForm() {
    const form = document.getElementById('seedlings-choice-form');
    const vegetables = Array.from(form.querySelectorAll('input[name="vegetables"]:checked')).map(cb => cb.value);
    const fruits = Array.from(form.querySelectorAll('input[name="fruits"]:checked')).map(cb => cb.value);
    const fertilizer = form.querySelector('input[name="fertilizer"]:checked')?.value || '';

    // Require at least one selection
    if (vegetables.length === 0 && fruits.length === 0 && !fertilizer) {
        alert('Please select at least one: Vegetable Seedling, Fruit-bearing Seedling, or Organic Fertilizer.');
        return;
    }

    // Show summary alert
    let summary = 'You have chosen:\n';
    if (vegetables.length) summary += '- Vegetable Seedlings: ' + vegetables.join(', ') + '\n';
    if (fruits.length) summary += '- Fruit-bearing Seedlings: ' + fruits.join(', ') + '\n';
    if (fertilizer) summary += '- Organic Fertilizer: ' + fertilizer + '\n';
    alert(summary);

    // Save choices for later use if needed
    window._seedlingsChoices = { vegetables, fruits, fertilizer };

    // Show only the application details form
    hideAllForms();
    const appForm = document.getElementById('seedlings-form');
    if (appForm) appForm.style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function backToSeedlingsChoice() {
    hideAllForms();
    const choice = document.getElementById('seedlings-choice');
    if (choice) {
        choice.style.display = 'block';
        // Restore previous selections
        if (window._seedlingsChoices) {
            const form = document.getElementById('seedlings-choice-form');
            form.querySelectorAll('input[name="vegetables"]').forEach(cb => {
                cb.checked = window._seedlingsChoices.vegetables.includes(cb.value);
            });
            form.querySelectorAll('input[name="fruits"]').forEach(cb => {
                cb.checked = window._seedlingsChoices.fruits.includes(cb.value);
            });
            form.querySelectorAll('input[name="fertilizer"]').forEach(rb => {
                rb.checked = (rb.value === window._seedlingsChoices.fertilizer);
            });
        }
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/seedlings');
}

function submitSeedlingsRequest(event) {
    event.preventDefault();
    const form = document.getElementById('seedlings-request-form');
    const vegetables = Array.from(form.querySelectorAll('input[name="vegetables"]:checked')).map(cb => cb.value);
    const fruits = Array.from(form.querySelectorAll('input[name="fruits"]:checked')).map(cb => cb.value);
    const fertilizer = form.querySelector('input[name="fertilizer"]:checked')?.value || '';

    // Require at least one selection
    if (vegetables.length === 0 && fruits.length === 0 && !fertilizer) {
        alert('Please select at least one: Vegetable Seedling, Fruit-bearing Seedling, or Organic Fertilizer.');
        return false;
    }

    // Gather details
    const firstName = form.first_name.value;
    const middleName = form.middle_name.value;
    const lastName = form.last_name.value;
    const mobile = form.mobile.value;
    const barangay = form.barangay.value;
    const address = form.address.value;

    // Show summary
    let summary = 'You have chosen:\n';
    if (vegetables.length) summary += '- Vegetable Seedlings: ' + vegetables.join(', ') + '\n';
    if (fruits.length) summary += '- Fruit-bearing Seedlings: ' + fruits.join(', ') + '\n';
    if (fertilizer) summary += '- Organic Fertilizer: ' + fertilizer + '\n';
    summary += '\nApplicant Details:\n';
    summary += `Name: ${firstName} ${middleName} ${lastName}\n`;
    summary += `Mobile: ${mobile}\nBarangay: ${barangay}\nAddress: ${address}`;
    alert(summary);

    // You can now submit the form via AJAX or allow the form to submit
    // return true; // if you want to submit to the server
    return false; // prevent actual submission for demo
}
