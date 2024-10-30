<?php
/**
 * Member List shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Meta_Box_List_Shortcode class.
 */
class MemberHero_Meta_Box_List_Shortcode {

	/**
	 * Output the metabox.
	 */
	public static function output( $post ) {
		global $thepostid, $the_list;

		$thepostid      = $post->ID;
		$the_list = $thepostid ? new MemberHero_List( $thepostid ) : new MemberHero_List();

		include 'views/html-list-shortcode.php';
	}

}