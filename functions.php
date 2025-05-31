<?php
if (!defined('ABSPATH')) {
  die();
}

define('DIVI_CHILD_VERSION', '3.0.0');

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use DiviChild\Core\Theme;

$plugin = new Theme();
$plugin->init();

/** -------- Add your own code after this! -------- **/

