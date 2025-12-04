<?php

/**
 * Force taxonomy template for product_filters taxonomy
 * This ensures WordPress uses taxonomy-product-size.php for product-size URLs
 */
add_filter('taxonomy_template_hierarchy', function($templates) {
	// Check if we're on a product_filters taxonomy archive
	if (is_tax('product_filters')) {
		$term = get_queried_object();
		if ($term && isset($term->taxonomy) && $term->taxonomy === 'product_filters') {
			// Add our custom template to the top of the hierarchy
			array_unshift($templates, 'taxonomy-product-size.php');
			array_unshift($templates, 'taxonomy-product_filters.php');
		}
	}
	return $templates;
}, 10, 1);

/**
 * Force taxonomy template by URL pattern
 * If URL matches /product-size/ pattern, force taxonomy template
 */
add_filter('template_include', function($template) {
	global $wp_query;
	
	// Check if URL matches product-size pattern
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';
	
	if (preg_match('#/product-size/([^/]+)/?$#', $request_uri, $matches)) {
		$term_slug = $matches[1];
		
		// Try to get the term
		$term = get_term_by('slug', $term_slug, 'product_filters');
		
		if ($term && !is_wp_error($term)) {
			// Force the taxonomy query
			$wp_query->is_tax = true;
			$wp_query->is_archive = true;
			$wp_query->is_home = false;
			$wp_query->is_single = false;
			$wp_query->is_singular = false;
			$wp_query->queried_object = $term;
			$wp_query->queried_object_id = $term->term_id;
			$wp_query->set('taxonomy', 'product_filters');
			$wp_query->set('term', $term_slug);
			
			// Force our template - check multiple possible locations
			$template_paths = [
				get_template_directory() . '/taxonomy-product-size.php',
				get_template_directory() . '/taxonomy-product_filters.php',
			];
			
			foreach ($template_paths as $template_path) {
				if (file_exists($template_path)) {
					if (defined('WP_DEBUG') && WP_DEBUG) {
						error_log('Forcing taxonomy template: ' . $template_path);
					}
					return $template_path;
				}
			}
		}
	}
	
	return $template;
}, 1); // Priority 1 to run early

/**
 * Debug: Log which template is being used (only if WP_DEBUG is enabled)
 */
if (defined('WP_DEBUG') && WP_DEBUG) {
	add_filter('template_include', function($template) {
		if (is_tax('product_filters')) {
			$term = get_queried_object();
			error_log('Taxonomy Template Debug:');
			error_log('  - Is taxonomy: ' . (is_tax() ? 'Yes' : 'No'));
			error_log('  - Taxonomy name: ' . ($term->taxonomy ?? 'N/A'));
			error_log('  - Term name: ' . ($term->name ?? 'N/A'));
			error_log('  - Template being used: ' . $template);
			error_log('  - Template exists: ' . (file_exists($template) ? 'Yes' : 'No'));
		}
		return $template;
	}, 999);
}
// Add filters.
add_filter('excerpt_length', 'new_excerpt_length');
add_filter('excerpt_more', 'new_excerpt_more');
add_filter('nav_menu_css_class', 'fix_blog_menu_css_class', 10, 2);
add_filter('wpseo_metabox_prio', 'change_seo_metabox_priority');
// add_filter('allowed_block_types', 'acf_allowed_blocks');
add_filter('mce_buttons', 'wysiwyg_add_formats_select');
add_filter('tiny_mce_before_init', 'wysiwyg_custom_formats');
// add_filter( 'load_separate_block_styles', '__return_true' );

/**
 * Allow SVG and other file types for uploads
 * This is especially important during imports
 */
add_filter('upload_mimes', function($mimes) {
	// Allow SVG files
	$mimes['svg'] = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	
	// Allow other common file types that might be needed
	$mimes['webp'] = 'image/webp';
	$mimes['ico'] = 'image/x-icon';
	$mimes['webm'] = 'video/webm';
	$mimes['mp4'] = 'video/mp4';
	$mimes['mov'] = 'video/quicktime';
	$mimes['pdf'] = 'application/pdf';
	
	return $mimes;
}, 10, 1);

/**
 * Fix MIME type detection for SVG files during upload/import
 */
add_filter('wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
	$filetype = wp_check_filetype($filename, $mimes);
	
	// Fix SVG detection
	if ($filetype['ext'] === 'svg' || $filetype['ext'] === 'svgz') {
		$data = array(
			'ext' => $filetype['ext'],
			'type' => 'image/svg+xml',
			'proper_filename' => $filename
		);
	}
	
	return $data;
}, 10, 4);


function input_to_button( $button, $form ) {
    $fragment = WP_HTML_Processor::create_fragment( $button );
    $fragment->next_token();
 
    $attributes = array( 'id', 'type', 'class', 'onclick' );
    $new_attributes = array();
    foreach ( $attributes as $attribute ) {
        $value = $fragment->get_attribute( $attribute );
        if ( ! empty( $value ) ) {
            $new_attributes[] = sprintf( '%s="%s"', $attribute, esc_attr( $value ) );
        }
    }
 
    return sprintf( '<button %s>%s</button>', implode( ' ', $new_attributes ), esc_html( $fragment->get_attribute( 'value' ) ) );
}
add_filter( 'gform_submit_button', 'input_to_button', 10, 2 );

/**
 * Filter product archive to only show parent products (products with no parent)
 */
function filter_product_archive_to_parents_only($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('product') || (is_archive() && $query->get('post_type') === 'product')) {
            $query->set('post_parent', 0);
        }
    }
}
add_action('pre_get_posts', 'filter_product_archive_to_parents_only');