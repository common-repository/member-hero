<?php
/**
 * Member List Hooks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'memberhero_pre_list_query', 'memberhero_list_add_filters', 50 );
add_action( 'memberhero_pre_list_query', 'memberhero_list_add_sorting', 60 );
add_action( 'memberhero_pre_list_query', 'memberhero_list_show_roles', 70 );
add_action( 'memberhero_pre_list_query', 'memberhero_list_exclude_admins', 80 );
add_action( 'memberhero_pre_list_query', 'memberhero_only_show_active_users', 90 );

add_filter( 'user_search_columns', 'memberhero_user_search_columns', 10, 3 );
add_action( 'memberhero_pre_users_query', 'memberhero_pre_users_query' );

/**
 * Add custom filters to the search results.
 */
function memberhero_list_add_filters( $the_list ) {
	$filters = $the_list->get_filters();

	if ( empty( $filters ) ) {
		return;
	}

	foreach( $filters as $filter_id => $data ) {
		switch( $filter_id ) {

			// Avatar.
			case 'avatar' :
				if ( $data ) {
					$the_list->query[ 'meta_query' ][] = array(
						'key'		=> '_memberhero_profile_avatar',
						'compare'	=> 'EXISTS',
					);
				}
			break;

			// Registration date.
			case 'date_registered' :
				if ( is_numeric( $data ) ) {
					$data = memberhero_seconds_to_hours( $data );
				}
				$the_list->query[ 'date_query' ][] = array(
					'after' => $data, 'inclusive' => true
				);
			break;

			default :
				// This hook allow custom search filters.
				do_action( 'memberhero_list_custom_filter', $filter_id, $data, $filters, $the_list );
			break;
		}
	}

}

/**
 * Add sortby parameters to member list query.
 */
function memberhero_list_add_sorting( $the_list ) {

	$sort = ! empty( $the_list->get_sort() ) ? $the_list->get_sort() : $the_list->orderby;

	// Core sorting options.
	switch( $sort ) {
		case 'date_desc' :
			$the_list->query[ 'orderby' ] = 'user_registered';
			$the_list->query[ 'order' ]   = 'desc';
		break;
		case 'date_asc' :
			$the_list->query[ 'orderby' ] = 'user_registered';
			$the_list->query[ 'order' ]   = 'asc';
		break;
		case 'name_asc' :
			$the_list->query[ 'orderby' ] = 'display_name';
			$the_list->query[ 'order' ]   = 'asc';
		break;
		case 'name_desc' :
			$the_list->query[ 'orderby' ] = 'display_name';
			$the_list->query[ 'order' ]   = 'desc';
		break;
	}

	do_action( 'memberhero_custom_sort_parameter', $the_list, $sort );
}

/**
 * Show only specific roles in member list.
 */
function memberhero_list_show_roles( $the_list ) {
	$roles = $the_list->roles;

	if ( ! empty( $roles ) ) {
		if ( in_array( '_all', $roles ) ) {
			if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_memberhero' ) ) {
				return;
			}
			$the_list->query[ 'role__not_in' ] = memberhero_get_admin_roles();
		} else {
			$the_list->query[ 'role__in' ] = $roles;
		}
	}
}

/**
 * Remove admin users from the member directory.
 */
function memberhero_list_exclude_admins( $the_list ) {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_memberhero' ) ) {
		return;
	}

	if ( apply_filters( 'memberhero_hide_admins_from_list', false ) ) {
		$the_list->query[ 'role__not_in' ] = memberhero_get_admin_roles();
	}
}

/**
 * This will not show inactive users in the member list. (except for admins)
 */
function memberhero_only_show_active_users( $the_list ) {
	if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_memberhero' ) || current_user_can( 'memberhero_mod_users' ) ) {
		return;
	}

	$the_list->query[ 'meta_query' ][] = array(
		'key'		=> '_memberhero_unconfirmed_email',
		'compare'	=> 'NOT EXISTS',
	);

	$the_list->query[ 'meta_query' ][] = array(
		'key'		=> '_memberhero_pending',
		'compare'	=> 'NOT EXISTS',
	);
}

/**
 * Search columns for front-end.
 */
function memberhero_user_search_columns( $search_columns, $search, $wp_user_query ) {

	// This is to make the filter plugin specific.
	if ( memberhero_is_in_loop() ) {
		$search_columns = apply_filters( 'memberhero_user_search_columns', array(
			'user_login',
			'user_url',
			'user_email',
			'user_nicename',
			'display_name',
		) );
	}

	return $search_columns;
}

/**
 * Fired before WP_User_Query is returned.
 */
function memberhero_pre_users_query() {
	add_action( 'pre_user_query', 'memberhero_list_hide_blocked_users', 10 );
}

/**
 * Hide users blocked from member list.
 */
function memberhero_list_hide_blocked_users( $user_query  ) {
	global $wpdb, $logged_user, $the_list;

	// This only apply to logged in users.
	if ( is_user_logged_in() ) {

		// Exclude blocked users from the list.
		$blocked = $logged_user->get( '_memberhero_blocked_users' );
		if ( ! empty( $blocked ) ) {
			$user_query->query_where .= " AND {$wpdb->users}.ID NOT IN ('" . implode( "','", array_keys( $blocked ) ) . "') ";
		}

	}

}