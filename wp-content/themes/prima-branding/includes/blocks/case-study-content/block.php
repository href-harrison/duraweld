<?php
/**
 * Basic Example
 */

/**
 * This is an argument that we provide to the render template
 */
$data = array(
	'sidebar_header_1' => get_field('sidebar_header_1') ?? false,
	'sidebar_header_2' => get_field('sidebar_header_2') ?? false,
	'case_studies_relation' => get_field('case_studies_relation') ?? false,
	'client_name' => get_field('client_name') ?? false,
	'brief' => get_field('brief') ?? false,
	'date' => get_field('date') ?? false,
	'case_study_content' => get_field('case_study_content') ?? false,
	'header' => get_field('header') ?? false,
	'client_logo' => get_field('client_logo') ?? false,
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