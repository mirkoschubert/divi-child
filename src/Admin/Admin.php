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
    add_action('admin_head', [$this, 'get_theme_colors']);
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
      'et_onboarding',
      __('Child Theme Options', 'divi-child'),
      __('Child Theme Options', 'divi-child'),
      'manage_options',
      'divi-child-options',
      [$this, 'render_page'],
      2
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

    // WordPress Media Library (for ImageField)
    wp_enqueue_media();

    // Unsere React-App
    $js_file_path = $this->config->theme_dir . '/admin-app/build/admin-app.js';
    $js_file_url = $this->config->theme_url . '/admin-app/build/admin-app.js';

    if (!file_exists($js_file_path)) {
      error_log("❌ React Admin JS file not found: {$js_file_path}");
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


  /**
   * Adds admin theme colors of a user to CSS variables
   * @return void
   * @since 3.0.0
   */
  public function get_theme_colors()
  {
    $user_id = get_current_user_id();
    $color_scheme = get_user_option('admin_color', $user_id);
    global $_wp_admin_css_colors;
    if (isset($_wp_admin_css_colors[$color_scheme])) {
      $colors = $_wp_admin_css_colors[$color_scheme]->colors;
      // Wenn Modern und nur 3 Farben, zweite Farbe aus der ersten ableiten
      if ($color_scheme === 'modern' && count($colors) === 3) {
        $lighter = $this->lighten_color($colors[0], 0.1);
        array_splice($colors, 1, 0, $lighter);
      }
      echo '<style>:root {';
      foreach ($colors as $i => $color) {
        echo "--wp-admin-theme-color-$i: $color;";
      }
      echo '}</style>';
    }
  }


  /**
   * Lightens a hex color by a given percentage
   * @param string $hex The hex color code
   * @param float $percent The percentage to lighten (0.0 to 1.0)
   * @return string The lightened hex color code
   * @since 3.0.0
   */
  public function lighten_color($hex, $percent)
  {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
      $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $r = min(255, intval($r + (255 - $r) * $percent));
    $g = min(255, intval($g + (255 - $g) * $percent));
    $b = min(255, intval($b + (255 - $b) * $percent));
    return sprintf("#%02x%02x%02x", $r, $g, $b);
  }

}