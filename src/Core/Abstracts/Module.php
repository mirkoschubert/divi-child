<?php

namespace DiviChild\Core\Abstracts;

use DiviChild\Core\Interfaces\ModuleInterface;
use DiviChild\Core\Config;

abstract class Module implements ModuleInterface
{

  protected $enabled = true;
  protected $name = '';
  protected $description = '';
  protected $version = '';
  protected $slug = '';
  protected $dependencies = [
    'jquery',
  ];
  protected $config;
  protected $options;
  protected $default_options = [
    'enabled' => true,
  ];
  private static $modules = [];

  public function __construct()
  {
    $this->init();

    if (!empty($this->slug)) {
      self::$modules[$this->slug] = $this;
    }
  }

  public function init()
  {
    if (empty($this->name)) {
      $this->name = get_class($this);
      $this->name = str_replace('DiviChild\\Modules\\', '', $this->name);
    }
    $this->version = empty($this->version) ? '1.0.0' : $this->version;
    $this->slug = empty($this->slug) ? strtolower($this->name) : $this->slug;

    $this->config = new Config();
    $this->options = $this->config->get_module_options($this->slug);
    $this->options = empty($this->options) ? $this->default_options : $this->options;

    if (is_admin()) {
      add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    } else {
      add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
  }

  public function activate()
  {
    $this->config->set_option($this->slug, 'enabled', true);
  }

  public function deactivate()
  {
    $this->config->set_option($this->slug, 'enabled', false);
  }
  
  public function uninstall()
  {
    $this->config->delete_module_options($this->slug);
  }

  public function enqueue_scripts()
  {
    // Enqueue module scripts
    wp_enqueue_style("divi-child-{$this->slug}-style","{$this->config->theme_url}/modules/{$this->slug}/assets/css/{$this->slug}.css");
    wp_enqueue_script("divi-child-{$this->slug}-script", "{$this->config->theme_url}/modules/{$this->slug}/assets/js/{$this->slug}.js", ['jquery'], null, true);
    wp_localize_script("divi-child-{$this->slug}-script", 'dvc_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
  }

  public function enqueue_admin_scripts()
  {
    // Enqueue module admin scripts
    wp_enqueue_style("divi-child-{$this->slug}-admin-style", "{$this->config->theme_url}/modules/{$this->slug}/assets/css/{$this->slug}-admin.css");
    wp_enqueue_script("divi-child-{$this->slug}-admin-script", "{$this->config->theme_url}/modules/{$this->slug}/assets/js/{$this->slug}-admin.js", ['jquery'], null, true);
    wp_localize_script("divi-child-{$this->slug}-admin-script", 'dvc_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
  }

  public function sanitize_options($options)
  {
    // Sanitize module options
    $sanitized_options = [];
    foreach ($this->default_options as $key => $value) {
      $sanitized_options[$key]= isset($options[$key]) ? sanitize_text_field($options[$key]) : $value;
    }
    return $sanitized_options;
  }

  public static function get_all_modules()
  {
    error_log('Module registry contents: ' . print_r(self::$modules, true));
    return self::$modules;
  }

  /**
   * Returns the default options of the module
   * @return array
   */
  public function get_default_options()
  {
    return $this->default_options;
  }

  /**
   * Returns all default options of all modules
   * @return array
   */
  public static function get_all_default_options()
  {
    $defaults = [];
    foreach (self::$modules as $slug => $instance) {
      $module_defaults = $instance->get_default_options();
      if (!empty($module_defaults)) {
        $defaults[$slug] = $module_defaults;
      }
    }
    return $defaults;
  }

  /**
   * Gets the module name
   * @return string
   */
  public function get_name()
  {
    return $this->name;
  }

  /**
   * Gets the module version
   * @return string
   */
  public function get_version()
  {
    return $this->version;
  }

  /**
   * Gets the module slug
   * @return string
   */
  public function get_slug()
  {
    return $this->slug;
  }

  /**
   * Gets the module description
   * @return string
   */
  public function get_description()
  {
    return $this->description;
  }

  /**
   * Checks if the module is enabled
   * @return bool
   */
  public function is_enabled()
  {
    return $this->enabled;
  }

}