<?php
/**
 * Modal: Remove profile cover
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="memberhero-modal-remove-cover" class="memberhero-modal modal-is-centered">

	<div class="modal-header">
		<h3 class="modal-title"><?php _e( 'Remove header photo?', 'memberhero' ); ?></h3>
		<a href="#" class="modal-close" rel="modal:close"><?php echo memberhero_svg_icon( 'x' ); ?></a>
	</div>

	<div class="modal-body">
		<p><?php _e( 'Your header photo makes your profile more personal.', 'memberhero' ); ?><br />
			<?php _e( 'Are you sure you want to remove it?', 'memberhero' ); ?></p>
	</div>

	<div class="modal-footer">
		<a href="#" class="memberhero-button main memberhero-ajax-action" data-action="remove_cover" data-user_id="<?php echo $the_user->user_id; ?>"><?php _e( 'Remove header', 'memberhero' ); ?></a>
		<a href="#" class="memberhero-button nav" rel="modal:close"><?php _e( 'Cancel', 'memberhero' ); ?></a>
	</div>

</div>