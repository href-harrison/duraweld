<?php
/**
 * Product Hierarchy Management
 * Makes it easy for non-technical users to set up product parent/child relationships
 */

/**
 * Ensure hierarchical permalinks work correctly for products
 * This enables parent/child URLs like: /products/parent/child/
 */
function ensure_product_hierarchical_permalinks() {
	global $wp_rewrite;
	
	// Get the product post type object
	$product_post_type = get_post_type_object('product');
	
	if ($product_post_type && $product_post_type->hierarchical) {
		// Ensure rewrite rules are set up for hierarchical products
		$product_post_type->rewrite = wp_parse_args($product_post_type->rewrite, [
			'slug' => 'products',
			'with_front' => true,
			'feeds' => false,
			'pages' => true,
			'hierarchical' => true,
		]);
		
		// Flush rewrite rules if needed (only on admin init to avoid performance issues)
		if (is_admin() && isset($_GET['flush_rewrite_rules'])) {
			flush_rewrite_rules(false);
		}
	}
}
add_action('init', 'ensure_product_hierarchical_permalinks', 20);

/**
 * Add "Duplicate" action to product row actions
 * Products are hierarchical, so we need to hook into page_row_actions filter
 */
function add_product_duplicate_action($actions, $post) {
	// Check if post exists and is a product
	if (!$post || !isset($post->post_type) || $post->post_type !== 'product') {
		return $actions;
	}
	
	// Check user permissions
	if (!current_user_can('edit_posts')) {
		return $actions;
	}
	
	// Build duplicate URL with nonce
	$duplicate_url = wp_nonce_url(
		admin_url('admin.php?action=duplicate_product&post=' . absint($post->ID)),
		'duplicate_product_' . $post->ID,
		'duplicate_nonce'
	);
	
	// Add duplicate action
	$actions['duplicate'] = '<a href="' . esc_url($duplicate_url) . '" title="' . esc_attr__('Duplicate this product', 'prima-branding') . '">' . __('Duplicate', 'prima-branding') . '</a>';
	
	return $actions;
}
// Products are hierarchical, so use page_row_actions filter (this is the main one)
add_filter('page_row_actions', 'add_product_duplicate_action', 10, 2);
// Also add to post_row_actions as fallback for non-hierarchical cases
add_filter('post_row_actions', 'add_product_duplicate_action', 10, 2);

/**
 * Handle product duplication
 */
function handle_product_duplication() {
	// Check if this is a duplicate request
	if (!isset($_GET['action']) || $_GET['action'] !== 'duplicate_product') {
		return;
	}
	
	// Check nonce
	if (!isset($_GET['duplicate_nonce']) || !isset($_GET['post'])) {
		wp_die(__('Security check failed', 'prima-branding'));
	}
	
	$post_id = absint($_GET['post']);
	if (!wp_verify_nonce($_GET['duplicate_nonce'], 'duplicate_product_' . $post_id)) {
		wp_die(__('Security check failed', 'prima-branding'));
	}
	
	// Check permissions
	if (!current_user_can('edit_posts')) {
		wp_die(__('You do not have permission to duplicate products', 'prima-branding'));
	}
	
	// Get the original post
	$original_post = get_post($post_id);
	if (!$original_post || $original_post->post_type !== 'product') {
		wp_die(__('Product not found', 'prima-branding'));
	}
	
	// Duplicate the product
	$new_post_id = duplicate_product($post_id);
	
	if ($new_post_id && !is_wp_error($new_post_id)) {
		// Redirect to edit the new product
		wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
		exit;
	} else {
		wp_die(__('Failed to duplicate product', 'prima-branding'));
	}
}
add_action('admin_action_duplicate_product', 'handle_product_duplication');

/**
 * Duplicate a product with all its data
 */
function duplicate_product($post_id) {
	// Get the original post
	$post = get_post($post_id);
	if (!$post) {
		return false;
	}
	
	// Prepare new post data
	$new_post_data = array(
		'post_title'     => 'Copy of ' . $post->post_title,
		'post_content'   => $post->post_content,
		'post_excerpt'   => $post->post_excerpt,
		'post_status'    => 'draft',
		'post_type'      => $post->post_type,
		'post_author'    => get_current_user_id(),
		'post_parent'    => $post->post_parent,
		'menu_order'     => $post->menu_order,
		'comment_status' => $post->comment_status,
		'ping_status'    => $post->ping_status,
	);
	
	// Insert the new post
	$new_post_id = wp_insert_post($new_post_data);
	
	if (is_wp_error($new_post_id)) {
		return false;
	}
	
	// Duplicate post meta (including ACF fields)
	$meta_keys = get_post_custom_keys($post_id);
	if ($meta_keys) {
		foreach ($meta_keys as $meta_key) {
			// Skip internal WordPress meta
			if (strpos($meta_key, '_wp_') === 0 && $meta_key !== '_wp_page_template') {
				continue;
			}
			
			$meta_values = get_post_custom_values($meta_key, $post_id);
			foreach ($meta_values as $meta_value) {
				$meta_value = maybe_unserialize($meta_value);
				add_post_meta($new_post_id, $meta_key, $meta_value);
			}
		}
	}
	
	// Duplicate taxonomies
	$taxonomies = get_object_taxonomies($post->post_type);
	foreach ($taxonomies as $taxonomy) {
		$terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
		if ($terms && !is_wp_error($terms)) {
			wp_set_object_terms($new_post_id, $terms, $taxonomy);
		}
	}
	
	// Duplicate featured image
	$thumbnail_id = get_post_thumbnail_id($post_id);
	if ($thumbnail_id) {
		set_post_thumbnail($new_post_id, $thumbnail_id);
	}
	
	// Clear ACF cache for the new post
	if (function_exists('acf_get_store')) {
		acf_get_store('values')->remove($new_post_id);
	}
	
	return $new_post_id;
}

/**
 * Add visual hierarchy meta box to product edit screen
 */
function add_product_hierarchy_meta_box() {
	add_meta_box(
		'product-hierarchy',
		'Product Hierarchy',
		'render_product_hierarchy_meta_box',
		'product',
		'side',
		'high'
	);
}
add_action('add_meta_boxes', 'add_product_hierarchy_meta_box');


/**
 * Render the hierarchy meta box
 */
function render_product_hierarchy_meta_box($post) {
	$parent_id = $post->post_parent;
	$children = get_children([
		'post_parent' => $post->ID,
		'post_type' => 'product',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC'
	]);
	
	$is_category_page = get_field('is_category_page', $post->ID);
	
	wp_nonce_field('product_hierarchy_meta_box', 'product_hierarchy_meta_box_nonce');
	?>
	<div class="product-hierarchy-info">
		<?php if ($parent_id) : 
			$parent = get_post($parent_id);
		?>
			<div class="hierarchy-section">
				<strong>📁 Parent Product:</strong>
				<p>
					<a href="<?php echo get_edit_post_link($parent_id); ?>" target="_blank">
						<?php echo esc_html($parent->post_title); ?>
					</a>
				</p>
				<p class="description">
					This product belongs under "<?php echo esc_html($parent->post_title); ?>". 
					To change the parent, use the "Parent" dropdown in the Page Attributes box.
				</p>
			</div>
		<?php else : ?>
			<div class="hierarchy-section">
				<strong>📦 Top-Level Product</strong>
				<p class="description">
					This is a top-level product. To make it a child of another product, 
					select a parent in the "Parent" dropdown in the Page Attributes box.
				</p>
			</div>
		<?php endif; ?>
		
		<?php if (!empty($children)) : ?>
			<div class="hierarchy-section">
				<strong>👶 Child Products (<?php echo count($children); ?>):</strong>
				<ul class="child-products-list">
					<?php foreach ($children as $child) : ?>
						<li>
							<a href="<?php echo get_edit_post_link($child->ID); ?>" target="_blank">
								<?php echo esc_html($child->post_title); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="description">
					<?php if ($is_category_page) : ?>
						✅ <strong>Category Page Mode:</strong> These child products will automatically 
						display on this page when you add a "Product Relationship" block.
					<?php else : ?>
						💡 <strong>Tip:</strong> Enable "Is Category Page" below to automatically display 
						these child products on this page.
					<?php endif; ?>
				</p>
			</div>
		<?php else : ?>
			<div class="hierarchy-section">
				<strong>👶 No Child Products</strong>
				<p class="description">
					This product has no child products. To add children:
					<ol style="margin-left: 20px; margin-top: 5px;">
						<li>Create or edit a product</li>
						<li>In the "Parent" dropdown (Page Attributes), select this product</li>
						<li>The child will appear here automatically</li>
					</ol>
				</p>
			</div>
		<?php endif; ?>
		
		<div class="hierarchy-section" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
			<strong>💡 How It Works:</strong>
			<ul style="margin-left: 20px; margin-top: 5px;">
				<li><strong>Top-level products</strong> can have child products</li>
				<li><strong>Child products</strong> belong under a parent</li>
				<li>Enable "Is Category Page" to auto-display children</li>
				<li>Use "Parent" dropdown to set relationships</li>
			</ul>
		</div>
	</div>
	
	<style>
		.product-hierarchy-info .hierarchy-section {
			margin-bottom: 15px;
		}
		.product-hierarchy-info .hierarchy-section strong {
			display: block;
			margin-bottom: 5px;
			color: #23282d;
		}
		.product-hierarchy-info .child-products-list {
			margin: 8px 0;
			padding-left: 20px;
		}
		.product-hierarchy-info .child-products-list li {
			margin: 5px 0;
		}
		.product-hierarchy-info .description {
			font-size: 12px;
			color: #646970;
			margin-top: 5px;
		}
	</style>
	<?php
}

/**
 * Add hierarchy columns to products list
 */
function add_product_hierarchy_columns($columns) {
	// Insert after title
	$new_columns = [];
	foreach ($columns as $key => $value) {
		$new_columns[$key] = $value;
		if ($key === 'title') {
			$new_columns['product_parent'] = 'Parent';
			$new_columns['product_children'] = 'Children';
			$new_columns['is_category'] = 'Category Page';
		}
	}
	return $new_columns;
}
add_filter('manage_product_posts_columns', 'add_product_hierarchy_columns');

/**
 * Display hierarchy data in columns
 */
function display_product_hierarchy_columns($column, $post_id) {
	switch ($column) {
		case 'product_parent':
			$parent_id = wp_get_post_parent_id($post_id);
			if ($parent_id) {
				$parent = get_post($parent_id);
				echo '<a href="' . get_edit_post_link($parent_id) . '">';
				echo esc_html($parent->post_title);
				echo '</a>';
			} else {
				echo '<span style="color: #999;">— Top Level —</span>';
			}
			break;
			
		case 'product_children':
			$children = get_children([
				'post_parent' => $post_id,
				'post_type' => 'product',
				'post_status' => 'publish',
				'numberposts' => -1
			]);
			$count = count($children);
			if ($count > 0) {
				echo '<strong>' . $count . '</strong> ';
				echo $count === 1 ? 'child' : 'children';
				if ($count <= 3) {
					echo '<br><small style="color: #666;">';
					$titles = array_slice(array_map(function($c) { return $c->post_title; }, $children), 0, 3);
					echo esc_html(implode(', ', $titles));
					echo '</small>';
				}
			} else {
				echo '<span style="color: #999;">—</span>';
			}
			break;
			
		case 'is_category':
			$is_category = get_field('is_category_page', $post_id);
			$has_children = !empty(get_children([
				'post_parent' => $post_id,
				'post_type' => 'product',
				'numberposts' => 1,
				'post_status' => 'publish'
			]));
			
			// Add data attribute for Quick Edit JavaScript
			$category_value = ($is_category || $has_children) ? '1' : '0';
			echo '<div class="is-category-page-value" data-category-page="' . esc_attr($category_value) . '">';
			
			if ($is_category || $has_children) {
				echo '<span style="color: #00a32a;">✅ Yes</span>';
				if ($has_children && !$is_category) {
					echo '<br><small style="color: #666;">(Auto-detected)</small>';
				}
			} else {
				echo '<span style="color: #999;">—</span>';
			}
			echo '</div>';
			break;
	}
}
add_action('manage_product_posts_custom_column', 'display_product_hierarchy_columns', 10, 2);

/**
 * Make hierarchy columns sortable
 */
function make_product_hierarchy_columns_sortable($columns) {
	$columns['product_parent'] = 'parent';
	$columns['product_children'] = 'children';
	return $columns;
}
add_filter('manage_edit-product_sortable_columns', 'make_product_hierarchy_columns_sortable');

/**
 * Sort products hierarchically in admin list
 * Children appear under their parents, unassigned products at bottom
 */
function sort_products_hierarchically($query) {
	// Only apply to admin product list
	if (!is_admin() || !$query->is_main_query()) {
		return;
	}
	
	$screen = get_current_screen();
	if (!$screen || $screen->post_type !== 'product' || $screen->id !== 'edit-product') {
		return;
	}
	
	// Don't override if user is sorting by a specific column (except default/menu_order)
	$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
	if ($orderby && $orderby !== 'menu_order' && $orderby !== 'parent') {
		return;
	}
	
	// Modify the query to sort hierarchically
	add_filter('posts_orderby', 'hierarchical_product_orderby', 10, 2);
}
add_action('pre_get_posts', 'sort_products_hierarchically');

/**
 * Custom ORDER BY clause for hierarchical product sorting
 */
function hierarchical_product_orderby($orderby, $query) {
	global $wpdb;
	
	// Only apply to product queries
	if (!isset($query->query_vars['post_type']) || $query->query_vars['post_type'] !== 'product') {
		return $orderby;
	}
	
	// Build hierarchical sort:
	// Priority groups:
	// 0 = Parents with children (post_parent = 0 AND has children)
	// 1 = Children (post_parent != 0)
	// 2 = Unassigned (post_parent = 0 AND no children)
	//
	// Then sort by:
	// - Group key (parent ID for children, own ID for parents)
	// - menu_order
	// - title
	
	$orderby = "
		CASE 
			WHEN {$wpdb->posts}.post_parent = 0 THEN 
				-- Check if this parent has children
				CASE 
					WHEN EXISTS (
						SELECT 1 FROM {$wpdb->posts} AS children 
						WHERE children.post_parent = {$wpdb->posts}.ID 
						AND children.post_type = 'product'
						LIMIT 1
					) THEN 0  -- Parent with children: priority 0
					ELSE 2     -- Unassigned (no children): priority 2
				END
			ELSE 1  -- Children: priority 1
		END ASC,
		CASE 
			WHEN {$wpdb->posts}.post_parent = 0 THEN {$wpdb->posts}.ID
			ELSE {$wpdb->posts}.post_parent
		END ASC,
		{$wpdb->posts}.post_parent ASC,
		{$wpdb->posts}.menu_order ASC,
		{$wpdb->posts}.post_title ASC
	";
	
	return $orderby;
}

/**
 * Improve the "Is Category Page" ACF field with better instructions
 */
function improve_category_page_field_instructions() {
	// This will be handled by updating the ACF JSON file
}
add_action('acf/init', 'improve_category_page_field_instructions');

/**
 * Add "Is Category Page" field to Quick Edit
 */
function add_category_page_to_quick_edit($column_name, $post_type) {
	if ($post_type !== 'product' || $column_name !== 'is_category') {
		return;
	}
	?>
	<fieldset class="inline-edit-col-right">
		<div class="inline-edit-col">
			<label class="inline-edit-categories">
				<span class="title">Category</span>
				<span class="checkbox-title">
					<input type="checkbox" name="is_category_page" value="1" />
					<span class="checkbox-title">Enable Category Page Mode</span>
				</span>
			</label>
		</div>
	</fieldset>
	<?php
}
add_action('quick_edit_custom_box', 'add_category_page_to_quick_edit', 10, 2);

/**
 * Add "Is Category Page" field to Bulk Edit
 */
function add_category_page_to_bulk_edit($column_name, $post_type) {
	if ($post_type !== 'product' || $column_name !== 'is_category') {
		return;
	}
	?>
	<fieldset class="inline-edit-col-right inline-edit-category">
		<div class="inline-edit-col">
			<label class="inline-edit-categories">
				<span class="title">Category</span>
				<div class="bulk-edit-category-page">
					<select name="is_category_page">
						<option value="-1">— No Change —</option>
						<option value="1">Enable Category Page Mode</option>
						<option value="0">Disable Category Page Mode</option>
					</select>
				</div>
			</label>
		</div>
	</fieldset>
	<?php
}
add_action('bulk_edit_custom_box', 'add_category_page_to_bulk_edit', 10, 2);

/**
 * Add JavaScript to populate Quick Edit form with current category page value
 */
function populate_quick_edit_category_page() {
	global $post_type;
	if ($post_type !== 'product') {
		return;
	}
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Store original value when Quick Edit is opened
		var $wp_inline_edit = inlineEditPost.edit;
		inlineEditPost.edit = function(id) {
			// Call original function
			$wp_inline_edit.apply(this, arguments);
			
			// Get post ID
			var post_id = 0;
			if (typeof(id) === 'object') {
				post_id = parseInt(this.getId(id));
			}
			
			if (post_id > 0) {
				// Get the row
				var $row = $('#post-' + post_id);
				var $inline_row = $('#edit-' + post_id);
				
				// Get current category page value from data attribute
				var $categoryValue = $row.find('.column-is_category .is-category-page-value');
				var isCategoryPage = $categoryValue.data('category-page') === '1';
				
				// Set checkbox value
				$inline_row.find('input[name="is_category_page"]').prop('checked', isCategoryPage);
			}
		};
	});
	</script>
	<?php
}
add_action('admin_footer-edit.php', 'populate_quick_edit_category_page');

/**
 * Save "Is Category Page" field from Quick Edit
 */
function save_quick_edit_category_page($post_id) {
	// Check if this is an autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	
	// Check user permissions
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}
	
	// Check if this is the correct post type
	if (get_post_type($post_id) !== 'product') {
		return;
	}
	
	// Check if this is a Quick Edit save
	// Quick Edit includes '_inline_edit' in the POST data
	if (!isset($_POST['_inline_edit'])) {
		return; // Not a Quick Edit save
	}
	
	// Verify nonce for Quick Edit
	if (!wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
		return;
	}
	
	// Save the category page field
	if (isset($_POST['is_category_page']) && $_POST['is_category_page'] === '1') {
		update_field('is_category_page', 1, $post_id);
	} else {
		update_field('is_category_page', 0, $post_id);
	}
}
add_action('save_post', 'save_quick_edit_category_page');

/**
 * Save "Is Category Page" field from Bulk Edit
 */
function save_bulk_edit_category_page($post_id) {
	// Check if this is an autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	
	// Check user permissions
	if (!current_user_can('edit_post', $post_id)) {
		return;
	}
	
	// Check if this is the correct post type
	if (get_post_type($post_id) !== 'product') {
		return;
	}
	
	// Check if this is a bulk edit save
	// Bulk edit includes 'bulk_edit' in the POST data
	if (!isset($_REQUEST['bulk_edit'])) {
		return; // Not a bulk edit save
	}
	
	// Check if category page value was set and is not "no change"
	if (!isset($_REQUEST['is_category_page']) || $_REQUEST['is_category_page'] === '-1') {
		return; // No change requested
	}
	
	$category_page_value = intval($_REQUEST['is_category_page']);
	
	// Save the category page field
	update_field('is_category_page', $category_page_value, $post_id);
}
add_action('save_post', 'save_bulk_edit_category_page');

/**
 * Add contextual help to product edit screen
 */
function add_product_hierarchy_help_tab() {
	$screen = get_current_screen();
	if ($screen && $screen->post_type === 'product') {
		$screen->add_help_tab([
			'id' => 'product-hierarchy-help',
			'title' => 'Product Hierarchy',
			'content' => '
				<h3>Understanding Product Hierarchy</h3>
				<p><strong>Parent Products (Category Pages):</strong></p>
				<ul>
					<li>Top-level products that can have child products</li>
					<li>Enable "Display Child Products Automatically" to automatically display child products</li>
					<li>Useful for organizing products into categories</li>
				</ul>
				<p><strong>Child Products:</strong></p>
				<ul>
					<li>Products that belong under a parent product</li>
					<li>Set the parent using the "Parent" dropdown in Page Attributes</li>
					<li>Will automatically appear on the parent page if it\'s a category page</li>
				</ul>
				<p><strong>Setting Up Hierarchy:</strong></p>
				<ol>
					<li>Create your parent product (e.g., "Industrial Doors")</li>
					<li>Enable "Display Child Products Automatically" on the parent</li>
					<li>Create child products and set the parent in Page Attributes</li>
					<li>Add a "Product Relationship" block to the parent page - children will appear automatically!</li>
				</ol>
			'
		]);
	}
}
add_action('admin_head', 'add_product_hierarchy_help_tab');

/**
 * Ensure Page Attributes meta box is visible for products
 * This ensures the Parent dropdown is available immediately
 * Run at multiple priorities to catch post type registration at different times
 */
function ensure_page_attributes_for_products() {
	// Add page-attributes support (needed for Parent dropdown)
	// This must be done early, before meta boxes are registered
	add_post_type_support('product', 'page-attributes');
}
// Run at multiple priorities to ensure it works regardless of when post type is registered
add_action('init', 'ensure_page_attributes_for_products', 5);
add_action('init', 'ensure_page_attributes_for_products', 99);

/**
 * Force Page Attributes meta box to show for products
 * Runs at multiple priorities to ensure it's available immediately
 */
function force_page_attributes_meta_box_for_products() {
	// Check if we're on the product edit screen
	$screen = get_current_screen();
	if (!$screen || $screen->post_type !== 'product') {
		return;
	}
	
	// Ensure the post type is hierarchical
	$post_type_obj = get_post_type_object('product');
	if (!$post_type_obj || !$post_type_obj->hierarchical) {
		return;
	}
	
	// Ensure page-attributes support is added (double-check)
	add_post_type_support('product', 'page-attributes');
	
	// Remove any existing pageparentdiv to avoid duplicates
	remove_meta_box('pageparentdiv', 'product', 'side');
	
	// Add it back with proper settings - use high priority to ensure it shows
	add_meta_box(
		'pageparentdiv',
		__('Page Attributes'),
		'page_attributes_meta_box',
		'product',
		'side',
		'core',
		null
	);
}
// Run at multiple priorities to catch different registration times
add_action('add_meta_boxes', 'force_page_attributes_meta_box_for_products', 1);
add_action('add_meta_boxes', 'force_page_attributes_meta_box_for_products', 99);

/**
 * Add JavaScript to ensure Page Attributes meta box is visible immediately
 */
function ensure_page_attributes_visible_js() {
	$screen = get_current_screen();
	if (!$screen || $screen->post_type !== 'product' || $screen->base !== 'post') {
		return;
	}
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Prevent any reloads - use sessionStorage to track if we've already tried
		var reloadKey = 'prima_product_page_attributes_checked';
		if (sessionStorage.getItem(reloadKey)) {
			// Already checked, don't do anything that might cause reloads
			return;
		}
		sessionStorage.setItem(reloadKey, '1');
		
		// Clear the flag after 5 seconds to allow for future checks if needed
		setTimeout(function() {
			sessionStorage.removeItem(reloadKey);
		}, 5000);
		
		function ensurePageAttributesVisible() {
			// Don't interfere if WordPress is saving
			if ($('body').hasClass('wp-saving') || 
			    $('#post').hasClass('saving') || 
			    $('#publish').hasClass('disabled') ||
			    $('#save-post').hasClass('disabled')) {
				return false; // Don't interfere with save operations
			}
			
		// Ensure Page Attributes meta box is visible
		var pageAttributesBox = $('#pageparentdiv');
		if (pageAttributesBox.length) {
			// Make sure it's not hidden
			pageAttributesBox.show();
			
			// If it's in a closed postbox, open it
			if (pageAttributesBox.hasClass('closed')) {
				pageAttributesBox.removeClass('closed');
			}
			
			// Ensure the parent dropdown is visible and functional
			var parentDropdown = $('#parent_id');
			if (parentDropdown.length && parentDropdown.is(':hidden')) {
				parentDropdown.show();
			}
				return true; // Meta box found
			}
			return false; // Meta box not found
		}
		
		// Try immediately
		ensurePageAttributesVisible();
		
		// If meta box doesn't exist yet, wait a bit and try again (WordPress might still be loading)
		// But NEVER reload - the PHP code should ensure the meta box is registered
		setTimeout(function() {
			ensurePageAttributesVisible();
		}, 300);
		
		// One more attempt after a longer delay (for slow loading)
			setTimeout(function() {
			ensurePageAttributesVisible();
		}, 1000);
		
		// Listen for WordPress save events and prevent any interference
		$(document).on('heartbeat-send', function() {
			// WordPress is auto-saving, don't interfere
		});
		
		// Prevent any accidental form submissions that might cause reloads
		$('#post').on('submit', function(e) {
			// Let WordPress handle the form submission normally
			// Don't prevent default, just ensure we're not interfering
		});
	});
	</script>
	<?php
}
add_action('admin_footer', 'ensure_page_attributes_visible_js');

/**
 * Get or create "Products" menu item in header menu
 * Returns the menu item ID for the Products parent item
 */
function get_or_create_products_menu_item($menu_id) {
	// Check if "Products" menu item already exists
	$menu_items = wp_get_nav_menu_items($menu_id);
	
	foreach ($menu_items as $item) {
		if ($item->title === 'Products' && $item->menu_item_parent == 0) {
			return $item->ID;
		}
	}
	
	// Create "Products" menu item if it doesn't exist
	$products_item_id = wp_update_nav_menu_item($menu_id, 0, [
		'menu-item-title' => 'Products',
		'menu-item-type' => 'custom',
		'menu-item-url' => '#',
		'menu-item-status' => 'publish',
		'menu-item-position' => 999,
	]);
	
	return $products_item_id;
}

/**
 * Sync products to menu manually
 * This function can be called to sync all products to the navigation menu
 */
function sync_products_to_menu() {
	// Get header menu
	$menu_locations = get_nav_menu_locations();
	if (empty($menu_locations['header'])) {
		return [
			'success' => false,
			'message' => 'Header menu not found. Please assign a menu to the "Header" location first.'
		];
	}
	
	$menu_id = $menu_locations['header'];
	
	// Get or create "Products" parent menu item
	$products_parent_id = get_or_create_products_menu_item($menu_id);
	
	// Get all published products
	$products = get_posts([
		'post_type' => 'product',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby' => 'menu_order',
		'order' => 'ASC'
	]);
	
	if (empty($products)) {
		return [
			'success' => false,
			'message' => 'No published products found.'
		];
	}
	
	// Get existing menu items
	$menu_items = wp_get_nav_menu_items($menu_id);
	$existing_product_ids = [];
	foreach ($menu_items as $item) {
		if ($item->object === 'product') {
			$existing_product_ids[$item->object_id] = $item->ID;
		}
	}
	
	$added = 0;
	$updated = 0;
	$skipped = 0;
	$product_menu_ids = []; // Track menu item IDs as we add them
	
	// STEP 1: Add all parent products (top-level products) FIRST
	// This ensures all parents exist in the menu before we try to add children
	$parent_products = [];
	foreach ($products as $product) {
		$parent_id = wp_get_post_parent_id($product->ID);
		
		// Collect only top-level products (no parent)
		if (!$parent_id) {
			$parent_products[] = $product;
		}
	}
	
	// Process all parent products first
	foreach ($parent_products as $product) {
		$has_children = !empty(get_children([
			'post_parent' => $product->ID,
			'post_type' => 'product',
			'numberposts' => 1,
			'post_status' => 'publish'
		]));
		
		$is_category_page = get_field('is_category_page', $product->ID);
		
		// Only add if it's a category page or has children
		if ($has_children || $is_category_page) {
			$menu_item_id = null;
			
			if (isset($existing_product_ids[$product->ID])) {
				// Update existing menu item
				$menu_item_id = $existing_product_ids[$product->ID];
				$result = wp_update_nav_menu_item($menu_id, $menu_item_id, [
					'menu-item-parent-id' => $products_parent_id,
				]);
				
				if (!is_wp_error($result)) {
				$updated++;
				}
			} else {
				// Add new menu item
				$menu_item_id = wp_update_nav_menu_item($menu_id, 0, [
					'menu-item-object-id' => $product->ID,
					'menu-item-object' => 'product',
					'menu-item-type' => 'post_type',
					'menu-item-title' => $product->post_title,
					'menu-item-status' => 'publish',
					'menu-item-parent-id' => $products_parent_id,
				]);
				
				// wp_update_nav_menu_item returns the menu item ID or WP_Error
				if (is_wp_error($menu_item_id)) {
					continue; // Skip if there was an error
				}
				
				$added++;
			}
			
			// Store the menu item ID for this product so children can find it
			if ($menu_item_id && !is_wp_error($menu_item_id)) {
				$product_menu_ids[$product->ID] = $menu_item_id;
			}
		} else {
			$skipped++;
		}
	}
	
	// STEP 2: Refresh menu items AFTER all parents are added
	// This ensures we have the complete list of parent menu IDs before processing children
	$menu_items = wp_get_nav_menu_items($menu_id);
	foreach ($menu_items as $item) {
		if ($item->object === 'product') {
			// Update our tracking array with all product menu items
			// This includes parents we just added and any existing ones
			$product_menu_ids[$item->object_id] = $item->ID;
		}
	}
	
	// Now add child products under their parents (recursively handles grandchildren)
	// We need to process in order: parents first, then children, then grandchildren
	$processed_products = [];
	$max_depth = 10; // Safety limit
	
	for ($depth = 1; $depth <= $max_depth; $depth++) {
		$found_children = false;
		
		foreach ($products as $product) {
			// Skip if already processed
			if (isset($processed_products[$product->ID])) {
				continue;
			}
			
			$parent_id = wp_get_post_parent_id($product->ID);
			
			// Skip top-level products (already processed)
			if (!$parent_id) {
				continue;
			}
			
			// Check if parent is a top-level product (already processed in first loop)
			// OR if parent has been processed in a previous depth iteration
			$parent_is_top_level = !$parent_id || !wp_get_post_parent_id($parent_id);
			$parent_processed = $parent_is_top_level || isset($processed_products[$parent_id]);
			
			if ($parent_processed) {
				// Find parent in menu
				$parent_menu_id = null;
				
				// Check if parent is in menu
				if (isset($product_menu_ids[$parent_id])) {
					$parent_menu_id = $product_menu_ids[$parent_id];
				} else {
					// Parent not in menu - this shouldn't happen if parent has children
					// But if it does, we'll add child under "Products" as fallback
					$parent_menu_id = $products_parent_id;
				}
				
				if ($parent_menu_id) {
					$menu_item_id = null;
					
					// Add or update child menu item
					if (isset($existing_product_ids[$product->ID])) {
						// Update existing
						$menu_item_id = $existing_product_ids[$product->ID];
						wp_update_nav_menu_item($menu_id, $menu_item_id, [
							'menu-item-parent-id' => $parent_menu_id,
						]);
						$updated++;
					} else {
						// Add new
						$menu_item_id = wp_update_nav_menu_item($menu_id, 0, [
							'menu-item-object-id' => $product->ID,
							'menu-item-object' => 'product',
							'menu-item-type' => 'post_type',
							'menu-item-title' => $product->post_title,
							'menu-item-status' => 'publish',
							'menu-item-parent-id' => $parent_menu_id,
						]);
						
						// wp_update_nav_menu_item returns the menu item ID or WP_Error
						if (is_wp_error($menu_item_id)) {
							continue; // Skip if there was an error
						}
						
						$added++;
					}
						
					// Store the new menu item ID so grandchildren can find this parent
					if ($menu_item_id) {
						$product_menu_ids[$product->ID] = $menu_item_id;
					}
					
					$processed_products[$product->ID] = true;
					$found_children = true;
				}
			}
		}
		
		// Refresh menu items after each depth level
		if ($found_children) {
			$menu_items = wp_get_nav_menu_items($menu_id);
			foreach ($menu_items as $item) {
				if ($item->object === 'product') {
					$product_menu_ids[$item->object_id] = $item->ID;
				}
			}
		} else {
			// No more children found, break
			break;
		}
	}
	
	return [
		'success' => true,
		'message' => sprintf(
			'Synced products to menu: %d added, %d updated, %d skipped (not category pages).',
			$added,
			$updated,
			$skipped
		),
		'added' => $added,
		'updated' => $updated,
		'skipped' => $skipped
	];
}

/**
 * Add admin page for manual product menu sync
 */
function add_product_menu_sync_page() {
	add_submenu_page(
		'edit.php?post_type=product',
		'Sync Products to Menu',
		'Sync to Menu',
		'manage_options',
		'sync-products-menu',
		'render_product_menu_sync_page'
	);
}
add_action('admin_menu', 'add_product_menu_sync_page', 20);

/**
 * Add sync button to Products list page
 */
function add_sync_button_to_products_list() {
	global $typenow;
	if ($typenow === 'product') {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Add sync button after page title
			if ($('.wp-heading-inline').length) {
				$('.wp-heading-inline').after(
					'<a href="<?php echo admin_url('edit.php?post_type=product&page=sync-products-menu'); ?>" class="page-title-action" style="margin-left: 10px;">Sync Products to Menu</a>'
				);
				$('.wp-heading-inline').after(
					'<a href="<?php echo admin_url('edit.php?post_type=product&page=sync-categories-footer-menu'); ?>" class="page-title-action" style="margin-left: 10px;">Sync Categories to Footer</a>'
				);
			}
		});
		</script>
		<?php
	}
}
add_action('admin_footer', 'add_sync_button_to_products_list');

/**
 * Add sync link to admin bar
 */
function add_sync_to_admin_bar($wp_admin_bar) {
	if (!current_user_can('manage_options')) {
		return;
	}
	
	global $typenow;
	if ($typenow === 'product' || (isset($_GET['post_type']) && $_GET['post_type'] === 'product')) {
		$wp_admin_bar->add_node([
			'id' => 'sync-products-menu',
			'title' => 'Sync Products to Menu',
			'href' => admin_url('edit.php?post_type=product&page=sync-products-menu'),
			'parent' => false,
		]);
	}
}
add_action('admin_bar_menu', 'add_sync_to_admin_bar', 100);

/**
 * Render the sync page
 */
function render_product_menu_sync_page() {
	if (isset($_POST['sync_products_menu']) && check_admin_referer('sync_products_menu_action')) {
		$result = sync_products_to_menu();
		$notice_class = $result['success'] ? 'notice-success' : 'notice-error';
		?>
		<div class="notice <?php echo $notice_class; ?> is-dismissible">
			<p><?php echo esc_html($result['message']); ?></p>
		</div>
		<?php
	}
	
	$menu_locations = get_nav_menu_locations();
	$has_header_menu = !empty($menu_locations['header']);
	?>
	<div class="wrap">
		<h1>Sync Products to Navigation Menu</h1>
		
		<?php if (!$has_header_menu) : ?>
			<div class="notice notice-warning">
				<p><strong>Warning:</strong> No menu assigned to "Header" location. Please go to <a href="<?php echo admin_url('nav-menus.php'); ?>">Appearance → Menus</a> and assign a menu to the "Header" location first.</p>
			</div>
		<?php endif; ?>
		
		<div class="card" style="max-width: 600px;">
			<h2>Manual Sync</h2>
			<p>This will sync all published products to the navigation menu based on their hierarchy:</p>
			<ul style="margin-left: 20px;">
				<li><strong>Parent products</strong> (category pages) will be added under "Products"</li>
				<li><strong>Child products</strong> will be added under their parent (or under "Products" if parent not in menu)</li>
				<li>Only products with "Display Child Products Automatically" enabled or that have children will be added</li>
			</ul>
			
			<form method="post" style="margin-top: 20px;">
				<?php wp_nonce_field('sync_products_menu_action'); ?>
				<input type="submit" name="sync_products_menu" class="button button-primary button-large" value="Sync Products to Menu" <?php echo $has_header_menu ? '' : 'disabled'; ?>>
			</form>
		</div>
		
		<div class="card" style="max-width: 600px; margin-top: 20px;">
			<h2>How It Works</h2>
			<ol style="margin-left: 20px;">
				<li>Creates a "Products" menu item if it doesn't exist</li>
				<li>Adds all parent products (category pages) under "Products"</li>
				<li>Adds all child products under their parent in the menu</li>
				<li>Updates existing menu items if products are already in the menu</li>
				<li>Maintains the product hierarchy structure</li>
			</ol>
		</div>
	</div>
	<?php
}

/**
 * Add CSS class to child product menu items for styling
 * This allows child products to be positioned at the top of the second column
 */
function add_product_child_menu_class($classes, $item, $args) {
	// Only apply to header menu
	if (!isset($args->theme_location) || $args->theme_location !== 'header') {
		return $classes;
	}
	
	// Check if this is a product menu item
	if ($item->object === 'product') {
		// Check if this product has a parent (is a child product)
		$parent_id = wp_get_post_parent_id($item->object_id);
		if ($parent_id) {
			// This is a child product - add special classes
			$classes[] = 'menu-item-product-child';
			$classes[] = 'child-sub-menu-item';
		}
	}
	
	return $classes;
}
add_filter('nav_menu_css_class', 'add_product_child_menu_class', 10, 3);

/**
 * Sync product categories to footer menu 2 (footer_3)
 * Adds all product categories as menu items to the footer menu
 */
function sync_product_categories_to_footer_menu() {
	// Get footer menu 2 location
	$menu_locations = get_nav_menu_locations();
	if (empty($menu_locations['footer_3'])) {
		return [
			'success' => false,
			'message' => 'Footer menu 2 not found. Please assign a menu to the "Footer Navigation 3" location first.'
		];
	}
	
	$menu_id = $menu_locations['footer_3'];
	
	// Get all product categories
	$categories = get_terms([
		'taxonomy' => 'product_category',
		'hide_empty' => false,
		'orderby' => 'name',
		'order' => 'ASC',
	]);
	
	if (is_wp_error($categories) || empty($categories)) {
		return [
			'success' => false,
			'message' => 'No product categories found.'
		];
	}
	
	// Get existing menu items
	$menu_items = wp_get_nav_menu_items($menu_id);
	$existing_category_ids = [];
	foreach ($menu_items as $item) {
		if ($item->object === 'product_category') {
			$existing_category_ids[$item->object_id] = $item->ID;
		}
	}
	
	$added = 0;
	$updated = 0;
	
	// Add or update each category
	foreach ($categories as $category) {
		$category_url = get_term_link($category);
		
		if (is_wp_error($category_url)) {
			continue; // Skip if term link fails
		}
		
		if (isset($existing_category_ids[$category->term_id])) {
			// Update existing menu item
			$menu_item_id = $existing_category_ids[$category->term_id];
			$result = wp_update_nav_menu_item($menu_id, $menu_item_id, [
				'menu-item-title' => $category->name,
				'menu-item-url' => $category_url,
				'menu-item-status' => 'publish',
			]);
			
			if (!is_wp_error($result)) {
				$updated++;
			}
		} else {
			// Add new menu item
			$menu_item_id = wp_update_nav_menu_item($menu_id, 0, [
				'menu-item-object-id' => $category->term_id,
				'menu-item-object' => 'product_category',
				'menu-item-type' => 'taxonomy',
				'menu-item-title' => $category->name,
				'menu-item-url' => $category_url,
				'menu-item-status' => 'publish',
			]);
			
			if (!is_wp_error($menu_item_id)) {
				$added++;
			}
		}
	}
	
	return [
		'success' => true,
		'message' => sprintf(
			'Synced product categories to footer menu: %d added, %d updated.',
			$added,
			$updated
		),
		'added' => $added,
		'updated' => $updated
	];
}

/**
 * Add admin page for syncing product categories to footer menu
 */
function add_product_categories_footer_menu_sync_page() {
	add_submenu_page(
		'edit.php?post_type=product',
		'Sync Categories to Footer Menu',
		'Sync Categories to Footer',
		'manage_options',
		'sync-categories-footer-menu',
		'render_product_categories_footer_menu_sync_page'
	);
}
add_action('admin_menu', 'add_product_categories_footer_menu_sync_page', 20);

/**
 * Render the sync page for product categories to footer menu
 */
function render_product_categories_footer_menu_sync_page() {
	if (isset($_POST['sync_categories_footer_menu']) && check_admin_referer('sync_categories_footer_menu_action')) {
		$result = sync_product_categories_to_footer_menu();
		$notice_class = $result['success'] ? 'notice-success' : 'notice-error';
		?>
		<div class="notice <?php echo $notice_class; ?> is-dismissible">
			<p><?php echo esc_html($result['message']); ?></p>
		</div>
		<?php
	}
	
	$menu_locations = get_nav_menu_locations();
	$has_footer_menu = !empty($menu_locations['footer_3']);
	?>
	<div class="wrap">
		<h1>Sync Product Categories to Footer Menu 2</h1>
		
		<?php if (!$has_footer_menu) : ?>
			<div class="notice notice-warning">
				<p><strong>Warning:</strong> No menu assigned to "Footer Navigation 3" location. Please go to <a href="<?php echo admin_url('nav-menus.php'); ?>">Appearance → Menus</a> and assign a menu to the "Footer Navigation 3" location first.</p>
			</div>
		<?php endif; ?>
		
		<div class="card" style="max-width: 600px;">
			<h2>Manual Sync</h2>
			<p>This will sync all product categories to Footer Menu 2:</p>
			<ul style="margin-left: 20px;">
				<li>All product categories will be added as menu items</li>
				<li>Existing menu items will be updated</li>
				<li>Categories are ordered alphabetically by name</li>
			</ul>
			
			<form method="post" style="margin-top: 20px;">
				<?php wp_nonce_field('sync_categories_footer_menu_action'); ?>
				<input type="submit" name="sync_categories_footer_menu" class="button button-primary button-large" value="Sync Categories to Footer Menu 2" <?php echo $has_footer_menu ? '' : 'disabled'; ?>>
			</form>
		</div>
		
		<div class="card" style="max-width: 600px; margin-top: 20px;">
			<h2>How It Works</h2>
			<ol style="margin-left: 20px;">
				<li>Retrieves all product categories from the "product_category" taxonomy</li>
				<li>Adds each category as a menu item to Footer Menu 2 (Footer Navigation 3)</li>
				<li>Updates existing menu items if categories are already in the menu</li>
				<li>Categories are linked to their archive pages</li>
			</ol>
		</div>
	</div>
<?php
}

/**
 * Auto-sync product categories to footer menu when a category is created/updated
 * This keeps the menu in sync automatically
 */
function auto_sync_categories_to_footer_menu($term_id, $tt_id, $taxonomy) {
	// Only sync if it's a product category
	if ($taxonomy !== 'product_category') {
		return;
	}
	
	// Check if footer menu exists
	$menu_locations = get_nav_menu_locations();
	if (empty($menu_locations['footer_3'])) {
		return; // No footer menu assigned, skip
	}
	
	$menu_id = $menu_locations['footer_3'];
	$category = get_term($term_id, $taxonomy);
	
	if (is_wp_error($category) || !$category) {
		return;
	}
	
	$category_url = get_term_link($category);
	if (is_wp_error($category_url)) {
		return;
	}
	
	// Check if category already exists in menu
	$menu_items = wp_get_nav_menu_items($menu_id);
	$existing_item_id = null;
	
	foreach ($menu_items as $item) {
		if ($item->object === 'product_category' && $item->object_id == $term_id) {
			$existing_item_id = $item->ID;
			break;
		}
	}
	
	if ($existing_item_id) {
		// Update existing
		wp_update_nav_menu_item($menu_id, $existing_item_id, [
			'menu-item-title' => $category->name,
			'menu-item-url' => $category_url,
		]);
	} else {
		// Add new
		wp_update_nav_menu_item($menu_id, 0, [
			'menu-item-object-id' => $term_id,
			'menu-item-object' => 'product_category',
			'menu-item-type' => 'taxonomy',
			'menu-item-title' => $category->name,
			'menu-item-url' => $category_url,
			'menu-item-status' => 'publish',
		]);
	}
}
// Hook into term creation and updates
add_action('created_product_category', 'auto_sync_categories_to_footer_menu', 10, 3);
add_action('edited_product_category', 'auto_sync_categories_to_footer_menu', 10, 3);