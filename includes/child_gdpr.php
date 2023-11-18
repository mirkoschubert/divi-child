<?php
if (!defined('ABSPATH')) {
  die();
}

/**
 * GDPR: Makes every comment and comment author link truely external (except 'respond')
 */
function divi_child_external_comment_links($content) {
  return str_replace("<a ", "<a target='_blank' ", $content);
}
if (divi_child_get_theme_option('gdpr_comments_external') === 'on') {
  add_filter("comment_text", "divi_child_external_comment_links");
  add_filter("get_comment_author_link", "divi_child_external_comment_links");
}

/**
 * GDPR: Removes IP addresses from comments (old entries have to be deleted by hand)
 */
function divi_child_remove_comments_ip($comment_author_ip) {
  return '';
}
if (divi_child_get_theme_option('gdpr_comments_ip') === 'on') {
  add_filter('pre_comment_user_ip', 'divi_child_remove_comments_ip');
}

/**
 * GDPR: Disable the emoji's
 */
function divi_child_disable_emojis() {
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_action('admin_print_styles', 'print_emoji_styles');
  remove_filter('the_content_feed', 'wp_staticize_emoji');
  remove_filter('comment_text_rss', 'wp_staticize_emoji');
  remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
  add_filter('tiny_mce_plugins', 'divi_child_disable_emojis_tinymce');
  add_filter('wp_resource_hints', 'divi_child_disable_emojis_remove_dns_prefetch', 10, 2);
}
if (divi_child_get_theme_option('disable_emojis') === 'on') {
  add_action('init', 'divi_child_disable_emojis');
}

function divi_child_disable_emojis_tinymce($plugins) {
  if (is_array($plugins)) {
    return array_diff($plugins, array('wpemoji'));
  } else {
    return array();
  }
}

function divi_child_disable_emojis_remove_dns_prefetch($urls, $relation_type) {
  if ('dns-prefetch' == $relation_type) {
    $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

    $urls = array_diff($urls, array($emoji_svg_url));
  }
  return $urls;
}

/**
 * GDPR: Disable oEmbeds
 */
function divi_child_disable_embeds() {
  remove_action('rest_api_init', 'wp_oembed_register_route'); // JSON API
  add_filter('embed_oembed_discover', '__return_false'); // Auto Discover
  remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10); // Results
  remove_action('wp_head', 'wp_oembed_add_discovery_links'); // Discovery Links
  remove_action('wp_head', 'wp_oembed_add_host_js'); // Frontend JS
  add_filter('tiny_mce_plugins', 'divi_child_disable_embeds_tinymce_plugin'); // TinyMCE
  add_filter('rewrite_rules_array', 'divi_child_disable_embeds_rewrites'); // Rerite Rules
  remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10); // oEmbeds Preloader
}
if (divi_child_get_theme_option('disable_oembeds') === 'on') {
  add_action('init', 'divi_child_disable_embeds', 9999);
}

// TinyMCE Plugin
function divi_child_disable_embeds_tinymce_plugin($plugins) {
  return array_diff($plugins, array('wpembed'));
}

// rewrite rules
function divi_child_disable_embeds_rewrites($rules) {
  foreach ($rules as $rule => $rewrite) {
    if (false !== strpos($rewrite, 'embed=true')) {
      unset($rules[$rule]);
    }
  }
  return $rules;
}

/**
 * GDPR: Remove DNS Prefetching for Wordpress
 */
function divi_child_remove_dns_prefetch() {
  remove_action('wp_head', 'wp_resource_hints', 2);
}
if (divi_child_get_theme_option('dns_prefetching') === 'on') {
  add_action('init', 'divi_child_remove_dns_prefetch');
}

/**
 * GDPR: Remove REST API & XMLRPC info from head and headers (for security reasons)
 */
function divi_child_remove_api_headers() {

  remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
  add_filter('xmlrpc_enabled', '__return_false'); // restrict xmlrpc
  remove_action('wp_head', 'rsd_link'); // remove rsd link
  remove_action('wp_head', 'rest_output_link_wp_head', 10);
  remove_action('template_redirect', 'rest_output_link_header', 11, 0);
  remove_action('wp_head', 'wp_generator'); // remove generator tag
  remove_action('wp_head', 'wlwmanifest_link'); // remove windows live writer manifest
}
if (divi_child_get_theme_option('rest_api') === 'on') {
  add_action('init', 'divi_child_remove_api_headers');
}

?>