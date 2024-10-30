<?php
/**
 * Create menus in WP admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Admin_Menus class.
 */
class MemberHero_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );

		add_action( 'admin_head', array( $this, 'menu_highlight' ) );
		add_action( 'admin_head', array( $this, 'menu_order_fix' ) );

		add_filter( 'menu_order', 		 array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		global $menu;

		if ( current_user_can( 'manage_memberhero' ) ) {
			$menu[] = array( '', 'read', 'separator-memberhero', '', 'wp-menu-separator memberhero' );
		}

		add_menu_page( __( 'Member Hero', 'memberhero' ), __( 'Member Hero', 'memberhero' ), 'manage_memberhero', 'memberhero', null, null, '25.5471' );
	}

	/**
	 * Add menu item.
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'memberhero', __( 'Settings', 'memberhero' ), __( 'Settings', 'memberhero' ), 'memberhero_settings', 'memberhero-settings', array( $this, 'settings_page' ) );
		add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
	}

	/**
	 * Loads gateways and shipping methods into memory for use within settings.
	 */
	public function settings_page_init() {
		global $current_tab, $current_section;

		// Include settings pages.
		MemberHero_Admin_Settings::get_settings_pages();

		// Get current tab/section.
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) );
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) );

		if ( $current_tab == 'addons' && empty( $current_section ) ) {
			$sections = apply_filters( 'memberhero_get_sections_addons', array() );
			asort( $sections );
			if ( $sections ) {
				$keys = array_keys( $sections );
				$first = $keys[0];
				exit( wp_safe_redirect( add_query_arg( 'section', $first ) ) );
			}
		}

		// Save settings if data has been posted.
		if ( '' !== $current_section && apply_filters( "memberhero_save_settings_{$current_tab}_{$current_section}", ! empty( $_POST['save'] ) ) ) {
			MemberHero_Admin_Settings::save();
		} elseif ( '' === $current_section && apply_filters( "memberhero_save_settings_{$current_tab}", ! empty( $_POST['save'] ) ) ) {
			MemberHero_Admin_Settings::save();
		}

		// Add any posted messages.
		if ( ! empty( $_GET['memberhero_error'] ) ) {
			MemberHero_Admin_Settings::add_error( wp_kses_post( wp_unslash( $_GET['memberhero_error'] ) ) );
		}

		// Custom message.
		if ( ! empty( $_GET['memberhero_message'] ) ) {
			MemberHero_Admin_Settings::add_message( wp_kses_post( wp_unslash( $_GET['memberhero_message'] ) ) );
		}

		// Save licenses.
		if ( ! empty( $_GET[ 'status' ] ) ) {
			if ( $_GET[ 'status' ] == 'deactivated' ) {
				MemberHero_Admin_Settings::add_message( wp_kses_post( wp_unslash( __( 'Your settings have been saved.', 'memberhero' ) ) ) );
			}
		}

		do_action( 'memberhero_settings_page_init' );
	}

	/**
	 * Highlights the correct top level admin menu item for post type add screens.
	 */
	public function menu_highlight() {
		global $parent_file, $submenu_file, $post_type;

		switch ( $post_type ) {

		}
	}

	/**
	 * Removes the parent menu item.
	 */
	public function menu_order_fix() {
		global $submenu;

		if ( isset( $submenu['memberhero'] ) ) {
			// Remove 'memberhero' sub menu item.
			unset( $submenu['memberhero'][0] );
		}
	}

	/**
	 * Reorder the menu items in admin.
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array.
		$memberhero_menu_order = array();

		// Get the index of our custom separator.
		$memberhero_separator = array_search( 'separator-memberhero', $menu_order, true );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $index => $item ) {

			if ( 'memberhero' === $item ) {
				$memberhero_menu_order[] = 'separator-memberhero';
				$memberhero_menu_order[] = $item;
				$memberhero_menu_order[] = 'edit.php?post_type=product';
				unset( $menu_order[ $memberhero_separator ] );
			} elseif ( ! in_array( $item, array( 'separator-memberhero' ), true ) ) {
				$memberhero_menu_order[] = $item;
			}
		}

		// Return order.
		return $memberhero_menu_order;
	}

	/**
	 * Custom menu order.
	 */
	public function custom_menu_order( $enabled ) {
		return $enabled || current_user_can( 'manage_memberhero' );
	}

	/**
	 * Init the settings page.
	 */
	public function settings_page() {
		MemberHero_Admin_Settings::output();
	}

}

return new MemberHero_Admin_Menus();