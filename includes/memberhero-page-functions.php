<?php
/**
 * Page Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve page permalink.
 */
function memberhero_get_page_permalink( $page, $fallback = null ) {
	$page_id   = memberhero_get_page_id( $page );
	$permalink = 0 < $page_id ? get_permalink( $page_id ) : '';

	if ( ! $permalink ) {
		$permalink = is_null( $fallback ) ? get_home_url() : $fallback;
	}

	return apply_filters( 'memberhero_get_' . $page . '_page_permalink', $permalink );
}

/**
 * Retrieve page ids
 */
function memberhero_get_page_id( $page ) {
	$page = apply_filters( 'memberhero_get_' . $page . '_page_id', get_option( 'memberhero_' . $page . '_page_id' ) );

	return $page ? absint( $page ) : -1;
}

/**
 * Get endpoint URL.
 */
function memberhero_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
	if ( ! $permalink ) {
		$permalink = get_permalink();
	}

	// Map endpoint to options.
	$query_vars = memberhero()->query->get_query_vars();
	$endpoint   = ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $permalink, '?' ) ) {
			$query_string = '?' . wp_parse_url( $permalink, PHP_URL_QUERY );
			$permalink    = current( explode( '?', $permalink ) );
		} else {
			$query_string = '';
		}
		$url = trailingslashit( $permalink ) . untrailingslashit( $endpoint );

		if ( $value ) {
			$url .= trailingslashit( $value );
		}

		$url .= $query_string;
	} else {
		$url = add_query_arg( $endpoint, $value, $permalink );
	}

	return apply_filters( 'memberhero_get_endpoint_url', $url, $endpoint, $value, $permalink );
}

/**
 * Get current URL.
 */
function memberhero_get_current_url() {
	global $wp;

	$url = home_url( add_query_arg( array(), $wp->request ) );

	return apply_filters( 'memberhero_get_current_url', $url );
}

/**
 * Get current URL without query parameters.
 */
function memberhero_get_current_url_clean() {
	$url = explode( '?', esc_url_raw( add_query_arg( array() ) ) );

	return apply_filters( 'memberhero_get_current_url_clean', $url[0] );
}

/**
 * Get logout endpoint.
 */
function memberhero_logout_url( $redirect = '' ) {
	return wp_logout_url( $redirect );
}

/**
 * Get default login URL.
 */
function memberhero_login_url() {

	return apply_filters( 'memberhero_login_url', memberhero_get_page_permalink( 'login' ) );
}

/**
 * Get default register URL.
 */
function memberhero_register_url() {

	return apply_filters( 'memberhero_register_url', memberhero_get_page_permalink( 'register' ) );
}

/**
 * Get default lostpassword URL.
 */
function memberhero_lostpassword_url() {

	return apply_filters( 'memberhero_lostpassword_url', memberhero_get_page_permalink( 'lostpassword' ) );
}