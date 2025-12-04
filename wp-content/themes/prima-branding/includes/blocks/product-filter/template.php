<?php
/**
 * Product Filter Block Template
 */

$block = $args['block'] ?? false;
$data = $args['data'];
$block_id = $args['block_id'] ?? false;
$class_name = $args['class_name'];

$header = $data['header'] ?? false;
$show_size_filter = $data['show_size_filter'] ?? true;
$show_style_filter = $data['show_style_filter'] ?? true;
$size_terms = $data['size_terms'] ?? [];
$style_terms = $data['style_terms'] ?? [];

if ($block && $block_id && isset($block['ghostkit']['styles']) && $spacings = $block['ghostkit']['styles']) {
    addGhostKitSpacings($spacings, $block_id);
}

// Get current category if on taxonomy page
$current_category = null;
if (is_tax('product_category')) {
	$current_category = get_queried_object();
}

?>

<section id="<?php echo $block_id; ?>" class="<?php echo $class_name; ?>" data-product-filter>
    <div class="site-container">
        <?php if($header) : ?>
            <h2 class="section-header" data-aos="fade-in"><?php echo esc_html($header); ?></h2>
        <?php endif; ?>
        
        <div class="product-filters">
            <?php if($show_size_filter && !empty($size_terms) && !is_wp_error($size_terms)) : ?>
                <div class="filter-group filter-size">
                    <h3 class="filter-label">Size</h3>
                    <div class="filter-options-grid">
                        <?php foreach($size_terms as $term) : 
                            $term_id = $term->term_id;
                            $term_name = $term->name;
                            $term_slug = $term->slug;
                        ?>
                            <label class="filter-option-tile">
                                <input 
                                    type="checkbox" 
                                    name="product_size[]" 
                                    value="<?php echo esc_attr($term_slug); ?>"
                                    data-taxonomy="product_size"
                                    data-term-id="<?php echo esc_attr($term_id); ?>"
                                    class="filter-checkbox"
                                >
                                <span class="filter-tile-content">
                                    <?php echo esc_html($term_name); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if($show_style_filter && !empty($style_terms) && !is_wp_error($style_terms)) : ?>
                <div class="filter-group filter-style">
                    <h3 class="filter-label">Style</h3>
                    <div class="filter-options-grid">
                        <?php foreach($style_terms as $term) : 
                            $term_id = $term->term_id;
                            $term_name = $term->name;
                            $term_slug = $term->slug;
                        ?>
                            <label class="filter-option-tile">
                                <input 
                                    type="checkbox" 
                                    name="product_style[]" 
                                    value="<?php echo esc_attr($term_slug); ?>"
                                    data-taxonomy="product_style"
                                    data-term-id="<?php echo esc_attr($term_id); ?>"
                                    class="filter-checkbox"
                                >
                                <span class="filter-tile-content">
                                    <?php echo esc_html($term_name); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="filter-reset" style="display: none;">
            <button type="button" class="reset-filters-btn">Clear Filters</button>
        </div>
    </div>
</section>

