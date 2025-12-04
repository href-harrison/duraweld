<?php
/**
 * Basic Example
 */

/**
 * This is an argument that we provide to the render template
 * ACF provides block data in $block['data'], use that to avoid duplicate rendering
 */
// Get field values - ACF blocks provide data in $block['data']
// Only use get_field as fallback if block data is not available
$data = array(
	'variant' => $block['data']['variant'] ?? 'default',
	'header' => $block['data']['header'] ?? false,
	'body_text' => $block['data']['body_text'] ?? false,
	'link' => $block['data']['link'] ?? false,
	'image' => $block['data']['image'] ?? false,
	'mobile_image' => $block['data']['mobile_image'] ?? false,
);

// Ensure images are arrays if they're IDs
if (!empty($data['image']) && is_numeric($data['image'])) {
	$data['image'] = acf_get_attachment($data['image']);
}
if (!empty($data['mobile_image']) && is_numeric($data['mobile_image'])) {
	$data['mobile_image'] = acf_get_attachment($data['mobile_image']);
}



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

// Add variant class
$variant = $data['variant'] ?? 'default';
if ($variant === 'image-only') {
	$class_name .= ' home-hero--image-only';
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