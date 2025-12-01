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
$copy = $data['copy'];
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
        <div class="text-column">
            <?php if($header) : ?>
                <h2 data-aos="fade-in"><?php echo $header; ?></h2>
            <?php endif; ?>
            <?php if($copy) : ?>
                <div class="text"  data-aos="fade-in">
                    <?php echo $copy; ?>
                </div>
            <?php endif; ?>
        </div>
        <figure class="image-column">
            <?php if($mobile_image && $image) : ?>
                <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="desktop-image" data-aos="fade-up">
                <img src="<?php echo $mobile_image['url']; ?>" alt="<?php echo $mobile_image['alt']; ?>" class="mobile-image" data-aos="fade-up">
            <?php elseif($image) : ?>
                <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" data-aos="fade-up">
            <?php endif; ?> 
        </figure>
    </div>
</section>