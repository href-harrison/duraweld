<?php
/**
 * Block Name: Product Filtered Grid
 *
 * Description: Displays child filter terms (e.g., A4, A5 under Size) with images and related products.
 */

/**
 * Block object provided by WordPress
 */
$block = $args['block'] ?? false;

/**
 * Data passed to the block template as an arg and extracted
 * into variables
 */
$data = $args['data'];

$filter_items = $data['filter_items'] ?? [];
$header = $data['header'] ?? false;
$parent_filter = $data['parent_filter'] ?? null;
$toggle_overlay = $data['toggle_overlay'] ?? true;

/**
 * Unique block identifier added to the block
 */
$block_id = $args['block_id'] ?? false;

/**
 * The block class names we passed to the
 * argument for the block
 */
$class_name = $args['class_name'];

if ($block && $block_id && isset($block['ghostkit']['styles']) && $spacings = $block['ghostkit']['styles']) {
    addGhostKitSpacings($spacings, $block_id);
}

?>

<!-- Our front-end template -->
<section
    id="<?php echo esc_attr($block_id); ?>" 
    class="<?php echo esc_attr($class_name); ?>"
>
    <div class="site-container">
        <?php if($header) : ?>
            <h2 class="section-header" data-aos="fade-in"><?php echo esc_html($header); ?></h2>
        <?php endif; ?>
        
        <?php if(!empty($filter_items)) : ?>
            <!-- Filter Terms Grid - styled like product relationship grid -->
            <div class="products-grid">
                <?php 
                foreach($filter_items as $index => $item) : 
                    $term = $item['term'] ?? null;
                    $term_image = $item['image'] ?? null;
                    $filter_link = $item['link'] ?? '#';
                    
                    if (!$term || is_wp_error($term)) {
                        continue; // Skip invalid terms
                    }
                    
                    // Process filter term image (from ACF block settings only)
                    $image_url = '';
                    $image_alt = $term->name;
                    if ($term_image) {
                        if (is_array($term_image)) {
                            $image_url = $term_image['url'] ?? '';
                            $image_alt = $term_image['alt'] ?? $term->name;
                        } elseif (is_numeric($term_image)) {
                            $image_url = wp_get_attachment_image_url($term_image, 'large');
                            $image_alt = get_post_meta($term_image, '_wp_attachment_image_alt', true) ?: $term->name;
                        } else {
                            $image_url = $term_image;
                        }
                    }
                    
                    // If still no image, try to get featured image from first product with this term
                    if (!$image_url) {
                        $products_with_term = get_posts([
                            'post_type' => 'product',
                            'posts_per_page' => 1,
                            'post_status' => 'publish',
                            'tax_query' => [
                                [
                                    'taxonomy' => 'product_filters',
                                    'field' => 'term_id',
                                    'terms' => $term->term_id,
                                ],
                            ],
                        ]);
                        
                        if (!empty($products_with_term)) {
                            $featured_image_id = get_post_thumbnail_id($products_with_term[0]->ID);
                            if ($featured_image_id) {
                                $image_url = wp_get_attachment_image_url($featured_image_id, 'large');
                            }
                        }
                    }
                    ?>
                    
                    <a href="<?php echo esc_url($filter_link); ?>" class="single-product-tile filter-term-tile" data-term-id="<?php echo esc_attr($term->term_id); ?>" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <?php if ($image_url) : ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="desktop-image">
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="mobile-image">
                        <?php else : ?>
                            <div class="filter-term-placeholder">
                                <span class="placeholder-text"><?php echo esc_html($term->name); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="toggle-overlay">
                            <div class="toggle-overlay-background"></div>
                            <h5><?php echo esc_html($term->name); ?></h5>
                            <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="25" cy="25" r="24.5" stroke="#414042"/>
                                <path d="M25 15V35M15 25H35" stroke="#414042" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </a>
                    
                <?php endforeach; 
                ?>
            </div>
        <?php else : ?>
            <p class="no-filters">No filter items found. Please add filter items in the block settings.</p>
        <?php endif; ?>
    </div>
</section>
