<?php
/**
 * Admin view: Import users.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="import-users" class="settings-panel">
	<h2><?php esc_html_e( 'Import users', 'memberhero' ); ?></h2>

	<div class="memberhero-info">
		<p><?php echo sprintf( __( '<strong>Important!</strong><br />Before you start your import, ensure your data labels (the first row of your csv file) match the labels found in <a href="%s" target="_blank">custom fields</a> settings.', 'memberhero' ), admin_url( 'edit.php?post_type=memberhero_field' ) ); ?></p>
		<p><?php echo __( 'Here is an example of how you should format the first row of your CSV file.', 'memberhero' ); ?></p>
		<p><img src="<?php echo memberhero()->plugin_url(); ?>/assets/sample/csv_image.png" /></p>
		<p><?php echo sprintf( __( 'Download example CSV <a href="%s" target="_blank">here</a>.', 'memberhero' ), memberhero()->plugin_url() . '/assets/sample/import.csv' ); ?></p>
	</div>

	<table id="import-users-options" class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="csv_file">
						<?php esc_html_e( 'CSV file', 'memberhero' ); ?>
						<?php echo memberhero_help_tip( esc_html__( 'Upload a CSV file to import users from', 'memberhero' ) ); ?>
					</label>
				</th>
				<td class="forminp">
					<input type="file" name="csv_file" id="csv_file"  multiple="false" accept=".csv" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php esc_html_e( 'Notification', 'memberhero' ); ?>
					<?php echo memberhero_help_tip( esc_html__( 'Send the account details to user email', 'memberhero' ) ); ?>
				</th>
				<td class="forminp">
					<label for="import_notification">
						<input name="import_notification" type="checkbox" id="import_notification" value="1" checked>
						<?php esc_html_e( 'Send email notification to new users', 'memberhero' ); ?>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php esc_html_e( 'Update users', 'memberhero' ); ?>
					<?php echo memberhero_help_tip( esc_html__( 'Check this box to update existing users if found', 'memberhero' ) ); ?>
				</th>
				<td class="forminp">
					<label for="import_override">
						<input name="import_override" type="checkbox" id="import_override" value="1">
						<?php esc_html_e( 'Update existing user if a username or email already exists (leave box unchecked to create a new user).', 'memberhero' ); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>

	<?php do_action( 'memberhero_admin_import_users' ); ?>

	<p class="submit">
		<?php submit_button( esc_html__( 'Import users', 'memberhero' ), 'primary', 'import_users', false ); ?>
	</p>

</div>