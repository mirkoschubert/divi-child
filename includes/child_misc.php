<?php
if (!defined('ABSPATH')) {
  die();
}

/**
 * MISC: Disable Projects Custom Post Type
 * @since 2.1.0
 */
function divi_child_unregister_projects() {
  unregister_taxonomy('project_category');
  unregister_taxonomy('project_tag');
  unregister_post_type('project');
}
if (divi_child_get_theme_option('misc', 'disable_projects') === 'on') {
  add_action('init', 'divi_child_unregister_projects', 100);
}

/**
 * MISC: Stops core auto update email notifications
 * @since 2.0.0
 * @since WordPress 5.5
 */
function divi_child_stop_update_mails($send, $type, $core_update, $result) {
  if (!empty($type) && $type == 'success') {
    return false;
  }
  return true;
}
if (divi_child_get_theme_option('misc', 'stop_mail_updates') === 'on') {
  add_filter('auto_core_update_send_email', 'divi_child_stop_update_mails', 10, 4); // core
  add_filter('auto_plugin_update_send_email', '__return_false'); // plugins
  add_filter('auto_theme_update_send_email', '__return_false'); // themes
}

/**
 * MISC: Adds SVG & WebP support for file uploads
 * @since 2.0.0
 */
function divi_child_supported_mimes($mimes = array()) {
  if (divi_child_get_theme_option('misc', 'svg_support') === 'on') {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
  }
  if (divi_child_get_theme_option('misc', 'webp_support') === 'on') {
    $mimes['webp'] = 'image/webp';
  }
  return $mimes;
}
add_action('upload_mimes', 'divi_child_supported_mimes', 99);

/**
 * MISC: Check Mime Types
 * @since 2.2.0
 */
function divi_child_svgs_upload_check($checked, $file, $filename, $mimes) {
  if (!$checked['type']) {
    $check_filetype = wp_check_filetype($filename, $mimes);
    $ext = $check_filetype['ext'];
    $type = $check_filetype['type'];
    $proper_filename = $filename;

    if ($type && 0 === strpos($type, 'image/') && $ext !== 'svg') {
      $ext = $type = false;
    }
    $checked = compact('ext', 'type', 'proper_filename');
  }
  return $checked;
}
if (divi_child_get_theme_option('misc', 'svg_support') === 'on') {
  add_filter('wp_check_filetype_and_ext', 'divi_child_svgs_upload_check', 10, 4);
}

/**
 * MISC: Mime Check fix for WP 4.7.1 / 4.7.2
 *
 * Fixes uploads for these 2 version of WordPress.
 * Issue was fixed in 4.7.3 core.
 * @since 2.2.0
 */
function divi_child_svgs_allow_svg_upload($data, $file, $filename, $mimes) {
  global $wp_version;
  if ($wp_version !== '4.7.1' || $wp_version !== '4.7.2') {
    return $data;
  }

  $filetype = wp_check_filetype($filename, $mimes);
  return [
    'ext' => $filetype['ext'],
    'type' => $filetype['type'],
    'proper_filename' => $data['proper_filename'],
  ];
}
if (divi_child_get_theme_option('misc', 'svg_support') === 'on') {
  add_filter('wp_check_filetype_and_ext', 'divi_child_svgs_allow_svg_upload', 10, 4);
}

/**
 * MISC: Add hyphenation to the whole website
 * @since 2.1.0
 */
function divi_child_hyphens() {
  ?><style id="divi-child-hyphens" type="text/css">*,html{word-break: break-word;hyphens: auto;-ms-hyphens: auto;-webkit-hyphens: auto}</style><?php
}
if (divi_child_get_theme_option('misc', 'hyphens') === 'on') {
  add_action('wp_head', 'divi_child_hyphens', 10);
}

/**
 * MISC: Set a higher breakpoint for the mobile menu
 * @since 2.1.0
 */
function divi_child_mobile_menu_breakpoint() {
  ?><style id="divi-child-mobile-menu-breakpoint" type="text/css">@media screen and (max-width:1280px){.child .et_pb_fullwidth_menu .et_pb_menu__menu,.child .et_pb_menu .et_pb_menu__menu{display:none}.child .et_pb_fullwidth_menu .et_mobile_nav_menu,.child .et_pb_menu .et_mobile_nav_menu{float:none;margin:0 6px;display:flex;-webkit-box-align:center;align-items:center}.child .et_pb_fullwidth_menu .et_mobile_menu,.child .et_pb_fullwidth_menu .et_mobile_menu ul,.child .et_pb_menu .et_mobile_menu,.child .et_pb_menu .et_mobile_menu ul{list-style:none!important;text-align:left}.et_pb_fullwidth_menu .et_mobile_menu,.et_pb_menu .et_mobile_menu{top:100%;padding:5%}}</style><?php
}
if (divi_child_get_theme_option('misc', 'mobile_menu_breakpoint') === 'on') {
  add_action('wp_head', 'divi_child_mobile_menu_breakpoint', 10);
}

/**
 * MISC: Enable a fullscreen mobile menu
 */
function divi_child_mobile_menu_fullscreen() {
  ?><style id="divi-child-mobile-menu-fullscreen" type="text/css">.child .et_pb_fullwidth_menu .et_mobile_menu,.child .et_pb_menu .et_mobile_menu{position:fixed;top:0;left:0;width:100%;height:calc(100vh);display:flex;flex-direction:column;align-items:center;justify-content:flex-start;overflow:scroll;transition:all .4s ease}.child.admin-bar .et_pb_fullwidth_menu .et_mobile_menu,.child.admin-bar .et_pb_menu .et_mobile_menu{top:32px}@media screen and (max-width:782px){.child.admin-bar .et_pb_fullwidth_menu .et_mobile_menu,.child.admin-bar .et_pb_menu .et_mobile_menu{top:46px}}.child .et_pb_fullwidth_menu .closed .et_mobile_menu,.child .et_pb_menu .closed .et_mobile_menu{display:none;transition:all .4s ease;background-color:transparent}.child .et_pb_fullwidth_menu .et_mobile_menu li,.child .et_pb_menu .et_mobile_menu li{width:100%;max-width:480px;text-align:center;padding-left:0}.child .et_pb_fullwidth_menu .et_mobile_menu li a,.child .et_pb_menu .et_mobile_menu li a{font-size:18px;font-weight:600;border:none;padding:.7rem 5%}.child .et_pb_fullwidth_menu .et_mobile_menu li li a,.child .et_pb_menu .et_mobile_menu li li a{font-size:16px;font-weight:300;padding:.5rem 5%}.child .et_pb_fullwidth_menu .et_mobile_menu li.current-menu-item li:not(.current-menu-item) a,.child .et_pb_menu .et_mobile_menu li.current-menu-item li:not(.current-menu-item) a{color:inherit!important}.child .et_pb_fullwidth_menu .et_mobile_menu li ul,.child .et_pb_menu .et_mobile_menu li ul{background-color:transparent!important;padding-left:0}.child .et_pb_fullwidth_menu .et_mobile_menu .menu-item-has-children>a,.child .et_pb_menu .et_mobile_menu .menu-item-has-children>a{background-color:transparent}.child .mobile_nav.opened .mobile_menu_bar{z-index:11111}.child .mobile_nav.opened .mobile_menu_bar{box-shadow:none!important}.child .mobile_nav.opened .mobile_menu_bar::before{content:'M'}</style><?php
}
if (divi_child_get_theme_option('misc', 'mobile_menu_fullscreen') === 'on') {
  add_action('wp_head', 'divi_child_mobile_menu_fullscreen', 10);
}
