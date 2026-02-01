<?php

namespace DiviChild\Core\Abstracts;

use DiviChild\Core\Interfaces\ServiceInterface;
use DiviChild\Core\Config;

abstract class ModuleService implements ServiceInterface
{
  /**
   * Reference to the module
   * @var Module
   */
  protected $module;

  /**
   * Configuration object
   * @var Config
   */
  protected $config;

  /**
   * Module options
   * @var array
   */
  protected $options;


  /**
   * Constructor
   * @param Module $module
   * @since 3.0.0
   */
  public function __construct(Module $module)
  {
    $this->module = $module;
    $this->config = new Config();
    $this->options = $module->get_options();
  }


  /**
   * Initializes all module services (to be overridden by child classes)
   * @return void
   * @since 3.0.0
   */
  public function init_service()
  {
    // Override in child classes
  }


  /**
   * Enqueues assets (to be overridden by child classes)
   * @return void
   * @since 3.0.0
   */
  public function enqueue_assets()
  {
    // Override in child classes
  }


  /**
   * Returns the module slug
   * @return string
   * @since 3.0.0
   */
  public function get_module_slug()
  {
    return $this->module->get_slug();
  }


  /**
   * Returns the module options
   * @return array
   * @since 3.0.0
   */
  public function get_module_options()
  {
    return $this->options;
  }


  /**
   * Returns a specific module option
   * @param string $key Optionsschlüssel
   * @return mixed
   * @since 3.0.0
   */
  public function get_module_option($key)
  {
    if (isset($this->options[$key])) {
      return $this->options[$key];
    }

    // Fallback auf Default-Werte
    $defaults = $this->module->get_default_options();
    return $defaults[$key] ?? null;
  }


  /**
   * Checks if a specific option is enabled
   * @param string $key
   * @return bool
   * @since 3.0.0
   */
  protected function is_option_enabled($key)
  {
    if (isset($this->options[$key])) {
      return (bool) $this->options[$key];
    }

    // Fallback auf Default-Werte
    $defaults = $this->module->get_default_options();
    return isset($defaults[$key]) ? (bool) $defaults[$key] : false;
  }

  /**
   * Checks if a specific option is empty
   * @param mixed $key
   * @return bool
   * @since 3.0.0
   */
  protected function is_option_empty($key)
  {
    return !isset($this->options[$key]) || empty($this->options[$key]) || $this->options[$key] === '';
  }

}
