<?php
/**
 * User Role Core.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MemberHero_Abstract_Post', false ) ) {
	include_once 'abstracts/abstract-class-memberhero-post.php';
}

/**
 * MemberHero_Role class.
 */
class MemberHero_Role extends MemberHero_Abstract_Post {

	/**
	 * Post type.
	 */
	public $post_type = 'memberhero_role';

	/**
	 * Meta keys.
	 */
	public $internal_meta_keys = array(
		'name',
		'is_created',
		'capabilities',
		'label',
		'bypass_globals',
		'email_confirm',
		'manual_approval',
	);

	/**
	 * Get capability state.
	 */
	public function get_cap( $capability ) {
		$capabilities = ( array ) $this->capabilities;

		if ( ! array_key_exists( $capability, $capabilities ) ) {
			if ( $this->is_default( $capability ) && ! $this->is_created )
				return true;
			return false;
		}

		return $capabilities[ $capability ];
	}

	/**
	 * Set capabilities.
	 */
	public function set_capabilities( $capabilities = array() ) {
		global $wp_roles;

		$capabilities = array_merge( $capabilities, array( 'read' => true, 'level_0' => true ) );

		foreach( $capabilities as $capability => $value ) {
			if ( ! empty( $value ) ) {
				$wp_roles->add_cap( $this->name, $capability );
			} else {
				$wp_roles->remove_cap( $this->name, $capability );
			}
		}
	}

	/**
	 * Check if capability on by default.
	 */
	public function is_default( $capability ) {
		return array_key_exists( $capability, memberhero_get_default_capabilities() );
	}

	/**
	 * Role exists.
	 */
	public function exists( $role ) {
		global $wp_roles;

		return ( in_array( $role, array_keys( memberhero_get_roles() ) ) || $wp_roles->is_role( $role ) );
	}

	/**
	 * Add New Role.
	 */
	public function add_new( $name, $title, $capabilities ) {
		memberhero_add_role( $name, $title, $capabilities );
	}

	/**
	 * Sync with previous role info.
	 */
	public function sync( $name ) {
		$wp_user_roles = get_option( 'wp_user_roles' );

		// Update WP roles database.
		$new_role = $wp_user_roles[ $this->name ];
		unset( $wp_user_roles[ $this->name ] );
		$wp_user_roles[ $name ] = $new_role;
		$wp_user_roles[ $name ]['name'] = memberhero_clean( $_POST['post_title'] );
		update_option( 'wp_user_roles', $wp_user_roles );

		// Change the role name in our own option.
		$allowed_roles = get_option( 'memberhero_roles' );
		if ( ( $key = array_search( $this->name, $allowed_roles ) ) !== false ) {
			unset( $allowed_roles[$key] );
			$allowed_roles[] = $name;
			update_option( 'memberhero_roles', $allowed_roles );
		}
	}

	/**
	 * When this item is deleted.
	 */
	public function _delete() {
		global $wp_roles;
		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$allowed_roles 	= get_option( 'memberhero_roles' );
		$role 			= $this->name;

		if ( ! empty( $role ) && in_array( $role, array( 'member', 'community_manager' ) ) ) {
			return;
		}

		if ( ( $key = array_search( $role, $allowed_roles ) ) !== false ) {

			unset( $allowed_roles[$key] );

			foreach( array_merge( memberhero_get_default_capabilities(), memberhero_get_admin_capabilities(), memberhero_get_wp_admin_capabilities() ) as $cap => $value ) {
				$wp_roles->remove_cap( $role, $cap );
			}

			update_option( 'memberhero_roles', $allowed_roles );
			remove_role( $role );
		}
	}

}