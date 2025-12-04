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

      // Initialize mega menu (reorganizes menu structure into two columns)
      initMegaMenu();

      // Initialize product filter
      initProductFilter();

      // const swiper = new Swiper()
    };
    
    /**
     * Initialize product filter functionality
     */
    const initProductFilter = () => {
      const filterSection = document.querySelector('[data-product-filter]');
      if (!filterSection) return;
      
      const checkboxes = filterSection.querySelectorAll('.filter-checkbox');
      const resetBtn = filterSection.querySelector('.reset-filters-btn');
      const resetContainer = filterSection.querySelector('.filter-reset');
      const productCards = document.querySelectorAll('.products-grid .product-card, .products-grid .single-product-tile');
      
      if (!checkboxes.length || !productCards.length) return;
      
      // Get all products with their taxonomy data
      const productsData = Array.from(productCards).map(card => {
        const productId = card.dataset.productId || card.getAttribute('data-product-id');
        const sizes = card.dataset.productSizes ? card.dataset.productSizes.split(',').map(s => s.trim()).filter(s => s) : [];
        const styles = card.dataset.productStyles ? card.dataset.productStyles.split(',').map(s => s.trim()).filter(s => s) : [];
        
        return {
          element: card,
          id: productId,
          sizes: sizes,
          styles: styles
        };
      });
      
      // Filter function
      const filterProducts = () => {
        const selectedSizes = Array.from(filterSection.querySelectorAll('input[name="product_size[]"]:checked'))
          .map(cb => cb.value);
        const selectedStyles = Array.from(filterSection.querySelectorAll('input[name="product_style[]"]:checked'))
          .map(cb => cb.value);
        
        const hasFilters = selectedSizes.length > 0 || selectedStyles.length > 0;
        
        // Show/hide reset button
        if (resetContainer) {
          resetContainer.style.display = hasFilters ? 'block' : 'none';
        }
        
        // Filter products
        productsData.forEach(product => {
          let show = true;
          
          // If sizes are selected, product must have at least one selected size
          if (selectedSizes.length > 0) {
            const hasSelectedSize = product.sizes.some(size => selectedSizes.includes(size));
            if (!hasSelectedSize) {
              show = false;
            }
          }
          
          // If styles are selected, product must have at least one selected style
          if (selectedStyles.length > 0) {
            const hasSelectedStyle = product.styles.some(style => selectedStyles.includes(style));
            if (!hasSelectedStyle) {
              show = false;
            }
          }
          
          // Show/hide product
          if (show) {
            product.element.classList.remove('filtered-out');
            product.element.style.display = '';
          } else {
            product.element.classList.add('filtered-out');
            product.element.style.display = 'none';
          }
        });
      };
      
      // Reset function
      const resetFilters = () => {
        checkboxes.forEach(cb => {
          cb.checked = false;
        });
        filterProducts();
      };
      
      // Event listeners
      checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
          // Toggle checked class on parent label for styling
          const label = this.closest('.filter-option-tile');
          if (label) {
            if (this.checked) {
              label.classList.add('checked');
            } else {
              label.classList.remove('checked');
            }
          }
          filterProducts();
        });
      });
      
      if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
      }
    };

    /**
     * Position child product menu items at top of second column
     */
    /**
     * Mega Menu Initialization
     * Reorganizes nested WordPress menu structure into two-column layout
     * Shows child products in column 2 only when their parent category is hovered
     */
    const initMegaMenu = () => {
      // Find all mega menu containers
      $('.site-header__menu > li.mega-menu > .sub-menu').each(function() {
        const $megaMenu = $(this);
        
        // Remove any existing event handlers to prevent duplicates
        $megaMenu.off('mouseenter mouseleave');
        $megaMenu.find('> li').not('.child-sub-menu-item').off('mouseenter mouseleave');
        
        // Get all parent categories (direct children of mega menu, excluding child products)
        // This gets items that are NOT child-sub-menu-item
        const $parentCategories = $megaMenu.find('> li').filter(function() {
          return !$(this).hasClass('child-sub-menu-item');
        });
        
        // Calculate row positions: parents and children need to align properly
        // Strategy: Each parent-child group takes max(1, num_children) rows
        // Parent is in column 1, children start at same row in column 2
        let currentRow = 1;
        
        // Process each parent category to extract and link its children
        $parentCategories.each(function(index) {
          const $parentCategory = $(this);
          const $parentSubMenu = $parentCategory.find('> .sub-menu');
          const $childProducts = $parentSubMenu.find('> li.child-sub-menu-item');
          
          const numChildren = $childProducts.length;
          const parentRow = currentRow;
          
          // Position parent in column 1 at current row
          $parentCategory.css({
            'grid-column': '1 / 2',
            'grid-row': parentRow
          });
          
          // Process children for this parent
          if (numChildren > 0) {
            $childProducts.each(function(childIndex) {
              const $child = $(this);
              
              // Store parent reference
              $child.data('parent-index', index);
              $child.data('parent-category', $parentCategory);
              $child.data('parent-row', parentRow);
              
              // Add class for easier selection
              $child.addClass('child-of-parent-' + index);
              
              // Move to mega menu (detach first to preserve event handlers)
              $child.detach().appendTo($megaMenu);
              
              // Position in column 2, starting at same row as parent
              // First child aligns with parent, subsequent children stack below
              const childRow = parentRow + childIndex;
              
              $child.css({
                'grid-column': '2 / 3',
                'grid-row': childRow
              }).removeClass('visible');
              
              // Make sure the class is present
              if (!$child.hasClass('child-sub-menu-item')) {
                $child.addClass('child-sub-menu-item');
              }
            });
            
            // Move to next available row: use the maximum rows needed
            // If parent has children, we need at least as many rows as children
            // If no children, parent takes 1 row
            currentRow += Math.max(1, numChildren);
          } else {
            // No children, parent takes 1 row
            currentRow += 1;
          }
        });
        
        // Debug: Log what we found
        if (window.console && console.log) {
          console.log('Mega Menu Init:', {
            parentCount: $parentCategories.length,
            childCount: allChildProducts.length,
            megaMenu: $megaMenu[0]
          });
        }
        
        // Set up hover handlers for parent categories
        $parentCategories.on('mouseenter', function() {
          const $hoveredParent = $(this);
          const parentIndex = $parentCategories.index($hoveredParent);
          
          // Hide all child products first (remove visible class)
          $megaMenu.find('> li.child-sub-menu-item').removeClass('visible');
          
          // Show only child products belonging to the hovered parent
          $megaMenu.find('> li.child-sub-menu-item.child-of-parent-' + parentIndex).addClass('visible');
        });
        
        // When mouse leaves a parent category, hide its children
        $parentCategories.on('mouseleave', function() {
          const $leftParent = $(this);
          const parentIndex = $parentCategories.index($leftParent);
          $megaMenu.find('> li.child-sub-menu-item.child-of-parent-' + parentIndex).removeClass('visible');
        });
        
        // When hovering over child products area, keep them visible
        $megaMenu.find('> li.child-sub-menu-item').on('mouseenter', function() {
          $(this).addClass('visible');
        });
        
        // When mouse leaves child product, hide it
        $megaMenu.find('> li.child-sub-menu-item').on('mouseleave', function() {
          $(this).removeClass('visible');
        });
      });
    };
    
    // Initialize on page load
    initMegaMenu();
    
    // Re-initialize when mega menu is hovered (in case menu structure changes)
    $(document).on('mouseenter', '.site-header__menu > li.mega-menu', function() {
      setTimeout(initMegaMenu, 50);
    });
    
    // Re-initialize if menu is dynamically updated
    $(document).on('DOMNodeInserted', '.site-header__menu', function() {
      setTimeout(initMegaMenu, 100);
    });

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
        const $mobileNav = $('.mobile-nav');
        const $siteLogo = $('.site-logo');
        const isOpening = !$mobileNav.hasClass('open');
        
        $mobileNav.toggleClass('open');
        $('html').toggleClass('no-scroll');
        $(this).toggleClass('open');
        
        if (isOpening) {
          // Menu is opening - hide logo with fade up animation
          $siteLogo.addClass('logo-hidden');
        } else {
          // Menu is closing - show logo with fade in from top animation
          $siteLogo.removeClass('logo-hidden');
        }
      });
    })();

    const mobileSubMenuToggle = (() => {
      // Handle clicks on menu items with children (scoped to mobile nav only)
      // Use event delegation to handle dynamically added menus
      $(document).on('click', '.mobile-nav .menu-item-has-children', function (e) {
        const $clickedItem = $(this);
        const $link = $clickedItem.find('> a').first();
        const $subMenu = $clickedItem.find('> .sub-menu, > .mega-menu-container').first();
    
        // Check if this is a sub-menu item (nested inside another sub-menu)
        const isSubMenuItem = $clickedItem.closest('.sub-menu').length > 0;
        
        if (isSubMenuItem && $subMenu.length > 0) {
          // This is a sub-menu item with grandchildren - check if click is in icon area
          const liOffset = $clickedItem.offset();
          const clickX = e.pageX - liOffset.left;
          
          if (clickX <= 60) {
            // Click is in icon area (first 60px) - prevent navigation and toggle grandchildren
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle grandchildren submenu
            $subMenu.slideToggle();
            
            // Toggle the 'active' class
            $clickedItem.toggleClass('active');
            return false;
          }
          // Otherwise, allow normal link navigation for sub-menu items
          return true;
        }
        
        // This is a top-level menu item - toggle its submenu (prevent navigation)
        if ($subMenu.length > 0) {
          e.preventDefault();
          e.stopPropagation();
          
          // Only close sibling menu items at the same level, not parent menus
          $clickedItem.siblings('.menu-item-has-children')
          .removeClass('active')
            .find('> .sub-menu, > .mega-menu-container')
          .slideUp();
    
          // Toggle the direct submenu
        $subMenu.slideToggle();
    
        // Toggle the 'active' class
        $clickedItem.toggleClass('active');
          
          return false;
        }
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
