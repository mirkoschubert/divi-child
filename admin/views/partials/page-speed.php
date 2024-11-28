<?php
defined('ABSPATH') or die('No script kiddies please!');

$this->add_checkbox(
  'page_speed',
  'remove_pingback',
  esc_html__('Disable page pingback', 'divi-child')
);
$this->add_checkbox(
  'page_speed',
  'remove_dashicons',
  esc_html__('Remove dashicons from the frontend', 'divi-child')
);
$this->add_checkbox(
  'page_speed',
  'remove_version_strings',
  esc_html__('Remove CSS and JS query strings', 'divi-child')
);
$this->add_checkbox(
  'page_speed',
  'remove_shortlink',
  esc_html__('Remove shortlink from head', 'divi-child')
);
$this->add_checkbox(
  'page_speed',
  'preload_fonts',
  esc_html__('Preload some fonts for speed', 'divi-child')
);
?>

<label for="font_list">
  <?php $font_list = $this->get_theme_option('page_speed', 'preload_fonts_list'); ?>
  <textarea id="font_list" name="divi_child_options[page_speed][preload_fonts_list]"
    rows="5"><?php echo esc_attr($font_list); ?></textarea>
  <p class="description">
    <?php esc_html_e('Type ony one path per line, otherwise it will break!', 'divi-child'); ?>
  </p>
</label>
<br>