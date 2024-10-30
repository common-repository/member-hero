<?php
/**
 * User actions menu.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $the_user->user_id == get_current_user_id() || ! is_user_logged_in() ) {
	return;
}

?>

<div class="memberhero-action-menudrop">

	<a href="#" class="memberhero-action-menu tips memberhero-dropdown-init" data-tip="<?php esc_html_e( 'User actions', 'memberhero' ); ?>" rel="memberhero-dropdown-useractions">
		<?php echo memberhero_svg_icon( 'more-vertical' ); ?>
	</a>

	<?php do_action( 'memberhero_user_actions_dropdown' ); ?>

</div>