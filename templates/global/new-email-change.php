<?php
/**
 * Shows a message when new account is pending.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-controls">

	<p>
		<?php
			printf( esc_html__( 'Check your email (%s) to confirm your new address. Until you confirm, notifications will continue to be sent to your current email address.', 'memberhero' ), $the_user->get_pending_email() );
		?>
	</p>

	<div class="memberhero-controls-links">
		<a href="#" class="memberhero-ajax-action memberhero-hide-controls" data-action="resend_confirmation"><?php esc_html_e( 'Resend confirmation', 'memberhero' ); ?></a>
		<span class="memberhero-bullet">&#183; </span>
		<a href="#" class="memberhero-ajax-action memberhero-hide-controls" data-action="cancel_pending_email"><?php esc_html_e( 'Cancel this change', 'memberhero' ); ?></a>
	</div>

</div>