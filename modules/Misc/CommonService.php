<?php

namespace DiviChild\Modules\Misc;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\CommonServiceInterface;

class CommonService extends ModuleService implements CommonServiceInterface
{
  /**
   * Initializes the common service
   * @return void
   * @package Misc
   * @since 1.0.0
   */
  public function init_common()
  {
    parent::init_common();

    // 1. Unregister Projects
    if ($this->is_option_enabled('disable_projects')) {
      add_action('init', [$this, 'unregister_projects'], 15);
    }

    // 2. Stop Update Mails
    if ($this->is_option_enabled('stop_mail_updates')) {
      add_filter('auto_core_update_send_email', [$this, 'stop_update_mails'], 10, 4);
      add_filter('auto_plugin_update_send_email', '__return_false');
      add_filter('auto_theme_update_send_email', '__return_false');
    }

    // 3. Add SVG and WebP support
    if ($this->is_option_enabled('svg_support') || $this->is_option_enabled('webp_support')) {
      add_action('upload_mimes', [$this, 'supported_mimes'], 99);
      add_filter('wp_check_filetype_and_ext', [$this, 'handle_svg_upload'], 10, 4);
    }
  }


  /**
   * Unregisters default Project type and taxonomies
   * @return void
   * @package Misc
   * @since 1.0.0
   */
  public function unregister_projects()
  {
    if (taxonomy_exists('project_category')) {
      unregister_taxonomy('project_category');
    }

    if (taxonomy_exists('project_tag')) {
      unregister_taxonomy('project_tag');
    }

    // Then unregister the post type
    if (post_type_exists('project')) {
      unregister_post_type('project');
    }
  }

  /**
   * Stops email notifications for automatic updates
   * @return void
   * @package Misc
   * @since 1.0.0
   */
  public function stop_update_mails($send, $type, $core_update, $result): bool
  {
    return empty($type) || $type !== 'success';
  }


  /**
   * Adds SVG and WebP support for file uploads
   * @param array $mimes
   * @return array
   * @package Misc
   * @since 1.0.0
   */
  public function supported_mimes(array $mimes = []): array
  {
    if ($this->is_option_enabled('svg_support')) {
      $mimes['svg'] = 'image/svg+xml';
      $mimes['svgz'] = 'image/svg+xml';
    }
    if ($this->is_option_enabled('webp_support')) {
      $mimes['webp'] = 'image/webp';
    }
    return $mimes;
  }


  /**
   * Handles SVG upload checks and compatibility fixes
   * @param array $checked Current check data
   * @param string $file Path to the uploaded file
   * @param string $filename The filename of the uploaded file
   * @param array $mimes Array of allowed mime types
   * @return array Modified check data
   * @package Misc
   * @since 1.0.0
   */
  public function handle_svg_upload(array $data, string $file, string $filename, array $mimes): array
  {
    global $wp_version;

    // Spezieller Fix für WordPress 4.7.1 und 4.7.2
    if ($wp_version === '4.7.1' || $wp_version === '4.7.2') {
      $filetype = wp_check_filetype($filename, $mimes);
      return [
        'ext' => $filetype['ext'],
        'type' => $filetype['type'],
        'proper_filename' => $data['proper_filename'],
      ];
    }

    // Reguläre SVG-Typenprüfung
    if (!$data['type']) {
      $check_filetype = wp_check_filetype($filename, $mimes);
      $ext = $check_filetype['ext'];
      $type = $check_filetype['type'];
      $proper_filename = $filename;

      // Spezielle Behandlung für Nicht-SVG-Bilder
      if ($type && 0 === strpos($type, 'image/') && $ext !== 'svg') {
        $ext = $type = false;
      }

      $data = compact('ext', 'type', 'proper_filename');
    }

    return $data;
  }

}