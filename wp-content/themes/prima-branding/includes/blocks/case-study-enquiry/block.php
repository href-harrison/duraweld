<?php
/**
 * Basic Example
 */

/**
 * This is an argument that we provide to the render template
 */
$data = array(
	'header' => get_field('header') ?? false,
	'sidebar_header' => get_field('sidebar_header') ?? false,
	'form_shortcode' => get_field('form_shortcode') ?? false,
	'case_study_relation' => get_field('case_study_relation') ?? false,
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