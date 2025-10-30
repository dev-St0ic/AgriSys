// ===================================
// EVENT LOADING FOR LANDING PAGE
// ===================================

let allEvents = [];
let currentFilter = 'all';

/**
 * Load events from API
 */
async function loadEvents() {
    try {
        const response = await fetch('/api/events?category=all');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success && Array.isArray(data.events)) {
            allEvents = data.events;
            renderEvents(allEvents);
        } else {
            console.error('Invalid response format:', data);
        }
    } catch (error) {
        console.error('Error loading events:', error);
        showEventLoadError();
    }
}

/**
 * Render events to the grid
 */
function renderEvents(events) {
    const eventsGrid = document.querySelector('.events-grid');
    
    if (!eventsGrid) {
        console.warn('Events grid container not found');
        return;
    }

    if (events.length === 0) {
        eventsGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i class="fas fa-inbox fa-4x text-muted mb-3" style="display: block; margin-bottom: 20px;"></i>
                <h5 class="text-muted">No events available</h5>
                <p class="text-muted">Check back soon for upcoming events</p>
            </div>
        `;
        return;
    }

    eventsGrid.innerHTML = events.map(event => createEventCard(event)).join('');
    
    // Reattach event listeners
    attachEventListeners();
}

/**
 * Create event card HTML
 */
function createEventCard(event) {
    const imageUrl = event.image || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22250%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22400%22 height=%22250%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2220%22 fill=%22%23999%22%3ENo Image Available%3C/text%3E%3C/svg%3E';
    
    const details = event.details || {};
    
    return `
        <div class="event-card" data-category="${event.category}">
            <img src="${imageUrl}" alt="${event.title}" class="event-image" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22250%22%3E%3Crect fill=%22%23e0e0e0%22 width=%22400%22 height=%22250%22/%3E%3C/svg%3E'">
            <div class="event-content">
                <h3>${event.title}</h3>
                <p class="event-description">${event.description}</p>
                
                <div class="event-info-box">
                    <div class="date">📅 ${event.date || 'Date TBA'}</div>
                    <div class="location">📍 ${event.location || 'Location TBA'}</div>
                </div>
                
                <button class="expand-btn" onclick="toggleEventDetails(this)">
                    <span>View More Details</span>
                    <span class="arrow">▼</span>
                </button>
                
                <div class="expandable-details">
                    ${renderEventDetails(details)}
                </div>
            </div>
        </div>
    `;
}

/**
 * Render event details
 */
function renderEventDetails(details) {
    if (!details || Object.keys(details).length === 0) {
        return '<p class="text-muted text-center py-3">No additional details available</p>';
    }

    let html = '<ul class="details-list">';
    
    for (const [key, value] of Object.entries(details)) {
        if (key === 'icon') continue;
        
        const icon = getDetailIcon(key);
        const label = formatLabel(key);
        
        html += `
            <li>
                <span class="icon">${icon}</span>
                <div class="text">
                    <div class="label">${label}</div>
                    <div class="value">${value}</div>
                </div>
            </li>
        `;
    }
    
    html += '</ul>';
    return html;
}

/**
 * Get icon for detail type
 */
function getDetailIcon(key) {
    const icons = {
        'participants': '👥',
        'cost': '💰',
        'achievement': '🌳',
        'impact': '🎯',
        'freebies': '🎁',
        'registration': '📝',
        'support': '💚',
        'certification': '🏆',
        'services': '🛠️',
        'report': '📞',
        'facilities': '⚽',
        'upgrades': '🔧',
        'techniques': '🌱',
        'materials': '📦',
        'for': '👨‍🌾',
        'requirement': '✓',
        'benefits': '⭐'
    };
    
    return icons[key] || '📌';
}

/**
 * Format label from key
 */
function formatLabel(key) {
    return key
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

/**
 * Toggle event details
 */
function toggleEventDetails(btn) {
    const card = btn.closest('.event-card');
    const details = card.querySelector('.expandable-details');
    const isExpanded = card.classList.contains('expanded');
    
    if (isExpanded) {
        // Collapse
        card.classList.remove('expanded');
        details.style.maxHeight = '0';
        btn.innerHTML = '<span>View More Details</span> <span class="arrow">▼</span>';
    } else {
        // Expand
        card.classList.add('expanded');
        details.style.maxHeight = details.scrollHeight + 'px';
        btn.innerHTML = '<span>Hide Details</span> <span class="arrow">▲</span>';
    }
}

/**
 * Attach event listeners to filter buttons
 */
function attachEventListeners() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const eventCards = document.querySelectorAll('.event-card');
    
    filterButtons.forEach(button => {
        button.removeEventListener('click', handleFilterClick);
        button.addEventListener('click', handleFilterClick);
    });
    
    eventCards.forEach(card => {
        const expandBtn = card.querySelector('.expand-btn');
        if (expandBtn) {
            expandBtn.removeEventListener('click', handleExpandClick);
            expandBtn.addEventListener('click', handleExpandClick);
        }
    });
}

/**
 * Handle filter button click
 */
function handleFilterClick(event) {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const eventCards = document.querySelectorAll('.event-card');
    
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.target.closest('.filter-btn').classList.add('active');
    
    const filterValue = event.target.closest('.filter-btn').getAttribute('data-filter');
    currentFilter = filterValue;
    
    eventCards.forEach(card => {
        if (filterValue === 'all') {
            card.style.display = 'block';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 10);
        } else {
            if (card.getAttribute('data-category') === filterValue) {
                card.style.display = 'block';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                }, 10);
            } else {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    card.style.display = 'none';
                }, 300);
            }
        }
    });
}

/**
 * Handle expand button click
 */
function handleExpandClick(event) {
    event.preventDefault();
    toggleEventDetails(event.currentTarget);
}

/**
 * Show error message when events fail to load
 */
function showEventLoadError() {
    const eventsGrid = document.querySelector('.events-grid');
    
    if (eventsGrid) {
        eventsGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i class="fas fa-exclamation-circle fa-4x text-danger mb-3" style="display: block; margin-bottom: 20px;"></i>
                <h5 class="text-danger">Failed to load events</h5>
                <p class="text-muted">Please refresh the page to try again</p>
            </div>
        `;
    }
}

/**
 * Refresh events (for real-time updates)
 */
function refreshEvents() {
    loadEvents();
}

/**
 * Initialize events when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if events section exists
    if (document.querySelector('.events-grid')) {
        loadEvents();
        
        // Optional: Auto-refresh events every 5 minutes
        // setInterval(refreshEvents, 5 * 60 * 1000);
    }
});

// Export functions for use in console if needed
window.refreshEvents = refreshEvents;
window.loadEvents = loadEvents;