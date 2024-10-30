<?php
/**
 * Custom field data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Meta_Box_Field_Data class.
 */
class MemberHero_Meta_Box_Field_Data {

	/**
	 * Output the metabox.
	 */
	public static function output( $post ) {
		global $thepostid, $the_field;

		$thepostid      = ( isset( $post->ID ) ) ? $post->ID : 0;
		$the_field 		= $thepostid ? new MemberHero_Field( $thepostid ) : new MemberHero_Field();

		wp_nonce_field( 'memberhero_save_data', 'memberhero_meta_nonce' );

		include 'views/html-field-data-panel.php';
	}

	/**
	 * Return array of tabs to show.
	 */
	private static function get_tabs() {
		$tabs = apply_filters(
			'memberhero_field_data_tabs', array(
				'general'        => array(
					'icon'	   => 'settings',
					'label'    => esc_html__( 'General', 'memberhero' ),
					'target'   => 'general_field_data',
					'class'    => array(),
					'priority' => 10,
				),
				'properties'	=> array(
					'icon'	   => 'database',
					'label'    => esc_html__( 'Properties', 'memberhero' ),
					'target'   => 'properties_field_data',
					'class'    => array(),
					'priority' => 20,
				),
				'customize'		=> array(
					'icon'	   => 'type',
					'label'    => esc_html__( 'Customize', 'memberhero' ),
					'target'   => 'customize_field_data',
					'class'    => array(),
					'priority' => 30,
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
		global $post, $thepostid, $the_field;

		include 'views/html-field-data-general.php';
		include 'views/html-field-data-properties.php';
		include 'views/html-field-data-customize.php';
	}

	/**
	 * Save meta box data.
	 */
	public static function save( $post_id, $post ) {
		global $the_field;
		$props = array();

		$the_field = new MemberHero_Field( $post_id );

		$props = memberhero_setup_field_props( 'post_title' );

		// No key was provided.
		if ( ! $props['key'] ) {
			MemberHero_Admin_Meta_Boxes::add_error( __( 'Please enter a unique meta key to use for this custom field below.', 'memberhero' ) );
		}

		// Check for key change.
		if ( $props['key'] != $the_field->key ) {
			if ( $the_field->exists( $props['key'] ) ) {
				MemberHero_Admin_Meta_Boxes::add_error( __( 'The custom key was not changed because It is already used by another custom field.', 'memberhero' ) );
				$props['key'] = $the_field->key;
			} else {
				$the_field->_delete();
			}
		}

		$the_field->save( $props );
	}

}