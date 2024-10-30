<?php
/**
 * Lost Password
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Shortcode_Form_Lostpassword class.
 */
class MemberHero_Shortcode_Form_Lostpassword {

	/**
	 * Output the shortcode.
	 */
	public static function output( $atts ) {
		global $the_form;

		// Fall back to defaults.
		if ( empty( $the_form->id ) ) {
			$default  = get_option( 'memberhero_lostpassword_form' );
			$the_form = memberhero_get_form( $default );
			if ( empty( $the_form->id ) ) {
				return;
			}
		}

		$atts = array_merge( array(
			'top_note'				=> __( 'Please write your email in the box below and we’ll send you a link to the password reset page.', 'memberhero' ),
			'first_button'			=> __( 'Retrieve Password', 'memberhero' ),
			'second_button'			=> __( 'Wait, I remember!', 'memberhero' ),
		), (array) $atts );

		if ( ! empty( $_GET['show-reset-form'] ) ) {
			if ( isset( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ) && 0 < strpos( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ], ':' ) ) {
				list( $rp_id, $rp_key ) = array_map( 'memberhero_clean', explode( ':', wp_unslash( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ), 2 ) );
				$userdata               = get_userdata( absint( $rp_id ) );
				$rp_login               = $userdata ? $userdata->user_login : '';
				$user                   = self::check_password_reset_key( $rp_key, $rp_login );

				// All good. Show the password reset form.
				if ( is_object( $user ) ) {
					$the_form->add_custom( 'password_reset', array( 'rp_login' => $rp_login, 'rp_key' => $rp_key ) );

					$atts = array(
						'top_note' 		=> '',
						'first_button' 	=> __( 'Change your password', 'memberhero' ),
						'second_button' => '',
					);
				}
			}
		}

		memberhero_get_template( 'form/form.php', array( 'atts' => $atts ) );
	}

	/**
	 * Save.
	 */
	public static function save( $object = null ) {
		global $the_form;

		if ( $object ) {
			$the_form = $object;
		}

		$the_form->is_request = true;

		/**
		 * Form validation.
		 */
		$the_form->validate();

		/**
		 * No errors so far.
		 */
		if ( ! memberhero_form_has_errors( $the_form->id ) ) {
			$the_form->is_request = false;

			$reset_password = self::generate_password_reset_email( memberhero_form_get_postdata( $the_form->id ) );

			if ( $reset_password ) {
				memberhero_add_notice( __( 'Instructions to reset your password will be sent to you shortly. Please check your email.', 'memberhero' ), 'success' );
				$the_form->cleardata = true;
			}
		}

	}

	/**
	 * Generate a secret key and send password reset email.
	 */
	public static function generate_password_reset_email( $args = array() ) {
		global $the_form;

		extract( $args );

		// No user was found.
		if ( empty( $user_email ) || ! is_email( $user_email ) || ! $user_id = email_exists( $user_email ) ) {
			do_action( 'memberhero_password_reset_failed', $args );
		}

		// Get user data.
		$user = memberhero_get_user( $user_id );

		if ( ! $user->ID ) {
			return true;
		}

		// Password reset not allowed for this role.
		if ( 'yes' === $the_form->force_role && $the_form->role ) {
			if ( $the_form->role !== $user->get_role() ) {
				return true;
			}
		}

		$allow = apply_filters( 'allow_password_reset', true, $user->ID );
		if ( ! $allow || is_wp_error( $allow ) ) {
			return true;
		}

		$reset_key = self::create_password_reset_key( $user );

		// Load the mailer class.
		memberhero()->mailer();
		do_action( 'memberhero_reset_password_notification', $user->user_login, $reset_key );

		do_action( 'memberhero_password_reset', $user );

		return true;
	}

	/**
	 * Set or unset the cookie.
	 */
	public static function set_reset_password_cookie( $value = '' ) {
		$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
		$rp_path   = isset( $_SERVER['REQUEST_URI'] ) ? current( explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';

		if ( $value ) {
			setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
		} else {
			setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
		}
	}

	/**
	 * Check the validity of given password reset key.
	 */
	public static function check_password_reset_key( $key, $login ) {

		$user   = get_user_by( 'login', $login );
		$stored = get_user_meta( $user->ID, '_memberhero_resetpass_token', true );

		if ( empty( $stored[ 'token' ] ) || empty( $stored[ 'expiry' ] ) || $stored[ 'token' ] !== $key || ( $stored[ 'expiry' ] - time() <= 0 ) ) {
			memberhero_add_notice( __( 'This key is invalid or may have expired. Please reset your password again if needed.', 'memberhero' ), 'error' );
			return false;
		}

		return $user;
	}

	/**
	 * Process password change request.
	 */
	public static function process_password_change( $object ) {
		global $the_form;

		if ( $object ) {
			$the_form = $object;
			$the_form->add_custom( 'password_reset' );
		}

		/**
		 * Form validation.
		 */
		$the_form->validate();

		// Get the post data we need.
		$postdata = array( 'password_1', 'password_2', 'rp_key', 'rp_login' );
		foreach( $postdata as $key ) {
			if ( ! isset( $_REQUEST[ $key ] ) ) {
				return;
			}
			$args[ $key ] = $_REQUEST[ $key ];
		}

		$user = self::check_password_reset_key( $args[ 'rp_key' ], $args[ 'rp_login' ] );

		if ( $user instanceof WP_User ) {
			if ( empty( $args[ 'password_1' ] ) ) {
				memberhero_add_notice( __( 'Please enter a new password.', 'memberhero' ), 'error', 'global' );
			} elseif ( $args[ 'password_1' ] !== $args[ 'password_2' ] ) {
				memberhero_add_notice( __( 'Passwords do not match.', 'memberhero' ), 'error', 'global' );
			}

			if ( 0 == memberhero_notice_count( 'error' ) ) {
				self::password_reset( $user, $args[ 'password_1' ] );
			}
		}

	}

	/**
	 * Set a new password for user.
	 */
	public static function password_reset( $user, $new_password ) {

		wp_set_password( $new_password, $user->ID );

		// Load the mailer class.
		memberhero()->mailer();
		do_action( 'memberhero_password_change_notification', $user->user_login );

		// Clear current password key.
		self::clear_password_reset_key( $user );

		wp_safe_redirect( add_query_arg( array( 'password-changed' => 'true' ), memberhero_get_page_permalink( 'login' ) ) );
		exit;
	}

	/**
	 * Create the password reset token for a user.
	 */
	public static function create_password_reset_key( $user ) {
		$secret_key = memberhero_get_token();

		$token = array(
			'token'  => $secret_key,
			'expiry' => time() + apply_filters( 'memberhero_password_reset_key_expiration', HOUR_IN_SECONDS ),
		);

		update_user_meta( $user->ID, '_memberhero_resetpass_token', $token );

		return $secret_key;
	}

	/**
	 * Clear the password reset token for a user.
	 */
	public function clear_password_reset_key( $user ) {

		delete_user_meta( $user->ID, '_memberhero_resetpass_token' );

	}

}