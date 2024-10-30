<?php
/**
 * User Hooks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hooks after a valid login - Member Hero authentication.
 */
add_action( 'wp_authenticate_user', 'memberhero_authenticate_user_login', 10, 2 );

// User update.
add_action( 'memberhero_created_user', 'memberhero_update_user_details', 10, 3 );
add_action( 'memberhero_account_update', 'memberhero_update_user_details', 10, 3 );
add_action( 'memberhero_profile_update', 'memberhero_update_user_details', 10, 3 );

// Validate usernames.
add_filter( 'validate_username', 'memberhero_validate_username', 100, 2 );

// Fired when user is deleted.
add_action( 'memberhero_pre_delete_user', 'memberhero_delete_user_files', 10, 2 );

/**
 * Authenticate user login and check their account status.
 */
function memberhero_authenticate_user_login( $user, $password ) {

	// Check that user capability does not allow login.
	if ( ! user_can( $user->ID, 'memberhero_log_in' ) && ! user_can( $user->ID, 'manage_options' ) ) {
		return new WP_Error( 'memberhero-login-disabled', apply_filters( 'memberhero_login_disabled_message', __( 'You do not have enough permissions to log in.', 'memberhero' ) ) );
	}

	$unconfirmed_email = get_user_meta( $user->ID, '_memberhero_unconfirmed_email', true );

	if ( $unconfirmed_email ) {
		return new WP_Error( 'unconfirmed_email', apply_filters( 'memberhero_unconfirmed_email_message', __( 'Your account email is unconfirmed.', 'memberhero' ) ) );
	}

	$pending = get_user_meta( $user->ID, '_memberhero_pending', true );

	if ( $pending ) {
		return new WP_Error( 'pending_account', apply_filters( 'memberhero_pending_account_message', __( 'Your account is under review. You will be emailed when your account is ready.', 'memberhero' ) ) );
	}

	return $user;
}

/**
 * Update user details when they register.
 */
function memberhero_update_user_details( $user_id, $user, $args ) {

	$user->update( $args );

}

/**
 * Only allow alphanumeric and underscores for username.
 */
function memberhero_validate_username( $valid, $user ) {
	if ( ! preg_match( '/^[A-Za-z0-9_.]+$/', $user ) ) {
		return false;
	}

	$sanitized 	= sanitize_user( $user, true );
	$valid 		= ( $sanitized == $user );

	return $valid;
}

/**
 * Delete user uploaded files.
 */
function memberhero_delete_user_files( $user_id, $user = null ) {

	// Delete all user uploads.
	memberhero_delete_user_avatar( $user_id );
	memberhero_delete_user_cover( $user_id );
	memberhero_delete_user_uploads( $user_id );
}

/**
 * Deletes user avatar.
 */
function memberhero_delete_user_avatar( $user_id, $force = false ) {
	return memberhero_delete_user_photo( 'avatar', $user_id, $force );
}

/**
 * Deletes user cover.
 */
function memberhero_delete_user_cover( $user_id, $force = false ) {
	return memberhero_delete_user_photo( 'cover', $user_id, $force );
}

/**
 * A wrapper function to delete a user's photo.
 */
function memberhero_delete_user_photo( $name = 'avatar', $user_id, $force = false ) {
	$photo = get_user_meta( $user_id, "_memberhero_profile_{$name}", true );

	do_action( "memberhero_pre_delete_user_{$name}", $user_id, $photo, $force );

	if ( $photo && ( apply_filters( "memberhero_delete_previous_user_{$name}", true ) || $force ) ) {
		$file_path = memberhero_plugin_uploads_path( $name ) . '/' . $photo;

		wp_delete_file( $file_path );

		$sizes = call_user_func( "memberhero_get_{$name}_sizes" );

		foreach( ( array ) $sizes as $width ) {
			$height = $width;
			if ( $name == 'cover' ) {
				$height = floor( $width / 2.70 );
			}
			$ext = pathinfo( $photo, PATHINFO_EXTENSION );
			$file_path = memberhero_plugin_uploads_path( $name ) . '/' . str_replace( '.' . $ext, "_{$width}x{$height}.{$ext}", $photo );

			wp_delete_file( $file_path );
		}

		delete_user_meta( $user_id, "_memberhero_profile_{$name}" );

		do_action( "memberhero_delete_user_{$name}", $user_id, $photo, $force );
	}
}

/**
 * Delete user uploads aside from avatar and header.
 */
function memberhero_delete_user_uploads( $user_id = 0 ) {
	$files = get_user_meta( $user_id, '_memberhero_files', true );

	if ( ! empty( $files ) ) {
		foreach( apply_filters( 'memberhero_delete_user_files', $files ) as $file ) {
			foreach( memberhero_supported_upload_types() as $type ) {
				$file_path = memberhero_plugin_uploads_path( $type ) . '/' . $file;
				wp_delete_file( $file_path );
				unset( $files[ $file ] );
			}
		}
		delete_user_meta( $user_id, '_memberhero_files' );
	}

	// Fired once user uploads are erased.
	do_action( 'memberhero_deleted_user_uploads', $user_id );
}