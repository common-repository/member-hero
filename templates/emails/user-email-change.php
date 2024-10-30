<?php
/**
 * Email change confirmation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'memberhero_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'memberhero' ), esc_attr( $user->get_greeting_name() ) ); ?></p>

<p><?php printf( wp_kses_post( __( 'We would like to confirm that you prefer using %s as your primary email for %s.', 'memberhero' ) ), '<strong>' . str_replace( array( '@', '.' ), array( '<span>@</span>', '<span>.</span>' ), $user->get_pending_email() ) . '</strong>', esc_html( wp_specialchars_decode( $blogname, ENT_QUOTES ) ) ); ?></p>

<p><?php esc_html_e( 'Just click on the button below to confirm your new address.', 'memberhero' ); ?></p>

<p class="button"><a href="<?php echo esc_url( add_query_arg( array( 'email_key' => $secret_key, 'id' => $user->ID ), memberhero_get_page_permalink( 'account' ) ) ); ?>" class="link"><?php esc_html_e( 'Confirm your email', 'memberhero' ); ?></a></p>

<p class="lighter"><?php printf( wp_kses_post( __( 'If you do not want to do this change, just disregard this email. You can login to <a href="%s">your account</a> and cancel this change.', 'memberhero' ) ), memberhero_get_page_permalink( 'account' ) );?></p>

<?php do_action( 'memberhero_email_footer', $email ); ?>