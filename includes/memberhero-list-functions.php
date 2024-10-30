<?php
/**
 * Member List Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a member list.
 */
function memberhero_get_list( $id = '' ) {
	return new MemberHero_List( absint( $id ) );
}

/**
 * Get default member directories.
 */
function memberhero_get_default_lists() {
	$array = array(
		'members'	=> array(
			'title'	=> __( 'Members', 'memberhero' ),
		),
	);

	return apply_filters( 'memberhero_get_default_lists', $array );
}

/**
 * Create default member directories.
 */
function memberhero_create_default_lists() {
	if ( ! empty( $lists = memberhero_get_default_lists() ) ) {

		foreach( $lists as $key => $data ) {
			$the_list = new MemberHero_List();

			$the_list->set( 'post_title', isset( $data['title'] ) ? memberhero_clean( $data['title'] ) : '' );
			$the_list->set( 'post_name', memberhero_clean( wp_unslash( $key ) ) );
			$the_list->set( 'meta_input', memberhero_get_default_list_args() );

			$the_list->insert();
			$the_list->save( $the_list->meta_input );

			if ( ! empty( $the_list->id ) ) {
				$the_list->set_default();
			}
		}

	}
}

/**
 * Return default member list options.
 */
function memberhero_get_default_list_args() {
	$args = array(
		'per_page'		=> 12,
		'use_ajax'		=> 'yes',
		'roles'			=> array( '_all' ),
	);

	return apply_filters( 'memberhero_get_default_list_args', $args );
}

/**
 * Add members container styles.
 */
function memberhero_list_container_styles( $return = null ) {
	global $the_list;

	$styles = array();

	// Loop through styles and add them.
	foreach( (array) apply_filters( 'memberhero_get_list_container_styles', $styles ) as $id => $style ) {
		$return .= $id . ': ' . $style . ';';
	}

	if ( $return ) {
		return apply_filters( 'memberhero_list_container_styles', 'style="' . $return . '"' );
	}
}

/**
 * Check if user does not have capability to view this list.
 */
function memberhero_user_cant_see_list() {
	global $the_list;

	if ( $the_list->login_required && ! is_user_logged_in() ) {
		return true;
	}

	return false;
}

/**
 * Get members.
 */
function memberhero_get_members( $args = array() ) {
	global $wpdb, $the_list, $logged_user, $the_user;

	if ( empty( $the_list->id ) ) {
		return;
	}

	// Defaults.
	$defaults = array(
		'meta_key'		=> '',
		'meta_query'	=> array( 'relation' => 'AND' ),
		'date_query'	=> array(),
		'search'		=> $the_list->get_search(),
		'role__in'		=> array(),
		'role__not_in'  => array(),
		'number'		=> $the_list->per_page,
		'paged'			=> $the_list->get_page(),
		'orderby'		=> 'user_registered',
		'order'			=> 'desc',
		'fields'		=> array( 'ID' ),
	);

	$the_list->query = wp_parse_args( $args, $defaults );
	$the_list->in_loop();

	do_action( 'memberhero_pre_list_query', $the_list );

	do_action( 'memberhero_pre_users_query' );

	extract( $the_list->query );

	$users = new WP_User_Query( compact( 'meta_key', 'meta_query', 'date_query', 'search', 'role__in', 'role__not_in', 'number', 'paged', 'orderby', 'order', 'fields' ) );

	$response = array(
		'users'		=> $users->get_results(),
		'pages'		=> $number > 0 ? absint( ceil( $users->get_total() / $number ) ) : 1,
		'page'		=> $paged,
		'total'		=> $users->get_total(),
	);

	return $response;
}

/**
 * Get member directory classes.
 */
function memberhero_get_list_classes( $classes = array() ) {
	global $the_list;

	if ( $the_list->show_menu === 'no' ) {
		$classes[] = 'no-menu';
	}

	if ( $the_list->centered === 'yes' ) {
		$classes[] = 'is-center';
	}

	return apply_filters( 'memberhero_get_list_classes', array_unique( $classes ) );
}

/**
 * Get member sorting options as array.
 */
function memberhero_get_sorting_options() {
	$array = array(
		'name_asc'	=> __( 'Name (A-Z)', 'memberhero' ),
		'name_desc' => __( 'Name (Z-A)', 'memberhero' ),
		'date_desc'	=> __( 'Recent Users', 'memberhero' ),
		'date_asc'  => __( 'First Users', 'memberhero' ),
	);

	return apply_filters( 'memberhero_get_sorting_options', $array );
}

/**
 * Get the default sorting parameter for member list.
 */
function memberhero_get_default_sort() {
	global $the_list;

	$default = isset( $the_list->orderby ) && ! empty( $the_list->orderby ) ? $the_list->orderby : 'date_desc';

	return apply_filters( 'memberhero_get_default_sort', $default );
}

/**
 * Get the current sorting parameter.
 */
function memberhero_get_current_sort() {
	global $the_list;

	$sort = $the_list->get_sort();
	$list = memberhero_get_sorting_options();

	if ( ! $sort || ! isset( $list[ $sort ] ) ) {
		$sort = memberhero_get_default_sort();
	}

	return apply_filters( 'memberhero_get_current_sort', $sort );
}

/**
 * Get the current sorting parameter as label.
 */
function memberhero_get_current_sort_label() {
	global $the_list;

	$sort = memberhero_get_current_sort();
	$list = memberhero_get_sorting_options();

	return isset( $list[ $sort ] ) ? '<span class="memberhero-mini">' . $list[ $sort ] . '</span>' : '<span class="memberhero-mini">' . $list[ memberhero_get_default_sort() ] . '</span>';
}