<?php

namespace DiviChild\Core\Abstracts;

use DiviChild\Core\Interfaces\ServiceInterface;
use DiviChild\Core\Config;

abstract class ModuleService implements ServiceInterface
{
  /**
   * Referenz zum Modul
   * @var Module
   */
  protected $module;

  /**
   * Konfigurationsobjekt
   * @var Config
   */
  protected $config;

  /**
   * Moduloptionen
   * @var array
   */
  protected $options;

  /**
   * Konstruktor
   * @param Module $module Referenz zum Modul
   */
  public function __construct(Module $module)
  {
    $this->module = $module;
    $this->config = new Config();
    $this->options = $module->get_options();
    $this->init();
  }

  /**
   * Initialisierung
   * @return void
   */
  public function init()
  {
    // Basisinitialisierung, kann von Kindklassen überschrieben werden
  }

  /**
   * Gibt den Modul-Slug zurück
   * @return string
   */
  public function get_module_slug()
  {
    return $this->module->get_slug();
  }

  /**
   * Gibt die Modul-Optionen zurück
   * @return array
   */
  public function get_module_options()
  {
    return $this->options;
  }
  /**
   * Gibt eine bestimmte Modul-Option zurück
   * @param string $key Optionsschlüssel
   * @return mixed
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
   * Prüft, ob eine Option aktiviert ist
   * @param string $key Optionsschlüssel
   * @return bool
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