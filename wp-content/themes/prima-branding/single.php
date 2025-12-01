<?php get_header(); ?>

<section class="site-page">
  <!-- <div class="site-container"> -->
    <article <?php post_class(); ?>>
      
      <?php 
        if (have_posts()) : while (have_posts()) : the_post();
          the_content(); 
        endwhile; endif;

      ?>

    </article>
  <!-- </div -->
</section>

<?php get_footer(); ?>
