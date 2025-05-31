<?php

namespace DiviChild\Admin;

use DiviChild\Core\Config;

final class ReactAdmin
{
  protected $config;

  public function __construct()
  {
    $this->config = new Config();
    $this->init();
  }

  public function init()
  {
    add_action('admin_menu', [$this, 'add_admin_menu'], 13);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
  }

  /**
   * Fügt React Admin-Seite hinzu
   */
  public function add_admin_menu()
  {
    add_submenu_page(
      'et_divi_options',
      __('Child Theme (React)', 'divi-child'),
      __('Child Theme (React)', 'divi-child'),
      'manage_options',
      'divi-child-react',
      [$this, 'render_page']
    );
  }

  /**
   * Lädt React-Skripte
   */
  public function enqueue_scripts($hook)
  {
    // Nur auf unserer Admin-Seite laden
    if ($hook !== 'divi_page_divi-child-react') {
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
      error_log("React Admin JS file not found: " . $js_file_path);
      return;
    }

    wp_enqueue_script(
      'divi-child-admin-app', // 🔧 WICHTIG: Richtiger Handle-Name!
      $js_file_url,
      ['wp-element', 'wp-components', 'wp-api-fetch', 'wp-i18n'],
      filemtime($js_file_path),
      true
    );

    // 🔧 KORREKTUR: Beide Konfigurationen mit richtigem Handle
    wp_localize_script('divi-child-admin-app', 'diviChildConfig', [
      'apiUrl' => rest_url('divi-child/v1/'),
      'nonce' => wp_create_nonce('wp_rest'),
      'version' => $this->config->theme_version ?? '1.0.0'
    ]);
  }


  /**
   * Rendert die Admin-Seite
   */
  public function render_page()
  {
    if (!current_user_can('manage_options')) {
      return;
    }

    // Nur den Container, keine Überschrift
    echo '<div id="divi-child-react-admin"></div>';
  }
}