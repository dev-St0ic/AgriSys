// ===================================
// DYNAMIC EVENTS LOADING SYSTEM
// ===================================

let allEvents = [];
let currentFilter = 'all';

/**
 * Load events from API with retry mechanism
 */
async function loadEvents(retryCount = 0) {
    console.log('🔄 [Events] Loading... Attempt:', retryCount + 1);
    
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
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        console.log('✅ [Events] API Response:', data);
        
        if (!data.success) {
            throw new Error(data.message || 'API returned error');
        }
        
        if (!Array.isArray(data.events)) {
            throw new Error('Invalid events data format');
        }
        
        console.log(`✅ [Events] Loaded ${data.events.length} events`);
        
        allEvents = data.events;
        
        // Update section header
        updateEventsSectionHeader();
        
        // Render events
        renderEventsLayout();
        
    } catch (error) {
        console.error('❌ [Events] Loading failed:', error.message);
        
        // Retry logic (max 3 attempts)
        if (retryCount < 2) {
            console.log('🔄 [Events] Retrying in 2 seconds...');
            setTimeout(() => loadEvents(retryCount + 1), 2000);
        } else {
            showError(error.message);
        }
    }
}

/**
 * Update section header with dynamic content
 */
function updateEventsSectionHeader() {
    console.log('📝 [Events] Updating section header');
    
    const titleEl = document.querySelector('#events-title');
    const subtitleEl = document.querySelector('#events-subtitle');
    
    if (!titleEl || !subtitleEl) {
        console.warn('⚠️ [Events] Header elements not found');
        return;
    }
    
    // Dynamic title
    titleEl.innerHTML = 'City <span class="highlight">Agriculture Office Events</span>';
    
    // Dynamic subtitle
    const eventCount = allEvents.length;
    if (eventCount > 0) {
        subtitleEl.textContent = `Explore ${eventCount} upcoming events and initiatives dedicated to promoting agricultural growth and community development.`;
    } else {
        subtitleEl.textContent = 'No events available at this time. Check back soon for upcoming agricultural activities and programs.';
    }
    
    console.log('✅ [Events] Header updated');
}

/**
 * Main render function - renders 3 cards + 1 featured event
 */
function renderEventsLayout() {
    console.log('🎨 [Events] Rendering events layout');
    
    const container = document.querySelector('.events-container');
    if (!container) {
        console.error('❌ [Events] Container not found');
        return;
    }

    if (!allEvents || allEvents.length === 0) {
        container.innerHTML = createEmptyState();
        return;
    }

    let html = '';

    // === TOP ROW: 3 CARDS ===
    html += '<div class="events-grid-top">';
    const topCards = allEvents.slice(0, 3);
    topCards.forEach(event => {
        html += createEventCard(event);
    });
    html += '</div>';

    // === FEATURED EVENT: Large section ===
    if (allEvents.length > 0) {
        const featuredEvent = allEvents[0]; // Use first event as featured
        html += createFeaturedEvent(featuredEvent);
    }

    container.innerHTML = html;
    console.log('✅ [Events] Layout rendered successfully');
}

/**
 * Create event card (for top row - 3 cards)
 */
function createEventCard(event) {
    const imageUrl = event.image && event.image.trim() 
        ? event.image 
        : createPlaceholder(400, 220, 'Event');
    
    const title = event.title || 'Untitled Event';
    const description = event.short_description || event.description || 'No description';
    const category = event.category_label || 'Event';
    
    // Truncate description
    const truncated = description.length > 100 
        ? description.substring(0, 100) + '...' 
        : description;
    
    return `
        <div class="event-card" onclick="handleEventClick(${event.id})">
            <img src="${imageUrl}" 
                 alt="${title}" 
                 class="event-card-image" 
                 onerror="this.src='${createPlaceholder(400, 220, 'Error')}'">
            <div class="event-card-content">
                <span class="event-card-category">${category}</span>
                <h3 class="event-card-title">${title}</h3>
                <p class="event-card-description">${truncated}</p>
                <button class="event-card-btn">Learn More</button>
            </div>
        </div>
    `;
}

/**
 * Create featured event section (large bottom section)
 */
function createFeaturedEvent(event) {
    const imageUrl = event.image && event.image.trim() 
        ? event.image 
        : createPlaceholder(800, 400, 'Featured');
    
    const title = event.title || 'Featured Event';
    const description = event.description || 'No description available';
    const date = event.date || 'Date TBA';
    const location = event.location || 'Location TBA';
    const category = event.category_label || 'Event';

    return `
        <div class="events-featured">
            <div class="featured-layout">
                <div class="featured-image-section">
                    <img src="${imageUrl}" 
                         alt="${title}" 
                         class="featured-image"
                         onerror="this.src='${createPlaceholder(800, 400, 'Error')}'">
                </div>
                <div class="featured-content-section">
                    <span class="featured-badge">${category}</span>
                    <h3 class="featured-title">${title}</h3>
                    <p class="featured-description">${description}</p>
                    
                    <div class="featured-meta">
                        <div class="featured-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>${date}</span>
                        </div>
                        <div class="featured-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>${location}</span>
                        </div>
                    </div>
                    
                    <button class="featured-cta" onclick="handleEventClick(${event.id})">
                        View Full Details
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
}

/**
 * Create empty state
 */
function createEmptyState() {
    return `
        <div class="events-empty-state">
            <svg width="120" height="120" fill="none" stroke="#ccc" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3>No Events Available</h3>
            <p>Check back soon for upcoming agricultural activities and programs.</p>
            <button class="btn-reload" onclick="loadEvents()">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>
    `;
}

/**
 * Show error message
 */
function showError(message) {
    console.log('🚨 [Events] Error:', message);
    
    const container = document.querySelector('.events-container');
    if (container) {
        container.innerHTML = `
            <div class="events-error-state">
                <svg width="120" height="120" fill="none" stroke="#dc3545" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3>Unable to Load Events</h3>
                <p class="error-message">${message}</p>
                <button class="btn-retry" onclick="loadEvents()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Try Again
                </button>
            </div>
        `;
    }
}

/**
 * Handle event click - placeholder for modal or redirect
 */
function handleEventClick(eventId) {
    console.log('📋 [Events] Event clicked:', eventId);
    const event = allEvents.find(e => e.id === eventId);
    
    if (event) {
        // Placeholder: You can implement modal here
        console.log('Event Details:', event);
        // alert(`Event: ${event.title}\n\n${event.description}`);
    }
}

/**
 * Create placeholder image as SVG data URL
 */
function createPlaceholder(width, height, text) {
    return `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='${width}' height='${height}'%3E%3Crect fill='%23f0f0f0' width='${width}' height='${height}'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-size='24' fill='%23999'%3E${text}%3C/text%3E%3C/svg%3E`;
}

/**
 * Initialize on DOM ready
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 [Events] DOM loaded, initializing...');
    loadEvents();
});

// Make functions globally accessible
window.loadEvents = loadEvents;
window.handleEventClick = handleEventClick;

console.log('✅ [Events] Enhanced loader script initialized');