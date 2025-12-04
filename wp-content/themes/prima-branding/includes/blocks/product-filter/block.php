<?php
/**
 * Product Filter Block
 * 
 * ACF includes this file directly and passes variables via function parameters
 */

if (!isset($block)) {
	return;
}

// Get block data
$header = $block['data']['header'] ?? get_field('header', $post_id) ?? false;
$show_size_filter = isset($block['data']['show_size_filter']) ? $block['data']['show_size_filter'] : (get_field('show_size_filter', $post_id) ?? true);
$show_style_filter = isset($block['data']['show_style_filter']) ? $block['data']['show_style_filter'] : (get_field('show_style_filter', $post_id) ?? true);

// Get all terms for filters
$size_terms = [];
$style_terms = [];

if ($show_size_filter && taxonomy_exists('product_size')) {
	$size_terms = get_terms([
		'taxonomy' => 'product_size',
		'hide_empty' => true,
	]);
}

if ($show_style_filter && taxonomy_exists('product_style')) {
	$style_terms = get_terms([
		'taxonomy' => 'product_style',
		'hide_empty' => true,
	]);
}

$data = array(
	'header' => $header,
	'show_size_filter' => $show_size_filter,
	'show_style_filter' => $show_style_filter,
	'size_terms' => $size_terms,
	'style_terms' => $style_terms,
);

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

