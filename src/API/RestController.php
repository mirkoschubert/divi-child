<?php

namespace DiviChild\API;

use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;
use Exception;
use DiviChild\Core\Config;
use DiviChild\Core\Abstracts\Module;

class RestController extends WP_REST_Controller
{
  protected $namespace = 'divi-child/v1';
  protected $config;

  public function __construct()
  {
    $this->config = Config::get_instance();
  }

  /**
   * Registriert die REST-Routes
   */
  public function register_routes()
  {
    // GET /divi-child/v1/modules - Alle Module holen
    register_rest_route($this->namespace, '/modules', [
      [
        'methods' => WP_REST_Server::READABLE,
        'callback' => [$this, 'get_modules'],
        'permission_callback' => [$this, 'check_permissions'],
      ]
    ]);

    // POST /divi-child/v1/modules/{slug} - Modul toggle
    register_rest_route($this->namespace, '/modules/(?P<slug>[a-zA-Z0-9_-]+)', [
      [
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => [$this, 'toggle_module'],
        'permission_callback' => [$this, 'check_permissions'],
        'args' => [
          'slug' => [
            'required' => true,
            'type' => 'string',
          ],
          'enabled' => [
            'required' => true,
            'type' => 'boolean',
          ],
        ],
      ]
    ]);

    // POST /divi-child/v1/modules/{slug}/settings - Modul-Einstellungen speichern
    register_rest_route($this->namespace, '/modules/(?P<slug>[a-zA-Z0-9_-]+)/settings', [
      [
        'methods' => WP_REST_Server::EDITABLE,
        'callback' => [$this, 'update_module_settings'],
        'permission_callback' => [$this, 'check_permissions'],
        'args' => [
          'slug' => [
            'required' => true,
            'type' => 'string',
          ],
        ],
      ]
    ]);
  }

  /**
   * Prüft Berechtigungen für API-Zugriff
   */
  public function check_permissions()
  {
    return current_user_can('manage_options');
  }

  /**
   * Gibt alle Module zurück
   */
  public function get_modules($request)
  {
    try {
      $modules = Module::get_all_modules();
      $modules_data = [];

      foreach ($modules as $slug => $module) {
        $options = $module->get_options();
        $modules_data[$slug] = [
          'slug' => $slug,
          'name' => $module->get_name(),
          'description' => $module->get_description(),
          'author' => $module->get_author(),
          'version' => $module->get_version(),
          'enabled' => isset($options['enabled']) && $options['enabled'] === true,
          'options' => $options,
          'admin_settings' => $module->get_admin_settings_with_dependencies()
        ];
      }

      return rest_ensure_response([
        'success' => true,
        'data' => $modules_data
      ]);

    } catch (Exception $e) {
      return new WP_Error(
        'modules_load_failed',
        __('Failed to load modules.', 'divi-child'),
        ['status' => 500]
      );
    }
  }

  /**
   * Togglet ein Modul an/aus
   */
  public function toggle_module($request)
  {
    $slug = $request->get_param('slug');
    $enabled = $request->get_param('enabled');

    try {
      $modules = Module::get_all_modules();

      if (!isset($modules[$slug])) {
        return new WP_Error(
          'module_not_found',
          __('Module not found.', 'divi-child'),
          ['status' => 404]
        );
      }

      // Aktuelle Optionen holen
      $options = $this->config->get_module_options($slug);
      $options['enabled'] = $enabled;

      // Speichern
      $result = $this->config->save_module_options($slug, $options);

      if ($result) {
        return rest_ensure_response([
          'success' => true,
          'message' => $enabled
            ? __('Module enabled.', 'divi-child')
            : __('Module disabled.', 'divi-child')
        ]);
      } else {
        return new WP_Error(
          'save_failed',
          __('Failed to save module settings.', 'divi-child'),
          ['status' => 500]
        );
      }

    } catch (Exception $e) {
      return new WP_Error(
        'toggle_failed',
        __('Failed to toggle module.', 'divi-child'),
        ['status' => 500]
      );
    }
  }

  /**
   * Aktualisiert Modul-Einstellungen
   */
  public function update_module_settings($request)
  {
    $slug = $request->get_param('slug');
    $settings = $request->get_json_params();

    try {
      $modules = Module::get_all_modules();

      if (!isset($modules[$slug])) {
        return new WP_Error(
          'module_not_found',
          __('Module not found.', 'divi-child'),
          ['status' => 404]
        );
      }

      $module = $modules[$slug];

      // Bestehende Optionen holen
      $existing_options = $this->config->get_module_options($slug);

      // Settings sanitisieren
      $sanitized = $module->sanitize_options($settings);

      // Enabled-Status beibehalten
      if (isset($existing_options['enabled'])) {
        $sanitized['enabled'] = $existing_options['enabled'];
      }

      // Speichern
      $result = $this->config->save_module_options($slug, $sanitized);

      if ($result) {
        return rest_ensure_response([
          'success' => true,
          'message' => __('Settings saved.', 'divi-child')
        ]);
      } else {
        return new WP_Error(
          'save_failed',
          __('Failed to save settings.', 'divi-child'),
          ['status' => 500]
        );
      }

    } catch (Exception $e) {
      return new WP_Error(
        'settings_update_failed',
        __('Failed to update settings.', 'divi-child'),
        ['status' => 500]
      );
    }
  }
}