/**
 * Product Filter JavaScript
 */

(function() {
    'use strict';
    
    const initProductFilter = () => {
        const filterSection = document.querySelector('[data-product-filter]');
        if (!filterSection) return;
        
        const checkboxes = filterSection.querySelectorAll('.filter-checkbox');
        const resetBtn = filterSection.querySelector('.reset-filters-btn');
        const resetContainer = filterSection.querySelector('.filter-reset');
        const productCards = document.querySelectorAll('.products-grid .product-card');
        
        if (!checkboxes.length || !productCards.length) return;
        
        // Get all products with their taxonomy data
        const productsData = Array.from(productCards).map(card => {
            const productId = card.dataset.productId || card.getAttribute('data-product-id');
            const sizes = card.dataset.productSizes ? card.dataset.productSizes.split(',') : [];
            const styles = card.dataset.productStyles ? card.dataset.productStyles.split(',') : [];
            
            return {
                element: card,
                id: productId,
                sizes: sizes.map(s => s.trim()),
                styles: styles.map(s => s.trim())
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
                } else {
                    product.element.classList.add('filtered-out');
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

