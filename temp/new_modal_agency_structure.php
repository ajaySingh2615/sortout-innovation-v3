<?php
// This is a template showing how modal_agency.php should be structured

// ... beginning of file ...

// End of the client card functionality script
});
</script>

<!-- ✅ New Hero Banner Section -->
<div class="hero-banner-section">
    <!-- Banner Slider Container -->
    <div class="banner-slider" id="bannerSlider">
        <!-- Banner Slides -->
        <div class="banner-slide active">
            <img src="/images/agency-banner/first-banner.jpg" alt="Agency Banner 1">
        </div>
        <div class="banner-slide">
            <img src="/images/agency-banner/second-banner.jpg" alt="Agency Banner 2">
        </div>
        <div class="banner-slide">
            <img src="/images/agency-banner/third-banner.jpg" alt="Agency Banner 3">
        </div>
    </div>
    
    <!-- Navigation Arrows -->
    <button class="banner-arrow banner-arrow-left" id="bannerArrowLeft">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="banner-arrow banner-arrow-right" id="bannerArrowRight">
        <i class="fas fa-chevron-right"></i>
    </button>
    
    <!-- Navigation Indicators -->
    <div class="banner-indicators" id="bannerIndicators">
        <span class="banner-indicator active" data-index="0"></span>
        <span class="banner-indicator" data-index="1"></span>
        <span class="banner-indicator" data-index="2"></span>
    </div>
</div>

<style>
/* New Hero Banner Section Styles */
.hero-banner-section {
    position: relative;
    width: 100%;
    height: 80vh; /* 80% of viewport height */
    overflow: hidden;
}

.banner-slider {
    position: relative;
    width: 100%;
    height: 100%;
}

.banner-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 1s ease;
    z-index: 1;
}

.banner-slide.active {
    opacity: 1;
    z-index: 2;
}

.banner-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* This will cover the entire area without distorting aspect ratio */
}

/* Navigation Arrows */
.banner-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
    font-size: 20px;
}

.banner-arrow:hover {
    background-color: rgba(0, 0, 0, 0.7);
}

.banner-arrow-left {
    left: 20px;
}

.banner-arrow-right {
    right: 20px;
}

/* Navigation Indicators */
.banner-indicators {
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 10px;
    z-index: 10;
}

.banner-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: all 0.3s ease;
}

.banner-indicator.active {
    background-color: #fff;
    transform: scale(1.2);
}

/* Responsive Styles */
@media (max-width: 768px) {
    .hero-banner-section {
        height: 60vh; /* Smaller height on mobile */
    }
    
    .banner-arrow {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .hero-banner-section {
        height: 50vh; /* Even smaller on very small devices */
    }
    
    .banner-arrow {
        width: 36px;
        height: 36px;
        font-size: 14px;
    }
}
</style>

<script>
// Banner Slider Logic
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.banner-slide');
    const indicators = document.querySelectorAll('.banner-indicator');
    const prevButton = document.getElementById('bannerArrowLeft');
    const nextButton = document.getElementById('bannerArrowRight');
    
    let currentSlide = 0;
    let slideInterval;
    
    // Initialize slider
    function startSlider() {
        slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
    }
    
    // Go to specific slide
    function goToSlide(index) {
        // Remove active class from all slides and indicators
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        // Add active class to current slide and indicator
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
        
        currentSlide = index;
        
        // Reset interval
        clearInterval(slideInterval);
        startSlider();
    }
    
    // Next slide function
    function nextSlide() {
        let next = currentSlide + 1;
        if (next >= slides.length) {
            next = 0;
        }
        goToSlide(next);
    }
    
    // Previous slide function
    function prevSlide() {
        let prev = currentSlide - 1;
        if (prev < 0) {
            prev = slides.length - 1;
        }
        goToSlide(prev);
    }
    
    // Event listeners
    prevButton.addEventListener('click', function() {
        prevSlide();
    });
    
    nextButton.addEventListener('click', function() {
        nextSlide();
    });
    
    // Add click event to indicators
    indicators.forEach(indicator => {
        indicator.addEventListener('click', function() {
            goToSlide(parseInt(this.getAttribute('data-index')));
        });
    });
    
    // Add swipe functionality for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    const slider = document.querySelector('.banner-slider');
    
    slider.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
        clearInterval(slideInterval);
    }, {passive: true});
    
    slider.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
        startSlider();
    }, {passive: true});
    
    function handleSwipe() {
        const swipeThreshold = 50;
        if (touchEndX < touchStartX - swipeThreshold) {
            nextSlide(); // Swipe left
        } else if (touchEndX > touchStartX + swipeThreshold) {
            prevSlide(); // Swipe right
        }
    }
    
    // Start the slider
    startSlider();
});
</script>

<!-- ✅ Trusted Clients Section -->
<section class="trusted-clients-section">
    <!-- ... rest of the file ... -->
</section> 