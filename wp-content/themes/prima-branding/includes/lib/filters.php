<?php
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