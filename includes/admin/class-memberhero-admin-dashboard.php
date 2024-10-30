<?php
/**
 * Admin Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Admin_Dashboard Class.
 */
class MemberHero_Admin_Dashboard {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'init' ) );
	}

	/**
	 * Init dashboard widgets.
	 */
	public function init() {
		if ( current_user_can( 'manage_memberhero' ) || current_user_can( 'memberhero_mod_users' ) ) {

		}
	}

}

return new MemberHero_Admin_Dashboard();