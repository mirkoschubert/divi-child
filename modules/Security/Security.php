<?php

namespace DiviChild\Modules\Security;

use DiviChild\Core\Abstracts\Module;

final class Security extends Module
{

  protected $enabled = false;
  protected $name = 'Security';
  protected $description = 'Security and privacy enhancements for WordPress.';
  protected $version = '1.0.0';
  protected $slug = 'security';
  protected $dependencies = [];
  protected $default_options = [
    'enabled' => false,
    'track_last_login' => false,
    'disable_author_archives' => false,
    'obfuscate_author_slugs' => false,
  ];

  /**
   * Admin settings for the module
   * @return array
   * @package Security
   * @since 1.0.0
   */
  public function admin_settings()
  {
    return [
      'track_last_login' => [
        'type' => 'toggle',
        'label' => __('Track last login time', 'divi-child'),
        'description' => __('Show a "Last Login" column in the users table.', 'divi-child'),
        'default' => $this->default_options['track_last_login'],
      ],
      'disable_author_archives' => [
        'type' => 'toggle',
        'label' => __('Disable author archives', 'divi-child'),
        'description' => __('Redirect author archive pages to 404 and remove author links.', 'divi-child'),
        'default' => $this->default_options['disable_author_archives'],
      ],
      'obfuscate_author_slugs' => [
        'type' => 'toggle',
        'label' => __('Obfuscate author slugs', 'divi-child'),
        'description' => __('Replace author usernames in URLs with encrypted IDs to prevent user enumeration.', 'divi-child'),
        'default' => $this->default_options['obfuscate_author_slugs'],
      ],
    ];
  }
}
