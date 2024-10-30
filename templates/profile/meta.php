<?php
/**
 * My Profile side meta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( memberhero_user_has_blocked( $the_user->user_id ) ) {
	return;
}

do_action( 'memberhero_before_profile_sidemeta' );

?>

<div class="memberhero-profile-meta">

	<?php if ( $the_user->get( 'country' ) ) : ?>
	<div class="memberhero-profile-metadata">
		<?php echo memberhero_svg_icon( 'map-pin' ); ?>
		<span><?php echo memberhero_get_user_country(); ?></span>
	</div>
	<?php endif; ?>

	<?php if ( $the_user->get( 'user_url' ) ) : ?>
	<div class="memberhero-profile-metadata">
		<?php echo memberhero_svg_icon( 'link' ); ?>
		<span><a href="<?php echo esc_url( $the_user->get( 'user_url' ) ); ?>" target="_blank" rel="nofollow"><?php echo memberhero_esc_url( $the_user->get( 'user_url' ) ); ?></a></span>
	</div>
	<?php endif; ?>

	<div class="memberhero-profile-metadata">
		<?php echo memberhero_svg_icon( 'calendar' ); ?>
		<span class="memberhero-tips" data-tip="<?php echo memberhero_get_user_registered_details(); ?>">
			<?php echo sprintf( esc_html__( 'Joined %s', 'memberhero' ), memberhero_get_user_registered() ); ?>
		</span>
	</div>

	<?php do_action( 'memberhero_inside_profile_sidemeta' ); ?>

</div>

<?php memberhero_show_meta_counts(); ?>

<?php do_action( 'memberhero_after_profile_sidemeta' ); ?>