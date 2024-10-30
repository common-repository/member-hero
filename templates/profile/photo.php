<?php
/**
 * My Profile photo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_profile_photo' );
?>

<div class="memberhero-profile-photo" data-user_id="<?php echo $the_user->user_id; ?>">

	<?php if ( is_memberhero_editing_profile() ) : ?>

		<a href="#" class="memberhero-profile-photo-edit-overlay memberhero-dropdown-init" rel="memberhero-dropdown-photo">
			<?php echo memberhero_svg_icon( 'camera' ); ?>
		</a>

		<?php do_action( 'memberhero_user_photo_dropdown' ); ?>

	<?php endif; ?>

	<?php if ( memberhero_user_can_edit_profile() && memberhero_user_has_no_gravatar() ) : ?>

		<div class="memberhero-profile-photo-add-wrap memberhero-tips" data-tip="<?php echo esc_attr( __( 'Add a profile avatar', 'memberhero' ) ); ?>">
			<a href="<?php echo esc_url( memberhero_get_profile_endpoint_url( 'edit' ) ); ?>" class="memberhero-profile-photo-add memberhero-dropdown-init" rel="memberhero-dropdown-photo"><?php echo memberhero_svg_icon( 'camera' ); ?></a>
		</div>

	<?php else : ?>

		<a href="<?php echo memberhero_get_profile_url(); ?>" class="memberhero-profile-photo-link memberhero-tips" data-tip="<?php echo esc_attr( $the_user->display_name ); ?>">
			<?php echo get_avatar( $the_user->user_id, memberhero_get_avatar_template_size() ); ?>
		</a>

	<?php endif; ?>

	<?php do_action( 'memberhero_user_profile_photo' ); ?>

</div>

<?php do_action( 'memberhero_after_profile_photo' ); ?>