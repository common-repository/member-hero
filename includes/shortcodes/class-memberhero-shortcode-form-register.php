<?php
/**
 * Register
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Shortcode_Form_Register class.
 */
class MemberHero_Shortcode_Form_Register {

	/**
	 * Output the shortcode.
	 */
	public static function output( $atts ) {
		global $the_form;

		if ( ! isset( $atts[ 'id' ] ) ) {
			$default  = get_option( 'memberhero_register_form' );
			$the_form = memberhero_get_form( $default );
		}

		$atts = array_merge( array(
			'top_note'				=> '',
			'first_button'			=> __( 'Let&#39;s get started!', 'memberhero' ),
			'second_button'			=> __( 'Got an account?', 'memberhero' ),
		), (array) $atts );

		// Email unconfirmed.
		if ( ! empty( $_GET['checkmail'] ) ) {
			if ( isset( $_COOKIE[ 'wp-regconfirm-' . COOKIEHASH ] ) && 0 < strpos( $_COOKIE[ 'wp-regconfirm-' . COOKIEHASH ], ':' ) ) {
				list( $id, $login ) = array_map( 'memberhero_clean', explode( ':', wp_unslash( $_COOKIE[ 'wp-regconfirm-' . COOKIEHASH ] ), 2 ) );
				$user_data = get_userdata( absint( $id ) );

				// Display account confirmation code template.
				if ( $user_data != false ) {
					ob_start();
					memberhero_get_template( 'global/new-account-confirmation.php', array( 'user' => $user_data ) );
					return ob_end_flush();
				}
			}
		}

		// Pending review.
		if ( ! empty( $_GET[ 'pending' ] ) ) {
			ob_start();
			memberhero_get_template( 'global/new-account-pending.php' );
			return ob_end_flush();
		}

		// Registration is disabled.
		if ( ! get_option( 'users_can_register' ) ) {
			ob_start();
			memberhero_add_notice( __( 'Registration is currently disabled for this site.', 'memberhero' ), 'notice' );
			memberhero_print_notices();
			return ob_end_flush();
		}

		memberhero_get_template( 'form/form.php', array( 'atts' => $atts ) );

		memberhero_reset_form_data();
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

			$user = memberhero_create_user( memberhero_form_get_postdata( $the_form->id ) );

			if ( is_wp_error( $user ) ) {

				memberhero_add_notice( __( 'We could not create your account. Please try again later.', 'memberhero' ), 'error', 'global' );

			} else {

				// This is triggered after user creation.
				self::after_registration( $user );

			}
		}

	}

	/**
	 * This controls what action is taken after registering.
	 */
	public static function after_registration( $user ) {
		global $the_form, $the_role;

		$role_id = $user->get_role_id();

		if ( ! $role_id ) {
			$the_role = null;
		}

		$the_role = new MemberHero_Role( $role_id );

		/**
		 * Allow 3rd party plugins to hook custom process after registration.
		 */
		do_action( 'memberhero_after_registration', $user );

		// Add form ID to user meta.
		update_user_meta( $user->ID, '_memberhero_form_id', $the_form->id );

		// Needs email confirmation.
		if ( $user->needs_email_confirmation() ) {
			$confirmation_code = $user->create_confirmation_code();

			if ( $confirmation_code ) {
				$user->save_registration( memberhero_form_get_postdata( $the_form->id ), $the_form->id, $role_id, $the_form->password_generated );
			}

			$the_form->get_redirect( add_query_arg( array( 'checkmail' => 'true' ), $_SERVER[ 'HTTP_REFERER' ] ) );
		}

		// Needs manual approval.
		if ( $user->needs_manual_review() ) {
			$user->create_pending_user();

			$user->save_registration( memberhero_form_get_postdata( $the_form->id ), $the_form->id, $role_id, $the_form->password_generated );

			// Send admin a notice
			memberhero()->mailer();
			do_action( 'memberhero_pending_user_notification', $user, memberhero_form_get_postdata( $the_form->id ), $the_form, $the_role );

			$the_form->get_redirect( add_query_arg( array( 'pending' => 'true' ), $_SERVER[ 'HTTP_REFERER' ] ) );
		}

		// No action needed, login user and send a confirmation mail.
		if ( $user->is_auto_approved() ) {

			// Send an email.
			memberhero()->mailer();
			do_action( 'memberhero_new_user_notification', $user, memberhero_form_get_postdata( $the_form->id ), $the_form, $the_role );

			// Login.
			$user->log_in();

			// This only should happen when user is 100% activated.
			do_action( 'memberhero_user_activated', $user->ID );

			$the_form->get_redirect( memberhero_get_register_redirect( $user ) );
		}

	}

}