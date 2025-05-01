<?php

namespace DiviChild\Core;

final class Config
{

  public $theme_name = '';
  public $theme_version = '';
  public $theme_slug = '';
  public $theme_dir = '';
  public $theme_url = '';
  protected $options;

  public function __construct()
  {
    $this->theme_name = 'Divi Child';
    $this->theme_version = DIVI_CHILD_VERSION;
    $this->theme_slug = 'divi-child';
    $this->theme_dir = get_stylesheet_directory();
    $this->theme_url = get_stylesheet_directory_uri();
  }

  /**
   * Gets all theme options
   * @return array|false
   * @since 1.0.0
   */
  public function get_options()
  {
    $options = get_option('divi_child_options');
    if ($options) {
      return $options;
    }
    return false;
  }

  /**
   * Gets all module options
   * @param string $module
   * @return array|false
   * @since 1.0.0
   */
  public function get_module_options($module)
  {
    $options = get_option('divi_child_options');
    if (isset($options[$module])) {
      return $options[$module];
    }
    return false;
  }

  /**
   * Gets a specific module option
   * @param string $module
   * @param string $id
   * @return mixed|false
   * @since 1.0.0
   */
  public function get_option($module, $id)
  {
    $options = get_option('divi_child_options');
    if (isset($options[$module][$id])) {
      return $options[$module][$id];
    }
    return false;
  }

  /**
   * Sets a specific module option
   * @param string $module
   * @param string $id
   * @param mixed $value
   * @return void
   * @since 3.0.0
   */
  public function set_option($module, $id, $value)
  {
    $options = get_option('divi_child_options');
    if (!isset($options[$module])) {
      $options[$module] = [];
    }
    $options[$module][$id] = $value;
    update_option('divi_child_options', $options);
  }

  /**
   * Deletes a specific module option
   * @param string $module
   * @param string $id
   * @return void
   * @since 3.0.0
   */
  public function delete_option($module, $id)
  {
    $options = get_option('divi_child_options');
    if (isset($options[$module][$id])) {
      unset($options[$module][$id]);
      update_option('divi_child_options', $options);
    }
  }

  /**
   * Deletes all module options
   * @param string $module
   * @return void
   * @since 3.0.0
   */
  public function delete_module_options($module)
  {
    $options = get_option('divi_child_options');
    if (isset($options[$module])) {
      unset($options[$module]);
      update_option('divi_child_options', $options);
    }
  }

  /**
   * Summary of get_defaults
   * @return array
   * @since 3.0.0
   */
  public function get_defaults()
  {
    // Hole alle Default-Optionen aus der Module-Registry
    $defaults = \DiviChild\Core\Abstracts\Module::get_all_default_options();
    error_log('Defaults: ' . print_r($defaults, true));

    return $defaults;
  }

  /**
   * Gets all registered modules with their metadata
   * @return array
   * @since 3.0.0
   */
  public function get_modules()
  {
    $modules = \DiviChild\Core\Abstracts\Module::get_all_modules();
    $module_data = [];

    foreach ($modules as $slug => $instance) {
        if (is_object($instance)) {
            $module_data[$slug] = [
                'enabled' => $this->get_option($slug, 'enabled') ?: $instance->is_enabled(),
                'name' => $instance->get_name(),
                'version' => $instance->get_version(),
                'slug' => $instance->get_slug(),
                'description' => $instance->get_description(),
                'default_options' => $instance->get_default_options(),
                'options' => $this->get_module_options($slug) ?: $instance->get_default_options(),
                'instance' => $instance
            ];
        } else {
            error_log("Invalid module instance for slug: $slug");
        }
    }

    return $module_data;
  }
}