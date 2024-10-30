<?php
/**
 * Account pending email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'memberhero_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'memberhero' ), esc_attr( $user->get_greeting_name() ) ); ?></p>

<p><?php esc_html_e( 'This is a confirmation that your account is pending and that we need to manually review your account details.', 'memberhero' ); ?></p>

<p><?php esc_html_e( 'You will receive another email once your account has been approved.', 'memberhero' ); ?></p>

<p><?php printf( wp_kses_post( __( 'Thanks,<br />%s', 'memberhero' ) ), esc_html( wp_specialchars_decode( $blogname, ENT_QUOTES ) ) ); ?></p>

<?php do_action( 'memberhero_email_footer', $email ); ?>