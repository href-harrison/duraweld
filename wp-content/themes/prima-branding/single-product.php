<?php get_header(); 

// Get current post ID
$current_post_id = get_the_ID();

// Get hero images - use product's featured image if available, fallback to site options
$products_hero_image = null;
$products_hero_image_mobile = null;
$products_hero_video = get_field('products_hero_video', 'option');
$products_hero_header = get_field('products_hero_header', 'option');

// Try to get featured image from the product itself (category product)
$product_featured_image_id = get_post_thumbnail_id($current_post_id);
if ($product_featured_image_id) {
    $products_hero_image = acf_get_attachment($product_featured_image_id);
    // Use same image for mobile if no separate mobile image is set
    $products_hero_image_mobile = $products_hero_image;
}

// Fallback to site options if product doesn't have featured image
if (!$products_hero_image) {
    $products_hero_image = get_field('products_hero_image', 'option');
}
if (!$products_hero_image_mobile) {
    $products_hero_image_mobile = get_field('products_hero_image_mobile', 'option');
}

// Handle images - convert IDs to arrays if needed
if (!empty($products_hero_image) && is_numeric($products_hero_image)) {
    $products_hero_image = acf_get_attachment($products_hero_image);
}
if (!empty($products_hero_image_mobile) && is_numeric($products_hero_image_mobile)) {
    $products_hero_image_mobile = acf_get_attachment($products_hero_image_mobile);
}

// Get image URLs
$image_url = '';
$mobile_url = '';
$image_alt = '';
$mobile_alt = '';

if (!empty($products_hero_image)) {
    if (is_array($products_hero_image)) {
        $image_url = $products_hero_image['url'] ?? '';
        $image_alt = $products_hero_image['alt'] ?? $products_hero_image['title'] ?? '';
    } elseif (is_object($products_hero_image)) {
        $image_url = $products_hero_image->url ?? '';
        $image_alt = $products_hero_image->alt ?? $products_hero_image->title ?? '';
    }
}

if (!empty($products_hero_image_mobile)) {
    if (is_array($products_hero_image_mobile)) {
        $mobile_url = $products_hero_image_mobile['url'] ?? '';
        $mobile_alt = $products_hero_image_mobile['alt'] ?? $products_hero_image_mobile['title'] ?? '';
    } elseif (is_object($products_hero_image_mobile)) {
        $mobile_url = $products_hero_image_mobile->url ?? '';
        $mobile_alt = $products_hero_image_mobile->alt ?? $products_hero_image_mobile->title ?? '';
    }
}
?>

<section class="products-hero">
    <?php if($products_hero_video) : ?>
        <div class="video-container">
            <video autoplay muted loop id="bgVideo">
                <source src="<?php echo esc_url($products_hero_video['url']); ?>" type="video/mp4">
            </video>
        </div>
    <?php elseif($image_url || $mobile_url) : ?>
        <div class="products-hero--image-wrapper">
            <?php if($image_url && $mobile_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="desktop-image" data-aos="fade-up">
                <img src="<?php echo esc_url($mobile_url); ?>" alt="<?php echo esc_attr($mobile_alt); ?>" class="mobile-image" data-aos="fade-up">
            <?php elseif($image_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" data-aos="fade-up">
            <?php elseif($mobile_url) : ?>
                <img src="<?php echo esc_url($mobile_url); ?>" alt="<?php echo esc_attr($mobile_alt); ?>" data-aos="fade-up">
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>


<section class="site-page">
    <article <?php post_class(); ?>>
      
      <?php 
        if (have_posts()) : while (have_posts()) : the_post();
          the_content(); 
        endwhile; endif;
      ?>
    </article>

</section>

<?php get_footer(); ?>
