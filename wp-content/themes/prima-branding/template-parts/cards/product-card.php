<?php 

$post_id = $args['post_id'] ?? "";
$index = $args['index'] ?? 1;

$thumbnail = get_the_post_thumbnail_url($post_id, 'large'); 
$thumbnail_mobile = get_the_post_thumbnail_url($post_id, 'medium_large'); 
$title = get_the_title($post_id);
$excerpt = get_the_excerpt($post_id);
$permalink = get_the_permalink($post_id);
$theme = get_field('dark_background', $post_id) ? 'dark ': 'light';


                        
                    ?>
                    <div class="single-product-tile <?php echo $theme; ?>" data-aos="fade-in" data-aos-delay="<?php echo $index * 100; ?>">
                        <a href="<?php echo $permalink; ?>" class="">
                            <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>" class="desktop-image">
                            <img src="<?php echo $thumbnail_mobile; ?>" alt="<?php echo $title; ?>" class="mobile-image">
                        
                            <div class="overlay">
                                <div class="text">
                                    <h2><?php echo $title; ?></h2>
                                    <?php if($excerpt) : ?>
                                        <p><?php echo $excerpt; ?></p>
                                    <?php endif; ?>

                                    <span class="btn-pb--arrow btn-pb--arrow--white">Find out more</span>
                                </div>
                            </div>
                        </a>
                        <!-- <div  class="mobile-tile">
                            <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>" class="desktop-image">
                            <img src="<?php echo $thumbnail_mobile; ?>" alt="<?php echo $title; ?>" class="mobile-image">
                        
                            <div class="overlay">
                                <div class="text">
                                    <h2><?php echo $title; ?></h2>
                                    <?php if($excerpt) : ?>
                                        <p><?php echo $excerpt; ?></p>
                                    <?php endif; ?>

                                    <a href="<?php echo $permalink; ?>" class="btn-pb--arrow btn-pb--arrow--white">Find out more</a>
                                </div>
                            </div>
                                    </div> -->
                        <div class="toggle-overlay">
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                            <circle cx="25" cy="24.7487" r="23.2487" transform="rotate(-90 25 24.7487)" stroke="#505156" stroke-width="3"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.6862 24.7487C13.6862 26.1596 14.83 27.3034 16.241 27.3034L22.4452 27.3034L22.4452 33.5077C22.4452 34.9186 23.589 36.0624 24.9999 36.0624C26.4109 36.0624 27.5547 34.9186 27.5547 33.5077L27.5547 27.3034L33.759 27.3034C35.1699 27.3034 36.3137 26.1596 36.3137 24.7487C36.3137 23.3377 35.1699 22.194 33.759 22.194L27.5547 22.194L27.5547 15.9897C27.5547 14.5787 26.4109 13.4349 24.9999 13.4349C23.589 13.4349 22.4452 14.5787 22.4452 15.9897L22.4452 22.194L16.241 22.194C14.83 22.194 13.6862 23.3377 13.6862 24.7487Z" fill="#505156"/>
                            </svg>
                            
                            <h5><?php echo $title; ?></h5>
                        </div>
                    </div>