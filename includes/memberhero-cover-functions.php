<?php
/**
 * Cover Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the best size for cover photo.
 */
function memberhero_get_best_cover_size( $search ) {
	$array = memberhero_get_cover_sizes();

	sort( $array );

	foreach ( $array as $item) {
		if ( $item >= $search ) {
			return $item;
		}
	}
	return false;
}

/**
 * Returns an array of different sizes for header photos.
 */
function memberhero_get_cover_sizes() {
	return apply_filters( 'memberhero_get_cover_sizes', array( 300, 600 ) );
}

/**
 * Get profile cover div classes.
 */
function memberhero_get_profile_cover_classes() {
	global $the_user;

	$classes = array();

	if ( ! $the_user->has_cover() ) {
		if ( ! is_memberhero_editing_profile() && is_memberhero_profile_page() ) {
			$classes[] = 'empty-cover';
		}
	}

	$classes = apply_filters( 'memberhero_get_profile_cover_classes', $classes );

	return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}