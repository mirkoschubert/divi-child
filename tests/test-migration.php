<?php
/**
 * Divi Child Theme - Migration Test Script
 *
 * Dieses Skript testet die Migration offline, ohne WordPress-Datenbank.
 * Es simuliert get_option/update_option und prüft alle Migrationspfade.
 *
 * Ausführen: php tests/test-migration.php
 */

// ============================================================
// Mock: WordPress-Funktionen simulieren
// ============================================================

$mock_options = [];

function get_option($key, $default = false) {
    global $mock_options;
    return $mock_options[$key] ?? $default;
}

function update_option($key, $value) {
    global $mock_options;
    $mock_options[$key] = $value;
    return true;
}

function delete_option($key) {
    global $mock_options;
    unset($mock_options[$key]);
}

// ============================================================
// Migration-Klasse laden (ohne Namespace/Autoloader)
// ============================================================

class TestMigration
{
    public $theme_version = '3.0.0';

    public function run()
    {
        $current_version = $this->theme_version;
        $stored_version = get_option('divi_child_version', null);

        if ($stored_version === null) {
            $stored_version = $this->detect_version_from_options();
        }

        if (version_compare($stored_version, $current_version, '<')) {
            $this->migrate($stored_version);
            update_option('divi_child_version', $current_version);
        }
    }

    public function detect_version_from_options()
    {
        $options = get_option('divi_child_options', []);

        if (empty($options)) {
            return '0';
        }

        if (isset($options['privacy']) && isset($options['pagespeed'])) {
            return '3.0.0';
        }

        if (isset($options['gdpr']) && isset($options['page_speed'])) {
            return '2.3.0';
        }

        if (isset($options['gdpr_comments_external'])) {
            return '2.0.0';
        }

        return '0';
    }

    protected function migrate($from_version)
    {
        if (version_compare($from_version, '2.3.0', '<')) {
            $this->migrate_pre_230();
        }

        if (version_compare($from_version, '3.0.0', '<')) {
            $this->migrate_pre_300();
        }
    }

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

    protected function migrate_pre_300()
    {
        $options = get_option('divi_child_options', []);

        if (empty($options)) {
            return;
        }

        foreach ($options as $module => $module_options) {
            if (!is_array($module_options)) {
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

        $pagespeed_key = isset($options['pagespeed']) ? 'pagespeed' : (isset($options['page_speed']) ? 'page_speed' : null);
        if ($pagespeed_key && isset($options[$pagespeed_key]['preload_fonts_list'])) {
            $list = $options[$pagespeed_key]['preload_fonts_list'];
            if (is_string($list)) {
                $path = trim($list);
                $options[$pagespeed_key]['preload_fonts_list'] = !empty($path) ? [['path' => $path]] : [];
            }
        }

        update_option('divi_child_options', $options);
    }

    private function normalize_bool($value)
    {
        if ($value === 'on' || $value === '1' || $value === true) {
            return true;
        }
        return false;
    }
}

// ============================================================
// Test-Framework
// ============================================================

$test_count = 0;
$pass_count = 0;
$fail_count = 0;
$failures = [];

function reset_db() {
    global $mock_options;
    $mock_options = [];
}

function assert_eq($actual, $expected, $message) {
    global $test_count, $pass_count, $fail_count, $failures;
    $test_count++;

    if ($actual === $expected) {
        $pass_count++;
        echo "  \033[32m✓\033[0m {$message}\n";
    } else {
        $fail_count++;
        $failures[] = $message;
        echo "  \033[31m✗\033[0m {$message}\n";
        echo "    Erwartet: " . var_export($expected, true) . "\n";
        echo "    Erhalten: " . var_export($actual, true) . "\n";
    }
}

function assert_true($actual, $message) {
    assert_eq($actual, true, $message);
}

function assert_false($actual, $message) {
    assert_eq($actual, false, $message);
}

function assert_key_exists($array, $key, $message) {
    global $test_count, $pass_count, $fail_count, $failures;
    $test_count++;

    if (isset($array[$key]) || array_key_exists($key, $array)) {
        $pass_count++;
        echo "  \033[32m✓\033[0m {$message}\n";
    } else {
        $fail_count++;
        $failures[] = $message;
        echo "  \033[31m✗\033[0m {$message}\n";
        echo "    Key '{$key}' nicht gefunden. Vorhandene Keys: " . implode(', ', array_keys($array)) . "\n";
    }
}

function assert_key_not_exists($array, $key, $message) {
    global $test_count, $pass_count, $fail_count, $failures;
    $test_count++;

    if (!isset($array[$key]) && !array_key_exists($key, $array)) {
        $pass_count++;
        echo "  \033[32m✓\033[0m {$message}\n";
    } else {
        $fail_count++;
        $failures[] = $message;
        echo "  \033[31m✗\033[0m {$message}\n";
        echo "    Key '{$key}' sollte nicht existieren, ist aber vorhanden.\n";
    }
}

// ============================================================
// Test 1: Versionserkennung
// ============================================================

echo "\n\033[1m=== Test 1: Versionserkennung ===\033[0m\n";

$migration = new TestMigration();

// Leere DB
reset_db();
assert_eq($migration->detect_version_from_options(), '0', 'Leere DB → Version 0');

// v2.2.0 Flat-Struktur
reset_db();
update_option('divi_child_options', [
    'gdpr_comments_external' => 'on',
    'disable_emojis' => 'on',
]);
assert_eq($migration->detect_version_from_options(), '2.0.0', 'Flat-Struktur → Version 2.0.0');

// v2.3.0 Modul-Struktur
reset_db();
update_option('divi_child_options', [
    'gdpr' => ['comments_external' => 'on'],
    'page_speed' => ['remove_pingback' => 'on'],
]);
assert_eq($migration->detect_version_from_options(), '2.3.0', 'gdpr + page_speed → Version 2.3.0');

// v3.0.0 Struktur
reset_db();
update_option('divi_child_options', [
    'privacy' => ['enabled' => true, 'comments_external' => true],
    'pagespeed' => ['enabled' => true, 'remove_pingback' => true],
]);
assert_eq($migration->detect_version_from_options(), '3.0.0', 'privacy + pagespeed → Version 3.0.0');

// ============================================================
// Test 2: Migration v2.2.0 → v3.0.0
// ============================================================

echo "\n\033[1m=== Test 2: Migration v2.2.0 → v3.0.0 (komplette Kette) ===\033[0m\n";

reset_db();
update_option('divi_child_options', [
    'gdpr_comments_external' => 'on',
    'gdpr_comments_ip' => 'on',
    'disable_emojis' => 'on',
    'disable_oembeds' => 'off',
    'dns_prefetching' => 'on',
    'rest_api' => 'on',
    'page_pingback' => 'on',
    'remove_dashicons' => 'on',
    'version_query_strings' => 'on',
    'remove_shortlink' => 'on',
    'preload_fonts' => 'off',
    'font_list' => '/wp-content/themes/Divi/core/admin/fonts/modules.ttf',
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
]);

$migration->run();
$result = get_option('divi_child_options');

// Alte Module-Keys dürfen nicht mehr existieren
assert_key_not_exists($result, 'gdpr', 'Alter Key "gdpr" entfernt');
assert_key_not_exists($result, 'page_speed', 'Alter Key "page_speed" entfernt');
assert_key_not_exists($result, 'bug_fixes', 'Alter Key "bug_fixes" entfernt');
assert_key_not_exists($result, 'misc', 'Alter Key "misc" entfernt');
assert_key_not_exists($result, 'gdpr_comments_external', 'Alter Flat-Key "gdpr_comments_external" entfernt');

// Neue Module-Keys müssen existieren
assert_key_exists($result, 'privacy', 'Neuer Key "privacy" vorhanden');
assert_key_exists($result, 'pagespeed', 'Neuer Key "pagespeed" vorhanden');
assert_key_exists($result, 'a11y', 'Neuer Key "a11y" vorhanden');
assert_key_exists($result, 'bugs', 'Neuer Key "bugs" vorhanden');
assert_key_exists($result, 'administration', 'Neuer Key "administration" vorhanden');

// Privacy: Werte prüfen
assert_true($result['privacy']['enabled'], 'privacy.enabled = true');
assert_true($result['privacy']['comments_external'], 'privacy.comments_external = true (war "on")');
assert_false($result['privacy']['disable_oembeds'], 'privacy.disable_oembeds = false (war "off")');

// Pagespeed: Werte prüfen
assert_true($result['pagespeed']['enabled'], 'pagespeed.enabled = true');
assert_true($result['pagespeed']['remove_pingback'], 'pagespeed.remove_pingback = true');
assert_false($result['pagespeed']['preload_fonts'], 'pagespeed.preload_fonts = false');

// Pagespeed: preload_fonts_list Format
assert_eq(
    $result['pagespeed']['preload_fonts_list'],
    [['path' => '/wp-content/themes/Divi/core/admin/fonts/modules.ttf']],
    'pagespeed.preload_fonts_list → Array von Objekten'
);

// A11y: nur fix_viewport aus v2.2.0, Rest fehlt (bekommt Defaults via Module::init())
assert_true($result['a11y']['enabled'], 'a11y.enabled = true');
assert_true($result['a11y']['fix_viewport'], 'a11y.fix_viewport = true (war viewport_meta "on")');

// Bugs
assert_true($result['bugs']['enabled'], 'bugs.enabled = true');
assert_false($result['bugs']['support_center'], 'bugs.support_center = false (war "off")');
assert_true($result['bugs']['fixed_navigation'], 'bugs.fixed_navigation = true (war tb_header_fix "on")');
assert_false($result['bugs']['display_errors'], 'bugs.display_errors = false (war tb_display_errors "off")');
assert_true($result['bugs']['logo_image_sizing'], 'bugs.logo_image_sizing = true (war "on")');
assert_false($result['bugs']['split_section'], 'bugs.split_section = false (war split_section_fix "off")');

// Administration
assert_true($result['administration']['enabled'], 'administration.enabled = true');
assert_false($result['administration']['disable_projects'], 'administration.disable_projects = false');
assert_true($result['administration']['stop_mail_updates'], 'administration.stop_mail_updates = true');
assert_true($result['administration']['svg_support'], 'administration.svg_support = true');
assert_true($result['administration']['hyphens'], 'administration.hyphens = true');

// Version gesetzt
assert_eq(get_option('divi_child_version'), '3.0.0', 'divi_child_version = 3.0.0');

// ============================================================
// Test 3: Migration v2.3.0 → v3.0.0
// ============================================================

echo "\n\033[1m=== Test 3: Migration v2.3.0 → v3.0.0 ===\033[0m\n";

reset_db();
update_option('divi_child_options', [
    'gdpr' => [
        'comments_external' => 'on',
        'comments_ip' => 'on',
        'disable_emojis' => 'on',
        'disable_oembeds' => 'on',
        'dns_prefetching' => 'on',
        'rest_api' => 'off',
    ],
    'page_speed' => [
        'remove_pingback' => 'on',
        'remove_dashicons' => 'on',
        'remove_version_strings' => 'on',
        'remove_shortlink' => 'on',
        'preload_fonts' => 'off',
        'preload_fonts_list' => '/wp-content/themes/Divi/core/admin/fonts/modules/all/modules.woff',
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
        'underline_links' => 'off',
    ],
    'bug_fixes' => [
        'support_center' => 'off',
        'fixed_navigation' => 'on',
        'display_errors' => 'off',
        'logo_image_sizing' => 'off',
        'split_section' => 'off',
    ],
    'misc' => [
        'disable_projects' => 'off',
        'stop_mail_updates' => 'off',
        'svg_support' => 'on',
        'webp_support' => 'on',
        'hyphens' => 'on',
        'mobile_menu_breakpoint' => 'on',
        'mobile_menu_fullscreen' => 'off',
    ],
]);

$migration->run();
$result = get_option('divi_child_options');

// Module umbenannt
assert_key_exists($result, 'privacy', 'gdpr → privacy');
assert_key_exists($result, 'pagespeed', 'page_speed → pagespeed');
assert_key_exists($result, 'bugs', 'bug_fixes → bugs');
assert_key_exists($result, 'administration', 'misc → administration');
assert_key_not_exists($result, 'gdpr', 'Alter Key "gdpr" entfernt');
assert_key_not_exists($result, 'page_speed', 'Alter Key "page_speed" entfernt');
assert_key_not_exists($result, 'bug_fixes', 'Alter Key "bug_fixes" entfernt');
assert_key_not_exists($result, 'misc', 'Alter Key "misc" entfernt');

// Boolean-Konvertierung
assert_true($result['privacy']['comments_external'], 'privacy.comments_external: "on" → true');
assert_false($result['privacy']['rest_api'], 'privacy.rest_api: "off" → false');
assert_false($result['a11y']['underline_links'], 'a11y.underline_links: "off" → false');
assert_true($result['bugs']['fixed_navigation'], 'bugs.fixed_navigation: "on" → true');
assert_false($result['administration']['mobile_menu_fullscreen'], 'administration.mobile_menu_fullscreen: "off" → false');

// enabled hinzugefügt
assert_true($result['privacy']['enabled'], 'privacy.enabled hinzugefügt');
assert_true($result['pagespeed']['enabled'], 'pagespeed.enabled hinzugefügt');
assert_true($result['a11y']['enabled'], 'a11y.enabled hinzugefügt');
assert_true($result['bugs']['enabled'], 'bugs.enabled hinzugefügt');
assert_true($result['administration']['enabled'], 'administration.enabled hinzugefügt');

// preload_fonts_list Format
assert_eq(
    $result['pagespeed']['preload_fonts_list'],
    [['path' => '/wp-content/themes/Divi/core/admin/fonts/modules/all/modules.woff']],
    'pagespeed.preload_fonts_list: String → Array von Objekten'
);

// Version gesetzt
assert_eq(get_option('divi_child_version'), '3.0.0', 'divi_child_version = 3.0.0');

// ============================================================
// Test 4: Bereits v3.0.0 - keine Änderung (Idempotenz)
// ============================================================

echo "\n\033[1m=== Test 4: Bereits v3.0.0 → keine Änderung ===\033[0m\n";

reset_db();
$v300_options = [
    'privacy' => [
        'enabled' => true,
        'comments_external' => true,
        'rest_api' => false,
    ],
    'pagespeed' => [
        'enabled' => true,
        'remove_pingback' => true,
        'preload_fonts_list' => [
            ['path' => '/wp-content/themes/Divi/core/admin/fonts/modules/all/modules.woff']
        ],
    ],
    'a11y' => ['enabled' => true, 'fix_viewport' => true],
    'bugs' => ['enabled' => true, 'support_center' => false],
    'administration' => ['enabled' => true, 'svg_support' => true],
];
update_option('divi_child_options', $v300_options);
update_option('divi_child_version', '3.0.0');

$migration->run();
$result = get_option('divi_child_options');

assert_eq($result, $v300_options, 'v3.0.0 Options bleiben unverändert');
assert_eq(get_option('divi_child_version'), '3.0.0', 'Version bleibt 3.0.0');

// ============================================================
// Test 5: Neuinstallation (leere DB)
// ============================================================

echo "\n\033[1m=== Test 5: Neuinstallation (leere DB) ===\033[0m\n";

reset_db();

$migration->run();
$result = get_option('divi_child_options', []);

assert_eq($result, [], 'Leere DB → keine Options erzeugt (Defaults kommen von Module::init())');
assert_eq(get_option('divi_child_version'), '3.0.0', 'divi_child_version wird trotzdem gesetzt');

// ============================================================
// Test 6: preload_fonts_list Sonderfälle
// ============================================================

echo "\n\033[1m=== Test 6: preload_fonts_list Sonderfälle ===\033[0m\n";

// Leerer String
reset_db();
update_option('divi_child_options', [
    'gdpr' => ['comments_external' => 'on'],
    'page_speed' => ['preload_fonts_list' => ''],
]);
$migration->run();
$result = get_option('divi_child_options');
assert_eq($result['pagespeed']['preload_fonts_list'], [], 'Leerer String → leeres Array');

// Bereits Array (nach migrate_pre_230 + migrate_pre_300)
reset_db();
update_option('divi_child_options', [
    'gdpr' => ['comments_external' => 'on'],
    'page_speed' => ['preload_fonts_list' => [['path' => '/test.woff']]],
]);
$migration->run();
$result = get_option('divi_child_options');
assert_eq(
    $result['pagespeed']['preload_fonts_list'],
    [['path' => '/test.woff']],
    'Bereits Array → bleibt unverändert'
);

// ============================================================
// Test 7: v2.2.0 mit fehlenden Keys (Robustheit)
// ============================================================

echo "\n\033[1m=== Test 7: v2.2.0 mit fehlenden Keys ===\033[0m\n";

reset_db();
// Minimale v2.2.0 Struktur - nur ein paar Keys
update_option('divi_child_options', [
    'gdpr_comments_external' => 'on',
    'gdpr_comments_ip' => 'off',
    // Rest fehlt → bekommt Fallback-Defaults via ??
]);

$migration->run();
$result = get_option('divi_child_options');

assert_key_exists($result, 'privacy', 'privacy existiert auch bei fehlenden Keys');
assert_true($result['privacy']['comments_external'], 'Vorhandener Key korrekt: comments_external = true');
assert_false($result['privacy']['comments_ip'], 'Vorhandener Key korrekt: comments_ip = false');
assert_true($result['privacy']['disable_emojis'], 'Fehlender Key → Default: disable_emojis = true');
assert_key_exists($result, 'pagespeed', 'pagespeed existiert');
assert_key_exists($result, 'bugs', 'bugs existiert');
assert_key_exists($result, 'administration', 'administration existiert');

// ============================================================
// Ergebnis
// ============================================================

echo "\n\033[1m========================================\033[0m\n";
if ($fail_count === 0) {
    echo "\033[32m✓ Alle {$test_count} Tests bestanden!\033[0m\n";
} else {
    echo "\033[31m✗ {$fail_count} von {$test_count} Tests fehlgeschlagen:\033[0m\n";
    foreach ($failures as $f) {
        echo "  - {$f}\n";
    }
}
echo "\033[1m========================================\033[0m\n\n";

exit($fail_count > 0 ? 1 : 0);
