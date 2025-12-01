<?php get_header(); 
  $case_hero_image = get_field('case_background_image', 'option');
  $case_hero_image_mobile = get_field('case_background_image_mobile', 'option');
  $case_hero_video = get_field('case_background_video', 'option');
  $case_hero_header = get_field('case_listing_header', 'option');
  $case_listing_benefits = get_field('case_benefits', 'option');
  $benefits_repeater = $case_listing_benefits['benefits_repeater'];
?>
<section class="news-hero">
    <?php if($case_hero_video) : ?>
        <div class="video-container">
        <video autoplay muted loop id="bgVideo">
            <source src="<?php echo $case_hero_video['url']; ?>" type="video/mp4">
        </video>
        </div>
    <?php elseif($case_hero_image && $case_hero_image_mobile ) : ?>
        <img src="<?php echo $case_hero_image['url']; ?>" alt="<?php echo $case_hero_image['alt']; ?>" class="image-desktop">
        <img src="<?php echo $case_hero_image['url']; ?>" alt="<?php echo $case_hero_image['alt']; ?>" class="image-mobile">

    <?php elseif($case_hero_image) : ?>
        <img src="<?php echo $case_hero_image['url']; ?>" alt="<?php echo $case_hero_image['alt']; ?>">
    <?php endif; ?>
    <?php if($case_hero_header) : ?>
        <div class="site-container">
          <h1 data-aos="fade-in"><?php echo $case_hero_header; ?></h1>
        </div>
    <?php endif; ?>
</section>
<section class="site-page">
  <div class="site-container">
    <?php if (have_posts()) : ?>
      <div class="post-list">
        <?php while (have_posts()) : the_post(); ?>
          <a class="post-list__entry" href="<?php the_permalink(); ?>"  data-aos="fade-up">
   
            <?php if(get_the_post_thumbnail()) : ?>
              <figure>
                <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="">
              </figure>
            <?php endif; ?>
            <h2 class="post-list__title"><?php the_title(); ?></h2>
            <div class="post-list__excerpt"><?php the_excerpt(); ?></div>
            <span class="btn-pb btn-pb--arrow">Read study</span>
          </a>
        <?php endwhile; ?>
      </div>
      <div class="pagination" data-aos="fade-in">
        <div class="older">
            <?php 
            next_posts_link(
                '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="16" viewBox="0 0 18 16" fill="none">
                    <g clip-path="url(#clip0_159_413)">
                        <path d="M0.376889 7.05821C-0.125365 7.57884 -0.125365 8.42435 0.37689 8.94498L6.80575 15.609C7.308 16.1297 8.12367 16.1297 8.62592 15.609C9.12817 15.0884 9.12817 14.2429 8.62592 13.7223L4.38689 9.33233L16.7142 9.33232C17.4254 9.33232 18 8.73672 18 7.99951C18 7.2623 17.4254 6.6667 16.7142 6.6667L4.39091 6.6667L8.6219 2.27675C9.12416 1.75612 9.12416 0.910616 8.6219 0.389986C8.11965 -0.130645 7.30398 -0.130645 6.80173 0.389986L0.372873 7.05405L0.376889 7.05821Z" fill="#444444"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_159_413">
                            <rect width="18" height="16" fill="white" transform="translate(18 16) rotate(180)"/>
                        </clipPath>
                    </defs>
                </svg>
                Older Articles'
            ); 
            ?>
        </div>
        <div class="newer">
            <?php 
            previous_posts_link(
                'Newer Articles
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="16" viewBox="0 0 18 16" fill="none">
                    <g clip-path="url(#clip0_159_413)">
                        <path d="M0.376889 7.05821C-0.125365 7.57884 -0.125365 8.42435 0.37689 8.94498L6.80575 15.609C7.308 16.1297 8.12367 16.1297 8.62592 15.609C9.12817 15.0884 9.12817 14.2429 8.62592 13.7223L4.38689 9.33233L16.7142 9.33232C17.4254 9.33232 18 8.73672 18 7.99951C18 7.2623 17.4254 6.6667 16.7142 6.6667L4.39091 6.6667L8.6219 2.27675C9.12416 1.75612 9.12416 0.910616 8.6219 0.389986C8.11965 -0.130645 7.30398 -0.130645 6.80173 0.389986L0.372873 7.05405L0.376889 7.05821Z" fill="#444444"/>
                    </g>
                    <defs>
                        <clipPath id="clip0_159_413">
                            <rect width="18" height="16" fill="white" transform="translate(18 16) rotate(180)"/>
                        </clipPath>
                    </defs>
                </svg>'
            ); 
            ?>
        </div>
    </div>
    <?php else : ?>
      <article>
        <h1>Sorry, there's nothing here yet!</h1>
        <p>Please check again at a later date.</p>
      </article>
    <?php endif; ?>
  </div>
</section>
<section
    id="benefits-case-archive" 
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
