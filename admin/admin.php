<?php
/**
 * Create A Simple Theme Options Panel
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues plugin admin scripts
 * @since 0.2.0
 */
function divi_child_admin_scripts($hook) {
	do_action( 'qm/notice', $hook );
  if($hook !== 'divi_page_et_divi_child_options') return;
  wp_enqueue_style('divi-child-admin-style', get_stylesheet_directory_uri() . '/admin/admin.css');
}
add_action('admin_enqueue_scripts', 'divi_child_admin_scripts');


// Start Class
if ( ! class_exists( 'Divi_Child_Theme_Options' ) ) {

	class Divi_Child_Theme_Options {

		public function __construct() {

			// We only need to register the admin panel on the back-end
			if ( is_admin() ) {
				add_action( 'admin_menu', array( 'Divi_Child_Theme_Options', 'add_admin_menu' ), 12 );
				add_action( 'admin_init', array( 'Divi_Child_Theme_Options', 'register_settings' ) );
			}

		}

		public static function get_theme_options() {
			return get_option( 'divi_child_options' );
		}

		public static function get_theme_option( $id ) {
			$options = self::get_theme_options();
			if ( isset( $options[$id] ) ) {
				return $options[$id];
			}
		}

		public static function add_admin_menu() {
      add_submenu_page(
        'et_divi_options',
        esc_html__('Child Theme Options', 'divi-child'),
        esc_html__('Child Theme Options', 'divi-child'),
        'manage_options',
        'et_divi_child_options',
        array('Divi_Child_Theme_Options', 'create_admin_page'),
        1
      );
		}

		public static function register_settings() {
			register_setting( 'divi_child_options', 'divi_child_options', array( 'Divi_Child_Theme_Options', 'sanitize' ) );
		}

		/**
		 * Sanitization callback
		 *
		 * @since 1.0.0
		 */
		public static function sanitize( $options ) {

			// If we have options lets sanitize them
			if ( $options ) {

				// Checkbox

				// GDPR
				$options['gdpr_comments'] = (!empty($options['gdpr_comments'])) ? 'on' : 'off';

				// Page Speed
				$options['version_query_strings'] = (!empty($options['version_query_strings'])) ? 'on' : 'off';

				// Bug Fixes
				$options['support_center'] = (!empty($options['support_center'])) ? 'on' : 'off';
				$options['tb_header_fix'] = (!empty($options['tb_header_fix'])) ? 'on' : 'off';
				$options['tb_display_errors'] = (!empty($options['tb_display_errors'])) ? 'on' : 'off';

				// Miscellaneous
				$options['stop_mail_updates'] = (!empty($options['stop_mail_updates'])) ? 'on' : 'off';
				$options['svg_support'] = (!empty($options['svg_support'])) ? 'on' : 'off';
				$options['webp_support'] = (!empty($options['webp_support'])) ? 'on' : 'off';


				if ( ! empty( $options['checkbox_example'] ) ) {
					$options['checkbox_example'] = 'on';
				} else {
					unset( $options['checkbox_example'] ); // Remove from options if not checked
				}

				// Input
				if ( ! empty( $options['input_example'] ) ) {
					$options['input_example'] = sanitize_text_field( $options['input_example'] );
				} else {
					unset( $options['input_example'] ); // Remove from options if empty
				}

				// Select
				if ( ! empty( $options['select_example'] ) ) {
					$options['select_example'] = sanitize_text_field( $options['select_example'] );
				}

			}

			// Return sanitized options
			return $options;

		}

		/**
		 * Settings page output
		 *
		 * @since 1.0.0
		 */
		public static function create_admin_page() { ?>

			<div id="divi-child-options" class="wrap">
				<div id="icon-plugins" class="icon32"></div>
				<h1><?php esc_html_e( 'Divi Child Options', 'divi-child' ); ?> <small><?php echo 'v'. DIVI_CHILD_VERSION; ?></small></h1>
				<form method="post" action="options.php">
					<?php settings_fields( 'divi_child_options' ); ?>
					<table class="form-table wpex-custom-admin-login-table">
						<!-- GDPR -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'GDPR', 'divi-child' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'GDPR', 'divi-child' ); ?></span></legend>
									<label for="gdpr_comments">
										<?php $gdpr_comments = self::get_theme_option('gdpr_comments'); ?>
										<input type="checkbox" name="divi_child_options[gdpr_comments]" id="gdpr_comments" <?php checked( $gdpr_comments, 'on' ); ?>> <?php esc_html_e( 'Fix external links in comments and remove commentor\'s IP.', 'divi-child' ); ?>
									</label>
									<br>
								</fieldset>
							</td>
						</tr>
						<!-- PAGE SPEED -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Page Speed', 'divi-child' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Page Speed', 'divi-child' ); ?></span></legend>
									<label for="version_query_strings">
										<?php $version_query_strings = self::get_theme_option('version_query_strings'); ?>
										<input type="checkbox" name="divi_child_options[version_query_strings]" id="version_query_strings" <?php checked( $version_query_strings, 'on' ); ?>> <?php esc_html_e( 'Remove CSS and JS query strings', 'divi-child' ); ?>
									</label>
									<br>
								</fieldset>
							</td>
						</tr>
						<!-- BUG FIXES -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Bug Fixes', 'divi-child' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Bug Fixes', 'divi-child' ); ?></span></legend>
									<label for="support_center">
										<?php $support_center = self::get_theme_option('support_center'); ?>
										<input type="checkbox" name="divi_child_options[support_center]" id="support_center" <?php checked( $support_center, 'on' ); ?>> <?php esc_html_e( 'Remove Divi Support Center from Frontend', 'divi-child' ); ?> <span class="versions"><?php esc_html_e( '(Divi 3.20.1 only)', 'divi-child' ); ?></span>
									</label>
									<br>
									<label for="tb_header_fix">
										<?php $tb_header_fix = self::get_theme_option('tb_header_fix'); ?>
										<input type="checkbox" name="divi_child_options[tb_header_fix]" id="tb_header_fix" <?php checked( $tb_header_fix, 'on' ); ?>> <?php esc_html_e( 'Enable fixed navigation bar option in Theme Builder', 'divi-child' ); ?> <span class="versions"><?php esc_html_e( '(Divi 4.0 and up)', 'divi-child' ); ?></span>
									</label>
									<br>
									<label for="tb_display_errors">
										<?php $tb_display_errors = self::get_theme_option('tb_display_errors'); ?>
										<input type="checkbox" name="divi_child_options[tb_display_errors]" id="tb_display_errors" <?php checked( $tb_display_errors, 'on' ); ?>> <?php esc_html_e( 'Fix display errors in Theme Builder', 'divi-child' ); ?> <span class="versions"><?php esc_html_e( '(Divi 4.0 and up)', 'divi-child' ); ?></span>
										
									</label>
									<br>
								</fieldset>
							</td>
						</tr>
						<!-- MISCELLANIOUS -->
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Miscellaneous', 'divi-child' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span><?php esc_html_e( 'Miscellaneous', 'divi-child' ); ?></span></legend>
									<label for="stop_mail_updates">
										<?php $stop_mail_updates = self::get_theme_option('stop_mail_updates'); ?>
										<input type="checkbox" name="divi_child_options[stop_mail_updates]" id="stop_mail_updates" <?php checked( $stop_mail_updates, 'on' ); ?>> <?php esc_html_e( 'Only send an email when autoupdate was not successful.', 'divi-child' ); ?>
									</label>
									<br>
									<label for="svg_support">
										<?php $svg_support = self::get_theme_option('svg_support'); ?>
										<input type="checkbox" name="divi_child_options[svg_support]" id="svg_support" <?php checked( $svg_support, 'on' ); ?>> <?php esc_html_e( 'Enable to upload SVG files', 'divi-child' ); ?>
									</label>
									<br>
									<label for="webp_support">
										<?php $webp_support = self::get_theme_option('webp_support'); ?>
										<input type="checkbox" name="divi_child_options[webp_support]" id="webp_support" <?php checked( $webp_support, 'on' ); ?>> <?php esc_html_e( 'Enable to upload WebP files', 'divi-child' ); ?>
									</label>
									<br>
								</fieldset>
							</td>
						</tr>

						<?php // Checkbox example ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Checkbox Example', 'divi-child' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'checkbox_example' ); ?>
								<input type="checkbox" name="divi_child_options[checkbox_example]" <?php checked( $value, 'on' ); ?>> <?php esc_html_e( 'Checkbox example description.', 'divi-child' ); ?>
								
							</td>
						</tr>

						<?php // Text input example ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Input Example', 'divi-child' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'input_example' ); ?>
								<input type="text" name="divi_child_options[input_example]" value="<?php echo esc_attr( $value ); ?>">
								<p class="description"><?php esc_html_e('This is a description', 'divi-child'); ?></p>
							</td>
						</tr>

						<?php // Select example ?>
						<tr valign="top" class="wpex-custom-admin-screen-background-section">
							<th scope="row"><?php esc_html_e( 'Select Example', 'divi-child' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'select_example' ); ?>
								<select name="divi_child_options[select_example]">
									<?php
									$options = array(
										'1' => esc_html__( 'Option 1', 'divi-child' ),
										'2' => esc_html__( 'Option 2', 'divi-child' ),
										'3' => esc_html__( 'Option 3', 'divi-child' ),
									);
									foreach ( $options as $id => $label ) { ?>
										<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $value, $id, true ); ?>>
											<?php echo strip_tags( $label ); ?>
										</option>
									<?php } ?>
								</select>
							</td>
						</tr>

					</table>

					<?php submit_button(); ?>

				</form>

			</div><!-- .wrap -->
		<?php }

	}
}
new Divi_Child_Theme_Options();

// Helper function to use in your theme to return a theme option value
function divi_child_get_theme_option( $id = '' ) {
	return Divi_Child_Theme_Options::get_theme_option( $id );
}