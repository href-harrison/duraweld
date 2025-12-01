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
$address_header = $data['address_header'];
$form_header = $data['form_header'];
$address = $data['address'];
$social_media_repeater = $data['social_media_repeater'];
$form_shortcode = $data['form_shortcode'];
$duraweld_icon = $data['duraweld_icon'];
$duraweld_info = $data['duraweld_info'];

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
           <h1 class="contact-header" data-aos="fade-in"><?php echo $header; ?></h1> 
        <?php endif; ?>

        <?php if($address || $form_shortcode) : ?>
            <div class="contact-container">
                <?php if($address) : ?>
                    <div class="contact-details contact-column" data-aos="fade-up">
                        <?php if($address_header) : ?>
                            <h3><?php echo $address_header; ?></h3>
                        <?php endif; ?>

                        <div class="address">
                            <?php echo $address; ?>
                        </div>

                        <?php if($social_media_repeater) : ?>
                        <div class="contact-sm">
                            <?php foreach($social_media_repeater as $sm) : ?>
                                <a href="<?php echo $sm['link']['url']; ?>" target="<?php echo $sm['link']['target']; ?>">
                                <img src="<?php echo $sm['icon']['url']; ?>" alt="<?php echo $sm['link']['title']; ?>">
                                </a>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="info">
                            <?php if($duraweld_icon) : ?>
                                <img src="<?php echo $duraweld_icon['url']; ?>" alt="<?php echo $duraweld_icon['alt']; ?>">
                            <?php endif; ?>
                            <?php if($duraweld_info) : ?>
                                <?php echo $duraweld_info; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if($form_shortcode) : ?>
                    <div class="contact-form  contact-column" data-aos="fade-up" data-aos-delay="100">
                        <?php if($form_header) : ?>
                            <h3><?php echo $form_header; ?></h3>
                        <?php endif; ?>

                        <div class="form">
                            <?php echo do_shortcode($form_shortcode); ?>
                        </div>
                    </div>
                <?php endif; ?>

                
            </div>
        <?php endif; ?>
    </div>
</section>