<?php

namespace DiviChild\Modules\GDPR;

use DiviChild\Core\Abstracts\Module;
//use DiviChild\Core\Config;

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
  protected $config;
  protected $default_options = [
    'enabled' => true,
    'comments_external' => true,
    'comments_ip' => true,
    'disable_emojis' => true,
    'disable_oembeds' => true,
    'dns_prefetching' => true,
    'rest_api' => true
  ];

  public function init()
  {
    parent::init();
  }


  public function sanitize_options($options)
  {
    $options['comments_external'] = isset($options['comments_external']);
    $options['comments_ip'] = isset($options['comments_ip']);
    $options['disable_emojis'] = isset($options['disable_emojis']);
    $options['disable_oembeds'] = isset($options['disable_oembeds']);
    $options['dns_prefetching'] = isset($options['dns_prefetching']);
    $options['rest_api'] = isset($options['rest_api']);

    return $options;
  }
}