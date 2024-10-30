<?php
/**
 * Admin view: Edit API keys
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="key-fields" class="settings-panel">
	<h2><?php esc_html_e( 'Key details', 'memberhero' ); ?></h2>

	<input type="hidden" id="key_id" value="<?php echo esc_attr( $key_id ); ?>" />

	<table id="api-keys-options" class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="key_description">
						<?php esc_html_e( 'Description', 'memberhero' ); ?>
						<?php echo memberhero_help_tip( esc_html__( 'Friendly name for identifying this key.', 'memberhero' ) ); ?>
					</label>
				</th>
				<td class="forminp">
					<input id="key_description" type="text" class="input-text regular-input" value="<?php echo esc_attr( $key_data['description'] ); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="key_user">
						<?php esc_html_e( 'User', 'memberhero' ); ?>
						<?php echo memberhero_help_tip( esc_html__( 'Owner of these keys.', 'memberhero' ) ); ?>
					</label>
				</th>
				<td class="forminp">
					<?php
					$current_user_id = get_current_user_id();
					$user_id         = ! empty( $key_data['user_id'] ) ? absint( $key_data['user_id'] ) : $current_user_id;
					$user            = get_user_by( 'id', $user_id );
					$user_string     = sprintf(
						/* translators: 1: user display name 2: user ID 3: user email */
						esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'memberhero' ),
						$user->display_name,
						absint( $user->ID ),
						$user->user_email
					);
					?>
					<select class="memberhero-user-search" id="key_user" data-placeholder="<?php esc_attr_e( 'Search for a user&hellip;', 'memberhero' ); ?>">
						<option value="<?php echo esc_attr( $user_id ); ?>" selected="selected" 
						data-data='{"id": "<?php echo absint( $user_id ); ?>", "name": "<?php echo esc_attr( $user->display_name ); ?>", "email": "<?php echo esc_attr( $user->user_email ); ?>"}'><?php echo wp_kses_post( $user_string ); ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="key_permissions">
						<?php esc_html_e( 'Permissions', 'memberhero' ); ?>
						<?php echo memberhero_help_tip( esc_html__( 'Select the access type of these keys.', 'memberhero' ) ); ?>
					</label>
				</th>
				<td class="forminp">
					<select id="key_permissions" class="memberhero-select small">
						<?php
						$permissions = array(
							'read'       => esc_html__( 'Read', 'memberhero' ),
							'write'      => esc_html__( 'Write', 'memberhero' ),
							'read_write' => esc_html__( 'Read/Write', 'memberhero' ),
						);

						foreach ( $permissions as $permission_id => $permission_name ) :
							?>
							<option value="<?php echo esc_attr( $permission_id ); ?>" <?php selected( $key_data['permissions'], $permission_id, true ); ?>><?php echo esc_html( $permission_name ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<?php if ( 0 !== $key_id ) : ?>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<?php esc_html_e( 'Consumer key ending in', 'memberhero' ); ?>
					</th>
					<td class="forminp">
						<code>&hellip;<?php echo esc_html( $key_data['truncated_key'] ); ?></code>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<?php esc_html_e( 'Last access', 'memberhero' ); ?>
					</th>
					<td class="forminp">
						<span>
						<?php
						if ( ! empty( $key_data['last_access'] ) ) {
							/* translators: 1: last access date 2: last access time */
							$date = sprintf( esc_html__( '%1$s at %2$s', 'memberhero' ), date_i18n( memberhero_date_format(), strtotime( $key_data['last_access'] ) ), date_i18n( memberhero_time_format(), strtotime( $key_data['last_access'] ) ) );

							echo esc_html( apply_filters( 'memberhero_api_key_last_access_datetime', $date, $key_data['last_access'] ) );
						} else {
							esc_html_e( 'Unknown', 'memberhero' );
						}
						?>
						</span>
					</td>
				</tr>
			<?php endif ?>
		</tbody>
	</table>

	<?php do_action( 'memberhero_admin_key_fields', $key_data ); ?>

	<?php
	if ( 0 === intval( $key_id ) ) {
		submit_button( esc_html__( 'Generate API key', 'memberhero' ), 'primary', 'update_api_key' );
	} else {
		?>
		<p class="submit">
			<?php submit_button( esc_html__( 'Save changes', 'memberhero' ), 'primary', 'update_api_key', false ); ?>
			<a style="color: #a00; text-decoration: none; margin-left: 10px;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'revoke-key' => $key_id ), admin_url( 'admin.php?page=memberhero-settings&tab=advanced&section=keys' ) ), 'revoke' ) ); ?>"><?php esc_html_e( 'Revoke key', 'memberhero' ); ?></a>
		</p>
		<?php
	}
	?>
</div>

<script type="text/template" id="tmpl-api-keys-template">
	<p id="copy-error"></p>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php esc_html_e( 'Consumer key', 'memberhero' ); ?>
				</th>
				<td class="forminp">
					<input id="key_consumer_key" type="text" value="{{ data.consumer_key }}" size="55" readonly="readonly"> <button type="button" class="button-secondary copy-key" data-tip="<?php esc_attr_e( 'Copied!', 'memberhero' ); ?>"><?php esc_html_e( 'Copy', 'memberhero' ); ?></button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php esc_html_e( 'Consumer secret', 'memberhero' ); ?>
				</th>
				<td class="forminp">
					<input id="key_consumer_secret" type="text" value="{{ data.consumer_secret }}" size="55" readonly="readonly"> <button type="button" class="button-secondary copy-secret" data-tip="<?php esc_attr_e( 'Copied!', 'memberhero' ); ?>"><?php esc_html_e( 'Copy', 'memberhero' ); ?></button>
				</td>
			</tr>
		</tbody>
	</table>
</script>