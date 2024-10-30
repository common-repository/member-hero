<?php
/**
 * My Account - Blocked
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_account_form' );

if ( $users ) :

?>

<div class="memberhero-section" data-content="blocked">

	<?php foreach( $users as $user_id => $time ) : $the_user = memberhero_get_user( $user_id ); ?>

	<div class="memberhero-item" data-permalink="<?php echo memberhero_get_profile_url(); ?>">
		<div class="memberhero-item-image">
			<a href="<?php echo memberhero_get_profile_url(); ?>" class="memberhero-item-image-avatar"><?php echo get_avatar( $user_id, 50 ); ?><?php do_action( 'memberhero_item_avatar_link' ); ?></a>
		</div>
		<div class="memberhero-item-info">
			<div class="memberhero-item-header">
				<div class="memberhero-item-head">
					<a href="<?php echo memberhero_get_profile_url(); ?>"><?php echo esc_html( $the_user->get( 'display_name' ) ); ?></a>
					<span class="memberhero-item-subhead">@<?php echo esc_html( $the_user->user_login ); ?>
					</span>
				</div>
				<div class="memberhero-item-buttons">
					<a href="#" class="memberhero-button memberhero-ajax-action memberhero_unblock_user alert" data-user_id="<?php echo absint( $the_user->user_id ); ?>" data-user="<?php echo esc_attr( $the_user->get( 'user_login' ) ); ?>" data-action="unblock_user" data-remove="true"><?php _e( 'Blocked', 'memberhero' ); ?></a>
				</div>
			</div>
			<?php if ( $the_user->get( 'description' ) ) : ?>
			<div class="memberhero-item-text"><?php echo memberhero_get_user_description( true ); ?></div>
			<?php endif; ?>
		</div>
	</div>

	<?php endforeach; ?>

</div>

<?php else : ?>

	<div class="memberhero-section is-centered">
		<div class="memberhero-strong-heading"><?php _e( 'You have not blocked anyone.', 'memberhero' ); ?></div>
		<div class="memberhero-subheading"><?php esc_html_e( 'When you block someone, they’ll be listed here.', 'memberhero' ); ?></div>
	</div>

<?php endif; ?>