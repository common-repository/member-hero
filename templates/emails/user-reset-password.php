<?php
/**
 * Reset password email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'memberhero_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'memberhero' ), esc_attr( $user->get_greeting_name() ) ); ?></p>

<p><?php esc_html_e( 'If you’ve lost your password or wish to reset it, use the link below to get started.', 'memberhero' ); ?></p>

<p class="button"><a href="<?php echo esc_url( add_query_arg( array( 'password_key' => $reset_key, 'id' => $user->ID ), memberhero_get_page_permalink( 'lostpassword' ) ) ); ?>" class="link"><?php esc_html_e( 'Reset your Password', 'memberhero' ); ?></a></p>

<p class="hr"></p>

<p><?php esc_html_e( 'If you did not request a password reset, you can safely ignore this email. Only a person with access to your email can reset your account password.', 'memberhero' ); ?></p>

<?php do_action( 'memberhero_email_footer', $email ); ?>