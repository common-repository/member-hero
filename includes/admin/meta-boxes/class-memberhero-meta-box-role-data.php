<?php
/**
 * User role data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Meta_Box_Role_Data class.
 */
class MemberHero_Meta_Box_Role_Data {

	/**
	 * Output the metabox.
	 */
	public static function output( $post ) {
		global $thepostid, $the_role;

		$thepostid      = $post->ID;
		$the_role 		= $thepostid ? new MemberHero_Role( $thepostid ) : new MemberHero_Role();

		include 'views/html-role-data-panel.php';
	}

	/**
	 * Return array of tabs to show.
	 */
	private static function get_tabs() {
		$tabs = apply_filters(
			'memberhero_role_data_tabs', array(
				'general'        => array(
					'icon'	   => 'settings',
					'label'    => esc_html__( 'General', 'memberhero' ),
					'target'   => 'general_role_data',
					'class'    => array(),
					'priority' => 10,
				),
			)
		);

		// Sort tabs based on priority.
		uasort( $tabs, array( __CLASS__, 'tabs_sort' ) );

		return $tabs;
	}

	/**
	 * Callback to sort data tabs on priority.
	 */
	private static function tabs_sort( $a, $b ) {
		if ( ! isset( $a['priority'], $b['priority'] ) ) {
			return -1;
		}

		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}

		return $a['priority'] < $b['priority'] ? -1 : 1;
	}

	/**
	 * Show tab content/settings.
	 */
	private static function output_tabs() {
		global $post, $thepostid, $the_role;

		include 'views/html-role-data-general.php';
	}

	/**
	 * Save meta box data.
	 */
	public static function save( $post_id, $post ) {
		
	}

}