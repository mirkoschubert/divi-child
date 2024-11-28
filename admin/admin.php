<?php
/**
 * Create A Simple Theme Options Panel
 *
 */

// Exit if accessed directly
defined('ABSPATH') or die('No script kiddies please!');

if (!class_exists('Divi_Child_Theme_Options')) {

  class Divi_Child_Theme_Options
  {

    private $version = DIVI_CHILD_VERSION;
    public $defaults;

    public function __construct()
    {

      $this->defaults = $this->set_defaults();

      if (is_admin()) {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'create_admin_menu'), 12);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
      }

    }

    public function set_defaults()
    {
      $options = [
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
      return $options;
    }

    public function enqueue_scripts()
    {
      wp_enqueue_style('divi-child-admin-style', get_stylesheet_directory_uri() . '/admin/assets/css/admin.css');
    }

    public function get_theme_option($topic, $id)
    {
      $options = get_option('divi_child_options');
      if (isset($options[$topic][$id])) {
        return $options[$topic][$id];
      }
    }

    public function create_admin_menu()
    {
      add_submenu_page(
        'et_divi_options',
        esc_html__('Child Theme Options', 'divi-child'),
        esc_html__('Child Theme Options', 'divi-child'),
        'manage_options',
        'et_divi_child_options',
        array($this, 'create_admin_page'),
        1
      );
    }

    public function register_settings()
    {
      /* do_action('qm/debug', get_option('divi_child_options')); */
      if (!get_option('divi_child_options')) {
        /* do_action('qm/notice', 'Saving defaults...'); */
        add_option('divi_child_options', $this->defaults);
      } else {
        register_setting('divi_child_options', 'divi_child_options', array($this, 'sanitize'));
      }
    }

    public function sanitize($options)
    {

      if ($options) {

        // GDPR
        $options['gdpr']['comments_external'] = (!empty($options['gdpr']['comments_external'])) ? 'on' : 'off';
        $options['gdpr']['comments_ip'] = (!empty($options['gdpr']['comments_ip'])) ? 'on' : 'off';
        $options['gdpr']['disable_emojis'] = (!empty($options['gdpr']['disable_emojis'])) ? 'on' : 'off';
        $options['gdpr']['disable_oembeds'] = (!empty($options['gdpr']['disable_oembeds'])) ? 'on' : 'off';
        $options['gdpr']['dns_prefetching'] = (!empty($options['gdpr']['dns_prefetching'])) ? 'on' : 'off';
        $options['gdpr']['rest_api'] = (!empty($options['gdpr']['rest_api'])) ? 'on' : 'off';

        // Page Speed
        $options['page_speed']['remove_pingback'] = (!empty($options['page_speed']['remove_pingback'])) ? 'on' : 'off';
        $options['page_speed']['remove_dashicons'] = (!empty($options['page_speed']['remove_dashicons'])) ? 'on' : 'off';
        $options['page_speed']['remove_version_strings'] = (!empty($options['page_speed']['remove_version_strings'])) ? 'on' : 'off';
        $options['page_speed']['remove_shortlink'] = (!empty($options['page_speed']['remove_shortlink'])) ? 'on' : 'off';
        $options['page_speed']['preload_fonts'] = (!empty($options['page_speed']['preload_fonts'])) ? 'on' : 'off';

        if (!empty($options['page_speed']['preload_fonts_list'])) {
          $options['page_speed']['preload_fonts_list'] = sanitize_textarea_field($options['page_speed']['preload_fonts_list']);
        } else {
          unset($options['page_speed']['preload_fonts_list']); // Remove from options if empty
        }

        // A11y
        $options['a11y']['aria_support'] = (!empty($options['a11y']['aria_support'])) ? 'on' : 'off';
        $options['a11y']['fix_viewport'] = (!empty($options['a11y']['fix_viewport'])) ? 'on' : 'off';
        $options['a11y']['skip_link'] = (!empty($options['a11y']['skip_link'])) ? 'on' : 'off';
        $options['a11y']['scroll_top'] = (!empty($options['a11y']['scroll_top'])) ? 'on' : 'off';
        $options['a11y']['focus_elements'] = (!empty($options['a11y']['focus_elements'])) ? 'on' : 'off';
        $options['a11y']['nav_keyboard'] = (!empty($options['a11y']['nav_keyboard'])) ? 'on' : 'off';
        $options['a11y']['external_links'] = (!empty($options['a11y']['external_links'])) ? 'on' : 'off';
        $options['a11y']['optimize_forms'] = (!empty($options['a11y']['optimize_forms'])) ? 'on' : 'off';
        $options['a11y']['fix_screenreader'] = (!empty($options['a11y']['fix_screenreader'])) ? 'on' : 'off';
        $options['a11y']['underline_links'] = (!empty($options['a11y']['underline_links'])) ? 'on' : 'off';

        // Bug Fixes
        $options['bug_fixes']['support_center'] = (!empty($options['bug_fixes']['support_center'])) ? 'on' : 'off';
        $options['bug_fixes']['fixed_navigation'] = (!empty($options['bug_fixes']['fixed_navigation'])) ? 'on' : 'off';
        $options['bug_fixes']['display_errors'] = (!empty($options['bug_fixes']['display_errors'])) ? 'on' : 'off';
        $options['bug_fixes']['logo_image_sizing'] = (!empty($options['bug_fixes']['logo_image_sizing'])) ? 'on' : 'off';
        $options['bug_fixes']['split_section'] = (!empty($options['bug_fixes']['split_section'])) ? 'on' : 'off';

        // Miscellaneous
        $options['misc']['disable_projects'] = (!empty($options['misc']['disable_projects'])) ? 'on' : 'off';
        $options['misc']['stop_mail_updates'] = (!empty($options['misc']['stop_mail_updates'])) ? 'on' : 'off';
        $options['misc']['svg_support'] = (!empty($options['misc']['svg_support'])) ? 'on' : 'off';
        $options['misc']['webp_support'] = (!empty($options['misc']['webp_support'])) ? 'on' : 'off';
        $options['misc']['hyphens'] = (!empty($options['misc']['hyphens'])) ? 'on' : 'off';
        $options['misc']['mobile_menu_breakpoint'] = (!empty($options['misc']['mobile_menu_breakpoint'])) ? 'on' : 'off';
        $options['misc']['mobile_menu_fullscreen'] = (!empty($options['misc']['mobile_menu_fullscreen'])) ? 'on' : 'off';

      }
      return $options;
    }

    public function add_checkbox($topic, $id, $description, $version = NULL)
    {
      ?>
			<label for="<?php echo $id; ?>">
				<?php $checked = $this->get_theme_option($topic, $id); ?>
				<input type="checkbox" name="divi_child_options[<?php echo $topic; ?>][<?php echo $id; ?>]" id="<?php echo $id; ?>" <?php checked($checked, 'on'); ?>> <?php echo $description;
                   if ($version) { ?> <span class="versions"><?php echo $version; ?></span><?php } ?>
			</label>
			<br>
			<?php
    }

    public function create_admin_page()
    {
      require_once plugin_dir_path(dirname(__FILE__)) . 'admin/views/options.php';
    }
  }
}
$theme_settings = new Divi_Child_Theme_Options();
