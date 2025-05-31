<?php

namespace DiviChild\Admin;

use DiviChild\Core\Config;
use DiviChild\Admin\AdminAjax;
use DiviChild\Admin\ReactAdmin;
use DiviChild\Admin\UI;

final class Admin
{
  protected $ui;
  protected $config;
  protected $options;

  protected $modules;

  public function __construct()
  {
    
    $this->ui = new UI();
    $this->config = new Config();
    $this->options = $this->config->get_options();

    new ReactAdmin();
    
    // Old Admin REST API
    new AdminAjax();

    $this->init();
  }

  public function init()
  {
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    add_action('admin_init', [$this, 'register_settings']);
    add_action('admin_menu', [$this, 'add_admin_menu'], 12);
    
    // 🔍 DEBUG: REST API Status prüfen
    add_action('admin_init', function() {      
      $rest_url = rest_url('divi-child/v1/modules');
    });
  }


  /**
   * Enqueue admin scripts and styles
   * @return void
   * @since 3.0.0
   */
  public function enqueue_scripts()
  {
    wp_enqueue_style('divi-child-admin-style', "{$this->config->theme_url}/assets/css/admin.css");
    wp_enqueue_script('divi-child-admin-script', "{$this->config->theme_url}/assets/js/admin.js", ['jquery'], null, true);
    wp_enqueue_script('divi-child-admin-ui-script', "{$this->config->theme_url}/assets/js/ui-components.js", ['jquery'], null, true);
    
    // NONCE für AJAX-Sicherheit 
    wp_localize_script('divi-child-admin-script', 'dvc_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dvc_ajax_nonce'), // NONCE hier erzeugen
        'messages' => [
            'saving' => __('Speichere...', 'divi-child'),
            'success' => __('Gespeichert!', 'divi-child'),
            'error' => __('Fehler beim Speichern.', 'divi-child')
        ]
    ]);
  }


  /**
   * Register settings for the admin page
   * @return void
   * @since 3.0.0
   */
  public function register_settings()
  {
    if (!get_option('divi_child_options')) {
      add_option('divi_child_options', $this->config->get_defaults());
    } else {
      register_setting('divi_child_options', 'divi_child_options', [$this, 'sanitize']);
    }
  }


  /**
   * Sanitize the options before saving
   * @param array $options
   * @return array
   * @since 3.0.0
   */
  public function add_admin_menu(): void
  {
    add_submenu_page(
      'et_divi_options',
      esc_html__('Child Theme Options', 'divi-child'),
      esc_html__('Child Theme Options', 'divi-child'),
      'manage_options',
      'admin.php?page=et_divi_child_options',
      [$this, 'create_admin_page'],
      1
    );
  }


  /**
   * Create the admin page
   * @return void
   * @since 3.0.0
   */
  public function create_admin_page()
  {
    if (!current_user_can('manage_options')) {
      return;
    }
    echo '<div class="wrap">';
    $this->ui->header('Divi Child Theme', $this->config->theme_version);
    $this->ui->list_modules($this->config->get_modules());
    $this->ui->modal();
    echo '</div>';
  }

}