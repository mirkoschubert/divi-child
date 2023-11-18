<?php
if (!defined('ABSPATH')) {
  die();
}

/**
 * A11Y: Fix viewport meta
 * @since 2.1.0
 */
function divi_child_remove_divi_viewport_meta() {
  remove_action('wp_head', 'et_add_viewport_meta');
}

function divi_child_fix_viewport_meta() {
  echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=1" />';
}
if (divi_child_get_theme_option('viewport_meta') === 'on') {
  add_action('init', 'divi_child_remove_divi_viewport_meta');
  add_action('wp_head', 'divi_child_fix_viewport_meta', 1);
}
