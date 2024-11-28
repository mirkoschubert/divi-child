<?php
defined('ABSPATH') or die('No script kiddies please!');

$this->add_checkbox(
  'a11y',
  'aria_support',
  esc_html__('Add ARIA support to all relevant elements', 'divi-child')
);
$this->add_checkbox(
  'a11y',
  'nav_keyboard',
  esc_html__('Make main navigation fully keyboard accessible', 'divi-child')
);
$this->add_checkbox(
  'a11y',
  'focus_elements',
  esc_html__('Focus all clickable elements correctly', 'divi-child')
);
$this->add_checkbox(
  'a11y',
  'external_links',
  esc_html__('Tag external links for assistive technology', 'divi-child')
);
$this->add_checkbox(
  'a11y',
  'skip_link',
  esc_html__('Add a skip link to the page', 'divi-child')
);
$this->add_checkbox(
  'a11y',
  'scroll_top',
  esc_html__('Accessible scroll to top button', 'divi-child'),
  esc_html__('(Turn the Divi back to top button off!)', 'divi-child')
);
$this->add_checkbox(
  'a11y',
  'fix_viewport',
  esc_html__('Fix viewport meta', 'divi-child')
);
$this->add_checkbox(
  'a11y',
  'fix_screenreader',
  esc_html__('Fix screenreader text', 'divi-child')
);
$this->add_checkbox(
  'a11y',
  'underline_links',
  esc_html__('Underline all links except headlines and social icons', 'divi-child')
);
$this->add_checkbox(
  'a11y',
  'optimize_forms',
  esc_html__('Optimize forms for accessibility', 'divi-child'),
  esc_html__('(Comment Form, Minimal Contact Form, Forminator)', 'divi-child')
);


?>