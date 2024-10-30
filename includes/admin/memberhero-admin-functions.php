<?php
/**
 * Admin Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks if form is a default.
 */
function is_memberhero_default_form( $object ) {
	if ( get_option( 'memberhero_' . $object->type . '_form' ) == $object->id ) {
		return true;
	}

	$endpoint = $object->endpoint;
	$endpoint = str_replace( '-', '_', $endpoint );

	if ( get_option( 'memberhero_account_' . $endpoint . '_form' ) == $object->id ) {
		return true;
	}

	return false;
}

/**
 * Get all screen ids.
 */
function memberhero_get_screen_ids() {
	$screen_ids = array();
	$screen_id  = sanitize_title( __( 'Member Hero', 'memberhero' ) );
	$post_types = memberhero_get_post_types();

	foreach( $post_types as $post_type ) {
		$screen_ids[] = "edit-{$post_type}";
		$screen_ids[] = $post_type;
	}

	$screen_ids[] = $screen_id . '_page_memberhero-settings';
	$screen_ids[] = 'users';

	return apply_filters( 'memberhero_screen_ids', $screen_ids );
}

/**
 * Create a page and store the ID in an option.
 */
function memberhero_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;

	$option_value = get_option( $option );

	if ( $option_value > 0 && ( $page_object = get_post( $option_value ) ) ) {
		if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
			// Valid page is already in place
			return $page_object->ID;
		}
	}

	if ( strlen( $page_content ) > 0 ) {
		// Search for an existing page with the specified page content (typically a shortcode)
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Search for an existing page with the specified page slug
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
	}

	$valid_page_found = apply_filters( 'memberhero_create_page_id', $valid_page_found, $slug, $page_content );

	if ( $valid_page_found ) {
		if ( $option ) {
			update_option( $option, $valid_page_found );
		}
		return $valid_page_found;
	}

	// Search for a matching valid trashed page
	if ( strlen( $page_content ) > 0 ) {
		// Search for an existing page with the specified page content (typically a shortcode)
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Search for an existing page with the specified page slug
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
	}

	if ( $trashed_page_found ) {
		$page_id   = $trashed_page_found;
		$page_data = array(
			'ID'          => $page_id,
			'post_status' => 'publish',
		);
		wp_update_post( $page_data );
	} else {
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed',
		);
		$page_id   = wp_insert_post( $page_data );
	}

	if ( $option ) {
		update_option( $option, $page_id );
	}

	return $page_id;
}

/**
 * Processes all actions sent via POST and GET by looking for the 'memberhero-action'
 */
function memberhero_process_actions() {
	if ( isset( $_POST['memberhero-action'] ) ) {
		do_action( 'memberhero_' . $_POST['memberhero-action'], $_POST );
	}

	if ( isset( $_GET['memberhero-action'] ) ) {
		do_action( 'memberhero_' . $_GET['memberhero-action'], $_GET );
	}
}
add_action( 'admin_init', 'memberhero_process_actions' );