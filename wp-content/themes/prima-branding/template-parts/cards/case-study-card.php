<?php
$post_id = $args['post_id'] ?? "";
$index = $args['index'] ?? "";
$thumbnail = get_the_post_thumbnail_url($post_id, 'medium_large');
$title = get_the_title($post_id); 
$excerpt = get_the_excerpt($post_id);
$permalink = get_the_permalink($post_id);
$excerpt_alternative = get_field('excerpt_alternative', $post_id);

?>
<a href="<?php echo $permalink; ?>" class="single-case-tile" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
    <?php if($thumbnail) : ?>
        <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>">
    <?php endif; ?>
    <h4><?php echo $title; ?></h4>
    <?php if($excerpt_alternative) : ?>
        <div class="excerpt">
            <?php echo $excerpt_alternative; ?>
        </div>
    <?php elseif($excerpt) : ?>
        <div class="excerpt">
            <p><?php echo $excerpt; ?></p>
        </div>
    <?php endif; ?>
    <span class="btn-pb btn-pb--arrow">View study</span>
</a>