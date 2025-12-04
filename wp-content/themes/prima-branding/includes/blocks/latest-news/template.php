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
        <div class="container-right">
            <?php if($header) : ?>
                <h2 data-aos="fade-in"><?php echo $header; ?></h2>
            <?php endif; ?>
            <?php if($copy) : ?>
                <div class="copy" data-aos="fade-in">
                    <?php 
                    // Extract first paragraph and remaining content for read more
                    $copy_content = $copy;
                    
                    // Split by paragraph tags
                    preg_match_all('/<p[^>]*>.*?<\/p>/s', $copy_content, $copy_paragraphs);
                    
                    $first_para = '';
                    $remaining_content = '';
                    
                    if (!empty($copy_paragraphs[0])) {
                        $first_para = $copy_paragraphs[0][0];
                        if (count($copy_paragraphs[0]) > 1) {
                            $remaining_content = implode('', array_slice($copy_paragraphs[0], 1));
                        }
                    } else {
                        // If no paragraph tags, use the content as-is
                        $first_para = $copy_content;
                    }
                    ?>
                    <div class="copy-excerpt">
                        <?php 
                        // On mobile: show first paragraph with ellipsis, hide rest
                        // On tablet/desktop: show all content
                        if (!empty($remaining_content)) {
                            // Add ellipsis to the end of first paragraph for mobile only
                            $first_para = rtrim($first_para);
                            // Check if it ends with a closing tag, if so add ellipsis before it
                            if (preg_match('/^(.*)(<\/p>)$/s', $first_para, $matches)) {
                                $first_para = $matches[1] . '  <span class="copy-ellipsis">...</span>' . $matches[2];
                            } else {
                                $first_para .= '  <span class="copy-ellipsis">...</span>';
                            }
                        }
                        echo $first_para; 
                        ?>
                        
                        <?php if (!empty($remaining_content)) : ?>
                            <div class="copy-content-expanded">
                                <?php echo $remaining_content; ?>
                            </div>
                            <a href="#" class="read-more-link" data-copy-id="copy-<?php echo $block_id; ?>">Read more</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($link) : ?>
                <a href="<?php echo $link['url']; ?>" class="btn-pb btn-pb--arrow" data-aos="fade-in"><?php echo $link['title']; ?></a>
            <?php endif; ?>
        </div>
        <div class="container-left">
            <h2 data-aos="fade-in">Latest Articles</h2>
            <div class="latest-news-container">
                <?php 
                $args = array(
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'posts_per_page' => 2,
                    'orderby'        => 'publish_date',
                    'order'          => 'DESC'
                );
                $loop = new WP_Query( $args );
                
                while ( $loop->have_posts() ): $loop->the_post(); ?>
                    <div class="single-post-tile">
                        <figure>
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium_large'); ?>" alt="<?php echo get_the_title(get_the_ID()); ?>" data-aos="fade-in">
                        </figure>
                        <div class="content" data-aos="fade-in">
                            <h5><?php echo get_the_title(get_the_ID()); ?></h5>
                            <p><?php echo get_the_excerpt(get_the_ID()); ?></p>
                            <a href="<?php echo get_the_permalink(get_the_ID()); ?>" class="btn-pb btn-pb--arrow" data-aos="fade-in">Read now</a>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata();

                ?>
            </div>
        </div>
    </div>
</section>