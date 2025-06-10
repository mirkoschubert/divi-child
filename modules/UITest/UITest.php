<?php

namespace DiviChild\Modules\UITest;

use DiviChild\Core\Abstracts\Module;

final class UITest extends Module
{

  protected $enabled = true;
  protected $name = 'UI Test Module';
  protected $description = 'Testing module for all form field types and combinations';
  protected $version = '1.0.0';
  protected $slug = 'uitest';
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
    
    // Advanced Features
    'dependent_text' => 'This depends on toggle',
    'dependent_number' => 100,
    'conditional_select' => 'conditional_option1',
    
    // Repeater Examples
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
    
    // Group Examples
    'group_basic_enabled' => false,
    'group_basic_text' => 'Group text',
    'group_basic_number' => 10,
    'group_advanced_enabled' => true,
    'group_advanced_api_key' => '',
    'group_advanced_timeout' => 30,
    'group_advanced_retries' => 3,
    'group_advanced_debug' => false,
  ];

  /**
   * Admin settings for the module - comprehensive form field testing
   * @return array
   * @package UITest
   * @since 1.0.0
   */
  public function admin_settings() {
    return [
      
      // BASIC FIELDS GROUP
      'basic_fields_group' => [
        'type' => 'group',
        'title' => __('Basic Form Fields', 'divi-child'),
        'description' => __('Testing all basic form field types', 'divi-child'),
        'fields' => [
          'test_text' => [
            'type' => 'text',
            'label' => __('Text Field', 'divi-child'),
            'description' => __('A simple text input field with placeholder', 'divi-child'),
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
            'description' => __('Multi-line text input for longer content', 'divi-child'),
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
            'description' => __('Boolean on/off switch - controls dependent fields below', 'divi-child'),
            'default' => true,
          ],
          'test_select' => [
            'type' => 'select',
            'label' => __('Select Field', 'divi-child'),
            'description' => __('Single selection dropdown', 'divi-child'),
            'default' => 'option2',
            'options' => [
              'option1' => __('First Option', 'divi-child'),
              'option2' => __('Second Option (Default)', 'divi-child'),
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
              'option1' => __('Multi Option 1', 'divi-child'),
              'option2' => __('Multi Option 2', 'divi-child'),
              'option3' => __('Multi Option 3', 'divi-child'),
              'option4' => __('Multi Option 4', 'divi-child'),
              'option5' => __('Multi Option 5', 'divi-child'),
            ]
          ],
          'test_color' => [
            'type' => 'color',
            'label' => __('Color Field', 'divi-child'),
            'description' => __('Color picker with hex value', 'divi-child'),
            'default' => '#007cba',
          ],
        ]
      ],

      // DEPENDENCY TESTING GROUP
      'dependency_group' => [
        'type' => 'group',
        'title' => __('Dependency Testing', 'divi-child'),
        'description' => __('Fields that show/hide based on other field values', 'divi-child'),
        'fields' => [
          'dependent_text' => [
            'type' => 'text',
            'label' => __('Dependent Text Field', 'divi-child'),
            'description' => __('This field only shows when the toggle above is enabled', 'divi-child'),
            'default' => 'This depends on toggle',
            'depends_on' => [
              'test_toggle' => true
            ]
          ],
          'dependent_number' => [
            'type' => 'number',
            'label' => __('Dependent Number Field', 'divi-child'),
            'description' => __('This number field also depends on the toggle being enabled', 'divi-child'),
            'default' => 100,
            'depends_on' => [
              'test_toggle' => true
            ],
            'validate' => [
              'min' => 50,
              'max' => 500
            ]
          ],
          'conditional_select' => [
            'type' => 'select',
            'label' => __('Conditional Select', 'divi-child'),
            'description' => __('Shows only when "Second Option" is selected in the basic select above', 'divi-child'),
            'default' => 'conditional_option1',
            'depends_on' => [
              'test_select' => 'option2'
            ],
            'options' => [
              'conditional_option1' => __('Conditional Option 1', 'divi-child'),
              'conditional_option2' => __('Conditional Option 2', 'divi-child'),
              'conditional_option3' => __('Conditional Option 3', 'divi-child'),
            ]
          ],
          'double_dependent' => [
            'type' => 'color',
            'label' => __('Double Dependent Color', 'divi-child'),
            'description' => __('Depends on both toggle=true AND select=option2', 'divi-child'),
            'default' => '#ff6b35',
            'depends_on' => [
              'test_toggle' => true,
              'test_select' => 'option2'
            ]
          ]
        ]
      ],

      // SIMPLE REPEATER
      'simple_repeater' => [
        'type' => 'repeater',
        'label' => __('Simple Repeater', 'divi-child'),
        'description' => __('Basic repeater with text and textarea fields', 'divi-child'),
        'fields' => [
          'title' => [
            'type' => 'text',
            'label' => __('Item Title', 'divi-child'),
            'description' => __('Title for this repeater item', 'divi-child'),
            'default' => 'New Item'
          ],
          'description' => [
            'type' => 'textarea',
            'label' => __('Item Description', 'divi-child'),
            'description' => __('Detailed description of this item', 'divi-child'),
            'default' => 'Item description here...'
          ]
        ]
      ],

      // COMPLEX REPEATER 
      'complex_repeater' => [
        'type' => 'repeater',
        'label' => __('Complex Repeater', 'divi-child'),
        'description' => __('Advanced repeater with all field types and dependencies', 'divi-child'),
        'fields' => [
          'name' => [
            'type' => 'text',
            'label' => __('Name', 'divi-child'),
            'description' => __('Name of this complex item', 'divi-child'),
            'default' => 'Complex Item'
          ],
          'type' => [
            'type' => 'select',
            'label' => __('Type', 'divi-child'),
            'description' => __('Type selection that controls other fields', 'divi-child'),
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
            'description' => __('Toggle to enable/disable this item', 'divi-child'),
            'default' => true
          ],
          'value' => [
            'type' => 'number',
            'label' => __('Value (Type A only)', 'divi-child'),
            'description' => __('Numeric value - only shown for Type A', 'divi-child'),
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
            'description' => __('Color picker - only shown when item is enabled', 'divi-child'),
            'default' => '#ff0000',
            'depends_on' => [
              'enabled' => true
            ]
          ],
          'advanced_text' => [
            'type' => 'text',
            'label' => __('Advanced Text (Type B & C)', 'divi-child'),
            'description' => __('Text field for Type B and Type C only', 'divi-child'),
            'default' => 'Advanced setting',
            'depends_on' => [
              'type' => ['type_b', 'type_c']
            ]
          ]
        ]
      ],

      // BASIC GROUP EXAMPLE
      'basic_group' => [
        'type' => 'group',
        'title' => __('Basic Settings Group', 'divi-child'),
        'description' => __('A collapsible group with basic settings', 'divi-child'),
        'fields' => [
          'group_basic_enabled' => [
            'type' => 'toggle',
            'label' => __('Enable Basic Group', 'divi-child'),
            'description' => __('Master toggle for basic group functionality', 'divi-child'),
            'default' => false
          ],
          'group_basic_text' => [
            'type' => 'text',
            'label' => __('Basic Text', 'divi-child'),
            'description' => __('Text setting within the basic group', 'divi-child'),
            'default' => 'Group text',
            'depends_on' => [
              'group_basic_enabled' => true
            ]
          ],
          'group_basic_number' => [
            'type' => 'number',
            'label' => __('Basic Number', 'divi-child'),
            'description' => __('Number setting within the basic group', 'divi-child'),
            'default' => 10,
            'depends_on' => [
              'group_basic_enabled' => true
            ],
            'validate' => [
              'min' => 1,
              'max' => 50
            ]
          ]
        ]
      ],

      // ADVANCED GROUP EXAMPLE
      'advanced_group' => [
        'type' => 'group',
        'title' => __('Advanced API Settings', 'divi-child'),
        'description' => __('Advanced configuration options for API integration', 'divi-child'),
        'fields' => [
          'group_advanced_enabled' => [
            'type' => 'toggle',
            'label' => __('Enable Advanced Settings', 'divi-child'),
            'description' => __('Enable advanced API configuration', 'divi-child'),
            'default' => true
          ],
          'group_advanced_api_key' => [
            'type' => 'text',
            'label' => __('API Key', 'divi-child'),
            'description' => __('Your secret API key for authentication', 'divi-child'),
            'default' => '',
            'depends_on' => [
              'group_advanced_enabled' => true
            ]
          ],
          'group_advanced_timeout' => [
            'type' => 'number',
            'label' => __('Timeout (seconds)', 'divi-child'),
            'description' => __('Request timeout in seconds', 'divi-child'),
            'default' => 30,
            'depends_on' => [
              'group_advanced_enabled' => true
            ],
            'validate' => [
              'min' => 5,
              'max' => 300
            ]
          ],
          'group_advanced_retries' => [
            'type' => 'number',
            'label' => __('Max Retries', 'divi-child'),
            'description' => __('Maximum number of retry attempts', 'divi-child'),
            'default' => 3,
            'depends_on' => [
              'group_advanced_enabled' => true
            ],
            'validate' => [
              'min' => 0,
              'max' => 10
            ]
          ],
          'group_advanced_debug' => [
            'type' => 'toggle',
            'label' => __('Debug Mode', 'divi-child'),
            'description' => __('Enable debug logging for API calls', 'divi-child'),
            'default' => false,
            'depends_on' => [
              'group_advanced_enabled' => true
            ]
          ]
        ]
      ],

      // NESTED COMPLEXITY EXAMPLE
      'nested_group' => [
        'type' => 'group',
        'title' => __('Nested Complexity Example', 'divi-child'),
        'description' => __('Demonstrates complex nesting and dependencies', 'divi-child'),
        'fields' => [
          'nested_mode' => [
            'type' => 'select',
            'label' => __('Operation Mode', 'divi-child'),
            'description' => __('Select the operation mode to reveal different options', 'divi-child'),
            'default' => 'mode_simple',
            'options' => [
              'mode_simple' => __('Simple Mode', 'divi-child'),
              'mode_advanced' => __('Advanced Mode', 'divi-child'),
              'mode_expert' => __('Expert Mode', 'divi-child'),
            ]
          ],
          'simple_option' => [
            'type' => 'text',
            'label' => __('Simple Option', 'divi-child'),
            'description' => __('Available in simple mode only', 'divi-child'),
            'default' => 'Simple setting',
            'depends_on' => [
              'nested_mode' => 'mode_simple'
            ]
          ],
          'advanced_options' => [
            'type' => 'multi_select',
            'label' => __('Advanced Options', 'divi-child'),
            'description' => __('Multiple options for advanced mode', 'divi-child'),
            'default' => ['adv_opt1'],
            'depends_on' => [
              'nested_mode' => 'mode_advanced'
            ],
            'options' => [
              'adv_opt1' => __('Advanced Option 1', 'divi-child'),
              'adv_opt2' => __('Advanced Option 2', 'divi-child'),
              'adv_opt3' => __('Advanced Option 3', 'divi-child'),
            ]
          ],
          'expert_config' => [
            'type' => 'textarea',
            'label' => __('Expert Configuration', 'divi-child'),
            'description' => __('Raw configuration for expert users', 'divi-child'),
            'default' => '{\n  "expert": true,\n  "level": "maximum"\n}',
            'depends_on' => [
              'nested_mode' => 'mode_expert'
            ]
          ],
          'expert_danger_zone' => [
            'type' => 'toggle',
            'label' => __('⚠️ Danger Zone', 'divi-child'),
            'description' => __('Enable potentially dangerous expert features', 'divi-child'),
            'default' => false,
            'depends_on' => [
              'nested_mode' => 'mode_expert'
            ]
          ],
          'expert_danger_color' => [
            'type' => 'color',
            'label' => __('Danger Color', 'divi-child'),
            'description' => __('Color for dangerous operations (expert + danger zone)', 'divi-child'),
            'default' => '#ff0000',
            'depends_on' => [
              'nested_mode' => 'mode_expert',
              'expert_danger_zone' => true
            ]
          ]
        ]
      ]
    ];
  }
}