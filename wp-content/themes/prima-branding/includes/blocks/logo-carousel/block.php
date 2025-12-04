<?php
/**
 * Basic Example
 */

/**
 * This is an argument that we provide to the render template
 * Check if we should use site options or override with block-specific logos
 */
$use_site_options = isset($block['data']['use_site_options']) ? $block['data']['use_site_options'] : (get_field('use_site_options', $post_id) ?? true);

$header = false;
$logo_repeater = false;

if ($use_site_options) {
	// Use logos from site options
	$site_brand_logos = get_field('brand_logos', 'option');
	if ($site_brand_logos) {
		$header = $site_brand_logos['header'] ?? false;
		$logo_repeater = $site_brand_logos['logo_repeater'] ?? false;
	}
} else {
	// Use block-specific logos
	$header = isset($block['data']['header']) ? $block['data']['header'] : (get_field('header', $post_id) ?? false);
	$logo_repeater = isset($block['data']['logo_repeater']) ? $block['data']['logo_repeater'] : (get_field('logo_repeater', $post_id) ?? false);
}

$data = array(
	'header' => $header,
	'logo_repeater' => $logo_repeater,
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