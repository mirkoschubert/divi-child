<?php

namespace DiviChild\Core\Abstracts;

use DiviChild\Core\Interfaces\ModuleInterface;
use DiviChild\Core\Config;
use DiviChild\Core\Traits\DependencyChecker;

abstract class Module implements ModuleInterface
{
  use DependencyChecker;

  protected $enabled = true;
  protected $name = '';
  protected $description = '';
  protected $author = '';
  protected $version = '';
  protected $slug = '';
  protected $dependencies = [
    'jquery',
  ];
  protected $config;
  protected $options;
  protected $default_options = [
    'enabled' => true,
  ];
  private static $modules = [];

  protected $frontend_service;
  protected $admin_service;
  protected $common_service;
  protected $rest_controller;

  public function __construct()
  {
    $this->init();

    if (!empty($this->slug)) {
      self::$modules[$this->slug] = $this;
    }
  }

  /**
   * Initialisiert das Modul
   * @return void
   * @since 3.0.0
   */
  public function init()
  {
    if (empty($this->name)) {
      $this->name = get_class($this);
      $this->name = str_replace('DiviChild\\Modules\\', '', $this->name);
    }
    $this->author = empty($this->author) ? 'Mirko Schubert' : $this->author;
    $this->version = empty($this->version) ? '1.0.0' : $this->version;
    $this->slug = empty($this->slug) ? strtolower($this->name) : $this->slug;

    $this->config = new Config();
    $this->options = $this->config->get_module_options($this->slug);

    // Stellen sicher, dass options ein Array ist
    if (!is_array($this->options)) {
      $this->options = [];
    }

    // Füge fehlende Optionen aus den Standardwerten hinzu
    if (!empty($this->default_options) && is_array($this->default_options)) {
      foreach ($this->default_options as $key => $default_value) {
        if (!array_key_exists($key, $this->options)) {
          $this->options[$key] = $default_value;
        }
      }
    }

    // Überprüfen, ob das Modul aktiviert ist
    if (!$this->is_enabled()) {
      return;
    }

    // Automatisch Services initialisieren, wenn sie existieren
    $this->init_services();

    // Initialize REST Controller
    add_action('rest_api_init', [$this, 'init_rest_controller'], 5);

    // Assets laden
    if (is_admin()) {
      add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    } else {
      add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
  }

  /**
   * Initializes the module services
   * @return void
   * @since 3.0.0
   */
  public function init_services()
  {
    // Verwende Reflection, um den tatsächlichen Verzeichnisnamen zu ermitteln
    $reflection = new \ReflectionClass($this);
    $module_dir = basename(dirname($reflection->getFileName()));

    // Frontend-Service-Initialisierung
    if (!is_admin()) {
      $possible_namespaces = [
        "DiviChild\\Modules\\{$module_dir}\\Services\\FrontendService",
        "DiviChild\\Modules\\{$module_dir}\\FrontendService"
      ];

      foreach ($possible_namespaces as $class_name) {
        if (class_exists($class_name)) {
          $this->frontend_service = new $class_name($this);

          if (method_exists($this->frontend_service, 'init_frontend')) {
            $this->frontend_service->init_frontend();
          }

          break;
        }
      }
    }

    // Admin-Service-Initialisierung
    if (is_admin()) {
      $possible_namespaces = [
        "DiviChild\\Modules\\{$module_dir}\\Services\\AdminService",
        "DiviChild\\Modules\\{$module_dir}\\AdminService"
      ];

      foreach ($possible_namespaces as $class_name) {
        if (class_exists($class_name)) {
          $this->admin_service = new $class_name($this);

          if (method_exists($this->admin_service, 'init_admin')) {
            $this->admin_service->init_admin();
          }

          break;
        }
      }
    }

    // Gemeinsamer Service für beide (falls vorhanden)
    $possible_namespaces = [
      "DiviChild\\Modules\\{$module_dir}\\Services\\CommonService",
      "DiviChild\\Modules\\{$module_dir}\\CommonService"
    ];

    foreach ($possible_namespaces as $class_name) {
      if (class_exists($class_name)) {
        $this->common_service = new $class_name($this);

        if (method_exists($this->common_service, 'init_common')) {
          $this->common_service->init_common();
        }

        break;
      }
    }
  }

  /**
   * Initialisiert den REST Controller falls vorhanden
   * @return void
   * @since 3.0.0
   */
  public function init_rest_controller()
  {
    if ($this->rest_controller !== null) {
      error_log("REST Controller für {$this->slug} bereits initialisiert");
      return;
    }
    $reflection = new \ReflectionClass($this);
    $module_dir = basename(dirname($reflection->getFileName()));

    $possible_namespaces = [
      "DiviChild\\Modules\\{$module_dir}\\API\\RestController",
      "DiviChild\\Modules\\{$module_dir}\\RestController"
    ];

    foreach ($possible_namespaces as $class_name) {
      if (class_exists($class_name)) {
        $this->rest_controller = new $class_name($this);
        if (method_exists($this->rest_controller, 'register_routes')) {
          $this->rest_controller->register_routes();
        }
        break;
      }
    }
  }

  /**
   * Gibt den REST Controller zurück
   * @return mixed|null
   */
  public function get_rest_controller()
  {
    return $this->rest_controller ?? null;
  }

  /**
   * Module activation hook
   * @return void
   * @since 3.0.0
   */
  public function activate()
  {
    $this->config->set_option($this->slug, 'enabled', true);
  }


  /**
   * Module deactivation hook
   * @return void
   * @since 3.0.0
   */
  public function deactivate()
  {
    $this->config->set_option($this->slug, 'enabled', false);
  }

  public function uninstall()
  {
    $this->config->delete_module_options($this->slug);
  }


  /**
   * Enqueues module scripts and styles
   * @return void
   * @since 3.0.0
   */
  public function enqueue_scripts()
  {
    // Ermittle den tatsächlichen Verzeichnisnamen des Moduls
    $reflection = new \ReflectionClass($this);
    $file_path = $reflection->getFileName();
    $module_dir = basename(dirname($file_path));

    $css_file = "{$this->config->theme_dir}/modules/{$module_dir}/assets/css/{$this->slug}.css";
    $js_file = "{$this->config->theme_dir}/modules/{$module_dir}/assets/js/{$this->slug}.js";

    // Nur CSS enqueuen, wenn die Datei existiert
    if (file_exists($css_file)) {
      wp_enqueue_style("divi-child-{$this->slug}-style", "{$this->config->theme_url}/modules/{$module_dir}/assets/css/{$this->slug}.css");
    }

    // Nur JS enqueuen, wenn die Datei existiert
    if (file_exists($js_file)) {
      wp_enqueue_script("divi-child-{$this->slug}-script", "{$this->config->theme_url}/modules/{$module_dir}/assets/js/{$this->slug}.js", ['jquery'], null, true);

      // Nur wenn JS vorhanden ist, dafür auch die AJAX-URL bereitstellen
      wp_localize_script("divi-child-{$this->slug}-script", 'dvc_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
    }
  }


  /**
   * Enqueues module admin scripts
   * @return void
   * @since 3.0.0
   */
  public function enqueue_admin_scripts()
  {
    $admin_css_file = "{$this->config->theme_dir}/modules/{$this->name}/assets/css/{$this->slug}-admin.css";
    $admin_js_file = "{$this->config->theme_dir}/modules/{$this->name}/assets/js/{$this->slug}-admin.js";

    // Nur Admin-CSS enqueuen, wenn die Datei existiert
    if (file_exists($admin_css_file)) {
      wp_enqueue_style("divi-child-{$this->slug}-admin-style", "{$this->config->theme_url}/modules/{$this->name}/assets/css/{$this->slug}-admin.css");
    }

    // Nur Admin-JS enqueuen, wenn die Datei existiert
    if (file_exists($admin_js_file)) {
      wp_enqueue_script("divi-child-{$this->slug}-admin-script", "{$this->config->theme_url}/modules/{$this->name}/assets/js/{$this->slug}-admin.js", ['jquery'], null, true);

      // Nur wenn JS vorhanden ist, dafür auch die AJAX-URL bereitstellen
      wp_localize_script("divi-child-{$this->slug}-admin-script", 'dvc_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
    }
  }


  /**
   * Sanitiert Optionen basierend auf default_options
   * @param array $options Die zu sanitierenden Optionen
   * @return array Sanitierte Optionen
   * @since 3.0.0
   */
  public function sanitize_options($options)
  {
    $sanitized = [];
    $admin_settings = $this->admin_settings();

    // PHP-Formular-Arrays korrigieren
    foreach ($options as $key => $value) {
      // Array-Notation korrigieren (z.B. "events[0" -> "events")
      if (preg_match('/^(\w+)\[(\d+)$/', $key, $matches)) {
        $real_key = $matches[1];
        $index = intval($matches[2]);

        if (!isset($sanitized[$real_key])) {
          $sanitized[$real_key] = [];
        }

        $sanitized[$real_key][$index] = $value;
        continue;
      }

      // Normale Felder verarbeiten
      $sanitized[$key] = $value;
    }

    // Jetzt reguläre Sanitierung durchführen
    foreach ($options as $key => $value) {
      // Für Listen-Felder
      if (isset($admin_settings[$key]) && $admin_settings[$key]['type'] === 'list') {
        $sanitized[$key] = $this->sanitize_list_field($key, $value, $admin_settings[$key]);
        continue;
      }

      // Für Repeater-Felder
      if (isset($admin_settings[$key]) && $admin_settings[$key]['type'] === 'repeater') {
        $sanitized[$key] = $this->sanitize_repeater_field($key, $value, $admin_settings[$key]);
        continue;
      }

      // Boolean-Werte richtig behandeln
      if (isset($this->default_options[$key]) && is_bool($this->default_options[$key])) {
        // Stringwerte "true" und "false" sowie Boolean-Werte und 0/1 korrekt interpretieren
        if (is_string($value)) {
          $sanitized[$key] = $value === 'true' || $value === '1' || $value === 'on';
        } else {
          $sanitized[$key] = (bool) $value;
        }
      }
      // Für Text-Felder: String sanitieren
      elseif (is_string($value)) {
        $sanitized[$key] = sanitize_text_field($value);
      }
      // Für numerische Felder: Float oder Int
      elseif (is_numeric($value)) {
        $sanitized[$key] = is_float($value + 0) ? (float) $value : (int) $value;
      }
      // Fallback
      else {
        $sanitized[$key] = $value;
      }
    }


    return $sanitized;
  }

  /**
   * Sanitiert ein Listenfeld
   * @param string $key Feldschlüssel
   * @param mixed $value Feldwert
   * @param array $field_config Feldkonfiguration
   * @return array Sanitierte Liste
   * @since 3.0.0
   */
  private function sanitize_list_field($key, $value, $field_config)
  {
    // Stelle sicher, dass es ein Array ist
    if (!is_array($value)) {
      // Wenn es ein String ist, prüfen ob es mehrere Zeilen sind
      if (is_string($value) && !empty($value)) {
        $value = explode("\n", $value);
        // Trimme jedes Element
        $value = array_map('trim', $value);
      } else {
        $value = [];
      }
    }

    $sanitized = [];
    $has_validation = isset($field_config['validate']) && isset($field_config['validate']['pattern']);
    $validation_pattern = $has_validation ? $field_config['validate']['pattern'] : '';

    foreach ($value as $item) {
      // Sanitize item
      $item = trim(sanitize_text_field($item));

      // Leere Einträge überspringen
      if (empty($item)) {
        continue;
      }

      // Validierung, falls konfiguriert
      if ($has_validation && !empty($validation_pattern)) {
        if (!preg_match($validation_pattern, $item)) {
          error_log("❌ sanitize_list_field: Ungültiger Eintrag {$item}");
          continue;
        }
      }

      $sanitized[] = $item;
    }

    return $sanitized;
  }


  /**
   * Sanitiert Repeater-Feld-Daten
   */
  private function sanitize_repeater_field($field_name, $value, $field_config)
  {
    if (!is_array($value)) {
      return [];
    }

    $sanitized = [];
    foreach ($value as $item) {
      if (is_array($item)) {
        $sanitized_item = [];
        foreach ($field_config['fields'] as $sub_field_id => $sub_field_config) {
          if (isset($item[$sub_field_id])) {
            $sanitized_item[$sub_field_id] = sanitize_text_field($item[$sub_field_id]);
          }
        }
        if (!empty($sanitized_item)) {
          $sanitized[] = $sanitized_item;
        }
      }
    }

    return $sanitized;
  }

  /**
   * Returns the module instance
   * @return Module
   * @since 3.0.0
   */
  public static function get_all_modules(): array
  {
    return self::$modules;
  }


  /**
   * Returns a module instance by slug
   * @param string $slug The module slug
   * @return Module|null
   * @since 3.0.0
   */
  public static function get_instance($slug)
  {
    return self::$modules[$slug] ?? null;
  }


  /**
   * Returns the default options of the module
   * @return array
   * @since 3.0.0
   */
  public function get_default_options()
  {
    return $this->default_options;
  }


  /**
   * Returns all default options of all modules
   * @return array
   * @since 3.0.0
   */
  public static function get_all_default_options()
  {
    $defaults = [];
    foreach (self::$modules as $slug => $instance) {
      $module_defaults = $instance->get_default_options();
      if (!empty($module_defaults)) {
        $defaults[$slug] = $module_defaults;
      }
    }
    return $defaults;
  }


  /**
   * Gets the module name
   * @return string
   * @since 3.0.0
   */
  public function get_name()
  {
    return $this->name;
  }


  /**
   * Gets the module version
   * @return string
   * @since 3.0.0
   */
  public function get_version()
  {
    return $this->version;
  }


  /**
   * Gets the module slug
   * @return string
   * @since 3.0.0
   */
  public function get_slug()
  {
    return $this->slug;
  }


  /**
   * Gets the module description
   * @return string
   * @since 3.0.0
   */
  public function get_description()
  {
    return $this->description;
  }


  /**
   * Gets the module author
   * @return string
   * @since 3.0.0
   */
  public function get_author()
  {
    return $this->author;
  }


  /**
   * Checks if the module is enabled
   * @return bool
   * @since 3.0.0
   */
  public function is_enabled()
  {
    // Falls das Modul noch nie aktiviert/deaktiviert wurde, verwende default_options
    if (!isset($this->options['enabled'])) {
      return isset($this->default_options['enabled']) ?
        (bool) $this->default_options['enabled'] :
        true;
    }

    // Stelle sicher, dass der Wert als Boolean zurückgegeben wird
    return (bool) $this->options['enabled'];
  }

  /**
   * Gibt die aktuellen Optionen des Moduls zurück
   * @return array
   * @since 3.0.0
   */
  public function get_options()
  {
    return $this->options ?: [];
  }

  /**
   * Überprüft, ob eine Option aktiviert ist
   * @param string $key Optionsschlüssel
   * @return bool
   */
  protected function is_option_enabled($key)
  {
    if (isset($this->options[$key])) {
      if (is_string($this->options[$key])) {
        return $this->options[$key] === 'on' || $this->options[$key] === '1' || $this->options[$key] === 'true';
      }
      return (bool) $this->options[$key];
    }

    // Fallback auf Default-Werte
    $defaults = $this->default_options;
    if (isset($defaults[$key])) {
      if (is_string($defaults[$key])) {
        return $defaults[$key] === 'on' || $defaults[$key] === '1' || $defaults[$key] === 'true';
      }
      return (bool) $defaults[$key];
    }

    return false;
  }

  /**
   * Gibt die URL zu einem Asset im Modul-Verzeichnis zurück
   * @param string $path Relativer Pfad innerhalb des assets-Verzeichnisses
   * @return string Vollständige URL zum Asset
   */
  public function get_asset_url($path)
  {
    // Reflection nutzen, um tatsächlichen Modulpfad zu erhalten
    $reflection = new \ReflectionClass($this);
    $dir_name = basename(dirname($reflection->getFileName()));

    return "{$this->config->theme_url}/modules/{$dir_name}/assets/{$path}";
  }

  /**
   * Returns admin settings with dependency checks
   * @return array
   */
  public function get_admin_settings_with_dependencies(): array
  {
    $settings = $this->admin_settings();
    
    foreach ($settings as $key => &$setting) {
      if (isset($setting['dependencies'])) {
        $dependency_check = $this->check_dependencies($setting['dependencies']);
        $setting['dependency_status'] = $dependency_check;
      }
      
      // Process grouped fields recursively
      if ($setting['type'] === 'group' && isset($setting['fields'])) {
        foreach ($setting['fields'] as $field_key => &$field_setting) {
          if (isset($field_setting['dependencies'])) {
            $dependency_check = $this->check_dependencies($field_setting['dependencies']);
            $field_setting['dependency_status'] = $dependency_check;
          }
        }
      }
    }
    
    return $settings;
  }
}