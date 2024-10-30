<?php
/**
 * Account rejected email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'memberhero_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'memberhero' ), esc_attr( $user->get_greeting_name() ) ); ?></p>

<p><?php esc_html_e( 'We apologize for the inconvenience, but following a thorough review of your account we are unable to accept your registration at this time.', 'memberhero' ); ?></p>

<p><?php printf( wp_kses_post( __( 'Thanks,<br />%s', 'memberhero' ) ), esc_html( wp_specialchars_decode( $blogname, ENT_QUOTES ) ) ); ?></p>

<?php do_action( 'memberhero_email_footer', $email ); ?>