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

    // 3. Enable infinite scroll for media library
    if ($this->is_option_enabled('media_infinite_scroll')) {
      add_filter('media_library_infinite_scrolling', '__return_true');
      add_filter('upload_per_page', function () {
        return 999999999999999999;
      });
    }

    // 4. Add SVG, WebP and AVIF support
    if ($this->is_option_enabled('svg_support') || $this->is_option_enabled('webp_support') || $this->is_option_enabled('avif_support')) {
      // Version-aware filter registration
      if (version_compare(get_bloginfo('version'), '5.8', '<')) {
        add_filter('mime_types', [$this, 'supported_mimes']);
      } else {
        add_filter('upload_mimes', [$this, 'supported_mimes']);
      }
      
      // Add file type validation for modern image formats
      if ($this->is_option_enabled('svg_support') || $this->is_option_enabled('webp_support') || $this->is_option_enabled('avif_support')) {
        add_filter('wp_check_filetype_and_ext', [$this, 'handle_modern_image_upload'], 10, 5);
      }
    }

    // 5. Disable Divi Upsells
    if ($this->is_option_enabled('disable_divi_upsells')) {
      add_action('init', [$this, 'hide_divi_dashboard']);
    }
  }

  public function enqueue_common_assets()
  {
    error_log('Enqueuing common assets for Misc module');
    // 1. Disable Divi Upsells
    if ($this->is_option_enabled('disable_divi_upsells')) {
      wp_enqueue_style('divi-child-upsells', $this->module->get_asset_url("css/misc-disable-divi-upsells.min.css"));
    }

    // 2. Disable Divi AI
    if ($this->is_option_enabled('disable_divi_ai')) {
      wp_enqueue_style('divi-child-ai', $this->module->get_asset_url("css/misc-disable-divi-ai.min.css"));
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
    // Add SVG support (never natively supported by WordPress)
    if ($this->is_option_enabled('svg_support')) {
      $mimes['svg'] = 'image/svg+xml';
    }
    
    // WebP support (only for WordPress < 5.8, as 5.8+ has native support)
    if ($this->is_option_enabled('webp_support') && version_compare(get_bloginfo('version'), '5.8', '<')) {
      $mimes['webp'] = 'image/webp';
    }
    
    // AVIF support (only for WordPress < 6.5, as 6.5+ has native support)
    if ($this->is_option_enabled('avif_support') && version_compare(get_bloginfo('version'), '6.5', '<')) {
      $mimes['avif'] = 'image/avif';
    }
    
    return $mimes;
  }


  /**
   * Handles modern image format upload checks and compatibility fixes
   * @param array $data File data array with 'ext', 'type', 'proper_filename'
   * @param string $file Path to the uploaded file
   * @param string $filename The filename of the uploaded file
   * @param array $mimes Array of allowed mime types
   * @param string|false $real_mime The actual mime type
   * @return array Modified check data
   * @package Misc
   * @since 1.0.0
   */
  public function handle_modern_image_upload($data, $file, $filename, $mimes, $real_mime)
  {
    // Only process if type is not already set
    if (!empty($data['type'])) {
      return $data;
    }
    
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    // Handle SVG files
    if ($ext === 'svg' && $this->is_option_enabled('svg_support')) {
      $data['type'] = 'image/svg+xml';
      $data['ext'] = 'svg';
    }
    
    // Handle WebP files (only for WordPress < 5.8)
    elseif ($ext === 'webp' && $this->is_option_enabled('webp_support') && version_compare(get_bloginfo('version'), '5.8', '<')) {
      $data['type'] = 'image/webp';
      $data['ext'] = 'webp';
    }
    
    // Handle AVIF files (only for WordPress < 6.5)
    elseif ($ext === 'avif' && $this->is_option_enabled('avif_support') && version_compare(get_bloginfo('version'), '6.5', '<')) {
      $data['type'] = 'image/avif';
      $data['ext'] = 'avif';
    }
    
    return $data;
  }

  public function hide_divi_dashboard()
  {
    global $pagenow;
    $page = !empty($_GET['page']) ? $_GET['page'] : ''; //phpcs:ignore
    if (!empty($page) && $page === 'et_onboarding' && !empty($pagenow) && $pagenow === 'admin.php') {
      wp_die(esc_attr__("You don't have permission to access this page", 'divi-child'));
    }
  }

}