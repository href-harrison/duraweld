<?php
/**
 * Basic Example
 */

/**
 * This is an argument that we provide to the render template
 * Check if we should use site options or override with block-specific benefits
 */
$use_site_options = isset($block['data']['use_site_options']) ? $block['data']['use_site_options'] : (get_field('use_site_options', $post_id) ?? true);

$benefits_repeater = false;

if ($use_site_options) {
	// Use benefits from unified product benefits in site options
	$unified_benefits = get_field('product_benefits', 'option');
	if ($unified_benefits) {
		$benefits_repeater = $unified_benefits['benefits_repeater'] ?? false;
	}
} else {
	// Use block-specific benefits
	$benefits_repeater = isset($block['data']['benefits_repeater']) ? $block['data']['benefits_repeater'] : (get_field('benefits_repeater', $post_id) ?? false);
}

$data = array(
	'benefits_repeater' => $benefits_repeater,
	'use_site_options' => $use_site_options,
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
	 */
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