<?php

class DVC_Admin
{
    public function __construct()
    {
      add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
      add_action('admin_menu', array($this, 'add_admin_menu'));
      add_action('admin_init', array($this, 'register_settings'));
      add_action('wp_ajax_dvc_save_options', array($this, 'save_options'));
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('divi-child-admin-style', get_stylesheet_directory_uri() . '/admin/assets/css/dvc-admin.css');
        wp_enqueue_script('divi-child-admin-script', get_stylesheet_directory_uri() . '/admin/assets/js/dvc-admin.js', array('jquery'), null, true);
        wp_localize_script('divi-child-admin-script', 'dvc_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    public function add_admin_menu()
    {
      add_submenu_page(
        'et_divi_options',
        esc_html__('Child Theme Options', 'divi-child'),
        esc_html__('Child Theme Options', 'divi-child'),
        'manage_options',
        'et_divi_child_options',
        [$this, 'create_admin_page'],
        1
      );
    }
    public function register_settings()
    {
        if (!get_option('divi_child_options')) {
            add_option('divi_child_options', $this->get_defaults());
        } else {
            register_setting('divi_child_options', 'divi_child_options', array($this, 'sanitize'));
        }
    }

    public function create_admin_page()
    {
        // Check if the user has the required capability
        if (!current_user_can('manage_options')) {
            return;
        }

        // Include the admin page template
        include_once get_template_directory() . '/admin/templates/admin-page.php';
    }
    public function get_options()
    {
        $options = get_option('divi_child_options');
        if ($options) {
            return $options;
        }
    }
    public function get_option($topic, $id)
    {
        $options = get_option('divi_child_options');
        if (isset($options[$topic][$id])) {
            return $options[$topic][$id];
        }
    }
    public function get_default($topic, $id)
    {
        $defaults = $this->get_defaults();
        if (isset($defaults[$topic][$id])) {
            return $defaults[$topic][$id];
        }
    }
    public function get_defaults()
    {
        $defaults = [
          'gdpr' => [
            'comments_external' => 'on',
            'comments_ip' => 'on',
            'disable_emojis' => 'on',
            'disable_oembeds' => 'on',
            'dns_prefetching' => 'on',
            'rest_api' => 'on'
          ],
          'page_speed' => [
            'remove_pingback' => 'on',
            'remove_dashicons' => 'on',
            'remove_version_strings' => 'on',
            'remove_shortlink' => 'on',
            'preload_fonts' => 'off',
            'preload_fonts_list' => sanitize_textarea_field('/wp-content/themes/Divi/core/admin/fonts/modules/all/modules.woff')
          ],
          'a11y' => [
            'fix_viewport' => 'on',
            'skip_link' => 'on',
            'scroll_top' => 'on',
            'focus_elements' => 'on',
            'nav_keyboard' => 'on',
            'external_links' => 'on',
            'optimize_forms' => 'on',
            'aria_support' => 'on',
            'fix_screenreader' => 'on',
            'underline_links' => 'on',
          ],
          'bug_fixes' => [
            'support_center' => 'off',
            'fixed_navigation' => 'off',
            'display_errors' => 'off',
            'logo_image_sizing' => 'off',
            'split_section' => 'off'
          ],
          'misc' => [
            'disable_projects' => 'off',
            'stop_mail_updates' => 'off',
            'svg_support' => 'on',
            'webp_support' => 'on',
            'hyphens' => 'on',
            'mobile_menu_breakpoint' => 'on',
            'mobile_menu_fullscreen' => 'on'
          ]
        ];
        return $defaults;
    }
    public function save_options()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('You do not have permission to perform this action.');
            return;
        }

        $options = $_POST['options'];
        update_option('divi_child_options', $options);
        wp_send_json_success('Options saved successfully.');
    }
    
  }