<?php

namespace DiviChild\Modules\Misc;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\FrontendServiceInterface;

class FrontendService extends ModuleService implements FrontendServiceInterface
{
  
  /**
   * Loads the frontend assets
   * @return void
   * @package Misc
   * @since 1.0.0
   */
  public function enqueue_frontend_assets() {
    if ($this->is_option_enabled('hyphens')) {
      wp_enqueue_style('divi-child-hyphens', $this->module->get_asset_url("css/misc-hyphens.min.css"));
    }
    if ($this->is_option_enabled('mobile_menu_breakpoint')) {
      wp_enqueue_style('divi-child-mobile-menu-breakpoint', $this->module->get_asset_url("css/misc-mobile-menu-breakpoint.min.css"));
    }
    if ($this->is_option_enabled('mobile_menu_fullscreen')) {
      wp_enqueue_style('divi-child-mobile-menu-fullscreen', $this->module->get_asset_url("css/misc-mobile-menu-fullscreen.min.css"));
    }
  }

}