<?php
/**
 * User Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the current user ID in loop.
 */
function memberhero_get_user_id() {
	global $the_user;

	$user_id = isset( $the_user->ID ) ? absint( $the_user->ID ) : 0;

	return $user_id;
}

/**
 * Update user account.
 */
function memberhero_update_account( $user = null, $args = array() ) {
	global $the_form;

	if ( ! is_object( $user ) ) {
		$user = memberhero_get_user( $user );
	}

	// Fired before updating user data.
	do_action( 'memberhero_pre_account_update', $user->ID, $user, $args);

	if ( isset( $the_form->endpoint ) ) {
		do_action( 'memberhero_pre_account_update_' . $the_form->get_endpoint(), $user->ID, $user, $args );
	}

	// Fired after updating user data.
	do_action( 'memberhero_account_update', $user->ID, $user, $args );

	if ( isset( $the_form->endpoint ) ) {
		do_action( 'memberhero_account_update_' . $the_form->get_endpoint(), $user->ID, $user, $args );
	}
}

/**
 * Update user profile.
 */
function memberhero_update_profile( $user = null, $args = array() ) {
	global $the_form;

	if ( empty( $user ) ) {
		$user = memberhero_get_active_profile_id();
	}

	if ( ! is_object( $user ) ) {
		$user = memberhero_get_user( $user );
	}

	// Fired before updating user data.
	do_action( 'memberhero_pre_profile_update', $user->ID, $user, $args);

	// Fired after updating user data.
	do_action( 'memberhero_profile_update', $user->ID, $user, $args );
}

/**
 * Create a new user.
 */
function memberhero_create_user( $args = array() ) {
	global $the_form;

	$defaults = apply_filters( 'memberhero_create_user_defaults', array(
		'user_login'	=> '',
		'user_email'	=> '',
		'user_pass'		=> wp_generate_password( 8, false, false ),
		'role'			=> memberhero_get_default_role(),
	) );

	// No worries. This is deleted once user registration is activated.
	if ( isset( $the_form ) && empty( $args[ 'user_pass' ] ) ) {
		$the_form->password_generated = $defaults[ 'user_pass' ];
	}

	$args = wp_parse_args( $args, $defaults );

	foreach( $defaults as $key => $value ) {
		if ( isset( $args[ $key ] ) && empty( $args[ $key ] ) ) {
			$args[ $key ] = $defaults[ $key ];
		}
	}

	// Register with email as username?
	if ( empty( $args[ 'user_login' ] ) ) {
		$args[ 'user_login' ] = $args[ 'user_email'];
	}

	// If this form must register a specific role.
	if ( isset( $the_form ) ) {
		if ( 'yes' === $the_form->force_role && $the_form->role ) {
			if ( ! in_array( $the_form->role, memberhero_get_admin_roles() ) ) {
				$args[ 'role' ] = $the_form->role;
			}
		}
	}

	// Add user.
	$new_user_id = wp_insert_user( $args );

	if ( is_wp_error( $new_user_id ) ) {

		do_action( 'memberhero_create_user_failed', $new_user_id, $args );

		return $new_user_id;

	} else {

		$user = memberhero_get_user( $new_user_id );

	}

	// Fired once a user is created.
	do_action( 'memberhero_created_user', $user->ID, $user, $args );

	return $user;
}

/**
 * Checks for a valid user login.
 */
function memberhero_check_user_login( $args = array() ) {
	global $the_form;

	if ( is_array( $args ) ) {
		extract( $args );
	}

	if ( empty( $user_pass ) ) {
		$user_pass = null;
	}

	if ( empty( $user_login ) ) {
		$user_login = ! empty( $user_email ) ? $user_email : '';
	}

	$user = memberhero_get_user( $user_login );

	// Before credentials check.
	$check = apply_filters( 'memberhero_pre_login_frontend', $user, $user_login, null );
	if ( is_wp_error( $check ) ) {
		return $check;
	}

	// Bypass login filter.
	$bypass = apply_filters( 'memberhero_bypass_invalid_login', false );

	if ( is_wp_error( $bypass ) ) {
		return $bypass;
	}

	// Check credentials.
	if ( ! $bypass && ( ! isset( $user->user_login ) || ! $auth = wp_check_password( $user_pass, $user->user_pass, $user->ID ) ) ) {

		// by pass login filter.
		apply_filters( 'memberhero_bypass_invalid_login', false );

		do_action( 'memberhero_invalid_login', $args );

		$invalid = apply_filters( 'memberhero_authenticate_user', $user, $user_pass );
		if ( is_wp_error( $invalid ) ) {
			return $invalid;
		}
		return new WP_Error( 'invalid_login', __( 'Invalid credentials were provided', 'memberhero' ) );
	}

	$user = apply_filters( 'wp_authenticate_user', $user, $user_pass );
	$user = apply_filters( 'memberhero_valid_login_credentials', $user );

	// If the form only allow specific role to log-in.
	if ( ! is_wp_error( $user ) ) {
		if ( 'yes' === $the_form->force_role && $the_form->role ) {
			if ( $the_form->role !== $user->get_role() ) {
				return new WP_Error( 'invalid_role', sprintf( __( 'You must be a %s to access this area.', 'memberhero' ), esc_html( $the_form->get_role_title() ) ) );
			}
		}
	}

	return $user;
}

/**
 * Log in a user programmatically.
 */
function memberhero_login_user( $user = null ) {
	// Log out if user is logged in.
	if ( is_user_logged_in() ) {
		wp_logout();
	}

	wp_set_current_user( $user->ID, $user->user_login );
	wp_set_auth_cookie( $user->ID, true );

	$wp_user = new WP_User( $user->ID );

	do_action( 'wp_login', $user->user_login, $wp_user );
	do_action( 'memberhero_valid_login', $user->ID, $user );
}

/**
 * Get a user.
 */
function memberhero_get_user( $user = '' ) {
	return new MemberHero_User( $user );
}

/**
 * Check if user can edit the current profile.
 */
function memberhero_user_can_edit_profile( $user_id = 0 ) {
	global $the_user;

	if ( ! is_user_logged_in() ) {
		return false;
	}

	// Default profile id.
	if ( empty( $user_id ) ) {
		$user_id = memberhero_get_active_profile_id();
	}

	// User viewing their own profile.
	if ( is_memberhero_my_profile( $user_id ) ) {
		if ( current_user_can( 'memberhero_edit_profile' ) ) {
			return true;
		}
	} else {
		// Check if the user has access to edit that profile.
		if ( current_user_can( 'manage_memberhero' ) || current_user_can( 'memberhero_edit_users' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Get the user registration date.
 */
function memberhero_get_user_registered( $registered = '' ) {
	global $the_user;

	if ( empty( $registered ) ) {
		$registered = $the_user->get( 'user_registered' );
	}

	$date = memberhero_get_date_from_gmt( $registered, 'F Y' );

	return apply_filters( 'memberhero_get_user_registered', $date );
}

/**
 * Get the user registration date in detailed format.
 */
function memberhero_get_user_registered_details( $registered = '' ) {
	global $the_user;

	if ( empty( $registered ) ) {
		$registered = $the_user->get( 'user_registered' );
	}

	$date = memberhero_get_date_from_gmt( $registered, 'g:i A - j M Y' );

	return apply_filters( 'memberhero_get_user_registered_details', $date );
}

/**
 * Get the user posts count.
 */
function memberhero_get_user_posts_count( $user_id = null ) {
	global $wpdb, $the_user;

	// If no user ID was provided.
	if ( ! $user_id ) {
		$user_id = isset( $the_user->user_id ) ? $the_user->user_id : 0;
	}

	$count = $wpdb->get_var(
		$wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_author = %d AND post_status = 'publish' AND post_type = 'post'", $user_id )
	);

	return apply_filters( 'memberhero_get_user_posts_count', $count, $user_id );
}

/**
 * Get the user comments count.
 */
function memberhero_get_user_comments_count( $user_id = null ) {
	global $wpdb, $the_user;

	// If no user ID was provided.
	if ( ! $user_id ) {
		$user_id = isset( $the_user->user_id ) ? $the_user->user_id : 0;
	}

	$count = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT COUNT( comment_id ) FROM '. $wpdb->comments .'
			WHERE user_id = %d
			AND comment_approved = "1"
			AND comment_type NOT IN ( "pingback", "trackback" )',
			$user_id
		)
	);

	return apply_filters( 'memberhero_get_user_comments_count', $count, $user_id );
}

/**
 * A wrapper function to check if user can access the field.
 */
function memberhero_user_can_access_field( $field, $scope = 'edit' ) {
	global $the_user;
	if ( $scope == 'edit' ) {
		return memberhero_user_can_edit_field( $field );
	} else {
		return memberhero_user_can_view_field( $field );
	}
}

/**
 * Check if the current user can edit the field.
 */
function memberhero_user_can_edit_field( $field ) {
	global $the_user;

	return true;
}

/**
 * Check if the current user can view the field.
 */
function memberhero_user_can_view_field( $field ) {
	global $the_user;

	// Empty value fields should not appear.
	if ( $field[ 'value' ] == null ) {
		if ( ! in_array( $field[ 'type' ], array( 'toggle' ) ) ) {
			return false;
		}
	}

	// Check for fields that should never be visible.
	if ( memberhero_is_password_field( $field ) ) {
		return false;
	}

	// Check for a private field.
	if ( memberhero_is_private_field( $field ) ) {
		if ( $the_user->user_id != get_current_user_id() ) {
			if ( ! current_user_can( 'memberhero_view_private_data' ) ) {
				return false;
			}
		}
	}

	return true;
}

/**
 * Get the user's country name.
 */
function memberhero_get_user_country() {
	global $the_user;

	return apply_filters( 'memberhero_get_user_country', memberhero()->countries->get_country( $the_user->get( 'country' ) ) );
}

/**
 * Returns true if the user has not any avatar.
 */
function memberhero_user_has_no_gravatar( $user_id = 0, $email = '' ) {
	global $the_user;

	if ( empty( $user_id ) ) {
		$user_id 	= $the_user->user_id;
		$email		= $the_user->get( 'user_email' );
	}

	// Quit early if the user has uploaded avatar.
	$has_avatar		= get_user_meta( $user_id, '_memberhero_profile_avatar', true );
	if ( $has_avatar ) {
		return false;
	}

	// Check that user has a gravatar.
	$user_hash 		= md5( strtolower( trim( $email ) ) );
	$uri 			= 'http://www.gravatar.com/avatar/' . $user_hash . '?d=404';
	$data 			= get_transient( '_memberhero_avatar_' . $user_hash );

	// Store the data in WP cache.
	if ( false === $data ) {
		$response = wp_remote_head( $uri );
		if ( is_wp_error( $response ) ) {
			$data = 'invalid';
		} else {
			$data = $response[ 'response' ][ 'code' ];
		}
		// Set the transient.
		set_transient( '_memberhero_avatar_' . $user_hash, $data, ( 6 * HOUR_IN_SECONDS ) );
	}

	if ( $data == 200 ) {
		return false;
	}

	return true;
}

/**
 * Checks whether the user has uploaded a custom avatar.
 */
function memberhero_user_uploaded_avatar( $user_id = 0 ) {
	return memberhero_user_uploaded_photo( 'avatar', $user_id );
}

/**
 *  Checks whether the user has uploaded a custom cover.
 */
function memberhero_user_uploaded_cover( $user_id = 0 ) {
	return memberhero_user_uploaded_photo( 'cover', $user_id );
}

/**
 * A wrapper function to check if user has uploaded a custom photo.
 */
function memberhero_user_uploaded_photo( $name = 'avatar', $user_id = 0 ) {
	global $the_user;

	if ( ! $user_id ) {
		$user_id = ! empty( $the_user->ID ) ? $the_user->ID : 0;
	}

	if ( empty( $user_id ) ) {
		return false;
	}

	$photo = get_user_meta( $user_id, "_memberhero_profile_{$name}", true );

	return apply_filters( "memberhero_user_uploaded_{$name}", ! empty( $photo ), $the_user );
}

/**
 * Returns user avatar URL.
 */
function memberhero_get_user_avatar_url( $user_id = 0, $size = null ) {
	return memberhero_get_user_photo_url( 'avatar', $user_id, $size );
}

/**
 * Returns user cover URL.
 */
function memberhero_get_user_cover_url( $user_id = 0, $size = null ) {
	// Uses a reduced thumb size when in the loop.
	if ( memberhero_is_in_loop() ) {
		$size = 600;
	}

	return memberhero_get_user_photo_url( 'cover', $user_id, $size );
}

/**
 * A wrapper function to return the user photo URL.
 */
function memberhero_get_user_photo_url( $name = 'avatar', $user_id = 0, $size = null ) {
	global $the_user;

	$url = null;

	if ( ! $user_id ) {
		$user_id 	= $the_user->user_id;
		$method 	= "get_{$name}";
		$photo 		= $the_user->$method();
	} else {
		$photo 		= get_user_meta( $user_id, "_memberhero_profile_{$name}", true );
	}

	// Get a specific size if requested.
	if ( $size ) {
		$memberhero_size = call_user_func_array( "memberhero_get_best_{$name}_size", array( $size ) );
		if ( $memberhero_size ) {
			$memberhero_height = $memberhero_size;
			if ( $name == 'cover' ) {
				$memberhero_height = floor( $memberhero_size / 2.70 );
			}
			$ext = pathinfo( $photo, PATHINFO_EXTENSION );
			$photo = str_replace( ".{$ext}", "_{$memberhero_size}x{$memberhero_height}.{$ext}", $photo );
		}
	}

	if ( ! empty( $photo ) ) {
		$url = memberhero_generate_upload_url( $photo, $name );
	}

	return apply_filters( "memberhero_get_user_{$name}_url", $url, $user_id, $size );
}

/**
 * Check if user can block or unblock another user.
 */
function memberhero_user_can_block( $user_id = 0 ) {
	if ( ! is_user_logged_in() || ( $user_id == get_current_user_id() ) ) {
		return false;
	}

	if ( ! ( is_numeric( $user_id ) && $user_id > 0 && $user_id == round( $user_id, 0 ) ) ) {
		return false;
	}

	// Check that user exists.
	$user = get_userdata( $user_id );
	if ( $user == false ) {
		return false;
	}

	return $user;
}

/**
 * Blocks a user.
 */
function memberhero_block_user( $user = null ) {
	if ( ! is_object( $user ) ) {
		$user_id = $user;
	} else {
		$user_id = $user->ID;
	}

	$logged_user = get_current_user_id();

	$blocked = get_user_meta( $logged_user, '_memberhero_blocked_users', true );

	// User already blocked?
	if ( is_array( $blocked ) && array_key_exists( $user_id, $blocked ) ) {
		return -1;
	}

	// Add user to block list.
	if ( empty( $blocked ) ) {
		$blocked = array();
	}

	$blocked[ $user_id ] = time();

	do_action( 'memberhero_pre_block_user', $logged_user, $user_id );

	update_user_meta( $logged_user, '_memberhero_blocked_users', $blocked );

	do_action( 'memberhero_block_user', $logged_user, $user_id );

	return true;
}

/**
 * Unblocks a user.
 */
function memberhero_unblock_user( $user = null ) {
	if ( ! is_object( $user ) ) {
		$user_id = $user;
	} else {
		$user_id = $user->ID;
	}

	$logged_user = get_current_user_id();

	$blocked = get_user_meta( $logged_user, '_memberhero_blocked_users', true );

	// User already un-blocked?
	if ( ! is_array( $blocked ) || ! array_key_exists( $user_id, $blocked ) ) {
		return -1;
	}

	// Remove the block.
	unset( $blocked[ $user_id ] );

	do_action( 'memberhero_pre_unblock_user', $logged_user, $user_id );

	update_user_meta( $logged_user, '_memberhero_blocked_users', $blocked );

	do_action( 'memberhero_unblock_user', $logged_user, $user_id );

	return true;
}

/**
 * Check if user has blocked someone.
 */
function memberhero_user_has_blocked( $user_id = 0 ) {
	if ( $user_id == get_current_user_id() ) {
		return false;
	}

	$blocked = get_user_meta( get_current_user_id(), '_memberhero_blocked_users', true );

	return is_array( $blocked ) && array_key_exists( $user_id, $blocked );
}

/**
 * Check if current profile blocked current user.
 */
function memberhero_user_has_blocked_me( $user_id = 0 ) {
	if ( $user_id == get_current_user_id() ) {
		return false;
	}

	$blocked = get_user_meta( $user_id, '_memberhero_blocked_users', true );

	return is_array( $blocked ) && array_key_exists( get_current_user_id(), $blocked );
}

/**
 * Delete a user.
 */
function memberhero_delete_user( $user_id = 0 ) {
	if ( ! function_exists( 'wp_delete_user' ) ) {
		require_once( ABSPATH. 'wp-admin/includes/user.php' );
	}

	if ( ! is_numeric( $user_id ) || absint( $user_id ) <= 0 ) {
		return false;
	}

	// To avoid deleting an admin by mistake.
	if ( is_super_admin( $user_id ) ) {
		return false;
	}

	$user = get_userdata( $user_id );

	// If user is not found. bail.
	if ( $user == false ) {
		return false;
	}

	do_action( 'memberhero_user_deleted', $user_id );

	do_action( 'memberhero_pre_delete_user', $user_id, $user );

	wp_delete_user( $user_id );

	do_action( 'memberhero_delete_user', $user_id, $user );

	return $user;
}

/**
 * Checks that email is registered but has not been confirmed yet.
 */
function memberhero_user_email_unconfirmed( $email = '' ) {
	$user_id = email_exists( $email );
	$email = get_user_meta( $user_id, '_memberhero_unconfirmed_email', true );

	if ( $email ) {
		return true;
	}
	return false;
}

/**
 * Returns true if a user account is not yet active.
 */
function is_memberhero_inactive_user( $user_id = 0 ) {
	$unconfirmed = get_user_meta( $user_id, '_memberhero_unconfirmed_email', true );
	$pending     = get_user_meta( $user_id, '_memberhero_pending', true );

	return apply_filters( 'is_memberhero_inactive_user', ( bool ) $unconfirmed || $pending, $user_id );
}

/**
 * Returns true if a user account is pending.
 */
function is_memberhero_pending_user( $user_id = 0 ) {
	$pending = get_user_meta( $user_id, '_memberhero_pending', true );

	return apply_filters( 'is_memberhero_pending_user', ( bool ) $pending, $user_id );
}

/**
 * Returns true if a user account was rejected.
 */
function is_memberhero_rejected_user( $user_id = 0 ) {
	$rejected = get_user_meta( $user_id, '_memberhero_rejected', true );

	return apply_filters( 'is_memberhero_rejected_user', ( bool ) $rejected, $user_id );
}

/**
 * Returns true if a user account has unconfirmed email.
 */
function is_memberhero_unconfirmed_email_user( $user_id = 0 ) {
	$unconfirmed = get_user_meta( $user_id, '_memberhero_unconfirmed_email', true );

	return apply_filters( 'is_memberhero_unconfirmed_email_user', ( bool ) $unconfirmed, $user_id );
}

/**
 * Checks if a user has a pending email change.
 */
function memberhero_user_has_pending_email( $user_id = 0 ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$email = get_user_meta( $user_id, '_memberhero_user_email', true );
	if ( $email ) {
		$pending_email = true;
	} else {
		$pending_email = false;
	}

	return apply_filters( 'memberhero_user_has_pending_email', ( bool ) $pending_email, $user_id );
}

/**
 * Cancel a pending email change for user.
 */
function memberhero_user_cancel_pending_email( $user_id = 0 ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	delete_user_meta( $user_id, '_memberhero_user_email' );
	delete_user_meta( $user_id, '_memberhero_emailchange_token' );
}

/**
 * Get user statuses.
 */
function memberhero_get_user_statuses() {

	$array = array(
		'approved'		=> __( 'Approved', 'memberhero' ),
		'unconfirmed'	=> __( 'Unconfirmed', 'memberhero' ),
		'pending'		=> __( 'Pending review', 'memberhero' ),
		'rejected'		=> __( 'Rejected', 'memberhero' ),
	);

	return apply_filters( 'memberhero_get_user_statuses', $array );
}

/**
 * Get user description.
 */
function memberhero_get_user_description( $limit = false ) {
	global $the_user, $the_list;

	if ( isset( $the_list->_in_loop ) || $limit ) {
		$description = memberhero_limit_text( $the_user->get( 'description' ) );
	} else {
		$description = $the_user->get( 'description' );
	}

	// If links are allowed in description.
	if ( 'yes' === get_option( 'memberhero_autolinks' ) ) {
		$description = memberhero_make_clickable( $description );
	}

	return apply_filters( 'memberhero_get_user_description', $description );
}

/**
 * A quick check if the user can delete their own account.
 */
function memberhero_can_delete_their_account( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	return apply_filters( 'memberhero_can_delete_their_account', current_user_can( 'memberhero_delete_account' ) && ! is_super_admin( $user_id ), $user_id );
}

/**
 * Bulk update user meta.
 */
function memberhero_update_usermeta( $user_id = 0, $data = array() ) {

	foreach( $data as $key => $value ) {
		if ( ! in_array( $key, memberhero_core_meta_keys() ) ) {
			update_user_meta( $user_id, $key, $value );
		}
	}
}

/**
 * Meta keys that should not be run in update_user_meta()
 */
function memberhero_core_meta_keys() {

	$array = array(
		'user_login',
		'user_email',
		'user_pass',
		'role'
	);

	return apply_filters( 'memberhero_core_meta_keys', $array );
}

/**
 * Email notification sent with account details.
 */
function memberhero_new_user_notification( $user_id = 0, $plaintext_pass = '' ) {
	$user = new WP_User( $user_id );

	$user_login = stripslashes( $user->user_login );
	$user_email = stripslashes( $user->user_email );

	if ( empty( $plaintext_pass ) )
		return;

	$the_user 	= memberhero_get_user( $user_id );
	$role_id	= $the_user->get_role_id();

	if ( ! $role_id ) {
		$the_role = null;
	}

	$the_role = new MemberHero_Role( $role_id );
	$the_form = new MemberHero_Form();
	$the_form->password_generated = $plaintext_pass;

	memberhero()->mailer();

	do_action( 'memberhero_new_user_notification', $the_user, array(), $the_form, $the_role );

	do_action( 'memberhero_new_user_notification', $user_id );
}