<?php
  global $post;

  $js_click_function = 'javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\'); return false;';
  $share_image = (has_post_thumbnail($post->ID)) ? get_the_post_thumbnail_url($post->ID, 'full') : '';
  $twitter_href = 'https://twitter.com/share?text=' . htmlspecialchars('Check this out: ' . $post->post_title) . '&url=' . get_permalink($post->ID);
  $facebook_href = 'http://www.facebook.com/sharer.php?u=' . get_permalink($post->ID) . '&t=' . htmlspecialchars('Check this out: ' . $post->post_title);
  $pinterest_href = 'http://pinterest.com/pin/create/button/?url=' . get_permalink($post->ID) . '&media=' . $share_image . '&description=' . htmlspecialchars($post->post_title);
  $linkedin_href = 'http://www.linkedin.com/shareArticle?mini=true&url=' . get_permalink($post->ID) . '&title=' . htmlspecialchars($post->post_title) . '&summary=' . htmlspecialchars($post->post_excerpt) . '&source=' . htmlspecialchars(get_bloginfo('name'));
  $email_href = 'mailto:?subject=' . htmlspecialchars('Check this out: ' . $post->post_title) . '&body=' . get_permalink($post->ID);
?>

<div class="post-share clearfix">
  <span class="post-share__share-text">Share this post</span>

  <a class="post-share__anchor post-share__anchor--twitter" href="<?php echo $twitter_href; ?>" onclick="<?php echo $js_click_function; ?>">
    Twitter
  </a>

  <a class="post-share__anchor post-share__anchor--facebook" href="<?php echo $facebook_href; ?>" onclick="<?php echo $js_click_function; ?>">
    Facebook
  </a>

  <a class="post-share__anchor post-share__anchor--pinterest" href="<?php echo $pinterest_href; ?>" onclick="<?php echo $js_click_function; ?>">
    Pinterest
  </a>

  <a class="post-share__anchor post-share__anchor--linkedin" href="<?php echo $linkedin_href; ?>" onclick="<?php echo $js_click_function; ?>">
    LinkedIn
  </a>

  <a class="post-share__anchor post-share__anchor--email" href="<?php echo $email_href; ?>" onclick="<?php echo $js_click_function; ?>">
    Email
  </a>
</div>
