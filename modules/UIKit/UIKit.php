<?php

namespace DiviChild\Modules\UIKit;

use DiviChild\Core\Abstracts\Module;

final class UIKit extends Module
{

  protected $enabled = true;
  protected $name = 'UI Kit';
  protected $description = 'Reference module for all form field types, repeaters and dependencies';
  protected $version = '1.0.0';
  protected $slug = 'uikit';
  protected $dependencies = [];
  protected $default_options = [
    'enabled' => true,
    // Basic Fields
    'test_text' => 'Default text value',
    'test_textarea' => "Default textarea\nwith multiple lines",
    'test_number' => 42,
    'test_toggle' => true,
    'test_select' => 'option2',
    'test_multi_select' => ['option1', 'option3'],
    'test_color' => '#007cba',
    'test_image' => '',
    // Repeaters
    'simple_repeater' => [
      [
        'title' => 'First Item',
        'description' => 'First item description'
      ],
      [
        'title' => 'Second Item',
        'description' => 'Second item description'
      ]
    ],
    'complex_repeater' => [
      [
        'name' => 'Complex Item 1',
        'type' => 'type_a',
        'enabled' => true,
        'value' => 25,
        'color' => '#ff0000'
      ]
    ],
    // Dependencies
    'dep_toggle' => true,
    'dep_text' => 'This depends on toggle',
    'dep_mode' => 'mode_a',
    'dep_conditional' => 'Conditional value',
    'dep_double' => '#ff6b35',
    'dep_array' => 'Array dependent value',
  ];

  /**
   * Admin settings for the UI Kit module
   * @return array
   * @package UIKit
   * @since 1.0.0
   */
  public function admin_settings(): array {
    return [

      // 1. BASIC FORM FIELDS
      'basic_fields' => [
        'type' => 'group',
        'title' => __('Basic Form Fields', 'divi-child'),
        'description' => __('All available field types', 'divi-child'),
        'fields' => [
          'test_text' => [
            'type' => 'text',
            'label' => __('Text Field', 'divi-child'),
            'description' => __('A simple text input field', 'divi-child'),
            'default' => 'Default text value',
            'validate' => [
              'required' => false,
              'min_length' => 3,
              'max_length' => 100
            ]
          ],
          'test_textarea' => [
            'type' => 'textarea',
            'label' => __('Textarea Field', 'divi-child'),
            'description' => __('Multi-line text input', 'divi-child'),
            'default' => "Default textarea\nwith multiple lines",
          ],
          'test_number' => [
            'type' => 'number',
            'label' => __('Number Field', 'divi-child'),
            'description' => __('Numeric input with min/max validation', 'divi-child'),
            'default' => 42,
            'validate' => [
              'min' => 0,
              'max' => 1000
            ]
          ],
          'test_toggle' => [
            'type' => 'toggle',
            'label' => __('Toggle Field', 'divi-child'),
            'description' => __('Boolean on/off switch', 'divi-child'),
            'default' => true,
          ],
          'test_select' => [
            'type' => 'select',
            'label' => __('Select Field', 'divi-child'),
            'description' => __('Single selection dropdown', 'divi-child'),
            'default' => 'option2',
            'options' => [
              'option1' => __('First Option', 'divi-child'),
              'option2' => __('Second Option', 'divi-child'),
              'option3' => __('Third Option', 'divi-child'),
              'option4' => __('Fourth Option', 'divi-child'),
            ]
          ],
          'test_multi_select' => [
            'type' => 'multi_select',
            'label' => __('Multi-Select Field', 'divi-child'),
            'description' => __('Multiple selection dropdown', 'divi-child'),
            'default' => ['option1', 'option3'],
            'options' => [
              'option1' => __('Option 1', 'divi-child'),
              'option2' => __('Option 2', 'divi-child'),
              'option3' => __('Option 3', 'divi-child'),
              'option4' => __('Option 4', 'divi-child'),
              'option5' => __('Option 5', 'divi-child'),
            ]
          ],
          'test_color' => [
            'type' => 'color',
            'label' => __('Color Field', 'divi-child'),
            'description' => __('Color picker with hex value', 'divi-child'),
            'default' => '#007cba',
          ],
          'test_image' => [
            'type' => 'image',
            'label' => __('Image Field', 'divi-child'),
            'description' => __('Image upload via media library', 'divi-child'),
            'default' => '',
          ],
        ]
      ],

      // 2. REPEATERS
      'repeater_fields' => [
        'type' => 'group',
        'title' => __('Repeater Fields', 'divi-child'),
        'description' => __('Simple and complex repeater examples', 'divi-child'),
        'fields' => [
          'simple_repeater' => [
            'type' => 'repeater',
            'label' => __('Simple Repeater', 'divi-child'),
            'description' => __('Basic repeater with text fields', 'divi-child'),
            'fields' => [
              'title' => [
                'type' => 'text',
                'label' => __('Item Title', 'divi-child'),
                'default' => 'New Item'
              ],
              'description' => [
                'type' => 'textarea',
                'label' => __('Item Description', 'divi-child'),
                'default' => 'Item description here...'
              ]
            ]
          ],
          'complex_repeater' => [
            'type' => 'repeater',
            'label' => __('Complex Repeater', 'divi-child'),
            'description' => __('Repeater with multiple field types and dependencies', 'divi-child'),
            'fields' => [
              'name' => [
                'type' => 'text',
                'label' => __('Name', 'divi-child'),
                'default' => 'Complex Item'
              ],
              'type' => [
                'type' => 'select',
                'label' => __('Type', 'divi-child'),
                'default' => 'type_a',
                'options' => [
                  'type_a' => __('Type A', 'divi-child'),
                  'type_b' => __('Type B', 'divi-child'),
                  'type_c' => __('Type C', 'divi-child'),
                ]
              ],
              'enabled' => [
                'type' => 'toggle',
                'label' => __('Enable Item', 'divi-child'),
                'default' => true
              ],
              'value' => [
                'type' => 'number',
                'label' => __('Value (Type A only)', 'divi-child'),
                'default' => 25,
                'depends_on' => [
                  'type' => 'type_a'
                ],
                'validate' => [
                  'min' => 1,
                  'max' => 100
                ]
              ],
              'color' => [
                'type' => 'color',
                'label' => __('Color (when enabled)', 'divi-child'),
                'default' => '#ff0000',
                'depends_on' => [
                  'enabled' => true
                ]
              ],
              'advanced_text' => [
                'type' => 'text',
                'label' => __('Advanced Text (Type B & C)', 'divi-child'),
                'default' => 'Advanced setting',
                'depends_on' => [
                  'type' => ['type_b', 'type_c']
                ]
              ]
            ]
          ],
        ]
      ],

      // 3. DEPENDENCIES
      'dependency_fields' => [
        'type' => 'group',
        'title' => __('Dependencies', 'divi-child'),
        'description' => __('All dependency variants: simple, conditional, double and array', 'divi-child'),
        'fields' => [
          'dep_toggle' => [
            'type' => 'toggle',
            'label' => __('Master Toggle', 'divi-child'),
            'description' => __('Controls dependent fields below', 'divi-child'),
            'default' => true,
          ],
          'dep_text' => [
            'type' => 'text',
            'label' => __('Simple Dependency', 'divi-child'),
            'description' => __('Visible when master toggle is on', 'divi-child'),
            'default' => 'This depends on toggle',
            'depends_on' => [
              'dep_toggle' => true
            ]
          ],
          'dep_mode' => [
            'type' => 'select',
            'label' => __('Mode Select', 'divi-child'),
            'description' => __('Controls conditional and array dependencies', 'divi-child'),
            'default' => 'mode_a',
            'options' => [
              'mode_a' => __('Mode A', 'divi-child'),
              'mode_b' => __('Mode B', 'divi-child'),
              'mode_c' => __('Mode C', 'divi-child'),
            ]
          ],
          'dep_conditional' => [
            'type' => 'text',
            'label' => __('Conditional Dependency', 'divi-child'),
            'description' => __('Visible only in Mode A', 'divi-child'),
            'default' => 'Conditional value',
            'depends_on' => [
              'dep_mode' => 'mode_a'
            ]
          ],
          'dep_double' => [
            'type' => 'color',
            'label' => __('Double Dependency', 'divi-child'),
            'description' => __('Visible when toggle is on AND mode is A', 'divi-child'),
            'default' => '#ff6b35',
            'depends_on' => [
              'dep_toggle' => true,
              'dep_mode' => 'mode_a'
            ]
          ],
          'dep_array' => [
            'type' => 'text',
            'label' => __('Array Dependency', 'divi-child'),
            'description' => __('Visible in Mode B or Mode C', 'divi-child'),
            'default' => 'Array dependent value',
            'depends_on' => [
              'dep_mode' => ['mode_b', 'mode_c']
            ]
          ],
        ]
      ],
    ];
  }
}
