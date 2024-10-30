<?php
/**
 * Modal: Delete user.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="memberhero-modal-user-delete" class="memberhero-modal modal-is-centered">

	<div class="modal-header">
		<h3 class="modal-title"><?php _e( 'Delete user', 'memberhero' ); ?></h3>
		<a href="#" class="modal-close" rel="modal:close"><?php echo memberhero_svg_icon( 'x' ); ?></a>
	</div>

	<div class="modal-body modal-important">
		<p class="modal-label">
			<?php _e( 'Delete @<b></b>?', 'memberhero' ); ?>
		</p>
		<p class="modal-notice">
			<?php _e( '@<b></b> profile and all their data will be removed permanently. They will not be able to sign in or view their profile. This action cannot be undone!', 'memberhero' ); ?>
		</p>
	</div>

	<div class="modal-footer alt">
		<a href="#" class="memberhero-button nav" rel="modal:close"><?php _e( 'Cancel', 'memberhero' ); ?></a>
		<a href="#" class="memberhero-button alert memberhero-ajax-action" data-action="delete_user" data-user_id=""><?php _e( 'Delete', 'memberhero' ); ?></a>
	</div>

</div>