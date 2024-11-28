<?php
if (!defined('ABSPATH')) {
  die();
}

class Custom_Fonts {

  private static $instance = null;

  public function __construct() {

  }

  public static function get_instance() {
    if (self::$instance == null) {
      self::$instance = new Custom_Fonts();
    }
    return self::$instance;
  }

  public static function get_folder() {
    $upload_dir = wp_get_upload_dir();
    $folder = $upload_dir['error'] ? WP_CONTENT_DIR . '/uploads/fonts' : $upload_dir['basedir'] . '/fonts';

    return apply_filters('divi_child_fonts_folder', $folder);
  }

  public static function get_folder_url() {
    $upload_dir = wp_get_upload_dir();
    $folder_url = $upload_dir['error'] ? WP_CONTENT_URL . '/uploads/fonts' : $upload_dir['baseurl'] . '/fonts';

    if (is_ssl()) {
      $folder_url = set_url_scheme($folder_url, 'https');
    }
    return apply_filters('divi_child_fonts_folder_url', $folder_url);
  }

  public static function remove_dns_prefetch($urls, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
      $urls = array_diff($urls, array('fonts.googleapis.com'));
    } elseif ('preconnect' === $relation_type) {
      foreach ($urls as $key => $url) {
        if (!isset($url['href'])) {
          continue;
        }
        if (preg_match('/\/\/fonts\.(gstatic|googleapis)\.com/', $url['href'])) {
          unset($urls[$key]);
        }
      }
    }
    return $urls;
  }

}