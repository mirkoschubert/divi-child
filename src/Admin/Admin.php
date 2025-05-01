<?php

namespace DiviChild\Admin;

use DiviChild\Core\Config;
use DiviChild\Admin\UI;
use DiviChild\Core\Interfaces\ModuleInterface;

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
    $this->init();
  }

  public function init()
  {
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    add_action('admin_init', [$this, 'register_settings']);
    add_action('admin_menu', [$this, 'add_admin_menu'], 12);
    add_action('wp_ajax_dvc_save_options', [$this, 'save_options']);
  }

  public function enqueue_scripts()
  {
    wp_enqueue_style('divi-child-admin-style', "{$this->config->theme_url}/assets/css/admin.css");
    wp_enqueue_script('divi-child-admin-script', "{$this->config->theme_url}/assets/js/admin.js", ['jquery'], null, true);
    wp_localize_script('divi-child-admin-script', 'dvc_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
  }

  public function register_settings()
  {
    if (!get_option('divi_child_options')) {
      add_option('divi_child_options', $this->config->get_defaults());
    } else {
      register_setting('divi_child_options', 'divi_child_options', [$this, 'sanitize']);
    }
  }

  public function add_admin_menu()
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

  public function create_admin_page()
  {
    if (!current_user_can('manage_options')) {
      return;
    }
    echo '<div class="wrap">';
    $this->ui->header('Child Theme Options', $this->config->theme_version);
    $this->list_modules();
    echo '</div>';
  }

  public function list_modules()
  {
    $modules = $this->config->get_modules();

    ?>
    <div class="dvc-modules">
      <div class="modules-grid">
      <?php foreach ($modules as $slug => $module) : ?>
        <div class="module">
          <div class="module-content">
            <div class="module-info">
              <h2><?php echo esc_html($module['name']); ?> <small class="version">v<?php echo esc_html($module['version']); ?></small></h2>
              <p><?php echo esc_html($module['description']); ?></p>
            </div>
            <div class="module-switch">
              <label class="switch">
                <input type="checkbox" class="module-toggle" data-slug="<?php echo esc_attr($slug); ?>" <?php checked($module['enabled'], true); ?>>
                <span class="slider round"></span>
              </label>
            </div>
          </div>
          <?php if (!empty($module['options'])) : ?>
          <div class="module-footer">
            <button class="btn settings-btn" data-slug="<?php echo esc_attr($module['slug']); ?>">
              <?php esc_html_e('Einstellungen', 'divi-child'); ?>
            </button>
          </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      </div>
    </div>
    <?php
  }

}