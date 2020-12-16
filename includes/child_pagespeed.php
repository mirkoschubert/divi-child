<?php
if (!defined('ABSPATH')) die();

/**
 * SPEED: Disable Self Pingback
 * @since 1.4.0
 */
function divi_child_disable_pingback( &$links ) {
  foreach ( $links as $l => $link ) {
    if (0 === strpos($link, get_option('home'))) unset($links[$l]);
  }
}
if (divi_child_get_theme_option('page_pingback') === 'on') {
  add_action('pre_ping', 'divi_child_disable_pingback');
}


/**
 * SPEED: Remove Dashicons on Frontend
 * @since 1.4.0
 */
function divi_child_dequeue_dashicons() {
  if (current_user_can( 'update_core' )) {
    return;
  }
  wp_deregister_style('dashicons');
}
if (divi_child_get_theme_option('remove_dashicons') === 'on') {
  add_action( 'wp_enqueue_scripts', 'divi_child_dequeue_dashicons' );
}


/**
 * SPEED: Remove CSS & JS version query strings
 * @since 1.4.0
 */
function divi_child_remove_query_strings( $src ) {
if( strpos( $src, '?ver=' ) )
 $src = remove_query_arg( 'ver', $src );
return $src;
}
if (divi_child_get_theme_option('version_query_strings') === 'on') {
  add_filter( 'style_loader_src', 'divi_child_remove_query_strings', 10, 2 );
  add_filter( 'script_loader_src', 'divi_child_remove_query_strings', 10, 2 );
}


/**
 * SPEED: Remove Shortlink from Head
 * @since 1.4.0
 */
function divi_child_remove_shortlink() {
  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
}
if (divi_child_get_theme_option('remove_shortlink') === 'on') {
  add_action('init', 'divi_child_remove_shortlink');
}


/**
 * SPEED: Preload some of the biggest fonts for speed
 * @since 1.4.0
 */
function divi_child_preload_fonts() {
  $fonts = array(
    '/wp-content/themes/Divi/core/admin/fonts/modules.ttf',
  );

  foreach ($fonts as $font) {
    $font_type = 'font/' . substr($font, strrpos($font, ".") + 1);
    echo '<link rel="preload" href="' . get_site_url()  . $font . '" as="font" type="' . $font_type . '" crossorigin />';
  }
}
if (divi_child_get_theme_option('preload_fonts') === 'on') {
  // TODO: Not working???
  do_action( 'qm/notice', 'SPEED: Preload fonts' );
  add_action('wp_head', 'divi_child_preload_fonts');
}


?>