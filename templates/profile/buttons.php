<?php
/**
 * Profile buttons.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_memberhero_editing_profile() ) :

	do_action( 'memberhero_profile_nav_editing' );

else :

	do_action( 'memberhero_profile_nav_not_editing' );

endif;