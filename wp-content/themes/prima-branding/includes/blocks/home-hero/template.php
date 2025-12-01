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
</section>