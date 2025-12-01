<?php get_header(); 
    $products_hero_image = get_field('products_hero_image', 'option');
    $products_hero_image_mobile = get_field('products_hero_image_mobile', 'option');
    $products_hero_video = get_field('products_hero_video', 'option');
    $products_hero_header = get_field('products_hero_header', 'option');
    $products_listing_benefits = get_field('products_listing_benefits', 'option');
    $benefits_repeater = $products_listing_benefits['benefits_repeater'];
?>
<section class="products-hero">
    <?php if($products_hero_video) : ?>
        <div class="video-container">
        <video autoplay muted loop id="bgVideo">
            <source src="<?php echo $products_hero_video['url']; ?>" type="video/mp4">
        </video>
        </div>
    <?php elseif($products_hero_image && $products_hero_image_mobile ) : ?>
        <img src="<?php echo $products_hero_image['url']; ?>" alt="<?php echo $products_hero_image['alt']; ?>" class="image-desktop">
        <img src="<?php echo $products_hero_image['url']; ?>" alt="<?php echo $products_hero_image['alt']; ?>" class="image-mobile">

    <?php elseif($products_hero_image) : ?>
        <img src="<?php echo $products_hero_image['url']; ?>" alt="<?php echo $products_hero_image['alt']; ?>">
    <?php endif; ?>
    <?php if($products_hero_header) : ?>
        <h1 data-aos="fade-in"><?php echo $products_hero_header; ?></h1>
    <?php endif; ?>
</section>
<section class="site-page products-page">
  <div class="site-container products-container">

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
    <?php endif; ?>
  </div>
</section>
<section
    id="benefits-products-archive" 
    class="benefits-block "
>
    <div class="site-container">
        <?php if($benefits_repeater) : ?>
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
