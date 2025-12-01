<?php
// WordPress theme supports.
$markup = array('search-form', 'comment-form', 'comment-list');

add_theme_support('post-thumbnails');
add_theme_support('html5', $markup);


remove_theme_support('core-block-patterns');

add_theme_support( 'custom-spacing' );
add_theme_support( 'appearance-tools' );

// WordPress image sizes.
add_image_size('landscape_banner', 1920, 1080, true);

// WordPress navigation menus.
$nav_menus = array(
  'header' => 'Header Navigation',
  'footer' => 'Footer Navigation',
  'footer_2' => 'Footer Navigation 2',
  'footer_3' => 'Footer Navigation 3'
);

register_nav_menus($nav_menus);

/**
 * Remove unnecessary menu items, we're just using the Ghostkit
 * settings for the spacing options on the blocks, so we can remove
 * the reusable template pages
 */
function remove_menus(){  

  remove_menu_page( 'edit-comments.php' );          //Comments  
  remove_menu_page('ghostkit');
  remove_menu_page('ghostkit_template');
  remove_menu_page('edit.php?post_type=ghostkit_template');
  remove_menu_page('edit.php?post_type=wp_block');

}  
add_action( 'admin_menu', 'remove_menus', 9999); 