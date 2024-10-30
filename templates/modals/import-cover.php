<?php
/**
 * Modal: Import cover photo from URL.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="memberhero-modal-import-cover" class="memberhero-modal memberhero-modal-sm modal-is-centered">

	<div class="modal-header">
		<h3 class="modal-title"><?php _e( 'Import from URL', 'memberhero' ); ?></h3>
		<a href="#" class="modal-close" rel="modal:close"><?php echo memberhero_svg_icon( 'x' ); ?></a>
	</div>

	<div class="modal-body modal-important">
		<p><?php _e( 'Enter the URL of your cover photo below.', 'memberhero' ); ?></p>
		<p class="memberhero-input"><input type="text" name="import_cover_url" id="import_cover_url" placeholder="http://" /></p>
	</div>

	<div class="modal-footer">
		<a href="#" class="memberhero-button main memberhero-ajax-action memberhero-loader" data-action="import_cover" data-user_id="<?php echo $the_user->user_id; ?>" data-onmodal="true"><?php _e( 'Import', 'memberhero' ); ?></a>
		<a href="#" class="memberhero-button nav" rel="modal:close"><?php _e( 'Cancel', 'memberhero' ); ?></a>
	</div>

</div>