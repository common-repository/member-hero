<?php
/**
 * Manage a user.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-action-menudrop">

	<a href="#" class="memberhero-action-menu tips memberhero-dropdown-init" data-tip="<?php esc_html_e( 'Manage user', 'memberhero' ); ?>" rel="memberhero-dropdown-useradmin"><?php echo memberhero_svg_icon( 'settings' ); ?></a>

	<?php do_action( 'memberhero_manage_user_dropdown' ); ?>

</div>