<?php get_header(); 
  $news_hero_image = get_field('news_background_image', 'option');
  $news_hero_image_mobile = get_field('news_background_image_mobile', 'option');
  $news_hero_video = get_field('news_background_video', 'option');
  $news_hero_header = get_field('news_listing_header', 'option');
  $news_listing_benefits = get_field('news_benefits', 'option');
  $benefits_repeater = $news_listing_benefits['benefits_repeater'];
?>
<section class="news-hero">
    <?php if($news_hero_video) : ?>
        <div class="video-container">
        <video autoplay muted loop id="bgVideo">
            <source src="<?php echo $news_hero_video['url']; ?>" type="video/mp4">
        </video>
        </div>
    <?php elseif($news_hero_image && $news_hero_image_mobile ) : ?>
        <img src="<?php echo $news_hero_image['url']; ?>" alt="<?php echo $news_hero_image['alt']; ?>" class="image-desktop">
        <img src="<?php echo $news_hero_image['url']; ?>" alt="<?php echo $news_hero_image['alt']; ?>" class="image-mobile">

    <?php elseif($news_hero_image) : ?>
        <img src="<?php echo $news_hero_image['url']; ?>" alt="<?php echo $news_hero_image['alt']; ?>">
    <?php endif; ?>
    <?php if($news_hero_header) : ?>
        <div class="site-container">
          <h1 data-aos="fade-in"><?php echo $news_hero_header; ?></h1>
        </div>
    <?php endif; ?>
</section>
<section class="site-page">
  <div class="site-container">
    <?php if (have_posts()) : ?>
      <div class="post-list">
        <?php while (have_posts()) : the_post(); ?>
          <a class="post-list__entry" href="<?php the_permalink(); ?>"  data-aos="fade-up">
            <div class="meta">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
            <g clip-path="url(#clip0_159_298)">
            <path d="M13.9792 0.815063H12.3167V2.28038C12.3167 2.99136 11.7625 3.56362 11.0833 3.56362C10.4042 3.56362 9.85 2.98703 9.85 2.28038V0.815063H5.15417V2.28038C5.15417 2.99136 4.6 3.56362 3.92083 3.56362C3.24167 3.56362 2.6875 2.98703 2.6875 2.28038V0.815063H1.02083C0.4625 0.815063 0 1.29194 0 1.8772V13.9379C0 14.5232 0.4625 15 1.02083 15H13.975C14.5375 15 14.9958 14.5188 14.9958 13.9379V1.8772C14.9958 1.29194 14.5333 0.815063 13.975 0.815063H13.9792ZM13.5958 13.6734H1.40833V4.62576H13.5958V13.6734Z" fill="#666666"/>
            <path d="M11.675 0.593931C11.6458 0.264451 11.3833 0 11.0542 0C10.725 0 10.4625 0.260116 10.4375 0.593931V1.52601C10.4667 1.85549 10.7292 2.11994 11.0542 2.11994C11.3792 2.11994 11.6458 1.85983 11.675 1.52601V0.593931Z" fill="#666666"/>
            <path d="M4.4875 0.715269C4.45833 0.385789 4.19583 0.121338 3.86667 0.121338C3.5375 0.121338 3.275 0.381454 3.25 0.715269V1.64735C3.27917 1.97683 3.54167 2.24128 3.86667 2.24128C4.19167 2.24128 4.45833 1.98116 4.4875 1.64735V0.715269Z" fill="#666666"/>
            <path d="M5.11273 5.9176H2.62939V8.50142H5.11273V5.9176Z" fill="#666666"/>
            <path d="M8.74163 5.9176H6.2583V8.50142H8.74163V5.9176Z" fill="#666666"/>
            <path d="M12.3002 5.9176H9.81689V8.50142H12.3002V5.9176Z" fill="#666666"/>
            <path d="M5.14593 9.79333H2.6626V12.3771H5.14593V9.79333Z" fill="#666666"/>
            <path d="M8.77484 9.79333H6.2915V12.3771H8.77484V9.79333Z" fill="#666666"/>
            <path d="M12.3334 9.79333H9.8501V12.3771H12.3334V9.79333Z" fill="#666666"/>
            </g>
            <defs>
            <clipPath id="clip0_159_298">
            <rect width="15" height="15" fill="white"/>
            </clipPath>
            </defs>
            </svg>

            <span><?php echo get_the_date('jS F Y'); ?></span>
            </div>
            <?php if(get_the_post_thumbnail()) : ?>
              <figure>
                <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="">
              </figure>
            <?php endif; ?>
            <h2 class="post-list__title"><?php the_title(); ?></h2>
            <div class="post-list__excerpt"><?php the_excerpt(); ?></div>
            <span class="btn-pb btn-pb--arrow">Read now</span>
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
    id="benefits-news-archive" 
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
