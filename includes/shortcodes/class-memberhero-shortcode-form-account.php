<?php
/**
 * Edit account
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Shortcode_Form_Account class.
 */
class MemberHero_Shortcode_Form_Account {

	/**
	 * Save.
	 */
	public static function save( $object = null ) {
		global $the_form, $the_user;

		if ( $object ) {
			$the_form = $object;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		$the_user = memberhero_get_user( get_current_user_id() );

		$the_form->is_request = true;

		/**
		 * Form validation.
		 */
		$the_form->validate();

		/**
		 * Show success message.
		 */
		if ( ! memberhero_form_has_errors( $the_form->id ) ) {

			memberhero_update_account( $the_user->ID, memberhero_form_get_postdata( $the_form->id ) );

			$the_form->is_request = false;

			$the_form->account_redirect();

		}

	}

}