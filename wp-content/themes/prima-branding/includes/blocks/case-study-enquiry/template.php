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
$sidebar_header = $data['sidebar_header'];
$form_shortcode = $data['form_shortcode'];
$case_study_relation = $data['case_study_relation'];

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
        <div class="relationship-column">
            <?php if($sidebar_header) : ?>
                <h3 data-aos="fade-in"><?php echo $sidebar_header; ?></h3>
            <?php endif; ?>
            <?php if($case_study_relation) : ?>
                <div class="products">
                    <?php foreach($case_study_relation as $case) : 
                        $thumbnail = get_the_post_thumbnail_url($case->ID, 'medium_large');
                        $title = get_the_title($case->ID); 
                        $excerpt = get_the_excerpt($case->ID);
                        $permalink = get_the_permalink($case->ID);
                        $excerpt_alternative = get_field('excerpt_alternative', $case->ID);
                        ?>
                    
                        <a href="<?php echo $permalink; ?>">
                            <?php if($thumbnail) : ?>
                                <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>">
                            <?php endif; ?>
                            <h4><?php echo $title; ?></h4>
                            <?php if($excerpt_alternative) : ?>
                                <?php echo $excerpt_alternative; ?>
                            <?php elseif($excerpt) : ?>
                                <p><?php echo $excerpt; ?></p>
                            <?php endif; ?>
                            <span class="btn-pb btn-pb--arrow">View study</span>
                        </a>
                    
                   <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="form-column">
            <?php if($header) : ?>
                <h3 data-aos="fade-in"><?php echo $header; ?></h3>
            <?php endif; ?>
            <?php if($form_shortcode) : ?>
                <?php echo do_shortcode($form_shortcode) ; ?>
            <?php endif; ?>
        </div>
    </div>
</section>