let mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
// const HtmlInjector = require('bs-html-injector');\
const fs = require('fs');
const path = require('path');
mix.webpackConfig({
    plugins: [
        
    ],
    module: {
        rules: [
            {
                test: /\.scss/,
                loader: 'import-glob-loader'
            }
        ]
    }
})
.js('./js/main.js', './dist/main.min.js')
    .sass('./scss/main.scss', './dist/main.min.css', {
        sassOptions: {
            outputStyle: 'compressed',
        }
    })
    .sass('./scss/_editor-styles.scss', './dist/editor-styles.min.css', {
        sassOptions: {
            outputStyle: 'compressed',
        }
    })


// Function to add all blocks
function mixBlocks(directory) {
    fs.readdirSync(directory).forEach(file => {
        const absolute = path.join(directory, file);
    
        if (fs.statSync(absolute).isDirectory()) {
            if (absolute.indexOf('dist') > -1) {
                return;
            } else {
                mixBlocks(absolute);
            }
        } else if (file.endsWith('.js')) {
            mix.js(absolute, directory + "/dist/block.js");
        } else if (file.endsWith('.scss')) {
            mix.sass(absolute, directory + "/dist/block.min.css", {
                sassOptions: {
                    outputStyle: 'compressed',
                }
            });
        }
    });
}

// Call the function on the blocks directory
mixBlocks('./includes/blocks/');

// Add postCss options
mix.options({
    processCssUrls: false,
    postCss: [
        tailwindcss('./tailwind.config.js')
    ],
});
