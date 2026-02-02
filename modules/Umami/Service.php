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

        if ($this->is_option_enabled('enable_events') && !$this->is_option_empty('events')) {
          add_action('wp_footer', [$this, 'add_umami_events_script']);
        }
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

    $umami_domain = $this->options['umami_domain'];
    $umami_domain = \str_replace(['http://', 'https://'], '', $umami_domain);
    $umami_domain = \rtrim($umami_domain, '/');
    $website_id = $this->options['website_id'];

    echo "<script async defer src='" . esc_attr("https://{$umami_domain}/script.js") . "' data-website-id='" . esc_attr($website_id) . "'></script>";
  }

  /**
   * Renders the Umami event tracking script
   * @return void
   * @since 3.0.0
   */
  public function add_umami_events_script()
  {
    if ($this->is_option_enabled('ignore_logged_in') && is_user_logged_in()) return;

    $events = $this->options['events'];
    if (empty($events)) return;

    $lines = [];
    foreach ($events as $event) {
      if (empty($event['id']) || empty($event['name'])) continue;
      $id = esc_js($event['id']);
      $name = esc_js($event['name']);
      $lines[] = "    \$('#{$id}').on('click', function() { umami.track('{$name}'); });";
    }

    if (empty($lines)) return;

    echo "<script>\njQuery(function($) {\n" . implode("\n", $lines) . "\n});\n</script>";
  }
}
