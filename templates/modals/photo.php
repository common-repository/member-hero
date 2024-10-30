<?php
/**
 * Modal: Profile photo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="memberhero-modal-photo" class="memberhero-modal">

	<div class="modal-header">
		<h3 class="modal-title"><?php _e( 'Position and size your photo', 'memberhero' ); ?></h3>
		<a href="#" class="modal-close" rel="modal:close"><?php echo memberhero_svg_icon( 'x' ); ?></a>
	</div>

	<div class="modal-body">
		<div class="memberhero-cropper-holder">
			<div class="memberhero-cropper">

			</div>
		</div>
	</div>

	<div class="modal-footer">
		<a href="#" class="memberhero-button nav" rel="modal:close"><?php _e( 'Cancel', 'memberhero' ); ?></a>
		<a href="#" class="memberhero-button main memberhero-crop-avatar" data-user_id="<?php echo $the_user->user_id; ?>"><?php _e( 'Apply', 'memberhero' ); ?></a>
	</div>

</div>