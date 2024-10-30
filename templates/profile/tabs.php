<?php
/**
 * My Profile tabs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<ul class="memberhero-profile-tabs">

	<?php foreach ( memberhero_get_profile_menu_items( true ) as $endpoint => $data ) : ?>
		<li class="<?php echo memberhero_get_profile_menu_item_classes( $endpoint, true ); ?>">
			<a href="<?php echo esc_url( isset( $data['url'] ) ? $data['url'] : memberhero_get_profile_endpoint_url( $endpoint ) ); ?>">
				<?php
					if ( ! empty( $data[ 'label' ] ) ) :
						echo esc_html( $data['label'] );
					endif;
				?>
				<?php memberhero_profile_menu_count( $data ); ?>
			</a>
		</li>
	<?php endforeach; ?>

</ul>