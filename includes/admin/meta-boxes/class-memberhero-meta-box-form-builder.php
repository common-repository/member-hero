<?php
/**
 * Form builder.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Meta_Box_Form_Builder class.
 */
class MemberHero_Meta_Box_Form_Builder {

	/**
	 * Output the metabox.
	 */
	public static function output( $post ) {
		global $thepostid, $the_form;

		$thepostid      = $post->ID;
		$the_form 		= $thepostid ? new MemberHero_Form( $thepostid ) : new MemberHero_Form();

		include 'views/html-form-builder.php';

		// modals
		include 'views/modals/html-add-element.php';
		include 'views/modals/html-add-field.php';
		include 'views/modals/html-edit-row.php';
	}

}