<?php
if (!defined('ABSPATH')) die();

// Helper function to use in your theme to return a theme option value
function divi_child_get_theme_option($id = '') {
	$options = get_option('divi_child_options');
  if ( isset( $options[$id] ) ) {
    return $options[$id];
  }
}
