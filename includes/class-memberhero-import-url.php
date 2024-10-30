<?php
/**
 * Import from URL.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_URL_Import class.
 */
class MemberHero_URL_Import {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_filter( 'memberhero_get_cover_dropdown_items',	 		array( __CLASS__, 'cover_dropdown' ), 50, 2 );
		add_filter( 'memberhero_get_avatar_dropdown_items', 		array( __CLASS__, 'avatar_dropdown' ), 50, 2 );
		add_action( 'memberhero_include_profile_modals', 	array( __CLASS__, 'add_modals' ) );

		add_filter( 'memberhero_ajax_nonces', 						array( __CLASS__, 'add_nonces' ) );

		// memberhero_EVENT => nopriv.
		$ajax_events = array(
			'import_cover'			=> false,
			'import_avatar'			=> false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_memberhero_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_memberhero_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Added to cover upload dropdown.
	 */
	public static function cover_dropdown( $items, $the_user ) {

		if ( 'no' === get_option( 'memberhero_uploads_via_url' ) ) {
			return $items;
		}

		$item = array(
			'class' => '',
			'html' 	=> '<a href="#" data-user_id="' . $the_user->user_id . '" class="memberhero-modal-open" rel="memberhero-modal-import-cover">' . __( 'Import from URL', 'memberhero' ) . '</a>',
		);

		$items = memberhero_array_insert_after( 'upload', $items, 'import_url', $item );

		return $items;
	}

	/**
	 * Added to avatar upload dropdown.
	 */
	public static function avatar_dropdown( $items, $the_user ) {

		if ( 'no' === get_option( 'memberhero_uploads_via_url' ) ) {
			return $items;
		}

		$item = array(
			'class' => '',
			'html' 	=> '<a href="#" data-user_id="' . $the_user->user_id . '" class="memberhero-modal-open" rel="memberhero-modal-import-avatar">' . __( 'Import from URL', 'memberhero' ) . '</a>',
		);

		$items = memberhero_array_insert_after( 'upload', $items, 'import_url', $item );

		return $items;
	}

	/**
	 * Added modals.
	 */
	public static function add_modals() {

		if ( 'no' === get_option( 'memberhero_uploads_via_url' ) ) {
			return;
		}

		memberhero_get_template( 'modals/import-cover.php' );
		memberhero_get_template( 'modals/import-avatar.php' );
	}

	/**
	 * Add nonces.
	 */
	public static function add_nonces( $nonces ) {

		if ( 'no' === get_option( 'memberhero_uploads_via_url' ) ) {
			return $nonces;
		}

		$nonces[ 'import_cover' ]	= wp_create_nonce( 'memberhero-import-cover' );
		$nonces[ 'import_avatar' ] 	= wp_create_nonce( 'memberhero-import-avatar' );

		return $nonces;
	}

	/**
	 * Import cover from URL.
	 */
	public static function import_cover() {
		global $the_upload;

		check_ajax_referer( 'memberhero-import-cover', 'security' );

		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : '';
		$url     = isset( $_POST[ 'import_cover_url' ] ) ? $_POST[ 'import_cover_url' ] : '';

		if ( 'no' === get_option( 'memberhero_uploads_via_url' ) ) {
			wp_die( -1 );
		}

		if ( ! memberhero_user_can_edit_profile( $user_id ) ) {
			wp_die( -1 );
		}

		// Test if the URL is valid.
		if ( filter_var( $url, FILTER_VALIDATE_URL ) === FALSE || esc_url_raw( $url ) !== $url ) {
			wp_send_json( array( 'keep_modal' => true, '_error' => __( 'Please enter a valid URL', 'memberhero' ) ) );
		}

		$the_upload 	= new MemberHero_Uploader();
		$file_return 	= $the_upload->import_from_url( $user_id, $url, 'cover' );

		// Catch errors.
		if ( ! empty( $file_return[ 'error' ] ) ) {
			wp_send_json( array( 'keep_modal' => true, '_error' => $file_return[ 'error' ] ) );
		}

		$html = '<div class="memberhero-profile-coverbg" style="background-image: url( ' . memberhero_get_user_cover_url( $user_id ) . ' );"></div>';

		$response = array(
			'message'		=> __( 'Your header photo was published successfully.', 'memberhero' ),
			'id'			=> $user_id,
			'file_return'	=> $file_return,
			'js_update'				=> array(
				'parent'			=> '.memberhero-profile-cover',
				'child'				=> '.memberhero-profile-coverbg',
				'html'				=> $html,
			),
			'toggle'		=> array(
				'.memberhero_no_cover'		=> 'memberhero_has_cover',
			),
		);

		wp_send_json( $response );
	}

	/**
	 * Import avatar from URL.
	 */
	public static function import_avatar() {
		global $the_upload;

		check_ajax_referer( 'memberhero-import-avatar', 'security' );

		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : '';
		$url     = isset( $_POST[ 'import_avatar_url' ] ) ? $_POST[ 'import_avatar_url' ] : '';

		if ( 'no' === get_option( 'memberhero_uploads_via_url' ) ) {
			wp_die( -1 );
		}

		if ( ! memberhero_user_can_edit_profile( $user_id ) ) {
			wp_die( -1 );
		}

		// Test if the URL is valid.
		if ( filter_var( $url, FILTER_VALIDATE_URL ) === FALSE || esc_url_raw( $url ) !== $url ) {
			wp_send_json( array( 'keep_modal' => true, '_error' => __( 'Please enter a valid URL', 'memberhero' ) ) );
		}

		$the_upload 	= new MemberHero_Uploader();
		$file_return 	= $the_upload->import_from_url( $user_id, $url, 'avatar' );

		// Catch errors.
		if ( ! empty( $file_return[ 'error' ] ) ) {
			wp_send_json( array( 'keep_modal' => true, '_error' => $file_return[ 'error' ] ) );
		}

		// Return correct avatar markup when the user avatar is updated.
		$user = get_userdata( $user_id );
		if ( memberhero_user_has_no_gravatar( $user_id, $user->user_email ) ) {
			$html = '<div class="memberhero-profile-photo-add-wrap">
						<a href="' . esc_url( memberhero_get_profile_endpoint_url( 'edit', $user->user_login ) ) . '" class="memberhero-profile-photo-add memberhero-dropdown-init" rel="memberhero-dropdown-photo">' . memberhero_svg_icon( 'camera' ) . '</a>
					</div>';
		} else {
			$html = '<a href="' . memberhero_get_profile_url( $user->user_login ) . '" class="memberhero-profile-photo-link">' . get_avatar( $user_id, 200 ) . '</a>';
		}

		$response = array(
			'message'		=> __( 'Your profile photo was published successfully.', 'memberhero' ),
			'id'			=> $user_id,
			'file_return'	=> $file_return,
			'js_update'				=> array(
				'parent'			=> '.memberhero-profile-photo[data-user_id=' . $user_id . ']',
				'child'				=> '.memberhero-profile-photo-add-wrap, .memberhero-profile-photo-link',
				'html'				=> $html,
			),
			'toggle'		=> array(
				'.memberhero_no_avatar'	=> 'memberhero_has_avatar',
			),
		);

		wp_send_json( $response );
	}

}

MemberHero_URL_Import::init();