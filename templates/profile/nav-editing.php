<?php
/**
 * My Profile nav when editing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_profile_nav_edit' );

?>

<div class="memberhero-profile-nav-edit">

	<div class="memberhero-profile-buttons">

		<a href="<?php echo esc_url( memberhero_get_profile_endpoint_url( 'view' ) ); ?>" class="memberhero-button memberhero-cancel nav">
			<?php esc_html_e( 'Cancel', 'memberhero' ); ?>
		</a>

		<a href="#" class="memberhero-button main">
			<?php esc_html_e( 'Save changes', 'memberhero' ); ?>
		</a>

	</div>

</div>