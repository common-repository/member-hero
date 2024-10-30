<?php
/**
 * User class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_User class.
 */
class MemberHero_User {

	/**
	 * User.
	 */
	public $user = null;

	/**
	 * User ID
	 */
	public $user_id = 0;

	/**
	 * WP_User object user id.
	 */
	public $ID = 0;

	/**
	 * @boolean Check whether user object is in widget.
	 */
	public $in_widget = false;

	/**
	 * Was the biography shown already.
	 */
	public $bio_shown = false;

	/**
	 * Keys that should not be updated in wp_usermeta.
	 */
	private $core_meta_keys = array(
		'user_login',
		'user_email',
		'user_pass',
		'user_url',
		'display_name',
		'new_password',
		'verify_new_password',
		'confirm_user_email',
		'confirm_user_pass',
		'role',
	);

	/**
	 * Construct.
	 */
	public function __construct( $user ) {
		$this->init( $user );
	}

	/**
	 * Init.
	 */
	public function init( $user ) {

		if ( is_numeric( $user ) ) {
			$user = get_user_by( 'id', absint( $user ) );
		} elseif ( is_email( $user ) ) {
			$user = get_user_by( 'email', sanitize_email( $user ) );
		} else {
			$user = get_user_by( 'login', sanitize_user( $user ) );
		}

		// Invalid user.
		if ( ! isset( $user->ID ) ) {
			return;
		}

		$this->user_id = $user->ID;

		// Add user data to the class.
		foreach( $user as $key => $value ) {
			if ( $key == 'data' ) {
				foreach( $value as $sub_data => $data ) {
					$this->$sub_data = $data;
				}
			} else {
				$this->{$key} = $value;
			}
		}
	}

	/**
	 * Get a user data.
	 */
	public function get( $key ) {
		$output = null;

		if ( isset( $this->{$key} ) ) {
			$output = $this->{$key};
		} else {
			$output = get_user_meta( $this->user_id, $key, true );
		}

		return apply_filters( "memberhero_getmeta_{$key}", $output, $this );
	}

	/**
	 * This is used to greet the user.
	 */
	public function get_greeting_name() {
		$first_name = $this->get( 'first_name' );

		return ! empty( $first_name ) ? $first_name : '@' . $this->user_login;
	}

	/**
	 * Update user details.
	 */
	public function update( $metadata ) {

		if ( is_array( $metadata ) ) {
			foreach( $metadata as $key => $value ) {
				if ( ! in_array( $key, $this->core_meta_keys ) ) {

					if ( memberhero_is_file_upload( $key, $value ) ) {

						// handle file upload.
						memberhero_handle_upload( $this->user_id, $key, $value );

					} else {

						// update user meta.
						update_user_meta( $this->user_id, $key, $value );
						do_action( 'memberhero_updated_user_' . $key, $value, $metadata, $this );
					}

				} else {

					$method = "maybe_update_{$key}";

					if ( method_exists( $this, $method ) ) {
						$this->$method( $value, $metadata );
					}
				}
			}
		}
	}

	/**
	 * Handle updating user login.
	 */
	public function maybe_update_user_login( $new_user_login, $metadata ) {
		global $wpdb, $the_form;

		$user_login = $this->user_login;

		if ( $new_user_login !== $user_login && get_option( 'memberhero_allow_user_login_change' ) == 'yes' ) {

			$wpdb->update( $wpdb->users, array( 'user_login' => $new_user_login ), array( 'ID' => $this->ID ) );

			// Clear cache and logout.
			wp_logout();

			wp_cache_delete( $this->ID, 'users' );
			wp_cache_delete( $user_login, 'userlogins' );

			// Log in again.
			$this->user_login = $new_user_login;
			$this->log_in();

			do_action( 'memberhero_username_updated', $this->ID, $user_login, $new_user_login );

			$the_form->saved = 'username_changed';
		}
	}

	/**
	 * Handle updating user email.
	 */
	public function maybe_update_user_email( $email, $metadata ) {
		global $the_form;

		// email unchanged.
		if ( $email == $this->user_email || apply_filters( 'memberhero_email_change_disabled', false, $this->ID ) ) {
			return;
		}

		$secret_key = $this->create_email_change_key( $email );

		// Load the mailer class.
		memberhero()->mailer();
		do_action( 'memberhero_email_change_notification', $this, $secret_key );

		$the_form->saved = 'confirm_email';
	}

	/**
	 * Handle updating user display name.
	 */
	public function maybe_update_display_name( $display_name, $metadata ) {
		global $wpdb, $the_form;

		wp_update_user( array(
			'ID' 			=> $this->ID,
			'display_name'	=> memberhero_clean( $display_name ),
		) );

		do_action( 'memberhero_display_name_updated', $this->ID, $display_name );
	}

	/**
	 * Handle updating user url.
	 */
	public function maybe_update_user_url( $url, $metadata ) {
		global $wpdb, $the_form;

		wp_update_user( array(
			'ID' 		=> $this->ID,
			'user_url'	=> esc_url_raw( $url ),
		) );

		do_action( 'memberhero_user_url_updated', $this->ID, $url );
	}

	/**
	 * Create email change key.
	 */
	public function create_email_change_key( $email = null ) {
		$secret_key = memberhero_get_token();

		// If no email is provided, get the stored pending email.
		if ( ! $email ) {
			$email = $this->get_pending_email();
		}

		$token = array(
			'token'  => $secret_key,
			'expiry' => time() + apply_filters( 'memberhero_email_change_key_expiration', ( HOUR_IN_SECONDS * 12 ) ),
		);

		update_user_meta( $this->ID, '_memberhero_user_email', sanitize_email( $email ) );
		update_user_meta( $this->ID, '_memberhero_emailchange_token', $token );

		return $secret_key;
	}

	/**
	 * Process email change key.
	 */
	public function process_email_change_key( $key = null ) {
		global $wpdb;

		if ( memberhero_is_invalid_token( $key, $this->get( '_memberhero_emailchange_token' ) ) ) {
			$this->add_account_error( __( 'This email confirmation link is invalid or has expired.', 'memberhero' ) );
		}

		$this->update_email( $this->get_pending_email() );
		$this->cancel_pending_email();

		$this->log_in();

		wp_safe_redirect( add_query_arg( 'saved', 'email_changed', memberhero_get_page_permalink( 'account' ) ) );
		exit;
	}

	/**
	 * Add error to account page and redirect.
	 */
	public function add_account_error( $error ) {
		memberhero_add_notice( $error, 'error' );

		wp_safe_redirect( memberhero_get_page_permalink( 'account' ) );
		exit;
	}

	/**
	 * Update the user email.
	 */
	public function update_email( $email ) {
		global $wpdb;
		$email = sanitize_email( $email );

		$wpdb->update( $wpdb->users, array( 'user_email' => $email ), array( 'ID' => $this->ID ) );
	}

	/**
	 * Get user pending email address.
	 */
	public function get_pending_email() {
		return $this->get( '_memberhero_user_email' );
	}

	/**
	 * Cancel pending email request.
	 */
	public function cancel_pending_email() {
		delete_user_meta( $this->ID, '_memberhero_user_email' );
		delete_user_meta( $this->ID, '_memberhero_emailchange_token' );
	}

	/**
	 * Handle updating user password.
	 */
	public function maybe_update_user_pass( $user_pass, $metadata ) {
		global $the_form;

		if ( empty( $metadata[ 'new_password'] ) || apply_filters( 'memberhero_password_change_disabled', false, $this->ID ) ) {
			return;
		}

		wp_set_password( $metadata[ 'new_password' ], $this->ID );

		// Load the mailer class.
		memberhero()->mailer();
		do_action( 'memberhero_password_change_notification', $this->user_login );

		// Log-in again.
		$this->log_in();

		do_action( 'memberhero_password_updated', $this->user_id, $user_pass, $metadata[ 'new_password' ] );

		$the_form->saved = 'password_changed';
	}

	/**
	 * Check if user has a avatar.
	 */
	public function has_avatar() {
		if ( $this->get( '_memberhero_profile_avatar' ) != '' ) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the user avatar.
	 */
	public function get_avatar() {
		if ( $this->has_avatar() ) {
			return $this->get( '_memberhero_profile_avatar' );
		}
		return false;
	}

	/**
	 * Check if user has a cover photo.
	 */
	public function has_cover() {
		if ( $this->get( '_memberhero_profile_cover' ) != '' ) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the user cover photo.
	 */
	public function get_cover() {
		if ( $this->has_cover() ) {
			return $this->get( '_memberhero_profile_cover' );
		}
		return false;
	}

	/**
	 * Checks if user profile is private.
	 */
	public function is_private() {
		$private = $this->get( '_memberhero_private' );
		if ( $private === 'yes' ) {
			return true;
		}
		return false;
	}

	/**
	 * Returns class based on user block status.
	 */
	public function get_block_class( $user_id = 0 ) {
		$blocked = $this->get( '_memberhero_blocked_users' );
		if ( is_array( $blocked ) && array_key_exists( $user_id, $blocked ) ) {
			return 'memberhero_blocked';
		}
		return 'memberhero_unblocked';
	}

	/**
	 * Get a role ID by a name/slug.
	 */
	public function get_role( $role = null ) {
		return isset( $this->roles[0] ) ? $this->roles[0] : null;
	}

	/**
	 * Get a user role ID.
	 */
	public function get_role_id() {
		global $wpdb;

		$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type='memberhero_role'", $this->get_role() ) );

		return absint( $post_id );
	}

	/**
	 * Automatically log in the user.
	 */
	public function log_in() {
		if ( is_user_logged_in() ) {
			wp_logout();
		}

		wp_set_current_user( $this->ID, $this->user_login );
		wp_set_auth_cookie( $this->ID, true );

		$wp_user = new WP_User( $this->ID );

		do_action( 'wp_login', $this->user_login, $wp_user );
		do_action( 'memberhero_valid_login', $this->ID, $this );
	}

	/**
	 * Save the registration form. This can be used to store data which can be needed later as with sending welcome email.
	 */
	public function save_registration( $data = array(), $form_id = null, $role_id = null, $password_generated = null ) {
		$memberhero_form_data = array(
			'data' 				 => $data,
			'form_id' 			 => $form_id,
			'role_id' 			 => $role_id,
			'password_generated' => $password_generated,
		);

		update_user_meta( $this->ID, '_memberhero_form_data', $memberhero_form_data );
	}

	/**
	 * Get the saved form.
	 */
	public function get_form_data() {
		return $this->get( '_memberhero_form_data' );
	}

	/**
	 * Check the saved form.
	 */
	public function has_form_data() {
		$memberhero_form_data = $this->get( '_memberhero_form_data' );

		if ( ! empty( $memberhero_form_data ) && is_array( $memberhero_form_data ) ) {
			if ( array_key_exists( 'data', $memberhero_form_data ) && array_key_exists( 'form_id', $memberhero_form_data ) && array_key_exists( 'role_id', $memberhero_form_data ) && array_key_exists( 'password_generated', $memberhero_form_data ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Delete the saved form.
	 */
	public function delete_form_data() {
		delete_user_meta( $this->ID, '_memberhero_form_data' );
	}

	/**
	 * Get the saved form data.
	 */
	public function get_saved_data() {
		$memberhero_form_data = $this->get_form_data();

		return $memberhero_form_data[ 'data' ];
	}

	/**
	 * Get the saved form ID.
	 */
	public function get_saved_form() {
		$memberhero_form_data = $this->get_form_data();

		$the_form = memberhero_get_form( $memberhero_form_data[ 'form_id' ] );

		if ( empty( $the_form->id ) ) {
			return null;
		}

		if ( ! empty( $memberhero_form_data[ 'password_generated' ] ) ) {
			$the_form->password_generated = $memberhero_form_data[ 'password_generated' ];
		}

		return $the_form;
	}

	/**
	 * Get the saved form role.
	 */
	public function get_saved_role() {
		$memberhero_form_data = $this->get_form_data();

		$the_role = get_memberhero_role( $memberhero_form_data[ 'role_id' ] );

		if ( empty( $the_role->id ) ) {
			return null;
		}

		return $the_role;
	}

	/**
	 * Create a pending user in the database.
	 */
	public function create_pending_user( $send_email = true ) {

		// Check if user is already pending.
		if ( is_memberhero_pending_user( $this->ID ) ) {
			return;
		}

		update_user_meta( $this->ID, '_memberhero_pending', 1 );

		// Send a notification to user.
		if ( $send_email ) {

			memberhero()->mailer();
			do_action( 'memberhero_account_review_notification', $this );

		}

		do_action( 'memberhero_pending_user', $this->ID, $this );
	}

	/**
	 * Approve a pending user.
	 */
	public function approve_pending_user() {

		$this->approve();
		$this->send_welcome_email();

		do_action( 'memberhero_approved_user', $this->ID, $this );

		do_action( 'memberhero_user_activated', $this->ID );
	}

	/**
	 * Approves the user.
	 */
	public function approve() {

		delete_user_meta( $this->ID, '_memberhero_pending' );
		delete_user_meta( $this->ID, '_memberhero_rejected' );
	}

	/**
	 * Reject a pending user.
	 */
	public function reject_pending_user() {

		$this->reject();
		$this->send_goodbye_email();

		do_action( 'memberhero_rejected_user', $this->ID, $this );
	}

	/**
	 * Rejects the user.
	 */
	public function reject() {

		update_user_meta( $this->ID, '_memberhero_pending', 1 );
		update_user_meta( $this->ID, '_memberhero_rejected', 1 );

	}

	/**
	 * Reinstate user account after he's been rejected.
	 */
	public function reinstate() {

		delete_user_meta( $this->ID, '_memberhero_rejected' );

		memberhero()->mailer();
		do_action( 'memberhero_account_review_notification', $this );

		do_action( 'memberhero_reinstated_user', $this->ID, $this );

	}

	/**
	 * Check if new user needs admin approval.
	 */
	public function needs_manual_review() {
		global $the_form, $the_role;
		if ( ( $the_role->bypass_globals == 'yes' && $the_role->manual_approval == 'yes' ) || get_option( 'memberhero_manual_approval' ) == 'yes' ) {
			return true;
		}
		return false;
	}

	/**
	 * Check if user needs email confirmation.
	 */
	public function needs_email_confirmation() {
		global $the_form, $the_role;
		if ( ( $the_role->bypass_globals == 'yes' && $the_role->email_confirm == 'yes' ) || get_option( 'memberhero_email_confirm' ) == 'yes' ) {
			return true;
		}
		return false;
	}

	/**
	 * Check if new user should be auto approved.
	 */
	public function is_auto_approved() {
		global $the_form, $the_role;
		if ( $the_role->bypass_globals != 'yes' ) {
			if ( get_option( 'memberhero_manual_approval' ) != 'yes' && get_option( 'memberhero_email_confirm' ) != 'yes' ) {
				return true;
			}
		} else {
			if ( $the_role->manual_approval != 'yes' && $the_role->email_confirm != 'yes' ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Create a confirmation code for a user.
	 */
	public function create_confirmation_code( $force = false ) {

		$confirmation_code = mt_rand( 100000, 999999 );
		$user_hash = md5( $this->ID . $this->user_login ); // This will always be unique.
		$transient = '_memberhero_activation_code_' . $user_hash;

		// We need to check if the transient is already created.
		if ( get_transient( $transient ) === false || $force = true ) {
			set_transient( '_memberhero_activation_code_' . $user_hash, $confirmation_code, ( 10 * YEAR_IN_SECONDS ) );

			update_user_meta( $this->ID, '_memberhero_activation_code', $confirmation_code );
			update_user_meta( $this->ID, '_memberhero_unconfirmed_email', 1 );

			do_action( 'memberhero_unconfirmed_email', $this->ID, $this );

			// Send an email.
			memberhero()->mailer();
			do_action( 'memberhero_confirm_email_notification', $this, $confirmation_code );
		}

		// Create a cookie so that we know the user can enter the code when he want.
		$rp_cookie = 'wp-regconfirm-' . COOKIEHASH;
		$rp_path   = isset( $_SERVER['REQUEST_URI'] ) ? current( explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
		$value     = sprintf( '%d:%s', $this->ID, wp_unslash( $this->user_login ) );
		setcookie( $rp_cookie, $value, time() + ( 10 * YEAR_IN_SECONDS ), $rp_path, COOKIE_DOMAIN, is_ssl(), true );

		return $confirmation_code;
	}

	/**
	 * Process a confirmation code and possibly activate user account.
	 */
	public function process_confirmation_code( $confirmation_code ) {

		if ( ! isset( $this->ID ) ) {
			return false;
		}

		// Validate the confirmation code provided.
		if ( ! $this->validate_confirm_code( $confirmation_code ) ) {
			return false;
		}

		$this->send_welcome_email(); // Send welcome email.
		$this->log_in(); // Log in.

		do_action( 'memberhero_user_activated', $this->ID );

		$redirect = memberhero_get_login_redirect( $this );

		return $redirect;
	}

	/**
	 * Validates a confirmation code.
	 */
	public function validate_confirm_code( $confirmation_code = null ) {

		// Check the transient.
		$user_hash 		= md5( $this->ID . $this->user_login );
		$transient 		= '_memberhero_activation_code_' . $user_hash;
		$user_transient = get_transient( $transient );

		if ( $user_transient === false || $user_transient != $confirmation_code ) {
			return false;
		}

		// Get the stored activation code to match it.
		$stored = get_user_meta( $this->ID, '_memberhero_activation_code', true );

		if ( $stored != $confirmation_code ) {
			return false;
		}

		delete_user_meta( $this->ID, '_memberhero_unconfirmed_email' );
		delete_user_meta( $this->ID, '_memberhero_activation_code' );

		delete_transient( $transient );

		return true;
	}

	/**
	 * Trigger welcome email.
	 */
	public function send_welcome_email() {

		if ( $this->has_form_data() ) {

			memberhero()->mailer();
			do_action( 'memberhero_new_user_notification', $this, $this->get_saved_data(), $this->get_saved_form(), $this->get_saved_role() );

			$this->delete_form_data(); // Delete this data so we do not use it again.

		} else {

			memberhero()->mailer();
			do_action( 'memberhero_user_approved_notification', $this );

		}

	}

	/**
	 * Triggers goodbye email.
	 */
	public function send_goodbye_email() {

		memberhero()->mailer();
		do_action( 'memberhero_user_rejected_notification', $this );

	}

	/**
	 * Set the time when this user earned a new role.
	 */
	public function set_role_time( $role ) {
		// Store the timestamp for this specific role.
		$role  = memberhero_sanitize_title( $role );
		$roles = get_user_meta( $this->ID, '_memberhero_roles_times', true );

		if ( ! $roles ) {
			$roles = array();
		}

		$roles[ $role ] = time();

		update_user_meta( $this->ID, '_memberhero_roles_times', $roles );
	}

}