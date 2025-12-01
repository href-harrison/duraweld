<?php get_header(); ?>

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
