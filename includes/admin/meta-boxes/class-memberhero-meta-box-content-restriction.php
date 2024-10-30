<?php
/**
 * Content restriction.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_Meta_Box_Content_Restriction class.
 */
class MemberHero_Meta_Box_Content_Restriction {

	/**
	 * Output the metabox.
	 */
	public static function output( $post ) {
		global $thepostid;

		$thepostid      = $post->ID;

		wp_nonce_field( 'memberhero_save_data', 'memberhero_meta_nonce' );

		$access 		= get_post_meta( $thepostid, '_memberhero_access', true );
		$roles  		= get_post_meta( $thepostid, '_memberhero_roles', true );
		$redirect  		= get_post_meta( $thepostid, '_memberhero_redirect', true );
		$custom_url  	= get_post_meta( $thepostid, '_memberhero_redirect_url', true );

		// Default access
		if ( ! $access ) {
			$access = 'everyone';
		}

		if ( ! $roles ) {
			$roles = array();
		}

		$options = array(
			'home'			=> __( 'Home page', 'memberhero' ),
			'login'			=> __( 'Login page', 'memberhero' ),
			'register'		=> __( 'Registration page', 'memberhero' ),
			'profile'		=> __( 'Profile', 'memberhero' ),
			'account'		=> __( 'Account', 'memberhero' ),
			'custom'		=> __( 'Custom URL', 'memberhero' ),
		);

		include 'views/html-content-restrict.php';
	}

	/**
	 * Save meta box data.
	 */
	public static function save( $post_id, $post ) {

		$access 	= isset( $_POST[ '_memberhero_access' ] ) ? memberhero_clean( $_POST[ '_memberhero_access' ] ) : '';
		$roles 		= isset( $_POST[ '_memberhero_roles' ] ) ? memberhero_clean( $_POST[ '_memberhero_roles' ] ) : array();
		$redirect 	= isset( $_POST[ '_memberhero_redirect' ] ) ? memberhero_clean( $_POST[ '_memberhero_redirect' ] ) : '';
		$custom_url = isset( $_POST[ '_memberhero_redirect_url' ] ) ? memberhero_clean( $_POST[ '_memberhero_redirect_url' ] ) : '';

		update_post_meta( $post_id, '_memberhero_access', $access );
		update_post_meta( $post_id, '_memberhero_roles', $roles );
		update_post_meta( $post_id, '_memberhero_redirect', $redirect );
		update_post_meta( $post_id, '_memberhero_redirect_url', $custom_url );
	}

}