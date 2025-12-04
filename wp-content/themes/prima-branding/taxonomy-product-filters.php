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

// Setup query for products with this filter term
$products_query = new WP_Query([
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'tax_query' => [
        [
            'taxonomy' => 'product_filters',
            'field' => 'term_id',
            'terms' => $term_id,
        ],
    ],
]);
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
    <?php if($products_hero_header) : ?>
      <h2 class="section-header" data-aos="fade-in"><?php echo esc_html($products_hero_header); ?></h2>
    <?php endif; ?>

    <?php if ($products_query->have_posts()) : ?>
      <ul class="products-grid">
        <?php 
        $index = 0;
        while ($products_query->have_posts()) : $products_query->the_post(); 
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
    wp_reset_postdata();
    ?>
  </div>
</section>


<?php if($logo_repeater && !empty($logo_repeater)) : ?>
    <section class="logo-carousel">
        <div class="site-container">
            <?php if($logo_header) : ?>
                <h2 data-aos="fade-in"><?php echo esc_html($logo_header); ?></h2>
            <?php endif; ?>
            
            <div class="logo-slider swiper">
                <div class="swiper-wrapper">
                    <?php foreach($logo_repeater as $index=>$logo) : 
                        $logo_image = is_array($logo) ? ($logo['logo'] ?? $logo) : $logo;
                        $logo_link = is_array($logo) ? ($logo['link'] ?? false) : false;
                        
                        // Handle if logo is just an ID
                        if (is_numeric($logo_image)) {
                            $logo_image = acf_get_attachment($logo_image);
                        }
                        
                        $logo_url = '';
                        $logo_alt = '';
                        if (is_array($logo_image)) {
                            $logo_url = $logo_image['url'] ?? '';
                            $logo_alt = $logo_image['alt'] ?? $logo_image['title'] ?? '';
                        } elseif (is_object($logo_image)) {
                            $logo_url = $logo_image->url ?? '';
                            $logo_alt = $logo_image->alt ?? $logo_image->title ?? '';
                        }
                        
                        if ($logo_url) :
                    ?>
                        <?php if($logo_link && !empty($logo_link['url'])) : ?>
                            <a href="<?php echo esc_url($logo_link['url']); ?>" 
                               target="<?php echo esc_attr($logo_link['target'] ?? '_self'); ?>" 
                               class="swiper-slide single-logo">
                                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($logo_alt); ?>">
                            </a>
                        <?php else : ?>
                            <div class="swiper-slide single-logo">
                                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($logo_alt); ?>">
                            </div>
                        <?php endif; ?>
                    <?php 
                        endif;
                    endforeach; ?>
                </div>
            </div>
            <div class="swiper-pagination logos-pagination"></div>
        </div>
    </section>
<?php endif; ?>

<section
    id="benefits-products-archive" 
    class="benefits-block "
>
    <div class="site-container">
        <?php if($benefits_repeater && !empty($benefits_repeater)) : ?>
            <div class="benefits-list">
                <?php foreach($benefits_repeater as $index=>$benefit) : 
                        $icon = $benefit['icon']; 
                        $header = $benefit['header']; 
                        
                        
                    ?>
                    <div class="single-benefit" data-aos="fade-in" data-aos-delay="<?php echo $index * 100; ?>">
                        <?php if($icon) : ?>
                            <img src="<?php echo $icon['url']; ?>" alt="<?php echo $icon['alt']; ?>">
                        <?php endif; ?>
                        <?php if($header) : ?>
                            <h5><?php echo $header; ?></h5>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>

