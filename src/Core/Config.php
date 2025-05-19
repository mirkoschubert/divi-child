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

    $this->options = get_option('divi_child_options');
  }

  /**
   * Gets all theme options
   * @return array|false
   * @since 1.0.0
   */
  public function get_options()
  {
    $options = get_option('divi_child_options');
    return $options ?? [];
  }

  /**
   * Gets all module options
   * @param string $module_slug
   * @return array
   * @since 3.0.0
   */
  public function get_module_options($module_slug)
  {
    //error_log("Optionen: " . print_r($this->options, true));

    if (!isset($this->options[$module_slug])) {
      error_log("Keine Optionen für {$module_slug} gefunden.");
      return [];
    }

    return $this->options[$module_slug] ?: [];
  }

  /**
   * Saves module options
   * @param string $module_slug
   * @param array $options
   * @return bool
   * @since 3.0.0
   */
  public function save_module_options($module_slug, $options)
  {
    // Debug-Ausgabe vor dem Speichern
    error_log("save_module_options({$module_slug}): Zu speichernde Optionen: " . print_r($options, true));

    if (empty($this->options)) {
      $this->options = $this->get_options();
    }

    // Vergleiche die neuen Optionen mit den vorhandenen
    $has_changes = !isset($this->options[$module_slug]) || 
                   $this->options[$module_slug] != $options;

    // Speichere nur, wenn sich etwas geändert hat
    if ($has_changes) {
      $this->options[$module_slug] = $options;
      // Speichern und Ergebnis zurückgeben
      $result = update_option('divi_child_options', $this->options);
      error_log("save_module_options({$module_slug}): Änderungen gespeichert, Ergebnis: " . ($result ? 'true' : 'false'));
      return $result;
    }
    
    // Keine Änderungen, trotzdem als erfolgreich melden
    error_log("save_module_options({$module_slug}): Keine Änderungen erkannt, nichts zu speichern");
    return true;
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
    if (empty($this->options)) {
      $this->options = $this->get_options();
    }
    return $this->options[$module][$id] ?? [];
  }

  /**
   * Sets a specific module option
   * @param string $module
   * @param string $id
   * @param mixed $value
   * @return void
   * @since 3.0.0
   */
  public function set_option($module, $key, $value): bool
  {
    if (empty($this->options)) {
      $this->options = $this->get_options();
    }

    if (!isset($this->options[$module])) {
      $this->options[$module] = [];
    }
    $this->options[$module][$key] = $value;

    return update_option('divi_child_options', $this->options);
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
    if (empty($this->options)) {
      $this->options = $this->get_options();
    }
    if (isset($this->options[$module][$id])) {
      unset($this->options[$module][$id]);
      update_option('divi_child_options', $this->options);
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
    if (empty($this->options)) {
      $this->options = $this->get_options();
    }
    if (isset($this->options[$module])) {
      unset($this->options[$module]);
      update_option('divi_child_options', $this->options);
    }
  }

  /**
   * Summary of get_defaults
   * @return array
   * @since 3.0.0
   */
  public function get_defaults()
  {
    return \DiviChild\Core\Abstracts\Module::get_all_default_options();
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