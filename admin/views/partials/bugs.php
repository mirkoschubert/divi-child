<?php
defined('ABSPATH') or die('No script kiddies please!');

$this->add_checkbox(
  'bug_fixes',
  'support_center',
  esc_html__('Remove Divi Support Center from Frontend', 'divi-child'),
  esc_html__('(Divi 3.20.1 only)', 'divi-child')
);
$this->add_checkbox(
  'bug_fixes',
  'fixed_navigation',
  esc_html__('Enable fixed navigation bar option in Theme Builder', 'divi-child'),
  esc_html__('(Divi 4.0 and up)', 'divi-child')
);
$this->add_checkbox(
  'bug_fixes',
  'display_errors',
  esc_html__('Fix display errors in Theme Builder', 'divi-child'),
  esc_html__('(Divi 4.0 up to Divi 4.12)', 'divi-child')
);
$this->add_checkbox(
  'bug_fixes',
  'logo_image_sizing',
  esc_html__('Fix logo image sizing in Theme Builder', 'divi-child'),
  esc_html__('(Divi 4.6.6)', 'divi-child')
);
$this->add_checkbox(
  'bug_fixes',
  'split_section',
  __('Set CSS class <code>.split-section-fix</code> for swapping image and text on tablet and phone', 'divi-child')
);
?>