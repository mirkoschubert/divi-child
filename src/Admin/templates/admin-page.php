<?php defined('ABSPATH') or die('No script kiddies please!'); ?>

<div id="divi-child-options" class="wrap">
	<div class="options-header">
		<div id="icon-plugins" class="icon32"></div>
		<h1><?php esc_html_e('Divi Child Options', 'divi-child'); ?> <small><?php echo 'v' . DIVI_CHILD_VERSION; ?></small>
	</div>
	</h1>
	<form method="post" action="options.php">
		<?php settings_fields('divi_child_options'); ?>
		<table class="form-table wpex-custom-admin-login-table">
			<!-- GDPR -->
			<tr valign="top">
				<th scope="row"><?php esc_html_e('GDPR', 'divi-child'); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php esc_html_e('GDPR', 'divi-child'); ?></span></legend>
						
					</fieldset>
				</td>
			</tr>
			<!-- PAGE SPEED -->
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Page Speed', 'divi-child'); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php esc_html_e('Page Speed', 'divi-child'); ?></span></legend>
						
					</fieldset>
				</td>
			</tr>
			<!-- ACCESSIBILITY -->
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Accessibility', 'divi-child'); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php esc_html_e('Accessibility', 'divi-child'); ?></span></legend>
						
					</fieldset>
				</td>
			</tr>
			<!-- BUG FIXES -->
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Bug Fixes', 'divi-child'); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php esc_html_e('Bug Fixes', 'divi-child'); ?></span></legend>
						
					</fieldset>
				</td>
			</tr>
			<!-- MISCELLANIOUS -->
			<tr valign="top">
				<th scope="row"><?php esc_html_e('Miscellaneous', 'divi-child'); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php esc_html_e('Miscellaneous', 'divi-child'); ?></span></legend>
						
					</fieldset>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>

</div><!-- .wrap -->