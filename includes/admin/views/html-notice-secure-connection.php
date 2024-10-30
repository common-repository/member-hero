<?php
/**
 * Admin View: Notice - Secure connection.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="message" class="updated memberhero-message">

	<a class="memberhero-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'memberhero-hide-notice', 'no_secure_connection' ), 'memberhero_hide_notices_nonce', '_memberhero_notice_nonce' ) ); ?>">
		<?php esc_html_e( 'Dismiss', 'memberhero' ); ?>
	</a>

	<p>
	<?php
		printf(
			wp_kses_post(
				__( 'Your website does not appear to be using a secure connection. We highly recommend serving your entire website over an HTTPS connection to help keep user data secure. <a href="%s">Learn more.</a>', 'memberhero' )
			),
			'https://docs.memberhero.pro/article/57-do-i-need-ssl'
		);
	?>
	</p>

</div>