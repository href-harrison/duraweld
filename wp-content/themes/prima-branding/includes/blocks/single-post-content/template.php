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
$featured_header = $data['featured_header'];
$image = $data['image'];
$image_url = $data['image'] ? $data['image']['url'] : get_the_post_thumbnail_url();
$copy = $data['copy'];

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
        <div class="post-content">
            <h1 data-aos="fade-in"><?php echo $header; ?></h1>
            <figure data-aos="fade-up">
                <img src="<?php echo $image_url; ?>" alt="<?php echo $image['alt']; ?>">

                <div class="meta">
                    <div class="date">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                        <g clip-path="url(#clip0_159_748)">
                        <path d="M13.9792 0.815002H12.3167V2.28032C12.3167 2.9913 11.7625 3.56356 11.0833 3.56356C10.4042 3.56356 9.85 2.98697 9.85 2.28032V0.815002H5.15417V2.28032C5.15417 2.9913 4.6 3.56356 3.92083 3.56356C3.24167 3.56356 2.6875 2.98697 2.6875 2.28032V0.815002H1.02083C0.4625 0.815002 0 1.29188 0 1.87714V13.9378C0 14.5231 0.4625 15 1.02083 15H13.975C14.5375 15 14.9958 14.5188 14.9958 13.9378V1.87714C14.9958 1.29188 14.5333 0.815002 13.975 0.815002H13.9792ZM13.5958 13.6734H1.40833V4.6257H13.5958V13.6734Z" fill="#666666"/>
                        <path d="M11.675 0.593931C11.6458 0.264451 11.3833 0 11.0542 0C10.725 0 10.4625 0.260116 10.4375 0.593931V1.52601C10.4667 1.85549 10.7292 2.11994 11.0542 2.11994C11.3792 2.11994 11.6458 1.85983 11.675 1.52601V0.593931Z" fill="#666666"/>
                        <path d="M4.4875 0.71533C4.45833 0.38585 4.19583 0.121399 3.86667 0.121399C3.5375 0.121399 3.275 0.381515 3.25 0.71533V1.64741C3.27917 1.97689 3.54167 2.24134 3.86667 2.24134C4.19167 2.24134 4.45833 1.98123 4.4875 1.64741V0.71533Z" fill="#666666"/>
                        <path d="M5.11273 5.9176H2.62939V8.50142H5.11273V5.9176Z" fill="#666666"/>
                        <path d="M8.74163 5.9176H6.2583V8.50142H8.74163V5.9176Z" fill="#666666"/>
                        <path d="M12.3002 5.9176H9.81689V8.50142H12.3002V5.9176Z" fill="#666666"/>
                        <path d="M5.14593 9.79333H2.6626V12.3771H5.14593V9.79333Z" fill="#666666"/>
                        <path d="M8.77484 9.79333H6.2915V12.3771H8.77484V9.79333Z" fill="#666666"/>
                        <path d="M12.3334 9.79333H9.8501V12.3771H12.3334V9.79333Z" fill="#666666"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_159_748">
                        <rect width="15" height="15" fill="white"/>
                        </clipPath>
                        </defs>
                        </svg>

                        <span><?php echo get_the_date('jS F Y'); ?></span>
                    </div>

                    <?php if(has_tag()) : ?> 
                        <div class="tags">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <g clip-path="url(#clip0_168_1180)">
                            <path d="M0 1.75523V7.22203C0 7.84368 0.245057 8.43972 0.683964 8.87853L7.12127 15.3144C8.03566 16.2285 9.51697 16.2285 10.4314 15.3144L15.3142 10.4326C16.2286 9.51846 16.2286 8.03748 15.3142 7.1233L8.8769 0.687464C8.43799 0.248657 7.84181 0.00365673 7.22002 0.00365673L1.75563 0C0.786376 0 0 0.786196 0 1.75523ZM4.09647 2.92538C4.74276 2.92538 5.26689 3.44939 5.26689 4.09553C5.26689 4.74168 4.74276 5.26568 4.09647 5.26568C3.45018 5.26568 2.92605 4.74168 2.92605 4.09553C2.92605 3.44939 3.45018 2.92538 4.09647 2.92538Z" fill="#666666"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_168_1180">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <div class="tags-list">
                            <?php the_tags('', ', ', ''); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </figure>

            <?php if($copy) : ?>
                <div class="copy">
                    <?php echo $copy; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="related-posts">
            <?php if($featured_header) : ?>
                <h3 data-aos="fade-in"><?php echo $featured_header; ?></h3>
            <?php endif; ?>

            <div class="posts-grid">
                <?php
                $current_post_id = get_the_ID();

                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 3,
                    'post__not_in'   => array($current_post_id), 
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                );



                $custom_query = new WP_Query($args);
                if ($custom_query->have_posts()) :
                    while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                        <a class="latest-post" href="<?php the_permalink(); ?>"  data-aos="fade-in">
                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?><">
                            <h4><?php the_title(); ?></h4>

                            <span class="btn-pb btn-pb--arrow">View study</span>
                        </a>
                    <?php endwhile;
                    wp_reset_postdata(); 
                else : ?>
                    <p>No recent posts available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>