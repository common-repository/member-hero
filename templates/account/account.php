<?php
/**
 * My Account page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="memberhero-profile">

	<div class="memberhero-sidenav">
		<?php
			/**
			 * My Profile nav.
			 */
			do_action( 'memberhero_profile_nav' );
		?>
	</div>

	<div class="memberhero-layout memberhero-account-layout">

		<?php
		/**
		 * My Account nav.
		 */
		do_action( 'memberhero_account_nav' );
		?>

		<div class="memberhero-content memberhero-account-content">
			<?php
				/**
				* My Account content.
				*/
				do_action( 'memberhero_account_content' );
			?>
		</div>

	</div>

</div>