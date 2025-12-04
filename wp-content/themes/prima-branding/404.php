<?php get_header(); 
$error_404_banner_image = get_field('error_404_banner_image', 'option');
$error_404_banner_image_mobile = get_field('error_404_banner_image_mobile', 'option');
$error_404_header = get_field('error_404_header', 'option') ?: 'We’re sorry, we can’t find that page';
$error_404_copy = get_field('error_404_copy', 'option');
$error_404_main_link = get_field('error_404_main_link', 'option');
$error_404_products_link = get_field('error_404_products_link', 'option');
$error_404_shop_link = get_field('error_404_shop_link', 'option');
// Always use unified product benefits for 404 page
$unified_benefits = get_field('product_benefits', 'option');
$benefits_repeater = $unified_benefits['benefits_repeater'] ?? [];
?>
<section
    id="error-404-page" 
    class="error-404-hero"
>
    <?php if($error_404_banner_image && $error_404_banner_image_mobile) : ?>
        <img src="<?php echo $error_404_banner_image['url']; ?>" alt="<?php echo $error_404_banner_image['alt']; ?>" class="image-desktop">
        <img src="<?php echo $error_404_banner_image_mobile['url']; ?>" alt="<?php echo $error_404_banner_image_mobile['alt']; ?>" class="image-mobile">
    <?php elseif($error_404_banner_image) : ?>
        <img src="<?php echo $error_404_banner_image['url']; ?>" alt="<?php echo $error_404_banner_image['alt']; ?>">
    <?php endif; ?>
</section>
<section class="site-page">
  <div class="site-container py-[50px] lg:py-100 xl:pt-118 xl:pb-147 error-404">
    <article>
      <h1 class=""><?php echo $error_404_header; ?></h1>
      <?php if($error_404_copy) : ?>
        <div class="error-404--copy">
          <?php echo $error_404_copy; ?>
        </div>
      <?php endif; ?>
      <div class="error-404--links">
      <?php if($error_404_main_link) : ?>
        <a href="<?php echo $error_404_main_link['url']; ?>" target="<?php echo $error_404_main_link['target']; ?>" class="btn-pb btn-pb--arrow btn-pb--yellow"><?php echo $error_404_main_link['title']; ?></a>
      <?php endif; ?>
      <?php if($error_404_products_link) : ?>
        <a href="<?php echo $error_404_products_link['url']; ?>" target="<?php echo $error_404_products_link['target']; ?>" class="btn-pb btn-pb--arrow btn-pb--arrow--white btn-pb--gray"><?php echo $error_404_products_link['title']; ?></a>
      <?php endif; ?>
     
      </div>
    </article>
  </div>
</section>
<section
    id="benefits-404" 
    class="benefits-block benefits-404"
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
