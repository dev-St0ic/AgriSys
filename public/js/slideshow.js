/**
 * Background Slideshow Functionality
 * Handles automatic background image transitions for the welcome section
 */

class BackgroundSlideshow {
    constructor() {
        this.slideIndex = 0;
        this.slides = document.querySelectorAll('.slide');
        this.indicators = document.querySelectorAll('.indicator');
        this.autoSlideInterval = null;
        this.slideDelay = 5000; // 5 seconds
        this.init();
    }

    init() {
        console.log('Initializing slideshow with', this.slides.length, 'slides');

        if (this.slides.length === 0) {
            console.warn('No slides found for slideshow');
            return;
        }

        // Preload images
        this.preloadImages();

        this.showSlide(0);
        this.startAutoSlide();
        this.addEventListeners();
    }

    preloadImages() {
        this.slides.forEach((slide, index) => {
            const bgImage = slide.style.backgroundImage;
            if (bgImage) {
                const imageUrl = bgImage.slice(4, -1).replace(/"/g, "");
                const img = new Image();
                img.onload = () => console.log(`Image ${index + 1} loaded successfully`);
                img.onerror = () => console.warn(`Failed to load image ${index + 1}:`, imageUrl);
                img.src = imageUrl;
            }
        });
    }

    showSlide(index) {
        console.log('Showing slide', index + 1);

        // Remove active class from all slides and indicators
        this.slides.forEach(slide => slide.classList.remove('active'));
        this.indicators.forEach(indicator => indicator.classList.remove('active'));

        // Add active class to current slide and indicator
        if (this.slides[index]) {
            this.slides[index].classList.add('active');
        }
        if (this.indicators[index]) {
            this.indicators[index].classList.add('active');
        }
    }

    nextSlide() {
        this.slideIndex = (this.slideIndex + 1) % this.slides.length;
        this.showSlide(this.slideIndex);
    }

    previousSlide() {
        this.slideIndex = (this.slideIndex - 1 + this.slides.length) % this.slides.length;
        this.showSlide(this.slideIndex);
    }

    goToSlide(index) {
        this.slideIndex = index;
        this.showSlide(this.slideIndex);
        this.restartAutoSlide();
    }

    startAutoSlide() {
        this.autoSlideInterval = setInterval(() => {
            this.nextSlide();
        }, this.slideDelay);
    }

    stopAutoSlide() {
        if (this.autoSlideInterval) {
            clearInterval(this.autoSlideInterval);
            this.autoSlideInterval = null;
        }
    }

    restartAutoSlide() {
        this.stopAutoSlide();
        this.startAutoSlide();
    }

    addEventListeners() {
        // Add click listeners to indicators
        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                this.goToSlide(index);
            });
        });

        // Pause on hover for better user experience
        const welcomeSection = document.querySelector('.welcome');
        if (welcomeSection) {
            welcomeSection.addEventListener('mouseenter', () => {
                this.stopAutoSlide();
            });

            welcomeSection.addEventListener('mouseleave', () => {
                this.startAutoSlide();
            });
        }

        // Handle keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (this.isInViewport(welcomeSection)) {
                if (e.key === 'ArrowLeft') {
                    this.previousSlide();
                    this.restartAutoSlide();
                } else if (e.key === 'ArrowRight') {
                    this.nextSlide();
                    this.restartAutoSlide();
                }
            }
        });
    }

    isInViewport(element) {
        if (!element) return false;
        const rect = element.getBoundingClientRect();
        return rect.top < window.innerHeight && rect.bottom > 0;
    }

    // Public method to change slide delay
    setSlideDelay(delay) {
        this.slideDelay = delay;
        this.restartAutoSlide();
    }
}

// Global functions for backwards compatibility
function currentSlide(index) {
    if (window.backgroundSlideshow) {
        window.backgroundSlideshow.goToSlide(index - 1);
    }
}

function nextSlide() {
    if (window.backgroundSlideshow) {
        window.backgroundSlideshow.nextSlide();
    }
}

function previousSlide() {
    if (window.backgroundSlideshow) {
        window.backgroundSlideshow.previousSlide();
    }
}

// Initialize slideshow when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.backgroundSlideshow = new BackgroundSlideshow();
});

// Handle visibility change to pause/resume slideshow
document.addEventListener('visibilitychange', function() {
    if (window.backgroundSlideshow) {
        if (document.hidden) {
            window.backgroundSlideshow.stopAutoSlide();
        } else {
            window.backgroundSlideshow.startAutoSlide();
        }
    }
});
