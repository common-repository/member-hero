<?php
/**
 * Form Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a form.
 */
function memberhero_get_form( $form_id = '' ) {
	return new MemberHero_Form( absint( $form_id ) );
}

/**
 * Get supported field types.
 */
function memberhero_get_form_types() {
	return apply_filters( 'memberhero_get_form_types', array(
		'register'		=>	array(
			'label'		=> __( 'Registration', 'memberhero' ),
			'icon'		=> 'user-plus',
		),
		'login'			=>	array(
			'label'		=> __( 'Login', 'memberhero' ),
			'icon'		=> 'lock',
		),
		'profile'		=>	array(
			'label'		=> __( 'Profile', 'memberhero' ),
			'icon'		=> 'user',
		),
		'account'		=> array(
			'label'		=> __( 'Account', 'memberhero' ),
			'icon'		=> 'settings',
		),
		'lostpassword'	=>	array(
			'label'		=> __( 'Lost Password', 'memberhero' ),
			'icon'		=> 'key',
		),
	) );
}

/**
 * Get form type name.
 */
function memberhero_get_form_type( $type ) {
	$types = memberhero_get_form_types();

	if ( ! isset( $types[ $type ] ) )
		return;

	return '<span class="memberhero-tag-icon">' . memberhero_svg_icon( $types[ $type ][ 'icon' ] ) . $types[ $type ][ 'label' ] . '</span>';
}

/**
 * Get default forms.
 */
function memberhero_get_default_forms() {
	$array = array();

	array_push(
		$array,
		array(
			'type'		=> 'register',
			'title'		=> __( 'Registration', 'memberhero' ),
			'fields'	=> array(
				0		=> array( 'data' => memberhero_get_field( 'user_email' ), 'row' => 1, 'col' => 1 ),
				1		=> array( 'data' => memberhero_get_field( 'user_login' ), 'row' => 1, 'col' => 1 ),
				2		=> array( 'data' => memberhero_get_field( 'user_pass' ), 'row' => 1, 'col' => 1 ),
			)
		) 
	);

	array_push(
		$array,
		array(
			'type'		=> 'login',
			'title'		=> __( 'Login', 'memberhero' ),
			'fields'	=> array(
				0		=> array( 'data' => memberhero_get_field( 'user_login' ), 'row' => 1, 'col' => 1 ),
				1		=> array( 'data' => memberhero_get_field( 'user_pass' ), 'row' => 1, 'col' => 1 ),
			)
		) 
	);

	array_push(
		$array,
		array(
			'type'		=> 'profile',
			'title'		=> __( 'Profile', 'memberhero' ),
			'fields'	=> array(
				1		=> array( 'data' => memberhero_get_field( 'display_name' ), 'row' => 1, 'col' => 1 ),
				2		=> array( 'data' => memberhero_get_field( 'first_name' ), 'row' => 1, 'col' => 1 ),
				3		=> array( 'data' => memberhero_get_field( 'last_name' ), 'row' => 1, 'col' => 1 ),
			)
		)
	);

	array_push(
		$array,
		array(
			'type'		=> 'lostpassword',
			'title'		=> __( 'Lost Password', 'memberhero' ),
			'fields'	=> array(
				0		=> array( 'data' => memberhero_get_field( 'user_email' ), 'row' => 1, 'col' => 1 ),
			)
		)
	);

	array_push(
		$array,
		array(
			'type'		=> 'account',
			'title'		=> __( 'Account - Main', 'memberhero' ),
			'fields'	=> array(
				0		=> array( 'data' => memberhero_get_field( 'user_login' ), 'row' => 1, 'col' => 1 ),
				1		=> array( 'data' => memberhero_get_field( 'user_email' ), 'row' => 1, 'col' => 1 ),
			),
			'endpoint'	=> memberhero_get_account_default_endpoint(),
		)
	);

	array_push(
		$array,
		array(
			'type'		=> 'account',
			'title'		=> __( 'Account - Password', 'memberhero' ),
			'fields'	=> array(
				1		=> array( 'data' => memberhero_get_field( 'user_pass' ), 'row' => 1, 'col' => 1 ),
			),
			'endpoint'	=> 'edit-password',
		)
	);

	array_push(
		$array,
		array(
			'type'		=> 'account',
			'title'		=> __( 'Account - Privacy', 'memberhero' ),
			'fields'	=> array(
				1		=> array( 'data' => memberhero_get_field( '_memberhero_private' ), 'row' => 1, 'col' => 1 ),
			),
			'endpoint'	=> 'privacy',
		)
	);

	return apply_filters( 'memberhero_get_default_forms', $array );
}

/**
 * Create default forms.
 */
function memberhero_create_default_forms() {
	// Ensure that default fields have created.
	memberhero_create_default_fields();

	if ( ! empty( $forms = memberhero_get_default_forms() ) ) {
		foreach( $forms as $key => $data ) {
			$type = memberhero_clean( wp_unslash( $data[ 'type' ] ) );

			$the_form = new MemberHero_Form();

			$the_form->set( 'post_title', isset( $data[ 'title' ] ) ? memberhero_clean( $data['title'] ) : '' );
			$the_form->set( 'post_name', sanitize_title( $the_form->post_title ) );
			$the_form->set( 'meta_input', array(
					'type'		=> $type,
					'fields'	=> isset( $data[ 'fields' ] ) ? memberhero_clean( $data[ 'fields' ] ) : '',
					'row_count'	=> 1,
					'cols'		=> memberhero_get_default_form_layout(),
					'endpoint'	=> isset( $data[ 'endpoint' ] ) ? sanitize_title( $data[ 'endpoint' ] ) : '',
			) );

			$the_form->insert();
			$the_form->save( $the_form->meta_input );

			// Assign the form id to an option.
			if ( ! empty( $the_form->id ) ) {
				memberhero_add_default_form_id( $type, $the_form->id );
			}
		}
	}
}

/**
 * Returns default form layout.
 */
function memberhero_get_default_form_layout() {
	$array = array(
		0 => array(
			'count' 	=> 0,
			'layout' 	=> 0
		),
		1 => array(
			'count' 	=> 1,
			'layout' 	=> array(
				0 => '100'
			)
		)
	);
	
	return apply_filters( 'memberhero_get_default_form_layout', $array );
}

/**
 * Add a default form ID.
 */
function memberhero_add_default_form_id( $type = '', $id = 0 ) {
	if ( in_array( $type, array( 'account' ) ) ) {
		return;
	}
	update_option( "memberhero_{$type}_form", absint( $id ) );
}

/**
 * Get a default form ID.
 */
function memberhero_get_default_form_id( $type = null ) {
	$form = "memberhero_{$type}_form";

	return apply_filters( 'memberhero_get_default_form_id', get_option( $form ) );
}

/**
 * Get a form linked to a specific endpoint.
 */
function memberhero_get_endpoint_form( $endpoint ) {
	if ( ! $endpoint ) {
		return;
	}

	$form_id = get_option( 'memberhero_' . $endpoint . '_form', 0 );

	return apply_filters( 'memberhero_get_endpoint_form', $form_id );
}

/**
 * Get redirection url after a login.
 */
function memberhero_get_login_redirect( $user = null ) {
	global $the_form;

	// By default, redirect user to profile.
	$default = memberhero_get_profile_url( $user->user_login );

	// But admins should go to WP-admin
	if ( user_can( $user->ID, 'memberhero_view_wpadmin' ) ) {
		$default = admin_url();
	}

	// Lets check if the form has specific rules.
	if ( isset( $the_form->redirect ) && $the_form->redirect !== 'none' ) {
		switch( $the_form->redirect ) {
			case 'profile' :
				$redirect = memberhero_get_profile_url( $user->user_login );
			break;
			case 'account' :
				$redirect = memberhero_get_page_permalink( 'account' );
			break;
			case 'custom' :
				$redirect = esc_url( $the_form->redirect_uri );
			break;
			case 'refresh' :
				$redirect = esc_url( remove_query_arg( memberhero_get_current_url() ) );
			break;
		}
	}

	// This has top priority so it comes as the last rule.
	if ( ! empty( $_REQUEST[ '_memberhero_redirect' ] ) ) {
		$redirect = esc_url_raw( $_REQUEST[ '_memberhero_redirect' ] );
	}

	// No redirect set. Use default.
	if ( empty( $redirect ) ) {
		$redirect = $default;
	}

	return apply_filters( 'memberhero_get_login_redirect', $redirect );
}

/**
 * Get redirection url after a registration.
 */
function memberhero_get_register_redirect( $user = null ) {
	global $the_form;

	// By default, redirect user to profile.
	$default = memberhero_get_profile_url( $user->user_login );

	// But admins should go to WP-admin
	if ( user_can( $user->ID, 'memberhero_view_wpadmin' ) ) {
		$default = admin_url();
	}

	// Lets check if the form has specific rules.
	if ( isset( $the_form->redirect ) && $the_form->redirect !== 'none' ) {
		switch( $the_form->redirect ) {
			case 'profile' :
				$redirect = memberhero_get_profile_url( $user->user_login );
			break;
			case 'account' :
				$redirect = memberhero_get_page_permalink( 'account' );
			break;
			case 'custom' :
				$redirect = esc_url( $the_form->redirect_uri );
			break;
			case 'refresh' :
				$redirect = esc_url( remove_query_arg( memberhero_get_current_url() ) );
			break;
		}
	}

	// This has top priority so it comes as the last rule.
	if ( ! empty( $_REQUEST[ '_memberhero_redirect' ] ) ) {
		$redirect = esc_url_raw( $_REQUEST[ '_memberhero_redirect' ] );
	}

	// No redirect set. Use default.
	if ( empty( $redirect ) ) {
		$redirect = $default;
	}

	return apply_filters( 'memberhero_get_register_redirect', $redirect );
}

/**
 * Add a redirect property to the form.
 */
function memberhero_form_set_redirect( $redirect = null ) {
	global $memberhero;

	if ( empty( $redirect ) ) {
		$redirect = memberhero_get_current_url();
	}

	memberhero()->_redirect = esc_url_raw( $redirect );
}

/**
 * Show form loop template.
 */
function memberhero_form_loop( $args = array() ) {
	memberhero_get_template( 'form/form-loop.php', $args );
}

/**
 * Get form column part.
 */
function memberhero_form_column( $args = array() ) {
	memberhero_get_template( 'form/form-column.php', $args );
}

/**
 * Get form top note if available.
 */
function memberhero_form_note( $args = array() ) {
	memberhero_get_template( 'form/form-note.php', $args );
}

/**
 * Get form buttons.
 */
function memberhero_form_buttons( $args = array() ) {
	if ( memberhero_get_scope() == 'view' ) {
		return;
	}
	memberhero_get_template( 'form/form-buttons.php', $args );
}

/**
 * Get classes for the form buttons container.
 */
function memberhero_get_buttons_classes( $classes = array() ) {
	global $the_form;

	return apply_filters( 'memberhero_get_buttons_classes', array_unique( $classes ) );
}

/**
 * Get classes for a form column.
 */
function memberhero_get_column_classes( $row, $classes = array() ) {
	global $the_form;

	return apply_filters( 'memberhero_get_column_classes', array_unique( $classes ) );
}

/**
 * Get form classes.
 */
function memberhero_get_form_classes( $classes = array() ) {
	global $the_form;

	$type 		= esc_attr( $the_form->type );
	$endpoint 	= $the_form->get_endpoint();

	$classes[] 	= "memberhero-{$type}";
	$classes[] 	= "memberhero-{$endpoint}";

	// Add a class depending on form maximum columns.
	$count = memberhero_get_max_column( $the_form->cols, 'count' );
	if ( $count == 1 && memberhero_get_scope() == 'edit' ) {
		$classes[] = 'memberhero-form-onecolumn';
	}

	return apply_filters( 'memberhero_get_form_classes', array_unique( $classes ) );
}

/**
 * Print form inline styles data.
 */
function memberhero_print_form_inline_styles() {
	if ( ! empty( $inline = memberhero_get_form_inline_styles() ) ) {
		echo 'style="' . implode( ';', $inline ) . ';"';
	}
}

/**
 * Print column inline styles data.
 */
function memberhero_print_column_inline_styles( $row ) {
	if ( ! empty( $inline = memberhero_get_column_inline_styles( $row ) ) ) {
		echo 'style="' . implode( ';', $inline ) . ';"';
	}
}

/**
 * Get form inline styles data.
 */
function memberhero_get_form_inline_styles() {
	global $the_form;

	$inline = array();

	return apply_filters( 'memberhero_get_form_inline_styles', $inline, $the_form );
}

/**
 * Get column inline styles data.
 */
function memberhero_get_column_inline_styles( $row ) {
	global $the_form;

	$inline = array();

	return apply_filters( 'memberhero_get_column_inline_styles', $inline, $the_form );
}

/**
 * Check if the form row has a defined title.
 */
function memberhero_has_row_title( $row ) {
	global $the_form;
	if ( empty( $the_form->rows ) ) {
		return false;
	}

	return isset( $the_form->rows[ $row - 1 ][ 'title' ] ) && ! empty( $the_form->rows[ $row - 1 ][ 'title' ] );
}

/**
 * Get a form row title.
 */
function memberhero_get_row_title( $row ) {
	global $the_form;

	return isset( $the_form->rows[ $row - 1 ][ 'title' ] ) && ! empty( $the_form->rows[ $row - 1 ][ 'title' ] ) ? $the_form->rows[ $row - 1 ][ 'title' ] : null;
}

/**
 * The possible form redirect options.
 */
function memberhero_get_form_redirect_options() {
	$array = array(
		'none'		=> __( 'Default Redirection', 'memberhero' ),
		'profile'	=> __( 'Profile', 'memberhero' ),
		'account'	=> __( 'Account', 'memberhero' ),
		'refresh'   => __( 'Refresh same page', 'memberhero' ),
		'custom'    => __( 'Custom URL', 'memberhero' ),
	);

	return apply_filters( 'memberhero_get_form_redirect_options', $array );
}

/**
 * Check if form does not exist by checking its status.
 */
function memberhero_form_does_not_exist( $form_id ) {
	$status = get_post_status( $form_id );

	return $status !== 'publish' ? true : false;
}

/**
 * Add error to form.
 */
function memberhero_form_add_error( $id, $key ) {
	MemberHero_Form_Handler::$errors[ $id ][ $key ] = true;
}

/**
 * If form has a specific key error.
 */
function memberhero_form_has_error( $id, $key ) {
	$errors = MemberHero_Form_Handler::$errors;

	if ( isset( $errors[ $id ][ $key ] ) ) {
		return true;
	}

	return false;
}

/**
 * Check if form contains errors.
 */
function memberhero_form_has_errors( $id ) {
	$errors = MemberHero_Form_Handler::$errors;

	return isset( $errors[ $id ] ) ? true : false;
}

/**
 * Get the error fields - return class names.
 */
function memberhero_form_get_error_fields( $id ) {
	$memberhero_form_get_error_fields = null;
	$errors = MemberHero_Form_Handler::$errors;

	if ( isset( $errors[ $id ] ) ) {
		foreach( $errors[ $id ] as $key => $boolean ) {
			$memberhero_form_get_error_fields[] = '.' . $key . '_field';
		}
	}

	return $memberhero_form_get_error_fields;
}

/**
 * Print the form ID prefix.
 */
function memberhero_form_prefix() {
	echo memberhero_get_form_prefix();
}

/**
 * Returns the form ID prefix.
 */
function memberhero_get_form_prefix() {
	global $the_form;

	if ( isset( $the_form->id ) && $the_form->is_custom == false ) {
		return apply_filters( 'memberhero_get_form_prefix', 'memberheroform' . $the_form->id . '_' );
	}
}

/**
 * Get form data for a specific form.
 */
function memberhero_form_get_postdata( $id ) {
	$formdata = MemberHero_Form_Handler::$formdata;

	return isset( $formdata[ $id ] ) ? apply_filters( 'memberhero_get_postdata', $formdata[ $id ], $id ) : null;
}

/**
 * Add a postdata to form.
 */
function memberhero_form_add_postdata( $id, $key, $value ) {
	MemberHero_Form_Handler::$formdata[ $id ][ $key ] = $value;
}

/**
 * Remove a postdata from a form.
 */
function memberhero_form_remove_postdata( $id, $key ) {
	if ( isset( MemberHero_Form_Handler::$formdata[ $id ][ $key ] ) ) {
		unset( MemberHero_Form_Handler::$formdata[ $id ][ $key ] );
	}
}

/**
 * Get postdata from a form.
 */
function memberhero_form_get_postdata_key( $id, $key ) {
	$formdata = MemberHero_Form_Handler::$formdata;

	return isset( $formdata[ $id ][ $key ] ) ? $formdata[ $id ][ $key ] : '';
}