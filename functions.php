<?php
if (!defined('ABSPATH')) die();

/**
* Load all scripts and styles
*/
function divi_child_enqueue_scripts() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'divi_child_enqueue_scripts' );


/**
 * Custom Body Class for Child Theme
 */
function divi_child_body_class( $classes ) {
  $classes[] = 'child';
  return $classes;
}
add_action( 'body_class', 'divi_child_body_class' );


// INFO: Disable Emojis 

/**
 * Disable the emoji's
 */
function divi_child_disable_emojis() {
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  add_filter( 'tiny_mce_plugins', 'divi_child_disable_emojis_tinymce' );
  add_filter( 'wp_resource_hints', 'divi_child_disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'divi_child_disable_emojis' );


/**
* Filter function used to remove the tinymce emoji plugin.
* @param array $plugins 
* @return array Difference betwen the two arrays
*/
function divi_child_disable_emojis_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}


/**
* Remove emoji CDN hostname from DNS prefetching hints.
* @param array $urls URLs to print for resource hints.
* @param string $relation_type The relation type the URLs are printed for.
* @return array Difference betwen the two arrays.
*/
function divi_child_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
  if ( 'dns-prefetch' == $relation_type ) {
    $emoji_svg_url = apply_filters( 'emoji_svg_url','https://s.w.org/images/core/emoji/2/svg/' );
  
    $urls = array_diff( $urls, array( $emoji_svg_url ) );
  }
  return $urls;
}


// INFO: Remove global DNS Prefetching 

/**
 * Remove DNS Prefetching for Wordpress
 */
function divi_child_remove_dns_prefetch() {
  remove_action('wp_head', 'wp_resource_hints', 2);
}
add_action( 'init', 'divi_child_remove_dns_prefetch');


// INFO: Remove REST API info from head and headers (for security reasons) 

remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

?>