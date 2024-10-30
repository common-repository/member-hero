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

<p><?php printf( esc_html__( 'Thank you for registering an account at %s.', 'memberhero' ), esc_html( wp_specialchars_decode( $blogname, ENT_QUOTES ) ) ); ?></p>

<?php if ( isset( $form->password_generated ) ) : ?>
<p><?php printf( wp_kses_post( __( 'Your password has been automatically generated: %s', 'memberhero' ) ), '<strong>' . esc_html( $form->password_generated ) . '</strong>' ); ?></p>
<?php endif; ?>

<p><?php printf( wp_kses_post( __( 'You can now view and edit your profile, manage your account settings and do more at %s.', 'memberhero' ) ), make_clickable( esc_url( memberhero_get_profile_url( $user->user_login ) ) ) ); ?></p>

<p class="button"><a href="<?php echo esc_url( memberhero_get_profile_url( $user->user_login ) ); ?>" class="link"><?php esc_html_e( 'View your Profile', 'memberhero' ); ?></a></p>

<p><?php printf( wp_kses_post( __( 'Thanks,<br />%s', 'memberhero' ) ), esc_html( wp_specialchars_decode( $blogname, ENT_QUOTES ) ) ); ?></p>

<p class="lighter"><?php esc_html_e( 'If you did not create this account, please contact us so we can fix this for you.', 'memberhero' ); ?></p>

<?php do_action( 'memberhero_email_footer', $email ); ?>