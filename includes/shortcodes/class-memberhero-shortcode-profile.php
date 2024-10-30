<?php
/**
 * Profile Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Shortcode_Profile class.
 */
class MemberHero_Shortcode_Profile {

	/**
	 * Get the shortcode content.
	 */
	public static function get( $atts ) {
		return MemberHero_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 */
	public static function output( $atts ) {
		
		if ( ! is_user_logged_in() && get_option( 'memberhero_disable_public_profiles' ) == 'yes' ) {

			ob_start();

			if ( memberhero_notice_count( 'error' ) == 0 ) {
				memberhero_add_notice( __( 'Please log in to view this profile.', 'memberhero' ), 'notice' );
			}

			// To force page refresh after login.
			memberhero_form_set_redirect();

			return MemberHero_Shortcode_Form::output( array( 'id' => memberhero_get_default_form_id( 'login' ) ) );

		} else {

			ob_start();

			self::profile( $atts );

			// Send output buffer.
			ob_end_flush();

		}
	}

	/**
	 * Profile page.
	 */
	private static function profile( $atts ) {
		global $the_user, $the_form;

		// Setup user and form ID.
		$user_id = isset( $atts[ 'user' ] ) ? memberhero_clean( $atts[ 'user' ] ) : memberhero_get_active_profile_id();

		// Get user ID from shortcode.
		if ( ! is_numeric( $user_id ) ) {
			if ( is_email( $user_id ) ) {
				$user_id = email_exists( $user_id );
			} else {
				$user_id = username_exists( $user_id );
			}
		}

		$the_user = memberhero_get_user( $user_id );

		$form_id  = memberhero_get_profile_endpoint_form();

		if ( ! $form_id ) {
			$form_id = absint( get_option( 'memberhero_profile_form_' . esc_attr( $the_user->get_role() ) ) );
			if ( ! $form_id ) {
				$form_id = memberhero_get_default_form_id( 'profile' );
			}
		}

		if ( memberhero_form_does_not_exist( $form_id ) ) {
			$form_id = memberhero_get_default_form_id( 'profile' );
		}

		$the_form = ( isset( $the_form->id ) ) ? $the_form : memberhero_get_form( $form_id );

		// Attributes.
		$atts = array_merge( array(

		), (array) $atts );

		memberhero_get_template(
			'profile/profile.php',
			array(
				'the_user' 			=> $the_user,
				'logged_user'		=> is_user_logged_in() ? memberhero_get_user( get_current_user_id() ) : '',
			)
		);

		do_action( 'memberhero_after_profile_template' );
	}

	/**
	 * Save.
	 */
	public static function save( $object = null ) {
		global $the_form;

		if ( $object ) {
			$the_form = $object;
		}

		if ( ! memberhero_user_can_edit_profile() ) {
			return;
		}

		$the_form->is_request = true;

		/**
		 * Form validation.
		 */
		$the_form->validate();

		/**
		 * Show success message.
		 */
		if ( ! memberhero_form_has_errors( $the_form->id ) ) {
			memberhero_update_profile( memberhero_get_active_profile_id(), memberhero_form_get_postdata( $the_form->id ) );

			$the_form->is_request = false;
			$the_form->profile_redirect();
		}

	}

}