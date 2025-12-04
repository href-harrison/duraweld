// Read more functionality for copy section on mobile
document.addEventListener('DOMContentLoaded', () => {
    const readMoreLinks = document.querySelectorAll('.read-more-link');
    
    readMoreLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            const copyId = link.getAttribute('data-copy-id');
            const expandedContent = link.previousElementSibling;
            const copyExcerpt = link.closest('.copy')?.querySelector('.copy-excerpt');
            const ellipsis = copyExcerpt?.querySelector('.copy-ellipsis');
            
            if (expandedContent && expandedContent.classList.contains('copy-content-expanded')) {
                const isExpanded = expandedContent.style.display !== 'none';
                
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
