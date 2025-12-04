/******/ (() => { // webpackBootstrap
/*!*************************************************!*\
  !*** ./includes/blocks/product-filter/block.js ***!
  \*************************************************/
/**
 * Product Filter JavaScript
 */

(function () {
  'use strict';

  var initProductFilter = function initProductFilter() {
    var filterSection = document.querySelector('[data-product-filter]');
    if (!filterSection) return;
    var checkboxes = filterSection.querySelectorAll('.filter-checkbox');
    var resetBtn = filterSection.querySelector('.reset-filters-btn');
    var resetContainer = filterSection.querySelector('.filter-reset');
    var productCards = document.querySelectorAll('.products-grid .product-card');
    if (!checkboxes.length || !productCards.length) return;

    // Get all products with their taxonomy data
    var productsData = Array.from(productCards).map(function (card) {
      var productId = card.dataset.productId || card.getAttribute('data-product-id');
      var sizes = card.dataset.productSizes ? card.dataset.productSizes.split(',') : [];
      var styles = card.dataset.productStyles ? card.dataset.productStyles.split(',') : [];
      return {
        element: card,
        id: productId,
        sizes: sizes.map(function (s) {
          return s.trim();
        }),
        styles: styles.map(function (s) {
          return s.trim();
        })
      };
    });

    // Filter function
    var filterProducts = function filterProducts() {
      var selectedSizes = Array.from(filterSection.querySelectorAll('input[name="product_size[]"]:checked')).map(function (cb) {
        return cb.value;
      });
      var selectedStyles = Array.from(filterSection.querySelectorAll('input[name="product_style[]"]:checked')).map(function (cb) {
        return cb.value;
      });
      var hasFilters = selectedSizes.length > 0 || selectedStyles.length > 0;

      // Show/hide reset button
      if (resetContainer) {
        resetContainer.style.display = hasFilters ? 'block' : 'none';
      }

      // Filter products
      productsData.forEach(function (product) {
        var show = true;

        // If sizes are selected, product must have at least one selected size
        if (selectedSizes.length > 0) {
          var hasSelectedSize = product.sizes.some(function (size) {
            return selectedSizes.includes(size);
          });
          if (!hasSelectedSize) {
            show = false;
          }
        }

        // If styles are selected, product must have at least one selected style
        if (selectedStyles.length > 0) {
          var hasSelectedStyle = product.styles.some(function (style) {
            return selectedStyles.includes(style);
          });
          if (!hasSelectedStyle) {
            show = false;
          }
        }

        // Show/hide product
        if (show) {
          product.element.classList.remove('filtered-out');
        } else {
          product.element.classList.add('filtered-out');
        }
      });
    };

    // Reset function
    var resetFilters = function resetFilters() {
      checkboxes.forEach(function (cb) {
        cb.checked = false;
      });
      filterProducts();
    };

    // Event listeners
    checkboxes.forEach(function (checkbox) {
      checkbox.addEventListener('change', filterProducts);
    });
    if (resetBtn) {
      resetBtn.addEventListener('click', resetFilters);
    }
  };

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductFilter);
  } else {
    initProductFilter();
  }
})();
/******/ })()
;