<?php

namespace DiviChild\Modules\Privacy;

use DiviChild\Core\Abstracts\Module;

final class Privacy extends Module
{

  protected $enabled = true;
  protected $author = 'Mirko Schubert';
  protected $version = '1.1.0';
  protected $slug = 'privacy';

  public function get_name(): string
  {
    return __('Privacy & Security', 'divi-child');
  }

  public function get_description(): string
  {
    return __('Privacy and security enhancements for WordPress including GDPR compliance.', 'divi-child');
  }
  protected $dependencies = [];
  protected $default_options = [
    'enabled' => true,
    // Privacy (ehem. GDPR)
    'comments_external' => true,
    'comments_ip' => true,
    'disable_emojis' => true,
    'disable_oembeds' => true,
    'dns_prefetching' => true,
    'rest_api' => true,
    // Security
    'track_last_login' => false,
    'disable_author_archives' => false,
    'obfuscate_author_slugs' => false,
  ];

  /**
   * Admin settings for the module
   * @return array
   * @since 1.0.0
   */
  public function admin_settings(): array
  {
    return [
      'privacy' => [
        'type' => 'group',
        'label' => __('Privacy', 'divi-child'),
        'fields' => [
          'comments_external' => [
            'type' => 'toggle',
            'label' => __('Comments External', 'divi-child'),
            'description' => __('Make external links in comments truely external.', 'divi-child'),
            'default' => $this->default_options['comments_external'],
          ],
          'comments_ip' => [
            'type' => 'toggle',
            'label' => __('Comments IP', 'divi-child'),
            'description' => __('Enable comments to be loaded with IP address.', 'divi-child'),
            'default' => $this->default_options['comments_ip'],
          ],
          'disable_emojis' => [
            'type' => 'toggle',
            'label' => __('Disable Emojis', 'divi-child'),
            'description' => __('Disable emojis for GDPR compliance.', 'divi-child'),
            'default' => $this->default_options['disable_emojis'],
          ],
          'disable_oembeds' => [
            'type' => 'toggle',
            'label' => __('Disable oEmbeds', 'divi-child'),
            'description' => __('Disable oEmbeds for GDPR compliance.', 'divi-child'),
            'default' => $this->default_options['disable_oembeds'],
          ],
          'dns_prefetching' => [
            'type' => 'toggle',
            'label' => __('Disable DNS Prefetching', 'divi-child'),
            'description' => __('Disable DNS prefetching for GDPR compliance.', 'divi-child'),
            'default' => $this->default_options['dns_prefetching'],
          ],
          'rest_api' => [
            'type' => 'toggle',
            'label' => __('Disable REST API', 'divi-child'),
            'description' => __('Disable REST API for GDPR compliance.', 'divi-child'),
            'default' => $this->default_options['rest_api'],
          ],
        ],
      ],
      'security' => [
        'type' => 'group',
        'label' => __('Security', 'divi-child'),
        'fields' => [
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
        ],
      ],
    ];
  }
}
