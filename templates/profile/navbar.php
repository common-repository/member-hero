<?php
/**
 * My Profile nav bar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_profile_navbar' );
?>

<div class="memberhero-navbar">

	<div class="memberhero-right-actions">

		<div class="memberhero-navbar-photo">
			<a href="#" class="memberhero-navbar-avatar memberhero-btn memberhero-navbar-tip memberhero-dropdown-init" data-tip="<?php esc_html_e( 'Profile and account', 'memberhero' ); ?>" rel="memberhero-dropdown-user">
				<?php echo get_avatar( get_current_user_id(), 48 ); ?>
			</a>
			<?php do_action( 'memberhero_user_dropdown_menu' ); ?>
		</div>

	</div>

</div>

<?php do_action( 'memberhero_after_profile_navbar' ); ?>