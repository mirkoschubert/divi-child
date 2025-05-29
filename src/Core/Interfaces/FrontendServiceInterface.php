<?php

namespace DiviChild\Core\Interfaces;

interface FrontendServiceInterface extends ServiceInterface
{
    /**
     * Initialisiert Frontend-spezifische Funktionalität
     * @return void
     */
    public function init_frontend();

    /**
     * Lädt Frontend-spezifische Assets
     * @return void
     */
    public function enqueue_frontend_assets();

}