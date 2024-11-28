<?php
defined('ABSPATH') or die('No script kiddies please!');

$this->add_checkbox(
  'misc',
  'disable_projects',
  esc_html__('Disable custom post type Projects.', 'divi-child')
);
$this->add_checkbox(
  'misc',
  'stop_mail_updates',
  esc_html__('Disable email notification when plugins or theme where automatically updated.', 'divi-child')
);
$this->add_checkbox(
  'misc',
  'svg_support',
  esc_html__('Enable to upload SVG files', 'divi-child')
);
$this->add_checkbox(
  'misc',
  'webp_support',
  esc_html__('Enable to upload WebP files', 'divi-child')
);
$this->add_checkbox(
  'misc',
  'hyphens',
  esc_html__('Enable hyphenation for the whole website', 'divi-child')
);
$this->add_checkbox(
  'misc',
  'mobile_menu_breakpoint',
  esc_html__('Set breakpoint for the mobile menu to 1280px', 'divi-child')
);
$this->add_checkbox(
  'misc',
  'mobile_menu_fullscreen',
  esc_html__('Enable fullscreen mode for the mobile menu', 'divi-child')
);
?>