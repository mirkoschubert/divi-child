<?php

namespace DiviChild\Core\Interfaces;

interface ServiceInterface {
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
