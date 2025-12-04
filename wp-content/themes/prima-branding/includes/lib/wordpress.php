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

/**
 * Fix meta database issues for ALL meta fields
 * This prevents "Could not update the meta value" errors for any problematic meta fields
 * Handles oversized values, corrupted data, and invalid types
 */
function fix_meta_database_issues() {
	// Known problematic meta keys that often cause issues
	$problematic_keys = [
		'ghostkit_', // All GhostKit keys
		'footnotes',
		'_elementor_',
		'_wp_page_template',
	];
	
	// JSON fields that need JSON validation
	$json_fields = [
		'ghostkit_customizer_options',
		'ghostkit_spacings',
		'ghostkit_attributes',
		'ghostkit_styles',
	];
	
	// Check if a meta key is problematic
	$is_problematic_key = function($meta_key) use ($problematic_keys) {
		foreach ($problematic_keys as $pattern) {
			if (strpos($meta_key, $pattern) === 0 || $meta_key === $pattern) {
				return true;
			}
		}
		return false;
	};
	
	// Hook into meta update to sanitize/validate the value before saving
	add_filter('update_post_metadata', function($check, $post_id, $meta_key, $meta_value) use ($is_problematic_key, $json_fields) {
		if ($is_problematic_key($meta_key)) {
			// If the value is too large for database, clear it
			if (is_string($meta_value) && strlen($meta_value) > 65535) {
				// Value is too large for database, clear it
				delete_post_meta($post_id, $meta_key);
				return true; // Prevent update
			}
			
			// For JSON fields, validate JSON
			if (in_array($meta_key, $json_fields)) {
				if (is_string($meta_value) && !empty($meta_value)) {
					$decoded = json_decode(urldecode($meta_value), true);
					if (json_last_error() !== JSON_ERROR_NONE) {
						// Invalid JSON, clear the corrupted value
						delete_post_meta($post_id, $meta_key);
						return true; // Prevent update
					}
				}
			}
			
			// For all other problematic fields, ensure they're valid types
			if (!in_array($meta_key, $json_fields)) {
				if (!is_string($meta_value) && !is_numeric($meta_value) && !is_bool($meta_value) && !is_null($meta_value) && !is_array($meta_value)) {
					// Invalid type, clear it
					delete_post_meta($post_id, $meta_key);
					return true; // Prevent update
				}
				
				// If it's an array, try to serialize it properly
				if (is_array($meta_value)) {
					$serialized = maybe_serialize($meta_value);
					if (strlen($serialized) > 65535) {
						// Serialized value too large, clear it
						delete_post_meta($post_id, $meta_key);
						return true; // Prevent update
					}
				}
			}
		}
		return $check;
	}, 10, 4);
	
	// Also hook into REST API meta update (used by Gutenberg)
	add_filter('rest_update_post_meta_value', function($value, $object_id, $field_name) use ($is_problematic_key, $json_fields) {
		if ($is_problematic_key($field_name)) {
			// Validate and sanitize the value
			if (is_string($value) && strlen($value) > 65535) {
				// Value too large, return empty
				return '';
			}
			
			// For JSON fields, validate JSON
			if (in_array($field_name, $json_fields)) {
				if (is_string($value) && !empty($value)) {
					$decoded = json_decode(urldecode($value), true);
					if (json_last_error() !== JSON_ERROR_NONE) {
						// Invalid JSON, return empty
						return '';
					}
				}
			}
			
			// For all other problematic fields, ensure they're valid types
			if (!in_array($field_name, $json_fields)) {
				if (!is_string($value) && !is_numeric($value) && !is_bool($value) && !is_null($value) && !is_array($value)) {
					return '';
				}
				
				// If it's an array, check serialized size
				if (is_array($value)) {
					$serialized = maybe_serialize($value);
					if (strlen($serialized) > 65535) {
						return '';
					}
				}
			}
		}
		return $value;
	}, 10, 3);
}
add_action('init', 'fix_meta_database_issues');

/**
 * Utility function to clear problematic meta fields from database
 * Run this once via WP-CLI or add ?clear_ghostkit_meta=1 to admin URL
 * This will delete ALL meta keys starting with 'ghostkit_' and other problematic keys
 */
function clear_corrupted_ghostkit_meta() {
	if (isset($_GET['clear_ghostkit_meta']) && current_user_can('manage_options') && !isset($_GET['cleared'])) {
		global $wpdb;
		
		// Delete ALL GhostKit meta keys (anything starting with 'ghostkit_')
		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
				'ghostkit_%'
			)
		);
		
		// Also delete footnotes if it's causing issues
		$deleted += $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
				'footnotes'
			)
		);
		
		// Redirect with success message instead of wp_die
		$redirect_url = add_query_arg([
			'cleared' => '1',
			'count' => $deleted
		], remove_query_arg('clear_ghostkit_meta'));
		
		wp_redirect($redirect_url);
		exit;
	}
	
		// Show admin notice if cleanup was successful
		if (isset($_GET['cleared']) && current_user_can('manage_options')) {
			$count = isset($_GET['count']) ? intval($_GET['count']) : 0;
			add_action('admin_notices', function() use ($count) {
				echo '<div class="notice notice-success is-dismissible"><p>';
				echo sprintf(
					esc_html__('Successfully cleared %d GhostKit meta entries (customizer options, custom CSS/JS, spacings, etc.).', 'prima-branding'),
					$count
				);
				echo '</p></div>';
			});
		}
}
add_action('admin_init', 'clear_corrupted_ghostkit_meta'); 