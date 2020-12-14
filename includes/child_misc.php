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
add_filter('auto_core_update_send_mail', 'divi_child_stop_update_mails', 10, 4);


/**
 * MISC: Adds SVG & WebP support for file uploads
 */
function divi_child_supported_filetypes($filetypes) {

  $new = array('svg' => 'image/svg+xml', 'svg' => 'image/svg', 'webp' => 'image/webp');
  return array_merge($filetypes, $new);
}
add_action('upload_mimes', 'divi_child_supported_filetypes');


?>