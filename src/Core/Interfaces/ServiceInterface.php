<?php
// filepath: /Users/mirkoschubert/Projects/cms/wordpress/themes/child-themes/divi-child/src/Core/Interfaces/ModuleServiceInterface.php

namespace DiviChild\Core\Interfaces;

interface ServiceInterface {
    /**
     * Initialisiert den Service
     * @return void
     */
    public function init();

    /**
     * Gibt den Modul-Slug zurück
     * @return string
     */
    public function get_module_slug();

    /**
     * Gibt die Modul-Optionen zurück
     * @return array
     */
    public function get_module_options();
}