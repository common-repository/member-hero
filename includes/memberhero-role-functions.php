<?php
/**
 * Role Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a role.
 */
function get_memberhero_role( $role_id = '' ) {
	return new MemberHero_Role( absint( $role_id ) );
}

/**
 * Add a WordPress role.
 */
function memberhero_add_role( $role = '', $label = '', $capabilities = '' ) {
	if ( ! $label ) {
		$label = ucfirst( $role );
	}

	$capabilities = array_merge( $capabilities, array( 'read' => true, 'level_0' => true ) );

	add_role( $role, $label, $capabilities );

	// update current plugin roles.
	$current_roles = get_option( 'memberhero_roles' );

	if ( ! in_array( $role, (array) $current_roles ) ) {
		$current_roles[] = $role;
		update_option( 'memberhero_roles', $current_roles );
	}
}

/**
 * Get a WordPress role.
 */
function memberhero_get_role( $role = '' ) {
	global $wp_roles;

	if ( ! class_exists( 'WP_Roles' ) ) {
		return;
	}

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	return $wp_roles->get_role( $role );
}

/**
 * Assign a specific role to a user.
 */
function memberhero_set_role( $user_id, $role ) {
	global $current_user;

	if ( ! class_exists( 'WP_User' ) ) {
		return;
	}

	if ( $user_id == 1 ) {
		return;
	}

	if ( $current_user->ID == 1 ) {
		$u = new WP_User( $user_id );
		$u->set_role( $role );
	} else {
		$current_user->set_role( $role );
	}
}

/**
 * Get a list of available WP roles.
 */
function memberhero_get_roles( $exclude_admin = false ) {
    global $wp_roles;

	$roles = array();

	if ( ! function_exists( 'get_editable_roles' ) ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
	}

	$editable_roles = array_reverse( get_editable_roles() );

	foreach ( $editable_roles as $role => $details ) {
		if ( $exclude_admin && in_array( $role, memberhero_get_admin_roles() ) ) {
			// Do not add admin roles.
		} else {
			$roles[ $role ] = translate_user_role( $details['name'] );
		}
	}

	return apply_filters( 'memberhero_get_roles', $roles );
}

/**
 * Returns the role friendly title.
 */
function memberhero_role( $role ) {
	global $wp_roles;
	
	$roles = memberhero_get_roles();
	
	if ( isset( $roles[ $role ] ) ) {
		return $roles[ $role ];
	} else {
		return '';
	}
}

/**
 * Get a list of plugin roles only.
 */
function memberhero_get_plugin_roles() {
	return apply_filters( 'memberhero_get_plugin_roles', get_option( 'memberhero_roles' ) );
}

/**
 * Get default role.
 */
function memberhero_get_default_role() {
	return apply_filters( 'memberhero_get_default_role', get_option( 'memberhero_default_role', 'member' ) );
}

/**
 * Get default capabilities.
 */
function memberhero_get_default_capabilities() {
	$array = array(
		'memberhero_edit_profile'		=> true,
		'memberhero_edit_account'		=> true,
		'memberhero_delete_account'		=> true,
		'memberhero_view_profiles'		=> true,
		'memberhero_view_list'			=> true,
		'memberhero_search_list'		=> true,
		'memberhero_log_in'				=> true,
	);

	return apply_filters( 'memberhero_get_default_capabilities', $array );
}

/**
 * Get admin capabilities.
 */
function memberhero_get_admin_capabilities() {
	$capabilities = array(
		'memberhero_view_private'		=> true,
		'memberhero_view_private_data'	=> true,
		'memberhero_edit_users'			=> true,
		'memberhero_delete_users'		=> true,
		'memberhero_mod_users'			=> true,
		'memberhero_view_adminbar'		=> true,
		'memberhero_view_wpadmin'		=> true,
		'memberhero_settings'			=> true,
		'manage_memberhero'				=> true,
	);

	$capability_types = memberhero_get_post_types();

	foreach ( $capability_types as $capability_type ) {
		$capabilities = $capabilities + array(
			"edit_{$capability_type}" 						=> true,
			"read_{$capability_type}" 						=> true,
			"delete_{$capability_type}" 					=> true,
			"edit_{$capability_type}s" 						=> true,
			"edit_others_{$capability_type}s" 				=> true,
			"publish_{$capability_type}s" 					=> true,
			"read_private_{$capability_type}s" 				=> true,
			"delete_{$capability_type}s" 					=> true,
			"delete_private_{$capability_type}s" 			=> true,
			"delete_published_{$capability_type}s" 			=> true,
			"delete_others_{$capability_type}s" 			=> true,
			"edit_private_{$capability_type}s" 				=> true,
			"edit_published_{$capability_type}s" 			=> true,
		);
	}

	return apply_filters( 'memberhero_get_admin_capabilities', $capabilities );
}

/**
 * Get WP admin capabilities.
 */
function memberhero_get_wp_admin_capabilities() {
	$array = array(
		'read'                   => true,
		'level_9'                => true,
		'level_8'                => true,
		'level_7'                => true,
		'level_6'                => true,
		'level_5'                => true,
		'level_4'                => true,
		'level_3'                => true,
		'level_2'                => true,
		'level_1'                => true,
		'level_0'                => true,
		'read_private_pages'     => true,
		'read_private_posts'     => true,
		'edit_posts'             => true,
		'edit_pages'             => true,
		'edit_published_posts'   => true,
		'edit_published_pages'   => true,
		'edit_private_pages'     => true,
		'edit_private_posts'     => true,
		'edit_others_posts'      => true,
		'edit_others_pages'      => true,
		'publish_posts'          => true,
		'publish_pages'          => true,
		'delete_posts'           => true,
		'delete_pages'           => true,
		'delete_private_pages'   => true,
		'delete_private_posts'   => true,
		'delete_published_pages' => true,
		'delete_published_posts' => true,
		'delete_others_posts'    => true,
		'delete_others_pages'    => true,
		'manage_categories'      => true,
		'manage_links'           => true,
		'moderate_comments'      => true,
		'upload_files'           => true,
		'export'                 => true,
		'import'                 => true,
		'list_users'             => true,
		'edit_theme_options'     => true,
	);

	return apply_filters( 'memberhero_get_wp_admin_capabilities', $array );
}

/**
 * Get capability titles.
 */
function memberhero_get_cap_titles() {
	$array = array(
		'memberhero_edit_profile'			=> __( 'Edit profile', 'memberhero' ),
		'memberhero_edit_account'			=> __( 'Edit "My Account"', 'memberhero' ),
		'memberhero_delete_account'			=> __( 'Delete account', 'memberhero' ),
		'memberhero_log_in'					=> __( 'Login to site', 'memberhero' ),
		'memberhero_view_profiles'			=> __( 'View profiles', 'memberhero' ),
		'memberhero_view_private'			=> __( 'View private profiles', 'memberhero' ),
		'memberhero_view_private_data'		=> __( 'View private info', 'memberhero' ),
		'memberhero_edit_users'				=> __( 'Edit users', 'memberhero' ),
		'memberhero_mod_users'				=> __( 'Moderate users', 'memberhero' ),
		'memberhero_delete_users'			=> __( 'Delete users', 'memberhero' ),
		'memberhero_view_adminbar'			=> __( 'View admin bar', 'memberhero' ),
		'memberhero_view_wpadmin'			=> __( 'View wp-admin', 'memberhero' ),
		'memberhero_view_list'				=> __( 'View member directories', 'memberhero' ),
		'memberhero_search_list'			=> __( 'Search member directories', 'memberhero' ),
		'manage_memberhero'					=> __( 'Community manager', 'memberhero' ),
		'memberhero_settings'				=> __( 'Edit community settings', 'memberhero' ),
		'publish_memberhero_forms'			=> __( 'Create forms', 'memberhero' ),
		'publish_memberhero_fields'			=> __( 'Create custom fields', 'memberhero' ),
		'publish_memberhero_roles'			=> __( 'Create user roles', 'memberhero' ),
		'publish_memberhero_lists'			=> __( 'Create member directories', 'memberhero' ),
		'edit_memberhero_forms'				=> __( 'Manage forms', 'memberhero' ),
		'edit_memberhero_fields'			=> __( 'Manage custom fields', 'memberhero' ),
		'edit_memberhero_roles'				=> __( 'Manage user roles', 'memberhero' ),
		'edit_memberhero_lists'				=> __( 'Manage member directories', 'memberhero' ),
		'delete_memberhero_forms'			=> __( 'Delete forms', 'memberhero' ),
		'delete_memberhero_fields'			=> __( 'Delete custom fields', 'memberhero' ),
		'delete_memberhero_roles'			=> __( 'Delete user roles', 'memberhero' ),
		'delete_memberhero_lists'			=> __( 'Delete member directories', 'memberhero' ),
	);

	return apply_filters( 'memberhero_get_cap_titles', $array );
}

/**
 * Get a nice display title for a given capability.
 */
function memberhero_get_cap_title( $cap ) {
	$array = memberhero_get_cap_titles();
	$title = isset( $array[ $cap ] ) ? memberhero_clean( $array[ $cap ] ) : '';

	return apply_filters( 'memberhero_get_cap_title', $title, $cap );
}

/**
 * Create default user roles.
 */
function memberhero_create_default_roles() {
	global $wp_roles;

	if ( ! class_exists( 'WP_Roles' ) ) {
		return;
	}

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	foreach( $wp_roles->roles as $key => $data ) {
		$the_role = new MemberHero_Role();

		$the_role->set( 'post_title', array_key_exists( 'name', $data ) ? $data['name'] : '' );
		$the_role->set( 'post_name', memberhero_clean( wp_unslash( $key ) ) );
		$the_role->set( 'meta_input', array(
				'name'		 => $key,
				'is_created' => 1
		) );

		$the_role->insert();
		$the_role->save( $the_role->meta_input );
	}
}

/**
 * Get admin roles.
 */
function memberhero_get_admin_roles() {

	return apply_filters( 'memberhero_get_admin_roles', array( 'administrator', 'community_manager' ) );
}

/**
 * Get a role label/title by role slug.
 */
function memberhero_get_role_label( $role ) {
	global $wp_roles;

	if ( ! class_exists( 'WP_Roles' ) ) {
		return;
	}

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	if ( isset( $wp_roles->roles[ $role ][ 'name' ] ) ) {
		return $wp_roles->roles[ $role ][ 'name' ];
	}

	return null;
}