# How to Flush Rewrite Rules in WordPress

## Method 1: Via Settings (Easiest)
1. Go to **Settings → Permalinks** in WordPress admin
2. Don't change anything, just click **"Save Changes"** at the bottom
3. This will flush the rewrite rules automatically

## Method 2: Via URL Parameter
1. Go to any admin page
2. Add `?flush_rewrite_rules=1` to the URL
3. Example: `https://yoursite.com/wp-admin/?flush_rewrite_rules=1`
4. Press Enter

## Method 3: Via Functions.php (Temporary)
If the above don't work, you can temporarily add this to your `functions.php`:

```php
add_action('init', function() {
    if (isset($_GET['flush_rewrite_rules'])) {
        flush_rewrite_rules();
    }
});
```

Then visit: `https://yoursite.com/wp-admin/?flush_rewrite_rules=1`

**Important:** Remove this code after flushing, as it can impact performance if left in.

## Verify Template is Working
After flushing:
1. Visit the taxonomy archive URL: `/product-size/a5-ring-binders/`
2. You should see the products grid, not a single product page
3. If you still see issues, clear any caching plugins

