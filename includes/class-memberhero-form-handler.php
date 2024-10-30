<?php
/**
 * Form Handler.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Form_Handler class.
 */
class MemberHero_Form_Handler {

	public static $formdata = array();
	public static $errors   = array();

	/**
	 * Get error fields.
	 */
	public static function get_error_fields() {

	}

	/**
	 * Hook in methods.
	 */
	public static function init() {
		// Detect password reset key and redirect.
		add_action( 'template_redirect', array( __CLASS__, 'redirect_reset_password_link' ) );

		// Detect email change key and redirect.
		add_action( 'template_redirect', array( __CLASS__, 'redirect_email_change_link' ) );

		// Login & Registration.
		add_action( 'template_redirect', array( __CLASS__, 'login' ) );
		add_action( 'template_redirect', array( __CLASS__, 'register' ) );

		// Lost password.
		add_action( 'template_redirect', array( __CLASS__, 'lostpassword' ) );
		add_action( 'template_redirect', array( __CLASS__, 'password_reset' ) );

		// Edit profile.
		add_action( 'template_redirect', array( __CLASS__, 'profile' ) );

		// Account actions.
		add_action( 'template_redirect', array( __CLASS__, 'edit_account' ) );
		add_action( 'template_redirect', array( __CLASS__, 'edit_password' ) );
		add_action( 'template_redirect', array( __CLASS__, 'privacy' ) );

		// Triggered when user profile or account is shown.
		add_action( 'template_redirect', array( __CLASS__, 'profile_redirect' ), 50 );
		add_action( 'template_redirect', array( __CLASS__, 'account_redirect' ), 50 );

		// Trigger a login as another user.
		add_action( 'template_redirect', array( __CLASS__, 'login_as_user' ), 9 );

		// Delete account.
		add_action( 'template_redirect', array( __CLASS__, 'delete_account' ) );

		// WP admin screen redirects.
		add_action( 'init', array( __CLASS__, 'wp_admin_screen' ) );

		// Hooks for core validation.
		add_action( 'memberhero_account_validation', array( __CLASS__, 'maybe_validate_username' ), 10 );
		add_action( 'memberhero_account_validation', array( __CLASS__, 'maybe_validate_email' ), 20 );
		add_action( 'memberhero_edit_password_validation', array( __CLASS__, 'maybe_validate_password' ), 10 );
	}

	/**
	 * Remove key and user ID from query string, and redirect to account page to show the form.
	 */
	public static function redirect_reset_password_link() {
		if ( isset( $_GET['password_key'] ) && ( isset( $_GET['id'] ) ) ) {
			$user_id = absint( $_GET['id'] );
			$value = sprintf( '%d:%s', $user_id, wp_unslash( $_GET['password_key'] ) );

			MemberHero_Shortcode_Form_Lostpassword::set_reset_password_cookie( $value );

			wp_safe_redirect( add_query_arg( 'show-reset-form', 'true', memberhero_lostpassword_url() ) );
			exit;
		}
	}

	/**
	 * Validate email change tokens from url.
	 */
	public static function redirect_email_change_link() {
		global $wpdb;

		if ( isset( $_GET['email_key'] ) && ( isset( $_GET['id'] ) ) ) {
			$the_user = memberhero_get_user( absint( $_GET['id'] ) );
			if ( isset( $the_user->ID ) ) {
				$the_user->process_email_change_key( $_GET['email_key'] );
			}
		}

	}

	/**
	 * Get valid handler.
	 */
	public static function handle( $mode ) {
		if ( ! isset( $_REQUEST['memberhero-' . $mode . '-nonce'] ) 
			|| ! wp_verify_nonce( $_REQUEST['memberhero-' . $mode. '-nonce'], 'memberhero-' . $mode )
		) {
			return false;
		}
		if ( ! empty( $_REQUEST[ '_' . $mode . '_id' ] ) ) {
			return absint( $_REQUEST[ '_' . $mode . '_id' ] );
		}
		return false;
	}

	/**
	 * Login.
	 */
	public static function login() {
		if ( ! $id = self::handle( 'login' ) ) {
			return;
		}

		memberhero()::$form_id = $id;

		MemberHero_Shortcode_Form_Login::save( memberhero_get_form( $id ) );
	}

	/**
	 * Register.
	 */
	public static function register() {
		if ( ! $id = self::handle( 'register' ) ) {
			return;
		}
		MemberHero_Shortcode_Form_Register::save( memberhero_get_form( $id ) );
	}

	/**
	 * Lost Password.
	 */
	public static function lostpassword() {
		if ( ! $id = self::handle( 'lostpassword' ) ) {
			return;
		}
		MemberHero_Shortcode_Form_Lostpassword::save( memberhero_get_form( $id ) );
	}

	/**
	 * Password reset.
	 */
	public static function password_reset() {
		if ( ! $id = self::handle( 'password_reset' ) ) {
			return;
		}
		MemberHero_Shortcode_Form_Lostpassword::process_password_change( memberhero_get_form( $id ) );
	}

	/**
	 * Profile - Edit
	 */
	public static function profile() {
		if ( ! $id = self::handle( 'edit-profile' ) ) {
			return;
		}
		MemberHero_Shortcode_Profile::save( memberhero_get_form( $id ) );
	}

	/**
	 * Account - Edit
	 */
	public static function edit_account() {
		if ( ! $id = self::handle( 'edit-account' ) ) {
			return;
		}
		MemberHero_Shortcode_Form_Account::save( memberhero_get_form( $id ) );
	}

	/**
	 * Account - Edit password
	 */
	public static function edit_password() {
		if ( ! $id = self::handle( 'edit-password' ) ) {
			return;
		}
		MemberHero_Shortcode_Form_Account::save( memberhero_get_form( $id ) );
	}

	/**
	 * Account - Privacy
	 */
	public static function privacy() {
		if ( ! $id = self::handle( 'privacy' ) ) {
			return;
		}
		MemberHero_Shortcode_Form_Account::save( memberhero_get_form( $id ) );
	}

	/**
	 * Profile redirect.
	 */
	public static function profile_redirect() {
		global $current_user;

		if ( ! is_memberhero_profile_page() ) {
			return;
		}

		memberhero_handle_profile_view();
	}

	/**
	 * Check that user can edit their account.
	 */
	public static function account_redirect() {
		global $current_user;
		if ( ! is_memberhero_account_page() || ! is_user_logged_in() ) {
			return;
		}
		if ( ! current_user_can( 'memberhero_edit_account' ) ) {
			exit( wp_safe_redirect( add_query_arg( 'unauthorized', 'true', home_url() ) ) );
		}
		if( memberhero()->query->get_current_endpoint() == 'delete' && ! memberhero_can_delete_their_account() ) {
			exit( wp_safe_redirect( add_query_arg( 'unauthorized', 'true', home_url() ) ) );
		}
	}

	/**
	 * Log in as another user.
	 */
	public static function login_as_user() {
		// This feature is only users with edit users capability.
		if ( ! current_user_can( 'memberhero_edit_users' ) || empty( $_REQUEST[ 'memberhero_logon' ] ) || empty( $_REQUEST[ 'memberhero_id' ] ) ) {
			return;
		}

		// Compare the md5 hashes.
		if ( $_REQUEST[ 'memberhero_logon' ] == memberhero_md5( $_REQUEST[ 'memberhero_id' ] ) ) {
			wp_logout();

			wp_set_current_user( $_REQUEST[ 'memberhero_id' ] );
			wp_set_auth_cookie( $_REQUEST[ 'memberhero_id' ], true );

			wp_safe_redirect( memberhero_get_profile_url( $_REQUEST[ 'memberhero_id' ] ) );
			exit;
		}
	}

	/**
	 * Fired when the user attempt to delete their account.
	 */
	public static function delete_account() {
		if ( isset( $_POST['memberhero_delete_account_field'] ) && wp_verify_nonce( $_POST['memberhero_delete_account_field'], 'memberhero_delete_account' ) ) {
			if ( memberhero_can_delete_their_account() ) {
				memberhero_delete_user( get_current_user_id() );
				$url = esc_url( get_option( 'memberhero_post_delete_url' ) );
				$url = ! empty( $url ) ? $url : home_url();
				exit( wp_redirect( $url ) );
			}
		}
	}

	/**
	 * Fired when the user attempt to enter a WP admin screen.
	 */
	public static function wp_admin_screen() {
		global $pagenow;
		if ( $pagenow != 'wp-login.php' ) {
			return;
		}

		$action = ! empty( $_REQUEST[ 'action' ] ) ? memberhero_clean( $_REQUEST[ 'action' ] ) : '';

		switch( $action ) :

			case 'register' :
				$block_register = get_option( 'memberhero_block_wpregister' );
				if ( $block_register === 'yes' ) {
					$check = is_memberhero_page_has_form( memberhero_get_page_id( 'register' ), 'register' );
					if ( $check ) {
						exit( wp_safe_redirect( apply_filters( 'memberhero_default_frontend_register_redirect', memberhero_register_url() ) ) );
					}
				}
			break;

			case 'lostpassword' :
				$block_lostpassword = get_option( 'memberhero_block_wplostpassword' );
				if ( $block_lostpassword === 'yes' ) {
					$check = is_memberhero_page_has_form( memberhero_get_page_id( 'lostpassword' ), 'lostpassword' );
					if ( $check ) {
						exit( wp_safe_redirect( apply_filters( 'memberhero_default_frontend_lostpassword_redirect', memberhero_lostpassword_url() ) ) );
					}
				}
			break;

			// Fix logout through WordPress.
			case 'logout' :

			break;

			default :
				$block_login = get_option( 'memberhero_block_wplogin' );
				if ( $block_login === 'yes' ) {
					$check = is_memberhero_page_has_form( memberhero_get_page_id( 'login' ), 'login' );
					if ( $check ) {
						exit( wp_safe_redirect( apply_filters( 'memberhero_default_frontend_login_redirect', memberhero_login_url() ) ) );
					}
				}
			break;

		endswitch;
	}

	/**
	 * Maybe validate username change.
	 */
	public static function maybe_validate_username( $postdata ) {
		global $the_form, $the_user;

		if ( empty( $postdata[ 'user_login' ] ) ) {
			return;
		}

		if ( get_option( 'memberhero_allow_user_login_change' ) != 'yes' ) {
			return;
		}

		if ( sanitize_user( $postdata[ 'user_login' ] ) == $the_user->user_login ) {
			return;
		}

		$value = sanitize_user( $postdata[ 'user_login' ] );

		if ( validate_username( $value ) == false ) {
			memberhero_add_notice( __( 'Your username can have letters, numbers and underscore.', 'memberhero' ), 'error', 'user_login' );
		}

		if ( username_exists( $value ) ) {
			memberhero_add_notice( __( 'Username has already been taken.', 'memberhero' ), 'error', 'user_login' );
		}

		memberhero_form_add_postdata( $the_form->id, 'user_login', $value );
	}

	/**
	 * Maybe validate email change.
	 */
	public static function maybe_validate_email( $postdata ) {
		global $the_form, $the_user;

		if ( empty( $postdata[ 'user_email' ] ) ) {
			return;
		}

		// A hook to disable email change for specific users by ID.
		if ( apply_filters( 'memberhero_email_change_disabled', false, $the_user->ID ) ) {
			memberhero_add_notice( __( 'Email change for this account has been disabled.', 'memberhero' ), 'error', 'global' );
			return;
		}

		$email = sanitize_email( $postdata[ 'user_email' ] );

		if ( $email == $the_user->user_email ) {

			memberhero_form_remove_postdata( $the_form->id, 'user_email' );

		} else {

			memberhero_form_add_postdata( $the_form->id, 'user_email', $email );

		}
	}

	/**
	 * Maybe validate password change.
	 */
	public static function maybe_validate_password( $postdata ) {
		global $the_form, $the_user;

		// A hook to disable password change for specific users by ID.
		if ( apply_filters( 'memberhero_password_change_disabled', false, $the_user->ID ) ) {
			memberhero_add_notice( __( 'Password change for this account has been disabled.', 'memberhero' ), 'error', 'global' );
			return;
		}

		if ( empty( $postdata[ 'user_pass' ] ) ) {
			memberhero_add_notice( __( 'Please enter your current password.', 'memberhero' ), 'error', 'user_pass' );
		} elseif ( empty( $postdata[ 'new_password' ] ) ) {
			memberhero_add_notice( __( 'You must enter a new password to change it.', 'memberhero' ), 'error', 'global' );
		} elseif ( $postdata[ 'new_password' ] !== $postdata[ 'verify_new_password' ] ) {
			memberhero_add_notice( __( 'Your new password must be confirmed correctly.', 'memberhero' ), 'error', 'global' );
		}

		$check_password = wp_check_password( $postdata[ 'user_pass' ], $the_user->user_pass, $the_user->ID );

		if ( ! $check_password ) {
			memberhero_add_notice( __( 'Your password is incorrect.', 'memberhero' ), 'error', 'user_pass' );
		}
	}

}

MemberHero_Form_Handler::init();