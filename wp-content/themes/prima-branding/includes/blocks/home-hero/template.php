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
        <div class="home-hero--image-wrapper" style="width: 100vw; max-width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); height: 557px; max-height: 557px; overflow: hidden; position: relative;">
            <?php if($image_url && $mobile_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="desktop-image" data-aos="fade-up" style="display: none; width: 100%; height: 557px; max-height: 557px; object-fit: cover; object-position: center;">
                <img src="<?php echo esc_url($mobile_url); ?>" alt="<?php echo esc_attr($mobile_alt); ?>" class="mobile-image" data-aos="fade-up" style="display: block; width: 100%; height: auto;">
                <style>
                    @media (min-width: 768px) {
                        .home-hero--image-only .home-hero--image-wrapper .mobile-image {
                            display: none !important;
                            visibility: hidden !important;
                            opacity: 0 !important;
                            height: 0 !important;
                            width: 0 !important;
                            overflow: hidden !important;
                            position: absolute !important;
                            pointer-events: none !important;
                        }
                        .home-hero--image-only .home-hero--image-wrapper .desktop-image {
                            display: block !important;
                            visibility: visible !important;
                            opacity: 1 !important;
                            width: 100% !important;
                            height: 557px !important;
                            max-height: 557px !important;
                            object-fit: cover !important;
                            object-position: center !important;
                        }
                        .home-hero--image-only .home-hero--image-wrapper {
                            height: 557px !important;
                            max-height: 557px !important;
                        }
                    }
                </style>
            <?php elseif($image_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" data-aos="fade-up" style="width: 100%; height: 557px; max-height: 557px; object-fit: cover; object-position: center;">
            <?php elseif($mobile_url) : ?>
                <img src="<?php echo esc_url($mobile_url); ?>" alt="<?php echo esc_attr($mobile_alt); ?>" data-aos="fade-up" style="width: 100%; height: auto;">
            <?php endif; ?>
        </div>
    <?php else : ?>
        <!-- Default variant: with headline and CTA -->
        <div class="site-container">
            <div class="home-hero--left">
                <div class="text">
                    <?php if($header) : ?>
                        <h1 data-aos="fade-in"><?php echo $header; ?></h1>
                    <?php endif; ?>
                    <?php if($link) : ?>
                        <a href="<?php echo $link['url']; ?>" class="btn-pb btn-pb--arrow" data-aos="fade-in"><?php echo $link['title']; ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="home-hero--right">
                    <?php if($image && $mobile_image) : ?>
                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="desktop-image" data-aos="fade-up">
                        <img src="<?php echo $mobile_image['url']; ?>" alt="<?php echo $mobile_image['alt']; ?>" class="mobile-image" data-aos="fade-up">
                    <?php elseif($image) : ?>
                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" data-aos="fade-up">
                    <?php endif; ?>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="27" viewBox="0 0 52 27" fill="none" class="shape">
            <path d="M26 0L51.9808 26.25H0.0192375L26 0Z" fill="white"/>
            </svg>
        </div>
    <?php endif; ?>
</section>