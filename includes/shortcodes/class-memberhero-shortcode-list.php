<?php
/**
 * Member list Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Shortcode_List class.
 */
class MemberHero_Shortcode_List {

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
		ob_start();

		self::memberlist( $atts );

		// Send output buffer.
		ob_end_flush();
	}

	/**
	 * Shortcode page.
	 */
	private static function memberlist( $atts ) {
		global $the_list, $logged_user, $the_user;

		$atts = array_merge( array(

		), ( array ) $atts );

		if ( empty( $atts[ 'id' ] ) ) {
			$atts[ 'id' ] = absint( get_option( 'memberhero_default_list' ) );
		}

		$the_list = memberhero_get_list( $atts[ 'id' ] );

		// Not eligible to view this?
		if ( memberhero_user_cant_see_list() ) {
			if ( memberhero_notice_count( 'error' ) == 0 ) {
				memberhero_add_notice( __( 'Please log in to view this page.', 'memberhero' ), 'notice' );
			}

			// To force page refresh after login.
			memberhero_form_set_redirect();

			return MemberHero_Shortcode_Form::output( array( 'id' => memberhero_get_default_form_id( 'login' ) ) );
		}

		$logged_user 	= memberhero_get_user( get_current_user_id() );
		$the_user    	= $logged_user;
		$list  			= memberhero_get_members();

		// Load template.
		memberhero_get_template(
			'list/list.php',
			array(
				'atts' 				=> $atts,
				'the_list' 			=> $the_list,
				'logged_user'		=> $logged_user,
				'list'				=> $list,
			)
		);

		do_action( 'memberhero_after_list_template' );

		// Important. Let the plugin know we finished looping.
		$the_list->_in_loop = false;
	}

}