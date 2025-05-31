<?php

namespace DiviChild\Admin;

use DiviChild\Core\Config;
use DiviChild\Core\Abstracts\Module;
use DiviChild\Admin\UI;

class AdminAjax
{
  protected $config;

  public function __construct()
  {
    $this->config = new Config();
    
    // AJAX-Handler registrieren
    add_action('wp_ajax_dvc_load_module_settings', [$this, 'ajax_load_module_settings']);
    add_action('wp_ajax_dvc_save_module_settings', [$this, 'ajax_save_module_settings']);
    add_action('wp_ajax_dvc_toggle_module', [$this, 'ajax_toggle_module']);
  }
  
  /**
   * AJAX-Handler zum Laden der Moduleinstellungen
   * @return void
   * @since 3.0.0
   */
  public function ajax_load_module_settings()
  {
    // Sicherheitscheck
    check_ajax_referer('dvc_ajax_nonce', 'nonce');
    
    // Parameter prüfen
    if (empty($_POST['module'])) {
      wp_send_json_error(['message' => 'Modul nicht gefunden.']);
      return;
    }
    
    $module_slug = sanitize_text_field($_POST['module']);
    
    // Alle Module holen
    $modules = Module::get_all_modules();
    
    // Prüfen, ob das Modul existiert
    if (!isset($modules[$module_slug])) {
      wp_send_json_error(['message' => 'Modul nicht gefunden.']);
      return;
    }
    
    $module = $modules[$module_slug];
    
    // Moduleinstellungen und aktuelle Werte holen
    $settings = $module->admin_settings();
    $values = $module->get_options();
    
    //error_log("ajax_load_module_settings für {$module_slug}: Einstellungen: " . print_r($settings, true));
    //error_log("ajax_load_module_settings für {$module_slug}: Werte: " . print_r($values, true));
    
    // UI-Klasse instanziieren
    $ui = new UI();
    
    // Formular rendern
    ob_start();
    $ui->render_module_settings_form($module_slug, $settings, $values);
    $form = ob_get_clean();
    
    // Validierungsmeldungen für alle Felder extrahieren
    $validation_messages = [];
    foreach ($settings as $field_id => $field_config) {
      if (isset($field_config['validate']) && isset($field_config['validate']['error_message'])) {
        $validation_messages[$field_id] = $field_config['validate']['error_message'];
      }
    }
    
    // Debug-Ausgabe
    //error_log("ajax_load_module_settings für {$module_slug}: Validierungsmeldungen: " . print_r($validation_messages, true));
    
    // Antwort zurückgeben
    wp_send_json_success([
      'title' => $module->get_name() . ' ' . __('Settings', 'divi-child'),
      'form' => $form,
      'values' => $values,
      'validation_messages' => $validation_messages
    ]);
  }
  
  /**
   * AJAX-Handler zum Speichern der Moduleinstellungen
   * @return void
   * @since 3.0.0
   */
  public function ajax_save_module_settings()
  {
    // Sicherheitscheck
    check_ajax_referer('dvc_ajax_nonce', 'nonce');
    
    // Parameter prüfen
    if (empty($_POST['slug'])) {
      wp_send_json_error(['message' => 'Kein Modul angegeben.']);
      return;
    }
    
    $module_slug = sanitize_text_field($_POST['slug']);
    
    // Settings können leer sein, wenn nichts geändert wurde
    $settings = $_POST['settings'] ?? [];
    error_log("AJAX Rohdaten: " . print_r($_POST['settings'], true));
    
    // Alle Module holen
    $modules = Module::get_all_modules();
    
    // Prüfen, ob das Modul existiert
    if (!isset($modules[$module_slug])) {
      wp_send_json_error(['message' => 'Modul nicht gefunden.']);
      return;
    }
    
    $module = $modules[$module_slug];
    
    // Bestehende Optionen holen
    $existing_options = $this->config->get_module_options($module_slug);
    
    // Wenn keine Einstellungen übergeben wurden, verwende bestehende
    $sanitized = empty($settings) ? $existing_options : $module->sanitize_options($settings);
    
    // Erhalte den enabled-Status aus den bestehenden Optionen
    if (isset($existing_options['enabled'])) {
      $sanitized['enabled'] = $existing_options['enabled'];
    }
    
    // Debug-Logging
    //error_log("ajax_save_module_settings für {$module_slug}: Sanitierte Optionen: " . print_r($sanitized, true));
    
    // Speichern
    $result = $this->config->save_module_options($module_slug, $sanitized);
    
    if ($result) {
      wp_send_json_success(['message' => 'Einstellungen gespeichert.']);
    } else {
      wp_send_json_error(['message' => 'Fehler beim Speichern.']);
    }
  }
  
  /**
   * AJAX-Handler zum Aktivieren/Deaktivieren eines Moduls
   * @return void
   * @since 3.0.0
   */
  public function ajax_toggle_module()
  {
    // Sicherheitscheck
    check_ajax_referer('dvc_ajax_nonce', 'nonce');
    
    // Parameter prüfen
    if (empty($_POST['slug']) || !isset($_POST['enabled'])) {
      wp_send_json_error(['message' => 'Ungültige Parameter.']);
      return;
    }
    
    $module_slug = sanitize_text_field($_POST['slug']);
    $enabled = filter_var($_POST['enabled'], FILTER_VALIDATE_BOOLEAN);
    
    // Einstellungen holen
    $options = $this->config->get_module_options($module_slug);
    
    // Enabled-Status aktualisieren
    $options['enabled'] = $enabled;
    
    // Speichern
    $result = $this->config->save_module_options($module_slug, $options);
    
    if ($result) {
      wp_send_json_success(['message' => $enabled ? 'Modul aktiviert.' : 'Modul deaktiviert.']);
    } else {
      wp_send_json_error(['message' => 'Fehler beim Speichern der Einstellungen.']);
    }
  }
}

