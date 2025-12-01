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