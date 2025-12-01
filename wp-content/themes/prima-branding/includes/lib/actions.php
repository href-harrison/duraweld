<?php
// Remove actions.
remove_action('wp_head', 'print_emoji_detection_script', 7);

// Add actions.
add_action('wp_footer', 'print_emoji_detection_script', 7);
add_action('wp_enqueue_scripts', 'script_enqueues');
add_action('acf/init', 'acf_add_maps_api_key');
// add_action('acf/init', 'acf_register_blocks');
add_action('admin_head', 'editor_full_width_gutenberg');

/**
 * admin AJAX function example
 * add_action('wp_ajax_example_admin_ajax', 'example_admin_ajax');
 * add_action('wp_ajax_nopriv_example_admin_ajax', 'example_admin_ajax');
 */
