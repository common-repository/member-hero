<?php
/**
 * Widget: user card template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * My Profile header.
 */
do_action( 'memberhero_profile_header' );
?>

<div class="memberhero-layout">

	<?php
		do_action( 'memberhero_profile_layout_start' );

		/**
		 * My Profile info.
		 */
		do_action( 'memberhero_profile_info' );
	?>

</div>