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
$link = $data['link'];
$image = $data['image'];
$mobile_image = $data['mobile_image'];
$reverse_layout = $data['reverse_layout'];
$is_quote = $data['is_quote'];

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
        <div class="split-content <?php if($reverse_layout) : ?> reversed <?php endif; ?>">
            <div class="split-content--left <?php if($is_quote) : ?> quote <?php endif; ?>">
                <?php if($header) : ?>
                    <h2 data-aos="fade-in"><?php echo $header; ?></h2>
                    
                <?php endif; ?>
                <?php if($copy) : ?>
                    <div class="text" data-aos="fade-in">
                        <?php echo $copy; ?>
                    </div>
                <?php endif; ?>
                <?php if($link) : ?>
                    <a href="<?php echo $link['url']; ?>" class="btn-pb btn-pb--arrow" data-aos="fade-in"><?php echo $link['title']; ?></a>
                <?php endif; ?>

            </div>
            <div class="split-content--right">
                <?php if($image && $mobile_image) : ?>
                    <figure data-aos="fade-up">
                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="desktop-image">
                        <img src="<?php echo $mobile_image['url']; ?>" alt="<?php echo $mobile_image['alt']; ?>" class="mobile-image">
                    </figure>
                <?php elseif($image) : ?> 
                    <figure data-aos="fade-up">
                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" >
                    </figure>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>