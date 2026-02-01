<?php

namespace DiviChild\Core\Interfaces;

interface ServiceInterface {
    /**
     * Initializes all module services
     * @return void
     */
    public function init_service();

    /**
     * Enqueues assets
     * @return void
     */
    public function enqueue_assets();

    /**
     * Returns the module slug
     * @return string
     */
    public function get_module_slug();

    /**
     * Returns the module options
     * @return array
     */
    public function get_module_options();
}
