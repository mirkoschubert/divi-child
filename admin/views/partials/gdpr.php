<?php
defined('ABSPATH') or die('No script kiddies please!');

$this->add_checkbox(
  'gdpr',
  'comments_external',
  esc_html__('Make every comment and comment author link truely external', 'divi-child')
);
$this->add_checkbox(
  'gdpr',
  'comments_ip',
  esc_html__('Don\'t save the commentor\'s IP address', 'divi-child')
);
$this->add_checkbox(
  'gdpr',
  'disable_emojis',
  esc_html__('Disable Emojis', 'divi-child')
);
$this->add_checkbox(
  'gdpr',
  'disable_oembeds',
  esc_html__('Disable oEmbeds', 'divi-child')
);
$this->add_checkbox(
  'gdpr',
  'dns_prefetching',
  esc_html__('Remove DNS prefetching for WordPress', 'divi-child')
);
$this->add_checkbox(
  'gdpr',
  'rest_api',
  esc_html__('Remove REST API & XML-RPC headers for security reasons', 'divi-child')
);

?>