// Enhanced My Applications Modal - User Application Management (Simplified)
// Fetch and display user's applications with professional styling
// Updated: Removed category badge, reduced redundancy, added border colors

/**
 * Load user applications in modal
 */
function loadUserApplicationsInModal() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    // Reset filter buttons first
    resetApplicationFilters();

    // Check if user is logged in
    if (!window.userData) {
        grid.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <path d="M20 8v6M23 11h-6"/>
                    </svg>
                </div>
                <h2 class="empty-title">Please Log In</h2>
                <p class="empty-text">You need to be logged in to view your applications.</p>
                <button class="primary-btn" onclick="closeApplicationsModal(); openAuthModal('login');">
                    Log In to Your Account
                </button>
            </div>
        `;
        return;
    }

    // Show loading state
    grid.innerHTML = `
        <div class="loading">
            <div class="loader"></div>
            <p>Loading your applications...</p>
        </div>
    `;

    // Fetch real applications from backend
    fetch('/api/user/applications/all', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.applications && data.applications.length > 0) {
            renderApplicationsInModal(data.applications);
            updateApplicationStatistics(data.applications);
        } else {
            renderEmptyApplications();
        }
    })
    .catch(error => {
        console.error('Error loading applications:', error);
        renderEmptyApplications();
    });
}

/**
 * Update statistics based on applications
 */
function updateApplicationStatistics(applications) {
    const statsContainer = document.getElementById('applications-stats');
    if (!statsContainer) return;

    const total = applications.length;
    const pending = applications.filter(app => ['pending', 'under_review', 'processing'].includes(app.status.toLowerCase())).length;
    const approved = applications.filter(app => app.status.toLowerCase() === 'approved').length;
    const rejected = applications.filter(app => app.status.toLowerCase() === 'rejected').length;

    statsContainer.innerHTML = `
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-number">${total}</div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${pending}</div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${approved}</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${rejected}</div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>
    `;
}

/**
 * Render applications in modal
 * Updated: Removed category badge, reduced redundancy
 */
function renderApplicationsInModal(applications) {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    if (applications.length === 0) {
        renderEmptyApplications();
        return;
    }

    grid.innerHTML = applications.map(app => {
        const statusClass = getStatusClass(app.status);
        const statusLabel = formatStatus(app.status);
        const isRejected = app.status.toLowerCase() === 'rejected';

        return `
            <div class="app-card ${statusClass}">
                <div class="card-header">
                    <div class="app-type">
                        <h3 class="app-title">${app.type}</h3>
                    </div>
                    <span class="status-badge ${statusClass}">
                        <span class="status-dot"></span>
                        ${statusLabel}
                    </span>
                </div>

                <div class="card-body">
                    <div class="ref-number">
                        <span class="ref-label">Reference:</span>
                        <span class="ref-value">${app.application_number || app.reference_number || 'N/A'}</span>
                    </div>

                    ${app.full_name || app.barangay || app.livelihood ? `
                        <div class="info-grid">
                            ${app.full_name ? `
                                <div class="info-item">
                                    <span class="info-label">Name</span>
                                    <span class="info-value">${app.full_name}</span>
                                </div>
                            ` : ''}
                            ${app.barangay ? `
                                <div class="info-item">
                                    <span class="info-label">Barangay</span>
                                    <span class="info-value">${app.barangay}</span>
                                </div>
                            ` : ''}
                            ${app.livelihood ? `
                                <div class="info-item">
                                    <span class="info-label">Sector</span>
                                    <span class="info-value">${app.livelihood}</span>
                                </div>
                            ` : ''}
                        </div>
                    ` : ''}


                    ${app.remarks ? `
                        <div class="remarks">
                            <div class="remarks-label">Remarks</div>
                            <div class="remarks-text">${app.remarks}</div>
                        </div>
                    ` : ''}
                </div>

                <div class="card-footer">
                    <div class="submitted-date">
                        <span class="date-label">Submitted</span>
                        <span class="date-value">${formatApplicationDate(app.submitted_at || app.date || app.created_at)}</span>
                    </div>
                    ${isRejected ? `
                        <button class="action-btn resubmit-btn" onclick="handleResubmit('${app.type}')">
                            Resubmit
                        </button>
                    ` : `
                    `}
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Handle Resubmit button click - Navigate to Services section and open the form
 */
function handleResubmit(applicationType) {
    closeApplicationsModal();

    const typeMap = {
        'RSBSA Registration': {
            formId: 'rsbsa-form',
            openFunction: (e) => openRSBSAForm(e),
            path: '/services/rsbsa'
        },
        'Seedlings Request': {
            formId: 'seedlings-form',
            openFunction: (e) => openFormSeedlings(e),
            path: '/services/seedlings'
        },
        'FishR Registration': {
            formId: 'fishr-form',
            openFunction: (e) => openFormFishR(e),
            path: '/services/fishr'
        },
        'BoatR Registration': {
            formId: 'boatr-form',
            openFunction: (e) => openFormBoatR(e),
            path: '/services/boatr'
        },
        'Training Request': {
            formId: 'training-form',
            openFunction: (e) => openFormTraining(e),
            path: '/services/training'
        }
    };

    const formConfig = typeMap[applicationType];

    if (formConfig) {
        // Scroll to top immediately
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Create synthetic event
        const syntheticEvent = new Event('click');
        syntheticEvent.preventDefault = () => {};
        
        // Call the form open function
        formConfig.openFunction(syntheticEvent);
        
        // Scroll to the form after a small delay to ensure it's displayed
        setTimeout(() => {
            const formElement = document.getElementById(formConfig.formId);
            if (formElement) {
                formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 600);
    } else {
        // Scroll to top
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        const servicesSection = document.getElementById('services');
        if (servicesSection) {
            servicesSection.scrollIntoView({ behavior: 'smooth' });
        }
        agrisysModal.info('Please select your desired service from the available options.', { title: 'Service Selection' });
    }
}

/**
 * Render empty state
 */
function renderEmptyApplications() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    grid.innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="13" x2="8" y2="13"/>
                    <line x1="12" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <h2 class="empty-title">No Applications Yet</h2>
            <p class="empty-text">You haven't submitted any applications. Start by exploring our available services designed to support your agricultural needs.</p>
            <button class="primary-btn" onclick="closeApplicationsModal(); document.getElementById('services').scrollIntoView({ behavior: 'smooth' });">
                Browse Available Services
            </button>
        </div>
    `;
}

/**
 * Get status class for styling
 */
function getStatusClass(status) {
    const statusMap = {
        'pending': 'status-pending',
        'under_review': 'status-under-review',
        'processing': 'status-processing',
        'approved': 'status-approved',
        'rejected': 'status-rejected'
    };
    return statusMap[status.toLowerCase()] || 'status-default';
}

/**
 * Format status text
 */
function formatStatus(status) {
    const statusMap = {
        'pending': 'Pending',
        'under_review': 'Under Review',
        'processing': 'Processing',
        'approved': 'Approved',
        'rejected': 'Rejected'
    };
    return statusMap[status.toLowerCase()] || status;
}

/**
 * Format application date
 */
function formatApplicationDate(dateString) {
    if (!dateString) return 'Date unknown';

    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays}d ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)}w ago`;
    
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Filter applications by status
 */
function filterApplicationsByStatus(status) {
    const cards = document.querySelectorAll('.app-card');
    const buttons = document.querySelectorAll('.filter-btn');

    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    if (status === 'all') {
        cards.forEach(card => card.style.display = '');
    } else {
        cards.forEach(card => {
            const cardStatus = card.className.match(/status-(\w+)/);
            if (cardStatus) {
                const currentStatus = cardStatus[1];
                card.style.display = currentStatus === status ? '' : 'none';
            }
        });
    }
}

/**
 * Reset filter buttons to 'All Applications' on modal close/open
 */
function resetApplicationFilters() {
    // Get all filter buttons
    const buttons = document.querySelectorAll('.filter-btn');
    
    console.log('Resetting filters - found', buttons.length, 'buttons');
    
    if (buttons.length === 0) {
        console.warn('No filter buttons found');
        return;
    }
    
    // Remove active class from ALL buttons
    buttons.forEach((btn, index) => {
        if (btn.classList.contains('active')) {
            console.log('Removing active from button', index, '(' + btn.textContent.trim() + ')');
            btn.classList.remove('active');
        }
    });
    
    // Find and activate the 'All Applications' button
    const allButton = Array.from(buttons).find(btn => btn.textContent.trim() === 'All Applications');
    
    if (allButton) {
        allButton.classList.add('active');
        console.log('All Applications button set to active');
    } else {
        console.warn('All Applications button not found, activating first button');
        buttons[0].classList.add('active');
    }
    
    // Show all application cards
    const cards = document.querySelectorAll('.app-card');
    cards.forEach(card => {
        card.style.display = ''; // Reset to default
    });
    
    console.log('Filter reset complete - showing', cards.length, 'cards');
}

// Export functions globally
window.loadUserApplicationsInModal = loadUserApplicationsInModal;
window.renderApplicationsInModal = renderApplicationsInModal;
window.renderEmptyApplications = renderEmptyApplications;
window.filterApplicationsByStatus = filterApplicationsByStatus;
window.resetApplicationFilters = resetApplicationFilters;
window.handleResubmit = handleResubmit;

console.log('Enhanced My Applications Modal - Simplified Version Loaded');