<?php
/**
 * My Profile nav when not editing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-profile-buttons">

	<?php if ( is_memberhero_my_profile() && current_user_can( 'memberhero_edit_profile' ) ) : ?>

		<a href="<?php echo esc_url( memberhero_get_profile_endpoint_url( 'edit' ) ); ?>" class="memberhero-button nav"><?php esc_html_e( 'Edit profile', 'memberhero' ); ?></a>

	<?php endif; ?>

	<?php do_action( 'memberhero_extend_profile_buttons' ); ?>

</div>