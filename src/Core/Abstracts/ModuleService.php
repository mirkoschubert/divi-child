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
   * @since 1.0.0
   */
  public function __construct(Module $module)
  {
    $this->module = $module;
    $this->config = new Config();
    $this->options = $module->get_options();
  }

  
  /**
   * Initializes common functionalities
   * @return void
   * @since 1.0.0
   */
  public function init_common()
  {
    // Basisinitialisierung für gemeinsame Funktionalitäten, kann von Kindklassen überschrieben werden
    add_action('wp_enqueue_scripts', [$this, 'enqueue_common_assets']);
  }


  /**
   * Initializes frontend functionalities
   * @return void
   * @since 1.0.0
   */
  public function init_frontend()
  {
    // Basisinitialisierung für Frontend-Funktionalitäten, kann von Kindklassen überschrieben werden
    if (is_admin()) return;
    
    add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
  }


  /**
   * Initializes admin functionalities
   * @return void
   * @since 1.0.0
   */
  public function init_admin()
  {
    // Basisinitialisierung für Admin-Funktionalitäten, kann von Kindklassen überschrieben werden
    if (!is_admin()) return;
    
    add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
  }


  /**
   * Enqueues common assets
   * @return void
   * @since 1.0.0
   */
  public function enqueue_common_assets()
  {
    // Base initialization for common assets, can be overridden by child classes
  }


  /**
   * Enqueues frontend assets
   * @return void
   * @since 1.0.0
   */
  public function enqueue_frontend_assets()
  {
    // Base initialization for frontend assets, can be overridden by child classes
  }


  /**
   * Enqueues admin assets
   * @return void
   * @since 1.0.0
   */
  public function enqueue_admin_assets()
  {
    // Base initialization for admin assets, can be overridden by child classes
  }


  /**
   * Returns the module slug
   * @return string
   * @since 1.0.0
   */
  public function get_module_slug()
  {
    return $this->module->get_slug();
  }


  /**
   * Returns the module options
   * @return array
   * @since 1.0.0
   */
  public function get_module_options()
  {
    return $this->options;
  }


  /**
   * Returns a specific module option
   * @param string $key Optionsschlüssel
   * @return mixed
   * @since 1.0.0
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
   * @since 1.0.0
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

}