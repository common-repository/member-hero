<?php
/**
 * Shows a message when new account is pending.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-info big memberhero-new-account-pending">

	<h1><?php esc_html_e( 'Thank you!', 'memberhero' ); ?></h1>

	<p>
		<?php esc_html_e( 'Thank you for registering! Your account will be reviewed shortly. You will receive an email once your account status is updated.', 'memberhero' ); ?>
	</p>

</div>