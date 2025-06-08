<?php

namespace DiviChild\Modules\LocalFonts\Services;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\CommonServiceInterface;
use DiviChild\Modules\LocalFonts\Downloads;

class CommonService extends ModuleService implements CommonServiceInterface
{
  public function init_common()
  {
    parent::init_common();

    // 🎯 GOOGLE FONTS DEAKTIVIERUNG - läuft überall (Frontend + Builder)
    if ($this->is_option_enabled('disable_google_fonts')) {
        add_filter('et_builder_load_google_fonts', '__return_false');
        add_filter('et_google_fonts_enqueue', '__return_false');
        error_log("✅ Google Fonts DISABLED in COMMON (Frontend + Builder)");
    }

    // 🎯 FONT-HOOKS IMMER REGISTRIEREN (Frontend + Builder)
    if (!empty($this->get_module_option('selected_fonts'))) {
      add_filter('et_websafe_fonts', [$this, 'register_local_fonts_websafe'], 999, 1);
    }

    // 🎯 AUTO-UPDATE SYSTEM - läuft überall
    add_action('divi_child_check_font_updates', [$this, 'check_and_update_fonts']);

    // Cron-Job registrieren (nur einmal)
    if (!wp_next_scheduled('divi_child_check_font_updates')) {
      wp_schedule_event(strtotime('03:00:00'), 'daily', 'divi_child_check_font_updates');
    }

    // Cleanup-Cron
    add_action('divi_child_daily_cron', [$this, 'cleanup_orphaned_files']);
  }


  /**
   * 🎯 Registriert lokale Fonts als Websafe Fonts (Frontend + Builder)
   */
  public function register_local_fonts_websafe($fonts)
  {
    $installed_fonts = get_option('divi_child_installed_fonts', []);

    if (empty($installed_fonts)) {
      error_log("❌ No installed fonts found in database");
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
   * Formatiert Font-Variants für Divi's erwartetes Format
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
        case '300':
        case '500':
        case '600':
        case '700':
        case '800':
        case '900':
          $divi_styles[] = $variant;
          break;
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

    // Standard-Weights hinzufügen falls leer
    if (empty($divi_styles)) {
      $divi_styles = ['400', '700'];
    }

    return implode(',', array_unique($divi_styles));
  }


  /**
   * Mappt Google Fonts Kategorien auf Divi Font-Types
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


  /**
   * Checks for updates of installed fonts and updates them if necessary.
   * @package LocalFonts
   * @since 3.0.0
   */
  public function check_and_update_fonts()
  {
    $installed_fonts = get_option('divi_child_installed_fonts', []);

    if (empty($installed_fonts)) {
      error_log("🔄 No local fonts installed, skipping auto-update check");
      return;
    }

    error_log("🔄 Auto-checking for font updates for " . count($installed_fonts) . " fonts");

    // Aktuelle Metadaten von API holen
    $current_metadata = $this->fetch_current_font_metadata();

    if (empty($current_metadata)) {
      error_log("❌ Failed to fetch current font metadata for auto-update check");
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
            'metadata' => $current_font
          ];

          error_log("🔄 Auto-update available: {$font_family} ({$local_version} → {$current_version})");
        }
      }
    }

    if (empty($fonts_to_update)) {
      error_log("✅ All fonts are up to date (auto-check)");
      return;
    }

    error_log("🔄 Auto-updating " . count($fonts_to_update) . " fonts");

    // Download-Service für Updates nutzen
    $download_service = new Downloads($this->module);

    foreach ($fonts_to_update as $font_update) {
      $download_service->update_single_font($font_update);
    }

    $this->log_font_updates($fonts_to_update);
  }


  /**
   * Fetches the current font metadata from the Google Web Fonts Helper API.
   * @package LocalFonts
   * @since 3.0.0
   */
  private function fetch_current_font_metadata()
  {
    $response = wp_remote_get("https://gwfh.mranftl.com/api/fonts", [
      'timeout' => 30,
      'user-agent' => 'DiviChild/3.0 LocalFonts-AutoUpdate'
    ]);

    if (is_wp_error($response)) {
      return [];
    }

    $fonts_data = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($fonts_data)) {
      return [];
    }

    $metadata = [];
    foreach ($fonts_data as $font) {
      $metadata[$font['family']] = $font;
    }

    return $metadata;
  }


  /**
   * Saves the font update log to the database.
   * @package LocalFonts
   * @since 3.0.0
   */
  private function log_font_updates($font_updates)
  {
    $update_log = get_option('divi_child_font_update_log', []);

    $log_entry = [
      'timestamp' => current_time('mysql'),
      'type' => 'auto-update',
      'updated_fonts' => [],
      'total_updated' => count($font_updates)
    ];

    foreach ($font_updates as $update) {
      $log_entry['updated_fonts'][] = [
        'family' => $update['family'],
        'old_version' => $update['old_version'],
        'new_version' => $update['new_version']
      ];
    }

    array_unshift($update_log, $log_entry);
    $update_log = array_slice($update_log, 0, 50);

    update_option('divi_child_font_update_log', $update_log);
  }


  /**
   * Cleans up orphaned font files that are no longer tracked.
   * @package LocalFonts
   * @since 3.0.0
   */
  public function cleanup_orphaned_files()
  {
    $upload_dir = wp_upload_dir();
    $fonts_dir = $upload_dir['basedir'] . '/local-fonts';

    if (!file_exists($fonts_dir)) {
      return;
    }

    $tracked_files = get_option('divi_child_font_files', []);
    $expected_files = [];

    foreach ($tracked_files as $files) {
      $expected_files = array_merge($expected_files, $files);
    }

    $actual_files = array_diff(scandir($fonts_dir), ['.', '..']);

    foreach ($actual_files as $file) {
      if (!in_array($file, $expected_files)) {
        unlink($fonts_dir . '/' . $file);
      }
    }
  }
}