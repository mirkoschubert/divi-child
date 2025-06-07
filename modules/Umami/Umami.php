<?php

namespace DiviChild\Modules\Umami;

use DiviChild\Core\Abstracts\Module;

final class Umami extends Module
{

  protected $enabled = true;
  protected $name = 'Umami';
  protected $description = 'Umami Analytics integration for Divi';
  protected $version = '1.0.0';
  protected $slug = 'umami';
  protected $dependencies = [
    'jquery',
  ];
  protected $default_options = [
    'enabled' => true,
    'umami_domain' => '',
    'website_id' => '',
    'ignore_logged_in' => true,
    'enable_events' => false,
    'events' => [],
  ];

  /**
   * Admin settings for the module
   * @return array
   * @package Misc
   * @since 1.0.0
   */
  public function admin_settings() {
    return [
      'umami_domain' => [
        'type' => 'text',
        'label' => __('Enter the Domain of your umami instance', 'divi-child'),
        'description' => __('without https://', 'divi-child'),
        'default' => $this->default_options['umami_domain'],
      ],
      'website_id' => [
        'type' => 'text',
        'label' => __('Enter your Umami website ID', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['website_id'],
      ],
      'ignore_logged_in' => [
        'type' => 'toggle',
        'label' => __('Ignore users that are logged in', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['ignore_logged_in'],
      ],
      'enable_events' => [
        'type' => 'toggle',
        'label' => __('Enable Events for Umami', 'divi-child'),
        'description' => __('', 'divi-child'),
        'default' => $this->default_options['enable_events'],
      ],
      'events' => [
        'type' => 'repeater',
        'label' => __('Umami Events', 'divi-child'),
        'description' => __('Configure events for Umami Analytics', 'divi-child'),
        'fields' => [
          'id' => [
            'type' => 'text',
            'label' => 'Event ID',
            'default' => ''
          ],
          'name' => [
            'type' => 'text',
            'label' => 'Event Name',
            'default' => ''
          ]
        ],
        'default' => [],
        'depends_on' => [
          'enable_events' => true
        ]
      ],
    ];
  }
}