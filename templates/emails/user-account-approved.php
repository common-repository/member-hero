<?php
/**
 * Account approved email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'memberhero_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'memberhero' ), esc_attr( $user->get_greeting_name() ) ); ?></p>

<p><?php esc_html_e( 'We’re glad to inform you that your account has been approved.', 'memberhero' ); ?></p>

<p><?php printf( wp_kses_post( __( 'You can now view and edit your profile, manage your account settings and do more at %s.', 'memberhero' ) ), make_clickable( esc_url( memberhero_get_profile_url( $user->user_login ) ) ) ); ?></p>

<p class="button"><a href="<?php echo esc_url( memberhero_get_profile_url( $user->user_login ) ); ?>" class="link"><?php esc_html_e( 'View your Profile', 'memberhero' ); ?></a></p>

<p><?php printf( wp_kses_post( __( 'Thanks,<br />%s', 'memberhero' ) ), esc_html( wp_specialchars_decode( $blogname, ENT_QUOTES ) ) ); ?></p>

<?php do_action( 'memberhero_email_footer', $email ); ?>