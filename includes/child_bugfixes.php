<?php
if (!defined('ABSPATH')) die();

/**
 * BUGFIX: Removes Divi Support Center from Frontend
 * @since Divi 3.20.1
 */
function divi_child_remove_support_center() {
	wp_dequeue_script( 'et-support-center' );
	wp_deregister_script( 'et-support-center' );
}
if (divi_child_get_theme_option('support_center') === 'on') {
  add_action( 'wp_enqueue_scripts', 'divi_child_remove_support_center', 99999 );
}


/**
 * BUGFIX: Fixed Body Classes for Theme Builder Header
 * @since 1.2.0
 * @since Divi 4.0
 */
function divi_child_tb_fixed_body_class( $classes ) {
  $has_tb_header = in_array( 'et-tb-has-header', $classes );
  $is_fixed_header = 'on' === et_get_option( 'divi_fixed_nav', 'on' );

  if ($has_tb_header) {
    if ($is_fixed_header) {
      $classes[] = 'et_fixed_nav';
    } else {
      $classes[] = 'et_non_fixed_nav';
    }
    $classes[] = 'et_show_nav';
    // With et-tb-has-header not set the page-container gets a padding-top of the height of the header
    unset($classes[array_search('et-tb-has-header', $classes)]);
  }
  return $classes;
}
if (divi_child_get_theme_option('tb_header_fix') === 'on') {
  add_filter( 'body_class', 'divi_child_tb_fixed_body_class');
}


/**
 * BUG: Add special inline scripts
 * @since 1.3.0
 */
function divi_child_page_fix() {
  ?><script type="text/javascript">/* <![CDATA[ */ document.addEventListener('DOMContentLoaded', function() { var h = document.querySelector('.et-l--header'); var p = document.querySelector('#page-container'); if (document.querySelector('body.et_fixed_nav') !== null) { p.style.paddingTop = h.clientHeight + 'px'; }}); /* ]]> */</script><?php
}
if (divi_child_get_theme_option('tb_display_errors') === 'on') {
  add_action( 'wp_head', 'divi_child_page_fix', 1, 1);
}


?>