<?php

namespace DiviChild\Modules\Login;

use DiviChild\Core\Abstracts\ModuleService;

class Service extends ModuleService
{
  /**
   * Initializes all module services
   * @return void
   * @since 1.0.0
   */
  public function init_service()
  {
    // Site Identity on Login Page
    if ($this->is_option_enabled('login_site_identity')) {
      add_action('login_head', [$this, 'render_login_logo_css']);
      add_filter('login_headerurl', [$this, 'login_header_url']);
      add_filter('login_headertext', [$this, 'login_header_text']);
    }

    // Background Image on Login Page
    if (!$this->is_option_empty('login_background_image')) {
      add_action('login_head', [$this, 'render_login_background_css']);
    }
  }

  /**
   * Renders CSS to replace the WP logo with the site icon
   * @return void
   */
  public function render_login_logo_css()
  {
    $icon_url = get_site_icon_url(512);
    if (empty($icon_url)) {
      return;
    }

    $width = (int) ($this->get_module_option('login_logo_width') ?: 120);
    ?>
    <style>
      .login h1 {
        text-align: center;
      }
      .login h1 a,
      .login h1.wp-login-logo a {
        background-image: url('<?php echo esc_url($icon_url); ?>') !important;
        background-size: <?php echo esc_attr($width); ?>px <?php echo esc_attr($width); ?>px;
        background-position: center top;
        background-repeat: no-repeat;
        width: 100%;
        height: auto;
        max-width: 100%;
        padding-top: <?php echo esc_attr($width + 10); ?>px;
        text-indent: 0 !important;
        overflow: visible !important;
        font-size: 20px;
        color: #3c434a;
        text-decoration: none;
        text-align: center;
      }
    </style>
    <?php
  }

  /**
   * Changes the login logo URL to the site URL
   * @return string
   */
  public function login_header_url()
  {
    return home_url('/');
  }

  /**
   * Changes the login logo alt text to the site name
   * @return string
   */
  public function login_header_text()
  {
    return get_bloginfo('name');
  }

  /**
   * Renders CSS for the login page background image
   * @return void
   */
  public function render_login_background_css()
  {
    $bg_id = (int) $this->get_module_option('login_background_image');
    if (empty($bg_id)) {
      return;
    }
    $bg_url = wp_get_attachment_url($bg_id);
    if (!$bg_url) {
      return;
    }
    ?>
    <style>
      body.login {
        background-image: url('<?php echo esc_url($bg_url); ?>') !important;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        position: relative;
      }
      body.login::before {
        content: '';
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 0;
      }
      body.login > * {
        position: relative;
        z-index: 1;
      }
      .login h1 a,
      .login h1.wp-login-logo a {
        color: #fff !important;
      }
      .login #nav a,
      .login #backtoblog a,
      .login .language-switcher {
        color: rgba(255, 255, 255, 0.7) !important;
      }
      .login #nav a:hover,
      .login #backtoblog a:hover {
        color: #fff !important;
      }
      .login .privacy-policy-page-link a {
        color: rgba(255, 255, 255, 0.7) !important;
      }
      .login .privacy-policy-page-link a:hover {
        color: #fff !important;
      }
    </style>
    <?php
  }
}
