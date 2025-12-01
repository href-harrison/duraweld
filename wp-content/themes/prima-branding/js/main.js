import Swiper from 'swiper/bundle';
import GLightbox from "glightbox";
import * as AOS from 'aos/dist/aos.js';
/** Include any other scripts here - this will combine them via Webpack for the final output script. */

((window, document, $, undefined) => {

  /*******************************************************************************/
  /* MODULE
  /*******************************************************************************/

  const Base = (() => {

    /**
     * Runs when the document is ready.
     */
    const ready = () => {
      console.log('document ready!');

      AOS.init({
        duration: 1300,
        once: true,
      });

      // const swiper = new Swiper()
    };

    /**
     * Runs when the window is loaded.
     */
    const accordionJS = () => {
      const blocks = $('.accordion-block');
      if(blocks.length) {

        const headings = $('.accordions__single--heading');

        headings.on('click', function () {
          // Checks if current elem is the active one or not
          if($(this).parent().hasClass('active')) {
            // Removes active from current item
            $(this).parent().toggleClass('active')
            $(this).parent().find('.accordions__single--content').stop().fadeToggle()

          } else {
            // Removes existing active state from current active element
            $(this).parents('.accordions').find('.active').find('.accordions__single--content').stop().fadeToggle()
            $(this).parents('.accordions').find('.active').toggleClass('active')
            // Adds active state on clicked item
            $(this).parent().toggleClass('active')
            $(this).parent().find('.accordions__single--content').stop().fadeToggle()

          }
        })
      }
    };

    const teamPopups = (() => {
      if($('.team-grid').length) {
        $('.single-team-member').on('click', function () {
          // Get the data-member attribute value
          const member = $(this).data('member');
          
          // Find the .popup-container with the matching data-content attribute
          const popup = $('.popup-container[data-content="' + member + '"]');
          
          // Add the 'active' class to the matching .popup-container
          popup.addClass('active');
          $('.popup-overlay').addClass('active');

          $('html').addClass('no-scroll');
        });
      }
    })();

    const closeTeamPopup = (() => {
      $('.close-popup').on('click', function() {
        $('.popup-container').removeClass('active');
        $('.popup-overlay').removeClass('active');
        $('html').removeClass('no-scroll');
      })
    })();

    const overlayClick = (() => {
      $('.popup-overlay').on('click', function() {
        $('.popup-container').removeClass('active');
        $('.popup-overlay').removeClass('active');
        $('html').removeClass('no-scroll');
      })
    })();

    $(document).ready(function () {
      $('.single-product-tile').each(function () {
        const $tile = $(this);
        const $overlay = $tile.find('.overlay');
        const $toggle = $tile.find('.toggle-overlay');
    
   
        $tile.hover(
          function () {
            $tile.addClass('active'); 
            $overlay.removeClass('hiding').addClass('active'); 
          },
          function () {
            $tile.removeClass('active');
            $overlay.removeClass('active').addClass('hiding');
            setTimeout(() => $overlay.removeClass('hiding'), 500); 
          }
        );
    
        // Remove .active for both tile and overlay on button click
        $toggle.on('click', function (e) {
          e.stopPropagation(); // Prevent triggering other events
          $tile.removeClass('active');
          $overlay.removeClass('active').addClass('hiding');
          setTimeout(() => $overlay.removeClass('hiding'), 500); 
        });
      });
    });

    // $(document).ready(function () {
    //   function isMobile() {
    //     return window.matchMedia("(max-width: 768px)").matches; // Adjust breakpoint for mobile
    //   }
    
    //   function isDesktop() {
    //     return window.matchMedia("(min-width: 1280px)").matches; // Adjust breakpoint for desktop (larger than 1279px)
    //   }
    
    //   $('.single-product-tile').each(function () {
    //     const $tile = $(this);
    //     const $overlay = $tile.find('.overlay');
    //     const $toggle = $tile.find('.toggle-overlay');
    
    //     function activateTile() {
    //       $tile.addClass('active');
    //       $overlay.removeClass('hiding').addClass('active');
    //     }
    
    //     function deactivateTile() {
    //       $tile.removeClass('active');
    //       $overlay.removeClass('active').addClass('hiding');
    //       setTimeout(() => $overlay.removeClass('hiding'), 500);
    //     }
    
    //     // Hover effect for screens larger than 1279px (Desktop)
    //     if (isDesktop()) {
    //       // Desktop: Use hover for showing the overlay
    //       $tile.hover(activateTile, deactivateTile);
    //     } else {
    //       // Mobile: Prevent overlay activation on tile touch (only toggle with the .toggle-overlay button)
    //       $tile.on('click', function (e) {
    //         e.stopPropagation(); // Prevent bubbling up
    //       });
    
    //       // Button click to toggle overlay visibility on mobile
    //       $toggle.on('click', function (e) {
    //         e.stopPropagation(); // Prevent triggering other events
    //         if ($tile.hasClass('active')) {
    //           deactivateTile();
    //         } else {
    //           activateTile();
    //         }
    //       });
    
    //       // Close overlay when clicking outside
    //       $(document).on('click', function (e) {
    //         if (!$(e.target).closest('.single-product-tile').length) {
    //           deactivateTile();
    //         }
    //       });
    //     }
    //   });
    // });
    
    

    const mobileMenuToggle = (() => {
      $('.hamburger').on('click', function() {
        $('.mobile-nav').toggleClass('open');
        $('html').toggleClass('no-scroll');
        $(this).toggleClass('open');
      });
    })();

    const mobileSubMenuToggle = (() => {
      $('.menu-item-has-children').on('click', function (e) {
        e.stopPropagation(); 
    
        const $clickedItem = $(this);
        const $subMenu = $clickedItem.find('.sub-menu, .mega-menu-container').first();
    
     
        $('.menu-item-has-children')
          .not($clickedItem)
          .removeClass('active')
          .find('.sub-menu, .mega-menu-container')
          .slideUp();
    
  
        $subMenu.slideToggle();
    
        // Toggle the 'active' class
        $clickedItem.toggleClass('active');
      });
    })();



    const load = () => {
      console.log('document load!');
    };

    /**
     * Return our module's publicly accessible functions.
     */
    return {
      ready: ready,
      accordionJS: accordionJS,
      load: load
    };

  })();

  /*******************************************************************************/
  /* MODULE INITIALISE
  /*******************************************************************************/

  jQuery(document).ready(function($) {
    Base.ready();
    Base.accordionJS();
  });

  jQuery(window).on('load', function($) {
    Base.load();
  });

})(window, document, jQuery);
