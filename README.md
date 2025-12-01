# Href Tailwind Starter

## Requirements
Please ensure you're using Node version 14.0.0^ to run this build.

## Setup
Download and extract the theme to your themes folder, then run `npm install` within that directory.

From here you can now run `npm run development` or `npm run watch` to start compiling SCSS & transpiling JS into ES5 compatible code. In development mode, Tailwind CSS will not be purged, so your stylesheet may look larger than usual.

Once you're ready to compile for production, run `npm run production` - This will purge the Tailwind styles and allow you to push production ready code. !IMPORTANT : This needs to happen when deploying to staging, otherwise your JS will be looking in node modules rather than importing.

## Config
In the project, you will find a `tailwind.config.js` file. This is where you setup all of the tailwind configuration, your typography, padding amounts, colours etc. There are some pre-defined styles in the config so you can see how you would manipulate them.

## Adding new blocks
In this new version, new blocks are now created in the includes/blocks folder within their own directory, this includes a scss file, a js file, the block render callback template.php and the block.php,

You also have a block.json file that will need to be configured to serve your block. Please copy one of the example/prebuilt blocks to configure this if the CLI is not yet finished. Once the CLI is finished, this readme will be updated with the correct commands to run to generate blocks over the terminal.

UPDATE:

The CLI is now partly complete, you can now generate blocks using the CLI, if you do not already have it installed, you can run the following command to install it globally:
`npm install href-cli -g`


Then if you run this command from your theme root:
`href-cli create-block`

You should be walked through a wizard to generate a new block.


## Custom post types & taxonomies
You can add new custom post types within `includes/lib/cpt.php` - I have included a link to my custom post types generator which also generates the required code for custom taxonomies too.

## General functionality
All filters/actions can be registered within their own file, `includes/lib/filters.php` and `includes/lib/actions.php` to keep this nice and organized. The only time I deviate from this is with WooCommerce, which I usually put in it's own file since they are specific to that. The functions that you hook in the filters & actions files can be registered within `includes/lib/methods.php`. We also have a few other files for specific uses to help keep things organised, so it's worth having a look through each file in the `includes/lib` folder to see what they do. 

Any kind of snippet or block of code that you want to be re-usable can easily be done by creating it as a partial within `includes/partials`, you can then include this directly onto multiple pages and they will read from the same partial. An example of this is in `single.php` as it includes the file `includes/partials/post-share.php`.

## Notes
There will be some predefined blocks and global elements such as headers etc scattered throughout this project already using Tailwind.css

Swiper.js is automatically included as part of node_modules https://swiperjs.com/

gLightBox is automatically included too as a lightbox plugin. - https://biati-digital.github.io/glightbox/, https://github.com/biati-digital/glightbox/blob/master/README.md


