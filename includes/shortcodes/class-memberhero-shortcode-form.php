<?php
/**
 * Form Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Shortcode_Form class.
 */
class MemberHero_Shortcode_Form {

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
		global $the_form;

		if ( isset( $atts[ 'id' ] ) && absint( $atts[ 'id' ] ) > 0 ) {
			$the_form = new MemberHero_Form( $atts[ 'id' ] );
		}

		// Add support for custom form output.
		if ( $the_form->type == 'password_reset' ) {
			return call_user_func( array( 'MemberHero_Shortcode_Form_Lostpassword', 'output' ), $atts );
		}

		// Default form types support.
		if ( ! array_key_exists( $the_form->type, memberhero_get_form_types() ) ) {
			return;
		}

		$classname = 'MemberHero_Shortcode_Form_' . ucfirst( $the_form->type );
		if ( class_exists( $classname ) ) {
			return call_user_func( array( $classname, 'output' ), $atts );
		}
	}

}