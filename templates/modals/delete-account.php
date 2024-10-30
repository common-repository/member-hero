<?php
/**
 * Modal: Delete my account.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( 'delete' !== memberhero()->query->get_current_endpoint() ) {
	return;
}

?>

<div id="memberhero-modal-delete-account" class="memberhero-modal memberhero-modal-sm modal-is-centered">

	<div class="modal-header">
		<h3 class="modal-title"><?php _e( 'Delete account', 'memberhero' ); ?></h3>
		<a href="#" class="modal-close" rel="modal:close"><?php echo memberhero_svg_icon( 'x' ); ?></a>
	</div>

	<div class="modal-body">
		<p><?php _e( 'Please click on the <strong>DELETE</strong> button to permanently delete your account.', 'memberhero' ); ?></p>
	</div>

	<div class="modal-footer alt">

		<form action="" method="post">
			<?php wp_nonce_field( 'memberhero_delete_account', 'memberhero_delete_account_field' ); ?>
			<a href="#" class="memberhero-button nav" rel="modal:close"><?php _e( 'Cancel', 'memberhero' ); ?></a>
			<input type="submit" value="<?php echo esc_attr( __( 'DELETE', 'memberhero' ) ); ?>" class="memberhero-button alert">
		</form>

	</div>

</div>