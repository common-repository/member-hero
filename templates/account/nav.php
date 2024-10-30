<?php
/**
 * My Account nav
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_account_nav' );
?>

<nav class="memberhero-nav memberhero-account-nav">
	<ul>
		<?php foreach ( memberhero_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo memberhero_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( memberhero_get_account_endpoint_url( $endpoint ) ); ?>">
					<span class="memberhero-nav-icon">
						<?php memberhero_get_account_endpoint_icon( $endpoint ); ?>
					</span>
					<span class="memberhero-nav-display">
						<?php echo esc_html( $label ); ?>
						<?php echo memberhero_svg_icon( 'chevron-right' ); ?>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'memberhero_after_account_nav' ); ?>