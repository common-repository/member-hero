<?php
/**
 * Form shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Meta_Box_Form_Shortcode class.
 */
class MemberHero_Meta_Box_Form_Shortcode {

	/**
	 * Output the metabox.
	 */
	public static function output( $post ) {
		global $thepostid, $the_form;

		$thepostid      = $post->ID;
		$the_form 		= $thepostid ? new MemberHero_Form( $thepostid ) : new MemberHero_Form();

		include 'views/html-form-shortcode.php';
	}

}