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
    <?php if($image && $mobile_image) : ?>
        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="image-desktop">
        <img src="<?php echo $mobile_image['url']; ?>" alt="<?php echo $mobile_image['alt']; ?>" class="image-mobile">
    <?php elseif($image) : ?>
        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
    <?php endif; ?>
</section>