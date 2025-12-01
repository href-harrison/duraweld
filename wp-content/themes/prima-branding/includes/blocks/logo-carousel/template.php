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
$logo_repeater = $data['logo_repeater'];

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
    <?php if($header) : ?>
            <h2 data-aos="fade-in"><?php echo $header; ?></h2>
        <?php endif; ?>

        <?php if($logo_repeater) : ?> 
            <div class="logo-slider">
                <div class="swiper-wrapper">
                    <?php foreach($logo_repeater as $index=>$logo) : ?>
                        <?php if($logo['link']) : ?>
                            <a href="<?php echo $logo['link']['url']; ?>" target="<?php echo $logo['link']['target']; ?>" class="swiper-slide single-logo">
                                <img src="<?php echo $logo['logo']['url']; ?>" alt="<?php echo $logo['logo']['alt']; ?>">
                            </a>
                        <?php else : ?>
                            <div class="swiper-slide single-logo">
                                <img src="<?php echo $logo['logo']['url']; ?>" alt="<?php echo $logo['logo']['alt']; ?>">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="swiper-pagination logos-pagination"></div>
        <?php endif; ?>
    </div>
</section>