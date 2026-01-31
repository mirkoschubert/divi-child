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

    // Status Panel (admin only)
    if (is_admin() && $this->is_option_enabled('status_panel')) {
      add_action('wp_dashboard_setup', [$this, 'add_status_widget']);
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
    $label = self::ENV_LABELS[$env] ?? ucfirst($env);

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
  // Status Panel
  // =====================================================================

  /**
   * Adds the status dashboard widget
   * @return void
   */
  public function add_status_widget()
  {
    wp_add_dashboard_widget(
      'dvc_status_panel',
      __('Divi Child — System Status', 'divi-child'),
      [$this, 'render_status_widget']
    );
  }

  /**
   * Renders the status dashboard widget
   * @return void
   */
  public function render_status_widget()
  {
    $php_version = phpversion();
    $wp_version = get_bloginfo('version');
    $divi_version = function_exists('et_get_theme_version') ? et_get_theme_version() : __('Not installed', 'divi-child');

    $svg_supported = \in_array('image/svg+xml', get_allowed_mime_types());
    $webp_supported = function_exists('imagewebp');
    $avif_supported = function_exists('imageavif');

    $check = '<span style="color: #46b450;">&#10003;</span>';
    $cross = '<span style="color: #dc3232;">&#10007;</span>';
    ?>
    <table class="widefat striped" style="border: none;">
      <thead>
        <tr>
          <th colspan="2"><strong><?php esc_html_e('Environment', 'divi-child'); ?></strong></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php esc_html_e('PHP Version', 'divi-child'); ?></td>
          <td><?php echo esc_html($php_version); ?>
            <?php echo version_compare($php_version, '7.4', '>=') ? $check : $cross; ?>
          </td>
        </tr>
        <tr>
          <td><?php esc_html_e('WordPress Version', 'divi-child'); ?></td>
          <td><?php echo esc_html($wp_version); ?>
            <?php echo version_compare($wp_version, '5.0', '>=') ? $check : $cross; ?>
          </td>
        </tr>
        <tr>
          <td><?php esc_html_e('Divi Version', 'divi-child'); ?></td>
          <td><?php echo esc_html($divi_version); ?></td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th colspan="2"><strong><?php esc_html_e('Image Format Support', 'divi-child'); ?></strong></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>SVG</td>
          <td>
            <?php echo $svg_supported ? $check . ' ' . esc_html__('Enabled', 'divi-child') : $cross . ' ' . esc_html__('Disabled', 'divi-child'); ?>
          </td>
        </tr>
        <tr>
          <td>WebP</td>
          <td>
            <?php echo $webp_supported ? $check . ' ' . esc_html__('Supported', 'divi-child') : $cross . ' ' . esc_html__('Not supported', 'divi-child'); ?>
          </td>
        </tr>
        <tr>
          <td>AVIF</td>
          <td>
            <?php echo $avif_supported ? $check . ' ' . esc_html__('Supported', 'divi-child') : $cross . ' ' . esc_html__('Not supported', 'divi-child'); ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php
  }
}
