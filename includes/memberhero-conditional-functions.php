<?php
/**
 * Conditional Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns true when viewing an account page.
 */
function is_memberhero_account_page() {
	$page_id = memberhero_get_page_id( 'account' );

	return ( $page_id && is_page( $page_id ) ) || memberhero_post_content_has_shortcode( 'memberhero_account' );
}

/**
 * Returns true when viewing a profile page.
 */
function is_memberhero_profile_page() {
	global $post;

	$page_id = memberhero_get_page_id( 'profile' );

	return isset( $post->ID ) && ( $page_id == $post->ID );
}

/**
 * Checks that page has a form.
 */
function is_memberhero_page_has_form( $page_id = 0, $type ) {
	$post = get_post( $page_id );

	if ( ! isset( $post->post_content ) ) {
		return false;
	}

	// Check post content for default shortcode.
	if ( strstr( $post->post_content, "[memberhero_{$type}]" ) ) {
		return true;
	}

	// Check post content for custom form.
	preg_match( '/memberhero_form id=([0-9]+)]/', $post->post_content, $matches );

	if ( isset( $matches[ 1 ] ) ) {
		$form = memberhero_get_form( $matches[ 1 ] );
		if ( isset( $form->type ) && $form->type == $type ) {
			return true;
		}
	}

	return false;
}

/**
 * Returns true when we're in edit profile section.
 */
function is_memberhero_editing_profile() {
	global $the_user;

	$bool = false;

	if ( get_query_var( 'memberhero_tab' ) == 'edit' ) {
		$bool = true;
	}

	// Do not allow editing inside user card widget.
	if ( isset( $the_user->in_widget ) && $the_user->in_widget ) {
		$bool = false;
	}

	return apply_filters( 'is_memberhero_editing_profile', $bool );
}

/**
 * Returns true when the logged-in user is viewing their profile.
 */
function is_memberhero_my_profile( $user_id = 0 ) {
	global $the_user;

	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( isset( $the_user->user_id ) && ( $the_user->user_id == get_current_user_id() ) ) {
		return true;
	}

	if ( ! empty( $user_id ) && ( $user_id == get_current_user_id() ) ) {
		return true;
	}

	return false;
}

/**
 * Checks whether the content passed contains a specific short code.
 */
function memberhero_post_content_has_shortcode( $tag = '' ) {
	global $post;

	return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}

/**
 * Check if an endpoint is showing.
 */
function is_memberhero_endpoint_url( $endpoint = false ) {
	global $wp;

	$memberhero_endpoints = memberhero()->query->get_query_vars();

	if ( false !== $endpoint ) {
		if ( ! isset( $memberhero_endpoints[ $endpoint ] ) ) {
			return false;
		} else {
			$endpoint_var = $memberhero_endpoints[ $endpoint ];
		}

		return isset( $wp->query_vars[ $endpoint_var ] );
	} else {
		foreach ( $memberhero_endpoints as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return true;
			}
		}

		return false;
	}
}