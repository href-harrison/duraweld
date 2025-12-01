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
$team_relation = $data['team_relation'];

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

// Featured post query
$args = array(
    'post_type' => 'team-member', // Your custom post type
    'meta_query' => array(
        array(
            'key' => 'is_featured', // ACF field key
            'value' => '1', // Value to check (true/1)
            'compare' => '=', // Comparison operator
        ),
    ),
);

$query = new WP_Query($args);

?>

<!-- Our front-end template -->
<section
    id="<?php echo $block_id; ?>" 
    class="<?php echo $class_name; ?>"
>
    <div class="site-container">
        <?php if($query->have_posts()) : 
        while ($query->have_posts()) : $query->the_post(); 
        $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'large'); 
        $thumbnail_mobile = get_the_post_thumbnail_url(get_the_ID(), 'medium_large'); 
        ?>
            <?php if($header) : ?>
                <h2 class="title" data-aos="fade-in"><?php echo $header; ?></h2>
            <?php endif; ?>
           <div class="featured-post" data-aos="fade-up">
                <?php if($thumbnail) : ?>
                    <figure>
                        <img src="<?php echo $thumbnail; ?>" alt="<?php echo get_the_title(); ?>" class="desktop-image">
                        <img src="<?php echo $thumbnail_mobile; ?>" alt="<?php echo get_the_title(); ?>" class="mobile-image">
                        <div class="featured-tag">
                            <span>Featured</span> Team Member
                        </div>
                    </figure>
                <?php endif; ?>
                <div class="header">
                    <h2 data-aos="fade-in"><?php echo get_the_title(); ?></h2>
                    <?php if(get_field('job_position', get_the_ID())) : ?>
                        <h4 data-aos="fade-in"><?php echo get_field('job_position', get_the_ID()); ?> </h4>
                    <?php endif; ?>
                </div>

                <?php if(get_field('long_bio', get_the_ID())) : ?>
                    <div class="text" data-aos="fade-in">
                        <?php echo get_field('long_bio', get_the_ID()); ?>
                    </div>
                <?php endif; ?>
           </div>
        <?php endwhile;

        // Restore original post data
        wp_reset_postdata(); ?>
        <?php endif; ?>

        <div class="team-grid">
            <?php if($team_relation) : ?>
                <?php foreach($team_relation as $index=>$member) : 
                    $thumbnail = get_the_post_thumbnail_url($member->ID, 'large'); 
                    $thumbnail_mobile = get_the_post_thumbnail_url($member->ID, 'medium_large'); 
                    ?>
                        <div class="single-team-member" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>"  data-member="<?php echo $index; ?>">
                        <?php if($thumbnail) : ?>
                            <figure>
                                <img src="<?php echo get_theme_file_uri(); ?>/assets/popup-icon.svg" alt="Open team member popup" class="popup-handle">
                                <img src="<?php echo $thumbnail; ?>" alt="<?php echo get_the_title($member->ID); ?>" class="desktop-image">
                                <img src="<?php echo $thumbnail_mobile; ?>" alt="<?php echo get_the_title($member->ID); ?>" class="mobile-image">
                            </figure>
                        <?php endif; ?>
                        <div class="info">
                            <h3 ><?php echo get_the_title($member->ID); ?></h3>
                            <?php if(get_field('job_position', $member->ID)) : ?>
                                <p ><?php echo get_field('job_position', $member->ID); ?> </p>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                    <div class="popup-container" data-content="<?php echo $index; ?>">
                    <div class="popup-content" >
                            <div class="close-popup">
                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                            <path d="M22.9131 26.0874L13.3036 16.478L3.69423 26.0874L0.519684 22.9129L10.1291 13.3035L0.476783 3.65115L3.65132 0.476605L13.3036 10.1289L22.9131 0.519508L26.0876 3.69405L16.4782 13.3035L26.0876 22.9129L22.9131 26.0874Z" fill="white"/>
                            </svg>
                            </div>
                            <?php if($thumbnail) : ?>
                                <figure>
                                    <img src="<?php echo get_theme_file_uri(); ?>/assets/icon-inner.svg" alt="" class="inner-icon">
                                    <img src="<?php echo $thumbnail; ?>" alt="<?php echo get_the_title($member->ID); ?>" class="desktop-image">
                                    <img src="<?php echo $thumbnail_mobile; ?>" alt="<?php echo get_the_title($member->ID); ?>" class="mobile-image">
                                </figure>
                            <?php endif; ?>
                            <div class="header">
                                <h2 data-aos="fade-in"><?php echo get_the_title(); ?></h2>
                                <?php if(get_field('job_position', $member->ID)) : ?>
                                    <h4 data-aos="fade-in"><?php echo get_field('job_position', $member->ID); ?> </h4>
                                <?php endif; ?>
                            </div>

                            <?php if(get_field('long_bio', $member->ID)) : ?>
                                <div class="text" data-aos="fade-in">
                                    <?php echo get_field('long_bio', $member->ID); ?>
                                </div>
                            <?php endif; ?>
                        
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : 
                // Define the query arguments
                $args = array(
                    'post_type' => 'team-member', 
                    'meta_query' => array(
                        'relation' => 'OR', 
                        array(
                            'key' => 'is_featured',
                            'value' => '1',
                            'compare' => '!=', 
                        ),
                        array(
                            'key' => 'is_featured',
                            'compare' => 'NOT EXISTS', 
                        ),
                    ),
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) :
                    $index = 0;
                while ($query->have_posts()) : $query->the_post();
                $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'large'); 
                $thumbnail_mobile = get_the_post_thumbnail_url(get_the_ID(), 'medium_large'); 
                ?>
                    <div class="single-team-member" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>" data-member="<?php echo $index; ?>">
                        <?php if($thumbnail) : ?>
                            <figure>
                                <img src="<?php echo get_theme_file_uri(); ?>/assets/popup-icon.svg" alt="" class="popup-handle">
                                <img src="<?php echo $thumbnail; ?>" alt="<?php echo get_the_title(); ?>" class="desktop-image">
                                <img src="<?php echo $thumbnail_mobile; ?>" alt="<?php echo get_the_title(); ?>" class="mobile-image">
                            </figure>
                        <?php endif; ?>
                        <div class="info">
                            <h3 ><?php echo get_the_title(); ?></h3>
                            <?php if(get_field('job_position', get_the_ID())) : ?>
                                <p ><?php echo get_field('job_position', get_the_ID()); ?> </p>
                            <?php endif; ?>
                        </div>
                        
                    </div>

                    <div class="popup-container" data-content="<?php echo $index; ?>">
                        <div class="popup-content">
                                <div class="close-popup">
                                <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                <path d="M22.9131 26.0874L13.3036 16.478L3.69423 26.0874L0.519684 22.9129L10.1291 13.3035L0.476783 3.65115L3.65132 0.476605L13.3036 10.1289L22.9131 0.519508L26.0876 3.69405L16.4782 13.3035L26.0876 22.9129L22.9131 26.0874Z" fill="white"/>
                                </svg>
                                </div>
                                <?php if($thumbnail) : ?>
                                    <figure>
                                        <img src="<?php echo get_theme_file_uri(); ?>/assets/icon-inner.svg" alt="" class="inner-icon">
                                        <img src="<?php echo $thumbnail; ?>" alt="<?php echo get_the_title(get_the_ID()); ?>" class="desktop-image">
                                        <img src="<?php echo $thumbnail_mobile; ?>" alt="<?php echo get_the_title(get_the_ID()); ?>" class="mobile-image">
                                    </figure>
                                <?php endif; ?>
                                <div class="header">
                                    <h2 data-aos="fade-in"><?php echo get_the_title(); ?></h2>
                                    <?php if(get_field('job_position', get_the_ID())) : ?>
                                        <h4 data-aos="fade-in"><?php echo get_field('job_position', get_the_ID()); ?> </h4>
                                    <?php endif; ?>
                                </div>

                                <?php if(get_field('long_bio', get_the_ID())) : ?>
                                    <div class="text" data-aos="fade-in">
                                        <?php echo get_field('long_bio', get_the_ID()); ?>
                                    </div>
                                <?php endif; ?>
                            
                        </div>
                    </div>
                <?php 
                $index++;
                endwhile;
                endif; 
                // Restore original post data
                wp_reset_postdata();
                ?>
            
            <?php endif; ?>
        </div>
    </div>
</section>