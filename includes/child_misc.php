<?php
if (!defined('ABSPATH')) die();

/**
 * MISC: Stops core auto update email notifications
 * @since 1.4.0
 * @since WordPress 5.5
 */
function divi_child_stop_update_mails($send, $type, $core_update, $result) {
  if (!empty($type) && $type == 'success' ) { return false; }
  return true;
}
if (divi_child_get_theme_option('stop_mail_updates') === 'on') {
  do_action( 'qm/notice', 'MISC: Update Emails' );
  add_filter('auto_core_update_send_mail', 'divi_child_stop_update_mails', 10, 4); // core
  add_filter('auto_plugin_update_send_email', '__return_false'); // plugins
  add_filter('auto_theme_update_send_email', '__return_false'); // themes
}


/**
 * MISC: Adds SVG & WebP support for file uploads
 */
function divi_child_supported_filetypes($filetypes) {
  $new = array();
  if (divi_child_get_theme_option('svg_support') === 'on') {
    $new['svg'] = 'image/svg';
  } 
  if (divi_child_get_theme_option('webp_support') === 'on') {
    $new['webp'] = 'image/webp';
  }
  return array_merge($filetypes, $new);
}
add_action('upload_mimes', 'divi_child_supported_filetypes');


?>