<?php
/**
 * Account confirmation email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'memberhero_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'memberhero' ), esc_attr( $user->get_greeting_name() ) ); ?></p>

<p><?php printf( esc_html__( 'There’s one quick step to complete before creating your %s account. We need you to confirm your email address.', 'memberhero' ), esc_html( wp_specialchars_decode( $blogname, ENT_QUOTES ) ) ); ?></p>

<p class="tight"><?php esc_html_e( 'Please enter this verification code to get started:', 'memberhero' ); ?></p>

<p class="code"><?php echo substr_replace( $confirmation_code, '-', 3, 0 ); ?></p>

<p><?php printf( wp_kses_post( __( 'Thanks,<br />%s', 'memberhero' ) ), esc_html( wp_specialchars_decode( $blogname, ENT_QUOTES ) ) ); ?></p>

<p class="lighter"><?php esc_html_e( 'If you did not register for an account or received this by mistake, you can safely ignore this email. Your account setup will only be completed if you enter the above code.', 'memberhero' ); ?></p>

<?php do_action( 'memberhero_email_footer', $email ); ?>