<?php
/**
 * Basic Example
 * 
 * ACF includes this file directly and passes variables via function parameters
 * Check if variables are available, if not they come from ACF's action hook
 */

// ACF passes these as function parameters when including the template
// If not set, they come from the action hook context
if (!isset($block)) {
	// Variables might be in function parameters - ACF passes: $block, $content, $is_preview, $post_id, $wp_block, $context
	// This shouldn't normally happen, but handle it gracefully
	return;
}

/**
 * This is an argument that we provide to the render template
 * ACF provides block data in $block['data'], use that to avoid duplicate rendering
 * For relationship fields, get_field() returns post objects, so use that as fallback
 */
$product_relationship = false;
if (isset($block['data']['product_relationship']) && !empty($block['data']['product_relationship'])) {
	$product_relationship = $block['data']['product_relationship'];
	// Ensure it's an array of post objects
	if (!is_array($product_relationship)) {
		$product_relationship = array($product_relationship);
	}
	// If items don't have ->ID property, they might be IDs - convert them
	if (!empty($product_relationship) && (!is_object($product_relationship[0]) || !isset($product_relationship[0]->ID))) {
		$product_relationship = array_map(function($item) {
			$id = is_object($item) ? $item->ID : $item;
			return get_post($id);
		}, $product_relationship);
		$product_relationship = array_filter($product_relationship);
	}
} else {
	// Fallback to get_field which returns post objects for relationship fields
	$product_relationship = get_field('product_relationship', $post_id) ?: false;
}

$data = array(
	'product_relationship' => $product_relationship,
);


/**
 * Getting the block directory name for use in screenshot rendering and template
 * rendering
 */
$block_directory_name = basename(__DIR__);

/**
 * Assigning a unique block ID to identify this specific block
 */
$block_id = $block_directory_name . '-' . $block['id'];

/**
 * Check if a custom anchor has been set in the CMS, if it has, use that as the ID.
 */
if (!empty($block['metadata']['name'])) {
	$block_id = $block['metadata']['name'];
}

/**
 * Adding the block directory name as a class, if you need to append additional
 * classes, you can do so after the space
 */

 $class_name = "$block_directory_name ";
 if (!empty($block['className'])) {
	 $class_name .= ' ' . $block['className'];
 }

if(!empty($block['backgroundColor'])) {
	$class_name .= ' has-bg bg-colour-' . $block['backgroundColor'];
}


/**
 * Conditional to render block screenshot 
 * in block preview editor mode.
 */
if ($screenshot_url = $block['data']['preview-screenshot'] ?? false) {
	$screenshot_url = get_template_directory_uri() . "/includes/blocks/$block_directory_name/block-preview.png";
	echo "<img style='max-width: 100%; height: auto;' src='$screenshot_url'></img>";
} else {
	/** 
	 * Pass the block data into the template part, we include the block template as a template part,
	 * this means we can use the block elsewhere by adding different information.
	 * 
	 * Use static variable to prevent double rendering
	 */
	static $rendered_blocks = array();
	$render_key = $block['id'] ?? uniqid();
	
	if (!isset($rendered_blocks[$render_key])) {
		$rendered_blocks[$render_key] = true;
		
		get_template_part(
			"includes/blocks/" . $block_directory_name . "/template",
			null,
			array(
				'block' => $block,
				'is_preview' => $is_preview,
				'post_id' => $post_id,
				'data' => $data,
				'class_name' => $class_name,
				'block_id' => $block_id,
			)
		);
	}
}