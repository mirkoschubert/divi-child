<?php

function migrate($version) {
  if (version_compare($version, '2.3.0', '>=')) {
    $options = get_option('divi_child_options');
    if (isset($options) && isset($options['gdpr_comments_external'])) {
      // Convert old options to new
      $new_options = [
        'gdpr' => [
          'comments_external' => $options['gdpr_comments_external'] || 'on',
          'comments_ip' => $options['gdpr_comments_ip'] || 'on',
          'disable_emojis' => $options['disable_emojis'] || 'on',
          'disable_oembeds' => $options['disable_oembeds'] || 'on',
          'dns_prefetching' => $options['dns_prefetching'] || 'on',
          'rest_api' => $options['rest_api'] || 'on'
        ],
        'page_speed' => [
          'remove_pingback' => $options['page_pingback'] || 'on',
          'remove_dashicons' => $options['remove_dashicons'] || 'on',
          'remove_version_strings' => $options['version_query_strings'] || 'on',
          'remove_shortlink' => $options['remove_shortlink'] || 'on',
          'preload_fonts' => $options['preload_fonts'] || 'off',
          'preload_fonts_list' => $options['font_list'] || sanitize_textarea_field('/wp-content/themes/Divi/core/admin/fonts/modules/all/modules.woff')
        ],
        'a11y' => [
          'fix_viewport' => $options['viewport_meta'] || 'on'
        ],
        'bug_fixes' => [
          'support_center' => $options['support_center'] || 'off',
          'fixed_navigation' => $options['tb_header_fix'] || 'off',
          'display_errors' => $options['tb_display_errors'] || 'off',
          'logo_image_sizing' => $options['logo_image_sizing'] || 'off',
          'split_section' => $options['split_section_fix'] || 'off'
        ],
        'misc' => [
          'disable_projects' => $options['disable_projects'] || 'off',
          'stop_mail_updates' => $options['stop_mail_updates'] || 'off',
          'svg_support' => $options['svg_support'] || 'on',
          'webp_support' => $options['webp_support'] || 'on',
          'hyphens' => $options['hyphens'] || 'on',
          'mobile_menu_breakpoint' => $options['mobile_menu_breakpoint'] || 'on',
          'mobile_menu_fullscreen' => $options['mobile_menu_fullscreen'] || 'on'
        ]
      ];
      update_option('divi_child_options', $new_options);
    }
  }
}