<?php

namespace DiviChild\Modules\Misc;

use DiviChild\Core\Abstracts\Module;

final class Misc extends Module
{

  protected $enabled = true;
  protected $name = 'Miscellaneous';
  protected $description = 'Miscellaneous fixes and enhancements for Divi';
  protected $version = '1.0.0';
  protected $slug = 'misc';
  protected $dependencies = [
    'jquery',
  ];
  protected $default_options = [
    'enabled' => true,
    'disable_projects' => false,
    'stop_mail_updates' => false,
    'svg_support' => false,
    'webp_support' => false,
    'hyphens' => false,
    'mobile_menu_breakpoint' => false,
    'mobile_menu_fullscreen' => false
  ];

  /**
   * Admin settings for the module
   * @return array
   * @package Misc
   * @since 1.0.0
   */
  public function admin_settings() {
    return [
      'disable_projects' => [
        'type' => 'toggle',
        'label' => __('Disable custom post type Projects.', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['disable_projects'],
      ],
      'stop_mail_updates' => [
        'type' => 'toggle',
        'label' => __('Disable email notification when plugins or theme were automatically updated.', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['stop_mail_updates'],
      ],
      'svg_support' => [
        'type' => 'toggle',
        'label' => __('Enable to upload SVG files', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['svg_support'],
      ],
      'webp_support' => [
        'type' => 'toggle',
        'label' => __('Enable to upload WebP files', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['webp_support'],
      ],
      'hyphens' => [
        'type' => 'toggle',
        'label' => __('Enable hyphenation for the whole website', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['hyphens'],
      ],
      'mobile_menu_breakpoint' => [
        'type' => 'toggle',
        'label' => __('Set breakpoint for the mobile menu to 1280px', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['mobile_menu_breakpoint'],
      ],
      'mobile_menu_fullscreen' => [
        'type' => 'toggle',
        'label' => __('Enable fullscreen mode for the mobile menu', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['mobile_menu_fullscreen'],
      ],
    ];
  }
}