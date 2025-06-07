<?php

namespace DiviChild\API\Abstracts;

use WP_REST_Controller;
use WP_Error;
use DiviChild\Core\Abstracts\Module;

abstract class ModuleController extends WP_REST_Controller
{
    protected $module;
    protected $namespace = 'divi-child/v1';

    public function __construct(Module $module)
    {
        $this->module = $module;
        $this->rest_base = 'modules/' . $module->get_slug();
    }

    /**
     * Registriert die Standard-Routes für ein Modul
     * Kann von Kindklassen erweitert werden
     */
    public function register_routes()
    {
        // Basis-Implementation - kann überschrieben werden
        // Hier können Module ihre eigenen Routen hinzufügen
    }

    /**
     * Standard-Berechtigungsprüfung
     */
    public function check_permissions($request = null)
    {
        return current_user_can('manage_options');
    }

    /**
     * Erstellt eine standardisierte Erfolgsantwort
     */
    protected function success_response($data = null, $message = '')
    {
        $response = ['success' => true];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if (!empty($message)) {
            $response['message'] = $message;
        }

        return rest_ensure_response($response);
    }

    /**
     * Erstellt eine standardisierte Fehlerantwort
     */
    protected function error_response($code, $message, $status = 400)
    {
        return new WP_Error($code, $message, ['status' => $status]);
    }

    /**
     * Gibt das zugehörige Modul zurück
     */
    public function get_module()
    {
        return $this->module;
    }
}