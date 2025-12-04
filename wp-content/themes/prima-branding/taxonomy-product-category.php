<?php 
get_header(); 

// Get the current term
$term = get_queried_object();
$term_id = $term->term_id ?? 0;
$term_name = $term->name ?? '';
$term_description = $term->description ?? '';

// Get hero images from site options
$products_hero_image = get_field('products_hero_image', 'option');
$products_hero_image_mobile = get_field('products_hero_image_mobile', 'option');
$products_hero_video = get_field('products_hero_video', 'option');
$products_hero_header = get_field('products_hero_header', 'option');

// Use unified product benefits from site options
$unified_benefits = get_field('product_benefits', 'option');
$benefits_repeater = $unified_benefits['benefits_repeater'] ?? [];

// Get logo carousel data from site options
$site_brand_logos = get_field('brand_logos', 'option');
$logo_header = $site_brand_logos['header'] ?? '';
$logo_repeater = $site_brand_logos['logo_repeater'] ?? [];

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
<section class="site-page products-page">
  <div class="site-container products-container">
    <?php 
    // Render content blocks from ACF flexible content
    if (function_exists('render_category_content_blocks')) {
        render_category_content_blocks($term_id);
    } else {
        // Fallback: Show default products grid if no blocks are set up
        if($products_hero_header) : ?>
          <h2 class="section-header" data-aos="fade-in"><?php echo esc_html($products_hero_header); ?></h2>
        <?php endif; ?>

        <?php if (have_posts()) : ?>
          <ul class="products-grid">
            <?php 
            $index = 0;
            while (have_posts()) : the_post(); 
            $post_id = get_the_ID(); 

            get_template_part('template-parts/cards/product', 'card', [
                'post_id' => $post_id,
                'index' => $index,
            ]);
            $index++;
            ?>
              
            <?php endwhile; ?>
          </ul>
        <?php else : ?>
          <article>
            <h1>Sorry, there's nothing here yet!</h1>
            <p>Please check again at a later date.</p>
          </article>
        <?php endif; 
        wp_reset_query();
    }
    ?>
  </div>
</section>

<?php get_footer(); ?>

