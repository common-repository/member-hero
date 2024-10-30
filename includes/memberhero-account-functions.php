<?php
/**
 * Account Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get account endpoints.
 */
function memberhero_get_account_endpoints() {
	$endpoints = array(
		'edit-account'				=> get_option( 'memberhero_account_edit_account_endpoint', 'edit-account' ),
		'edit-password'       		=> get_option( 'memberhero_account_edit_password_endpoint', 'edit-password' ),
		'privacy'              		=> get_option( 'memberhero_account_privacy_endpoint', 'privacy' ),
		'email-notifications'		=> get_option( 'memberhero_account_email_notifications_endpoint', 'email-notifications' ),
		'blocked'					=> get_option( 'memberhero_account_blocked_endpoint', 'blocked' ),
		'delete'					=> get_option( 'memberhero_account_delete_endpoint', 'delete' ),
	);

	$endpoints = apply_filters( 'memberhero_get_account_endpoints', ( array ) $endpoints );

	return $endpoints;
}

/**
 * Get the account endpoint icon.
 */
function memberhero_get_account_endpoint_icon( $endpoint ) {
	$icon = null;

	switch( $endpoint ) {
		case 'edit-account' :
			$icon = 'settings';
		break;
		case 'edit-password' :
			$icon = 'key';
		break;
		case 'privacy' :
			$icon = 'lock';
		break;
		case 'email-notifications' :
			$icon = 'mail';
		break;
		case 'blocked' :
			$icon = 'slash';
		break;
		case 'delete' :
			$icon = 'trash';
		break;
	}

	$icon = apply_filters( 'memberhero_get_account_endpoint_icon', $icon, $endpoint );

	if ( ! empty( $icon ) ) {
		echo memberhero_svg_icon( esc_attr( $icon ) );
	}
}

/**
 * Get account default endpoint.
 */
function memberhero_get_account_default_endpoint() {
	$endpoint = get_option( 'memberhero_default_account_endpoint', 'edit-account' );

	if ( empty( trim( $endpoint ) ) ) {
		$endpoint = 'edit-account';
	}

	return apply_filters( 'memberhero_get_account_default_endpoint', $endpoint );
}

/**
 * Get account endpoint URL.
 */
function memberhero_get_account_endpoint_url( $endpoint ) {
	if ( 'logout' === $endpoint ) {
		return memberhero_logout_url();
	}

	return memberhero_get_endpoint_url( $endpoint, '', memberhero_get_page_permalink( 'account' ) );
}

/**
 * Get account endpoint form.
 */
function memberhero_get_account_endpoint_form() {
	$endpoint = memberhero()->query->get_current_endpoint();

	if ( $endpoint ) {
		return get_option( 'memberhero_account_' . memberhero_sanitize_title( $endpoint ) . '_form' );
	}

	return get_option( 'memberhero_account_' . memberhero_sanitize_title( memberhero_get_account_default_endpoint() ) . '_form' );
}

/**
 * Get account menu items.
 */
function memberhero_get_account_menu_items( $exclude = array() ) {

	$endpoints = memberhero_get_account_endpoints();
	$default = memberhero_get_account_default_endpoint();

	// Remove missing endpoints.
	foreach ( $endpoints as $endpoint_id => $endpoint ) {
		if ( empty( $endpoint ) ) {
			unset( $items[ $endpoint_id ] );
		}
		$items[ $endpoint_id ] = memberhero()->query->get_endpoint_title( $endpoint_id );
	}

	// Make sure that default endpoint comes on top.
	$default_endpoint = $items[ $default ];
	unset( $items[ $default ] );
	$items = array_merge( array( $default => $default_endpoint ) , $items );

	// Exclude items from menu.
	if ( ! empty( $exclude ) ) {
		foreach( $exclude as $item ) {
			unset( $items[ $item ] );
		}
	}

	// User not allowed to delete account.
	if ( ! memberhero_can_delete_their_account() ) {
		unset( $items[ 'delete' ] );
	}

	// Move blocked account tab to the very bottom.
	if ( isset( $items[ 'blocked' ] ) ) {
		$del = $items[ 'blocked' ];
		unset( $items[ 'blocked' ] );
		$items[ 'blocked' ] = $del;
	}

	// Move delete account tab to the very bottom.
	if ( isset( $items[ 'delete' ] ) ) {
		$del = $items[ 'delete' ];
		unset( $items[ 'delete' ] );
		$items[ 'delete' ] = $del;
	}

	return apply_filters( 'memberhero_account_menu_items', $items, $endpoints );
}

/**
 * Get account menu item classes.
 */
function memberhero_get_account_menu_item_classes( $endpoint ) {
	global $wp;

	$classes = array(
		'memberhero-account-navigation-link',
		'memberhero-account-navigation-link--' . $endpoint,
	);

	// Set current item class.
	$current = isset( $wp->query_vars[ $endpoint ] );

	// Fallback to default content item.
	if ( memberhero_get_account_default_endpoint() === $endpoint && ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) ) {
		$current = true;
	}

	if ( $current ) {
		$classes[] = 'is-active';
	}

	$classes = apply_filters( 'memberhero_account_menu_item_classes', $classes, $endpoint );

	return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}