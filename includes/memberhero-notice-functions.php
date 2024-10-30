<?php
/**
 * Message Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the count of notices added, either for all notices (default) or for one.
 */
function memberhero_notice_count( $notice_type = '' ) {

	$notice_count = 0;
	$all_notices  = (array) get_option( 'memberhero_notices' );

	if ( isset( $all_notices[ $notice_type ] ) ) {

		$notice_count = count( $all_notices[ $notice_type ] );

	} elseif ( empty( $notice_type ) ) {

		foreach ( $all_notices as $notices ) {
			$notice_count += count( $notices );
		}
	}

	return $notice_count;
}

/**
 * Check if a notice has already been added.
 */
function memberhero_has_notice( $message, $notice_type = 'success' ) {

	$notices = (array) get_option( 'memberhero_notices' );
	$notices = isset( $notices[ $notice_type ] ) ? $notices[ $notice_type ] : array();
	return array_search( $message, $notices, true ) !== false;
}

/**
 * Add and store a notice.
 */
function memberhero_add_notice( $message, $notice_type = 'success', $key = null ) {
	global $the_form;

	if ( ! empty( $key ) ) {
		if ( memberhero_form_has_error( $the_form->id, $key ) ) {
			return;
		}
		memberhero_form_add_error( $the_form->id, $key );
	}

	$notices = (array) get_option( 'memberhero_notices' );

	$notices[ $notice_type ][] = apply_filters( 'memberhero_add_' . $notice_type, $message );

	update_option( 'memberhero_notices', $notices );
}

/**
 * Set all notices at once.
 */
function memberhero_set_notices( $notices ) {
	update_option( 'memberhero_notices', $notices );
}


/**
 * Unset all notices.
 */
function memberhero_clear_notices() {
	update_option( 'memberhero_notices', null );
}

/**
 * Prints messages and errors which are stored then clears them.
 */
function memberhero_print_notices( $return = false ) {
	global $the_form;

	if ( isset( $the_form->id ) && memberhero()::$form_id > 0 && ( memberhero()::$form_id != $the_form->id ) ) {
		return;
	}

	$all_notices  = (array) get_option( 'memberhero_notices' );
	$notice_types = apply_filters( 'memberhero_notice_types', array( 'error', 'success', 'notice' ) );

	// Buffer output.
	ob_start();

	foreach ( $notice_types as $notice_type ) {
		if ( memberhero_notice_count( $notice_type ) > 0 ) {
			memberhero_get_template( "notices/{$notice_type}.php", array(
				'messages' => array_filter( $all_notices[ $notice_type ] ),
			) );
		}
	}

	memberhero_clear_notices();

	$notices = memberhero_kses_notice( ob_get_clean() );

	if ( $return ) {
		return $notices;
	}

	echo $notices;
}

/**
 * Print a single notice immediately.
 */
function memberhero_print_notice( $message, $notice_type = 'success' ) {
	if ( 'success' === $notice_type ) {
		$message = apply_filters( 'memberhero_add_message', $message );
	}

	memberhero_get_template( "notices/{$notice_type}.php", array(
		'messages' => array( apply_filters( 'memberhero_add_' . $notice_type, $message ) ),
	) );
}

/**
 * Returns all queued notices, optionally filtered by a notice type.
 */
function memberhero_get_notices( $notice_type = '' ) {

	$all_notices = (array) get_option( 'memberhero_notices' );

	if ( empty( $notice_type ) ) {
		$notices = $all_notices;
	} elseif ( isset( $all_notices[ $notice_type ] ) ) {
		$notices = $all_notices[ $notice_type ];
	} else {
		$notices = array();
	}

	return $notices;
}

/**
 * Add notices for WP Errors.
 */
function memberhero_add_wp_error_notices( $errors ) {
	if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			memberhero_add_notice( $error, 'error' );
		}
	}
}

/**
 * Filters out the same tags as wp_kses_post, but allows tabindex for <a> element.
 */
function memberhero_kses_notice( $message ) {
	return html_entity_decode( $message );
}