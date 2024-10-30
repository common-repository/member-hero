<?php
/**
 * Form data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Meta_Box_Form_Data class.
 */
class MemberHero_Meta_Box_Form_Data {

	/**
	 * Output the metabox.
	 */
	public static function output( $post ) {
		global $thepostid, $the_form;

		$thepostid      = ( isset( $post->ID ) ) ? $post->ID : 0;
		$the_form 		= $thepostid ? new MemberHero_Form( $thepostid ) : new MemberHero_Form();

		wp_nonce_field( 'memberhero_save_data', 'memberhero_meta_nonce' );

		include 'views/html-form-data-panel.php';
	}

	/**
	 * Return array of tabs to show.
	 */
	private static function get_tabs() {
		$tabs = apply_filters(
			'memberhero_form_data_tabs', array(
				'general'			=> array(
					'icon'	   => 'settings',
					'label'    => esc_html__( 'General', 'memberhero' ),
					'target'   => 'general_form_data',
					'class'    => array(),
					'priority' => 10,
				),
				'redirect'			=> array(
					'icon'	   => 'repeat',
					'label'    => esc_html__( 'Redirection', 'memberhero' ),
					'target'   => 'redirect_form_data',
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
		global $post, $thepostid, $the_form;

		include 'views/html-form-data-general.php';
		include 'views/html-form-data-redirect.php';
	}

	/**
	 * Save meta box data.
	 */
	public static function save( $post_id, $post ) {
		global $the_form;
		$props = array();

		$the_form = new MemberHero_Form( $post_id );

		$props['type']         			= isset( $_POST['_type'] ) ? memberhero_clean( wp_unslash( $_POST['_type'] ) ) : '';
		$props['role']         			= isset( $_POST['role'] ) ? memberhero_clean( wp_unslash( $_POST['role'] ) ) : '';
		$props['icons']         		= isset( $_POST['icons'] ) ? memberhero_clean( wp_unslash( $_POST['icons'] ) ) : '';
		$props['endpoint']         		= isset( $_POST['endpoint'] ) ? memberhero_clean( wp_unslash( $_POST['endpoint'] ) ) : '';
		$props['redirect']         		= isset( $_POST['redirect'] ) ? memberhero_clean( wp_unslash( $_POST['redirect'] ) ) : '';
		$props['redirect_uri']     		= isset( $_POST['redirect_uri'] ) ? memberhero_clean( wp_unslash( $_POST['redirect_uri'] ) ) : '';
		$props['use_ajax']				= isset( $_POST['use_ajax'] ) ? 'yes' : 'no';
		$props['force_role']			= isset( $_POST['force_role'] ) ? 'yes' : 'no';
		$props['show_cover']			= isset( $_POST['show_cover'] ) ? 'yes' : 'no';
		$props['show_menu']				= isset( $_POST['show_menu'] ) ? 'yes' : 'no';
		$props['show_members_menu']		= isset( $_POST['show_members_menu'] ) ? 'yes' : 'no';
		$props['show_social']			= isset( $_POST['show_social'] ) ? 'yes' : 'no';
		$props['aligncenter']			= isset( $_POST['aligncenter'] ) ? 'yes' : 'no';
		$props['confirm_email']			= isset( $_POST['confirm_email'] ) ? 'yes' : 'no';
		$props['confirm_password']		= isset( $_POST['confirm_password'] ) ? 'yes' : 'no';

		// Set as default form?
		if ( isset( $_POST['set_default'] ) && $_POST['set_default'] == 'yes' ) {
			update_option( 'memberhero_' . $props[ 'type' ] . '_form', $post_id );
		}

		// This hook allow us to modify the options before they're sent/updated.
		$props = apply_filters( 'memberhero_form_save_options', $props );

		$the_form->save( $props );
	}

}