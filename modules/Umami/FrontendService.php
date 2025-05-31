<?php

namespace DiviChild\Modules\Umami;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\FrontendServiceInterface;

class FrontendService extends ModuleService implements FrontendServiceInterface
{

  /**
   * Initialisiert Frontend-spezifische Funktionalität
   * @return void
   */
  public function init_frontend()
  {
    parent::init_frontend();

    // 1. Umami active
    if (!$this->is_option_empty('umami_domain') && !$this->is_option_empty('website_id')) {
      add_action('wp_footer', [$this, 'add_umami_script']);
    }
  }

  /**
   * Loads the frontend assets
   * @return void
   * @package Misc
   * @since 1.0.0
   */
  public function enqueue_frontend_assets()
  {
    /* if ($this->is_option_enabled('enable_events')) {
      wp_enqueue_script('divi-child-umami-events', $this->module->get_asset_url("js/umami-events.min.js"));
    } */

  }

  public function add_umami_script()
  {
    if ($this->is_option_enabled('ignore_logged_in') && is_user_logged_in()) return; // Skip for logged-in users if option is enabled

    $umami_domain = esc_url($this->options['umami_domain']);
    $umami_domain = str_replace(['http://', 'https://'], '', $umami_domain);
    $website_id = esc_js($this->options['website_id']);

    echo "<script async defer src='https://{$umami_domain}/script.js' data-website-id='{$website_id}'></script>";
  }

}