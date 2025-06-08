<?php

namespace DiviChild\Modules\A11y;

use DiviChild\Core\Abstracts\Module;

final class A11y extends Module
{

  protected $enabled = true;
  protected $name = 'Accessibility';
  protected $description = 'Accessibility module for WordPress.';
  protected $author = 'Horst Nahrstedt';
  protected $version = '1.1.0';
  protected $slug = 'a11y';
  protected $dependencies = [
    'js' => [
      'jquery'
    ],
    'divi_version' => '4.0.0',
    'php_version' => '7.4',
    'wp_version' => '5.0',
  ];
  protected $default_options = [
    'enabled' => true,
    'aria_support' => true,
    'nav_keyboard' => true,
    'focus_elements' => true,
    'external_links' => true,
    'skip_link' => true,
    'scroll_top' => true,
    'fix_viewport' => true,
    'fix_screenreader' => true,
    'underline_links' => true,
    'optimize_forms' => true,
    'stop_animations' => true,
    'text_highlight_bg' => '#3399ff',
    'text_highlight_color' => '#ffffff',
    'slider_nav_spacing' => false
  ];


  /**
   * Admin settings for the module
   * @return array
   * @package A11y
   * @since 1.0.0
   */
  public function admin_settings()
  {
    return [
      'basic_group' => [
        'type' => 'group',
        'title' => __('Basic Accessibility', 'divi-child'),
        'description' => __('Essential accessibility features for your website', 'divi-child'),
        'fields' => [
          'skip_link' => [
            'type' => 'toggle',
            'label' => __('Add a skip link to the page', 'divi-child'),
            'description' => '',
            'default' => $this->default_options['skip_link'],
          ],
          'fix_viewport' => [
            'type' => 'toggle',
            'label' => __('Fix viewport meta', 'divi-child'),
            'description' => '',
            'default' => $this->default_options['fix_viewport'],
          ],
          'scroll_top' => [
            'type' => 'toggle',
            'label' => __('Accessible scroll to top button', 'divi-child'),
            'description' => __('Turn the Divi back to top button off!', 'divi-child'),
            'default' => $this->default_options['scroll_top'],
          ],
        ]
      ],
      'navigation_group' => [
        'type' => 'group',
        'title' => __('Navigation & Focus', 'divi-child'),
        'description' => __('Keyboard navigation and focus management', 'divi-child'),
        'fields' => [
          'nav_keyboard' => [
            'type' => 'toggle',
            'label' => __('Make main navigation fully keyboard accessible', 'divi-child'),
            'description' => '',
            'default' => $this->default_options['nav_keyboard'],
          ],
          'focus_elements' => [
            'type' => 'toggle',
            'label' => __('Focus all clickable elements correctly', 'divi-child'),
            'description' => '',
            'default' => $this->default_options['focus_elements'],
          ],
          'external_links' => [
            'type' => 'toggle',
            'label' => __('Tag external links for assistive technology', 'divi-child'),
            'description' => '',
            'default' => $this->default_options['external_links'],
          ],
        ]
      ],
      'content_group' => [
        'type' => 'group',
        'title' => __('Content & ARIA', 'divi-child'),
        'description' => __('Content accessibility and ARIA enhancements', 'divi-child'),
        'fields' => [
          'aria_support' => [
            'type' => 'toggle',
            'label' => __('Add ARIA support to all relevant elements', 'divi-child'),
            'description' => '',
            'default' => $this->default_options['aria_support'],
          ],
          'optimize_forms' => [
            'type' => 'toggle',
            'label' => __('Optimize forms for accessibility', 'divi-child'),
            'description' => __('Supports comment form, Minimal Contact Form and Forminator', 'divi-child'),
            'default' => $this->default_options['optimize_forms'],
          ],
          'fix_screenreader' => [
            'type' => 'toggle',
            'label' => __('Fix screenreader text', 'divi-child'),
            'description' => '',
            'default' => $this->default_options['fix_screenreader'],
          ],
        ]
      ],
      'visual_group' => [
        'type' => 'group',
        'title' => __('Visual & Animation', 'divi-child'),
        'description' => __('Visual accessibility and animation controls', 'divi-child'),
        'fields' => [
          'stop_animations' => [
            'type' => 'toggle',
            'label' => __('Stop animations if users have preference set for no animations', 'divi-child'),
            'description' => __('Respects the prefers-reduced-motion setting on user devices', 'divi-child'),
            'default' => $this->default_options['stop_animations'],
          ],
          'underline_links' => [
            'type' => 'toggle',
            'label' => __('Underline all links except headlines and social icons', 'divi-child'),
            'description' => '',
            'default' => $this->default_options['underline_links'],
          ],
          'text_highlight_bg' => [
            'type' => 'color',
            'label' => __('Text selection background color', 'divi-child'),
            'description' => __('Background color for selected text', 'divi-child'),
            'default' => $this->default_options['text_highlight_bg'],
          ],
          'text_highlight_color' => [
            'type' => 'color',
            'label' => __('Text selection text color', 'divi-child'),
            'description' => __('Text color for selected text', 'divi-child'),
            'default' => $this->default_options['text_highlight_color'],
          ],
          'slider_nav_spacing' => [
            'type' => 'toggle',
            'label' => __('Add spacing to slider navigation elements', 'divi-child'),
            'description' => __('Improves accessibility by adding space between slider controls', 'divi-child'),
            'default' => $this->default_options['slider_nav_spacing'],
          ],
        ]
      ]
    ];
  }


}