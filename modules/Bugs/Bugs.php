<?php

namespace DiviChild\Modules\Bugs;

use DiviChild\Core\Abstracts\Module;

final class Bugs extends Module
{

  protected $enabled = true;
  protected $name = 'Bug Fixes';
  protected $description = 'Fixes some bugs in Divi';
  protected $version = '1.1.0';
  protected $slug = 'bugs';
  protected $dependencies = [
    'jquery',
  ];
  protected $default_options = [
    'enabled' => true,
    'support_center' => false,
    'fixed_navigation' => true,
    'header_height' => 80,
    'display_errors' => false,
    'logo_image_sizing' => false,
    'split_section' => false,
  ];

  /**
   * Admin settings for the module
   * @return array
   * @package Bugs
   * @since 1.0.0
   */
  public function admin_settings() {
    return [
      'support_center' => [
        'type' => 'toggle',
        'label' => __('Remove Divi Support Center from Frontend', 'divi-child'),
        'description' => __('Available only on Divi 3.20.1', 'divi-child'),
        'default' => $this->default_options['support_center'],
        'dependencies' => [
          'divi' => '= 3.20.1',
        ]
      ],
      'fixed_navigation' => [
        'type' => 'toggle',
        'label' => __('Enable fixed navigation bar option in Theme Builder', 'divi-child'),
        'description' => __('Available on Divi 4.0 and up', 'divi-child'),
        'default' => $this->default_options['fixed_navigation'],
        'dependencies' => [
          'divi' => '>= 4.0',
        ]
      ],
      'header_height' => [
        'type' => 'number',
        'label' => __('Header Height (px)', 'divi-child'),
        'description' => __('Set the height of the fixed header in pixels', 'divi-child'),
        'default' => $this->default_options['header_height'],
        'min' => 50,
        'max' => 200,
        'step' => 1,
        'depends_on' => [
          'fixed_navigation' => true
        ]
      ],
      'display_errors' => [
        'type' => 'toggle',
        'label' => __('Fix display errors in Theme Builder', 'divi-child'),
        'description' => __('Available on Divi 4.0 up to 4.12', 'divi-child'),
        'default' => $this->default_options['display_errors'],
        'dependencies' => [
          'divi' => '4.0 - 4.12',
        ]
      ],
      'logo_image_sizing' => [
        'type' => 'toggle',
        'label' => __('Fix logo image sizing in Theme Builder', 'divi-child'),
        'description' => __('Available only on Divi 4.6.6', 'divi-child'),
        'default' => $this->default_options['logo_image_sizing'],
        'dependencies' => [
          'divi' => '= 4.6.6',
        ]
      ],
      'split_section' => [
        'type' => 'toggle',
        'label' => __('Set CSS class .split-section-fix for swapping image and text on tablet and phone', 'divi-child'),
        'description' => __('Deprecated - will be removed on version 3.2', 'divi-child'),
        'default' => $this->default_options['split_section'],
      ],
    ];
  }
}