<?php
/**
 * Block Name: Product Categories
 *
 * Description: 4-column product category grid
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
$category_items = $data['category_items'] ?? false;

/**
 * Unique block identifier added to the block
 */
$block_id = $args['block_id'] ?? false;

/**
 * The block class names we passed to the argument for the block
 */
$class_name = $args['class_name'] ?? '';

/**
 * Check if we're in preview/editor mode
 */
$is_preview = $args['is_preview'] ?? false;
$is_editor = $is_preview;

if ($block && $block_id && isset($block['ghostkit']['styles']) && $spacings = $block['ghostkit']['styles']) {
    addGhostKitSpacings($spacings, $block_id);
}

?>

<!-- Our front-end template -->
<section
    id="<?php echo esc_attr($block_id); ?>" 
    class="<?php echo esc_attr($class_name); ?><?php echo $is_editor ? ' is-editor-preview' : ''; ?>"
    <?php if(!$is_editor) : ?>data-aos="fade-in"<?php endif; ?>
>
    <div class="site-container">
        <?php if($category_items && is_array($category_items) && !empty($category_items)) : ?>
            <!-- Desktop Grid -->
            <div class="categories-grid categories-grid-desktop">
                <?php foreach($category_items as $index => $item) :
                    // Initialize variables
                    $image = false;
                    $heading = '';
                    $link = false;
                    
                    // ACF repeater fields return arrays with sub-field keys
                    // Handle both array and object formats
                    if (is_array($item) && !empty($item)) {
                        $image = $item['image'] ?? false;
                        $heading = $item['heading'] ?? '';
                        $link = $item['link'] ?? false;
                    } elseif (is_object($item)) {
                        $image = $item->image ?? false;
                        $heading = $item->heading ?? '';
                        $link = $item->link ?? false;
                    }
                    
                    // Handle image - could be ID, array, or object
                    if ($image) {
                        if (is_numeric($image)) {
                            // Image is an ID, get full attachment data
                            $image = acf_get_attachment($image);
                        } elseif (is_object($image)) {
                            // Convert object to array
                            $image = (array) $image;
                        }
                        // If it's already an array, use it as-is
                    }
                    
                    // Handle link - ACF link field returns array with url, title, target
                    if ($link && !is_array($link) && is_object($link)) {
                        $link = (array) $link;
                    }
                    
                    ?>
                    <div class="category-item" <?php if(!$is_editor) : ?>data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>"<?php endif; ?>>
                        <?php if($link && is_array($link) && !empty($link['url'])) : ?>
                            <a href="<?php echo esc_url($link['url']); ?>" class="category-link" <?php if(!empty($link['target'])) : ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?>>
                        <?php endif; ?>
                        
                        <?php if($image && is_array($image) && !empty($image['url'])) : ?>
                            <div class="category-image">
                                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? $image['title'] ?? $heading); ?>">
                            </div>
                        <?php else : ?>
                            <div class="category-image category-image--placeholder">
                                <span><?php echo esc_html($heading ?: 'Image ' . ($index + 1)); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($heading) : ?>
                            <h3 class="category-heading"><?php echo esc_html($heading); ?></h3>
                        <?php else : ?>
                            <h3 class="category-heading category-heading--placeholder">Category <?php echo $index + 1; ?></h3>
                        <?php endif; ?>
                        
                        <?php if($link && is_array($link) && !empty($link['url'])) : ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Mobile Carousel -->
            <div class="categories-carousel-wrapper">
                <div class="categories-carousel swiper">
                    <div class="swiper-wrapper">
                        <?php foreach($category_items as $index => $item) :
                            // Reuse the same data extraction logic
                            $image = false;
                            $heading = '';
                            $link = false;
                            
                            if (is_array($item) && !empty($item)) {
                                $image = $item['image'] ?? false;
                                $heading = $item['heading'] ?? '';
                                $link = $item['link'] ?? false;
                            } elseif (is_object($item)) {
                                $image = $item->image ?? false;
                                $heading = $item->heading ?? '';
                                $link = $item->link ?? false;
                            }
                            
                            if ($image) {
                                if (is_numeric($image)) {
                                    $image = acf_get_attachment($image);
                                } elseif (is_object($image)) {
                                    $image = (array) $image;
                                }
                            }
                            
                            if ($link && !is_array($link) && is_object($link)) {
                                $link = (array) $link;
                            }
                            ?>
                            <div class="swiper-slide" <?php if(!$is_editor) : ?>data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>"<?php endif; ?>>
                                <div class="category-item">
                                    <?php if($link && is_array($link) && !empty($link['url'])) : ?>
                                        <a href="<?php echo esc_url($link['url']); ?>" class="category-link" <?php if(!empty($link['target'])) : ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?>>
                                    <?php endif; ?>
                                    
                                    <?php if($image && is_array($image) && !empty($image['url'])) : ?>
                                        <div class="category-image">
                                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? $image['title'] ?? $heading); ?>">
                                        </div>
                                    <?php else : ?>
                                        <div class="category-image category-image--placeholder">
                                            <span><?php echo esc_html($heading ?: 'Image ' . ($index + 1)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($heading) : ?>
                                        <h3 class="category-heading"><?php echo esc_html($heading); ?></h3>
                                    <?php else : ?>
                                        <h3 class="category-heading category-heading--placeholder">Category <?php echo $index + 1; ?></h3>
                                    <?php endif; ?>
                                    
                                    <?php if($link && is_array($link) && !empty($link['url'])) : ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Navigation dots -->
                    <div class="swiper-pagination categories-pagination"></div>
                </div>
            </div>
        <?php else : ?>
            <?php if (current_user_can('edit_posts')) : ?>
                <div class="categories-empty">
                    <p><strong>Product Categories Block</strong></p>
                    <p>No category items found. Add items using the repeater field in the block settings.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
