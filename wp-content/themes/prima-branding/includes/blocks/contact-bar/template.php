<?php
/**
 * Block Name: Contact Bar
 *
 * Description: Contact bar with phone and email information
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
$contact_items = $data['contact_items'] ?? false;

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
>
    <div class="site-container">
        <?php if($contact_items && is_array($contact_items) && !empty($contact_items)) : ?>
            <div class="contact-bar-items">
                <?php foreach($contact_items as $index => $item) :
                    // Initialize variables
                    $icon = false;
                    $icon_image = false;
                    $heading = '';
                    $link = false;
                    
                    // ACF repeater fields return arrays with sub-field keys
                    if (is_array($item) && !empty($item)) {
                        $icon = $item['icon'] ?? false;
                        $icon_image = $item['icon_image'] ?? false;
                        $heading = $item['heading'] ?? '';
                        $link = $item['link'] ?? false;
                    } elseif (is_object($item)) {
                        $icon = $item->icon ?? false;
                        $icon_image = $item->icon_image ?? false;
                        $heading = $item->heading ?? '';
                        $link = $item->link ?? false;
                    }
                    
                    // Handle icon_image - could be ID, array, or object
                    if ($icon_image) {
                        if (is_numeric($icon_image)) {
                            $icon_image = acf_get_attachment($icon_image);
                        } elseif (is_object($icon_image)) {
                            $icon_image = (array) $icon_image;
                        }
                    }
                    
                    // Handle link - ACF link field returns array with url, title, target
                    if ($link && !is_array($link) && is_object($link)) {
                        $link = (array) $link;
                    }
                    
                    // Get link URL and determine if it's tel: or mailto:
                    $link_url = '';
                    $link_type = '';
                    if ($link && is_array($link) && !empty($link['url'])) {
                        $link_url = esc_url($link['url']);
                        if (strpos($link_url, 'tel:') === 0) {
                            $link_type = 'tel';
                        } elseif (strpos($link_url, 'mailto:') === 0) {
                            $link_type = 'email';
                        }
                    }
                    ?>
                    <div class="contact-item" data-aos="fade-in" data-aos-delay="<?php echo $index * 100; ?>">
                        <?php if($link_url) : ?>
                            <a href="<?php echo $link_url; ?>" class="contact-link" <?php if(!empty($link['target'])) : ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?>>
                        <?php endif; ?>
                        
                        <?php if($icon_image || $icon) : ?>
                            <div class="contact-icon">
                                <?php 
                                // Priority: custom icon image > icon selection
                                if ($icon_image && is_array($icon_image) && !empty($icon_image['url'])) {
                                    // Custom icon image
                                    echo '<img src="' . esc_url($icon_image['url']) . '" alt="' . esc_attr($icon_image['alt'] ?? '') . '">';
                                } else {
                                    // Icon selection or default based on link type
                                    $icon_name = strtolower($icon ?? '');
                                    if ($icon_name === 'phone' || $link_type === 'tel') {
                                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>';
                                    } elseif ($icon_name === 'email' || $link_type === 'email') {
                                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>';
                                    }
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="contact-content">
                            <?php if($heading) : ?>
                                <span class="contact-heading"><?php echo esc_html($heading); ?></span>
                            <?php endif; ?>
                            
                            <?php if($link && is_array($link) && !empty($link['title'])) : ?>
                                <span class="contact-value"><?php echo esc_html($link['title']); ?></span>
                            <?php elseif($link_url) : ?>
                                <span class="contact-value"><?php echo esc_html(str_replace(['tel:', 'mailto:'], '', $link_url)); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($link_url) : ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <?php if (current_user_can('edit_posts')) : ?>
                <div class="contact-bar-empty">
                    <p><strong>Contact Bar Block</strong></p>
                    <p>No contact items found. Add items using the repeater field in the block settings.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

