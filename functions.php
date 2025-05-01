<?php
if (!defined('ABSPATH')) {
  die();
}

define('DIVI_CHILD_VERSION', '3.0.0');

// INFO: Setup

/**
 * STATIC: Load all scripts and styles
 */
/* function divi_child_enqueue_scripts() {
  wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
  wp_enqueue_style('divi-fonts', get_stylesheet_directory_uri() . '/assets/css/fonts.css');

  wp_enqueue_script('wp-i18n');
  wp_enqueue_script('divi-scripts', get_stylesheet_directory_uri() . '/assets/js/main.js', ['jquery', 'wp-i18n'], null, true);

  $options = get_option('divi_child_options');
  wp_localize_script('divi-scripts', 'themeOptions', $options);

}
add_action('wp_enqueue_scripts', 'divi_child_enqueue_scripts'); */

/**
 * STATIC: Register Script Translations
 * 
 * @since 2.3.0
 */
/* function divi_child_set_script_translations() {
  wp_set_script_translations('divi-scripts', 'divi-child', get_stylesheet_directory() . '/languages');
}
add_action('wp_enqueue_scripts', 'divi_child_set_script_translations', 100); */


/**
 * STATIC: Load all language files
 */
/* function divi_child_languages() {
  load_child_theme_textdomain('divi-child', get_stylesheet_directory() . '/languages');
  load_child_theme_textdomain('Divi', get_stylesheet_directory() . '/languages/theme'); //Divi Theme
  load_child_theme_textdomain('et-core', get_stylesheet_directory() . '/languages/core'); //Divi Core
  load_child_theme_textdomain('et_builder', get_stylesheet_directory() . '/languages/builder'); //Divi Builder
}
add_action('after_setup_theme', 'divi_child_languages'); */

/**
 * STATIC: Custom Body Class for Child Theme
 */
/* function divi_child_body_class($classes) {
  $classes[] = 'child';
  return $classes;
}
add_action('body_class', 'divi_child_body_class'); */

// Migration
/* include_once 'includes/migration.php';
migrate(DIVI_CHILD_VERSION); */

// Admin
//include_once 'admin/admin.php';

// Helpers
//include_once 'includes/helpers.php';

// Custom Fonts
//include_once 'includes/class_custom_fonts.php';
//Custom_Fonts::get_instance();

// GDPR
//include_once 'includes/child_gdpr.php';

// Bugfixes
//include_once 'includes/child_bugfixes.php';

// Pagespeed
//include_once 'includes/child_pagespeed.php';

// Accessibility
//include_once 'includes/child_a11y.php';

// Miscellaneous
//include_once 'includes/child_misc.php';

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use DiviChild\Core\Theme;

$plugin = new Theme();
$plugin->init();

/** -------- Add your own code after this! -------- **/

?>