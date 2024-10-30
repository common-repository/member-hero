<?php
/**
 * My Account - Delete
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_account_form' );

?>

<form action="" method="post">

	<div class="memberhero-p">
		<?php _e( 'Are you sure you want to delete your account? This will permanently delete all your account data, photos, uploads, and everything.', 'memberhero' ); ?>
	</div>

	<div class="memberhero-p">
		<strong><?php _e( 'Please note that this action cannot be undone.', 'memberhero' ); ?></strong>
	</div>

	<div class="memberhero-buttons">

		<a href="#" class="memberhero-button alert memberhero-modal-open" rel="memberhero-modal-delete-account">
			<?php echo esc_attr( __( 'Yes, delete my account', 'memberhero' ) ); ?>
		</a>

		<a href="<?php echo esc_url( home_url() ); ?>" class="memberhero-button nav">
			<?php echo esc_html( __( 'No, keep my account', 'memberhero' ) ); ?>
		</a>

	</div>

</form>