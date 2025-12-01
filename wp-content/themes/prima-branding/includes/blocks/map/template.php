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
$header_icon = $data['header_icon'];
$map_shortcode = $data['map_shortcode'];

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
            <h3 data-aos="fade-in">
                <?php if($header_icon) : ?>
                    <img src="<?php echo $header_icon['url']; ?>" alt="<?php echo $header_icon['alt']; ?>">
                <?php endif; ?>
                <span><?php echo $header; ?></span>
            </h3>
        <?php endif; ?>
        
    </div>
    <?php if($map_shortcode) : ?>
            <div class="map-container">
                <?php echo do_shortcode($map_shortcode); ?>
            </div>
        <?php endif; ?>
</section>