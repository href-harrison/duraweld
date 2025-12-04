<?php
/**
 * Product Category Template Block
 * 
 * A single block that contains all the product category page blocks in order
 */

if (!isset($block)) {
	return;
}

// Get all block data
$block_1_product_relationship = get_field('block_1_product_relationship') ?? array();
$block_wysiwyg = get_field('block_wysiwyg') ?? array();
$block_product_categories = get_field('block_product_categories') ?? array();
$block_2_product_relationship = get_field('block_2_product_relationship') ?? array();
$block_3_product_relationship = get_field('block_3_product_relationship') ?? array();
$block_logo_carousel = get_field('block_logo_carousel') ?? array();
$block_benefits = get_field('block_benefits') ?? array();

$block_directory_name = basename(__DIR__);
$block_id = $block_directory_name . '-' . $block['id'];

if (!empty($block['metadata']['name'])) {
	$block_id = $block['metadata']['name'];
}

$class_name = "$block_directory_name ";
if (!empty($block['className'])) {
	$class_name .= ' ' . $block['className'];
}

if(!empty($block['backgroundColor'])) {
	$class_name .= ' has-bg bg-colour-' . $block['backgroundColor'];
}

if ($screenshot_url = $block['data']['preview-screenshot'] ?? false) {
	$screenshot_url = get_template_directory_uri() . "/includes/blocks/$block_directory_name/block-preview.png";
	echo "<img style='max-width: 100%; height: auto;' src='$screenshot_url'></img>";
} else {
	// Render all blocks in order
	// Block 1: Product Relationship Grid
	if (!empty($block_1_product_relationship)) {
		render_template_block('product-relationship', $block_1_product_relationship, $block, $post_id, $is_preview, $block_id . '-1');
	}
	
	// Block 2: WYSIWYG Content
	if (!empty($block_wysiwyg)) {
		render_template_block('wysiwyg-content', $block_wysiwyg, $block, $post_id, $is_preview, $block_id . '-2');
	}
	
	// Block 3: Product Relationship Grid (Second)
	if (!empty($block_2_product_relationship)) {
		render_template_block('product-relationship', $block_2_product_relationship, $block, $post_id, $is_preview, $block_id . '-3');
	}
	
	// Block 4: Product Relationship Grid (Third)
	if (!empty($block_3_product_relationship)) {
		render_template_block('product-relationship', $block_3_product_relationship, $block, $post_id, $is_preview, $block_id . '-4');
	}
	
	// Block 5: Product Categories Grid (moved after third relationship grid)
	if (!empty($block_product_categories)) {
		render_template_block('product-categories', $block_product_categories, $block, $post_id, $is_preview, $block_id . '-5');
	}
	
	// Block 6: Logo Carousel
	if (!empty($block_logo_carousel)) {
		render_template_block('logo-carousel', $block_logo_carousel, $block, $post_id, $is_preview, $block_id . '-6');
	}
	
	// Block 7: Benefits Block
	if (!empty($block_benefits)) {
		render_template_block('benefits-block', $block_benefits, $block, $post_id, $is_preview, $block_id . '-7');
	}
}

/**
 * Helper function to render a block from template
 */
function render_template_block($block_name, $block_data, $parent_block, $post_id, $is_preview, $unique_id) {
	if (empty($block_data) || !is_array($block_data)) {
		return;
	}
	
	// Create block structure matching ACF block format
	$template_block = array(
		'id' => $unique_id,
		'data' => $block_data,
		'metadata' => $parent_block['metadata'] ?? array(),
		'className' => $parent_block['className'] ?? '',
		'backgroundColor' => $parent_block['backgroundColor'] ?? '',
	);
	
	// Get block directory
	$block_directory_name = $block_name;
	
	// Use get_template_part to render the block template
	// We'll pass the block data directly to the template
	$template_file = get_template_directory() . '/includes/blocks/' . $block_directory_name . '/template.php';
	
	if (file_exists($template_file)) {
		// Get the block's PHP file to process data
		$block_file = get_template_directory() . '/includes/blocks/' . $block_directory_name . '/block.php';
		
		if (file_exists($block_file)) {
			// Set up variables for the block
			$block = $template_block;
			$is_preview = $is_preview ?? false;
			$post_id = $post_id ?? false;
			
			// Include block file which will process data and call template
			include $block_file;
		}
	}
}

