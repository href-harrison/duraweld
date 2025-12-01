<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <title><?php wp_title(); ?></title>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-D608SLW50G"></script>
    <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-D608SLW50G'); </script>
    
    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?>>
    <?php wp_body_open(); 
    $header_logo = get_field('header_logo', 'option');
    ?>
    
    <div class="site-wrapper">
      <header class="site-header">
        <div class="site-container">
          <a href="<?php echo home_url(); ?>" class="site-logo">
            <?php if($header_logo) : ?>
              <img src="<?php echo $header_logo['url']; ?>" alt="<?php bloginfo('name'); ?>">
            <?php else : ?>
              <span class="text-replace"><?php bloginfo('name'); ?></span>
            <?php endif; ?>
          </a>

          <nav class="site-header__navigation">
            <?php
              echo wp_nav_menu([
                'theme_location' => 'header',
                'menu_class' => 'site-header__menu',
                'container' => false
              ]);
            ?>
          </nav>

          <div class="hamburger">
            <span></span>
          </div>
        </div>

        <div class="mobile-nav">
            <div class="site-container">
              <nav class="site-header__mobile-navigation">
              <?php
              echo wp_nav_menu([
                'theme_location' => 'header',
                'menu_class' => 'site-header__mobile-menu',
                'container' => false
              ]);
            ?>
              </nav>
            </div>
      </div>
      </header>

      

      <main class="site-main">
