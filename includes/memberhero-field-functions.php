<?php
/**
 * Field Functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get supported field types.
 */
function memberhero_get_field_types( $type = 'default' ) {

	$array = array(
		'text'			=>	array(
			'label'		=> __( 'Text Input', 'memberhero' ),
			'icon'		=> 'file-text',
		),
		'select'		=>	array(
			'label'		=> __( 'Select', 'memberhero' ),
			'icon'		=> 'list',
			'no_input'	=> 1,
		),
		'textarea'		=>	array(
			'label'		=> __( 'Textarea', 'memberhero' ),
			'icon'		=> 'clipboard',
			'no_input'	=> 1,
		),
		'checkbox'		=>	array(
			'label'		=> __( 'Checkbox', 'memberhero' ),
			'icon'		=> 'square',
			'no_input'	=> 1,
		),
		'radio'			=>	array(
			'label'		=> __( 'Radio', 'memberhero' ),
			'icon'		=> 'circle',
			'no_input'	=> 1,
		),
		'email'			=>	array(
			'label'		=> __( 'Email', 'memberhero' ),
			'icon'		=> 'mail',
		),
		'url'			=>	array(
			'label'		=> __( 'URL Address', 'memberhero' ),
			'icon'		=> 'link',
		),
		'number'		=> array(
			'label'		=> __( 'Number', 'memberhero' ),
			'icon'		=> 'bar-chart-2',
		),
		'phone'			=>	array(
			'label'		=> __( 'Phone Number', 'memberhero' ),
			'icon'		=> 'phone',
		),
		'password'		=>	array(
			'label'		=> __( 'Password', 'memberhero' ),
			'icon'		=> 'key',
		),
		'image'			=>	array(
			'label'		=> __( 'Image Upload', 'memberhero' ),
			'icon'		=> 'image',
		),
		'file'			=>	array(
			'label'		=> __( 'File Upload', 'memberhero' ),
			'icon'		=> 'file',
		),
		'toggle'		=>	array(
			'label'		=> __( 'Toggle', 'memberhero' ),
			'icon'		=> 'toggle-left',
			'no_input'	=> 1,
		),
		'rating'		=>	array(
			'label'		=> __( 'Rating', 'memberhero' ),
			'icon'		=> 'star',
			'no_input'	=> 1,
		),
		'date'			=> array(
			'label'		=>	__( 'Date Picker', 'memberhero' ),
			'icon'		=> 'calendar',
			'no_input'	=> 1,
		),
		'dynamic'		=> array(
			'label'		=>	__( 'Dynamic', 'memberhero' ),
			'icon'		=> 'box',
		),
		'html'			=> array(
			'label'		=> __( 'Custom HTML', 'memberhero' ),
			'icon'		=> 'code',
		),
	);

	return apply_filters( 'memberhero_get_field_types', $array, $type );
}

/**
 * Get field type name.
 */
function memberhero_get_field_type( $type, $return = false ) {
	$types = memberhero_get_field_types();

	if ( ! isset( $types[ $type ] ) )
		return;

	if ( $return == 'label' ) {
		return $types[ $type ][ 'label' ];
	}

	if ( $return == 'icon' ) {
		return $types[ $type ][ 'icon' ];
	}

	if ( $return == 'html' ) {
		return '<span class="memberhero-tag-icon">' . memberhero_svg_icon( $types[ $type ][ 'icon' ] ) . $types[ $type ][ 'label' ] . '</span>';
	}

	return $types[ $type ];
}

/**
 * Get custom fields.
 */
function memberhero_get_custom_fields() {
	return apply_filters( 'memberhero_get_custom_fields', get_option( 'memberhero_fields' ) );
}

/**
 * Get a custom field from options.
 */
function memberhero_get_field( $key ) {
	$array = ( array ) get_option( 'memberhero_fields' );

	return array_key_exists( $key, $array ) ? $array[$key] : '';
}

/**
 * Get custom fields as options or key/value pair.
 */
function memberhero_get_fields_array() {
	$result = array();
	$fields = memberhero_get_custom_fields();

	foreach( $fields as $key => $data ) {
		$result[ $key ] = $data[ 'label' ];
	}

	return apply_filters( 'memberhero_get_fields_array', $result );
}

/**
 * Get default fields.
 */
function memberhero_get_default_fields() {
	$array = array(
		'user_login'		=>	array(
			'label'			=> __( 'Username', 'memberhero' ),
			'type'			=> 'text',
			'icon'			=> 'user',
			'is_readonly'	=> 1,
		),
		'user_email'		=>	array(
			'label'			=> __( 'Email Address', 'memberhero' ),
			'type'			=> 'email',
			'icon'			=> 'mail',
			'is_private'	=> 1,
			'can_view'		=> array( 'owner' ),
		),
		'first_name'		=>	array(
			'label'			=> __( 'First Name', 'memberhero' ),
			'type'			=> 'text',
			'is_private'	=> 1,
			'can_view'		=> array( 'owner' ),
		),
		'last_name'			=>	array(
			'label'			=> __( 'Last Name', 'memberhero' ),
			'type'			=> 'text',
			'is_private'	=> 1,
			'can_view'		=> array( 'owner' ),
		),
		'display_name'		=>	array(
			'label'			=> __( 'Display Name', 'memberhero' ),
			'type'			=> 'text',
		),
		'description'		=>	array(
			'label'			=> __( 'Biography', 'memberhero' ),
			'type'			=> 'textarea',
		),
		'country'			=> array(
			'label'			=> __( 'Country/Region', 'memberhero' ),
			'type'			=> 'select',
			'icon'			=> 'map-pin',
		),
		'role'				=> array(
			'label'			=> __( 'Role', 'memberhero' ),
			'type'			=> 'select',
		),
		'user_url'			=>	array(
			'label'			=> __( 'Website URL', 'memberhero' ),
			'type'			=> 'url',
			'icon'			=> 'link',
		),
		'user_registered'	=>	array(
			'label'			=> __( 'Registration Date', 'memberhero' ),
			'type'			=> 'dynamic',
			'is_readonly'	=> 1,
		),
		'user_pass'			=> array(
			'label'			=> __( 'Password', 'memberhero' ),
			'type'			=> 'password',
			'icon'			=> 'lock',
			'is_private'	=> 1,
			'can_view'		=> array( '_none' ),
		),
		'_memberhero_private'		=> array(
			'label'			=> __( 'Profile privacy', 'memberhero' ),
			'type'			=> 'toggle',
			'is_private'	=> 1,
			'can_view'		=> array( '_none' ),
			'helper'		=> __( 'Protect my profile information from the public.', 'memberhero' ),
		),
		'_memberhero_facebook'		=>	array(
			'label'			=> __( 'Facebook', 'memberhero' ),
			'type'			=> 'url',
		),
		'_memberhero_twitter'		=>	array(
			'label'			=> __( 'Twitter', 'memberhero' ),
			'type'			=> 'url',
		),
		'_memberhero_instagram'	=>	array(
			'label'			=> __( 'Instagram', 'memberhero' ),
			'type'			=> 'url',
		),
	);

	return apply_filters( 'memberhero_get_default_fields', $array );
}

/**
 * Create default fields.
 */
function memberhero_create_default_fields() {

	$fields = memberhero_get_default_fields();

	memberhero_create_fields_from_array( $fields );

}

/**
 * Create fields from array.
 */
function memberhero_create_fields_from_array( $fields ) {
	if ( ! empty( $fields ) ) {
		foreach( $fields as $key => $data ) {
			$the_field = new MemberHero_Field();
			$the_field->set( 'post_title', isset( $data['label'] ) ? memberhero_clean( wp_unslash( $data['label'] ) ) : '' );
			$the_field->set( 'post_name', memberhero_clean( wp_unslash( $key ) ) );
			$the_field->set(
				'meta_input',
				array_merge(
					array( 'key' => memberhero_clean( wp_unslash( $key ) ) ),
					memberhero_clean( $data )
				)
			);
			$the_field->insert();
			$the_field->save( $the_field->meta_input );
		}
	}
}

/**
 * Remove default fields.
 */
function memberhero_remove_default_fields() {
	global $wpdb;

	if ( ! current_user_can( 'delete_memberhero_fields' ) ) {
		wp_die( -1 );
	}

	if ( ! empty( $fields = memberhero_get_default_fields() ) ) {
		$memberhero_fields = get_option( 'memberhero_fields' );

		foreach( $fields as $key => $data ) {

			if ( isset( $memberhero_fields[ $key ] ) ) {
				unset( $memberhero_fields[ $key ] );
			}

			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_type = 'memberhero_field' AND post_name = %s AND 1=1", $key ) );
		}

		$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

		if ( ! empty( $memberhero_fields ) ) {
			update_option( 'memberhero_fields', $memberhero_fields );
		} else {
			delete_option( 'memberhero_fields' );
		}

	}

}

/**
 * Get field data attributes.
 */
function memberhero_get_data_attributes( $data ) {
	$output = '';

	if ( empty( $data ) ) {
		return;
	}

	foreach( $data as $key => $value ) {
		if ( is_array( $value ) ) {
			$value = implode( ',', $value );
		} else {
			$value = esc_attr( $value );
		}
		// Replace new line breaks with plus sign.
		$value = str_replace( ["\r\n", "\r", "\n"], '+', $value );
		$output .= ' data-' . esc_attr( $key ) . '="' . $value . '"';
	}

	return $output;
}

/**
 * Check if a field is private.
 */
function memberhero_is_private_field( $field ) {
	$bool = false;

	if ( ! empty( $field[ 'is_private'] ) && $field[ 'is_private' ] == 1 ) {
		$bool = true;
	}

	return apply_filters( 'memberhero_is_private_field', $bool, $field );
}

/**
 * Check if field is a password field.
 */
function memberhero_is_password_field( $field ) {
	$bool = false;

	if ( ! empty( $field[ 'type'] ) && $field[ 'type' ] == 'password' ) {
		$bool = true;
	}

	return apply_filters( 'memberhero_is_password_field', $bool, $field );
}

/**
 * Setup a field.
 */
function memberhero_setup_field( $array ) {
	global $the_form, $the_user;

	if ( ! array_key_exists( 'type', ( array ) $array[ 'data' ] ) ) {
		return;
	}

	$field = $array['data'];

	$defaults = array(
		'form_type'		=> $the_form->type,
		'field_type'	=> memberhero_get_field_type( $field['type'] ),
		'options'		=> memberhero_get_field_options( $field ),
		'title_class'	=> array(),
		'label_class'	=> array(),
		'input_class'	=> array(),
		'field_class'	=> array(),
		'control_class'	=> array(),
		'attributes'	=> array(),
	);

	$the_field = new MemberHero_Field();
	foreach( apply_filters( 'memberhero_field_meta_keys', $the_field->internal_meta_keys ) as $default ) {
		$defaults[ $default ] = '';
	}

	$field = wp_parse_args( $field, $defaults );

	// Edit label needs to override default label.
	if ( ! empty( $field['edit_label'] ) ) {
		$field['label'] = $field['edit_label'];
	}

	// View scope.
	if ( memberhero_get_scope() == 'view' && ! empty( $field['view_label'] ) ) {
		$field['label'] = $field['view_label'];
	}

	// Get field value.
	if ( in_array( $field['form_type'], array( 'account', 'profile' ) ) ) {
		$field[ 'value' ] = $the_user->get( $field['key'] );
	}

	$field['value'] = memberhero_form_get_postdata_key( $the_form->id, $field['key'] ) ? memberhero_form_get_postdata_key( $the_form->id, $field['key'] ) : $field['value'];

	// No input field.
	if ( ! empty( $field['field_type']['no_input'] ) || memberhero_get_scope() == 'view' ) {
		$field['no_input'] = 1;
	}

	// Classes.
	$field['field_class'][] = 'memberhero-field-' . esc_attr( $field['type'] );
	$field['field_class'][] = esc_attr( $field['key'] ) . '_field';

	if ( empty( $field['label'] ) && is_array( $field['options'] ) && count( $field['options'] ) == 1 ) {
		$field['field_class'][] = 'memberhero-single-option';
	}

	if ( $field['type'] == 'select' ) {
		$field['input_class'][] = 'memberhero-select';
	}

	if ( ! empty( $field[ 'emojis' ] ) ) {
		$field['input_class'][] = 'memberhero-emoji';
	}

	if ( memberhero_form_has_error( $the_form->id, $field['key'] ) ) {
		$field['label_class'][] = 'memberhero-invalid';
		$field['input_class'][] = 'memberhero-invalid';
	}

	if ( $the_form->icons == 'label' || ( $the_form->icons == 'inside' && $field['no_input'] ) ) {
		if ( $field[ 'icon' ] ) {
			if ( in_array( $field[ 'type' ], array( 'image', 'file' ) ) ) {
				if ( memberhero_get_scope() == 'view' ) {
					$field['title_class'][] = 'has-icon';
				}
			} else {
				$field['title_class'][] = 'has-icon';
			}
		}
	}

	if ( $the_form->icons == 'inside' && $field['icon'] ) {
		if ( ! $field['no_input'] ) {
			$field['control_class'][] = 'has-icon';
		}
	}

	if ( memberhero_get_scope() == 'view' ) {
		$field['title_class'][] = 'memberhero-view';
	}

	// Attributes.
	if ( $field['key'] == 'user_login' && $field['form_type'] != 'login' ) {
		$field['attributes'][] = 'autocomplete=off';
	}

	if ( $field['type'] == 'password' && $field['form_type'] != 'login' ) {
		$field['attributes'][] = 'autocomplete=new-password';
	}

	if ( ! empty( $field['placeholder'] ) ) {
		$field['attributes'][] = 'placeholder="' . esc_attr( $field['placeholder'] ) . '"';
	} else {
		$field['attributes'][] = 'placeholder="' . esc_attr( memberhero_get_default_placeholder( $field ) ) . '"';
	}

	if ( ! empty( $field['is_readonly'] ) && ! empty( $the_user->user_id ) ) {
		if ( get_option( 'memberhero_allow_' . $field['key'] . '_change' ) != 'yes' ) {
			$field['attributes'][] = 'disabled="disabled"';
		}
	}

	if ( empty( $field['no_input'] ) ) {
		$field['attributes'][] = 'spellcheck="false"';
	}

	// Filter for field value.
	if ( memberhero_get_scope() == 'view' ) {
		$field[ 'value' ] = apply_filters( 'memberhero_get_' . esc_attr( $field['key'] ) . '_value', $field[ 'value' ], $field );
	}

	/**
	 * Hooks to modify field attributes and settings.
	 */
	$field = apply_filters( 'memberhero_get_field', $field, $the_form );
	$field = apply_filters( 'memberhero_get_' . esc_attr( $field['key'] ) . '_field', $field, $the_form );
	$field = apply_filters( 'memberhero_get_' . esc_attr( $field['form_type'] ) . '_' . esc_attr( $field['key'] ) . '_field', $field, $the_form );

	return $field;
}

/**
 * Get field options.
 */
function memberhero_get_field_options( $field ) {
	extract( $field );

	if ( $type == 'select' ) {
		$options = ! empty( $dropdown_options ) ? $dropdown_options : '';
	} elseif ( $type == 'radio' ) {
		$options = ! empty( $radio_options ) ? $radio_options : '';
	} elseif ( $type == 'checkbox' ) {
		$options = ! empty( $checkbox_options ) ? $checkbox_options : '';
	}

	// Get default options.
	if ( empty( $options ) && in_array( $type, array( 'select', 'checkbox', 'radio' ) ) ) {
		return memberhero_get_default_field_options( $field );
	} else {
		// Explode options.
		if ( ! empty( $options ) ) {
			if ( strstr( $options, '+' ) ) {
				$options = str_replace( '++', '+', $options );
				$options = explode( '+', $options );
			} else {
				$options = preg_split('/\r\n|[\r\n]/', $options );
			}
			$options = array_combine( array_map( 'sanitize_title', $options ), $options );
			if ( memberhero_get_default_placeholder( $field ) ) {
				return array( '' => memberhero_get_default_placeholder( $field ) ) + $options;
			}
			return $options;
		}
	}

	return null;
}

/**
 * Get default field options.
 */
function memberhero_get_default_field_options( $field ) {
	extract( $field );

	// Get countries list.
	if ( $key == 'country' ) {
		return array( '' => memberhero_get_default_placeholder( $field ) ) + memberhero()->countries->countries;
	}

	// Get roles list.
	if ( $key == 'role' ) {
		return memberhero_get_roles( is_admin() ? false : true );
	}

	// Get gender list.
	if ( $key == 'gender' ) {
		return apply_filters( 'memberhero_gender_options', array(
			'female'	=> __( 'Female', 'memberhero' ),
			'male'		=> __( 'Male', 'memberhero' ),
			'none'		=> __( 'Prefer not to say', 'memberhero' ),
		) );
	}
}

/**
 * Get default field placeholder.
 */
function memberhero_get_default_placeholder( $field ) {
	extract( $field );

	// Country placeholder.
	if ( $key == 'country' && $field[ 'type' ] === 'select' ) {
		return ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : __( 'Select a country...', 'memberhero' );
	}

	// Role placeholder.
	if ( $key == 'role' && $field[ 'type' ] === 'select' ) {
		return ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : __( 'Select account type...', 'memberhero' );
	}

	// Gender placeholder.
	if ( $key == 'gender' && $field[ 'type' ] === 'select' ) {
		return ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : __( 'Select your gender...', 'memberhero' );
	}

	return ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : null;
}

/**
 * Get a textarea value.
 */
function memberhero_get_textarea( $field ) {

	$value = wp_kses( $field[ 'value' ], memberhero_textarea_allowed_tags() );

	// If links are allowed in this field.
	if ( $field[ 'autolinks' ] && apply_filters( 'memberhero_autolinks_in_textarea', true ) ) {
		$value = memberhero_make_clickable( $value );
	}

	return apply_filters( 'memberhero_get_textarea', $value, $field );
}

/**
 * Display a separator in the form.
 */
function memberhero_separator() {
	echo '<div class="memberhero-sep"></div>';
}

/**
 * Display a toggle field control in the form.
 */
function memberhero_toggle( $field = array() ) {

	memberhero_get_template( 'edit/toggle.php', array( 'field' => $field ) );
}

/**
 * Get a list of misc field types.
 */
function memberhero_misc_fields() {
	return apply_filters( 'memberhero_misc_fields', array() );
}