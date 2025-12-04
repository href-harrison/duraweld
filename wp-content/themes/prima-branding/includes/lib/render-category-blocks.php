<?php
/**
 * Render Product Category Blocks
 * 
 * Converts ACF flexible content blocks to ACF block format for rendering
 */

/**
 * Render category content blocks from flexible content
 */
function render_category_content_blocks($term_id) {
	if (!$term_id) {
		return;
	}
	
	// Get flexible content field
	if (!have_rows('category_content_blocks', 'product_category_' . $term_id)) {
		return;
	}
	
	// Loop through flexible content rows
	while (have_rows('category_content_blocks', 'product_category_' . $term_id)) {
		the_row();
		
		$layout = get_row_layout();
		
		// Render based on layout type
		switch ($layout) {
			case 'product_relationship':
				render_category_block_product_relationship($term_id);
				break;
				
			case 'wysiwyg':
				render_category_block_wysiwyg($term_id);
				break;
				
			case 'product_categories':
				render_category_block_product_categories($term_id);
				break;
				
			case 'logo_carousel':
				render_category_block_logo_carousel($term_id);
				break;
				
			case 'benefits':
				render_category_block_benefits($term_id);
				break;
		}
	}
}

/**
 * Render Product Relationship block from flexible content
 */
function render_category_block_product_relationship($term_id) {
	$block_data = get_sub_field('product_relationship_block');
	
	if (!$block_data) {
		return;
	}
	
	// Create block structure
	$block = array(
		'id' => 'category-block-' . uniqid(),
		'data' => $block_data,
		'metadata' => array(),
		'className' => '',
		'backgroundColor' => '',
	);
	
	// Get block directory
	$block_directory_name = 'product-relationship';
	
	// Include block PHP file
	$block_file = get_template_directory() . '/includes/blocks/' . $block_directory_name . '/block.php';
	
	if (file_exists($block_file)) {
		// Set up variables for block
		$post_id = false;
		$is_preview = false;
		
		// Include block file
		include $block_file;
	}
}

/**
 * Render WYSIWYG block from flexible content
 */
function render_category_block_wysiwyg($term_id) {
	$block_data = get_sub_field('wysiwyg_block');
	
	if (!$block_data) {
		return;
	}
	
	// Create block structure
	$block = array(
		'id' => 'category-block-' . uniqid(),
		'data' => $block_data,
		'metadata' => array(),
		'className' => '',
		'backgroundColor' => '',
	);
	
	// Get block directory
	$block_directory_name = 'wysiwyg-content';
	
	// Include block PHP file
	$block_file = get_template_directory() . '/includes/blocks/' . $block_directory_name . '/block.php';
	
	if (file_exists($block_file)) {
		// Set up variables for block
		$post_id = false;
		$is_preview = false;
		
		// Include block file
		include $block_file;
	}
}

/**
 * Render Product Categories block from flexible content
 */
function render_category_block_product_categories($term_id) {
	$block_data = get_sub_field('product_categories_block');
	
	if (!$block_data) {
		return;
	}
	
	// Create block structure
	$block = array(
		'id' => 'category-block-' . uniqid(),
		'data' => $block_data,
		'metadata' => array(),
		'className' => '',
		'backgroundColor' => '',
	);
	
	// Get block directory
	$block_directory_name = 'product-categories';
	
	// Include block PHP file
	$block_file = get_template_directory() . '/includes/blocks/' . $block_directory_name . '/block.php';
	
	if (file_exists($block_file)) {
		// Set up variables for block
		$post_id = false;
		$is_preview = false;
		
		// Include block file
		include $block_file;
	}
}

/**
 * Render Logo Carousel block from flexible content
 */
function render_category_block_logo_carousel($term_id) {
	$block_data = get_sub_field('logo_carousel_block');
	
	if (!$block_data) {
		return;
	}
	
	// Create block structure
	$block = array(
		'id' => 'category-block-' . uniqid(),
		'data' => $block_data,
		'metadata' => array(),
		'className' => '',
		'backgroundColor' => '',
	);
	
	// Get block directory
	$block_directory_name = 'logo-carousel';
	
	// Include block PHP file
	$block_file = get_template_directory() . '/includes/blocks/' . $block_directory_name . '/block.php';
	
	if (file_exists($block_file)) {
		// Set up variables for block
		$post_id = false;
		$is_preview = false;
		
		// Include block file
		include $block_file;
	}
}

/**
 * Render Benefits block from flexible content
 */
function render_category_block_benefits($term_id) {
	$block_data = get_sub_field('benefits_block');
	
	if (!$block_data) {
		return;
	}
	
	// Create block structure
	$block = array(
		'id' => 'category-block-' . uniqid(),
		'data' => $block_data,
		'metadata' => array(),
		'className' => '',
		'backgroundColor' => '',
	);
	
	// Get block directory
	$block_directory_name = 'benefits-block';
	
	// Include block PHP file
	$block_file = get_template_directory() . '/includes/blocks/' . $block_directory_name . '/block.php';
	
	if (file_exists($block_file)) {
		// Set up variables for block
		$post_id = false;
		$is_preview = false;
		
		// Include block file
		include $block_file;
	}
}

