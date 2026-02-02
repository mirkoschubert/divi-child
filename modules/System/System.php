<?php

namespace DiviChild\Modules\System;

use DiviChild\Core\Abstracts\Module;

final class System extends Module
{

  protected $enabled = false;
  protected $name = 'System';
  protected $description = 'Environment badge, search engine visibility warning and status dashboard widget.';
  protected $version = '1.0.0';
  protected $slug = 'system';
  protected $dependencies = [];
  protected $default_options = [
    'enabled' => false,
    'environment_badge' => false,
    'search_visibility_warning' => true,
    'status_panel' => false,
  ];

  /**
   * Admin settings for the module
   * @return array
   * @package System
   * @since 1.0.0
   */
  public function admin_settings(): array
  {
    return [
      'environment_badge' => [
        'type' => 'toggle',
        'label' => __('Show environment badge', 'divi-child'),
        'description' => __('Display a colored badge in the admin bar showing the current environment (Local, Dev, Staging, Live).', 'divi-child'),
        'default' => $this->default_options['environment_badge'],
      ],
      'search_visibility_warning' => [
        'type' => 'toggle',
        'label' => __('Show search engine visibility warning', 'divi-child'),
        'description' => __('Display a warning icon in the environment badge when "Discourage search engines" is enabled.', 'divi-child'),
        'default' => $this->default_options['search_visibility_warning'],
        'depends_on' => ['environment_badge' => true],
      ],
      'status_panel' => [
        'type' => 'toggle',
        'label' => __('Show status dashboard widget', 'divi-child'),
        'description' => __('Display a dashboard widget with Divi requirements and image format support info.', 'divi-child'),
        'default' => $this->default_options['status_panel'],
      ],
    ];
  }
}
