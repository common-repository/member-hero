<?php
/**
 * Password changed email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'memberhero_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'memberhero' ), esc_attr( $user->get_greeting_name() ) ); ?></p>

<p><?php printf( esc_html__( 'This is a confirmation that your password has been changed successfully at %s %s on %s', 'memberhero' ),  current_time( 'g:i a' ), get_option( 'timezone_string' ) ? '(' . get_option( 'timezone_string' ) . ')' : '', current_time( 'F j, Y' ) ); ?></p>

<p class="lighter"><?php esc_html_e( 'If you did not authorize this change, your account may have been compromised. Please contact us as soon as possible in such case.', 'memberhero' ); ?></p>

<?php do_action( 'memberhero_email_footer', $email ); ?>