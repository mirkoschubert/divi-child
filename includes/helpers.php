<?php
if (!defined('ABSPATH')) {
  die();
}

// Helper function to use in your theme to return a theme option value
function divi_child_get_theme_option($topic = '',$id = '') {
  $options = get_option('divi_child_options');
  if (isset($options[$topic][$id])) {
    return $options[$topic][$id];
  }
}

class Helpers {

  public static function article_link($path, $text) {
    return '<a href="https://academy.bricksbuilder.io/article/' . $path . '" target="_blank" rel="noopener">' . $text . '</a>';
  }

  public static function generate_random_id($echo = true) {
    $hash = self::generate_hash(md5(uniqid(rand(), true)));

    if ($echo) {
      echo $hash;
    }

    return $hash;
  }

  public static function generate_hash($string, $length = 6) {
    // Generate SHA1 hexadecimal string (40-characters)
    $sha1 = sha1($string);
    $sha1_length = strlen($sha1);
    $hash = '';

    // Generate random site hash based on SHA1 string
    for ($i = 0; $i < $length; $i++) {
      $hash .= $sha1[rand(0, $sha1_length - 1)];
    }

    // Convert site path to lowercase
    $hash = strtolower($hash);

    return $hash;
  }
}