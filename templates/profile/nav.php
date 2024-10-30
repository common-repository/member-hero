<?php
/**
 * My Profile nav
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_profile_nav' );
?>

<nav class="memberhero-profile-nav">

	<?php do_action( 'memberhero_profile_nav_items' ); ?>

</nav>

<?php do_action( 'memberhero_after_profile_nav' ); ?>