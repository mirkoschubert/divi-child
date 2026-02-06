<?php

namespace DiviChild\Core;

use DiviChild\API\RestController;
use DiviChild\Core\Config;
use DiviChild\Core\Migration;
use DiviChild\Admin\Admin;

final class Theme
{
  protected $admin;
  protected $migration;
  protected $config;
  protected $rest_controller;
  protected $options = [];

  /**
   * Initialize the Theme
   * @return void
   * @since 1.0.0
   */
  public function init()
  {
    $this->config = Config::get_instance();
    $this->options = $this->config->get_options();

    $this->migration = new Migration();
    $this->migration->run();

    add_action('after_switch_theme', [$this, 'activate'], 10, 2);
    add_action('switch_theme', [$this, 'deactivate'], 10, 3);
    add_action('delete_theme', [$this, 'uninstall'], 10, 1);

    add_action('rest_api_init', [$this, 'init_rest_api'], 10);

    $this->load_modules();

    if (is_admin()) {
      $this->admin = new Admin();
    }

    add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    add_action('after_setup_theme', [$this, 'setup_languages']);

    $this->add_child_body_class();
  }

  public function init_rest_api()
  {
    $this->rest_controller = new RestController();
    $this->rest_controller->register_routes();
  }

  public function activate($old_name = '', $old_theme = null)
  {

    $current_theme = wp_get_theme();
    if ($current_theme->get('Name') !== $this->config->theme_name)
      return; // Only run if this is the correct theme

    $this->migration->run();

    update_option('divi_child_version', $this->config->theme_version);
    flush_rewrite_rules();
  }

  public function deactivate($new_name = '', $new_theme = null, $old_theme = null)
  {
    if ($old_theme && $old_theme->get('Name') !== $this->config->theme_name)
      return;

    // Do any necessary cleanup here    
    flush_rewrite_rules();
  }

  public function uninstall($stylesheet)
  {
    $theme = wp_get_theme($stylesheet);
    if ($theme->get('Name') !== $this->config->theme_name)
      return; // Only uninstall if this is the correct theme

    // Remove options on uninstall
    delete_option('divi_child_options');
    delete_option('divi_child_version');
  }

  /**
   * Enqueue scripts and styles
   * @return void
   * @since 1.0.0
   */
  public function enqueue_scripts()
  {
    wp_enqueue_style('divi-child-style', "{$this->config->theme_url}/style.css");

    wp_enqueue_script('wp-i18n');
    wp_enqueue_script('divi-child-script', "{$this->config->theme_url}/assets/js/main.js", ['jquery', 'wp-i18n'], null, true);
  }

  /**
   * Summary of setup_languages
   * @return void
   * @since 1.0.0
   */
  public function setup_languages()
  {
    static $done = false;
    if ($done)
      return;
    $done = true;

    $user_locale = get_user_locale(get_current_user_id() ?: 0);
    $path = $this->config->theme_dir . '/languages';

    global $l10n;
    unset($l10n['divi-child']);

    $old_locale = switch_to_locale($user_locale);
    load_child_theme_textdomain('divi-child', $path);
    switch_to_locale($old_locale);

    // Divi-Domains nur bei Bedarf (sonst unnötig)
    load_child_theme_textdomain('Divi', $this->config->theme_dir . '/languages/theme');
    load_child_theme_textdomain('et-core', $this->config->theme_dir . '/languages/core');
    load_child_theme_textdomain('et_builder', $this->config->theme_dir . '/languages/builder');
  }



  /**
   * Add a 'child' class to the body tag
   * @return void
   * @since 1.0.0
   */
  public function add_child_body_class()
  {
    if (is_admin()) {
      add_filter('admin_body_class', function ($classes) {
        $classes .= ' child';
        return $classes;
      });
    } else {
      add_filter('body_class', function ($classes) {
        $classes[] = 'child';
        return $classes;
      });
    }
  }

  /**
   * Load all modules from the modules directory
   * @return void
   */
  private function load_modules()
  {
    $modules_dir = $this->config->theme_dir . '/modules';

    if (!is_dir($modules_dir)) {
      error_log("DiviChild: Modules directory not found: {$modules_dir}");
      return;
    }

    // Get all subdirectories in the modules folder
    $module_folders = glob("{$modules_dir}/*", GLOB_ONLYDIR);

    foreach ($module_folders as $module_folder) {
      $module_name = \basename($module_folder);

      // Skip directories starting with underscore or dot
      if (\substr($module_name, 0, 1) === '_' || \substr($module_name, 0, 1) === '.') {
        continue;
      }

      // Get main module class file (should be named same as directory)
      $module_file = "{$module_folder}/{$module_name}.php";

      if (\file_exists($module_file)) {
        $class_name = "\\DiviChild\\Modules\\{$module_name}\\{$module_name}";

        // Initialize module if class exists
        if (\class_exists($class_name)) {
          try {
            $instance = new $class_name();
          } catch (\Exception $e) {
            error_log("DiviChild: Failed to load module {$module_name}: " . $e->getMessage());
          }
        } else {
          error_log("DiviChild: Module class not found: {$class_name}");
        }
      } else {
        error_log("DiviChild: Module file not found: {$module_file}");
      }
    }
  }

}