<?php
/**
 * Member list data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Meta_Box_List_Data class.
 */
class MemberHero_Meta_Box_List_Data {

	/**
	 * Output the metabox.
	 */
	public static function output( $post ) {
		global $thepostid, $the_list;

		$thepostid      = $post->ID;
		$the_list 		= $thepostid ? new MemberHero_List( $thepostid ) : new MemberHero_List();

		wp_nonce_field( 'memberhero_save_data', 'memberhero_meta_nonce' );

		include 'views/html-list-data-panel.php';
	}

	/**
	 * Return array of tabs to show.
	 */
	private static function get_tabs() {
		$tabs = apply_filters(
			'memberhero_list_data_tabs', array(
				'general'        => array(
					'icon'	   => 'settings',
					'label'    => esc_html__( 'General', 'memberhero' ),
					'target'   => 'general_list_data',
					'class'    => array(),
					'priority' => 10,
				),
				'customize'		=> array(
					'icon'	   => 'edit-2',
					'label'    => esc_html__( 'Customize', 'memberhero' ),
					'target'   => 'customize_list_data',
					'class'    => array(),
					'priority' => 20,
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
		global $post, $thepostid, $the_list;

		include 'views/html-list-data-general.php';
		include 'views/html-list-data-customize.php';
	}

	/**
	 * Save meta box data.
	 */
	public static function save( $post_id, $post ) {
		global $the_list;
		$props = array();

		$the_list = new MemberHero_List( $post_id );

		$props['roles']					= isset( $_POST['roles'] ) ? memberhero_clean( wp_unslash( $_POST['roles'] ) ) : '';
		$props['orderby']				= isset( $_POST['orderby'] ) ? memberhero_clean( wp_unslash( $_POST['orderby'] ) ) : '';
		$props['login_required']		= ! empty( $_POST['login_required'] );
		$props['use_ajax']				= isset( $_POST['use_ajax'] ) ? 'yes' : 'no';
		$props['per_page']				= isset( $_POST['per_page'] ) ? absint( wp_unslash( $_POST['per_page'] ) ) : '';
		$props['show_menu']				= isset( $_POST['show_menu'] ) ? 'yes' : 'no';
		$props['show_social']			= isset( $_POST['show_social'] ) ? 'yes' : 'no';
		$props['show_bio']				= isset( $_POST['show_bio'] ) ? 'yes' : 'no';
		$props['centered']				= isset( $_POST['centered'] ) ? 'yes' : 'no';

		// This hook allow us to modify the options before they're sent/updated.
		$props = apply_filters( 'memberhero_list_save_options', $props );

		$the_list->save( $props );
	}

}