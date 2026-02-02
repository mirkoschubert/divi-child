<?php

namespace DiviChild\Modules\Umami;

use DiviChild\Core\Abstracts\ModuleService;

class Service extends ModuleService
{
  /**
   * Initializes all module services
   * @return void
   * @since 3.0.0
   */
  public function init_service()
  {
    // === Frontend Only ===
    if (!is_admin()) {
      if (!$this->is_option_empty('umami_domain') && !$this->is_option_empty('website_id')) {
        add_action('wp_footer', [$this, 'add_umami_script']);
      }
    }
  }

  /**
   * Renders the Umami tracking script
   * @return void
   * @since 1.0.0
   */
  public function add_umami_script()
  {
    if ($this->is_option_enabled('ignore_logged_in') && is_user_logged_in()) return;

    $umami_domain = esc_url($this->options['umami_domain']);
    $umami_domain = \str_replace(['http://', 'https://'], '', $umami_domain);
    $website_id = esc_js($this->options['website_id']);

    echo "<script async defer src='https://{$umami_domain}/script.js' data-website-id='{$website_id}'></script>";
  }
}
