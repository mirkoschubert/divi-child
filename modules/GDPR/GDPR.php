<?php

namespace DiviChild\Modules\GDPR;

use DiviChild\Core\Abstracts\Module;

final class GDPR extends Module
{

  protected $enabled = true;
  protected $name = 'GDPR';
  protected $description = 'General Data Protection Regulation (GDPR) compliance module for WordPress.';
  protected $version = '1.0.0';
  protected $slug = 'gdpr';
  protected $dependencies = [
    'jquery',
  ];
  protected $default_options = [
    'enabled' => true,
    'comments_external' => true,
    'comments_ip' => true,
    'disable_emojis' => true,
    'disable_oembeds' => true,
    'dns_prefetching' => true,
    'rest_api' => true
  ];

  public function admin_settings()
  {
    return [
      'comments_external' => [
        'type' => 'toggle',
        'label' => __('Comments External', 'divi-child'),
        'description' => __('Enable comments to be loaded from external sources.', 'divi-child'),
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
    ];
  }
}