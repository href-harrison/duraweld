      </main>
      <div class="popup-overlay"></div>
      <?php
      $footer_contact = get_field('footer_contact', 'option');
      $footer_phone = get_field('footer_phone', 'option');
      $footer_email = get_field('footer_email', 'option');
      $social_media_repeater = get_field('social_media_repeater', 'option');
      $menu_1_header = get_field('menu_1_header', 'option');
      $menu_2_header = get_field('menu_2_header', 'option');
      $menu_3_header = get_field('menu_3_header', 'option');
      $copyright = get_field('copyright', 'option');
      $terms_link = get_field('terms_link', 'option');
      $privacy_link = get_field('privacy_link', 'option');
      $agency_link = get_field('agency_link', 'option');
      $company_info = get_field('company_info', 'option');
      $duraweld_logo = get_field('duraweld_logo', 'option');
      $duraweld_info = get_field('duraweld_info', 'option');
      
      /**
       * Render social media icons
       *
       * @param array $social_media_repeater Social media repeater field data
       * @param string $class_name Additional CSS class name
       * @return void
       */
      function render_social_media_icons($social_media_repeater, $class_name = '') {
          if (!$social_media_repeater || !is_array($social_media_repeater)) {
              return;
          }
          
          $wrapper_class = 'footer-sm';
          if ($class_name) {
              $wrapper_class .= ' ' . esc_attr($class_name);
          }
          
          echo '<div class="' . esc_attr($wrapper_class) . '">';
          foreach ($social_media_repeater as $sm) {
              if (!isset($sm['link']) || !isset($sm['icon'])) {
                  continue;
              }
              
              $link_url = isset($sm['link']['url']) ? esc_url($sm['link']['url']) : '';
              $link_target = isset($sm['link']['target']) ? esc_attr($sm['link']['target']) : '_self';
              $link_title = isset($sm['link']['title']) ? esc_attr($sm['link']['title']) : '';
              $icon_url = isset($sm['icon']['url']) ? esc_url($sm['icon']['url']) : '';
              $icon_alt = isset($sm['icon']['alt']) ? esc_attr($sm['icon']['alt']) : $link_title;
              
              if ($link_url && $icon_url) {
                  echo '<a href="' . esc_url($link_url) . '" target="' . esc_attr($link_target) . '">';
                  echo '<img src="' . esc_url($icon_url) . '" alt="' . esc_attr($icon_alt) . '">';
                  echo '</a>';
              }
          }
          echo '</div>';
      }
      ?>

      <footer class="site-footer">
        <div class="site-container">
          <div class="site-footer__top">
            <div class="site-footer__top--contact footer-column">
              <?php if ($footer_contact) : ?>
                <div class="contact">
                  <?php echo wp_kses_post($footer_contact); ?>
                </div>
              <?php endif; ?>
              
              <?php if ($footer_phone && $footer_email) : ?>
                <div class="contact-ctas">
                  <div class="footer-cta">
                    <span>Telephone:</span>
                    <a href="<?php echo esc_url($footer_phone['url']); ?>">
                      <?php echo esc_html($footer_phone['title']); ?>
                    </a>
                  </div>
                  <div class="footer-cta">
                    <span>email:</span>
                    <a href="<?php echo esc_url($footer_email['url']); ?>">
                      <?php echo esc_html($footer_email['title']); ?>
                    </a>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php render_social_media_icons($social_media_repeater, 'footer-sm--mobile'); ?>
            </div>
            
            <div class="footer-column">
              <?php if ($menu_1_header) : ?>
                <h5><?php echo esc_html($menu_1_header); ?></h5>
              <?php endif; ?>
              <nav class="site-footer__navigation">
                <?php
                wp_nav_menu([
                  'theme_location' => 'footer_2',
                  'menu_class' => 'site-footer__menu',
                  'container' => false,
                  'echo' => true,
                  'fallback_cb' => false,
                  'depth' => 1
                ]);
                ?>
              </nav>
            </div>

            <div class="footer-column">
              <?php if ($menu_2_header) : ?>
                <h5><?php echo esc_html($menu_2_header); ?></h5>
              <?php endif; ?>
              <nav class="site-footer__navigation">
                <?php
                wp_nav_menu([
                  'theme_location' => 'footer_3',
                  'menu_class' => 'site-footer__menu',
                  'container' => false,
                  'echo' => true,
                  'fallback_cb' => false,
                  'depth' => 1
                ]);
                ?>
              </nav>
            </div>

            <div class="footer-column">
              <?php if ($menu_3_header) : ?>
                <h5><?php echo esc_html($menu_3_header); ?></h5>
              <?php endif; ?>
              <nav class="site-footer__navigation">
                <?php
                wp_nav_menu([
                  'theme_location' => 'footer',
                  'menu_class' => 'site-footer__menu',
                  'container' => false,
                  'echo' => true,
                  'fallback_cb' => false,
                  'depth' => 1
                ]);
                ?>
              </nav>
              <?php render_social_media_icons($social_media_repeater, 'footer-sm--desktop'); ?>
            </div>
          </div>

          <div class="site-footer__bottom">
            <div class="info-container">
              <?php if ($copyright) : ?>
                <p class="copyrights">
                  <?php echo wp_kses_post($copyright); ?>
                  <span class="copyright-year"><?php echo esc_html(date('Y')); ?></span>
                </p>
              <?php endif; ?>

              <?php if ($terms_link && $privacy_link) : ?>
                <div class="privacy-links">
                  <span class="separator">|</span>
                  <a href="<?php echo esc_url($terms_link['url']); ?>" target="<?php echo esc_attr($terms_link['target']); ?>">
                    <?php echo esc_html($terms_link['title']); ?>
                  </a>
                  <span> |</span>
                  <a href="<?php echo esc_url($privacy_link['url']); ?>" target="<?php echo esc_attr($privacy_link['target']); ?>">
                    <?php echo esc_html($privacy_link['title']); ?>
                  </a>
                  <span class="separator-info">|</span>
                </div>
              <?php endif; ?>
              
              <?php if ($agency_link) : ?>
                <span><?php echo wp_kses_post($agency_link); ?></span>
              <?php endif; ?>
            </div>
            
            <?php if ($company_info) : ?>
              <div class="company-info">
                <?php echo wp_kses_post($company_info); ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </footer>
    </div>

    <?php wp_footer(); ?>
  </body>
</html>
