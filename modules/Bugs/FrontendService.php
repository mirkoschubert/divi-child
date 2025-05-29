<?php

namespace DiviChild\Modules\Bugs;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\FrontendServiceInterface;

final class FrontendService extends ModuleService implements FrontendServiceInterface
{

  /**
   * Initializes the frontend service
   * @return void
   * @package Bugs
   * @since 1.0.0
   */
  public function init_frontend()
  {
    parent::init_frontend();

    // 1. Support Center
    if ($this->is_option_enabled('support_center')) {
      add_action('wp_enqueue_scripts', [$this, 'remove_divi_support_center']);
    }

    // 2. Fixed Navigation
    if ($this->is_option_enabled('fixed_navigation')) {
      add_filter('body_class', [$this, 'add_fixed_navigation_class']);
      add_action('wp_head', [$this, 'add_header_css_variables'], 5);
    }
    
  }


  /**
   * Loads the frontend assets
   * @return void
   * @package Bugs
   * @since 1.0.0
   */
  public function enqueue_frontend_assets()
  {
    // 2. Fixed Navigation
    if ($this->is_option_enabled('fixed_navigation')) {
      wp_enqueue_style('divi-child-fixed-navigation', $this->module->get_asset_url("css/bugs-fixed-navigation.min.css"));
    }
    // 3. Display Errors
    if ($this->is_option_enabled('display_errors')) {
      wp_enqueue_script('divi-child-display-errors', $this->module->get_asset_url("js/bugs-display-errors.min.js"), ['jquery'], $this->module->get_version(), true);
    }
    // 4. Logo Image Sizing
    if ($this->is_option_enabled('logo_image_sizing')) {
      wp_enqueue_style('divi-child-logo-image-sizing', $this->module->get_asset_url("css/bugs-logo-image-sizing.min.css"));
    }
    // 5. Split Section
    if ($this->is_option_enabled('split_section')) {
      wp_enqueue_style('divi-child-split-section', $this->module->get_asset_url("css/bugs-split-section.min.css"));
    }
  }


  /**
   * Removes the Divi Support Center script
   * @return void
   * @package Bugs
   * @since 1.0.0
   */
  public function remove_divi_support_center()
  {
    wp_dequeue_script('et-support-center');
    wp_deregister_script('et-support-center');
  }


  /**
   * Adds a fixed navigation class to the body
   * @return void
   * @package Bugs
   * @since 1.0.0
   */
  function add_fixed_navigation_class($classes)
  {
    $has_tb_header = in_array('et-tb-has-header', $classes);
    if (function_exists('et_get_option')) {
      $is_fixed_header = 'on' === et_get_option('divi_fixed_nav', 'on');
    } else {
      return $classes;
    }

    if ($has_tb_header) {
      $classes[] = $is_fixed_header ? 'et_fixed_nav' : 'et_non_fixed_nav';
      $classes[] = 'et_show_nav';
      // With et-tb-has-header not set the page-container gets a padding-top of the height of the header
      unset($classes[array_search('et-tb-has-header', $classes)]);
    }
    return $classes;
  }


  /**
   * Gibt CSS-Variablen für den Header aus
   * @return void
   * @package Bugs
   * @since 1.1.0
   */
  public function add_header_css_variables() {
    if ($this->is_option_enabled('fixed_navigation')) {
      $header_height = intval($this->get_module_option('header_height')) ?: 80; // Fallback auf 80px
      ?>
      <style id="dvc-header-variables">
        :root {
          --header-height: <?php echo esc_attr($header_height); ?>px;
        }
      </style>
      <?php
    }
  }

}