<?php

namespace DiviChild\Modules\System;

use DiviChild\Core\Abstracts\ModuleService;

class Service extends ModuleService
{

  /**
   * Environment labels (fixed, not configurable)
   */
  private const ENV_LABELS = [
    'local' => 'Local',
    'development' => 'Dev',
    'staging' => 'Staging',
    'production' => 'Live',
  ];

  /**
   * Initializes all module services
   * @return void
   * @since 1.0.0
   */
  public function init_service()
  {
    // Environment Badge
    if ($this->is_option_enabled('environment_badge')) {
      add_action('admin_bar_menu', [$this, 'add_environment_badge'], 90);
      add_action('wp_enqueue_scripts', [$this, 'enqueue_badge_css']);
      add_action('admin_enqueue_scripts', [$this, 'enqueue_badge_css']);
    }

    // Status summary in At a Glance (admin only)
    if (is_admin() && $this->is_option_enabled('status_panel')) {
      add_action('rightnow_end', [$this, 'display_system_summary']);
    }
  }


  // =====================================================================
  // Environment Badge
  // =====================================================================

  /**
   * Gets the current environment type
   * @return string
   */
  private function get_environment()
  {
    return function_exists('wp_get_environment_type') ? wp_get_environment_type() : 'production';
  }

  /**
   * Enqueues the badge CSS file
   * @return void
   */
  public function enqueue_badge_css()
  {
    if (!is_admin_bar_showing()) {
      return;
    }
    wp_enqueue_style('divi-child-system-badge', $this->module->get_asset_url('css/system-badge.css'));
  }

  /**
   * Adds the environment badge to the admin bar, right after the site name
   * @param \WP_Admin_Bar $wp_admin_bar
   * @return void
   */
  public function add_environment_badge(\WP_Admin_Bar $wp_admin_bar)
  {

    // page-title gibt es nur im Frontend; wenn nicht vorhanden, abbrechen
    $site_name = $wp_admin_bar->get_node('site-name');
    if (!$site_name) {
      return;
    }

    $env = $this->get_environment();
    $label = self::ENV_LABELS[$env] ?? \ucfirst($env);

    // SE-Warnicon, falls Suche blockiert
    $se_icon = '';
    if ($this->is_option_enabled('search_visibility_warning') && get_option('blog_public') === '0') {
      $se_icon = ' <span class="dvc-se-warning" title="'
        . esc_attr__('Search engines are discouraged from indexing this site', 'divi-child')
        . '">&#9888;</span>';
    }

    // neuen Titel aufbauen: alter Titel + Badge
    $new_title = $site_name->title
      . ' <span class="dvc-environment-badge dvc-env-' . esc_attr($env) . '">'
      . $se_icon
      . esc_html($label)
      . '</span>';

    // Node mit erweitertem Titel zurückschreiben
    $wp_admin_bar->add_node([
      'id' => $site_name->id,
      'title' => $new_title,
      'parent' => $site_name->parent,
      'href' => $site_name->href,
      'meta' => (array) $site_name->meta,
    ]);
  }





  // =====================================================================
  // System Summary (At a Glance)
  // =====================================================================

  /**
   * Displays a compact system summary line at the bottom of the At a Glance widget
   * @return void
   */
  public function display_system_summary()
  {
    $php_version = phpversion();
    $wp_version = get_bloginfo('version');
    $divi_version = function_exists('et_get_theme_version') ? et_get_theme_version() : null;

    $formats = [
      'WebP' => function_exists('imagewebp'),
      'AVIF' => function_exists('imageavif'),
      'SVG' => \in_array('image/svg+xml', get_allowed_mime_types()),
    ];

    $green = '#3c763d';
    $red = '#a94442';

    $parts = [
      'PHP ' . esc_html($php_version),
      'WP ' . esc_html($wp_version),
    ];
    if ($divi_version) {
      $parts[] = 'Divi ' . esc_html($divi_version);
    }

    $format_parts = [];
    foreach ($formats as $name => $supported) {
      $color = $supported ? $green : $red;
      $format_parts[] = '<span style="color:' . $color . ';">' . esc_html($name) . '</span>';
    }

    echo '<p class="dvc-system-summary" style="padding:0 12px 4px;margin:0;">'
      . implode(' &#9642; ', $parts)
      . ' &#9642; '
      . implode(' ', $format_parts)
      . '</p>';
  }
}
