<?php
/**
 * Contact Bar Block
 * 
 * ACF includes this file directly and passes variables via function parameters
 */

// ACF passes these as function parameters when including the template
if (!isset($block)) {
	return;
}

// Get is_preview from function parameters if available
$is_preview = $is_preview ?? false;

/**
 * Get field data from ACF
 * For ACF blocks with repeater fields, $block['data'] may only contain the row count.
 * We need to use get_field() which properly formats repeater data.
 */
$contact_items = false;

// For repeater fields in ACF blocks, get_field() without post_id uses block context
// This is the most reliable way to get properly formatted repeater data
$contact_items = get_field('contact_items') ?: false;

// If that didn't work and we have a post_id, try with post context
if (!$contact_items && isset($post_id)) {
	$contact_items = get_field('contact_items', $post_id) ?: false;
}

// If block data exists and is not just a number (row count), use it
if (!$contact_items && isset($block['data']['contact_items'])) {
	$raw_data = $block['data']['contact_items'];
	// Only use if it's actual array data, not just a count
	if (is_array($raw_data) && !is_numeric(key($raw_data))) {
		$contact_items = $raw_data;
	}
}

// Ensure contact_items is an array of arrays (repeater format)
if ($contact_items) {
	// If it's not an array, it's invalid
	if (!is_array($contact_items)) {
		$contact_items = false;
	} else {
		// Filter out any empty items and ensure proper structure
		$contact_items = array_filter($contact_items, function($item) {
			// Item should be an array with sub-fields (icon, heading, link)
			return is_array($item) && !empty($item);
		});
		// Re-index array after filtering
		if (!empty($contact_items)) {
			$contact_items = array_values($contact_items);
		} else {
			$contact_items = false;
		}
	}
}

// Ensure contact_items is an array and not empty
if ($contact_items && !is_array($contact_items)) {
	$contact_items = array($contact_items);
}

// Filter out any empty items
if ($contact_items && is_array($contact_items)) {
	$contact_items = array_filter($contact_items, function($item) {
		return !empty($item);
	});
	// Re-index array after filtering
	if (!empty($contact_items)) {
		$contact_items = array_values($contact_items);
	} else {
		$contact_items = false;
	}
}

$data = array(
	'contact_items' => $contact_items,
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
	echo "<img class='block-preview-image' src='$screenshot_url' alt='Block Preview'>";
} elseif ($is_preview && empty($contact_items)) {
	?>
	<div class="contact-bar-empty">
		<p><strong>Contact Bar Block</strong></p>
		<p>Add contact items using the repeater field in the sidebar.</p>
	</div>
	<?php
} else {
	/** 
	 * Pass the block data into the template part
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

