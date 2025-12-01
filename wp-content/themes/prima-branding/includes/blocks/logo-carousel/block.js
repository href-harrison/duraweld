import Swiper from 'swiper/bundle';

const logoCarousel = (() => {
    const swiper = new Swiper('.logo-slider', {
        slidesPerView: 2, 
        spaceBetween: 20, 
        centeredSlides: false, 
        // freeMode: true, 
        loop: true,
         autoplay: {
                delay: 3000, 
                disableOnInteraction: false, 
            },
        speed: 800,
        pagination: {
            el: ".logos-pagination",
          },
        breakpoints : {
            760: {
                slidesPerView: 2,
                centeredSlides: false,
                loop: true,
            },
            1020: {
                slidesPerView: 3,
                centeredSlides: false,
                loop: true,
            },
            1200: {
                slidesPerView: 5,
                centeredSlides: false,
                spaceBetween: 35,
                loop: true,
            }
        }
      });
})();