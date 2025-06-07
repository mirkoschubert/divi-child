<?php
if (!defined('ABSPATH')) {
  die();
}

define('DIVI_CHILD_VERSION', '3.0.0');
define('GOOGLE_FONTS_API_KEY', 'AIzaSyAI9txf9yWhhjxeMOCR94PVaVgM-1sYAjU');


require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use DiviChild\Core\Theme;

$plugin = new Theme();
$plugin->init();

/** -------- Add your own code after this! -------- **/


// TEMPORÄRER DEBUG - Google Fonts Status prüfen
add_action('wp_head', function() {
    if (function_exists('et_builder_google_fonts_is_enabled')) {
        $google_fonts_enabled = et_builder_google_fonts_is_enabled();
        error_log('GOOGLE FONTS ENABLED: ' . ($google_fonts_enabled ? 'YES' : 'NO'));
    }
    
    if (function_exists('et_get_option')) {
        $divi_use_google_fonts = et_get_option('divi_use_google_fonts', 'on');
        error_log('DIVI USE GOOGLE FONTS OPTION: ' . $divi_use_google_fonts);
    }
}, 1);

// ALTERNATIVE: Websafe Fonts Test
add_filter('et_websafe_fonts', function($fonts) {
    error_log("🎯 DIRECT WEBSAFE Hook called!");
    
    $fonts['Roboto'] = [
        'styles' => '100,200,300,400,500,600,700,800,900,100italic,200italic,300italic,400italic,500italic,600italic,700italic,800italic,900italic',
        'character_set' => 'latin-ext',
        'type' => 'sans-serif',
        'standard' => 1
    ];
    
    return $fonts;
}, 999, 1);

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
