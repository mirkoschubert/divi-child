<?php

namespace DiviChild\Modules\Login;

use DiviChild\Core\Abstracts\Module;

final class Login extends Module
{

  protected $enabled = false;
  protected $author = 'Mirko Schubert';
  protected $version = '1.0.0';
  protected $slug = 'login';

  public function get_name(): string
  {
    return __('Login', 'divi-child');
  }

  public function get_description(): string
  {
    return __('Customize the WordPress login page with site identity and background image.', 'divi-child');
  }
  protected $dependencies = [];
  protected $default_options = [
    'enabled' => false,
    'login_site_identity' => false,
    'login_logo_width' => 120,
    'login_background_image' => 0,
  ];

  /**
   * Admin settings for the module
   * @return array
   * @package Login
   * @since 1.0.0
   */
  public function admin_settings(): array
  {
    return [
      'login_site_identity' => [
        'type' => 'toggle',
        'label' => __('Use site icon as login logo', 'divi-child'),
        'description' => __('Replace the WordPress logo on the login page with the site icon and link to the homepage.', 'divi-child'),
        'default' => $this->default_options['login_site_identity'],
      ],
      'login_logo_width' => [
        'type' => 'number',
        'label' => __('Logo width (px)', 'divi-child'),
        'description' => __('Width of the logo on the login page in pixels.', 'divi-child'),
        'default' => $this->default_options['login_logo_width'],
        'depends_on' => ['login_site_identity' => true],
      ],
      'login_background_image' => [
        'type' => 'image',
        'label' => __('Background Image', 'divi-child'),
        'description' => __('Background image for the login page (displayed full-size).', 'divi-child'),
        'default' => $this->default_options['login_background_image'],
      ],
    ];
  }
}
