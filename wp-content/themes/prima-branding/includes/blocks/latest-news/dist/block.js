/******/ (() => { // webpackBootstrap
/*!**********************************************!*\
  !*** ./includes/blocks/latest-news/block.js ***!
  \**********************************************/
// Read more functionality for copy section on mobile
document.addEventListener('DOMContentLoaded', function () {
  var readMoreLinks = document.querySelectorAll('.read-more-link');
  readMoreLinks.forEach(function (link) {
    link.addEventListener('click', function (e) {
      var _link$closest;
      e.preventDefault();
      var copyId = link.getAttribute('data-copy-id');
      var expandedContent = link.previousElementSibling;
      var copyExcerpt = (_link$closest = link.closest('.copy')) === null || _link$closest === void 0 ? void 0 : _link$closest.querySelector('.copy-excerpt');
      var ellipsis = copyExcerpt === null || copyExcerpt === void 0 ? void 0 : copyExcerpt.querySelector('.copy-ellipsis');
      if (expandedContent && expandedContent.classList.contains('copy-content-expanded')) {
        var isExpanded = expandedContent.style.display !== 'none';
        if (isExpanded) {
          // Collapse
          expandedContent.style.display = 'none';
          link.textContent = 'Read more';
          link.classList.remove('expanded');
          // Show ellipsis
          if (ellipsis) {
            ellipsis.style.display = 'inline';
          }
        } else {
          // Expand
          expandedContent.style.display = 'block';
          link.textContent = 'Read less';
          link.classList.add('expanded');
          // Hide ellipsis
          if (ellipsis) {
            ellipsis.style.display = 'none';
          }
        }
      }
    });
  });
});
/******/ })()
;