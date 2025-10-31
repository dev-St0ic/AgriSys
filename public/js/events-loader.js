// ===================================
// EVENT LOADING FOR LANDING PAGE
// ===================================

let allEvents = [];
let currentFilter = 'all';

/**
 * Load events from API with detailed debugging
 */
async function loadEvents() {
    console.log('🔄 [Events] Starting load...');
    
    const grid = document.querySelector('.events-grid');
    
    if (!grid) {
        console.error('❌ [Events] Grid not found on page');
        return;
    }
    
    console.log('✅ [Events] Grid found, fetching data...');
    
    try {
        const url = '/api/events?category=all';
        console.log(`📡 [Events] Fetching: ${url}`);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log(`📊 [Events] Response status: ${response.status}`);
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }
        
        const data = await response.json();
        console.log('✅ [Events] API Response:', data);
        
        if (!data.success) {
            throw new Error(data.message || 'API error');
        }
        
        if (!Array.isArray(data.events)) {
            throw new Error('Events is not an array: ' + JSON.stringify(data.events));
        }
        
        console.log(`✅ [Events] Loaded ${data.events.length} events`);
        
        allEvents = data.events;
        renderEvents(data.events);
        attachEventListeners();
        
    } catch (error) {
        console.error('❌ [Events] Loading failed:', error.message);
        console.error('Full error:', error);
        showError(error.message);
    }
}

/**
 * Render events to grid
 */
function renderEvents(events) {
    console.log('🎨 [Events] Rendering', events ? events.length : 0, 'events');
    
    const grid = document.querySelector('.events-grid');
    if (!grid) return;

    if (!events || events.length === 0) {
        grid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i class="fas fa-inbox fa-4x text-muted mb-3" style="display: block; margin-bottom: 20px;"></i>
                <h5 class="text-muted">No events available</h5>
                <p class="text-muted">Check back soon for upcoming events</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = events.map(event => createEventCard(event)).join('');
    console.log('✅ [Events] Rendered successfully');
}

/**
 * Create single event card
 */
function createEventCard(event) {
    const imageUrl = event.image && event.image.trim() ? event.image : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22250%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22400%22 height=%22250%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2220%22 fill=%22%23999%22%3ENo Image%3C/text%3E%3C/svg%3E';
    
    const details = event.details || {};
    const title = event.title || 'Untitled';
    const description = event.description || 'No description';
    const date = event.date || 'Date TBA';
    const location = event.location || 'Location TBA';
    
    return `
        <div class="event-card" data-category="${event.category}">
            <img src="${imageUrl}" alt="${title}" class="event-image" 
                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22250%22%3E%3Crect fill=%22%23e0e0e0%22 width=%22400%22 height=%22250%22/%3E%3C/svg%3E'">
            <div class="event-content">
                <h3>${title}</h3>
                <p class="event-description">${description}</p>
                
                <div class="event-info-box">
                    <div class="date">📅 ${date}</div>
                    <div class="location">📍 ${location}</div>
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
        if (key === 'icon' || !value) continue;
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
 * Get icon for detail
 */
function getDetailIcon(key) {
    const icons = {
        'participants': '👥', 'cost': '💰', 'achievement': '🌳',
        'impact': '🎯', 'freebies': '🎁', 'registration': '📝',
        'support': '💚', 'certification': '🏆', 'services': '🛠️',
        'report': '📞', 'facilities': '⚽', 'upgrades': '🔧',
        'techniques': '🌱', 'materials': '📦', 'for': '👨‍🌾',
        'requirement': '✓', 'benefits': '⭐'
    };
    return icons[key] || '📌';
}

/**
 * Format label
 */
function formatLabel(key) {
    return key.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
}

/**
 * Toggle event details
 */
function toggleEventDetails(btn) {
    const card = btn.closest('.event-card');
    const details = card.querySelector('.expandable-details');
    const isExpanded = card.classList.contains('expanded');
    
    if (isExpanded) {
        card.classList.remove('expanded');
        details.style.maxHeight = '0';
        btn.innerHTML = '<span>View More Details</span> <span class="arrow">▼</span>';
    } else {
        card.classList.add('expanded');
        details.style.maxHeight = details.scrollHeight + 'px';
        btn.innerHTML = '<span>Hide Details</span> <span class="arrow">▲</span>';
    }
}

/**
 * Attach event listeners to filter buttons
 */
function attachEventListeners() {
    console.log('🔗 [Events] Attaching listeners to filter buttons...');
    
    const filterButtons = document.querySelectorAll('.filter-btn');
    console.log(`Found ${filterButtons.length} filter buttons`);
    
    filterButtons.forEach(button => {
        button.removeEventListener('click', handleFilterClick);
        button.addEventListener('click', handleFilterClick);
    });
}

/**
 * Handle filter click
 */
function handleFilterClick(event) {
    event.preventDefault();
    
    const filterButtons = document.querySelectorAll('.filter-btn');
    const eventCards = document.querySelectorAll('.event-card');
    const filterValue = event.currentTarget.getAttribute('data-filter');
    
    console.log(`🔍 [Events] Filtering by: ${filterValue}`);
    
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.currentTarget.classList.add('active');
    
    let visibleCount = 0;
    eventCards.forEach(card => {
        const shouldShow = filterValue === 'all' || card.getAttribute('data-category') === filterValue;
        if (shouldShow) {
            visibleCount++;
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
    });
    
    console.log(`✅ [Events] Showing ${visibleCount} events`);
}

/**
 * Show error
 */
function showError(message) {
    console.log('🚨 [Events] Error:', message);
    const grid = document.querySelector('.events-grid');
    if (grid) {
        grid.innerHTML = `
            <div style="text-align: center; padding: 40px; grid-column: 1/-1;">
                <p style="color: red; font-size: 16px; margin-bottom: 20px;">
                    <strong>⚠️ Error:</strong> ${message}
                </p>
                <button onclick="loadEvents()" style="padding: 10px 20px; background: #0A6953; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    🔄 Retry
                </button>
            </div>
        `;
    }
}

/**
 * Initialize on DOM ready
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 [Events] DOM loaded, starting events loader...');
    loadEvents();
});

// Make functions global
window.loadEvents = loadEvents;
window.toggleEventDetails = toggleEventDetails;
window.attachEventListeners = attachEventListeners;

console.log('✅ [Events] Loader script initialized');