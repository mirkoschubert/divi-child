<?php
if (!defined('ABSPATH')) {
  die();
}

/**
 * BUGFIX: Removes Divi Support Center from Frontend
 * @since Divi 3.20.1
 */
function divi_child_remove_support_center() {
  wp_dequeue_script('et-support-center');
  wp_deregister_script('et-support-center');
}
if (divi_child_get_theme_option('bug_fixes', 'support_center') === 'on') {
  add_action('wp_enqueue_scripts', 'divi_child_remove_support_center', 99999);
}

/**
 * BUGFIX: Fixed Body Classes for Theme Builder Header
 * @since 1.2.0
 * @since Divi 4.0
 */
function divi_child_tb_fixed_body_class($classes) {
  $has_tb_header = in_array('et-tb-has-header', $classes);
  $is_fixed_header = 'on' === et_get_option('divi_fixed_nav', 'on');

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

/**
 * BUGFIX: Add CSS for fixed navigation with theme builder
 * @since 2.1.0
 * @since Divi 4.0
 */
function divi_child_fixed_navigation() {
  ?><style id="divi-child-fixed-navigation" type="text/css">.child.et-db #et-boc .et-l--header {position: relative;z-index: 99997;top: 0;width: 100%;transition: background-color .4s, opacity .4s ease-in-out}.child.et_fixed_nav #et-boc .et-l--header {position: fixed}.child.et-db #et-boc .et-l .et_pb_fullwidth_menu .et_pb_row {width: 100%;margin: 0;padding: 0 !important}.child.admin-bar.et_fixed_nav #et-boc .et-l--header {top: 32px}@media screen and (max-width:782px) {.child.admin-bar.et_fixed_nav #et-boc .et-l--header {top: 46px}}@media screen and (max-width:600px) {.child.admin-bar.et_fixed_nav #wpadminbar {position: fixed}}.child.et-db #et-boc .et-l .et_pb_fullwidth_menu .et_pb_menu__search {padding: 0 2rem;background-color: transparent}.child.et_fixed_nav #page-container {padding-top: var(--header-height) !important}</style><?php
}

if (divi_child_get_theme_option('bug_fixes', 'fixed_navigation') === 'on') {
  add_filter('body_class', 'divi_child_tb_fixed_body_class');
  add_action('wp_head', 'divi_child_fixed_navigation', 10);
}

/**
 * BUGFIX: Add special inline scripts
 * @since 1.3.0
 */
function divi_child_page_fix() {
  ?><script id="divi-child-page-fix" type="text/javascript">document.addEventListener('DOMContentLoaded', function() {var h = document.querySelector('.et-l--header');var p = document.querySelector('#page-container');if (document.querySelector('body.et_fixed_nav') !== null) {p.style.paddingTop = h.clientHeight + 'px';}});</script><?php
}
if (divi_child_get_theme_option('bug_fixes', 'display_errors') === 'on') {
  add_action('wp_head', 'divi_child_page_fix', 1);
}

/**
 * BUGFIX: Handle for switching image and text on tablet and lower (.split-section-fix)
 * @since 2.1.0
 */
function divi_child_split_section_fix() {
  ?><style id="divi-child-split-section-fix" type="text/css">@media screen and (max-width:980px){.child .split-section-fix .et_pb_column.et_pb_column_empty{display:block;min-height:60vw}.child .split-section-fix .et_pb_row:nth-child(2n){display:flex;flex-direction:column-reverse}.child .split-section-fix .et_pb_column{padding-left:16%;padding-right:16%}.child .split-section-fix .et_pb_column .et_pb_text_align_right{text-align:left}}@media screen and (max-width:767px){.child .split-section-fix .et_pb_column{padding-left:8%;padding-right:8%}}</style><?php
}
if (divi_child_get_theme_option('bug_fixes', 'split_section') === 'on') {
  add_action('wp_head', 'divi_child_split_section_fix', 10);
}

/**
 * BUGFIX: Theme Builder logo image sizing
 * @since 2.1.0
 * @since Divi 4.6.6
 */
function divi_child_logo_image_sizing() {
  ?><style id="divi-child-logo-image-sizing" type="text/css">.child .et_pb_menu_0_tb_header .et_pb_menu_inner_container>.et_pb_menu__logo-wrap .et_pb_menu__logo img,.child .et_pb_menu_0_tb_header .et_pb_menu__logo-slot .et_pb_menu__logo-wrap img {width: auto}</style><?php
}
if (divi_child_get_theme_option('bug_fixes', 'logo_image_sizing') === 'on') {
  add_action('wp_head', 'divi_child_logo_image_sizing', 10);
}
