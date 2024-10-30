<?php
/**
 * Members Directory.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-profile memberhero-members <?php echo implode( ' ', memberhero_get_list_classes() ); ?>">

	<?php if ( $the_list->show_menu !== 'no' ) : ?>
	<div class="memberhero-sidenav">
		<?php
			/**
			 * My Profile nav.
			 */
			do_action( 'memberhero_profile_nav' );
		?>
	</div>
	<?php endif; ?>

	<div class="memberhero-layout">
		<?php
			/**
			 * Member list top section.
			 */
			do_action( 'memberhero_list_top', $list );

			/**
			 * Display members directory.
			 */
			if ( ! empty( $list[ 'users' ] ) ) : ?>

				<?php do_action( 'memberhero_list_loop', $list ); ?>

			<?php else : ?>

				<?php do_action( 'memberhero_list_no_users', $list ); ?>

			<?php

			endif; // End members loop.

			do_action( 'memberhero_list_pagination', $list );
		?>
	</div>

</div>