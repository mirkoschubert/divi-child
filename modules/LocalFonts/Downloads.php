<?php

namespace DiviChild\Modules\LocalFonts;

class Downloads
{
  protected $module;

  public function __construct($module)
  {
    $this->module = $module;
  }


  /**
   * Downloads and installs multiple fonts from Google Web Fonts Helper.
   * @package LocalFonts
   * @since 3.0.0
   */
  public function download_fonts($font_families)
  {
    $upload_dir = wp_upload_dir();
    $fonts_dir = $upload_dir['basedir'] . '/local-fonts';

    if (!\file_exists($fonts_dir)) {
      wp_mkdir_p($fonts_dir);
    }

    $installed_fonts = get_option('divi_child_installed_fonts', []);
    $font_display = $this->module->get_options()['font_display'] ?? 'swap';

    foreach ($font_families as $font_family) {
      if (\defined('WP_DEBUG') && WP_DEBUG) {
        error_log("LocalFonts: Downloading font: {$font_family}");
      }

      $metadata = $this->get_font_metadata($font_family);
      $font_id = $metadata['id'];

      if ($this->download_font_zip($font_id, $font_family, $fonts_dir, $font_display, $metadata)) {
        $installed_fonts[$font_family] = [
          'id' => $font_id,
          'installed_date' => current_time('mysql'),
          'last_updated' => current_time('mysql'),
          'metadata' => $metadata
        ];
        if (\defined('WP_DEBUG') && WP_DEBUG) {
          error_log("LocalFonts: Font '{$font_family}' downloaded successfully");
        }
      }
    }

    update_option('divi_child_installed_fonts', $installed_fonts);
  }


  /**
   * Updates a single font by removing the old version and downloading the new one.
   * @package LocalFonts
   * @since 3.0.0
   */
  public function update_single_font($font_update)
  {
    $font_family = $font_update['family'];

    if (\defined('WP_DEBUG') && WP_DEBUG) {
      error_log("LocalFonts: Auto-updating font: {$font_family}");
    }

    $this->remove_fonts([$font_family]);
    $this->download_fonts([$font_family]);
  }


  /**
   * Downloads a font ZIP file from Google Web Fonts Helper.
   * @package LocalFonts
   * @since 3.0.0
   */
  private function download_font_zip($font_id, $font_family, $fonts_dir, $font_display, $metadata)
  {
    $zip_url = "https://gwfh.mranftl.com/api/fonts/{$font_id}?download=zip&subsets=latin-ext&formats=woff2";

    $response = wp_remote_get($zip_url, [
      'timeout' => 30,
      'user-agent' => 'DiviChild/3.0 LocalFonts'
    ]);

    if (is_wp_error($response)) {
      error_log("LocalFonts: ZIP download failed for {$font_family}: " . $response->get_error_message());
      return false;
    }

    $zip_content = wp_remote_retrieve_body($response);
    $temp_zip = $fonts_dir . '/' . $font_id . '_temp.zip';
    \file_put_contents($temp_zip, $zip_content);

    $extracted_files = $this->extract_zip_wordpress_style($temp_zip, $fonts_dir);
    \unlink($temp_zip);

    if (empty($extracted_files)) {
      return false;
    }

    $this->generate_css_from_metadata($font_family, $extracted_files, $fonts_dir, $font_display, $metadata);

    $css_filename = sanitize_title($font_family) . '.css';
    $this->track_font_files($font_family, \array_merge($extracted_files, [$css_filename]));

    return true;
  }

  /**
   * Gets font metadata from transient or returns default values.
   * @package LocalFonts
   * @since 3.0.0
   */
  private function get_font_metadata($font_family)
  {
    $metadata = get_transient('divi_child_gwfh_fonts_metadata');

    if (isset($metadata[$font_family])) {
      return $metadata[$font_family];
    }

    // Fallback
    return [
      'id' => \strtolower(\str_replace(' ', '-', $font_family)),
      'family' => $font_family,
      'variants' => ['regular'],
      'subsets' => ['latin-ext'],
      'category' => 'sans-serif',
      'version' => 'v1'
    ];
  }


  /**
   * Extracts a ZIP file using WordPress functions.
   * @package LocalFonts
   * @since 3.0.0
   */
  private function extract_zip_wordpress_style($zip_path, $fonts_dir)
  {
    require_once(ABSPATH . 'wp-admin/includes/file.php');

    WP_Filesystem();
    global $wp_filesystem;

    $extracted_files = [];
    $temp_extract_dir = $fonts_dir . '/temp_extract_' . \uniqid();
    wp_mkdir_p($temp_extract_dir);

    $result = unzip_file($zip_path, $temp_extract_dir);

    if (is_wp_error($result)) {
      error_log("LocalFonts: Unzip failed: " . $result->get_error_message());
      return [];
    }

    $files = \scandir($temp_extract_dir);

    foreach ($files as $file) {
      if ($file === '.' || $file === '..')
        continue;

      $source_path = $temp_extract_dir . '/' . $file;
      $dest_path = $fonts_dir . '/' . $file;

      if (\is_file($source_path) && \pathinfo($file, PATHINFO_EXTENSION) === 'woff2') {
        \rename($source_path, $dest_path);
        $extracted_files[] = $file;
        if (\defined('WP_DEBUG') && WP_DEBUG) {
          error_log("LocalFonts: Extracted: {$file}");
        }
      }
    }

    $wp_filesystem->rmdir($temp_extract_dir, true);
    return $extracted_files;
  }


  /**
   * Generates CSS rules from metadata and extracted files.
   * @package LocalFonts
   * @since 3.0.0
   */
  private function generate_css_from_metadata($font_family, $extracted_files, $fonts_dir, $font_display, $metadata)
  {
    $upload_dir = wp_upload_dir();
    $fonts_url = $upload_dir['baseurl'] . '/local-fonts';
    $css_rules = [];

    // Get variants from metadata
    $variants = $metadata['variants'] ?? ['regular'];

    foreach ($extracted_files as $filename) {
      // Versuche Weight und Style aus Dateinamen zu extrahieren
      $weight = '400';
      $style = 'normal';

      // Robuste Extraktion
      if (\preg_match('/(\d{3})/', $filename, $weight_matches)) {
        $weight = $weight_matches[1];
      }

      if (\strpos($filename, 'italic') !== false) {
        $style = 'italic';
      }

      // Spezielle Behandlung für "regular"
      if (\strpos($filename, 'regular') !== false) {
        $weight = '400';
        $style = 'normal';
      }

      // @font-face CSS generieren
      $css_rules[] = \sprintf(
        "/* %s %s %s */\n@font-face {\n  font-display: %s;\n  font-family: '%s';\n  font-style: %s;\n  font-weight: %s;\n  src: url('%s/%s') format('woff2');\n}",
        $font_family,
        $weight,
        $style,
        $font_display,
        $font_family,
        $style,
        $weight,
        $fonts_url,
        $filename
      );
    }

    if (empty($css_rules)) {
      error_log("LocalFonts: No CSS rules generated for {$font_family}");
      return;
    }

    // CSS-Datei speichern
    $css_content = \implode("\n\n", $css_rules);
    $css_filename = sanitize_title($font_family) . '.css';
    $css_path = $fonts_dir . '/' . $css_filename;

    \file_put_contents($css_path, $css_content);
  }


  /**
   * Tracks font files in the database.
   * @package LocalFonts
   * @since 3.0.0
   */
  private function track_font_files($font_family, $files)
  {
    $tracked_files = get_option('divi_child_font_files', []);
    $tracked_files[$font_family] = $files;
    update_option('divi_child_font_files', $tracked_files);
  }


  /**
   * Removes specified font families and their files.
   * @package LocalFonts
   * @since 3.0.0
   */
  public function remove_fonts($font_families)
  {
    $upload_dir = wp_upload_dir();
    $fonts_dir = $upload_dir['basedir'] . '/local-fonts';
    $installed_fonts = get_option('divi_child_installed_fonts', []);
    $tracked_files = get_option('divi_child_font_files', []);

    foreach ($font_families as $font_family) {
      if (isset($tracked_files[$font_family])) {
        foreach ($tracked_files[$font_family] as $file) {
          $file_path = $fonts_dir . '/' . $file;
          if (\file_exists($file_path)) {
            \unlink($file_path);
            if (\defined('WP_DEBUG') && WP_DEBUG) {
              error_log("LocalFonts: Deleted: {$file}");
            }
          }
        }
        unset($tracked_files[$font_family]);
      }

      unset($installed_fonts[$font_family]);
      if (\defined('WP_DEBUG') && WP_DEBUG) {
        error_log("LocalFonts: Font '{$font_family}' removed");
      }
    }

    update_option('divi_child_installed_fonts', $installed_fonts);
    update_option('divi_child_font_files', $tracked_files);
  }
}