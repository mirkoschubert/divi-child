<?php

namespace DiviChild\Modules\LocalFonts\Services;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\FrontendServiceInterface;

class FrontendService extends ModuleService implements FrontendServiceInterface
{
  

  /**
   * Initializes the frontend service for the Local Fonts module.
   */
  public function init_frontend()
  {
    parent::init_frontend();

    // CSS-Dateien einbinden (nur im Frontend)
    $selected_fonts = $this->get_module_option('selected_fonts');
    if (!empty($selected_fonts)) {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_local_fonts'], 5);
    }
  }


  /**
   * CSS-Dateien einbinden
   */
  public function enqueue_local_fonts()
  {
    $upload_dir = wp_upload_dir();
    $fonts_dir = $upload_dir['basedir'] . '/local-fonts';
    $fonts_url = $upload_dir['baseurl'] . '/local-fonts';
    $installed_fonts = get_option('divi_child_installed_fonts', []);

    foreach ($installed_fonts as $font_family => $font_data) {
      $css_filename = sanitize_title($font_family) . '.css';
      $css_path = $fonts_dir . '/' . $css_filename;
      $css_url = $fonts_url . '/' . $css_filename;

      if (file_exists($css_path)) {
        wp_enqueue_style(
          'divi-child-font-' . sanitize_title($font_family),
          $css_url,
          [],
          filemtime($css_path)
        );
      } else {
        error_log("❌ CSS file not found: {$css_path}");
      }
    }
  }


}