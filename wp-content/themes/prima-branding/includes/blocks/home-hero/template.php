<?php
/**
 * Block Name: My First Block
 *
 * Description: Displays my very first block.
 */

/**
 * Block object provided by Wordpress
 */
$block = $args['block'] ?? false;

/**
 * Data passed to the block template as an arg and extracted
 * into variables
 */
$data = $args['data'];

$variant = $data['variant'] ?? 'default';
$header = $data['header'];
$body_text = $data['body_text'] ?? false;
$link = $data['link'];
$image = $data['image'];
$mobile_image = $data['mobile_image'];

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
    id="<?php echo $block_id; ?>" 
    class="<?php echo $class_name; ?>"
>
    <?php if ($variant === 'image-only') : ?>
        <!-- Image-only variant: full-width hero image -->
        <?php 
        // Try to get images directly from block data if not already set
        if (empty($image) && !empty($block['data']['image'])) {
            $image = $block['data']['image'];
        }
        if (empty($mobile_image) && !empty($block['data']['mobile_image'])) {
            $mobile_image = $block['data']['mobile_image'];
        }
        
        // Handle if image is just an ID
        if (!empty($image) && is_numeric($image)) {
            $image = acf_get_attachment($image);
        }
        if (!empty($mobile_image) && is_numeric($mobile_image)) {
            $mobile_image = acf_get_attachment($mobile_image);
        }
        
        // Get image URLs - handle both array and object formats
        $image_url = '';
        $mobile_url = '';
        $image_alt = '';
        $mobile_alt = '';
        
        if (!empty($image)) {
            if (is_array($image)) {
                $image_url = $image['url'] ?? '';
                $image_alt = $image['alt'] ?? $image['title'] ?? '';
            } elseif (is_object($image)) {
                $image_url = $image->url ?? '';
                $image_alt = $image->alt ?? $image->title ?? '';
            }
        }
        
        if (!empty($mobile_image)) {
            if (is_array($mobile_image)) {
                $mobile_url = $mobile_image['url'] ?? '';
                $mobile_alt = $mobile_image['alt'] ?? $mobile_image['title'] ?? '';
            } elseif (is_object($mobile_image)) {
                $mobile_url = $mobile_image->url ?? '';
                $mobile_alt = $mobile_image->alt ?? $mobile_image->title ?? '';
            }
        }
        ?>
        <div class="home-hero--image-wrapper">
            <?php if($image_url && $mobile_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="desktop-image" data-aos="fade-up">
                <img src="<?php echo esc_url($mobile_url); ?>" alt="<?php echo esc_attr($mobile_alt); ?>" class="mobile-image" data-aos="fade-up">
            <?php elseif($image_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" data-aos="fade-up">
            <?php elseif($mobile_url) : ?>
                <img src="<?php echo esc_url($mobile_url); ?>" alt="<?php echo esc_attr($mobile_alt); ?>" data-aos="fade-up">
            <?php endif; ?>
        </div>
    <?php else : ?>
        <!-- Default variant: with headline and CTA - matching product grid overlay style (static overlay) -->
        <?php 
        // Handle image - could be ID, array, or object
        $image_url = '';
        $mobile_url = '';
        $image_alt = '';
        $mobile_alt = '';
        
        if (!empty($image)) {
            if (is_numeric($image)) {
                $image = acf_get_attachment($image);
            }
            if (is_array($image)) {
                $image_url = $image['url'] ?? '';
                $image_alt = $image['alt'] ?? $image['title'] ?? '';
            } elseif (is_object($image)) {
                $image_url = $image->url ?? '';
                $image_alt = $image->alt ?? $image->title ?? '';
            }
        }
        
        if (!empty($mobile_image)) {
            if (is_numeric($mobile_image)) {
                $mobile_image = acf_get_attachment($mobile_image);
            }
            if (is_array($mobile_image)) {
                $mobile_url = $mobile_image['url'] ?? '';
                $mobile_alt = $mobile_image['alt'] ?? $mobile_image['title'] ?? '';
            } elseif (is_object($mobile_image)) {
                $mobile_url = $mobile_image->url ?? '';
                $mobile_alt = $mobile_image->alt ?? $mobile_image->title ?? '';
            }
        }
        ?>
        <div class="home-hero--image-wrapper">
            <?php if($image_url || $mobile_url) : ?>
                <?php if($image_url && $mobile_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="desktop-image" data-aos="fade-up">
                    <img src="<?php echo esc_url($mobile_url); ?>" alt="<?php echo esc_attr($mobile_alt); ?>" class="mobile-image" data-aos="fade-up">
                <?php elseif($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" data-aos="fade-up">
                <?php elseif($mobile_url) : ?>
                    <img src="<?php echo esc_url($mobile_url); ?>" alt="<?php echo esc_attr($mobile_alt); ?>" data-aos="fade-up">
                <?php endif; ?>
            <?php endif; ?>
            <!-- Dark overlay (static, always visible) -->
            <div class="home-hero--overlay">
                <div class="text">
                    <?php if($header) : ?>
                        <h2 data-aos="fade-in"><?php echo esc_html($header); ?></h2>
                    <?php endif; ?>
                    <?php if ($body_text) : ?>
                        <p data-aos="fade-in" data-aos-delay="100"><?php echo wp_kses_post($body_text); ?></p>
                    <?php endif; ?>
                    <?php if($link && is_array($link) && !empty($link['url'])) : ?>
                        <a href="<?php echo esc_url($link['url']); ?>" class="btn-pb--arrow" data-aos="fade-in" data-aos-delay="200" <?php if(!empty($link['target'])) : ?>target="<?php echo esc_attr($link['target']); ?>"<?php endif; ?>><?php echo esc_html($link['title'] ?? 'Find out more'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>