<?php
/**
 * Blocked user message.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-section is-centered is-warning">
	<div class="memberhero-strong-heading"><?php echo sprintf( __( '@%s has blocked you.', 'memberhero' ), $the_user->user_login ); ?></div>
	<div class="memberhero-subheading"><?php esc_html_e( 'You will not be able to see their profile or content.', 'memberhero' ); ?></div>
</div>