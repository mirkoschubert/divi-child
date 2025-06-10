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


add_action('wp_head', function() {
    if (function_exists('et_get_theme_version')) {
        $divi_version = et_get_theme_version();
        error_log("🔍 Divi Version: {$divi_version}");
    }
    
    // Verfügbare Font-Filter prüfen
    $font_filters = [
        'et_builder_custom_fonts',
        'et_websafe_fonts', 
        'et_builder_google_fonts',
        'et_builder_load_fonts',
        'et_get_google_fonts'
    ];
    
    foreach ($font_filters as $filter) {
        $has_filter = has_filter($filter);
        error_log("🔍 Filter '{$filter}': " . ($has_filter ? 'EXISTS' : 'NOT FOUND'));
    }
});
