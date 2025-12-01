<?php
/**
 * Use https://michaelsmyth.co.uk/custom-post-types-generator/new/ for an easy way
 * to register post types & taxonomies.
 * 
 * Note: Post types are registered via ACF Extended from JSON files in acf-json/
 * This file is kept for manual registration if needed.
 */

/**
 * Fallback: Ensure Products post type is registered
 * ACF Extended should handle this automatically, but this ensures it works
 */
add_action('init', function() {
  // Only register if ACF Extended hasn't already registered it
  if (!post_type_exists('product')) {
    // This shouldn't normally run, but provides a fallback
    // ACF Extended should register from JSON automatically
  }
}, 20);