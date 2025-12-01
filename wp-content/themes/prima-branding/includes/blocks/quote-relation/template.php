<?php
/**
 * Block Name: My First Block
 *
 * Description: Displays my very first block.
 */

/**
 * Block object provided by Wordpress
 */
$block = $args['block'] ?? false;

/**
 * Data passed to the block template as an arg and extracted
 * into variables
 */
$data = $args['data'];

$section_header = $data['section_header'];
$header = $data['header'];
$copy = $data['copy'];
$link = $data['link'];
$image = $data['image'];
$quotes_relation = $data['quotes_relation'];

/**
 * Unique block identifier added to the block
 */
$block_id = $args['block_id'] ?? false;

/**
 * The block class names we passed to the
 * argument for the block
 */
$class_name = $args['class_name'];


if ($block && $block_id && isset($block['ghostkit']['styles']) && $spacings = $block['ghostkit']['styles']) {
    addGhostKitSpacings($spacings, $block_id);
}

?>

<!-- Our front-end template -->
<section
    id="<?php echo $block_id; ?>" 
    class="<?php echo $class_name; ?>"
>
    <div class="site-container">
        <div class="text">
           <div class="text--intro">
                <?php if($section_header) : ?>
                    <h5><?php echo $section_header; ?></h5>
                <?php endif; ?>
                <?php if($header) : ?>
                    <h2><?php echo $header; ?></h2>
                <?php endif; ?>
                <?php if($copy) : ?>
                    <?php echo $copy; ?>
                <?php endif; ?>
                <?php if($link) : ?>
                    <a href="<?php echo $link['url']; ?>" class="btn-pb btn-pb--arrow btn-pb--arrow--white" data-aos="fade-in"><?php echo $link['title']; ?></a>
                <?php endif; ?>
           </div>

            <?php if($quotes_relation) : ?>
                <div class="quote">
                    <?php foreach ($quotes_relation as $quote) : ?>
                        <div class="quote--mark">
                        <svg width="35" height="28" viewBox="0 0 35 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.0146 21.5234C16.0146 23.1484 15.3545 24.5703 14.0342 25.7891C12.7393 27.0078 11.2031 27.6172 9.42578 27.6172C6.63281 27.6172 4.4873 26.6904 2.98926 24.8369C1.49121 22.9834 0.742188 20.4189 0.742188 17.1436C0.742188 14.0967 2.02441 11.0244 4.58887 7.92676C7.17871 4.8291 10.2891 2.48047 13.9199 0.880859L15.5957 3.58496C12.7266 4.98145 10.4795 6.60645 8.85449 8.45996C7.22949 10.3135 6.29004 12.5605 6.03613 15.2012H8.16895C9.74316 15.2012 11.0254 15.3789 12.0156 15.7344C13.0059 16.0898 13.8057 16.585 14.415 17.2197C14.999 17.8291 15.4053 18.502 15.6338 19.2383C15.8877 19.9746 16.0146 20.7363 16.0146 21.5234ZM34.791 21.5234C34.791 23.1484 34.1309 24.5703 32.8105 25.7891C31.5156 27.0078 29.9795 27.6172 28.2021 27.6172C25.4092 27.6172 23.2637 26.6904 21.7656 24.8369C20.2676 22.9834 19.5186 20.4189 19.5186 17.1436C19.5186 14.0967 20.8008 11.0244 23.3652 7.92676C25.9551 4.8291 29.0654 2.48047 32.6963 0.880859L34.3721 3.58496C31.5029 4.98145 29.2559 6.60645 27.6309 8.45996C26.0059 10.3135 25.0664 12.5605 24.8125 15.2012H26.9453C28.5195 15.2012 29.8018 15.3789 30.792 15.7344C31.7822 16.0898 32.582 16.585 33.1914 17.2197C33.7754 17.8291 34.1816 18.502 34.4102 19.2383C34.6641 19.9746 34.791 20.7363 34.791 21.5234Z" fill="#FBB034"/>
                        </svg>

                        </div>
                        <div class="quote--content">
                            <p><?php echo get_the_excerpt($quote->ID); ?></p>
                            <div class="author">
                            <span><?php echo get_field('name', $quote->ID); ?></span> - <span><?php echo get_field('position', $quote->ID); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="images">
            <?php if($image) : ?>
                <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
            <?php endif; ?>
        </div>
    </div>
</section>