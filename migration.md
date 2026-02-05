# Migrationsanforderungen

## Allgemeine Options

Neben den Modul-Options gibt es eine separate WordPress-Option für die Theme-Version:

- **`divi_child_version`** — Gespeichert als eigene Option in `wp_options` (nicht Teil von `divi_child_options`). Existiert erst ab v3.0.0. Für ältere Installationen erkennt `Migration::detect_version_from_options()` die Version anhand der Struktur von `divi_child_options`.

## bis v2.2.0

```php
$options = array(
  'gdpr_comments_external' => 'on',
  'gdpr_comments_ip' => 'on',
  'disable_emojis' => 'on',
  'disable_oembeds' => 'on',
  'dns_prefetching' => 'on',
  'rest_api' => 'on',
  'page_pingback' => 'on',
  'remove_dashicons' => 'on',
  'version_query_strings' => 'on',
  'remove_shortlink' => 'on',
  'preload_fonts' => 'off',
  'viewport_meta' => 'on',
  'support_center' => 'off',
  'tb_header_fix' => 'on',
  'tb_display_errors' => 'off',
  'logo_image_sizing' => 'on',
  'split_section_fix' => 'off',
  'disable_projects' => 'off',
  'stop_mail_updates' => 'on',
  'svg_support' => 'on',
  'webp_support' => 'on',
  'hyphens' => 'on',
  'mobile_menu_breakpoint' => 'on',
  'mobile_menu_fullscreen' => 'on',
  'font_list' => sanitize_textarea_field('/wp-content/themes/Divi/core/admin/fonts/modules.ttf'),
);
```

## v2.3.0

```php
$options = [
  'gdpr' => [
    'comments_external' => 'on',
    'comments_ip' => 'on',
    'disable_emojis' => 'on',
    'disable_oembeds' => 'on',
    'dns_prefetching' => 'on',
    'rest_api' => 'on'
  ],
  'page_speed' => [
    'remove_pingback' => 'on',
    'remove_dashicons' => 'on',
    'remove_version_strings' => 'on',
    'remove_shortlink' => 'on',
    'preload_fonts' => 'off',
    'preload_fonts_list' => sanitize_textarea_field('/wp-content/themes/Divi/core/admin/fonts/modules/all/modules.woff')
  ],
  'a11y' => [
    'fix_viewport' => 'on',
    'skip_link' => 'on',
    'scroll_top' => 'on',
    'focus_elements' => 'on',
    'nav_keyboard' => 'on',
    'external_links' => 'on',
    'optimize_forms' => 'on',
    'aria_support' => 'on',
    'fix_screenreader' => 'on',
    'underline_links' => 'on',
  ],
  'bug_fixes' => [
    'support_center' => 'off',
    'fixed_navigation' => 'off',
    'display_errors' => 'off',
    'logo_image_sizing' => 'off',
    'split_section' => 'off'
  ],
  'misc' => [
    'disable_projects' => 'off',
    'stop_mail_updates' => 'off',
    'svg_support' => 'on',
    'webp_support' => 'on',
    'hyphens' => 'on',
    'mobile_menu_breakpoint' => 'on',
    'mobile_menu_fullscreen' => 'on'
  ]
];
```

## v3.0.0

Die Options sind jetzt modul-basiert. Jedes Modul definiert seine Defaults in `$default_options`.
Neue Module (umami, system, login, localfonts, uikit) haben kein Mapping von alten Versionen - fehlende Keys bekommen automatisch ihre Defaults durch die Module-Initialisierung (`Module::init()`).

```php
// Gespeichert als WordPress Option: 'divi_child_options'
// Booleans sind jetzt native true/false statt 'on'/'off'
$options = [
  'privacy' => [           // ehem. 'gdpr'
    'enabled' => true,
    'comments_external' => true,
    'comments_ip' => true,
    'disable_emojis' => true,
    'disable_oembeds' => true,
    'dns_prefetching' => true,
    'rest_api' => true,
    'track_last_login' => false,
    'disable_author_archives' => false,
    'obfuscate_author_slugs' => false,
  ],
  'pagespeed' => [          // ehem. 'page_speed'
    'enabled' => true,
    'remove_pingback' => true,
    'remove_dashicons' => true,
    'remove_version_strings' => true,
    'remove_shortlink' => true,
    'preload_fonts' => false,
    'preload_fonts_list' => [  // Format geändert: war String, jetzt Array von Objekten
      ['path' => '/wp-content/themes/Divi/core/admin/fonts/modules/all/modules.woff']
    ],
  ],
  'a11y' => [               // Slug unverändert
    'enabled' => true,
    'aria_support' => true,
    'nav_keyboard' => true,
    'focus_elements' => true,
    'external_links' => true,
    'skip_link' => true,
    'scroll_top' => true,
    'fix_viewport' => true,
    'fix_screenreader' => true,
    'underline_links' => true,
    'optimize_forms' => true,
    'stop_animations' => true,
    'text_highlight_bg' => '#3399ff',
    'text_highlight_color' => '#ffffff',
    'slider_nav_spacing' => false,
  ],
  'bugs' => [                // ehem. 'bug_fixes'
    'enabled' => true,
    'support_center' => false,
    'fixed_navigation' => true,
    'header_height' => 80,
    'display_errors' => false,
    'logo_image_sizing' => false,
    'split_section' => false,
  ],
  'administration' => [      // ehem. 'misc'
    'enabled' => true,
    'duplicate_posts' => false,
    'disable_projects' => false,
    'stop_mail_updates' => false,
    'media_infinite_scroll' => false,
    'svg_support' => false,
    'webp_support' => false,
    'avif_support' => false,
    'hyphens' => false,
    'mobile_menu_breakpoint' => false,
    'mobile_menu_fullscreen' => false,
    'disable_divi_upsells' => false,
    'disable_divi_ai' => false,
    'duplicate_library' => false,
    'builder_default' => false,
    'builder_post_types' => [],
    'external_links_new_tab' => false,
    'external_links_rel' => 'noopener noreferrer nofollow',
  ],
  'umami' => [
    'enabled' => true,
    'umami_domain' => '',
    'website_id' => '',
    'ignore_logged_in' => true,
    'enable_events' => false,
    'events' => [],
  ],
  'system' => [
    'enabled' => false,
    'environment_badge' => false,
    'search_visibility_warning' => true,
    'status_panel' => false,
  ],
  'login' => [
    'enabled' => false,
    'login_site_identity' => false,
    'login_logo_width' => 120,
    'login_background_image' => 0,
  ],
  'localfonts' => [
    'enabled' => true,
    'disable_google_fonts' => true,
    'selected_fonts' => [],
    'font_display' => 'swap',
  ],
  'uikit' => [              // nur dev/local
    'enabled' => true,
  ],
];
```