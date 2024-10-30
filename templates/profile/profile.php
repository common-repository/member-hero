<?php
/**
 * My Profile page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-profile <?php echo implode( ' ', memberhero_get_profile_classes() ); ?>" id="memberhero-user-<?php echo $the_user->user_id; ?>">

	<?php if ( $the_form->show_menu !== 'no' ) : ?>
	<div class="memberhero-sidenav">
		<?php
			/**
			 * My Profile nav.
			 */
			do_action( 'memberhero_profile_nav' );
		?>
	</div>
	<?php endif; ?>

	<div class="memberhero-main">
		<?php
		/**
		 * My Profile header.
		 */
		do_action( 'memberhero_profile_header' ); ?>

		<div class="memberhero-layout">

			<?php
				/**
				 * My Profile info.
				 */
				do_action( 'memberhero_profile_info' );

				/**
				 * My Profile tabs.
				 */
				do_action( 'memberhero_profile_tabs' );
			?>

			<div class="memberhero-content memberhero-profile-content">
				<?php
					/**
					 * My Profile content.
					 */
					do_action( 'memberhero_profile_content' );
				?>
			</div>

		</div>
	</div>

</div>