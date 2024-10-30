<?php
/**
 * Modal: Block user.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="memberhero-modal-block" class="memberhero-modal modal-is-centered">

	<div class="modal-header">
		<h3 class="modal-title"><?php _e( 'Block', 'memberhero' ); ?></h3>
		<a href="#" class="modal-close" rel="modal:close"><?php echo memberhero_svg_icon( 'x' ); ?></a>
	</div>

	<div class="modal-body modal-important">
		<p class="modal-label">
			<?php _e( 'Block @<b></b>?', 'memberhero' ); ?>
		</p>
		<p class="modal-notice">
			<?php _e( '@<b></b> will no longer be able to see your profile or send you notifications, and you will not be able to see @<b></b>.', 'memberhero' ); ?>
		</p>
	</div>

	<div class="modal-footer alt">
		<a href="#" class="memberhero-button nav" rel="modal:close"><?php _e( 'Cancel', 'memberhero' ); ?></a>
		<a href="#" class="memberhero-button alert memberhero-ajax-action" data-action="block_user" data-user_id=""><?php _e( 'Block', 'memberhero' ); ?></a>
	</div>

</div>