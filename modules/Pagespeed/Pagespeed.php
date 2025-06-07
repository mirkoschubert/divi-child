<?php

namespace DiviChild\Modules\Pagespeed;

use DiviChild\Core\Abstracts\Module;

final class Pagespeed extends Module
{

  protected $enabled = true;
  protected $name = 'Pagespeed';
  protected $description = 'Google Pagespeed optimization module for WordPress.';
  protected $author = 'Mirko Schubert';
  protected $version = '1.0.0';
  protected $slug = 'pagespeed';
  protected $dependencies = [
    'jquery',
  ];
  protected $default_options = [
    'enabled' => true,
    'remove_pingback' => true,
    'remove_dashicons' => true,
    'remove_version_strings' => true,
    'remove_shortlink' => true,
    'preload_fonts' => false,
    'preload_fonts_list' => [
      ['path' => '/wp-content/themes/Divi/core/admin/fonts/modules/all/modules.woff']
    ]
  ];

  /**
   * Summary of admin_settings
   * @return array
   * @package Pagespeed
   * @since 1.0.0
   */
  public function admin_settings()
  {
    return [
      'remove_pingback' => [
        'type' => 'toggle',
        'label' => __('Remove Pingback', 'divi-child'),
        'description' => __('Removes the pingback header from the site.', 'divi-child'),
        'default' => $this->default_options['remove_pingback'],
      ],
      'remove_dashicons' => [
        'type' => 'toggle',
        'label' => __('Remove Dashicons', 'divi-child'),
        'description' => __('Removes dashicons from the frontend', 'divi-child'),
        'default' => $this->default_options['remove_dashicons'],
      ],
      'remove_version_strings' => [
        'type' => 'toggle',
        'label' => __('Remove Version Strings', 'divi-child'),
        'description' => __('Removes CSS and JS version query strings', 'divi-child'),
        'default' => $this->default_options['remove_version_strings'],
      ],
      'remove_shortlink' => [
        'type' => 'toggle',
        'label' => __('Remove Shortlink', 'divi-child'),
        'description' => __('Removes shortlink from head', 'divi-child'),
        'default' => $this->default_options['remove_shortlink'],
      ],
      'preload_fonts' => [
        'type' => 'toggle',
        'label' => __('Preload Fonts', 'divi-child'),
        'description' => __('Preload some fonts for speed', 'divi-child'),
        'default' => $this->default_options['preload_fonts'],
      ],
      'preload_fonts_list' => [
        'type' => 'repeater',
        'label' => __('Fonts List', 'divi-child'),
        'description' => __('Enter font paths to preload for better performance.', 'divi-child'),
        'fields' => [
          'path' => [
            'type' => 'text',
            'label' => __('Font Path', 'divi-child'),
            'description' => __('Path starting with "/wp-content/" and ending with font extension', 'divi-child'),
            'default' => '',
            'validate' => [
              'pattern' => '/^\/wp-content\/.*\.(woff|woff2|ttf|otf|eot)$/',
              'error_message' => __('Please enter a valid font path. It should start with "/wp-content/" and end with a font extension (woff, woff2, ttf, otf, eot).', 'divi-child')
            ]
          ]
        ],
        'default' => $this->default_options['preload_fonts_list'],
        'depends_on' => [
          'preload_fonts' => true
        ]
      ],
    ];
  }
}