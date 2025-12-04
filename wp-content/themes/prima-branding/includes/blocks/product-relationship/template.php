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

$product_relationship = $data['product_relationship'];
$header = $data['header'] ?? false;
$toggle_overlay = $data['toggle_overlay'] ?? true;

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
            <h2 class="section-header" data-aos="fade-in"><?php echo esc_html($header); ?></h2>
        <?php endif; ?>
        <?php if($product_relationship) : ?>
            <div class="products-grid <?php echo $toggle_overlay ? 'has-toggle-overlay' : 'no-toggle-overlay'; ?>">
                <?php foreach($product_relationship as $index=>$product) : 

                        get_template_part('template-parts/cards/product', 'card', [
                            'post_id' => $product->ID,
                            'index' => $index,
                            'toggle_overlay' => $toggle_overlay,
                        ]); ?>

                        
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>