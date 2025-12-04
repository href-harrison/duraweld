import Swiper from 'swiper/bundle';

const productCategoriesCarousel = (() => {
    // Only initialize on mobile phones in portrait (not landscape, not tablets)
    const initCarousel = () => {
        const isMobilePortrait = window.innerWidth <= 767 && window.innerHeight > window.innerWidth;
        const carouselWrapper = document.querySelector('.categories-carousel-wrapper');
        const carousel = document.querySelector('.categories-carousel.swiper');
        
        if (!isMobilePortrait || !carouselWrapper || !carousel) {
            // Destroy existing Swiper if it exists
            if (carousel && carousel.swiper) {
                carousel.swiper.destroy(true, true);
            }
            return;
        }
        
        // Initialize Swiper
        const swiper = new Swiper('.categories-carousel.swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            centeredSlides: true,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            speed: 800,
            pagination: {
                el: ".categories-pagination",
                clickable: true,
            },
        });
    };
    
    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCarousel);
    } else {
        initCarousel();
    }
    
    // Re-initialize on window resize/orientation change
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            initCarousel();
        }, 250);
    });
    
    // Handle orientation change
    window.addEventListener('orientationchange', () => {
        setTimeout(() => {
            initCarousel();
        }, 100);
    });
})();
