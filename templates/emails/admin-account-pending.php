<?php
/**
 * Admin account pending email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'memberhero_email_header', $email_heading, $email ); ?>

<p><?php _e( 'Howdy!', 'memberhero' ); ?></p>

<p><?php printf( __( 'A new user has registered an account at %s and you can manually approve or reject their registration.', 'memberhero' ), esc_html( wp_specialchars_decode( $blogname, ENT_QUOTES ) ) ); ?></p>

<?php

/**
 * Hook to display user meta / account information.
 */
do_action( 'memberhero_email_user_meta', $user, $sent_to_admin, $plain_text, $email );

?>

<p><?php _e( 'You can approve/reject this user by visiting one of the following links:', 'memberhero' ); ?></p>

<p>
	<a href="<?php echo esc_url( memberhero_get_profile_url( $user->user_login ) ); ?>" class="link2"><?php _e( 'View/manage this user', 'memberhero' ); ?></a>
	<a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>" class="link2"><?php _e( 'Visit the users backend &rarr;', 'memberhero' ); ?></a>
</p>

<?php do_action( 'memberhero_email_footer', $email ); ?>