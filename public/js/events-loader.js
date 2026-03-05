// ===================================
// DYNAMIC EVENTS LOADING SYSTEM (WITH SLIDER SUPPORT)
// ===================================
// Layout rules:
//   2+ announcements OR 3+ non-announcement OR 4+ total  → slider
//   otherwise → static grid + featured announcement below
//   0 events → fallback featured section

let allEvents = [];
let sliderCurrentIndex = 0;
let sliderAutoInterval = null;
const CARDS_PER_VIEW_DESKTOP = 3;
const CARDS_PER_VIEW_TABLET = 2;
const CARDS_PER_VIEW_MOBILE = 1;

function getCardsPerView() {
    if (window.innerWidth <= 768) return CARDS_PER_VIEW_MOBILE;
    if (window.innerWidth <= 1024) return CARDS_PER_VIEW_TABLET;
    return CARDS_PER_VIEW_DESKTOP;
}

async function loadEvents(retryCount = 0) {
    try {
        const response = await fetch('/api/events?category=all', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'API returned error');
        if (!Array.isArray(data.events)) throw new Error('Invalid events data format');

        allEvents = data.events.length > 0 ? data.events : [getFallbackEvent()];
        updateEventsSectionHeader();
        renderEventsLayout();

    } catch (error) {
        console.error('Events loading failed:', error.message);
        if (retryCount < 2) {
            setTimeout(() => loadEvents(retryCount + 1), 2000);
        } else {
            allEvents = [getFallbackEvent()];
            updateEventsSectionHeader();
            renderEventsLayout();
        }
    }
}

function getFallbackEvent() {
    return {
        id: 0,
        title: 'City Agriculture Office Programs',
        description: 'The City Agriculture Office is committed to promoting sustainable farming practices, supporting local farmers, and developing agricultural programs that benefit our community.',
        short_description: 'Dedicated to promoting sustainable agriculture and supporting our farming community.',
        category: 'announcement',
        category_label: 'Announcement',
        image: '/images/logos/cago_web.png',
        date: 'Ongoing',
        location: 'City Agriculture Office',
        is_active: true,
        display_order: 0,
        is_fallback: true
    };
}

function updateEventsSectionHeader() {
    const titleEl = document.querySelector('#events-title');
    const subtitleEl = document.querySelector('#events-subtitle');
    if (!titleEl || !subtitleEl) return;

    titleEl.innerHTML = 'City <span class="highlight">Agriculture Office Events</span>';

    const hasFallback = allEvents.some(e => e.is_fallback);
    if (hasFallback) {
        subtitleEl.innerHTML = '<i class="fas fa-info-circle"></i> Stay updated with our ongoing agricultural programs and initiatives. New events will be announced here.';
        subtitleEl.style.color = '#6c757d';
    } else {
        subtitleEl.textContent = 'Explore our agricultural events and initiatives dedicated to promoting agricultural growth and community development.';
        subtitleEl.style.color = '';
    }
}

function getNonAnnouncementEvents() {
    return allEvents.filter(e => e.category !== 'announcement');
}

function getAnnouncementEvents() {
    return allEvents.filter(e => e.category === 'announcement');
}

function renderEventsLayout() {
    const container = document.querySelector('.events-container');
    if (!container) return;

    if (!allEvents || allEvents.length === 0) allEvents = [getFallbackEvent()];

    const nonAnnouncementEvents = getNonAnnouncementEvents();
    const announcementEvents = getAnnouncementEvents();

    // Use slider when:
    // - Total events >= 4, OR
    // - Non-announcement events >= 3 (fills a full desktop row), OR
    // - Announcements alone >= 2
    const useSlider = allEvents.length >= 4
        || nonAnnouncementEvents.length >= 3
        || announcementEvents.length >= 2;

    let html = '';

    if (useSlider) {
        html += createEventsSlider(allEvents);
    } else {
        if (nonAnnouncementEvents.length > 0) {
            html += '<div class="events-grid-top">';
            nonAnnouncementEvents.forEach((event, index) => {
                html += createEventCard(event, index);
            });
            html += '</div>';
        }

        if (announcementEvents.length > 0) {
            html += createFeaturedEvent(announcementEvents[0]);
        } else if (nonAnnouncementEvents.length === 0) {
            html += createFeaturedEvent(getFallbackEvent());
        }
    }

    container.innerHTML = html;

    if (useSlider) {
        initEventsSlider(allEvents);
    }
}

// ─── SLIDER ────────────────────────────────────────────────────────────────

function createEventsSlider(events) {
    const dots = events.map((_, i) =>
        `<span class="event-dot ${i === 0 ? 'active' : ''}" onclick="goToEventSlide(${i})"></span>`
    ).join('');

    const cards = events.map((event, index) => createEventCard(event, index)).join('');

    return `
        <div class="events-slider-wrapper">
            <div class="events-slider-viewport" id="eventsSliderViewport">
                <div class="events-slider-track" id="eventsSliderTrack">
                    ${cards}
                </div>
            </div>
            <div class="events-slider-footer">
                <button class="events-slider-nav prev" id="eventsSliderPrev" onclick="prevEventSlide()" aria-label="Previous events">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="events-slider-dots" id="eventsSliderDots">
                    ${dots}
                </div>
                <button class="events-slider-nav next" id="eventsSliderNext" onclick="nextEventSlide()" aria-label="Next events">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="events-slider-counter" id="eventsSliderCounter"></div>
        </div>
    `;
}

function initEventsSlider(events) {
    sliderCurrentIndex = 0;

    requestAnimationFrame(() => {
        updateSliderPosition(events.length);
    });

    startSliderAuto(events.length);
    addSwipeSupport(events.length);

    const viewport = document.getElementById('eventsSliderViewport');
    if (viewport) {
        viewport.addEventListener('mouseenter', stopSliderAuto);
        viewport.addEventListener('mouseleave', () => startSliderAuto(events.length));
    }

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => updateSliderPosition(events.length), 100);
    });
}

function updateSliderPosition(totalEvents) {
    const track = document.getElementById('eventsSliderTrack');
    const viewport = document.getElementById('eventsSliderViewport');
    const counter = document.getElementById('eventsSliderCounter');
    const prevBtn = document.getElementById('eventsSliderPrev');
    const nextBtn = document.getElementById('eventsSliderNext');
    const dots = document.querySelectorAll('.event-dot');

    if (!track || !viewport) return;

    const cardsPerView = getCardsPerView();
    const maxIndex = Math.max(0, totalEvents - cardsPerView);

    sliderCurrentIndex = Math.max(0, Math.min(sliderCurrentIndex, maxIndex));

    const firstCard = track.querySelector('.event-card');
    if (!firstCard) return;

    const gap = cardsPerView === 1 ? 20 : cardsPerView === 2 ? 25 : 30;
    const cardWidth = firstCard.offsetWidth;
    const translateX = sliderCurrentIndex * (cardWidth + gap);

    track.style.transform = `translateX(-${translateX}px)`;

    dots.forEach((dot, i) => dot.classList.toggle('active', i === sliderCurrentIndex));

    if (counter) counter.textContent = `${sliderCurrentIndex + 1} / ${maxIndex + 1}`;

    if (prevBtn) prevBtn.classList.toggle('disabled', sliderCurrentIndex === 0);
    if (nextBtn) nextBtn.classList.toggle('disabled', sliderCurrentIndex >= maxIndex);
}

function goToEventSlide(index) {
    const total = allEvents.length;
    const maxIndex = Math.max(0, total - getCardsPerView());
    sliderCurrentIndex = Math.min(index, maxIndex);
    updateSliderPosition(total);
    stopSliderAuto();
    startSliderAuto(total);
}

function nextEventSlide() {
    const total = allEvents.length;
    const maxIndex = Math.max(0, total - getCardsPerView());
    sliderCurrentIndex = sliderCurrentIndex < maxIndex ? sliderCurrentIndex + 1 : 0;
    updateSliderPosition(total);
    stopSliderAuto();
    startSliderAuto(total);
}

function prevEventSlide() {
    const total = allEvents.length;
    const maxIndex = Math.max(0, total - getCardsPerView());
    sliderCurrentIndex = sliderCurrentIndex > 0 ? sliderCurrentIndex - 1 : maxIndex;
    updateSliderPosition(total);
    stopSliderAuto();
    startSliderAuto(total);
}

function startSliderAuto(totalEvents) {
    stopSliderAuto();
    sliderAutoInterval = setInterval(nextEventSlide, 4000);
}

function stopSliderAuto() {
    if (sliderAutoInterval) {
        clearInterval(sliderAutoInterval);
        sliderAutoInterval = null;
    }
}

function addSwipeSupport(totalEvents) {
    const viewport = document.getElementById('eventsSliderViewport');
    if (!viewport) return;

    let startX = 0;

    viewport.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    }, { passive: true });

    viewport.addEventListener('touchend', (e) => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) nextEventSlide();
            else prevEventSlide();
        }
    }, { passive: true });
}

// ─── CARD & FEATURED ───────────────────────────────────────────────────────

function createEventCard(event, index = 0) {
    const imageUrl = event.image && event.image.trim()
        ? event.image
        : createPlaceholder(400, 220, 'Event');

    const title = event.title || 'Untitled Event';
    const description = event.short_description || event.description || 'No description';
    const category = event.category_label || 'Event';
    const truncated = description.length > 100 ? description.substring(0, 100) + '...' : description;

    return `
        <div class="event-card" onclick="handleEventCardClick(${event.id}, ${event.is_fallback || false})">
            <img src="${imageUrl}" alt="${title}" class="event-card-image"
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

    const notificationBadge = isFallback
        ? '<div class="alert alert-info mb-3"><i class="fas fa-info-circle me-2"></i><strong>Notice:</strong> New announcements will be posted here soon.</div>'
        : '';

    return `
        <div class="events-featured">
            <div class="featured-layout">
                <div class="featured-image-section">
                    <img src="${imageUrl}" alt="${title}" class="featured-image"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>${date}</span>
                        </div>
                        <div class="featured-meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
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

// ─── EVENT HANDLERS ────────────────────────────────────────────────────────

function handleEventCardClick(eventId, isFallback = false) {
    if (isFallback) {
        agrisysModal.info('For more information about our agricultural programs, please contact the City Agriculture Office.', { title: 'Contact Information' });
        return;
    }
    const event = allEvents.find(e => e.id === eventId);
    if (event) openEventDetailsInNewTab(event);
}

function handleLearnMoreClick(clickEvent, eventId, isFallback = false) {
    clickEvent.stopPropagation();
    if (isFallback) {
        agrisysModal.info('Contact: (02) 8808-2020, Local 109\nEmail: agriculture.sanpedrocity@gmail.com', { title: 'Contact Information' });
        return;
    }
    const event = allEvents.find(e => e.id === eventId);
    if (!event) {
        agrisysModal.error('Event details could not be found.', { title: 'Event Not Found' });
        return;
    }
    openEventDetailsInNewTab(event);
}

function openEventDetailsInNewTab(event) {
    const blob = new Blob([generateEventDetailsHTML(event)], { type: 'text/html' });
    const url = window.URL.createObjectURL(blob);
    window.open(url, '_blank');
    setTimeout(() => window.URL.revokeObjectURL(url), 100);
}

function generateEventDetailsHTML(event) {
    const title = event.title || 'Event Details';
    const description = event.description || 'No description available';
    const date = event.date || 'Date TBA';
    const location = event.location || 'Location TBA';
    const category = event.category_label || 'Event';
    const imageUrl = (event.image && event.image.trim()) ? event.image : createPlaceholder(800, 400, event.title);

    let detailsHTML = '';
    if (event.details && Object.keys(event.details).length > 0) {
        detailsHTML = '<div class="details"><h3>Additional Details</h3><ul>';
        for (const [key, value] of Object.entries(event.details)) {
            const displayKey = key.replace(/_/g, ' ').charAt(0).toUpperCase() + key.replace(/_/g, ' ').slice(1);
            detailsHTML += `<li><strong>${displayKey}:</strong> ${value}</li>`;
        }
        detailsHTML += '</ul></div>';
    }

    return `<!DOCTYPE html><html lang="en"><head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>${title} - AgriSys Events</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            *{margin:0;padding:0;box-sizing:border-box}
            body{font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#f5f7fa,#c3cfe2);min-height:100vh;padding:20px}
            .wrap{max-width:900px;margin:0 auto;background:white;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.15);overflow:hidden}
            .hdr{position:relative}.img{width:100%;height:400px;object-fit:cover;display:block}
            .overlay{position:absolute;inset:0;background:linear-gradient(to bottom,transparent,rgba(0,0,0,.5));display:flex;align-items:flex-end;padding:40px 30px;color:white}
            .badge{background:#0A6953;color:white;padding:6px 12px;border-radius:20px;font-size:.9em;font-weight:600;margin-bottom:10px;display:inline-block}
            h1{font-size:2.2em;text-shadow:0 2px 4px rgba(0,0,0,.3)}
            .body{padding:40px}
            .meta{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px;padding-bottom:30px;border-bottom:2px solid #f0f0f0}
            .mi{display:flex;align-items:center;gap:15px}
            .icon{width:50px;height:50px;background:#f0f0f0;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5em;color:#0A6953}
            .mt h4{color:#666;font-size:.85em}.mt p{color:#333;font-weight:600}
            h3{color:#0A6953;margin-bottom:15px;font-size:1.4em}
            p{color:#555;line-height:1.8;margin-bottom:20px}
            .details{background:#f9f9f9;padding:25px;border-radius:8px;margin-bottom:30px}
            .details ul{list-style:none}.details li{padding:10px 0;border-bottom:1px solid #e0e0e0;color:#555}
            .details li:last-child{border-bottom:none}
            .actions{padding-top:30px;border-top:2px solid #f0f0f0}
            .btn{padding:12px 30px;background:#0A6953;color:white;border:none;border-radius:6px;font-size:1em;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:10px}
            .btn:hover{background:#084a3d}
            footer{background:#f9f9f9;padding:20px;text-align:center;color:#666;font-size:.9em;border-top:1px solid #e0e0e0}
        </style></head>
    <body><div class="wrap">
        <div class="hdr">
            <img src="${imageUrl}" alt="${title}" class="img">
            <div class="overlay"><div><span class="badge">${category}</span><h1>${title}</h1></div></div>
        </div>
        <div class="body">
            <div class="meta">
                <div class="mi"><div class="icon"><i class="fas fa-calendar-alt"></i></div><div class="mt"><h4>Date & Time</h4><p>${date}</p></div></div>
                <div class="mi"><div class="icon"><i class="fas fa-map-marker-alt"></i></div><div class="mt"><h4>Location</h4><p>${location}</p></div></div>
            </div>
            <h3>About This Event</h3><p>${description}</p>
            ${detailsHTML}
            <div class="actions"><button class="btn" onclick="window.close()"><i class="fas fa-arrow-left"></i> Back to Home</button></div>
        </div>
        <footer><p>&copy; 2026 City Agriculture Office of San Pedro. All rights reserved.</p></footer>
    </div></body></html>`;
}

function createPlaceholder(width, height, text) {
    return `data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='${width}' height='${height}'%3E%3Crect fill='%23f0f0f0' width='${width}' height='${height}'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-size='24' fill='%23999'%3E${text}%3C/text%3E%3C/svg%3E`;
}

// ─── INIT ──────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => loadEvents());

window.loadEvents = loadEvents;
window.handleEventCardClick = handleEventCardClick;
window.handleLearnMoreClick = handleLearnMoreClick;
window.goToEventSlide = goToEventSlide;
window.nextEventSlide = nextEventSlide;
window.prevEventSlide = prevEventSlide;