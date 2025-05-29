<?php

namespace DiviChild\Core\Interfaces;

interface CommonServiceInterface extends ServiceInterface
{
    /**
     * Initialisiert die gemeinsamen Funktionalitäten
     * @return void
     */
    public function init_common();

    /**
     * Lädt gemeinsame Assets
     * @return void
     */
    public function enqueue_common_assets();

}