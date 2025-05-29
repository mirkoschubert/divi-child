<?php
// filepath: /Users/mirkoschubert/Projects/cms/wordpress/themes/child-themes/divi-child/src/Core/Interfaces/AdminServiceInterface.php

namespace DiviChild\Core\Interfaces;

interface AdminServiceInterface extends ServiceInterface
{
    /**
     * Initialisiert Admin-spezifische Funktionalität
     * @return void
     */
    public function init_admin();

    /**
     * Lädt Admin-spezifische Assets
     * @return void
     */
    public function enqueue_admin_assets();

}