<?php
if (!defined('ABSPATH')) die();

// INFO: Setup 

/**
 * Set up automatic updates
 * @since WordPress 3.7
 */
add_filter( 'auto_update_core', '__return_true' );
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'auto_update_theme', '__return_true' );
add_filter( 'auto_update_translations', '__return_true' );

/**
 * Load all scripts and styles
 */
function divi_child_enqueue_scripts() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style('divi-fonts', get_stylesheet_directory_uri() . '/css/fonts.css');
}
add_action( 'wp_enqueue_scripts', 'divi_child_enqueue_scripts' );

/**
 * TODO: Load all language files
 */
function divi_child_languages() {
  load_child_theme_textdomain( 'Divi', get_stylesheet_directory() . '/languages/theme' );
  load_child_theme_textdomain( 'et-core', get_stylesheet_directory() . '/languages/core' );
  load_child_theme_textdomain( 'et_builder', get_stylesheet_directory() . '/languages/builder' );
}
add_action( 'after_setup_theme', 'divi_child_languages');

/**
 * Removes Divi Support Center from Frontend
 * @since Divi 3.20.1
 */
function divi_child_remove_support_center() {
	wp_dequeue_script( 'et-support-center' );
	wp_deregister_script( 'et-support-center' );
}
add_action( 'wp_enqueue_scripts', 'divi_child_remove_support_center', 99999 );

/**
 * Adds SVG support for file uploads
 */
function divi_child_supported_filetypes($filetypes) {

  $new = array('svg' => 'image/svg+xml', 'svg' => 'image/svg');
  return array_merge($filetypes, $new);
}
add_action('upload_mimes', 'divi_child_supported_filetypes');

/**
 * Custom Body Class for Child Theme
 */
function divi_child_body_class( $classes ) {
  $classes[] = 'child';
  return $classes;
}
add_action( 'body_class', 'divi_child_body_class' );

/**
 * Fixed Body Classes for Theme Builder Header
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
    unset($classes[array_search('et-tb-has-header', $classes)]);
  }
  return $classes;
}
add_filter( 'body_class', 'divi_child_tb_fixed_body_class');

/**
 * Set Layout ID for the Main Header
 */
function divi_child_set_layout_id( $layout_id, $post_type ) {
  if ($post_type === 'et_header_layout') {
    $layout_id = 'main-header';
  }
  return $layout_id;
}
add_filter( 'et_builder_layout_id', 'divi_child_set_layout_id', 10, 2);

// INFO: Comments (external links & comments IP) 

/**
 * Makes every comment and comment author link truely external (except 'respond')
 */
function divi_child_external_comment_links( $content ){
  return str_replace( "<a ", "<a target='_blank' ", $content );
}
add_filter( "comment_text", "divi_child_external_comment_links" );
add_filter( "get_comment_author_link", "divi_child_external_comment_links" );


/**
 * Removes IP addresses from comments (old entries have to be deleted by hand)
 */
function divi_child_remove_comments_ip( $comment_author_ip ) {
  return '';
}
add_filter( 'pre_comment_user_ip', 'divi_child_remove_comments_ip' );


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


// INFO: Disable oEmbeds

/**
 * Disable oEmbeds
 */
function divi_child_disable_embeds() {
  remove_action( 'rest_api_init', 'wp_oembed_register_route' ); // JSON API
  add_filter( 'embed_oembed_discover', '__return_false' ); // Auto Discover
  remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 ); // Results
  remove_action( 'wp_head', 'wp_oembed_add_discovery_links' ); // Discovery Links
  remove_action( 'wp_head', 'wp_oembed_add_host_js' ); // Frontend JS
  add_filter( 'tiny_mce_plugins', 'divi_child_disable_embeds_tinymce_plugin' ); // TinyMCE
  add_filter( 'rewrite_rules_array', 'divi_child_disable_embeds_rewrites' ); // Rerite Rules
  remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 ); // oEmbeds Preloader
}
add_action( 'init', 'divi_child_disable_embeds', 9999 );


/**
 * Remove oEmbed TinyMCE Plugin
 */
function divi_child_disable_embeds_tinymce_plugin( $plugins ) {
  return array_diff( $plugins, array('wpembed') );
}


/**
 * Disable oEmbeds rewrite rules
 */
function divi_child_disable_embeds_rewrites( $rules ) {
  foreach( $rules as $rule => $rewrite ) {
    if (false !== strpos($rewrite, 'embed=true')) {
      unset($rules[$rule]);
    }
  }
  return $rules;
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

function divi_child_remove_api_headers() {
  
  remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
  remove_action('wp_head', 'rest_output_link_wp_head', 10);
  remove_action('template_redirect', 'rest_output_link_header', 11, 0);
}
add_action('init', 'divi_child_remove_api_headers');

?>