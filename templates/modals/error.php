<?php
/**
 * Modal: Error
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="memberhero-modal-error" class="memberhero-modal modal-is-centered">

	<div class="modal-header">
		<h3 class="modal-title"><?php _e( 'Something went wrong!', 'memberhero' ); ?></h3>
		<a href="#" class="modal-close" rel="modal:close"><?php echo memberhero_svg_icon( 'x' ); ?></a>
	</div>

	<div class="modal-body modal-important">
		<p class="modal-label">
			<?php _e( 'An error has occured.', 'memberhero' ); ?>
		</p>
		<p class="modal-notice">
			<?php _e( 'Something went wrong and we could not process your request right now. Please click the button to try again.', 'memberhero' ); ?>
		</p>
	</div>

	<div class="modal-footer alt">
		<a href="#" class="memberhero-button nav" rel="modal:close"><?php _e( 'Try again', 'memberhero' ); ?></a>
	</div>

</div>