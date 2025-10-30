// ===================================
// EVENT LOADING FOR LANDING PAGE - DIAGNOSTIC
// ===================================

let allEvents = [];
let currentFilter = 'all';

/**
 * Load events from API
 */
async function loadEvents() {
    console.log('🔄 Starting to load events...');
    
    const grid = document.querySelector('.events-grid');
    
    try {
        const apiUrl = '/api/events?category=all';
        console.log('📡 API URL:', apiUrl);
        
        const response = await fetch(apiUrl);
        console.log('📊 Response Status:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('✅ Raw API Data:', data);
        
        if (data.success) {
            console.log('✅ API Success! Events count:', data.events.length);
            allEvents = data.events;
            renderEvents(data.events);
        } else {
            console.error('❌ API returned error:', data);
            showError(data.message || 'Failed to load events');
        }
    } catch (error) {
        console.error('❌ Fetch error:', error);
        console.error('Error details:', error.message);
        showError('Cannot connect to server: ' + error.message);
    }
}

function showError(message) {
    console.log('🚨 Showing error:', message);
    const grid = document.querySelector('.events-grid');
    if (grid) {
        grid.innerHTML = `
            <div style="text-align: center; padding: 40px; grid-column: 1/-1;">
                <p style="color: red; font-size: 16px;"><strong>Error:</strong> ${message}</p>
                <button onclick="loadEvents()" style="margin-top: 20px; padding: 10px 20px; background: #0A6953; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Retry
                </button>
            </div>
        `;
    }
}

/**
 * Render events to the grid
 */
function renderEvents(events) {
    console.log('🎨 Rendering events, count:', events ? events.length : 0);
    
    const eventsGrid = document.querySelector('.events-grid');
    
    if (!eventsGrid) {
        console.warn('⚠️ Events grid container not found');
        return;
    }

    if (!events || events.length === 0) {
        console.log('ℹ️ No events to display');
        eventsGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i class="fas fa-inbox fa-4x text-muted mb-3" style="display: block; margin-bottom: 20px;"></i>
                <h5 class="text-muted">No events available</h5>
                <p class="text-muted">Check back soon for upcoming events</p>
            </div>
        `;
        return;
    }

    console.log('📝 Creating event cards...');
    eventsGrid.innerHTML = events.map((event, index) => {
        console.log(`Creating card ${index}:`, event.title);
        return createEventCard(event);
    }).join('');
    
    console.log('✅ Event cards rendered');
    
    // Reattach event listeners
    attachEventListeners();
}

/**
 * Create event card HTML
 */
function createEventCard(event) {
    // Use image URL from API or fallback to placeholder
    const imageUrl = event.image && event.image.trim() 
        ? event.image 
        : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22250%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22400%22 height=%22250%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2220%22 fill=%22%23999%22%3ENo Image Available%3C/text%3E%3C/svg%3E';
    
    const details = event.details || {};
    const eventTitle = event.title || 'Untitled Event';
    const eventDescription = event.description || 'No description available';
    const eventDate = event.date || 'Date TBA';
    const eventLocation = event.location || 'Location TBA';
    
    return `
        <div class="event-card" data-category="${event.category}">
            <img src="${imageUrl}" alt="${eventTitle}" class="event-image" 
                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22250%22%3E%3Crect fill=%22%23e0e0e0%22 width=%22400%22 height=%22250%22/%3E%3C/svg%3E'">
            <div class="event-content">
                <h3>${eventTitle}</h3>
                <p class="event-description">${eventDescription}</p>
                
                <div class="event-info-box">
                    <div class="date">📅 ${eventDate}</div>
                    <div class="location">📍 ${eventLocation}</div>
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
    console.log('🔗 Attaching event listeners...');
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    console.log('Found filter buttons:', filterButtons.length);
    
    filterButtons.forEach((button, index) => {
        button.removeEventListener('click', handleFilterClick);
        button.addEventListener('click', handleFilterClick);
        console.log(`Filter button ${index}:`, button.getAttribute('data-filter'));
    });
}

/**
 * Handle filter button click
 */
function handleFilterClick(event) {
    event.preventDefault();
    
    const filterButtons = document.querySelectorAll('.filter-btn');
    const eventCards = document.querySelectorAll('.event-card');
    
    console.log('🔍 Filter clicked:', event.currentTarget.getAttribute('data-filter'));
    
    // Update active button
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.currentTarget.classList.add('active');
    
    const filterValue = event.currentTarget.getAttribute('data-filter');
    currentFilter = filterValue;
    
    console.log('Filtering by:', filterValue);
    console.log('Total cards:', eventCards.length);
    
    // Filter cards
    let visibleCount = 0;
    eventCards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        const shouldShow = filterValue === 'all' || cardCategory === filterValue;
        
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
    
    console.log('Visible cards:', visibleCount);
}

/**
 * Refresh events
 */
function refreshEvents() {
    console.log('🔄 Refreshing events...');
    loadEvents();
}

/**
 * Initialize events when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded');
    
    const grid = document.querySelector('.events-grid');
    if (grid) {
        console.log('✅ Events grid found, loading events...');
        loadEvents();
    } else {
        console.log('❌ Events grid NOT found');
    }
});

// Export functions
window.refreshEvents = refreshEvents;
window.loadEvents = loadEvents;