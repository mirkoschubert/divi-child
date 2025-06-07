<?php

namespace DiviChild\Admin;

use DiviChild\Core\Config;

final class Admin
{
  protected $config;

  public function __construct()
  {
    $this->config = new Config();
    $this->init();
  }


  /**
   * Initialize the admin functionalities
   * @return void
   * @since 3.0.0
   */
  public function init()
  {
    add_action('admin_menu', [$this, 'add_admin_menu'], 12);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
  }


  /**
   * Add the admin menu page
   * @return void
   * @since 3.0.0
   */
  public function add_admin_menu()
  {
    add_submenu_page(
      'et_divi_options',
      __('Child Theme Options', 'divi-child'),
      __('Child Theme Options', 'divi-child'),
      'manage_options',
      'divi-child-options',
      [$this, 'render_page'],
      1
    );
  }


  /**
   * Enqueue admin scripts and styles
   * @return void
   * @since 3.0.0
   */
  public function enqueue_scripts($hook)
  {
    // Nur auf unserer Admin-Seite laden
    if ($hook !== 'divi_page_divi-child-options') {
      return;
    }

    // WordPress React Dependencies
    wp_enqueue_script('wp-element');
    wp_enqueue_script('wp-components');
    wp_enqueue_script('wp-api-fetch');
    wp_enqueue_script('wp-i18n');
    wp_enqueue_style('wp-components');

    // Unsere React-App
    $js_file_path = $this->config->theme_dir . '/admin-app/build/admin-app.js';
    $js_file_url = $this->config->theme_url . '/admin-app/build/admin-app.js';

    if (!file_exists($js_file_path)) {
      error_log("React Admin JS file not found: {$js_file_path}");
      return;
    }

    wp_enqueue_script(
      'divi-child-admin-app',
      $js_file_url,
      ['wp-element', 'wp-components', 'wp-api-fetch', 'wp-i18n'],
      filemtime($js_file_path),
      true
    );

    wp_localize_script('divi-child-admin-app', 'diviChildConfig', [
      'apiUrl' => rest_url('divi-child/v1/'),
      'nonce' => wp_create_nonce('wp_rest'),
      'version' => $this->config->theme_version ?? '1.0.0'
    ]);
  }


  /**
   * Renders the admin page
   * @return void
   * @since 3.0.0
   */
  public function render_page()
  {
    if (!current_user_can('manage_options')) {
      return;
    }

    echo '<div id="divi-child-react-admin"></div>';
  }

}