// Enhanced My Applications Modal - User Application Management
// Fetch and display user's applications with professional styling
// Updated: Added Resubmit button for rejected applications

/**
 * Load user applications in modal
 */
function loadUserApplicationsInModal() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

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
 * Updated: Added Resubmit button for rejected applications
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
        const appTypeLabel = formatApplicationType(app.type);
        const isRejected = app.status.toLowerCase() === 'rejected';

        return `
            <div class="app-card ${statusClass}">
                <div class="card-header">
                    <div class="app-type">
                        <span class="app-type-badge">${appTypeLabel}</span>
                        <h3 class="app-title">${app.type}</h3>
                    </div>
                    <span class="status-badge ${statusClass}">
                        <span class="status-dot"></span>
                        ${statusLabel}
                    </span>
                </div>

                <div class="card-body">
                    <div class="ref-number">
                        <span class="ref-label">Reference Number:</span>
                        <span class="ref-value">${app.application_number || app.reference_number || 'N/A'}</span>
                    </div>

                    ${app.full_name || app.livelihood || app.barangay ? `
                        <div class="info-grid">
                            ${app.full_name ? `
                                <div class="info-item">
                                    <span class="info-label">Full Name</span>
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
                                    <span class="info-label">Livelihood/Sector</span>
                                    <span class="info-value">${app.livelihood}</span>
                                </div>
                            ` : ''}
                        </div>
                    ` : ''}

                    ${app.description ? `
                        <div class="description">
                            ${app.description}
                        </div>
                    ` : ''}

                    ${app.remarks ? `
                        <div class="remarks">
                            <div class="remarks-label">Rejection Reason</div>
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
                        <button class="action-btn" onclick="alert('View details for: ${app.application_number || app.reference_number}')">
                            View Details
                        </button>
                    `}
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Handle Resubmit button click - Navigate to Services section
 */
function handleResubmit(applicationType) {
    // Close the modal
    closeApplicationsModal();

    // Map application types to their form opening functions
    const typeMap = {
        'RSBSA Registration': () => openRSBSAForm(event),
        'Seedlings Request': () => openFormSeedlings(event),
        'FishR Registration': () => openFormFishR(event),
        'BoatR Registration': () => openFormBoatR(event),
        'Training Request': () => openFormTraining(event)
    };

    // Get the appropriate function or default to services
    const formFunction = typeMap[applicationType];

    if (formFunction) {
        // Scroll to services first
        const servicesSection = document.getElementById('services');
        if (servicesSection) {
            servicesSection.scrollIntoView({ behavior: 'smooth' });
            
            // Open the form after a short delay
            setTimeout(() => {
                formFunction();
            }, 500);
        } else {
            // If services section not found, just open the form
            formFunction();
        }
    } else {
        // Fallback: Just scroll to services
        const servicesSection = document.getElementById('services');
        if (servicesSection) {
            servicesSection.scrollIntoView({ behavior: 'smooth' });
        }
        showNotification('info', 'Please select your desired service from the available options.');
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
        'pending': 'Pending Review',
        'under_review': 'Under Review',
        'processing': 'Processing',
        'approved': 'Approved',
        'rejected': 'Rejected'
    };
    return statusMap[status.toLowerCase()] || status;
}

/**
 * Format application type text
 */
function formatApplicationType(type) {
    const typeMap = {
        'RSBSA Registration': 'RSBSA',
        'Seedlings Request': 'Seedlings',
        'FishR Registration': 'FishR',
        'BoatR Registration': 'BoatR',
        'Training Request': 'Training'
    };
    return typeMap[type] || type;
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
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    
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

    // Update button states
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    // Filter cards
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

// Export functions globally
window.loadUserApplicationsInModal = loadUserApplicationsInModal;
window.renderApplicationsInModal = renderApplicationsInModal;
window.renderEmptyApplications = renderEmptyApplications;
window.filterApplicationsByStatus = filterApplicationsByStatus;
window.handleResubmit = handleResubmit;

console.log('Enhanced My Applications Modal with Resubmit Feature Loaded Successfully');