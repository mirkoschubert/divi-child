<?php
/**
 * Create A Simple Theme Options Panel
 *
 */

// Exit if accessed directly
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// Start Class
if ( ! class_exists( 'Divi_Child_Theme_Options' ) ) {

	class Divi_Child_Theme_Options {

		private $version = DIVI_CHILD_VERSION;
		public $defaults;


		public function __construct() {

			$this->defaults = $this->set_defaults();

			if ( is_admin() ) {
				
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
				'support_center' => 'off',
				'tb_header_fix' => 'on',
				'tb_display_errors' => 'on',
				'stop_mail_updates' => 'on',
				'svg_support' => 'on',
				'webp_support' => 'on',
				'font_list' => sanitize_textarea_field('/wp-content/themes/Divi/core/admin/fonts/modules.ttf')
			);
			return $options;
		}


		public function enqueue_scripts() {
			wp_enqueue_style('divi-child-admin-style', get_stylesheet_directory_uri() . '/admin/admin.css');
		}


		public function get_theme_option($id) {
			$options = get_option('divi_child_options');
			if ( isset( $options[$id] ) ) {
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
			do_action('qm/debug', get_option('divi_child_options'));
			if (!get_option('divi_child_options')) {
				do_action('qm/notice', 'Saving defaults...');
				add_option('divi_child_options', $this->defaults);
			} else {
				register_setting( 'divi_child_options', 'divi_child_options', array($this, 'sanitize' ) );
			}
		}


		public function sanitize($options) {

			if ( $options ) {

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

				// Bug Fixes
				$options['support_center'] = (!empty($options['support_center'])) ? 'on' : 'off';
				$options['tb_header_fix'] = (!empty($options['tb_header_fix'])) ? 'on' : 'off';
				$options['tb_display_errors'] = (!empty($options['tb_display_errors'])) ? 'on' : 'off';

				// Miscellaneous
				$options['stop_mail_updates'] = (!empty($options['stop_mail_updates'])) ? 'on' : 'off';
				$options['svg_support'] = (!empty($options['svg_support'])) ? 'on' : 'off';
				$options['webp_support'] = (!empty($options['webp_support'])) ? 'on' : 'off';
				
				
				if ( ! empty( $options['font_list'] ) ) {
					$options['font_list'] = sanitize_textarea_field( $options['font_list'] );
				} else {
					unset( $options['font_list'] ); // Remove from options if empty
				}
			}
			return $options;
		}


		public function add_checkbox($id, $description, $version = NULL) {
			?>
			<label for="<?php echo $id; ?>">
				<?php $checked = $this->get_theme_option($id); ?>
				<input type="checkbox" name="divi_child_options[<?php echo $id; ?>]" id="<?php echo $id; ?>" <?php checked($checked, 'on'); ?>> <?php esc_html_e($description, 'divi-child'); if ($version) {?> <span class="versions"><?php esc_html_e($version, 'divi-child'); ?></span><?php }?>
			</label>
			<br>
			<?php
		}


		public function create_admin_page() { ?>

			<div id="divi-child-options" class="wrap">
				<div id="icon-plugins" class="icon32"></div>
				<h1><?php esc_html_e('Divi Child Options', 'divi-child'); ?> <small><?php echo 'v'. DIVI_CHILD_VERSION; ?></small></h1>
				<form method="post" action="options.php">
					<?php settings_fields('divi_child_options'); ?>
					<table class="form-table wpex-custom-admin-login-table">
						<!-- GDPR -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e('GDPR', 'divi-child'); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'GDPR', 'divi-child' ); ?></span></legend>
									<?php
										$this->add_checkbox('gdpr_comments_external', 'Make every comment and comment author link truely external');
										$this->add_checkbox('gdpr_comments_ip', 'Don\'t save the commentor\'s IP address');
										$this->add_checkbox('disable_emojis', 'Disable Emojis');
										$this->add_checkbox('disable_oembeds', 'Disable oEmbeds');
										$this->add_checkbox('dns_prefetching', 'Remove DNS prefetching for WordPress');
										$this->add_checkbox('rest_api', 'Remove REST API & XML-RPC headers for security reasons');
									?>
								</fieldset>
							</td>
						</tr>
						<!-- PAGE SPEED -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e('Page Speed', 'divi-child'); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e('Page Speed', 'divi-child'); ?></span></legend>
									<?php
										$this->add_checkbox('page_pingback', 'Disable page pingback');
										$this->add_checkbox('remove_dashicons', 'Remove dashicons from the frontend');
										$this->add_checkbox('version_query_strings', 'Remove CSS and JS query strings');
										$this->add_checkbox('remove_shortlink', 'Remove shortlink from head');
										$this->add_checkbox('preload_fonts', 'Preload some fonts for speed');
									?>
									<label for="font_list">
										<?php $font_list = $this->get_theme_option('font_list'); ?>
										<textarea id="font_list" name="divi_child_options[font_list]" rows="5"><?php echo esc_attr($font_list); ?></textarea>
										<p class="description"><?php esc_html_e('Type ony one path per line, otherwise it will break!', 'divi-child'); ?></p>
									</label>
									<br>
								</fieldset>
							</td>
						</tr>
						<!-- BUG FIXES -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e('Bug Fixes', 'divi-child'); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e('Bug Fixes', 'divi-child'); ?></span></legend>
									<?php
										$this->add_checkbox('support_center', 'Remove Divi Support Center from Frontend', '(Divi 3.20.1 only)');
										$this->add_checkbox('tb_header_fix', 'Enable fixed navigation bar option in Theme Builder', '(Divi 4.0 and up)');
										$this->add_checkbox('tb_display_errors', 'Fix display errors in Theme Builder', '(Divi 4.0 and up)');
									?>
								</fieldset>
							</td>
						</tr>
						<!-- MISCELLANIOUS -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e('Miscellaneous', 'divi-child'); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e('Miscellaneous', 'divi-child'); ?></span></legend>
									<?php
										$this->add_checkbox('stop_mail_updates', 'Disable email notification when plugins or theme where automatically updated.');
										$this->add_checkbox('svg_support', 'Enable to upload SVG files');
										$this->add_checkbox('webp_support', 'Enable to upload WebP files');
									?>
								</fieldset>
							</td>
						</tr>
					</table>

					<?php submit_button(); ?>

				</form>

			</div><!-- .wrap -->
		<?php }

	}
}
$theme_settings = new Divi_Child_Theme_Options();

/* var_dump($theme_settings->defaults); */

// Helper function to use in your theme to return a theme option value
function divi_child_get_theme_option($id = '') {
	global $theme_settings;
	return $theme_settings->get_theme_option($id);
}