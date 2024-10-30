<?php
/**
 * Content restriction metabox.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="components-base-control" id="memberhero-access">
	<h4><?php _e( 'Who can view this content?', 'memberhero' ); ?></h4>
	<div class="components-panel__row">
		<div class="components-base-control">
			<div class="components-base-control__field">
				<label class="components-checkbox-control__label" for="memberhero-access-everyone">
					<input name="_memberhero_access" id="memberhero-access-everyone" class="components-checkbox-control__input" type="radio" value="everyone" <?php checked( $access, 'everyone' ); ?>>
					<?php _e( 'Everyone', 'memberhero' ); ?>
				</label>
			</div>
		</div>
	</div>
	<div class="components-panel__row">
		<div class="components-base-control">
			<div class="components-base-control__field">
				<label class="components-checkbox-control__label" for="memberhero-access-members">
					<input name="_memberhero_access" id="memberhero-access-members" class="components-checkbox-control__input" type="radio" value="members" <?php checked( $access, 'members' ); ?>>
					<?php _e( 'Logged-in users', 'memberhero' ); ?>
				</label>
			</div>
		</div>
	</div>
	<div class="components-panel__row">
		<div class="components-base-control">
			<div class="components-base-control__field">
				<label class="components-checkbox-control__label" for="memberhero-access-guests">
					<input name="_memberhero_access" id="memberhero-access-guests" class="components-checkbox-control__input" type="radio" value="guests" <?php checked( $access, 'guests' ); ?>>
					<?php _e( 'Non-logged in users', 'memberhero' ); ?>
				</label>
			</div>
		</div>
	</div>
</div>

<div class="components-base-control memberhero_hide_if_everyone memberhero_show_if_logged">
	<h4><?php _e( 'Restrict this content to specific roles', 'memberhero' ); ?></h4>
	<?php foreach( memberhero_get_roles() as $key => $role ) : ?>
	<div class="components-panel__row">
		<div class="components-base-control">
			<div class="components-base-control__field">
				<input name="_memberhero_roles[]" id="memberhero-role-<?php echo $key; ?>" type="checkbox" value="<?php echo $key; ?>"
				<?php if ( in_array( $key, $roles ) ) echo 'checked'; ?>>
				<label class="components-checkbox-control__label" for="memberhero-role-<?php echo $key; ?>"><?php echo $role; ?></label>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>

<div class="components-base-control memberhero_hide_if_everyone">
	<h4><?php _e( 'Redirection settings', 'memberhero' ); ?></h4>
	<div class="components-panel__row">
		<div class="components-base-control">
			<div class="components-base-control__field">
				<label class="components-base-control__label" for="_memberhero_redirect"><?php _e( 'Redirect user to:', 'memberhero' ); ?></label>
				<select name="_memberhero_redirect" id="_memberhero_redirect">
					<?php foreach( $options as $slug => $title ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $slug, $redirect ); ?>><?php echo esc_html( $title ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
	<div class="components-panel__row memberhero_show_if_custom">
		<div class="components-base-control">
			<div class="components-base-control__field">
				<label class="components-base-control__label" for="_memberhero_redirect_url"><?php _e( 'Custom URL:', 'memberhero' ); ?></label>
				<input class="components-text-control__input" type="text" id="_memberhero_redirect_url" name="_memberhero_redirect_url" value="<?php echo esc_attr( $custom_url ); ?>" placeholder="http://">
			</div>
		</div>
	</div>
</div>