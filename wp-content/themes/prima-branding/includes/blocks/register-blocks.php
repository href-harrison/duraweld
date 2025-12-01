<?php
    function registerBlockTypes() {
        $block_directory = get_template_directory() . '/includes/blocks';
        $block_url = get_template_directory_uri() . '/includes/blocks';
        $directory = new DirectoryIterator($block_directory);
        $blocks = [];

        foreach($directory as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                $block_name = $fileinfo->getFilename();
            
                $block_json_file = "$block_directory/$block_name/block.json";
                
                // Check if block is already registered to prevent duplicates
                if (WP_Block_Type_Registry::get_instance()->is_registered("acf/$block_name")) {
                    continue;
                }
                
                // Check if block.json exists
                if (!file_exists($block_json_file)) {
                    continue;
                }
                
                wp_register_script(
                    "$block_name-scripts",
                    "$block_url/$block_name/dist/block.js",
                    [],
                    file_exists("$block_directory/$block_name/dist/block.js") ? filemtime("$block_directory/$block_name/dist/block.js") : '1.0.0',
                    true
                );
                $new_block = register_block_type($block_json_file, [
                    'supports' => [
                        'anchor' => true,
                        'ghostkit' => [
                            'spacings' => true,
                        ]
                    ],
                    'script' => "$block_name-scripts"
                ]);
                // var_dump($new_block);
            }
        }
    }

    add_action('init', 'registerBlockTypes', 20);

    /**
     * Load global Tailwind classes and container styles etc
     */
    function mytheme_enqueue_block_editor_assets() {
        wp_enqueue_style(
            'mytheme-editor-styles',
            get_stylesheet_directory_uri() . '/dist/editor-styles.min.css',
            [],
            '1.0',
            'all'
        );
    }
    add_action( 'enqueue_block_editor_assets', 'mytheme_enqueue_block_editor_assets' );


    /**
     * Restrict block editor to only blocks we have created,
     * if you want to add additional core blocks into
     * this array, you can find a list of all of the 
     * core blocks on this URL:
     * https://developer.wordpress.org/block-editor/reference-guides/core-blocks/
     * 
     * If the blocks you want to show are a part of a plugin,
     * you will have to refer to that plugin's documentation
     */
    function edit_allowed_block_list($block_editor_context, $editor_context) {
        $block_directory = get_template_directory() . '/includes/blocks';
        $directory = new DirectoryIterator($block_directory);
        $blocks = [];

        foreach($directory as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                $block_name = $fileinfo->getFilename();
                $blocks[] = "acf/$block_name";
            }
        }
        return $blocks;
    }
    add_filter('allowed_block_types_all', 'edit_allowed_block_list', 10, 2);
?>
