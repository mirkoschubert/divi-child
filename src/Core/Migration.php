<?php

namespace DiviChild\Core;

class Migration
{
    protected $config;

    public function __construct()
    {
        $this->config = Config::get_instance();
    }

    /**
     * Führt Migration aus, falls erforderlich
     */
    public function run()
    {
        $current_version = $this->config->theme_version;
        $stored_version = get_option('divi_child_version', null);

        // Wenn keine Version gespeichert ist, bestimmen wir sie anhand der DB-Struktur
        if ($stored_version === null) {
            $stored_version = $this->detect_version_from_options();
        }

        if (version_compare($stored_version, $current_version, '<')) {
            $this->migrate($stored_version);
            update_option('divi_child_version', $current_version);
        }
    }

    /**
     * Erkennt die Version anhand der Optionsstruktur
     */
    protected function detect_version_from_options()
    {
        $options = get_option('divi_child_options', []);

        if (empty($options)) {
            return '0';
        }

        // v3.0.0: Umbenannte Module mit nativen Booleans
        if (isset($options['privacy']) && isset($options['pagespeed'])) {
            return '3.0.0';
        }

        // v2.3.0: Verschachtelte Arrays mit alten Modul-Namen
        if (isset($options['gdpr']) && isset($options['page_speed'])) {
            return '2.3.0';
        }

        // Vor 2.3.0: Flaches Array mit Präfixen
        if (isset($options['gdpr_comments_external'])) {
            return '2.0.0';
        }

        return '0';
    }

    /**
     * Führt die eigentliche Migration durch
     */
    protected function migrate($from_version)
    {
        if (version_compare($from_version, '2.3.0', '<')) {
            $this->migrate_pre_230();
        }

        if (version_compare($from_version, '3.0.0', '<')) {
            $this->migrate_pre_300();
        }
    }

    /**
     * Migration von vor 2.3.0 (flache Struktur -> v2.3.0 Modulstruktur)
     */
    protected function migrate_pre_230()
    {
        $options = get_option('divi_child_options', []);

        if (!isset($options['gdpr_comments_external'])) {
            return;
        }

        $new_options = [
            'gdpr' => [
                'comments_external' => $this->normalize_bool($options['gdpr_comments_external'] ?? 'on'),
                'comments_ip' => $this->normalize_bool($options['gdpr_comments_ip'] ?? 'on'),
                'disable_emojis' => $this->normalize_bool($options['disable_emojis'] ?? 'on'),
                'disable_oembeds' => $this->normalize_bool($options['disable_oembeds'] ?? 'on'),
                'dns_prefetching' => $this->normalize_bool($options['dns_prefetching'] ?? 'on'),
                'rest_api' => $this->normalize_bool($options['rest_api'] ?? 'on'),
            ],
            'page_speed' => [
                'remove_pingback' => $this->normalize_bool($options['page_pingback'] ?? 'on'),
                'remove_dashicons' => $this->normalize_bool($options['remove_dashicons'] ?? 'on'),
                'remove_version_strings' => $this->normalize_bool($options['version_query_strings'] ?? 'on'),
                'remove_shortlink' => $this->normalize_bool($options['remove_shortlink'] ?? 'on'),
                'preload_fonts' => $this->normalize_bool($options['preload_fonts'] ?? 'off'),
                'preload_fonts_list' => $options['font_list'] ?? '/wp-content/themes/Divi/core/admin/fonts/modules.ttf',
            ],
            'a11y' => [
                'fix_viewport' => $this->normalize_bool($options['viewport_meta'] ?? 'on'),
            ],
            'bug_fixes' => [
                'support_center' => $this->normalize_bool($options['support_center'] ?? 'off'),
                'fixed_navigation' => $this->normalize_bool($options['tb_header_fix'] ?? 'on'),
                'display_errors' => $this->normalize_bool($options['tb_display_errors'] ?? 'off'),
                'logo_image_sizing' => $this->normalize_bool($options['logo_image_sizing'] ?? 'on'),
                'split_section' => $this->normalize_bool($options['split_section_fix'] ?? 'off'),
            ],
            'misc' => [
                'disable_projects' => $this->normalize_bool($options['disable_projects'] ?? 'off'),
                'stop_mail_updates' => $this->normalize_bool($options['stop_mail_updates'] ?? 'on'),
                'svg_support' => $this->normalize_bool($options['svg_support'] ?? 'on'),
                'webp_support' => $this->normalize_bool($options['webp_support'] ?? 'on'),
                'hyphens' => $this->normalize_bool($options['hyphens'] ?? 'on'),
                'mobile_menu_breakpoint' => $this->normalize_bool($options['mobile_menu_breakpoint'] ?? 'on'),
                'mobile_menu_fullscreen' => $this->normalize_bool($options['mobile_menu_fullscreen'] ?? 'on'),
            ],
        ];

        update_option('divi_child_options', $new_options);
    }

    /**
     * Migration von 2.3.0 zu 3.0.0
     * - 'on'/'off' -> true/false
     * - 'enabled' hinzufügen
     * - Module umbenennen (gdpr->privacy, page_speed->pagespeed, bug_fixes->bugs, misc->administration)
     * - preload_fonts_list: String -> Array von Objekten
     */
    protected function migrate_pre_300()
    {
        $options = get_option('divi_child_options', []);

        if (empty($options)) {
            return;
        }

        // 1. Konvertiere 'on'/'off' zu true/false und füge 'enabled' hinzu
        foreach ($options as $module => $module_options) {
            if (!\is_array($module_options)) {
                continue;
            }

            if (!isset($module_options['enabled'])) {
                $options[$module]['enabled'] = true;
            }

            foreach ($module_options as $key => $value) {
                if ($value === 'on') {
                    $options[$module][$key] = true;
                } elseif ($value === 'off') {
                    $options[$module][$key] = false;
                }
            }
        }

        // 2. Module umbenennen
        $renames = [
            'gdpr' => 'privacy',
            'page_speed' => 'pagespeed',
            'bug_fixes' => 'bugs',
            'misc' => 'administration',
        ];

        foreach ($renames as $old_slug => $new_slug) {
            if (isset($options[$old_slug])) {
                $options[$new_slug] = $options[$old_slug];
                unset($options[$old_slug]);
            }
        }

        // 3. preload_fonts_list: String -> Array von Objekten
        $pagespeed_key = isset($options['pagespeed']) ? 'pagespeed' : (isset($options['page_speed']) ? 'page_speed' : null);
        if ($pagespeed_key && isset($options[$pagespeed_key]['preload_fonts_list'])) {
            $list = $options[$pagespeed_key]['preload_fonts_list'];
            if (\is_string($list)) {
                $path = \trim($list);
                $options[$pagespeed_key]['preload_fonts_list'] = !empty($path) ? [['path' => $path]] : [];
            }
        }

        update_option('divi_child_options', $options);
    }

    /**
     * Normalisiert einen boolean-Wert
     */
    private function normalize_bool($value)
    {
        if ($value === 'on' || $value === '1' || $value === true) {
            return true;
        }
        return false;
    }
}
