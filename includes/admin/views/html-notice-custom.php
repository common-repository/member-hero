<?php
/**
 * Admin View: Custom Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated memberhero-message">

	<a class="memberhero-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'memberhero-hide-notice', $notice ), 'memberhero_hide_notices_nonce', '_memberhero_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'memberhero' ); ?></a>
	<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>

</div>
