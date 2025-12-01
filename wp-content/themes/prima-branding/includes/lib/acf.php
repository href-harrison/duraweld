<?php
/**
 * Register blocks to use with ACF.
 * Different icons can be chosen here: https://developer.wordpress.org/resource/dashicons/.
 * Once you have chosen an icon, i.e "dashicons-admin-users" - make sure to remove the "dashicons-" part, so it would be come "admin-users". 
 */
// function acf_register_blocks() {
//   $block_defaults = [
//     'category' => 'common',
//     'mode' => 'edit',
//     'align' => 'full',
//     'supports' => [
//       'align' => false,
//       'mode' => true,

//     ]
//   ];

//   if (function_exists('acf_register_block_type')) {
//     $blocks = [
//       array_merge([
//         'name' => 'basic_example',
//         'title' => 'Basic Example',
//         'icon' => 'editor-justify',
//         'render_template' => 'includes/blocks/basic-example.php',
//       ], $block_defaults),
//       array_merge([
//         'name' => 'basic_example_accordion',
//         'title' => 'Basic Accordion',
//         'icon' => 'editor-justify',
//         'render_template' => 'includes/blocks/basic-example-accordion.php',
//       ], $block_defaults),
//     ];

//     foreach ($blocks as $block) {
//       acf_register_block_type($block);
//     }
//   }
// }

/**
 * Choose which blocks are allowed on certain types.
 * We keep some of the default WordPress blocks on posts so users can still create blog posts via those,
 * but we only enable our registered blocks onto everything else to ensure they only use compatible blocks
 * for pages and such.
 * 
 * When you have registered a new block above, don't forget to add it into the list below. 
 * It would be added as "acf/block_name".
 */
// function acf_allowed_blocks($allowed_blocks) {
//   global $post;

//   return ($post->post_type == 'post') ? [
//     'core/image',
//     'core/video',
// 		'core/paragraph',
// 		'core/heading',
// 		'core/list',
//     'core/shortcode'
//   ] : [
//     'acf/basic-example',
//     'acf/basic-example-accordion',
//   ];
// }

/**
 * Register our Google Maps API key for use with the Google Maps fields.
 * MAPS_API_KEY is defined in definitions.php.
 */
function acf_add_maps_api_key() {
  acf_update_setting('google_api_key', MAPS_API_KEY);
}

/**
 * Register options pages.
 */
if (function_exists('acf_add_options_page')) {
  acf_add_options_page([
    'page_title' => 'Site Options',
    'menu_title' => 'Site Options',
    'menu_slug' => 'site-options',
    'capability' => 'edit_posts',
    'redirect' => false
  ]);
}