<?php

namespace DiviChild\Modules\Pagespeed;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\FrontendServiceInterface;

class FrontendService extends ModuleService implements FrontendServiceInterface
{
  /**
   * Initialisiert Frontend-spezifische Funktionalität
   * @return void
   */
  public function init_frontend()
  {
    parent::init_frontend();
    
    // 1. Remove Pingback
    if ($this->is_option_enabled('remove_pingback')) {
      add_filter('pre_ping', [$this, 'remove_pingback']);
    }
    // 2. Remove Dashicons
    if ($this->is_option_enabled('remove_dashicons')) {
      add_action('wp_enqueue_scripts', [$this, 'remove_dashicons']);
    }
    // 3. Remove Version Strings
    if ($this->is_option_enabled('remove_version_strings')) {
      add_filter('style_loader_src', [$this, 'remove_query_strings'], 10, 2);
      add_filter('script_loader_src', [$this, 'remove_query_strings'], 10, 2);
    }
    // 4. Remove Shortlink
    if ($this->is_option_enabled('remove_shortlink')) {
      add_action('init', [$this, 'remove_shortlink']);
    }
    // 5. Preload Fonts
    if ($this->is_option_enabled('preload_fonts') && !empty($this->get_module_option('preload_fonts_list'))) {
      add_action('wp_head', [$this, 'preload_fonts']);
    }
  }


  /**
   * Lädt Frontend-spezifische Assets
   * @return void
   * @package Pagespeed
   * @since 1.0.0
   */
  public function enqueue_frontend_assets()
  {
    // Keine Assets für dieses Modul im Frontend
  }


  /**
   * Entfernt den Pingback-Header
   * @param array $links
   * @return void
   * @package Pagespeed
   * @since 1.0.0
   */
  public function remove_pingback(&$links)
  {
    foreach ($links as $l => $link) {
      if (0 === strpos($link, get_option('home'))) {
        unset($links[$l]);
      }
    }
  }


  /**
   * Entfernt Dashicons vom Frontend
   * @return void
   * @package Pagespeed
   * @since 1.0.0
   */
  public function remove_dashicons()
  {
    if (current_user_can('update_core')) {
      return;
    }
    wp_deregister_style('dashicons');
  }


  /**
   * Entfernt CSS- und JS-Version-Query-Strings
   * @param string $src
   * @return string
   * @package Pagespeed
   * @since 1.0.0
   */
  public function remove_query_strings($src)
  {
    if (strpos($src, '?ver=')) {
      $src = remove_query_arg('ver', $src);
    }

    return $src;
  }


  /**
   * Entfernt den Shortlink-Header
   * @return void
   * @package Pagespeed
   * @since 1.0.0
   */
  public function remove_shortlink()
  {
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
  }

  /**
   * Preload Fonts
   * @return void
   * @package Pagespeed
   * @since 1.0.0
   */
  public function preload_fonts()
  {
    $fonts = $this->get_module_option('preload_fonts_list');
    foreach ($fonts as $font) {
      $font_type = 'font/' . substr($font, strrpos($font, ".") + 1);
      $font_path = (substr($font, 0, 4) === "http") ? $font : get_site_url() . $font;
      echo '<link rel="preload" href="' . $font_path . '" as="font" type="' . $font_type . '" crossorigin="anonymous">' . "\n";
    }
  }
}