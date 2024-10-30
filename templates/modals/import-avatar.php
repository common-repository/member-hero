<?php
/**
 * Modal: Import avatar photo from URL.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="memberhero-modal-import-avatar" class="memberhero-modal memberhero-modal-sm modal-is-centered">

	<div class="modal-header">
		<h3 class="modal-title"><?php _e( 'Import from URL', 'memberhero' ); ?></h3>
		<a href="#" class="modal-close" rel="modal:close"><?php echo memberhero_svg_icon( 'x' ); ?></a>
	</div>

	<div class="modal-body modal-important">
		<p><?php _e( 'Enter the URL of your photo below.', 'memberhero' ); ?></p>
		<p class="memberhero-input"><input type="text" name="import_avatar_url" id="import_avatar_url" placeholder="http://" /></p>
	</div>

	<div class="modal-footer">
		<a href="#" class="memberhero-button main memberhero-ajax-action memberhero-loader" data-action="import_avatar" data-user_id="<?php echo $the_user->user_id; ?>" data-onmodal="true"><?php _e( 'Import', 'memberhero' ); ?></a>
		<a href="#" class="memberhero-button nav" rel="modal:close"><?php _e( 'Cancel', 'memberhero' ); ?></a>
	</div>

</div>