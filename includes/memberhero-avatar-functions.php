<?php
/**
 * Avatar Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom user-avatar output.
 */
add_filter( 'get_avatar', 'memberhero_get_avatar', 9999, 6 );
function memberhero_get_avatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {
    $user = false;

	if ( is_numeric( $id_or_email ) ) {
		$id = (int) $id_or_email;
		$user = get_user_by( 'id', $id );
    } elseif ( is_object( $id_or_email ) ) {
		if ( ! empty( $id_or_email->user_id ) ) {
			$id = (int) $id_or_email->user_id;
			$user = get_user_by( 'id', $id );
		}
	} else {
		$user = get_user_by( 'email', $id_or_email );	
	}

	// If no user is found, assume the user id is 0.
	$user_id = ( isset( $user->ID ) ) ? absint( $user->ID ) : 0;

	// Check if user has a custom uploaded avatar.
	if ( memberhero_user_uploaded_avatar( $user_id ) ) {
		$avatar_url = memberhero_get_user_avatar_url( $user_id, $size );
	} else {
		$avatar_url = get_avatar_url( $user_id, array( 'default' => memberhero_get_default_avatar(), 'size' => $size ) );
	}

	// User display name.
	$display_name = ! empty( $user->display_name ) ? $user->display_name : '';

	// HTML for avatar output.
	$avatar = "<img alt='' src='{$avatar_url}' srcset='{$avatar_url} 2x' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}'>";

	return apply_filters( 'memberhero_get_avatar', $avatar, $id_or_email, $size, $default, $alt, $args );
}

/**
 * Get default avatar.
 */
function memberhero_get_default_avatar() {
	$url = memberhero()->plugin_url() . '/assets/images/default-avatar.jpg';

	return apply_filters( 'memberhero_get_default_avatar', esc_url( $url ) );
}

/**
 * Get the best or closest avatar size.
 */
function memberhero_get_best_avatar_size( $search ) {
	$array = memberhero_get_avatar_sizes();

	sort( $array );

	foreach ( $array as $item) {
		if ( $item >= $search ) {
			return $item;
		}
	}
	return false;
}

/**
 * Returns an array of different sizes for avatars.
 */
function memberhero_get_avatar_sizes() {
	return apply_filters( 'memberhero_get_avatar_sizes', array( 48, 80, 200 ) );
}

/**
 * Get avatar size based on template location.
 */
function memberhero_get_avatar_template_size() {
	$size = 200; // Default size.

	// Uses a small avatar version in the loop.
	if ( memberhero_is_in_loop() ) {
		$size = 80;
	}

	return apply_filters( 'memberhero_get_avatar_template_size', $size );
}