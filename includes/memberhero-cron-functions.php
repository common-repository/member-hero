<?php
/**
 * Cron Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove the users who were rejected to clean up database.
 */
function memberhero_delete_rejected_users() {
	$args = array(
		'number'		=> 20,
		'fields'		=> 'ID',
		'orderby'		=> 'registered',
		'order'			=> 'asc',
		'meta_query' 	=> array(
			'relation'	=> 'and',
			array(
				'key' 		=> '_memberhero_rejected',
                'value' 	=> 1,
                'compare' 	=> '='
			)
		)
	);

	$users = get_users( $args );

	if ( ! empty( $users ) ) {
		foreach( $users as $user_id ) {
			memberhero_delete_user( $user_id );
		}
	}
}
add_action( 'memberhero_delete_rejected_users', 'memberhero_delete_rejected_users' );

/**
 * Remove the users who have not confirmed their email in the specified time.
 */
function memberhero_delete_unconfirmed_users() {

	$duration = absint( get_option( 'memberhero_delete_unconfirmed_emails_duration', 6 ) );

	if ( ! $duration ) {
		return;
	}

	$before = absint( $duration );

	if ( $before <= 0 ) {
		return;
	}

	$before = $before == 1 ? '-1 hour' : '-' . $before . ' hours';

	$args = array(
		'number'		=> 20,
		'fields'		=> 'ID',
		'orderby'		=> 'registered',
		'order'			=> 'asc',
		'meta_query' 	=> array(
			'relation'	=> 'and',
			array(
				'key' 		=> '_memberhero_unconfirmed_email',
                'value' 	=> 1,
                'compare' 	=> '='
			)
		),
		'date_query'	=> array(
			array(
				'before' 	=> date( 'Y-m-d H:i:s', strtotime( $before ) ),
				'inclusive' => true
			)
		)
	);

	$users = get_users( $args );

	if ( ! empty( $users ) ) {
		foreach( $users as $user_id ) {
			memberhero_delete_user( $user_id );
		}
	}
}
add_action( 'memberhero_delete_unconfirmed_users', 'memberhero_delete_unconfirmed_users' );