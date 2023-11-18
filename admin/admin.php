<?php
/**
 * Create A Simple Theme Options Panel
 *
 */

// Exit if accessed directly
defined('ABSPATH') or die('No script kiddies please!');

if (!class_exists('Divi_Child_Theme_Options')) {

  class Divi_Child_Theme_Options {

    private $version = DIVI_CHILD_VERSION;
    public $defaults;

    public function __construct() {

      $this->defaults = $this->set_defaults();

      if (is_admin()) {

        add_action('admin_init', array($this, 'register_settings'));

        add_action('admin_menu', array($this, 'create_admin_menu'), 12);

        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
      }

    }

    public function set_defaults() {
      $options = array(
        'gdpr_comments_external' => 'on',
        'gdpr_comments_ip' => 'on',
        'disable_emojis' => 'on',
        'disable_oembeds' => 'on',
        'dns_prefetching' => 'on',
        'rest_api' => 'on',
        'page_pingback' => 'on',
        'remove_dashicons' => 'on',
        'version_query_strings' => 'on',
        'remove_shortlink' => 'on',
        'preload_fonts' => 'off',
        'viewport_meta' => 'on',
        'support_center' => 'off',
        'tb_header_fix' => 'on',
        'tb_display_errors' => 'off',
        'logo_image_sizing' => 'on',
        'split_section_fix' => 'off',
        'disable_projects' => 'off',
        'stop_mail_updates' => 'on',
        'svg_support' => 'on',
        'webp_support' => 'on',
        'hyphens' => 'on',
        'mobile_menu_breakpoint' => 'on',
        'mobile_menu_fullscreen' => 'on',
        'font_list' => sanitize_textarea_field('/wp-content/themes/Divi/core/admin/fonts/modules.ttf'),
      );
      return $options;
    }

    public function enqueue_scripts() {
      wp_enqueue_style('divi-child-admin-style', get_stylesheet_directory_uri() . '/admin/admin.css');
    }

    public function get_theme_option($id) {
      $options = get_option('divi_child_options');
      if (isset($options[$id])) {
        return $options[$id];
      }
    }

    public function create_admin_menu() {
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

    public function register_settings() {
      /* do_action('qm/debug', get_option('divi_child_options')); */
      if (!get_option('divi_child_options')) {
        /* do_action('qm/notice', 'Saving defaults...'); */
        add_option('divi_child_options', $this->defaults);
      } else {
        register_setting('divi_child_options', 'divi_child_options', array($this, 'sanitize'));
      }
    }

    public function sanitize($options) {

      if ($options) {

        // GDPR
        $options['gdpr_comments_external'] = (!empty($options['gdpr_comments_external'])) ? 'on' : 'off';
        $options['gdpr_comments_ip'] = (!empty($options['gdpr_comments_ip'])) ? 'on' : 'off';
        $options['disable_emojis'] = (!empty($options['disable_emojis'])) ? 'on' : 'off';
        $options['disable_oembeds'] = (!empty($options['disable_oembeds'])) ? 'on' : 'off';
        $options['dns_prefetching'] = (!empty($options['dns_prefetching'])) ? 'on' : 'off';
        $options['rest_api'] = (!empty($options['rest_api'])) ? 'on' : 'off';

        // Page Speed
        $options['page_pingback'] = (!empty($options['page_pingback'])) ? 'on' : 'off';
        $options['remove_dashicons'] = (!empty($options['remove_dashicons'])) ? 'on' : 'off';
        $options['version_query_strings'] = (!empty($options['version_query_strings'])) ? 'on' : 'off';
        $options['remove_shortlink'] = (!empty($options['remove_shortlink'])) ? 'on' : 'off';
        $options['preload_fonts'] = (!empty($options['preload_fonts'])) ? 'on' : 'off';

        // A11y
        $options['viewport_meta'] = (!empty($options['viewport_meta'])) ? 'on' : 'off';

        // Bug Fixes
        $options['support_center'] = (!empty($options['support_center'])) ? 'on' : 'off';
        $options['tb_header_fix'] = (!empty($options['tb_header_fix'])) ? 'on' : 'off';
        $options['tb_display_errors'] = (!empty($options['tb_display_errors'])) ? 'on' : 'off';
        $options['logo_image_sizing'] = (!empty($options['logo_image_sizing'])) ? 'on' : 'off';
        $options['split_section_fix'] = (!empty($options['split_section_fix'])) ? 'on' : 'off';

        // Miscellaneous
        $options['stop_mail_updates'] = (!empty($options['stop_mail_updates'])) ? 'on' : 'off';
        $options['svg_support'] = (!empty($options['svg_support'])) ? 'on' : 'off';
        $options['webp_support'] = (!empty($options['webp_support'])) ? 'on' : 'off';
        $options['hyphens'] = (!empty($options['hyphens'])) ? 'on' : 'off';
        $options['mobile_menu_breakpoint'] = (!empty($options['mobile_menu_breakpoint'])) ? 'on' : 'off';
        $options['mobile_menu_fullscreen'] = (!empty($options['mobile_menu_fullscreen'])) ? 'on' : 'off';

        if (!empty($options['font_list'])) {
          $options['font_list'] = sanitize_textarea_field($options['font_list']);
        } else {
          unset($options['font_list']); // Remove from options if empty
        }
      }
      return $options;
    }

    public function add_checkbox($id, $description, $version = NULL) {
      ?>
			<label for="<?php echo $id; ?>">
				<?php $checked = $this->get_theme_option($id);?>
				<input type="checkbox" name="divi_child_options[<?php echo $id; ?>]" id="<?php echo $id; ?>" <?php checked($checked, 'on');?>> <?php echo $description;if ($version) { ?> <span class="versions"><?php echo $version; ?></span><?php }?>
			</label>
			<br>
			<?php
}

    public function create_admin_page() {?>

			<div id="divi-child-options" class="wrap">
				<div id="icon-plugins" class="icon32"></div>
				<h1><?php esc_html_e('Divi Child Options', 'divi-child');?> <small><?php echo 'v' . DIVI_CHILD_VERSION; ?></small></h1>
				<form method="post" action="options.php">
					<?php settings_fields('divi_child_options');?>
					<table class="form-table wpex-custom-admin-login-table">
						<!-- GDPR -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e('GDPR', 'divi-child');?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e('GDPR', 'divi-child');?></span></legend>
									<?php
$this->add_checkbox('gdpr_comments_external', esc_html__('Make every comment and comment author link truely external', 'divi-child'));
      $this->add_checkbox('gdpr_comments_ip', esc_html__('Don\'t save the commentor\'s IP address', 'divi-child'));
      $this->add_checkbox('disable_emojis', esc_html__('Disable Emojis', 'divi-child'));
      $this->add_checkbox('disable_oembeds', esc_html__('Disable oEmbeds', 'divi-child'));
      $this->add_checkbox('dns_prefetching', esc_html__('Remove DNS prefetching for WordPress', 'divi-child'));
      $this->add_checkbox('rest_api', esc_html__('Remove REST API & XML-RPC headers for security reasons', 'divi-child'));
      ?>
								</fieldset>
							</td>
						</tr>
						<!-- PAGE SPEED -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e('Page Speed', 'divi-child');?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e('Page Speed', 'divi-child');?></span></legend>
									<?php
$this->add_checkbox('page_pingback', esc_html__('Disable page pingback', 'divi-child'));
      $this->add_checkbox('remove_dashicons', esc_html__('Remove dashicons from the frontend', 'divi-child'));
      $this->add_checkbox('version_query_strings', esc_html__('Remove CSS and JS query strings', 'divi-child'));
      $this->add_checkbox('remove_shortlink', esc_html__('Remove shortlink from head', 'divi-child'));
      $this->add_checkbox('preload_fonts', esc_html__('Preload some fonts for speed', 'divi-child'));
      ?>
									<label for="font_list">
										<?php $font_list = $this->get_theme_option('font_list');?>
										<textarea id="font_list" name="divi_child_options[font_list]" rows="5"><?php echo esc_attr($font_list); ?></textarea>
										<p class="description"><?php esc_html_e('Type ony one path per line, otherwise it will break!', 'divi-child');?></p>
									</label>
									<br>
								</fieldset>
							</td>
						</tr>
						<!-- ACCESSIBILITY -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e('Accessibility', 'divi-child');?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e('Accessibility', 'divi-child');?></span></legend>
									<?php
$this->add_checkbox('viewport_meta', esc_html__('Fix Viewport Meta', 'divi-child'));
      ?>
								</fieldset>
							</td>
						</tr>
						<!-- BUG FIXES -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e('Bug Fixes', 'divi-child');?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e('Bug Fixes', 'divi-child');?></span></legend>
									<?php
$this->add_checkbox(
        'support_center',
        esc_html__('Remove Divi Support Center from Frontend', 'divi-child'),
        esc_html__('(Divi 3.20.1 only)', 'divi-child')
      );
      $this->add_checkbox(
        'tb_header_fix',
        esc_html__('Enable fixed navigation bar option in Theme Builder', 'divi-child'),
        esc_html__('(Divi 4.0 and up)', 'divi-child')
      );
      $this->add_checkbox(
        'tb_display_errors',
        esc_html__('Fix display errors in Theme Builder', 'divi-child'),
        esc_html__('(Divi 4.0 up to Divi 4.12)', 'divi-child')
      );
      $this->add_checkbox(
        'logo_image_sizing',
        esc_html__('Fix logo image sizing in Theme Builder', 'divi-child'),
        esc_html__('(Divi 4.6.6)', 'divi-child')
      );
      $this->add_checkbox(
        'split_section_fix',
        __('Set CSS class <code>.split-section-fix</code> for swapping image and text on tablet and phone', 'divi-child')
      );
      ?>
								</fieldset>
							</td>
						</tr>
						<!-- MISCELLANIOUS -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e('Miscellaneous', 'divi-child');?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e('Miscellaneous', 'divi-child');?></span></legend>
									<?php
$this->add_checkbox('disable_projects', esc_html__('Disable custom post type Projects.', 'divi-child'));
      $this->add_checkbox('stop_mail_updates', esc_html__('Disable email notification when plugins or theme where automatically updated.', 'divi-child'));
      $this->add_checkbox('svg_support', esc_html__('Enable to upload SVG files', 'divi-child'));
      $this->add_checkbox('webp_support', esc_html__('Enable to upload WebP files', 'divi-child'));
      $this->add_checkbox('hyphens', esc_html__('Enable hyphenation for the whole website', 'divi-child'));
      $this->add_checkbox('mobile_menu_breakpoint', esc_html__('Set breakpoint for the mobile menu to 1280px', 'divi-child'));
      $this->add_checkbox('mobile_menu_fullscreen', esc_html__('Enable fullscreen mode for the mobile menu', 'divi-child'));
      ?>
								</fieldset>
							</td>
						</tr>
					</table>

					<?php submit_button();?>

				</form>

			</div><!-- .wrap -->
		<?php }

  }
}
$theme_settings = new Divi_Child_Theme_Options();
