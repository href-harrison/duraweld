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
        <?php if($header) : ?>
            <h3 data-aos="fade-in"><?php echo $header; ?></h3>
        <?php endif; ?>
        <?php if($case_study_relation) : ?>
            <div class="cases-grid">
                <?php foreach($case_study_relation as $index=>$case) : 
                     get_template_part('template-parts/cards/case-study', 'card', [
                        'post_id' => $case->ID,
                        'index' => $index,
                    ]); ?>
                    
                
                    
                
                <?php endforeach; ?>
            </div>
        <?php else : 
        $current_post_id = get_the_ID();

        // Set up the query arguments
        $args = array(
            'post_type'      => 'case-study', // Custom post type
            'posts_per_page' => 3,           // Limit to 3 posts
            'post__not_in'   => array($current_post_id), // Exclude current post
            'orderby'        => 'date',      // Order by date (default)
            'order'          => 'DESC',      // Latest posts first
        );
        
        // Custom query
        $case_studies_query = new WP_Query($args);
        
        if ($case_studies_query->have_posts()) :
            $index = 0; ?>
            <div class="cases-grid">
            <?php while ($case_studies_query->have_posts()) : $case_studies_query->the_post();
            get_template_part('template-parts/cards/case-study', 'card', [
                'post_id' => get_the_ID(),
                'index' => $index,
            ]);
            endwhile;
            wp_reset_postdata(); ?>
            </div>
        <?php else :
            ?>
            <p>No case studies found.</p>
            <?php
        endif;
        ?>
        <?php endif; ?>
    </div>
</section>