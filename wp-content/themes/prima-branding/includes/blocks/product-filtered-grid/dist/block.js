/******/ (() => { // webpackBootstrap
/*!********************************************************!*\
  !*** ./includes/blocks/product-filtered-grid/block.js ***!
  \********************************************************/
/**
 * Product Filtered Grid Block
 * Displays filter terms in a grid similar to Product Relationship Grid
 * Filter items are clickable and link to taxonomy archive pages
 */

(function () {
  'use strict';

  // No JavaScript needed for this block - links handle navigation
  // But we can initialize AOS if needed

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      // Re-initialize AOS if available
      if (typeof AOS !== 'undefined') {
        AOS.refresh();
      }
    });
  }

  // Re-initialize if new blocks are loaded (for ACF preview mode)
  if (typeof acf !== 'undefined') {
    acf.addAction('render_block_preview', function () {
      setTimeout(function () {
        if (typeof AOS !== 'undefined') {
          AOS.refresh();
        }
      }, 100);
    });
  }
})();
/******/ })()
;