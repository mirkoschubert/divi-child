<?php
if (!defined('ABSPATH')) {
  die();
}

/**
 * A11Y: Fix viewport meta
 * @since 2.1.0
 */
function divi_child_remove_divi_viewport_meta() {
  remove_action('wp_head', 'et_add_viewport_meta');
}

function divi_child_fix_viewport_meta() {
  echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=1" />';
}
if (divi_child_get_theme_option('a11y', 'fix_viewport') === 'on') {
  add_action('init', 'divi_child_remove_divi_viewport_meta');
  add_action('wp_head', 'divi_child_fix_viewport_meta', 1);
}


/**
 * A11Y: Add a skip link
 * @since 2.3.0
 */
function divi_child_skip_link() {
  echo '<a href="#main-content" target="_self" class="skip-link" role="link">' . __('Skip to content', 'divi-child') . '</a>';
}

function divi_child_skip_link_css() {
  ?><style id="divi-child-skip-link" type="text/css">.child .skip-link{position:absolute;top:-100px;left:0;background:#fff;color:#000;padding:8px;z-index:99999;}.child .skip-link:focus{top:0;}</style><?php
}

if (divi_child_get_theme_option('a11y', 'skip_link') === 'on') {
  add_action('wp_body_open', 'divi_child_skip_link');
  add_action('wp_head', 'divi_child_skip_link_css', 10);
}


/**
 * A11Y: Add accessable Scroll to Top button
 * @since 2.3.0
 */
function divi_child_scroll_top() {
  ?><button class="top-link hide" id="js-top"><svg role="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 6"><path d="M12 6H0l6-6z"/></svg><span class="screen-reader-text">Back to top</span></button><?php
}


function divi_child_scroll_top_css() {
  ?><style id="divi-child-scroll-top">.child .top-link{ border: 1px solid #fff; border-radius: 5px; transition: all .25s ease-in-out; position: fixed; bottom: 0; right: 0; display: inline-flex; cursor: pointer; align-items: center; justify-content: center; margin: 0 3em 3em 0; padding: .25em; width: 40px; height: 40px; background-color: #333; z-index: 100}.child .show{ visibility: visible; opacity: .8;}.child .hide{ visibility: hidden; opacity: 0}.child .top-link svg{ fill: #fff; width: 1rem; height: .5rem}</style><?php
}
if (divi_child_get_theme_option('a11y', 'scroll_top') === 'on') {
  add_action('wp_footer', 'divi_child_scroll_top', 10);
  add_action('wp_head', 'divi_child_scroll_top_css', 10);
}


/**
 * A11Y: Focus all elements correctly
 * @since 2.3.0
 */
function divi_child_focus_elements() {
  ?><style id="divi-child-focus-elements" type="text/css">.child a:focus,.child .et_clickable:focus{outline:2px solid black;box-shadow:0 0 0 3px white;}.child .keyboard-outline{outline:2px solid black!important;box-shadow:0 0 0 3px white!important;-webkit-transition:none!important;transition:none!important;}.child .et_pb_menu__logo .keyboard-outline img{outline:2px solid black!important;outline-offset:-2px;}.child button:active.keyboard-outline,.child button:focus.keyboard-outline,.child input:active.keyboard-outline,.child input:focus.keyboard-outline,.child a[role="tab"].keyboard-outline{outline-offset:-5px;}.child .et-search-form input:focus.keyboard-outline{padding-left:15px;padding-right:15px;}.child .et_pb_tab{-webkit-animation:none!important;animation:none!important;}.child .et_pb_scroll_top.et-visible:focus{outline-width:2px;outline-style:solid;outline-color:Highlight;}@media (-webkit-min-device-pixel-ratio:0){.child .et_pb_scroll_top.et-visible:focus{outline-color:-webkit-focus-ring-color;outline-style:auto}}</style><?php
}
if (divi_child_get_theme_option('a11y', 'focus_elements') === 'on') {
  add_action('wp_head', 'divi_child_focus_elements', 10);
}


/**
 * A11Y: Fix keyboard navigation
 * @since 2.3.0
 */
function divi_child_nav_keyboard() {
  ?><style id="divi-child-nav-keyboard">.nav li.et-hover>ul,.menu li.et-hover>ul{visibility:visible!important;opacity:1!important;}.a11y-submenu-show{visibility:visible!important;opacity:1!important;}</style><?php
}
if (divi_child_get_theme_option('a11y', 'nav_keyboard') === 'on') {
  add_action( 'wp_head', 'divi_child_nav_keyboard');
}


/**
 * A11Y: Fix screenreader text
 * @since 2.3.0
 */
function divi_child_fix_screenreader() {
  ?><style id="divi-child-fix-screenreader">.child .et_pb_contact_form_label,.child .widget_search .screen-reader-text,.child .et_pb_social_media_follow_network_name,.child .et_pb_search .screen-reader-text{display:block!important;}.child .a11y-screen-reader-text,.child .et_pb_contact_form_label,.child .widget_search .screen-reader-text,.child .et_pb_social_media_follow_network_name,.child .et_pb_search .screen-reader-text{clip:rect(1px,1px,1px,1px);position:absolute!important;height:1px;width:1px;overflow:hidden;text-shadow:none;text-transform:none;letter-spacing:normal;line-height:normal;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;font-size:1em;font-weight:600;-webkit-font-smoothing:subpixel-antialiased;}.child .a11y-screen-reader-text:focus{background:#f1f1f1;color:#00547A;-webkit-box-shadow:0 0 2px 2px rgba(0,0,0,.6);box-shadow:0 0 2px 2px rgba(0,0,0,.6);clip:auto!important;display:block;height:auto;left:5px;padding:15px 23px 14px;text-decoration:none;top:7px;width:auto;z-index:1000000;}</style><?php
}
if (divi_child_get_theme_option('a11y', 'fix_screenreader') === 'on') {
  add_action( 'wp_head', 'divi_child_fix_screenreader');
}


/**
 * A11Y: Unterline all links except headlines and socials
 * @since 2.3.0
 */
function divi_child_underline_links() {
  ?><style id="divi-child-nav-keyboard">.child #et-main-area a:not(.et-social-icons a){text-decoration:underline;}.child #et-main-area .entry-title a,.child .wp-block-button a,.child .et_pb_button,.child .et_pb_module_header a,.child .et_pb_video_play{text-decoration:none!important;}</style><?php
}
if (divi_child_get_theme_option('a11y', 'underline_links') === 'on') {
  add_action( 'wp_head', 'divi_child_underline_links');
}