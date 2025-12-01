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

$header = $data['header'] ?: get_the_title();

$post_type = get_post_type();

// Retrieve the post type object
$post_type_obj = get_post_type_object($post_type);

$section_header = $data['section_header'] ?: $post_type_obj->labels->name;
$copy = $data['copy'];
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
        <div class="hero-content">
            <div class="hero-content--left">
                <h3 data-aos="fade-in"><?php echo $section_header; ?></h3>
                <h1 data-aos="fade-in"><?php echo $header; ?></h1>

                <?php if($copy) : ?>
                    <div class="text" data-aos="fade-in"><?php echo $copy; ?></div>
                <?php endif; ?>
                <?php if($link) : ?>
                    <a href="<?php echo $link['url']; ?>" class="btn-pb btn-pb--arrow" data-aos="fade-in"><?php echo $link['title']; ?></a>
                <?php endif; ?>
            </div>
            <div class="hero-content--right">
                <?php if($image && $mobile_image) : ?>
                    <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" data-aos="fade-up" class="desktop-image">
                    <img src="<?php echo $mobile_image['url']; ?>" alt="<?php echo $mobile_image['alt']; ?>" data-aos="fade-up" class="mobile-image">
                <?php elseif($image) : ?>
                    <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" data-aos="fade-up" >
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>