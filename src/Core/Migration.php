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
        
        // Prüfen wir die Struktur
        if (empty($options)) {
            return '0'; // Keine Optionen gefunden
        }
        
        // Vor 2.3.0: Flaches Array mit Präfixen
        if (isset($options['gdpr_comments_external'])) {
            return '2.0.0';
        }
        
        // Ab 2.3.0: Verschachtelte Arrays mit Modulen
        if (isset($options['gdpr']) && isset($options['page_speed'])) {
            return '2.3.0';
        }
        
        // Wenn keine bekannte Struktur erkannt wird
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

        if (version_compare($from_version, '3.0.1', '<')) {
            $this->migrate_misc_to_administration();
        }
    }
    
    /**
     * Migration von vor 2.3.0
     */
    protected function migrate_pre_230()
    {
        $options = get_option('divi_child_options', []);
        
        // Prä-2.3.0 hatte flache Struktur mit Präfixen
        if (isset($options['gdpr_comments_external'])) {
            $new_options = [
                'gdpr' => [
                    'enabled' => true,
                    'comments_external' => $this->normalize_bool($options['gdpr_comments_external']),
                    'comments_ip' => $this->normalize_bool($options['gdpr_comments_ip']),
                    'disable_emojis' => $this->normalize_bool($options['disable_emojis']),
                    'disable_oembeds' => $this->normalize_bool($options['disable_oembeds']),
                    'dns_prefetching' => $this->normalize_bool($options['dns_prefetching']),
                    'rest_api' => $this->normalize_bool($options['rest_api'])
                ],
                // Weitere Module konvertieren...
            ];
            
            update_option('divi_child_options', $new_options);
        }
    }
    
    /**
     * Migration von 2.3.0 zu 3.0.0 
     */
    protected function migrate_pre_300()
    {
        $options = get_option('divi_child_options', []);
        
        // Ab 2.3.0 gab es bereits Module, aber 'on'/'off' statt true/false
        if (isset($options['gdpr'])) {
            foreach ($options as $module => $module_options) {
                // 'enabled' als true hinzufügen, wenn nicht existiert
                if (!isset($module_options['enabled'])) {
                    $options[$module]['enabled'] = true;
                }
                
                // Konvertiere 'on'/'off' zu true/false
                foreach ($module_options as $key => $value) {
                    if ($value === 'on') {
                        $options[$module][$key] = true;
                    } elseif ($value === 'off') {
                        $options[$module][$key] = false;
                    }
                }
            }
            
            update_option('divi_child_options', $options);
        }
    }
    
    /**
     * Migration: Rename module slug from 'misc' to 'administration'
     */
    protected function migrate_misc_to_administration()
    {
        $options = get_option('divi_child_options', []);

        if (isset($options['misc'])) {
            $options['administration'] = $options['misc'];
            unset($options['misc']);
            update_option('divi_child_options', $options);
        }
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