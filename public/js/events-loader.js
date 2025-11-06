// ===================================
// DYNAMIC EVENTS LOADING SYSTEM (WITH SAFETY FALLBACK)
// ===================================
// CRITICAL: Landing page NEVER shows empty state
// Top 3 cards show non-announcement events, featured (bottom) shows announcements only

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
        
        console.log(`✅ [Events] Loaded ${data.events.length} active events`);
        
        allEvents = data.events;
        
        // SAFETY CHECK: Use fallback if no active events
        if (allEvents.length === 0) {
            console.warn('⚠️ [Events] No active events found - using fallback event');
            allEvents = [getFallbackEvent()];
        }
        
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
            // Even on error, use fallback to prevent empty page
            console.warn('⚠️ [Events] All retries failed - using fallback event');
            allEvents = [getFallbackEvent()];
            updateEventsSectionHeader();
            renderEventsLayout();
        }
    }
}

/**
 * Get fallback event when no active events exist
 * This ensures the landing page NEVER shows empty state
 */
function getFallbackEvent() {
    return {
        id: 0,
        title: 'City Agriculture Office Programs',
        description: 'The City Agriculture Office is committed to promoting sustainable farming practices, supporting local farmers, and developing agricultural programs that benefit our community. We continuously work on initiatives to enhance food security, improve agricultural productivity, and foster environmental stewardship. Stay tuned for announcements about upcoming events, training sessions, and community programs designed to strengthen our agricultural sector.',
        short_description: 'Dedicated to promoting sustainable agriculture and supporting our farming community.',
        category: 'announcement',
        category_label: 'Announcement',
        image: createPlaceholder(800, 400, 'Agriculture Office'),
        date: 'Ongoing',
        location: 'City Agriculture Office',
        is_active: true,
        display_order: 0,
        is_fallback: true // Flag to identify fallback event
    };
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
    const hasFallback = allEvents.some(e => e.is_fallback);
    
    if (hasFallback) {
        subtitleEl.innerHTML = '<i class="fas fa-info-circle me-2"></i>Stay updated with our ongoing agricultural programs and initiatives. New events will be announced here.';
        subtitleEl.style.color = '#6c757d';
    } else if (eventCount > 0) {
        subtitleEl.textContent = `Explore our agricultural events and initiatives dedicated to promoting agricultural growth and community development.`;
        subtitleEl.style.color = '';
    }
    
    console.log('✅ [Events] Header updated');
}

/**
 * Get non-announcement events (for top 3 cards)
 */
function getNonAnnouncementEvents() {
    return allEvents.filter(e => e.category !== 'announcement');
}

/**
 * Get announcement events (for featured bottom section)
 */
function getAnnouncementEvents() {
    return allEvents.filter(e => e.category === 'announcement');
}

/**
 * Main render function - 3 cards (non-announcement) + featured announcement
 * ALWAYS displays content (never empty)
 */
function renderEventsLayout() {
    console.log('🎨 [Events] Rendering events layout');
    
    const container = document.querySelector('.events-container');
    if (!container) {
        console.error('❌ [Events] Container not found');
        return;
    }

    // SAFETY: Should never reach here due to fallback, but double-check
    if (!allEvents || allEvents.length === 0) {
        console.error('❌ [Events] Critical: No events available and no fallback loaded');
        allEvents = [getFallbackEvent()];
    }

    let html = '';

    // === TOP ROW: 3 CARDS (Non-announcement events) ===
    html += '<div class="events-grid-top">';
    
    const nonAnnouncementEvents = getNonAnnouncementEvents();
    const topCards = [];
    
    // Fill 3 cards with non-announcement events (duplicate if less than 3)
    if (nonAnnouncementEvents.length >= 3) {
        topCards.push(...nonAnnouncementEvents.slice(0, 3));
    } else if (nonAnnouncementEvents.length > 0) {
        // Use what we have and duplicate to fill 3 slots
        for (let i = 0; i < 3; i++) {
            topCards.push(nonAnnouncementEvents[i % nonAnnouncementEvents.length]);
        }
    } else {
        // No non-announcement events, use fallback
        const fallbackEvent = getFallbackEvent();
        fallbackEvent.category = 'past_event';
        fallbackEvent.category_label = 'Past Event';
        for (let i = 0; i < 3; i++) {
            topCards.push({...fallbackEvent, id: -1 - i});
        }
    }
    
    topCards.forEach((event, index) => {
        html += createEventCard(event, index);
    });
    html += '</div>';

    // === FEATURED EVENT: Large section (Announcement only) ===
    const announcementEvents = getAnnouncementEvents();
    const featuredEvent = announcementEvents.length > 0 ? announcementEvents[0] : getFallbackEvent();
    html += createFeaturedEvent(featuredEvent);

    container.innerHTML = html;
    console.log('✅ [Events] Layout rendered with ' + allEvents.length + ' event(s)');
}

/**
 * Create event card (for top row - 3 cards)
 */
function createEventCard(event, index = 0) {
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
        <div class="event-card" onclick="handleEventClick(${event.id}, ${event.is_fallback || false})">
            <img src="${imageUrl}" 
                 alt="${title}" 
                 class="event-card-image" 
                 onerror="this.src='${createPlaceholder(400, 220, 'Event')}'">
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
 * Create featured event section (large bottom section - announcements only)
 */
function createFeaturedEvent(event) {
    const imageUrl = event.image && event.image.trim() 
        ? event.image 
        : createPlaceholder(800, 400, 'Featured');
    
    const title = event.title || 'Featured Announcement';
    const description = event.description || 'No description available';
    const date = event.date || 'Date TBA';
    const location = event.location || 'Location TBA';
    const category = event.category_label || 'Announcement';
    const isFallback = event.is_fallback || false;

    // Show notification badge if fallback
    const notificationBadge = isFallback 
        ? '<div class="alert alert-info mb-3"><i class="fas fa-info-circle me-2"></i><strong>Notice:</strong> New announcements will be posted here. Check back soon for updates on upcoming agricultural programs and activities.</div>'
        : '';

    return `
        <div class="events-featured">
            <div class="featured-layout">
                <div class="featured-image-section">
                    <img src="${imageUrl}" 
                         alt="${title}" 
                         class="featured-image"
                         onerror="this.src='${createPlaceholder(800, 400, 'Featured')}'">
                </div>
                <div class="featured-content-section">
                    ${notificationBadge}
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
                    
                    <button class="featured-cta" onclick="handleEventClick(${event.id}, ${isFallback})">
                        ${isFallback ? 'Contact Us' : 'View Full Details'}
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
 * Handle event click
 */
function handleEventClick(eventId, isFallback = false) {
    console.log('📋 [Events] Event clicked:', eventId, 'Is fallback:', isFallback);
    
    if (isFallback) {
        // Redirect to contact page or show info
        alert('For more information about our agricultural programs, please contact the City Agriculture Office.');
        return;
    }
    
    const event = allEvents.find(e => e.id === eventId);
    
    if (event) {
        console.log('Event Details:', event);
        // TODO: Implement modal or detail page
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

console.log('✅ [Events] Enhanced loader script with announcements featured');