// ===================================
// DYNAMIC EVENTS LOADING SYSTEM (WITH SAFETY FALLBACK)
// ===================================
// CRITICAL: Landing page NEVER shows empty state
// Shows actual number of non-announcement events (no duplication)
// Featured section shows announcements only

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
 * Get non-announcement events (for top cards)
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
 * Main render function - Shows actual events without duplication
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

    // === TOP ROW: ACTUAL NON-ANNOUNCEMENT EVENTS (No duplication) ===
    const nonAnnouncementEvents = getNonAnnouncementEvents();
    
    if (nonAnnouncementEvents.length > 0) {
        // Show only actual events - 1, 2, 3, or more cards
        html += '<div class="events-grid-top">';
        nonAnnouncementEvents.forEach((event, index) => {
            html += createEventCard(event, index);
        });
        html += '</div>';
        
        console.log(`✅ [Events] Rendered ${nonAnnouncementEvents.length} non-announcement event(s)`);
    } else {
        // No non-announcement events - show informative message or skip top section
        console.log('ℹ️ [Events] No non-announcement events to display');
    }

    // === FEATURED EVENT: Large section (Announcement only) ===
    const announcementEvents = getAnnouncementEvents();
    if (announcementEvents.length > 0) {
        const featuredEvent = announcementEvents[0];
        html += createFeaturedEvent(featuredEvent);
        console.log('✅ [Events] Rendered featured announcement');
    } else if (nonAnnouncementEvents.length === 0) {
        // Only show fallback if there are NO events at all
        html += createFeaturedEvent(getFallbackEvent());
        console.log('⚠️ [Events] No events available - showing fallback');
    }

    container.innerHTML = html;
    console.log('✅ [Events] Layout rendered with ' + allEvents.length + ' event(s)');
}

/**
 * Create event card (for top row)
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
        <div class="event-card" onclick="handleEventCardClick(${event.id}, ${event.is_fallback || false})">
            <img src="${imageUrl}"
                 alt="${title}"
                 class="event-card-image"
                 onerror="this.src='${createPlaceholder(400, 220, 'Event')}'">
            <div class="event-card-content">
                <span class="event-card-category">${category}</span>
                <h3 class="event-card-title">${title}</h3>
                <p class="event-card-description">${truncated}</p>
                <button class="event-card-btn" onclick="handleLearnMoreClick(event, ${event.id}, ${event.is_fallback || false})">Learn More</button>
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

                    <button class="featured-cta" onclick="handleLearnMoreClick(event, ${event.id}, ${isFallback})">
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
 * Handle event card click (for card itself, not button)
 */
function handleEventCardClick(eventId, isFallback = false) {
    console.log('📋 [Events] Event card clicked:', eventId, 'Is fallback:', isFallback);

    if (isFallback) {
        agrisysModal.info('For more information about our agricultural programs, please contact the City Agriculture Office.', { title: 'Contact Information' });
        return;
    }

    const event = allEvents.find(e => e.id === eventId);

    if (event) {
        console.log('Event Details:', event);
    }
}

/**
 * Handle "Learn More" / "View Full Details" button click
 * Opens event details in a new tab
 */
function handleLearnMoreClick(clickEvent, eventId, isFallback = false) {
    clickEvent.stopPropagation(); // Prevent triggering parent card click

    console.log('📖 [Events] Learn More clicked for event:', eventId, 'Is fallback:', isFallback);

    if (isFallback) {
        // For fallback events, show contact info or redirect to contact page
        console.log('ℹ️ [Events] Fallback event - showing contact info');
        agrisysModal.info('For more information about our agricultural programs, please contact the City Agriculture Office.\n\nContact: (02) 8808-2020, Local 109\nEmail: agriculture.sanpedrocity@gmail.com', { title: 'Contact Information' });
        return;
    }

    // Get the full event data
    const event = allEvents.find(e => e.id === eventId);

    if (!event) {
        console.error('❌ [Events] Event not found:', eventId);
        agrisysModal.error('Event details could not be found.', { title: 'Event Not Found' });
        return;
    }

    // Open event details in a new tab
    openEventDetailsInNewTab(event);
}

/**
 * Open event details in a new tab with full information
 */
function openEventDetailsInNewTab(event) {
    console.log('🔗 [Events] Opening event details in new tab:', event.id);

    // Create a data URL or redirect to a details page
    // Option 1: If you have a dedicated event details page
    // window.open(`/events/${event.id}`, '_blank');

    // Option 2: Create an HTML page dynamically in a new tab
    const detailsHTML = generateEventDetailsHTML(event);

    // Open in new tab using blob URL (doesn't require a server route)
    const blob = new Blob([detailsHTML], { type: 'text/html' });
    const url = window.URL.createObjectURL(blob);
    window.open(url, '_blank');

    // Clean up the blob URL after a short delay
    setTimeout(() => window.URL.revokeObjectURL(url), 100);
}

/**
 * Generate comprehensive HTML for event details page
 */
function generateEventDetailsHTML(event) {
    const title = event.title || 'Event Details';
    const description = event.description || 'No description available';
    const date = event.date || 'Date TBA';
    const location = event.location || 'Location TBA';
    const category = event.category_label || 'Event';
    const imageUrl = (event.image && event.image.trim()) ? event.image : createPlaceholder(800, 400, event.title);

    // Format details if they exist
    let detailsHTML = '';
    if (event.details && Object.keys(event.details).length > 0) {
        detailsHTML = '<div class="details-section"><h3>Additional Details</h3><ul>';
        for (const [key, value] of Object.entries(event.details)) {
            const displayKey = key.replace(/_/g, ' ').charAt(0).toUpperCase() + key.replace(/_/g, ' ').slice(1);
            detailsHTML += `<li><strong>${displayKey}:</strong> ${value}</li>`;
        }
        detailsHTML += '</ul></div>';
    }

    return `
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>${title} - AgriSys Events</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                padding: 20px;
            }

            .event-details-container {
                max-width: 900px;
                margin: 0 auto;
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                overflow: hidden;
            }

            .event-header {
                position: relative;
                overflow: hidden;
            }

            .event-image {
                width: 100%;
                height: 400px;
                object-fit: cover;
                display: block;
            }

            .event-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.5) 100%);
                display: flex;
                align-items: flex-end;
                padding: 40px 30px;
                color: white;
            }

            .event-overlay h1 {
                font-size: 2.5em;
                margin-bottom: 10px;
                text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            }

            .event-category-badge {
                display: inline-block;
                background: #0A6953;
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 0.9em;
                font-weight: 600;
                margin-bottom: 10px;
            }

            .event-content {
                padding: 40px;
            }

            .event-meta {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
                padding-bottom: 30px;
                border-bottom: 2px solid #f0f0f0;
            }

            .meta-item {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .meta-icon {
                width: 50px;
                height: 50px;
                background: #f0f0f0;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5em;
                color: #0A6953;
            }

            .meta-text h4 {
                color: #666;
                font-size: 0.85em;
                margin-bottom: 4px;
            }

            .meta-text p {
                color: #333;
                font-weight: 600;
                font-size: 1.1em;
            }

            .description-section {
                margin-bottom: 30px;
            }

            .description-section h3 {
                color: #0A6953;
                margin-bottom: 15px;
                font-size: 1.5em;
            }

            .description-section p {
                color: #555;
                line-height: 1.8;
                font-size: 1.05em;
            }

            .details-section {
                background: #f9f9f9;
                padding: 25px;
                border-radius: 8px;
                margin-bottom: 30px;
            }

            .details-section h3 {
                color: #0A6953;
                margin-bottom: 15px;
            }

            .details-section ul {
                list-style: none;
            }

            .details-section li {
                padding: 10px 0;
                border-bottom: 1px solid #e0e0e0;
                color: #555;
            }

            .details-section li:last-child {
                border-bottom: none;
            }

            .details-section strong {
                color: #0A6953;
            }

            .action-buttons {
                display: flex;
                gap: 15px;
                margin-top: 30px;
                padding-top: 30px;
                border-top: 2px solid #f0f0f0;
            }

            .btn {
                padding: 12px 30px;
                border: none;
                border-radius: 6px;
                font-size: 1em;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }

            .btn-primary {
                background: #0A6953;
                color: white;
            }

            .btn-primary:hover {
                background: #084a3d;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(10, 105, 83, 0.3);
            }

            .btn-secondary {
                background: #f0f0f0;
                color: #333;
            }

            .btn-secondary:hover {
                background: #e0e0e0;
            }

            .footer-section {
                background: #f9f9f9;
                padding: 20px 40px;
                border-top: 1px solid #e0e0e0;
                text-align: center;
                color: #666;
                font-size: 0.9em;
            }

            @media (max-width: 768px) {
                .event-overlay h1 {
                    font-size: 1.8em;
                }

                .event-content {
                    padding: 25px;
                }

                .event-meta {
                    grid-template-columns: 1fr;
                }

                .action-buttons {
                    flex-direction: column;
                }

                .btn {
                    justify-content: center;
                }
            }
        </style>
    </head>
    <body>
        <div class="event-details-container">
            <!-- Event Header with Image -->
            <div class="event-header">
                <img src="${imageUrl}" alt="${title}" class="event-image">
                <div class="event-overlay">
                    <div>
                        <span class="event-category-badge">${category}</span>
                        <h1>${title}</h1>
                    </div>
                </div>
            </div>

            <!-- Event Content -->
            <div class="event-content">
                <!-- Event Metadata -->
                <div class="event-meta">
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="meta-text">
                            <h4>Date & Time</h4>
                            <p>${date}</p>
                        </div>
                    </div>

                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="meta-text">
                            <h4>Location</h4>
                            <p>${location}</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="description-section">
                    <h3>About This Event</h3>
                    <p>${description}</p>
                </div>

                <!-- Additional Details -->
                ${detailsHTML}

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="window.close();">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </button>
                </div>

                <script>
                    window.addEventListener('beforeunload', function() {
                        if (window.opener) {
                            window.opener.focus();
                        }
                    });
                </script>
            </div>

            <!-- Footer -->
            <div class="footer-section">
                <p>&copy; 2026 City Agriculture Office of San Pedro. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    `;
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
window.handleEventCardClick = handleEventCardClick;
window.handleLearnMoreClick = handleLearnMoreClick;

console.log('✅ [Events] Enhanced loader script - shows actual events only (no duplication)');