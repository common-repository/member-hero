<?php
/**
 * My Profile header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'memberhero_before_profile_header' );

do_action( 'memberhero_profile_cover' );

do_action( 'memberhero_after_profile_header' );