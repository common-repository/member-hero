<?php
/**
 * AJAX Events.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MemberHero_AJAX class.
 */
class MemberHero_AJAX {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		// memberhero_EVENT => nopriv.
		$ajax_events = array(
			'check_confirmation_code'	=> true,
			'send_form'					=> true,
			'cancel_pending_email'		=> false,
			'resend_confirmation'		=> false,
			'save_avatar'				=> false,
			'save_cover'				=> false,
			'remove_avatar'				=> false,
			'remove_cover'				=> false,
			'unblock_user'				=> false,
			'block_user'				=> false,
			'delete_user'				=> false,
			'save_form'					=> false,
			'add_field'					=> false,
			'create_forms'				=> false,
			'create_fields'				=> false,
			'create_roles'				=> false,
			'create_lists'				=> false,
			'duplicate_form'			=> false,
			'duplicate_field'			=> false,
			'duplicate_role'			=> false,
			'duplicate_list'			=> false,
			'confirm_email'				=> false,
			'set_pending'				=> false,
			'approve_user'				=> false,
			'reject_user'				=> false,
			'reinstate_user'			=> false,
			'update_api_key'			=> false,
			'json_search_users'			=> false,
			'json_search_terms'			=> false,
			'access_signup'				=> false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_memberhero_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_memberhero_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Could not perform an ajax action.
	 */
	public static function could_not_perform_action() {
		$array = array(
			'message'	=> apply_filters( 'memberhero_could_not_perform_action', __( 'Could not perform this action.', 'memberhero' ) ),
		);

		return $array;
	}

	/**
	 * Checks a confirmation code and completes user registration.
	 */
	public static function check_confirmation_code() {
		check_ajax_referer( 'memberhero-confirmation-code', 'security' );

		$user_id 		   = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : '';
		$confirmation_code = isset( $_POST[ 'confirmation_code' ] ) ? absint( $_POST[ 'confirmation_code' ] ) : '';

		$user = memberhero_get_user( $user_id );

		$result = $user->process_confirmation_code( $confirmation_code );

		if ( ! $result || is_wp_error( $result ) ) {
			wp_send_json( array( 'error' => 'true' ) );
		} else {
			wp_send_json( array( 'redirect' => $result ) );
		}

	}

	/**
	 * Send a form.
	 */
	public static function send_form() {
		global $the_form;

		/**
		 * This will take care of security checks.
		 */
		if ( isset( $_REQUEST['_memberhero_hook'] ) && ! empty( $_REQUEST['_memberhero_hook'] ) ) {
			if ( function_exists( $_REQUEST['_memberhero_hook'] ) ) {
				call_user_func( $_REQUEST['_memberhero_hook'] );
			}
		} else {

			// Call built-in function.
			call_user_func(
				array(
					'MemberHero_Form_Handler',
					memberhero_sanitize_title( wp_unslash( $_REQUEST[ '_endpoint' ] ) )
				)
			);
		}

		$response = array(
			'html' 			=> memberhero_print_notices( true ),
			'error_fields' 	=> memberhero_form_get_error_fields( $the_form->id ),
			'js_redirect'	=> $the_form->js_redirect,
			'js_replace'	=> $the_form->js_replace,
		);

		// Maybe clear the data from form after completion.
		if ( isset( $the_form->cleardata ) || ! empty( $the_form->js_replace ) ) {
			$response[ 'cleardata' ] = 'true';
		}

		wp_send_json( $response );
	}

	/**
	 * Cancel pending email change.
	 */
	public static function cancel_pending_email() {

		check_ajax_referer( 'memberhero-cancel-email', 'security' );

		if ( ! is_user_logged_in() || ! current_user_can( 'memberhero_edit_account' ) ) {
			return;
		}

		memberhero_user_cancel_pending_email( get_current_user_id() );

		$response = array(
			'message' => __( 'Your email change request was cancelled.', 'memberhero' ),
		);

		wp_send_json( $response );
	}

	/**
	 * Resend confirmation for email change.
	 */
	public static function resend_confirmation() {

		check_ajax_referer( 'memberhero-resend-confirmation', 'security' );

		if ( ! is_user_logged_in() || ! current_user_can( 'memberhero_edit_account' ) ) {
			return;
		}

		$user = memberhero_get_user( get_current_user_id() );

		$secret_key = $user->create_email_change_key();

		// Load the mailer class.
		memberhero()->mailer();
		do_action( 'memberhero_email_change_notification', $user, $secret_key );

		$response = array(
			'message' => __( 'A confirmation email has been sent to you.', 'memberhero' ),
		);

		wp_send_json( $response );
	}

	/**
	 * Save user avatar.
	 */
	public static function save_avatar() {

		check_ajax_referer( 'memberhero-save-avatar', 'security' );

		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : '';

		if ( ! memberhero_user_can_edit_profile( $user_id ) ) {
			wp_die( -1 );
		}

		// Upload the avatar.
		$file_return = memberhero_upload_avatar( $_POST );

		// Return errors.
		if ( isset( $file_return[ 'error' ] ) ) {
			wp_send_json( $file_return );
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

	/**
	 * Remove user avatar.
	 */
	public static function remove_avatar() {

		check_ajax_referer( 'memberhero-remove-avatar', 'security' );

		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : '';

		if ( ! memberhero_user_can_edit_profile( $user_id ) ) {
			wp_die( -1 );
		}

		// Removes the avatar.
		memberhero_delete_user_avatar( $user_id, true );

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
			'message'		=>__( 'Your profile photo was removed.', 'memberhero' ),
			'id'			=> $user_id,
			'js_update'				=> array(
				'parent'			=> '.memberhero-profile-photo[data-user_id=' . $user_id . ']',
				'child'				=> '.memberhero-profile-photo-add-wrap, .memberhero-profile-photo-link',
				'html'				=> $html,
			),
			'toggle'		=> array(
				'.memberhero_has_avatar'	=> 'memberhero_no_avatar',
			),
		);

		wp_send_json( $response );
	}

	/**
	 * Save user cover.
	 */
	public static function save_cover() {

		check_ajax_referer( 'memberhero-save-cover', 'security' );

		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : '';

		if ( ! memberhero_user_can_edit_profile( $user_id ) ) {
			wp_die( -1 );
		}

		// Upload the cover.
		$file_return = memberhero_upload_cover( $_POST );

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
	 * Remove user cover.
	 */
	public static function remove_cover() {

		check_ajax_referer( 'memberhero-remove-cover', 'security' );

		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : '';

		if ( ! memberhero_user_can_edit_profile( $user_id ) ) {
			wp_die( -1 );
		}

		// Removes the cover.
		memberhero_delete_user_cover( $user_id, true );

		$html = '<div class="memberhero-profile-coverbg" style="background-image: url( ' . memberhero_get_user_cover_url( $user_id ) . ' );"></div>';

		$response = array(
			'message'		=> __( 'Your profile header was removed.', 'memberhero' ),
			'id'			=> $user_id,
			'js_update'				=> array(
				'parent'			=> '.memberhero-profile-cover',
				'child'				=> '.memberhero-profile-coverbg',
				'html'				=> $html,
			),
			'toggle'		=> array(
				'.memberhero_has_cover'	=> 'memberhero_no_cover',
			),
		);

		wp_send_json( $response );
	}

	/**
	 * Unblock a user.
	 */
	public static function unblock_user() {

		check_ajax_referer( 'memberhero-unblock-user', 'security' );

		if ( ! $user = memberhero_user_can_block( $_POST[ 'user_id' ] ) ) {
			wp_send_json( self::could_not_perform_action() );
		}

		// Unblock user.
		if ( memberhero_unblock_user( $user ) > 0 ) {
			$message = sprintf( __( '@%s is no longer blocked.', 'memberhero' ), $user->user_login );
		} else {
			$message = sprintf( __( '@%s could not be unblocked.', 'memberhero' ), $user->user_login );
		}

		$response = array(
			'id'			=> $user->ID,
			'message'		=> $message,
			'js_update'				=> array(
				'parent'			=> '#memberhero-user-' . $user->ID,
			),
			'toggle'		=> array(
				'.memberhero_blocked'		=> 'memberhero_unblocked',
			),
			'keep_alive'	=> true,
		);

		wp_send_json( $response );
	}

	/**
	 * Block a user.
	 */
	public static function block_user() {

		check_ajax_referer( 'memberhero-block-user', 'security' );

		if ( ! $user = memberhero_user_can_block( $_POST[ 'user_id' ] ) ) {
			wp_send_json( self::could_not_perform_action() );
		}

		// Block user.
		if ( memberhero_block_user( $user ) > 0 ) {
			$message = sprintf( __( '@%s is now blocked.', 'memberhero' ), $user->user_login );
		} else {
			$message = sprintf( __( '@%s could not be blocked.', 'memberhero' ), $user->user_login );
		}

		$response = array(
			'id'			=> $user->ID,
			'message'		=> $message,
			'js_update'				=> array(
				'parent'			=> '#memberhero-user-' . $user->ID,
			),
			'toggle'		=> array(
				'.memberhero_unblocked'	=> 'memberhero_blocked',
			),
		);

		wp_send_json( $response );
	}

	/**
	 * Delete a user.
	 */
	public static function delete_user() {

		check_ajax_referer( 'memberhero-delete-user', 'security' );

		// Make sure that user can delete other users.
		if ( ! current_user_can( 'memberhero_delete_users' ) ) {
			wp_send_json( self::could_not_perform_action() );
		}

		$user_id = isset( $_POST[ 'user_id' ] ) && ! empty( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : 0;

		if ( $user = memberhero_delete_user( $_POST[ 'user_id' ] ) ) {
			$response = array(
				'id'		=> $user->ID,
				'message'	=> sprintf( __( '@%s is now deleted.', 'memberhero' ), $user->user_login ),
				'js_remove'	=> '#memberhero-user-' . $user->ID,
			);

			wp_send_json( $response );
		} else {

			wp_send_json( self::could_not_perform_action() );
		}
	}

	/**
	 * Save a form.
	 */
	public static function save_form() {

		check_ajax_referer( 'memberhero-save-form', 'security' );

		if ( ! current_user_can( 'edit_memberhero_forms' ) ) {
			wp_die( -1 );
		}

		$the_form = new MemberHero_Form( absint( $_POST['id'] ) );
		$the_form->save( array(
			'rows'	    => isset( $_POST['rows'] ) ? memberhero_clean( $_POST['rows'] ) : '', 
			'fields'	=> isset( $_POST['fields'] ) ? memberhero_clean( $_POST['fields'] ) : '',
			'row_count'	=> absint( $_POST['row_count'] ),
			'cols'		=> memberhero_clean( $_POST['cols'] ),
		) );

		wp_die();
	}

	/**
	 * Add a field.
	 */
	public static function add_field() {

		check_ajax_referer( 'memberhero-add-field', 'security' );

		if ( ! current_user_can( 'edit_memberhero_fields' ) ) {
			wp_die( -1 );
		}

		$errors = array();
		$props = array();

		$the_field = new MemberHero_Field();

		$props['key']         			= isset( $_POST['key'] ) ? sanitize_title( wp_unslash( $_POST['key'] ) ) : '';

		if ( ! $props['key'] ) {
			$errors['key'] = __( 'You must provide a unique key for this custom field.', 'memberhero' );
		}

		if ( $the_field->exists( $props['key'] ) ) {
			$errors['key'] = __( 'The key provided is already in use. Please write a unique key.', 'memberhero' );
		}

		// Send errors back to form.
		if ( ! empty( $errors ) ) {
			wp_send_json( array( 'errors' => $errors ) );
		}

		// No errors? Setup all props now.
		$props = memberhero_setup_field_props( 'label' );

		// Add the field to database.
		if ( empty( $errors ) ) {
			$the_field->set( 'post_title', $props['label'] );
			$the_field->set( 'post_name', memberhero_clean( wp_unslash( $props['key'] ) ) );
			$the_field->set( 'meta_input', $props );
			$the_field->insert();
			$the_field->save( $the_field->meta_input );
		}

		$html = '<a href="#" class="button button-secondary insert_field" ' . memberhero_get_data_attributes( $props ) . '>' . __( $props['label'] ) . '</a>';

		wp_send_json( array( 'data' => $html ) );
	}

	/**
	 * Create default forms.
	 */
	public static function create_forms() {

		check_ajax_referer( 'memberhero-create-forms', 'security' );

		if ( ! current_user_can( 'publish_memberhero_forms' ) ) {
			wp_die( -1 );
		}

		memberhero_create_default_forms();

		wp_die();
	}

	/**
	 * Create default custom fields.
	 */
	public static function create_fields() {

		check_ajax_referer( 'memberhero-create-fields', 'security' );

		if ( ! current_user_can( 'publish_memberhero_fields' ) ) {
			wp_die( -1 );
		}

		memberhero_create_default_fields();

		wp_die();
	}

	/**
	 * Create default user roles.
	 */
	public static function create_roles() {

		check_ajax_referer( 'memberhero-create-roles', 'security' );

		if ( ! current_user_can( 'publish_memberhero_roles' ) ) {
			wp_die( -1 );
		}

		memberhero_create_default_roles();

		wp_send_json( array( 'redirect' => admin_url( 'edit.php?post_type=memberhero_role' ) ) );
	}

	/**
	 * Create default member directories.
	 */
	public static function create_lists() {

		check_ajax_referer( 'memberhero-create-lists', 'security' );

		if ( ! current_user_can( 'publish_memberhero_lists' ) ) {
			wp_die( -1 );
		}

		memberhero_create_default_lists();

		wp_die();
	}

	/**
	 * Duplicate a form.
	 */
	public static function duplicate_form() {

		check_ajax_referer( 'duplicate-form', 'security' );

		if ( ! current_user_can( 'publish_memberhero_forms' ) ) {
			wp_die( -1 );
		}

		$item = new MemberHero_Form( absint( $_POST[ 'id' ] ) );
		$item->duplicate();

		wp_die();
	}

	/**
	 * Duplicate a custom field.
	 */
	public static function duplicate_field() {

		check_ajax_referer( 'duplicate-field', 'security' );

		if ( ! current_user_can( 'publish_memberhero_fields' ) ) {
			wp_die( -1 );
		}

		$item = new MemberHero_Field( absint( $_POST[ 'id' ] ) );
		$item->duplicate();

		wp_die();
	}

	/**
	 * Duplicate a user role.
	 */
	public static function duplicate_role() {

		check_ajax_referer( 'duplicate-role', 'security' );

		if ( ! current_user_can( 'publish_memberhero_roles' ) ) {
			wp_die( -1 );
		}

		$item = new MemberHero_Role( absint( $_POST[ 'id' ] ) );
		$item->duplicate();

		wp_die();
	}

	/**
	 * Duplicate a member directory.
	 */
	public static function duplicate_list() {

		check_ajax_referer( 'duplicate-list', 'security' );

		if ( ! current_user_can( 'publish_memberhero_lists' ) ) {
			wp_die( -1 );
		}

		$item = new MemberHero_List( absint( $_POST[ 'id' ] ) );
		$item->duplicate();

		wp_die();
	}

	/**
	 * Initiate email confirmation from admin.
	 */
	public static function confirm_email() {
		if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'memberhero-confirm-email' ) ) ) {
			die( __( 'Security check.', 'memberhero' ) );
		}

		$user_id = absint( $_REQUEST[ 'id' ] );

		if ( ! current_user_can( 'memberhero_mod_users' ) ) {
			wp_die( -1 );
		}

		$user = memberhero_get_user( $user_id );
		$user->create_confirmation_code( true );

		$response = array(
			'updated'	=> __( 'Email confirmation requested.', 'memberhero' ),
		);

		wp_send_json( $response );
	}

	/**
	 * Set user as pending.
	 */
	public static function set_pending() {
		if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'memberhero-set-pending' ) ) ) {
			die( __( 'Security check.', 'memberhero' ) );
		}

		$user_id = absint( $_REQUEST[ 'id' ] );

		if ( ! current_user_can( 'memberhero_mod_users' ) ) {
			wp_die( -1 );
		}

		$user = memberhero_get_user( $user_id );
		$user->create_pending_user();

		wp_send_json( array( 'updated' => __( 'User is now pending.', 'memberhero' ) ) );
	}

	/**
	 * Approve a user.
	 */
	public static function approve_user() {
		if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'memberhero-approve-user' ) ) && ! check_ajax_referer( 'memberhero-approve-user', 'security' ) ) {
			die( __( 'Security check.', 'memberhero' ) );
		}

		$user_id = ! empty( $_REQUEST[ 'id' ] ) ? absint( $_REQUEST[ 'id' ] ) : absint( $_REQUEST[ 'user_id' ] );

		if ( ! current_user_can( 'memberhero_mod_users' ) ) {
			wp_die( -1 );
		}

		$user = memberhero_get_user( $user_id );
		$user->approve_pending_user();

		// Frontend vs backend.
		if ( ! empty( $_REQUEST[ 'user_id' ] ) ) {
			$response = array( 'message' => sprintf( __( '@%s is now approved.', 'memberhero' ), $user->user_login ) );
		} else {
			$response = array( 'updated' => __( 'User is now approved.', 'memberhero' ) );
		}

		wp_send_json( $response );
	}

	/**
	 * Reject a user.
	 */
	public static function reject_user() {
		if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'memberhero-reject-user' ) ) && ! check_ajax_referer( 'memberhero-reject-user', 'security' ) ) {
			die( __( 'Security check.', 'memberhero' ) );
		}

		$user_id = ! empty( $_REQUEST[ 'id' ] ) ? absint( $_REQUEST[ 'id' ] ) : absint( $_REQUEST[ 'user_id' ] );

		if ( ! current_user_can( 'memberhero_mod_users' ) ) {
			wp_die( -1 );
		}

		$user = memberhero_get_user( $user_id );
		$user->reject_pending_user();

		// Frontend vs backend.
		if ( ! empty( $_REQUEST[ 'user_id' ] ) ) {
			$response = array( 'message' => sprintf( __( '@%s is now rejected.', 'memberhero' ), $user->user_login ) );
		} else {
			$response = array( 'updated' => __( 'User is moved to auto deletion queue.', 'memberhero' ) );
		}

		wp_send_json( $response );
	}

	/**
	 * Reinstate a user.
	 */
	public static function reinstate_user() {
		if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'memberhero-reinstate-user' ) ) ) {
			die( __( 'Security check.', 'memberhero' ) );
		}

		$user_id = absint( $_REQUEST[ 'id' ] );

		if ( ! current_user_can( 'memberhero_mod_users' ) ) {
			wp_die( -1 );
		}

		$user = memberhero_get_user( $user_id );
		$user->reinstate();

		wp_send_json( array( 'updated' => __( 'User is now pending.', 'memberhero' ) ) );
	}

	/**
	 * Create/Update API key.
	 */
	public static function update_api_key() {
		ob_start();

		global $wpdb;

		check_ajax_referer( 'update-api-key', 'security' );

		if ( ! current_user_can( 'manage_memberhero' ) ) {
			wp_die( -1 );
		}

		$response = array();

		try {
			if ( empty( $_POST['description'] ) ) {
				throw new Exception( __( 'Description is missing.', 'memberhero' ) );
			}
			if ( empty( $_POST['user'] ) ) {
				throw new Exception( __( 'User is missing.', 'memberhero' ) );
			}
			if ( empty( $_POST['permissions'] ) ) {
				throw new Exception( __( 'Permissions is missing.', 'memberhero' ) );
			}

			$key_id      = isset( $_POST['key_id'] ) ? absint( $_POST['key_id'] ) : 0;
			$description = sanitize_text_field( wp_unslash( $_POST['description'] ) );
			$permissions = ( in_array( wp_unslash( $_POST['permissions'] ), array( 'read', 'write', 'read_write' ), true ) ) ? sanitize_text_field( wp_unslash( $_POST['permissions'] ) ) : 'read';
			$user_id     = absint( $_POST['user'] );

			// Check if current user can edit other users.
			if ( $user_id && ! current_user_can( 'edit_user', $user_id ) ) {
				if ( get_current_user_id() !== $user_id ) {
					throw new Exception( __( 'You do not have permission to assign API Keys to the selected user.', 'memberhero' ) );
				}
			}

			if ( 0 < $key_id ) {
				$data = array(
					'user_id'     => $user_id,
					'description' => $description,
					'permissions' => $permissions,
				);

				$wpdb->update(
					$wpdb->prefix . 'memberhero_api_keys',
					$data,
					array( 'key_id' => $key_id ),
					array(
						'%d',
						'%s',
						'%s',
					),
					array( '%d' )
				);

				$response                    = $data;
				$response['consumer_key']    = '';
				$response['consumer_secret'] = '';
				$response['message']         = __( 'API Key updated successfully.', 'memberhero' );
			} else {
				$consumer_key    = 'ck_' . memberhero_rand_hash();
				$consumer_secret = 'cs_' . memberhero_rand_hash();

				$data = array(
					'user_id'         => $user_id,
					'description'     => $description,
					'permissions'     => $permissions,
					'consumer_key'    => memberhero_api_hash( $consumer_key ),
					'consumer_secret' => $consumer_secret,
					'truncated_key'   => substr( $consumer_key, -7 ),
				);

				$wpdb->insert(
					$wpdb->prefix . 'memberhero_api_keys',
					$data,
					array(
						'%d',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
					)
				);

				$key_id                      = $wpdb->insert_id;
				$response                    = $data;
				$response['consumer_key']    = $consumer_key;
				$response['consumer_secret'] = $consumer_secret;
				$response['message']         = __( 'API Key generated successfully. Make sure to copy your new keys now as the secret key will be hidden once you leave this page.', 'memberhero' );
				$response['revoke_url']      = '<a style="color: #a00; text-decoration: none;" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'revoke-key' => $key_id ), admin_url( 'admin.php?page=memberhero-settings&tab=advanced&section=keys' ) ), 'revoke' ) ) . '">' . __( 'Revoke key', 'memberhero' ) . '</a>';
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}

		// wp_send_json_success must be outside the try block not to break phpunit tests.
		wp_send_json_success( $response );
	}

	/**
	 * Search users and get result in JSON format.
	 */
	public static function json_search_users() {

		check_ajax_referer( 'memberhero-json-search-users', 'security' );

		if ( ! current_user_can( 'manage_memberhero' ) ) {
			wp_die( -1 );
		}

		$results = array();
		$search  = esc_attr( $_REQUEST[ 'keyword' ] );

		$users = new WP_User_Query( array(
			'search'         => "*{$search}*",
			'search_columns' => array(
				'user_login',
				'user_nicename',
				'user_email',
				'user_url',
			),
		) );

		$users_found = $users->get_results();

		if ( ! empty( $users_found ) ) {
			foreach( $users_found as $user ) {
				$results[] = array(
					'id'	=> $user->ID,
					'email'	=> $user->user_email,
					'name'	=> $user->display_name,
				);
			}
		}

		wp_send_json( $results );
	}

	/**
	 * Search terms and get result in JSON format.
	 */
	public static function json_search_terms() {

		check_ajax_referer( 'memberhero-json-search-terms', 'security' );

		if ( ! current_user_can( 'manage_memberhero' ) ) {
			wp_die( -1 );
		}

		$results = array();

		$type = esc_attr( $_REQUEST[ 'type' ] );

		if ( in_array( $type, array( 'posts', 'pages', 'categories' ) ) ) {
			$data_store = MemberHero_Data_Store::load( $type );
			$method 	= 'search_' . $type;
			$results 	= $data_store->$method( memberhero_clean( esc_attr( $_REQUEST[ 'keyword' ] ) ) );
		}

		wp_send_json( $results );
	}

	/**
	 * Signup for lifetime access.
	 */
	public static function access_signup() {
		$error = '';
		$response = '';

		check_ajax_referer( 'memberhero-signup-nonce', 'security' );

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( get_user_meta( get_current_user_id(), 'memberhero_dismissed_memberhero_lifetime_access_notice', true ) ) {
			return;
		}
		
		$email = isset($_POST['customer_email']) ? sanitize_email( $_POST['customer_email'] ) : '';
		$name  = isset($_POST['customer_name']) ? esc_attr($_POST['customer_name']) : '';
		$use   = isset($_POST['customer_use']) ? sanitize_key($_POST['customer_use']) : 'myself';

		if ( ! is_email( $email ) ) {
			$error = __( 'Please enter a valid email', 'memberhero' );
		}

		// API request.
		if ( empty( $error ) ) {
			$url = 'https://memberhero.pro/?_lfetmeaccess=optin&email=' . $email . '&name=' . urlencode( $name ) . '&use=' . $use;
			$request  = wp_remote_get( $url );
			$response = wp_remote_retrieve_body( $request );

			if ( isset( $response ) && 'success' === $response ) {
				update_user_meta( get_current_user_id(), 'memberhero_dismissed_memberhero_lifetime_access_notice', true );
			} else {
				$error = __( 'We could not sign you up at this time.', 'memberhero' );
			}

		}

		$response = array(
			'message' 	=> '<p class="mhero-lead">' . __( 'Thank you for signing up for lifetime access to Member Hero Pro!', 'memberhero' ) . '</p>',
			'error'		=> $error,
			'url'		=> $url,
		);

		wp_send_json( $response );
	}

}

MemberHero_AJAX::init();