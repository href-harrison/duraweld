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

$benefits_repeater = $data['benefits_repeater'];

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
        <?php if($benefits_repeater) : ?>
            <div class="benefits-list">
                <?php foreach($benefits_repeater as $index=>$benefit) : 
                        $icon = $benefit['icon']; 
                        $header = $benefit['header']; 
                        
                        
                    ?>
                    <div class="single-benefit" data-aos="fade-in" data-aos-delay="<?php echo $index * 100; ?>">
                        <?php if($icon) : ?>
                            <img src="<?php echo $icon['url']; ?>" alt="<?php echo $icon['alt']; ?>">
                        <?php endif; ?>
                        <?php if($header) : ?>
                            <h5><?php echo $header; ?></h5>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>