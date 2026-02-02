<?php

namespace DiviChild\Core\Interfaces;

interface ModuleInterface {
  public function init();
  public function activate();
  public function deactivate();
  public function uninstall();
  public function enqueue_scripts();
  public function enqueue_admin_scripts();
  public function sanitize_options($options);
  public static function get_all_modules();
  public function get_default_options();
  public static function get_all_default_options();
  public function admin_settings(): array;

}