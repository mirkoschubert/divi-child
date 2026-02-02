<?php

namespace DiviChild\Modules\Pagespeed;

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
    // === Frontend Only ===
    if (!is_admin()) {
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
  }

  /**
   * Removes the pingback header
   * @param array $links
   * @return void
   * @since 1.0.0
   */
  public function remove_pingback(&$links)
  {
    foreach ($links as $l => $link) {
      if (0 === \strpos($link, get_option('home'))) {
        unset($links[$l]);
      }
    }
  }

  /**
   * Removes dashicons from the frontend
   * @return void
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
   * Removes CSS and JS version query strings
   * @param string $src
   * @return string
   * @since 1.0.0
   */
  public function remove_query_strings($src)
  {
    if (\strpos($src, '?ver=')) {
      $src = remove_query_arg('ver', $src);
    }

    return $src;
  }

  /**
   * Removes the shortlink header
   * @return void
   * @since 1.0.0
   */
  public function remove_shortlink()
  {
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
  }

  /**
   * Preloads fonts
   * @return void
   * @since 1.0.0
   */
  public function preload_fonts()
  {
    $fonts_data = $this->get_module_option('preload_fonts_list');

    if (\is_array($fonts_data)) {
      foreach ($fonts_data as $font_item) {
        if (\is_array($font_item) && isset($font_item['path'])) {
          $font_path = $font_item['path'];
        } else {
          $font_path = \is_string($font_item) ? $font_item : '';
        }

        if (!empty($font_path)) {
          $font_type = 'font/' . \substr($font_path, \strrpos($font_path, ".") + 1);
          $font_url = (\substr($font_path, 0, 4) === "http") ? $font_path : get_site_url() . $font_path;
          echo '<link rel="preload" href="' . esc_url($font_url) . '" as="font" type="' . esc_attr($font_type) . '" crossorigin="anonymous">' . "\n";
        }
      }
    }
  }
}
