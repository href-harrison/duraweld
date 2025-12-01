<?php
// Add the shortcode "[btn]" which will trigger "btn_sc".
add_shortcode('btn', 'btn_sc');

/** 
 * This will add a button with some defaults.
 */
function btn_sc($atts, $content = null) {
  $a = shortcode_atts(array(
    'url' => '#',
    'target' => '_self',
    'class' => 'btn--shortcode'
  ), $atts);

  return '<a class="btn ' . $a['class'] . '" target="' . $a['target'] . '" href="' . $a['url'] . '">' . $content . '</a>';
}