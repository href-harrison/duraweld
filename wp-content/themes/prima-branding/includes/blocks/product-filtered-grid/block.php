<?php
/**
 * Product Filtered Grid Block
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
 * Get block data
 */
$current_post_id = $post_id ?? get_the_ID();
$header = $block['data']['header'] ?? get_field('header', $post_id) ?? false;

// Get parent filter from block
// Try get_field first (handles ACF properly)
$parent_filter = get_field('parent_filter', $post_id);
if (!$parent_filter && isset($block['data']['parent_filter'])) {
	$parent_filter = $block['data']['parent_filter'];
}

$parent_filter_term = null;

// Convert parent filter to term object if needed
if ($parent_filter) {
	$parent_term_id = null;
	if (is_numeric($parent_filter)) {
		$parent_term_id = $parent_filter;
	} elseif (is_array($parent_filter)) {
		$parent_term_id = $parent_filter['term_id'] ?? $parent_filter['ID'] ?? $parent_filter['id'] ?? null;
	} elseif (is_object($parent_filter)) {
		$parent_term_id = $parent_filter->term_id ?? $parent_filter->ID ?? $parent_filter->id ?? null;
	}
	
	if ($parent_term_id) {
		$parent_filter_term = get_term($parent_term_id, 'product_filters');
		if (is_wp_error($parent_filter_term)) {
			$parent_filter_term = null;
		}
	}
}

$toggle_overlay = isset($block['data']['toggle_overlay']) ? $block['data']['toggle_overlay'] : (get_field('toggle_overlay', $post_id) !== false ? get_field('toggle_overlay', $post_id) : true);

// Get filter items from repeater field
// ACF stores repeaters as individual fields (filter_items_0_field_name, filter_items_1_field_name, etc.)
// The filter_items field itself is just a count
$filter_items = [];

// Try get_field first (this should work correctly)
$raw_items = get_field('filter_items', $post_id);
if (is_array($raw_items) && !empty($raw_items)) {
	$filter_items = $raw_items;
} else {
	// If get_field doesn't work, try to reconstruct from block data
	// Check if we have the count
	$item_count = 0;
	if (isset($block['data']['filter_items']) && is_numeric($block['data']['filter_items'])) {
		$item_count = intval($block['data']['filter_items']);
	} else {
		// Try to count how many items exist by checking for filter_items_0_filter_term, filter_items_1_filter_term, etc.
		$block_data = $block['data'] ?? [];
		$max_index = -1;
		foreach ($block_data as $key => $value) {
			if (preg_match('/^filter_items_(\d+)_filter_term$/', $key, $matches)) {
				$index = intval($matches[1]);
				if ($index > $max_index) {
					$max_index = $index;
				}
			}
		}
		if ($max_index >= 0) {
			$item_count = $max_index + 1;
		}
	}
	
	// Reconstruct repeater array from individual fields
	if ($item_count > 0) {
		for ($i = 0; $i < $item_count; $i++) {
			$item = [];
			
			// Get filter_term - try multiple methods
			$term_key = "filter_items_{$i}_filter_term";
			$filter_term_value = null;
			
			// Method 1: From block data
			if (isset($block['data'][$term_key])) {
				$filter_term_value = $block['data'][$term_key];
			}
			
			// Method 2: Using get_field with field key
			if (!$filter_term_value) {
				$filter_term_value = get_field("filter_items_{$i}_filter_term", $post_id);
			}
			
			// Method 3: Using get_field with full path
			if (!$filter_term_value) {
				$filter_term_value = get_field("filter_items.{$i}.filter_term", $post_id);
			}
			
			$item['filter_term'] = $filter_term_value;
			
			// Get filter_image - try multiple methods
			$image_key = "filter_items_{$i}_filter_image";
			$filter_image_value = null;
			
			// Method 1: From block data
			if (isset($block['data'][$image_key])) {
				$filter_image_value = $block['data'][$image_key];
			}
			
			// Method 2: Using get_field with field key
			if (!$filter_image_value) {
				$filter_image_value = get_field("filter_items_{$i}_filter_image", $post_id);
			}
			
			// Method 3: Using get_field with full path
			if (!$filter_image_value) {
				$filter_image_value = get_field("filter_items.{$i}.filter_image", $post_id);
			}
			
			$item['filter_image'] = $filter_image_value;
			
			// Debug: Log what we found
			if (defined('WP_DEBUG') && WP_DEBUG) {
				if ($filter_term_value) {
					error_log("Product Filtered Grid: Found filter_term for item {$i}: " . print_r($filter_term_value, true));
				} else {
					error_log("Product Filtered Grid: No filter_term found for item {$i}");
				}
			}
			
			// Only add item if it has a filter_term
			if (!empty($item['filter_term'])) {
				$filter_items[] = $item;
			}
		}
	}
}

// Debug: Log what we received
if (defined('WP_DEBUG') && WP_DEBUG) {
	if (empty($filter_items)) {
		error_log('Product Filtered Grid: No filter items found after reconstruction. Block data keys: ' . implode(', ', array_keys($block['data'] ?? [])));
	} else {
		error_log('Product Filtered Grid: Found ' . count($filter_items) . ' filter items');
	}
}

// Process filter items - ensure terms are valid and belong to parent
// Auto-populate products for each filter term
$processed_items = [];
if (!empty($filter_items) && is_array($filter_items)) {
	foreach ($filter_items as $item) {
		$filter_term = $item['filter_term'] ?? null;
		
		if (!$filter_term) {
			continue; // Skip if no filter term
		}
		
		// Convert filter term to term object - handle all ACF return formats
		$term_id = null;
		if (is_numeric($filter_term)) {
			$term_id = $filter_term;
		} elseif (is_array($filter_term)) {
			// ACF can return array with term_id, ID, or the term object properties
			$term_id = $filter_term['term_id'] ?? $filter_term['ID'] ?? $filter_term['id'] ?? null;
		} elseif (is_object($filter_term)) {
			// ACF can return WP_Term object or object with term_id property
			$term_id = $filter_term->term_id ?? $filter_term->ID ?? $filter_term->id ?? null;
		}
		
		if (!$term_id) {
			// Debug: log what we received
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('Product Filtered Grid: Could not extract term ID from: ' . print_r($filter_term, true));
			}
			continue; // Skip if we can't get term ID
		}
		
		// Get the term object
		$filter_term_obj = get_term($term_id, 'product_filters');
		
		if (!$filter_term_obj || is_wp_error($filter_term_obj)) {
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('Product Filtered Grid: Invalid term ID: ' . $term_id);
			}
			continue; // Skip invalid terms
		}
		
		// Verify term belongs to selected parent (if parent filter is set)
		// Only validate if parent filter is explicitly set and we want to filter by it
		// For now, we'll show all filter terms regardless of parent (user manually selects which ones to show)
		$should_include = true;
		
		if ($parent_filter_term && !is_wp_error($parent_filter_term)) {
			$term_parent_id = $filter_term_obj->parent ?? 0;
			$parent_term_id = $parent_filter_term->term_id ?? 0;
			
			// Check if term is a child of the selected parent (direct or indirect)
			$is_child = false;
			if ($term_parent_id == $parent_term_id) {
				$is_child = true; // Direct child
			} else {
				// Check if it's an indirect child (grandchild, etc.)
				$ancestors = get_ancestors($filter_term_obj->term_id, 'product_filters', 'taxonomy');
				if (in_array($parent_term_id, $ancestors)) {
					$is_child = true;
				}
			}
			
			// Note: We're not filtering by parent anymore - user manually selects which terms to show
			// Parent filter is just for reference/organization
			// If you want to auto-filter, uncomment the line below:
			// $should_include = $is_child;
		}
		
		// If we get here, the term is valid - add it to processed items
		if ($filter_term_obj && $should_include) {
			// Get link to filter page (taxonomy archive)
			$filter_link = get_term_link($filter_term_obj, 'product_filters');
			if (is_wp_error($filter_link)) {
				// Fallback: create link with query parameter
				$filter_link = add_query_arg('filter_term', $filter_term_obj->term_id, get_permalink($current_post_id));
			}
			
			// Store the filter term with its image and link
			// Products will be displayed on the taxonomy archive page when clicked
			$processed_items[] = [
				'term' => $filter_term_obj,
				'image' => $item['filter_image'] ?? null, // Filter image from block settings
				'link' => $filter_link,
			];
		}
	}
}

$data = array(
	'filter_items' => $processed_items,
	'header' => $header,
	'parent_filter' => $parent_filter_term,
	'toggle_overlay' => $toggle_overlay,
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