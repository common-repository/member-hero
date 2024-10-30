<?php
/**
 * Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Shortcodes class.
 */
class MemberHero_Shortcodes {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'form'			=> __CLASS__ . '::form',
			'lostpassword'	=> __CLASS__ . '::lostpassword',
			'login'			=> __CLASS__ . '::login',
			'register'		=> __CLASS__ . '::register',
			'list'			=> __CLASS__ . '::memberlist',
			'account'		=> __CLASS__ . '::account',
			'profile'		=> __CLASS__ . '::profile',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( 'memberhero_' . $shortcode, $function );
		}
	}

	/**
	 * Shortcode Wrapper.
	 */
	public static function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class'  => 'memberhero',
			'before' => null,
			'after'  => null,
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

	/**
	 * Output form.
	 */
	public static function form( $atts ) {
		if ( is_admin() ) {
			return;
		}
		return self::shortcode_wrapper( array( 'MemberHero_Shortcode_Form', 'output' ), $atts );
	}

	/**
	 * Output lost password.
	 */
	public static function lostpassword( $atts ) {
		if ( is_admin() ) {
			return;
		}
		return self::shortcode_wrapper( array( 'MemberHero_Shortcode_Form_Lostpassword', 'output' ), $atts );
	}

	/**
	 * Output login
	 */
	public static function login( $atts ) {
		if ( is_admin() ) {
			return;
		}
		return self::shortcode_wrapper( array( 'MemberHero_Shortcode_Form_Login', 'output' ), $atts );
	}

	/**
	 * Output register
	 */
	public static function register( $atts ) {
		if ( is_admin() ) {
			return;
		}
		return self::shortcode_wrapper( array( 'MemberHero_Shortcode_Form_Register', 'output' ), $atts );
	}

	/**
	 * Member list page shortcode.
	 */
	public static function memberlist( $atts ) {
		if ( is_admin() ) {
			return;
		}

		return self::shortcode_wrapper( array( 'MemberHero_Shortcode_List', 'output' ), $atts );
	}

	/**
	 * Account page shortcode.
	 */
	public static function account( $atts ) {
		if ( is_admin() ) {
			return;
		}
		return self::shortcode_wrapper( array( 'MemberHero_Shortcode_Account', 'output' ), $atts );
	}

	/**
	 * Profile page shortcode.
	 */
	public static function profile( $atts ) {
		if ( is_admin() ) {
			return;
		}
		return self::shortcode_wrapper( array( 'MemberHero_Shortcode_Profile', 'output' ), $atts );
	}

}