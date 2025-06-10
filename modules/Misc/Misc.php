<?php

namespace DiviChild\Modules\Misc;

use DiviChild\Core\Abstracts\Module;

final class Misc extends Module
{

  protected $enabled = true;
  protected $name = 'Miscellaneous';
  protected $description = 'Miscellaneous fixes and enhancements for Divi';
  protected $version = '1.2.0';
  protected $slug = 'misc';
  protected $dependencies = [
    'jquery',
  ];
  protected $default_options = [
    'enabled' => true,
    'admin_dark_mode' => false,
    'duplicate_posts' => false,
    'disable_projects' => false,
    'stop_mail_updates' => false,
    'media_infinite_scroll' => false,
    'svg_support' => false,
    'webp_support' => false,
    'avif_support' => false,
    'hyphens' => false,
    'mobile_menu_breakpoint' => false,
    'mobile_menu_fullscreen' => false,
    'disable_divi_upsells' => false,
    'disable_divi_ai' => false,
  ];

  /**
   * Admin settings for the module
   * @return array
   * @package Misc
   * @since 1.0.0
   */
  public function admin_settings() {
    return [
      'admin_group' => [
        'type' => 'group',
        'title' => __('Admin & Backend', 'divi-child'),
        'description' => __('WordPress backend improvements and content management features', 'divi-child'),
        'fields' => [
          'admin_dark_mode' => [
            'type' => 'toggle',
            'label' => __('Enable dark mode for admin', 'divi-child'),
            'description' => __('Switch WordPress admin interface to dark mode with the theme colors', 'divi-child'),
            'default' => $this->default_options['admin_dark_mode'],
          ],
          'duplicate_posts' => [
            'type' => 'toggle',
            'label' => __('Enable duplicate posts', 'divi-child'),
            'description' => __('Allows you to duplicate posts and pages easily.', 'divi-child'),
            'default' => $this->default_options['duplicate_posts'],
          ],
          'disable_projects' => [
            'type' => 'toggle',
            'label' => __('Disable custom post type Projects', 'divi-child'),
            'description' => __('Removes the Projects post type from Divi', 'divi-child'),
            'default' => $this->default_options['disable_projects'],
          ],
          'stop_mail_updates' => [
            'type' => 'toggle',
            'label' => __('Disable auto-update emails', 'divi-child'),
            'description' => __('Stop email notifications when plugins or themes are automatically updated', 'divi-child'),
            'default' => $this->default_options['stop_mail_updates'],
          ],
        ]
      ],
      'media_group' => [
        'type' => 'group',
        'title' => __('Media & Files', 'divi-child'),
        'description' => __('Media library enhancements and file format support', 'divi-child'),
        'fields' => [
          'media_infinite_scroll' => [
            'type' => 'toggle',
            'label' => __('Enable infinite scroll for media library', 'divi-child'),
            'description' => __('Load media files continuously without pagination', 'divi-child'),
            'default' => $this->default_options['media_infinite_scroll'],
          ],
          'svg_support' => [
            'type' => 'toggle',
            'label' => __('Enable SVG file uploads', 'divi-child'),
            'description' => __('Allow SVG files in media library. Warning: SVG files can contain malicious code', 'divi-child'),
            'default' => $this->default_options['svg_support'],
            'dependencies' => [
              'wordpress' => '>= 4.7',
            ]
          ],
          'webp_support' => [
            'type' => 'toggle',
            'label' => __('Enable WebP file uploads', 'divi-child'),
            'description' => __('Allow WebP files in media library. Native support available in WordPress 5.8+', 'divi-child'),
            'default' => $this->default_options['webp_support'],
            'dependencies' => [
              'wordpress' => '< 5.8',
            ]
          ],
          'avif_support' => [
            'type' => 'toggle',
            'label' => __('Enable AVIF file uploads', 'divi-child'),
            'description' => __('Allow AVIF files in media library. Native support available in WordPress 6.5+', 'divi-child'),
            'default' => $this->default_options['avif_support'],
            'dependencies' => [
              'wordpress' => '< 6.5',
            ]
          ],
        ]
      ],
      'frontend_group' => [
        'type' => 'group',
        'title' => __('Frontend & Design', 'divi-child'),
        'description' => __('Visual improvements and responsive design enhancements', 'divi-child'),
        'fields' => [
          'hyphens' => [
            'type' => 'toggle',
            'label' => __('Enable hyphenation', 'divi-child'),
            'description' => __('Activate automatic word hyphenation for better text layout', 'divi-child'),
            'default' => $this->default_options['hyphens'],
          ],
          'mobile_menu_breakpoint' => [
            'type' => 'toggle',
            'label' => __('Mobile menu at 1280px', 'divi-child'),
            'description' => __('Switch to mobile menu at 1280px instead of default breakpoint', 'divi-child'),
            'default' => $this->default_options['mobile_menu_breakpoint'],
          ],
          'mobile_menu_fullscreen' => [
            'type' => 'toggle',
            'label' => __('Fullscreen mobile menu', 'divi-child'),
            'description' => __('Display mobile menu in fullscreen mode for better UX', 'divi-child'),
            'default' => $this->default_options['mobile_menu_fullscreen'],
          ],
        ]
      ],
      'divi_group' => [
        'type' => 'group',
        'title' => __('Divi Customizations', 'divi-child'),
        'description' => __('Disable or customize specific Divi features', 'divi-child'),
        'fields' => [
          'disable_divi_upsells' => [
            'type' => 'toggle',
            'label' => __('Disable Divi upsells', 'divi-child'),
            'description' => __('Hide promotional messages and upsells in Divi interface', 'divi-child'),
            'default' => $this->default_options['disable_divi_upsells'],
          ],
          'disable_divi_ai' => [
            'type' => 'toggle',
            'label' => __('Disable Divi AI', 'divi-child'),
            'description' => __('Remove Divi AI features and interface elements', 'divi-child'),
            'default' => $this->default_options['disable_divi_ai'],
          ],
        ]
      ],
    ];
  }
}