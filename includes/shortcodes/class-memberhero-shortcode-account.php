<?php
/**
 * Account Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Shortcode_Account class.
 */
class MemberHero_Shortcode_Account {

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

		if ( ! is_user_logged_in() ) {

			if ( memberhero_notice_count( 'error' ) == 0 ) {
				memberhero_add_notice( __( 'Please log in to access your account.', 'memberhero' ), 'notice' );
			}

			// To force page refresh after login.
			memberhero_form_set_redirect();

			return MemberHero_Shortcode_Form::output( array( 'id' => memberhero_get_default_form_id( 'login' ) ) );

		} else {
			// Start output buffer since the html may need discarding for BW compatibility.
			ob_start();

			// Collect notices before output.
			$notices = memberhero_get_notices();

			// Output the new account page.
			self::account( $atts );

			// Send output buffer.
			ob_end_flush();
		}
	}

	/**
	 * Account page.
	 */
	private static function account( $atts ) {
		$atts = array_merge( array(

		), (array) $atts );

		memberhero_get_template(
			'account/account.php',
			array(
				'the_user' 	=> memberhero_get_user( get_current_user_id() ),
			)
		);

		do_action( 'memberhero_after_account_template' );

	}

}