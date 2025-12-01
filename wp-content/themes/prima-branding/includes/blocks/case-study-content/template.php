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

$sidebar_header_1 = $data['sidebar_header_1'];
$sidebar_header_2 = $data['sidebar_header_2'];
$case_studies_relation = $data['case_studies_relation'];
$client_name = $data['client_name'];
$brief = $data['brief'];
$date = $data['date'];
$case_study_content = $data['case_study_content'];
$header = $data['header'];
$client_logo = $data['client_logo'];

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
        <div class="sidebar">
            <div class="case-details">
                <?php if($sidebar_header_1) : ?>
                    <h3 class="sidebar-header"><?php echo $sidebar_header_1; ?></h3>
                <?php endif; ?>
                <?php if($client_logo) : ?>
                    <img src="<?php echo $client_logo['url']; ?>" alt="<?php echo $client_logo['alt']; ?>" class="client-logo">
                <?php endif; ?>
                <?php if($client_name) : ?>
                    <div class="info">
                        <p><span>Client name:</span> <?php echo $client_name; ?></p>
                    </div>
                <?php endif; ?>
                <?php if($date) : ?>
                    <div class="info">
                        <p><span>Date:</span> <?php echo $date; ?></p>
                    </div>
                <?php endif; ?>
                <?php if($brief) : ?>
                    <div class="info brief">
                        <p><span>Brief:</span> 
                        <?php echo $brief; ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="related-case-studies cs-desktop">
                <?php if($sidebar_header_2) : ?>
                    <h3 class="sidebar-header"><?php echo $sidebar_header_2; ?></h3>
                <?php endif; ?>
                <?php if($case_studies_relation) : ?>
                    <div class="studies-grid">
                        <?php foreach($case_studies_relation as $case) : 
                            $thumbnail = get_the_post_thumbnail_url($case->ID, 'medium_large'); 
                            $title = get_the_title($case->ID);
                            $permalink = get_the_permalink($case->ID);
                            ?>
                            <a class="single-case" href="<?php echo $permalink; ?>">
                                <?php if($thumbnail) : ?>
                                    <figure>
                                        <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>">
                                    </figure>
                                    <h4><?php echo $title; ?></h4>
                                    <span class="btn-pb btn-pb--arrow">
                                        View study
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="studies-grid">
                    <?php
                        // Get the current post ID
                        $current_post_id = get_the_ID();

                        // Query arguments
                        $args = array(
                            'post_type'      => 'case-study', // Custom post type
                            'posts_per_page' => 2,            // Limit to 2 posts
                            'post__not_in'   => array($current_post_id), // Exclude the current post
                            'orderby'        => 'date',       // Order by date
                            'order'          => 'DESC',       // Most recent posts first
                        );

                        // The custom query
                        $case_study_query = new WP_Query($args);

                        // Loop through the results
                        if ($case_study_query->have_posts()) :
                         
                            while ($case_study_query->have_posts()) : $case_study_query->the_post(); 
                            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium_large'); 
                            $title = get_the_title(get_the_ID());
                            $permalink = get_the_permalink(get_the_ID());
                            ?>
                                <a class="single-case" href="<?php echo $permalink; ?>">
                                <?php if($thumbnail) : ?>
                                    <figure>
                                        <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>">
                                    </figure>
                                    <h4><?php echo $title; ?></h4>
                                    <span class="btn-pb btn-pb--arrow">
                                        View study
                                    </span>
                                <?php endif; ?>
                            </a>
                            <?php endwhile;
                          
                        else :
                            echo '<p>No case studies available.</p>';
                        endif;

                        // Reset post data
                        wp_reset_postdata();
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <a href="<?php echo get_post_type_archive_link( 'case-study' ); ?>" class="more-link btn-pb--arrow btn-pb">View more case studies</a>
        </div>
        <div class="content">
            <?php if($header) : ?>
                <h1 data-aos="fade-in"><?php echo $header; ?></h1>
            <?php endif; ?>
            <?php foreach ($case_study_content as $row) : ?>
                <?php if ($row['acf_fc_layout'] == 'long_text') : 
                    $copy = $row['copy'];
                    ?>
                    
                    <div class="copy" data-aos="fade-in">
                        <?php echo $copy; ?>
                    </div>
                <?php elseif ($row['acf_fc_layout'] == 'quote') : 
                    $quote = $row['quote'];
                    ?>
                    <div class="copy quote" data-aos="fade-in">
                        <?php echo $quote; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="related-case-studies cs-mobile">
                <?php if($sidebar_header_2) : ?>
                    <h3 class="sidebar-header"><?php echo $sidebar_header_2; ?></h3>
                <?php endif; ?>
                <?php if($case_studies_relation) : ?>
                    <div class="studies-grid">
                        <?php foreach($case_studies_relation as $case) : 
                            $thumbnail = get_the_post_thumbnail_url($case->ID, 'medium_large'); 
                            $title = get_the_title($case->ID);
                            $permalink = get_the_permalink($case->ID);
                            ?>
                            <a class="single-case" href="<?php echo $permalink; ?>">
                                <?php if($thumbnail) : ?>
                                    <figure>
                                        <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>">
                                    </figure>
                                    <h4><?php echo $title; ?></h4>
                                    <span class="btn-pb btn-pb--arrow">
                                        View study
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="studies-grid">
                    <?php
                        // Get the current post ID
                        $current_post_id = get_the_ID();

                        // Query arguments
                        $args = array(
                            'post_type'      => 'case-study', // Custom post type
                            'posts_per_page' => 2,            // Limit to 2 posts
                            'post__not_in'   => array($current_post_id), // Exclude the current post
                            'orderby'        => 'date',       // Order by date
                            'order'          => 'DESC',       // Most recent posts first
                        );

                        // The custom query
                        $case_study_query = new WP_Query($args);

                        // Loop through the results
                        if ($case_study_query->have_posts()) :
                         
                            while ($case_study_query->have_posts()) : $case_study_query->the_post(); 
                            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium_large'); 
                            $title = get_the_title(get_the_ID());
                            $permalink = get_the_permalink(get_the_ID());
                            ?>
                                <a class="single-case" href="<?php echo $permalink; ?>">
                                <?php if($thumbnail) : ?>
                                    <figure>
                                        <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>">
                                    </figure>
                                    <h4><?php echo $title; ?></h4>
                                    <span class="btn-pb btn-pb--arrow">
                                        View study
                                    </span>
                                <?php endif; ?>
                            </a>
                            <?php endwhile;
                          
                        else :
                            echo '<p>No case studies available.</p>';
                        endif;

                        // Reset post data
                        wp_reset_postdata();
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>