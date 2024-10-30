<?php
/**
 * User role capabilities.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Meta_Box_Role_Caps class.
 */
class MemberHero_Meta_Box_Role_Caps {

	/**
	 * Output the metabox.
	 */
	public static function output( $post ) {
		global $thepostid, $the_role;

		$thepostid      = $post->ID;
		$the_role 		= $thepostid ? new MemberHero_Role( $thepostid ) : new MemberHero_Role();

		// Map role capabilities with WP role.
		$wp_role = get_role( $the_role->name );
		if ( isset( $wp_role->name ) ) {
			$the_role->capabilities = $wp_role->capabilities;
		}

		wp_nonce_field( 'memberhero_save_data', 'memberhero_meta_nonce' );

		include 'views/html-role-caps-panel.php';
	}

	/**
	 * Return array of tabs to show.
	 */
	private static function get_tabs() {
		$tabs = apply_filters(
			'memberhero_role_caps_tabs', array(
				'general'        => array(
					'icon'	   => 'settings',
					'label'    => __( 'General', 'memberhero' ),
					'target'   => 'general_role_caps',
					'class'    => array(),
					'priority' => 10,
				),
				'access'        => array(
					'icon'	   => 'lock',
					'label'    => __( 'Access', 'memberhero' ),
					'target'   => 'access_role_caps',
					'class'    => array(),
					'priority' => 20,
				),
				'admin'        => array(
					'icon'	   => 'shield',
					'label'    => __( 'Admin', 'memberhero' ),
					'target'   => 'admin_role_caps',
					'class'    => array(),
					'priority' => 30,
				),
				'forms'        => array(
					'icon'	   => 'file-text',
					'label'    => __( 'Forms', 'memberhero' ),
					'target'   => 'forms_role_caps',
					'class'    => array(),
					'priority' => 40,
				),
				'fields'       => array(
					'icon'	   => 'database',
					'label'    => __( 'Custom fields', 'memberhero' ),
					'target'   => 'fields_role_caps',
					'class'    => array(),
					'priority' => 50,
				),
				'roles'        => array(
					'icon'	   => 'user-check',
					'label'    => __( 'User roles', 'memberhero' ),
					'target'   => 'roles_role_caps',
					'class'    => array(),
					'priority' => 60,
				),
				'lists'  		=> array(
					'icon'	   => 'users',
					'label'    => __( 'Member directories', 'memberhero' ),
					'target'   => 'lists_role_caps',
					'class'    => array(),
					'priority' => 70,
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

		include 'views/html-role-caps-general.php';
		include 'views/html-role-caps-admin.php';
		include 'views/html-role-caps-access.php';
		include 'views/html-role-caps-forms.php';
		include 'views/html-role-caps-fields.php';
		include 'views/html-role-caps-roles.php';
		include 'views/html-role-caps-lists.php';
	}

	/**
	 * Save meta box data.
	 */
	public static function save( $post_id, $post ) {
		global $the_role;
		$props = array();

		$the_role = new MemberHero_Role( $post_id );

		$name = memberhero_sanitize_title( $_POST['post_title'] );

		// New user role.
		if ( ! $the_role->is_created ) {
			if ( $the_role->exists( $name ) ) {
				MemberHero_Admin_Meta_Boxes::add_error( sprintf( wp_kses_post( __( 'You are trying to add a user role which already exists. Try <a href="%s">setting up default roles</a>.', 'memberhero' ) ), '#memberhero-create-roles' ) );
				wp_safe_redirect( admin_url( 'post-new.php?post_type=memberhero_role' ) );
				exit;
			} else {
				$the_role->add_new( $name, memberhero_clean( $_POST['post_title'] ), memberhero_get_default_capabilities() );
				$props['name'] = $name;
				$props['is_created'] = 1;
			}
		} else {
			if ( memberhero_clean( wp_unslash( $_POST['post_title'] ) ) != memberhero_clean( wp_unslash( $_POST['original_post_title'] ) ) ) {
				if ( $the_role->exists( $name ) ) {
					MemberHero_Admin_Meta_Boxes::add_error( esc_html__( 'Your role title was not updated because a role with the same name already exists.', 'memberhero' ) );
				} else {
					$the_role->sync( $name );
					$props['name'] = $name;
					$props['is_created'] = 1;
				}
			}
		}

		// Set properties.
		$the_role->set( 'name', $name );

		// Check if user is trying to edit admin capabilities.
		if ( ! in_array( $name, array( 'administrator' ) ) ) {

			// Set capabilities.
			$capabilities = memberhero_get_cap_titles();
			foreach( $capabilities as $capability => $title ) {
				$capabilities[ $capability ] = ! empty( $_POST[ $capability ] );
			}
			$the_role->set_capabilities( $capabilities );

		}

		// Set props.
		$props['capabilities'] 		= null;
		$props['label'] 			= memberhero_clean( wp_unslash( $_POST['post_title'] ) );
		$props['bypass_globals']	= isset( $_POST['bypass_globals'] ) ? 'yes' : 'no';
		$props['bypass_globals']	= isset( $_POST['bypass_globals'] ) ? 'yes' : 'no';
		$props['email_confirm']		= isset( $_POST['email_confirm'] ) ? 'yes' : 'no';
		$props['manual_approval']	= isset( $_POST['manual_approval'] ) ? 'yes' : 'no';

		// Save.
		$the_role->save( $props );
	}

}