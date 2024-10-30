<?php
/**
 * My Profile nav items
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_profile_nav_items' );

?>

<ul class="memberhero-profile-nav-ul">

	<?php foreach ( memberhero_get_profile_menu_items() as $endpoint => $data ) : ?>

		<li class="<?php echo memberhero_get_profile_menu_item_classes( $endpoint ); ?>">
			<a href="<?php echo esc_url( isset( $data['url'] ) ? $data['url'] : memberhero_get_current_user_endpoint( $endpoint ) ); ?>">
				<?php memberhero_profile_menu_icon( $data ); ?>
				<?php memberhero_profile_menu_count( $data ); ?>
				<?php
					if ( ! empty( $data[ 'label' ] ) ) :
						echo '<span class="memberhero-nav-title">' . esc_html( $data['label'] ) . '</span>';
					endif;
				?>
			</a>
		</li>

	<?php endforeach; ?>

	<?php do_action( 'memberhero_extend_profile_nav_items' ); ?>

</ul>

<?php do_action( 'memberhero_after_profile_nav_items' ); ?>