<?php

namespace DiviChild\Modules\LocalFonts;

use DiviChild\Core\Abstracts\ModuleService;

class Service extends ModuleService
{
  /**
   * Initializes all module services
   * @return void
   * @since 3.0.0
   */
  public function init_service()
  {
    // === Common (Admin + Frontend) ===

    // Disable Google Fonts
    if ($this->is_option_enabled('disable_google_fonts')) {
      add_filter('et_builder_google_fonts_is_enabled', '__return_false');
      add_filter('et_builder_google_fonts', '__return_empty_array', 999);

      add_action('wp_enqueue_scripts', [$this, 'dequeue_google_fonts'], 100);
      add_action('wp_footer', [$this, 'dequeue_google_fonts'], 1);
    }

    // Register local fonts as websafe fonts
    $installed_fonts = get_option('divi_child_installed_fonts', []);
    if (!empty($installed_fonts)) {
      add_filter('et_websafe_fonts', [$this, 'register_local_fonts_websafe'], 999, 1);
    }

    // Auto-update system
    add_action('divi_child_check_font_updates', [$this, 'check_and_update_fonts']);

    // Register weekly cron interval
    add_filter('cron_schedules', [$this, 'add_weekly_cron_schedule']);

    // Schedule cron job (once)
    if (!wp_next_scheduled('divi_child_check_font_updates')) {
      wp_schedule_event(\strtotime('03:00:00'), 'weekly', 'divi_child_check_font_updates');
    }

    // Cleanup cron
    add_action('divi_child_daily_cron', [$this, 'cleanup_orphaned_files']);

    // === Frontend + Visual Builder ===
    if (!empty($installed_fonts)) {
      add_action('wp_enqueue_scripts', [$this, 'enqueue_assets'], 5);
    }
  }


  /**
   * Adds a weekly interval to WordPress cron schedules
   * @param array $schedules
   * @return array
   * @since 3.0.0
   */
  public function add_weekly_cron_schedule($schedules)
  {
    if (!isset($schedules['weekly'])) {
      $schedules['weekly'] = [
        'interval' => 604800,
        'display'  => __('Once Weekly', 'divi-child'),
      ];
    }
    return $schedules;
  }

  /**
   * Dequeues all Divi Google Font stylesheets
   * @return void
   * @since 3.0.0
   */
  public function dequeue_google_fonts()
  {
    wp_dequeue_style('et-divi-open-sans');
    wp_dequeue_style('et-builder-googlefonts');
    wp_dequeue_style('et-builder-googlefonts-cached');
  }


  /**
   * Enqueues local font CSS files on the frontend
   * @return void
   * @since 3.0.0
   */
  public function enqueue_assets()
  {
    $upload_dir = wp_upload_dir();
    $fonts_dir = $upload_dir['basedir'] . '/local-fonts';
    $fonts_url = $upload_dir['baseurl'] . '/local-fonts';
    $installed_fonts = get_option('divi_child_installed_fonts', []);

    foreach ($installed_fonts as $font_family => $font_data) {
      $css_filename = sanitize_title($font_family) . '.css';
      $css_path = $fonts_dir . '/' . $css_filename;
      $css_url = $fonts_url . '/' . $css_filename;

      if (\file_exists($css_path)) {
        wp_enqueue_style(
          'divi-child-font-' . sanitize_title($font_family),
          $css_url,
          [],
          \filemtime($css_path)
        );
      }
    }
  }


  // =====================================================================
  // Font Registration
  // =====================================================================

  /**
   * Registers local fonts as websafe fonts (Frontend + Builder)
   * @param array $fonts
   * @return array
   * @since 3.0.0
   */
  public function register_local_fonts_websafe($fonts)
  {
    $installed_fonts = get_option('divi_child_installed_fonts', []);

    if (empty($installed_fonts)) {
      return $fonts;
    }

    foreach ($installed_fonts as $font_family => $font_data) {
      $metadata = $font_data['metadata'] ?? [];
      $variants = $metadata['variants'] ?? ['regular'];
      $category = $metadata['category'] ?? 'sans-serif';

      $formatted_styles = $this->format_font_styles_for_divi($variants);

      $fonts[$font_family] = [
        'styles' => $formatted_styles,
        'character_set' => 'latin-ext',
        'type' => $this->map_category_to_divi_type($category),
        'standard' => 1
      ];
    }

    return $fonts;
  }


  /**
   * Formats font variants for Divi's expected format
   * @param array $variants
   * @return string
   * @since 3.0.0
   */
  private function format_font_styles_for_divi($variants)
  {
    $divi_styles = [];

    foreach ($variants as $variant) {
      switch ($variant) {
        case 'regular':
          $divi_styles[] = '400';
          break;
        case 'italic':
          $divi_styles[] = '400italic';
          break;
        case '100':
        case '200':
        case '300':
        case '500':
        case '600':
        case '700':
        case '800':
        case '900':
          $divi_styles[] = $variant;
          break;
        case '100italic':
        case '200italic':
        case '300italic':
        case '500italic':
        case '600italic':
        case '700italic':
        case '800italic':
        case '900italic':
          $divi_styles[] = $variant;
          break;
      }
    }

    if (empty($divi_styles)) {
      $divi_styles = ['400', '700'];
    }

    return \implode(',', \array_unique($divi_styles));
  }


  /**
   * Maps Google Fonts categories to Divi font types
   * @param string $category
   * @return string
   * @since 3.0.0
   */
  private function map_category_to_divi_type($category)
  {
    $mapping = [
      'sans-serif' => 'sans-serif',
      'serif' => 'serif',
      'display' => 'sans-serif',
      'handwriting' => 'cursive',
      'monospace' => 'monospace'
    ];

    return $mapping[$category] ?? 'sans-serif';
  }


  // =====================================================================
  // Auto-Update System
  // =====================================================================

  /**
   * Checks for updates of installed fonts and updates them if necessary
   * @return void
   * @since 3.0.0
   */
  public function check_and_update_fonts()
  {
    $installed_fonts = get_option('divi_child_installed_fonts', []);

    if (empty($installed_fonts)) {
      return;
    }

    $current_metadata = $this->fetch_current_font_metadata();

    if (empty($current_metadata)) {
      return;
    }

    $fonts_to_update = [];

    foreach ($installed_fonts as $font_family => $local_font_data) {
      $local_metadata = $local_font_data['metadata'] ?? [];
      $local_version = $local_metadata['version'] ?? 'v1';
      $local_last_modified = $local_metadata['lastModified'] ?? '2020-01-01';

      if (isset($current_metadata[$font_family])) {
        $current_font = $current_metadata[$font_family];
        $current_version = $current_font['version'] ?? 'v1';
        $current_last_modified = $current_font['lastModified'] ?? '2020-01-01';

        if ($current_version !== $local_version || $current_last_modified !== $local_last_modified) {
          $fonts_to_update[] = [
            'family' => $font_family,
            'old_version' => $local_version,
            'new_version' => $current_version,
          ];
        }
      }
    }

    if (empty($fonts_to_update)) {
      return;
    }

    $download_service = new Downloads($this->module);

    foreach ($fonts_to_update as $font_update) {
      $download_service->update_single_font($font_update);
    }

    $this->log_font_updates($fonts_to_update);
  }


  /**
   * Fetches current font metadata from the Google Web Fonts Helper API
   * @return array
   * @since 3.0.0
   */
  private function fetch_current_font_metadata()
  {
    $installed_fonts = get_option('divi_child_installed_fonts', []);
    $metadata = [];

    foreach ($installed_fonts as $font_family => $font_data) {
      $font_id = $font_data['id'] ?? \strtolower(\str_replace(' ', '-', $font_family));

      $response = wp_remote_get("https://gwfh.mranftl.com/api/fonts/{$font_id}", [
        'timeout' => 15,
        'user-agent' => 'DiviChild/3.0 LocalFonts-AutoUpdate'
      ]);

      if (is_wp_error($response)) {
        continue;
      }

      $font = \json_decode(wp_remote_retrieve_body($response), true);
      if (\is_array($font) && isset($font['family'])) {
        $metadata[$font['family']] = $font;
      }
    }

    return $metadata;
  }


  /**
   * Saves the font update log to the database
   * @param array $font_updates
   * @return void
   * @since 3.0.0
   */
  private function log_font_updates($font_updates)
  {
    $update_log = get_option('divi_child_font_update_log', []);

    $log_entry = [
      'timestamp' => current_time('mysql'),
      'type' => 'auto-update',
      'updated_fonts' => [],
      'total_updated' => \count($font_updates)
    ];

    foreach ($font_updates as $update) {
      $log_entry['updated_fonts'][] = [
        'family' => $update['family'],
        'old_version' => $update['old_version'],
        'new_version' => $update['new_version']
      ];
    }

    \array_unshift($update_log, $log_entry);
    $update_log = \array_slice($update_log, 0, 50);

    update_option('divi_child_font_update_log', $update_log);
  }


  /**
   * Cleans up orphaned font files that are no longer tracked
   * @return void
   * @since 3.0.0
   */
  public function cleanup_orphaned_files()
  {
    $upload_dir = wp_upload_dir();
    $fonts_dir = $upload_dir['basedir'] . '/local-fonts';

    if (!\file_exists($fonts_dir)) {
      return;
    }

    $tracked_files = get_option('divi_child_font_files', []);
    $expected_files = [];

    foreach ($tracked_files as $files) {
      $expected_files = \array_merge($expected_files, $files);
    }

    $actual_files = \array_diff(\scandir($fonts_dir), ['.', '..']);

    foreach ($actual_files as $file) {
      if (!\in_array($file, $expected_files)) {
        \unlink($fonts_dir . '/' . $file);
      }
    }
  }
}
