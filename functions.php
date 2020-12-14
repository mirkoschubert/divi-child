<?php
if (!defined('ABSPATH')) die();

define('DIVI_CHILD_VERSION', '1.4.0');

// INFO: Setup

include_once('admin/admin.php');

/**
 * STATIC: Load all scripts and styles
 */
function divi_child_enqueue_scripts() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style('divi-fonts', get_stylesheet_directory_uri() . '/assets/css/fonts.css');
  wp_enqueue_script( 'divi-scripts', get_stylesheet_directory_uri() . '/assets/js/main.js', array(), null, true);
}
add_action( 'wp_enqueue_scripts', 'divi_child_enqueue_scripts' );


/**
 * STATIC: TODO: Load all language files
 */
function divi_child_languages() {
  load_child_theme_textdomain( 'Divi', get_stylesheet_directory() . '/languages/theme' );
  load_child_theme_textdomain( 'et-core', get_stylesheet_directory() . '/languages/core' );
  load_child_theme_textdomain( 'et_builder', get_stylesheet_directory() . '/languages/builder' );
}
add_action( 'after_setup_theme', 'divi_child_languages');


/**
 * STATIC: Custom Body Class for Child Theme
 */
function divi_child_body_class( $classes ) {
  $classes[] = 'child';
  return $classes;
}
add_action( 'body_class', 'divi_child_body_class' );


// GDPR
include_once('includes/child_gdpr.php');

// Bugfixes
include_once('includes/child_bugfixes.php');

// Pagespeed
include_once('includes/child_pagespeed.php');

// Miscellaneous
include_once('includes/child_misc.php')


?>