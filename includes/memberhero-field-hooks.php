<?php
/**
 * Field Hooks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Email change.
add_action( 'memberhero_after_email_field_input', 'memberhero_email_pending_notification_text' );

// Form ID and nonce fields.
add_action( 'memberhero_after_register_form_content', 'memberhero_add_form_inputs' );
add_action( 'memberhero_after_login_form_content', 'memberhero_add_form_inputs' );
add_action( 'memberhero_after_lostpassword_form_content', 'memberhero_add_form_inputs' );
add_action( 'memberhero_after_account_form_content', 'memberhero_add_form_inputs' );
add_action( 'memberhero_after_profile_form_content', 'memberhero_add_form_inputs' );

// Core and custom forms.
add_action( 'memberhero_after_password_reset_form_content', 'memberhero_add_form_inputs' );

// Extra fields.
add_filter( 'memberhero_get_form_fields', 'memberhero_add_new_password_fields', 10, 2 );
add_filter( 'memberhero_get_form_fields', 'memberhero_add_confirm_fields', 10, 2 );

// Account fields.
add_filter( 'memberhero_get_account_user_login_field', 'memberhero_get_account_user_login_field', 10, 2 );
add_filter( 'memberhero_get_account_user_email_field', 'memberhero_get_account_user_email_field', 10, 2 );
add_filter( 'memberhero_get_account_user_pass_field', 'memberhero_get_account_user_pass_field', 10, 2 );
add_filter( 'memberhero_get_account_new_password_field', 'memberhero_get_account_new_password_field', 10, 2 );
add_filter( 'memberhero_get_account_verify_new_password_field', 'memberhero_get_account_verify_new_password_field', 10, 2 );

// Registration fields.
add_filter( 'memberhero_get_register_user_login_field', 'memberhero_get_register_user_login_field', 10, 2 );
add_filter( 'memberhero_get_register_user_email_field', 'memberhero_get_register_user_email_field', 10, 2 );

// Login fields.
add_filter( 'memberhero_get_login_user_pass_field', 'memberhero_get_login_user_pass_field', 10, 2 );

// Add extra field icons beside label.
add_action( 'memberhero_after_label_text', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_url', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_email', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_phone', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_password', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_image', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_file', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_checkbox', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_radio', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_select', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_textarea', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_rating', 'memberhero_get_field_icons', 10 );
add_action( 'memberhero_after_label_toggle', 'memberhero_get_field_icons', 10 );

/**
 * Inform the user that his new email is unconfirmed.
 */
function memberhero_email_pending_notification_text() {
	global $the_form;

	if ( memberhero_user_has_pending_email() && $the_form->type == 'account' ) {
		memberhero_get_template( 'global/new-email-change.php' );
	}
}

/**
 * Add the hidden inputs including nonce.
 */
function memberhero_add_form_inputs() {
	global $the_form;

	// Do not add inputs when the form is in 'view' mode.
	if ( memberhero_get_scope() == 'view' ) {
		return;
	}

	if ( ! empty( memberhero()->query->get_current_endpoint() ) ) {
		$endpoint = memberhero()->query->get_current_endpoint();
	} else {
		$endpoint = esc_attr( $the_form->type );
	}

	if ( $endpoint == 'account' ) {
		$endpoint = memberhero_get_account_default_endpoint();
	}

	if ( isset( $_GET[ 'login' ] ) && $endpoint == 'memberhero_user' ) {
		$endpoint = 'login';
	}

	if ( $endpoint == 'memberhero_user' ) {
		$endpoint = 'edit-profile';
	}

	if ( $the_form->type == 'login' ) {
		$endpoint = 'login';
	}

	// Add hidden fields assigned to this form.
	if ( ! empty( $the_form->hidden_fields ) ) {
		foreach( $the_form->hidden_fields as $key => $value ) {
			echo '<input type="hidden" id="' . $key . '" name="' . $key . '" value="' . $value . '" />';
		}
	}

	// Allow the endpoint to be filtered.
	$endpoint = apply_filters( 'memberhero_get_nonce_endpoint', $endpoint, $the_form );

	echo '<input type="hidden" id="_' . $endpoint . '_id" name="_' . $endpoint . '_id" value="' . absint( $the_form->id ) . '" />';
	echo '<input type="hidden" id="_endpoint" name="_endpoint" value="' . $the_form->get_endpoint() . '" />';

	// Adds the user ID as hidden input field.
	if ( $the_form->get_endpoint() == 'profile' ) {
		echo '<input type="hidden" id="_user_id" name="_user_id" value="' . memberhero_get_active_profile_id() . '" />';
	}

	// Add enforced redirection.
	if ( ! empty( memberhero()->_redirect ) ) {
		echo '<input type="hidden" id="_memberhero_redirect" name="_memberhero_redirect" value="' . esc_url_raw( memberhero()->_redirect ) . '" />';
	}

	do_action( 'memberhero_custom_form_hook_input', $the_form );

	wp_nonce_field( 'memberhero-' . $endpoint, 'memberhero-' . $endpoint . '-nonce' );
}

/**
 * Account - Edit password.
 */
function memberhero_add_new_password_fields( $fields, $the_form ) {

	if ( $the_form->get_endpoint() == 'edit-password' ) {

		$item = memberhero_array_search( 'user_pass', $fields );
		if ( is_array( $item ) ) {
			$item[ 'data' ][ 'key' ] = 'new_password';
			array_push( $fields, $item );
			$item[ 'data' ][ 'key' ] = 'verify_new_password';
			array_push( $fields, $item );
		}

	}

	return $fields;
}

/**
 * Register - confirm fields.
 */
function memberhero_add_confirm_fields( $fields, $the_form ) {

	if ( $the_form->type == 'register' ) {
		
		$add = 0;

		foreach( $fields as $index => $array ) {
			if ( $array['data']['key'] == 'user_email' && $the_form->confirm_email === 'yes' ) {
				$array['data']['key'] 		= 'confirm_user_email';
				$array['data']['label'] 	= __( 'Confirm email', 'memberhero' );
				$array['data']['helper'] 	= '';
				array_splice( $fields, $index + 1, 0, array( $array ) );
				$add = 1;
			}
			if ( $array['data']['key'] == 'user_pass' && $the_form->confirm_password === 'yes' ) {
				$array['data']['key'] 		= 'confirm_user_pass';
				$array['data']['label'] 	= __( 'Confirm password', 'memberhero' );
				$array['data']['helper'] 	= '';
				array_splice( $fields, $index + 1 + $add, 0, array( $array ) );
			}
		}

	}

	return $fields;
}

/**
 * Account - Username
 */
function memberhero_get_account_user_login_field( $field, $the_form ) {
	if ( empty( $field[ 'helper' ] ) ) {
		$field[ 'helper' ] = memberhero_get_profile_url( '', $ajax = true );
	}

	return $field;
}

/**
 * Account - Email
 */
function memberhero_get_account_user_email_field( $field, $the_form ) {
	if ( empty( $field[ 'helper' ] ) ) {
		$field[ 'helper' ] = __( 'Email will not be publicly displayed.', 'memberhero' );
	}

	return $field;
}

/**
 * Account - Current user password.
 */
function memberhero_get_account_user_pass_field( $field, $the_form ) {
	$field = array_merge( $field, array(
		'label'			=> __( 'Current password', 'memberhero' ),
		'helper'		=> sprintf( '<a href="%s">%s</a>', memberhero_lostpassword_url(), __( 'Forgotten your password?', 'memberhero' ) ),
		'hide_toggle'	=> true,
	) );

	return $field;
}

/**
 * Account - New user password.
 */
function memberhero_get_account_new_password_field( $field, $the_form ) {
	$field = array_merge( $field, array(
		'label'			=> __( 'New password', 'memberhero' ),
		'hide_toggle'	=> true,
	) );

	return $field;
}

/**
 * Account - Verify user password.
 */
function memberhero_get_account_verify_new_password_field( $field, $the_form ) {
	$field = array_merge( $field, array(
		'label'			=> __( 'Verify password', 'memberhero' ),
		'hide_toggle'	=> true,
	) );

	return $field;
}

/**
 * Register - Username
 */
function memberhero_get_register_user_login_field( $field, $the_form ) {
	if ( empty( $field[ 'helper' ] ) ) {
		$field[ 'helper' ] = __( 'You can use letters, numbers, and underscores.', 'memberhero' );
	}

	return $field;
}

/**
 * Register - Email
 */
function memberhero_get_register_user_email_field( $field, $the_form ) {
	if ( empty( $field[ 'helper' ] ) ) {
		$field[ 'helper' ] = __( 'Your email will never be published.', 'memberhero' );
	}

	return $field;
}

/**
 * Login - Password
 */
function memberhero_get_login_user_pass_field( $field, $the_form ) {
	if ( empty( $field[ 'helper' ] ) ) {
		$field[ 'helper' ] = sprintf( '<a href="%s">%s</a>', memberhero_lostpassword_url(), __( 'Forgotten your password?', 'memberhero' ) );
	}

	return $field;
}

/**
 * Returns the field icons. e.g. private
 */
function memberhero_get_field_icons( $field ) {
	extract( $field );

	$output = '';

	// Display privacy lock beside email.
	if ( memberhero_is_private_field( $field ) ) {

		if ( in_array( $form_type, array( 'login', 'register', 'lostpassword' ) ) ) {
			return;
		}
		if ( in_array( $type, array( 'password' ) ) ) {
			return;
		}
		if ( in_array( $key, array( '_memberhero_private' ) ) ) {
			return;
		}

		if ( apply_filters( 'memberhero_show_private_field_marker', true, $field ) ) {
			$output .= '<span class="memberhero-private tips" data-tip="' . __( 'People will not see this', 'memberhero' ) . '">' . memberhero_svg_icon( 'lock' ) . '</span>';
		}
	}

	$output = apply_filters( 'memberhero_get_field_icons', $output, $field );

	echo $output;
}