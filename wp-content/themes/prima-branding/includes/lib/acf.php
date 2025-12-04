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

/**
 * Ensure ACF JSON is loaded from theme directory
 * This ensures post types, taxonomies, and field groups load from acf-json folder
 */
add_filter('acf/settings/load_json', function($paths) {
  $paths[] = get_stylesheet_directory() . '/acf-json';
  return $paths;
});

/**
 * Optimize ACF taxonomy field queries for performance
 */

// Optimize parent_filter field (only show top-level terms)
add_filter('acf/fields/taxonomy/query/name=parent_filter', function($args, $field, $post_id) {
	$args['parent'] = 0; // Only show top-level terms
	$args['number'] = 50; // Limit results for performance
	$args['orderby'] = 'name';
	$args['order'] = 'ASC';
	$args['hide_empty'] = false; // Show all terms even if empty
	return $args;
}, 10, 3);

// Filter results for parent_filter to ensure only top-level terms appear
add_filter('acf/fields/taxonomy/result/name=parent_filter', function($text, $term, $field) {
	// Prevent child terms from appearing in the parent filter dropdown
	if ($term->parent !== 0) {
		return false;
	}
	return $text;
}, 10, 3);

// Optimize filter_term field - consolidated and aggressive optimization
add_filter('acf/fields/taxonomy/query', function($args, $field, $post_id) {
	// Only optimize filter_term fields in the product-filtered-grid block
	if (isset($field['name']) && $field['name'] === 'filter_term') {
		// Very aggressive limiting for initial load
		if (empty($args['search'])) {
			$args['number'] = 25; // Only 25 terms on initial load for fast performance
		} else {
			$args['number'] = 100; // More when searching
		}
		
		$args['orderby'] = 'name';
		$args['order'] = 'ASC';
		$args['hide_empty'] = false;
		
		// Exclude top-level parent terms (Size, Style) - only show child terms
		// This significantly reduces the number of terms to load
		$parent_terms = get_terms([
			'taxonomy' => 'product_filters',
			'parent' => 0,
			'fields' => 'ids',
			'hide_empty' => false,
		]);
		
		if (!empty($parent_terms) && !is_wp_error($parent_terms)) {
			// Exclude parent term IDs from results (only show child terms like A4, A5, etc.)
			$existing_exclude = isset($args['exclude']) && is_array($args['exclude']) ? $args['exclude'] : [];
			$args['exclude'] = array_unique(array_merge($existing_exclude, $parent_terms));
		}
	}
	
	return $args;
}, 5, 3); // Priority 5 to run early, before other filters