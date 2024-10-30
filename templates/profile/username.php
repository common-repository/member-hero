<?php
/**
 * My Profile username
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_profile_username' );
?>

<div class="memberhero-profile-user">

	<div class="memberhero-profile-name">
		<a href="<?php echo memberhero_get_profile_url(); ?>"><?php echo esc_html( $the_user->get( 'display_name' ) ); ?></a>

		<?php do_action( 'memberhero_after_profile_name' ); ?>
	</div>

	<div class="memberhero-profile-hash">
		<a href="<?php echo memberhero_get_profile_url(); ?>">
			<span>@<b><?php echo esc_html( $the_user->get( 'user_login' ) ); ?></b></span>
		</a>
	</div>

	<?php
		// Action to display in member list.
		if ( ! empty( $the_list ) ) :
			memberhero_list_actions();
		endif;
	?>

</div>

<?php do_action( 'memberhero_after_profile_username' ); ?>