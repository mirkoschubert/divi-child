<?php

namespace DiviChild\Modules\A11y;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\FrontendServiceInterface;

class FrontendService extends ModuleService implements FrontendServiceInterface
{

  /**
   * Initializes the frontend service
   * @return void
   */
  public function init_frontend()
  {
    // 1. Viewport Meta
    if ($this->is_option_enabled('fix_viewport')) {
      add_action('init', [$this, 'remove_divi_viewport_meta']);
      add_action('wp_head', [$this, 'fix_viewport_meta'], 1);
    }
    
    // 2. Skip Link
    if ($this->is_option_enabled('skip_link')) {
      add_action('wp_body_open', [$this, 'add_skip_link']);
    }

    // 3. Scroll to Top
    if ($this->is_option_enabled('scroll_top')) {
      add_action('wp_footer', [$this, 'add_scroll_top'], 10);
    }
    

    // Scripts
    add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
  }
  

  /**
   * Loads the frontend assets
   * @return void
   * @package A11y
   * @since 1.0.0
   */
  public function enqueue_frontend_assets()
  {
    
    // 2. Skip Link
    if ($this->is_option_enabled('skip_link')) {
      wp_enqueue_style('divi-child-skip-link', $this->module->get_asset_url("css/a11y-skip-link.min.css"));
    }

    // 3. Scroll to Top
    if ($this->is_option_enabled('scroll_top')) {
      wp_enqueue_style('divi-child-scroll-top', $this->module->get_asset_url("css/a11y-scroll-top.min.css"));
    }

    // 4. Focus Elements
    if ($this->is_option_enabled('focus_elements')) {
      wp_enqueue_style('divi-child-focus-elements', $this->module->get_asset_url("css/a11y-focus-elements.min.css"));
    }

    // 5. Keyboard Navigation
    if ($this->is_option_enabled('nav_keyboard')) {
      wp_enqueue_style('divi-child-nav-keyboard', $this->module->get_asset_url("css/a11y-nav-keyboard.min.css"));
    }

    // 6. Fix Screenreader
    if ($this->is_option_enabled('fix_screenreader')) {
      wp_enqueue_style('divi-child-fix-screenreader', $this->module->get_asset_url("css/a11y-fix-screenreader.min.css"));
    }

    // 7. Underline Links
    if ($this->is_option_enabled('underline_links')) {
      wp_enqueue_style('divi-child-underline-links', $this->module->get_asset_url("css/a11y-underline-links.min.css"));
    }
      
    wp_enqueue_script(
      'divi-child-a11y-script', 
      $this->module->get_asset_url('js/a11y.js'), 
      ['jquery'], 
      null, 
      true
    );
    
    // Optionen an JS übergeben
    wp_localize_script('divi-child-a11y-script', 'a11yOptions', $this->get_module_options());
    
  }
  

  /**
   * Removes the Divi viewport meta tag
   * @return void
   * @package A11y
   * @since 1.0.0
   */
  public function remove_divi_viewport_meta()
  {
    remove_action('wp_head', 'et_add_viewport_meta');
  }
  

  /**
   * Adds the viewport meta tag
   * @return void
   * @package A11y
   * @since 1.0.0
   */
  public function fix_viewport_meta()
  {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=1" />';
  }
  

  /**
   * Adds a skip link to the page
   * @return void
   * @package A11y
   * @since 1.0.0
   */
  public function add_skip_link()
  {
    echo '<a href="#main-content" target="_self" class="skip-link" role="link">' . esc_html__('Skip to content', 'divi-child') . '</a>';
  }


  /**
   * Adds a scroll to top button
   * @return void
   * @package A11y
   * @since 1.0.0
   */
  public function add_scroll_top() {
    echo '<button class="top-link hide" id="js-top"><svg role="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 6"><path d="M12 6H0l6-6z"/></svg><span class="screen-reader-text">Back to top</span></button>';
  }

}