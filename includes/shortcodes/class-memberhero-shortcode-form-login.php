<?php
/**
 * Login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Shortcode_Form_Login class.
 */
class MemberHero_Shortcode_Form_Login {

	/**
	 * Output the shortcode.
	 */
	public static function output( $atts ) {
		global $the_form;

		if ( ! isset( $atts[ 'id' ] ) ) {
			$default  = get_option( 'memberhero_login_form' );
			$the_form = memberhero_get_form( $default );
		}

		// If user logged in a form that has refresh as redirection.
		if ( ! empty( $the_form->redirect ) && $the_form->redirect == 'refresh' ) {
			if ( is_user_logged_in() ) {
				return;
			}
		}

		$atts = array_merge( array(
			'top_note'				=> '',
			'first_button'			=> __( 'Log In', 'memberhero' ),
			'second_button'			=> __( 'Create Account?', 'memberhero' ),
		), (array) $atts );

		if ( ! empty( $_GET['password-changed'] ) ) {
			if ( 0 == memberhero_notice_count( 'error' ) ) {
				memberhero_add_notice( __( 'Your password has been changed.', 'memberhero' ), 'success' );
			}
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
			$user = memberhero_check_user_login( memberhero_form_get_postdata( $the_form->id ) );

			if ( is_wp_error( $user ) ) {

				memberhero_add_notice( $user->get_error_message(), 'error', 'global' );

			} else {

				$login_filter = apply_filters( 'memberhero_two_step_login_filter', false, $user );

				if ( ! $login_filter ) {
					$user->log_in();
					$the_form->get_redirect( memberhero_get_login_redirect( $user ) );
				} else {
					do_action( 'memberhero_two_step_login', $user );
				}
			}
		}

	}

}