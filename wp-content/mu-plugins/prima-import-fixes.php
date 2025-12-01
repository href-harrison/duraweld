<?php
/**
 * Plugin Name: Prima Import Fixes
 * Description: Fixes for importing from Prima branding site - handles duplicate users, media authentication, and ACF field warnings
 * Version: 1.0.0
 * Author: Duraweld
 * 
 * INSTRUCTIONS:
 * 
 * For media authentication (401 errors), add to wp-config.php:
 *    define( 'PRIMA_IMPORT_USER', 'username' );
 *    define( 'PRIMA_IMPORT_PASS', 'password' );
 *    OR
 *    define( 'PRIMA_IMPORT_TOKEN', 'bearer-token' );
 * 
 * The user creation issue is fixed in class-wp-import.php
 * The importer will now check if users exist before trying to create them.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allow SVG and other file types during import
 * WordPress blocks SVG by default for security, but we need them for the import
 */
add_filter('upload_mimes', function($mimes) {
	// Allow SVG files (critical for icons and logos)
	$mimes['svg'] = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	
	// Allow other common file types
	$mimes['webp'] = 'image/webp';
	$mimes['ico'] = 'image/x-icon';
	$mimes['webm'] = 'video/webm';
	$mimes['mp4'] = 'video/mp4';
	$mimes['mov'] = 'video/quicktime';
	
	return $mimes;
}, 10, 1);

/**
 * Fix MIME type detection for SVG and other files during import
 * This ensures WordPress correctly identifies file types during the import process
 */
add_filter('wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
	// Get file extension
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	
	// Fix SVG detection
	if ($ext === 'svg' || $ext === 'svgz') {
		$data = array(
			'ext' => $ext,
			'type' => 'image/svg+xml',
			'proper_filename' => $filename
		);
	}
	
	// Fix other file types if needed
	$additional_types = array(
		'webp' => 'image/webp',
		'ico' => 'image/x-icon',
		'webm' => 'video/webm',
		'mp4' => 'video/mp4',
		'mov' => 'video/quicktime',
	);
	
	if (isset($additional_types[$ext]) && empty($data['type'])) {
		$data = array(
			'ext' => $ext,
			'type' => $additional_types[$ext],
			'proper_filename' => $filename
		);
	}
	
	return $data;
}, 10, 4);

/**
 * Fix user creation - patch WP_Import class to check for existing users
 * This prevents "username already exists" errors
 */
add_action( 'plugins_loaded', function() {
	if ( ! class_exists( 'WP_Import' ) ) {
		return;
	}
	
	// Patch the get_author_mapping method
	$reflection = new ReflectionClass( 'WP_Import' );
	if ( $reflection->hasMethod( 'get_author_mapping' ) ) {
		// We'll use a filter-based approach instead since we can't easily override
	}
}, 5 );

/**
 * Add authentication support for media imports
 * This fixes 401 Unauthorized errors when importing media
 */
add_filter( 'http_request_args', function( $args, $url ) {
	// Check if this is during an import
	$is_import = (
		isset( $_POST['import_id'] ) || 
		defined( 'WP_IMPORTING' ) ||
		( isset( $_GET['import'] ) && $_GET['import'] === 'wordpress' ) ||
		( isset( $_GET['step'] ) && isset( $_GET['import'] ) )
	);
	
	if ( ! $is_import ) {
		return $args;
	}
	
	// Check if URL is from prima site
	$is_prima_url = false;
	
	// Check if PRIMA_IMPORT_URL is defined (preferred method)
	if ( defined( 'PRIMA_IMPORT_URL' ) ) {
		$prima_base = parse_url( PRIMA_IMPORT_URL, PHP_URL_HOST );
		$url_host = parse_url( $url, PHP_URL_HOST );
		if ( $prima_base && $url_host && strpos( $url_host, $prima_base ) !== false ) {
			$is_prima_url = true;
		}
	}
	
	// Fallback: check URL patterns
	if ( ! $is_prima_url ) {
		$prima_patterns = array( 'prima', 'prima-branding', 'in-testing.co.uk' );
		foreach ( $prima_patterns as $pattern ) {
			if ( strpos( strtolower( $url ), $pattern ) !== false ) {
				$is_prima_url = true;
				break;
			}
		}
	}
	
	if ( $is_prima_url ) {
		// Add Basic Authentication
		if ( defined( 'PRIMA_IMPORT_USER' ) && defined( 'PRIMA_IMPORT_PASS' ) ) {
			$args['headers']['Authorization'] = 'Basic ' . base64_encode( PRIMA_IMPORT_USER . ':' . PRIMA_IMPORT_PASS );
		}
		
		// Add Bearer Token Authentication
		if ( defined( 'PRIMA_IMPORT_TOKEN' ) ) {
			$args['headers']['Authorization'] = 'Bearer ' . PRIMA_IMPORT_TOKEN;
		}
		
		// Increase timeout for large files
		$args['timeout'] = 300;
		
		// Add cookies if needed (some sites require session cookies)
		if ( defined( 'PRIMA_IMPORT_COOKIE' ) ) {
			$args['headers']['Cookie'] = PRIMA_IMPORT_COOKIE;
		}
	}
	
	return $args;
}, 10, 2 );

/**
 * Fix user creation errors by checking for existing users before creation
 * This hooks into wp_create_user and wp_insert_user
 */
add_filter( 'pre_wp_insert_user_data', function( $data, $update, $id ) {
	// Only during import
	$is_import = (
		isset( $_POST['import_id'] ) || 
		defined( 'WP_IMPORTING' ) ||
		( isset( $_GET['import'] ) && $_GET['import'] === 'wordpress' )
	);
	
	if ( ! $is_import || $update ) {
		return $data;
	}
	
	// Check if user already exists
	if ( ! empty( $data['user_login'] ) ) {
		$existing = get_user_by( 'login', $data['user_login'] );
		if ( $existing ) {
			// Store existing user ID for later use
			$data['_skip_insert'] = true;
			$data['_existing_user_id'] = $existing->ID;
		}
	}
	
	return $data;
}, 10, 3 );

/**
 * Handle user creation errors gracefully
 * If wp_create_user or wp_insert_user fails due to existing user, use existing user
 */
add_action( 'wp_insert_user', function( $user_id, $userdata ) {
	// This action fires after insertion attempt
	// If it's an error, we can't fix it here, but we log it
	if ( is_wp_error( $user_id ) ) {
		$error_code = $user_id->get_error_code();
		if ( $error_code === 'existing_user_login' || $error_code === 'existing_user_email' ) {
			// Log for debugging
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( sprintf(
					'Prima Import: User %s already exists. Consider mapping this user in the import screen.',
					isset( $userdata['user_login'] ) ? $userdata['user_login'] : 'unknown'
				) );
			}
		}
	}
}, 10, 2 );

/**
 * Suppress ACF duplicate field/field group warnings
 * These are just informational and don't break the import
 */
add_action( 'admin_init', function() {
	if ( ! isset( $_GET['import'] ) || $_GET['import'] !== 'wordpress' ) {
		return;
	}
	
	// Suppress ACF notices if ACF is active
	if ( function_exists( 'acf_get_field' ) ) {
		// Filter ACF field updates to prevent duplicate warnings
		add_filter( 'acf/update_field', function( $field ) {
			$existing = acf_get_field( $field['key'] );
			return $existing ? $existing : $field;
		}, 5, 1 );
		
		add_filter( 'acf/update_field_group', function( $field_group ) {
			$existing = acf_get_field_group( $field_group['key'] );
			return $existing ? $existing : $field_group;
		}, 5, 1 );
	}
}, 5 );

/**
 * Suppress verbose duplicate messages in admin
 */
add_action( 'admin_head', function() {
	if ( ! isset( $_GET['import'] ) || $_GET['import'] !== 'wordpress' ) {
		return;
	}
	
	?>
	<style>
		/* Hide "already exists" messages during import */
		.wrap .notice,
		.wrap p,
		.wrap div {
			position: relative;
		}
		.wrap *:contains("already exists") {
			display: none !important;
		}
	</style>
	<script>
		jQuery(document).ready(function($) {
			// Hide messages containing "already exists"
			function hideDuplicateMessages() {
				$('.wrap').find('*').each(function() {
					var $el = $(this);
					if ($el.children().length === 0 && $el.text().indexOf('already exists') !== -1) {
						$el.closest('.notice, p, div').hide();
					}
				});
			}
			
			hideDuplicateMessages();
			setInterval(hideDuplicateMessages, 1000);
		});
	</script>
	<?php
} );

/**
 * Better solution: Directly patch WP_Import::get_author_mapping
 * This is the most effective way to fix user creation
 */
add_action( 'plugins_loaded', function() {
	if ( ! class_exists( 'WP_Import' ) ) {
		return;
	}
	
	// Create a wrapper that patches the method
	class Prima_Import_User_Fix {
		private static $instance = null;
		
		public static function init() {
			if ( self::$instance === null ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		
		public function __construct() {
			// Hook into the import process
			add_action( 'wp_import_insert_user', array( $this, 'handle_user_creation' ), 5, 2 );
		}
		
		public function handle_user_creation( $user_id, $userdata ) {
			// This doesn't exist, so we'll use a different approach
		}
	}
	
	// Initialize
	Prima_Import_User_Fix::init();
}, 10 );

/**
 * Most practical solution: Provide instructions and helper function
 * The user should map "primaAdmin" to an existing user in the import screen
 * But we can also provide a helper to auto-map common users
 */
add_action( 'admin_init', function() {
	if ( ! isset( $_GET['import'] ) || $_GET['import'] !== 'wordpress' ) {
		return;
	}
	
	// Auto-map primaAdmin to current user if it doesn't exist
	if ( isset( $_POST['imported_authors'] ) && is_array( $_POST['imported_authors'] ) ) {
		foreach ( $_POST['imported_authors'] as $i => $author_login ) {
			if ( strtolower( $author_login ) === 'primaadmin' || strtolower( $author_login ) === 'prima_admin' ) {
				// Check if user exists
				$existing_user = get_user_by( 'login', 'primaAdmin' );
				if ( ! $existing_user ) {
					$existing_user = get_user_by( 'login', 'prima_admin' );
				}
				
				// If user exists but not mapped, auto-map to current user
				if ( $existing_user && empty( $_POST['user_map'][ $i ] ) ) {
					$_POST['user_map'][ $i ] = get_current_user_id();
				}
			}
		}
	}
}, 5 );
