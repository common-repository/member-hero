<?php
/**
 * My Profile info
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-profile-info">

	<?php do_action( 'memberhero_profile_photo' ); ?>

	<div class="memberhero-profile-about">

		<?php do_action( 'memberhero_profile_username' ); ?>

		<?php do_action( 'memberhero_profile_nav_buttons' ); ?>

		<?php
			if ( ! $the_user->is_private() || current_user_can( 'memberhero_view_private' ) || ( $the_user->ID == get_current_user_id() ) ) {
				do_action( 'memberhero_profile_bio' );
				do_action( 'memberhero_profile_sidemeta' );
			}
		?>

	</div>

</div>